<?php
$result = null;
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']  = trim($_POST['name'] ?? '');
    $data['email'] = trim($_POST['email'] ?? '');
    $data['phone'] = trim($_POST['phone'] ?? '');
    $data['dob']   = trim($_POST['dob'] ?? '');
    $data['score'] = trim($_POST['score'] ?? '');
    $data['id']    = trim($_POST['id'] ?? '');

    if (empty($data['name'])) $errors['name'] = 'Required';
    elseif (!preg_match('/^[A-Za-z\s]+$/', $data['name'])) $errors['name'] = 'Letters only';

    if (empty($data['email'])) $errors['email'] = 'Required';
    elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email';

    if (empty($data['phone'])) $errors['phone'] = 'Required';
    elseif (!preg_match('/^[0-9]{10}$/', $data['phone'])) $errors['phone'] = 'Must be 10 digits';

    if (empty($data['dob'])) $errors['dob'] = 'Required';
    else {
        $age = (int)date_diff(date_create($data['dob']), date_create('today'))->y;
        if ($age < 16 || $age > 60) $errors['dob'] = 'Age must be 16–60';
    }

    if ($data['score'] === '') $errors['score'] = 'Required';
    elseif (!is_numeric($data['score']) || $data['score'] < 0 || $data['score'] > 100)
        $errors['score'] = 'Score must be 0–100';

    if (empty($data['id'])) $errors['id'] = 'Required';
    elseif (!preg_match('/^APP-[0-9]{4}$/', $data['id'])) $errors['id'] = 'Format: APP-1234';

    if (empty($errors)) {
        $score = (float)$data['score'];
        $age   = (int)date_diff(date_create($data['dob']), date_create('today'))->y;
        $grade = $score>=90?'A+':($score>=80?'A':($score>=70?'B':($score>=60?'C':($score>=50?'D':'F'))));
        $result = ['status'=>$score>=50?'APPROVED':'REJECTED','score'=>$score,'age'=>$age,'grade'=>$grade,'eligible'=>$score>=50];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Applicant Validation System</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
  body { background: #f4f4f4; font-family: 'Inter', sans-serif; color: #0a0a0a; min-height: 100vh; }
  .page-wrap { max-width: 700px; margin: 0 auto; padding: 48px 20px 80px; }
  .chip { display: inline-flex; align-items: center; gap: 6px; background: #0a0a0a; color: #fff;
    padding: 5px 14px; border-radius: 50px; font-size: 11px; font-weight: 600; letter-spacing: 1px;
    text-transform: uppercase; margin-bottom: 16px; }
  h1 { font-family: 'Outfit', sans-serif; font-size: 2.3rem; font-weight: 800; line-height: 1.15; letter-spacing: -0.5px; }
  p.sub { color: #6b7280; margin-top: 8px; font-size: 0.95rem; margin-bottom: 36px; }
  .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 20px; padding: 36px; box-shadow: 0 8px 40px rgba(0,0,0,0.08); }
  .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
  .fg { display: flex; flex-direction: column; gap: 5px; }
  .fg.full { grid-column: 1 / -1; }
  label { font-size: 0.77rem; font-weight: 600; color: #0a0a0a; letter-spacing: 0.4px; text-transform: uppercase; }
  input { background: #fafafa; border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 11px 15px;
    color: #0a0a0a; font-size: 0.94rem; width: 100%; transition: all 0.2s; }
  input:focus { outline: none; border-color: #0a0a0a; background: #fff; box-shadow: 0 0 0 3px rgba(10,10,10,0.08); }
  input::placeholder { color: #9ca3af; }
  .err-msg { color: #dc2626; font-size: 0.76rem; font-weight: 500; }
  .input-err { border-color: #dc2626 !important; }
  .btn { width: 100%; padding: 14px; background: #0a0a0a; color: #fff; border: none; border-radius: 10px;
    font-size: 0.96rem; font-weight: 700; font-family: 'Outfit', sans-serif; cursor: pointer;
    margin-top: 22px; transition: transform 0.15s, box-shadow 0.15s; }
  .btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,0,0,0.18); }
  .section-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px;
    color: #9ca3af; margin-bottom: 18px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb; }

  /* Result */
  .result-box { margin-top: 28px; border-radius: 16px; padding: 28px; border: 1.5px solid; text-align: center; }
  .approved-box { background: #f0fdf4; border-color: #bbf7d0; }
  .rejected-box { background: #fef2f2; border-color: #fecaca; }
  .verdict { font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 800; margin-bottom: 6px; }
  .approved-box .verdict { color: #16a34a; }
  .rejected-box .verdict { color: #dc2626; }
  .result-name { font-size: 0.9rem; color: #6b7280; margin-bottom: 20px; }
  .stats { display: flex; justify-content: center; gap: 0; margin-top: 4px; }
  .stat { text-align: center; padding: 16px 28px; }
  .stat:not(:last-child) { border-right: 1px solid #e5e7eb; }
  .stat .v { font-size: 2rem; font-weight: 800; font-family: 'Outfit', sans-serif; color: #0a0a0a; }
  .stat .l { font-size: 0.7rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-top: 3px; }
  .result-note { margin-top: 16px; font-size: 0.84rem; color: #6b7280; }
  @media(max-width:560px) { .grid { grid-template-columns: 1fr; } .card { padding: 22px; } .stats { flex-direction: column; gap: 8px; } .stat:not(:last-child){border-right:none;border-bottom:1px solid #e5e7eb;} }
</style>
</head>
<body>
<div class="page-wrap">
  <div class="chip">✅ Task 02</div>
  <h1>Applicant Validation<br>System</h1>
  <p class="sub">Validate applicant credentials and determine eligibility status.</p>

  <div class="card">
    <div class="section-label">Applicant Information</div>
    <form method="POST">
      <div class="grid">
        <div class="fg full">
          <label>Full Name</label>
          <input type="text" name="name" placeholder="Letters only" value="<?= htmlspecialchars($data['name']??'') ?>" class="<?= isset($errors['name'])?'input-err':'' ?>">
          <?php if(isset($errors['name'])): ?><span class="err-msg"><?= $errors['name'] ?></span><?php endif; ?>
        </div>
        <div class="fg">
          <label>Email</label>
          <input type="text" name="email" placeholder="email@domain.com" value="<?= htmlspecialchars($data['email']??'') ?>" class="<?= isset($errors['email'])?'input-err':'' ?>">
          <?php if(isset($errors['email'])): ?><span class="err-msg"><?= $errors['email'] ?></span><?php endif; ?>
        </div>
        <div class="fg">
          <label>Phone Number</label>
          <input type="text" name="phone" placeholder="10 digits" value="<?= htmlspecialchars($data['phone']??'') ?>" class="<?= isset($errors['phone'])?'input-err':'' ?>">
          <?php if(isset($errors['phone'])): ?><span class="err-msg"><?= $errors['phone'] ?></span><?php endif; ?>
        </div>
        <div class="fg">
          <label>Date of Birth</label>
          <input type="date" name="dob" value="<?= htmlspecialchars($data['dob']??'') ?>" class="<?= isset($errors['dob'])?'input-err':'' ?>">
          <?php if(isset($errors['dob'])): ?><span class="err-msg"><?= $errors['dob'] ?></span><?php endif; ?>
        </div>
        <div class="fg">
          <label>Entrance Score (0–100)</label>
          <input type="number" name="score" placeholder="e.g. 75" min="0" max="100" value="<?= htmlspecialchars($data['score']??'') ?>" class="<?= isset($errors['score'])?'input-err':'' ?>">
          <?php if(isset($errors['score'])): ?><span class="err-msg"><?= $errors['score'] ?></span><?php endif; ?>
        </div>
        <div class="fg full">
          <label>Applicant ID</label>
          <input type="text" name="id" placeholder="Format: APP-1234" value="<?= htmlspecialchars($data['id']??'') ?>" class="<?= isset($errors['id'])?'input-err':'' ?>">
          <?php if(isset($errors['id'])): ?><span class="err-msg"><?= $errors['id'] ?></span><?php endif; ?>
        </div>
      </div>
      <button type="submit" class="btn">Validate Applicant →</button>
    </form>

    <?php if ($result): ?>
    <div class="result-box <?= $result['eligible']?'approved-box':'rejected-box' ?>">
      <div class="verdict"><?= $result['eligible']?'✓ Approved':'✗ Rejected' ?></div>
      <div class="result-name"><?= htmlspecialchars($data['name']) ?> &nbsp;·&nbsp; <?= htmlspecialchars($data['id']) ?></div>
      <div class="stats">
        <div class="stat"><div class="v"><?= $result['score'] ?>%</div><div class="l">Score</div></div>
        <div class="stat"><div class="v"><?= $result['grade'] ?></div><div class="l">Grade</div></div>
        <div class="stat"><div class="v"><?= $result['age'] ?> yrs</div><div class="l">Age</div></div>
      </div>
      <p class="result-note"><?= $result['eligible'] ? 'The applicant meets all eligibility criteria (min. score: 50%).' : 'Applicant did not meet the minimum score requirement of 50%.' ?></p>
    </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
