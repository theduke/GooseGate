This is a PHP client for Sleepy Mongoose, a REST server for MongoDB (https://github.com/kchodorow/sleepy.mongoose).

I was inspired by Mongate (https://github.com/bcoe/mongate), the same thing for Python.


The interface is mostly the same as the regular Mongo driver for PHP,
so you can use the same code to access the DB directly if possible and 
fall back on this library if you can't, maybe because you have to 
use a shared server where the Mongo PHP extension is not installed.

Mongo username/password authentication is supported.

For example usage see the example.php file.

When doing many inserts, always use the $collection->batchInsert() method instead of
the regular insert(). 

Requires PHP5.3.
