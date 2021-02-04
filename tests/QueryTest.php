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

  public function testSelectWithGroupByAndLimit() {
    $query = new Query;
    $qString = $query
      ->select('a')
      ->table('table')
      ->where('conditionA', '>', 'conditionB')
      ->groupBy('a')
      ->limit(3)
      ->getSelectString();
    $this->assertEquals('SELECT a FROM table WHERE conditionA > :0 GROUP BY a LIMIT 3', $qString);
  }

  public function testMissingTableNameThrows() {
    $query = new Query;
    try {
      $query
        ->select('a')
        ->where('conditionA', '>', 'conditionB')
        ->groupBy('a')
        ->limit(3)
        ->getSelectString();
    } catch (Exception $e) {
      $this->assertEquals('missing table', $e->getMessage());
    }

  }

  public function testMissingInsertValuesOnUpdate() {
    $query = new Query;
    try {
      $query
        ->table("test")
        ->where('jambon', '=', 'espagnol')
        ->getUpdateString();
    } catch (Exception $e) {
      $this->assertEquals('missing values to insert/update', $e->getMessage());
    }
  }

  public function testMissingInsertValuesOnInsert() {
    $query = new Query;
    try {
      $query
        ->table('test')
        ->getInsertString();
    } catch (Exception $e) {
      $this->assertEquals('missing values to insert/update', $e->getMessage());
    }
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

  public function testInsert() {
    $query = new Query();
    $qString = $query
      ->table('test')
      ->insertValue('a', 99999)
      ->insertValue('b',  88888)
      ->insertValue('jambon', 'espagnol')
      ->insert();
    $this->assertEquals(true, $qString);
  }

  public function testSelect() {
    $query = new Query;
    $qString = $query
      ->select('a')
      ->table('test')
      ->where('jambon', '=', 'espagnol')
      ->get();
    $this->assertIsArray($qString);
  }

  public function testDelete() {
    $query = new Query();
    $qString = $query
      ->table('test')
      ->where('jambon', '=', 'parme')
      ->delete();
    $this->assertEquals(true, $qString);
  }

  public function testUpdate() {
    $query = new Query;
    $qString = $query
      ->table("test")
      ->insertValue('jambon', 'polonais')
      ->where('jambon', '=', 'espagnol')
      ->update();
    $this->assertEquals(true, $qString);
  }
}