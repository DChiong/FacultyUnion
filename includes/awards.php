<?php
require_once('config/database.php');
$database = new Database();
$db = $database->getConnection();
$awards = [];

try {
    if ($db instanceof PDO) {
        $awards_query = $db->query("SELECT * FROM awards ORDER BY award_year DESC, created_at DESC");
        $awards = $awards_query->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) { 
    $awards = []; 
}
?>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    :root { --maroon: #8c1d1d; --gold: #d4af37; --dark: #1a1a1a; }

    /* Section Styling */
    #awards-section { padding: 80px 0; background: #fff; }
    .section-title { margin-bottom: 50px; }

    /* Carousel Item Styling */
    .award-item { padding: 15px; outline: none; }
    .award-card {
        background: #fff; border-radius: 12px; overflow: hidden;
        transition: all 0.3s ease; border: 1px solid #eee;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08); position: relative;
        display: flex; flex-direction: column; height: 100%; min-height: 480px;
    }
    .award-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.12); border-color: var(--gold); }
    .award-img-wrapper { position: relative; height: 230px; width: 100%; }
    .award-img-wrapper img { width: 100%; height: 100%; object-fit: cover; border-bottom: 4px solid var(--gold); }
    .award-icon-float {
        position: absolute; bottom: -28px; left: 50%; transform: translateX(-50%);
        width: 56px; height: 56px; background: var(--maroon); border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1.4rem; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        z-index: 2;
    }
    .award-card-body { padding: 45px 25px 30px; text-align: center; display: flex; flex-direction: column; flex: 1; }
    .award-card-body h5 { color: var(--maroon); font-weight: 800; font-size: 1.15rem; margin-bottom: 12px; text-transform: uppercase; }
    .award-card-body p { color: #555; font-size: 0.85rem; line-height: 1.6; margin-bottom: 25px; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; }
    .award-btn {
        margin-top: auto; align-self: center; background: transparent; color: var(--maroon);
        border: 2px solid var(--maroon); border-radius: 30px; padding: 10px 24px;
        font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;
        transition: all 0.3s ease; text-decoration: none; cursor: pointer;
    }
    .award-btn i { font-size: 0.9em; margin-left: 5px; }
    .award-btn:hover { background: var(--maroon); color: #fff; border-color: var(--maroon); }

    /* Custom Navigation Arrows */
    .award-arrow {
        position: absolute; top: 50%; transform: translateY(-50%);
        z-index: 10; cursor: pointer; background: var(--maroon);
        color: white; width: 40px; height: 40px; border-radius: 4px;
        display: flex; align-items: center; justify-content: center; transition: all 0.3s;
    }
    .award-arrow:hover { background: var(--gold); color: var(--dark); }
    .award-prev { left: -50px; }
    .award-next { right: -50px; }

    /* Modal Styling (Detail View) */
    .award-modal-overlay {
        display: none; position: fixed; z-index: 9999; left: 0; top: 0; 
        width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(2px);
        align-items: center; justify-content: center; padding: 20px;
    }
    .award-modal-box {
        background: white; width: 100%; max-width: 900px; border-radius: 12px;
        overflow: hidden; position: relative; max-height: 85vh; display: flex; flex-direction: column;
        box-shadow: 0 15px 50px rgba(0,0,0,0.25);
    }
    
    .modal-main-body { display: flex; flex-wrap: nowrap; overflow-y: auto; padding: 40px; gap: 35px; }
    @media (max-width: 768px) { .modal-main-body { flex-direction: column; padding: 25px; gap: 25px; } }

    .modal-img-pane { flex: 0 0 45%; max-width: 45%; display: flex; align-items: flex-start; justify-content: center; }
    @media (max-width: 768px) { .modal-img-pane { max-width: 100%; } }
    .modal-img-pane img { width: 100%; object-fit: cover; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    
    .modal-text-pane { flex: 1; display: flex; flex-direction: column; min-width: 0; }
    .modal-text-pane h2 { color: var(--maroon); font-size: 1.8rem; font-weight: 800; margin: 0 0 25px 0; line-height: 1.2; text-transform: uppercase; padding-right: 20px; word-break: break-word; }
    
    .info-group { margin-bottom: 20px; min-width: 0; }
    .info-label { color: #888; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; display: block; margin-bottom: 5px; letter-spacing: 0.5px; }
    .info-value { font-size: 1.15rem; color: #222; font-weight: 600; display: block; word-break: break-word; }
    .info-desc { font-size: 0.95rem; line-height: 1.7; color: #555; background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 3px solid var(--maroon); border-top: 1px solid #eee; border-right: 1px solid #eee; border-bottom: 1px solid #eee; margin-top: 10px; text-align: left; word-break: break-word; overflow-wrap: break-word; }

    .close-btn-modal { 
        position: absolute; right: 15px; top: 15px; width: 35px; height: 35px;
        background: rgba(0,0,0,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 18px; color: #333; cursor: pointer; z-index: 100; transition: all 0.2s;
    }
    .close-btn-modal:hover { background: var(--maroon); color: white; }
</style>

<section id="awards">
    <div class="container">
        <div class="section-title text-center" style="margin-bottom: 30px; padding-bottom: 0;">
            <h2 style="color: var(--maroon); font-weight: 900; font-size: 2.8rem; text-transform: uppercase; margin: 0 0 15px 0;">Faculty Awards & Recognition</h2>
            <p style="color: #555; max-width: 650px; margin: 0 auto; font-size: 1rem; line-height: 1.6;">
                Honoring the dedication, achievements, and outstanding contributions<br>of our faculty in service, artistry, and community impact.
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-11 position-relative">
                <div class="award-arrow award-prev"><i class="fas fa-chevron-left"></i></div>
                <div class="award-arrow award-next"><i class="fas fa-chevron-right"></i></div>
                
                <div class="awards-slick-carousel">
                    <?php if(!empty($awards)): foreach ($awards as $row): ?>
                        <div class="award-item">
                            <div class="award-card">
                                <div class="award-img-wrapper">
                                    <img src="<?php echo htmlspecialchars($row['award_image']); ?>" alt="Award">
                                    <div class="award-icon-float">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                </div>
                                <div class="award-card-body">
                                    <h5><?php echo htmlspecialchars($row['award_title']); ?></h5>
                                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                                    <button class="award-btn" onclick="openAwardModal(
                                        '<?php echo addslashes($row['award_title']); ?>', 
                                        '<?php echo addslashes($row['recipient_name']); ?>', 
                                        '<?php echo addslashes($row['description']); ?>', 
                                        '<?php echo htmlspecialchars($row['award_image']); ?>', 
                                        '<?php echo $row['award_year']; ?>'
                                    )">EXPLORE AWARDS <i class="fas fa-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <div class="text-center p-5 w-100"><p>No awards found.</p></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- "Want to See More?" CTA Section -->
        <?php if (!empty($awards)): ?>
        <div style="background: #fdfaf4; padding: 40px; border-radius: 12px; border: 1px solid #f0e8dc; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; align-items: center; justify-content: space-between; gap: 30px; margin-top: 50px;">
            <div style="display: flex; align-items: center; gap: 25px;">
                <div style="position: relative; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-leaf" style="position: absolute; left: -5px; color: var(--gold); font-size: 1.5rem; transform: rotate(-45deg);"></i>
                    <i class="fas fa-leaf" style="position: absolute; right: -5px; color: var(--gold); font-size: 1.5rem; transform: rotate(45deg) scaleX(-1);"></i>
                    <div style="width: 50px; height: 50px; background: var(--maroon); border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 2; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                        <i class="fas fa-trophy" style="font-size: 1.3rem; color: #fff;"></i>
                    </div>
                </div>
                <div>
                    <h5 style="margin: 0; color: var(--maroon); font-weight: 800; font-size: 1.3rem;">Want to see more?</h5>
                    <p style="margin: 5px 0 0 0; color: #555; font-size: 0.95rem;">Explore all awards and recognitions conferred to our outstanding faculty.</p>
                </div>
            </div>
            <a href="includes/all/all_awards.php" class="btn" style="background-color: var(--maroon); color: white; border: 2px solid var(--gold); padding: 12px 35px; font-weight: 700; white-space: nowrap; border-radius: 6px; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px; transition: 0.3s; box-shadow: 0 4px 10px rgba(140, 29, 29, 0.2);">
                VIEW ALL AWARDS <i class="fas fa-arrow-right" style="margin-left: 5px; font-size: 0.9em;"></i>
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>

<div id="awardOverlay" class="award-modal-overlay">
    <div class="award-modal-box">
        <span class="close-btn-modal" onclick="closeAwardModal()"><i class="fas fa-times"></i></span>
        
        <div class="modal-main-body">
            <div class="modal-img-pane">
                <img id="mImg" src="">
            </div>

            <div class="modal-text-pane">
                <h2 id="mTitle"></h2>
                <div class="info-group">
                    <span class="info-label">Honoree</span>
                    <span id="mRecipient" class="info-value"></span>
                </div>
                <div class="info-group">
                    <span class="info-label">Year Conferred</span>
                    <span id="mYear" class="info-value"></span>
                </div>
                <div class="info-group" style="flex-grow: 1;">
                    <span class="info-label">Achievement Details</span>
                    <div id="mDesc" class="info-desc"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script>
$(document).ready(function(){
    $('.awards-slick-carousel').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3500,
        pauseOnHover: true,
        prevArrow: $('.award-prev'),
        nextArrow: $('.award-next'),
        responsive: [
            { breakpoint: 992, settings: { slidesToShow: 2 } },
            { breakpoint: 768, settings: { slidesToShow: 1 } }
        ]
    });
});

function openAwardModal(title, recipient, desc, img, year) {
    document.getElementById('mTitle').innerText = title;
    document.getElementById('mRecipient').innerText = recipient;
    document.getElementById('mYear').innerText = year;
    document.getElementById('mDesc').innerText = desc;
    document.getElementById('mImg').src = img;
    
    document.getElementById('awardOverlay').style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Disable scroll
}

function closeAwardModal() {
    document.getElementById('awardOverlay').style.display = 'none';
    document.body.style.overflow = 'auto'; // Enable scroll
}

// Exit modal on background click
window.onclick = function(e) {
    if (e.target == document.getElementById('awardOverlay')) closeAwardModal();
}
</script>
