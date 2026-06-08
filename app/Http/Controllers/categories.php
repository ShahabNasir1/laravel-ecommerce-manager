<?php

namespace App\Http\Controllers;

use App\Models\category;
use Illuminate\Http\Request;

class categories extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if the request is coming via DataTables AJAX
        if ($request->ajax()) {
            $query = category::query();

            // 1. Total records in database before filtering
            $totalRecords = $query->count();

            // 2. Handle Global Search Bar
            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $searchValue = trim($request->input('search')['value']);

                $query->where(function ($q) use ($searchValue) {
                    // 1. Numeric check for ID (avoids useless string scanning on an integer column)
                    if (is_numeric($searchValue)) {
                        $q->where('category_id', $searchValue);
                    } else {
                        // 2. Optimized wildcard search for name
                        $q->where('category_name', 'like', "%{$searchValue}%");

                        // 3. Dynamic status matching (only search status if they actually typed "act" or "inact")
                        if (str_starts_with(strtolower($searchValue), 'a')) {
                            $q->orWhere('category_status', 'active');
                        } elseif (str_starts_with(strtolower($searchValue), 'i')) {
                            $q->orWhere('category_status', 'inactive');
                        }
                    }
                });
            }

            // 3. Total records after filtering applied
            $filteredRecords = $query->count();

            // 4. Handle Sorting Columns dynamically
            $columnsMap = [
                0 => 'category_id',
                1 => 'category_name',
                2 => 'category_status',
                3 => 'created_at',
                4 => 'updated_at'
            ];

            if ($request->has('order')) {
                $columnIndex = $request->input('order')[0]['column'];
                $sortDirection = $request->input('order')[0]['dir'];

                if (isset($columnsMap[$columnIndex])) {
                    $query->orderBy($columnsMap[$columnIndex], $sortDirection);
                }
            } else {
                $query->latest('category_id');
            }

            // 5. Handle Pagination Chunking (Limit & Offset)
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $categories = $query->skip($start)->take($length)->get();

            // 6. Loop and format JSON payload
            $data = [];
            foreach ($categories as $category) {

                // Generate clean Frontend HTML Badges dynamically
                $statusHtml = $category->category_status === 'active'
                    ? '<span class="label label-primary">Active</span>'
                    : '<span class="label label-danger">Inactive</span>';

                $actionsHtml = '
                <a href="' . route('categories.edit', $category->category_id) . '" class="btn btn-info btn-sm">
                    <i class="fa fa-paste"></i> Edit
                </a>
                <button type="button" class="btn btn-danger btn-sm btn-delete" 
                        data-id="' . $category->category_id . '" 
                        data-name="' . e($category->category_name) . '">
                    <i class="fa fa-trash"></i> Delete
                </button>
            ';

                $data[] = [
                    'category_id'          => $category->category_id,
                    'category_name'        => $category->category_name,
                    'status'               => $statusHtml,
                    'created_at_formatted' => $category->created_at ? $category->created_at->format('d-M-Y H:i A') : 'N/A',
                    'updated_at_formatted' => $category->updated_at ? $category->updated_at->format('d-M-Y H:i A') : 'N/A',
                    'actions'              => $actionsHtml,
                ];
            }

            // 7. Return exact JSON structure DataTables expects
            return response()->json([
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalRecords),
                "recordsFiltered" => intval($filteredRecords),
                "data"            => $data
            ]);
        }

        // Return the initial blank layout page on standard direct page loading
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
