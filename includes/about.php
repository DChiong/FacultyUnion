<?php
// Include your database class
require_once 'config/database.php';

// Initialize the database class
$database = new Database();
$db = $database->getConnection();

$content = [
  'heading' => 'About Faculty Union',
  'p1' => 'The WMSU Faculty Union is a united and independent organization dedicated to protecting the rights and welfare of academic personnel.',
  'p2' => 'Our union serves as a strong collective voice and promotes equitable access to professional development opportunities.',
  'p3' => 'We are committed to defending academic freedom and fostering solidarity.',
];

// Fetch mission, vision, and values
$mission = 'To protect and advance the rights, welfare, and interests of faculty members through collective action, negotiation, and advocacy.';
$vision = 'A united faculty working together for academic excellence and institutional progress.';
$values = 'Solidarity, Integrity, Academic Freedom, and Professionalism.';

try {
  if ($db instanceof PDO) {
    $stmt = $db->prepare("SELECT * FROM about_content WHERE section_name = 'about_union' LIMIT 1");
    $stmt->execute();
    $dbContent = $stmt->fetch(PDO::FETCH_ASSOC);
    if (is_array($dbContent)) {
      $content = array_merge($content, $dbContent);
    }
    
    // Fetch vision from database
    $info_stmt = $db->prepare("SELECT vision FROM union_info LIMIT 1");
    $info_stmt->execute();
    $info = $info_stmt->fetch(PDO::FETCH_ASSOC);
    if ($info) {
      $vision = !empty($info['vision']) ? $info['vision'] : $vision;
    }
    
    // Fetch objectives from database (only the first 3 for the cards)
    $obj_stmt = $db->query("SELECT * FROM objectives ORDER BY id ASC LIMIT 3");
    if ($obj_stmt) {
        $objectives = $obj_stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $objectives = [];
    }
  }
} catch (PDOException $e) {
  // Keep defaults when DB lookup fails
  $objectives = [];
}
?>

<section id="about" class="py-5" style="background-color: #fcfbf9; overflow-x: hidden;">
  <div class="container py-4">
    <!-- About Content -->
    <div class="row align-items-center mb-5 g-5">
      
      <!-- Left Column (Image & Graphics) -->
      <div class="col-lg-5 position-relative mb-5 mb-lg-0 pe-lg-4">
        <div class="about-image-wrapper" style="position: relative; z-index: 1; padding-bottom: 2rem; padding-right: 1rem;">
            <!-- Main Image with Hover Effect -->
            <div style="position: relative; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.15), 0 0 0 10px #ffffff;">
                <img src="assets/img/office.jpg" class="img-fluid" alt="Faculty Union Office" style="object-fit: cover; height: 480px; width: 100%; transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">
                <!-- Subtle Gradient Overlay -->
                <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(114, 22, 22, 0.5) 0%, transparent 50%); pointer-events: none;"></div>
            </div>
            
            <!-- Floating Gold Accent -->
            <div style="position: absolute; bottom: 0; right: 0; background: linear-gradient(135deg, #d4af37, #b5952f); width: 140px; height: 140px; border-radius: 30px; z-index: -1; box-shadow: 0 15px 30px rgba(212, 175, 55, 0.4); animation: float-accent 5s ease-in-out infinite;"></div>
            
            <!-- Decorative Dots -->
            <div style="position: absolute; top: -20px; left: -20px; background-image: radial-gradient(#8c1d1d 2px, transparent 2px); background-size: 20px 20px; width: 160px; height: 160px; opacity: 0.15; z-index: -2;"></div>
            
            <style>
                @keyframes float-accent {
                    0% { transform: translateY(0px) rotate(0deg); }
                    50% { transform: translateY(-15px) rotate(5deg); }
                    100% { transform: translateY(0px) rotate(0deg); }
                }
            </style>
        </div>
      </div>

      <!-- Right Column (Text & Cards) -->
      <div class="col-lg-7 ps-lg-5 mt-5 mt-lg-0">
        <!-- Badge -->
        <div class="d-inline-flex align-items-center mb-3 shadow-sm" style="background-color: #f7f1e6; color: #8c1d1d; padding: 6px 16px; border-radius: 20px; font-weight: 800; font-size: 0.75rem; letter-spacing: 0.5px;">
            <i class="bi bi-people-fill me-2" style="font-size: 1rem;"></i> WMSU FACULTY UNION
        </div>

        <!-- Heading -->
        <h2 style="font-weight: 900; font-size: 2.8rem; text-transform: uppercase; line-height: 1.05; margin-bottom: 20px; color: #8c1d1d; letter-spacing: -0.5px;">
          UPHOLDING FACULTY RIGHTS<br>AND ACADEMIC FREEDOM
        </h2>

        <!-- Gold Separator -->
        <div style="width: 70px; height: 3px; background-color: #d4af37; margin-bottom: 25px;"></div>

        <!-- Paragraph -->
        <p class="text-muted mb-4" style="line-height: 1.8; font-size: 1rem; color: #4a5568 !important;">
          The WMSU Faculty Union is a united and independent organization dedicated to protecting the rights and welfare of the academic personnel. Our union serves as a strong collective voice, striving to ensure equitable access to professional development. We are committed to defending academic freedom and fostering solidarity.
        </p>

        <!-- Feature Cards Grid (Dynamic Objectives) -->
        <div class="row g-3 mb-4">
            <?php 
            if (!empty($objectives)): 
                // Array of bootstrap icons to cycle through for variety
                $icons = ['bi-shield-shaded', 'bi-book-half', 'bi-graph-up-arrow', 'bi-star-fill', 'bi-award-fill', 'bi-flag-fill'];
                $iconIndex = 0;
                foreach ($objectives as $obj): 
                    $icon = $icons[$iconIndex % count($icons)];
                    $iconIndex++;
            ?>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center p-3" style="border-radius: 16px; transition: transform 0.3s, box-shadow 0.3s; cursor: default;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 .125rem .25rem rgba(0,0,0,.075)';">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px; border-radius: 50%; border: 2px solid #8c1d1d; background: #fff;">
                        <i class="bi <?= $icon ?>" style="font-size: 1.5rem; color: #8c1d1d;"></i>
                    </div>
                    <p class="text-muted mt-2" style="font-size: 0.85rem; margin-bottom: 0; line-height: 1.6; font-weight: 500;">
                        <?= htmlspecialchars($obj['content']) ?>
                    </p>
                </div>
            </div>
            <?php 
                endforeach; 
            else: 
            ?>
                <div class="col-12">
                    <p class="text-muted">No objectives have been set yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Button -->
        <a href="about.php" class="btn mt-3 shadow-sm" style="background: linear-gradient(135deg, #8c1d1d, #681212); color: white; padding: 12px 25px; font-weight: 700; border-radius: 6px; font-size: 0.85rem; letter-spacing: 0.5px; border: none;">
          LEARN MORE ABOUT US <i class="bi bi-chevron-right ms-2" style="font-size: 0.8rem; font-weight: bold;"></i>
        </a>
      </div>
    </div>

    <!-- Vision Content -->
    <div class="row mt-5 justify-content-center">
      <div class="col-12">
        <style>
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

        @media (max-width: 992px) {
            .vision-wrapper { flex-direction: column; }
            .vision-left { width: 100%; clip-path: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%); padding-bottom: 60px; }
            .vision-right { width: 100%; padding: 40px 30px; }
        }
        </style>

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
                    $vision_text = htmlspecialchars($vision ?? 'Vision content is currently unavailable.');
                    $vision_text = str_ireplace('WMSU FACULTY', '<strong style="color: #8c1d1d;">WMSU FACULTY</strong>', $vision_text);
                ?>
                <div class="vision-text-content">
                    "<?= $vision_text; ?>"
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</section>
