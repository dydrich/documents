<?php

/**
 * inizio della gestione unificata via classe dei documenti, con creazione, modifica e cancellazione
 * si parte con la cancellazione degli allegati al registro docente 
 */

require_once "../../lib/start.php";
require_once '../../lib/EventLogFactory.php';
require_once "lib/Document.php";
require_once "lib/DidacticDocument.php";
require_once "lib/AlboDocument.php";
require_once "lib/RecordGradesAttach.php";
require_once "lib/RBFile.php";
require_once "lib/TeachingDocument.php";
require_once "lib/ClassCommitteeDocument.php";
require_once "lib/SchoolDocument.php";

$sel_module = "SELECT * FROM rb_modules WHERE code_name = 'docs'";
$res_module = $db->execute($sel_module);
$module = $res_module->fetch_assoc();

$module_code = 'docs';

$_SESSION['__modules__'][$module_code]['home'] = $module['home'];
$_SESSION['__modules__'][$module_code]['lib_home'] = $module['lib_home'];
$_SESSION['__modules__'][$module_code]['front_page'] = $module['front_page'];
$_SESSION['__modules__'][$module_code]['path_to_root'] = $module['path_to_root'];
if (isset($_REQUEST['area'])){
	$_SESSION['__mod_area__'] = $_REQUEST['area'];
}

$response = array("status" => "ok");
header("Content-type: application/json");

if ($_POST['action'] == 99) {
	// quick delete
	$f = $_POST['server_file'];
	$t = $_POST['type'];
	$id = $_REQUEST['id'];
	$fp = "download/{$t}/";
	$doc = new Document($id, null, new MYSQLDataLoader($db));
	$doc->setFile($f);
	$doc->setFilePath($fp);
	$doc->setDocumentType($t);
	/*
	if (!$doc->deleteFile()) {
		$response['status'] = "ko";
		$response['message'] = "File non trovato";
		$response['dbg_message'] =
		$res = json_encode($response);
		echo $res;
		exit;
	}
	*/
	$doc->delete();
	$response['message'] = "Il file è stato cancellato";
	$res = json_encode($response);
	echo $res;
	exit;
}

if ($_POST['action'] == 4){
	$f = $_POST['server_file'];
	if ($_POST['doc_type'] == "document" || $_POST['doc_type'] == "document_cdc"){
		$fp = "../../download/{$_POST['tipo']}/{$f}";
	}
	else if ($_POST['doc_type'] == "teaching_doc" || $_POST['tipo'] == 10) {
		if ($_SESSION['__user__']->getSchoolOrder() != ""){
			$ordine_scuola = $_SESSION['__user__']->getSchoolOrder();
			$school_year = $_SESSION['__school_year__'][$ordine_scuola];
			$fine_q = format_date($school_year->getFirstSessionEndDate(), IT_DATE_STYLE, SQL_DATE_STYLE, "-");
			$school_order_directory = "scuola_media";
			if ($ordine_scuola == 2){
				$school_order_directory = "scuola_primaria";
			}

			$user_directory = $_SESSION['__user__']->getFullName();
			$user_directory = preg_replace("/ /", "_", $user_directory);
			$user_directory = strtolower($user_directory);
		}
		$fp = "../../download/registri/{$_SESSION['__current_year__']->get_descrizione()}/{$school_order_directory}/docenti/{$user_directory}/{$f}";
	}
	if (file_exists($fp)){
		unlink($fp);
	}
	else {
		$response['status'] = "ko";
		$response['message'] = "Il file richiesto ({$fp}) non è presente sul server";
		$res = json_encode($response);
		echo $res;
		exit;
	}
	$response['message'] = "Il file è stato cancellato";
	$res = json_encode($response);
	echo $res;
	exit;
}

switch ($_POST['doc_type']){
	case "document":
	case "document_cdc":
	case "teaching_doc":
		if ($_POST['tipo'] == 4){
			if (!isset($_POST['classi'])){
				$_POST['classi'] = array();
			}
			$data = array("anno_scolastico" => $_POST['anno'], "owner" => $_SESSION['__user__']->getUid(), "titolo" => $db->real_escape_string($_POST['titolo']), "doc_type" => $_POST['tipo'], "abstract" => $db->real_escape_string($_POST['abstract']), "file" => $_POST['server_file'], "data_upload" => date("Y-m-d H:i:s"), "categoria" => $_POST['categoria'], "materia" => $_POST['materia'], "classe_rif" => $_POST['classe'], "ordine_scuola" => $_POST['ordine_scuola'], "privato"=> $_POST['private'], "classi" => $_POST['classi'], "tags" => $_POST['tags']);
			$doc = new DidacticDocument($_POST['_i'], $data, new MYSQLDataLoader($db));
		}
		else if ($_POST['tipo'] == 7){
			$data = array(
				"anno_scolastico" => $_POST['anno'], "owner" => $_SESSION['__user__']->getUid(), "titolo" => $db->real_escape_string($_POST['titolo']),
				"doc_type" => $_POST['tipo'], "abstract" => $db->real_escape_string($_POST['abstract']), "file" => $_POST['server_file'],
			    "data_upload" => date("Y-m-d H:i:s"), "categoria" => $_POST['categoria'], "scadenza" => $_POST['scadenza'], "numero_atto" => $_POST['act'],
				"progressivo_atto" => $_POST['progressivo_atto'],  "protocollo" => $db->real_escape_string($_POST['protocol']),
			    "evidenziato" => format_date($_POST['highlighted'], IT_DATE_STYLE, SQL_DATE_STYLE, "-"), "data_pubblicazione" => format_date
				($_POST['published'], IT_DATE_STYLE, SQL_DATE_STYLE, "-"));
			$doc = new AlboDocument($_POST['_i'], $data, new MYSQLDataLoader($db));
		}
        else if ($_POST['tipo'] == 2){
            $data = array(
                "anno_scolastico" => $_POST['anno'], "owner" => $_SESSION['__user__']->getUid(), "titolo" => $db->real_escape_string($_POST['titolo']),
                "doc_type" => $_POST['tipo'], "abstract" => $db->real_escape_string($_POST['abstract']), "file" => $_POST['server_file'],
                "data_upload" => date("Y-m-d H:i:s"), "categoria" => $_POST['categoria'],
                "evidenziato" => format_date($_POST['highlighted'], IT_DATE_STYLE, SQL_DATE_STYLE, "-"));
            $doc = new \eschool\SchoolDocument($_POST['_i'], $data, new MYSQLDataLoader($db));
        }
		else if ($_POST['tipo'] == 10){
			$data = array("anno_scolastico" => $_POST['anno'], "owner" => $_SESSION['__user__']->getUid(), "titolo" => $db->real_escape_string($_POST['titolo']), "doc_type" => $_POST['tipo'], "abstract" => $db->real_escape_string($_POST['abstract']), "file" => $_POST['server_file'], "data_upload" => date("Y-m-d H:i:s"), "categoria" => $_POST['tipo_documento']);
			$alunno = null;
			if (isset($_POST['alunni'])) {
				$alunno = $_POST['alunni'][0];
			}
			$doc = new \eschool\TeachingDocument($_POST['_i'], $data, $_POST['classi'], $_POST['materie'], new MYSQLDataLoader($db), $alunno);
		}
		else if ($_POST['tipo'] == 11){
			$data = array("anno_scolastico" => $_POST['anno'], "owner" => $_SESSION['__user__']->getUid(), "titolo" => $db->real_escape_string($_POST['titolo']), "doc_type" => $_POST['tipo'], "abstract" => $db->real_escape_string($_POST['abstract']), "file" => $_POST['server_file'], "data_upload" => date("Y-m-d H:i:s"), "categoria" => $_POST['tipo_documento']);
			$alunno = null;
			if (isset($_POST['student'])) {
				$alunno = $_POST['student'];
				if ($alunno == 0 || $alunno == "") {
					$alunno = null;
				}
			}
			$doc = new \eschool\ClassCommitteeDocument($_POST['_i'], $data, $_POST['classe'],new MYSQLDataLoader($db), $alunno);
		}
		else {
			$data = array("anno_scolastico" => $_POST['anno'], "owner" => $_SESSION['__user__']->getUid(), "titolo" => $db->real_escape_string($_POST['titolo']), "doc_type" => $_POST['tipo'], "abstract" => $db->real_escape_string($_POST['abstract']), "file" => $_POST['server_file'], "data_upload" => date("Y-m-d H:i:s"), "evidenziato" => format_date($_POST['highlighted'], IT_DATE_STYLE, SQL_DATE_STYLE, "-"), "tags" => $_POST['tags']);
			$doc = new Document($_POST['_i'], $data, new MYSQLDataLoader($db));
		}
		break;
	case "teacherbook_att":
		$file = $_POST['f'];
		$cls = $_POST['cls'];
		$sub = $_POST['sub'];
		$id = $_POST['id'];
		$registro = $_POST['registro'];
		$delete_file = $_POST['delete_file'];
		$id_documento = $_POST['id_doc'];
		$doc = new RecordGradesAttach($file, $_SESSION['__current_year__'], $cls, $_SESSION['__user__'], $sub, new MySQLDataLoader($db), $id_documento);
		$doc->setID($id);
		break;
	case "file":
		$target = $_POST['targetID'];
		$file = $_POST['server_file'];
		$id = $_POST['id'];
		$data = array("id" => $id, "destinatario" => $target, "file" => $file, "data_invio" => null, "data_download" => null);
		$doc = new RBFile($data, new MySQLDataLoader($db));
		try {
			$doc->save();
			$response['status'] = "ok";
		} catch (MySQLException $ex) {
			$response['status'] = "kosql";
			$response['dbg_message'] = "Query: {$ex->getQuery()} ------ Errore: {$ex->getMessage()}";
			$response['message'] = "Si è verificato un errore di rete: controlla lo stato della tua connessione e riprova";
			$res = json_encode($response);
			echo $res;
			exit;
		}
		$response['message'] = "Il file è stato inviato al destinatario";
		$res = json_encode($response);
		echo $res;
		exit;
		break;
	default:
		$response['status'] = "ko";
		$response['dbg_message'] = "Tipo documento sconosciuto: {$_POST['doc_type']} o tipo sconosciuto: {$_POST['tipo']}";
		$response['message'] = "Si è verificato un errore di rete: controlla lo stato della tua connessione e riprova";
		$res = json_encode($response);
		echo $res;
		exit;
		break;
}

try{
	switch ($_POST['action']){
		case INSERT_OBJECT:
			$doc->save();
			$response['message'] = "Il documento è stato inserito";
			break;
		case UPDATE_OBJECT:
			$doc->update();
			$response['message'] = "Il documento è stato modificato";
			break;
		case DELETE_OBJECT:
			if ($doc instanceof RecordGradesAttach && $doc->getDocumentID() != 0) {
				$doc->deleteAttach();
			}
			else {
				$doc->delete();
			}
			$response['message'] = "Il documento è stato cancellato";
			break;
	}
} catch (MySQLException $ex){
	/*
	 * albo pretorio
	 * in caso di errore, restituire il progressivo
	 */
	if ($_POST['action'] == INSERT_OBJECT && $_POST['doc_type'] == "document" && $_POST['tipo'] == 7){
		$max = $db->executeCount("SELECT MAX(progressivo_anno) FROM rb_documents WHERE anno_scolastico = ".$_POST['anno']);
		$max += 1;
		$prog = $db->executeCount("SELECT progressivo_atto FROM rb_progressivi_atto WHERE anno = ".date("Y"));
		if (($prog - $max) == 2) {
			$prog--;
			$db->executeUpdate("UPDATE rb_progressivi_atto SET progressivo_atto = {$prog} WHERE anno = ".date("Y"));
		}
	}
	$response['status'] = "kosql";
	$response['dbg_message'] = "Query: {$ex->getQuery()} ------ Errore: {$ex->getMessage()}";
	$response['message'] = "Si è verificato un errore di rete: controlla lo stato della tua connessione e riprova";
	$res = json_encode($response);
	echo $res;
	exit;
}

$res = json_encode($response);
echo $res;
exit;
