<?php

namespace App\Http\Controllers;

use App\Models\color;
use Illuminate\Http\Request;

class colors extends Controller
{
    /**
     * Display a listing of the resource.
     * Handles both AJAX DataTables requests and initial page loads.
     */
    public function index(Request $request)
    {
        // 1. Check if the request is coming via DataTables AJAX
        if ($request->ajax()) {
            $query = color::query();

            // Total records in database before filtering
            $totalRecords = $query->count();

            // 2. Handle Global Search Bar
            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $searchValue = trim($request->input('search')['value']);

                $query->where(function ($q) use ($searchValue) {
                    if (is_numeric($searchValue)) {
                        $q->where('color_id', $searchValue);
                    } else {
                        $q->where('color_name', 'like', "%{$searchValue}%");
                        
                        if (str_starts_with(strtolower($searchValue), 'a')) {
                            $q->orWhere('color_status', 'active');
                        } elseif (str_starts_with(strtolower($searchValue), 'i')) {
                            $q->orWhere('color_status', 'inactive');
                        }
                    }
                });
            }

            // Total records after filtering applied
            $filteredRecords = $query->count();

            // 3. Handle Sorting Columns dynamically
            $columnsMap = [
                0 => 'color_id',
                1 => 'color_name',
                2 => 'color_status',
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
                $query->latest('color_id');
            }

            // 4. Handle Pagination Chunking
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $colors = $query->skip($start)->take($length)->get();

            // 5. Loop and format JSON payload
            $data = [];
            foreach ($colors as $color) {

                $statusHtml = $color->color_status === 'active'
                    ? '<span class="label label-primary">Active</span>'
                    : '<span class="label label-danger">Inactive</span>';

                // FIXED: Changed from anchor href redirect link to data-attribute driven modal trigger buttons
                $actionsHtml = '
                    <button type="button" class="btn btn-info btn-sm btn-edit" data-id="' . $color->color_id . '">
                        <i class="fa fa-paste"></i> Edit
                    </button>
                    <button type="button" class="btn btn-danger btn-sm btn-delete" 
                            data-id="' . $color->color_id . '" 
                            data-name="' . e($color->color_name) . '">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                ';

                $data[] = [
                    'color_id'             => $color->color_id,
                    'color_name'           => $color->color_name,
                    'color_status'         => $statusHtml, 
                    'created_at_formatted' => $color->created_at ? $color->created_at->format('d-M-Y H:i A') : 'N/A',
                    'updated_at_formatted' => $color->updated_at ? $color->updated_at->format('d-M-Y H:i A') : 'N/A',
                    'actions'              => $actionsHtml,
                ];
            }

            return response()->json([
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalRecords),
                "recordsFiltered" => intval($filteredRecords),
                "data"            => $data
            ]);
        }

        $pageTitle = 'List Colors';

        $breadcrumbs = [
            'Colors' => route('colors.index'),
            'List Colors' => '#' 
        ];

        return view('frontend.colors.list-color', compact('pageTitle', 'breadcrumbs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Add Color';

        $breadcrumbs = [
            'Colors' => route('colors.index'),
            'Add Color' => '#' 
        ];
        return view('frontend.colors.add-color', compact('pageTitle', 'breadcrumbs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'colorName' => 'required|string|min:3|max:100|unique:colors,color_name',
            'colorStatus' => 'required|in:active,inactive',
        ]);

        color::create([
            'color_name' => $request->colorName,
            'color_status' => $request->colorStatus
        ]);

        return redirect()->route('colors.index')->with('success', 'Color successfully added');
    }

    /**
     * Display the specified resource data via JSON for the Edit Modal.
     */
    public function show(int|string $id)
    {
        $color = color::find($id);

        if (!$color) {
            return response()->json([
                'success' => false,
                'message' => 'Color record not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $color
        ]);
    }

    /**
     * FIXED: Fallback safety measure to block explicit URL execution requests to /colors/{id}/edit
     */
    public function edit(color $color)
    {
        return redirect()->route('colors.index')
            ->with('error', 'Please use the actions table system tool to edit color parameters.');
    }

    /**
     * Update the specified resource in storage.
     * FIXED: Form returns AJAX clean structured JSON confirmations instead of layout page redirects.
     */
    public function update(Request $request, color $color)
    {
        $request->validate([
            'colorName'   => 'required|string|max:100|unique:colors,color_name,' . $color->color_id . ',color_id',
            'colorStatus' => 'required|in:active,inactive',
        ]);

        $color->update([
            'color_name'   => $request->colorName,
            'color_status' => $request->colorStatus,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Color successfully updated!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * FIXED: Form returns AJAX JSON confirmation payloads.
     */
    public function destroy(color $color)
    {
        $color->delete();

        return response()->json([
            'success' => true,
            'message' => 'Color successfully deleted!'
        ]);
    }
}