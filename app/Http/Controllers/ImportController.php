<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FundImport;
use App\Models\Sample;
use Illuminate\Support\Facades\Auth;

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
                'price'       => $item->price ?? '',
                'total'       => $item->total ?? '',
                'created_at'  => $item->created_at ?? '',
                'updated_at'  => $item->updated_at ?? '',
            ];
        });
    
        return response()->json($formattedData);
    }
}
