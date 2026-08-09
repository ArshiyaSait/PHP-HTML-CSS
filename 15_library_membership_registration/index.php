<?php
$success = false;
$errors  = [];
$data    = [];
$mid     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']     = trim($_POST['name'] ?? '');
    $data['email']    = trim($_POST['email'] ?? '');
    $data['phone']    = trim($_POST['phone'] ?? '');
    $data['dob']      = trim($_POST['dob'] ?? '');
    $data['address']  = trim($_POST['address'] ?? '');
    $data['type']     = trim($_POST['type'] ?? '');
    $data['duration'] = trim($_POST['duration'] ?? '');
    $data['books']    = (int)($_POST['books'] ?? 0);

    if (empty($data['name']))      $errors['name']     = 'Required';
    if (!filter_var($data['email'],FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email required';
    if (!preg_match('/^[0-9]{10}$/',$data['phone'])) $errors['phone'] = '10 digits required';
    if (empty($data['dob']))       $errors['dob']      = 'Required';
    if (empty($data['address']))   $errors['address']  = 'Required';
    if (empty($data['type']))      $errors['type']     = 'Required';
    if (empty($data['duration']))  $errors['duration'] = 'Required';

    $fees = ['Basic'=>500,'Standard'=>900,'Premium'=>1500,'Student'=>300,'Corporate'=>2500];
    $dur  = ['3 Months'=>0.25,'6 Months'=>0.5,'1 Year'=>1,'2 Years'=>1.8,'3 Years'=>2.5];

    if (empty($errors)) {
        $base_fee = $fees[$data['type']] ?? 500;
        $dur_mult = $dur[$data['duration']] ?? 1;
        $fee      = round($base_fee * $dur_mult, 0);
        $deposit  = $data['books'] * 100;
        $total    = $fee + $deposit;
        $mid      = 'LIB-'.strtoupper(substr(md5($data['name'].time()),0,6));
        $expiry   = date('d M Y', strtotime('+'.(int)($dur_mult*12).' months'));
        $success  = true;
        $result   = compact('fee','deposit','total','mid','expiry');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Library Membership Registration</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:740px;margin:0 auto;padding:48px 20px 80px;}
  .chip{display:inline-flex;background:#0a0a0a;color:#fff;padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;}
  h1{font-family:'Outfit',sans-serif;font-size:2.3rem;font-weight:800;letter-spacing:-0.5px;line-height:1.15;}
  .sub{color:#6b7280;margin-top:8px;font-size:0.95rem;margin-bottom:36px;}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:36px;box-shadow:0 8px 40px rgba(0,0,0,0.08);}
  .sec-label{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#9ca3af;margin-bottom:18px;padding-bottom:8px;border-bottom:1px solid #e5e7eb;}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
  .fg{display:flex;flex-direction:column;gap:5px;}
  .fg.full{grid-column:1/-1;}
  label{font-size:0.77rem;font-weight:600;letter-spacing:0.4px;text-transform:uppercase;}
  input,select,textarea{background:#fafafa;border:1.5px solid #e5e7eb;border-radius:10px;padding:11px 15px;color:#0a0a0a;font-size:0.94rem;font-family:'Inter',sans-serif;width:100%;transition:all 0.2s;appearance:none;}
  input:focus,select:focus,textarea:focus{outline:none;border-color:#0a0a0a;background:#fff;box-shadow:0 0 0 3px rgba(10,10,10,0.08);}
  select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;padding-right:40px;}
  textarea{resize:vertical;min-height:68px;}
  .err-msg{color:#dc2626;font-size:0.76rem;font-weight:500;}
  .input-err{border-color:#dc2626!important;}
  .btn{width:100%;padding:14px;background:#0a0a0a;color:#fff;border:none;border-radius:10px;font-size:0.96rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;margin-top:22px;transition:transform 0.15s,box-shadow 0.15s;}
  .btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,0,0,0.18);}
  /* Membership card */
  .mem-card{background:linear-gradient(135deg,#0a0a0a 0%,#1a1a2e 100%);color:#fff;border-radius:18px;padding:28px;margin-bottom:20px;position:relative;overflow:hidden;}
  .mem-card::before{content:'';position:absolute;top:-40px;right:-40px;width:160px;height:160px;background:rgba(255,255,255,0.04);border-radius:50%;}
  .mc-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;}
  .mc-org{font-family:'Outfit',sans-serif;font-size:1rem;font-weight:700;}
  .mc-type{background:rgba(255,255,255,0.1);padding:4px 12px;border-radius:50px;font-size:0.72rem;font-weight:600;}
  .mc-id{font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:800;letter-spacing:2px;margin-bottom:6px;}
  .mc-name{font-size:0.9rem;opacity:0.7;margin-bottom:16px;}
  .mc-bottom{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:12px;}
  .mc-fee .label{font-size:0.65rem;opacity:0.6;text-transform:uppercase;letter-spacing:1px;}
  .mc-fee .val{font-family:'Outfit',sans-serif;font-size:1.1rem;font-weight:700;margin-top:2px;}
  .mc-expiry .label{font-size:0.65rem;opacity:0.6;text-transform:uppercase;letter-spacing:1px;text-align:right;}
  .mc-expiry .val{font-family:'Outfit',sans-serif;font-size:1rem;font-weight:700;margin-top:2px;}
  .fee-breakdown{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px;}
  .fb-item{background:#fafafa;border:1px solid #e5e7eb;border-radius:10px;padding:14px;text-align:center;}
  .fb-val{font-family:'Outfit',sans-serif;font-size:1.3rem;font-weight:800;}
  .fb-lbl{font-size:0.7rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-top:3px;}
  .btn-out{display:inline-block;padding:10px 24px;border:1.5px solid #0a0a0a;border-radius:50px;font-size:0.86rem;font-weight:600;color:#0a0a0a;cursor:pointer;background:transparent;margin-top:12px;transition:all 0.2s;font-family:'Outfit',sans-serif;}
  .btn-out:hover{background:#0a0a0a;color:#fff;}
  @media(max-width:560px){.grid{grid-template-columns:1fr;}.fee-breakdown{grid-template-columns:1fr 1fr;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">📚 Task 15</div>
  <h1>Library Membership<br>Registration System</h1>
  <p class="sub">Register for library membership and get instant access to thousands of books.</p>

  <?php if($success && isset($result)): ?>
  <div class="mem-card">
    <div class="mc-top">
      <div class="mc-org">📚 City Public Library</div>
      <div class="mc-type"><?= htmlspecialchars($data['type']) ?> Member</div>
    </div>
    <div class="mc-id"><?= htmlspecialchars($result['mid']) ?></div>
    <div class="mc-name"><?= htmlspecialchars($data['name']) ?></div>
    <div class="mc-bottom">
      <div class="mc-fee"><div class="label">Total Paid</div><div class="val">₹<?= number_format($result['total'],2) ?></div></div>
      <div class="mc-expiry"><div class="label">Valid Until</div><div class="val"><?= $result['expiry'] ?></div></div>
    </div>
  </div>
  <div class="card">
    <div class="sec-label">Fee Breakdown</div>
    <div class="fee-breakdown">
      <div class="fb-item"><div class="fb-val">₹<?= number_format($result['fee'],2) ?></div><div class="fb-lbl">Membership Fee</div></div>
      <div class="fb-item"><div class="fb-val">₹<?= number_format($result['deposit'],2) ?></div><div class="fb-lbl">Book Deposit</div></div>
      <div class="fb-item"><div class="fb-val">₹<?= number_format($result['total'],2) ?></div><div class="fb-lbl">Total Paid</div></div>
    </div>
    <p style="font-size:0.82rem;color:#6b7280;">Duration: <strong><?= htmlspecialchars($data['duration']) ?></strong> &nbsp;·&nbsp; Books Allowed: <strong><?= $data['books'] ?: 'Unlimited' ?></strong></p>
    <div style="text-align:center;"><button class="btn-out" onclick="window.location='<?= $_SERVER['PHP_SELF'] ?>'">New Registration</button></div>
  </div>
  <?php else: ?>
  <div class="card">
    <div class="sec-label">Applicant Details</div>
    <form method="POST">
      <div class="grid">
        <div class="fg"><label>Full Name</label>
          <input type="text" name="name" placeholder="Full name" value="<?= htmlspecialchars($data['name']??'') ?>" class="<?= isset($errors['name'])?'input-err':'' ?>">
          <?php if(isset($errors['name'])): ?><span class="err-msg"><?= $errors['name'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Email Address</label>
          <input type="email" name="email" placeholder="email@domain.com" value="<?= htmlspecialchars($data['email']??'') ?>" class="<?= isset($errors['email'])?'input-err':'' ?>">
          <?php if(isset($errors['email'])): ?><span class="err-msg"><?= $errors['email'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Phone Number</label>
          <input type="text" name="phone" placeholder="10 digits" value="<?= htmlspecialchars($data['phone']??'') ?>" class="<?= isset($errors['phone'])?'input-err':'' ?>">
          <?php if(isset($errors['phone'])): ?><span class="err-msg"><?= $errors['phone'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Date of Birth</label>
          <input type="date" name="dob" value="<?= htmlspecialchars($data['dob']??'') ?>" class="<?= isset($errors['dob'])?'input-err':'' ?>">
          <?php if(isset($errors['dob'])): ?><span class="err-msg"><?= $errors['dob'] ?></span><?php endif; ?></div>
        <div class="fg full"><label>Address</label>
          <textarea name="address" placeholder="Full address" class="<?= isset($errors['address'])?'input-err':'' ?>"><?= htmlspecialchars($data['address']??'') ?></textarea>
          <?php if(isset($errors['address'])): ?><span class="err-msg"><?= $errors['address'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Membership Type</label>
          <select name="type" class="<?= isset($errors['type'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach(['Student'=>'₹300/yr','Basic'=>'₹500/yr','Standard'=>'₹900/yr','Premium'=>'₹1500/yr','Corporate'=>'₹2500/yr'] as $t=>$f): ?>
            <option value="<?= $t ?>" <?= (($data['type']??'')===$t)?'selected':'' ?>><?= $t ?> — <?= $f ?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['type'])): ?><span class="err-msg"><?= $errors['type'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Duration</label>
          <select name="duration" class="<?= isset($errors['duration'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach(['3 Months','6 Months','1 Year','2 Years','3 Years'] as $d): ?>
            <option value="<?= $d ?>" <?= (($data['duration']??'')===$d)?'selected':'' ?>><?= $d ?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['duration'])): ?><span class="err-msg"><?= $errors['duration'] ?></span><?php endif; ?></div>
        <div class="fg full"><label>Number of Books to Issue at Once</label>
          <input type="number" name="books" placeholder="e.g. 3" min="0" max="10" value="<?= htmlspecialchars($data['books']??'3') ?>"></div>
      </div>
      <button type="submit" class="btn">Register Membership →</button>
    </form>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
