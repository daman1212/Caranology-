<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$uid = $_SESSION['user_id'];

$questions = $conn->query("
  SELECT q.*, (SELECT COUNT(*) FROM answers WHERE question_id=q.id) AS ans_count
  FROM questions q WHERE q.user_id = $uid ORDER BY q.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

function timeAgo($ts) {
    $d = time()-strtotime($ts);
    if($d<60) return 'just now';
    if($d<3600) return floor($d/60).'m ago';
    if($d<86400) return floor($d/3600).'h ago';
    return date('M j', strtotime($ts));
}
function initials($n){$p=explode(' ',trim($n));return strtoupper(substr($p[0],0,1).(isset($p[1])?substr($p[1],0,1):''));}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Questions — Caranology</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
  <a href="feed.php" class="nav-brand"><span></span>Caranology</a>
  <div class="nav-actions">
    <a href="feed.php" class="btn btn-ghost btn-sm">← Back to Feed</a>
    <button class="btn btn-primary btn-sm" onclick="openModal('createModal')">+ Ask Question</button>
    <div class="av-circle" title="<?= htmlspecialchars($_SESSION['user_name']) ?>">
      <?php if ($_SESSION['avatar']): ?><img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" alt=""><?php else: ?><?= initials($_SESSION['user_name']) ?><?php endif; ?>
    </div>
  </div>
</nav>

<div class="page-wrap">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:10px">
    <div>
      <h1 style="font-size:1.3rem;font-weight:700">My Questions</h1>
      <p style="font-size:0.82rem;color:var(--gray-400)"><?= count($questions) ?> question<?= count($questions)!=1?'s':'' ?> asked</p>
    </div>
    <button class="btn btn-primary btn-sm" onclick="openModal('createModal')">+ Ask New</button>
  </div>

  <?php if (empty($questions)): ?>
    <div class="empty">
      <div class="empty-icon">🚗</div>
      <h3>No questions yet</h3>
      <p>Ask your first car question!</p>
      <button class="btn btn-primary" style="margin-top:16px" onclick="openModal('createModal')">Ask a Question</button>
    </div>
  <?php else: ?>
    <?php foreach ($questions as $q): ?>
      <div class="q-card fade-up">
        <div class="q-title"><?= htmlspecialchars($q['title']) ?></div>
        <div class="q-preview" style="margin-top:6px"><?= htmlspecialchars(mb_strimwidth($q['body'],0,180,'…')) ?></div>
        <div style="display:flex;align-items:center;gap:8px;margin-top:14px;flex-wrap:wrap">
          <span style="font-size:0.78rem;color:var(--gray-400)"><?= timeAgo($q['created_at']) ?></span>
          <span class="badge badge-teal">▲ <?= $q['upvotes'] ?> · ▼ <?= $q['downvotes'] ?></span>
          <span style="font-size:0.78rem;color:var(--gray-500)">💬 <?= $q['ans_count'] ?> answer<?= $q['ans_count']!=1?'s':'' ?></span>
          <div style="margin-left:auto;display:flex;gap:8px">
            <button class="btn btn-outline btn-sm" onclick="openEdit(<?= $q['id'] ?>, <?= htmlspecialchars(json_encode($q['title'])) ?>, <?= htmlspecialchars(json_encode($q['body'])) ?>)">Edit</button>
            <button class="btn btn-danger btn-sm" onclick="deleteQ(<?= $q['id'] ?>)">Delete</button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- ── MOBILE NAV ── -->
<nav class="mobile-nav">
  <div class="mobile-nav-inner">
    <a href="feed.php" class="mob-nav-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
      Feed
    </a>
    <button class="mob-nav-btn" onclick="openModal('createModal')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Ask
    </button>
    <a href="my_questions.php" class="mob-nav-btn active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      Mine
    </a>
    <a href="logout.php" class="mob-nav-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Logout
    </a>
  </div>
</nav>

<!-- Create Modal -->
<div class="modal-bg" id="createModal">
  <div class="modal">
    <div class="modal-head"><div class="modal-title">Ask a Question</div><button class="modal-x" onclick="closeModal('createModal')">✕</button></div>
    <div class="modal-body">
      <form id="createForm">
        <div class="form-group"><label class="form-label">Question Title</label><input type="text" id="qTitle" class="form-input" placeholder="What's your car question?" required></div>
        <div class="form-group"><label class="form-label">Details</label><textarea id="qBody" class="form-input" rows="4" placeholder="Describe in detail..." required></textarea></div>
        <button type="submit" class="btn btn-primary btn-block">Post Question</button>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-bg" id="editModal">
  <div class="modal">
    <div class="modal-head"><div class="modal-title">Edit Question</div><button class="modal-x" onclick="closeModal('editModal')">✕</button></div>
    <div class="modal-body">
      <form id="editForm">
        <input type="hidden" id="editId">
        <div class="form-group"><label class="form-label">Title</label><input type="text" id="editTitle" class="form-input" required></div>
        <div class="form-group"><label class="form-label">Details</label><textarea id="editBody" class="form-input" rows="4" required></textarea></div>
        <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
      </form>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
function openModal(id){document.getElementById(id).classList.add('open');document.body.style.overflow='hidden';}
function closeModal(id){document.getElementById(id).classList.remove('open');document.body.style.overflow='';}
document.querySelectorAll('.modal-bg').forEach(bg=>bg.addEventListener('click',e=>{if(e.target===bg)closeModal(bg.id);}));
function toast(msg,type='success'){const t=document.getElementById('toast');t.textContent=msg;t.className='toast toast-'+type+' show';setTimeout(()=>t.classList.remove('show'),2800);}

document.getElementById('createForm').addEventListener('submit', async e=>{
  e.preventDefault();
  const fd=new FormData();
  fd.append('title',document.getElementById('qTitle').value.trim());
  fd.append('body',document.getElementById('qBody').value.trim());
  const r=await fetch('api_create_question.php',{method:'POST',body:fd});
  const d=await r.json();
  if(d.success){closeModal('createModal');toast('Question posted!');setTimeout(()=>location.reload(),700);}
  else toast(d.error||'Error','error');
});

function openEdit(id,title,body){
  document.getElementById('editId').value=id;
  document.getElementById('editTitle').value=title;
  document.getElementById('editBody').value=body;
  openModal('editModal');
}

document.getElementById('editForm').addEventListener('submit', async e=>{
  e.preventDefault();
  const fd=new FormData();
  fd.append('id',document.getElementById('editId').value);
  fd.append('title',document.getElementById('editTitle').value.trim());
  fd.append('body',document.getElementById('editBody').value.trim());
  const r=await fetch('api_edit_question.php',{method:'POST',body:fd});
  const d=await r.json();
  if(d.success){closeModal('editModal');toast('Updated!');setTimeout(()=>location.reload(),700);}
  else toast(d.error||'Error','error');
});

async function deleteQ(id){
  if(!confirm('Delete this question?')) return;
  const fd=new FormData();fd.append('id',id);
  const r=await fetch('api_delete_question.php',{method:'POST',body:fd});
  const d=await r.json();
  if(d.success){toast('Deleted.');setTimeout(()=>location.reload(),700);}
  else toast(d.error||'Error','error');
}
</script>
</body>
</html>
