<?php

include "../../lib/start.php";

check_session();
check_permission(DSG_PERM);

$id = $_REQUEST['id'];
if($_REQUEST['action'] != 2){
	$codice = $db->real_escape_string($_REQUEST['codice']);
	$nome = utf8_encode($db->real_escape_string($_REQUEST['nome']));
	$descrizione = utf8_encode($db->real_escape_string($_REQUEST['abstract']));
}
header("Content-type: application/json");
switch($_REQUEST['action']){
	case 1:
		// insert
		$statement = "INSERT INTO rb_categorie_docs (tipo_documento, codice, nome, descrizione) VALUES (7, '$codice', '$nome', '$descrizione')";
		$msg = "Inserimento completato";
		break;
	case 2:
		// delete
		$statement = "DELETE FROM rb_categorie_docs WHERE id_categoria = $id";
		$msg = "Cancellazione completata";
		break;
	case 3:
		// update
		$statement = "UPDATE rb_categorie_docs SET codice = '$codice', nome = '$nome', descrizione = '$descrizione' WHERE id_categoria = $id";
		$msg = "Modifica completata";
		break;
}

try{
	$recordset = $db->executeUpdate($statement);
} catch (MySQLException $ex){
	$response['status'] = "kosql";
	$response['dbg_message'] = "Query: {$ex->getQuery()} ------ Errore: {$ex->getMessage()}";
	$response['message'] = "Errore nella registrazione dei dati";
	$res = json_encode($response);
	echo $res;
	exit;
}

$response['status'] = "ok";
$response['message'] = $msg;
$res = json_encode($response);
echo $res;
exit;
