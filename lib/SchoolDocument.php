<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 9/3/16
 * Time: 12:57 AM
 */

namespace eschool;


class SchoolDocument extends \Document
{
    private $category;

    public function __construct($id, $data, \MySQLDataLoader $dl){
        parent::__construct($id, $data, $dl);
        if ($data != null){
            $this->category = $data['categoria'];
        }
        $this->deleteOnDownload = false;
    }

    public function setCategory($c){
        $this->category = $c;
    }

    public function getCategory(){
        return $this->category;
    }

    public function save(){
        $this->id = $this->datasource->executeUpdate("INSERT INTO rb_documents (data_upload, file, doc_type, titolo, abstract, anno_scolastico, owner, evidenziato, categoria) VALUES (NOW(), '{$this->file}', {$this->documentType}, '{$this->title}', '{$this->abstract}', {$this->year}, {$_SESSION['__user__']->getUid()}, ".field_null($this->highlighted, true).", {$this->category})");
        $this->insertTags();
    }

    public function update(){
        $this->datasource->executeUpdate("UPDATE rb_documents SET file = '{$this->file}', titolo = '{$this->title}', abstract = '{$this->abstract}', anno_scolastico = {$this->year}, evidenziato =  ".field_null($this->highlighted, true).", categoria = {$this->category} WHERE id = {$this->id}");
        $this->insertTags();
    }

    public function delete(){
        $this->deleteFile();
        $this->datasource->executeUpdate("DELETE FROM rb_documents_tags WHERE id_documento = {$this->id}");
        $this->datasource->executeUpdate("DELETE FROM rb_downloads WHERE doc_id = {$this->id}");
        $this->datasource->executeUpdate("DELETE FROM rb_documents WHERE id = {$this->id}");
    }
}