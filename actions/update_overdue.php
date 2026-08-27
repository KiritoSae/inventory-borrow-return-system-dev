<?php

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

require_once __DIR__ . '/../config/database.php';

/*
 * Automatically mark borrowed transactions as overdue
 * when the due date has passed.
 */

$stmt = $pdo->prepare("
    UPDATE transactions
    SET status = 'Overdue'
    WHERE status = 'Borrowed'
      AND due_date < NOW()
");

$stmt->execute();

$updated = $stmt->rowCount();

/*
 * Return to the transaction history page.
 */
header("Location: ../admin/transactions.php");
exit;
?>
