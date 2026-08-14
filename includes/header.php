<?php
// 1. Fetch Site Settings from Database
require_once(__DIR__ . '/../config/database.php');
$base_path = defined('BASE_PATH') ? BASE_PATH : '';
$database = new Database();
$db = $database->getConnection();

$display_name = "Faculty Union";
$display_logo = "";

if ($db instanceof PDO) {
  try {
    $settings_query = $db->query("SELECT site_name, logo_path FROM site_settings WHERE id = 1");
    $settings = $settings_query ? $settings_query->fetch(PDO::FETCH_ASSOC) : false;

    if (is_array($settings)) {
      $display_name = !empty($settings['site_name']) ? htmlspecialchars($settings['site_name']) : $display_name;
      $display_logo = !empty($settings['logo_path']) ? $settings['logo_path'] : $display_logo;
    }
  } catch (PDOException $exception) {
    // Use defaults when DB lookup fails
  }
}
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
  /* Reset body margins for horizontal navbar */
  body {
    margin-left: 0;
    overflow-x: hidden;
    padding-top: 70px;
  }

  /* Header Styling */
  header {
    font-family: "Poppins", sans-serif;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background-color: #ffffff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 1000;
    padding: 0;
  }

  .navbar {
    padding: 10px 0;
  }

  .navbar-brand {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: var(--primary-maroon, #8c1d1d);
    font-weight: 700;
    font-size: 1.2rem;
  }

  .navbar-brand img {
    max-height: 50px;
    width: auto;
    margin-right: 10px;
  }

  .navbar-nav {
    margin-left: auto;
  }

  .nav-item {
    margin-left: 5px;
  }

  .nav-link {
    color: var(--primary-maroon, #8c1d1d) !important;
    font-weight: 500;
    padding: 8px 15px !important;
    text-transform: uppercase;
    font-size: 0.85rem;
    transition: all 0.3s ease;
  }

  .nav-link:hover,
  .nav-link.active {
    color: var(--primary-gold, #d4af37) !important;
    border-bottom: 2px solid var(--primary-gold, #d4af37);
  }

  /* Sidebar - Hidden */
  #sidebar {
    display: none;
    margin-top: auto;
    border-top: 1px solid #eee;
  }

  #sidebar .btn-login, #sidebar .btn-logout {
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
  }

  #sidebar .btn-login {
    border: 1px solid var(--primary-maroon, #8c1d1d);
    background-color: var(--primary-maroon, #8c1d1d);
    color: #ffffff !important;
  }
  #sidebar .btn-login:hover {
    background-color: var(--primary-gold, #d4af37);
    border-color: var(--primary-gold, #d4af37);
    color: #000000 !important;
  }

  #sidebar .btn-logout {
    background-color: #6c757d;
    color: #fff !important;
  }
  #sidebar .btn-logout:hover {
    background-color: #5a6268;
  }

  /* Mobile Tweaks */
  @media (max-width: 768px) {
    body {
      padding-top: 60px;
    }

    .navbar {
      padding: 5px 0;
    }

    .navbar-brand {
      font-size: 1rem;
    }

    .navbar-brand img {
      max-height: 40px;
    }

    .nav-link {
      font-size: 0.75rem;
      padding: 5px 10px !important;
    }
  }
</style>

<header>
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-4">
      <a href="<?php echo $base_path; ?>index.php" class="navbar-brand">
        <?php if(!empty($display_logo)): ?>
          <img src="<?php echo $base_path; ?><?php echo $display_logo; ?>" alt="Logo">
        <?php endif; ?>
        <div style="display: flex; flex-direction: column; justify-content: center;">
          <span style="line-height: 1; font-size: 1.6rem; margin-bottom: 3px; font-weight: 800;"><?php echo $display_name; ?></span>
          <span style="font-size: 0.75rem; color: #555; font-weight: 500; line-height: 1;">Empowering Educators, Protecting Excellence</span>
        </div>
      </a>
      
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <?php 
      $current_page = basename($_SERVER['PHP_SELF']); 
      
      $nav_items = [];
      if ($db instanceof PDO) {
          try {
              $nav_items = $db->query("SELECT title, url, active_check FROM menu_items WHERE is_active = 1 ORDER BY display_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {}
      }
      ?>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <?php foreach ($nav_items as $item): ?>
            <?php 
              $is_active = '';
              $request_uri = $_SERVER['REQUEST_URI'];
              
              // For dynamic pages, we need to match the exact ID in the URL
              if (strpos($item['url'], 'view_page.php') !== false) {
                  if (strpos($request_uri, $item['url']) !== false) {
                      $is_active = 'active';
                  }
              } else {
                  // For static pages, use the active_check
                  if (!empty($item['active_check'])) {
                      if ($current_page == $item['active_check']) {
                          $is_active = 'active';
                      }
                  } else if ($current_page == $item['url']) {
                      $is_active = 'active';
                  }
              }
            ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $is_active; ?>" href="<?php echo $base_path . htmlspecialchars($item['url']); ?>">
                <?php echo htmlspecialchars($item['title']); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </nav>
</header>
