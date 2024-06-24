<?php

namespace App\Parsers;
use Illuminate\Support\Facades\Log;

class ProductFileParserFactory {
    public static function fetchParser($filePath) {

        try {

            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

            switch (strtolower($fileExtension)) {
                case 'xml':
                    return new XMLParser();
                case 'csv':
                    return new CSVParser();
                case 'json':
                    return new JSONParser();
                case 'docx':
                    return new DOCXParser();
                default:
                    throw new \Exception("Unsupported file type: $fileExtension");
            }
            
        } catch (\Exception $e) {

            $errorMessage = "Error in ProductFileParserFactory for file $filePath: " . $e->getMessage();
            Log::error($errorMessage);
            throw $e;

        }
    }
}
