<?php
$result = null;
$errors = [];
$data   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']     = trim($_POST['name'] ?? '');
    $data['phone']    = trim($_POST['phone'] ?? '');
    $data['plan']     = trim($_POST['plan'] ?? '');
    $data['calls']    = trim($_POST['calls'] ?? '0');
    $data['data']     = trim($_POST['data'] ?? '0');
    $data['sms']      = trim($_POST['sms'] ?? '0');
    $data['roaming']  = trim($_POST['roaming'] ?? 'No');
    $data['month']    = trim($_POST['month'] ?? '');

    if (empty($data['name']))  $errors['name']  = 'Required';
    if (!preg_match('/^[0-9]{10}$/',$data['phone'])) $errors['phone'] = '10 digits required';
    if (empty($data['plan']))  $errors['plan']  = 'Required';
    if (empty($data['month'])) $errors['month'] = 'Required';

    if (empty($errors)) {
        $plans = [
            'Basic'    => ['rent'=>199, 'free_calls'=>100, 'free_data'=>1,   'free_sms'=>100],
            'Standard' => ['rent'=>399, 'free_calls'=>300, 'free_data'=>5,   'free_sms'=>300],
            'Premium'  => ['rent'=>699, 'free_calls'=>999, 'free_data'=>15,  'free_sms'=>999],
            'Unlimited'=> ['rent'=>999, 'free_calls'=>9999,'free_data'=>50,  'free_sms'=>9999],
        ];
        $p    = $plans[$data['plan']];
        $calls= max(0,(float)$data['calls'] - $p['free_calls']);
        $dg   = max(0,(float)$data['data']  - $p['free_data']);
        $sms  = max(0,(float)$data['sms']   - $p['free_sms']);
        $call_ch = round($calls * 0.50, 2);
        $data_ch = round($dg * 10, 2);
        $sms_ch  = round($sms * 0.10, 2);
        $roam_ch = $data['roaming']==='Yes' ? 99 : 0;
        $total_charges = $call_ch + $data_ch + $sms_ch + $roam_ch;
        $tax     = round($total_charges * 0.18, 2);
        $total   = round($p['rent'] + $total_charges + $tax, 2);
        $result  = compact('p','call_ch','data_ch','sms_ch','roam_ch','tax','total','calls','dg','sms');
    }
}
$months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mobile Bill Generator</title>
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
  /* Bill */
  .bill-top{background:#0a0a0a;color:#fff;border-radius:14px;padding:24px;display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;}
  .bt-left .name{font-family:'Outfit',sans-serif;font-size:1rem;font-weight:700;}
  .bt-left .phone{font-size:0.82rem;color:#9ca3af;margin-top:3px;}
  .bt-right .lbl{font-size:0.65rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;}
  .bt-right .amount{font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;}
  .usage-row{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #f0f0f0;font-size:0.88rem;}
  .usage-row:last-child{border-bottom:none;}
  .usage-row .lbl{color:#6b7280;}
  .usage-row .val{font-weight:600;}
  .total-row{display:flex;justify-content:space-between;padding:11px 0;font-size:0.9rem;font-weight:700;border-top:2px solid #0a0a0a;margin-top:4px;}
  @media(max-width:560px){.grid{grid-template-columns:1fr;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">📱 Task 16</div>
  <h1>Mobile Bill Generator</h1>
  <p class="sub">Generate a detailed mobile phone bill with usage-based charges.</p>

  <div class="card">
    <div class="sec-label">Subscriber Details</div>
    <form method="POST">
      <div class="grid">
        <div class="fg"><label>Customer Name</label>
          <input type="text" name="name" placeholder="Full name" value="<?= htmlspecialchars($data['name']??'') ?>" class="<?= isset($errors['name'])?'input-err':'' ?>">
          <?php if(isset($errors['name'])): ?><span class="err-msg"><?= $errors['name'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Mobile Number</label>
          <input type="text" name="phone" placeholder="10-digit number" value="<?= htmlspecialchars($data['phone']??'') ?>" class="<?= isset($errors['phone'])?'input-err':'' ?>">
          <?php if(isset($errors['phone'])): ?><span class="err-msg"><?= $errors['phone'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Billing Month</label>
          <select name="month" class="<?= isset($errors['month'])?'input-err':'' ?>">
            <option value="">Select Month</option>
            <?php foreach($months as $m): ?>
            <option value="<?= $m ?>" <?= (($data['month']??'')===$m)?'selected':'' ?>><?= $m ?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['month'])): ?><span class="err-msg"><?= $errors['month'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Plan</label>
          <select name="plan" class="<?= isset($errors['plan'])?'input-err':'' ?>">
            <option value="">Select Plan</option>
            <option value="Basic" <?= (($data['plan']??'')==='Basic')?'selected':'' ?>>Basic — ₹199/mo</option>
            <option value="Standard" <?= (($data['plan']??'')==='Standard')?'selected':'' ?>>Standard — ₹399/mo</option>
            <option value="Premium" <?= (($data['plan']??'')==='Premium')?'selected':'' ?>>Premium — ₹699/mo</option>
            <option value="Unlimited" <?= (($data['plan']??'')==='Unlimited')?'selected':'' ?>>Unlimited — ₹999/mo</option>
          </select>
          <?php if(isset($errors['plan'])): ?><span class="err-msg"><?= $errors['plan'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Call Minutes Used</label>
          <input type="number" name="calls" placeholder="Minutes" min="0" value="<?= htmlspecialchars($data['calls']??'0') ?>"></div>
        <div class="fg"><label>Data Used (GB)</label>
          <input type="number" name="data" placeholder="GB" min="0" step="0.1" value="<?= htmlspecialchars($data['data']??'0') ?>"></div>
        <div class="fg"><label>SMS Sent</label>
          <input type="number" name="sms" placeholder="Count" min="0" value="<?= htmlspecialchars($data['sms']??'0') ?>"></div>
        <div class="fg"><label>International Roaming?</label>
          <select name="roaming">
            <option value="No" <?= (($data['roaming']??'No')==='No')?'selected':'' ?>>No</option>
            <option value="Yes" <?= (($data['roaming']??'')==='Yes')?'selected':'' ?>>Yes (+₹99)</option>
          </select></div>
      </div>
      <button type="submit" class="btn">Generate Bill →</button>
    </form>
  </div>

  <?php if($result): ?>
  <div class="card">
    <div class="bill-top">
      <div class="bt-left"><div class="name"><?= htmlspecialchars($data['name']) ?></div><div class="phone"><?= htmlspecialchars($data['phone']) ?> · <?= htmlspecialchars($data['plan']) ?> Plan · <?= htmlspecialchars($data['month']) ?></div></div>
      <div class="bt-right"><div class="lbl">Total Due</div><div class="amount">₹<?= number_format($result['total'],2) ?></div></div>
    </div>
    <div class="sec-label">Bill Breakdown</div>
    <div class="usage-row"><span class="lbl">Plan Rental</span><span class="val">₹<?= number_format($result['p']['rent'],2) ?></span></div>
    <div class="usage-row"><span class="lbl">Extra Call Charges (<?= number_format($result['calls'],1) ?> mins)</span><span class="val">₹<?= number_format($result['call_ch'],2) ?></span></div>
    <div class="usage-row"><span class="lbl">Extra Data Charges (<?= number_format($result['dg'],2) ?> GB)</span><span class="val">₹<?= number_format($result['data_ch'],2) ?></span></div>
    <div class="usage-row"><span class="lbl">Extra SMS Charges (<?= number_format($result['sms']) ?> SMS)</span><span class="val">₹<?= number_format($result['sms_ch'],2) ?></span></div>
    <?php if($result['roam_ch']>0): ?>
    <div class="usage-row"><span class="lbl">International Roaming</span><span class="val">₹<?= number_format($result['roam_ch'],2) ?></span></div>
    <?php endif; ?>
    <div class="usage-row"><span class="lbl">GST (18%)</span><span class="val">₹<?= number_format($result['tax'],2) ?></span></div>
    <div class="total-row"><span>Total Amount Due</span><span>₹<?= number_format($result['total'],2) ?></span></div>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
