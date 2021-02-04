<?php

class Query
{
  //private \PDO $conn;

  private string $table;

  private array $select = [];

  private array $insertValues = [];

  // on separe car plusieurs conditions peuvent s'appliquer au meme param
  private array $where = [];
  private array $whereParams = [];

  private string $groupBy;
  private string $limit;

  function __construct() {
    //$this->conn = Connection::getInstance();
  }

  private function writeSelect(&$queryString): void {
    if (isset($this->select)) {
      $queryString[] = join(', ', $this->select);
    } else {
      $queryString[] = '*';
    }
  }

  private function writeWhere(&$queryString): void {
    if ($this->where) {
      $queryString[] = 'WHERE';
      $queryString[] = join(' ', $this->where);
    }
  }
  private function writeTable(&$queryString): void {
    if (isset($this->table)) {
      $queryString[] = 'FROM';
      $queryString[] = $this->table;
    }
  }
  private function writeGroupBy(&$queryString): void {
    if (isset($this->groupBy)) {
      $queryString[] = 'GROUP BY';
      $queryString[] = $this->groupBy;
    }
  }
  private function writeLimit(&$queryString): void {
    if (isset($this->limit)) {
      $queryString[] = 'LIMIT';
      $queryString[] = $this->limit;
    }
  }

  function getSelectString()
  {
    $queryString[] = 'SELECT';
    $this->writeSelect($queryString);
    $this->writeTable($queryString);
    $this->writeWhere($queryString);
    $this->writeGroupBy($queryString);
    $this->writeLimit($queryString);
    return implode(' ', $queryString);
  }

  function getUpdateString() {
    $queryString[] = 'UPDATE';
    if (isset($this->table)) {
      $queryString[] = $this->table;
    }
    $queryString[] = 'SET';
    $columns = [];
    foreach($this->insertValues as $key => $value) {
      $columns[] = $key . ' = :' . $key;
    }
    $queryString[] = join(', ', $columns);
    $this->writeWhere($queryString);
    return join(' ', $queryString);
  }

  function getDeleteString() {
    $queryString[] = 'DELETE';
    $this->writeTable($queryString);
    $this->writeWhere($queryString);
    return join(' ', $queryString);
  }

  function getInsertString() {
    $queryString[] = 'INSERT INTO';
    if (isset($this->table)) {
      $queryString[] = $this->table;
      $queryString[] = '(';
    }
    $columns = [];
    foreach($this->insertValues as $key => $value) {
      $columns[] = $key;
    }
    $queryString[] = join(', ', $columns);
    $queryString[] = ') VALUES (';
    $columns = [];
    foreach ($this->insertValues as $key => $value) {
      $columns[] = ':'.$key;
    }
    $queryString[] = join(', ', $columns);
    $queryString[] = ')';
    return $queryString = join(' ', $queryString);
  }

  function get() {
    $statement = $this->conn->prepare($this->getSelectString());
    foreach ($this->whereParams as $key => $value) {
      $statement->bindParam(':'.$key, $this->whereParams[$key]);
    }
    if ($statement->execute()) {
      return $statement->fetchAll();
    }
    return 'Error with query';
  }

  function delete() {
    $statement = $this->conn->prepare($this->getDeleteString());
    foreach ($this->whereParams as $key => $value) {
      $statement->bindParam(':'.$key, $this->whereParams[$key]);
    }
    return $statement->execute();
  }

  function insertValue(string $column, $value) {
    $this->insertValues[$column] = $value;
    return $this;
  }

  function update() {
    $statement = $this->conn->prepare($this->getUpdateString());
    foreach ($this->insertValues as $key => $value) {
      $statement->bindParam(':' . $key, $this->insertValues[$key]);
    }
    foreach ($this->whereParams as $key => $value) {
      $statement->bindParam(':'.$key, $this->whereParams[$key]);
    }

    return $statement->execute();
  }

  function insert() {
    $statement = $this->conn->prepare($this->getInsertString());

    foreach ($this->insertValues as $key => $value) {
      $statement->bindParam(':' . $key, $this->insertValues[$key]);
    }
    return $statement->execute();
  }

  function select(string $columns): self
  {
    $this->select[] = $columns;
    return $this;
  }

  function table(string $table): self
  {
    $this->table = $table;
    return $this;
  }

  function where(string $left, string $comparator, $right): self
  {
    if (empty($this->where)) {
      $this->where[] = $left . ' ' . $comparator . ' :'.count($this->whereParams);
      $this->whereParams[] = $right;
    } else {
      $this->where[] = 'AND';
      $this->where[] = $left . ' ' . $comparator . ' :'.count($this->whereParams);
      $this->whereParams[] = $right;
    }
    return $this;
  }

  function orWhere(string $left, string $comparator, $right): self
  {
    if (empty($this->where)) {
      $this->where[] = $left . ' ' . $comparator . ' :'.count($this->whereParams);
      $this->whereParams[] = $right;
    } else {
      $this->where[] = 'OR';
      $this->where[] = $left . ' ' . $comparator . ' :'.count($this->whereParams);
      $this->whereParams[] = $right;
    }
    return $this;
  }

  function groupBy(string $column): self
  {
    $this->groupBy = $column;
    return $this;
  }

  function limit(int $limit): self
  {
    $this->limit = $limit;
    return $this;
  }
}