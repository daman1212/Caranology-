<?php
require 'db.php';
$err_email = $err_pass = $err_gen = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err_email = 'Please enter a valid email address.';
    } elseif (empty($pass)) {
        $err_pass = 'Password is required.';
    } else {
        $stmt = $conn->prepare("SELECT id, name, username, password, avatar FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $err_email = 'No account found with that email.';
        } else {
            $user = $result->fetch_assoc();
            if (!password_verify($pass, $user['password'])) {
                $err_pass = 'Incorrect password.';
            } else {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['avatar']    = $user['avatar'];
                header('Location: feed.php');
                exit;
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Caranology</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-wrap">
  <div class="card auth-card">
    <div class="card-body">

      <!-- Logo -->
      <div style="text-align:center;margin-bottom:28px">
        <div style="font-size:1.6rem;font-weight:800;color:var(--teal);letter-spacing:-0.03em;display:inline-flex;align-items:center;gap:8px">
          <span style="width:10px;height:10px;background:var(--teal);border-radius:50%;display:inline-block"></span>
          Caranology
        </div>
        <p style="font-size:0.85rem;color:var(--gray-400);margin-top:6px">Your car questions, answered.</p>
      </div>

      <h2 style="font-size:1.3rem;font-weight:700;margin-bottom:22px">Welcome back</h2>

      <?php if ($err_gen): ?>
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--r);padding:12px 14px;font-size:0.875rem;color:var(--red);margin-bottom:18px">
          <?= htmlspecialchars($err_gen) ?>
        </div>
      <?php endif; ?>

      <form method="POST" novalidate id="loginForm">
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-input <?= $err_email ? 'err' : '' ?>"
                 placeholder="you@example.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          <?php if ($err_email): ?>
            <div class="form-err"><?= htmlspecialchars($err_email) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <div style="position:relative">
            <input type="password" name="password" id="passInput"
                   class="form-input <?= $err_pass ? 'err' : '' ?>"
                   placeholder="Enter your password" style="padding-right:44px">
            <button type="button" onclick="togglePass()" title="Show/hide"
              style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--gray-400);font-size:1rem">
              👁
            </button>
          </div>
          <?php if ($err_pass): ?>
            <div class="form-err"><?= htmlspecialchars($err_pass) ?></div>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:8px">
          Log In
        </button>
      </form>

      <hr style="margin:22px 0">

      <div style="text-align:center;font-size:0.875rem;color:var(--gray-500)">
        Don't have an account?
        <a href="signup.php" style="color:var(--teal);font-weight:600;margin-left:4px">Sign up</a>
      </div>

    </div>
  </div>
</div>

<script>
function togglePass() {
  const p = document.getElementById('passInput');
  p.type = p.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
