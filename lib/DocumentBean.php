<?php

require_once 'Document.php';

/**
 * a simple container for downloading documents
 * @author riccardo
 *
 */

class DocumentBean extends Document{
	
	public function __construct($id, $dt, $f, $dod, $o){
		$this->id = $id;
		$this->documentType = $dt;
		$this->file = $f;
		$this->deleteOnDownload = $dod;
		$this->filePath = "download/".$this->documentType."/";
		$this->owner = $o;
	}
	
	public function deleteOnDownload(){
		return $this->deleteOnDownload();
	}

	public function download(){
		if (file_exists("../../".$this->getFilePath().$this->file)){
			parent::downloadFile();
		}
		else {
			$_SESSION['no_file']['file'] =  $this->getFilePath().$this->file;
			if ($_SESSION['no_file']['referer'] == "albo/index.php"){
				$_SESSION['no_file']['id'] = $this->getID();
				$_SESSION['no_file']['fn'] = $this->getTitle();
				header("Location: ../../../albo-pretorio/no_file.php");
			}
			else {
				header("Location: no_file.php");
			}
		}
	}

}