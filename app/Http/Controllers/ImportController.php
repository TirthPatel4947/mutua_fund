<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FundImport;
use App\Models\Sample;
use App\Models\Portfolio;
use App\Models\MutualFund_Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\DB;

class ImportController 
{
    // Show the import page
    public function showImportPage()
    {
        return view('import'); // Ensure 'import.blade.php' exists in the resources/views directory
    }

    // Handle file upload and import data
    public function submit(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            // Get the authenticated user's ID
            $userId = Auth::id();

            // Ensure userId is not null
            if (!$userId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Pass user ID to FundImport
            Excel::import(new FundImport($userId), $request->file('excel_file'));

            return response()->json(['message' => 'Data imported successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error importing data: ' . $e->getMessage()], 500);
        }
    }

    // Fetch the latest inserted data for the logged-in user
    public function list()
    {
        $userId = Auth::id(); // Get the logged-in user ID
    
        $data = Sample::with('portfolio', 'fund') // Include 'fund' to avoid optional() use later
            ->where('user_id', $userId) // Filter by authenticated user
            ->latest()
            ->take(50)
            ->get();
    
        $formattedData = $data->map(function ($item) {
            return [
                'id'          => $item->id,
                'portfolio'   => $item->portfolio ? $item->portfolio->name : '',
                'fund_name'   => $item->fund ? $item->fund->fundname : '',
                'date'        => $item->date ?? '',
                'unit'        => $item->unit ?? '', // ✅ Added Unit Column
                'price'       => $item->price ?? '',
                'total'       => $item->total ?? '',
                'status'      => $item->status === 1 ? 'Buy' : 'Sale', 
                'created_at'  => $item->created_at ?? '',
                'updated_at'  => $item->updated_at ?? '',
            ];
        });
    
        return response()->json($formattedData);
    }
    
    public function fetchOptions($type, Request $request)
    {
        $search = $request->query('search', '');
        $userId = auth()->id(); // Get logged-in user ID

        if ($type === 'portfolio') {
            $query = Portfolio::where('user_id', $userId)->select('id', 'name as name');
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%{$search}%"); // Apply search filter for portfolios
            }
        } elseif ($type === 'fund_name') {
            $query = MutualFund_Master::select('id', 'fundname as name');
            if (!empty($search)) {
                $query->where('fundname', 'LIKE', $search . '%'); // Apply search filter for fund names
            }
        } else {
            return response()->json([]);
        }

        return response()->json($query->get());
    }

    public function updateRecord(Request $request) {
        try {
            $record = Sample::find($request->id);
    
            if (!$record) {
                Log::error("Record not found for ID: {$request->id}");
                return response()->json(['error' => 'Record not found'], 404);
            }
    
            Log::info("Before update:", $record->toArray());
    
            // ✅ Allowed fields for update
            $validFields = ['portfolio_id', 'fundname_id'];
    
            if (!$request->has('field') || !$request->has('value')) {
                Log::warning("Field or value missing", ['request' => $request->all()]);
                return response()->json(['error' => 'Field or value missing'], 400);
            }
    
            if (!in_array($request->field, $validFields)) {
                Log::warning("Invalid field attempt", ['field' => $request->field]);
                return response()->json(['error' => 'Invalid field'], 400);
            }
    
            // ✅ Ensure value is an integer (ID)
            if (!is_numeric($request->value)) {
                return response()->json(['error' => 'Invalid value. Must be an ID.'], 400);
            }
    
            // ✅ Update field dynamically
            $record->{$request->field} = (int) $request->value;
    
            if ($record->isDirty()) {
                $record->save();
                Log::info("After update:", $record->toArray());
                return response()->json(['success' => true, 'message' => 'Record updated successfully!']);
            } else {
                return response()->json(['success' => false, 'message' => 'No changes detected.']);
            }
        } catch (\Exception $e) {
            Log::error("Error updating record", ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    
    public function storeReport(Request $request)
    {
        Log::info('Received Data:', $request->all()); // ✅ Log received request data
    
        $validatedData = $request->validate([
            'report_data' => 'required|array',
            'report_data.*.portfolio' => 'required|string',
            'report_data.*.fund_name' => 'required|string',
            'report_data.*.date' => 'required|date',
            'report_data.*.unit' => 'required|numeric',
            'report_data.*.price' => 'required|numeric',
            'report_data.*.status' => 'required|string',
        ]);
    
        Log::info('Validated Data:', $validatedData); // ✅ Log validated data
    
        // ✅ Begin database transaction
        DB::beginTransaction();
    
        try {
            // ✅ Insert data into report_history
            DB::insert("
                INSERT INTO report_history (user_id, portfolio_id, fundname_id, date, unit, price, total, status, created_at, updated_at)
                SELECT 
                    user_id, 
                    portfolio_id, 
                    fundname_id, 
                    date, 
                    unit * (status * 2 - 1),   -- ✅ Converts unit to negative if status = 0
                    price * (status * 2 - 1),  -- ✅ Converts price to negative if status = 0
                    total * (status * 2 - 1),  -- ✅ Converts total to negative if status = 0
                    status, 
                    NOW(), 
                    NOW()
                FROM sample
                WHERE user_id = ?", [auth()->id()]
            );
    
            // ✅ Delete the logged-in user's data from the sample table
            DB::delete("DELETE FROM sample WHERE user_id = ?", [auth()->id()]);
    
            DB::commit(); // ✅ Commit transaction
    
            return response()->json([
                'success' => true,
                'message' => 'Data inserted successfully and deleted from sample table.',
                'redirect_url' => route('dashboard')
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // ❌ Rollback transaction on failure
            Log::error('Error in storeReport: ' . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing data.'
            ], 500);
        }
    }
    
    
    
}
