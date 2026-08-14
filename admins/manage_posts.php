<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { 
    header("Location: ../auth/login.php"); 
    exit(); 
}

require_once('../config/database.php');
$database = new Database();
$db = $database->getConnection();

$success = "";

// Handle Post Actions (Add/Edit/Delete)
if (isset($_GET['delete_post'])) {
    // Delete image first if exists
    $stmt = $db->prepare("SELECT image_path FROM dynamic_posts WHERE id = ?");
    $stmt->execute([$_GET['delete_post']]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($post && !empty($post['image_path']) && file_exists('../' . $post['image_path'])) {
        unlink('../' . $post['image_path']);
    }

    $stmt = $db->prepare("DELETE FROM dynamic_posts WHERE id = ?");
    $stmt->execute([$_GET['delete_post']]);
    header("Location: manage_posts.php?msg=Deleted");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_post'])) {
    $page_id = (int)$_POST['page_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $image_path = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = 'uploads/posts/';
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = 'admins/' . $target_file;
        }
    }

    if (!empty($title) && $page_id > 0) {
        $stmt = $db->prepare("INSERT INTO dynamic_posts (page_id, title, content, image_path, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$page_id, $title, $content, $image_path, $is_active]);
        header("Location: manage_posts.php?msg=Added");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_post'])) {
    $id = $_POST['id'];
    $page_id = (int)$_POST['page_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $image_path = $_POST['existing_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = 'uploads/posts/';
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Remove old image
            if (!empty($image_path) && file_exists('../' . $image_path)) {
                unlink('../' . $image_path);
            }
            $image_path = 'admins/' . $target_file;
        }
    }

    if ($page_id > 0) {
        $stmt = $db->prepare("UPDATE dynamic_posts SET page_id = ?, title = ?, content = ?, image_path = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$page_id, $title, $content, $image_path, $is_active, $id]);
        header("Location: manage_posts.php?msg=Updated");
        exit();
    }
}

// Fetch Current Data
$pages = $db->query("SELECT id, title FROM dynamic_pages ORDER BY title ASC")->fetchAll(PDO::FETCH_ASSOC);

$posts = $db->query("
    SELECT p.*, dp.title as page_title 
    FROM dynamic_posts p 
    LEFT JOIN dynamic_pages dp ON p.page_id = dp.id 
    ORDER BY p.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Include layout files AFTER all redirects and logic
require_once('sidebar.php');
$navtext = "Manage Posts";
require_once('navbar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        :root { --maroon: #8c1d1d; --gold: #d4af37; }
        body { background-color: #f4f7f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
        .btn-maroon { background: var(--maroon); color: white; border: none; font-weight: 600; }
        .btn-maroon:hover { background: var(--gold); color: black; }
        .section-card { border: none; border-top: 5px solid var(--maroon); border-radius: 8px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); margin-bottom: 28px; }
        .table thead { background: var(--maroon); color: white; }
        .table tbody tr:nth-child(odd) { background: #ffffff; }
        .table tbody tr:nth-child(even) { background: #fbfbfb; }
        .modal-header { background: var(--maroon); color: #fff; border-top-left-radius: 8px; border-top-right-radius: 8px; }
        .modal-header h5 { margin: 0; font-weight: 600; }
        .form-control:focus { box-shadow: 0 0 0 0.15rem rgba(140,29,29,0.15); border-color: var(--maroon); }
        .action-btns .btn { margin-right:6px; }
    </style>
</head>
<body>

    <link rel="icon" href="../images/facultyunion.png">
<div class="main-content">

    <?php if($success || isset($_GET['msg'])):
        $msg = $success ?: (isset($_GET['msg']) ? $_GET['msg'] : 'Action successful');
    ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($msg); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card section-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5><i class="fas fa-edit mr-2"></i> Dynamic Posts</h5>
            <button class="btn btn-sm btn-maroon" data-toggle="modal" data-target="#addModal"><i class="fas fa-plus"></i> Add New Post</button>
        </div>
        
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="15%">Page Category</th>
                    <th>Post Title</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th width="20%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($posts as $post): ?>
                <tr>
                    <td><span class="badge badge-info"><?php echo htmlspecialchars($post['page_title']); ?></span></td>
                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                    <td>
                        <?php if(!empty($post['image_path'])): ?>
                            <img src="../<?php echo htmlspecialchars($post['image_path']); ?>" alt="img" style="max-height: 50px;">
                        <?php else: ?>
                            <span class="text-muted">None</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($post['is_active']): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="action-btns">
                        <button class="btn btn-sm btn-outline-info edit-btn" 
                                data-id="<?php echo $post['id']; ?>"
                                data-page="<?php echo $post['page_id']; ?>"
                                data-title="<?php echo htmlspecialchars($post['title']); ?>"
                                data-content="<?php echo htmlspecialchars($post['content']); ?>"
                                data-active="<?php echo $post['is_active']; ?>"
                                data-image="<?php echo htmlspecialchars($post['image_path']); ?>"
                                data-toggle="modal" data-target="#editModal"><i class="fas fa-edit"></i> Edit</button>
                        <a href="?delete_post=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this post?')"><i class="fas fa-trash"></i> Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($posts)): ?>
                <tr>
                    <td colspan="5" class="text-center">No posts found. Add one!</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" enctype="multipart/form-data">
            <div class="modal-header"><h5>Add New Post</h5></div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Select Page (Category)</label>
                    <select name="page_id" class="form-control" required>
                        <option value="">-- Select Page --</option>
                        <?php foreach($pages as $page): ?>
                            <option value="<?php echo $page['id']; ?>"><?php echo htmlspecialchars($page['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Post Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" class="form-control" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label>Image (Optional)</label>
                    <input type="file" name="image" class="form-control-file" accept="image/*">
                </div>
                <div class="form-group">
                    <label class="d-block">Status</label>
                    <div class="custom-control custom-switch mt-2">
                        <input type="checkbox" class="custom-control-input" id="add_active" name="is_active" checked>
                        <label class="custom-control-label" for="add_active">Active</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="add_post" class="btn btn-maroon">Save Post</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" enctype="multipart/form-data">
            <div class="modal-header"><h5>Edit Post</h5></div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="existing_image" id="edit_existing_image">
                <div class="form-group">
                    <label>Select Page (Category)</label>
                    <select name="page_id" id="edit_page_id" class="form-control" required>
                        <option value="">-- Select Page --</option>
                        <?php foreach($pages as $page): ?>
                            <option value="<?php echo $page['id']; ?>"><?php echo htmlspecialchars($page['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Post Title</label>
                    <input type="text" name="title" id="edit_title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" id="edit_content" class="form-control" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label>Image (Optional)</label>
                    <input type="file" name="image" class="form-control-file" accept="image/*">
                    <small class="form-text text-muted">Leave blank to keep existing image.</small>
                </div>
                <div class="form-group">
                    <label class="d-block">Status</label>
                    <div class="custom-control custom-switch mt-2">
                        <input type="checkbox" class="custom-control-input" id="edit_active" name="is_active">
                        <label class="custom-control-label" for="edit_active">Active</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" name="edit_post" class="btn btn-maroon">Update Post</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $('.edit-btn').on('click', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_page_id').val($(this).data('page'));
        $('#edit_title').val($(this).data('title'));
        $('#edit_content').val($(this).data('content'));
        $('#edit_existing_image').val($(this).data('image'));
        
        if ($(this).data('active') == 1) {
            $('#edit_active').prop('checked', true);
        } else {
            $('#edit_active').prop('checked', false);
        }
    });
</script>
</body>
</html>
