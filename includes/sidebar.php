<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = strtolower(trim($_SESSION['role'] ?? ''));

?>

<aside class="sidebar">

    <div class="sidebar-logo">

        <h2>Inventory</h2>
