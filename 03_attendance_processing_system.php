<?php
$result = null;
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']    = trim($_POST['name'] ?? '');
    $data['roll']    = trim($_POST['roll'] ?? '');
    $data['dept']    = trim($_POST['dept'] ?? '');
    $data['total']   = trim($_POST['total'] ?? '');
    $data['present'] = trim($_POST['present'] ?? '');

    if (empty($data['name']))    $errors['name']    = 'Required';
    if (empty($data['roll']))    $errors['roll']    = 'Required';
    if (empty($data['dept']))    $errors['dept']    = 'Required';
    if ($data['total'] === '')   $errors['total']   = 'Required';
    elseif ((int)$data['total'] < 1) $errors['total'] = 'Must be at least 1';
    if ($data['present'] === '') $errors['present'] = 'Required';
    elseif (!empty($data['total']) && (int)$data['present'] > (int)$data['total'])
        $errors['present'] = 'Cannot exceed total classes';

    if (empty($errors)) {
        $total   = (int)$data['total'];
        $present = (int)$data['present'];
        $absent  = $total - $present;
        $pct     = round(($present / $total) * 100, 2);
        $eligible = $pct >= 75;
        $needed  = 0;
        if (!$eligible) $needed = (int)ceil((0.75 * $total - $present) / 0.25);
        $result = compact('total','present','absent','pct','eligible','needed');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Processing System</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:700px;margin:0 auto;padding:48px 20px 80px;}
  .chip{display:inline-flex;align-items:center;background:#0a0a0a;color:#fff;
    padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;
    text-transform:uppercase;margin-bottom:16px;}
  h1{font-family:'Outfit',sans-serif;font-size:2.3rem;font-weight:800;line-height:1.15;letter-spacing:-0.5px;}
  .sub{color:#6b7280;margin-top:8px;font-size:0.95rem;margin-bottom:36px;}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:36px;box-shadow:0 8px 40px rgba(0,0,0,0.08);}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
  .fg{display:flex;flex-direction:column;gap:5px;}
  .fg.full{grid-column:1/-1;}
  label{font-size:0.77rem;font-weight:600;letter-spacing:0.4px;text-transform:uppercase;}
  input,select{background:#fafafa;border:1.5px solid #e5e7eb;border-radius:10px;
    padding:11px 15px;color:#0a0a0a;font-size:0.94rem;width:100%;transition:all 0.2s;appearance:none;}
  input:focus,select:focus{outline:none;border-color:#0a0a0a;background:#fff;box-shadow:0 0 0 3px rgba(10,10,10,0.08);}
  select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 14px center;padding-right:40px;}
  .err-msg{color:#dc2626;font-size:0.76rem;font-weight:500;}
  .input-err{border-color:#dc2626!important;}
  .btn{width:100%;padding:14px;background:#0a0a0a;color:#fff;border:none;border-radius:10px;
    font-size:0.96rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;margin-top:22px;
    transition:transform 0.15s,box-shadow 0.15s;}
  .btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,0,0,0.18);}
  .section-label{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;
    color:#9ca3af;margin-bottom:18px;padding-bottom:8px;border-bottom:1px solid #e5e7eb;}

  /* Progress ring */
  .result-area{margin-top:28px;}
  .ring-wrap{display:flex;flex-direction:column;align-items:center;margin-bottom:24px;}
  .ring-label{font-family:'Outfit',sans-serif;font-size:2.2rem;font-weight:800;margin-top:12px;}
  .ring-sublabel{font-size:0.78rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;}
  .stats-row{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:20px;}
  .stat-box{background:#fafafa;border:1px solid #e5e7eb;border-radius:12px;padding:16px;text-align:center;}
  .stat-box .v{font-size:1.8rem;font-weight:800;font-family:'Outfit',sans-serif;}
  .stat-box .l{font-size:0.7rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-top:4px;}
  .status-banner{border-radius:12px;padding:14px;text-align:center;font-family:'Outfit',sans-serif;font-size:1rem;font-weight:700;}
  .eligible-banner{background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;}
  .detained-banner{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;}
  .note-box{margin-top:12px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;
    padding:12px 16px;font-size:0.84rem;color:#92400e;text-align:center;}
  @media(max-width:560px){.grid{grid-template-columns:1fr;}.card{padding:22px;}.stats-row{grid-template-columns:1fr;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">📅 Task 03</div>
  <h1>Attendance Processing<br>System</h1>
  <p class="sub">Calculate student attendance percentage and exam eligibility.</p>

  <div class="card">
    <div class="section-label">Student Details</div>
    <form method="POST">
      <div class="grid">
        <div class="fg">
          <label>Student Name</label>
          <input type="text" name="name" placeholder="Full name" value="<?= htmlspecialchars($data['name']??'') ?>" class="<?= isset($errors['name'])?'input-err':'' ?>">
          <?php if(isset($errors['name'])): ?><span class="err-msg"><?= $errors['name'] ?></span><?php endif; ?>
        </div>
        <div class="fg">
          <label>Roll Number</label>
          <input type="text" name="roll" placeholder="e.g. 24CS001" value="<?= htmlspecialchars($data['roll']??'') ?>" class="<?= isset($errors['roll'])?'input-err':'' ?>">
          <?php if(isset($errors['roll'])): ?><span class="err-msg"><?= $errors['roll'] ?></span><?php endif; ?>
        </div>
        <div class="fg full">
          <label>Department</label>
          <select name="dept" class="<?= isset($errors['dept'])?'input-err':'' ?>">
            <option value="">Select Department</option>
            <?php foreach(['Computer Science','Information Technology','Mathematics','Physics','Chemistry','Commerce','Electronics'] as $d): ?>
            <option value="<?= $d ?>" <?= (($data['dept']??'')===$d)?'selected':'' ?>><?= $d ?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['dept'])): ?><span class="err-msg"><?= $errors['dept'] ?></span><?php endif; ?>
        </div>
        <div class="fg">
          <label>Total Classes Held</label>
          <input type="number" name="total" placeholder="e.g. 120" min="1" value="<?= htmlspecialchars($data['total']??'') ?>" class="<?= isset($errors['total'])?'input-err':'' ?>">
          <?php if(isset($errors['total'])): ?><span class="err-msg"><?= $errors['total'] ?></span><?php endif; ?>
        </div>
        <div class="fg">
          <label>Classes Attended</label>
          <input type="number" name="present" placeholder="e.g. 95" min="0" value="<?= htmlspecialchars($data['present']??'') ?>" class="<?= isset($errors['present'])?'input-err':'' ?>">
          <?php if(isset($errors['present'])): ?><span class="err-msg"><?= $errors['present'] ?></span><?php endif; ?>
        </div>
      </div>
      <button type="submit" class="btn">Calculate Attendance →</button>
    </form>

    <?php if ($result): ?>
    <?php
      $r = 60; $circ = 2 * M_PI * $r;
      $offset = $circ - ($result['pct']/100) * $circ;
      $clr = $result['eligible'] ? '#16a34a' : '#dc2626';
    ?>
    <div class="result-area">
      <div class="ring-wrap">
        <svg width="160" height="160" viewBox="0 0 160 160">
          <circle cx="80" cy="80" r="<?= $r ?>" fill="none" stroke="#f0f0f0" stroke-width="14"/>
          <circle cx="80" cy="80" r="<?= $r ?>" fill="none" stroke="<?= $clr ?>" stroke-width="14"
            stroke-linecap="round" stroke-dasharray="<?= $circ ?>" stroke-dashoffset="<?= $offset ?>"
            transform="rotate(-90 80 80)"/>
          <text x="80" y="74" text-anchor="middle" font-family="Outfit,sans-serif" font-size="24" font-weight="800" fill="#0a0a0a"><?= $result['pct'] ?>%</text>
          <text x="80" y="92" text-anchor="middle" font-family="Inter,sans-serif" font-size="11" fill="#9ca3af">ATTENDANCE</text>
        </svg>
        <div class="ring-sublabel"><?= htmlspecialchars($data['name']) ?> &nbsp;|&nbsp; <?= htmlspecialchars($data['roll']) ?></div>
      </div>

      <div class="stats-row">
        <div class="stat-box"><div class="v"><?= $result['total'] ?></div><div class="l">Total</div></div>
        <div class="stat-box"><div class="v" style="color:#16a34a"><?= $result['present'] ?></div><div class="l">Present</div></div>
        <div class="stat-box"><div class="v" style="color:#dc2626"><?= $result['absent'] ?></div><div class="l">Absent</div></div>
      </div>

      <div class="status-banner <?= $result['eligible']?'eligible-banner':'detained-banner' ?>">
        <?= $result['eligible'] ? '✓ Eligible for Examinations' : '✗ Detained — Below 75% Requirement' ?>
      </div>

      <?php if ($result['needed'] > 0): ?>
      <div class="note-box">⚠ Needs <strong><?= $result['needed'] ?></strong> more consecutive classes to reach 75% eligibility.</div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
