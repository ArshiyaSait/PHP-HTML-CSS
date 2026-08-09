<?php
$success = false;
$errors  = [];
$data    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['pname']   = trim($_POST['pname'] ?? '');
    $data['tname']   = trim($_POST['tname'] ?? '');
    $data['student'] = trim($_POST['student'] ?? '');
    $data['class']   = trim($_POST['class'] ?? '');
    $data['subject'] = trim($_POST['subject'] ?? '');
    $data['date']    = trim($_POST['date'] ?? '');
    $data['time']    = trim($_POST['time'] ?? '');
    $data['mode']    = trim($_POST['mode'] ?? '');
    $data['phone']   = trim($_POST['phone'] ?? '');
    $data['email']   = trim($_POST['email'] ?? '');
    $data['agenda']  = trim($_POST['agenda'] ?? '');

    if (empty($data['pname']))   $errors['pname']   = 'Required';
    if (empty($data['tname']))   $errors['tname']   = 'Required';
    if (empty($data['student'])) $errors['student'] = 'Required';
    if (empty($data['class']))   $errors['class']   = 'Required';
    if (empty($data['subject'])) $errors['subject'] = 'Required';
    if (empty($data['date']))    $errors['date']    = 'Required';
    elseif (strtotime($data['date']) < strtotime('today')) $errors['date'] = 'Date must be today or future';
    if (empty($data['time']))    $errors['time']    = 'Required';
    if (empty($data['mode']))    $errors['mode']    = 'Required';
    if (!preg_match('/^[0-9]{10}$/',$data['phone'])) $errors['phone'] = '10 digits required';
    if (!filter_var($data['email'],FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email required';

    if (empty($errors)) {
        $success = true;
        $booking_id = 'PTM-'.strtoupper(substr(md5($data['pname'].time()),0,6));
        $data['booking_id'] = $booking_id;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Parent-Teacher Meeting Registration</title>
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
  textarea{resize:vertical;min-height:72px;}
  .err-msg{color:#dc2626;font-size:0.76rem;font-weight:500;}
  .input-err{border-color:#dc2626!important;}
  .btn{width:100%;padding:14px;background:#0a0a0a;color:#fff;border:none;border-radius:10px;font-size:0.96rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;margin-top:22px;transition:transform 0.15s,box-shadow 0.15s;}
  .btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,0,0,0.18);}
  .success-banner{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:24px;text-align:center;margin-bottom:24px;}
  .success-banner h2{font-family:'Outfit',sans-serif;font-size:1.3rem;font-weight:700;color:#16a34a;}
  .booking-id{display:inline-block;background:#0a0a0a;color:#fff;padding:6px 18px;border-radius:50px;font-size:0.85rem;font-weight:700;font-family:'Outfit',sans-serif;margin-top:8px;letter-spacing:1px;}
  .info-table{width:100%;border-collapse:collapse;}
  .info-table tr{border-bottom:1px solid #f0f0f0;}
  .info-table td{padding:10px 4px;font-size:0.88rem;}
  .info-table td:first-child{color:#6b7280;font-weight:500;width:40%;}
  .info-table td:last-child{font-weight:600;}
  .btn-out{display:inline-block;padding:10px 24px;border:1.5px solid #0a0a0a;border-radius:50px;font-size:0.86rem;font-weight:600;color:#0a0a0a;cursor:pointer;background:transparent;margin-top:18px;transition:all 0.2s;font-family:'Outfit',sans-serif;}
  .btn-out:hover{background:#0a0a0a;color:#fff;}
  @media(max-width:560px){.grid{grid-template-columns:1fr;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">🤝 Task 18</div>
  <h1>Parent–Teacher Meeting<br>Registration System</h1>
  <p class="sub">Schedule a meeting with your child's teacher conveniently online.</p>

  <div class="card">
    <?php if($success): ?>
      <div class="success-banner">
        <div style="font-size:2rem;margin-bottom:8px;">✅</div>
        <h2>Meeting Scheduled!</h2>
        <p>Your PTM appointment has been confirmed.</p>
        <div class="booking-id"><?= htmlspecialchars($data['booking_id']) ?></div>
      </div>
      <div class="sec-label">Appointment Details</div>
      <table class="info-table">
        <tr><td>Parent Name</td><td><?= htmlspecialchars($data['pname']) ?></td></tr>
        <tr><td>Teacher Name</td><td><?= htmlspecialchars($data['tname']) ?></td></tr>
        <tr><td>Student Name</td><td><?= htmlspecialchars($data['student']) ?></td></tr>
        <tr><td>Class / Section</td><td><?= htmlspecialchars($data['class']) ?></td></tr>
        <tr><td>Subject</td><td><?= htmlspecialchars($data['subject']) ?></td></tr>
        <tr><td>Meeting Date</td><td><?= date('d M Y', strtotime($data['date'])) ?></td></tr>
        <tr><td>Meeting Time</td><td><?= date('h:i A', strtotime($data['time'])) ?></td></tr>
        <tr><td>Mode</td><td><?= htmlspecialchars($data['mode']) ?></td></tr>
        <tr><td>Contact Phone</td><td><?= htmlspecialchars($data['phone']) ?></td></tr>
        <tr><td>Email</td><td><?= htmlspecialchars($data['email']) ?></td></tr>
        <?php if($data['agenda']): ?><tr><td>Agenda</td><td><?= htmlspecialchars($data['agenda']) ?></td></tr><?php endif; ?>
      </table>
      <div style="text-align:center;"><button class="btn-out" onclick="window.location='<?= $_SERVER['PHP_SELF'] ?>'">Schedule Another</button></div>
    <?php else: ?>
      <form method="POST">
        <div class="sec-label">Parent Information</div>
        <div class="grid">
          <div class="fg"><label>Parent Name</label>
            <input type="text" name="pname" placeholder="Parent's full name" value="<?= htmlspecialchars($data['pname']??'') ?>" class="<?= isset($errors['pname'])?'input-err':'' ?>">
            <?php if(isset($errors['pname'])): ?><span class="err-msg"><?= $errors['pname'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Contact Phone</label>
            <input type="text" name="phone" placeholder="10 digits" value="<?= htmlspecialchars($data['phone']??'') ?>" class="<?= isset($errors['phone'])?'input-err':'' ?>">
            <?php if(isset($errors['phone'])): ?><span class="err-msg"><?= $errors['phone'] ?></span><?php endif; ?></div>
          <div class="fg full"><label>Email Address</label>
            <input type="email" name="email" placeholder="email@domain.com" value="<?= htmlspecialchars($data['email']??'') ?>" class="<?= isset($errors['email'])?'input-err':'' ?>">
            <?php if(isset($errors['email'])): ?><span class="err-msg"><?= $errors['email'] ?></span><?php endif; ?></div>
        </div>

        <div class="sec-label" style="margin-top:20px;">Student & Teacher</div>
        <div class="grid">
          <div class="fg"><label>Student Name</label>
            <input type="text" name="student" placeholder="Student's name" value="<?= htmlspecialchars($data['student']??'') ?>" class="<?= isset($errors['student'])?'input-err':'' ?>">
            <?php if(isset($errors['student'])): ?><span class="err-msg"><?= $errors['student'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Class / Section</label>
            <select name="class" class="<?= isset($errors['class'])?'input-err':'' ?>">
              <option value="">Select</option>
              <?php foreach(['Grade 1 - A','Grade 2 - A','Grade 3 - B','Grade 4 - A','Grade 5 - B','Grade 6 - A','Grade 7 - C','Grade 8 - A','Grade 9 - B','Grade 10 - A','Grade 11 - Science','Grade 12 - Commerce'] as $c): ?>
              <option value="<?= $c ?>" <?= (($data['class']??'')===$c)?'selected':'' ?>><?= $c ?></option>
              <?php endforeach; ?>
            </select>
            <?php if(isset($errors['class'])): ?><span class="err-msg"><?= $errors['class'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Teacher Name</label>
            <select name="tname" class="<?= isset($errors['tname'])?'input-err':'' ?>">
              <option value="">Select Teacher</option>
              <?php foreach(['Ms. Anjali Singh','Mr. Ramesh Kumar','Ms. Priya Nair','Mr. Suresh Patel','Ms. Kavitha Reddy','Mr. Arun Sharma'] as $t): ?>
              <option value="<?= $t ?>" <?= (($data['tname']??'')===$t)?'selected':'' ?>><?= $t ?></option>
              <?php endforeach; ?>
            </select>
            <?php if(isset($errors['tname'])): ?><span class="err-msg"><?= $errors['tname'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Subject of Concern</label>
            <select name="subject" class="<?= isset($errors['subject'])?'input-err':'' ?>">
              <option value="">Select Subject</option>
              <?php foreach(['Mathematics','Science','English','Social Studies','Computer','General Progress','Behaviour','Attendance'] as $s): ?>
              <option value="<?= $s ?>" <?= (($data['subject']??'')===$s)?'selected':'' ?>><?= $s ?></option>
              <?php endforeach; ?>
            </select>
            <?php if(isset($errors['subject'])): ?><span class="err-msg"><?= $errors['subject'] ?></span><?php endif; ?></div>
        </div>

        <div class="sec-label" style="margin-top:20px;">Meeting Schedule</div>
        <div class="grid">
          <div class="fg"><label>Preferred Date</label>
            <input type="date" name="date" value="<?= htmlspecialchars($data['date']??'') ?>" min="<?= date('Y-m-d') ?>" class="<?= isset($errors['date'])?'input-err':'' ?>">
            <?php if(isset($errors['date'])): ?><span class="err-msg"><?= $errors['date'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Preferred Time</label>
            <input type="time" name="time" value="<?= htmlspecialchars($data['time']??'') ?>" class="<?= isset($errors['time'])?'input-err':'' ?>">
            <?php if(isset($errors['time'])): ?><span class="err-msg"><?= $errors['time'] ?></span><?php endif; ?></div>
          <div class="fg full"><label>Meeting Mode</label>
            <select name="mode" class="<?= isset($errors['mode'])?'input-err':'' ?>">
              <option value="">Select Mode</option>
              <option value="In-Person (School)" <?= (($data['mode']??'')==='In-Person (School)')?'selected':'' ?>>In-Person (School)</option>
              <option value="Online (Video Call)" <?= (($data['mode']??'')==='Online (Video Call)')?'selected':'' ?>>Online (Video Call)</option>
              <option value="Phone Call" <?= (($data['mode']??'')==='Phone Call')?'selected':'' ?>>Phone Call</option>
            </select>
            <?php if(isset($errors['mode'])): ?><span class="err-msg"><?= $errors['mode'] ?></span><?php endif; ?></div>
          <div class="fg full"><label>Agenda / Notes (Optional)</label>
            <textarea name="agenda" placeholder="Brief description of what you'd like to discuss..."><?= htmlspecialchars($data['agenda']??'') ?></textarea></div>
        </div>
        <button type="submit" class="btn">Schedule Meeting →</button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
