<?php
/**
 * 1. Path to your class file
 */
require_once('config/database.php'); 
/**
 * 2. Create an instance and get the PDO connection
 */
$database = new Database();
$db = $database->getConnection(); 

if (!$db) {
    die("Database connection failed.");
}

// ==========================================
// A. FETCH ABOUT PAGE DATA
// ==========================================
try {
    // Fetch Vision
    $vision_stmt = $db->prepare("SELECT vision FROM union_info LIMIT 1");
    $vision_stmt->execute();
    $vision_data = $vision_stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch Objectives
    $objectives_stmt = $db->prepare("SELECT * FROM objectives ORDER BY sort_order ASC, id ASC");
    $objectives_stmt->execute();
    $objectives_result = $objectives_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Executive Officers
    $exec_stmt = $db->prepare("SELECT * FROM officers WHERE category = 'Executive' ORDER BY rank ASC");
    $exec_stmt->execute();
    $exec_rows = $exec_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Finance Officers
    $fin_stmt = $db->prepare("SELECT * FROM officers WHERE category = 'Finance' ORDER BY rank ASC");
    $fin_stmt->execute();
    $fin_rows = $fin_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Custom Topics
    $topics_stmt = $db->prepare("SELECT * FROM about_topics WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
    $topics_stmt->execute();
    $custom_topics = $topics_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

function resolveOfficerPhoto($path) {
    if (!empty($path)) {
        if (file_exists(__DIR__ . '/' . $path)) {
            return $path;
        }
        $mainDir = dirname(__DIR__) . '/faculty-union-main';
        if (file_exists($mainDir . '/' . $path)) {
            return '../faculty-union-main/' . $path;
        }
    }
    return 'images/facultyunion.png';
}

// ==========================================
// B. FETCH NAVBAR SITE SETTINGS
// ==========================================
$display_name = "Faculty Union";
$display_logo = "";
try {
    $settings_query = $db->query("SELECT site_name, logo_path FROM site_settings WHERE id = 1");
    $settings = $settings_query ? $settings_query->fetch(PDO::FETCH_ASSOC) : false;
    if (is_array($settings)) {
        $display_name = !empty($settings['site_name']) ? htmlspecialchars($settings['site_name']) : $display_name;
        $display_logo = !empty($settings['logo_path']) ? $settings['logo_path'] : $display_logo;
    }
} catch (PDOException $e) {}

// ==========================================
// C. FETCH FOOTER CONTACT INFO
// ==========================================
try {
    $contact_query = $db->query("SELECT * FROM contact_info WHERE id = 1");
    $contact = $contact_query ? $contact_query->fetch(PDO::FETCH_ASSOC) : false;
} catch (Exception $e) {
    $contact = false;
}
$address = $contact['address'] ?? '2nd Floor, Executive Bldg, WMSU Main Campus';
$phone = $contact['phone'] ?? '+63 62 991 1771';
$email = $contact['email'] ?? 'facultyunion@wmsu.edu.ph';
$fb_url = $contact['facebook_url'] ?? 'https://www.facebook.com/WMSUFacultyUnion';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>About - WMSU Faculty Union</title>
    
    <?php require_once('./includes/head.php'); ?>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">

    <style>
        /* =======================================
           1. GLOBAL & LAYOUT
           ======================================= */
        body { 
            background-color: #f8f9fa; 
            line-height: 1.8; 
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
            overflow-x: hidden;
        }

        body.sidebar-open {
            margin-left: 250px;
        }

        @media (max-width: 768px) {
            body.sidebar-open {
                margin-left: 0; 
            }
        }

        .main-wrapper {
            padding-top: 60px;
        }

        /* =======================================
           2. SIDEBAR NAVBAR
           ======================================= */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            background-color: #ffffff;
            border-right: 1px solid #eee;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }

        body.sidebar-open #sidebar {
            transform: translateX(0);
        }

        #sidebar .sidebar-brand {
            padding: 60px 20px 30px; 
            text-align: center;
            border-bottom: 1px solid #eee;
            text-decoration: none;
            display: block;
        }

        #sidebar .logo img {
            max-height: 80px;
            width: auto;
            margin-bottom: 10px;
        }

        #sidebar .sitename {
            color: #8c1d1d !important;
            font-weight: 700;
            font-size: 1.4rem;
            margin: 0;
        }

        #sidebar .nav-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
            width: 100%;
        }

        #sidebar .nav-link {
            color: #8c1d1d !important;
            font-weight: 600;
            padding: 12px 25px;
            display: block;
            text-decoration: none;
            transition: 0.3s;
        }

        #sidebar .nav-link:hover {
            color: #d4af37 !important;
            background-color: #fcf8f8;
            border-left: 4px solid #d4af37;
            padding-left: 21px; 
        }

        #sidebar .auth-buttons {
            padding: 20px;
            margin-top: auto;
            border-top: 1px solid #eee;
        }

        #sidebar .btn-logout {
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            display: block;
            text-align: center;
            text-decoration: none;
            width: 100%;
            transition: all 0.3s ease;
            background-color: #6c757d;
            color: #fff !important;
        }
        #sidebar .btn-logout:hover { background-color: #5a6268; }

        #sidebar-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background-color: #8c1d1d;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 8px 12px;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: background-color 0.3s ease;
        }
        #sidebar-toggle:hover {
            background-color: #d4af37;
            color: #000;
        }

        /* =======================================
           3. SMALLER HEADER STYLES
           ======================================= */
        .header-img-small { 
            position: relative; 
            overflow: hidden; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); 
            border-radius: 8px; 
            height: 110px !important; 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat;
            margin-bottom: 20px;
        }

        .header-overlay-small { 
            position: absolute; 
            top: 0;
            bottom: 0; 
            left: 0; 
            right: 0; 
            padding: 15px !important;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px; 
            background: linear-gradient(90deg, rgba(140, 29, 29, 0.98) 0%, rgba(140, 29, 29, 0.8) 60%, transparent 100%); 
            color: white; 
        }

        .header-logo-small {
            width: 60px !important;
            height: 60px !important;
            object-fit: contain;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.3));
            margin: 0 !important; 
        }

        .text-block-small {
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
        }

        .artistic-title-small { 
            font-family: 'Playfair Display', serif; 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            font-size: 1.5rem !important; 
            margin: 0 0 2px 0 !important;
            line-height: 1.2;
            color: #ffffff !important;
        }

        .subtitle-small {
            font-size: 0.85rem !important;
            font-weight: 400;
            margin: 0 !important;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .header-img-small { height: 100px !important; }
            .header-logo-small { width: 50px !important; height: 50px !important; }
            .artistic-title-small { font-size: 1.2rem !important; }
            .subtitle-small { font-size: 0.75rem !important; }
        }

        /* =======================================
           4. ABOUT PAGE CONTENT
           ======================================= */
        .section-title { 
            color: #8c1d1d; 
            margin-bottom: 30px; 
            font-weight: 700; 
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 1.7rem;
            position: relative;
            padding-bottom: 15px;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            height: 3px;
            width: 60px;
            background-color: #d4af37;
            border-radius: 2px;
        }

        .content-card { 
            background: white; 
            padding: 2.5rem; 
            margin-bottom: 3rem; 
            border-top: 5px solid #8c1d1d !important; 
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
            border-radius: 1rem;
            border: 0;
        }

        .inner-content-box { 
            background: linear-gradient(135deg, #fdfcf3 0%, #f9f5e3 100%); 
            position: relative; 
            z-index: 1; 
            border: 1px solid rgba(212, 175, 55, 0.3); 
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
            margin-top: 1.5rem;
        }

        .vision-container { 
            background-color: #fdfaf3; 
            border: 1px solid #e9d7a5; 
            padding: 25px; 
            text-align: center; 
            border-radius: 8px; 
        }

        .person-card { 
            background-color: #fcfcfc; 
            border-left: 3px solid #8c1d1d; 
            padding: 15px; 
            margin-bottom: 15px; 
            transition: 0.3s; 
            display: flex; 
            align-items: center; 
            gap: 15px; 
            border-radius: 4px;
        }
        .person-card:hover { 
            transform: translateX(5px); 
            background: #fff; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
        }

        .officer-avatar { 
            width: 70px; height: 70px; 
            border-radius: 50%; 
            overflow: hidden; 
            flex: 0 0 70px; 
            border: 2px solid #e9d7a5; 
            background: #f7f4ec; 
        }
        .officer-avatar img { width: 100%; height: 100%; object-fit: cover; }

        .person-details { min-width: 0; line-height: 1.4; font-size: 0.95rem; }
        .person-name { font-weight: bold; color: #8c1d1d; display: block; margin-bottom: 2px; }

        .team-header, .finance-header { 
            padding: 12px; 
            color: #fff; 
            margin-bottom: 20px; 
            text-align: center; 
            font-weight: bold; 
            text-transform: uppercase; 
            border-radius: 5px;
        }
        .team-header { background-color: #8c1d1d; }
        .finance-header { background-color: #8c1d1d; margin-top: 30px; }

        /* =======================================
           4.b. VISION & OBJECTIVES ENHANCEMENTS
           ======================================= */
        .vision-wrapper {
            display: flex;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 40px;
            position: relative;
        }
        .vision-left {
            background-color: #8c1d1d;
            background-image: linear-gradient(rgba(140, 29, 29, 0.9), rgba(140, 29, 29, 0.9)), url('images/wmsu-building.jpg');
            background-size: cover;
            background-position: center;
            width: 35%;
            padding: 50px 30px;
            color: #fff;
            text-align: center;
            clip-path: polygon(0 0, 100% 0, 85% 100%, 0% 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .vision-left-icon-container {
            width: 90px;
            height: 90px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            box-shadow: 0 0 0 6px rgba(255,255,255,0.2);
        }
        .vision-left-icon-container i {
            color: #8c1d1d;
            font-size: 2.2rem;
        }
        .vision-left h3 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            margin-bottom: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #ffffff !important;
        }
        .vision-left-divider {
            width: 60px;
            height: 2px;
            background: #d4af37;
            position: relative;
        }
        .vision-left-divider::after {
            content: '';
            position: absolute;
            top: -4px;
            left: 50%;
            transform: translateX(-50%) rotate(45deg);
            width: 10px;
            height: 10px;
            border: 2px solid #d4af37;
            background: #8c1d1d;
        }
        .vision-right {
            width: 65%;
            padding: 50px 60px;
            display: flex;
            align-items: center;
            position: relative;
            background: #fff;
        }
        
        .vision-text-content {
            font-size: 1.15rem;
            line-height: 1.8;
            color: #444;
            text-align: center;
            font-weight: 500;
            border-top: 1px solid #f7ebbe;
            border-bottom: 1px solid #f7ebbe;
            padding: 25px 0;
            margin: 0 30px;
            position: relative;
            z-index: 2;
        }

        .objectives-wrapper {
            background-color: #fdfaf5;
            border-radius: 12px;
            padding: 50px 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
            margin-bottom: 40px;
        }
        .objectives-title-container {
            text-align: center;
            margin-bottom: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .obj-title-flex {
            display: flex;
            align-items: center;
            gap: 15px;
            justify-content: center;
        }
        .obj-icon-circle {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #e9d7a5;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .obj-icon-circle i {
            color: #8c1d1d;
            font-size: 1.5rem;
        }
        .obj-title-text {
            color: #8c1d1d;
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: 2px;
        }
        .obj-title-divider {
            width: 80px;
            height: 1px;
            background: #d4af37;
            margin-top: 15px;
            position: relative;
        }
        .obj-title-divider::after {
            content: '';
            position: absolute;
            top: -4px;
            left: 50%;
            transform: translateX(-50%) rotate(45deg);
            width: 8px;
            height: 8px;
            border: 1px solid #d4af37;
            background: #fdfaf5;
        }

        .obj-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .obj-card {
            background: #fff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.04);
            padding-right: 20px;
            min-height: 80px;
        }
        .obj-card-left {
            background: #8c1d1d;
            width: 75px;
            height: 100%;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px 0 0 8px;
            clip-path: polygon(0 0, 80% 0, 100% 50%, 80% 100%, 0 100%);
            position: relative;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .obj-card-left i {
            color: #fff;
            font-size: 1.4rem;
            margin-right: 10px;
        }
        .obj-card-number {
            color: #d4af37;
            font-weight: 700;
            font-size: 1.4rem;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .obj-card-text {
            color: #555;
            font-size: 0.95rem;
            line-height: 1.5;
            font-weight: 500;
        }

        @media (max-width: 992px) {
            .vision-wrapper { flex-direction: column; }
            .vision-left { width: 100%; clip-path: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%); padding-bottom: 60px; }
            .vision-right { width: 100%; padding: 40px 30px; }
            .obj-grid { grid-template-columns: 1fr; }
        }

        /* =======================================
           5. FOOTER
           ======================================= */
        .footer-clean {
            background-color: #212529; 
            color: #d1d5db; 
            padding: 50px 0 20px 0;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-top: 40px;
        }
        .footer-clean h4 { color: #ffffff; font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; }
        .footer-clean a { color: #d1d5db; text-decoration: none; transition: 0.3s; }
        .footer-clean a:hover { color: #d4af37; }
        .footer-clean ul { list-style: none; padding: 0; margin: 0; }
        .footer-clean ul li { margin-bottom: 12px; }
        
        .footer-brand .logos img { max-height: 65px; margin-right: 10px; margin-bottom: 15px; }
        .footer-brand .title { color: #ffffff; font-size: 1.4rem; font-weight: 700; margin-bottom: 10px; }
        
        .social-icons a { font-size: 1.2rem; margin-right: 15px; margin-top: 15px; display: inline-block; }
        .contact-item { display: flex; align-items: flex-start; margin-bottom: 15px; }
        .contact-item i { color: #d4af37; font-size: 1.1rem; margin-right: 12px; margin-top: 3px; }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 40px;
            padding-top: 20px;
            font-size: 0.85rem;
            color: #9ca3af;
        }
    </style>
</head>

<body>

    <?php include('./includes/header.php'); ?>

    <div class="main-wrapper">
        <div class="container">
            
            <div class="row">
                <div class="col-lg-10 mx-auto">
                


                    <main role="main" class="mt-4">
                        
                        <div class="row mb-5 justify-content-center">
                            <div class="col-12">
                                <div class="vision-wrapper" data-aos="fade-up">
                                    <div class="vision-left">
                                        <div class="vision-left-icon-container">
                                            <i class="fas fa-eye"></i>
                                        </div>
                                        <h3>VISION</h3>
                                        <div class="vision-left-divider"></div>
                                    </div>
                                    <div class="vision-right">
                                        <?php
                                            $vision_text = htmlspecialchars($vision_data['vision'] ?? 'Vision content is currently unavailable.');
                                            $vision_text = str_ireplace('WMSU FACULTY', '<strong style="color: #8c1d1d;">WMSU FACULTY</strong>', $vision_text);
                                        ?>
                                        <div class="vision-text-content">
                                            "<?php echo $vision_text; ?>"
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-12">
                                <div class="objectives-wrapper" data-aos="fade-up">
                                    <div class="objectives-title-container">
                                        <div class="obj-title-flex">
                                            <div class="obj-icon-circle">
                                                <i class="fas fa-bullseye"></i>
                                            </div>
                                            <h2 class="obj-title-text">OBJECTIVES</h2>
                                        </div>
                                        <div class="obj-title-divider"></div>
                                    </div>

                                    <div class="obj-grid">
                                        <?php 
                                        $icons = ['fa-university', 'fa-handshake', 'fa-users', 'fa-chart-line', 'fa-balance-scale', 'fa-shield-alt', 'fa-check', 'fa-star'];
                                        $count = 1;
                                        foreach($objectives_result as $idx => $obj): 
                                            // Use modulo to loop through icons continuously
                                            $icon = $icons[$idx % count($icons)];
                                        ?>
                                            <div class="obj-card" data-aos="fade-up" data-aos-delay="<?php echo ($idx % 6) * 100; ?>">
                                                <div class="obj-card-left">
                                                    <i class="fas <?php echo $icon; ?>"></i>
                                                </div>
                                                <div class="obj-card-number"><?php echo sprintf("%02d", $count); ?></div>
                                                <div class="obj-card-text">
                                                    <?php echo htmlspecialchars($obj['content']); ?>
                                                </div>
                                            </div>
                                        <?php 
                                        $count++;
                                        endforeach; 
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if(!empty($custom_topics)): ?>
                        <div class="row mb-5">
                            <div class="col-12">
                                <?php foreach($custom_topics as $idx => $topic): ?>
                                    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden" data-aos="fade-up" data-aos-delay="<?php echo $idx * 50; ?>">
                                        <div class="row g-0 align-items-center">
                                            <?php if(!empty($topic['image_path'])): ?>
                                                <div class="col-md-4">
                                                    <img src="<?php echo htmlspecialchars($topic['image_path']); ?>" class="img-fluid rounded-start w-100" style="object-fit: cover; min-height: 250px;" alt="Topic Image">
                                                </div>
                                                <div class="col-md-8">
                                            <?php else: ?>
                                                <div class="col-12">
                                            <?php endif; ?>
                                                <div class="card-body p-4 p-md-5">
                                                    <h3 class="card-title fw-bold" style="color: #8c1d1d;"><?php echo htmlspecialchars($topic['title']); ?></h3>
                                                    <div style="width: 50px; height: 3px; background: #d4af37; margin-bottom: 20px;"></div>
                                                    <div class="card-text text-muted" style="line-height: 1.8; font-size: 1.05rem;">
                                                        <?php echo nl2br(htmlspecialchars($topic['content'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                    </main>
                </div>
            </div>
        </div>
    </div>

    <?php include('./includes/footer_simple.php'); ?>

    <script>
        // Set Footer Year
        document.getElementById('footer-year').textContent = new Date().getFullYear();

    </script>
    <?php include('./includes/scripts.php');?>
    <script src="assets/jscripts/main.js"></script>
    <script src="assets/jscripts/ind.js"></script>
</body>
</html>
