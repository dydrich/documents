<?php

require_once "../../lib/start.php";

$param = $_REQUEST['cls'];

$response = array("status" => "ok");
header("Content-type: application/json");

$sel = "SELECT CONCAT_WS(' ', cognome, nome) AS name, id_alunno FROM rb_alunni, rb_assegnazione_sostegno WHERE alunno = id_alunno AND anno = {$_SESSION['__current_year__']->get_ID()} AND classe = {$param} ORDER BY cognome, nome";
try {
	$res_sel = $db->executeQuery($sel);
} catch (MySQLException $ex) {
	$response['status'] = "kosql";
	$response['dbg_message'] = "Query: {$ex->getQuery()} ------ Errore: {$ex->getMessage()}";
	$response['message'] = "Si è verificato un errore di rete: controlla lo stato della tua connessione e riprova";
	$res = json_encode($response);
	echo $res;
	exit;
}
$users = array();
while ($us = $res_sel->fetch_assoc()){
	$users[] = array("id" => $us['id_alunno'], "name" => $us['name']);
}

$response['students'] = $users;
$res = json_encode($response);
echo $res;
exit;
