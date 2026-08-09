<?php
$success = false;
$errors  = [];
$data    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['sid','sname','dob','gender','phone','email','address','dept','sem','batch','category','guardian','gphone'];
    foreach ($fields as $f) {
        $data[$f] = trim($_POST[$f] ?? '');
        if (empty($data[$f])) $errors[$f] = 'Required';
    }
    if (!empty($data['email']) && !filter_var($data['email'],FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email';
    if (!empty($data['phone']) && !preg_match('/^[0-9]{10}$/',$data['phone'])) $errors['phone'] = '10 digits';
    if (!empty($data['gphone']) && !preg_match('/^[0-9]{10}$/',$data['gphone'])) $errors['gphone'] = '10 digits';
    if (empty($errors)) $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Registration Form</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:780px;margin:0 auto;padding:48px 20px 80px;}
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
  .sec-gap{margin-top:24px;}
  .success-banner{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:24px;text-align:center;margin-bottom:24px;}
  .success-banner h2{font-family:'Outfit',sans-serif;font-size:1.3rem;font-weight:700;color:#16a34a;}
  .sid-badge{display:inline-block;background:#0a0a0a;color:#fff;padding:5px 18px;border-radius:50px;font-size:0.82rem;font-weight:700;margin-top:8px;letter-spacing:1px;}
  .info-table{width:100%;border-collapse:collapse;}
  .info-table tr{border-bottom:1px solid #f0f0f0;}
  .info-table td{padding:9px 4px;font-size:0.87rem;}
  .info-table td:first-child{color:#6b7280;font-weight:500;width:38%;}
  .info-table td:last-child{font-weight:600;}
  .btn-out{display:inline-block;padding:10px 24px;border:1.5px solid #0a0a0a;border-radius:50px;font-size:0.86rem;font-weight:600;color:#0a0a0a;cursor:pointer;background:transparent;margin-top:18px;transition:all 0.2s;font-family:'Outfit',sans-serif;}
  .btn-out:hover{background:#0a0a0a;color:#fff;}
  @media(max-width:560px){.grid{grid-template-columns:1fr;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">🎓 Task 25</div>
  <h1>Student Registration<br>Form</h1>
  <p class="sub">Complete your student enrollment with all required academic and personal details.</p>

  <div class="card">
    <?php if($success): ?>
      <div class="success-banner">
        <div style="font-size:2rem;margin-bottom:8px;">🎉</div>
        <h2>Registration Successful!</h2>
        <p>Welcome to the institution, <?= htmlspecialchars($data['sname']) ?>!</p>
        <div class="sid-badge"><?= htmlspecialchars($data['sid']) ?></div>
      </div>
      <div class="sec-label">Registered Details</div>
      <table class="info-table">
        <tr><td>Student ID</td><td><?= htmlspecialchars($data['sid']) ?></td></tr>
        <tr><td>Name</td><td><?= htmlspecialchars($data['sname']) ?></td></tr>
        <tr><td>Date of Birth</td><td><?= htmlspecialchars($data['dob']) ?></td></tr>
        <tr><td>Gender</td><td><?= htmlspecialchars($data['gender']) ?></td></tr>
        <tr><td>Email</td><td><?= htmlspecialchars($data['email']) ?></td></tr>
        <tr><td>Phone</td><td><?= htmlspecialchars($data['phone']) ?></td></tr>
        <tr><td>Department</td><td><?= htmlspecialchars($data['dept']) ?></td></tr>
        <tr><td>Semester</td><td><?= htmlspecialchars($data['sem']) ?></td></tr>
        <tr><td>Batch</td><td><?= htmlspecialchars($data['batch']) ?></td></tr>
        <tr><td>Category</td><td><?= htmlspecialchars($data['category']) ?></td></tr>
        <tr><td>Guardian Name</td><td><?= htmlspecialchars($data['guardian']) ?></td></tr>
        <tr><td>Guardian Phone</td><td><?= htmlspecialchars($data['gphone']) ?></td></tr>
      </table>
      <div style="text-align:center;"><button class="btn-out" onclick="window.location='<?= $_SERVER['PHP_SELF'] ?>'">Register Another</button></div>
    <?php else: ?>
      <form method="POST">
        <div class="sec-label">Academic Information</div>
        <div class="grid">
          <div class="fg"><label>Student ID</label>
            <input type="text" name="sid" placeholder="e.g. 24SBCS053" value="<?= htmlspecialchars($data['sid']??'') ?>" class="<?= isset($errors['sid'])?'input-err':'' ?>">
            <?php if(isset($errors['sid'])): ?><span class="err-msg"><?= $errors['sid'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Full Name</label>
            <input type="text" name="sname" placeholder="Student's full name" value="<?= htmlspecialchars($data['sname']??'') ?>" class="<?= isset($errors['sname'])?'input-err':'' ?>">
            <?php if(isset($errors['sname'])): ?><span class="err-msg"><?= $errors['sname'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Department</label>
            <select name="dept" class="<?= isset($errors['dept'])?'input-err':'' ?>">
              <option value="">Select</option>
              <?php foreach(['B.Sc Computer Science','BCA','B.Com','B.Sc Mathematics','B.Sc Physics','MCA','MBA'] as $d): ?><option value="<?= $d ?>" <?= (($data['dept']??'')===$d)?'selected':'' ?>><?= $d ?></option><?php endforeach; ?>
            </select>
            <?php if(isset($errors['dept'])): ?><span class="err-msg"><?= $errors['dept'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Semester</label>
            <select name="sem" class="<?= isset($errors['sem'])?'input-err':'' ?>">
              <option value="">Select</option>
              <?php for($i=1;$i<=8;$i++): ?><option value="Semester <?= $i ?>" <?= (($data['sem']??'')==="Semester $i")?'selected':'' ?>>Semester <?= $i ?></option><?php endfor; ?>
            </select>
            <?php if(isset($errors['sem'])): ?><span class="err-msg"><?= $errors['sem'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Batch Year</label>
            <select name="batch" class="<?= isset($errors['batch'])?'input-err':'' ?>">
              <option value="">Select</option>
              <?php foreach(['2020-2023','2021-2024','2022-2025','2023-2026','2024-2027'] as $b): ?><option value="<?= $b ?>" <?= (($data['batch']??'')===$b)?'selected':'' ?>><?= $b ?></option><?php endforeach; ?>
            </select>
            <?php if(isset($errors['batch'])): ?><span class="err-msg"><?= $errors['batch'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Category</label>
            <select name="category" class="<?= isset($errors['category'])?'input-err':'' ?>">
              <option value="">Select</option>
              <?php foreach(['General','OBC','SC','ST','EWS'] as $c): ?><option value="<?= $c ?>" <?= (($data['category']??'')===$c)?'selected':'' ?>><?= $c ?></option><?php endforeach; ?>
            </select>
            <?php if(isset($errors['category'])): ?><span class="err-msg"><?= $errors['category'] ?></span><?php endif; ?></div>
        </div>

        <div class="sec-label sec-gap">Personal Details</div>
        <div class="grid">
          <div class="fg"><label>Date of Birth</label>
            <input type="date" name="dob" value="<?= htmlspecialchars($data['dob']??'') ?>" class="<?= isset($errors['dob'])?'input-err':'' ?>">
            <?php if(isset($errors['dob'])): ?><span class="err-msg"><?= $errors['dob'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Gender</label>
            <select name="gender" class="<?= isset($errors['gender'])?'input-err':'' ?>">
              <option value="">Select</option>
              <?php foreach(['Male','Female','Other'] as $g): ?><option value="<?= $g ?>" <?= (($data['gender']??'')===$g)?'selected':'' ?>><?= $g ?></option><?php endforeach; ?>
            </select>
            <?php if(isset($errors['gender'])): ?><span class="err-msg"><?= $errors['gender'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Phone Number</label>
            <input type="text" name="phone" placeholder="10 digits" value="<?= htmlspecialchars($data['phone']??'') ?>" class="<?= isset($errors['phone'])?'input-err':'' ?>">
            <?php if(isset($errors['phone'])): ?><span class="err-msg"><?= $errors['phone'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Email Address</label>
            <input type="email" name="email" placeholder="email@domain.com" value="<?= htmlspecialchars($data['email']??'') ?>" class="<?= isset($errors['email'])?'input-err':'' ?>">
            <?php if(isset($errors['email'])): ?><span class="err-msg"><?= $errors['email'] ?></span><?php endif; ?></div>
          <div class="fg full"><label>Address</label>
            <textarea name="address" class="<?= isset($errors['address'])?'input-err':'' ?>"><?= htmlspecialchars($data['address']??'') ?></textarea>
            <?php if(isset($errors['address'])): ?><span class="err-msg"><?= $errors['address'] ?></span><?php endif; ?></div>
        </div>

        <div class="sec-label sec-gap">Guardian Details</div>
        <div class="grid">
          <div class="fg"><label>Guardian Name</label>
            <input type="text" name="guardian" placeholder="Parent/Guardian name" value="<?= htmlspecialchars($data['guardian']??'') ?>" class="<?= isset($errors['guardian'])?'input-err':'' ?>">
            <?php if(isset($errors['guardian'])): ?><span class="err-msg"><?= $errors['guardian'] ?></span><?php endif; ?></div>
          <div class="fg"><label>Guardian Phone</label>
            <input type="text" name="gphone" placeholder="10 digits" value="<?= htmlspecialchars($data['gphone']??'') ?>" class="<?= isset($errors['gphone'])?'input-err':'' ?>">
            <?php if(isset($errors['gphone'])): ?><span class="err-msg"><?= $errors['gphone'] ?></span><?php endif; ?></div>
        </div>

        <button type="submit" class="btn">Register Student →</button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
