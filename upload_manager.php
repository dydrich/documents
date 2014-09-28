<?php

require_once "../../lib/start.php";
require_once "../../lib/UploadManager.php";
require_once "../../lib/MimeType.php";

check_session();

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>File uploader</title>
<link rel="stylesheet" href="../../css/main.css" type="text/css" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/documents.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript"></script>
</head>
<body style="background-color: #FAFAFA">
<form action="upload_manager.php?action=upload&upl_type=<?php echo $_REQUEST['upl_type'] ?>&tipo=<?php echo $_GET['tipo'] ?>" method="post" enctype="multipart/form-data" id="doc_form">
<div style="height: 25px; display: block" id="_div">
<?php if ($_REQUEST['upl_type'] == "document" && isset($_REQUEST['action'])){ ?>
<fieldset style="padding-left: 2px">
	<legend>File</legend>
<span style="font-weight: normal" id="_span"></span>
</fieldset>
<?php 
}
else{
?>
<input class="form_input" type="file" name="fname" id="fname" class="file" style="width: 90%" onchange="parent.loading(30); document.forms[0].submit()" />
<?php
}
?>
</div>
</form>
</body>
</html>
<?php
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

if (isset($_REQUEST['action']) && $_REQUEST['action'] == "upload"){
	switch ($_GET['upl_type']){
		case "teacherbook_att":
			$path = "download/registri/{$_SESSION['__current_year__']->get_descrizione()}/{$school_order_directory}/docenti/{$user_directory}/";
			break;
		case "document":
			$type = $_GET['tipo'];
			$path = "download/{$type}/";
			break;
	}
	
	$file_name = $_FILES['fname']['name'];
	$file = preg_replace("/ /", "_", $file_name);
	$file = preg_replace("/'/", "", $file);
	$file = preg_replace("/\\\/", "", $file);
	$upload_manager = new UploadManager($path, $_FILES['fname'], $_GET['upl_type'], $db);
	if (isset($_SESSION['registro'])){
		$upload_manager->setData($_SESSION['registro']);
	}
	$ret = $upload_manager->upload();
	$fs = 00;
	$dati_file = MimeType::getMimeContentType($file);
	if (file_exists("../../{$path}{$file}")){
		$fs = filesize("../../{$path}{$file}");
	}
	$dati_file['size'] = formatBytes($fs, 2);
	$dati_file['encoded_name'] = $file;
	$json = json_encode($dati_file);
	$html = addslashes("$file_name<br />Tipo: {$dati_file['tipo']}<br />Size: {$dati_file['size']}");
	if ($_GET['upl_type'] == "document") {
		switch ($ret){
			case UploadManager::FILE_EXISTS:
				print("<script>parent.show_error('File presente in archivio'); parent.reload_iframe();</script>");
				break;
			case UploadManager::UPL_ERROR:
				//echo "ko|There was an error uploading the file, please try again!|".$_FILES['fname']['name'];
				print("<script>parent.show_error('Errore nella copia del file. Riprovare tra poco'); </script>");
				break;
			case UploadManager::UPL_OK:
				print("<script>parent.loaded('".$file."'); $('#_span').html('$html'); </script>");
				break;
		}
	}
	else{
		switch ($ret){
			case UploadManager::FILE_EXISTS:
				print("<script>parent.timeout = 0; window.setTimeout('parent._alert(\"Il file esiste gi&agrave; in archivio. Rinominalo prima di inserirlo\")', 100); window.setTimeout('parent.parent.win.close()', 2000)</script>");
				break;
			case UploadManager::UPL_ERROR:
				echo "ko|There was an error uploading the file, please try again!|".$_FILES['fname']['name'];
				print("<script>parent.timeout = 0; window.setTimeout('parent._alert(\"There was an error uploading the file, please try again!\")', 100); </script>");
				break;
			default:
				echo "<script>parent.timeout = 0; var cont = parent.document.getElementById('att_container');var np = document.createElement('p');np.setAttribute('id', 'att_{$ret}');var _a = document.createElement('a'); _a.setAttribute('href', '#');_a.setAttribute('onclick', 'show_menu(event, {$ret}, \"{$file}\")');_a.style.textDecoration='none';_a.appendChild(document.createTextNode('{$file_name}'));np.appendChild(_a);cont.appendChild(np);</script>";
				break;
		}	
	}
}
