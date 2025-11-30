<?php
include 'db.php';

// --- 0. AUTO-MAINTENANCE ---
$conn->query("DELETE FROM notes WHERE is_trashed = 1 AND trashed_at < NOW() - INTERVAL 30 DAY");
$conn->query("DELETE FROM notebooks WHERE is_trashed = 1 AND trashed_at < NOW() - INTERVAL 30 DAY");

// --- INITIALIZE VARIABLES ---
$current_id = "";
$current_title = "";
$current_content = "";
$editor_target_notebook_id = ""; 
$search_query = "";
$filter_notebook_id = "";
$filter_notebook_name = "";
$view_mode = "all"; // 'all', 'notes', 'notebook', 'trash', 'search', 'notebooks_list'

// --- 1. HANDLE POST ACTIONS ---

// A. SAVE NOTE
if (isset($_POST['save_note'])) {
    if(isset($_POST['is_trash_mode']) && $_POST['is_trash_mode'] == '1') {
        header("Location: dashboard.php?view=trash"); exit();
    }
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $nb_id = $_POST['notebook_id'];
    $id = $_POST['id'];
    $nb_sql = ($nb_id == "" || $nb_id == "0") ? "NULL" : "'$nb_id'";
    
    // Redirect Logic
    $redirect_url = "dashboard.php";
    if(isset($_GET['notebook'])) $redirect_url .= "?notebook=" . $_GET['notebook'];
    elseif(isset($_GET['view']) && $_GET['view'] == 'notes') $redirect_url .= "?view=notes"; 
    if($id != '') $redirect_url .= "&edit=" . $id; 

    // Validation (Empty Note)
    if (trim($title) === '' && trim($content) === '') {
         header("Location: " . str_replace("&edit=" . $id, "", $redirect_url)); exit();
    }

    if ($id != '') {
        $sql = "UPDATE notes SET title='$title', content='$content', notebook_id=$nb_sql WHERE id=$id";
    } else {
        $sql = "INSERT INTO notes (title, content, notebook_id) VALUES ('$title', '$content', $nb_sql)";
    }
    $conn->query($sql);

    // Update Notebook's 'updated_at' if applicable
    if ($nb_id > 0) {
        $conn->query("UPDATE notebooks SET updated_at = NOW() WHERE id=$nb_id");
    }

    header("Location: " . $redirect_url);
    exit();
}

// B. CREATE NOTEBOOK
if (isset($_POST['create_notebook'])) {
    $name = mysqli_real_escape_string($conn, $_POST['notebook_name']);
    if(!empty($name)){
        $conn->query("INSERT INTO notebooks (name) VALUES ('$name')");
    }
    header("Location: dashboard.php?view=notebooks_list");
    exit();
}

// C. RENAME NOTEBOOK
if (isset($_POST['rename_notebook'])) {
    $id = $_POST['notebook_id'];
    $name = mysqli_real_escape_string($conn, $_POST['new_name']);
    if(!empty($name)){
        $conn->query("UPDATE notebooks SET name='$name', updated_at=NOW() WHERE id=$id");
    }
    header("Location: dashboard.php?view=notebooks_list");
    exit();
}

// --- 2. HANDLE GET ACTIONS ---

// A. TRASH NOTEBOOK (Soft Delete)
if (isset($_GET['trash_notebook'])) {
    $nb_id = $_GET['trash_notebook'];
    $conn->query("UPDATE notebooks SET is_trashed = 1, trashed_at = NOW() WHERE id=$nb_id");
    $conn->query("UPDATE notes SET is_trashed = 1, trashed_at = NOW() WHERE notebook_id=$nb_id");
    // Return to list if we were in list view
    if(isset($_GET['from_list'])) { header("Location: dashboard.php?view=notebooks_list"); exit(); }
    header("Location: dashboard.php"); exit();
}

// B. RESTORE NOTEBOOK
if (isset($_GET['restore_notebook'])) {
    $nb_id = $_GET['restore_notebook'];
    $conn->query("UPDATE notebooks SET is_trashed = 0, trashed_at = NULL WHERE id=$nb_id");
    $conn->query("UPDATE notes SET is_trashed = 0, trashed_at = NULL WHERE notebook_id=$nb_id");
    header("Location: dashboard.php?view=trash"); exit();
}

// C. DELETE NOTEBOOK FOREVER
if (isset($_GET['delete_notebook_forever'])) {
    $nb_id = $_GET['delete_notebook_forever'];
    $conn->query("DELETE FROM notes WHERE notebook_id=$nb_id");
    $conn->query("DELETE FROM notebooks WHERE id=$nb_id");
    header("Location: dashboard.php?view=trash"); exit();
}

// D. NOTES ACTIONS (Trash/Restore/Delete)
if (isset($_GET['trash_id'])) {
    $id = $_GET['trash_id'];
    $conn->query("UPDATE notes SET is_trashed = 1, trashed_at = NOW() WHERE id=$id");
    $redirect_url = "dashboard.php";
    if(isset($_GET['notebook'])) $redirect_url .= "?notebook=" . $_GET['notebook'];
    elseif(isset($_GET['view']) && $_GET['view'] == 'notes') $redirect_url .= "?view=notes";
    header("Location: " . $redirect_url); exit();
}
if (isset($_GET['restore_id'])) {
    $id = $_GET['restore_id'];
    $conn->query("UPDATE notes SET is_trashed = 0, trashed_at = NULL WHERE id=$id");
    header("Location: dashboard.php?view=trash"); exit();
}
if (isset($_GET['delete_forever'])) {
    $id = $_GET['delete_forever'];
    $conn->query("DELETE FROM notes WHERE id=$id");
    header("Location: dashboard.php?view=trash"); exit();
}

// --- 3. DETERMINE CURRENT VIEW ---
$where_clauses = [];
$trashed_notebooks_result = false;

if (isset($_GET['view'])) {
    if ($_GET['view'] == 'trash') {
        $view_mode = 'trash';
        $where_clauses[] = "is_trashed = 1"; 
        $trashed_notebooks_result = $conn->query("SELECT * FROM notebooks WHERE is_trashed = 1");
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
        $nb_name_res = $conn->query("SELECT name FROM notebooks WHERE id = '$filter_notebook_id'");
        if($nb_name_row = $nb_name_res->fetch_assoc()) $filter_notebook_name = $nb_name_row['name'];
    }
    if (isset($_GET['search'])) {
        $view_mode = 'search';
        $term = mysqli_real_escape_string($conn, $_GET['search']);
        $where_clauses[] = "(title LIKE '%$term%' OR content LIKE '%$term%')";
        $search_query = $_GET['search'];
    }
}

// --- 4. DATA FETCHING ---

// A. Fetch Notes (For List/Editor Views)
$sql_query = "SELECT * FROM notes";
if (count($where_clauses) > 0) $sql_query .= " WHERE " . implode(' AND ', $where_clauses);
$sql_query .= " ORDER BY created_at DESC";

// B. Fetch Notebooks (For Notebook Manager View)
$nb_search_term = "";
$nb_sql = "SELECT * FROM notebooks WHERE is_trashed = 0";
if (isset($_GET['nb_search']) && !empty($_GET['nb_search'])) {
    $nb_search_term = mysqli_real_escape_string($conn, $_GET['nb_search']);
    $nb_sql .= " AND name LIKE '%$nb_search_term%'";
}
$nb_sql .= " ORDER BY updated_at DESC";
$notebooks_list = $conn->query($nb_sql);

// C. Fetch Single Note (For Editor)
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM notes WHERE id=$id");
    if ($row = $res->fetch_assoc()) {
        $current_id = $row['id'];
        $current_title = $row['title'];
        $current_content = $row['content'];
        $editor_target_notebook_id = $row['notebook_id'];
        if($row['is_trashed'] == 1 && $view_mode != 'trash') {
            $current_id = ""; $current_title = ""; $current_content = "";
        }
    }
} else {
    if ($view_mode == 'notebook') $editor_target_notebook_id = $filter_notebook_id;
}

$new_note_url = "dashboard.php";
if ($view_mode == 'notebook') $new_note_url .= "?notebook=" . $filter_notebook_id;
elseif ($view_mode == 'notes') $new_note_url .= "?view=notes";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | QuickNote</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function createNotebook() {
            let name = prompt("Enter Notebook Name:");
            if (name) {
                document.getElementById('nb_name_input').value = name;
                document.getElementById('nb_form').submit();
            }
        }
        function renameNotebook(id, currentName) {
            let name = prompt("Rename Notebook:", currentName);
            if (name) {
                document.getElementById('rename_id').value = id;
                document.getElementById('rename_val').value = name;
                document.getElementById('rename_form').submit();
            }
        }
        function toggleActionMenu(id) {
            // Hide all others
            document.querySelectorAll('.action-dropdown').forEach(el => {
                if (el.id !== 'menu-'+id) el.classList.remove('show');
            });
            // Toggle current
            document.getElementById('menu-'+id).classList.toggle('show');
        }
        // Close menus when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.action-btn')) {
                document.querySelectorAll('.action-dropdown').forEach(el => el.classList.remove('show'));
            }
        }
    </script>
</head>
<body>

<form id="nb_form" method="POST" style="display:none;">
    <input type="hidden" name="create_notebook" value="1">
    <input type="hidden" name="notebook_name" id="nb_name_input">
</form>
<form id="rename_form" method="POST" style="display:none;">
    <input type="hidden" name="rename_notebook" value="1">
    <input type="hidden" name="notebook_id" id="rename_id">
    <input type="hidden" name="new_name" id="rename_val">
</form>

<div class="dashboard-container">
    
    <div class="sidebar">
        <form action="dashboard.php" method="GET">
            <?php if($filter_notebook_id): ?><input type="hidden" name="notebook" value="<?php echo $filter_notebook_id; ?>"><?php endif; ?>
            <input type="text" name="search" class="search-box" placeholder="🔍 Search notes..." value="<?php echo htmlspecialchars($search_query); ?>">
        </form>

        <div class="new-note-group">
            <button class="btn-new" onclick="window.location.href='<?php echo $new_note_url; ?>'">+ Note</button>
        </div>

        <div class="nav-menu">
            <a href="dashboard.php" class="nav-item <?php echo ($view_mode == 'all') ? 'active' : ''; ?>">
                <span class="nav-icon">🏠</span> All Notes
            </a>
            
            <a href="dashboard.php?view=notes" class="nav-item <?php echo ($view_mode == 'notes') ? 'active' : ''; ?>">
                <span class="nav-icon">📄</span> Notes
            </a>

            <a href="dashboard.php?view=notebooks_list" class="nav-item <?php echo ($view_mode == 'notebooks_list' || $view_mode == 'notebook') ? 'active' : ''; ?>">
                <span class="nav-icon">📓</span> Notebooks
            </a>
            
            <a href="dashboard.php?view=trash" class="nav-item <?php echo ($view_mode == 'trash') ? 'active' : ''; ?>" style="margin-top:auto;">
                <span class="nav-icon">🗑️</span> Trash
            </a>
        </div>
    </div>

    <?php if ($view_mode == 'notebooks_list'): ?>
    
    <div class="notebook-manager-panel">
        <div class="nb-header">
            <h1 style="margin:0; font-size: 2rem;">Notebooks</h1>
            <div style="display:flex; gap:10px;">
                <form action="dashboard.php" method="GET">
                    <input type="hidden" name="view" value="notebooks_list">
                    <input type="text" name="nb_search" class="nb-search-bar" placeholder="Find Notebooks..." value="<?php echo htmlspecialchars($nb_search_term); ?>">
                </form>
                <button class="btn-save" onclick="createNotebook()">+ New Notebook</button>
            </div>
        </div>

        <?php if ($notebooks_list->num_rows > 0): ?>
        <table class="nb-table">
            <thead>
                <tr>
                    <th>TITLE</th>
                    <th>CREATED</th>
                    <th>UPDATED</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php while($nb = $notebooks_list->fetch_assoc()): ?>
                <tr>
                    <td>
                        <a href="dashboard.php?notebook=<?php echo $nb['id']; ?>" class="nb-link">
                            📓 <?php echo htmlspecialchars($nb['name']); ?>
                        </a>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($nb['created_at'])); ?></td>
                    <td><?php echo ($nb['updated_at']) ? date('M d, Y', strtotime($nb['updated_at'])) : '-'; ?></td>
                    <td>
                        <div class="action-menu-container">
                            <button class="action-btn" onclick="toggleActionMenu(<?php echo $nb['id']; ?>)">•••</button>
                            <div id="menu-<?php echo $nb['id']; ?>" class="action-dropdown">
                                <a href="dashboard.php?notebook=<?php echo $nb['id']; ?>" class="action-item">Open</a>
                                <a href="javascript:void(0)" onclick="renameNotebook(<?php echo $nb['id']; ?>, '<?php echo addslashes($nb['name']); ?>')" class="action-item">Rename notebook</a>
                                <hr style="border:0; border-top:1px solid #444; margin:0;">
                                <a href="dashboard.php?trash_notebook=<?php echo $nb['id']; ?>&from_list=1" onclick="return confirm('Move notebook to trash?')" class="action-item delete">Delete notebook</a>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty-state-container">
                <div class="empty-state-title">No Notebooks Found</div>
                <div class="empty-state-desc">Create a notebook to organize your notes.</div>
                <button class="btn-save" style="margin-top:20px;" onclick="createNotebook()">Create Notebook</button>
            </div>
        <?php endif; ?>
    </div>

    <?php else: ?>

    <div class="note-list-panel">
        <div class="note-list-header">
            <h2>
                <?php 
                if($view_mode == 'trash') echo "Trash";
                elseif($view_mode == 'notes') echo "Notes"; 
                elseif($view_mode == 'notebook') echo htmlspecialchars($filter_notebook_name);
                elseif($view_mode == 'search') echo "Search Results";
                else echo "Home"; 
                ?>
            </h2>
            <?php if($view_mode != 'trash'): ?>
                <span class="note-count"><?php echo $conn->query($sql_query)->num_rows; ?> notes</span>
            <?php endif; ?>
        </div>
        
        <div class="note-items-container">
            <?php
            $has_items = false;

            // TRASHED NOTEBOOKS
            if($view_mode == 'trash' && $trashed_notebooks_result && $trashed_notebooks_result->num_rows > 0) {
                $has_items = true;
                echo "<div style='padding:10px 20px; font-size:0.8rem; color:#888; text-transform:uppercase;'>Notebooks</div>";
                while($nb = $trashed_notebooks_result->fetch_assoc()) {
                   ?>
                   <div class="note-item" style="border-left: 3px solid orange; background:#222;">
                        <h4 style="margin:0; color:#ddd;">📓 <?php echo htmlspecialchars($nb['name']); ?></h4>
                        <div style="margin-top:10px; display:flex; gap:10px;">
                            <a href="dashboard.php?restore_notebook=<?php echo $nb['id']; ?>" style="color:var(--accent-green); font-size:0.8rem; text-decoration:none;">♻ Restore</a>
                            <a href="dashboard.php?delete_notebook_forever=<?php echo $nb['id']; ?>" onclick="return confirm('Permanently delete?')" style="color:#e74c3c; font-size:0.8rem; text-decoration:none;">✖ Delete</a>
                        </div>
                   </div>
                   <?php
                }
                echo "<hr style='border:0; border-top:1px solid #333; margin:0;'>";
            }

            // NOTES
            $result = $conn->query($sql_query);
            if ($result->num_rows > 0) {
                $has_items = true;
                if($view_mode == 'trash') echo "<div style='padding:10px 20px; font-size:0.8rem; color:#888; text-transform:uppercase;'>Notes</div>";
                while($row = $result->fetch_assoc()) {
                    $active_class = ($row['id'] == $current_id) ? 'selected' : '';
                    $edit_url = "dashboard.php?edit=" . $row['id'];
                    if($view_mode == 'notebook') $edit_url .= "&notebook=" . $filter_notebook_id;
                    if($view_mode == 'trash') $edit_url .= "&view=trash"; 
                    if($view_mode == 'notes') $edit_url .= "&view=notes";
                    
                    $trash_url = "dashboard.php?trash_id=" . $row['id'];
                    if($view_mode == 'notebook') $trash_url .= "&notebook=" . $filter_notebook_id;
                    if($view_mode == 'notes') $trash_url .= "&view=notes";
                    ?>
                    <div class="note-item <?php echo $active_class; ?>" onclick="window.location.href='<?php echo $edit_url; ?>'">
                        <div style="display:flex; justify-content:space-between;">
                            <h4 style="flex-grow:1;"><?php echo $row['title'] ? htmlspecialchars($row['title']) : 'Untitled'; ?></h4>
                            <?php if($view_mode != 'trash'): ?>
                            <a href="<?php echo $trash_url; ?>" onclick="event.stopPropagation(); return confirm('Move to Trash?');" style="color:#666; padding:0 5px; text-decoration:none;">🗑️</a>
                            <?php endif; ?>
                        </div>
                        <p><?php echo htmlspecialchars($row['content']); ?></p>
                        <span class="note-meta">
                            <?php 
                                if($view_mode == 'trash') echo "<span style='color:orange;'>In Trash</span>";
                                else echo date('M d', strtotime($row['created_at'])); 
                            ?>
                        </span>
                    </div>
                    <?php
                }
            } 
            
            if (!$has_items) {
                if($view_mode == 'trash') {
                    echo '<div class="empty-state-container"><svg class="trash-icon-svg" viewBox="0 0 24 24"><path d="M19 6h-3.5l-1-1h-5l-1 1H5v2h14V6zM6 9v11a2 2 0 002 2h8a2 2 0 002-2V9H6z"/></svg><div class="empty-state-title">Your trash is empty</div></div>';
                } else {
                    echo "<p style='padding:20px; color:#666;'>No notes found.</p>";
                }
            }
            ?>
        </div>
    </div>

    <div class="editor-panel">
        <form action="dashboard.php<?php 
            if($filter_notebook_id) echo '?notebook='.$filter_notebook_id; 
            elseif($view_mode == 'notes') echo '?view=notes';
        ?>" method="POST" style="height:100%; display:flex; flex-direction:column;">
            
            <div class="editor-toolbar">
                <?php if ($view_mode == 'trash'): ?>
                    <input type="hidden" name="is_trash_mode" value="1">
                    <?php if ($current_id): ?>
                        <div style="margin-right:auto;">
                            <span style="color:orange;">⚠ In Trash</span>
                            <a href="dashboard.php?restore_id=<?php echo $current_id; ?>" style="color:var(--accent-green); margin-left:15px; text-decoration:none;">♻ Restore</a>
                        </div>
                        <a href="dashboard.php?delete_forever=<?php echo $current_id; ?>" onclick="return confirm('Permanently delete?')" style="color:red; text-decoration:none;">✖ Delete Forever</a>
                    <?php endif; ?>
                <?php else: ?>
                    <?php 
                    $display_label = "All Notes"; $display_icon = "📝"; $save_notebook_id = "0";
                    if ($view_mode == 'notebook') { $display_label = htmlspecialchars($filter_notebook_name); $display_icon = "📓"; $save_notebook_id = $filter_notebook_id; }
                    elseif ($view_mode == 'notes') { $display_label = "Notes"; $display_icon = "📄"; $save_notebook_id = ($current_id) ? $editor_target_notebook_id : "0"; }
                    else { $save_notebook_id = ($current_id) ? $editor_target_notebook_id : "0"; }
                    ?>
                    <span style="background:#333; color:#aaa; padding:6px 12px; border-radius:4px; font-size:0.9rem; user-select: none;">
                        <?php echo $display_icon . " " . $display_label; ?>
                    </span>
                    <input type="hidden" name="notebook_id" value="<?php echo $save_notebook_id; ?>">
                    <?php if($current_id): ?>
                        <a href="dashboard.php?trash_id=<?php echo $current_id; ?>" style="color:#666; text-decoration:none; margin-left:10px; font-size:1.2rem;">🗑️</a>
                    <?php endif; ?>
                    <button type="submit" name="save_note" class="btn-save">Save Note</button>
                <?php endif; ?>
            </div>

            <input type="hidden" name="id" value="<?php echo $current_id; ?>">
            <input type="text" name="title" class="editor-title" placeholder="Title" value="<?php echo htmlspecialchars($current_title); ?>" <?php echo ($view_mode=='trash') ? 'readonly style="color:#666;"' : ''; ?>>
            <textarea name="content" class="editor-content" placeholder="Start writing..." <?php echo ($view_mode=='trash') ? 'readonly style="color:#666;"' : ''; ?>><?php echo htmlspecialchars($current_content); ?></textarea>
        </form>
    </div>

    <?php endif; ?>
    </div>

</body>
</html>