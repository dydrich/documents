<?php 
/*
 * documenti segnalati per il livello di classe (prime, seconde o terze)
 */

require_once "../../lib/start.php";

ini_set("display_errors", DISPLAY_ERRORS);

check_session();

$gid = DOC_PERM;

$materie = array();
$res_materie = $db->execute("SELECT id_materia, materia FROM rb_materie");
while($row = $res_materie->fetch_assoc()){
	$materie[$row['id_materia']] = array("materia" => $row['materia'], "docs" => array());
}

$cls = $db->executeCount("SELECT anno_corso FROM rb_classi WHERE id_classe = {$_SESSION['__user__']->getClass()}");

$sel_docs = "SELECT rb_documents.*, rb_anni.descrizione, CONCAT_WS(' ', cognome, nome) AS prof FROM rb_documents, rb_anni, rb_utenti WHERE owner = uid AND doc_type = 4 AND rb_documents.ordine_scuola = {$_SESSION['__user__']->getSchoolOrder()} AND rb_anni.id_anno = anno_scolastico AND classe_rif = {$cls} ORDER BY materia, anno_scolastico DESC, data_upload DESC";
$res_docs = $db->execute($sel_docs);
while($doc = $res_docs->fetch_assoc()){
	if(($doc['privato'] == 1) && (isset($gid) && $gid != "") && (!($gid&$doc['permessi']))){
		continue;
	}
	if ($doc['classe_rif'] != "" && $doc['classe_rif'] != $_SESSION['__classe__']->get_anno()){
		continue;
	}
	if ($doc['materia'] == ""){
		continue;
	}
	$materie[$doc['materia']]['docs'][] = $doc;
}

$navigation_label = "Registro elettronico - Documenti didattici consigliati";

$_SESSION['no_file'] = array("referer" => "modules/documents/recommended_docs.php", "path" => "intranet/alunni/", "relative" => "documenti/documenti_consigliati.php");

include "recommended_docs.html.php";

?>
