<?php

require_once "../../lib/start.php";

check_session();

$drawer_label = "Home page";

$_SESSION['__path_to_root__'] = "../../";

$_SESSION['no_file'] = array("referer" => "modules/documents/index.php", "path" => "intranet/manager/", "relative" => "index.php");

include 'index.html.php';
