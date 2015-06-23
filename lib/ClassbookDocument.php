<?php

require_once 'Document.php';

class ClassbookDocument extends Document{
	
	private $cls;
	private $school_order;
	
	public function __construct($file, $y, $cls, $sc){
		$this->id = 0;
		$this->file = $file;
		$this->year = $y;
		$this->owner = 0;
		$this->cls = $cls;
		$this->school_order = $sc;
		$this->deleteOnDownload = false;
		$dir = "scuola_secondaria";
		if ($this->school_order == 1) {
			$dir = "scuola_media";
		}
		else if ($this->school_order == 2) {
			$dir = "scuola_primaria";
		}
		$this->filePath = "download/registri/{$this->year->get_descrizione()}/{$dir}/classi/";
	}
	
	public function download(){
		if (file_exists("../../".$this->getFilePath().$this->file)){
			$this->downloadFile();
		}
		else {
			$_SESSION['no_file']['file'] =  $this->getFilePath().$this->file;
			header("Location: no_file.php");
		}
	}
	
}
