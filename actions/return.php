<?php

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/inventory.php");
    exit;
}


$transactionId = filter_input(
    INPUT_POST,
    'transaction_id',
    FILTER_VALIDATE_INT
);

$itemId = filter_input(
    INPUT_POST,
    'item_id',
    FILTER_VALIDATE_INT
);

$returnCondition = trim(
    $_POST['return_condition'] ?? ''
);

$returnRemarks = trim(
    $_POST['return_remarks'] ?? ''
);


if (
    !$transactionId ||
    !$itemId ||
    $returnCondition === ''
) {
    die(
        "Transaction, item, and return condition are required."
    );
}


try {

    $pdo->beginTransaction();


    /*
     * Find active transaction.
     */
    $stmt = $pdo->prepare("
        SELECT
            id,
            item_id,
            status
        FROM transactions
        WHERE id = ?
        AND item_id = ?
        AND status = 'Borrowed'
        LIMIT 1
        FOR UPDATE
    ");

    $stmt->execute([
        $transactionId,
        $itemId
    ]);

    $transaction = $stmt->fetch(
        PDO::FETCH_ASSOC
    );


    if (!$transaction) {

        throw new Exception(
            "Active borrowing transaction not found."
        );

    }


    /*
     * Update transaction.
     */
    $updateTransaction = $pdo->prepare("
        UPDATE transactions

        SET
            returned_date = NOW(),
            condition_after = ?,
            remarks = CASE
                WHEN ? = '' THEN remarks
                WHEN remarks IS NULL OR remarks = '' THEN ?
                ELSE CONCAT(remarks, '\nReturn: ', ?)
            END,
            status = 'Returned'

        WHERE id = ?
    ");


    $updateTransaction->execute([

        $returnCondition,

        $returnRemarks,

        $returnRemarks,

        $returnRemarks,

        $transactionId

    ]);


    /*
     * Make item available again.
     */
    $updateItem = $pdo->prepare("
        UPDATE items

        SET
            status = 'Available',
            item_condition = ?

        WHERE id = ?
    ");


    $updateItem->execute([

        $returnCondition,

        $itemId

    ]);


    $pdo->commit();


    header(
        "Location: ../admin/inventory.php"
    );

    exit;


} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    die(
        "Return failed: " .
        htmlspecialchars($e->getMessage())
    );
}
?>
