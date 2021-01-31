<?php

namespace App\Core\Database;

class Query
{
  private \PDO $conn;

  private string $table;

  private array $select = [];

  private array $insertValues = [];

  // on separe car plusieurs conditions peuvent s'appliquer au meme param
  private array $where = [];
  private array $whereParams = [];

  private string $groupBy;
  private string $limit;

  function __construct() {
    $this->conn = Connection::getInstance();
  }

  function get()
  {
    $queryString[] = 'SELECT';
    if (isset($this->select) && !empty($this->select)) {
      $queryString[] = join(', ', $this->select);
    } else {
      $queryString[] = '*';
    }

    if (isset($this->table)) {
      $queryString[] = 'FROM';
      $queryString[] = $this->table;
    }

    if ($this->where) {
      $queryString[] = 'WHERE';
      $queryString[] = join(' ', $this->where);
    }
    if (isset($this->groupBy)) {
      $queryString[] = 'GROUP BY';
      $queryString[] = $this->groupBy;
    }
    if (isset($this->limit)) {
      $queryString[] = 'LIMIT';
      $queryString[] = $this->limit;
    }

    $queryString = implode(' ', $queryString);
    $statement = $this->conn->prepare($queryString);
    foreach ($this->whereParams as $key => $value) {
      $statement->bindParam(':'.$key, $this->whereParams[$key]);
    }
    if ($statement->execute()) return $statement->fetchAll();
    return 'Error with query';
  }

  function delete() {
    $queryString[] = 'DELETE';
    if (isset($this->table)) {
      $queryString[] = 'FROM';
      $queryString[] = $this->table;
    }
    if ($this->where) {
      $queryString[] = 'WHERE';
      $queryString[] = join(' ', $this->where);
    }
    $queryString = implode(' ', $queryString);
    $statement = $this->conn->prepare($queryString);
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
    $queryString[] = 'UPDATE';
    if (isset($this->table)) {
      $queryString[] = $this->table;
    }
    $queryString[] = 'SET';
    $columns = [];
    foreach($this->insertValues as $key => $value) {
      $columns[] = $key . ' = ' . ':'.$key;
    }
    $queryString[] = join(', ', $columns);
    if ($this->where) {
      $queryString[] = 'WHERE';
      $queryString[] = join(' ', $this->where);
    }
    $queryString = join(' ', $queryString);
    $statement = $this->conn->prepare($queryString);
    foreach ($this->insertValues as $key => $value) {
      $statement->bindParam(':' . $key, $this->insertValues[$key]);
    }
    foreach ($this->whereParams as $key => $value) {
      $statement->bindParam(':'.$key, $this->whereParams[$key]);
    }

    return $statement->execute();
  }

  function insert() {
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
    $queryString = join(' ', $queryString);
    $statement = $this->conn->prepare($queryString);

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

  /**
   * @param string $table
   * @return mixed
   */
  function table(string $table): self
  {
    $this->table = $table;
    return $this;
  }

  /**
   * @param string $left
   * @param string $comparator
   */
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

  /**
   * @param string $column
   */
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