<?php

require_once __DIR__ . '/../includes/auth.php';

requireBorrower();

require_once __DIR__ . '/../config/database.php';


$userId = $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| Get Borrower
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        borrower_code,
        full_name,
        email,
        department_id,
        status
    FROM borrowers
    WHERE user_id = ?
    LIMIT 1
");

$stmt->execute([$userId]);

$borrower = $stmt->fetch(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Statistics
|--------------------------------------------------------------------------
*/

$borrowedCount = 0;
$pendingCount = 0;
$historyCount = 0;


if ($borrower) {

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM transactions
        WHERE borrower_id = ?
        AND status IN ('Borrowed', 'Overdue')
    ");

    $stmt->execute([$borrower['id']]);

    $borrowedCount = (int) $stmt->fetchColumn();


    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM transactions
        WHERE borrower_id = ?
        AND status = 'Pending'
    ");

    $stmt->execute([$borrower['id']]);

    $pendingCount = (int) $stmt->fetchColumn();


    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM transactions
        WHERE borrower_id = ?
    ");

    $stmt->execute([$borrower['id']]);

    $historyCount = (int) $stmt->fetchColumn();

}

?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="app-layout">

    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="main-content">

        <div class="page-header">

            <div>

                <h1>
                    Welcome,
                    <?= htmlspecialchars(
                        $_SESSION['user_name'] ?? 'User'
                    ) ?>!
                </h1>

                <p>
                    Manage your borrowed inventory and requests.
                </p>

            </div>

        </div>


        <!-- QUICK ACTION -->

        <div
            style="
                background:#198754;
                color:white;
                padding:30px;
                border-radius:15px;
                margin-bottom:25px;
            "
        >

            <h2 style="margin-top:0;">
                Need to borrow an item?
            </h2>

            <p>
                Scan the item's QR code to view its information
                and request to borrow it.
            </p>

            <a
                href="../qr/scan.php"
                class="btn"
                style="
                    background:white;
                    color:#198754;
                    text-decoration:none;
                    font-weight:bold;
                "
            >
                📷 Scan QR Code
            </a>

        </div>


        <!-- STATISTICS -->

        <div class="dashboard-cards">

            <div class="dashboard-card">

                <h3>My Borrowed Items</h3>

                <div class="number">
                    <?= $borrowedCount ?>
                </div>

            </div>


            <div class="dashboard-card warning">

                <h3>Pending Requests</h3>

                <div class="number">
                    <?= $pendingCount ?>
                </div>

            </div>


            <div class="dashboard-card">

                <h3>Transaction History</h3>

                <div class="number">
                    <?= $historyCount ?>
                </div>

            </div>

        </div>


        <!-- MENU -->

        <div class="table-container" style="padding:25px;">

            <h2>Quick Access</h2>

            <div
                style="
                    display:grid;
                    grid-template-columns:
                        repeat(auto-fit,minmax(200px,1fr));
                    gap:15px;
                    margin-top:20px;
                "
            >

                <a
                    href="../qr/scan.php"
                    class="btn btn-primary"
                    style="text-decoration:none;"
                >
                    📷 Scan QR Code
                </a>


                <a
                    href="borrow.php"
                    class="btn"
                    style="
                        text-decoration:none;
                        background:#e9ecef;
                        color:#333;
                    "
                >
                    📦 Borrowed Items
                </a>


                <a
                    href="history.php"
                    class="btn"
                    style="
                        text-decoration:none;
                        background:#e9ecef;
                        color:#333;
                    "
                >
                    📋 My History
                </a>

            </div>

        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
