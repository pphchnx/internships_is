<?php
session_start();

function checkLogin() {
    $base = '/project_is/hu_internships';
    if(!isset($_SESSION['user_id'])) {
        header("Location: " . $base . "/login.php");
        exit;
    }
}

function checkRole($allowed_role) {
    $base = '/project_is/hu_internships';
    if(!isset($_SESSION['role']) || $_SESSION['role'] !== $allowed_role) {
        header("Location: " . $base . "/dashboard.php");
        exit;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>
