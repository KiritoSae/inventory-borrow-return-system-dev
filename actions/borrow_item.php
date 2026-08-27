<?php

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/inventory.php");
    exit;
}

$itemId = filter_input(
    INPUT_POST,
    'item_id',
    FILTER_VALIDATE_INT
);

$borrowerId = filter_input(
    INPUT_POST,
    'borrower_id',
    FILTER_VALIDATE_INT
);

$dueDate = trim($_POST['due_date'] ?? '');
$remarks = trim($_POST['remarks'] ?? '');

if (!$itemId || !$borrowerId || $dueDate === '') {
    die("Item, borrower, and due date are required.");
}

try {

    $pdo->beginTransaction();

    /*
     * Check item and lock the row.
     */
    $itemStmt = $pdo->prepare("
        SELECT
            id,
            item_code,
            item_name,
            status,
            item_condition
        FROM items
        WHERE id = ?
        FOR UPDATE
    ");

    $itemStmt->execute([$itemId]);

    $item = $itemStmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        throw new Exception("Inventory item not found.");
    }

    if ($item['status'] !== 'Available') {
        throw new Exception("This item is no longer available.");
    }


    /*
     * Check borrower.
     */
    $borrowerStmt = $pdo->prepare("
        SELECT
            id,
            status
        FROM borrowers
        WHERE id = ?
        LIMIT 1
    ");

    $borrowerStmt->execute([$borrowerId]);

    $borrower = $borrowerStmt->fetch(PDO::FETCH_ASSOC);

    if (!$borrower) {
        throw new Exception("Borrower not found.");
    }

    if ($borrower['status'] !== 'Active') {
        throw new Exception("This borrower is inactive.");
    }


    /*
     * Generate transaction code.
     *
     * Example:
     * TRX-000001
     */
    $transactionCode = 'TRX-' . str_pad(
        (string) (time() . random_int(1, 99)),
        6,
        '0',
        STR_PAD_LEFT
    );


    /*
     * Create transaction.
     */
    $transactionStmt = $pdo->prepare("
        INSERT INTO transactions
        (
            transaction_code,
            item_id,
            borrower_id,
            processed_by,
            borrowed_date,
            due_date,
            condition_before,
            remarks,
            status
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            NOW(),
            ?,
            ?,
            ?,
            'Borrowed'
        )
    ");

    $transactionStmt->execute([

        $transactionCode,

        $itemId,

        $borrowerId,

        $_SESSION['user_id'],

        $dueDate,

        $item['item_condition'],

        $remarks ?: null

    ]);


    /*
     * Change inventory status.
     */
    $updateStmt = $pdo->prepare("
        UPDATE items
        SET status = 'Borrowed'
        WHERE id = ?
    ");

    $updateStmt->execute([$itemId]);


    $pdo->commit();


    /*
     * Return to inventory.
     */
    header("Location: ../admin/inventory.php");
    exit;


} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    die(
        "Borrowing failed: " .
        htmlspecialchars($e->getMessage())
    );
}
?>
