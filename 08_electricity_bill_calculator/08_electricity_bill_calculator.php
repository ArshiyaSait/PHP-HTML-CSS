<?php
$result = null;
$errors = [];
$data   = [];

// Slab rates (per unit)
$slabs = [
    ['limit'=>100, 'rate'=>2.50,  'label'=>'0–100 units'],
    ['limit'=>200, 'rate'=>3.75,  'label'=>'101–200 units'],
    ['limit'=>300, 'rate'=>5.00,  'label'=>'201–300 units'],
    ['limit'=>PHP_INT_MAX,'rate'=>7.00,'label'=>'Above 300 units'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']     = trim($_POST['name'] ?? '');
    $data['accno']    = trim($_POST['accno'] ?? '');
    $data['meter']    = trim($_POST['meter'] ?? '');
    $data['prev']     = trim($_POST['prev'] ?? '');
    $data['curr']     = trim($_POST['curr'] ?? '');
    $data['month']    = trim($_POST['month'] ?? '');

    if (empty($data['name']))   $errors['name']   = 'Required';
    if (empty($data['accno']))  $errors['accno']  = 'Required';
    if (empty($data['meter']))  $errors['meter']  = 'Required';
    if ($data['prev'] === '')   $errors['prev']   = 'Required';
    if ($data['curr'] === '')   $errors['curr']   = 'Required';
    elseif ((float)$data['curr'] < (float)$data['prev']) $errors['curr'] = 'Current must be ≥ previous reading';
    if (empty($data['month']))  $errors['month']  = 'Required';

    if (empty($errors)) {
        $units = (float)$data['curr'] - (float)$data['prev'];
        $charge = 0; $remaining = $units; $breakdown = [];
        $prev_limit = 0;
        foreach ($slabs as $slab) {
            if ($remaining <= 0) break;
            $in_slab = min($remaining, $slab['limit'] - $prev_limit);
            $cost    = $in_slab * $slab['rate'];
            $breakdown[] = ['label'=>$slab['label'],'units'=>$in_slab,'rate'=>$slab['rate'],'cost'=>$cost];
            $charge     += $cost;
            $remaining  -= $in_slab;
            $prev_limit  = $slab['limit'];
        }
        $fixed    = 50;
        $tax      = round($charge * 0.18, 2);
        $total    = round($charge + $fixed + $tax, 2);
        $result   = compact('units','charge','fixed','tax','total','breakdown');
    }
}
$months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Electricity Bill Calculator</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:700px;margin:0 auto;padding:48px 20px 80px;}
  .chip{display:inline-flex;align-items:center;background:#0a0a0a;color:#fff;padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;}
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
  /* Bill */
  .bill-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;flex-wrap:wrap;gap:12px;}
  .bill-title{font-family:'Outfit',sans-serif;font-size:1.1rem;font-weight:700;}
  .bill-month{font-size:0.82rem;color:#6b7280;}
  .units-display{text-align:center;background:#fafafa;border:1px solid #e5e7eb;border-radius:12px;padding:20px;margin-bottom:20px;}
  .units-num{font-family:'Outfit',sans-serif;font-size:3rem;font-weight:800;}
  .units-lbl{font-size:0.75rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-top:4px;}
  .slab-table{width:100%;border-collapse:collapse;margin-bottom:20px;}
  .slab-table th{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#9ca3af;padding:8px 4px;text-align:left;border-bottom:2px solid #f0f0f0;}
  .slab-table td{padding:10px 4px;font-size:0.88rem;border-bottom:1px solid #f8f8f8;}
  .slab-table td:last-child{text-align:right;font-weight:600;}
  .totals{background:#0a0a0a;color:#fff;border-radius:12px;padding:20px;}
  .total-row{display:flex;justify-content:space-between;padding:6px 0;font-size:0.88rem;}
  .total-row.big{font-family:'Outfit',sans-serif;font-size:1.2rem;font-weight:800;padding-top:12px;margin-top:8px;border-top:1px solid rgba(255,255,255,0.15);}
  .total-row span:last-child{font-weight:600;}
  @media(max-width:560px){.grid{grid-template-columns:1fr;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">⚡ Task 08</div>
  <h1>Electricity Bill<br>Calculator</h1>
  <p class="sub">Calculate electricity bill using slab-based tariff rates with GST.</p>

  <div class="card">
    <div class="sec-label">Consumer Details</div>
    <form method="POST">
      <div class="grid">
        <div class="fg"><label>Consumer Name</label>
          <input type="text" name="name" placeholder="Full name" value="<?= htmlspecialchars($data['name']??'') ?>" class="<?= isset($errors['name'])?'input-err':'' ?>">
          <?php if(isset($errors['name'])): ?><span class="err-msg"><?= $errors['name'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Account Number</label>
          <input type="text" name="accno" placeholder="e.g. ELEC-12345" value="<?= htmlspecialchars($data['accno']??'') ?>" class="<?= isset($errors['accno'])?'input-err':'' ?>">
          <?php if(isset($errors['accno'])): ?><span class="err-msg"><?= $errors['accno'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Meter Number</label>
          <input type="text" name="meter" placeholder="Meter ID" value="<?= htmlspecialchars($data['meter']??'') ?>" class="<?= isset($errors['meter'])?'input-err':'' ?>">
          <?php if(isset($errors['meter'])): ?><span class="err-msg"><?= $errors['meter'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Billing Month</label>
          <select name="month" class="<?= isset($errors['month'])?'input-err':'' ?>">
            <option value="">Select Month</option>
            <?php foreach($months as $m): ?>
            <option value="<?= $m ?>" <?= (($data['month']??'')===$m)?'selected':'' ?>><?= $m ?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['month'])): ?><span class="err-msg"><?= $errors['month'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Previous Reading (kWh)</label>
          <input type="number" name="prev" placeholder="Previous meter reading" min="0" step="0.01" value="<?= htmlspecialchars($data['prev']??'') ?>" class="<?= isset($errors['prev'])?'input-err':'' ?>">
          <?php if(isset($errors['prev'])): ?><span class="err-msg"><?= $errors['prev'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Current Reading (kWh)</label>
          <input type="number" name="curr" placeholder="Current meter reading" min="0" step="0.01" value="<?= htmlspecialchars($data['curr']??'') ?>" class="<?= isset($errors['curr'])?'input-err':'' ?>">
          <?php if(isset($errors['curr'])): ?><span class="err-msg"><?= $errors['curr'] ?></span><?php endif; ?></div>
      </div>
      <button type="submit" class="btn">Generate Bill →</button>
    </form>
  </div>

  <?php if ($result): ?>
  <div class="card">
    <div class="bill-header">
      <div>
        <div class="bill-title">Electricity Bill — <?= htmlspecialchars($data['month']) ?></div>
        <div class="bill-month"><?= htmlspecialchars($data['name']) ?> &nbsp;|&nbsp; Acc: <?= htmlspecialchars($data['accno']) ?></div>
      </div>
      <div style="text-align:right;font-size:0.82rem;color:#9ca3af;">Meter: <?= htmlspecialchars($data['meter']) ?></div>
    </div>
    <div class="units-display">
      <div class="units-num"><?= number_format($result['units'],2) ?></div>
      <div class="units-lbl">Units Consumed (kWh)</div>
    </div>
    <div class="sec-label">Slab-wise Breakdown</div>
    <table class="slab-table">
      <thead><tr><th>Slab</th><th>Units</th><th>Rate (₹)</th><th>Amount</th></tr></thead>
      <tbody>
      <?php foreach($result['breakdown'] as $b): if($b['units']<=0) continue; ?>
      <tr>
        <td><?= $b['label'] ?></td>
        <td><?= number_format($b['units'],2) ?></td>
        <td>₹<?= number_format($b['rate'],2) ?></td>
        <td>₹<?= number_format($b['cost'],2) ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <div class="totals">
      <div class="total-row"><span>Energy Charges</span><span>₹<?= number_format($result['charge'],2) ?></span></div>
      <div class="total-row"><span>Fixed Charges</span><span>₹<?= number_format($result['fixed'],2) ?></span></div>
      <div class="total-row"><span>GST (18%)</span><span>₹<?= number_format($result['tax'],2) ?></span></div>
      <div class="total-row big"><span>Total Amount Due</span><span>₹<?= number_format($result['total'],2) ?></span></div>
    </div>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
