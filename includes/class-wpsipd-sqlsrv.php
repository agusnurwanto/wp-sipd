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
    public $status;
    public function __construct($host,$port,$usrdb,$passdb,$dbname){
        $this->dbhost=$host;
        $this->dbport=$port;
        $this->dbuser=$usrdb;
        $this->dbpass=$passdb;
        $this->dbname=$dbname;
        $this->conn=$this->connect();
        if($this->conn)
            $this->status=true;
        else
            $this->status=false;
    }

    public function __destruct(){
        if($this->conn){
            $this->conn.close();
        }
    }

    private function connect(){
        try{
            $conn =new PDO("sqlsrv:Server=".$this->dbhost.",".$this->dbport.";Database=".$this->dbname,$this->dbuser,$this->dbpass);
            $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            return $conn;
        }catch(Exception $e){
            return null;
        }
    }

    //query dinamis sql server
    public function query($qr,$param){

    }

    //menambah data di table sql server
    public function insert($tbl,$param){

    }

    //perbaharui data di table sql server
    public function update($tbl,$param,$where){

    }

    //hapus data di table database sql server
    public function delete($tbl,$where){

    }

    //exec stored procedure sql server
    public function exec($sp,$param){

    }
}