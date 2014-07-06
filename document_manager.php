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

if ($_POST['action'] == 4){
	$f = $_POST['server_file'];
	if ($_POST['doc_type'] == "document"){
		$fp = "../../download/{$_POST['tipo']}/{$f}";
	}
	if (file_exists($fp)){
		unlink($fp);
	}
	else {
		$response['status'] = "ko";
		$response['message'] = "File {$fp} inesistente";
		$res = json_encode($response);
		echo $res;
		exit;
	}
	$response['message'] = "File cancellato";
	$res = json_encode($response);
	echo $res;
	exit;
}

switch ($_POST['doc_type']){
	case "document":
		if ($_POST['tipo'] == 4){
			if (!isset($_POST['classi'])){
				$_POST['classi'] = array();
			}
			$data = array("anno_scolastico" => $_POST['anno'], "owner" => $_SESSION['__user__']->getUid(), "titolo" => $db->real_escape_string($_POST['titolo']), "doc_type" => $_POST['tipo'], "abstract" => $db->real_escape_string($_POST['abstract']), "file" => $_POST['server_file'], "data_upload" => date("Y-m-d H:i:s"), "categoria" => $_POST['categoria'], "materia" => $_POST['materia'], "classe_rif" => $_POST['classe'], "ordine_scuola" => $_POST['ordine_scuola'], "privato"=> $_POST['private'], "classi" => $_POST['classi'], "tags" => $_POST['tags']);
			$doc = new DidacticDocument($_POST['_i'], $data, new MYSQLDataLoader($db));
		}
		else if ($_POST['tipo'] == 7){
			$data = array("anno_scolastico" => $_POST['anno'], "owner" => $_SESSION['__user__']->getUid(), "titolo" => $db->real_escape_string($_POST['titolo']), "doc_type" => $_POST['tipo'], "abstract" => $db->real_escape_string($_POST['abstract']), "file" => $_POST['server_file'], "data_upload" => date("Y-m-d H:i:s"), "categoria" => $_POST['categoria'], "scadenza" => $_POST['scadenza'], "numero_atto" => $_POST['act'], "progressivo_atto" => $_POST['progressivo_atto'],  "protocollo" => $db->real_escape_string($_POST['protocol']), "evidenziato" => format_date($_POST['highlighted'], IT_DATE_STYLE, SQL_DATE_STYLE, "-"));
			$doc = new AlboDocument($_POST['_i'], $data, new MYSQLDataLoader($db));
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
		$doc = new RecordGradesAttach($file, $_SESSION['__current_year__'], $cls, $_SESSION['__user__'], $sub, new MySQLDataLoader($db));
		$doc->setID($id);
		break;
	case "file":
		$target = $_POST['targetID'];
		$file = $_POST['server_file'];
		$id = $_POST['id'];
		$data = array("id" => $id, "destinatario" => $target, "file" => $file, "data_invio" => null, "data_download" => null);
		$doc = new RBFile($data, new MySQLDataLoader($db));
		$doc->save();
		$response['status'] = "ok";
		$response['message'] = "File inviato";
		exit;
		break;
	default:
		$response['status'] = "ko";
		$response['dbg_message'] = "Tipo documento sconosciuto: {$_POST['doc_type']} o tipo sconosciuto: {$_POST['tipo']}";
		$response['message'] = "Errore nella trasmissione dei dati";
		$res = json_encode($response);
		echo $res;
		exit;
		break;
}

try{
	switch ($_POST['action']){
		case INSERT_OBJECT:
			$doc->save();
			$response['message'] = "Documento inserito";
			break;
		case UPDATE_OBJECT:
			$doc->update();
			$response['message'] = "Documento modificato";
			break;
		case DELETE_OBJECT:
			$doc->delete();
			$response['message'] = "Documento cancellato";
			break;
	}
} catch (MySQLException $ex){
	$response['status'] = "kosql";
	$response['dbg_message'] = "Query: {$ex->getQuery()} ------ Errore: {$ex->getMessage()}";
	$response['message'] = "Errore nella registrazione dei dati";
	$res = json_encode($response);
	echo $res;
	exit;
}

$res = json_encode($response);
echo $res;
exit;