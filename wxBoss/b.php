<?php

require_once "mysql.php";
$db = new M("ai_sign");
$userRow = $db->select('unionid = 123');

print_r($userRow);
$db->close();
?>