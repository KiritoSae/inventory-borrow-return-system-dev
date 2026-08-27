<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| Check Login
|--------------------------------------------------------------------------
*/

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}


/*
|--------------------------------------------------------------------------
| Require Login
|--------------------------------------------------------------------------
*/

function requireLogin(): void
{
    if (!isLoggedIn()) {

        header('Location: ../login.php');
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Check Admin
|--------------------------------------------------------------------------
*/

function isAdmin(): bool
{
    if (!isLoggedIn()) {
        return false;
    }

    $role = strtolower(
        trim($_SESSION['role'] ?? '')
    );

    return $role === 'admin'
        || $role === 'administrator';
}


/*
|--------------------------------------------------------------------------
| Require Admin
|--------------------------------------------------------------------------
*/

function requireAdmin(): void
{
    requireLogin();

    if (!isAdmin()) {

        header('Location: ../borrower/dashboard.php');
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Require Normal User
|--------------------------------------------------------------------------
*/

function requireBorrower(): void
{
    requireLogin();

    if (isAdmin()) {

        header('Location: ../admin/dashboard.php');
        exit;
    }
}
