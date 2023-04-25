<?php
require_once './session.class.php';
if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
    if (Session::getUser()) {
        Session::deleteSession();
    }
}
header('Location: ../');
exit();
?>
