<?php 
$sel_types = "SELECT * FROM rb_document_types WHERE (id <> 1 AND id <> 8)  ORDER BY id ";
try{
	$res_types = $db->executeQuery($sel_types);
} catch (MySQLException $ex){
	$ex->alert();
}
$types = array();
while($type = $res_types->fetch_assoc()){
	if( ($type['codice'] == "AP" && (!is_installed("albo"))) || ($type['codice'] == "PG" && (!is_installed("project"))) ){
		continue;
	}
	$types[$type['id']] = $type['commento'];
}
?>
		
<div class="smallbox" id="working">
<?php if ($_SESSION['__user__']->check_perms(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM|DOC_PERM)): ?>
	<p class="menu_label class_icon">Gestisci</p>
	<ul class="menublock" style="" dir="rtl">
		<?php
        foreach ($types as $k => $v) {
			if (($k != 4 && $k != 10 && $k != 11) && !$_SESSION['__user__']->check_perms(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM)):
				continue;
			endif;
			if ($k == 7 || $k == 12):
				continue;
			endif;
			if (($k == 4 || $k == 10 || $k == 11)  && (!$_SESSION['__user__']->check_perms(DOC_PERM))):
				continue;
			endif;
		?>
		<li><a href="docs.php?tipo=<?php echo $k ?>"><?php echo $v ?></a></li>
		<?php 
		} 
		?>
	</ul>
<?php endif; ?>
<?php if ($_SESSION['__user__']->check_perms(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM|DOC_PERM|STD_PERM|GEN_PERM|ATA_PERM)): ?>
	<p class="menu_label act_icon">Vedi</p>
	<ul class="menublock" style="" dir="rtl">
		<?php 
		reset($types);
        foreach ($types as $k => $v){
			if ($k != 4 && ($_SESSION['__user__']->check_perms(STD_PERM) || ($_SESSION['__user__']->check_perms(GEN_PERM) && !$_SESSION['__user__']->check_perms(DIR_PERM|DSG_PERM|SEG_PERM|DOC_PERM|ATA_PERM)))):
				continue;
			endif;
			if (($k == 10 || $k == 11)  && (!$_SESSION['__user__']->check_perms(DIR_PERM) || $_SESSION['__role__'] != "Dirigente scolastico")):
				continue;
			endif;
			if ($k == 12):
				continue;
			endif;
		?>
		<li><a href="documents.php?tipo=<?php echo $k ?>"><?php echo $v ?></a></li>
		<?php
		} 
		?>
		<?php if ($_SESSION['__user__']->check_perms(STD_PERM)): ?>
		<li><a href="recommended_docs.php">Documenti consigliati</a>
		<li><a href="shared_docs.php">Documenti condivisi</a>
		<?php endif; ?>
			<?php if ($_SESSION['__user__']->check_perms(GEN_PERM)): ?>
        <li><a href="<?php echo $_SESSION['__path_to_root__'] ?>intranet/genitori/programmazioni.php">Relazioni</a>
			<?php endif; ?>
	</ul>
<?php endif; ?>
<?php if ($_SESSION['__user__']->check_perms(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM)){ ?>
	<p class="menu_label class_icon">Albo pretorio</p>
	<ul class="menublock" style="" dir="rtl">
		<li><a href="docs.php?tipo=7">Gestisci documenti</a></li>
		<?php if ($_SESSION['__user__']->check_perms(DSG_PERM) && $_SESSION['__role__'] == "DSGA"){ ?>
		<li><a href="categories.php">Gestisci categorie di documento</a></li>
		<?php } ?>
		<li><a href="summary.php">Stampa riepiloghi mensili</a></li>
	</ul>
<?php } ?>
</div>
