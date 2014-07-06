<?php

require_once 'Document.php';

class ClassbookDocument extends Document{
	
	private $cls;
	
	public function __construct($file, $y, $cls){
		$this->id = 0;
		$this->file = $file;
		$this->year = $y;
		$this->owner = 0;
		$this->cls = $cls;
		$this->deleteOnDownload = false;
		$this->filePath = "download/registri/{$this->year->get_ID()}/classi/";
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