<?php

namespace App\Http\Controllers;

use App\Models\category;
use App\Models\brand;
use App\Models\color;
use App\Models\size;
use App\Models\product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Added for production debugging

class products extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'List Products';

        $breadcrumbs = [
            'Products' => route('products.index'),
            'List Products' => '#'
        ];

        // Eager load all relations to prevent N+1 performance bottleneck
        $products = \App\Models\Product::with(['category', 'brand', 'sizes', 'colors', 'images'])->get();

        return view('frontend.products.list-product', compact('pageTitle', 'breadcrumbs', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Add Product';

        $breadcrumbs = [
            'Products' => route('products.index'),
            'Add Product' => '#'
        ];

        // Fetch lookup records from the database filtering by your active status columns
        $categories = category::where('category_status', 'active')->orderBy('category_name', 'asc')->get();
        $brands     = brand::where('brand_status', 'active')->orderBy('brand_name', 'asc')->get();
        $colors     = color::where('color_status', 'active')->orderBy('color_name', 'asc')->get();
        $sizes      = size::where('size_status', 'active')->get();

        // Pass all variables straight down into the view layout
        return view('frontend.products.add-product', compact(
            'pageTitle',
            'breadcrumbs',
            'categories',
            'brands',
            'colors',
            'sizes'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Strict Validation
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
            'productPic.*'       => 'image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        // 2. Safe File Upload Logic
        $storedImages = [];
        try {
            if ($request->hasFile('productPic')) {
                foreach ($request->file('productPic') as $image) {
                    if ($image->isValid()) {
                        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $image->storeAs('products', $imageName, 'public');
                        $storedImages[] = $imageName;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Image Upload Failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['productPic' => 'Image processing failed.']);
        }

        // 3. Database Persistence - Perfect Mapping to Product Model Attributes
        $product = \App\Models\Product::create([
            'product_name'   => $validated['productName'],        // Maps to model's 'product_name'
            'description'    => $validated['productDescription'],
            'price'          => $validated['price'],
            'category_id'    => $validated['productCategory'],
            'brand_id'       => $validated['productBrand'],
            'product_status' => $validated['productStatus'],      // Maps to model's 'product_status'
            'user_id'        => \Illuminate\Support\Facades\Auth::id() ?? 1, // Fallback safely if no auth context
        ]);

        // 4. Pivot Table Syncing for Colors and Sizes
        if (!empty($validated['colors'])) {
            $product->colors()->sync($validated['colors']);
        }

        if (!empty($validated['size'])) {
            $product->sizes()->sync($validated['size']);
        }

        // 5. Database Multi-Image Association
        if (!empty($storedImages)) {
            foreach ($storedImages as $index => $filename) {
                // Using your exact model class name 'product_image' and column 'image_url'
                \App\Models\product_image::create([
                    'product_id' => $product->product_id,
                    'image_url'  => $filename, // FIXED: Changed from image_path to image_url
                    'sort_order' => $index + 1 // Automatically sets order 1, 2, 3...
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product and operational dependencies mapped and saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product successfully deleted');
    }
}
