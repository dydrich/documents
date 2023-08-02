<?php

require_once "../../lib/start.php";
require_once "../../lib/MimeType.php";
require_once '../../lib/EventLogFactory.php';
require_once "lib/DidacticDocument.php";
require_once "lib/Document.php";
require_once "lib/AlboDocument.php";
require_once "lib/TeachingDocument.php";
require_once "lib/ClassCommitteeDocument.php";
require_once "lib/SchoolDocument.php";
require_once "../../lib/ArrayMultiSort.php";
require_once "../../lib/RBUtilities.php";

ini_set("display_errors", DISPLAY_ERRORS);

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|DOC_PERM);

$_SESSION['__path_to_root__'] = "../../";

// per la paginazione del REFERER
if (isset($_SERVER['HTTP_REFERER'])){
	$referer = $_SERVER['HTTP_REFERER'];
}
else {
	$referer = $_SERVER['PHP_SELF'];
}

$sel_gruppi = "SELECT * FROM rb_gruppi ORDER BY nome";
$res_gruppi = $db->executeQuery($sel_gruppi);

if($_REQUEST['_i'] != 0){
	$_i = $_REQUEST['_i'];
	$sel_doc = "SELECT * FROM rb_documents WHERE id = ".$_REQUEST['_i'];
	$r_doc = $db->executeQuery($sel_doc);
	$current_doc = $r_doc->fetch_assoc();
	$array_gruppi = explode(",", $current_doc['gruppi']);
	$tipo = $current_doc['doc_type'];
	// condivisioni
	$sel_share = "SELECT * FROM rb_documents_shared WHERE id_documento = {$_i}";
	$res_share = $db->executeQuery($sel_share);
	$sel_tags = "SELECT tag FROM rb_tags, rb_documents_tags WHERE rb_tags.tid = rb_documents_tags.tid AND id_documento = {$_i}";
	$res_tags = $db->executeQuery($sel_tags);
	$tags = array();
	if ($res_tags->num_rows > 0){
		while ($rt = $res_tags->fetch_assoc()){
			$tags[] = $rt['tag'];
		}
	}
}
else{
	$_i = 0;
	$current_doc = null;
	$tipo = $_REQUEST['tipo'];
}

$drawer_label = "Gestione documento";

// anni scolastici
$sel_anni = "SELECT id_anno, descrizione FROM rb_anni WHERE id_anno <= ".$_SESSION['__current_year__']->get_ID()." ORDER BY id_anno DESC";
$res_anni = $db->executeQuery($sel_anni);

/*
 * limitazioni tipo file
 */
$res_ext = $db->executeQuery("SELECT extension FROM rb_document_extensions WHERE doc_type = {$tipo}");
$ext = null;
if ($res_ext->num_rows > 0) {
	$ext = [];
	while ($r = $res_ext->fetch_assoc()) {
		$ext[] = strtolower($r['extension']);
	}
}

$default_due_date = null;

if ($tipo == 4){
	// materie
	$sel_materie = "SELECT * FROM rb_materie WHERE id_materia > 2 AND tipologia_scuola = {$_SESSION['__user__']->getSchoolOrder()}";
	$res_materie = $db->executeQuery($sel_materie);
	
	// categorie
	$sel_categorie = "SELECT * FROM rb_categorie_docs WHERE tipo_documento = 4";
	$res_categorie = $db->executeQuery($sel_categorie);
	
	// ordini di scuola
	$sel_ordini = "SELECT * FROM rb_tipologia_scuola WHERE id_tipo <> 6 AND id_tipo <> 4 AND attivo = 1";
	$res_ordini = $db->executeQuery($sel_ordini);
	
	$document = new DidacticDocument($_i, $current_doc, new MySQLDataLoader($db));

	$shared = array();
	if (isset($res_share) && $res_share->num_rows > 0){
		while ($row = $res_share->fetch_assoc()){
			$shared[] = $row['classe'];
		}
		$document->setAllowedClasses($shared);
	}
}
else if ($tipo == 7){
	$sel_categorie = "SELECT * FROM rb_categorie_docs WHERE tipo_documento = 7";
	$res_categorie = $db->executeQuery($sel_categorie);
	
	$document = new AlboDocument($_REQUEST['_i'], $current_doc, new MySQLDataLoader($db));

	$default_due_date = null;
	$default_due_date = $db->executeCount('SELECT DATE(DATE_ADD(NOW(), INTERVAL +15 DAY)) AS due_date');
}
else if ($tipo == 10) {
	$sel_tipologie = "SELECT * FROM rb_tipologie_relazione_docente ORDER BY id";
	$res_tipologie = $db->executeQuery($sel_tipologie);
	$classi = $_SESSION['__user__']->getClasses();
	$msarray = new ArrayMultiSort($classi);
	$msarray->setSortFields(array("classe"));
	$msarray->sort();
	$classi = $msarray->getData();

	if ($_SESSION['__user__']->getSubject() == 27 || $_SESSION['__user__']->getSubject() == 41) {
		$sel_sts = "SELECT nome, cognome, id_alunno, id_classe FROM rb_alunni, rb_assegnazione_sostegno WHERE alunno = id_alunno AND anno = {$_SESSION['__current_year__']->get_ID()} AND docente = {$_SESSION['__user__']->getUid()}";
		$res_sts = $db->executeQuery($sel_sts);
	}

	$rb = RBUtilities::getInstance($db);
	$subjects = $rb->getSubjectsOfTeacher($_SESSION['__user__']);

	if ($_i != 0) {
		$document = new \eschool\TeachingDocument($_i, $current_doc, null, null, new MYSQLDataLoader($db), null);
	}
}
else if ($tipo == 11) {
	$sel_tipologie = "SELECT * FROM rb_tipologie_documento_cdc ORDER BY id";
	$res_tipologie = $db->executeQuery($sel_tipologie);
	$classi = $_SESSION['__user__']->getClasses();
	$msarray = new ArrayMultiSort($classi);
	$msarray->setSortFields(array("classe"));
	$msarray->sort();
	$classi = $msarray->getData();

	if ($_i != 0) {
		$document = new \eschool\ClassCommitteeDocument($_i, $current_doc, null, new MYSQLDataLoader($db), null);
	}
}
else if ($tipo == 2) {
    // categorie
    $sel_categorie = "SELECT * FROM rb_categorie_docs WHERE tipo_documento = 2";
    $res_categorie = $db->executeQuery($sel_categorie);

    if ($_i != 0) {
        $document = new \eschool\SchoolDocument($_REQUEST['_i'], $current_doc, new MySQLDataLoader($db));
    }
}
else {
	$document = new Document($_REQUEST['_i'], $current_doc, new MySQLDataLoader($db));
}

if($_REQUEST['_i'] != 0){
	$document->setTags($tags);
}

include "doc.html.php";
