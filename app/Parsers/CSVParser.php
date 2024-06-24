<?php

namespace App\Parsers;

use Illuminate\Support\Facades\Log;

/**
 * Class CSVParser
 *
 * Inputs the CSV file and parse the results into the data and columns
 */

class CSVParser implements ProductFileParserInterface {

    public function parseData($filePath) {
        $data = [];
        $columns = [];

        try {

            $delimiter = ',';
            if (($resource = fopen($filePath, "r")) !== FALSE) {
                $columns = fgetcsv($resource, 2000, $delimiter);

                while (($row = fgetcsv($resource, 2000, $delimiter)) !== FALSE) {
                    if (count($columns) == count($row)) {
                        $data[] = array_combine($columns, $row);
                    } else {
                        $errorMessage = "Row and column data count mismatch: " . json_encode($row);
                        Log::error($errorMessage);
                    }
                }

                fclose($resource);

            } else {
                $errorMessage = "Failed to open file: $filePath";
                Log::error($errorMessage);
                throw new \Exception($errorMessage);
            }

        } catch (\Exception $e) {

            $errorMessage = "An error occurred while parsing the CSV file: " . $e->getMessage();
            Log::error($errorMessage);
            throw $e;

        }

        return [$columns, $data];
    }
}
