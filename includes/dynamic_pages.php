<?php
require_once('./config/database.php');
$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT * FROM dynamic_pages WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
$stmt->execute();
$pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($pages):
    foreach ($pages as $page):
?>
    <section class="section pb-5 pt-5">
        <div class="container" data-aos="fade-up">
            <div class="row gy-4 align-items-center">
                <?php if (!empty($page['image_path'])): ?>
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <img src="<?php echo htmlspecialchars($page['image_path']); ?>" class="img-fluid rounded shadow-sm" alt="Image" style="width: 100%; height: auto; object-fit: cover;">
                    </div>
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="section-title mb-4">
                            <h2><?php echo htmlspecialchars($page['title']); ?></h2>
                        </div>
                        <div class="content" style="line-height: 1.6; font-size: 1.05rem;">
                            <?php echo nl2br(htmlspecialchars($page['content'])); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="col-12" data-aos="fade-up" data-aos-delay="100">
                        <div class="section-title mb-4 text-center">
                            <h2><?php echo htmlspecialchars($page['title']); ?></h2>
                        </div>
                        <div class="content" style="line-height: 1.6; font-size: 1.05rem; max-width: 900px; margin: 0 auto;">
                            <?php echo nl2br(htmlspecialchars($page['content'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php
    endforeach;
endif;
?>
