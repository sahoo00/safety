<?php

$data = false;

$params = [];
foreach ($_POST as $k => $v) {
  $params[$k] = $v;
}
foreach ($_GET as $k => $v) {
  $params[$k] = $v;
}

function registration_callback($username, $email, $role)
{
  // all it does is bind registration data in a global array,
  // which is echoed on the page after a registration
  global $data;
  $data = array($username, $email, $role);
}

require_once("user.php");
$USER = new User($params, "registration_callback");

//if ($data && $params["op"] == "signup") {
//  $params["op"] = "login";
//  $USER = new User($params, "registration_callback");
//}

$data = [$USER->username, $USER->email, $USER->role, $USER->userid];

if (array_key_exists("type", $params) && $params["type"] == "json") {
    $res = [$USER->result, $USER->authenticated, $data, $USER->info_log, $USER->error_log, $USER->error];
    echo json_encode($res);
}
else {

  if ($params["op"] == "update") {
    $res = [$USER->result, $USER->authenticated, $data, $USER->info_log, $USER->error_log];
    echo json_encode($res);
  }
  else {
    if ($USER) {
      foreach ($USER->error_log as $k) {
        echo "<p> $k </p>\n";
      }
      foreach ($USER->info_log as $k) {
        echo "<p> $k </p>\n";
      }
      echo "<p> $USER->error </p>\n";
      if ($USER->authenticated) {
        if ($USER->role == "user") {
          echo "<p>You must be verified user to use this website</p>\n";
        }
        else {
          echo "<p>You are logged in</p>\n";
        }
      }
    }
  }
}

?>
