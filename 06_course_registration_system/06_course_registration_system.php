<?php
$success = false;
$errors  = [];
$data    = [];

$courses = [
  'CS101' => 'Introduction to Programming',
  'CS201' => 'Data Structures & Algorithms',
  'CS301' => 'Database Management Systems',
  'CS401' => 'Web Technologies',
  'MA101' => 'Mathematics I',
  'MA201' => 'Statistics & Probability',
  'PH101' => 'Physics Fundamentals',
  'EN101' => 'English Communication',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['sid']      = trim($_POST['sid'] ?? '');
    $data['sname']    = trim($_POST['sname'] ?? '');
    $data['dept']     = trim($_POST['dept'] ?? '');
    $data['semester'] = trim($_POST['semester'] ?? '');
    $data['courses']  = $_POST['courses'] ?? [];

    if (empty($data['sid']))      $errors['sid']      = 'Required';
    if (empty($data['sname']))    $errors['sname']    = 'Required';
    if (empty($data['dept']))     $errors['dept']     = 'Required';
    if (empty($data['semester'])) $errors['semester'] = 'Required';
    if (empty($data['courses']))  $errors['courses']  = 'Select at least one course';
    elseif (count($data['courses']) > 5) $errors['courses'] = 'Maximum 5 courses allowed';

    if (empty($errors)) $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Course Registration System</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:740px;margin:0 auto;padding:48px 20px 80px;}
  .chip{display:inline-flex;align-items:center;background:#0a0a0a;color:#fff;
    padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;
    text-transform:uppercase;margin-bottom:16px;}
  h1{font-family:'Outfit',sans-serif;font-size:2.3rem;font-weight:800;line-height:1.15;letter-spacing:-0.5px;}
  .sub{color:#6b7280;margin-top:8px;font-size:0.95rem;margin-bottom:36px;}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:36px;box-shadow:0 8px 40px rgba(0,0,0,0.08);}
  .section-label{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;
    color:#9ca3af;margin-bottom:18px;padding-bottom:8px;border-bottom:1px solid #e5e7eb;}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
  .fg{display:flex;flex-direction:column;gap:5px;}
  .fg.full{grid-column:1/-1;}
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
  /* Course checkboxes */
  .courses-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:4px;}
  .course-check{display:flex;align-items:flex-start;gap:10px;padding:12px;border:1.5px solid #e5e7eb;
    border-radius:10px;cursor:pointer;transition:all 0.2s;background:#fafafa;}
  .course-check:hover{border-color:#0a0a0a;background:#fff;}
  .course-check input[type=checkbox]{width:16px;height:16px;accent-color:#0a0a0a;margin-top:2px;cursor:pointer;flex-shrink:0;}
  .course-check .cc{display:flex;flex-direction:column;}
  .course-check .code{font-size:0.7rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;}
  .course-check .cname{font-size:0.85rem;font-weight:500;color:#0a0a0a;margin-top:1px;}
  .course-check:has(input:checked){border-color:#0a0a0a;background:#f8f8f8;}
  /* Success */
  .success-banner{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:24px;text-align:center;margin-bottom:24px;}
  .success-banner h2{font-family:'Outfit',sans-serif;font-size:1.3rem;font-weight:700;color:#16a34a;}
  .success-banner p{color:#4b7a5b;font-size:0.87rem;margin-top:6px;}
  .info-table{width:100%;border-collapse:collapse;}
  .info-table tr{border-bottom:1px solid #f0f0f0;}
  .info-table tr:last-child{border-bottom:none;}
  .info-table td{padding:10px 4px;font-size:0.88rem;}
  .info-table td:first-child{color:#6b7280;font-weight:500;width:38%;}
  .info-table td:last-child{font-weight:600;}
  .reg-courses{display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;}
  .rcourse-tag{background:#f0f0f0;border-radius:6px;padding:4px 10px;font-size:0.78rem;font-weight:600;}
  .btn-outline{display:inline-block;padding:10px 24px;border:1.5px solid #0a0a0a;border-radius:50px;
    font-size:0.86rem;font-weight:600;color:#0a0a0a;cursor:pointer;background:transparent;
    margin-top:18px;transition:all 0.2s;font-family:'Outfit',sans-serif;}
  .btn-outline:hover{background:#0a0a0a;color:#fff;}
  @media(max-width:560px){.grid{grid-template-columns:1fr;}.courses-grid{grid-template-columns:1fr;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">📚 Task 06</div>
  <h1>Course Registration<br>System</h1>
  <p class="sub">Register for your semester courses quickly and efficiently.</p>

  <div class="card">
    <?php if($success): ?>
      <div class="success-banner">
        <div style="font-size:2rem;margin-bottom:8px;">🎓</div>
        <h2>Registration Successful!</h2>
        <p>You have been enrolled in <?= count($data['courses']) ?> course(s) for this semester.</p>
      </div>
      <div class="section-label">Registration Summary</div>
      <table class="info-table">
        <tr><td>Student ID</td><td><?= htmlspecialchars($data['sid']) ?></td></tr>
        <tr><td>Student Name</td><td><?= htmlspecialchars($data['sname']) ?></td></tr>
        <tr><td>Department</td><td><?= htmlspecialchars($data['dept']) ?></td></tr>
        <tr><td>Semester</td><td><?= htmlspecialchars($data['semester']) ?></td></tr>
        <tr><td>Registered Courses</td><td>
          <div class="reg-courses">
            <?php foreach($data['courses'] as $c): ?>
            <span class="rcourse-tag"><?= htmlspecialchars($c) ?> — <?= htmlspecialchars($courses[$c] ?? '') ?></span>
            <?php endforeach; ?>
          </div>
        </td></tr>
      </table>
      <div style="text-align:center;">
        <button class="btn-outline" onclick="window.location='<?= $_SERVER['PHP_SELF'] ?>'">Register Another</button>
      </div>
    <?php else: ?>
      <div class="section-label">Student Information</div>
      <form method="POST">
        <div class="grid">
          <div class="fg">
            <label>Student ID</label>
            <input type="text" name="sid" placeholder="e.g. 24SBCS053" value="<?= htmlspecialchars($data['sid']??'') ?>" class="<?= isset($errors['sid'])?'input-err':'' ?>">
            <?php if(isset($errors['sid'])): ?><span class="err-msg"><?= $errors['sid'] ?></span><?php endif; ?>
          </div>
          <div class="fg">
            <label>Student Name</label>
            <input type="text" name="sname" placeholder="Full name" value="<?= htmlspecialchars($data['sname']??'') ?>" class="<?= isset($errors['sname'])?'input-err':'' ?>">
            <?php if(isset($errors['sname'])): ?><span class="err-msg"><?= $errors['sname'] ?></span><?php endif; ?>
          </div>
          <div class="fg">
            <label>Department</label>
            <select name="dept" class="<?= isset($errors['dept'])?'input-err':'' ?>">
              <option value="">Select</option>
              <?php foreach(['Computer Science','Information Technology','Mathematics','Physics','Commerce'] as $d): ?>
              <option value="<?= $d ?>" <?= (($data['dept']??'')===$d)?'selected':'' ?>><?= $d ?></option>
              <?php endforeach; ?>
            </select>
            <?php if(isset($errors['dept'])): ?><span class="err-msg"><?= $errors['dept'] ?></span><?php endif; ?>
          </div>
          <div class="fg">
            <label>Semester</label>
            <select name="semester" class="<?= isset($errors['semester'])?'input-err':'' ?>">
              <option value="">Select</option>
              <?php for($i=1;$i<=8;$i++): ?>
              <option value="Semester <?= $i ?>" <?= (($data['semester']??'')==="Semester $i")?'selected':'' ?>>Semester <?= $i ?></option>
              <?php endfor; ?>
            </select>
            <?php if(isset($errors['semester'])): ?><span class="err-msg"><?= $errors['semester'] ?></span><?php endif; ?>
          </div>
          <div class="fg full" style="margin-top:8px;">
            <label>Select Courses <span style="color:#9ca3af;text-transform:none;font-weight:400;font-size:0.72rem;">(Max 5)</span></label>
            <div class="courses-grid">
              <?php foreach($courses as $code => $cname): ?>
              <label class="course-check">
                <input type="checkbox" name="courses[]" value="<?= $code ?>"
                  <?= in_array($code, $data['courses']??[]) ? 'checked' : '' ?>>
                <div class="cc">
                  <span class="code"><?= $code ?></span>
                  <span class="cname"><?= $cname ?></span>
                </div>
              </label>
              <?php endforeach; ?>
            </div>
            <?php if(isset($errors['courses'])): ?><span class="err-msg"><?= $errors['courses'] ?></span><?php endif; ?>
          </div>
        </div>
        <button type="submit" class="btn">Register Courses →</button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
