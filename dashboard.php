<?php
include 'config/db.php';

session_start();
// Prevent browser caching to fix back-button issue after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// --- 0. AUTO-MAINTENANCE ---
$conn->query("DELETE FROM notes WHERE is_trashed = 1 AND trashed_at < NOW() - INTERVAL 30 DAY AND user_id = $user_id");
$conn->query("DELETE FROM notebooks WHERE is_trashed = 1 AND trashed_at < NOW() - INTERVAL 30 DAY AND user_id = $user_id");

// --- INITIALIZE VARIABLES ---
$current_id = "";
$current_title = "";
$current_title_style = "";
$current_content_style = ""; // NEW
$current_content = "";
$current_notebook_name_display = "Loose Note"; // Default
$current_updated_at = "";
$editor_target_notebook_id = "";
$current_tags = ""; // NEW: Hold tags string
$search_query = "";
$filter_notebook_id = "";
$filter_notebook_name = "";
$view_mode = "all";

// --- 1. HANDLE POST ACTIONS ---

// A. SAVE NOTE
if (isset($_POST['save_note'])) {
    if (isset($_POST['is_trash_mode']) && $_POST['is_trash_mode'] == '1') {
        header("Location: dashboard.php?view=trash");
        exit();
    }
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $title_style = mysqli_real_escape_string($conn, $_POST['title_style']);
    $content_style = mysqli_real_escape_string($conn, $_POST['content_style'] ?? ''); // Fix Undefined Index
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $nb_id = $_POST['notebook_id'];
    $id = $_POST['id'];
    $nb_sql = ($nb_id == "" || $nb_id == "0") ? "NULL" : "'$nb_id'";

    $params = [];
    if (isset($_GET['notebook'])) {
        $params[] = "notebook=" . $_GET['notebook'];
    } elseif (isset($_GET['view']) && $_GET['view'] == 'notes') {
        $params[] = "view=notes";
    }

    // We always want to return to the edit view of the saved note
    if ($id != '') {
        $params[] = "edit=" . $id;
    }

    $redirect_url = "dashboard.php";
    if (!empty($params)) {
        $redirect_url .= "?" . implode("&", $params);
    }

    if (trim($title) === '' && trim($content) === '') {
        header("Location: " . str_replace("&edit=" . $id, "", $redirect_url));
        exit();
    }

    if ($id != '') {
        $sql = "UPDATE notes SET title='$title', title_style='$title_style', content_style='$content_style', content='$content', notebook_id=$nb_sql WHERE id=$id AND user_id=$user_id";
    } else {
        $sql = "INSERT INTO notes (title, title_style, content_style, content, notebook_id, user_id) VALUES ('$title', '$title_style', '$content_style', '$content', $nb_sql, $user_id)";
    }

    // Execute Save/Update
    if ($id != '') { // Update Mode
        $conn->query($sql);
    } else { // Insert Mode
        if ($conn->query($sql) === TRUE) {
            $id = $conn->insert_id; // Capture new ID
        }
    }

    // --- HANDLE TAGS ---
    if (isset($_POST['tags']) && $id) {
        $tags_input = $_POST['tags'];
        $conn->query("DELETE FROM note_tags WHERE note_id=$id"); // Clear existing connections

        if (!empty($tags_input)) {
            $tags_array = explode(',', $tags_input);
            foreach ($tags_array as $tag_name) {
                $tag_name = trim(mysqli_real_escape_string($conn, $tag_name));
                if (empty($tag_name))
                    continue;

                // Check/Create Tag
                $tag_res = $conn->query("SELECT id FROM tags WHERE name='$tag_name'");
                if ($tag_res && $tag_res->num_rows > 0) {
                    $tag_id = $tag_res->fetch_assoc()['id'];
                } else {
                    $conn->query("INSERT INTO tags (name) VALUES ('$tag_name')");
                    $tag_id = $conn->insert_id;
                }
                // Link
                $conn->query("INSERT INTO note_tags (note_id, tag_id) VALUES ($id, $tag_id)");
            }
        }
    }

    if ($nb_id > 0) {
        $conn->query("UPDATE notebooks SET updated_at = NOW() WHERE id=$nb_id");
    }

    // AJAX RESPONSE FOR AUTO-SAVE
    if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
        echo json_encode(['status' => 'success', 'id' => $id, 'updated_at' => date('M d, Y H:i'), 'tags' => $tags_input]);
        exit();
    }

    header("Location: " . $redirect_url);
    exit();
}

// --- AJAX: HANDLE FILE UPLOAD ---
if(isset($_FILES['attachment']) && isset($_POST['note_id'])) {
    $note_id = $_POST['note_id'];
    $file = $_FILES['attachment'];
    $upload_dir = 'uploads/';
    
    if($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status'=>'error', 'message'=>'Upload failed code: '.$file['error']]); exit;
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_name = uniqid() . '.' . $ext;
    $target_path = $upload_dir . $new_name;
    
    if(move_uploaded_file($file['tmp_name'], $target_path)) {
        $orig_name = mysqli_real_escape_string($conn, $file['name']);
        $conn->query("INSERT INTO attachments (note_id, file_path, original_name) VALUES ($note_id, '$target_path', '$orig_name')");
        $new_id = $conn->insert_id;
        echo json_encode(['status'=>'success', 'id'=>$new_id, 'name'=>$orig_name, 'path'=>$target_path]);
    } else {
        echo json_encode(['status'=>'error', 'message'=>'Move failed']);
    }
    exit();
}

// --- AJAX: HANDLE FILE DELETE ---
if(isset($_POST['delete_attachment'])) {
    $att_id = $_POST['att_id'];
    $res = $conn->query("SELECT file_path FROM attachments WHERE id=$att_id");
    if($row = $res->fetch_assoc()) {
        if(file_exists($row['file_path'])) unlink($row['file_path']);
        $conn->query("DELETE FROM attachments WHERE id=$att_id");
        echo json_encode(['status'=>'success']);
    } else {
        echo json_encode(['status'=>'error']);
    }
    exit();
}

// --- AJAX: HANDLE FILE UPLOAD ---
if(isset($_FILES['attachment']) && isset($_POST['note_id'])) {
    $note_id = $_POST['note_id'];

    // Verify ownership
    $check = $conn->query("SELECT id FROM notes WHERE id=$note_id AND user_id=$user_id");
    if($check->num_rows == 0) { echo json_encode(['status'=>'error', 'message'=>'Unauthorized']); exit; }

    $file = $_FILES['attachment'];
    $upload_dir = 'uploads/';
    
    if($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status'=>'error', 'message'=>'Upload failed code: '.$file['error']]); exit;
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_name = uniqid() . '.' . $ext;
    $target_path = $upload_dir . $new_name;
    
    if(move_uploaded_file($file['tmp_name'], $target_path)) {
        $orig_name = mysqli_real_escape_string($conn, $file['name']);
        $conn->query("INSERT INTO attachments (note_id, file_path, original_name) VALUES ($note_id, '$target_path', '$orig_name')");
        $new_id = $conn->insert_id;
        echo json_encode(['status'=>'success', 'id'=>$new_id, 'name'=>$orig_name, 'path'=>$target_path]);
    } else {
        echo json_encode(['status'=>'error', 'message'=>'Move failed']);
    }
    exit();
}

// --- AJAX: HANDLE FILE DELETE ---
if(isset($_POST['delete_attachment'])) {
    $att_id = $_POST['att_id'];
    
    // Check ownership via note
    $check = $conn->query("SELECT a.file_path, a.id FROM attachments a JOIN notes n ON a.note_id = n.id WHERE a.id=$att_id AND n.user_id=$user_id");
    
    if($row = $check->fetch_assoc()) {
        if(file_exists($row['file_path'])) unlink($row['file_path']);
        $conn->query("DELETE FROM attachments WHERE id=$att_id");
        echo json_encode(['status'=>'success']);
    } else {
        echo json_encode(['status'=>'error']);
    }
    exit();
}

// B. CREATE / RENAME NOTEBOOK
if (isset($_POST['create_notebook'])) {
    $name = mysqli_real_escape_string($conn, $_POST['notebook_name']);
    if (!empty($name)) {
        $conn->query("INSERT INTO notebooks (name, space_name, created_by_user, user_id) VALUES ('$name', 'Personal', '$username', $user_id)");
    }
    header("Location: dashboard.php?view=notebooks_list");
    exit();
}
if (isset($_POST['rename_notebook'])) {
    $id = $_POST['notebook_id'];
    $name = mysqli_real_escape_string($conn, $_POST['new_name']);
    if (!empty($name)) {
        $conn->query("UPDATE notebooks SET name='$name', updated_at=NOW() WHERE id=$id AND user_id=$user_id");
    }
    header("Location: dashboard.php?view=notebooks_list");
    exit();
}

// --- 2. HANDLE GET ACTIONS ---
if (isset($_GET['trash_notebook'])) {
    $nb_id = $_GET['trash_notebook'];
    $conn->query("UPDATE notebooks SET is_trashed = 1, trashed_at = NOW() WHERE id=$nb_id AND user_id=$user_id");
    $conn->query("UPDATE notes SET is_trashed = 1, trashed_at = NOW() WHERE notebook_id=$nb_id AND user_id=$user_id");
    if (isset($_GET['from_list'])) {
        header("Location: dashboard.php?view=notebooks_list");
        exit();
    }
    header("Location: dashboard.php");
    exit();
}
if (isset($_GET['restore_notebook'])) {
    $nb_id = $_GET['restore_notebook'];
    $conn->query("UPDATE notebooks SET is_trashed = 0, trashed_at = NULL WHERE id=$nb_id AND user_id=$user_id");
    $conn->query("UPDATE notes SET is_trashed = 0, trashed_at = NULL WHERE notebook_id=$nb_id AND user_id=$user_id");
    header("Location: dashboard.php?view=trash");
    exit();
}
if (isset($_GET['delete_notebook_forever'])) {
    $nb_id = $_GET['delete_notebook_forever'];
    // Confirm ownership
    $check = $conn->query("SELECT id FROM notebooks WHERE id=$nb_id AND user_id=$user_id");
    if($check->num_rows > 0) {
        $conn->query("DELETE FROM notes WHERE notebook_id=$nb_id");
        $conn->query("DELETE FROM notebooks WHERE id=$nb_id");
    }
    header("Location: dashboard.php?view=trash");
    exit();
}
if (isset($_GET['trash_id'])) {
    $id = $_GET['trash_id'];
    $conn->query("UPDATE notes SET is_trashed = 1, trashed_at = NOW() WHERE id=$id AND user_id=$user_id");
    $redirect_url = "dashboard.php";
    if (isset($_GET['notebook']))
        $redirect_url .= "?notebook=" . $_GET['notebook'];
    elseif (isset($_GET['view']) && $_GET['view'] == 'notes')
        $redirect_url .= "?view=notes";
    header("Location: " . $redirect_url);
    exit();
}
if (isset($_GET['restore_id'])) {
    $id = $_GET['restore_id'];
    $conn->query("UPDATE notes SET is_trashed = 0, trashed_at = NULL WHERE id=$id AND user_id=$user_id");
    header("Location: dashboard.php?view=trash");
    exit();
}
if (isset($_GET['delete_forever'])) {
    $id = $_GET['delete_forever'];
    $conn->query("DELETE FROM notes WHERE id=$id AND user_id=$user_id");
    header("Location: dashboard.php?view=trash");
    exit();
}

// --- 3. DETERMINE CURRENT VIEW ---
$where_clauses = [];
$where_clauses[] = "user_id = $user_id"; // GLOBAL FILTER

$trashed_notebooks_result = false;

if (isset($_GET['view'])) {
    if ($_GET['view'] == 'trash') {
        $view_mode = 'trash';
        $trashed_notebooks_result = $conn->query("SELECT * FROM notebooks WHERE is_trashed = 1 AND user_id=$user_id");
        $where_clauses[] = "is_trashed = 1";
        $where_clauses[] = "(notebook_id IS NULL OR notebook_id = 0 OR notebook_id IN (SELECT id FROM notebooks WHERE is_trashed = 0 AND user_id=$user_id))";
    } elseif ($_GET['view'] == 'notes') {
        $view_mode = 'notes';
        $where_clauses[] = "is_trashed = 0";
        $where_clauses[] = "(notebook_id IS NULL OR notebook_id = 0)";
    } elseif ($_GET['view'] == 'notebooks_list') {
        $view_mode = 'notebooks_list';
    }
}

if ($view_mode == 'all') {
    $where_clauses[] = "is_trashed = 0";
    if (isset($_GET['notebook'])) {
        $view_mode = 'notebook';
        $filter_notebook_id = mysqli_real_escape_string($conn, $_GET['notebook']);
        $where_clauses[] = "notebook_id = '$filter_notebook_id'";
        $nb_name_res = $conn->query("SELECT name FROM notebooks WHERE id = '$filter_notebook_id' AND user_id=$user_id");
        if ($nb_name_row = $nb_name_res->fetch_assoc())
            $filter_notebook_name = $nb_name_row['name'];
    }
    if (isset($_GET['search'])) {
        $view_mode = 'search';
        $term = mysqli_real_escape_string($conn, $_GET['search']);
        $where_clauses[] = "(title LIKE '%$term%' OR content LIKE '%$term%')";
        $search_query = $_GET['search'];
    }
}

// --- 4. DATA FETCHING ---
$sql_query = "SELECT * FROM notes";
if (count($where_clauses) > 0)
    $sql_query .= " WHERE " . implode(' AND ', $where_clauses);
$sql_query .= " ORDER BY created_at DESC";

$nb_search_term = "";
$nb_sql = "SELECT nb.*, COUNT(n.id) as note_count FROM notebooks nb LEFT JOIN notes n ON nb.id = n.notebook_id AND n.is_trashed = 0 WHERE nb.is_trashed = 0 AND nb.user_id=$user_id";
if (isset($_GET['nb_search']) && !empty($_GET['nb_search'])) {
    $nb_search_term = mysqli_real_escape_string($conn, $_GET['nb_search']);
    $nb_sql .= " AND nb.name LIKE '%$nb_search_term%'";
}
$nb_sql .= " GROUP BY nb.id ORDER BY nb.updated_at DESC";
$notebooks_list = $conn->query($nb_sql);
$total_notebooks_count = ($notebooks_list) ? $notebooks_list->num_rows : 0;

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = $conn->query("SELECT n.*, nb.name as notebook_name FROM notes n LEFT JOIN notebooks nb ON n.notebook_id = nb.id WHERE n.id=$id AND n.user_id=$user_id");
    if ($row = $res->fetch_assoc()) {
        $current_id = $row['id'];
        $current_title = $row['title'];
        $current_title_style = $row['title_style'];
        $current_content_style = isset($row['content_style']) ? $row['content_style'] : ""; // NEW
        $current_content = $row['content'];
        $editor_target_notebook_id = $row['notebook_id'];
        $current_updated_at = date('M d', strtotime($row['created_at']));
        if ($row['notebook_name']) {
            $current_notebook_name_display = htmlspecialchars($row['notebook_name']);
        }
        if ($row['is_trashed'] == 1 && $view_mode != 'trash') {
            $current_id = "";
            $current_title = "";
            $current_content = "";
        }

        // FETCH TAGS
        if ($current_id) {
            $tags_res = $conn->query("SELECT t.name FROM tags t JOIN note_tags nt ON t.id = nt.tag_id WHERE nt.note_id = $current_id");
            $tags_list = [];
            while ($t_row = $tags_res->fetch_assoc()) {
                $tags_list[] = $t_row['name'];
            }
            $current_tags = implode(', ', $tags_list);
        }
        
        // FETCH ATTACHMENTS
        if ($current_id) {
            $att_res = $conn->query("SELECT * FROM attachments WHERE note_id = $current_id ORDER BY created_at ASC");
            $current_attachments = [];
            while($a_row = $att_res->fetch_assoc()) { $current_attachments[] = $a_row; }
        }


    }
} else {
    if ($view_mode == 'notebook') {
        $editor_target_notebook_id = $filter_notebook_id;
        $current_notebook_name_display = $filter_notebook_name;
    }
}

$new_note_url = "dashboard.php";
if ($view_mode == 'notebook')
    $new_note_url .= "?notebook=" . $filter_notebook_id;
elseif ($view_mode == 'notes')
    $new_note_url .= "?view=notes";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | QuickNote</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2300d26a'%3E%3Ccircle cx='12' cy='12' r='12'/%3E%3C/svg%3E">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2300d26a'%3E%3Ccircle cx='12' cy='12' r='12'/%3E%3C/svg%3E">
    <!-- Script moved to assets/js/dashboard.js -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        /* Override Quill Toolbar for Dark Mode */
        .ql-toolbar.ql-snow {
            border: none;
            border-bottom: 1px solid #333;
            background: #191919;
            padding: 12px 20px;
            /* Removed margins for full width */
        }

        .ql-container.ql-snow {
            border: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 1.1rem;
            color: #ccc;
        }

        .ql-editor {
            padding: 20px 40px;
        }

        .ql-editor.ql-blank::before {
            color: #555;
            font-style: normal;
        }

        /* Toolbar Icons Color */
        .ql-snow .ql-stroke {
            stroke: #888;
        }

        .ql-snow .ql-fill {
            fill: #888;
        }

        .ql-snow .ql-picker {
            color: #888;
        }

        .ql-snow .ql-picker-options {
            background-color: #252525;
            border: 1px solid #333;
        }
        
        /* Fix for Custom Font Labels (Visual Update) */
        .ql-snow .ql-picker-label[data-label]::before {
            content: attr(data-label) !important;
        }
    </style>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <style>
        /* IMAGE PREVIEW MODAL */
        #verify-image-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.8);
            justify-content: center;
            align-items: center;
        }
        #verify-image-modal.show {
            display: flex;
        }
        .img-modal-content {
            position: relative;
            background-color: #222;
            padding: 10px;
            border-radius: 8px;
            max-width: 80%;
            max-height: 80%;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            text-align: center;
        }
        .img-modal-content img {
            max-width: 100%;
            max-height: 70vh;
            border-radius: 4px;
        }
        .img-download-link {
            display: block;
            margin-top: 10px;
            color: var(--accent-green);
            text-decoration: none;
        }
        .close-img-modal {
            position: absolute;
            top: -15px;
            right: -15px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-weight: bold;
            line-height: 30px;
            text-align: center;
        }
    </style>
</head>

<body>

    <form id="rename_form" method="POST" style="display:none;">
        <input type="hidden" name="rename_notebook" value="1">
        <input type="hidden" name="notebook_id" id="rename_id">
        <input type="hidden" name="new_name" id="rename_val">
    </form>

    <div id="create-notebook-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Create new notebook</h3>
                <button class="close-modal" onclick="closeModal()">×</button>
            </div>
            <p class="modal-desc">Notebooks are useful for grouping notes around a common topic. They can be private or
                shared.</p>

            <form action="dashboard.php" method="POST">
                <input type="hidden" name="create_notebook" value="1">
                <div style="margin-bottom:10px;">
                    <input type="text" name="notebook_name" id="modal-nb-name" class="modal-input"
                        placeholder="Notebook name" autocomplete="off" onkeyup="checkInput()">
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-modal-create" id="btn-create-nb" disabled>Create</button>
                </div>
            </form>
        </div>
    </div>

    <div class="dashboard-container">

        <div class="sidebar">
            <!-- BRAND HEADER -->
            <div class="sidebar-brand">
                <span class="brand-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="var(--accent-primary)" stroke="none">
                        <circle cx="12" cy="12" r="10"></circle>
                    </svg>
                </span>
                <span class="brand-text">QuickNote</span>
            </div>

            <!-- SEARCH (Moved up) -->
            <!-- SEARCH (Moved up) -->
            <form action="dashboard.php" method="GET" class="sidebar-search-form" style="position: relative;">
                <?php if ($filter_notebook_id): ?><input type="hidden" name="notebook"
                        value="<?php echo $filter_notebook_id; ?>"><?php endif; ?>
                <span class="search-icon-overlay">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input type="text" name="search" class="search-box" placeholder="Search notes..."
                    value="<?php echo htmlspecialchars($search_query); ?>">
            </form>

            <div class="new-note-group">
                <button class="btn-new" onclick="window.location.href='<?php echo $new_note_url; ?>'">+ Note</button>
            </div>

            <div class="nav-menu">
                <a href="dashboard.php" class="nav-item <?php echo ($view_mode == 'all') ? 'active' : ''; ?>">
                    <span class="nav-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </span> All Notes
                </a>

                <a href="dashboard.php?view=notes"
                    class="nav-item <?php echo ($view_mode == 'notes') ? 'active' : ''; ?>">
                    <span class="nav-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </span> Notes
                </a>

                <a href="dashboard.php?view=notebooks_list"
                    class="nav-item <?php echo ($view_mode == 'notebooks_list' || $view_mode == 'notebook') ? 'active' : ''; ?>">
                    <span class="nav-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </span> Notebooks
                </a>

                <a href="dashboard.php?view=trash"
                    class="nav-item <?php echo ($view_mode == 'trash') ? 'active' : ''; ?>">
                    <span class="nav-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </span> Trash
                </a>
            </div>

            <!-- USER PROFILE (Moved to Bottom) -->
            <div class="user-profile-bottom">
                <div class="user-avatar-placeholder"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
                <div class="user-info-group">
                    <span class="user-name-display"><?php echo htmlspecialchars($username); ?></span>
                    <a href="logout.php" class="user-logout-action">Log out</a>
                </div>
            </div>
        </div>

        <?php if ($view_mode == 'notebooks_list'): ?>

            <div class="notebook-manager-panel">
                <div class="nb-header-container">
                    <div class="nb-header-top">
                        <h1 class="nb-header-title">Notebooks</h1>
                        <span class="nb-count-badge"><?php echo $total_notebooks_count; ?></span>
                    </div>

                    <div class="nb-header-actions">
                        <button class="btn-new-nb-action" onclick="openCreateModal()">
                            <span style="font-size: 1.2rem; margin-right: 5px;">+</span> New Notebook
                        </button>

                        <form action="dashboard.php" method="GET" class="nb-search-container">
                            <input type="hidden" name="view" value="notebooks_list">
                            <input type="text" name="nb_search" class="nb-search-bar" placeholder="Find Notebooks..."
                                value="<?php echo htmlspecialchars($nb_search_term); ?>">
                            <span class="nb-search-icon"> </span>
                        </form>
                    </div>
                </div>

                <?php if ($total_notebooks_count > 0): ?>
                    <div class="nb-table-container">
                        <table class="nb-table">
                            <thead>
                                <tr>
                                    <th style="width: 55%;">Title ↑</th>
                                    <th style="width: 20%;">Date Created</th>
                                    <th style="width: 20%;">Date Updated</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($nb = $notebooks_list->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <a href="dashboard.php?notebook=<?php echo $nb['id']; ?>" class="nb-title-cell">
                                                <span class="nb-title-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z">
                                                        </path>
                                                    </svg>
                                                </span>
                                                <?php echo htmlspecialchars($nb['name']); ?>
                                                <span class="nb-notes-count">(<?php echo $nb['note_count']; ?>)</span>
                                            </a>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($nb['created_at'])); ?></td>
                                        <td><?php echo ($nb['updated_at']) ? date('M d, Y', strtotime($nb['updated_at'])) : '-'; ?>
                                        </td>
                                        <td style="text-align: right;">
                                            <div class="action-menu-container">
                                                <button class="action-btn"
                                                    onclick="toggleActionMenu(<?php echo $nb['id']; ?>)">•••</button>
                                                <div id="menu-<?php echo $nb['id']; ?>" class="action-dropdown">
                                                    <a href="dashboard.php?notebook=<?php echo $nb['id']; ?>"
                                                        class="action-item">Add note</a>
                                                    <a href="javascript:void(0)"
                                                        onclick="renameNotebook(<?php echo $nb['id']; ?>, '<?php echo addslashes($nb['name']); ?>')"
                                                        class="action-item">Rename notebook</a>
                                                    <hr style="border:0; border-top:1px solid #3d3d3d; margin:4px 0;">
                                                    <a href="dashboard.php?trash_notebook=<?php echo $nb['id']; ?>&from_list=1"
                                                        onclick="return confirm('Move notebook to trash?')"
                                                        class="action-item delete">Delete notebook</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state-container">
                        <div class="empty-state-title">No Notebooks Found</div>
                        <div class="empty-state-desc">Create a notebook to organize your notes.</div>
                        <button class="btn-save" style="margin-top:20px;" onclick="openCreateModal()">Create Notebook</button>
                    </div>
                <?php endif; ?>
            </div>

        <?php else: ?>

            <div class="note-list-panel">
                <div class="note-list-header">
                    <h2>
                        <?php
                        if ($view_mode == 'trash')
                            echo "Trash";
                        elseif ($view_mode == 'notes')
                            echo "Notes";
                        elseif ($view_mode == 'notebook')
                            echo htmlspecialchars($filter_notebook_name);
                        elseif ($view_mode == 'search')
                            echo "Search Results";
                        else
                            echo "Home";
                        ?>
                    </h2>
                    <?php if ($view_mode != 'trash'): ?>
                        <span class="note-count"><?php echo $conn->query($sql_query)->num_rows; ?> notes</span>
                    <?php endif; ?>
                </div>

                <div class="note-items-container">
                    <?php
                    $has_items = false;

                    // 1. TRASHED NOTEBOOKS SECTION
                    if ($view_mode == 'trash' && $trashed_notebooks_result && $trashed_notebooks_result->num_rows > 0) {
                        $has_items = true;
                        echo "<div style='padding:15px 20px 5px; font-size:0.75rem; color:#666; text-transform:uppercase; letter-spacing:1px;'>NOTEBOOKS</div>";

                        while ($nb = $trashed_notebooks_result->fetch_assoc()) {
                            // Nested notes inside deleted notebook (filtered by user)
                            $nb_notes_res = $conn->query("SELECT * FROM notes WHERE notebook_id = {$nb['id']} AND user_id=$user_id");
                            ?>

                            <div style="border-bottom: 1px solid #333; background:#222;">
                                <div style="padding: 15px 20px; display:flex; justify-content:space-between; align-items:center;">
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <span style="font-size:1.1rem; display:flex; align-items:center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                            </svg>
                                        </span>
                                        <h4 style="margin:0; color:#eee; font-weight:500; font-size:0.95rem;">
                                            <?php echo htmlspecialchars($nb['name']); ?>
                                        </h4>
                                    </div>
                                    <div style="display:flex; gap:10px;">
                                        <a href="dashboard.php?restore_notebook=<?php echo $nb['id']; ?>"
                                            style="color:var(--accent-green); font-size:0.8rem; text-decoration:none;">♻ Restore</a>
                                        <a href="dashboard.php?delete_notebook_forever=<?php echo $nb['id']; ?>"
                                            onclick="return confirm('Permanently delete?')"
                                            style="color:#e74c3c; font-size:0.8rem; text-decoration:none;">✖ Delete</a>
                                    </div>
                                </div>

                                <?php if ($nb_notes_res->num_rows > 0): ?>
                                    <div style="background:#1a1a1a; padding: 5px 0;">
                                        <?php while ($n_row = $nb_notes_res->fetch_assoc()):
                                            // Check if this specific note is currently open in the editor
                                            $is_active = ($current_id == $n_row['id']) ? 'active' : '';
                                            // Link to open note in READ ONLY mode
                                            $trash_link = "dashboard.php?edit=" . $n_row['id'] . "&view=trash";
                                            ?>
                                            <a href="<?php echo $trash_link; ?>" class="trash-nested-note <?php echo $is_active; ?>">
                                                <span style="font-size:0.8rem; display:flex; align-items:center;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                                        <polyline points="10 9 9 9 8 9"></polyline>
                                                    </svg>
                                                </span>
                                                <span><?php echo $n_row['title'] ? htmlspecialchars($n_row['title']) : 'Untitled'; ?></span>
                                            </a>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <div style="padding: 5px 20px 10px 45px; color:#555; font-size:0.8rem; font-style:italic;">Empty
                                        notebook</div>
                                <?php endif; ?>
                            </div>
                            <?php
                        }
                        echo "<hr style='border:0; border-top:1px solid #333; margin:0;'>";
                    }

                    // 2. TRASHED NOTES SECTION
                    $result = $conn->query($sql_query);
                    if ($result->num_rows > 0) {
                        $has_items = true;
                        if ($view_mode == 'trash')
                            echo "<div style='padding:25px 20px 5px; font-size:0.75rem; color:#666; text-transform:uppercase; letter-spacing:1px;'>NOTES</div>";

                        while ($row = $result->fetch_assoc()) {
                            $active_class = ($row['id'] == $current_id) ? 'selected' : '';
                            $edit_url = "dashboard.php?edit=" . $row['id'];
                            if ($view_mode == 'notebook')
                                $edit_url .= "&notebook=" . $filter_notebook_id;
                            if ($view_mode == 'trash')
                                $edit_url .= "&view=trash";
                            if ($view_mode == 'notes')
                                $edit_url .= "&view=notes";

                            $trash_url = "dashboard.php?trash_id=" . $row['id'];
                            if ($view_mode == 'notebook')
                                $trash_url .= "&notebook=" . $filter_notebook_id;
                            if ($view_mode == 'notes')
                                $trash_url .= "&view=notes";

                            $snippet = substr(strip_tags($row['content']), 0, 50);
                            if (strlen($row['content']) > 50)
                                $snippet .= "...";
                            if (!$snippet)
                                $snippet = "No additional text";

                            ?>
                            <div class="note-item <?php echo $active_class; ?>"
                                onclick="window.location.href='<?php echo $edit_url; ?>'">
                                <div style="display:flex; justify-content:space-between;">
                                    <h4 style="flex-grow:1; <?php echo htmlspecialchars($row['title_style'] ?? ''); ?>" id="sidebar-title-<?php echo $row['id']; ?>">
                                        <?php echo $row['title'] ? htmlspecialchars($row['title']) : 'Untitled'; ?>
                                    </h4>
                                    <?php if ($view_mode != 'trash'): ?>
                                        <a href="<?php echo $trash_url; ?>"
                                            onclick="event.stopPropagation(); return confirm('Move to Trash?');"
                                            style="color:#666; padding:0 5px; text-decoration:none;">🗑️</a>
                                    <?php endif; ?>
                                </div>
                                <p class="note-snippet" id="sidebar-snippet-<?php echo $row['id']; ?>" style="<?php echo htmlspecialchars($row['content_style'] ?? ''); ?>"><?php echo $snippet; ?></p>
                                <span class="note-meta">
                                    <?php
                                    if ($view_mode == 'trash')
                                        echo "<span style='color:orange;'>In Trash</span>";
                                    else
                                        echo date('M d', strtotime($row['created_at']));
                                    ?>
                                </span>
                            </div>
                            <?php
                        }
                    }

                    if (!$has_items) {
                        if ($view_mode == 'trash') {
                            echo '<div class="empty-state-container"><svg class="trash-icon-svg" viewBox="0 0 24 24"><path d="M19 6h-3.5l-1-1h-5l-1 1H5v2h14V6zM6 9v11a2 2 0 002 2h8a2 2 0 002-2V9H6z"/></svg><div class="empty-state-title">Your trash is empty</div></div>';
                        } else {
                            echo "<p style='padding:20px; color:#666;'>No notes found.</p>";
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="editor-panel">
                <form action="dashboard.php" method="POST" class="editor-form-element"
                    style="height:100%; display:flex; flex-direction:column;">
                    <?php if ($filter_notebook_id): ?>
                        <input type="hidden" name="notebook" value="<?php echo $filter_notebook_id; ?>">
                    <?php endif; ?>
                    <?php if ($view_mode == 'notes'): ?>
                        <input type="hidden" name="view" value="notes">
                    <?php endif; ?>

                    <div class="editor-top-bar">

                        <div class="editor-header-info">
                            <div class="breadcrumbs">
                                <span class="notebook-name">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" style="margin-right:4px; vertical-align:text-bottom;">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                    <?php echo htmlspecialchars($current_notebook_name_display); ?></span>
                                <span>›</span>
                                <span><?php echo $current_title ? htmlspecialchars($current_title) : "Untitled"; ?></span>
                            </div>
                            <div class="last-edited">Edited
                                <?php echo $current_updated_at ? $current_updated_at : "just now"; ?>
                            </div>
                        </div>

                        <div class="editor-actions">
                            <?php if ($view_mode == 'trash'): ?>
                                <input type="hidden" name="is_trash_mode" value="1">
                                <?php if ($current_id): ?>
                                    <span style="color:orange; font-size:0.9rem; margin-right:10px;">⚠ In Trash</span>
                                    <a href="dashboard.php?restore_id=<?php echo $current_id; ?>" class="btn-save-pill"
                                        style="border-color:var(--accent-green);">Restore</a>
                                    <a href="dashboard.php?delete_forever=<?php echo $current_id; ?>"
                                        onclick="return confirm('Permanently delete?')" class="btn-save-pill"
                                        style="color:red; border-color:red;">Delete</a>
                                <?php endif; ?>
                            <?php else: ?>

                                <?php
                                $display_label = "All Notes";
                                $display_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>';
                                $save_notebook_id = "0";
                                if ($view_mode == 'notebook') {
                                    $display_label = htmlspecialchars($filter_notebook_name);
                                    $display_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>';
                                    $save_notebook_id = $filter_notebook_id;
                                } elseif ($view_mode == 'notes') {
                                    $display_label = "Notes";
                                    $display_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>';
                                    $save_notebook_id = ($current_id) ? $editor_target_notebook_id : "0";
                                } else {
                                    $save_notebook_id = ($current_id) ? $editor_target_notebook_id : "0";
                                }
                                ?>

                                <span class="notebook-badge">
                                    <?php echo $display_icon . " " . $display_label; ?>
                                </span>

                                <input type="hidden" name="notebook_id" value="<?php echo $save_notebook_id; ?>">

                                <!-- MOVED STATUS AND ATTACHMENT BUTTON OUTSIDE THE IF BLOCK -->
                                <span id="save-status" style="margin-right:10px; font-size:0.8rem; color:#666; font-style:italic;"></span>
                                
                                <!-- Attachment Button -->
                                <button type="button" class="btn-icon-trash" style="margin-right:5px;" onclick="document.getElementById('upload-input').click()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48">
                                        </path>
                                    </svg>
                                </button>
                                <input type="file" id="upload-input" name="attachment" style="display:none;" onchange="uploadFile(this)">

                                <?php if ($current_id): ?>
                                    <a href="dashboard.php?trash_id=<?php echo $current_id; ?>" class="btn-icon-trash">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </a>
                                <?php endif; ?>

                                <button type="submit" name="save_note" class="btn-save-green">Save Note</button>
                            <?php endif; ?>
                        </div>

                    </div>
                    
                    <!-- CUSTOM TOOLBAR CONTAINER (Evernote Style) -->
                    <div id="toolbar-container" class="toolbar-loading" style="visibility: hidden;">
                        <span class="ql-formats">
                            <select class="ql-font" style="width: 120px;">
                                <option value="poppins" selected>Poppins</option>
                                <option value="arial">Arial</option>
                                <option value="calibri">Calibri</option>
                                <option value="roboto">Roboto</option>
                                <option value="opensans">Open Sans</option>
                                <option value="montserrat">Montserrat</option>
                                <option value="inter">Inter</option>
                                <option value="lato">Lato</option>
                                <option value="verdana">Verdana</option>
                                <option value="georgia">Georgia</option>
                                <option value="serif">Serif</option>
                                <option value="monospace">Monospace</option>
                            </select>
                        </span>
                        <span class="ql-formats">
                            <select class="ql-header">
                                <option value="1">Heading 1</option>
                                <option value="2">Heading 2</option>
                                <option selected>Normal</option>
                            </select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-bold"></button>
                            <button class="ql-italic"></button>
                            <button class="ql-underline"></button>
                            <button class="ql-strike"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-list" value="ordered"></button>
                            <button class="ql-list" value="bullet"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-blockquote"></button>
                            <button class="ql-code-block"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-clean"></button>
                        </span>
                    </div>

                    <div class="editor-form-inner">
                    <input type="hidden" name="id" value="<?php echo $current_id; ?>">
                    <input type="hidden" name="title_style" id="title-style-input" value="<?php echo htmlspecialchars($current_title_style); ?>">
                    <input type="hidden" name="content_style" id="content-style-input" value="<?php echo htmlspecialchars($current_content_style); ?>"> <!-- NEW -->
                    <input type="text" name="title" class="editor-title" placeholder="Title"
                        value="<?php echo htmlspecialchars($current_title); ?>" 
                        style="<?php echo htmlspecialchars($current_title_style); ?>"
                        <?php echo ($view_mode == 'trash') ? 'readonly' : ''; ?>>

                    <!-- TAGS INPUT -->
                    <input type="text" name="tags" class="editor-tags" placeholder="Add tags (separated by comma)..."
                        value="<?php echo htmlspecialchars($current_tags); ?>" <?php echo ($view_mode == 'trash') ? 'readonly' : ''; ?>>
                    
                    <!-- ATTACHMENTS LIST -->
                    <div id="attachments-container" style="padding: 0 40px; margin-bottom: 10px; display:flex; flex-wrap:wrap; gap:10px;">
                        <?php if(isset($current_attachments) && !empty($current_attachments)): ?>
                            <?php foreach($current_attachments as $att): 
                                $ext = strtolower(pathinfo($att['original_name'], PATHINFO_EXTENSION));
                                $is_image = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            ?>
                                <div class="att-chip" id="att-<?php echo $att['id']; ?>" style="display:flex; align-items:center; background:#333; padding:5px 10px; border-radius:15px; font-size:0.85rem;">
                                    <span style="margin-right:5px; display:flex;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path
                                                d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48">
                                            </path>
                                        </svg>
                                    </span>
                                    <?php if($is_image): ?>
                                        <a href="javascript:void(0)" onclick="viewImage('<?php echo htmlspecialchars($att['file_path']); ?>')" style="color:#ddd; text-decoration:none; margin-right:8px; border-bottom:1px dashed #666;"><?php echo htmlspecialchars($att['original_name']); ?></a>
                                    <?php else: ?>
                                        <a href="<?php echo htmlspecialchars($att['file_path']); ?>" target="_blank" style="color:#ddd; text-decoration:none; margin-right:8px;"><?php echo htmlspecialchars($att['original_name']); ?></a>
                                    <?php endif; ?>
                                    <span style="cursor:pointer; color:#888;" onclick="deleteAttachment(<?php echo $att['id']; ?>)">×</span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Hidden input to store Quill HTML -->
                    <input type="hidden" name="content" value="<?php echo htmlspecialchars($current_content); ?>">

                    <!-- Quill Editor Container -->
                    <div id="editor-container" style="flex-grow:1; border:none;"><?php echo $current_content; ?></div>

                </form>
            </div>

        <?php endif; ?>

    </div>


    <!-- Image Preview Modal -->
    <div id="verify-image-modal" class="modal-overlay" onclick="closeImageModal()">
        <div class="img-modal-content" onclick="event.stopPropagation()">
            <button class="close-img-modal" onclick="closeImageModal()">×</button>
            <img id="preview-img-tag" src="">
            <a id="preview-dl-link" href="" download class="img-download-link">Download Original</a>
        </div>
    </div>

    <script src="assets/js/dashboard.js"></script>
</body>
</html>
