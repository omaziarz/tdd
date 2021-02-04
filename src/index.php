<?php
namespace App;

require 'vendor/autoload.php';
use App\Database\Query;

$query = new Query();
$qString = $query
  ->table('test')
  ->insertValue('a', 99999)
  ->insertValue('b',  88888)
  ->insertValue('jambon', 'espagnol')
  ->getInsertString();

echo 'test';
