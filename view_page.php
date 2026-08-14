<?php
require_once('./config/database.php');
$database = new Database();
$db = $database->getConnection();

$page_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $db->prepare("SELECT * FROM dynamic_pages WHERE id = ? AND is_active = 1");
$stmt->execute([$page_id]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$page) {
    header("Location: index.php");
    exit();
}

$stmt_posts = $db->prepare("SELECT * FROM dynamic_posts WHERE page_id = ? AND is_active = 1 ORDER BY id DESC");
$stmt_posts->execute([$page_id]);
$posts = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<?php require_once('./includes/head.php');?>
<body class="index-page">

<main class="main">
    <!-- Header Section -->
    <?php include('./includes/header.php');?>
    <!-- Search Section -->
    <style>
        .page-header-bar {
            position: sticky;
            top: 70px;
            z-index: 998;
            background: rgba(251, 248, 244, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .search-container {
            position: relative;
            width: 300px;
            max-width: 100%;
        }
        .search-container .form-control {
            border-radius: 20px;
            padding-left: 40px;
            border: 1px solid #e5ddd7;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .search-container .form-control:focus {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(140, 29, 29, 0.15);
            border-color: #8c1d1d;
        }
        .search-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            z-index: 4;
        }
    </style>
    <div class="page-header-bar d-flex align-items-center px-3 px-md-4">
        <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" id="postSearch" class="form-control" placeholder="Search posts...">
        </div>
    </div>


    <!-- Posts Section -->
    <section class="section bg-light pt-5 pb-5">
        <div class="container" data-aos="fade-up">
            <?php if ($posts): ?>
                <div class="row justify-content-center" id="postContainer">
                    <div class="col-12 col-lg-8">
                        <?php foreach ($posts as $post): ?>
                            <div class="card shadow-sm border-0 mb-4 post-item" style="border-radius: 12px; overflow: hidden;">
                                <div class="card-body p-4">
                                    <!-- Header: Title and Date -->
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-circle bg-maroon text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background-color: #8c1d1d; flex-shrink: 0;">
                                            <i class="fas fa-bullhorn fs-5"></i>
                                        </div>
                                        <div>
                                            <h5 class="card-title text-maroon post-title mb-1" style="color: #8c1d1d; font-weight: 700; font-size: 1.1rem; margin:0;">
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </h5>
                                            <small class="text-muted" style="font-size: 0.85rem;">
                                                <?php echo date('F j, Y \a\t g:i A', strtotime($post['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <!-- Content -->
                                    <p class="card-text post-content mb-3" style="color: #333; font-size: 1rem; line-height: 1.6;">
                                        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                                    </p>
                                </div>
                                
                                <!-- Image (Bottom) -->
                                <?php if (!empty($post['image_path'])): ?>
                                    <div style="border-top: 1px solid #eee;">
                                        <img src="<?php echo htmlspecialchars($post['image_path']); ?>" class="img-fluid w-100" alt="Post Image" style="object-fit: contain; max-height: 600px; background-color: #f8f9fa;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center mt-5 mb-5">
                    <h5 class="text-muted">No posts available yet.</h5>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Post Details Modal -->
    <div class="modal fade" id="postModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 p-md-5" id="postModalBody">
                    <!-- Dynamic Content -->
                </div>
            </div>
        </div>
    </div>

</main>

<!-- Footer Section -->
<?php include('./includes/footer_simple.php');?>

<!-- Scroll Top Button -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center active"><i class="bi bi-arrow-up-short"></i></a>

<!-- Preloader -->
<div id="preloader"></div>

<!-- Vendor JS Files -->
<?php include('./includes/scripts.php');?>
<script src="assets/jscripts/main.js"></script>
<script src="assets/jscripts/ind.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("postSearch");
        if(searchInput) {
            searchInput.addEventListener("keyup", function() {
                const filter = searchInput.value.toLowerCase();
                const posts = document.querySelectorAll(".post-item");

                posts.forEach(function(post) {
                    const title = post.querySelector(".post-title").innerText.toLowerCase();
                    const content = post.querySelector(".post-content").innerText.toLowerCase();
                    if (title.indexOf(filter) > -1 || content.indexOf(filter) > -1) {
                        post.style.display = "";
                    } else {
                        post.style.display = "none";
                    }
                });
            });
        }
    });

    function viewPostDetails(element) {
        const title = element.getAttribute('data-title');
        const img = element.getAttribute('data-img');
        const date = element.getAttribute('data-date');
        const content = element.getAttribute('data-content');

        let imgHtml = '';
        if (img && img.trim() !== '') {
            imgHtml = `<img src="${img}" class="img-fluid rounded shadow-sm mb-4" style="width:100%; max-height:450px; object-fit:cover;" alt="Post Image">`;
        }

        const formattedContent = content.replace(/\n/g, '<br>');

        const modalBody = document.getElementById('postModalBody');
        modalBody.innerHTML = `
            ${imgHtml}
            <h3 class="fw-bold mb-2" style="color: #8c1d1d;">${title}</h3>
            <p class="text-muted small mb-4"><i class="bi bi-calendar3"></i> ${date}</p>
            <div style="font-size: 1.05rem; line-height: 1.7; color: #444;">
                ${formattedContent}
            </div>
        `;

        const postModal = new bootstrap.Modal(document.getElementById('postModal'));
        postModal.show();
    }
</script>


</body>
</html>
