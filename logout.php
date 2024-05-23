<?php
    session_start();
    unset($_SESSION["valid"]);
    unset($_SESSION["id"]);
    unset($_SESSION["email"]);
    unset($_SESSION["password"]);
    unset($_SESSION["role"]);
    header("Location: index.php"); exit;
?>