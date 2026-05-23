<?php
require_once 'includes/db.php';
check_login();

// Get dashboard statistics
$stats_query = "SELECT * FROM dashboard_stats";
$stats = $conn->query($stats_query)->fetch_assoc();

// Get low stock products
$low_stock_query = "SELECT * FROM low_stock_view LIMIT 5";
$low_stock_products = $conn->query($low_stock_query);

// Get recent bills
$recent_bills_query = "SELECT * FROM bills ORDER BY id DESC LIMIT 5";
$recent_bills = $conn->query($recent_bills_query);

// Get monthly sales data for chart
$monthly_sales_query = "SELECT DATE_FORMAT(bill_date, '%M') as month, SUM(final_amount) as sales 
                        FROM bills 
                        WHERE YEAR(bill_date) = YEAR(CURDATE())
                        GROUP BY MONTH(bill_date)
                        ORDER BY MONTH(bill_date)";
$monthly_sales = $conn->query($monthly_sales_query);
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
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="d-flex">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
                        <p class="text-muted">Welcome back, <?php echo get_admin_name(); ?>!</p>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <div class="stats-icon primary">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="stats-number"><?php echo format_currency($stats['total_sales']); ?></div>
                                <div class="stats-label">Total Sales</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <div class="stats-icon success">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="stats-number"><?php echo format_currency($stats['today_sales']); ?></div>
                                <div class="stats-label">Today's Sales</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <div class="stats-icon warning">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="stats-number"><?php echo $stats['total_products']; ?></div>
                                <div class="stats-label">Total Products</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <div class="stats-icon info">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stats-number"><?php echo $stats['total_customers']; ?></div>
                                <div class="stats-label">Total Customers</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-lg-8 mb-4">
                        <div class="chart-container">
                            <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i>Monthly Sales</h5>
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header bg-gradient-primary text-white">
                                <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
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
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Bills -->
                <div class="row">
                    <div class="col-12">
                        <div class="card table-custom">
                            <div class="card-header bg-gradient-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Recent Bills</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
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
                                                       class="btn btn-sm btn-primary" target="_blank">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
   <?php include 'includes/footer.php'; ?>
    
    <script>
    // Sales Chart
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
                borderColor: 'rgb(102, 126, 234)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
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
    </script>
</body>
</html>