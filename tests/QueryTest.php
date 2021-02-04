<?php

use PHPUnit\Framework\TestCase;
use App\Database\Query;

class QueryTest extends TestCase
{
  public function testSelectQuery() {
    $query = new Query;
    $qString = $query
      ->select('a')
      ->table('table')
      ->where('conditionA', '>', 'conditionB')
      ->getSelectString();
    $this->assertEquals('SELECT a FROM table WHERE conditionA > :0', $qString);
  }

  public function testSelectHandlesNoSelect() {
    $query = new Query;
    $qString = $query
      ->table('table')
      ->where('conditionA', '>', 'conditionB')
      ->getSelectString();
    $this->assertEquals('SELECT * FROM table WHERE conditionA > :0', $qString);
  }

  public function testUpdateQuery() {
    $query = new Query;
    $qString = $query
      ->table("test")
      ->insertValue('jambon', 'polonais')
      ->where('jambon', '=', 'espagnol')
      ->getUpdateString();
    $this->assertEquals('UPDATE test SET jambon = :jambon WHERE jambon = :0', $qString);
  }

  public function testDeleteQuery() {
    $query = new Query();
    $qString = $query
      ->table('test')
      ->where('jambon', '=', 'parme')
      ->getDeleteString();
    $this->assertEquals('DELETE FROM test WHERE jambon = :0', $qString);
  }

  public function testInsertQuery() {
    $query = new Query();
    $qString = $query
      ->table('test')
      ->insertValue('a', 99999)
      ->insertValue('b',  88888)
      ->insertValue('jambon', 'espagnol')
      ->getInsertString();
    $this->assertEquals('INSERT INTO test ( a, b, jambon ) VALUES ( :a, :b, :jambon )', $qString);
  }
}