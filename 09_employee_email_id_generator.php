<?php
$result = null;
$errors = [];
$data   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['fname']  = trim($_POST['fname'] ?? '');
    $data['lname']  = trim($_POST['lname'] ?? '');
    $data['eid']    = trim($_POST['eid'] ?? '');
    $data['dept']   = trim($_POST['dept'] ?? '');
    $data['domain'] = trim($_POST['domain'] ?? '');

    if (empty($data['fname']))  $errors['fname']  = 'Required';
    elseif (!preg_match('/^[A-Za-z]+$/', $data['fname'])) $errors['fname'] = 'Letters only';
    if (empty($data['lname']))  $errors['lname']  = 'Required';
    elseif (!preg_match('/^[A-Za-z]+$/', $data['lname'])) $errors['lname'] = 'Letters only';
    if (empty($data['eid']))    $errors['eid']    = 'Required';
    if (empty($data['dept']))   $errors['dept']   = 'Required';
    if (empty($data['domain'])) $errors['domain'] = 'Required';

    if (empty($errors)) {
        $fn   = strtolower($data['fname']);
        $ln   = strtolower($data['lname']);
        $dept = strtolower(str_replace([' ','&'], ['',''], $data['dept']));
        $id   = strtolower(preg_replace('/[^A-Za-z0-9]/','',$data['eid']));

        $formats = [
            'firstname.lastname'  => "$fn.$ln@{$data['domain']}",
            'f.lastname'          => substr($fn,0,1).".$ln@{$data['domain']}",
            'firstname.dept'      => "$fn.$dept@{$data['domain']}",
            'empid.firstname'     => "$id.$fn@{$data['domain']}",
            'firstname_id'        => "{$fn}_{$id}@{$data['domain']}",
        ];
        $result = ['formats' => $formats, 'primary' => "$fn.$ln@{$data['domain']}"];
    }
}
$depts = ['Computer Science','Information Technology','Human Resources','Finance','Marketing','Operations','Legal','Sales'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Employee Email ID Generator</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:680px;margin:0 auto;padding:48px 20px 80px;}
  .chip{display:inline-flex;background:#0a0a0a;color:#fff;padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;}
  h1{font-family:'Outfit',sans-serif;font-size:2.3rem;font-weight:800;letter-spacing:-0.5px;line-height:1.15;}
  .sub{color:#6b7280;margin-top:8px;font-size:0.95rem;margin-bottom:36px;}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:36px;box-shadow:0 8px 40px rgba(0,0,0,0.08);}
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
  .primary-email{background:#0a0a0a;color:#fff;border-radius:14px;padding:24px;text-align:center;margin-top:24px;}
  .primary-label{font-size:0.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;}
  .primary-addr{font-family:'Outfit',sans-serif;font-size:1.15rem;font-weight:700;word-break:break-all;}
  .copy-btn{margin-top:12px;padding:7px 20px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:50px;color:#fff;font-size:0.8rem;font-weight:600;cursor:pointer;transition:background 0.2s;}
  .copy-btn:hover{background:rgba(255,255,255,0.2);}
  .alt-list{margin-top:20px;}
  .alt-item{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#fafafa;border:1px solid #e5e7eb;border-radius:10px;margin-bottom:8px;gap:12px;flex-wrap:wrap;}
  .alt-format{font-size:0.74rem;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;min-width:140px;}
  .alt-email{font-family:'Outfit',sans-serif;font-size:0.9rem;font-weight:600;word-break:break-all;}
  .copy-sm{padding:5px 14px;background:#0a0a0a;border:none;border-radius:50px;color:#fff;font-size:0.72rem;font-weight:600;cursor:pointer;}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">📧 Task 09</div>
  <h1>Employee Email ID<br>Generator</h1>
  <p class="sub">Auto-generate professional email IDs in multiple formats.</p>

  <div class="card">
    <div class="sec-label">Employee Details</div>
    <form method="POST">
      <div class="grid">
        <div class="fg"><label>First Name</label>
          <input type="text" name="fname" placeholder="First name" value="<?= htmlspecialchars($data['fname']??'') ?>" class="<?= isset($errors['fname'])?'input-err':'' ?>">
          <?php if(isset($errors['fname'])): ?><span class="err-msg"><?= $errors['fname'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Last Name</label>
          <input type="text" name="lname" placeholder="Last name" value="<?= htmlspecialchars($data['lname']??'') ?>" class="<?= isset($errors['lname'])?'input-err':'' ?>">
          <?php if(isset($errors['lname'])): ?><span class="err-msg"><?= $errors['lname'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Employee ID</label>
          <input type="text" name="eid" placeholder="e.g. EMP-001" value="<?= htmlspecialchars($data['eid']??'') ?>" class="<?= isset($errors['eid'])?'input-err':'' ?>">
          <?php if(isset($errors['eid'])): ?><span class="err-msg"><?= $errors['eid'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Department</label>
          <select name="dept" class="<?= isset($errors['dept'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach($depts as $d): ?>
            <option value="<?= $d ?>" <?= (($data['dept']??'')===$d)?'selected':'' ?>><?= $d ?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['dept'])): ?><span class="err-msg"><?= $errors['dept'] ?></span><?php endif; ?></div>
        <div class="fg full"><label>Email Domain</label>
          <input type="text" name="domain" placeholder="e.g. company.com" value="<?= htmlspecialchars($data['domain']??'') ?>" class="<?= isset($errors['domain'])?'input-err':'' ?>">
          <?php if(isset($errors['domain'])): ?><span class="err-msg"><?= $errors['domain'] ?></span><?php endif; ?></div>
      </div>
      <button type="submit" class="btn">Generate Email IDs →</button>
    </form>

    <?php if($result): ?>
    <div class="primary-email">
      <div class="primary-label">✉ Primary Email ID</div>
      <div class="primary-addr" id="primaryEmail"><?= htmlspecialchars($result['primary']) ?></div>
      <button class="copy-btn" onclick="copyText('primaryEmail',this)">Copy</button>
    </div>
    <div class="sec-label" style="margin-top:20px;">Alternative Formats</div>
    <div class="alt-list">
      <?php $i=0; foreach($result['formats'] as $fmt => $email): if($email===$result['primary']){$i++;continue;} ?>
      <div class="alt-item">
        <span class="alt-format"><?= htmlspecialchars(str_replace('_',' ',$fmt)) ?></span>
        <span class="alt-email" id="email<?= $i ?>"><?= htmlspecialchars($email) ?></span>
        <button class="copy-sm" onclick="copyText('email<?= $i ?>',this)">Copy</button>
      </div>
      <?php $i++; endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
<script>
function copyText(id, btn) {
  const text = document.getElementById(id).textContent;
  navigator.clipboard.writeText(text).then(() => {
    btn.textContent = 'Copied!';
    setTimeout(() => btn.textContent = 'Copy', 2000);
  });
}
</script>
</body>
</html>
