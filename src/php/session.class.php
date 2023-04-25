<?php
class Session {
    private function startSession() {
        $success = false;
        if (session_id() === '') {
            if (session_name() !== 'sws_session' && session_name('sws_session') !== false) {
                // if you already have an HTTPS protocol configured you can set the Secure cookie flag to true
                session_set_cookie_params(24 * 3600, '/', '', false, true);
            }
            session_start();
        }
        if (session_id()) {
            $success = true;
        }
        return $success;
    }
    static function setUser($user) {
        $success = false;
        if (!is_a($user, 'User')) {
            // log possible cookie tampering
        } else if (self::startSession() && session_regenerate_id(true) && session_id()) {
            $_SESSION['user'] = $user;
            $success = true;
        }
        return $success;
    }
    static function getUser() {
        $user = null;
        if (self::startSession() && isset($_SESSION['user'])) {
            if (!is_a($_SESSION['user'], 'User')) {
                // log possible cookie tampering
                self::deleteSession();
            } else if ($_SESSION['user']->getRemoteAddr() !== $_SERVER['REMOTE_ADDR']) {
                // log possible session hijacking
                // need to test using VPN
                self::deleteSession();
            } else {
                $user = $_SESSION['user'];
            }
        }
        return $user;
    }
    static function deleteSession() {
        if (self::startSession()) {
            if (ini_get('session.use_cookies') !== false) {
                $parameters = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $parameters['path'], $parameters['domain'], $parameters['secure'], $parameters['httponly']);
            }
            session_destroy();
        }
    }
    // synchronizer token pattern | unique token per-request
    public function generateToken($name) {
        $token = null;
        $name = "{$name}_csrf";
        if (self::startSession()) {
            $_SESSION['tokens'][$name] = array('value' => bin2hex(openssl_random_pseudo_bytes(32)), 'expiration' => time() + 60 * 15);
            $token = $_SESSION['tokens'][$name]['value'];
        }
        return $token;
    }
    public function verifyToken($name, $value) {
        $success = false;
        $name = "{$name}_csrf";
        if (self::startSession() && isset($_SESSION['tokens'][$name])) {
            if ($value !== $_SESSION['tokens'][$name]['value'] || time() > $_SESSION['tokens'][$name]['expiration']) {
                // log possible CSRF (Cross-Site Request Forgery)
            } else {
                unset($_SESSION['tokens'][$name]);
                $success = true;
            }
        }
        return $success;
    }
}
?>
