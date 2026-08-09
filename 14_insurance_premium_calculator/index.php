<?php
$result = null;
$errors = [];
$data   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']     = trim($_POST['name'] ?? '');
    $data['age']      = trim($_POST['age'] ?? '');
    $data['gender']   = trim($_POST['gender'] ?? '');
    $data['type']     = trim($_POST['type'] ?? '');
    $data['coverage'] = trim($_POST['coverage'] ?? '');
    $data['term']     = trim($_POST['term'] ?? '');
    $data['smoker']   = trim($_POST['smoker'] ?? '');
    $data['medical']  = trim($_POST['medical'] ?? '');

    if (empty($data['name']))     $errors['name']     = 'Required';
    if ($data['age'] === '' || $data['age'] < 18 || $data['age'] > 70) $errors['age'] = 'Age must be 18–70';
    if (empty($data['gender']))   $errors['gender']   = 'Required';
    if (empty($data['type']))     $errors['type']     = 'Required';
    if ($data['coverage'] === '') $errors['coverage'] = 'Required';
    if ($data['term'] === '')     $errors['term']     = 'Required';

    if (empty($errors)) {
        $age      = (int)$data['age'];
        $coverage = (float)$data['coverage'];
        $term     = (int)$data['term'];

        // Base rate per 1000 coverage
        $base_rates = ['Term Life'=>1.5,'Whole Life'=>3.2,'Health'=>2.8,'Accident'=>1.2,'Vehicle'=>2.0];
        $base = $base_rates[$data['type']] ?? 2.0;

        // Age factor
        $age_factor = $age < 30 ? 1.0 : ($age < 40 ? 1.2 : ($age < 50 ? 1.5 : ($age < 60 ? 2.0 : 2.8)));
        // Gender factor
        $gender_factor = $data['gender'] === 'Female' ? 0.95 : 1.0;
        // Smoker
        $smoker_factor = ($data['smoker'] === 'Yes') ? 1.4 : 1.0;
        // Medical history
        $medical_factor = ($data['medical'] === 'Yes') ? 1.3 : 1.0;

        $annual = round(($coverage / 1000) * $base * $age_factor * $gender_factor * $smoker_factor * $medical_factor * 12, 2);
        $monthly = round($annual / 12, 2);
        $quarterly = round($annual / 4, 2);
        $total_premium = round($annual * $term, 2);

        $result = compact('annual','monthly','quarterly','total_premium','coverage','term');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Insurance Premium Calculator</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:700px;margin:0 auto;padding:48px 20px 80px;}
  .chip{display:inline-flex;background:#0a0a0a;color:#fff;padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;}
  h1{font-family:'Outfit',sans-serif;font-size:2.3rem;font-weight:800;letter-spacing:-0.5px;line-height:1.15;}
  .sub{color:#6b7280;margin-top:8px;font-size:0.95rem;margin-bottom:36px;}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:36px;box-shadow:0 8px 40px rgba(0,0,0,0.08);margin-bottom:20px;}
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
  /* Result */
  .premium-hero{background:#0a0a0a;color:#fff;border-radius:16px;padding:32px;text-align:center;margin-top:24px;}
  .ph-label{font-size:0.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;}
  .ph-amount{font-family:'Outfit',sans-serif;font-size:3.5rem;font-weight:800;line-height:1;}
  .ph-period{font-size:0.85rem;color:#9ca3af;margin-top:4px;}
  .premium-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:20px;}
  .pg-item{background:#fafafa;border:1px solid #e5e7eb;border-radius:12px;padding:16px;text-align:center;}
  .pg-val{font-family:'Outfit',sans-serif;font-size:1.3rem;font-weight:800;}
  .pg-lbl{font-size:0.7rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-top:4px;}
  .coverage-info{background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;margin-top:14px;font-size:0.85rem;color:#92400e;text-align:center;}
  @media(max-width:560px){.grid{grid-template-columns:1fr;}.premium-grid{grid-template-columns:1fr 1fr;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">🛡️ Task 14</div>
  <h1>Insurance Premium<br>Calculator</h1>
  <p class="sub">Calculate your insurance premium based on age, risk factors and coverage amount.</p>

  <div class="card">
    <div class="sec-label">Policyholder Details</div>
    <form method="POST">
      <div class="grid">
        <div class="fg"><label>Full Name</label>
          <input type="text" name="name" placeholder="Full name" value="<?= htmlspecialchars($data['name']??'') ?>" class="<?= isset($errors['name'])?'input-err':'' ?>">
          <?php if(isset($errors['name'])): ?><span class="err-msg"><?= $errors['name'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Age</label>
          <input type="number" name="age" placeholder="18–70" min="18" max="70" value="<?= htmlspecialchars($data['age']??'') ?>" class="<?= isset($errors['age'])?'input-err':'' ?>">
          <?php if(isset($errors['age'])): ?><span class="err-msg"><?= $errors['age'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Gender</label>
          <select name="gender" class="<?= isset($errors['gender'])?'input-err':'' ?>">
            <option value="">Select</option>
            <option value="Male" <?= (($data['gender']??'')==='Male')?'selected':'' ?>>Male</option>
            <option value="Female" <?= (($data['gender']??'')==='Female')?'selected':'' ?>>Female</option>
          </select>
          <?php if(isset($errors['gender'])): ?><span class="err-msg"><?= $errors['gender'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Insurance Type</label>
          <select name="type" class="<?= isset($errors['type'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach(['Term Life','Whole Life','Health','Accident','Vehicle'] as $t): ?>
            <option value="<?= $t ?>" <?= (($data['type']??'')===$t)?'selected':'' ?>><?= $t ?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['type'])): ?><span class="err-msg"><?= $errors['type'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Coverage Amount (₹)</label>
          <select name="coverage" class="<?= isset($errors['coverage'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach([100000,250000,500000,1000000,2500000,5000000] as $c): ?>
            <option value="<?= $c ?>" <?= (($data['coverage']??'')==$c)?'selected':'' ?>>₹<?= number_format($c) ?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['coverage'])): ?><span class="err-msg"><?= $errors['coverage'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Policy Term (Years)</label>
          <select name="term" class="<?= isset($errors['term'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach([5,10,15,20,25,30] as $t): ?>
            <option value="<?= $t ?>" <?= (($data['term']??'')===$t)?'selected':'' ?>><?= $t ?> years</option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['term'])): ?><span class="err-msg"><?= $errors['term'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Are you a smoker?</label>
          <select name="smoker">
            <option value="No" <?= (($data['smoker']??'No')==='No')?'selected':'' ?>>No</option>
            <option value="Yes" <?= (($data['smoker']??'')==='Yes')?'selected':'' ?>>Yes</option>
          </select></div>
        <div class="fg"><label>Pre-existing Medical Condition?</label>
          <select name="medical">
            <option value="No" <?= (($data['medical']??'No')==='No')?'selected':'' ?>>No</option>
            <option value="Yes" <?= (($data['medical']??'')==='Yes')?'selected':'' ?>>Yes</option>
          </select></div>
      </div>
      <button type="submit" class="btn">Calculate Premium →</button>
    </form>

    <?php if($result): ?>
    <div class="premium-hero">
      <div class="ph-label">Monthly Premium</div>
      <div class="ph-amount">₹<?= number_format($result['monthly'],2) ?></div>
      <div class="ph-period">per month &nbsp;·&nbsp; <?= htmlspecialchars($data['type']) ?> Insurance</div>
    </div>
    <div class="premium-grid">
      <div class="pg-item"><div class="pg-val">₹<?= number_format($result['quarterly'],2) ?></div><div class="pg-lbl">Quarterly</div></div>
      <div class="pg-item"><div class="pg-val">₹<?= number_format($result['annual'],2) ?></div><div class="pg-lbl">Annual</div></div>
      <div class="pg-item"><div class="pg-val">₹<?= number_format($result['total_premium'],2) ?></div><div class="pg-lbl">Total (<?= $result['term'] ?> yrs)</div></div>
    </div>
    <div class="coverage-info">🛡 Coverage Amount: <strong>₹<?= number_format($result['coverage']) ?></strong> &nbsp;·&nbsp; Term: <strong><?= $result['term'] ?> years</strong></div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
