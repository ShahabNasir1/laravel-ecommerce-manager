<?php

namespace App\Http\Controllers;
use App\Models\category;
use App\Models\brand;
use App\Models\color;
use App\Models\size;
use Illuminate\Http\Request;

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
            'List Products' => '#' // Active/current page
        ];
        return view('frontend.products.list-product', compact('pageTitle', 'breadcrumbs'));
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
            'productCategory'    => 'required|exists:categories,category_id', // Verify if your PK is id or category_id
            'productBrand'       => 'required|exists:brands,brand_id',       // Verify if your PK is id or brand_id
            'productName'        => 'required|string|max:255',
            'productDescription' => 'required|string',
            'price'              => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'productStatus'      => 'required|in:active,inactive',
            'colors'             => 'nullable|array',
            'colors.*'           => 'exists:colors,color_id',
            'size'               => 'nullable|array',
            'size.*'             => 'exists:sizes,id', // Verify if your sizes PK is id or size_id
            'productPic'         => 'nullable|array',
            'productPic.*'       => 'image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        
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
    public function destroy(string $id)
    {
        //
    }
}
