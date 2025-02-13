<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImportController
{
    // Method to return the import view
    public function showImportForm()
    {
        return view('importexcel');  // View for file upload form
    }

    // Method to handle file upload without validation
    public function import(Request $request)
    {
        // Retrieve the uploaded file
        $file = $request->file('excelFile');

        // If a file is uploaded, process it
        if ($file) {
            // You can save the file or handle the logic to process the file here
            $path = $file->storeAs('uploads', $file->getClientOriginalName());

            // Return success message
            return back()->with('success', 'File uploaded successfully!');
        }

        // If no file is uploaded, return an error message
        return back()->with('error', 'No file selected!');
    }
}
