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

// Handle Page Actions (Add/Edit/Delete)
if (isset($_GET['delete_page'])) {
    $page_id = (int)$_GET['delete_page'];
    
    // Delete image first if exists
    $stmt = $db->prepare("SELECT image_path FROM dynamic_pages WHERE id = ?");
    $stmt->execute([$page_id]);
    $page = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($page && !empty($page['image_path']) && file_exists('../' . $page['image_path'])) {
        unlink('../' . $page['image_path']);
    }

    $stmt = $db->prepare("DELETE FROM dynamic_pages WHERE id = ?");
    $stmt->execute([$page_id]);
    
    // Also delete from menu_items
    $stmt = $db->prepare("DELETE FROM menu_items WHERE url = ?");
    $stmt->execute(['view_page.php?id=' . $page_id]);
    
    header("Location: manage_pages.php?msg=Deleted");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_page'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $display_order = (int)$_POST['display_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $image_path = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = 'uploads/pages/';
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = 'admins/' . $target_file;
        }
    }

    if (!empty($title)) {
        $stmt = $db->prepare("INSERT INTO dynamic_pages (title, content, image_path, display_order, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $content, $image_path, $display_order, $is_active]);
        $new_page_id = $db->lastInsertId();
        
        // Also insert into menu_items
        $stmt = $db->prepare("INSERT INTO menu_items (title, url, active_check, display_order, is_active) VALUES (?, ?, ?, ?, ?)");
        // Add after the last item by default, or use a high number
        $stmt->execute([strtoupper($title), 'view_page.php?id=' . $new_page_id, 'view_page.php', 100, $is_active]);

        header("Location: manage_pages.php?msg=Added");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_page'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $display_order = (int)$_POST['display_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $image_path = $_POST['existing_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = 'uploads/pages/';
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

    $stmt = $db->prepare("UPDATE dynamic_pages SET title = ?, content = ?, image_path = ?, display_order = ?, is_active = ? WHERE id = ?");
    $stmt->execute([$title, $content, $image_path, $display_order, $is_active, $id]);
    
    // Also update title and active status in menu_items
    $stmt = $db->prepare("UPDATE menu_items SET title = ?, is_active = ?, display_order = ? WHERE url = ?");
    $stmt->execute([strtoupper($title), $is_active, $display_order, 'view_page.php?id=' . $id]);
    
    header("Location: manage_pages.php?msg=Updated");
    exit();
}

// Navbar Handle Actions
if (isset($_GET['delete_item'])) {
    $item_id = (int)$_GET['delete_item'];
    
    // Check if it's a static page
    $stmt = $db->prepare("SELECT url FROM menu_items WHERE id = ?");
    $stmt->execute([$item_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($item && strpos($item['url'], 'index.php') === false && strpos($item['url'], 'officers.php') === false) {
        $stmt = $db->prepare("DELETE FROM menu_items WHERE id = ?");
        $stmt->execute([$item_id]);
        header("Location: manage_pages.php?msg=Navbar+Item+Deleted");
        exit();
    } else {
        header("Location: manage_pages.php?msg=Cannot+delete+core+static+pages");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {
    $title = trim($_POST['title']);
    $url = trim($_POST['url']);
    $active_check = trim($_POST['active_check']);
    $display_order = (int)$_POST['display_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    if (empty($url)) $url = '#';
    
    if (!empty($title)) {
        $stmt = $db->prepare("INSERT INTO menu_items (title, url, active_check, display_order, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([strtoupper($title), $url, $active_check, $display_order, $is_active]);
        header("Location: manage_pages.php?msg=Navbar+Item+Added");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_item'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $url = trim($_POST['url']);
    $active_check = trim($_POST['active_check']);
    $display_order = (int)$_POST['display_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    if (empty($url)) $url = '#';

    if (!empty($title)) {
        $stmt = $db->prepare("UPDATE menu_items SET title = ?, url = ?, active_check = ?, display_order = ?, is_active = ? WHERE id = ?");
        $stmt->execute([strtoupper($title), $url, $active_check, $display_order, $is_active, $id]);
        header("Location: manage_pages.php?msg=Navbar+Item+Updated");
        exit();
    }
}

// Fetch Current Data
$pages = $db->query("SELECT * FROM dynamic_pages ORDER BY display_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
$menu_items = $db->query("SELECT * FROM menu_items ORDER BY display_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Include layout files AFTER all redirects and logic
require_once('sidebar.php');
$navtext = "Manage Pages";
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

    <!-- Unified Navbar & Pages Table -->

    <div class="card section-card p-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5><i class="fas fa-list mr-2"></i> Navbar Items</h5>
            <button class="btn btn-sm btn-maroon" data-toggle="modal" data-target="#addModal"><i class="fas fa-plus"></i> Add New Page</button>
        </div>
        
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="10%">Order</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th width="20%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($menu_items as $item): ?>
                <tr>
                    <td><?php echo $item['display_order']; ?></td>
                    <td><strong><?php echo htmlspecialchars($item['title']); ?></strong></td>
                    <td>
                        <?php if($item['is_active']): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="action-btns">
                        <?php 
                        $is_dynamic = false;
                        $dyn_page = null;
                        if (strpos($item['url'], 'view_page.php?id=') !== false) {
                            $is_dynamic = true;
                            $id = str_replace('view_page.php?id=', '', $item['url']);
                            foreach($pages as $p) {
                                if ($p['id'] == $id) {
                                    $dyn_page = $p;
                                    break;
                                }
                            }
                        }
                        
                        if ($is_dynamic && $dyn_page): 
                        ?>
                        <button class="btn btn-sm btn-outline-info edit-btn" 
                                data-id="<?php echo $dyn_page['id']; ?>"
                                data-title="<?php echo htmlspecialchars($dyn_page['title']); ?>"
                                data-content="<?php echo htmlspecialchars($dyn_page['content']); ?>"
                                data-order="<?php echo $item['display_order']; ?>"
                                data-active="<?php echo $dyn_page['is_active']; ?>"
                                data-image="<?php echo htmlspecialchars($dyn_page['image_path']); ?>"
                                data-toggle="modal" data-target="#editModal"><i class="fas fa-edit"></i> Edit Page</button>
                        <a href="?delete_page=<?php echo $dyn_page['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this page entirely?')"><i class="fas fa-trash"></i> Delete</a>
                        <?php else: ?>
                        <button class="btn btn-sm btn-outline-info edit-nav-btn" 
                                data-id="<?php echo $item['id']; ?>"
                                data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                data-url="<?php echo htmlspecialchars($item['url']); ?>"
                                data-active_check="<?php echo htmlspecialchars($item['active_check']); ?>"
                                data-order="<?php echo $item['display_order']; ?>"
                                data-active="<?php echo $item['is_active']; ?>"
                                data-toggle="modal" data-target="#editNavModal"><i class="fas fa-edit"></i> Edit</button>
                        <?php 
                        $is_static = (strpos($item['url'], 'index.php') !== false || strpos($item['url'], 'officers.php') !== false);
                        if (!$is_static): 
                        ?>
                        <a href="?delete_item=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this menu item?')"><i class="fas fa-trash"></i> Delete</a>
                        <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($menu_items)): ?>
                <tr>
                    <td colspan="5" class="text-center">No navbar items found. Add one!</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" enctype="multipart/form-data">
            <div class="modal-header"><h5>Add New Page Section</h5></div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" class="form-control" rows="5" required></textarea>
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
                <button type="submit" name="add_page" class="btn btn-maroon">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" enctype="multipart/form-data">
            <div class="modal-header"><h5>Edit Page Section</h5></div>
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
                <button type="submit" name="edit_page" class="btn btn-maroon">Update</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addNavModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST">
            <div class="modal-header"><h5>Add Navbar Link</h5></div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. MY PAGE">
                </div>
                <input type="hidden" name="url" value="">
                <input type="hidden" name="active_check" value="">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Display Order</label>
                        <input type="number" name="display_order" class="form-control" value="100">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="d-block">Status</label>
                        <div class="custom-control custom-switch mt-2">
                            <input type="checkbox" class="custom-control-input" id="add_nav_active" name="is_active" checked>
                            <label class="custom-control-label" for="add_nav_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="add_item" class="btn btn-maroon">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editNavModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST">
            <div class="modal-header"><h5>Edit Navbar Link</h5></div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_nav_id">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" id="edit_nav_title" class="form-control" required>
                </div>
                <input type="hidden" name="url" id="edit_nav_url">
                <input type="hidden" name="active_check" id="edit_nav_active_check">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Display Order</label>
                        <input type="number" name="display_order" id="edit_nav_order" class="form-control" value="0">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="d-block">Status</label>
                        <div class="custom-control custom-switch mt-2">
                            <input type="checkbox" class="custom-control-input" id="edit_nav_active" name="is_active">
                            <label class="custom-control-label" for="edit_nav_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" name="edit_item" class="btn btn-maroon">Update</button>
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

    $('.edit-nav-btn').on('click', function() {
        $('#edit_nav_id').val($(this).data('id'));
        $('#edit_nav_title').val($(this).data('title'));
        $('#edit_nav_url').val($(this).data('url'));
        $('#edit_nav_active_check').val($(this).data('active_check'));
        $('#edit_nav_order').val($(this).data('order'));
        
        if ($(this).data('active') == 1) {
            $('#edit_nav_active').prop('checked', true);
        } else {
            $('#edit_nav_active').prop('checked', false);
        }
    });
</script>
</body>
</html>
