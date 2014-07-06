<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|DOC_PERM|STD_PERM);

if(!$_SESSION['__user__']->isInGroup(DS_GROUP)){
	$gid = $_SESSION['__user__']->getPerms();
}

$student_param = "";
if ($_SESSION['__user__']->check_perms(STD_PERM)){
	$student_param = " AND categoria = 2 ";
}
$sel_docs = "SELECT rb_documents.*, rb_anni.descrizione, progressivo_atto, cognome, nome FROM rb_documents, rb_document_types, rb_anni, rb_utenti WHERE doc_type = rb_document_types.id AND rb_document_types.id = ".$_REQUEST['tipo']." AND rb_anni.id_anno = anno_scolastico AND owner = uid {$student_param} ORDER BY anno_scolastico DESC, data_upload DESC";
$res_docs = $db->execute($sel_docs);

$sel_type = "SELECT commento FROM rb_document_types WHERE id = ".$_REQUEST['tipo'];
$res_type = $db->execute($sel_type);
$doc_type = $res_type->fetch_assoc();
$navigation_label = "Area documenti - {$doc_type['commento']}";

include "documents.html.php";

?>