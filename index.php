<?php
require_once 'includes/db.php';
check_login();

// Fetch Products (query unchanged)
$products = $conn->query("SELECT * FROM products");

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

    // Insert Into Bills Table (prepared statement — same columns/values as before)
    $stmt = $conn->prepare("
        INSERT INTO bills
        (bill_number, customer_name, subtotal, gst_amount, final_amount, bill_date)
        VALUES
        (?, ?, ?, ?, ?, CURDATE())
    ");
    $stmt->bind_param("ssddd", $bill_number, $customer_name, $subtotal, $gst_amount, $final_amount);
    $stmt->execute();

    $bill_id = $conn->insert_id;
    $stmt->close();

    // Insert Bill Items (prepared statement — same columns/values as before)
    $item_stmt = $conn->prepare("
        INSERT INTO bill_items
        (bill_id, product_id, product_name, quantity, price, gst_amount, total_amount)
        VALUES
        (?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($items as $item) {
        $item_stmt->bind_param(
            "iisiddd",
            $bill_id,
            $item['id'],
            $item['name'],
            $item['quantity'],
            $item['price'],
            $item['gst'],
            $item['total']
        );
        $item_stmt->execute();
    }
    $item_stmt->close();

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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Bill - <?php echo APP_NAME; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="d-flex">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="dash-welcome mb-4">
                    <div>
                        <span class="dash-eyebrow">Billing · New Entry</span>
                        <h2 class="dash-title"><i class="fas fa-file-invoice me-2"></i>Create Bill</h2>
                        <p class="dash-subtitle">Add products, review the totals, and save the bill.</p>
                    </div>
                </div>

                <form method="POST" id="billForm">
                    <div class="row g-4">

                        <!-- Left column: customer + product selection -->
                        <div class="col-lg-5">
                            <div class="dash-card mb-4">
                                <div class="dash-card-head">
                                    <h5><i class="fas fa-user me-2"></i>Customer</h5>
                                </div>
                                <div class="dash-card-body">
                                    <div class="form-field-light mb-1">
                                        <i class="fas fa-user field-icon-light"></i>
                                        <input type="text" name="customer_name" id="customer_name" placeholder=" " required>
                                        <label for="customer_name">Customer Name</label>
                                    </div>
                                </div>
                            </div>

                            <div class="dash-card">
                                <div class="dash-card-head">
                                    <h5><i class="fas fa-box-open me-2"></i>Add Product</h5>
                                </div>
                                <div class="dash-card-body">
                                    <label class="form-label-light">Select a product</label>
                                    <select id="product_id" class="form-select form-select-light mb-3">
                                        <option value="">-- Select Product --</option>
                                        <?php while ($p = $products->fetch_assoc()) { ?>
                                            <option
                                                value="<?= $p['id'] ?>"
                                                data-price="<?= $p['price'] ?>">
                                                <?= $p['product_name'] ?> — <?php echo format_currency($p['price']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>

                                    <button type="button" onclick="kbAddItem()" class="btn-billing-add w-100">
                                        <i class="fas fa-plus me-2"></i>Add Item to Bill
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Right column: bill items + totals -->
                        <div class="col-lg-7">
                            <div class="dash-card mb-4">
                                <div class="dash-card-head">
                                    <h5><i class="fas fa-receipt me-2"></i>Bill Items</h5>
                                </div>
                                <div class="dash-card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover dash-table billing-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Sr</th>
                                                    <th>Item</th>
                                                    <th>Qty</th>
                                                    <th class="text-end">Price</th>
                                                    <th class="text-end">GST</th>
                                                    <th class="text-end">Total</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="billTableBody">
                                                <tr id="billEmptyRow">
                                                    <td colspan="7">
                                                        <div class="dash-empty py-4">
                                                            <i class="fas fa-cart-plus"></i>
                                                            <p>No items added yet</p>
                                                            <span>Select a product on the left to get started</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-4">
                                    <div class="stat-card stat-glass billing-total-card">
                                        <div class="stat-label">Subtotal</div>
                                        <div class="stat-number billing-total-num">₹<span id="subtotal">0.00</span></div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-card stat-glass billing-total-card">
                                        <div class="stat-label">Total GST</div>
                                        <div class="stat-number billing-total-num">₹<span id="total_gst">0.00</span></div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-card stat-teal billing-total-card">
                                        <div class="stat-label">Final Amount</div>
                                        <div class="stat-number billing-total-num">₹<span id="final_amount">0.00</span></div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="bill_items" id="bill_items">

                            <button type="submit" name="save_bill" id="saveBillBtn" class="btn-dash-cta w-100 justify-content-center">
                                <i class="fas fa-save me-2"></i>Save Bill
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Namespaced with "kb" (Kirana Billing) prefix so these never collide
        // with the shared functions already defined in script.js.
        let kbBillItems = [];
        let kbSrNo = 1;
        const KB_GST_RATE = <?php echo GST_RATE; ?>;

        function kbAddItem() {
            const prodSelect = document.getElementById('product_id');
            const prodId = prodSelect.value;
            const prodName = prodSelect.options[prodSelect.selectedIndex].text.split(' — ')[0];
            const prodPrice = prodSelect.options[prodSelect.selectedIndex].getAttribute('data-price');

            if (!prodId) {
                Swal.fire('Select a product', 'Please choose a product before adding it to the bill.', 'warning');
                return;
            }

            const qty = 1;
            const gst = (prodPrice * qty * KB_GST_RATE) / 100;
            const total = (prodPrice * qty) + gst;

            kbBillItems.push({
                sr: kbSrNo++,
                id: prodId,
                name: prodName,
                price: parseFloat(prodPrice),
                quantity: qty,
                gst: gst,
                total: total
            });

            kbRenderTable();
            prodSelect.value = "";
        }

        function kbUpdateQuantity(index, change) {
            kbBillItems[index].quantity += change;
            if (kbBillItems[index].quantity < 1) {
                kbBillItems[index].quantity = 1;
            }
            const price = kbBillItems[index].price;
            const qty = kbBillItems[index].quantity;
            kbBillItems[index].gst = (price * qty * KB_GST_RATE) / 100;
            kbBillItems[index].total = (price * qty) + kbBillItems[index].gst;
            kbRenderTable();
        }

        function kbRemoveItem(index) {
            kbBillItems.splice(index, 1);
            kbRenderTable();
        }

        function kbRenderTable() {
            const tableBody = document.getElementById('billTableBody');
            tableBody.innerHTML = "";

            if (kbBillItems.length === 0) {
                tableBody.innerHTML = `
                    <tr id="billEmptyRow">
                        <td colspan="7">
                            <div class="dash-empty py-4">
                                <i class="fas fa-cart-plus"></i>
                                <p>No items added yet</p>
                                <span>Select a product on the left to get started</span>
                            </div>
                        </td>
                    </tr>`;
            }

            let subtotal = 0, totalGst = 0, finalAmount = 0;

            kbBillItems.forEach((item, index) => {
                subtotal += item.price * item.quantity;
                totalGst += item.gst;
                finalAmount += item.total;

                tableBody.innerHTML += `
                <tr>
                    <td>${item.sr}</td>
                    <td>${item.name}</td>
                    <td>
                        <button type="button" class="qty-btn" onclick="kbUpdateQuantity(${index}, -1)">-</button>
                        <span class="mx-2 fw-bold">${item.quantity}</span>
                        <button type="button" class="qty-btn" onclick="kbUpdateQuantity(${index}, 1)">+</button>
                    </td>
                    <td class="text-end billing-mono">${item.price.toFixed(2)}</td>
                    <td class="text-end billing-mono">${item.gst.toFixed(2)}</td>
                    <td class="text-end billing-mono">${item.total.toFixed(2)}</td>
                    <td class="text-end">
                        <button type="button" class="btn-row-remove" onclick="kbRemoveItem(${index})" title="Remove item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
            });

            document.getElementById('subtotal').innerText = subtotal.toFixed(2);
            document.getElementById('total_gst').innerText = totalGst.toFixed(2);
            document.getElementById('final_amount').innerText = finalAmount.toFixed(2);
            document.getElementById('bill_items').value = JSON.stringify(kbBillItems);
        }

        document.getElementById('billForm').addEventListener('submit', function (e) {
            if (kbBillItems.length === 0) {
                e.preventDefault();
                Swal.fire('No items in bill', 'Add at least one product before saving the bill.', 'error');
            }
        });
    </script>
</body>
</html>