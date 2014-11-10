<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM);

$drawer_label = "Stampa riepilogo albo pretorio";

$sel_docs = "SELECT data_upload FROM rb_documents WHERE doc_type = 7 ORDER BY rb_documents.data_upload DESC ";
try{
	$res_docs = $db->executeQuery($sel_docs);
} catch (MySQLException $ex){
	$ex->redirect();
	exit;
}

$mesi = array("", "gennaio", "febbraio", "marzo", "aprile", "maggio", "giugno", "luglio", "agosto", "settembre", "ottobre", "novembre", "dicembre");
$anni = array();

while ($row = $res_docs->fetch_assoc()){
	list($date, $hour) = split(" ", $row['data_upload']);
	list($y, $m, $d) = split("-", $date);
	if (!isset($anni[$y])){
		$anni[$y] = array();
	}
	if (!in_array($m, $anni[$y])){
		$anni[$y][] = $m;
	}
}

//print_r($anni);

include "summary.html.php";
