<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryListingService
{
    /**
     * Process the server-side DataTables payload using Query Builder.
     */
    public function getProcessedPayload(Request $request): array
    {
        // Eloquent model ki jagah DB::table use kiya
        $query = DB::table('categories');

        // 1. Total records count (Before filtering)
        $totalRecords = $query->count();

        // 2. Global Searching Logic apply karein
        $query = $this->applySearchFilters($query, $request);

        // 3. Total records count (After filtering)
        $filteredRecords = $query->count();

        // 4. Sorting logic apply karein
        $query = $this->applySortingOrder($query, $request);

        // 5. Pagination (Limit & Offset)
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        
        $categories = $query->skip($start)->take($length)->get();

        // 6. Data formatting (Yahan $category ab Eloquent object nahi, balki stdClass raw object hai)
        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'category_id'          => $category->category_id,
                'category_name'        => $category->category_name,
                'status'               => $this->renderStatusBadge($category->category_status),
                // Query Builder mein Carbon automatic nahi chalta, isliye date formatting check karni hogi
                'created_at_formatted' => $category->created_at ? date('d-M-Y H:i A', strtotime($category->created_at)) : 'N/A',
                'updated_at_formatted' => $category->updated_at ? date('d-M-Y H:i A', strtotime($category->updated_at)) : 'N/A',
                'actions'              => $this->renderActionButtons($category),
            ];
        }

        return [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalRecords),
            "recordsFiltered" => intval($filteredRecords),
            "data"            => $data
        ];
    }

    /**
     * Handle the wildcard textual criteria matching
     */
    private function applySearchFilters($query, Request $request)
    {
        if ($request->has('search') && !empty($request->input('search')['value'])) {
            $searchValue = trim($request->input('search')['value']);

            $query->where(function ($q) use ($searchValue) {
                if (is_numeric($searchValue)) {
                    $q->where('category_id', $searchValue);
                } else {
                    $q->where('category_name', 'like', "%{$searchValue}%");

                    if (str_starts_with(strtolower($searchValue), 'a')) {
                        $q->orWhere('category_status', 'active');
                    } elseif (str_starts_with(strtolower($searchValue), 'i')) {
                        $q->orWhere('category_status', 'inactive');
                    }
                }
            });
        }

        return $query;
    }

    /**
     * Resolve the active column mapping index array
     */
    private function applySortingOrder($query, Request $request)
    {
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
            // Default sorting order
            $query->orderBy('category_id', 'desc');
        }

        return $query;
    }

    /**
     * Format raw database column values to dynamic HTML badges
     */
    private function renderStatusBadge(string $status): string
    {
        return $status === 'active'
            ? '<span class="label label-primary">Active</span>'
            : '<span class="label label-danger">Inactive</span>';
    }

    /**
     * Build standard raw HTML string bindings safely
     */
    private function renderActionButtons($category): string
    {
        return '
            <a href="' . route('categories.edit', $category->category_id) . '" class="btn btn-info btn-sm">
                <i class="fa fa-paste"></i> Edit
            </a>
            <button type="button" class="btn btn-danger btn-sm btn-delete" 
                    data-id="' . $category->category_id . '" 
                    data-name="' . e($category->category_name) . '">
                <i class="fa fa-trash"></i> Delete
            </button>
        ';
    }
}