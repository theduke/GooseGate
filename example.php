<?php

require 'lib/GooseGate/HttpClient.php';
require 'lib/GooseGate/Mongo.php';
require 'lib/GooseGate/MongoCollection.php';
require 'lib/GooseGate/MongoCursor.php';
require 'lib/GooseGate/MongoDB.php';

use GooseGate\Mongo;

$con = new Mongo('myserver.com:27080');

$con->authenticate('user', 'password');

$db = $con->testdb;
$coll = $db->testcoll;

$oid = $coll->insert(array('a' => 1, 'b' => '20'));

$oid = $coll->insert(array('a' => 1, 'b' => '30'));
$coll->update(array('_id' => $oid), array('a' => 33));
$coll->remove(array('_id' => $oid));

$data = $coll->find(array('b' => '20'), array('a'));
