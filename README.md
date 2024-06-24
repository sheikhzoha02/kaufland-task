# kaufland-task
The task was to create a command-line program that processes an XML file and pushes the data to a chosen database. This has been achieved by developing a Laravel Command capable of processing various file formats (XML, CSV, JSON, and DOCX) with the flexibility to easily add more formats. The data is then pushed to the specified database. The errors are logged in the Laravel log files located in 'storage/logs/*.log'. The task is tested using the test case located at '/tests/Feature/AddProductsCommandTest.php'.


## Prerequisites
- PHP: 8.3.8
- Laravel Framework: 11.11.1
- PostgreSQL: 16.1
