<div class="sidebar bg-dark" id="sidebar">
    <div class="sidebar-header p-3 text-white">
        <h5><i class="fas fa-bars me-2"></i>Menu</h5>
    </div>
    <ul class="sidebar-menu list-unstyled">
        <li>
            <a href="dashboard.php" class="sidebar-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="create_bill.php" class="sidebar-link">
                <i class="fas fa-file-invoice"></i>
                <span>Create Bill</span>
            </a>
        </li>
        <li>
            <a href="bill_history.php" class="sidebar-link">
                <i class="fas fa-history"></i>
                <span>Bill History</span>
            </a>
        </li>
        <li class="sidebar-dropdown">
            <a href="#productSubmenu" data-bs-toggle="collapse" class="sidebar-link">
                <i class="fas fa-box"></i>
                <span>Products</span>
                <i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul class="collapse list-unstyled" id="productSubmenu">
                <li><a href="add_product.php" class="sidebar-sublink">Add Product</a></li>
                <li><a href="manage_products.php" class="sidebar-sublink">Manage Products</a></li>
                <li><a href="categories.php" class="sidebar-sublink">Categories</a></li>
            </ul>
        </li>
        <li>
            <a href="customers.php" class="sidebar-link">
                <i class="fas fa-users"></i>
                <span>Customers</span>
            </a>
        </li>
        <li class="sidebar-dropdown">
            <a href="#reportSubmenu" data-bs-toggle="collapse" class="sidebar-link">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
                <i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul class="collapse list-unstyled" id="reportSubmenu">
                <li><a href="daily_report.php" class="sidebar-sublink">Daily Sales</a></li>
                <li><a href="monthly_report.php" class="sidebar-sublink">Monthly Sales</a></li>
                <li><a href="inventory_report.php" class="sidebar-sublink">Inventory Report</a></li>
            </ul>
        </li>
        <li>
            <a href="settings.php" class="sidebar-link">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>
</div>

<script>
// Toggle sidebar on mobile
document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
});
</script>