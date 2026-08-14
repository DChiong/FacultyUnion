    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    
   <style>
        :root { --maroon: #8c1d1d; --gold: #d4af37; }
        body { background: #f4f7f6; overflow-x: hidden; }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: var(--maroon);
            color: #fff;
            padding: 24px 0;
            overflow-y: auto;
            box-shadow: 0 0 24px rgba(0, 0, 0, 0.12);
        }
        .sidebar .brand {
            padding: 0 24px 18px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            margin-bottom: 8px;
        }
        .sidebar .brand img {
            max-height: 100px;
            margin-bottom: 12px;
        }
        .sidebar .brand h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 12px 24px;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: var(--gold);
        }
        .sidebar .nav-link i {
            width: 24px;
            font-size: 1.1rem;
            text-align: center;
            margin-right: 12px;
            opacity: 0.8;
        }
        .sidebar .nav-link.active i {
            opacity: 1;
            color: var(--gold);
        }
        .main-content { margin-left: 260px; padding: 40px; }
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1040;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
    <div class="sidebar" id="sidebar">
    <div class="brand">
        <img src="../images/facultyunion.png" alt="WMSU Faculty Union logo">
        <h5>WMSU-FU</h5>
        <small>Admin Panel</small>
    </div>
    <a href="dashboard.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? ' active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a>
    <a href="manage_site.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_site.php' ? ' active' : ''; ?>"><i class="fas fa-image"></i> Manage Logo &amp; Title</a>

    <a href="manage_officers.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_officers.php' ? ' active' : ''; ?>"><i class="fas fa-users"></i> Manage Officers</a>

    <a href="manage_about_topics.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_about_topics.php' ? ' active' : ''; ?>"><i class="fas fa-align-left"></i> Manage About Content</a>
    <a href="manage_contact.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_contact.php' ? ' active' : ''; ?>"><i class="fas fa-address-book"></i> Manage Contact</a>
    <a href="manage_events.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_events.php' ? ' active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Manage Events</a>
    <a href="manage_awards.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_awards.php' ? ' active' : ''; ?>"><i class="fas fa-award"></i> Manage Awards</a>
    <a href="manage_videos.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_videos.php' ? ' active' : ''; ?>"><i class="fas fa-play-circle"></i> Manage Videos</a>
    <a href="manage_pages.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_pages.php' ? ' active' : ''; ?>"><i class="fas fa-file-alt"></i> Manage Pages</a>

    <a href="manage_posts.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_posts.php' ? ' active' : ''; ?>"><i class="fas fa-edit"></i> Manage Posts</a>
   
</div>
