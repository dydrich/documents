<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 31/12/15
 * Time: 15.24
 */

namespace document;

require_once 'Document.php';


class MonthlyReport extends \Document
{

	private $session;
	private $student;
	private $registerReading;

	public function __construct($file, $st, DataLoader $dl = null){
		$this->id = 0;
		$this->file = $file;
		$this->owner = 0;
		$this->student = $st;
		$this->deleteOnDownload = true;
		$this->filePath = "tmp/";
		$this->setDocumentType(12);
		if ($dl != null) $this->datasource = $dl;
		$this->registerReading = false;
	}

	public function getRegisterReading(){
		return $this->registerReading;
	}

	public function setRegisterReading($update){
		$this->registerReading = $update;
	}

	public function download($data = null){
		if (file_exists("../../".$this->getFilePath().$this->file)){
			if ($this->getRegisterReading()) {
				$this->registerReportReading($data);
			}
			$this->downloadFile();
			if ($this->deleteOnDownload()){
				$this->deleteFile();
			}
		}
		else {
			$_SESSION['no_file']['file'] =  $this->getFilePath().$this->file;
			header("Location: no_file.php");
		}
	}

	private function registerReportReading($data){

	}

}
