<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class sizes extends Controller
{
    /**
     * Display a listing of the resource.
     * Manages both raw base template views and high-throughput DataTables AJAX transfers.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Size::query();
            $totalRecords = $query->count();

            // Handle Server Side Searching
            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $searchValue = trim($request->input('search')['value']);

                $query->where(function ($q) use ($searchValue) {
                    if (is_numeric($searchValue)) {
                        $q->where('size_id', $searchValue);
                    } else {
                        $q->where('size_name', 'like', "%{$searchValue}%");
                        
                        if (str_starts_with(strtolower($searchValue), 'a')) {
                            $q->orWhere('size_status', 'active');
                        } elseif (str_starts_with(strtolower($searchValue), 'i')) {
                            $q->orWhere('size_status', 'inactive');
                        }
                    }
                });
            }

            $filteredRecords = $query->count();

            // Handle Column Ordering Mapping Matrix
            $columnsMap = [
                0 => 'size_id',
                1 => 'size_name',
                2 => 'size_status',
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
                $query->latest('size_id');
            }

            // Handle Pagination Chunk Range Limit bounds
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $sizes = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($sizes as $size) {
                $statusHtml = $size->size_status === 'active'
                    ? '<span class="label label-primary">Active</span>'
                    : '<span class="label label-danger">Inactive</span>';

                $actionsHtml = '
                    <button type="button" class="btn btn-info btn-sm btn-edit" data-id="' . $size->size_id . '">
                        <i class="fa fa-paste"></i> Edit
                    </button>
                    <button type="button" class="btn btn-danger btn-sm btn-delete" 
                            data-id="' . $size->size_id . '" 
                            data-name="' . e($size->size_name) . '">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                ';

                $data[] = [
                    'size_id'              => $size->size_id,
                    'size_name'            => $size->size_name,
                    'size_status'          => $statusHtml, 
                    'created_at_formatted' => $size->created_at ? $size->created_at->tz('Asia/Karachi')->format('d-M-Y H:i A') : 'N/A',
                    'updated_at_formatted' => $size->updated_at ? $size->updated_at->tz('Asia/Karachi')->format('d-M-Y H:i A') : 'N/A',
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

        $pageTitle = 'List Sizes';
        $breadcrumbs = [
            'Sizes' => route('sizes.index'),
            'List Sizes' => '#' 
        ];

        return view('frontend.sizes.list-size', compact('pageTitle', 'breadcrumbs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sizeName'   => 'required|string|min:1|max:100|unique:sizes,size_name',
            'sizeStatus' => 'required|in:active,inactive',
        ]);

        Size::create([
            'size_name'   => $request->sizeName,
            'size_status' => $request->sizeStatus
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Size successfully added',
        ]);
    }

    /**
     * Provide raw resource item arrays to the Edit Modal engine via AJAX.
     * Using strict identification lookups ensures robust data fetching.
     */
    public function show(int|string $id)
    {
        $size = Size::find($id);

        if (!$size) {
            return response()->json([
                'success' => false,
                'message' => 'Size record not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $size
        ]);
    }

    /**
     * Catch and intercept dirty address bar navigation attempts to /sizes/{id}/edit
     */
    public function edit()
    {
        return redirect()->route('sizes.index')
            ->with('error', 'Please utilize table action control configurations to manage modifications.');
    }

    /**
     * Update the specified resource data via AJAX pipeline.
     */
    public function update(Request $request, int|string $id)
    {
        $size = Size::find($id);

        if (!$size) {
            return response()->json([
                'success' => false,
                'message' => 'Size record not found.'
            ], 404);
        }

        $request->validate([
            'sizeName'   => 'required|string|max:100|unique:sizes,size_name,' . $size->size_id . ',size_id',
            'sizeStatus' => 'required|in:active,inactive',
        ]);

        $size->update([
            'size_name'   => $request->sizeName,
            'size_status' => $request->sizeStatus,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Size parameters updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource completely from storage.
     */
    public function destroy(int|string $id)
    {
        $size = Size::find($id);

        if (!$size) {
            return response()->json([
                'success' => false,
                'message' => 'Size record not found.'
            ], 404);
        }

        $size->delete();

        return response()->json([
            'success' => true,
            'message' => 'Size tracking unit safely dropped from registry.'
        ]);
    }
}