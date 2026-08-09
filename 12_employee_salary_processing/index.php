<?php
$result = null;
$errors = [];
$data   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']    = trim($_POST['name'] ?? '');
    $data['eid']     = trim($_POST['eid'] ?? '');
    $data['dept']    = trim($_POST['dept'] ?? '');
    $data['basic']   = trim($_POST['basic'] ?? '');
    $data['days']    = trim($_POST['days'] ?? '');
    $data['worked']  = trim($_POST['worked'] ?? '');
    $data['leaves']  = trim($_POST['leaves'] ?? '');
    $data['lop']     = trim($_POST['lop'] ?? '');
    $data['bonus']   = trim($_POST['bonus'] ?? '');
    $data['allowances'] = trim($_POST['allowances'] ?? '0');

    if (empty($data['name'])) $errors['name'] = 'Required';
    if (empty($data['eid']))  $errors['eid']  = 'Required';
    if (empty($data['dept'])) $errors['dept'] = 'Required';
    if ($data['basic'] === '' || !is_numeric($data['basic']) || $data['basic'] <= 0) $errors['basic'] = 'Enter valid basic salary';
    if ($data['days'] === ''  || !is_numeric($data['days'])  || $data['days'] <= 0)  $errors['days']  = 'Enter working days';

    if (empty($errors)) {
        $basic      = (float)$data['basic'];
        $days       = (int)$data['days'];
        $worked     = (int)($data['worked'] ?: $days);
        $lop        = (int)($data['lop'] ?? 0);
        $bonus      = (float)($data['bonus'] ?? 0);
        $allowances = (float)($data['allowances'] ?? 0);

        // Salary components
        $hra        = round($basic * 0.40, 2);
        $da         = round($basic * 0.10, 2);
        $ta         = 1500;
        $per_day    = round($basic / $days, 2);
        $lop_deduct = round($per_day * $lop, 2);
        $gross      = round($basic + $hra + $da + $ta + $allowances + $bonus, 2);

        // Deductions
        $pf         = round($basic * 0.12, 2);
        $esi        = round($gross * 0.0175, 2);
        $pt         = $gross > 15000 ? 200 : ($gross > 10000 ? 150 : 0);
        $tax        = round($gross * 0.10, 2);
        $total_ded  = round($lop_deduct + $pf + $esi + $pt + $tax, 2);
        $net        = round($gross - $total_ded, 2);

        $result = compact('basic','hra','da','ta','allowances','bonus','lop_deduct','pf','esi','pt','tax','gross','total_ded','net','per_day','lop');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Employee Salary Processing System</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:760px;margin:0 auto;padding:48px 20px 80px;}
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
  /* Payslip */
  .payslip{background:#fff;border:1px solid #e5e7eb;border-radius:20px;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,0.08);}
  .payslip-header{background:#0a0a0a;color:#fff;padding:28px 32px;display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;}
  .ph-company{font-family:'Outfit',sans-serif;font-size:1.1rem;font-weight:800;}
  .ph-title{font-size:0.75rem;color:#9ca3af;margin-top:2px;text-transform:uppercase;letter-spacing:1px;}
  .ph-net{text-align:right;}
  .ph-netlabel{font-size:0.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;}
  .ph-netval{font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;}
  .payslip-body{padding:28px 32px;}
  .payslip-meta{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;}
  .pm-item .pm-label{font-size:0.7rem;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:3px;}
  .pm-item .pm-val{font-size:0.88rem;font-weight:600;}
  .earn-ded{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
  .col-title{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#9ca3af;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid #f0f0f0;}
  .pay-row{display:flex;justify-content:space-between;padding:7px 0;font-size:0.87rem;border-bottom:1px solid #fafafa;}
  .pay-row .lbl{color:#6b7280;}
  .pay-row .val{font-weight:600;}
  .pay-total{display:flex;justify-content:space-between;padding:10px 0;font-size:0.9rem;font-weight:700;margin-top:4px;border-top:2px solid #0a0a0a;}
  .earn-total{color:#16a34a;}
  .ded-total{color:#dc2626;}
  @media(max-width:600px){.grid{grid-template-columns:1fr;}.earn-ded{grid-template-columns:1fr;}.payslip-meta{grid-template-columns:1fr 1fr;}.payslip-header{flex-direction:column;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">💰 Task 12</div>
  <h1>Employee Salary<br>Processing System</h1>
  <p class="sub">Generate detailed payslips with earnings, deductions and net pay.</p>

  <div class="card">
    <div class="sec-label">Employee & Salary Details</div>
    <form method="POST">
      <div class="grid">
        <div class="fg"><label>Employee Name</label>
          <input type="text" name="name" placeholder="Full name" value="<?= htmlspecialchars($data['name']??'') ?>" class="<?= isset($errors['name'])?'input-err':'' ?>">
          <?php if(isset($errors['name'])): ?><span class="err-msg"><?= $errors['name'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Employee ID</label>
          <input type="text" name="eid" placeholder="e.g. EMP-001" value="<?= htmlspecialchars($data['eid']??'') ?>" class="<?= isset($errors['eid'])?'input-err':'' ?>">
          <?php if(isset($errors['eid'])): ?><span class="err-msg"><?= $errors['eid'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Department</label>
          <select name="dept" class="<?= isset($errors['dept'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach(['Computer Science','Human Resources','Finance','Marketing','Operations','Sales'] as $d): ?>
            <option value="<?= $d ?>" <?= (($data['dept']??'')===$d)?'selected':'' ?>><?= $d ?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['dept'])): ?><span class="err-msg"><?= $errors['dept'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Basic Salary (₹)</label>
          <input type="number" name="basic" placeholder="e.g. 35000" min="0" step="0.01" value="<?= htmlspecialchars($data['basic']??'') ?>" class="<?= isset($errors['basic'])?'input-err':'' ?>">
          <?php if(isset($errors['basic'])): ?><span class="err-msg"><?= $errors['basic'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Working Days</label>
          <input type="number" name="days" placeholder="e.g. 26" min="1" max="31" value="<?= htmlspecialchars($data['days']??'') ?>" class="<?= isset($errors['days'])?'input-err':'' ?>">
          <?php if(isset($errors['days'])): ?><span class="err-msg"><?= $errors['days'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Days Worked</label>
          <input type="number" name="worked" placeholder="e.g. 24" min="0" max="31" value="<?= htmlspecialchars($data['worked']??'') ?>"></div>
        <div class="fg"><label>LOP Days (Loss of Pay)</label>
          <input type="number" name="lop" placeholder="0" min="0" value="<?= htmlspecialchars($data['lop']??'0') ?>"></div>
        <div class="fg"><label>Other Allowances (₹)</label>
          <input type="number" name="allowances" placeholder="0.00" min="0" step="0.01" value="<?= htmlspecialchars($data['allowances']??'0') ?>"></div>
        <div class="fg full"><label>Bonus / Incentive (₹)</label>
          <input type="number" name="bonus" placeholder="0.00" min="0" step="0.01" value="<?= htmlspecialchars($data['bonus']??'0') ?>"></div>
      </div>
      <button type="submit" class="btn">Process Salary →</button>
    </form>
  </div>

  <?php if($result): ?>
  <div class="payslip">
    <div class="payslip-header">
      <div>
        <div class="ph-company">Company Payroll System</div>
        <div class="ph-title">Payslip — <?= htmlspecialchars($data['name']) ?> &nbsp;|&nbsp; <?= htmlspecialchars($data['eid']) ?></div>
      </div>
      <div class="ph-net">
        <div class="ph-netlabel">Net Pay</div>
        <div class="ph-netval">₹<?= number_format($result['net'],2) ?></div>
      </div>
    </div>
    <div class="payslip-body">
      <div class="payslip-meta">
        <div class="pm-item"><div class="pm-label">Department</div><div class="pm-val"><?= htmlspecialchars($data['dept']) ?></div></div>
        <div class="pm-item"><div class="pm-label">Basic Salary</div><div class="pm-val">₹<?= number_format($result['basic'],2) ?></div></div>
        <div class="pm-item"><div class="pm-label">LOP Days</div><div class="pm-val"><?= $result['lop'] ?> days</div></div>
        <div class="pm-item"><div class="pm-label">Per Day Rate</div><div class="pm-val">₹<?= number_format($result['per_day'],2) ?></div></div>
      </div>
      <div class="earn-ded">
        <div>
          <div class="col-title">Earnings</div>
          <div class="pay-row"><span class="lbl">Basic Salary</span><span class="val">₹<?= number_format($result['basic'],2) ?></span></div>
          <div class="pay-row"><span class="lbl">HRA (40%)</span><span class="val">₹<?= number_format($result['hra'],2) ?></span></div>
          <div class="pay-row"><span class="lbl">DA (10%)</span><span class="val">₹<?= number_format($result['da'],2) ?></span></div>
          <div class="pay-row"><span class="lbl">Transport Allowance</span><span class="val">₹<?= number_format($result['ta'],2) ?></span></div>
          <?php if($result['allowances']>0): ?><div class="pay-row"><span class="lbl">Other Allowances</span><span class="val">₹<?= number_format($result['allowances'],2) ?></span></div><?php endif; ?>
          <?php if($result['bonus']>0): ?><div class="pay-row"><span class="lbl">Bonus / Incentive</span><span class="val">₹<?= number_format($result['bonus'],2) ?></span></div><?php endif; ?>
          <div class="pay-total earn-total"><span>Gross Pay</span><span>₹<?= number_format($result['gross'],2) ?></span></div>
        </div>
        <div>
          <div class="col-title">Deductions</div>
          <?php if($result['lop_deduct']>0): ?><div class="pay-row"><span class="lbl">LOP Deduction</span><span class="val">₹<?= number_format($result['lop_deduct'],2) ?></span></div><?php endif; ?>
          <div class="pay-row"><span class="lbl">PF (12%)</span><span class="val">₹<?= number_format($result['pf'],2) ?></span></div>
          <div class="pay-row"><span class="lbl">ESI (1.75%)</span><span class="val">₹<?= number_format($result['esi'],2) ?></span></div>
          <div class="pay-row"><span class="lbl">Professional Tax</span><span class="val">₹<?= number_format($result['pt'],2) ?></span></div>
          <div class="pay-row"><span class="lbl">Income Tax (10%)</span><span class="val">₹<?= number_format($result['tax'],2) ?></span></div>
          <div class="pay-total ded-total"><span>Total Deductions</span><span>₹<?= number_format($result['total_ded'],2) ?></span></div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
