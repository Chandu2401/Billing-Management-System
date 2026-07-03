<?php
require_once 'includes/db.php';
check_login();

// Get dashboard statistics
$stats_query = "SELECT * FROM dashboard_stats";
$stats = $conn->query($stats_query)->fetch_assoc();

// Get low stock products
$low_stock_query = "SELECT * FROM low_stock_view LIMIT 5";
$low_stock_products = $conn->query($low_stock_query);
$has_low_stock = $low_stock_products->num_rows > 0;

// Get recent bills
$recent_bills_query = "SELECT * FROM bills ORDER BY id DESC LIMIT 5";
$recent_bills = $conn->query($recent_bills_query);
$has_recent_bills = $recent_bills->num_rows > 0;

// Get monthly sales data for chart
$monthly_sales_query = "SELECT DATE_FORMAT(bill_date, '%M') as month, SUM(final_amount) as sales 
                        FROM bills 
                        WHERE YEAR(bill_date) = YEAR(CURDATE())
                        GROUP BY MONTH(bill_date)
                        ORDER BY MONTH(bill_date)";
$monthly_sales = $conn->query($monthly_sales_query);
$has_sales_data = $monthly_sales->num_rows > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="d-flex">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="container-fluid">
                <!-- Welcome Section -->
                <div class="dash-welcome mb-4">
                    <div>
                        <span class="dash-eyebrow">Dashboard · <?php echo date('l, d M Y'); ?></span>
                        <h2 class="dash-title"><i class="fas fa-tachometer-alt me-2"></i>Welcome back, <?php echo get_admin_name(); ?></h2>
                        <p class="dash-subtitle">Here's how the store is doing today.</p>
                    </div>
                    <a href="index.php" class="btn-dash-cta">
                        <i class="fas fa-plus me-2"></i>Create New Bill
                    </a>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stat-card stat-teal">
                            <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                            <div class="stat-number"><?php echo format_currency($stats['total_sales']); ?></div>
                            <div class="stat-label">Total Sales</div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stat-card stat-amber">
                            <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                            <div class="stat-number"><?php echo format_currency($stats['today_sales']); ?></div>
                            <div class="stat-label">Today's Sales</div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stat-card stat-ink">
                            <div class="stat-icon"><i class="fas fa-box"></i></div>
                            <div class="stat-number"><?php echo $stats['total_products']; ?></div>
                            <div class="stat-label">Total Products</div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stat-card stat-glass">
                            <div class="stat-icon"><i class="fas fa-users"></i></div>
                            <div class="stat-number"><?php echo $stats['total_customers']; ?></div>
                            <div class="stat-label">Total Customers</div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-lg-8 mb-4">
                        <div class="dash-card chart-card">
                            <div class="dash-card-head">
                                <h5><i class="fas fa-chart-line me-2"></i>Monthly Sales</h5>
                            </div>
                            <div class="dash-card-body">
                                <?php if ($has_sales_data): ?>
                                    <!-- Chart.js renders here from $monthly_sales (query unchanged) -->
                                    <canvas id="salesChart"></canvas>
                                <?php else: ?>
                                    <!--
                                        Chart.js placeholder — $monthly_sales returned 0 rows for the
                                        current year. Once bills exist, this block is skipped and the
                                        <canvas> above renders automatically; no code change needed.
                                    -->
                                    <div class="chart-empty">
                                        <i class="fas fa-chart-line"></i>
                                        <p>No sales recorded yet this year</p>
                                        <span>The chart will appear automatically once bills come in</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 mb-4">
                        <div class="dash-card">
                            <div class="dash-card-head dash-card-head-alert">
                                <h5><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert</h5>
                            </div>
                            <div class="dash-card-body p-0">
                                <?php if ($has_low_stock): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover dash-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($product = $low_stock_products->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <?php echo $product['product_name']; ?>
                                                    <br>
                                                    <small class="text-muted"><?php echo $product['category_name']; ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger"><?php echo $product['stock_quantity']; ?></span>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                    <div class="dash-empty">
                                        <i class="fas fa-check-circle"></i>
                                        <p>All products are well stocked</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Bills -->
                <div class="row">
                    <div class="col-12">
                        <div class="dash-card">
                            <div class="dash-card-head">
                                <h5><i class="fas fa-receipt me-2"></i>Recent Bills</h5>
                                <a href="bill_history.php" class="dash-card-link">View all <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                            <div class="dash-card-body p-0">
                                <?php if ($has_recent_bills): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover dash-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Bill No</th>
                                                <th>Customer</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Payment</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($bill = $recent_bills->fetch_assoc()): ?>
                                            <tr>
                                                <td><strong><?php echo $bill['bill_number']; ?></strong></td>
                                                <td>
                                                    <?php echo $bill['customer_name']; ?>
                                                    <br>
                                                    <small class="text-muted"><?php echo $bill['customer_phone']; ?></small>
                                                </td>
                                                <td><?php echo date('d M Y', strtotime($bill['bill_date'])); ?></td>
                                                <td><strong><?php echo format_currency($bill['final_amount']); ?></strong></td>
                                                <td>
                                                    <span class="badge bg-success"><?php echo $bill['payment_method']; ?></span>
                                                </td>
                                                <td>
                                                    <a href="print_bill.php?bill_id=<?php echo $bill['id']; ?>" 
                                                       class="btn-table-action" target="_blank" title="Print bill">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                    <div class="dash-empty">
                                        <i class="fas fa-receipt"></i>
                                        <p>No bills yet</p>
                                        <span>Create your first bill to see it show up here</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
   <?php include 'includes/footer.php'; ?>
    
    <script>
    <?php if ($has_sales_data): ?>
    // Sales Chart — data straight from $monthly_sales (query unchanged)
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                <?php 
                $monthly_sales->data_seek(0);
                while($row = $monthly_sales->fetch_assoc()) {
                    echo "'" . $row['month'] . "',";
                }
                ?>
            ],
            datasets: [{
                label: 'Monthly Sales (₹)',
                data: [
                    <?php 
                    $monthly_sales->data_seek(0);
                    while($row = $monthly_sales->fetch_assoc()) {
                        echo $row['sales'] . ",";
                    }
                    ?>
                ],
                borderColor: '#178C79',
                backgroundColor: 'rgba(23, 140, 121, 0.12)',
                pointBackgroundColor: '#E8A33D',
                pointBorderColor: '#fff',
                pointRadius: 4,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    <?php endif; ?>
    </script>
</body>
</html>