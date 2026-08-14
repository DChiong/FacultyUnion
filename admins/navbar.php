<link rel="stylesheet" href="../vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../vendor/bootstrap-icons/bootstrap-icons.css">
<link rel="icon" href="../images/facultyunion.png">

<style>
    .navbar-custom {
        position: sticky;
        top: 0;
        z-index: 1030;
        margin-left: 260px; /* align with sidebar width */
        background: #ffffff;
        border-bottom: 1px solid rgba(0,0,0,0.08);
    }
    .top-header-inner { padding: 12px 18px; }
    .title { margin: 0; }
    .navtext { font-weight: 700; font-size: 1.25rem; color: #3b0b0b; }
    .logout-link { color: #3b0b0b; text-decoration: none; }
    .logout-link:hover { color: var(--bs-red); }
    @media (max-width: 991.98px) {
        .navbar-custom { margin-left: 0; }
        .navtext { font-size: 1.05rem; }
    }
</style>

<div class="navbar-custom">
    <header>
        <div class="container-fluid top-header-inner d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <button id="menu-toggle" class="btn d-lg-none me-2">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="title">
                    <h1 class="navtext d-none d-lg-block m-0"><?php echo $navtext ?></h1>
                </div>
            </div>

            <div class="d-flex align-items-center">
                <a href="../auth/logout.php" class="logout-link d-flex align-items-center gap-2 px-3 py-2">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                    <span class="fw-semibold d-none d-lg-inline">Logout</span>
                </a>
            </div>
        </div>
    </header>
</div>
