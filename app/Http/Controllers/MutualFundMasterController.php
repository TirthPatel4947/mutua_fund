<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MutualFund_Master;
use App\Models\Nav;
use Carbon\Carbon;

class MutualFundMasterController
{
    public function fetch_fund()
    {
        set_time_limit(600);

        $api = 'https://api.mfapi.in/mf';
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ]);

        $response = curl_exec($curl);
        $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            $errorMessage = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $errorMessage], 500);
        }

        curl_close($curl);

        if ($httpStatusCode === 200) {
            $data = json_decode($response, true);

            if (empty($data)) {
                return response()->json(['message' => 'No data received from the API'], 204);
            }

            $upsertData = [];
            $seenCodes = [];

            foreach ($data as $fund) {
                if (!in_array($fund['schemeCode'], $seenCodes)) {
                    $upsertData[] = [
                        'fundcode' => $fund['schemeCode'],
                        'fundname' => $fund['schemeName'],
                        'last_status_updated' => Carbon::now() // Automatically set the current timestamp
                    ];
                    $seenCodes[] = $fund['schemeCode'];
                }
            }

            if (!empty($upsertData)) {
                $chunkSize = 10000;
                $chunks = array_chunk($upsertData, $chunkSize);

                foreach ($chunks as $chunk) {
                    MutualFund_Master::upsert($chunk, ['fundcode'], ['fundname', 'last_status_updated']);
                }
            }

            // Update fund status and last_status_updated
            $this->updateFundStatus();

            return response()->json(['message' => 'Funds fetched, updated, and saved successfully']);
        } else {
            return response()->json(['error' => 'API request failed', 'status_code' => $httpStatusCode], 500);
        }
    }

    private function updateFundStatus()
    {
        // Get the date 2 days ago
        $twoDaysAgo = Carbon::now()->subDays(2);

        // Find fund IDs that have NAV records older than 2 days
        $fundIdsToUpdate = Nav::select('fundname_id')
            ->where('date', '<', $twoDaysAgo)
            ->groupBy('fundname_id')
            ->pluck('fundname_id')
            ->toArray();

        // Also find funds that have no NAV records at all
        $fundsWithNoNav = MutualFund_Master::doesntHave('nav') // Funds with no associated NAV
            ->pluck('id') // Get the fund IDs with no NAV records
            ->toArray();

        // Merge the two arrays of fund IDs to update
        $fundIdsToUpdate = array_merge($fundIdsToUpdate, $fundsWithNoNav);

        // If there are funds to update, set status to 0 and ensure last_status_updated is set to the current timestamp
        if (!empty($fundIdsToUpdate)) {
            MutualFund_Master::whereIn('id', $fundIdsToUpdate)
                ->update([
                    'status' => 0,
                    'last_status_updated' => now() // Update with the current timestamp
                ]);
        }

        // If the fund has status 1, do not change the last_status_updated
        $fundsWithStatus1 = MutualFund_Master::where('status', 1)
            ->whereIn('id', $fundIdsToUpdate)
            ->get();

        foreach ($fundsWithStatus1 as $fund) {
            // Only update the status to 0, without modifying last_status_updated
            if ($fund->status != 0) {
                $fund->status = 0;
                $fund->last_status_updated = now(); // Update last_status_updated when status is set to 0
                $fund->save();
            }
        }
    }
}
