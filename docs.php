<?php

require_once "../../lib/start.php";

ini_set("DISPLAY_ERRORS", DISPLAY_ERRORS);

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
else {
	$sel_docs = "SELECT rb_documents.*, rb_anni.descrizione, progressivo_atto FROM rb_documents, rb_document_types, rb_anni WHERE doc_type = rb_document_types.id AND rb_document_types.id = ".$_REQUEST['tipo']." AND rb_anni.id_anno = anno_scolastico AND owner = {$_SESSION['__user__']->getUId()} ORDER BY anno_scolastico DESC, data_upload DESC ";
}

$sel_type = "SELECT commento FROM rb_document_types WHERE id = ".$_REQUEST['tipo'];
$doc_type = $db->executeCount($sel_type);
$navigation_label = "Area documenti - {$doc_type}";

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
$row_class = "docs_row";
$row_class_menu = " docs_row_menu";
$nav_params = "&tipo={$_REQUEST['tipo']}";

include "docs.html.php";

?>