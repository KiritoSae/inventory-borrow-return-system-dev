<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = strtolower(trim($_SESSION['role'] ?? ''));

?>

<aside class="sidebar">

    <div class="sidebar-logo">

        <div class="logo-icon">
            📦
        </div>

        <div>
            <h2>Inventory</h2>

            <p>Management System</p>
        </div>

    </div>


    <nav class="sidebar-nav">


        <?php if ($role === 'admin' || $role === 'administrator'): ?>

            <!-- ADMIN MENU -->

            <a href="../admin/dashboard.php" class="nav-link">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>


            <a href="../admin/inventory.php" class="nav-link">
                <span class="nav-icon">📦</span>
                <span>Inventory</span>
            </a>


            <a href="../admin/borrowers.php" class="nav-link">
                <span class="nav-icon">👥</span>
                <span>Borrowers</span>
            </a>


            <a href="../admin/departments.php" class="nav-link">
                <span class="nav-icon">🏢</span>
                <span>Departments</span>
            </a>


            <a href="../qr/scan.php" class="nav-link">
                <span class="nav-icon">📷</span>
                <span>QR Scanner</span>
            </a>


            <a href="../admin/transactions.php" class="nav-link">
                <span class="nav-icon">📋</span>
                <span>Transactions</span>
            </a>


        <?php else: ?>

            <!-- NORMAL USER MENU -->

            <a href="../borrower/dashboard.php" class="nav-link">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>


            <a href="../qr/scan.php" class="nav-link">
                <span class="nav-icon">📷</span>
                <span>Scan QR Code</span>
            </a>


            <a href="../borrower/borrow.php" class="nav-link">
                <span class="nav-icon">📦</span>
                <span>My Borrowed Items</span>
            </a>


            <a href="../borrower/history.php" class="nav-link">
                <span class="nav-icon">📋</span>
                <span>Transaction History</span>
            </a>


        <?php endif; ?>


        <!-- LOGOUT -->

        <a
            href="../logout.php"
            class="nav-link logout-link"
        >
            <span class="nav-icon">🚪</span>
            <span>Logout</span>
        </a>


    </nav>

</aside>
