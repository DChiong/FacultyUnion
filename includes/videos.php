<?php
require_once('config/database.php');
$database = new Database();
$db = $database->getConnection();
$videos = [];

try {
    if ($db instanceof PDO) {
        $video_query = $db->query("SELECT * FROM admin_videos ORDER BY created_at DESC");
        $videos = $video_query->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) { 
    $videos = []; 
}
?>

<style>
    :root { --maroon: #8c1d1d; --gold: #d4af37; --dark: #1a1a1a; }

    #videos-section { padding: 80px 0; background: #fff; }
    
    /* Carousel Item Styling */
    .video-item { padding: 15px; outline: none; }
    .video-card {
        background: #fff; border-radius: 12px; overflow: hidden;
        transition: all 0.3s ease; cursor: pointer; border: 1px solid #eee;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05); display: flex; flex-direction: column; height: 100%; min-height: 420px;
    }
    .video-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.1); border-color: var(--gold); }
    
    /* Video Thumbnail Wrapper */
    .video-thumb-container { position: relative; width: 100%; height: 230px; background: #000; overflow: hidden; }
    .video-thumb-container img { width: 100%; height: 100%; object-fit: cover; border-bottom: 4px solid var(--gold); opacity: 0.9; }
    .play-overlay { 
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); 
        color: white; font-size: 3rem; text-shadow: 0 0 15px rgba(0,0,0,0.5); transition: all 0.3s;
        background: rgba(255,255,255,0.2); border-radius: 50%; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(5px);
    }
    .video-card:hover .play-overlay { background: rgba(255,255,255,0.9); color: var(--maroon); transform: translate(-50%, -50%) scale(1.1); }

    .video-card-body { padding: 25px 20px; display: flex; flex-direction: column; flex: 1; }
    .video-info-row { display: flex; gap: 15px; margin-bottom: 20px; }
    .video-icon-box { width: 50px; height: 50px; border-radius: 10px; background: var(--maroon); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; box-shadow: 0 4px 10px rgba(140,29,29,0.2); }
    .video-title-wrap { display: flex; flex-direction: column; justify-content: center; }
    .video-title-wrap h5 { color: var(--maroon); font-weight: 800; margin: 0; font-size: 1.05rem; text-transform: uppercase; line-height: 1.3; }
    .video-title-wrap p { margin: 5px 0 0; font-size: 0.8rem; color: #666; line-height: 1.5; }
    
    .video-btn {
        margin-top: auto; align-self: flex-start; margin-left: 65px;
        background: transparent; color: var(--maroon); border: none; padding: 0;
        font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;
        transition: all 0.3s ease; text-decoration: none; cursor: pointer; display: flex; align-items: center; gap: 5px;
    }
    .video-btn:hover { color: var(--gold); }

    /* Navigation Arrows */
    .video-arrow {
        position: absolute; top: 50%; transform: translateY(-50%);
        z-index: 10; cursor: pointer; background: var(--maroon);
        color: white; width: 40px; height: 40px; border-radius: 4px;
        display: flex; align-items: center; justify-content: center; transition: all 0.3s;
    }
    .video-arrow:hover { background: var(--gold); color: var(--dark); }
    .video-prev { left: -50px; }
    .video-next { right: -50px; }

    /* Modal Styling */
    .video-modal-overlay {
        display: none; position: fixed; z-index: 9999; left: 0; top: 0; 
        width: 100%; height: 100%; background: rgba(0,0,0,0.9);
        align-items: center; justify-content: center; padding: 20px;
    }
    .video-modal-box {
        background: white; width: 100%; max-width: 900px; border-radius: 20px;
        overflow: hidden; position: relative; box-shadow: 0 0 30px rgba(0,0,0,0.5);
    }
    .video-modal-header { background: var(--maroon); color: white; padding: 15px 20px; border-bottom: 4px solid var(--gold); }
    .video-modal-header h2 { color: white; margin: 0; font-size: 1.3rem; text-transform: uppercase; }
    
    .video-player-container { background: #000; width: 100%; aspect-ratio: 16 / 9; }
    .video-player-container iframe, .video-player-container video { width: 100%; height: 100%; border: none; }

    .close-video-modal { position: absolute; right: 20px; top: 10px; font-size: 30px; color: white; cursor: pointer; z-index: 100; }
</style>

<section id="videos">
    <div class="container">
        <div class="section-title text-center mb-4 pb-2">
            <h2 style="color: var(--maroon); font-weight: 800; font-size: 2.2rem; margin-bottom: 0;">VIDEO HIGHLIGHTS</h2>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-11 position-relative">
                <div class="video-arrow video-prev"><i class="fas fa-chevron-left"></i></div>
                <div class="video-arrow video-next"><i class="fas fa-chevron-right"></i></div>
                
                <div class="videos-slick-carousel">
                    <?php if(!empty($videos)): foreach ($videos as $row): 
                        // Generate thumbnail if it's YouTube
                        $thumb = $row['thumbnail'];
                        if($row['video_type'] == 'youtube' && empty($thumb)) {
                            preg_match('/embed\/([^?]+)/', $row['video_source'], $matches);
                            $id = $matches[1] ?? '';
                            $thumb = "https://img.youtube.com/vi/$id/hqdefault.jpg";
                        }
                    ?>
                        <div class="video-item">
                            <div class="video-card" onclick="openVideoModal(
                                '<?php echo addslashes($row['video_title']); ?>', 
                                '<?php echo $row['video_source']; ?>', 
                                '<?php echo $row['video_type']; ?>'
                            )">
                                <div class="video-thumb-container">
                                    <img src="<?php echo $thumb; ?>" alt="Thumbnail">
                                    <div class="play-overlay"><i class="fas fa-play"></i></div>
                                </div>
                                <div class="video-card-body">
                                    <div class="video-info-row">
                                        <div class="video-icon-box"><i class="fas fa-video"></i></div>
                                        <div class="video-title-wrap">
                                            <h5><?php echo htmlspecialchars($row['video_title']); ?></h5>
                                            <p>Watch this video highlight from the Faculty Union.</p>
                                        </div>
                                    </div>
                                    <button class="video-btn">WATCH NOW <i class="fas fa-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <div class="text-center p-5 w-100"><p>No videos available.</p></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- "Want to See More?" CTA Section -->
        <?php if (!empty($videos)): ?>
        <div style="background: #fdfaf4; padding: 40px; border-radius: 12px; border: 1px solid #f0e8dc; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; align-items: center; justify-content: space-between; gap: 30px; margin-top: 50px;">
            <div style="display: flex; align-items: center; gap: 25px;">
                <div style="position: relative; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                    <div style="width: 60px; height: 60px; background: var(--maroon); border-radius: 12px; display: flex; align-items: center; justify-content: center; z-index: 2; box-shadow: 0 4px 10px rgba(140, 29, 29, 0.2);">
                        <i class="fas fa-video" style="font-size: 1.5rem; color: #fff;"></i>
                    </div>
                    <!-- Sparkles decoration -->
                    <i class="fas fa-star" style="position: absolute; right: -10px; bottom: 0; color: var(--gold); font-size: 0.8rem;"></i>
                    <i class="fas fa-star" style="position: absolute; left: -5px; top: 0; color: var(--gold); font-size: 0.6rem; opacity: 0.7;"></i>
                </div>
                <div>
                    <h5 style="margin: 0; color: var(--maroon); font-weight: 800; font-size: 1.3rem;">Want to see more?</h5>
                    <p style="margin: 5px 0 0 0; color: #555; font-size: 0.95rem;">Watch all event highlights and video updates from the Faculty Union.</p>
                </div>
            </div>
            <a href="includes/all/all_videos.php" class="btn" style="background-color: var(--maroon); color: white; border: none; padding: 12px 35px; font-weight: 700; white-space: nowrap; border-radius: 6px; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px; transition: 0.3s; box-shadow: 0 4px 10px rgba(140, 29, 29, 0.2);">
                VIEW ALL VIDEOS <i class="fas fa-arrow-right" style="margin-left: 5px; font-size: 0.9em;"></i>
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>

<div id="videoOverlay" class="video-modal-overlay">
    <div class="video-modal-box">
        <span class="close-video-modal" onclick="closeVideoModal()">&times;</span>
        <div class="video-modal-header"><h2 id="vTitle"></h2></div>
        <div id="vPlayer" class="video-player-container">
            </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('.videos-slick-carousel').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 4000,
        prevArrow: $('.video-prev'),
        nextArrow: $('.video-next'),
        responsive: [
            { breakpoint: 992, settings: { slidesToShow: 2 } },
            { breakpoint: 768, settings: { slidesToShow: 1 } }
        ]
    });
});

function openVideoModal(title, source, type) {
    document.getElementById('vTitle').innerText = title;
    const player = document.getElementById('vPlayer');
    
    if(type === 'youtube') {
        player.innerHTML = `<iframe src="${source}?autoplay=1" allow="autoplay; encrypted-media" allowfullscreen></iframe>`;
    } else {
        player.innerHTML = `<video controls autoplay><source src="${source}" type="video/mp4">Your browser does not support the video tag.</video>`;
    }
    
    document.getElementById('videoOverlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeVideoModal() {
    document.getElementById('vPlayer').innerHTML = ""; // Stop video
    document.getElementById('videoOverlay').style.display = 'none';
    document.body.style.overflow = 'auto';
}

window.onclick = function(e) {
    if (e.target == document.getElementById('videoOverlay')) closeVideoModal();
}
</script>
