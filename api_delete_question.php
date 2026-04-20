<?php require 'db.php'; header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['error'=>'Not logged in']); exit; }
$id = intval($_POST['id'] ?? 0);
if (!$id) { echo json_encode(['error'=>'Invalid']); exit; }
$s = $conn->prepare("DELETE FROM questions WHERE id=? AND user_id=?");
$s->bind_param("ii",$id,$_SESSION['user_id']);
echo $s->execute() ? json_encode(['success'=>true]) : json_encode(['error'=>'DB error']);
