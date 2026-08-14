<?php
require_once('config/database.php');
$database = new Database();
$db = $database->getConnection();

$today = date('Y-m-d');
$events = [];

try {
    if ($db instanceof PDO) {
        $query = "SELECT * FROM events WHERE event_start_date >= :today ORDER BY event_start_date ASC";
        $events_query = $db->prepare($query);
        $events_query->execute(['today' => $today]);
        $events = $events_query->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) { 
    $events = []; 
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    /* Carousel Item Styling */
    .event-slide-item { padding: 15px; outline: none; }
    .event-slide-card {
        background: #fff; border-radius: 12px; overflow: hidden;
        transition: all 0.3s ease; border: 1px solid #eee;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08); position: relative;
        display: flex; flex-direction: column; height: 100%; min-height: 480px;
    }
    .event-slide-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.12); border-color: #d4af37; }
    .event-img-wrapper { position: relative; height: 230px; width: 100%; }
    .event-img-wrapper img { width: 100%; height: 100%; object-fit: cover; border-bottom: 4px solid #d4af37; }
    .event-icon-float {
        position: absolute; bottom: -28px; left: 50%; transform: translateX(-50%);
        width: 56px; height: 56px; background: var(--primary-maroon, #8c1d1d); border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1.4rem; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        z-index: 2;
    }
    
    .event-card-body { padding: 45px 25px 30px; text-align: center; display: flex; flex-direction: column; flex: 1; }
    .event-card-body h5 { color: var(--primary-maroon, #8c1d1d); font-weight: 800; font-size: 1.15rem; margin-bottom: 12px; text-transform: uppercase; }
    .event-card-body p { color: #555; font-size: 0.85rem; line-height: 1.6; margin-bottom: 25px; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; }
    .event-btn {
        margin-top: auto; align-self: center; background: transparent; color: var(--primary-maroon, #8c1d1d);
        border: 2px solid var(--primary-maroon, #8c1d1d); border-radius: 30px; padding: 10px 24px;
        font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;
        transition: all 0.3s ease; text-decoration: none; cursor: pointer;
    }
    .event-btn i { font-size: 0.9em; margin-left: 5px; }
    .event-btn:hover { background: var(--primary-maroon, #8c1d1d); color: #fff; border-color: var(--primary-maroon, #8c1d1d); }

    /* Custom Navigation Arrows */
    .event-arrow {
        position: absolute; top: 50%; transform: translateY(-50%);
        z-index: 10; cursor: pointer; background: var(--primary-maroon, #8c1d1d);
        color: white; width: 40px; height: 40px; border-radius: 4px;
        display: flex; align-items: center; justify-content: center; transition: all 0.3s;
    }
    .event-arrow:hover { background: #d4af37; color: #1a1a1a; }
    .event-next { right: -50px; }

    /* Modal Styles */
    .custom-modal-overlay {
        display: none; 
        position: fixed; 
        z-index: 9999; 
        left: 0; 
        top: 0; 
        width: 100%; 
        height: 100%; 
        background-color: rgba(0,0,0,0.5); 
        backdrop-filter: blur(2px); 
        align-items: center; 
        justify-content: center; 
        padding: 20px;
    }
    
    .modal-card {
        background: white; 
        width: 100%; 
        max-width: 900px;
        border-radius: 12px;
        overflow: hidden; 
        position: relative; 
        box-shadow: 0 15px 50px rgba(0,0,0,0.25);
        animation: modalFadeIn 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95) translateY(-10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .modal-close-btn { 
        position: absolute; 
        right: 15px; 
        top: 15px; 
        width: 35px;
        height: 35px;
        background: rgba(0,0,0,0.05);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px; 
        cursor: pointer; 
        color: #333; 
        z-index: 100;
        transition: all 0.2s;
    }
    
    .modal-close-btn:hover { background: var(--primary-maroon, #8c1d1d); color: white; }
    
    .modal-content-wrap { padding: 40px; display: flex; gap: 35px; max-height: 85vh; overflow-y: auto; }
    @media (max-width: 768px) { .modal-content-wrap { flex-direction: column; padding: 25px; gap: 25px; } }

    .modal-left { flex: 0 0 45%; max-width: 45%; }
    @media (max-width: 768px) { .modal-left { max-width: 100%; } }
    .modal-left img { width: 100%; border-radius: 8px; object-fit: cover; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    
    .modal-right { flex: 1; display: flex; flex-direction: column; min-width: 0; }
    
    .modal-title-main { color: var(--primary-maroon, #8c1d1d); font-size: 1.8rem; font-weight: 800; margin: 0 0 8px 0; line-height: 1.2; text-transform: uppercase; padding-right: 20px; word-break: break-word; }
    .modal-tagline { color: #6c757d; font-size: 1.05rem; font-weight: 600; margin-bottom: 25px; font-style: italic; word-break: break-word; }
    
    .modal-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; }
    .info-item { display: flex; align-items: flex-start; gap: 15px; background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #eee; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: transform 0.2s, border-color 0.2s; min-width: 0; }
    .info-item:hover { border-color: var(--primary-maroon, #8c1d1d); transform: translateY(-2px); }
    .info-item i { color: var(--primary-maroon, #8c1d1d); font-size: 1.3rem; margin-top: 2px; flex-shrink: 0; }
    .info-item div { min-width: 0; }
    .info-item div strong { display: block; font-size: 0.75rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px; }
    .info-item div span { font-size: 0.95rem; color: #222; font-weight: 600; line-height: 1.3; display: block; word-break: break-word; }
    
    .modal-desc-box { background: #f8f9fa; padding: 25px; border-radius: 8px; border-left: 3px solid var(--primary-maroon, #8c1d1d); flex-grow: 1; border-top: 1px solid #eee; border-right: 1px solid #eee; border-bottom: 1px solid #eee; min-width: 0; }
    .modal-desc-box h6 { color: var(--primary-maroon, #8c1d1d); font-weight: 800; margin-bottom: 10px; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .modal-desc-box p { margin: 0; color: #555; font-size: 0.95rem; line-height: 1.7; word-break: break-word; overflow-wrap: break-word; }

</style>


<section id="events" class="news-section section py-5" style="background-color: #fff;">
    <div class="container" data-aos="fade-up">
        
        <div class="section-title text-center" style="margin-bottom: 30px; padding-bottom: 0;">
            <h2 style="color: var(--primary-maroon, #8c1d1d); font-weight: 900; font-size: 2.8rem; text-transform: uppercase; margin: 0 0 15px 0;">Upcoming Events</h2>
            <p style="color: #555; max-width: 650px; margin: 0 auto; font-size: 1rem; line-height: 1.6;">Stay updated with the latest happenings.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-11 position-relative">
                <div class="event-arrow event-prev"><i class="fas fa-chevron-left"></i></div>
                <div class="event-arrow event-next"><i class="fas fa-chevron-right"></i></div>
                
                <div class="events-slick-carousel">
                    <?php if(!empty($events)): foreach ($events as $row): ?>
                        <?php $displayDate = date("M d, Y", strtotime($row['event_start_date'])); ?>
                        <div class="event-slide-item">
                            <div class="event-slide-card">
                                <div class="event-img-wrapper">
                                    <img src="<?php echo htmlspecialchars($row['banner_path']); ?>" alt="Event Banner">
                                    <div class="event-icon-float">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.7); color: #fff; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem;">
                                        <?php echo htmlspecialchars($displayDate); ?>
                                    </div>
                                </div>
                                <div class="event-card-body">
                                    <h5><?php echo htmlspecialchars($row['title']); ?></h5>
                                    <p><?php echo htmlspecialchars($row['subtitle']); ?></p>
                                    <button class="event-btn" onclick="showEvent(this)"
                                         data-title="<?php echo htmlspecialchars($row['title'], ENT_QUOTES); ?>"
                                         data-desc="<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>"
                                         data-img="<?php echo htmlspecialchars($row['banner_path'], ENT_QUOTES); ?>"
                                         data-date="<?php echo htmlspecialchars($displayDate, ENT_QUOTES); ?>"
                                         data-loc="<?php echo htmlspecialchars($row['location'], ENT_QUOTES); ?>"
                                         data-time="<?php echo htmlspecialchars($row['event_time'], ENT_QUOTES); ?>"
                                         data-adm="<?php echo htmlspecialchars($row['admission'], ENT_QUOTES); ?>"
                                         data-high="<?php echo htmlspecialchars($row['highlights'], ENT_QUOTES); ?>"
                                         data-tag="<?php echo isset($row['subtitle']) ? htmlspecialchars($row['subtitle'], ENT_QUOTES) : ''; ?>">
                                         EXPLORE EVENTS <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <div class="text-center p-5 w-100"><p>No upcoming events at this time.</p></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($events)): ?>
        <div style="background: #fdfaf4; padding: 40px; border-radius: 12px; border: 1px solid #f0e8dc; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; align-items: center; justify-content: space-between; gap: 30px; margin-top: 50px;">
            <div style="display: flex; align-items: center; gap: 25px;">
                <div style="position: relative; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                    <div style="width: 50px; height: 50px; background: var(--primary-maroon, #8c1d1d); border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 2; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                        <i class="fas fa-calendar-alt" style="font-size: 1.3rem; color: #fff;"></i>
                    </div>
                </div>
                <div>
                    <h5 style="margin: 0; color: var(--primary-maroon, #8c1d1d); font-weight: 800; font-size: 1.3rem;">Want to see more?</h5>
                    <p style="margin: 5px 0 0 0; color: #555; font-size: 0.95rem;">Explore all upcoming events and activities organized by the Faculty Union.</p>
                </div>
            </div>
            <a href="includes/all/all_events.php" class="btn" style="background-color: var(--primary-maroon, #8c1d1d); color: white; border: 2px solid #d4af37; padding: 12px 35px; font-weight: 700; white-space: nowrap; border-radius: 6px; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px; transition: 0.3s; box-shadow: 0 4px 10px rgba(140, 29, 29, 0.2);">
                VIEW ALL EVENTS <i class="fas fa-arrow-right" style="margin-left: 5px; font-size: 0.9em;"></i>
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>


<div id="eventDetailOverlay" class="custom-modal-overlay">
    <div class="modal-card">
        <span class="modal-close-btn" onclick="hideEvent()"><i class="fas fa-times"></i></span>
        
        <div class="modal-content-wrap">
            <div class="modal-left">
                <img id="modalImg" src="">
                
                <div class="mt-4" id="highContainer">
                    <h6 class="fw-bold mb-2" style="color: var(--primary-maroon, #8c1d1d); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Event Highlights</h6>
                    <p id="modalHigh" class="text-muted small" style="white-space: pre-wrap; line-height: 1.6;"></p>
                </div>
            </div>
            
            <div class="modal-right">
                <h3 id="modalTitle" class="modal-title-main"></h3>
                <div id="modalTagline" class="modal-tagline"></div>
                
                <div class="modal-info-grid">
                    <div class="info-item">
                        <i class="fas fa-calendar-check"></i>
                        <div><strong>Date</strong><span id="modaldate"></span></div>
                    </div>
                    <div class="info-item">
                        <i class="far fa-clock"></i>
                        <div><strong>Time</strong><span id="modalTime"></span></div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div><strong>Location</strong><span id="modalLoc"></span></div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-ticket-alt"></i>
                        <div><strong>Entry</strong><span id="modalAdm"></span></div>
                    </div>
                </div>
                
                <div class="modal-desc-box">
                    <h6>About the Event</h6>
                    <p id="modalDesc"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// --- Modal Logic ---
function showEvent(element) {
    document.getElementById('modalTitle').innerText = element.getAttribute('data-title');
    document.getElementById('modalDesc').innerText = element.getAttribute('data-desc');
    document.getElementById('modalImg').src = element.getAttribute('data-img');
    document.getElementById('modalLoc').innerText = element.getAttribute('data-loc');
    document.getElementById('modalTime').innerText = element.getAttribute('data-time');
    document.getElementById('modalAdm').innerText = element.getAttribute('data-adm');
    document.getElementById('modaldate').innerText = element.getAttribute('data-date');
    document.getElementById('modalHigh').innerText = element.getAttribute('data-high');
    
    const tagEl = document.getElementById('modalTagline');
    const tagline = element.getAttribute('data-tag');
    
    if(tagline && tagline.trim() !== "") {
        tagEl.innerText = tagline;
        tagEl.style.display = "block";
    } else {
        tagEl.style.display = "none";
    }

    const highEl = document.getElementById('modalHigh');
    const highContainer = document.getElementById('highContainer');
    const highlights = element.getAttribute('data-high');
    if(highlights && highlights.trim() !== "") {
        highEl.innerText = highlights;
        highContainer.style.display = "block";
    } else {
        highContainer.style.display = "none";
    }

    document.getElementById('eventDetailOverlay').style.display = 'flex';
    document.body.style.overflow = 'hidden'; 
}

function hideEvent() {
    document.getElementById('eventDetailOverlay').style.display = 'none';
    document.body.style.overflow = 'auto'; 
}

window.onclick = function(event) {
    if (event.target == document.getElementById('eventDetailOverlay')) {
        hideEvent();
    }
}

document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        hideEvent();
    }
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script>
$(document).ready(function(){
    $('.events-slick-carousel').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3500,
        pauseOnHover: true,
        prevArrow: $('.event-prev'),
        nextArrow: $('.event-next'),
        responsive: [
            { breakpoint: 992, settings: { slidesToShow: 2 } },
            { breakpoint: 768, settings: { slidesToShow: 1 } }
        ]
    });
});
</script>
