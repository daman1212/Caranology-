<?php
require 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['error'=>'Not logged in']); exit; }

$qid = intval($_GET['question_id'] ?? 0);
if (!$qid) { echo json_encode(['error'=>'Invalid']); exit; }

$rows = $conn->query("
  SELECT a.body, a.created_at, u.name, u.avatar
  FROM answers a JOIN users u ON u.id=a.user_id
  WHERE a.question_id=$qid ORDER BY a.created_at ASC
")->fetch_all(MYSQLI_ASSOC);

function timeAgo($ts){$d=time()-strtotime($ts);if($d<60)return 'just now';if($d<3600)return floor($d/60).'m ago';if($d<86400)return floor($d/3600).'h ago';return date('M j',strtotime($ts));}
function initials($n){$p=explode(' ',trim($n));return strtoupper(substr($p[0],0,1).(isset($p[1])?substr($p[1],0,1):''));}

$out = array_map(fn($r) => [
    'body'     => htmlspecialchars($r['body']),
    'name'     => htmlspecialchars($r['name']),
    'initials' => initials($r['name']),
    'time_ago' => timeAgo($r['created_at']),
    'avatar'   => $r['avatar']
], $rows);

echo json_encode(['answers'=>$out]);
