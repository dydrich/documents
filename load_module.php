<?php

/**
 * load the requested module
 */

require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

check_session();

$module_code = $_REQUEST['module'];

$sel_module = "SELECT * FROM rb_modules WHERE code_name = '{$module_code}'";
$res_module = $db->execute($sel_module);
$module = $res_module->fetch_assoc();

$_SESSION['__modules__'][$module_code]['home'] = $module['home'];
$_SESSION['__modules__'][$module_code]['lib_home'] = $module['lib_home'];
$_SESSION['__modules__'][$module_code]['front_page'] = $module['front_page'];
$_SESSION['__modules__'][$module_code]['path_to_root'] = $module['path_to_root'];

$_SESSION['__mod_area__'] = $_REQUEST['area'];

if (isset($_REQUEST['page'])){
	if ($_REQUEST['page'] == "tags"){
		header("Location: {$_REQUEST['page']}.php?tag={$_REQUEST['tag']}");
	}
	else if ($_REQUEST['page'] == "document"){
		header("Location: {$_REQUEST['page']}.php?id={$_REQUEST['value']}");
	}
}
else {
	header("Location: {$module['front_page']}");
}
