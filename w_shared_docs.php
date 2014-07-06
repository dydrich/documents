<?php
/*
* estrazione documenti condivisi
*/
$sel_ev_docs = "SELECT rb_documents.id, file, doc_type, abstract, titolo, link, privato, id_materia, rb_materie.materia AS materia, cognome, nome FROM rb_documents, rb_documents_shared, rb_materie, rb_utenti WHERE owner = uid AND rb_documents.id = id_documento AND rb_documents.materia = id_materia AND rb_documents_shared.classe = {$_SESSION['__user__']->getClass()} AND anno = {$_SESSION['__current_year__']->get_ID()} ORDER BY data_upload DESC";
//echo $sel_ev_docs;
$res_ev = $db->execute($sel_ev_docs);
if ($res_ev->num_rows < 1){
?>	
<div class="welcome">
	<p id="w_head">Documenti in evidenza</p>
    <p class="w_text" id="hd">
    	Nessun documento condiviso
    </p>
</div>
<?php 	
}
if($res_ev->num_rows > 0){
?>
<h3 style="margin-left: 5%">Documenti condivisi per la tua classe</h3>
<div id="accordion">
<?php
	$shared_docs = array();
   	$x = 0;
	while($doc_ev = $res_ev->fetch_assoc()){
		if (!$shared_docs[$doc_ev['materia']]){
			$shared_docs[$doc_ev['materia']] = array();
		}
		$shared_docs[$doc_ev['materia']][] = $doc_ev;
	}
	foreach ($shared_docs as $mt => $sd){
?>
	<h3><?php print $mt ?></h3>
	<div>
<?php 
		foreach ($sd as $dc){
			if($dc['titolo'] == "") {
				$ab = $dc['abstract'];
			}
			else {
				$ab = $dc['titolo'];
			}
			$ab .= " ({$dc['materia']}, di {$dc['cognome']} {$dc['nome']})";
?>
		<a href="download_manager.php?doc=document&id=<?php print $dc['id'] ?>" class="attention"><?php print utf8_decode($ab) ?></a><br />
<?php
		}
?>
	</div>
<?php 	
	}
?>
</div>
<?php
}
?>