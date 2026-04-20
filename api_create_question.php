<?php
require 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['error'=>'Not logged in']); exit; }

$title = trim($_POST['title'] ?? '');
$body  = trim($_POST['body']  ?? '');
if (!$title || !$body) { echo json_encode(['error'=>'Title and details required.']); exit; }

$stmt = $conn->prepare("INSERT INTO questions (user_id, title, body) VALUES (?,?,?)");
$stmt->bind_param("iss", $_SESSION['user_id'], $title, $body);
echo $stmt->execute() ? json_encode(['success'=>true]) : json_encode(['error'=>'DB error']);
