<?php

require_once "../../lib/start.php";
require_once "../../lib/MimeType.php";
require_once "lib/DidacticDocument.php";
require_once "lib/Document.php";
require_once "lib/AlboDocument.php";

ini_set("display_errors", "1");

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|DOC_PERM|STD_PERM);

$sel_doc = "SELECT rb_documents.*, rb_anni.descrizione, progressivo_atto, cognome, nome FROM rb_documents, rb_document_types, rb_anni, rb_utenti WHERE doc_type = rb_document_types.id AND rb_anni.id_anno = anno_scolastico AND owner = uid AND rb_documents.id = {$_REQUEST['id']}";
$res_doc = $db->execute($sel_doc);
$mydoc = $res_doc->fetch_assoc();

if ($mydoc['doc_type'] == 4){
	$doc = new DidacticDocument($mydoc['id'], $mydoc, new MYSQLDataLoader($db));
}
else if ($mydoc['doc_type'] == 7){
	$doc = new AlboDocument($mydoc['id'], $mydoc, new MYSQLDataLoader($db));
}
else {
	$doc = new Document($mydoc['id'], $mydoc, new MYSQLDataLoader($db));
}
$dt = $doc->getDataUpload();
list($d_upl, $t) = explode(" ", $dt);

$file = "../../download/{$mydoc['doc_type']}/{$mydoc['file']}";
if (file_exists($file)){
	$filedata = MimeType::getMimeContentType($file);
	$fs = filesize($file);
	$size = formatBytes($fs, 2);
}
else {
	$size = "non disponibile";
}

$school = "";
if ($doc instanceof DidacticDocument && $doc->getSchoolOrder() != 4){
	$school = $db->executeCount("SELECT tipo FROM rb_tipologia_scuola WHERE id_tipo = {$doc->getSchoolOrder()}");
}

$sel_type = "SELECT commento FROM rb_document_types WHERE id = {$mydoc['doc_type']}";
$doc_type = $db->executeCount($sel_type);
$navigation_label = "Area documenti - {$doc_type}";

$_SESSION['no_file'] = array("referer" => "modules/documents/document.php?id={$_REQUEST['id']}", "path" => "", "relative" => "document.php?id={$_REQUEST['id']}");

include "document.html.php";

?>