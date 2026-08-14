<?php
require_once('config/database.php'); 
$database = new Database();
$db = $database->getConnection(); 

if (!$db) {
    die("Database connection failed.");
}

// ==========================================
// A. FETCH OFFICERS DATA
// ==========================================
try {
    // Fetch Executive Officers
    $exec_stmt = $db->prepare("SELECT * FROM officers WHERE category = 'Executive' ORDER BY rank ASC");
    $exec_stmt->execute();
    $exec_rows = $exec_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Finance Officers
    $fin_stmt = $db->prepare("SELECT * FROM officers WHERE category = 'Finance' ORDER BY rank ASC");
    $fin_stmt->execute();
    $fin_rows = $fin_stmt->fetchAll(PDO::FETCH_ASSOC);

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

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Officers - WMSU Faculty Union</title>
    
    <?php require_once('./includes/head.php'); ?>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body { 
            background-color: #f8f9fa; 
            line-height: 1.8; 
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
            overflow-x: hidden;
        }
        .main-wrapper {
            padding-top: 60px;
        }
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

    </style>
</head>

<body>

    <?php include('./includes/header.php'); ?>

    <div class="main-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <main role="main" class="mt-4">
                        <div class="content-card">
                            <h2 class="section-title">Union Leadership</h2>
                            
                            <div class="inner-content-box">
                                <div class="team-header">Executive Officers</div>
                                <div class="row mb-2">
                                    <?php 
                                    $split = ceil(count($exec_rows) / 2);
                                    $chunks = array_chunk($exec_rows, $split > 0 ? $split : 1);
                                    foreach ($chunks as $column): ?>
                                        <div class="col-md-6">
                                            <?php foreach ($column as $officer): ?>
                                                <div class="person-card">
                                                    <div class="officer-avatar">
                                                        <img src="<?php echo htmlspecialchars(resolveOfficerPhoto($officer['profile_picture'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($officer['full_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    </div>
                                                    <div class="person-details">
                                                        <span class="person-name"><?php echo htmlspecialchars($officer['position']); ?></span> 
                                                        <?php echo htmlspecialchars($officer['full_name']); ?> 
                                                        <br><small class="text-muted">(<?php echo htmlspecialchars($officer['department_acronym']); ?>)</small>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="finance-header">Finance Officers</div>
                                <div class="row">
                                    <?php foreach($fin_rows as $fin): ?>
                                        <div class="col-md-6">
                                            <div class="person-card">
                                                <div class="officer-avatar">
                                                    <img src="<?php echo htmlspecialchars(resolveOfficerPhoto($fin['profile_picture'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($fin['full_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                                <div class="person-details">
                                                    <span class="person-name"><?php echo htmlspecialchars($fin['position']); ?></span> 
                                                    <?php echo htmlspecialchars($fin['full_name']); ?> 
                                                    <br><small class="text-muted">(<?php echo htmlspecialchars($fin['department_acronym']); ?>)</small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>

    <?php include('./includes/footer_simple.php'); ?>
    <?php include('./includes/scripts.php');?>
    <script src="assets/jscripts/main.js"></script>
    <script src="assets/jscripts/ind.js"></script>
</body>
</html>
