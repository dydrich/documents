<?php

require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";
require_once "lib/Document.php";
require_once "lib/DidacticDocument.php";
require_once "lib/AlboDocument.php";
require_once "lib/ClassbookDocument.php";
require_once "lib/RecordGradesAttach.php";
require_once "lib/RecordGradesDocument.php";
require_once "lib/Report.php";
require_once "lib/RBFile.php";
require_once "lib/CircularAttachment.php";
require_once "lib/DocumentBean.php";
require_once "lib/TeachingDocument.php";
require_once "lib/MonthlyReport.php";

ini_set("display_errors", DISPLAY_ERRORS);

$sel_module = "SELECT * FROM rb_modules WHERE code_name = 'docs'";
$res_module = $db->execute($sel_module);
$module = $res_module->fetch_assoc();

$_SESSION['__modules__']['docs']['home'] = $module['home'];
$_SESSION['__modules__']['docs']['lib_home'] = $module['lib_home'];
$_SESSION['__modules__']['docs']['front_page'] = $module['front_page'];
$_SESSION['__modules__']['docs']['path_to_root'] = $module['path_to_root'];
if (isset($_REQUEST['area'])){
	$_SESSION['__mod_area__'] = $_REQUEST['area'];
}

$document = null;

if ($_GET['doc'] == "document"){
	$sel_doc = "SELECT rb_documents.*, rb_anni.descrizione, progressivo_atto, cognome, nome FROM rb_documents, rb_document_types, rb_anni, rb_utenti WHERE doc_type = rb_document_types.id AND rb_anni.id_anno = anno_scolastico AND owner = uid AND rb_documents.id = {$_REQUEST['id']}";
	$res_doc = $db->execute($sel_doc);
	$doc = $res_doc->fetch_assoc();
	
	switch ($doc['doc_type']){
		case 4:
			$document = new DidacticDocument($_GET['id'], $doc, new MYSQLDataLoader($db));
			break;
		case 7:
			$document = new AlboDocument($_GET['id'], $doc, new MYSQLDataLoader($db));
			break;
		case 10:
			$document = new \eschool\TeachingDocument($_GET['id'], $doc, null, null, new MYSQLDataLoader($db), null);
			break;
		default:
			$document = new Document($_GET['id'], $doc, new MYSQLDataLoader($db));
			break;
	}
	try{
		$document->download();
	} catch (MYSQLException $ex){
		echo "kosql|".$ex->getQuery()."|".$ex->getMessage();
		exit;
	}
}
else if ($_GET['doc'] == "classbook") {
	$file = $_GET['f'];
	$f = explode("_", $file);
	$school_order = $_GET['sc'];
	$document = new ClassbookDocument($file.".pdf", $_SESSION['__current_year__'], $f[2], $school_order);
	try{
		$document->download();
	} catch (MYSQLException $ex){
		echo "kosql|".$ex->getQuery()."|".$ex->getMessage();
		exit;
	}
}
else if ($_GET['doc'] == "teacherbook") {
	/*
	 * gestione supplenti
	 */
	$isSuppplyTeacher = false;
	$rb = RBUtilities::getInstance($db);
	if ($_SESSION['__user__']->isSupplyTeacher()) {
		$tit = $_SESSION['__user__']->getLecturer();
		$user = $rb->loadUserFromUid($tit, 'school');
		$isSuppplyTeacher = true;
	}
	else {
		$user = $_SESSION['__user__'];
	}
	$user = $_SESSION['__user__'];

	$file = $_GET['f'];
	$f = explode("_", $file);
	$document = new RecordGradesDocument($file.".pdf", $_SESSION['__current_year__'], $f[3], $user, new MySQLDataLoader($db));
	try{
		$document->download();
	} catch (MYSQLException $ex){
		echo "kosql|".$ex->getQuery()."|".$ex->getMessage();
		exit;
	}
}
else if ($_GET['doc'] == "teacherbookall") {
	/*
	 * gestione supplenti
	 */
	$isSuppplyTeacher = false;
	$rb = RBUtilities::getInstance($db);
	if ($_SESSION['__user__']->isSupplyTeacher()) {
		$tit = $_SESSION['__user__']->getLecturer();
		$user = $rb->loadUserFromUid($tit, 'school');
		$isSuppplyTeacher = true;
	}
	else {
		$user = $_SESSION['__user__'];
	}
	$user = $_SESSION['__user__'];

	$file = $_GET['f'];
	$f = explode("_", $file);
	$support = false;
	if (isset($_GET['support']) && $_GET['support'] == 1){
		$support = true;
	}
	$document = new RecordGradesDocument($file.".pdf", $_SESSION['__current_year__'], $f[2], $user, new MySQLDataLoader($db));
	$document->setHasAttach(true);
	if (!$support){
		$id = $db->executeCount("SELECT id FROM rb_registri_personali WHERE anno = {$_SESSION['__current_year__']->get_ID()} AND docente = {$_SESSION['__user__']->getUid(false)} AND classe = {$f[2]} AND materia = {$f[3]}");
	}
	else {
		$id = $db->executeCount("SELECT id FROM rb_registri_personali WHERE anno = {$_SESSION['__current_year__']->get_ID()} AND docente = {$_SESSION['__user__']->getUid(false)} AND classe = {$f[3]} AND alunno = {$f[4]}");
	}
	$document->setID($id);
	try{
		$document->download();
	} catch (MYSQLException $ex){
		echo "kosql|".$ex->getQuery()."|".$ex->getMessage();
		exit;
	}
}
else if ($_GET['doc'] == "teacherbook_att"){
	/*
	 * documento allegato al registro personale del docente
	*/
	$file = $_GET['f'];
	$cls = $_GET['cls'];
	$sub = $_GET['sub'];

	/*
	 * gestione supplenti
	 */
	$isSuppplyTeacher = false;
	$rb = RBUtilities::getInstance($db);
	if ($_SESSION['__user__']->isSupplyTeacher()) {
		$tit = $_SESSION['__user__']->getLecturer();
		$user = $rb->loadUserFromUid($tit, 'school');
		$isSuppplyTeacher = true;
	}
	else {
		$user = $_SESSION['__user__'];
	}

	$document = new RecordGradesAttach($file, $_SESSION['__current_year__'], $cls, $user, $sub);
	try{
		$document->download();
	} catch (MYSQLException $ex){
		echo "kosql|".$ex->getQuery()."|".$ex->getMessage();
		exit;
	}
}
else if ($_GET['doc'] == "report") {
	/*
	 * reports
	*/
	$file = $_REQUEST['f'];
	$sess = $_REQUEST['sess'];
	$rb = RBUtilities::getInstance($db);
	$final_dir = "scuola-secondaria";
	if (isset($_REQUEST['school_order']) && $_REQUEST['school_order'] == 2){
		$final_dir = "scuola-primaria";
	}
	list ($y_d, $q, $cl, $st) = explode("_", $file);
	$year = $rb->loadYearFromID($_REQUEST['y']);
	$id_pubblicazione = $db->executeCount("SELECT id_pagella FROM rb_pubblicazione_pagelle WHERE anno = {$_REQUEST['y']} AND quadrimestre = {$sess}");
	list($n, $ext) = explode(".", $file);
	$ar = explode("_", $n);
	$stid = $_REQUEST['stid'];
	$document = new Report($file, $year, "", $sess, $id_pubblicazione, $stid, new MySQLDataLoader($db));
	/*
	 * il percorso di archiviazione e` cambiato il secondo anno
	 */
	if ($_REQUEST['y'] > 1 && $sess == 2){
		$dir = "download/pagelle/{$y_d}/{$final_dir}/{$cl}/";
		$document->setFilePath($dir);
	}
	$data = null;
	if ($_GET['noread'] == 1){
		$document->setRegisterReading(false);
	}
	else {
		$data = array();
		$data['parent'] = $_REQUEST['parent'];
		$data['student'] = $stid;
		$data['idp'] = $id_pubblicazione;
		$document->setRegisterReading(true);
	}
	try{
		$document->download($data);
	} catch (MYSQLException $ex){
		echo "kosql|".$ex->getQuery()."|".$ex->getMessage();
		exit;
	}
}
else if ($_GET['doc'] == "file") {
	$data = array();
	$data['id'] = $_GET['id'];
	$data['file'] = $db->executeCount("SELECT file FROM rb_com_files WHERE id = {$_GET['id']}");
	$document = new RBFile($data, new MySQLDataLoader($db));
	try{
		$document->download();
	} catch (MYSQLException $ex){
		echo "kosql|".$ex->getQuery()."|".$ex->getMessage();
		exit;
	}
}
else if ($_GET['doc'] == "allegato") {
	$data = array();
	$data['id'] = $_GET['id'];
	$f = $db->executeCount("SELECT file FROM rb_com_allegati_circolari WHERE id = {$_GET['id']}");
	$data['file'] = preg_replace("/ /", "_", $f);
	$document = new CircularAttachment($data, new MySQLDataLoader($db));
	try{
		$document->download();
	} catch (MYSQLException $ex){
		echo "kosql|".$ex->getQuery()."|".$ex->getMessage();
		exit;
	}
}
else if ($_GET['doc'] == "report_backup") {
	$file = $_GET['f'];
	$session = $_GET['sess'];
	$y = $_GET['y'];
	$area = $_GET['area'];
	$year_desc = $db->executeCount("SELECT descrizione FROM rb_anni WHERE id_anno = {$y}");
	$document = new DocumentBean(0, null, $file, false, null);
	if ($session == 1){
		$folder = "scuola_secondaria";
		if ($area == 2){
			$folder = "scuola_primaria";
		}
		$fp = "/tmp/{$year_desc}/{$session}/{$folder}/";
	}
	else if ($session == 2) {
		$folder = "scuola-secondaria";
		if ($area == 2){
			$folder = "scuola-primaria";
		}
		$fp = "/download/pagelle/{$year_desc}/";
	}
	else {
		$fp = "/download/pagelle/";
	}
	$document->setFilePath($fp);
	try{
		$document->download();
	} catch (MYSQLException $ex){
		echo "kosql|".$ex->getQuery()."|".$ex->getMessage();
		exit;
	}
}
else if ($_GET['doc'] == "teacherbooks_archive") {
	$file = $_GET['f'];
	$y = $_GET['y'];
	$area = $_GET['area'];
	$year_desc = $db->executeCount("SELECT descrizione FROM rb_anni WHERE id_anno = {$y}");
	$document = new DocumentBean(0, null, $file, false, null);
	$fp = "/download/registri/";

	$document->setFilePath($fp);
	try{
		$document->download();
	} catch (MYSQLException $ex){
		echo "kosql|".$ex->getQuery()."|".$ex->getMessage();
		exit;
	}
}
else if ($_GET['doc'] == "monthly_report") {
	$file = $_GET['f'];
	$st = $_GET['st'];
	$document = new \document\MonthlyReport($file, $st, null);
	try{
		$document->download();
	} catch (MYSQLException $ex){
		echo "kosql|".$ex->getQuery()."|".$ex->getMessage();
		exit;
	}
}
