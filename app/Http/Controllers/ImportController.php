<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FundImport;
use App\Models\sample;

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
        // Validate the uploaded file
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            // Import data using FundImport
            Excel::import(new FundImport, $request->file('excel_file'));

            return response()->json(['message' => 'Data imported successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error importing data: ' . $e->getMessage()], 500);
        }
    }

    // Fetch the latest inserted data
    public function list()
    {
        $data = sample::latest()->take(50)->get(); // Fetch last 50 records
        return response()->json($data);
    }
}
