<?php

namespace App\Helpers;

class Sessao {
    private static $maxTimeSessao;

    public static function startSession($arrayUsuario) {
        self::$maxTimeSessao = $GLOBALS['timesession'];

        // Configurações de sessão
        ini_set('session.gc_maxlifetime', self::$maxTimeSessao);
        ini_set('session.cookie_lifetime', self::$maxTimeSessao);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);

        if (session_status() == PHP_SESSION_ACTIVE) {
            self::encerrarSessao();
        }

        session_start();

        // Sanitização dos dados do usuário
        $userId = $arrayUsuario['CD_USUARIO'];
        $username = $arrayUsuario['USERNAME'];

        $_SESSION['userid'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['session_start_time'] = time();
    }

    public static function verificaSessao() {
        session_start();
    
        if (isset($_SESSION['userid']) && isset($_SESSION['session_start_time']) && session_status() == PHP_SESSION_ACTIVE) {

            $tempoUltimaOperacao = time() - $_SESSION['session_start_time'];
            if($tempoUltimaOperacao > $GLOBALS['timesession']){
                self::encerrarSessao();
                return false;
            }
            $_SESSION['session_start_time'] = time();
            return true;
        } else {
            self::encerrarSessao();
            return false;
        }
    }

    public static function getInfoSessao(){
        if (isset($_SESSION['userid']) && isset($_SESSION['session_start_time']) && session_status() == PHP_SESSION_ACTIVE) {
            return $_SESSION;
        } else {
            self::encerrarSessao();
            return false;
        }
    }
    

    public static function encerrarSessao() {
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    }
}
