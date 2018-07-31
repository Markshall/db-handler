<?php
class DB {

  public static $_instance = null;
  public        $query = '';
  public        $orAnd = false;
  public        $_conn;
  public        $_details  = [
    'engine'   => 'mysql',
    'host'     => 'localhost',
    'username' => 'root',
    'password' => '',
    'dbname'   => ''
  ];

  public static function getInstance() {
    if (self::$_instance === null) {
      self::$_instance = new self;
    }

    return self::$_instance;
  }

  public function __construct($_Args=[]) {
    $this->_details = array_merge($this->_details, $_Args);
    $this->_conn = new PDO($this->_details['engine'] . ':host=' . $this->_details['host'] . ';dbname=' . $this->_details['dbname'], $this->_details['username'], $this->_details['password']);
  }

  public function update($table, $data) {
    $this->query = "UPDATE `{$table}` SET ";

    foreach ($data as $column => $value) {
      $this->query .= "`" . $column . "` = '" . $value . "', ";
    }

    $this->query = rtrim($this->query, ', ');

    return $this;
  }

  public function insert($table, $data) {
    $this->query = "INSERT INTO `{$table}` (`" . (implode('`, `', $data[0])) . "`) VALUES ('" . (implode("', '", $data[1])) . "')";
    return $this;
  }

  public function delete($table) {
    $this->query = "DELETE FROM `{$table}`";
    return $this;
  }

  public function run() {
    try {
      $tryQuery = $this->_conn->prepare($this->query);
      $tryQuery->execute();

      $this->orAnd = false;

      return $tryQuery->rowCount();
    } catch(PDOException $e) {
      throw($e->getMessage);
    }
  }

  public function where($whereColumn, $whereValue) {
    if ($this->orAnd === false) { $this->query .= ' WHERE '; }
    $this->query .= "`{$whereColumn}` = '{$whereValue}'";
    return $this;
  }

  public function _or() {
    $this->query .= ' OR ';
    $this->orAnd = true;
    return $this;
  }

  public function _and() {
    $this->query .= ' AND ';
    $this->orAnd = true;
    return $this;
  }

  public function limit($qty) {
    $this->query .= " LIMIT {$qty}";
    return $this;
  }

  public function echoQuery() {
    return "<pre>{$this->query}</pre>";
  }
}
