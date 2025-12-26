<?php
include 'config/db.php';
$res = $conn->query("SHOW COLUMNS FROM notes LIKE 'content_style'");
if ($res->num_rows > 0) {
    echo json_encode(["status" => "exists"]);
} else {
    echo json_encode(["status" => "missing"]);
}
?>