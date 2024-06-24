<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Parsers\ProductFileParserFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class AddProductsCommand
 *
 * This command handles the process of adding products to the database from a given file
 * It supports various file types (e.g., CSV, XML, JSON) through different parsers
 * It configures the database connection based on the provided type
 * The command also ensures that the products table is created if it doesn't exist and inserts the parsed data into the table
 */

class AddProductsCommand extends Command
{
    protected $signature = 'add:products {file} {dbType=pgsql}';
    protected $description = 'Read the products from the given file and insert them into the specified database';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Handle the execution of the command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $dbType = $this->argument('dbType');
    
        try {

            //configure the database
            $this->configureDatabase($dbType);   

            //parse the products file
            $parser = ProductFileParserFactory::fetchParser($filePath);
            list($columns, $data) = $parser->parseData($filePath);

            //create products table if not created
            $this->createProductTableIfNotExists($columns);

            //insert the products data
            $this->insertProductData($columns, $data);

        } catch (\Exception $e) {

            //log the errors to the file /storage/logs/laravel.log
            Log::error('Processing error', [
                'file' => $filePath,
                'dbType' => $dbType,
                'error' => $e->getMessage()
            ]);
    
            $errorMessage = 'Processing error: ' . json_encode([
                'file' => $filePath,
                'dbType' => $dbType,
                'error' => $e->getMessage()
            ]);
            
            $this->error($errorMessage);

        }
    }
    
    /**
     * Configure the database connection based on the provided type
     *
     * @param string $dbType
     * @throws \Exception
     */
    private function configureDatabase($dbType)
    {
        try {

            switch ($dbType) {
                case 'mysql':
                    config([
                        'database.default' => 'mysql',
                        'database.connections.mysql.host' => env('DB_HOST', '127.0.0.1'),
                        'database.connections.mysql.port' => env('DB_PORT', '3306'),
                        'database.connections.mysql.database' => env('DB_DATABASE', 'database'),
                        'database.connections.mysql.username' => env('DB_USERNAME', 'root'),
                        'database.connections.mysql.password' => env('DB_PASSWORD', ''),
                    ]);
                    break;
                case 'sqlite':
                    config([
                        'database.default' => 'sqlite',
                        'database.connections.sqlite.database' => database_path('database.sqlite'),
                        'database.connections.sqlite.prefix' => '',
                    ]);
                    break;
                case 'pgsql':
                    config([
                        'database.default' => 'pgsql',
                        'database.connections.pgsql.host' => env('DB_HOST', '127.0.0.1'),
                        'database.connections.pgsql.port' => env('DB_PORT', '5432'),
                        'database.connections.pgsql.database' => env('DB_DATABASE', 'database'),
                        'database.connections.pgsql.username' => env('DB_USERNAME', 'root'),
                        'database.connections.pgsql.password' => env('DB_PASSWORD', ''),
                    ]);
                    break;
                default:
                    throw new \Exception("Unsupported database type: $dbType");
            }

        } catch (\Exception $e) {

            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            throw $e;

        }
    }
    
    /**
     * Create the 'products' table if it doesn't exist.
     *
     * @param array $columns
     */
    private function createProductTableIfNotExists($columns)
    {
        if (!Schema::hasTable('products')) {
     
            Schema::create('products', function (Blueprint $table) use ($columns) {
     
                $table->string('entity_id')->primary();
                $table->string('sku')->unique();
     
                foreach ($columns as $column) {
                    if ($column === 'name') {

                        $table->string($column);

                    } elseif ($column !== 'entity_id' && $column !== 'sku') {

                        $table->text($column)->nullable();

                    }
                }
     
                $table->timestamps();
     
            });
        }
    }    

    /**
     * Insert data into the 'products' table.
     *
     * @param array $columns
     * @param array $data
     */
    private function insertProductData($columns, $data)
    {
        // Start a new transaction
        DB::beginTransaction();
        
        $successfulInserts = 0;
        foreach ($data as $rowData) {

            if (empty($rowData['name'])) {

                $errorMessage = "Product name cannot be empty for entity_id: " . $rowData['entity_id'] . " and sku: " . $rowData['sku'];
                $this->error($errorMessage);
                Log::error($errorMessage);
                continue;

            }

            $timestamp = now();
            $rowData['created_at'] = $timestamp;
            $rowData['updated_at'] = $timestamp;            
            
            try {

                DB::table('products')->insert($rowData);
                $successfulInserts++;            
            } catch (\Illuminate\Database\QueryException $e) {
                //code for identifying the duplicate entry
                if ($e->getCode() == '23505') {

                    $errorMessage = "Duplicate entry for sku: " . $rowData['sku'];
                    $this->error($errorMessage);
                    Log::error($errorMessage);

                } else {
                    //roll back the transaction in case error came
                    DB::rollBack();
                    $errorMessage = "SQL error for entity_id: " . $rowData['entity_id'] . " and sku: " . $rowData['sku'] . " - " . $e->getMessage();
                    $this->error($errorMessage);
                    Log::error($errorMessage);

                }
            }
        }

        if ($successfulInserts > 0) {
            //commit the transaction to database in case everything goes fine
            DB::commit();
            $this->info('Data has been successfully inserted into the database.');
        } else {
            //roll back to maintain the clean state
            DB::rollBack();
            $errorMessage = 'No data was inserted into the database.';
            $this->error($errorMessage);
            Log::error($errorMessage);
        }
    }
}
