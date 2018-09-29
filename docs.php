<?php

require_once "../../lib/start.php";

ini_set("DISPLAY_ERRORS", 1);

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|DOC_PERM);

if(!isset($_REQUEST['offset'])) {
	$offset = 0;
}
else {
	$offset = $_REQUEST['offset'];
}

$limit = 10;

if ($_REQUEST['tipo'] == 4){
	$sel_docs = "SELECT rb_documents.*, rb_anni.descrizione, rb_categorie_docs.nome AS categ FROM rb_documents, rb_document_types, rb_anni, rb_categorie_docs WHERE rb_documents.categoria = rb_categorie_docs.id_categoria AND rb_documents.doc_type = rb_document_types.id AND rb_document_types.id = 4 AND rb_anni.id_anno = rb_documents.anno_scolastico AND owner = ".$_SESSION['__user__']->getUid()." ORDER BY anno_scolastico DESC, data_upload DESC ";
}
else if ($_REQUEST['tipo'] == 10) {
	$sel_docs = "SELECT rb_documents.*, rb_anni.descrizione, progressivo_atto FROM rb_documents, rb_document_types, rb_anni WHERE doc_type = rb_document_types.id AND rb_document_types.id = ".$_REQUEST['tipo']." AND rb_anni.id_anno = anno_scolastico AND owner = {$_SESSION['__user__']->getUId()} AND anno_scolastico = {$_SESSION['__current_year__']->get_ID()} ORDER BY data_upload DESC ";
}
else {
	$owner_perm = "AND owner = {$_SESSION['__user__']->getUId()}";
	if ($_SESSION['__user__']->check_perms(DSG_PERM)) {
		$owner_perm = '';
	}
	$sel_docs = "SELECT rb_documents.*, rb_anni.descrizione, progressivo_atto FROM rb_documents, rb_document_types, rb_anni WHERE doc_type = rb_document_types.id AND rb_document_types.id = ".$_REQUEST['tipo']." AND rb_anni.id_anno = anno_scolastico ".$owner_perm." ORDER BY anno_scolastico DESC, data_upload DESC ";
}

$sel_type = "SELECT commento FROM rb_document_types WHERE id = ".$_REQUEST['tipo'];
$doc_type = $db->executeCount($sel_type);
$drawer_label = "Gestione ". strtolower($doc_type);

/*
if(!isset($_REQUEST['second'])){
	$res_docs = $db->execute($sel_docs);
	//print $sel_links;
	$count = $res_docs->num_rows;
	//print $count;
	$_SESSION['count_docs'] = $count;
}
else{
	$sel_docs .= " LIMIT $limit OFFSET $offset";
	$res_docs = $db->execute($sel_docs);
}
//print $sel_docs;
if($offset == 0) {
	$page = 1;
}
else {
	$page = ($offset / $limit) + 1;
}

$pagine = ceil($_SESSION['count_docs'] / $limit);
if($pagine < 1) {
	$pagine = 1;
}

// dati per la paginazione (navigate.php)
$colspan = 5;
$link = basename($_SERVER['PHP_SELF']);
$count_name = "count_docs";
$nav_params = "&tipo={$_REQUEST['tipo']}";
*/

$res_docs = $db->execute($sel_docs);
$count = $res_docs->num_rows;
$_SESSION['count_docs'] = $count;

include "docs.html.php";
