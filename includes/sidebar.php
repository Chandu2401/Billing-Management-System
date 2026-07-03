<?php
// Used to highlight the current page's link — purely presentational, reads
// nothing user-supplied.
$current_page = basename($_SERVER['PHP_SELF']);
function nav_active($page, $current_page) {
    return $page === $current_page ? 'active' : '';
}
$product_pages = ['add_product.php', 'manage_products.php', 'categories.php'];
$report_pages  = ['daily_report.php', 'monthly_report.php', 'inventory_report.php'];
$products_open = in_array($current_page, $product_pages) ? 'show' : '';
$reports_open  = in_array($current_page, $report_pages) ? 'show' : '';
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <span class="sidebar-header-icon"><i class="fas fa-book"></i></span>
        <span class="sidebar-header-text">Menu</span>
    </div>

    <ul class="sidebar-menu list-unstyled">
        <li>
            <a href="dashboard.php" class="sidebar-link <?php echo nav_active('dashboard.php', $current_page); ?>" title="Dashboard">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="index.php" class="sidebar-link <?php echo nav_active('index.php', $current_page); ?>" title="Create Bill">
                <i class="fas fa-file-invoice"></i>
                <span>Create Bill</span>
            </a>
        </li>
        <li>
            <a href="bill_history.php" class="sidebar-link <?php echo nav_active('bill_history.php', $current_page); ?>" title="Bill History">
                <i class="fas fa-history"></i>
                <span>Bill History</span>
            </a>
        </li>
        <li class="sidebar-dropdown">
            <a href="#productSubmenu" data-bs-toggle="collapse" class="sidebar-link <?php echo $products_open ? 'active' : ''; ?>" title="Products">
                <i class="fas fa-box"></i>
                <span>Products</span>
                <i class="fas fa-chevron-down ms-auto submenu-caret"></i>
            </a>
            <ul class="collapse list-unstyled <?php echo $products_open; ?>" id="productSubmenu">
                <li><a href="add_product.php" class="sidebar-sublink <?php echo nav_active('add_product.php', $current_page); ?>">Add Product</a></li>
                <li><a href="manage_products.php" class="sidebar-sublink <?php echo nav_active('manage_products.php', $current_page); ?>">Manage Products</a></li>
                <li><a href="categories.php" class="sidebar-sublink <?php echo nav_active('categories.php', $current_page); ?>">Categories</a></li>
            </ul>
        </li>
        <li>
            <a href="customers.php" class="sidebar-link <?php echo nav_active('customers.php', $current_page); ?>" title="Customers">
                <i class="fas fa-users"></i>
                <span>Customers</span>
            </a>
        </li>
        <li class="sidebar-dropdown">
            <a href="#reportSubmenu" data-bs-toggle="collapse" class="sidebar-link <?php echo $reports_open ? 'active' : ''; ?>" title="Reports">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
                <i class="fas fa-chevron-down ms-auto submenu-caret"></i>
            </a>
            <ul class="collapse list-unstyled <?php echo $reports_open; ?>" id="reportSubmenu">
                <li><a href="daily_report.php" class="sidebar-sublink <?php echo nav_active('daily_report.php', $current_page); ?>">Daily Sales</a></li>
                <li><a href="monthly_report.php" class="sidebar-sublink <?php echo nav_active('monthly_report.php', $current_page); ?>">Monthly Sales</a></li>
                <li><a href="inventory_report.php" class="sidebar-sublink <?php echo nav_active('inventory_report.php', $current_page); ?>">Inventory Report</a></li>
            </ul>
        </li>
        <li>
            <a href="settings.php" class="sidebar-link <?php echo nav_active('settings.php', $current_page); ?>" title="Settings">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
// Sidebar toggle: icon-only collapse on desktop, slide-over on mobile
(function () {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');

    function isMobile() {
        return window.innerWidth < 992;
    }

    toggleBtn?.addEventListener('click', function () {
        if (isMobile()) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('show');
        } else {
            sidebar.classList.toggle('collapsed');
            document.body.classList.toggle('sidebar-collapsed');
        }
    });

    overlay?.addEventListener('click', function () {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('show');
    });
})();
</script>