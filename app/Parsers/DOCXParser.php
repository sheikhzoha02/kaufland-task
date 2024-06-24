<?php

namespace App\Parsers;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use Illuminate\Support\Facades\Log;

/**
 * Class DOCXParser
 *
 * Inputs the DOCX file and look for the Table instance
 * Read the table and parse the results into columns and data
 */

class DOCXParser implements ProductFileParserInterface {
    public function parseData($filePath) {
        $data = [];
        $columns = [];
        $isFirstRow = true;

        try {
            
            $phpWord = IOFactory::load($filePath);
            
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if ($element instanceof Table) {
                        foreach ($element->getRows() as $row) {
                            $cellData = [];
                            foreach ($row->getCells() as $column) {
                                $columnValue = '';

                                foreach ($column->getElements() as $cellContent) {
                                    if ($cellContent instanceof TextRun) {
                                        foreach ($cellContent->getElements() as $textContent) {
                                            $columnValue .= $textContent->getText();
                                        }
                                    }
                                }

                                $cellData[] = trim($columnValue);
                            }
                            
                            if ($isFirstRow) {
                                $columns = $cellData;
                                $isFirstRow = false;
                            } else {
                                if (count($columns) === count($cellData)) {
                                    $data[] = array_combine($columns, $cellData);
                                } else {
                                    $errorMessage = "Row and column data count mismatch: " . json_encode($cellData);
                                    Log::error($errorMessage);
                                }
                            }
                        }
                    }
                }
            }

        } catch (\Exception $e) {

            $errorMessage = "An error occurred while parsing the DOCX file: " . $e->getMessage();
            Log::error($errorMessage);
            throw $e;

        }

        return [$columns, $data];
    }
}
