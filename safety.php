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
  if (strcmp($_GET["go"], "getMyTriggers") == 0) {
    getMyTriggers($_GET["pid"]);
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
  if (strcmp($_GET["go"], "removeCircle") == 0) {
    removeCircle($_GET["pid"], $_GET["username"], $_GET["relationship"]);
  }
  if (strcmp($_GET["go"], "addDevice") == 0) {
    addDevice($_GET["did"], $_GET["pid"], $_GET["dtype"], $_GET["ckey"]);
  }
  if (strcmp($_GET["go"], "removeDevice") == 0) {
    removeDevice($_GET["did"], $_GET["pid"], $_GET["dtype"], $_GET["ckey"]);
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
  if (strcmp($_GET["go"], "myDevices") == 0) {
    myDevices($_GET["pid"]);
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
  if (strcmp($_GET["go"], "getPrices") == 0) {
    getPrices();
  }
  if (strcmp($_GET["go"], "buyDevice") == 0) {
    buyDevice($_GET["dtype"]);
  }
  if (strcmp($_GET["go"], "makeDevice") == 0) {
    makeDevice($_GET["dtype"]);
  }
  if (strcmp($_GET["go"], "getStock") == 0) {
    getStock();
  }
}

function getStock() {
  $array = [];
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "SELECT ID,Dtype FROM Devices WHERE CKey is NULL ORDER BY ID";
  $res = $mdb->query($str);
  if (!$res) {
    $array = [0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()];
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

function makeDevice($dtype) {
  $array = [1, ""];
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  if (!$mdb->lock()) {
    echo json_encode([0, "Couldn't get lock"]);
    return;
  }
  $did = null;
  $ckey = null;
  $str = "INSERT into Devices (Dtype) VALUES('$dtype')";
  $res = $mdb->query($str);
  if (!$res) {
    $array = [0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()];
  }
  if ($array[0] == 1) {
    $str = "SELECT ID,Dtype FROM Devices ORDER BY ID DESC LIMIT 1";
    $res = $mdb->query($str);
    if (!$res) {
      $array = [0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()];
    }
    else {
      $array = [];
      $res->data_seek(0);
      while ($row = $res->fetch_assoc()) {
        array_push($array,
          [$row["ID"], $row["Dtype"]]);
      }
    }
  }
  if (!$mdb->release()) {
    $res = [0, "Couldn't release lock"];
    echo json_encode($res);
    return;
  }
  echo json_encode($array);
}

function generate_ckey ($did) {
  $isSourceStrong = true;
  $random = openssl_random_pseudo_bytes(16, $isSourceStrong);
  if (false === $isSourceStrong || false === $random) {
    return null;
  }
  return bin2hex($random);
}

function buyDevice($dtype) {
  $pid = null;
  if (array_key_exists("pid", $_GET)) {
    $pid = $_GET["pid"];
  }
  $array = [1, ""];
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  if (!$mdb->lock()) {
    echo json_encode([0, "Couldn't get lock"]);
    return;
  }
  $did = null;
  $ckey = null;
  $str = "SELECT ID,Dtype FROM Devices WHERE CKey is NULL 
    AND Dtype = '$dtype' ORDER BY ID LIMIT 1";
  $res = $mdb->query($str);
  if (!$res) {
    $array = [0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()];
  }
  else {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      $did = $row["ID"];
      break;
    }
  }
  if ($did != null) {
    $ckey = generate_ckey($did);
    if ($ckey == null) {
      $array = [0, "Couldn't generate confirmation key"];
    }
  }
  else {
    $array = [0, "Out of Stock"];
  }
  if ($array[0] == 1 && $did != null && $ckey != null) {
    $str = "UPDATE Devices SET CKey = '$ckey' WHERE ID = $did";
    $res = $mdb->query($str);
    if (!$res) {
      $array = [0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()];
    }
  }
  if ($array[0] == 1 && $did != null && $ckey != null && $pid != null) {
    $str = "UPDATE Devices SET PID = $pid WHERE ID = $did";
    $res = $mdb->query($str);
    if (!$res) {
      $array = [0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()];
    }
  }
  if ($array[0] == 1 && $did != null && $ckey != null) {
    $str = "SELECT ID,Dtype,CKey,PID FROM Devices WHERE ID = $did";
    $res = $mdb->query($str);
    if (!$res) {
      $array = [0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()];
    }
    else {
      $res->data_seek(0);
      $array = [];
      while ($row = $res->fetch_assoc()) {
        array_push($array,
          [$row["ID"], $row["Dtype"], $row["CKey"], $row["PID"]]);
      }
    }
  }
  if (!$mdb->release()) {
    $res = [0, "Couldn't release lock"];
    echo json_encode($res);
    return;
  }
  echo json_encode($array);
}

function getPrices() {
  $array = [];
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "SELECT Prices.Dtype,Version,Price,
    IFNULL(COUNT(Devices.Dtype), 0) as Stock
    FROM Prices left join Devices on 
    Prices.Dtype = Devices.Dtype AND CKey is NULL
    GROUP by Prices.Dtype";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
    return null;
  }
  else {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      array_push($array,
        [$row["Dtype"], $row["Version"],
        sprintf("\$%.2f", $row["Price"]), $row["Stock"]]);
    }
  }
  echo json_encode($array);
}

function closeTrigger($pid) {
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "UPDATE Triggers SET status = 'close', endTime=now()
    WHERE PID = $pid";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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
    $res = [0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()];
    echo json_encode($res);
    return null;
  }
  echo json_encode([1, "Success"]);
}

function myDevices ($pid) {
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
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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

function getDevices ($pid) {
  $array = [];
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return;
  }
  $str = "SELECT ID, Dtype, CKey from Devices WHERE PID = $pid";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
    return;
  }
  else {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      array_push($array,
        [$row["ID"], $row["Dtype"], $row["CKey"]]);
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
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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
  $str = "INSERT into Location (DID, lat, lon) VALUES($did, $lat, $lon)
    ON DUPLICATE KEY UPDATE DID = $did, lat = $lat, lon = $lon";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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
  $str = "INSERT into Location (PID, lat, lon) VALUES($pid, $lat, $lon)
    ON DUPLICATE KEY UPDATE PID = $pid, lat = $lat, lon = $lon";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
    return null;
  }
  echo json_encode([1, "Success"]);
}

function verifyCKey($did, $dtype, $ckey) {
  $mdb = new MDB();
  if (!$mdb->connected) {
    return 0;
  }
  $id = null;
  $str = "SELECT ID from Devices WHERE ID = $did AND 
    CKey = '$ckey' AND Dtype = '$dtype'";
  $res = $mdb->query($str);
  if (!$res) {
    return 0;
  }
  else {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      $id = $row["ID"];
      break;
    }
  }

  if ($id != null && strcmp($id, $did) == 0) {
    return 1;
  }
}

function addDevice($did, $pid, $dtype, $ckey) {
  if (!verifyCKey($did, $dtype, $ckey)) {
    echo json_encode([0, "Couldn't verify confirmation key $ckey"]);
    return null;
  }
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "INSERT into Owner VALUES($did, $pid)";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
    return null;
  }
  echo json_encode([1, "Success"]);
}

function removeDevice($did, $pid, $dtype, $ckey) {
  if (!verifyCKey($did, $dtype, $ckey)) {
    echo json_encode([0, "Couldn't verify confirmation key"]);
    return null;
  }
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "DELETE from Owner where DID=$did";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
    return null;
  }
  echo json_encode([1, "Success"]);
}

function removeCircle($pid, $username, $relationship) {
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "DELETE FROM SafetyCircle WHERE PID1 = $pid AND PID2 =
    (SELECT ID from Persons WHERE username = '$username')";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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
  $str = "SELECT LastName, FirstName, username, Email,
    SafetyCircle.relationship from Persons LEFT JOIN SafetyCircle
    on SafetyCircle.PID1 = 1 AND Persons.ID=SafetyCircle.PID2
    where LastName LIKE '%$search%' OR FirstName LIKE '%$search%' OR
    username LIKE '%$search%' OR Email LIKE '%$search%' LIMIT 100";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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

function pullTrigger($pid) {
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return null;
  }
  $str = "INSERT into Triggers (PID, status) VALUES($pid, 'open')";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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

function getMyTriggers($pid) {
  $array = [];
  $mdb = new MDB();
  if (!$mdb->connected) {
    echo json_encode([0, "database not connected"]);
    return;
  }
  $str = "
    SELECT LastName, FirstName, username, Email,
      t1.TID, EXISTS(select 1 from Responses where TID=t1.TID) as
        Response
          from Persons inner join
              (select Triggers.TID, Triggers.PID, Triggers.event from
                  Triggers WHERE Triggers.PID=$pid AND Triggers.status = 'open')
t1
              on Persons.ID = t1.PID";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
    return;
  }
  else {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      array_push($array,
        [$row["LastName"], $row["FirstName"], $row["username"], $row["Email"],
        0, $row["TID"], $row["Response"]]);
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
    on SafetyCircle.PID1 = $pid AND Triggers.PID=SafetyCircle.PID2
        AND Triggers.status = 'open') t1
            inner join Location on Location.PID = t1.PID) t2
              on Persons.ID = t2.PID";
  $res = $mdb->query($str);
  if (!$res) {
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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
    echo json_encode([0, "Query failed: (" . $mdb->errorNo() . ") " . $mdb->errorInfo()]);
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

