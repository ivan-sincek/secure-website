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
                // log a possible cookie tampering
                self::deleteSession();
            } else if ($_SESSION['user']->getRemoteAddr() !== $_SERVER['REMOTE_ADDR']) {
                // log possible session hijacking
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
    public function generateToken($name) {
        $token = null;
        if (self::startSession()) {
            $_SESSION['tokens']["${name}_token"] = array('value' => bin2hex(random_bytes(64)), 'expiration' => time() + 60 * 15);
            $token = $_SESSION['tokens']["${name}_token"]['value'];
        }
        return $token;
    }
    public function verifyToken($name, $value) {
        $success = false;
        if (self::startSession() && isset($_SESSION['tokens']["${name}_token"])) {
            if ($value !== $_SESSION['tokens']["${name}_token"]['value'] || time() > $_SESSION['tokens']["${name}_token"]['expiration']) {
                // log possible CSRF (Cross-Site Request Forgery)
            } else {
                $success = true;
            }
        }
        return $success;
    }
}
?>
