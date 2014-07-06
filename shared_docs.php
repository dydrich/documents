<?php

require_once "../../lib/start.php";

ini_set("display_errors", DISPLAY_ERRORS);

check_session();

/*
 * estrazione documenti condivisi
*/
$sel_ev_docs = "SELECT rb_documents.id, file, doc_type, abstract, titolo, link, privato, id_materia, rb_materie.materia AS materia, cognome, nome FROM rb_documents, rb_documents_shared, rb_materie, rb_utenti WHERE owner = uid AND rb_documents.id = id_documento AND rb_documents.materia = id_materia AND rb_documents_shared.classe = {$_SESSION['__user__']->getClass()} AND anno = {$_SESSION['__current_year__']->get_ID()} ORDER BY data_upload DESC";
//echo $sel_ev_docs;
$res_ev = $db->execute($sel_ev_docs);

$navigation_label = "Registro elettronico - Documenti didattici condivisi";

include "shared_docs.html.php";