<?php

echo "Hello\n";

if (array_key_exists("go", $_GET)) {
  if (strcmp($_GET["go"], "set") == 0) {
     setValue($_GET["value"]);
  }
  if (strcmp($_GET["go"], "read") == 0) {
     readValue();
  }
}

class MDB {
  public $link;
  public $connected;
  public $error;

  public function __construct() {
    $this->connected = 1;
    $this->link = new mysqli('127.0.0.1:3306', 'shanvish_user', 'Shanvi2018safety', 'shanvish_safety');
    /* check connection */
    if (mysqli_connect_errno()) {
      $this->error = mysqli_connect_error();
      $this->connected = 0;
    }
  }

  function __destruct() {
    /* close connection */
    $this->link->close();
  }

  public function query($str) {
     return $this->link->query($str);
  }

  public function errorNo() {
     return $this->link->errorno;
  }
  public function errorInfo() {
     return $this->link->error;
  }

  public function begin() {
    $str = "START TRANSACTION";
    return $this->query($str);
  }
  public function end() {
    $str = "COMMIT";
    return $this->query($str);
  }

  public function lock($name = "lock1", $timeout = 10) {
    $str = "SELECT GET_LOCK('$name',$timeout)";
    $result = $this->link->query($str); 
    if ($result) {
      $result->close();
    }
    return $result;
  }

  public function release($name = "lock1") {
    $str = "SELECT RELEASE_LOCK('$name')";
    $result = $this->link->query($str); 
    if ($result) {
      $result->close();
    }
    return $result;
  }
}

function setValue($v) {
  $mdb = new MDB();
  if (!$mdb->connected) {
    return;
  }
  $str = "UPDATE test SET grade=$v WHERE id=1";
  if (!$mdb->query($str)) {
    echo "Table creation failed: (" . $mdb->errorNo() . ") " . $mdb->error() .
         "\n";
  }
  else {
    echo "Success\n";
  }
}

function readValue() {
  $mdb = new MDB();
  if (!$mdb->connected) {
    return;
  }
  $str = "SELECT * from test WHERE id=1";
  $res = $mdb->query($str);
  if (!$res) {
    echo "Table creation failed: (" . $mdb->errorNo() . ") " . $mdb->error() .
         "\n";
  }
  else {
    echo "Success\n";
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
        echo " id = " . $row['id'] . "\n";
        echo " userName = " . $row['userName'] . "\n";
        echo " grade = " . $row['grade'] . "\n";
    }
  }
}


?>
