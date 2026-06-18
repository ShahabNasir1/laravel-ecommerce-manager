<?php

namespace App\Http\Controllers;

use App\Models\category;
use App\Models\brand;
use App\Models\color;
use App\Models\size;
use App\Models\product;
use App\Models\product_image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class products extends Controller
{
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

        $storedFilenames = [];

        try {
            if ($request->hasFile('productPic')) {
                $manager = new ImageManager(new Driver());

                foreach ($request->file('productPic') as $image) {
                    if ($image->isValid()) {
                        $filename = time() . '_' . uniqid() . '.webp';

                        // 1. Save all 4 size variants synchronously
                        $imgSmall = $manager->read($image->getRealPath())->scale(width: 150);
                        Storage::disk('public')->put('products/small_image/' . $filename, (string) $imgSmall->toWebp(quality: 80));

                        $imgMedium = $manager->read($image->getRealPath())->scale(width: 600);
                        Storage::disk('public')->put('products/medium_image/' . $filename, (string) $imgMedium->toWebp(quality: 80));

                        $imgLarge = $manager->read($image->getRealPath())->scale(width: 1200);
                        Storage::disk('public')->put('products/large_image/' . $filename, (string) $imgLarge->toWebp(quality: 80));

                        $imgOriginal = $manager->read($image->getRealPath())->scale(width: 1200);
                        Storage::disk('public')->put('products/' . $filename, (string) $imgOriginal->toWebp(quality: 80));

                        $storedFilenames[] = $filename;
                    }
                }
            }
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

            if (!empty($validated['color'])) {
                $product->colors()->sync($validated['color']);
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

            // Clean up files across ALL 4 directories on rollback failure
            foreach ($storedFilenames as $filename) {
                Storage::disk('public')->delete([
                    'products/small_image/' . $filename,
                    'products/medium_image/' . $filename,
                    'products/large_image/' . $filename,
                    'products/' . $filename
                ]);
            }

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

        try {
            if ($hasNewImages) {
                $manager = new ImageManager(new Driver());

                foreach ($request->file('productPic') as $image) {
                    if ($image->isValid()) {
                        $filename = time() . '_' . uniqid() . '.webp';

                        $imgSmall = $manager->read($image->getRealPath())->scale(width: 150);
                        Storage::disk('public')->put('products/small_image/' . $filename, (string) $imgSmall->toWebp(quality: 80));

                        $imgMedium = $manager->read($image->getRealPath())->scale(width: 600);
                        Storage::disk('public')->put('products/medium_image/' . $filename, (string) $imgMedium->toWebp(quality: 80));

                        $imgLarge = $manager->read($image->getRealPath())->scale(width: 1200);
                        Storage::disk('public')->put('products/large_image/' . $filename, (string) $imgLarge->toWebp(quality: 80));

                        $imgOriginal = $manager->read($image->getRealPath())->scale(width: 1200);
                        Storage::disk('public')->put('products/' . $filename, (string) $imgOriginal->toWebp(quality: 80));

                        $storedFilenames[] = $filename;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Image Update Processing Failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['productPic' => 'Image processing failed.']);
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

            // If new images were successfully processed, swap them with the old ones
            if ($hasNewImages && !empty($storedFilenames)) {

                // 1. Get references to old entries before deleting DB lines
                $oldImages = product_image::where('product_id', $product->product_id)->get();

                // 2. Clear old file logs out of the database
                product_image::where('product_id', $product->product_id)->delete();

                // 3. Delete old files from ALL 4 structural directories
                foreach ($oldImages as $oldImage) {
                    if ($oldImage->image_url) {
                        Storage::disk('public')->delete([
                            'products/small_image/' . $oldImage->image_url,
                            'products/medium_image/' . $oldImage->image_url,
                            'products/large_image/' . $oldImage->image_url,
                            'products/' . $oldImage->image_url
                        ]);
                    }
                }

                // 4. Record new images into the database
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

            // If the database transaction fails, remove the newly uploaded files to prevent junk buildup
            foreach ($storedFilenames as $filename) {
                Storage::disk('public')->delete([
                    'products/small_image/' . $filename,
                    'products/medium_image/' . $filename,
                    'products/large_image/' . $filename,
                    'products/' . $filename
                ]);
            }

            Log::error('Product Update DB Fail: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Database error during update. Uploaded files reverted safely.']);
        }
    }

    public function destroy(string $id) // Change from 'product $product' to 'string $id'
    {
        try {
            // Explicitly find the product by its actual primary key field
            $product = product::findOrFail($id);

            $productImages = $product->images;

            // 1. Delete existing assets across ALL 4 absolute folder destinations 
            foreach ($productImages as $image) {
                if ($image->image_url) {
                    Storage::disk('public')->delete([
                        'products/small_image/' . $image->image_url,
                        'products/medium_image/' . $image->image_url,
                        'products/large_image/' . $image->image_url,
                        'products/' . $image->image_url
                    ]);
                }
            }

            // 2. Delete the product record
            $product->delete();

            // 3. Redirect back to the index
            return redirect()->route('products.index')->with('success', 'Product and all associated size-variant images deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Product Deletion Failed: ' . $e->getMessage());
            return redirect()->route('products.index')->with('error', 'An error occurred while deleting the product.');
        }
    }
}
