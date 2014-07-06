<?php

require_once "../../lib/start.php";

$param = $_REQUEST['term'];

$sel_tags = "SELECT tag FROM rb_tags WHERE tag LIKE '%{$param}%' ORDER BY tag";
$res_tags = $db->execute($sel_tags);
$tags = array();
while ($us = $res_tags->fetch_assoc()){
	$tags[] = array("value" => $us['tag']);
}

$json_tags = json_encode($tags);
header("Content-type: text/plain");
echo $json_tags;
exit;