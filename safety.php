<?php

$data = false;

$params = [];

function registration_callback($username, $email, $role)
{
  // all it does is bind registration data in a global array,
  // which is echoed on the page after a registration
  global $data;
  $data = array($username, $email, $role);
}

require_once("user.php");
$USER = new User($params, "registration_callback");

$auth = $USER->authenticated;
if ($USER->role == 'user') {
  $auth = 0;
}

if (array_key_exists("go", $_GET)) {
  if (strcmp($_GET["go"], "Trigger") == 0) {
    pullTrigger($_GET["pid"]);
  }
  if (strcmp($_GET["go"], "getUsers") == 0) {
    getUsers();
  }
  if (strcmp($_GET["go"], "getPID") == 0) {
    getPID();
  }
  if ($auth && strcmp($_GET["go"], "updateRole") == 0) {
    updateRole($_GET["username"], $_GET["role"]);
  }
  if (strcmp($_GET["go"], "getResponses") == 0) {
    getResponses($_GET["pid"]);
  }
  if (strcmp($_GET["go"], "getTriggers") == 0) {
    getTriggers($_GET["pid"]);
  }
  if (strcmp($_GET["go"], "getCircle") == 0) {
    getCircle($_GET["pid"]);
  }
  if (strcmp($_GET["go"], "searchCircle") == 0) {
    searchCircle($_GET["pid"], $_GET["search"]);
  }
  if (strcmp($_GET["go"], "addCircle") == 0) {
    addCircle($_GET["pid"], $_GET["username"], $_GET["relationship"]);
  }
  if (strcmp($_GET["go"], "addDevice") == 0) {
    addDevice($_GET["did"], $_GET["pid"]);
  }
  if (strcmp($_GET["go"], "addDeviceLocation") == 0) {
    addDeviceLocation($_GET["did"], $_GET["lat"], $_GET["lon"]);
  }
  if (strcmp($_GET["go"], "addPersonLocation") == 0) {
    addPersonLocation($_GET["pid"], $_GET["lat"], $_GET["lon"]);
  }
  if (strcmp($_GET["go"], "getDeviceLocation") == 0) {
    getDeviceLocation($_GET["did"]);
  }
  if (strcmp($_GET["go"], "getPersonLocation") == 0) {
    getPersonLocation($_GET["pid"]);
  }
  if (strcmp($_GET["go"], "getDevices") == 0) {
    getDevices($_GET["pid"]);
  }
  if (strcmp($_GET["go"], "addDevices") == 0) {
    addDevices($_GET["did"], $_GET["dtype"]);
  }
  if (strcmp($_GET["go"], "addResponse") == 0) {
    addResponse($_GET["pid"], $_GET["tid"]);
  }
  if (strcmp($_GET["go"], "closeResponse") == 0) {
    closeResponse($_GET["pid"], $_GET["tid"]);
  }
  if (strcmp($_GET["go"], "closeTrigger") == 0) {
    closeTrigger($_GET["pid"]);
  }
}

function closeTrigger($pid) {
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "UPDATE Triggers SET status = 'close', endTime=now()
    WHERE TID = $tid AND PID = $pid";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return null;
  }
  echo json_encode([1, "Success"]);
}

function getRecentTrigger($username) {
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "SELECT TID from Triggers inner join Persons
    on Triggers.PID = Persons.ID AND Persons.username = '$username'
    order by Triggers.event DESC";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return null;
  }
  else {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      return $row["TID"];
    }
  }
  return null;
}

function closeResponse($pid, $tid) {
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "UPDATE Responses SET status = 'close', delivery=now()
    WHERE TID = $tid AND PID = $pid";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return null;
  }
  echo json_encode([1, "Success"]);
}

function addResponse($pid, $tid) {
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "INSERT into Responses (TID, PID, status) VALUES($tid, $pid, 'open')";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return null;
  }
  echo json_encode([1, "Success"]);
}

function addDevices($did, $dtype) {
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "INSERT into Devices VALUES($did, '$dtype')";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return null;
  }
  echo json_encode([1, "Success"]);
}

function getDevices ($pid) {
  $array = [];
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return;
  }
  $str = "SELECT ID, Dtype from Devices inner join Owner
    on Devices.ID = Owner.DID AND Owner.PID = $pid";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return;
  }
  else {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      array_push($array,
        [$row["ID"], $row["Dtype"]]);
    }
  }
  echo json_encode($array);
}

function getPersonLocation($pid) {
  $array = [];
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "SELECT lat,lon FROM Location WHERE PID = $pid";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return null;
  }
  else {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      array_push($array,
        [$row["lat"], $row["lon"]]);
    }
  }
  echo json_encode($array);
}

function getDeviceLocation($did) {
  $array = [];
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "SELECT lat,lon FROM Location WHERE DID = $did";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return null;
  }
  else {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      array_push($array,
        [$row["lat"], $row["lon"]]);
    }
  }
  echo json_encode($array);
}

function addDeviceLocation($did, $lat, $lon) {
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "INSERT into Location (DID, lat, lon) VALUES($did, $lat, $lon)";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return null;
  }
  echo json_encode([1, "Success"]);
}

function addPersonLocation($pid, $lat, $lon) {
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "INSERT into Location (PID, lat, lon) VALUES($pid, $lat, $lon)";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return null;
  }
  echo json_encode([1, "Success"]);
}

function addDevice($did, $pid) {
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "INSERT into Owner VALUES($did, $pid)";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return null;
  }
  echo json_encode([1, "Success"]);
}

function addCircle($pid, $username, $relationship) {
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "INSERT into SafetyCircle VALUES($pid, 
    (SELECT ID from Persons WHERE username = '$username'), '$relationship')";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return null;
  }
  echo json_encode([1, "Success"]);
}

function searchCircle($pid, $search) {
  $array = [];
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return;
  }
  $str = "SELECT LastName, FirstName, username, Email from Persons 
    where LastName LIKE '%$search%' OR FirstName LIKE '%$search%' OR
    username LIKE '%$search%' OR Email LIKE '%$search%' LIMIT 100";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return;
  }
  else {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      array_push($array,
        [$row["LastName"], $row["FirstName"], $row["username"], $row["Email"]]);
    }
  }
  echo json_encode($array);
}

function pullTrigger($pid) {
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "INSERT into Triggers (PID, status) VALUES($pid, 'open')";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return null;
  }
  echo json_encode([1, "Success"]);
}

function getPID() {
  global $data;
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "SELECT ID from Persons where username='".$data[0]."'";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return null;
  }
  else {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      echo json_encode([1, $row["ID"]]);
      return $row["ID"];
    }
  }
  return null;
}

function getCircle($pid) {
  $array = [];
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return;
  }
  $str = "SELECT LastName, FirstName, username, Email,
    SafetyCircle.relationship from Persons 
    inner join SafetyCircle
    on SafetyCircle.PID1 = $pid AND Persons.ID=SafetyCircle.PID2";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return;
  }
  else {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      array_push($array,
        [$row["LastName"], $row["FirstName"], $row["username"], $row["Email"],
        $row["relationship"]]);
    }
  }
  echo json_encode($array);
}

function getTriggers($pid) {
  $array = [];
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return;
  }
  $str = "
    SELECT LastName, FirstName, username, Email,
    slc( (select lat from Location where PID = $pid),
  (select lon from Location where PID = $pid), lat, lon) as distance,
  t2.TID, EXISTS(select 1 from Responses where TID=t2.TID AND PID= $pid)
  as Response
  from Persons inner join
  (select t1.TID, t1.PID, lat, lon from
    (select Triggers.TID, Triggers.PID, Triggers.event from 
    Triggers inner join SafetyCircle
    on SafetyCircle.PID1 = $pid AND Triggers.PID=SafetyCircle.PID2) t1
            inner join Location on Location.PID = t1.PID) t2
              on Persons.ID = t2.PID";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return;
  }
  else {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      array_push($array,
        [$row["LastName"], $row["FirstName"], $row["username"], $row["Email"],
        $row["distance"], $row["TID"], $row["Response"]]);
    }
  }
  echo json_encode($array);
}

function getResponses($pid) {
  $array = [];
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return;
  }
  $str = "
    SELECT LastName, FirstName, username, Email,
    slc( (select lat from Location where PID = $pid),
  (select lon from Location where PID = $pid), lat, lon) as distance,
  t2.TID, t2.PID, t2.status
  from Persons inner join
  (select t1.PID, t1.TID, t1.status, lat, lon from
  (select Responses.PID, Responses.TID, Responses.status from Responses 
  inner join Triggers
        on Responses.TID = Triggers.TID AND Triggers.PID = $pid
            AND Triggers.status='open') t1 inner join Location
              on Location.PID = t1.PID) t2
              on Persons.ID = t2.PID";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return;
  }
  else {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      array_push($array,
        [$row["LastName"], $row["FirstName"], $row["username"], $row["Email"],
        $row["distance"], $row["TID"], $row["PID"], $row["status"]]);
    }
  }
  echo json_encode($array);
}

function updateRole($username, $role) {
  $res = [];
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return;
  }
  $str = "UPDATE Persons SET role='$role' WHERE username='$username'";
  $results = $mdb->query($str);
  array_push($res, $str);
  echo json_encode($res);
}

function getUsers() {
  $array = [];
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return;
  }
  $str = "SELECT LastName, FirstName, username, Email, role, active, last from Persons";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->error()]);
    return;
  }
  else {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      array_push($array,
        [$row["LastName"], $row["FirstName"], $row["username"], $row["Email"],
        $row["role"], $row["active"], time() - $row["last"]]);
    }
  }
  echo json_encode($array);
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

