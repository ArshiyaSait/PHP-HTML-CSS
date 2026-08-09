<?php
$result = null;
$errors = [];
$data   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']      = trim($_POST['name'] ?? '');
    $data['eid']       = trim($_POST['eid'] ?? '');
    $data['dept']      = trim($_POST['dept'] ?? '');
    $data['period']    = trim($_POST['period'] ?? '');
    $data['attendance']= (int)($_POST['attendance'] ?? 0);
    $data['targets']   = (int)($_POST['targets'] ?? 0);
    $data['quality']   = (int)($_POST['quality'] ?? 0);
    $data['teamwork']  = (int)($_POST['teamwork'] ?? 0);
    $data['initiative']= (int)($_POST['initiative'] ?? 0);

    if (empty($data['name'])) $errors['name'] = 'Required';
    if (empty($data['eid']))  $errors['eid']  = 'Required';
    if (empty($data['dept'])) $errors['dept'] = 'Required';
    if (empty($data['period'])) $errors['period'] = 'Required';

    if (empty($errors)) {
        $weights  = ['attendance'=>20,'targets'=>30,'quality'=>25,'teamwork'=>15,'initiative'=>10];
        $weighted = 0;
        foreach ($weights as $k => $w) $weighted += ($data[$k] / 10) * $w;
        $overall  = round($weighted, 1);
        $grade    = $overall>=90?'A+':($overall>=80?'A':($overall>=70?'B':($overall>=60?'C':($overall>=50?'D':'F'))));
        $rating   = $overall>=90?'Outstanding':($overall>=75?'Exceeds Expectations':($overall>=60?'Meets Expectations':($overall>=50?'Needs Improvement':'Unsatisfactory')));
        $result   = compact('weighted','overall','grade','rating');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Employee Performance Evaluation</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:720px;margin:0 auto;padding:48px 20px 80px;}
  .chip{display:inline-flex;background:#0a0a0a;color:#fff;padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;}
  h1{font-family:'Outfit',sans-serif;font-size:2.3rem;font-weight:800;letter-spacing:-0.5px;line-height:1.15;}
  .sub{color:#6b7280;margin-top:8px;font-size:0.95rem;margin-bottom:36px;}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:36px;box-shadow:0 8px 40px rgba(0,0,0,0.08);}
  .sec-label{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#9ca3af;margin-bottom:18px;padding-bottom:8px;border-bottom:1px solid #e5e7eb;}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
  .fg{display:flex;flex-direction:column;gap:5px;}
  .fg.full{grid-column:1/-1;}
  label{font-size:0.77rem;font-weight:600;letter-spacing:0.4px;text-transform:uppercase;}
  input,select{background:#fafafa;border:1.5px solid #e5e7eb;border-radius:10px;padding:11px 15px;color:#0a0a0a;font-size:0.94rem;width:100%;transition:all 0.2s;appearance:none;}
  input:focus,select:focus{outline:none;border-color:#0a0a0a;background:#fff;box-shadow:0 0 0 3px rgba(10,10,10,0.08);}
  select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;padding-right:40px;}
  .err-msg{color:#dc2626;font-size:0.76rem;font-weight:500;}
  .input-err{border-color:#dc2626!important;}
  .btn{width:100%;padding:14px;background:#0a0a0a;color:#fff;border:none;border-radius:10px;font-size:0.96rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;margin-top:22px;transition:transform 0.15s,box-shadow 0.15s;}
  .btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,0,0,0.18);}
  /* Rating slider */
  .rating-item{display:grid;grid-template-columns:1fr auto auto;align-items:center;gap:14px;margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid #f0f0f0;}
  .rating-item:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0;}
  .rating-info .ri-label{font-size:0.88rem;font-weight:600;}
  .rating-info .ri-weight{font-size:0.72rem;color:#9ca3af;}
  input[type=range]{width:180px;accent-color:#0a0a0a;height:4px;}
  .rating-val{font-family:'Outfit',sans-serif;font-size:1.2rem;font-weight:800;min-width:32px;text-align:center;}
  /* Result */
  .result-top{background:#0a0a0a;color:#fff;border-radius:14px;padding:28px;text-align:center;margin-top:24px;}
  .result-score{font-family:'Outfit',sans-serif;font-size:4rem;font-weight:800;line-height:1;}
  .result-grade{font-size:1.1rem;font-weight:600;margin-top:4px;opacity:0.7;}
  .result-rating{margin-top:10px;display:inline-block;background:rgba(255,255,255,0.1);padding:6px 16px;border-radius:50px;font-size:0.85rem;font-weight:600;}
  .breakdown-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:20px;}
  .breakdown-item{background:#fafafa;border:1px solid #e5e7eb;border-radius:10px;padding:14px;}
  .bi-label{font-size:0.74rem;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;}
  .bi-bar{height:6px;background:#f0f0f0;border-radius:50px;overflow:hidden;}
  .bi-fill{height:100%;background:#0a0a0a;border-radius:50px;}
  .bi-val{font-family:'Outfit',sans-serif;font-size:1.1rem;font-weight:800;margin-top:6px;}
  @media(max-width:560px){.grid{grid-template-columns:1fr;}.rating-item{grid-template-columns:1fr auto;}.breakdown-grid{grid-template-columns:1fr;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">⭐ Task 11</div>
  <h1>Employee Performance<br>Evaluation System</h1>
  <p class="sub">Rate employee performance across key criteria with weighted scoring.</p>

  <div class="card">
    <form method="POST">
      <div class="sec-label">Employee Details</div>
      <div class="grid" style="margin-bottom:24px;">
        <div class="fg"><label>Employee Name</label>
          <input type="text" name="name" placeholder="Full name" value="<?= htmlspecialchars($data['name']??'') ?>" class="<?= isset($errors['name'])?'input-err':'' ?>">
          <?php if(isset($errors['name'])): ?><span class="err-msg"><?= $errors['name'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Employee ID</label>
          <input type="text" name="eid" placeholder="e.g. EMP-001" value="<?= htmlspecialchars($data['eid']??'') ?>" class="<?= isset($errors['eid'])?'input-err':'' ?>">
          <?php if(isset($errors['eid'])): ?><span class="err-msg"><?= $errors['eid'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Department</label>
          <select name="dept" class="<?= isset($errors['dept'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach(['Computer Science','Human Resources','Finance','Marketing','Operations'] as $d): ?>
            <option value="<?= $d ?>" <?= (($data['dept']??'')===$d)?'selected':'' ?>><?= $d ?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['dept'])): ?><span class="err-msg"><?= $errors['dept'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Review Period</label>
          <select name="period" class="<?= isset($errors['period'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach(['Q1 2025','Q2 2025','Q3 2025','Q4 2025','Annual 2024','Annual 2025'] as $p): ?>
            <option value="<?= $p ?>" <?= (($data['period']??'')===$p)?'selected':'' ?>><?= $p ?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['period'])): ?><span class="err-msg"><?= $errors['period'] ?></span><?php endif; ?></div>
      </div>

      <div class="sec-label">Performance Ratings (1–10)</div>
      <?php
      $criteria = [
        'attendance'  => ['Attendance & Punctuality', '20% weight'],
        'targets'     => ['Target Achievement',       '30% weight'],
        'quality'     => ['Work Quality',             '25% weight'],
        'teamwork'    => ['Teamwork & Collaboration',  '15% weight'],
        'initiative'  => ['Initiative & Innovation',  '10% weight'],
      ];
      foreach($criteria as $key => [$label, $weight]): $val = $data[$key] ?? 5; ?>
      <div class="rating-item">
        <div class="rating-info">
          <div class="ri-label"><?= $label ?></div>
          <div class="ri-weight"><?= $weight ?></div>
        </div>
        <input type="range" name="<?= $key ?>" min="1" max="10" value="<?= $val ?>" oninput="this.nextElementSibling.textContent=this.value">
        <div class="rating-val"><?= $val ?></div>
      </div>
      <?php endforeach; ?>
      <button type="submit" class="btn">Evaluate Performance →</button>
    </form>

    <?php if($result): ?>
    <div class="result-top">
      <div style="font-size:0.78rem;color:#9ca3af;letter-spacing:1px;text-transform:uppercase;margin-bottom:8px;">Overall Score</div>
      <div class="result-score"><?= $result['overall'] ?></div>
      <div class="result-grade">Grade: <?= $result['grade'] ?></div>
      <div class="result-rating"><?= $result['rating'] ?></div>
    </div>
    <div class="breakdown-grid" style="margin-top:20px;">
      <?php foreach($criteria as $key => [$label, $weight]): $score = $data[$key]; ?>
      <div class="breakdown-item">
        <div class="bi-label"><?= $label ?></div>
        <div class="bi-bar"><div class="bi-fill" style="width:<?= $score*10 ?>%"></div></div>
        <div class="bi-val"><?= $score ?><span style="font-size:0.75rem;font-weight:400;color:#9ca3af;">/10</span></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
