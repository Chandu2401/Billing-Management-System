<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$admin_name = get_admin_name();
$admin_initials = strtoupper(substr($admin_name, 0, 1));
?>
<nav class="topbar sticky-top">
    <div class="topbar-inner">
        <button class="icon-btn topbar-toggle" id="sidebarToggle" type="button" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <a class="topbar-brand" href="dashboard.php">
            <span class="topbar-brand-icon"><i class="fas fa-receipt"></i></span>
            <span class="topbar-brand-text d-none d-md-inline"><?php echo APP_NAME; ?></span>
        </a>

        <div class="topbar-search d-none d-lg-flex">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search bills, products, customers…" aria-label="Search">
        </div>

        <div class="topbar-actions">
            <div class="dropdown">
                <button class="icon-btn" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="icon-btn-dot"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end notif-menu" aria-labelledby="notifDropdown">
                    <li class="notif-header">Notifications</li>
                    <li>
                        <a class="dropdown-item notif-item" href="#">
                            <i class="fas fa-box text-warning"></i>
                            <div>
                                <div class="notif-title">Low stock alert</div>
                                <div class="notif-sub">Some products are running low</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item notif-item" href="#">
                            <i class="fas fa-file-invoice text-info"></i>
                            <div>
                                <div class="notif-title">New bill generated</div>
                                <div class="notif-sub">Check recent activity on the dashboard</div>
                            </div>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="notif-footer text-center">
                        <small class="text-muted">UI preview — not wired to live data yet</small>
                    </li>
                </ul>
            </div>

            <div class="dropdown">
                <button class="profile-pill" type="button" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="profile-avatar"><?php echo $admin_initials; ?></span>
                    <span class="d-none d-md-inline profile-name"><?php echo $admin_name; ?></span>
                    <i class="fas fa-chevron-down profile-caret"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>