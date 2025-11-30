<?php
include 'db.php';

// --- 0. AUTO-MAINTENANCE ---
$conn->query("DELETE FROM notes WHERE is_trashed = 1 AND trashed_at < NOW() - INTERVAL 30 DAY");
$conn->query("DELETE FROM notebooks WHERE is_trashed = 1 AND trashed_at < NOW() - INTERVAL 30 DAY");

// --- INITIALIZE VARIABLES ---
$current_id = "";
$current_title = "";
$current_content = "";
$current_notebook_name_display = "Note"; // Default
$current_updated_at = "";
$editor_target_notebook_id = ""; 
$search_query = "";
$filter_notebook_id = "";
$filter_notebook_name = "";
$view_mode = "all"; 

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
    
    $redirect_url = "dashboard.php";
    if(isset($_GET['notebook'])) $redirect_url .= "?notebook=" . $_GET['notebook'];
    elseif(isset($_GET['view']) && $_GET['view'] == 'notes') $redirect_url .= "?view=notes"; 
    if($id != '') $redirect_url .= "&edit=" . $id; 

    if (trim($title) === '' && trim($content) === '') { header("Location: " . str_replace("&edit=" . $id, "", $redirect_url)); exit(); }

    if ($id != '') {
        $sql = "UPDATE notes SET title='$title', content='$content', notebook_id=$nb_sql WHERE id=$id";
    } else {
        $sql = "INSERT INTO notes (title, content, notebook_id) VALUES ('$title', '$content', $nb_sql)";
    }
    $conn->query($sql);

    if ($nb_id > 0) { $conn->query("UPDATE notebooks SET updated_at = NOW() WHERE id=$nb_id"); }
    header("Location: " . $redirect_url); exit();
}

// B. CREATE / RENAME NOTEBOOK
if (isset($_POST['create_notebook'])) {
    $name = mysqli_real_escape_string($conn, $_POST['notebook_name']);
    if(!empty($name)){ $conn->query("INSERT INTO notebooks (name, space_name, created_by_user) VALUES ('$name', 'Personal', 'dianacast555')"); }
    header("Location: dashboard.php?view=notebooks_list"); exit();
}
if (isset($_POST['rename_notebook'])) {
    $id = $_POST['notebook_id'];
    $name = mysqli_real_escape_string($conn, $_POST['new_name']);
    if(!empty($name)){ $conn->query("UPDATE notebooks SET name='$name', updated_at=NOW() WHERE id=$id"); }
    header("Location: dashboard.php?view=notebooks_list"); exit();
}

// --- 2. HANDLE GET ACTIONS ---
if (isset($_GET['trash_notebook'])) {
    $nb_id = $_GET['trash_notebook'];
    $conn->query("UPDATE notebooks SET is_trashed = 1, trashed_at = NOW() WHERE id=$nb_id");
    $conn->query("UPDATE notes SET is_trashed = 1, trashed_at = NOW() WHERE notebook_id=$nb_id");
    if(isset($_GET['from_list'])) { header("Location: dashboard.php?view=notebooks_list"); exit(); }
    header("Location: dashboard.php"); exit();
}
if (isset($_GET['restore_notebook'])) {
    $nb_id = $_GET['restore_notebook'];
    $conn->query("UPDATE notebooks SET is_trashed = 0, trashed_at = NULL WHERE id=$nb_id");
    $conn->query("UPDATE notes SET is_trashed = 0, trashed_at = NULL WHERE notebook_id=$nb_id");
    header("Location: dashboard.php?view=trash"); exit();
}
if (isset($_GET['delete_notebook_forever'])) {
    $nb_id = $_GET['delete_notebook_forever'];
    $conn->query("DELETE FROM notes WHERE notebook_id=$nb_id");
    $conn->query("DELETE FROM notebooks WHERE id=$nb_id");
    header("Location: dashboard.php?view=trash"); exit();
}
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

// A. Fetch Notes
$sql_query = "SELECT * FROM notes";
if (count($where_clauses) > 0) $sql_query .= " WHERE " . implode(' AND ', $where_clauses);
$sql_query .= " ORDER BY created_at DESC";

// B. Fetch Notebooks
$nb_search_term = "";
$nb_sql = "SELECT nb.*, COUNT(n.id) as note_count 
           FROM notebooks nb 
           LEFT JOIN notes n ON nb.id = n.notebook_id AND n.is_trashed = 0
           WHERE nb.is_trashed = 0";
if (isset($_GET['nb_search']) && !empty($_GET['nb_search'])) {
    $nb_search_term = mysqli_real_escape_string($conn, $_GET['nb_search']);
    $nb_sql .= " AND nb.name LIKE '%$nb_search_term%'";
}
$nb_sql .= " GROUP BY nb.id ORDER BY nb.updated_at DESC";
$notebooks_list = $conn->query($nb_sql);
$total_notebooks_count = ($notebooks_list) ? $notebooks_list->num_rows : 0;

// C. Fetch Single Note (UPDATED to get Notebook Name for Breadcrumbs)
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    // Left Join to get Notebook Name
    $res = $conn->query("SELECT n.*, nb.name as notebook_name FROM notes n LEFT JOIN notebooks nb ON n.notebook_id = nb.id WHERE n.id=$id");
    if ($row = $res->fetch_assoc()) {
        $current_id = $row['id'];
        $current_title = $row['title'];
        $current_content = $row['content'];
        $editor_target_notebook_id = $row['notebook_id'];
        $current_updated_at = date('M d', strtotime($row['created_at'])); // Using created for now, or update to updated_at if available
        if ($row['notebook_name']) {
            $current_notebook_name_display = htmlspecialchars($row['notebook_name']);
        }
        
        if($row['is_trashed'] == 1 && $view_mode != 'trash') {
            $current_id = ""; $current_title = ""; $current_content = "";
        }
    }
} else {
    if ($view_mode == 'notebook') {
        $editor_target_notebook_id = $filter_notebook_id;
        $current_notebook_name_display = $filter_notebook_name;
    }
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
            if (name) { document.getElementById('nb_name_input').value = name; document.getElementById('nb_form').submit(); }
        }
        function renameNotebook(id, currentName) {
            let name = prompt("Rename Notebook:", currentName);
            if (name) { document.getElementById('rename_id').value = id; document.getElementById('rename_val').value = name; document.getElementById('rename_form').submit(); }
        }
        function toggleActionMenu(id) {
            document.querySelectorAll('.action-dropdown').forEach(el => { if (el.id !== 'menu-'+id) el.classList.remove('show'); });
            document.getElementById('menu-'+id).classList.toggle('show');
        }
        window.onclick = function(event) {
            if (!event.target.matches('.action-btn')) { document.querySelectorAll('.action-dropdown').forEach(el => el.classList.remove('show')); }
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
        </div> 
        <a href="dashboard.php?view=trash" class="nav-item <?php echo ($view_mode == 'trash') ? 'active' : ''; ?>" style="margin-top:auto;">
            <span class="nav-icon">🗑️</span> Trash
        </a>
    </div>

    <?php if ($view_mode == 'notebooks_list'): ?>
    <div class="notebook-manager-panel">
        <div class="nb-header-container">
            <div class="nb-header-top">
                <h1 class="nb-header-title">Notebooks</h1>
                <span class="nb-count-badge"><?php echo $total_notebooks_count; ?></span>
            </div>
            <div class="nb-header-actions">
                <form action="dashboard.php" method="GET" class="nb-search-container" style="margin-right: auto;">
                    <input type="hidden" name="view" value="notebooks_list">
                    <input type="text" name="nb_search" class="nb-search-bar" placeholder="Find Notebooks..." value="<?php echo htmlspecialchars($nb_search_term); ?>">
                    <span class="nb-search-icon">🔍</span>
                </form>
                <button class="btn-new-nb-action" onclick="createNotebook()"><span style="font-size: 1.1rem;">+</span> New Notebook</button>
            </div>
        </div>
        <?php if ($total_notebooks_count > 0): ?>
        <div class="nb-table-container">
            <table class="nb-table">
                <thead>
                    <tr><th style="width: 45%;">Title ↑</th><th style="width: 25%;">Date Created</th><th style="width: 25%;">Date Updated</th><th style="width: 5%;"></th></tr>
                </thead>
                <tbody>
                    <?php while($nb = $notebooks_list->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <a href="dashboard.php?notebook=<?php echo $nb['id']; ?>" class="nb-title-cell">
                                <span class="nb-title-icon">📓</span> <?php echo htmlspecialchars($nb['name']); ?> <span class="nb-notes-count">(<?php echo $nb['note_count']; ?>)</span>
                            </a>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($nb['created_at'])); ?></td>
                        <td><?php echo ($nb['updated_at']) ? date('M d, Y', strtotime($nb['updated_at'])) : '-'; ?></td>
                        <td style="text-align: right;">
                            <div class="action-menu-container">
                                <button class="action-btn" onclick="toggleActionMenu(<?php echo $nb['id']; ?>)">•••</button>
                                <div id="menu-<?php echo $nb['id']; ?>" class="action-dropdown">
                                    <a href="dashboard.php?notebook=<?php echo $nb['id']; ?>" class="action-item">Add note</a>
                                    <a href="javascript:void(0)" onclick="renameNotebook(<?php echo $nb['id']; ?>, '<?php echo addslashes($nb['name']); ?>')" class="action-item">Rename notebook</a>
                                    <hr style="border:0; border-top:1px solid #3d3d3d; margin:4px 0;">
                                    <a href="dashboard.php?trash_notebook=<?php echo $nb['id']; ?>&from_list=1" onclick="return confirm('Move notebook to trash?')" class="action-item delete">Delete notebook</a>
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
                <button class="btn-new" style="width:auto; margin-top:20px; padding: 10px 30px;" onclick="createNotebook()">Create Notebook</button>
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
            <?php if($view_mode != 'trash'): ?><span class="note-count"><?php echo $conn->query($sql_query)->num_rows; ?> notes</span><?php endif; ?>
        </div>
        <div class="note-items-container">
            <?php
            $has_items = false;
            // Trashed Notebooks (Only in trash view)
            if($view_mode == 'trash' && $trashed_notebooks_result && $trashed_notebooks_result->num_rows > 0) {
                $has_items = true;
                echo "<div style='padding:10px 20px; font-size:0.8rem; color:#888; text-transform:uppercase;'>Notebooks</div>";
                while($nb = $trashed_notebooks_result->fetch_assoc()) {
                   echo "<div class='note-item' style='border-left: 4px solid orange; background:#222;'><h4 style='margin:0; color:#ddd;'>📓 ".htmlspecialchars($nb['name'])."</h4><div style='margin-top:10px; display:flex; gap:10px;'><a href='dashboard.php?restore_notebook={$nb['id']}' style='color:var(--accent-green); font-size:0.8rem;'>♻ Restore</a><a href='dashboard.php?delete_notebook_forever={$nb['id']}' onclick='return confirm(\"Delete forever?\")' style='color:#e74c3c; font-size:0.8rem;'>✖ Delete</a></div></div>";
                }
                echo "<hr style='border:0; border-top:1px solid #333; margin:0;'>";
            }
            
            // Notes List
            $result = $conn->query($sql_query);
            if ($result->num_rows > 0) {
                $has_items = true;
                while($row = $result->fetch_assoc()) {
                    $active_class = ($row['id'] == $current_id) ? 'selected' : '';
                    $edit_url = "dashboard.php?edit=" . $row['id'];
                    // Construct URL to maintain view state
                    if($view_mode == 'notebook') $edit_url .= "&notebook=" . $filter_notebook_id;
                    if($view_mode == 'trash') $edit_url .= "&view=trash"; 
                    if($view_mode == 'notes') $edit_url .= "&view=notes";
                    
                    // Snippet generation
                    $snippet = substr(strip_tags($row['content']), 0, 50);
                    if(strlen($row['content']) > 50) $snippet .= "...";
                    if(!$snippet) $snippet = "No additional text";
                    
                    ?>
                    <div class="note-item <?php echo $active_class; ?>" onclick="window.location.href='<?php echo $edit_url; ?>'">
                        <h4><?php echo $row['title'] ? htmlspecialchars($row['title']) : 'Untitled'; ?></h4>
                        <p class="note-snippet"><?php echo $snippet; ?></p>
                        <span class="note-meta">
                            <?php echo ($view_mode == 'trash') ? "<span style='color:orange;'>In Trash</span>" : date('M d', strtotime($row['created_at'])); ?>
                        </span>
                    </div>
                    <?php
                }
            } 
            if (!$has_items) {
                if($view_mode == 'trash') echo '<div class="empty-state-container"><svg class="trash-icon-svg" viewBox="0 0 24 24"><path d="M19 6h-3.5l-1-1h-5l-1 1H5v2h14V6zM6 9v11a2 2 0 002 2h8a2 2 0 002-2V9H6z"/></svg><div class="empty-state-title">Your trash is empty</div></div>';
                else echo "<p style='padding:20px; color:#666;'>No notes found.</p>";
            }
            ?>
        </div>
    </div>

    <div class="editor-panel">
        <form action="dashboard.php<?php 
            if($filter_notebook_id) echo '?notebook='.$filter_notebook_id; 
            elseif($view_mode == 'notes') echo '?view=notes';
        ?>" method="POST" class="editor-form">
            
            <div class="editor-actions">
                <?php if ($view_mode == 'trash'): ?>
                    <input type="hidden" name="is_trash_mode" value="1">
                    <?php if ($current_id): ?>
                        <span style="color:orange; font-size:0.9rem; margin-right:10px;">⚠ In Trash</span>
                        <a href="dashboard.php?restore_id=<?php echo $current_id; ?>" class="btn-save-pill" style="border-color:var(--accent-green);">Restore</a>
                        <a href="dashboard.php?delete_forever=<?php echo $current_id; ?>" onclick="return confirm('Permanently delete?')" class="btn-save-pill" style="color:red; border-color:red;">Delete</a>
                    <?php endif; ?>
                <?php else: ?>
                    <input type="hidden" name="notebook_id" value="<?php echo ($current_id ? $editor_target_notebook_id : ($view_mode == 'notebook' ? $filter_notebook_id : 0)); ?>">
                    <?php if($current_id): ?>
                        <a href="dashboard.php?trash_id=<?php echo $current_id; ?>" class="btn-save-pill" style="background:transparent; border:none; color:#666; font-size:1.2rem;">🗑️</a>
                    <?php endif; ?>
                    <button type="submit" name="save_note" class="btn-save-pill">Save Note</button>
                <?php endif; ?>
            </div>

            <div class="editor-header-info">
                <div class="breadcrumbs">
                    <span class="notebook-name">📓 <?php echo htmlspecialchars($current_notebook_name_display); ?></span>
                    <span>›</span>
                    <span><?php echo $current_title ? htmlspecialchars($current_title) : "Untitled"; ?></span>
                </div>
                <div class="last-edited">Edited <?php echo $current_updated_at ? $current_updated_at : "just now"; ?></div>
            </div>

            <input type="hidden" name="id" value="<?php echo $current_id; ?>">
            <input type="text" name="title" class="editor-title" placeholder="Title" value="<?php echo htmlspecialchars($current_title); ?>" <?php echo ($view_mode=='trash') ? 'readonly' : ''; ?>>
            <textarea name="content" class="editor-content" placeholder="Start writing..." <?php echo ($view_mode=='trash') ? 'readonly' : ''; ?>><?php echo htmlspecialchars($current_content); ?></textarea>
        </form>
    </div>

    <?php endif; ?>

</div>
</body>
</html>