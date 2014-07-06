<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|DOC_PERM|STD_PERM);

if(!$_SESSION['__user__']->isInGroup(DS_GROUP)){
	$gid = $_SESSION['__user__']->getPerms();
}

$tag = $_GET['tag'];
$sel_docs = "SELECT rb_documents.*, rb_anni.descrizione, progressivo_atto, cognome, nome FROM rb_documents, rb_documents_tags, rb_anni, rb_utenti, rb_document_types WHERE doc_type = rb_document_types.id AND id_documento = rb_documents.id AND tid = {$_GET['tag']} AND rb_anni.id_anno = anno_scolastico AND owner = uid {$student_param} ORDER BY anno_scolastico DESC, data_upload DESC";
$res_docs = $db->execute($sel_docs);

$sel_type = "SELECT tag FROM rb_tags WHERE tid = ".$_REQUEST['tag'];
$res_type = $db->execute($sel_type);
$doc_type = $res_type->fetch_assoc();
$navigation_label = "Area documenti - tag";

include "tags.html.php";

?>