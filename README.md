# kaufland-task
The task involved creating a command-line program to process an XML file and push the data to a chosen database. This has been accomplished by developing a Laravel Command that can handle various file formats (XML, CSV, JSON, and DOCX), with the flexibility to add more formats easily. The processed data is then pushed to the specified database. Errors are logged in the Laravel log files, and the task is tested using test cases that identify potential issues when running the command with different file formats.

## Prerequisites
- PHP: 8.3.8
- Laravel Framework: 11.11.1
- PostgreSQL: 16.1

## Essential Files
- Command-line Program: '/app/Console/Commands/AddProductsCommand.php'
- Parsers: '/Users/zohasheikh/kaufland-task/app/Parsers/*.php'
- Migration: '/database/migrations/2024_06_22_011258_create_products_table.php'
- Test Cases: '/tests/Feature/AddProductsCommandTest.php'
- Logs: '/storage/logs/*.log'
- Data Files: '/data_files/'
- Test Files: '/tests/files/'

## Commands
- Migration: php artisan migrate
- Add Products Command: php artisan add:products {file} {dbType=pgsql}
- Test Case: php artisan test --filter=ProcessDataCommandTest
