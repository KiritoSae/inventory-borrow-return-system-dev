<?php

require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

require_once __DIR__ . '/../config/database.php';

?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="app-layout">

    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>


    <main class="main-content">

        <div class="page-header">

            <div>

                <h1>Dashboard</h1>

                <p>
                    Welcome back,
                    <?= htmlspecialchars($_SESSION['full_name']) ?>.
                </p>

            </div>

        </div>


        <div class="dashboard-cards">

            <div class="dashboard-card">

                <h3>Total Items</h3>

                <div class="number">
                    0
                </div>

            </div>


            <div class="dashboard-card success">

                <h3>Available</h3>

                <div class="number">
                    0
                </div>

            </div>


            <div class="dashboard-card">

                <h3>Borrowed</h3>

                <div class="number">
                    0
                </div>

            </div>


            <div class="dashboard-card warning">

                <h3>Overdue</h3>

                <div class="number">
                    0
                </div>

            </div>

        </div>


        <div class="table-container">

            <table class="data-table">

                <thead>

                    <tr>

                        <th>
                            Recent Transaction
                        </th>

                        <th>
                            Item
                        </th>

                        <th>
                            Borrower
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td colspan="4">
                            No transactions yet.
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
