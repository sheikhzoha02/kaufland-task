<?php

namespace App\Parsers;

use Illuminate\Support\Facades\Log;

/**
 * Class JSONParser
 *
 * Inputs the JSON file
 * Read and parse the results into columns and products
 */

class JSONParser implements ProductFileParserInterface {
    public function parseData($filePath) {

        try {

            $json = file_get_contents($filePath);

            if ($json === false) {
                throw new \Exception("Failed to read the JSON file: $filePath");
            }
            
            $data = json_decode($json, true);

            if (!isset($data['products'])) {
                throw new \Exception("Invalid JSON format: 'products' key not found.");
            }

            $products = $data['products'];

            if (empty($products)) {
                throw new \Exception("The 'products' array is empty.");
            }
            $columns = array_keys(reset($products));

        } catch (\Exception $e) {

            $errorMessage = "An error occurred while parsing the JSON file: $filePath - " . $e->getMessage();
            Log::error($errorMessage);
            throw $e;

        }

        return [$columns, $products];
    }
}
