<?php
class DB extends PDO {

  private static $_conn;
  private static $_details = [
    'engine'   => 'mysql',
    'host'     => 'localhost',
    'username' => 'root',
    'password' => ''
  ];

  public static function getInstance() {
    if (!isset(self::$_conn)) {
      self::$_conn = new self(self::$_details);
    }

    return self::$_conn;
  }

  public function __construct($_Args=[]) {
    $this::$_details = array_merge($this::$_details, $_Args);
    try {
      $db = parent::__construct(
        $this::$_details['engine'] . ':host=' . $this::$_details['host'] . ';dbname=' . $this::$_details['dbname'],
        $this::$_details['username'],
        $this::$_details['password']);
    } catch (PDOException $e) {
      die('Unable to connect to database.<br /><br /><hr /><strong>Error message:</strong><br /><pre>' . $e->getMessage() . '</pre>');
    }
  }

  public function delete($table, $_Args) {
    $queryBuild = '';

    if (is_array($_Args[0])) {
      for ($q=0; $q<=count($_Args[0])-1; $q++) {
        list ($column, $operator, $value) = $_Args[0][$q];
        $queryBuild .= '`' . $column . '` ' . $operator . ' \'' . $value . '\' AND ';
      }
      $queryBuild = rtrim($queryBuild, ' AND ');
    } else {
      $queryBuild .= $_Args[0];
    }

    $sql = "DELETE FROM {$table} WHERE {$queryBuild};";
    echo $sql;
  }

  public function update($table, $data, $_Args=[]) {
    $queryBuild = '';
    if (is_array($data)) {
      foreach ($data as $column => $value) {
        $queryBuild .= '`' . $column . '` = \'' . $value . '\', ';
      }
      $queryBuild = rtrim($queryBuild, ', ');
    } else {
      $queryBuild = $data;
    }

    if (isset($_Args)) {
      $queryBuild .= ' ' . $_Args[0] . ' ';

      if (is_array($_Args[1])) {
        for ($q=0; $q<=count($_Args[1])-1; $q++) {
          list ($column, $operator, $value) = $_Args[1][$q];
          $queryBuild .= '`' . $column . '` ' . $operator . ' \'' . $value . '\' AND ';
        }
        $queryBuild = rtrim($queryBuild, ' AND ');
      } else {
        $queryBuild .= $_Args[1];
      }

      if (isset($_Args[2]))
        $queryBuild .= ' ' . strtoupper($_Args[2]);
    }

    $sql = "UPDATE `{$table}` SET {$queryBuild};";
    $runQuery = $this::$_conn->prepare($sql);
    $runQuery->execute();

    return $runQuery->rowCount();
  }

  public function insert($table, $data) {
    $queryBuild = '';
    $result     = 0;

    if (!is_array($data)) {
      return;
    } else {
      for ($i=0;$i<=count($data)-1;$i++) {
        $columns = $data[$i][0];
        $values  = $data[$i][1];
        $sql = "INSERT INTO {$table} (`" . implode("`, `", $columns) . "`) VALUES ('" . implode("', '", $values) . "');";

        $runQuery = $this::$_conn->prepare($sql);
        if ($runQuery->execute()) {
          $result++;
        }
      }
    }

    return $result;
  }


}
