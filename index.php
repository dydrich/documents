<?php

require_once "../../lib/start.php";

check_session();

$navigation_label = "Area documenti - home page";

$_SESSION['no_file'] = array("referer" => "modules/documents/index.php", "path" => "intranet/manager/", "relative" => "index.php");

include 'index.html.php';

?>