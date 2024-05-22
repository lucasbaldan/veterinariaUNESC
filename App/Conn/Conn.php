<?php

namespace App\Conn;

use Exception;

class Conn {

    private static $Host;
    private static $Driver;
    private static $User;
    private static $Pass;
    private static $Dbsa;

    /** @var PDO */
    private static $Connect = null;
  
    
    public function __construct(&$conn = false) {
        $this->Conn = $conn;
    }

    /**
     * Conecta com o banco de dados com o pattern Singleton.
     * Retorna um objeto PDO!
     */
    private static function Conectar() {
        try {
            if (self::$Connect == null) {
                $dsn = self::$Driver . ':host=' . self::$Host . ';dbname=' . self::$Dbsa . ';charset=utf8';
                $options = self::$Driver == 'mysql' ? [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8, sql_mode=\'STRICT_ALL_TABLES\''] : []; 
                self::$Connect = new \PDO($dsn, self::$User, self::$Pass, $options); 
            }
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage());
            die;
        }
        self::$Connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return self::$Connect;
    }

    private static function setEnderecoBanco(){
        self::$Host = $GLOBALS['database']['host'];
        self::$Driver = $GLOBALS['database']['driver'];
        self::$User = $GLOBALS['database']['username'];
        self::$Pass = $GLOBALS['database']['password'];
        self::$Dbsa = $GLOBALS['database']['database'];
    }

    /** Retorna um objeto PDO Singleton Pattern. */
    public static function getConn($trasaction = false) {
        self::setEnderecoBanco();
        $conn = self::Conectar();
        if ($trasaction) {
            $conn->setAttribute(\PDO::ATTR_AUTOCOMMIT, 0);
            $conn->beginTransaction();
        }
        return $conn;
    }

    public function Rollback() {
        if ($this->Conn->inTransaction()) {
            $this->Conn->rollback();
            $this->Conn->setAttribute(\PDO::ATTR_AUTOCOMMIT, 1);
        }
    }

    public function Commit() {
        if ($this->Conn->inTransaction()) {
            $this->Conn->commit();
            $this->Conn->setAttribute(\PDO::ATTR_AUTOCOMMIT, 1);
        }
    }
}
