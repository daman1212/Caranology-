<?php
// api_create_answer.php
require 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['error'=>'Not logged in']); exit; }
$qid  = intval($_POST['question_id'] ?? 0);
$body = trim($_POST['body'] ?? '');
if (!$qid || !$body) { echo json_encode(['error'=>'Missing fields']); exit; }
$s = $conn->prepare("INSERT INTO answers (question_id,user_id,body) VALUES (?,?,?)");
$s->bind_param("iis",$qid,$_SESSION['user_id'],$body);
echo $s->execute() ? json_encode(['success'=>true]) : json_encode(['error'=>'DB error']);
