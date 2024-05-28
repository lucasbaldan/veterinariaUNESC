<?php

namespace App\Helpers;

class UserSessao {
    private static $maxTimeSessao;

    public static function startSession($arrayUsuario) {
        self::$maxTimeSessao = $GLOBALS['timesession'];

        ini_set('session.gc_maxlifetime', self::$maxTimeSessao);
        ini_set('session.cookie_lifetime', self::$maxTimeSessao);

        if (session_status() == PHP_SESSION_ACTIVE) {
            self::encerrarSessao();
        }

        session_start();
        $_SESSION['userid'] = $arrayUsuario[0]['CD_USUARIO'];
        $_SESSION['username'] = $arrayUsuario[0]['TESTE DE USUARIO'];
    }

    public static function verificaSessao() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['userid']) && session_status() == PHP_SESSION_ACTIVE) {
            return true;
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
