<?php

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "billing_system";

// Database Connection
$con = new mysqli($servername, $username, $password, $dbname);

// Check Connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Check Bill ID
if(!isset($_GET['bill_id'])){
    die("Invalid Bill!");
}

$bill_id = $_GET['bill_id'];

// Fetch Bill Details
$bill = $con->query("
    SELECT * FROM bills 
    WHERE id = '$bill_id'
")->fetch_assoc();

// Fetch Bill Items
$items = $con->query("
    SELECT bi.*, p.product_name
    FROM bill_items bi
    JOIN products p ON bi.product_id = p.id
    WHERE bi.bill_id = '$bill_id'
");

?>

<!DOCTYPE html>
<html>

<head>

    <title>Print Bill</title>

    <style>

        body{
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        h2,h3{
            margin: 5px 0;
        }

        table{
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td{
            border: 1px solid #000;
        }

        th, td{
            padding: 10px;
            text-align: center;
        }

        .total-section{
            margin-top: 20px;
            text-align: right;
        }

    </style>

</head>

<body onload="window.print()">

    <h2 style="text-align:center;">
        Kirana Store Bill
    </h2>

    <hr>

    <p>
        <b>Bill No:</b>
        <?= $bill['bill_number'] ?>
    </p>

    <p>
        <b>Customer Name:</b>
        <?= $bill['customer_name'] ?>
    </p>

    <p>
        <b>Date:</b>
        <?= date('d-m-Y', strtotime($bill['bill_date'])) ?>
    </p>

    <table>

        <thead>

            <tr>
                <th>Sr No</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>GST</th>
                <th>Total</th>
            </tr>

        </thead>

        <tbody>

            <?php
            $sr = 1;

            while($row = $items->fetch_assoc()){
            ?>

            <tr>

                <td><?= $sr++ ?></td>

                <td><?= $row['product_name'] ?></td>

                <td><?= $row['quantity'] ?></td>

                <td><?= number_format($row['price'],2) ?></td>

                <td><?= number_format($row['gst_amount'],2) ?></td>

                <td><?= number_format($row['total_amount'],2) ?></td>

            </tr>

            <?php } ?>

        </tbody>

    </table>

    <div class="total-section">

        <h3>
            Total Amount:
            ₹<?= number_format($bill['subtotal'],2) ?>
        </h3>

        <h3>
            Total GST:
            ₹<?= number_format($bill['gst_amount'],2) ?>
        </h3>

        <h2>
            Final Amount:
            ₹<?= number_format($bill['final_amount'],2) ?>
        </h2>

    </div>

</body>
</html>