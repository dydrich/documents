<?php

include "../../lib/start.php";

check_session();
check_permission(DSG_PERM);

$navigation_label = "Albo pretorio - categorie di documento";

$sel_type = "SELECT * FROM rb_categorie_docs WHERE tipo_documento = 7";
$res_type = $db->execute($sel_type);

include "categories.html.php";

?>