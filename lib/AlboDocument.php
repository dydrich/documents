<?php

require_once 'Document.php';

class AlboDocument extends Document{
	
	private $actNumber;
	private $protocol;
	private $progressive;
	private $progressiveString;
	private $dueDate;
	private $category;
	private $publishDate;
	
	public function __construct($id, $data, DataLoader $dl){
		parent::__construct($id, $data, $dl);
		if ($data != null){
			$this->filePath = "download/{$data['doc_type']}/";
			$this->progressive = $data['progressivo_atto'];
			$this->actNumber = $data['numero_atto'];
			$this->protocol = $data['protocollo'];
			$this->dueDate = format_date($data['scadenza'], IT_DATE_STYLE, SQL_DATE_STYLE, "-");
			$this->category = $data['categoria'];
			$this->publishDate = format_date($data['data_pubblicazione'], IT_DATE_STYLE, SQL_DATE_STYLE, "-");
		}	
		$this->deleteOnDownload = false;
		$this->area = "public";
		if ($this->id == 0){
			$this->askForProgressive();
		}
	}
	
	public function setCategory($c){
		$this->category = $c;
	}
	
	public function getCategory(){
		return $this->category;
	}
	
	private function askForProgressive(){
		$sel_prog = "SELECT progressivo_atto FROM rb_progressivi_albo WHERE anno = ".date("Y");
		$prog = $this->datasource->executeCount($sel_prog);
		if ($prog == null || $prog == ""){
			$this->datasource->executeUpdate("INSERT INTO rb_progressivi_albo (progressivo_atto, anno) VALUES (1, ".date("Y").")");
			$prog = $this->datasource->executeCount($sel_prog);
		}
		$max = $this->datasource->executeCount("SELECT MAX(progressivo_anno) FROM rb_documents WHERE doc_type = 7 AND YEAR(data_upload) = ".date("Y"));
		if ($max != null && $max != false && $max != "") {
			$max++;
		}
		else {
			$max = 1;
		}
		if ($prog > $max) {
			$prog = $max;
		}
		$progressivo_atto = $prog."/".date("Y");
		$this->progressive = $prog;
		$this->progressiveString = $progressivo_atto;
	}
	
	private function updateProgressive(){
		$prog = $this->progressive + 1;
		//$max = $this->datasource->executeCount("SELECT MAX(progressivo_anno) FROM rb_documents WHERE anno_scolastico = ".$this->year);
		$upd = "UPDATE rb_progressivi_albo SET progressivo_atto = {$prog} WHERE anno = ".date("Y");
		$this->datasource->executeUpdate($upd);
	}
	
	public function setActNumber($an){
		$this->actNumber = $an;
	}
	
	public function getActNumber(){
		return $this->actNumber;
	}
	
	public function setProtocol($p){
		$this->protocol = $p;
	}
	
	public function getProtocol(){
		return $this->protocol;
	}
	
	public function setProgressive($pr){
		$this->progressive = $pr;
	}
	
	public function getProgressive(){
		return $this->progressive;
	}
	
	public function setDueDate($dd){
		$this->dueDate = $dd;
	}
	
	public function getDueDate(){
		return $this->dueDate;
	}
	
	public function isExpired(){
		return (date("Y-m-d") > $this->dueDate);
	}
	
	public function save(){
		$this->askForProgressive();
		$this->id = $this->datasource->executeUpdate("INSERT INTO rb_documents (progressivo_anno, progressivo_atto, data_upload, file, doc_type, titolo, 
		abstract, anno_scolastico, owner, scadenza, categoria, evidenziato, protocollo, numero_atto, data_pubblicazione) VALUES ({$this->progressive}, 
		'{$this->progressiveString}', NOW(), '{$this->file}', {$this->documentType}, '{$this->title}', '{$this->abstract}', {$this->year}, 
		{$_SESSION['__user__']->getUid()}, '{$this->dueDate}', {$this->category}, ".field_null($this->highlighted, true).", ".field_null($this->protocol, true).", 
		".field_null($this->actNumber, false).", '{$this->publishDate}')");
		$this->updateProgressive();
	}
	
	public function update(){
		$this->datasource->executeUpdate("UPDATE rb_documents SET file = '{$this->file}', titolo = '{$this->title}', abstract = '{$this->abstract}', 
			anno_scolastico = {$this->year}, categoria = {$this->category}, scadenza = '{$this->dueDate}', evidenziato =  ".field_null($this->highlighted, true).", 
			protocollo = ".field_null($this->protocol, true).", numero_atto = ".field_null($this->actNumber, false).", data_pubblicazione = '{$this->publishDate}' 
			WHERE id = {$this->id}");
	}
	
	public function delete(){
		$this->deleteFile();
		$this->datasource->executeUpdate("DELETE FROM rb_downloads WHERE doc_id = {$this->id}");
		$this->datasource->executeUpdate("DELETE FROM rb_documents WHERE id = {$this->id}");
		/*
		 * controllo del valore del progressivo, che va impostato a max+1 (max = valore massimo usato nell'anno)
		 */
		$max = $this->datasource->executeCount("SELECT MAX(progressivo_anno) FROM rb_documents WHERE doc_type = 7 AND YEAR(data_upload) = ".date("Y"));
		if ($max != null && $max != false && $max != "") {
			$max++;
		}
		else {
			$max = 1;
		}
		$this->datasource->executeUpdate("UPDATE rb_progressivi_albo SET progressivo_atto = {$max} WHERE anno = ".date("Y"));

		$data = array();
		$data['docId'] = $this->getID();
		$data['year'] = date("Y");
		$data['prog'] = $this->progressive;
		$elf = \eschool\EventLogFactory::getInstance($data, $this->datasource);
		$log = $elf->getEventLog();
		$log->logDeletedDocument();
	}
	
	/**
	 * @return string
	 */
	public function getPublishDate() {
		return $this->publishDate;
	}
	
	/**
	 * @param string $publishDate
	 */
	public function setPublishDate($publishDate) {
		$this->publishDate = $publishDate;
	}
	
	
}
