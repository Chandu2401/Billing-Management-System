<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "billing_system";

// Database Connection
$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

define('GST_RATE', 5);

// Fetch Products
$products = $con->query("SELECT * FROM products");

// Save Bill
if (isset($_POST['save_bill'])) {

    $customer_name = $_POST['customer_name'];
    $items = json_decode($_POST['bill_items'], true);

    $subtotal = 0;
    $gst_amount = 0;
    $final_amount = 0;

    foreach ($items as $item) {

        $subtotal += $item['price'] * $item['quantity'];
        $gst_amount += $item['gst'];
        $final_amount += $item['total'];
    }

    // Generate Bill Number
    $bill_number = "BILL-" . rand(1000, 9999);

    // Insert Into Bills Table
    $con->query("
        INSERT INTO bills 
        (bill_number, customer_name, subtotal, gst_amount, final_amount, bill_date) 
        
        VALUES 
        ('$bill_number', '$customer_name', '$subtotal', '$gst_amount', '$final_amount', CURDATE())
    ");

    $bill_id = $con->insert_id;

    // Insert Bill Items
    foreach ($items as $item) {

        $con->query("
            INSERT INTO bill_items
            (bill_id, product_id, product_name, quantity, price, gst_amount, total_amount)

            VALUES
            (
                '$bill_id',
                '".$item['id']."',
                '".$item['name']."',
                '".$item['quantity']."',
                '".$item['price']."',
                '".$item['gst']."',
                '".$item['total']."'
            )
        ");
    }

    echo "
    <script>
        alert('Bill Saved Successfully!');
        window.location='print_bill.php?bill_id=$bill_id';
    </script>
    ";

    exit;
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Kirana Billing System</title>

    <link rel="stylesheet" href="style.css">

    <script>

        let billItems = [];
        let srNo = 1;

        const GST_RATE = 5;

        // Add Item
        function addItem() {

            let prodSelect = document.getElementById('product_id');

            let prodId =
                prodSelect.value;

            let prodName =
                prodSelect.options[prodSelect.selectedIndex].text;

            let prodPrice =
                prodSelect.options[prodSelect.selectedIndex]
                .getAttribute('data-price');

            let qty = 1;

            if (!prodId) {
                alert("Please Select Product");
                return;
            }

            let gst =
                (prodPrice * qty * GST_RATE) / 100;

            let total =
                (prodPrice * qty) + gst;

            billItems.push({

                sr: srNo,
                id: prodId,
                name: prodName,
                price: parseFloat(prodPrice),
                quantity: qty,
                gst: gst,
                total: total
            });

            srNo++;

            renderTable();
        }

        // Update Quantity
        function updateQuantity(index, change) {

            billItems[index].quantity += change;

            if (billItems[index].quantity < 1) {
                billItems[index].quantity = 1;
            }

            let price = billItems[index].price;
            let qty = billItems[index].quantity;

            billItems[index].gst =
                (price * qty * GST_RATE) / 100;

            billItems[index].total =
                (price * qty) + billItems[index].gst;

            renderTable();
        }

        // Render Table
        function renderTable() {

            let tableBody =
                document.getElementById('billTableBody');

            tableBody.innerHTML = "";

            let subtotal = 0;
            let totalGst = 0;
            let finalAmount = 0;

            billItems.forEach((item, index) => {

                subtotal += item.price * item.quantity;
                totalGst += item.gst;
                finalAmount += item.total;

                tableBody.innerHTML += `

                <tr>

                    <td>${item.sr}</td>

                    <td>${item.name}</td>

                    <td>

                        <button 
                            type="button" 
                            class="qty-btn"
                            onclick="updateQuantity(${index}, -1)">
                            -
                        </button>

                        ${item.quantity}

                        <button 
                            type="button"
                            class="qty-btn"
                            onclick="updateQuantity(${index}, 1)">
                            +
                        </button>

                    </td>

                    <td>${item.price}</td>

                    <td>${item.gst.toFixed(2)}</td>

                    <td>${item.total.toFixed(2)}</td>

                </tr>
                `;
            });

            document.getElementById('subtotal')
                .innerText = subtotal.toFixed(2);

            document.getElementById('total_gst')
                .innerText = totalGst.toFixed(2);

            document.getElementById('final_amount')
                .innerText = finalAmount.toFixed(2);

            document.getElementById('bill_items').value =
                JSON.stringify(billItems);
        }

    </script>

</head>

<body>

<div class="container">

    <h2>Kirana Billing System</h2>

    <form method="POST">

        <label>Customer Name:</label>

        <input type="text" name="customer_name" required>

        <br><br>

        <label>Products:</label>

        <select id="product_id">

            <option value="">--Select Product--</option>

            <?php while($p = $products->fetch_assoc()) { ?>

                <option
                    value="<?= $p['id'] ?>"
                    data-price="<?= $p['price'] ?>">

                    <?= $p['product_name'] ?>

                </option>

            <?php } ?>

        </select>

        <button
            type="button"
            onclick="addItem()"
            class="btn">

            Add Item

        </button>

        <h3>Bill Items</h3>

        <table>

            <thead>

                <tr>
                    <th>Sr No</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>GST</th>
                    <th>Total Amount</th>
                </tr>

            </thead>

            <tbody id="billTableBody"></tbody>

        </table>

        <p>
            Total Amount:
            <span id="subtotal">0</span>
        </p>

        <p>
            Total GST:
            <span id="total_gst">0</span>
        </p>

        <p>
            Final Amount:
            <span id="final_amount">0</span>
        </p>

        <input
            type="hidden"
            name="bill_items"
            id="bill_items">

        <button
            type="submit"
            name="save_bill"
            class="btn-submit">

            Save Bill

        </button>

    </form>

</div>

</body>
</html>