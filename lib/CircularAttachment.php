<?php

require_once 'Document.php';

class CircularAttachment extends Document {
	
	private $downloadDate;
	
	public function __construct($data, DataLoader $dl){
		$this->id = $data['id'];
		$this->file = $data['file'];
		$this->datasource = $dl;
		$this->deleteOnDownload = false;
		$this->filePath = "download/allegati/";
	}
	
	public function save(){
		
	}
	
	public function delete(){
		$this->executeUpdate("DELETE FROM rb_com_allegati_circolari WHERE id = {$this->id}");
		$this->deleteFile();
	}
	
	public function download(){
		$this->downloadFile();
	}
	
}