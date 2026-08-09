<?php
$success = false;
$errors  = [];
$data    = [];
$pid     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['name','dob','gender','blood','phone','email','address','ward','disease','doctor'];
    foreach ($fields as $f) {
        $data[$f] = trim($_POST[$f] ?? '');
        if (empty($data[$f])) $errors[$f] = 'Required';
    }
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Invalid email';
    if (!empty($data['phone']) && !preg_match('/^[0-9]{10}$/', $data['phone']))
        $errors['phone'] = '10 digits required';

    if (empty($errors)) {
        $success = true;
        $pid = 'PAT-'.strtoupper(substr(md5($data['name'].time()),0,6));
        $data['pid'] = $pid;
        $data['date'] = date('d M Y');
        $data['time'] = date('h:i A');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Registration System</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:760px;margin:0 auto;padding:48px 20px 80px;}
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
  /* Patient card */
  .pat-card{background:#0a0a0a;color:#fff;border-radius:18px;padding:28px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;}
  .pc-left .pc-pid{font-size:0.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;}
  .pc-left .pc-name{font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:800;}
  .pc-left .pc-sub{font-size:0.82rem;color:#9ca3af;margin-top:4px;}
  .pc-right{text-align:right;}
  .pc-right .pc-date{font-size:0.78rem;color:#9ca3af;}
  .blood-badge{display:inline-block;background:#dc2626;color:#fff;padding:4px 12px;border-radius:50px;font-size:0.78rem;font-weight:700;margin-top:8px;}
  .info-table{width:100%;border-collapse:collapse;}
  .info-table tr{border-bottom:1px solid #f0f0f0;}
  .info-table td{padding:10px 4px;font-size:0.88rem;}
  .info-table td:first-child{color:#6b7280;font-weight:500;width:38%;}
  .info-table td:last-child{font-weight:600;}
  .btn-out{display:inline-block;padding:10px 24px;border:1.5px solid #0a0a0a;border-radius:50px;font-size:0.86rem;font-weight:600;color:#0a0a0a;cursor:pointer;background:transparent;margin-top:18px;transition:all 0.2s;font-family:'Outfit',sans-serif;}
  .btn-out:hover{background:#0a0a0a;color:#fff;}
  @media(max-width:560px){.grid{grid-template-columns:1fr;}.card{padding:22px;}.pat-card{flex-direction:column;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">🏥 Task 20</div>
  <h1>Patient Registration<br>System</h1>
  <p class="sub">Register new patients and generate a hospital patient ID instantly.</p>

  <?php if($success): ?>
  <div class="pat-card">
    <div class="pc-left">
      <div class="pc-pid">Patient ID</div>
      <div class="pc-name"><?= htmlspecialchars($data['pid']) ?></div>
      <div class="pc-sub"><?= htmlspecialchars($data['name']) ?> &nbsp;·&nbsp; <?= htmlspecialchars($data['gender']) ?></div>
      <div class="blood-badge"><?= htmlspecialchars($data['blood']) ?></div>
    </div>
    <div class="pc-right">
      <div class="pc-date">Registered on</div>
      <div style="font-family:'Outfit',sans-serif;font-weight:700;margin-top:4px;"><?= $data['date'] ?></div>
      <div style="font-size:0.8rem;color:#9ca3af;margin-top:2px;"><?= $data['time'] ?></div>
    </div>
  </div>
  <div class="card">
    <div class="sec-label">Patient Details</div>
    <table class="info-table">
      <tr><td>Full Name</td><td><?= htmlspecialchars($data['name']) ?></td></tr>
      <tr><td>Date of Birth</td><td><?= htmlspecialchars($data['dob']) ?></td></tr>
      <tr><td>Gender</td><td><?= htmlspecialchars($data['gender']) ?></td></tr>
      <tr><td>Blood Group</td><td><?= htmlspecialchars($data['blood']) ?></td></tr>
      <tr><td>Phone</td><td><?= htmlspecialchars($data['phone']) ?></td></tr>
      <tr><td>Email</td><td><?= htmlspecialchars($data['email']) ?></td></tr>
      <tr><td>Address</td><td><?= htmlspecialchars($data['address']) ?></td></tr>
      <tr><td>Ward / Department</td><td><?= htmlspecialchars($data['ward']) ?></td></tr>
      <tr><td>Disease / Complaint</td><td><?= htmlspecialchars($data['disease']) ?></td></tr>
      <tr><td>Assigned Doctor</td><td><?= htmlspecialchars($data['doctor']) ?></td></tr>
    </table>
    <div style="text-align:center;"><button class="btn-out" onclick="window.location='<?= $_SERVER['PHP_SELF'] ?>'">Register New Patient</button></div>
  </div>
  <?php else: ?>
  <div class="card">
    <form method="POST">
      <div class="sec-label">Personal Information</div>
      <div class="grid">
        <div class="fg full"><label>Full Name</label>
          <input type="text" name="name" placeholder="Patient's full name" value="<?= htmlspecialchars($data['name']??'') ?>" class="<?= isset($errors['name'])?'input-err':'' ?>">
          <?php if(isset($errors['name'])): ?><span class="err-msg"><?= $errors['name'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Date of Birth</label>
          <input type="date" name="dob" value="<?= htmlspecialchars($data['dob']??'') ?>" class="<?= isset($errors['dob'])?'input-err':'' ?>">
          <?php if(isset($errors['dob'])): ?><span class="err-msg"><?= $errors['dob'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Gender</label>
          <select name="gender" class="<?= isset($errors['gender'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach(['Male','Female','Other'] as $g): ?><option value="<?= $g ?>" <?= (($data['gender']??'')===$g)?'selected':'' ?>><?= $g ?></option><?php endforeach; ?>
          </select>
          <?php if(isset($errors['gender'])): ?><span class="err-msg"><?= $errors['gender'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Blood Group</label>
          <select name="blood" class="<?= isset($errors['blood'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $b): ?><option value="<?= $b ?>" <?= (($data['blood']??'')===$b)?'selected':'' ?>><?= $b ?></option><?php endforeach; ?>
          </select>
          <?php if(isset($errors['blood'])): ?><span class="err-msg"><?= $errors['blood'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Phone Number</label>
          <input type="text" name="phone" placeholder="10 digits" value="<?= htmlspecialchars($data['phone']??'') ?>" class="<?= isset($errors['phone'])?'input-err':'' ?>">
          <?php if(isset($errors['phone'])): ?><span class="err-msg"><?= $errors['phone'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Email Address</label>
          <input type="email" name="email" placeholder="email@domain.com" value="<?= htmlspecialchars($data['email']??'') ?>" class="<?= isset($errors['email'])?'input-err':'' ?>">
          <?php if(isset($errors['email'])): ?><span class="err-msg"><?= $errors['email'] ?></span><?php endif; ?></div>
        <div class="fg full"><label>Address</label>
          <textarea name="address" placeholder="Full address" class="<?= isset($errors['address'])?'input-err':'' ?>"><?= htmlspecialchars($data['address']??'') ?></textarea>
          <?php if(isset($errors['address'])): ?><span class="err-msg"><?= $errors['address'] ?></span><?php endif; ?></div>
      </div>
      <div class="sec-label" style="margin-top:20px;">Medical Details</div>
      <div class="grid">
        <div class="fg"><label>Ward / Department</label>
          <select name="ward" class="<?= isset($errors['ward'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach(['General Ward','ICU','Emergency','Paediatrics','Cardiology','Orthopaedics','Neurology','Oncology','Gynaecology'] as $w): ?><option value="<?= $w ?>" <?= (($data['ward']??'')===$w)?'selected':'' ?>><?= $w ?></option><?php endforeach; ?>
          </select>
          <?php if(isset($errors['ward'])): ?><span class="err-msg"><?= $errors['ward'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Assigned Doctor</label>
          <select name="doctor" class="<?= isset($errors['doctor'])?'input-err':'' ?>">
            <option value="">Select Doctor</option>
            <?php foreach(['Dr. Anjali Sharma','Dr. Ravi Patel','Dr. Meena Nair','Dr. Suresh Kumar','Dr. Priya Reddy','Dr. Arun Bose'] as $dr): ?><option value="<?= $dr ?>" <?= (($data['doctor']??'')===$dr)?'selected':'' ?>><?= $dr ?></option><?php endforeach; ?>
          </select>
          <?php if(isset($errors['doctor'])): ?><span class="err-msg"><?= $errors['doctor'] ?></span><?php endif; ?></div>
        <div class="fg full"><label>Chief Complaint / Disease</label>
          <textarea name="disease" placeholder="Describe symptoms or known diagnosis" class="<?= isset($errors['disease'])?'input-err':'' ?>"><?= htmlspecialchars($data['disease']??'') ?></textarea>
          <?php if(isset($errors['disease'])): ?><span class="err-msg"><?= $errors['disease'] ?></span><?php endif; ?></div>
      </div>
      <button type="submit" class="btn">Register Patient →</button>
    </form>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
