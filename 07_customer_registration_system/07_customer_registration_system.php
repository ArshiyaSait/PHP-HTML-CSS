<?php
$success = false;
$errors  = [];
$data    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['cid']     = trim($_POST['cid'] ?? '');
    $data['name']    = trim($_POST['name'] ?? '');
    $data['email']   = trim($_POST['email'] ?? '');
    $data['phone']   = trim($_POST['phone'] ?? '');
    $data['dob']     = trim($_POST['dob'] ?? '');
    $data['gender']  = trim($_POST['gender'] ?? '');
    $data['type']    = trim($_POST['type'] ?? '');
    $data['address'] = trim($_POST['address'] ?? '');
    $data['city']    = trim($_POST['city'] ?? '');
    $data['pincode'] = trim($_POST['pincode'] ?? '');

    if (empty($data['cid']))     $errors['cid']     = 'Required';
    if (empty($data['name']))    $errors['name']    = 'Required';
    if (empty($data['email'])  || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email required';
    if (!preg_match('/^[0-9]{10}$/', $data['phone'])) $errors['phone'] = '10 digits required';
    if (empty($data['dob']))     $errors['dob']     = 'Required';
    if (empty($data['gender']))  $errors['gender']  = 'Required';
    if (empty($data['type']))    $errors['type']    = 'Required';
    if (empty($data['address'])) $errors['address'] = 'Required';
    if (empty($data['city']))    $errors['city']    = 'Required';
    if (!preg_match('/^[0-9]{6}$/', $data['pincode'])) $errors['pincode'] = '6 digits required';

    if (empty($errors)) $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Registration System</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:760px;margin:0 auto;padding:48px 20px 80px;}
  .chip{display:inline-flex;align-items:center;background:#0a0a0a;color:#fff;padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;}
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
  textarea{resize:vertical;min-height:70px;}
  .err-msg{color:#dc2626;font-size:0.76rem;font-weight:500;}
  .input-err{border-color:#dc2626!important;}
  .btn{width:100%;padding:14px;background:#0a0a0a;color:#fff;border:none;border-radius:10px;font-size:0.96rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;margin-top:22px;transition:transform 0.15s,box-shadow 0.15s;}
  .btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,0,0,0.18);}
  .sec-gap{margin-top:24px;}
  .success-banner{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:24px;text-align:center;margin-bottom:24px;}
  .success-banner h2{font-family:'Outfit',sans-serif;font-size:1.3rem;font-weight:700;color:#16a34a;}
  .info-table{width:100%;border-collapse:collapse;}
  .info-table tr{border-bottom:1px solid #f0f0f0;}
  .info-table td{padding:10px 4px;font-size:0.88rem;}
  .info-table td:first-child{color:#6b7280;font-weight:500;width:38%;}
  .info-table td:last-child{font-weight:600;}
  .cid-badge{display:inline-block;background:#0a0a0a;color:#fff;padding:4px 14px;border-radius:50px;font-size:0.78rem;font-weight:700;font-family:'Outfit',sans-serif;margin-bottom:8px;}
  .btn-out{display:inline-block;padding:10px 24px;border:1.5px solid #0a0a0a;border-radius:50px;font-size:0.86rem;font-weight:600;color:#0a0a0a;cursor:pointer;background:transparent;margin-top:18px;transition:all 0.2s;font-family:'Outfit',sans-serif;}
  .btn-out:hover{background:#0a0a0a;color:#fff;}
  @media(max-width:560px){.grid{grid-template-columns:1fr;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">👤 Task 07</div>
  <h1>Customer Registration<br>System</h1>
  <p class="sub">Register new customers with complete profile details.</p>
  <div class="card">
    <?php if($success): ?>
      <div class="success-banner">
        <div style="font-size:2rem;margin-bottom:8px;">✅</div>
        <h2>Customer Registered!</h2>
        <p>Registration successful for <?= htmlspecialchars($data['name']) ?>.</p>
      </div>
      <div class="cid-badge">CID: <?= htmlspecialchars($data['cid']) ?></div>
      <table class="info-table">
        <tr><td>Full Name</td><td><?= htmlspecialchars($data['name']) ?></td></tr>
        <tr><td>Email</td><td><?= htmlspecialchars($data['email']) ?></td></tr>
        <tr><td>Phone</td><td><?= htmlspecialchars($data['phone']) ?></td></tr>
        <tr><td>Date of Birth</td><td><?= htmlspecialchars($data['dob']) ?></td></tr>
        <tr><td>Gender</td><td><?= htmlspecialchars($data['gender']) ?></td></tr>
        <tr><td>Customer Type</td><td><?= htmlspecialchars($data['type']) ?></td></tr>
        <tr><td>City</td><td><?= htmlspecialchars($data['city']) ?></td></tr>
        <tr><td>PIN Code</td><td><?= htmlspecialchars($data['pincode']) ?></td></tr>
      </table>
      <div style="text-align:center;"><button class="btn-out" onclick="window.location='<?= $_SERVER['PHP_SELF'] ?>'">Register Another</button></div>
    <?php else: ?>
      <div class="sec-label">Customer Information</div>
      <form method="POST">
        <div class="grid">
          <div class="fg"><label>Customer ID</label>
            <input type="text" name="cid" placeholder="e.g. CUST-001" value="<?= htmlspecialchars($data['cid']??'') ?>" class="<?= isset($errors['cid'])?'input-err':'' ?>">
            <?php if(isset($errors['cid'])): ?><span class="err-msg"><?= $errors['cid'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Full Name</label>
            <input type="text" name="name" placeholder="Full name" value="<?= htmlspecialchars($data['name']??'') ?>" class="<?= isset($errors['name'])?'input-err':'' ?>">
            <?php if(isset($errors['name'])): ?><span class="err-msg"><?= $errors['name'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Email Address</label>
            <input type="email" name="email" placeholder="email@domain.com" value="<?= htmlspecialchars($data['email']??'') ?>" class="<?= isset($errors['email'])?'input-err':'' ?>">
            <?php if(isset($errors['email'])): ?><span class="err-msg"><?= $errors['email'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Phone Number</label>
            <input type="text" name="phone" placeholder="10-digit number" value="<?= htmlspecialchars($data['phone']??'') ?>" class="<?= isset($errors['phone'])?'input-err':'' ?>">
            <?php if(isset($errors['phone'])): ?><span class="err-msg"><?= $errors['phone'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Date of Birth</label>
            <input type="date" name="dob" value="<?= htmlspecialchars($data['dob']??'') ?>" class="<?= isset($errors['dob'])?'input-err':'' ?>">
            <?php if(isset($errors['dob'])): ?><span class="err-msg"><?= $errors['dob'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Gender</label>
            <select name="gender" class="<?= isset($errors['gender'])?'input-err':'' ?>">
              <option value="">Select</option>
              <?php foreach(['Male','Female','Other'] as $g): ?>
              <option value="<?= $g ?>" <?= (($data['gender']??'')===$g)?'selected':'' ?>><?= $g ?></option>
              <?php endforeach; ?>
            </select>
            <?php if(isset($errors['gender'])): ?><span class="err-msg"><?= $errors['gender'] ?></span><?php endif; ?></div>
          <div class="fg full"><label>Customer Type</label>
            <select name="type" class="<?= isset($errors['type'])?'input-err':'' ?>">
              <option value="">Select Type</option>
              <?php foreach(['Regular','Premium','VIP','Corporate','Student'] as $t): ?>
              <option value="<?= $t ?>" <?= (($data['type']??'')===$t)?'selected':'' ?>><?= $t ?></option>
              <?php endforeach; ?>
            </select>
            <?php if(isset($errors['type'])): ?><span class="err-msg"><?= $errors['type'] ?></span><?php endif; ?></div>
          <div class="fg full"><label>Address</label>
            <textarea name="address" placeholder="Street address" class="<?= isset($errors['address'])?'input-err':'' ?>"><?= htmlspecialchars($data['address']??'') ?></textarea>
            <?php if(isset($errors['address'])): ?><span class="err-msg"><?= $errors['address'] ?></span><?php endif; ?></div>
          <div class="fg"><label>City</label>
            <input type="text" name="city" placeholder="City" value="<?= htmlspecialchars($data['city']??'') ?>" class="<?= isset($errors['city'])?'input-err':'' ?>">
            <?php if(isset($errors['city'])): ?><span class="err-msg"><?= $errors['city'] ?></span><?php endif; ?></div>
          <div class="fg"><label>PIN Code</label>
            <input type="text" name="pincode" placeholder="6-digit PIN" value="<?= htmlspecialchars($data['pincode']??'') ?>" class="<?= isset($errors['pincode'])?'input-err':'' ?>">
            <?php if(isset($errors['pincode'])): ?><span class="err-msg"><?= $errors['pincode'] ?></span><?php endif; ?></div>
        </div>
        <button type="submit" class="btn">Register Customer →</button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
