<?php

require_once 'Document.php';

class Report extends Document{
	
	private $session;
	private $id_pubb;
	private $student;
	private $parent;
	private $registerReading;
	
	public function __construct($file, $y, $fp, $sess, $id_pubb, $st, DataLoader $dl = null){
		$this->id = 0;
		$this->file = $file;
		$this->year = $y;
		$this->owner = 0;
		$this->filePath = $fp;
		$this->id_pubb = $id_pubb;
		$this->student = $st;
		$this->deleteOnDownload = false;
		$this->filePath = "download/pagelle/";
		$this->area = "intranet";
		$this->session = $sess;
		if ($this->session == 1){
			//$this->deleteOnDownload = true;
			$this->filePath = "tmp/";
		}
		$this->setDocumentType(8);
		if ($dl != null) $this->datasource = $dl;
	}
	
	public function getRegisterReading(){
		return $this->registerReading;
	}
	
	public function setRegisterReading($update){
		$this->registerReading = $update;
	}
	
	public function download($data = null){
		if (file_exists($_SESSION['__config__']['html_root']."/".$this->getFilePath().$this->file)){
			if ($this->getRegisterReading()) {
				$this->registerReportReading($data);
			}
			$this->downloadFile();
			if ($this->deleteOnDownload()){
				$this->deleteFile();
			}
		}
		else {
			$_SESSION['no_file']['file'] =  $_SESSION['__config__']['html_root']."/".$this->getFilePath().$this->file;
			header("Location: no_file.php");
		}
	}
	
	private function registerReportReading($data){
		$parent = $data['parent'];
		$al = $data['student'];
		$idp = $data['idp'];
		$this->datasource->execute("INSERT INTO rb_lettura_pagelle (id_pubblicazione, alunno, data_lettura, genitore) VALUES ({$idp}, {$al}, NOW(), {$parent})");
	}
	
}
