<?php

namespace Tests\Feature;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class AddProductsCommandTest
 *
 * The first test case checks for the correct parsing of an XML file and add products to the database
 * The second test case checks for the correct parsing of a CSV file and add products to the database
 * The third test case checks for the correct parsing of a JSON file and add products to the database
 * The fourth test case checks for the correct parsing of a DOCX file and add products to the database
 * The fifth test case checks for the unsupported file type
 * The last test case checks for the unsupported database type
 */

class AddProductsCommandTest extends TestCase
{
    use DatabaseTransactions;

    /**     
     * Test processing of an XML file. 
     */
     public function testProcessXmlFile()
    {
        $filePath = base_path('tests/files/feed_test.xml');
        Artisan::call('add:products', ['file' => $filePath, 'dbType' => 'pgsql']);
        
        $output = Artisan::output();
        $this->assertStringContainsString('Data has been successfully inserted into the database.', $output);
        
        $this->assertDatabaseHas('products', [
            "entity_id" => "340-1",
            "CategoryName" => "Green Mountain Ground Coffee",
            "sku" => "20-1",
            "name" => "Green Mountain Coffee French Roast Ground Coffee 24 2.2oz Bag",
            "price" => "41.6000",
            "Brand" => "Green Mountain Coffee",
            "CaffeineType" => "Caffeinated"
        ]);
    }

    /**     
     * Test processing of a CSV file. 
     */ 
     public function testProcessCsvFile()
    {
        $filePath = base_path('tests/files/feed_test.csv');
        Artisan::call('add:products', ['file' => $filePath, 'dbType' => 'pgsql']);
        
        $output = Artisan::output();
        $this->assertStringContainsString('Data has been successfully inserted into the database.', $output);
        
        $this->assertDatabaseHas('products', [
            "entity_id" => "340-1",
            "CategoryName" => "Green Mountain Ground Coffee",
            "sku" => "20-1",
            "name" => "Green Mountain Coffee French Roast Ground Coffee 24 2.2oz Bag",
            "price" => "41.6000",
            "Brand" => "Green Mountain Coffee",
            "CaffeineType" => "Caffeinated"
        ]);
    }

    /**     
     * Test processing of a JSON file. 
     */ 
     public function testProcessJsonFile()
    {
        $filePath = base_path('tests/files/feed_test.json');
        Artisan::call('add:products', ['file' => $filePath, 'dbType' => 'pgsql']);
        
        $output = Artisan::output();
        $this->assertStringContainsString('Data has been successfully inserted into the database.', $output);
        
        $this->assertDatabaseHas('products', [
            "entity_id" => "340-1",
            "CategoryName" => "Green Mountain Ground Coffee",
            "sku" => "20-1",
            "name" => "Green Mountain Coffee French Roast Ground Coffee 24 2.2oz Bag",
            "price" => "41.6000",
            "Brand" => "Green Mountain Coffee",
            "CaffeineType" => "Caffeinated"
        ]);
    }

    /**     
     * Test processing of a DOCX file. 
     */     
    public function testProcessDocxFile()
    {
        $filePath = base_path('tests/files/feed_test.docx');
        Artisan::call('add:products', ['file' => $filePath, 'dbType' => 'pgsql']);
        
        $output = Artisan::output();
        $this->assertStringContainsString('Data has been successfully inserted into the database.', $output);
        
        $this->assertDatabaseHas('products', [
            "entity_id" => "340-1",
            "CategoryName" => "Green Mountain Ground Coffee",
            "sku" => "20-1",
            "name" => "Green Mountain Coffee French Roast Ground Coffee 24 2.2oz Bag",
            "price" => "41.6000",
            "Brand" => "Green Mountain Coffee",
            "CaffeineType" => "Caffeinated"
        ]);
    }

    /**     
     * Test processing of an Unsupported file type
     */  
    public function testProcessUnsupportedFile()
    {
        $filePath = base_path('tests/files/feed_test.txt');

        Log::shouldReceive('error')
            ->once()
            ->with(\Mockery::on(function ($message) {
                return strpos($message, 'Processing error') !== false;
            }), \Mockery::on(function ($context) {
                return isset($context['file']) && $context['file'] === base_path('tests/files/feed_test.txt') &&
                       isset($context['dbType']) && $context['dbType'] === 'pgsql' &&
                       isset($context['error']) && strpos($context['error'], 'Unsupported file type: txt') !== false;
            }));

        Artisan::call('add:products', ['file' => $filePath, 'dbType' => 'pgsql']);
        
        $output = Artisan::output();
        $this->assertStringContainsString('Unsupported file type: txt', $output);
    }

    /**     
     * Test processing of an Unsupported database type
     */  
    public function testProcessUnsupportedDataBase()
    {
        $filePath = base_path('tests/files/feed_test.xml');
        
        Log::shouldReceive('error')
            ->once()
            ->with(\Mockery::on(function ($message) {
                return strpos($message, 'Processing error') !== false;
            }), \Mockery::on(function ($context) {
                return isset($context['file']) && $context['file'] === base_path('tests/files/feed_test.xml') &&
                       isset($context['dbType']) && $context['dbType'] === 'mongodb' &&
                       isset($context['error']) && strpos($context['error'], 'Unsupported database type: mongodb') !== false;
            }));
        
        Artisan::call('add:products', ['file' => $filePath, 'dbType' => 'mongodb']);
        
        $output = Artisan::output();
        $this->assertStringContainsString('Unsupported database type: mongodb', $output);
    }
}
