<?php

namespace App\Http\Controllers;

use App\Models\category;
use App\Models\brand;
use App\Models\color;
use App\Models\size;
use App\Models\product;
use App\Models\product_image;
use App\Traits\HandlesImageUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class products extends Controller
{
    use HandlesImageUploads;

    public function index()
    {
        $pageTitle = 'List Products';
        $breadcrumbs = [
            'Products' => route('products.index'),
            'List Products' => '#'
        ];

        $products = product::with(['category', 'brand', 'sizes', 'colors', 'images'])->get();
        return view('frontend.products.list-product', compact('pageTitle', 'breadcrumbs', 'products'));
    }

    public function create()
    {
        $pageTitle = 'Add Product';
        $breadcrumbs = [
            'Products' => route('products.index'),
            'Add Product' => '#'
        ];

        $categories = category::where('category_status', 'active')->orderBy('category_name', 'asc')->get();
        $brands     = brand::where('brand_status', 'active')->orderBy('brand_name', 'asc')->get();
        $colors     = color::where('color_status', 'active')->orderBy('color_name', 'asc')->get();
        $sizes      = size::where('size_status', 'active')->get();

        return view('frontend.products.add-product', compact('pageTitle', 'breadcrumbs', 'categories', 'brands', 'colors', 'sizes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'productCategory'    => 'required|exists:categories,category_id',
            'productBrand'       => 'required|exists:brands,brand_id',
            'productName'        => 'required|string|max:255',
            'productDescription' => 'required|string',
            'price'              => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'productStatus'      => 'required|in:active,inactive',
            'colors'             => 'required|array|min:1',
            'colors.*'           => 'exists:colors,color_id',
            'size'               => 'required|array|min:1',
            'size.*'             => 'exists:sizes,size_id',
            'productPic'         => 'required|array|min:1',
            'productPic.*'       => 'image|mimes:jpeg,png,jpg,webp|max:10240'
        ]);

        try {
            $storedFilenames = $this->uploadAndResizeImages($request->file('productPic'), 'products');
        } catch (\Exception $e) {
            Log::error('Image Processing Failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['productPic' => 'Image processing failed.']);
        }

        try {
            DB::beginTransaction();

            $product = product::create([
                'product_name'   => $validated['productName'],
                'description'    => $validated['productDescription'],
                'price'          => $validated['price'],
                'category_id'    => $validated['productCategory'],
                'brand_id'       => $validated['productBrand'],
                'product_status' => $validated['productStatus'],
                'user_id'        => Auth::id() ?? 1,
            ]);

            if (!empty($validated['colors'])) {
                $product->colors()->sync($validated['colors']);
            }

            if (!empty($validated['size'])) {
                $product->sizes()->sync($validated['size']);
            }

            foreach ($storedFilenames as $index => $filename) {
                product_image::create([
                    'product_id' => $product->product_id,
                    'image_url'  => $filename,
                    'sort_order' => $index + 1
                ]);
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Product saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->deleteImageVariations($storedFilenames, 'products');

            Log::error('Product Store DB Fail: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Database error. Uploaded files reverted safely.']);
        }
    }

    public function edit(string $id)
    {
        $pageTitle = 'Edit Product';
        $breadcrumbs = [
            'Products' => route('products.index'),
            'Edit Product' => '#'
        ];

        $product = product::with(['images', 'colors', 'sizes'])->findOrFail($id);
        $categories = category::where('category_status', 'active')->orderBy('category_name', 'asc')->get();
        $brands     = brand::where('brand_status', 'active')->orderBy('brand_name', 'asc')->get();
        $colors     = color::where('color_status', 'active')->orderBy('color_name', 'asc')->get();
        $sizes      = size::where('size_status', 'active')->get();

        return view('frontend.products.edit-product', compact('pageTitle', 'breadcrumbs', 'product', 'categories', 'brands', 'colors', 'sizes'));
    }

    public function update(Request $request, string $id)
    {
        $product = product::findOrFail($id);

        $validated = $request->validate([
            'productCategory'    => 'required|exists:categories,category_id',
            'productBrand'       => 'required|exists:brands,brand_id',
            'productName'        => 'required|string|max:255',
            'productDescription' => 'required|string',
            'price'              => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'productStatus'      => 'required|in:active,inactive',
            'colors'             => 'nullable|array',
            'colors.*'           => 'exists:colors,color_id',
            'size'               => 'nullable|array',
            'size.*'             => 'exists:sizes,size_id',
            'productPic'         => 'nullable|array',
            'productPic.*'       => 'image|mimes:jpeg,png,jpg,webp|max:10240'
        ]);

        $storedFilenames = [];
        $hasNewImages = $request->hasFile('productPic');

        if ($hasNewImages) {
            try {
                $storedFilenames = $this->uploadAndResizeImages($request->file('productPic'), 'products');
            } catch (\Exception $e) {
                Log::error('Image Update Processing Failed: ' . $e->getMessage());
                return redirect()->back()->withInput()->withErrors(['productPic' => 'Image processing failed.']);
            }
        }

        try {
            DB::beginTransaction();

            $product->update([
                'product_name'   => $validated['productName'],
                'description'    => $validated['productDescription'],
                'price'          => $validated['price'],
                'category_id'    => $validated['productCategory'],
                'brand_id'       => $validated['productBrand'],
                'product_status' => $validated['productStatus']
            ]);

            $product->colors()->sync($validated['colors'] ?? []);
            $product->sizes()->sync($validated['size'] ?? []);

            if ($hasNewImages && !empty($storedFilenames)) {
                // Fetch the relationships collection directly instead of using a broken where method call
                $oldImages = $product->images;

                product_image::where('product_id', $product->product_id)->delete();

                // Pass the Eloquent collection straight into our modernized trait method
                $this->deleteImageVariations($oldImages, 'products');

                foreach ($storedFilenames as $index => $filename) {
                    product_image::create([
                        'product_id' => $product->product_id,
                        'image_url'  => $filename,
                        'sort_order' => $index + 1
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if (!empty($storedFilenames)) {
                $this->deleteImageVariations($storedFilenames, 'products');
            }

            Log::error('Product Update DB Fail: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Database error during update. Uploaded files reverted safely.']);
        }
    }

    public function destroy(string $id)
    {
        try {
            $product = product::findOrFail($id);

            // Pass collection reference directly to trait for file system deletion before record drops
            $this->deleteImageVariations($product->images, 'products');

            $product->delete();

            return redirect()->route('products.index')->with('success', 'Product and all variations deleted successfully from storage.');
        } catch (\Exception $e) {
            Log::error('Product Deletion Failed: ' . $e->getMessage());
            return redirect()->route('products.index')->with('error', 'An error occurred while deleting the product.');
        }
    }
}