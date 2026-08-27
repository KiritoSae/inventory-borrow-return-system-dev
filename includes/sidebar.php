<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<aside class="sidebar">

    <div class="sidebar-logo">

        <h2>Inventory</h2>

        <p>
            Management System
        </p>

    </div>


    <nav>

        <?php if (
            isset($_SESSION['role']) &&
            strtolower($_SESSION['role']) === 'admin'
        ): ?>

            <!-- ADMIN MENU -->

            <a href="../admin/dashboard.php">
                Dashboard
            </a>

            <a href="../admin/inventory.php">
                Inventory
            </a>

            <a href="../admin/borrowers.php">
                Borrowers
            </a>

            <a href="../admin/departments.php">
                Departments
            </a>

            <a href="../qr/scanner.php">
                QR Scanner
            </a>

            <a href="../admin/transactions.php">
                Transactions
            </a>


        <?php else: ?>

            <!-- NORMAL USER MENU -->

            <a href="../borrower/dashboard.php">
                Dashboard
            </a>

            <a href="../qr/scan.php">
                📷 Scan QR Code
            </a>

            <a href="../borrower/borrow.php">
                My Borrowed Items
            </a>

            <a href="../borrower/history.php">
                Transaction History
            </a>

        <?php endif; ?>


        <!-- LOGOUT -->

        <a
            href="../logout.php"
            style="
                margin-top:20px;
                color:#dc3545;
            "
        >
            Logout
        </a>

    </nav>

</aside>
