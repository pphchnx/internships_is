<?php
session_start();

function checkLogin() {
    if(!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit;
    }
}

function checkRole($allowed_role) {
    if(!isset($_SESSION['role']) || $_SESSION['role'] !== $allowed_role) {
        header("Location: ../dashboard.php");
        exit;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>
