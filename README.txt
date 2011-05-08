This is a PHP client for Sleepy Mongoose, a REST server for MongoDB.

The interface is exactly the same as the regular Mongo driver for PHP,
so you can use the same code to access the DB directly if possible and 
to fall back on this library if you can't, maybe because you have to 
use a shared server where the Mongo PHP extension is not installed.

For example usage see the example.php file.

When doing many inserts, always use the $collection->batchInsert() method instead of
the regular insert(). 

Requires PHP5.3 
