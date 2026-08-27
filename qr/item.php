<?php

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

require_once __DIR__ . '/../config/database.php';


$code = trim($_GET['code'] ?? '');


if ($code === '') {

    die("No item code was provided.");

}


/*
 * Find the inventory item.
 *
 * We search using qr_code first because
 * that is what the QR contains.
 */

$stmt = $pdo->prepare("
    SELECT
        items.id,
        items.item_code,
        items.item_name,
        items.serial_number,
        items.location,
        items.item_condition,
        items.status,
        categories.category_name

    FROM items

    INNER JOIN categories
        ON items.category_id = categories.id

    WHERE items.qr_code = ?

    LIMIT 1
");

$stmt->execute([$code]);

$item = $stmt->fetch(PDO::FETCH_ASSOC);


/*
 * If QR value wasn't found through qr_code,
 * also try item_code.
 *
 * This makes the system more tolerant
 * of existing QR codes.
 */

if (!$item) {

    $stmt = $pdo->prepare("
        SELECT
            items.id,
            items.item_code,
            items.item_name,
            items.serial_number,
            items.location,
            items.item_condition,
            items.status,
            categories.category_name

        FROM items

        INNER JOIN categories
            ON items.category_id = categories.id

        WHERE items.item_code = ?

        LIMIT 1
    ");

    $stmt->execute([$code]);

    $item = $stmt->fetch(PDO::FETCH_ASSOC);

}


?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Inventory Item</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    <style>

        .item-page {
            max-width: 700px;
            margin: 0 auto;
        }

        .item-card {
            background: #ffffff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .item-title {
            margin-bottom: 5px;
        }

        .item-code {
            color: #198754;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 25px;
        }

        .item-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .detail-box {
            background: #f7f9f8;
            padding: 15px;
            border-radius: 10px;
        }

        .detail-label {
            display: block;
            font-size: 12px;
            color: #78847d;
            margin-bottom: 5px;
        }

        .detail-value {
            font-weight: 600;
        }

        .item-actions {
            margin-top: 25px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .not-found {
            text-align: center;
            padding: 30px;
        }

        @media (max-width: 600px) {

            .item-card {
                padding: 20px;
            }

            .item-details {
                grid-template-columns: 1fr;
            }

            .item-actions .btn {
                width: 100%;
            }

        }

    </style>

</head>


<body>


<div class="app-layout">

    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>


    <main class="main-content">


        <div class="item-page">


            <?php if (!$item): ?>


                <div class="item-card not-found">

                    <h1>
                        Item Not Found
                    </h1>

                    <p>
                        The scanned QR code does not match
                        an inventory item.
                    </p>

                    <a
                        href="scan.php"
                        class="btn btn-primary"
                        style="text-decoration:none;"
                    >
                        Scan Again
                    </a>

                </div>


            <?php else: ?>


                <div class="item-card">


                    <h1 class="item-title">

                        <?= htmlspecialchars(
                            $item['item_name']
                        ) ?>

                    </h1>


                    <div class="item-code">

                        <?= htmlspecialchars(
                            $item['item_code']
                        ) ?>

                    </div>


                    <div class="item-details">


                        <div class="detail-box">

                            <span class="detail-label">
                                Category
                            </span>

                            <span class="detail-value">

                                <?= htmlspecialchars(
                                    $item['category_name']
                                ) ?>

                            </span>

                        </div>


                        <div class="detail-box">

                            <span class="detail-label">
                                Status
                            </span>

                            <span class="detail-value">

                                <?= htmlspecialchars(
                                    $item['status']
                                ) ?>

                            </span>

                        </div>


                        <div class="detail-box">

                            <span class="detail-label">
                                Serial Number
                            </span>

                            <span class="detail-value">

                                <?= htmlspecialchars(
                                    $item['serial_number']
                                    ?: '—'
                                ) ?>

                            </span>

                        </div>


                        <div class="detail-box">

                            <span class="detail-label">
                                Location
                            </span>

                            <span class="detail-value">

                                <?= htmlspecialchars(
                                    $item['location']
                                    ?: '—'
                                ) ?>

                            </span>

                        </div>


                        <div class="detail-box">

                            <span class="detail-label">
                                Condition
                            </span>

                            <span class="detail-value">

                                <?= htmlspecialchars(
                                    $item['item_condition']
                                ) ?>

                            </span>

                        </div>


                    </div>


                    <div class="item-actions">


                        <?php if ($item['status'] === 'Available'): ?>


                            <a
                                href="../borrower/borrow.php?item_id=<?= $item['id'] ?>"
                                class="btn btn-primary"
                                style="text-decoration:none;"
                            >
                                Request to Borrow
                            </a>


                        <?php elseif ($item['status'] === 'Borrowed'): ?>


                            <div
                                style="
                                    background:#fff3cd;
                                    color:#664d03;
                                    padding:12px;
                                    border-radius:8px;
                                    width:100%;
                                "
                            >

                                This item is currently borrowed.

                            </div>


                        <?php elseif ($item['status'] === 'Maintenance'): ?>


                            <div
                                style="
                                    background:#f8d7da;
                                    color:#842029;
                                    padding:12px;
                                    border-radius:8px;
                                    width:100%;
                                "
                            >

                                This item is currently under maintenance.

                            </div>


                        <?php elseif ($item['status'] === 'Lost'): ?>


                            <div
                                style="
                                    background:#f8d7da;
                                    color:#842029;
                                    padding:12px;
                                    border-radius:8px;
                                    width:100%;
                                "
                            >

                                This item has been marked as lost.

                            </div>


                        <?php endif; ?>


                        <a
                            href="scan.php"
                            class="btn"
                            style="
                                background:#e9ecef;
                                color:#333;
                                text-decoration:none;
                            "
                        >
                            Scan Another
                        </a>


                    </div>


                </div>


            <?php endif; ?>


        </div>


    </main>

</div>


</body>

</html>
