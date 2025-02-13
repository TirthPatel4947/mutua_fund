<?php

namespace App\Http\Controllers;

use App\Models\Mutualfund_Master;
use App\Models\Nav;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class NavController
{
    public function nav()
    {
        set_time_limit(600);  // Allow 10 minutes of execution time

        try {
            $batch_no = Str::uuid()->toString();  // Generate UUID for a unique batch number

            // Fetch all mutual funds
            $funds = MutualFund_Master::where('status', 1)
                ->orderBy('last_status_updated', 'asc')
                ->limit(600)  // Limits the result to 30 records
                ->get();

            if ($funds->isEmpty()) {
                return response()->json(['message' => 'No mutual funds found.'], 404);
            }

            // Process in chunks of 100
            $chunks = $funds->chunk(200);

            foreach ($chunks as $chunk) {
                foreach ($chunk as $fund) {
                    $url = "https://api.mfapi.in/mf/{$fund->fundcode}/latest";

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    $response = curl_exec($ch);

                    if (curl_errno($ch)) {
                        curl_close($ch);
                        continue;
                    }

                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if ($httpCode !== 200) {
                        continue;
                    }

                    $data = json_decode($response, true);
                    if (isset($data['data'][0])) {
                        $nav_value = $data['data'][0]['nav'];
                        $nav_date = $data['data'][0]['date'];
                        $formatted_date = Carbon::createFromFormat('d-m-Y', $nav_date)->format('Y-m-d');

                        if ($nav_value > 0) {
                            // Using upsert to insert or update
                            Nav::updateOrInsert(
                                [
                                    'fundname_id' => $fund->id,
                                    'date'         => $formatted_date,
                                ],
                                [
                                    'nav'         => $nav_value,
                                    'batch_no'    => $batch_no,
                                ]
                            );
                        }
                    }
                }
            }

            return response()->json(['message' => 'NAV data successfully fetched and stored.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching NAV data.'], 500);
        }
    }
}
