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

// Handle Topic Actions (Add/Edit/Delete)
if (isset($_GET['delete_topic'])) {
    // Delete image first if exists
    $stmt = $db->prepare("SELECT image_path FROM about_topics WHERE id = ?");
    $stmt->execute([$_GET['delete_topic']]);
    $topic = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($topic && !empty($topic['image_path']) && file_exists('../' . $topic['image_path'])) {
        unlink('../' . $topic['image_path']);
    }

    $stmt = $db->prepare("DELETE FROM about_topics WHERE id = ?");
    $stmt->execute([$_GET['delete_topic']]);
    header("Location: manage_about_topics.php?msg=Deleted");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_topic'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $display_order = (int)$_POST['display_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $image_path = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Ensure upload directory exists
        if (!is_dir('uploads/topics/')) {
            mkdir('uploads/topics/', 0777, true);
        }
        $upload_dir = 'uploads/topics/';
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = 'admins/' . $target_file;
        }
    }

    if (!empty($title)) {
        $stmt = $db->prepare("INSERT INTO about_topics (title, content, image_path, display_order, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $content, $image_path, $display_order, $is_active]);
        header("Location: manage_about_topics.php?msg=Added");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_topic'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $display_order = (int)$_POST['display_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $image_path = $_POST['existing_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        if (!is_dir('uploads/topics/')) {
            mkdir('uploads/topics/', 0777, true);
        }
        $upload_dir = 'uploads/topics/';
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

    $stmt = $db->prepare("UPDATE about_topics SET title = ?, content = ?, image_path = ?, display_order = ?, is_active = ? WHERE id = ?");
    $stmt->execute([$title, $content, $image_path, $display_order, $is_active, $id]);
    header("Location: manage_about_topics.php?msg=Updated");
    exit();
}

// ==========================================
// 1. Handle Vision Update (Single Textarea)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_vision'])) {
    $new_vision = $_POST['vision'];
    $stmt = $db->prepare("UPDATE union_info SET vision = ? WHERE id = 1");
    $stmt->execute([$new_vision]);
    $success = "Vision updated successfully!";
}

// ==========================================
// 2. Handle Objective Actions (Add/Edit/Delete)
// ==========================================
if (isset($_GET['delete_obj'])) {
    $stmt = $db->prepare("DELETE FROM objectives WHERE id = ?");
    $stmt->execute([$_GET['delete_obj']]);
    header("Location: manage_about_topics.php?msg=Objective+Deleted");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_objective'])) {
    $content = trim($_POST['content']);
    if (!empty($content)) {
        $stmt = $db->prepare("INSERT INTO objectives (content) VALUES (?)");
        $stmt->execute([$content]);
        header("Location: manage_about_topics.php?msg=Objective+Added");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_objective'])) {
    $stmt = $db->prepare("UPDATE objectives SET content = ? WHERE id = ?");
    $stmt->execute([trim($_POST['content']), $_POST['id']]);
    header("Location: manage_about_topics.php?msg=Objective+Updated");
    exit();
}

// Fetch Current Data
$topics = $db->query("SELECT * FROM about_topics ORDER BY display_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
$vision = $db->query("SELECT vision FROM union_info WHERE id = 1")->fetchColumn();
$objectives = $db->query("SELECT * FROM objectives ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Include layout files AFTER all redirects and logic
require_once('sidebar.php');
$navtext = "Manage About Content";
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
        <h5 class="mb-3"><i class="fas fa-eye mr-2"></i> Union Vision</h5>
        <form method="POST">
            <div class="form-group">
                <textarea name="vision" class="form-control" rows="4" required><?php echo htmlspecialchars($vision); ?></textarea>
            </div>
            <button type="submit" name="update_vision" class="btn btn-maroon px-4">Update Vision</button>
        </form>
    </div>

    <div class="card section-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5><i class="fas fa-list-ul mr-2"></i> Union Objectives</h5>
            <button class="btn btn-sm btn-maroon" data-toggle="modal" data-target="#addObjModal"><i class="fas fa-plus"></i> Add Objective</button>
        </div>
        
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="10%">#</th>
                    <th>Objective</th>
                    <th width="20%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($objectives as $index => $obj): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo nl2br(htmlspecialchars($obj['content'])); ?></td>
                    <td class="action-btns">
                        <button class="btn btn-sm btn-outline-info edit-obj-btn" 
                                data-id="<?php echo $obj['id']; ?>"
                                data-content="<?php echo htmlspecialchars($obj['content']); ?>"
                                data-toggle="modal" data-target="#editObjModal"><i class="fas fa-edit"></i> Edit</button>
                        <a href="?delete_obj=<?php echo $obj['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i> Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card section-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5><i class="fas fa-file-alt mr-2"></i> About Page Content</h5>
            <button class="btn btn-sm btn-maroon" data-toggle="modal" data-target="#addModal"><i class="fas fa-plus"></i> Add New Content</button>
        </div>
        
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="5%">Order</th>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th width="20%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($topics as $topic): ?>
                <tr>
                    <td><?php echo $topic['display_order']; ?></td>
                    <td><?php echo htmlspecialchars($topic['title']); ?></td>
                    <td>
                        <?php if(!empty($topic['image_path'])): ?>
                            <img src="../<?php echo htmlspecialchars($topic['image_path']); ?>" alt="img" style="max-height: 50px;">
                        <?php else: ?>
                            <span class="text-muted">None</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($topic['is_active']): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="action-btns">
                        <button class="btn btn-sm btn-outline-info edit-btn" 
                                data-id="<?php echo $topic['id']; ?>"
                                data-title="<?php echo htmlspecialchars($topic['title']); ?>"
                                data-content="<?php echo htmlspecialchars($topic['content']); ?>"
                                data-order="<?php echo $topic['display_order']; ?>"
                                data-active="<?php echo $topic['is_active']; ?>"
                                data-image="<?php echo htmlspecialchars($topic['image_path']); ?>"
                                data-toggle="modal" data-target="#editModal"><i class="fas fa-edit"></i> Edit</button>
                        <a href="?delete_topic=<?php echo $topic['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this topic?')"><i class="fas fa-trash"></i> Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($topics)): ?>
                <tr>
                    <td colspan="5" class="text-center">No dynamic content found on About page. Add one!</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" enctype="multipart/form-data">
            <div class="modal-header"><h5>Add New Content</h5></div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Title</label>
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
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Display Order</label>
                        <input type="number" name="display_order" class="form-control" value="0">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="d-block">Status</label>
                        <div class="custom-control custom-switch mt-2">
                            <input type="checkbox" class="custom-control-input" id="add_active" name="is_active" checked>
                            <label class="custom-control-label" for="add_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="add_topic" class="btn btn-maroon">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" enctype="multipart/form-data">
            <div class="modal-header"><h5>Edit Content</h5></div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="existing_image" id="edit_existing_image">
                <div class="form-group">
                    <label>Title</label>
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
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Display Order</label>
                        <input type="number" name="display_order" id="edit_order" class="form-control" value="0">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="d-block">Status</label>
                        <div class="custom-control custom-switch mt-2">
                            <input type="checkbox" class="custom-control-input" id="edit_active" name="is_active">
                            <label class="custom-control-label" for="edit_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" name="edit_topic" class="btn btn-maroon">Update</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addObjModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST">
            <div class="modal-header"><h5>Add New Objective</h5></div>
            <div class="modal-body">
                <textarea name="content" class="form-control" rows="4" placeholder="Enter objective content..." required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="add_objective" class="btn btn-maroon">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editObjModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST">
            <div class="modal-header"><h5>Edit Objective</h5></div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_obj_id">
                <textarea name="content" id="edit_obj_content" class="form-control" rows="4" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" name="edit_objective" class="btn btn-maroon">Update</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $('.edit-btn').on('click', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_title').val($(this).data('title'));
        $('#edit_content').val($(this).data('content'));
        $('#edit_order').val($(this).data('order'));
        $('#edit_existing_image').val($(this).data('image'));
        
        if ($(this).data('active') == 1) {
            $('#edit_active').prop('checked', true);
        } else {
            $('#edit_active').prop('checked', false);
        }
    });

    $('.edit-obj-btn').on('click', function() {
        $('#edit_obj_id').val($(this).data('id'));
        $('#edit_obj_content').val($(this).data('content'));
    });
</script>
</body>
</html>
