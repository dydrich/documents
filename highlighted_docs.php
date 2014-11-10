<?php

require_once "../../lib/start.php";

ini_set("display_errors", DISPLAY_ERRORS);

check_session();
check_permission(DOC_PERM|SEG_PERM|DSG_PERM|DIR_PERM);

$drawer_label = "Documenti in evidenza";

$sel_ev_docs = "SELECT rb_documents.id, evidenziato AS data, file, doc_type, abstract, titolo, link, commento FROM rb_documents, rb_document_types WHERE doc_type = rb_document_types.id AND evidenziato IS NOT NULL AND evidenziato >= NOW() ORDER BY data_upload DESC";
$res_ev = $db->execute($sel_ev_docs);

$_SESSION['no_file'] = array("referer" => "modules/documents/highlighted_docs.php", "path" => "modules/documents/", "relative" => "highlighted_docs.php");

include "highlighted_docs.html.php";
