<?php
require 'db.php';
$errors = [];
$vals   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vals['name']     = trim($_POST['name']     ?? '');
    $vals['email']    = trim($_POST['email']    ?? '');
    $vals['username'] = trim($_POST['username'] ?? '');
    $vals['dob']      = trim($_POST['dob']      ?? '');
    $pass             = $_POST['password']      ?? '';
    $pass2            = $_POST['password2']     ?? '';

    if (empty($vals['name']))                             $errors['name']     = 'Name is required.';
    if (!filter_var($vals['email'], FILTER_VALIDATE_EMAIL)) $errors['email']  = 'Enter a valid email.';
    if (strlen($vals['username']) < 3)                    $errors['username'] = 'Username must be at least 3 characters.';
    if (empty($vals['dob']))                              $errors['dob']      = 'Date of birth is required.';
    if (strlen($pass) < 6)                                $errors['password'] = 'Password must be at least 6 characters.';
    if ($pass !== $pass2)                                 $errors['password2']= 'Passwords do not match.';

    // Check duplicates
    if (empty($errors['email'])) {
        $s = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $s->bind_param("s", $vals['email']); $s->execute();
        if ($s->get_result()->num_rows > 0) $errors['email'] = 'This email is already registered.';
        $s->close();
    }
    if (empty($errors['username'])) {
        $s = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $s->bind_param("s", $vals['username']); $s->execute();
        if ($s->get_result()->num_rows > 0) $errors['username'] = 'Username already taken.';
        $s->close();
    }

    // Handle avatar upload
    $avatar_path = null;
    if (!empty($_FILES['avatar']['name'])) {
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors['avatar'] = 'Only JPG, PNG, GIF, WEBP allowed.';
        } elseif ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
            $errors['avatar'] = 'Image must be under 2MB.';
        } else {
            $fname = 'uploads/' . uniqid('av_') . '.' . $ext;
            move_uploaded_file($_FILES['avatar']['tmp_name'], $fname);
            $avatar_path = $fname;
        }
    }

    if (empty($errors)) {
        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name,email,username,dob,password,avatar) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $vals['name'], $vals['email'], $vals['username'], $vals['dob'], $hashed, $avatar_path);
        if ($stmt->execute()) {
            $_SESSION['user_id']   = $conn->insert_id;
            $_SESSION['user_name'] = $vals['name'];
            $_SESSION['username']  = $vals['username'];
            $_SESSION['avatar']    = $avatar_path;
            header('Location: feed.php');
            exit;
        } else {
            $errors['gen'] = 'Something went wrong. Please try again.';
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
<title>Sign Up — Caranology</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-wrap">
  <div class="card auth-card">
    <div class="card-body">

      <div style="text-align:center;margin-bottom:24px">
        <div style="font-size:1.6rem;font-weight:800;color:var(--teal);letter-spacing:-0.03em;display:inline-flex;align-items:center;gap:8px">
          <span style="width:10px;height:10px;background:var(--teal);border-radius:50%;display:inline-block"></span>
          Caranology
        </div>
        <p style="font-size:0.85rem;color:var(--gray-400);margin-top:6px">Join the community</p>
      </div>

      <h2 style="font-size:1.3rem;font-weight:700;margin-bottom:20px">Create your account</h2>

      <?php if (!empty($errors['gen'])): ?>
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--r);padding:12px 14px;font-size:0.875rem;color:var(--red);margin-bottom:16px">
          <?= htmlspecialchars($errors['gen']) ?>
        </div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" novalidate>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-input <?= isset($errors['name']) ? 'err' : '' ?>"
                   placeholder="Daman Tiwana" value="<?= htmlspecialchars($vals['name'] ?? '') ?>">
            <?php if (isset($errors['name'])): ?><div class="form-err"><?= $errors['name'] ?></div><?php endif; ?>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-input <?= isset($errors['username']) ? 'err' : '' ?>"
                   placeholder="daman123" value="<?= htmlspecialchars($vals['username'] ?? '') ?>">
            <?php if (isset($errors['username'])): ?><div class="form-err"><?= $errors['username'] ?></div><?php endif; ?>
          </div>
        </div>

        <div class="form-group" style="margin-top:14px">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-input <?= isset($errors['email']) ? 'err' : '' ?>"
                 placeholder="you@example.com" value="<?= htmlspecialchars($vals['email'] ?? '') ?>">
          <?php if (isset($errors['email'])): ?><div class="form-err"><?= $errors['email'] ?></div><?php endif; ?>
        </div>

        <div class="form-group">
          <label class="form-label">Date of Birth</label>
          <input type="date" name="dob" class="form-input <?= isset($errors['dob']) ? 'err' : '' ?>"
                 value="<?= htmlspecialchars($vals['dob'] ?? '') ?>">
          <?php if (isset($errors['dob'])): ?><div class="form-err"><?= $errors['dob'] ?></div><?php endif; ?>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-input <?= isset($errors['password']) ? 'err' : '' ?>"
                   placeholder="Min 6 chars">
            <?php if (isset($errors['password'])): ?><div class="form-err"><?= $errors['password'] ?></div><?php endif; ?>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Confirm</label>
            <input type="password" name="password2" class="form-input <?= isset($errors['password2']) ? 'err' : '' ?>"
                   placeholder="Repeat password">
            <?php if (isset($errors['password2'])): ?><div class="form-err"><?= $errors['password2'] ?></div><?php endif; ?>
          </div>
        </div>

        <div class="form-group" style="margin-top:14px">
          <label class="form-label">Profile Photo <span style="font-weight:400;text-transform:none;color:var(--gray-400)">(optional)</span></label>
          <input type="file" name="avatar" accept="image/*" class="form-input" style="padding:8px">
          <?php if (isset($errors['avatar'])): ?><div class="form-err"><?= $errors['avatar'] ?></div><?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:4px">
          Create Account
        </button>
      </form>

      <hr style="margin:20px 0">

      <div style="text-align:center;font-size:0.875rem;color:var(--gray-500)">
        Already have an account?
        <a href="login.php" style="color:var(--teal);font-weight:600;margin-left:4px">Log in</a>
      </div>

    </div>
  </div>
</div>
</body>
</html>
