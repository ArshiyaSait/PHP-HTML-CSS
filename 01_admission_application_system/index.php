<?php
$success = false;
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['fullname','dob','gender','email','phone','address','course','qualification','year'];
    foreach ($fields as $f) {
        $data[$f] = trim($_POST[$f] ?? '');
        if (empty($data[$f])) $errors[$f] = 'Required';
    }
    if (empty($errors)) $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admission Application System</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
  :root {
    --bg: #f4f4f4;
    --surface: #ffffff;
    --text: #0a0a0a;
    --text-secondary: #6b7280;
    --text-muted: #9ca3af;
    --border: #e5e7eb;
    --border-focus: #0a0a0a;
    --accent: #0a0a0a;
    --error: #dc2626;
    --success-bg: #f0fdf4;
    --success-border: #bbf7d0;
    --success-text: #16a34a;
    --radius: 12px;
    --shadow: 0 1px 4px rgba(0,0,0,0.06), 0 4px 20px rgba(0,0,0,0.06);
    --shadow-lg: 0 8px 40px rgba(0,0,0,0.10);
  }
  body { background: var(--bg); font-family: 'Inter', sans-serif; color: var(--text); min-height: 100vh; }
  .page-wrap { max-width: 780px; margin: 0 auto; padding: 48px 20px 80px; }

  /* Header */
  .page-header { margin-bottom: 40px; }
  .chip { display: inline-flex; align-items: center; gap: 6px; background: var(--text); color: #fff;
    padding: 5px 14px; border-radius: 50px; font-size: 11px; font-weight: 600; letter-spacing: 1px;
    text-transform: uppercase; margin-bottom: 16px; }
  .page-header h1 { font-family: 'Outfit', sans-serif; font-size: 2.4rem; font-weight: 800;
    color: var(--text); line-height: 1.15; letter-spacing: -0.5px; }
  .page-header p { color: var(--text-secondary); margin-top: 8px; font-size: 0.95rem; }
  .divider { height: 1px; background: var(--border); margin: 28px 0; }

  /* Card */
  .card { background: var(--surface); border: 1px solid var(--border); border-radius: 20px;
    padding: 40px; box-shadow: var(--shadow-lg); }

  /* Form */
  .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
  .form-group { display: flex; flex-direction: column; gap: 6px; }
  .form-group.full { grid-column: 1 / -1; }
  label { font-size: 0.78rem; font-weight: 600; color: var(--text); letter-spacing: 0.4px; text-transform: uppercase; }
  input, select, textarea {
    background: #fafafa; border: 1.5px solid var(--border); border-radius: var(--radius);
    padding: 12px 16px; color: var(--text); font-size: 0.94rem; font-family: 'Inter', sans-serif;
    transition: border-color 0.2s, box-shadow 0.2s; width: 100%; appearance: none;
  }
  input:focus, select:focus, textarea:focus {
    outline: none; border-color: var(--border-focus); background: #fff;
    box-shadow: 0 0 0 3px rgba(10,10,10,0.08);
  }
  input::placeholder, textarea::placeholder { color: var(--text-muted); }
  select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 14px center; padding-right: 40px; cursor: pointer; }
  textarea { resize: vertical; min-height: 72px; }
  .err-msg { color: var(--error); font-size: 0.76rem; font-weight: 500; }
  .input-err { border-color: var(--error) !important; }

  /* Button */
  .btn-primary { width: 100%; padding: 15px; background: var(--text); color: #fff; border: none;
    border-radius: var(--radius); font-size: 0.97rem; font-weight: 700; font-family: 'Outfit', sans-serif;
    cursor: pointer; margin-top: 28px; letter-spacing: 0.3px; transition: transform 0.15s, box-shadow 0.15s; }
  .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,0,0,0.2); }
  .btn-primary:active { transform: translateY(0); }

  /* Success */
  .success-banner { background: var(--success-bg); border: 1px solid var(--success-border);
    border-radius: 14px; padding: 28px; text-align: center; margin-bottom: 28px; }
  .success-icon { font-size: 2.5rem; margin-bottom: 10px; }
  .success-banner h2 { font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 700; color: var(--success-text); }
  .success-banner p { color: #4b7a5b; margin-top: 6px; font-size: 0.88rem; }
  .summary-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
  .summary-table tr { border-bottom: 1px solid var(--border); }
  .summary-table tr:last-child { border-bottom: none; }
  .summary-table td { padding: 11px 4px; font-size: 0.9rem; }
  .summary-table td:first-child { color: var(--text-secondary); font-weight: 500; width: 40%; }
  .summary-table td:last-child { color: var(--text); font-weight: 600; }
  .btn-outline { display: inline-block; padding: 11px 28px; border: 1.5px solid var(--text);
    border-radius: 50px; font-size: 0.88rem; font-weight: 600; color: var(--text); cursor: pointer;
    background: transparent; margin-top: 20px; transition: all 0.2s; font-family: 'Outfit',sans-serif; }
  .btn-outline:hover { background: var(--text); color: #fff; }

  /* Section label */
  .section-label { font-family: 'Outfit', sans-serif; font-size: 0.75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 20px;
    padding-bottom: 10px; border-bottom: 1px solid var(--border); }

  @media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } .card { padding: 24px; } h1 { font-size: 1.8rem; } }
</style>
</head>
<body>
<div class="page-wrap">
  <div class="page-header">
    <div class="chip">📋 Task 01</div>
    <h1>Admission Application<br>System</h1>
    <p>Submit your application to enroll in your desired academic program.</p>
  </div>

  <div class="card">
    <?php if ($success): ?>
      <div class="success-banner">
        <div class="success-icon">🎓</div>
        <h2>Application Submitted!</h2>
        <p>Your application has been received. We'll contact you at <?= htmlspecialchars($data['email']) ?>.</p>
      </div>
      <div class="section-label">Application Summary</div>
      <table class="summary-table">
        <tr><td>Full Name</td><td><?= htmlspecialchars($data['fullname']) ?></td></tr>
        <tr><td>Date of Birth</td><td><?= htmlspecialchars($data['dob']) ?></td></tr>
        <tr><td>Gender</td><td><?= htmlspecialchars($data['gender']) ?></td></tr>
        <tr><td>Email</td><td><?= htmlspecialchars($data['email']) ?></td></tr>
        <tr><td>Phone</td><td><?= htmlspecialchars($data['phone']) ?></td></tr>
        <tr><td>Course Applied</td><td><?= htmlspecialchars($data['course']) ?></td></tr>
        <tr><td>Qualification</td><td><?= htmlspecialchars($data['qualification']) ?></td></tr>
        <tr><td>Year of Passing</td><td><?= htmlspecialchars($data['year']) ?></td></tr>
        <tr><td>Address</td><td><?= htmlspecialchars($data['address']) ?></td></tr>
      </table>
      <div style="text-align:center;">
        <button class="btn-outline" onclick="window.location='<?= $_SERVER['PHP_SELF'] ?>'">Submit Another Application</button>
      </div>
    <?php else: ?>
      <div class="section-label">Personal Details</div>
      <form method="POST">
        <div class="form-grid">
          <div class="form-group full">
            <label>Full Name</label>
            <input type="text" name="fullname" placeholder="Enter your full name" value="<?= htmlspecialchars($data['fullname'] ?? '') ?>" class="<?= isset($errors['fullname']) ? 'input-err' : '' ?>">
            <?php if(isset($errors['fullname'])): ?><span class="err-msg"><?= $errors['fullname'] ?></span><?php endif; ?>
          </div>
          <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" name="dob" value="<?= htmlspecialchars($data['dob'] ?? '') ?>" class="<?= isset($errors['dob']) ? 'input-err' : '' ?>">
            <?php if(isset($errors['dob'])): ?><span class="err-msg"><?= $errors['dob'] ?></span><?php endif; ?>
          </div>
          <div class="form-group">
            <label>Gender</label>
            <select name="gender" class="<?= isset($errors['gender']) ? 'input-err' : '' ?>">
              <option value="">Select Gender</option>
              <option value="Male" <?= (($data['gender']??'')==='Male')?'selected':'' ?>>Male</option>
              <option value="Female" <?= (($data['gender']??'')==='Female')?'selected':'' ?>>Female</option>
              <option value="Other" <?= (($data['gender']??'')==='Other')?'selected':'' ?>>Other</option>
            </select>
            <?php if(isset($errors['gender'])): ?><span class="err-msg"><?= $errors['gender'] ?></span><?php endif; ?>
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="you@example.com" value="<?= htmlspecialchars($data['email'] ?? '') ?>" class="<?= isset($errors['email']) ? 'input-err' : '' ?>">
            <?php if(isset($errors['email'])): ?><span class="err-msg"><?= $errors['email'] ?></span><?php endif; ?>
          </div>
          <div class="form-group">
            <label>Phone Number</label>
            <input type="tel" name="phone" placeholder="10-digit number" value="<?= htmlspecialchars($data['phone'] ?? '') ?>" class="<?= isset($errors['phone']) ? 'input-err' : '' ?>">
            <?php if(isset($errors['phone'])): ?><span class="err-msg"><?= $errors['phone'] ?></span><?php endif; ?>
          </div>
          <div class="form-group full">
            <label>Address</label>
            <textarea name="address" placeholder="Enter your full address" class="<?= isset($errors['address']) ? 'input-err' : '' ?>"><?= htmlspecialchars($data['address'] ?? '') ?></textarea>
            <?php if(isset($errors['address'])): ?><span class="err-msg"><?= $errors['address'] ?></span><?php endif; ?>
          </div>

          <div style="grid-column:1/-1; margin-top:8px;" class="section-label">Academic Details</div>

          <div class="form-group">
            <label>Course Applied For</label>
            <select name="course" class="<?= isset($errors['course']) ? 'input-err' : '' ?>">
              <option value="">Select Course</option>
              <?php foreach(['B.Sc Computer Science','BCA','MCA','MBA','B.Com','B.Tech','B.Sc Mathematics'] as $c): ?>
              <option value="<?= $c ?>" <?= (($data['course']??'')===$c)?'selected':'' ?>><?= $c ?></option>
              <?php endforeach; ?>
            </select>
            <?php if(isset($errors['course'])): ?><span class="err-msg"><?= $errors['course'] ?></span><?php endif; ?>
          </div>
          <div class="form-group">
            <label>Highest Qualification</label>
            <select name="qualification" class="<?= isset($errors['qualification']) ? 'input-err' : '' ?>">
              <option value="">Select Qualification</option>
              <?php foreach(['10th','12th','Diploma','UG Degree','PG Degree'] as $q): ?>
              <option value="<?= $q ?>" <?= (($data['qualification']??'')===$q)?'selected':'' ?>><?= $q ?></option>
              <?php endforeach; ?>
            </select>
            <?php if(isset($errors['qualification'])): ?><span class="err-msg"><?= $errors['qualification'] ?></span><?php endif; ?>
          </div>
          <div class="form-group full">
            <label>Year of Passing</label>
            <input type="number" name="year" placeholder="e.g. 2024" min="1990" max="2030" value="<?= htmlspecialchars($data['year'] ?? '') ?>" class="<?= isset($errors['year']) ? 'input-err' : '' ?>">
            <?php if(isset($errors['year'])): ?><span class="err-msg"><?= $errors['year'] ?></span><?php endif; ?>
          </div>
        </div>
        <button type="submit" class="btn-primary">Submit Application →</button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
