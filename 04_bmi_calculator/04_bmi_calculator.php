<?php
$result = null;
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']   = trim($_POST['name'] ?? '');
    $data['unit']   = trim($_POST['unit'] ?? '');
    $data['height'] = trim($_POST['height'] ?? '');
    $data['weight'] = trim($_POST['weight'] ?? '');

    if (empty($data['name']))   $errors['name']   = 'Required';
    if (empty($data['unit']))   $errors['unit']   = 'Required';
    if ($data['height'] === '') $errors['height'] = 'Required';
    elseif (!is_numeric($data['height']) || $data['height'] <= 0) $errors['height'] = 'Enter valid height';
    if ($data['weight'] === '') $errors['weight'] = 'Required';
    elseif (!is_numeric($data['weight']) || $data['weight'] <= 0) $errors['weight'] = 'Enter valid weight';

    if (empty($errors)) {
        $h = (float)$data['height'];
        $w = (float)$data['weight'];
        if ($data['unit'] === 'imperial') {
            $h_m = $h * 0.0254; // inches to meters
            $w_kg = $w * 0.453592;
        } else {
            $h_m = $h / 100;
            $w_kg = $w;
        }
        $bmi = round($w_kg / ($h_m * $h_m), 1);
        if      ($bmi < 18.5) { $cat = 'Underweight'; $clr = '#2563eb'; $tip = 'Consider a balanced diet with more nutrients.'; }
        elseif  ($bmi < 25.0) { $cat = 'Normal Weight'; $clr = '#16a34a'; $tip = 'Great! Maintain your healthy lifestyle.'; }
        elseif  ($bmi < 30.0) { $cat = 'Overweight'; $clr = '#f59e0b'; $tip = 'Consider moderate exercise and a balanced diet.'; }
        else                  { $cat = 'Obese'; $clr = '#dc2626'; $tip = 'Please consult a healthcare professional.'; }
        $pct = min(100, max(0, (($bmi - 10) / 30) * 100));
        $result = compact('bmi','cat','clr','tip','pct','h_m','w_kg');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BMI Calculator</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:600px;margin:0 auto;padding:48px 20px 80px;}
  .chip{display:inline-flex;align-items:center;background:#0a0a0a;color:#fff;
    padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;
    text-transform:uppercase;margin-bottom:16px;}
  h1{font-family:'Outfit',sans-serif;font-size:2.3rem;font-weight:800;line-height:1.15;letter-spacing:-0.5px;}
  .sub{color:#6b7280;margin-top:8px;font-size:0.95rem;margin-bottom:36px;}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:36px;box-shadow:0 8px 40px rgba(0,0,0,0.08);}
  .section-label{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;
    color:#9ca3af;margin-bottom:18px;padding-bottom:8px;border-bottom:1px solid #e5e7eb;}
  .form-col{display:flex;flex-direction:column;gap:16px;}
  .fg{display:flex;flex-direction:column;gap:5px;}
  .grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
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

  /* BMI result */
  .result{margin-top:28px;}
  .bmi-display{text-align:center;padding:28px 20px;background:#fafafa;border:1px solid #e5e7eb;border-radius:16px;margin-bottom:20px;}
  .bmi-num{font-family:'Outfit',sans-serif;font-size:4rem;font-weight:800;line-height:1;}
  .bmi-unit{font-size:0.9rem;color:#9ca3af;margin-top:4px;text-transform:uppercase;letter-spacing:1px;}
  .bmi-cat{display:inline-block;margin-top:12px;padding:6px 18px;border-radius:50px;font-weight:700;font-size:0.9rem;}
  .bar-wrap{margin:20px 0;}
  .bar-track{height:10px;background:#f0f0f0;border-radius:50px;position:relative;overflow:hidden;}
  .bar-fill{height:100%;border-radius:50px;transition:width 0.8s ease;}
  .bar-labels{display:flex;justify-content:space-between;margin-top:6px;font-size:0.7rem;color:#9ca3af;}
  .tip-box{background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 16px;font-size:0.85rem;color:#78350f;margin-top:4px;}
  .bmi-scale{display:flex;gap:8px;margin-top:16px;}
  .scale-item{flex:1;text-align:center;padding:8px 4px;border-radius:8px;font-size:0.68rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;}
  @media(max-width:480px){.grid2{grid-template-columns:1fr;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">⚖️ Task 04</div>
  <h1>BMI Calculator</h1>
  <p class="sub">Calculate your Body Mass Index and understand your health status.</p>

  <div class="card">
    <div class="section-label">Your Details</div>
    <form method="POST">
      <div class="form-col">
        <div class="fg">
          <label>Full Name</label>
          <input type="text" name="name" placeholder="Your name" value="<?= htmlspecialchars($data['name']??'') ?>" class="<?= isset($errors['name'])?'input-err':'' ?>">
          <?php if(isset($errors['name'])): ?><span class="err-msg"><?= $errors['name'] ?></span><?php endif; ?>
        </div>
        <div class="fg">
          <label>Measurement Unit</label>
          <select name="unit" class="<?= isset($errors['unit'])?'input-err':'' ?>">
            <option value="">Select Unit</option>
            <option value="metric" <?= (($data['unit']??'')==='metric')?'selected':'' ?>>Metric (cm / kg)</option>
            <option value="imperial" <?= (($data['unit']??'')==='imperial')?'selected':'' ?>>Imperial (inches / lbs)</option>
          </select>
          <?php if(isset($errors['unit'])): ?><span class="err-msg"><?= $errors['unit'] ?></span><?php endif; ?>
        </div>
        <div class="grid2">
          <div class="fg">
            <label>Height</label>
            <input type="number" name="height" placeholder="cm or inches" step="0.1" min="0" value="<?= htmlspecialchars($data['height']??'') ?>" class="<?= isset($errors['height'])?'input-err':'' ?>">
            <?php if(isset($errors['height'])): ?><span class="err-msg"><?= $errors['height'] ?></span><?php endif; ?>
          </div>
          <div class="fg">
            <label>Weight</label>
            <input type="number" name="weight" placeholder="kg or lbs" step="0.1" min="0" value="<?= htmlspecialchars($data['weight']??'') ?>" class="<?= isset($errors['weight'])?'input-err':'' ?>">
            <?php if(isset($errors['weight'])): ?><span class="err-msg"><?= $errors['weight'] ?></span><?php endif; ?>
          </div>
        </div>
      </div>
      <button type="submit" class="btn">Calculate BMI →</button>
    </form>

    <?php if ($result): ?>
    <div class="result">
      <div class="bmi-display">
        <div class="bmi-num" style="color:<?= $result['clr'] ?>"><?= $result['bmi'] ?></div>
        <div class="bmi-unit">Body Mass Index</div>
        <div class="bmi-cat" style="background:<?= $result['clr'] ?>22;color:<?= $result['clr'] ?>"><?= $result['cat'] ?></div>
      </div>
      <div class="bar-wrap">
        <div class="bar-track">
          <div class="bar-fill" style="width:<?= $result['pct'] ?>%;background:<?= $result['clr'] ?>"></div>
        </div>
        <div class="bar-labels"><span>10 — Underweight</span><span>25 — Normal</span><span>30 — Obese</span><span>40+</span></div>
      </div>
      <div class="bmi-scale">
        <div class="scale-item" style="background:#eff6ff;color:#2563eb">Under<br>weight<br>&lt;18.5</div>
        <div class="scale-item" style="background:#f0fdf4;color:#16a34a">Normal<br>18.5–25</div>
        <div class="scale-item" style="background:#fffbeb;color:#d97706">Over<br>weight<br>25–30</div>
        <div class="scale-item" style="background:#fef2f2;color:#dc2626">Obese<br>&gt;30</div>
      </div>
      <div class="tip-box" style="margin-top:14px;">💡 <?= $result['tip'] ?></div>
      <p style="text-align:center;margin-top:12px;font-size:0.8rem;color:#9ca3af;">
        Height: <?= round($result['h_m']*100,1) ?> cm &nbsp;|&nbsp; Weight: <?= round($result['w_kg'],1) ?> kg
      </p>
    </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
