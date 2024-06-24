<?php

namespace App\Parsers;

use Illuminate\Support\Facades\Log;

/**
 * Class XMLParser
 *
 * Inputs the XML file
 * Read and parse the results into columns and data
 */

class XMLParser implements ProductFileParserInterface {
    public function parseData($filePath) {
        $data = [];
        $columns = [];

        try {

            $xml = simplexml_load_file($filePath);
            if ($xml === false) {
                throw new \Exception("Failed to load XML file: $filePath");
            }

            foreach ($xml->item as $row) {
                $rowData = [];
                foreach ($row as $key => $value) {
                    $columns[] = $key;
                    $rowData[$key] = (string)$value;
                }
                $data[] = $rowData;
            }
            $columns = array_unique($columns);
            
        } catch (\Exception $e) {

            $errorMessage = "An error occurred while parsing the XML file: $filePath - " . $e->getMessage();
            Log::error($errorMessage);
            throw $e;

        }

        return [$columns, $data];
    }
}
