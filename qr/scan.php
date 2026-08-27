<?php

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

require_once __DIR__ . '/../config/database.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Scan QR Code - Inventory System</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    <!-- QR Scanner Library -->
    <script
        src="https://unpkg.com/html5-qrcode"
        type="text/javascript"
    ></script>

    <style>

        .scanner-page {
            max-width: 700px;
            margin: 0 auto;
        }

        .scanner-card {
            background: #ffffff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        #reader {
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
        }

        .scanner-info {
            text-align: center;
            color: #66736c;
            margin-bottom: 20px;
        }

        .manual-section {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
        }

        .manual-section h3 {
            margin-bottom: 8px;
        }

        .scan-result {
            display: none;
            margin-top: 20px;
            padding: 15px;
            border-radius: 10px;
            background: #e8f5e9;
            color: #146c43;
            text-align: center;
        }

        .btn {
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        @media (max-width: 600px) {

            .scanner-card {
                padding: 15px;
            }

            #reader {
                width: 100%;
            }

        }

    </style>

</head>


<body>


<div class="app-layout">

    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>


    <main class="main-content">


        <div class="scanner-page">


            <div class="page-header">

                <div>

                    <h1>Scan QR Code</h1>

                    <p>
                        Scan an inventory item's QR code using your phone camera.
                    </p>

                </div>

            </div>


            <div class="scanner-card">


                <div class="scanner-info">

                    <strong>
                        Point your camera at the item's QR code.
                    </strong>

                    <br>

                    The system will automatically identify the item.

                </div>


                <!-- CAMERA SCANNER -->

                <div id="reader"></div>


                <!-- RESULT -->

                <div
                    id="scanResult"
                    class="scan-result"
                >

                    QR Code detected.

                    <br>

                    Opening item...

                </div>


                <!-- MANUAL FALLBACK -->

                <div class="manual-section">

                    <h3>
                        Can't use the camera?
                    </h3>

                    <p>
                        Enter the item code printed below the QR code.
                    </p>


                    <form
                        method="GET"
                        action="item.php"
                    >

                        <div class="form-group">

                            <input
                                type="text"
                                name="code"
                                class="form-control"
                                placeholder="Example: IT-001"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Find Item
                        </button>

                    </form>

                </div>


            </div>


        </div>


    </main>

</div>


<script>

    let scannerStarted = false;


    function onScanSuccess(decodedText, decodedResult) {

        if (scannerStarted) {

            scannerStarted = false;

            document.getElementById(
                "scanResult"
            ).style.display = "block";


            /*
             * The QR currently stores the item code.
             *
             * Example:
             *
             * IT-001
             */

            const code = encodeURIComponent(
                decodedText.trim()
            );


            /*
             * Stop camera before leaving page.
             */

            try {

                html5QrcodeScanner.clear();

            } catch (error) {

                console.log(error);

            }


            window.location.href =
                "item.php?code=" + code;

        }

    }


    function onScanFailure(error) {

        /*
         * Ignore normal scanning failures.
         *
         * The scanner continuously tries
         * until a valid QR code is detected.
         */

    }


    const html5QrcodeScanner =
        new Html5QrcodeScanner(

            "reader",

            {
                fps: 10,

                qrbox: {
                    width: 250,
                    height: 250
                },

                rememberLastUsedCamera: true

            },

            false

        );


    html5QrcodeScanner.render(
        onScanSuccess,
        onScanFailure
    );


    scannerStarted = true;

</script>


</body>

</html>
