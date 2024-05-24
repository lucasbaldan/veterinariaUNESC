<?php

namespace App\Helpers;

class SessionManager {
    private $sessionTimeoutMinutes;

    public function __construct($sessionTimeoutMinutes = 30) {
        $this->sessionTimeoutMinutes = $sessionTimeoutMinutes;
        $this->startSession();
    }

    private function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function createSession($userId) {
        $_SESSION['user'][$userId] = array(
            'last_activity' => time()
            // Outros dados da sessão podem ser adicionados aqui, se necessário
        );
    }

    public function updateSession($userId) {
        if (isset($_SESSION['user'][$userId])) {
            $_SESSION['user'][$userId]['last_activity'] = time();
        }
    }

    public function isSessionValid($userId) {
        if (isset($_SESSION['user'][$userId])) {
            $session = $_SESSION['user'][$userId];
            if (time() - $session['last_activity'] < $this->sessionTimeoutMinutes * 60) {
                $this->updateSession($userId);
                return true;
            } else {
                $this->endSession($userId);
            }
        }
        return false;
    }

    public function endSession($userId) {
        if (isset($_SESSION['user'][$userId])) {
            unset($_SESSION['user'][$userId]);
        }
    }
}

// Exemplo de uso:

$sessionManager = new SessionManager();

// Criar uma sessão para um usuário
$sessionManager->createSession('user123');

// Verificar se a sessão é válida
if ($sessionManager->isSessionValid('user123')) {
    echo "Sessão válida para o usuário user123.";
} else {
    echo "Sessão inválida ou expirada para o usuário user123.";
}

// Encerrar a sessão de um usuário
$sessionManager->endSession('user123');
