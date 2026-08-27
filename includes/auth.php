<?php

/*
|--------------------------------------------------------------------------
| Authentication Helper
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| Check if user is logged in
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
        header("Location: ../login.php");
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Get Current User
|--------------------------------------------------------------------------
*/

function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'] ?? null,
        'name' => $_SESSION['user_name'] ?? '',
        'role' => $_SESSION['role'] ?? '',
        'username' => $_SESSION['username'] ?? ''
    ];
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

    $role = strtolower(trim($_SESSION['role'] ?? ''));

    return in_array($role, [
        'admin',
        'administrator'
    ], true);
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
        http_response_code(403);

        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport"
                  content="width=device-width, initial-scale=1.0">

            <title>Access Denied</title>

            <link rel="stylesheet"
                  href="../assets/css/style.css">
        </head>

        <body>

            <div style="
                min-height:100vh;
                display:flex;
                align-items:center;
                justify-content:center;
                padding:20px;
            ">

                <div style="
                    background:#fff;
                    padding:40px;
                    border-radius:15px;
                    text-align:center;
                    max-width:450px;
                    box-shadow:0 5px 25px rgba(0,0,0,.08);
                ">

                    <h1>Access Denied</h1>

                    <p>
                        You do not have permission to access
                        this page.
                    </p>

                    <a
                        href="../borrower/dashboard.php"
                        class="btn btn-primary"
                        style="text-decoration:none;"
                    >
                        Return to Dashboard
                    </a>

                </div>

            </div>

        </body>
        </html>
        ';

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Require Borrower / Normal User
|--------------------------------------------------------------------------
*/

function requireBorrower(): void
{
    requireLogin();

    if (isAdmin()) {
        header("Location: ../admin/dashboard.php");
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Redirect User After Login
|--------------------------------------------------------------------------
*/

function redirectAfterLogin(): void
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }

    if (isAdmin()) {
        header("Location: admin/dashboard.php");
        exit;
    }

    header("Location: borrower/dashboard.php");
    exit;
}
