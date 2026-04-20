<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$uid = $_SESSION['user_id'];

// Fetch all questions with author info + vote status for current user
$sql = "
  SELECT q.*, u.name, u.username, u.avatar,
         v.vote_type AS my_vote,
         (SELECT COUNT(*) FROM answers WHERE question_id = q.id) AS ans_count
  FROM questions q
  JOIN users u ON u.id = q.user_id
  LEFT JOIN votes v ON v.question_id = q.id AND v.user_id = $uid
  ORDER BY q.created_at DESC
";
$questions = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

function timeAgo($ts) {
    $diff = time() - strtotime($ts);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff/60)   . 'm ago';
    if ($diff < 86400)  return floor($diff/3600)  . 'h ago';
    if ($diff < 604800) return floor($diff/86400) . 'd ago';
    return date('M j', strtotime($ts));
}

function initials($name) {
    $parts = explode(' ', trim($name));
    return strtoupper(substr($parts[0],0,1) . (isset($parts[1]) ? substr($parts[1],0,1) : ''));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Feed — Caranology</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ── DESKTOP NAVBAR ── -->
<nav class="navbar">
  <a href="feed.php" class="nav-brand">
    <span></span>Caranology
  </a>

  <div style="flex:1;max-width:360px;margin:0 24px">
    <input type="text" id="searchBox" class="form-input" placeholder="🔍  Search questions..." style="padding:8px 14px;font-size:0.875rem" oninput="filterQuestions(this.value)">
  </div>

  <div class="nav-actions">
    <button class="btn btn-primary btn-sm" onclick="openModal('createModal')">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Ask Question
    </button>

    <div class="dropdown">
      <div class="av-circle" onclick="toggleDD('userDD')" title="<?= htmlspecialchars($_SESSION['user_name']) ?>">
        <?php if ($_SESSION['avatar']): ?>
          <img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="">
        <?php else: ?>
          <?= initials($_SESSION['user_name']) ?>
        <?php endif; ?>
      </div>
      <div class="dd-menu" id="userDD">
        <div style="padding:12px 16px;border-bottom:1px solid var(--gray-100)">
          <div style="font-weight:700;font-size:0.9rem"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
          <div style="font-size:0.78rem;color:var(--gray-400)">@<?= htmlspecialchars($_SESSION['username']) ?></div>
        </div>
        <a href="my_questions.php" class="dd-item">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
          My Questions
        </a>
        <button class="dd-item" onclick="openModal('createModal')">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Ask a Question
        </button>
        <hr class="dd-sep">
        <a href="logout.php" class="dd-item red">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Log Out
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- ── MOBILE HEADER ── -->
<div style="display:none" class="mob-header">
  <div style="padding:14px 16px 10px;display:flex;align-items:center;justify-content:space-between;background:white;border-bottom:1px solid var(--gray-200)">
    <div style="font-size:1.25rem;font-weight:800;color:var(--teal);display:flex;align-items:center;gap:6px">
      <span style="width:8px;height:8px;background:var(--teal);border-radius:50%;display:inline-block"></span>
      Caranology
    </div>
    <button class="btn btn-primary btn-sm" onclick="openModal('createModal')">+ Ask</button>
  </div>
  <div style="padding:8px 14px;background:white;border-bottom:1px solid var(--gray-100)">
    <input type="text" class="form-input" placeholder="🔍 Search questions..." style="font-size:0.875rem;padding:8px 12px" oninput="filterQuestions(this.value)">
  </div>
</div>

<!-- ── MAIN CONTENT ── -->
<div class="page-wrap">

  <!-- Stats bar -->
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
    <div>
      <h1 style="font-size:1.3rem;font-weight:700">Car Questions</h1>
      <p style="font-size:0.82rem;color:var(--gray-400)"><?= count($questions) ?> question<?= count($questions) !== 1 ? 's' : '' ?> in the community</p>
    </div>
    <span class="badge badge-teal">Community Feed</span>
  </div>

  <!-- Questions -->
  <div id="questionsList">
    <?php if (empty($questions)): ?>
      <div class="empty">
        <div class="empty-icon">🚗</div>
        <h3>No questions yet</h3>
        <p>Be the first to ask something!</p>
      </div>
    <?php else: ?>
      <?php foreach ($questions as $i => $q): ?>
        <div class="q-card fade-up fade-up-<?= min($i+1,4) ?>"
             data-title="<?= htmlspecialchars(strtolower($q['title'])) ?>"
             data-body="<?= htmlspecialchars(strtolower($q['body'])) ?>">

          <div class="q-meta">
            <div class="q-avatar">
              <?php if ($q['avatar']): ?>
                <img src="<?= htmlspecialchars($q['avatar']) ?>" alt="">
              <?php else: ?>
                <?= initials($q['name']) ?>
              <?php endif; ?>
            </div>
            <div>
              <div class="q-author"><?= htmlspecialchars($q['name']) ?></div>
              <div class="q-date">@<?= htmlspecialchars($q['username']) ?> · <?= timeAgo($q['created_at']) ?></div>
            </div>
          </div>

          <div class="q-title"><?= htmlspecialchars($q['title']) ?></div>
          <div class="q-preview"><?= htmlspecialchars(mb_strimwidth($q['body'], 0, 160, '…')) ?></div>

          <div class="q-actions">
            <button class="vote-btn <?= $q['my_vote']==='up' ? 'up-active' : '' ?>"
                    onclick="vote(<?= $q['id'] ?>, 'up', this)">
              ▲ <span class="vcount"><?= $q['upvotes'] ?></span>
            </button>
            <button class="vote-btn <?= $q['my_vote']==='down' ? 'down-active' : '' ?>"
                    onclick="vote(<?= $q['id'] ?>, 'down', this)">
              ▼ <span class="vcount"><?= $q['downvotes'] ?></span>
            </button>
            <button class="btn btn-outline btn-sm" onclick="openAnswers(<?= $q['id'] ?>, <?= htmlspecialchars(json_encode($q['title'])) ?>)">
              💬 <?= $q['ans_count'] ?> Answer<?= $q['ans_count'] != 1 ? 's' : '' ?>
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<!-- ── MOBILE BOTTOM NAV ── -->
<nav class="mobile-nav">
  <div class="mobile-nav-inner">
    <a href="feed.php" class="mob-nav-btn active">
      <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Feed
    </a>
    <button class="mob-nav-btn" onclick="openModal('createModal')">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Ask
    </button>
    <a href="my_questions.php" class="mob-nav-btn">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      Mine
    </a>
    <a href="logout.php" class="mob-nav-btn">
      <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Logout
    </a>
  </div>
</nav>

<!-- ── CREATE QUESTION MODAL ── -->
<div class="modal-bg" id="createModal">
  <div class="modal">
    <div class="modal-head">
      <div class="modal-title">Ask a Question 🚗</div>
      <button class="modal-x" onclick="closeModal('createModal')">✕</button>
    </div>
    <div class="modal-body">
      <form id="createForm">
        <div class="form-group">
          <label class="form-label">Question Title</label>
          <input type="text" id="qTitle" class="form-input" placeholder="e.g. Why is my engine making a clicking sound?" maxlength="255" required>
        </div>
        <div class="form-group">
          <label class="form-label">Details</label>
          <textarea id="qBody" class="form-input" placeholder="Describe your question in detail..." rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Post Question</button>
      </form>
    </div>
  </div>
</div>

<!-- ── ANSWERS MODAL ── -->
<div class="modal-bg" id="answersModal">
  <div class="modal">
    <div class="modal-head">
      <div class="modal-title" id="ansModalTitle">Answers</div>
      <button class="modal-x" onclick="closeModal('answersModal')">✕</button>
    </div>
    <div class="modal-body">
      <div id="answersList" style="margin-bottom:18px"></div>
      <hr>
      <div style="margin-top:16px">
        <label class="form-label">Your Answer</label>
        <textarea id="answerInput" class="form-input" rows="3" placeholder="Share your knowledge..."></textarea>
        <button class="btn btn-primary btn-block" style="margin-top:10px" onclick="submitAnswer()">Post Answer</button>
      </div>
    </div>
  </div>
</div>

<!-- ── TOAST ── -->
<div class="toast" id="toast"></div>

<script>
// ── DROPDOWN ──
function toggleDD(id) {
  const m = document.getElementById(id);
  m.classList.toggle('open');
}
document.addEventListener('click', e => {
  document.querySelectorAll('.dd-menu').forEach(m => {
    if (!m.parentElement.contains(e.target)) m.classList.remove('open');
  });
});

// ── MODAL ──
function openModal(id) {
  document.getElementById(id).classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeModal(id) {
  document.getElementById(id).classList.remove('open');
  document.body.style.overflow = '';
}
document.querySelectorAll('.modal-bg').forEach(bg => {
  bg.addEventListener('click', e => { if (e.target === bg) closeModal(bg.id); });
});

// ── TOAST ──
function toast(msg, type='success') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast toast-' + type + ' show';
  setTimeout(() => t.classList.remove('show'), 2800);
}

// ── CREATE QUESTION ──
document.getElementById('createForm').addEventListener('submit', async e => {
  e.preventDefault();
  const title = document.getElementById('qTitle').value.trim();
  const body  = document.getElementById('qBody').value.trim();
  if (!title || !body) return toast('Please fill in all fields.', 'error');

  const fd = new FormData();
  fd.append('title', title); fd.append('body', body);

  const r = await fetch('api_create_question.php', { method:'POST', body: fd });
  const d = await r.json();
  if (d.success) {
    closeModal('createModal');
    toast('Question posted!');
    setTimeout(() => location.reload(), 700);
  } else {
    toast(d.error || 'Error posting question.', 'error');
  }
});

// ── VOTE ──
async function vote(qid, type, btn) {
  const fd = new FormData();
  fd.append('question_id', qid); fd.append('vote_type', type);
  const r = await fetch('api_vote.php', { method:'POST', body: fd });
  const d = await r.json();
  if (d.success) {
    // Update both buttons
    const card = btn.closest('.q-card');
    const upBtn   = card.querySelectorAll('.vote-btn')[0];
    const downBtn = card.querySelectorAll('.vote-btn')[1];
    upBtn.querySelector('.vcount').textContent   = d.upvotes;
    downBtn.querySelector('.vcount').textContent = d.downvotes;
    upBtn.classList.toggle('up-active',   d.my_vote === 'up');
    downBtn.classList.toggle('down-active', d.my_vote === 'down');
  } else {
    toast(d.error || 'Could not vote.', 'error');
  }
}

// ── ANSWERS ──
let currentQid = null;
async function openAnswers(qid, title) {
  currentQid = qid;
  document.getElementById('ansModalTitle').textContent = title;
  document.getElementById('answerInput').value = '';
  await loadAnswers(qid);
  openModal('answersModal');
}

async function loadAnswers(qid) {
  const r = await fetch('api_answers.php?question_id=' + qid);
  const d = await r.json();
  const el = document.getElementById('answersList');
  if (!d.answers || d.answers.length === 0) {
    el.innerHTML = '<div class="empty"><div class="empty-icon">💬</div><p>No answers yet — be the first!</p></div>';
    return;
  }
  el.innerHTML = d.answers.map(a => `
    <div class="ans-card">
      <div class="ans-meta">
        <div class="q-avatar" style="width:28px;height:28px;font-size:0.7rem">${a.initials}</div>
        <span class="ans-author">${a.name}</span>
        <span class="ans-date">${a.time_ago}</span>
      </div>
      <div class="ans-body">${a.body}</div>
    </div>
  `).join('');
}

async function submitAnswer() {
  const body = document.getElementById('answerInput').value.trim();
  if (!body) return toast('Please write an answer first.', 'error');
  const fd = new FormData();
  fd.append('question_id', currentQid); fd.append('body', body);
  const r = await fetch('api_create_answer.php', { method:'POST', body: fd });
  const d = await r.json();
  if (d.success) {
    document.getElementById('answerInput').value = '';
    await loadAnswers(currentQid);
    toast('Answer posted!');
    // Update answer count on card
    document.querySelectorAll('.q-card').forEach(card => {
      const btn = card.querySelector('.btn-outline');
      if (btn && card.querySelector('.vote-btn')) {
        // find by question id is tricky here — reload is simpler
      }
    });
  } else {
    toast(d.error || 'Error posting answer.', 'error');
  }
}

// ── SEARCH ──
function filterQuestions(q) {
  q = q.toLowerCase();
  document.querySelectorAll('.q-card').forEach(card => {
    const title = card.dataset.title || '';
    const body  = card.dataset.body  || '';
    card.style.display = (title.includes(q) || body.includes(q)) ? '' : 'none';
  });
}

// ── MOBILE HEADER ──
if (window.innerWidth <= 640) {
  document.querySelector('.mob-header').style.display = 'block';
}
</script>

<style>
@media (max-width: 640px) {
  .mob-header { display: block !important; position: sticky; top: 0; z-index: 100; }
}
</style>

</body>
</html>
