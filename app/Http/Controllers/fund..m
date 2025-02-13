<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MutualFund_Master;

class MutualFundMasterController
{
    public function fetch_fund()
    {
        $api = 'https://api.mfapi.in/mf';
     
        // Initialize cURL
        $curl = curl_init();
    
        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $api,
            CURLOPT_RETURNTRANSFER => true, // Return response as a string
            CURLOPT_FOLLOWLOCATION => true, // Follow redirects
            CURLOPT_TIMEOUT => 30, // Timeout in seconds
            CURLOPT_SSL_VERIFYHOST => 0, // Skip SSL host verification (optional)
            CURLOPT_SSL_VERIFYPEER => 0, // Skip SSL peer verification (optional)
        ]);
    
        // Execute cURL request
        $response = curl_exec($curl);
        $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
        // Check for cURL errors
        if (curl_errno($curl)) {
            $errorMessage = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $errorMessage], 500);
        }
    
        // Close cURL
        curl_close($curl);
     
        // Process the response
        if ($httpStatusCode === 200) {
            $data = json_decode($response, true); // Decode JSON response
    
            if (empty($data)) {
                return response()->json(['message' => 'No data received from the API'], 204);
            }
    
            // Prepare the update data
            $updatedData = [];
            $existingFundCodes = MutualFund_Master::pluck('fundcode')->toArray(); // Fetch existing fund codes
    
            foreach ($data as $fund) {
                if (in_array($fund['schemeCode'], $existingFundCodes)) {
                    // Update the existing record
                    $updatedData[] = [
                        'fundcode' => $fund['schemeCode'],
                        'fundname' => $fund['schemeName'],
                    ];
                } else {
                    // Prepare for new records
                    $batchData[] = [
                        'fundname' => $fund['schemeName'],
                        'fundcode' => $fund['schemeCode'],
                    ];
                }
            }
    
            // Update existing records
            foreach ($updatedData as $update) {
                MutualFund_Master::where('fundcode', $update['fundcode'])
                    ->update(['fundname' => $update['fundname']]);
            }
    
            // Insert new records in chunks
            if (!empty($batchData)) {
                $chunkSize = 500; // Chunk size define
                $chunks = array_chunk($batchData, $chunkSize); 
                
                foreach ($chunks as $chunk) {
                    MutualFund_Master::insert($chunk); // Insert data chunk
                }
    
                return response()->json(['message' => 'Funds fetched, updated and saved successfully']);
            } else {
                return response()->json(['message' => 'No new funds to insert']);
            }
        } else {
            return response()->json(['error' => 'API request failed', 'status_code' => $httpStatusCode], 500);
        }
    }
}


///////////////////////////////////////////////////////
wit upsert
//////////////////////////////////
 <?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MutualFund_Master;

class MutualFundMasterController
{
    public function fetch_fund()
    {
        // Increase execution time limit to 600 seconds
        set_time_limit(600); 
        
        $api = 'https://api.mfapi.in/mf';
     
        // Initialize cURL
        $curl = curl_init();
    
        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $api,
            CURLOPT_RETURNTRANSFER => true, // Return response as a string
            CURLOPT_FOLLOWLOCATION => true, // Follow redirects
            CURLOPT_TIMEOUT => 30, // Timeout in seconds
            CURLOPT_SSL_VERIFYHOST => 0, // Skip SSL host verification (optional)
            CURLOPT_SSL_VERIFYPEER => 0, // Skip SSL peer verification (optional)
        ]);
    
        // Execute cURL request
        $response = curl_exec($curl);
        $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
        // Check for cURL errors
        if (curl_errno($curl)) {
            $errorMessage = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $errorMessage], 500);
        }               
    
        // Close cURL
        curl_close($curl);
     
        // Process the response
        if ($httpStatusCode === 200) {
            $data = json_decode($response, true); // Decode JSON response
    
            if (empty($data)) {
                return response()->json(['message' => 'No data received from the API'], 204);
            }
    
            // Prepare the data for upsert without duplicates
            $upsertData = [];
            $seenCodes = [];
            
            foreach ($data as $fund) {
                if (!in_array($fund['schemeCode'], $seenCodes)) {
                    $upsertData[] = [
                        'fundcode' => $fund['schemeCode'],
                        'fundname' => $fund['schemeName'],
                    ];
                    $seenCodes[] = $fund['schemeCode'];
                }
            }

            // Upsert new records in chunks
            if (!empty($upsertData)) {
                $chunkSize = 2000; // Chunk size define
                $chunks = array_chunk($upsertData, $chunkSize);
                
                foreach ($chunks as $chunk) {
                    MutualFund_Master::upsert($chunk, ['fundcode'], ['fundname']); // Use upsert
                }
            }
    
            return response()->json(['message' => 'Funds fetched, updated, and saved successfully']);
        } else {
            return response()->json(['error' => 'API request failed', 'status_code' => $httpStatusCode], 500);
        }
    }
}


 