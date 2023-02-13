<?php
/*
    library sql server wp-sipd
*/

class Wpsipd_sqlsrv{
    private $conn;
    private $dbhost;
    private $dbport;
    private $dbuser;
    private $dbpass;
    private $dbname;
    public function __construct($host,$port,$usrdb,$passdb,$dbname){
        $this->dbhost=$host;
        $this->dbport=$port;
        $this->dbuser=$usrdb;
        $this->dbpass=$passdb;
        $this->dbname=$dbname;
        $this->conn=$this->connect();
    }

    public function __destruct(){
        $this->conn.close();
    }

    private function connect(){
       return new PDO("sqlsrv:Server=".$this->dbhost.",".$this->dbport.";Database=".$this->dbname,$this->dbuser,$this->dbpass);
    }

    public function query($qr,$param){

    }

    public function insert($tbl,$param){

    }

    public function update($tbl,$param,$where){

    }

    public function delete($tbl,$where){

    }

    public function exec($sp,$param){

    }
}