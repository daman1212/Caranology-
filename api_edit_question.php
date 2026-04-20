<?php require 'db.php'; header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['error'=>'Not logged in']); exit; }
$id    = intval($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$body  = trim($_POST['body']  ?? '');
if (!$id || !$title || !$body) { echo json_encode(['error'=>'Missing fields']); exit; }
$s = $conn->prepare("UPDATE questions SET title=?,body=? WHERE id=? AND user_id=?");
$s->bind_param("ssii",$title,$body,$id,$_SESSION['user_id']);
echo $s->execute() ? json_encode(['success'=>true]) : json_encode(['error'=>'DB error']);
