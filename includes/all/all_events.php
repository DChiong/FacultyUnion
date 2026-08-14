<?php
require_once(__DIR__ . '/../../config/database.php');
$database = new Database();
$db = $database->getConnection();

$events = [];
$assetBase = '../../';
$currentPage = basename($_SERVER['PHP_SELF']);

function resolveAssetPath($path, $basePath)
{
    $path = trim((string) $path);
    if ($path === '') {
        return $basePath . 'images/facultyunion.png';
    }

    if (preg_match('~^(?:https?:)?//~i', $path) || strpos($path, '/') === 0) {
        return $path;
    }

    return $basePath . ltrim($path, './');
}

try {
    if ($db instanceof PDO) {
        $query = "SELECT title, subtitle, event_start_date, banner_path, location, event_time, admission, description, highlights, created_at FROM events ORDER BY event_start_date DESC, created_at DESC";
        $events = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $events = [];
}

$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Events - Faculty Union</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="../../images/facultyunion.png">

    <style>
        :root {
            --maroon: #8c1d1d;
            --maroon-dark: #671414;
            --bg: #f5f2ed;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --line: #e5ddd7;
        }

        * { box-sizing: border-box; }

        body {
            background:
                radial-gradient(circle at top left, rgba(140, 29, 29, 0.08), transparent 32%),
                linear-gradient(180deg, #fbf8f4 0%, #f3ede6 100%);
            color: var(--text);
            overflow-x: hidden;
        }

        .content-wrap {
            margin-left: 0;
            width: 100%;
            min-height: 100vh;
        }

        .gallery-header {
            position: sticky;
            top: 70px;
            z-index: 999;
            background: rgba(251, 248, 244, 0.95);
            backdrop-filter: blur(10px);
        }

        .search-container {
            position: relative;
            width: 300px;
        }

        .search-container .form-control {
            border-radius: 20px;
            padding-left: 40px;
        }

        .search-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
        }

        .filter-chip {
            border: 1px solid var(--line);
            background: #fff;
            color: #4b5563;
            border-radius: 999px;
            padding: 10px 16px;
            font-weight: 700;
            transition: 0.2s ease;
        }

        .filter-chip:hover,
        .filter-chip.active {
            background: var(--maroon);
            color: #fff;
            border-color: var(--maroon);
        }

        .gallery-body {
            padding: 20px;
        }

        .event-card {
            background: var(--card);
            border: 1px solid rgba(229, 221, 215, 0.95);
            border-radius: 18px;
            overflow: hidden;
            height: 100%;
            box-shadow: 0 12px 26px rgba(31, 41, 55, 0.05);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            cursor: pointer;
        }

        .event-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(31, 41, 55, 0.1);
        }

        .media-wrapper {
            position: relative;
            width: 100%;
            padding-top: 56.25%;
            background: linear-gradient(135deg, #111827, #374151);
        }

        .media-wrapper img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .media-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.05), rgba(0, 0, 0, 0.55));
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            padding: 14px;
            color: #fff;
        }

        .event-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .event-badge.upcoming { background: rgba(16, 185, 129, 0.95); color: #fff; }
        .event-badge.past { background: rgba(107, 114, 128, 0.95); color: #fff; }

        .event-body {
            padding: 16px;
        }

        .event-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .event-subtitle {
            color: var(--muted);
            font-size: 0.92rem;
            margin-bottom: 14px;
            min-height: 42px;
        }

        .event-meta {
            display: grid;
            gap: 8px;
        }

        .meta-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.86rem;
            color: #4b5563;
            background: #faf7f4;
            border: 1px solid #f0e8e1;
            border-radius: 12px;
            padding: 9px 11px;
        }

        .meta-row i { color: var(--maroon); width: 16px; text-align: center; }



        .modal-content {
            border-radius: 24px;
            overflow: hidden;
        }

        .detail-info {
            font-size: 0.9rem;
            margin-bottom: 10px;
            padding: 10px 12px;
            background: #f8f8f8;
            border-radius: 10px;
        }

        .detail-info i {
            color: var(--maroon);
            width: 18px;
            margin-right: 8px;
        }

        .empty-state {
            background: rgba(255, 255, 255, 0.9);
            border: 1px dashed var(--line);
            border-radius: 22px;
            padding: 48px 20px;
            text-align: center;
            color: var(--muted);
        }
    </style>
</head>
<body>
<?php 
define('BASE_PATH', '../../');
include '../../includes/header.php';
?>

<div class="content-wrap">
    <div class="gallery-header d-flex flex-wrap align-items-center justify-content-between px-3 px-md-4 py-3 gap-3">
        <div class="search-container position-relative">
            <i class="fas fa-search"></i>
            <input type="text" id="eventSearch" class="form-control" placeholder="Search events..." onkeyup="applyFilters()">
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <button class="filter-chip active" type="button" onclick="setCategory('all', this)">All</button>
            <button class="filter-chip" type="button" onclick="setCategory('upcoming', this)">Upcoming</button>
            <button class="filter-chip" type="button" onclick="setCategory('past', this)">Past</button>
            <button class="filter-chip" type="button" onclick="setCategory('this_year', this)">This Year</button>
        </div>
    </div>

    <div class="gallery-body pt-3">
        <div class="row g-3 g-md-4" id="events-grid">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <?php
                        $eventDate = $event['event_start_date'] ?? '';
                        $eventStatus = ($eventDate !== '' && $eventDate >= $today) ? 'upcoming' : 'past';
                        $displayDate = $eventDate ? date('M d, Y', strtotime($eventDate)) : 'TBA';
                        $title = $event['title'] ?? '';
                        $subtitle = $event['subtitle'] ?? '';
                        $location = $event['location'] ?? '';
                        $time = $event['event_time'] ?? '';
                        $admission = $event['admission'] ?? '';
                        $description = $event['description'] ?? '';
                        $highlights = $event['highlights'] ?? '';
                        $image = resolveAssetPath($event['banner_path'] ?? '', $assetBase);
                        $cardData = htmlspecialchars(json_encode($event), ENT_QUOTES, 'UTF-8');
                    ?>
                    <div class="col-12 col-md-6 col-lg-4 event-card-wrap"
                         data-type="event"
                         data-event-status="<?php echo htmlspecialchars($eventStatus, ENT_QUOTES, 'UTF-8'); ?>"
                         data-title="<?php echo htmlspecialchars(strtolower($title), ENT_QUOTES, 'UTF-8'); ?>"
                         data-date="<?php echo htmlspecialchars($displayDate, ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="event-card" onclick="viewEventDetails(<?php echo $cardData; ?>)">
                            <div class="media-wrapper">
                                <img src="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="media-overlay">
                                    <span class="event-badge <?php echo $eventStatus; ?>">
                                        <i class="fas fa-circle" style="font-size: 0.45rem;"></i>
                                        <?php echo strtoupper($eventStatus); ?>
                                    </span>
                                    <span class="event-badge" style="background: rgba(255,255,255,0.14); color: #fff;">
                                        <?php echo htmlspecialchars($displayDate, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="event-body">
                                <div class="event-title text-truncate"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php if ($subtitle): ?>
                                    <div class="event-subtitle"><?php echo htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                                <div class="event-meta">
                                    <div class="meta-row">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo htmlspecialchars($location ?: 'Location not set', ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                    <div class="meta-row">
                                        <i class="fas fa-clock"></i>
                                        <span><?php echo htmlspecialchars($time ?: 'Time not set', ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-calendar-xmark fa-2x mb-3" style="color: var(--maroon);"></i>
                        <h5 class="fw-bold mb-2">No events available</h5>
                        <p class="mb-0">There are no records in the events table yet.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4" id="eventModalBody"></div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
let currentCategory = 'all';



function setCategory(category, element) {
    document.querySelectorAll('.filter-chip').forEach(link => link.classList.remove('active'));
    if (element) {
        element.classList.add('active');
    }
    currentCategory = category;


    applyFilters();
}

function applyFilters() {
    const searchTerm = document.getElementById('eventSearch').value.toLowerCase();
    const cards = document.querySelectorAll('.event-card-wrap');

    cards.forEach(card => {
        const title = (card.getAttribute('data-title') || '');
        const eventStatus = card.getAttribute('data-event-status');
        const date = card.getAttribute('data-date') || '';

        const matchesSearch = title.includes(searchTerm) || date.toLowerCase().includes(searchTerm);
        let matchesCategory = currentCategory === 'all';

        if (currentCategory === 'upcoming' || currentCategory === 'past') {
            matchesCategory = eventStatus === currentCategory;
        } else if (currentCategory === 'this_year') {
            matchesCategory = date.includes(new Date().getFullYear().toString());
        }

        card.style.display = (matchesSearch && matchesCategory) ? 'block' : 'none';
    });
}

function viewEventDetails(eventData) {
    const modalBody = document.getElementById('eventModalBody');
    const status = (eventData.event_start_date && eventData.event_start_date >= '<?php echo $today; ?>') ? 'upcoming' : 'past';
    const dateOptions = { month: 'long', day: '2-digit', year: 'numeric' };
    const displayDate = eventData.event_start_date ? new Date(eventData.event_start_date).toLocaleDateString('en-US', dateOptions) : 'TBA';

    const mediaHtml = `<img src="${escapeHtml(resolveAssetUrl(eventData.banner_path || 'images/facultyunion.png'))}" class="img-fluid rounded-4 shadow-sm" alt="${escapeHtml(eventData.title || '')}">`;
    const infoHtml = `
        <div class="detail-info"><i class="fas fa-calendar-alt"></i> <strong>Date:</strong> ${escapeHtml(displayDate)}</div>
        <div class="detail-info"><i class="fas fa-clock"></i> <strong>Time:</strong> ${escapeHtml(eventData.event_time || 'Not set')}</div>
        <div class="detail-info"><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> ${escapeHtml(eventData.location || 'Not set')}</div>
        <div class="detail-info"><i class="fas fa-ticket-alt"></i> <strong>Admission:</strong> ${escapeHtml(eventData.admission || 'Not set')}</div>
        <div class="detail-info"><i class="fas fa-circle"></i> <strong>Status:</strong> ${escapeHtml(status.toUpperCase())}</div>
    `;

    modalBody.innerHTML = `
        <div class="row g-3">
            <div class="col-md-5">
                ${mediaHtml}
            </div>
            <div class="col-md-7">
                <h4 class="fw-bold mb-1" style="color: var(--maroon);">${escapeHtml(eventData.title || '')}</h4>
                ${eventData.subtitle ? `<p class="text-muted small mb-3">${escapeHtml(eventData.subtitle)}</p>` : ''}
                ${infoHtml}
                <hr>
                <p class="small text-secondary mb-3">${escapeHtml(eventData.description || 'No description available.')}</p>
                ${eventData.highlights ? `<div class="detail-info"><i class="fas fa-star"></i> <strong>Highlights:</strong><br>${escapeHtml(eventData.highlights)}</div>` : ''}
            </div>
        </div>
    `;

    new bootstrap.Modal(document.getElementById('eventDetailsModal')).show();
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

const assetBase = "../../";

function resolveAssetUrl(path) {
    const value = String(path || '').trim();

    if (!value) {
        return `${assetBase}images/facultyunion.png`;
    }

    if (/^(?:https?:)?\/\//i.test(value) || value.startsWith('/')) {
        return value;
    }

    return `${assetBase}${value.replace(/^\.\/?/, '')}`;
}
</script>
<?php include('../footer_simple.php'); ?>
</body>
</html>
