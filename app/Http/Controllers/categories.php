<?php

namespace App\Http\Controllers;

use App\Models\category;
use Illuminate\Http\Request;
use App\Services\CategoryListingService;

class categories extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, CategoryListingService $listingService)
    {
        if ($request->ajax()) {
            // Yeh line bilkul theek chalti rahegi
            return response()->json(
                $listingService->getProcessedPayload($request)
            );
        }

        $pageTitle = 'List Categories';
        $breadcrumbs = [
            'Categories' => route('categories.index'),
            'List Categories' => '#'
        ];

        return view('frontend.categories.list-category', compact('pageTitle', 'breadcrumbs'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Add Category';

        $breadcrumbs = [
            'Categories' => route('categories.index'),
            'Add Category' => '#'
        ];
        return view('frontend.categories.add-category', compact('pageTitle', 'breadcrumbs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // UPDATED: Ab input se direct 'active' ya 'inactive' aa raha hai, isliye rule 'in:active,inactive' kiya
        $request->validate([
            'categoryName' => 'required|string|min:3|max:100|unique:categories,category_name',
            'categoryStatus' => 'required|in:active,inactive',
        ]);

        // UPDATED: Ternary checking hata di, raw clean value direct pass ho rahi hai
        category::create([
            'category_name' => $request->categoryName,
            'category_status' => $request->categoryStatus
        ]);

        // UX Fix: Category add hone ke baad list page par redirect karna zyada behtar hai
        return redirect()->route('categories.index')->with('success', 'Category successfully added');
    }

    /**
     * Display the specified resource.
     */
    public function show(category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(category $category)
    {
        $pageTitle = 'Edit Category';
        $breadcrumbs = [
            'Categories' => route('categories.index'),
            'Edit Category' => '#'
        ];

        return view('frontend.categories.edit-category', compact('pageTitle', 'breadcrumbs', 'category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, category $category)
    {
        // UPDATED: 'in:1,0' ko badal kar 'in:active,inactive' kiya tumhare naye HTML attribute ke mutabiq
        $request->validate([
            'categoryName'   => 'required|string|max:100|unique:categories,category_name,' . $category->category_id . ',category_id',
            'categoryStatus' => 'required|in:active,inactive',
        ]);

        // UPDATED: Faltu ki condition mapping khatam, ab direct attribute sync hoga
        $category->update([
            'category_name'   => $request->categoryName,
            'category_status' => $request->categoryStatus,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category successfully deleted');
    }
}
