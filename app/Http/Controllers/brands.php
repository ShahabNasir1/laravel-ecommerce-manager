<?php

namespace App\Http\Controllers;

use App\Models\brand; // Model ko import kiya
use Illuminate\Http\Request;

class brands extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'List Brands';

        $breadcrumbs = [
            'Brands' => route('brands.index'),
            'List Brands' => '#'
        ];

        // Fetch all the brands from the database
        $allBrands = brand::latest('brand_id')->paginate(25);

        return view('frontend.brands.list-brand', compact('pageTitle', 'breadcrumbs', 'allBrands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Add Brand';

        $breadcrumbs = [
            'Brands' => route('brands.index'),
            'Add Brand' => '#'
        ];

        return view('frontend.brands.add-brand', compact('pageTitle', 'breadcrumbs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Form inputs ko validate kiya
        $request->validate([
            'brandName'   => 'required|string|max:100|unique:brands,brand_name',
            'brandStatus' => 'required|in:active,inactive',
        ]);

        // 2. Form values ko database columns ke mutabiq map karke insert kiya
        brand::create([
            'brand_name'   => $request->brandName,
            // Form se 1/0 aa raha hai, schema ke mutabiq 'active'/'inactive' mein convert kiya
            'brand_status' => $request->brandStatus,
        ]);

        // 3. Insert ke baad brand list page par redirect kar diya
        return redirect()->route('brands.create')->with('success', 'Brand successfully inserted!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // 1. Database se woh specific brand find kiya jiski id aayi hai
        $brand = brand::findOrFail($id);

        $pageTitle = 'Edit Brand';
        $breadcrumbs = [
            'Brands' => route('brands.index'),
            'Edit Brand' => '#'
        ];

        // 2. Data ko edit wale view form par bhej diya
        return view('frontend.brands.edit-brand', compact('pageTitle', 'breadcrumbs', 'brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 1. Validation check ki (Bina duplicate name crash ke, isliye unique rule mein ID pass ki)
        $request->validate([
            'brandName'   => 'required|string|max:100|unique:brands,brand_name,' . $id . ',brand_id',
            'brandStatus' => 'required|in:active,inactive',
        ]);

        $brand = brand::findOrFail($id);

        // 2. Database mein new records update kiye
        $brand->update([
            'brand_name'   => $request->brandName,
            'brand_status' => $request->brandStatus,
        ]);

        // 3. Wapas list page par success message ke sath redirect kar diya
        return redirect()->route('brands.index')->with('success', 'Brand successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) 
    {
        $brand = brand::findOrFail($id);
        $brand->delete();

        // 2. Delete hone ke baad table page ko refresh kiya success status ke sath
        return redirect()->route('brands.index')->with('success', 'Brand successfully deleted!');
    }
}
