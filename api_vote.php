<?php
require 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['error'=>'Not logged in']); exit; }

$uid  = $_SESSION['user_id'];
$qid  = intval($_POST['question_id'] ?? 0);
$type = $_POST['vote_type'] ?? '';
if (!$qid || !in_array($type, ['up','down'])) { echo json_encode(['error'=>'Invalid']); exit; }

// Check existing vote
$s = $conn->prepare("SELECT vote_type FROM votes WHERE user_id=? AND question_id=?");
$s->bind_param("ii",$uid,$qid); $s->execute();
$existing = $s->get_result()->fetch_assoc();

if ($existing) {
    if ($existing['vote_type'] === $type) {
        // Remove vote (toggle off)
        $conn->prepare("DELETE FROM votes WHERE user_id=? AND question_id=?")->bind_param("ii",$uid,$qid) && $conn->query("DELETE FROM votes WHERE user_id=$uid AND question_id=$qid");
        $col = $type === 'up' ? 'upvotes' : 'downvotes';
        $conn->query("UPDATE questions SET $col = GREATEST(0, $col - 1) WHERE id=$qid");
        $my_vote = null;
    } else {
        // Switch vote
        $conn->query("UPDATE votes SET vote_type='$type' WHERE user_id=$uid AND question_id=$qid");
        $old = $type==='up' ? 'downvotes' : 'upvotes';
        $new = $type==='up' ? 'upvotes' : 'downvotes';
        $conn->query("UPDATE questions SET $old=GREATEST(0,$old-1), $new=$new+1 WHERE id=$qid");
        $my_vote = $type;
    }
} else {
    // New vote
    $conn->query("INSERT INTO votes (user_id,question_id,vote_type) VALUES ($uid,$qid,'$type')");
    $col = $type==='up' ? 'upvotes' : 'downvotes';
    $conn->query("UPDATE questions SET $col=$col+1 WHERE id=$qid");
    $my_vote = $type;
}

$row = $conn->query("SELECT upvotes, downvotes FROM questions WHERE id=$qid")->fetch_assoc();
echo json_encode(['success'=>true, 'upvotes'=>$row['upvotes'], 'downvotes'=>$row['downvotes'], 'my_vote'=>$my_vote]);
