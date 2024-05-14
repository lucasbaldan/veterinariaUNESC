<?php

namespace App\Models;
use Exception;

class Pessoas{
    
    private $codigo;
    private $login;
    private $senha;

    public function __construct($login, $senha, $codigo = null)
    {
        $this->login = $login;
        $this->senha = $senha;
        $this->codigo = $codigo;

    }

    public function verificarAcesso(){
        $read = new \App\Conn\Read();

        $read->FullRead("SELECT * FROM USUARIOS WHERE USUARIOS.USUARIO =:L AND USUARIOS.SENHA = :S LIMIT 1", "L=$this->login&S=$this->senha");

        return $read->getResult();
    }
}