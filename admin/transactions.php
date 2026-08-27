<?php

require_once __DIR__ . '/../includes/auth.php';

requireLogin();

require_once __DIR__ . '/../config/database.php';


// ==============================
// SEARCH & FILTERS
// ==============================

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';


// ==============================
// BUILD QUERY
// ==============================

$sql = "
    SELECT
        t.id,
        t.transaction_code,
        t.borrowed_date,
        t.due_date,
        t.returned_date,
        t.status,
        t.condition_before,
        t.condition_after,
        t.remarks,

        i.item_code,
        i.item_name,

        b.borrower_code,
        b.full_name AS borrower_name,

        d.department_name,

        u.full_name AS processed_by

    FROM transactions t

    INNER JOIN items i
        ON t.item_id = i.id

    INNER JOIN borrowers b
        ON t.borrower_id = b.id

    INNER JOIN departments d
        ON b.department_id = d.id

    INNER JOIN users u
        ON t.processed_by = u.id

    WHERE 1 = 1
";

$params = [];


// ==============================
// SEARCH
// ==============================

if ($search !== '') {

    $sql .= "
        AND (
            t.transaction_code LIKE :search
            OR i.item_code LIKE :search
            OR i.item_name LIKE :search
            OR b.borrower_code LIKE :search
            OR b.full_name LIKE :search
            OR d.department_name LIKE :search
        )
    ";

    $params[':search'] = '%' . $search . '%';
}


// ==============================
// STATUS FILTER
// ==============================

if ($status !== '') {

    $sql .= " AND t.status = :status";

    $params[':status'] = $status;
}


// ==============================
// DATE FILTER
// ==============================

if ($from_date !== '') {

    $sql .= " AND DATE(t.borrowed_date) >= :from_date";

    $params[':from_date'] = $from_date;
}


if ($to_date !== '') {

    $sql .= " AND DATE(t.borrowed_date) <= :to_date";

    $params[':to_date'] = $to_date;
}


// ==============================
// SORT
// ==============================

$sql .= " ORDER BY t.borrowed_date DESC";


// ==============================
// EXECUTE QUERY
// ==============================

$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ==============================
// SUMMARY COUNTS
// ==============================

$total_transactions = (int) $pdo
    ->query("SELECT COUNT(*) FROM transactions")
    ->fetchColumn();


$total_borrowed = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM transactions
        WHERE status = 'Borrowed'
    ")
    ->fetchColumn();


$total_returned = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM transactions
        WHERE status = 'Returned'
    ")
    ->fetchColumn();


$total_overdue = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM transactions
        WHERE status = 'Overdue'
    ")
    ->fetchColumn();


$total_lost = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM transactions
        WHERE status = 'Lost'
    ")
    ->fetchColumn();

?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>


<div class="app-layout">

    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>


    <main class="main-content">


        <!-- PAGE HEADER -->

        <div class="page-header">

            <div>

                <h1>Transaction History</h1>

                <p>
                    View and monitor all inventory borrowing and return transactions.
                </p>

            </div>

        </div>


        <!-- SUMMARY CARDS -->

        <div class="dashboard-cards">


            <div class="dashboard-card">

                <h3>Total Transactions</h3>

                <div class="number">
                    <?= $total_transactions ?>
                </div>

            </div>


            <div class="dashboard-card success">

                <h3>Currently Borrowed</h3>

                <div class="number">
                    <?= $total_borrowed ?>
                </div>

            </div>


            <div class="dashboard-card">

                <h3>Returned</h3>

                <div class="number">
                    <?= $total_returned ?>
                </div>

            </div>


            <div class="dashboard-card warning">

                <h3>Overdue</h3>

                <div class="number">
                    <?= $total_overdue ?>
                </div>

            </div>

        </div>


        <!-- SEARCH AND FILTER -->

        <div class="table-container" style="padding: 20px; margin-bottom: 25px;">

            <form method="GET">

                <div style="
                    display: grid;
                    grid-template-columns: 2fr 1fr 1fr 1fr auto;
                    gap: 12px;
                    align-items: end;
                ">


                    <!-- SEARCH -->

                    <div class="form-group" style="margin-bottom: 0;">

                        <label for="search">
                            Search
                        </label>

                        <input
                            type="text"
                            id="search"
                            name="search"
                            class="form-control"
                            placeholder="Transaction, item, borrower..."
                            value="<?= htmlspecialchars($search) ?>"
                        >

                    </div>


                    <!-- STATUS -->

                    <div class="form-group" style="margin-bottom: 0;">

                        <label for="status">
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="form-control"
                        >

                            <option value="">All Status</option>

                            <option
                                value="Borrowed"
                                <?= $status === 'Borrowed' ? 'selected' : '' ?>
                            >
                                Borrowed
                            </option>

                            <option
                                value="Returned"
                                <?= $status === 'Returned' ? 'selected' : '' ?>
                            >
                                Returned
                            </option>

                            <option
                                value="Overdue"
                                <?= $status === 'Overdue' ? 'selected' : '' ?>
                            >
                                Overdue
                            </option>

                            <option
                                value="Lost"
                                <?= $status === 'Lost' ? 'selected' : '' ?>
                            >
                                Lost
                            </option>

                        </select>

                    </div>


                    <!-- FROM DATE -->

                    <div class="form-group" style="margin-bottom: 0;">

                        <label for="from_date">
                            From
                        </label>

                        <input
                            type="date"
                            id="from_date"
                            name="from_date"
                            class="form-control"
                            value="<?= htmlspecialchars($from_date) ?>"
                        >

                    </div>


                    <!-- TO DATE -->

                    <div class="form-group" style="margin-bottom: 0;">

                        <label for="to_date">
                            To
                        </label>

                        <input
                            type="date"
                            id="to_date"
                            name="to_date"
                            class="form-control"
                            value="<?= htmlspecialchars($to_date) ?>"
                        >

                    </div>


                    <!-- BUTTON -->

                    <button
                        type="submit"
                        class="btn btn-primary"
                        style="width: auto;"
                    >
                        Search
                    </button>

                </div>

            </form>

        </div>


        <!-- TRANSACTION TABLE -->

        <div class="table-container">

            <table class="data-table">

                <thead>

                    <tr>

                        <th>Transaction</th>

                        <th>Item</th>

                        <th>Borrower</th>

                        <th>Department</th>

                        <th>Borrowed</th>

                        <th>Due</th>

                        <th>Returned</th>

                        <th>Status</th>

                    </tr>

                </thead>


                <tbody>


                    <?php if (empty($transactions)): ?>

                        <tr>

                            <td colspan="8" style="text-align: center; padding: 35px;">

                                No transactions found.

                            </td>

                        </tr>


                    <?php else: ?>


                        <?php foreach ($transactions as $transaction): ?>


                            <tr>


                                <!-- TRANSACTION -->

                                <td>

                                    <strong>
                                        <?= htmlspecialchars($transaction['transaction_code']) ?>
                                    </strong>

                                </td>


                                <!-- ITEM -->

                                <td>

                                    <strong>
                                        <?= htmlspecialchars($transaction['item_name']) ?>
                                    </strong>

                                    <br>

                                    <small style="color: #78847d;">

                                        <?= htmlspecialchars($transaction['item_code']) ?>

                                    </small>

                                </td>


                                <!-- BORROWER -->

                                <td>

                                    <?= htmlspecialchars($transaction['borrower_name']) ?>

                                    <br>

                                    <small style="color: #78847d;">

                                        <?= htmlspecialchars($transaction['borrower_code']) ?>

                                    </small>

                                </td>


                                <!-- DEPARTMENT -->

                                <td>

                                    <?= htmlspecialchars($transaction['department_name']) ?>

                                </td>


                                <!-- BORROWED DATE -->

                                <td>

                                    <?= date(
                                        'M d, Y',
                                        strtotime($transaction['borrowed_date'])
                                    ) ?>

                                </td>


                                <!-- DUE DATE -->

                                <td>

                                    <?= date(
                                        'M d, Y',
                                        strtotime($transaction['due_date'])
                                    ) ?>

                                </td>


                                <!-- RETURNED DATE -->

                                <td>

                                    <?php if ($transaction['returned_date']): ?>

                                        <?= date(
                                            'M d, Y',
                                            strtotime($transaction['returned_date'])
                                        ) ?>

                                    <?php else: ?>

                                        <span style="color: #78847d;">
                                            —
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- STATUS -->

                                <td>

                                    <?php

                                    $status_class = match ($transaction['status']) {

                                        'Borrowed' => 'status-borrowed',

                                        'Overdue' => 'status-overdue',

                                        'Returned' => 'status-available',

                                        default => 'status-maintenance'

                                    };

                                    ?>


                                    <span class="status <?= $status_class ?>">

                                        <?= htmlspecialchars($transaction['status']) ?>

                                    </span>

                                </td>


                            </tr>


                        <?php endforeach; ?>


                    <?php endif; ?>


                </tbody>

            </table>

        </div>


    </main>

</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
