<?php
$result = null;
$errors = [];
$data   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']   = trim($_POST['name'] ?? '');
    $data['roll']   = trim($_POST['roll'] ?? '');
    $data['dept']   = trim($_POST['dept'] ?? '');
    $data['sem']    = trim($_POST['sem'] ?? '');
    $data['marks']  = $_POST['marks'] ?? [];
    $data['subs']   = $_POST['subs'] ?? [];
    $data['max']    = $_POST['max'] ?? [];
    $data['credits']= $_POST['credits'] ?? [];

    if (empty($data['name'])) $errors['name'] = 'Required';
    if (empty($data['roll'])) $errors['roll'] = 'Required';
    if (empty($data['dept'])) $errors['dept'] = 'Required';
    if (empty($data['sem']))  $errors['sem']  = 'Required';

    $subjects = [];
    $valid = false;
    for ($i = 0; $i < 6; $i++) {
        $sname   = trim($data['subs'][$i] ?? '');
        $m       = $data['marks'][$i] ?? '';
        $mx      = $data['max'][$i] ?? 100;
        $cr      = (int)($data['credits'][$i] ?? 3);
        if ($sname !== '' && $m !== '') {
            if (!is_numeric($m) || $m < 0 || $m > $mx) { $errors["m$i"] = 'Invalid'; continue; }
            $pct   = round(($m/$mx)*100, 1);
            $grade = $pct>=90?'O':($pct>=80?'A+':($pct>=70?'A':($pct>=60?'B+':($pct>=50?'B':($pct>=40?'C':'F')))));
            $gp    = $pct>=90?10:($pct>=80?9:($pct>=70?8:($pct>=60?7:($pct>=50?6:($pct>=40?5:0)))));
            $subjects[] = compact('sname','m','mx','pct','grade','gp','cr');
            $valid = true;
        }
    }
    if (!$valid && empty($errors)) $errors['subs'] = 'Add at least one subject';

    if (empty($errors) && $valid) {
        $total_m  = array_sum(array_column($subjects,'m'));
        $total_mx = array_sum(array_column($subjects,'mx'));
        $tot_cr   = array_sum(array_column($subjects,'cr'));
        $sgpa_num = 0;
        foreach($subjects as $s) $sgpa_num += $s['gp'] * $s['cr'];
        $sgpa = $tot_cr > 0 ? round($sgpa_num/$tot_cr, 2) : 0;
        $pct  = $total_mx > 0 ? round(($total_m/$total_mx)*100, 2) : 0;
        $pass = !in_array('F', array_column($subjects,'grade'));
        $overall = $pct>=90?'O':($pct>=80?'A+':($pct>=70?'A':($pct>=60?'B+':($pct>=50?'B':($pct>=40?'C':'F')))));
        $result = compact('subjects','total_m','total_mx','sgpa','pct','pass','overall','tot_cr');
    }
}
$def_subs = ['Mathematics','Physics','Chemistry','Computer Science','English','Statistics'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Result Processing System</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:780px;margin:0 auto;padding:48px 20px 80px;}
  .chip{display:inline-flex;background:#0a0a0a;color:#fff;padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;}
  h1{font-family:'Outfit',sans-serif;font-size:2.3rem;font-weight:800;letter-spacing:-0.5px;line-height:1.15;}
  .sub{color:#6b7280;margin-top:8px;font-size:0.95rem;margin-bottom:36px;}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:36px;box-shadow:0 8px 40px rgba(0,0,0,0.08);margin-bottom:20px;}
  .sec-label{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#9ca3af;margin-bottom:18px;padding-bottom:8px;border-bottom:1px solid #e5e7eb;}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
  .fg{display:flex;flex-direction:column;gap:5px;}
  label{font-size:0.77rem;font-weight:600;letter-spacing:0.4px;text-transform:uppercase;}
  input,select{background:#fafafa;border:1.5px solid #e5e7eb;border-radius:10px;padding:11px 15px;color:#0a0a0a;font-size:0.94rem;width:100%;transition:all 0.2s;appearance:none;}
  input:focus,select:focus{outline:none;border-color:#0a0a0a;background:#fff;box-shadow:0 0 0 3px rgba(10,10,10,0.08);}
  select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;padding-right:40px;}
  .err-msg{color:#dc2626;font-size:0.76rem;font-weight:500;}
  .input-err{border-color:#dc2626!important;}
  .btn{width:100%;padding:14px;background:#0a0a0a;color:#fff;border:none;border-radius:10px;font-size:0.96rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;margin-top:22px;transition:transform 0.15s,box-shadow 0.15s;}
  .btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,0,0,0.18);}
  .sub-row{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:10px;margin-bottom:10px;align-items:start;}
  .sub-row input{padding:9px 12px;font-size:0.88rem;}
  /* Result */
  .result-header{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:20px;}
  .rh-box{background:#fafafa;border:1px solid #e5e7eb;border-radius:12px;padding:18px;text-align:center;}
  .rh-val{font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;}
  .rh-lbl{font-size:0.7rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-top:4px;}
  .result-table{width:100%;border-collapse:collapse;}
  .result-table th{font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#9ca3af;padding:8px 6px;text-align:left;border-bottom:2px solid #f0f0f0;}
  .result-table td{padding:10px 6px;font-size:0.87rem;border-bottom:1px solid #f8f8f8;}
  .grade-tag{display:inline-block;padding:3px 9px;border-radius:6px;font-size:0.78rem;font-weight:700;}
  .status-bar{text-align:center;border-radius:12px;padding:12px;font-family:'Outfit',sans-serif;font-size:0.96rem;font-weight:700;margin-top:16px;}
  .pass{background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;}
  .fail{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;}
  @media(max-width:560px){.grid{grid-template-columns:1fr;}.sub-row{grid-template-columns:1fr 1fr;}.result-header{grid-template-columns:1fr;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">📊 Task 26</div>
  <h1>Student Result<br>Processing System</h1>
  <p class="sub">Calculate SGPA, percentage, grades and generate a detailed result marksheet.</p>

  <div class="card">
    <form method="POST">
      <div class="sec-label">Student Info</div>
      <div class="grid" style="margin-bottom:20px;">
        <div class="fg"><label>Student Name</label>
          <input type="text" name="name" placeholder="Full name" value="<?= htmlspecialchars($data['name']??'') ?>" class="<?= isset($errors['name'])?'input-err':'' ?>">
          <?php if(isset($errors['name'])): ?><span class="err-msg"><?= $errors['name'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Roll Number</label>
          <input type="text" name="roll" placeholder="e.g. 24CS001" value="<?= htmlspecialchars($data['roll']??'') ?>" class="<?= isset($errors['roll'])?'input-err':'' ?>">
          <?php if(isset($errors['roll'])): ?><span class="err-msg"><?= $errors['roll'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Department</label>
          <select name="dept" class="<?= isset($errors['dept'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach(['Computer Science','Mathematics','Physics','Commerce','Biology'] as $d): ?><option value="<?= $d ?>" <?= (($data['dept']??'')===$d)?'selected':'' ?>><?= $d ?></option><?php endforeach; ?>
          </select>
          <?php if(isset($errors['dept'])): ?><span class="err-msg"><?= $errors['dept'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Semester</label>
          <select name="sem" class="<?= isset($errors['sem'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php for($i=1;$i<=8;$i++): ?><option value="Semester <?= $i ?>" <?= (($data['sem']??'')==="Semester $i")?'selected':'' ?>>Semester <?= $i ?></option><?php endfor; ?>
          </select>
          <?php if(isset($errors['sem'])): ?><span class="err-msg"><?= $errors['sem'] ?></span><?php endif; ?></div>
      </div>

      <div class="sec-label">Subject Marks</div>
      <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:10px;margin-bottom:8px;">
        <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#9ca3af;">Subject Name</div>
        <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#9ca3af;">Marks</div>
        <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#9ca3af;">Max</div>
        <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#9ca3af;">Credits</div>
      </div>
      <?php for($i=0;$i<6;$i++): ?>
      <div class="sub-row">
        <input type="text" name="subs[]" placeholder="Subject <?= $i+1 ?>" value="<?= htmlspecialchars(($_POST['subs'][$i]??$def_subs[$i])) ?>">
        <input type="number" name="marks[]" placeholder="Marks" min="0" step="0.5" value="<?= htmlspecialchars($_POST['marks'][$i]??'') ?>" class="<?= isset($errors["m$i"])?'input-err':'' ?>">
        <input type="number" name="max[]" placeholder="100" min="1" value="<?= htmlspecialchars($_POST['max'][$i]??'100') ?>">
        <input type="number" name="credits[]" placeholder="3" min="1" max="6" value="<?= htmlspecialchars($_POST['credits'][$i]??'3') ?>">
      </div>
      <?php endfor; ?>
      <?php if(isset($errors['subs'])): ?><span class="err-msg"><?= $errors['subs'] ?></span><?php endif; ?>
      <button type="submit" class="btn">Process Result →</button>
    </form>
  </div>

  <?php if($result): ?>
  <div class="card">
    <div class="sec-label"><?= htmlspecialchars($data['name']) ?> — <?= htmlspecialchars($data['roll']) ?> — <?= htmlspecialchars($data['sem']) ?></div>
    <div class="result-header">
      <div class="rh-box"><div class="rh-val"><?= $result['sgpa'] ?></div><div class="rh-lbl">SGPA</div></div>
      <div class="rh-box"><div class="rh-val"><?= $result['pct'] ?>%</div><div class="rh-lbl">Percentage</div></div>
      <div class="rh-box"><div class="rh-val"><?= $result['overall'] ?></div><div class="rh-lbl">Overall Grade</div></div>
    </div>
    <table class="result-table">
      <thead><tr><th>Subject</th><th>Marks</th><th>%</th><th>Grade</th><th>GP</th><th>Credits</th></tr></thead>
      <tbody>
      <?php foreach($result['subjects'] as $s):
        $gc = $s['grade']==='F'?'#fef2f2':'#f0fdf4';
        $gt = $s['grade']==='F'?'#dc2626':'#16a34a';
      ?>
      <tr>
        <td style="font-weight:600;"><?= htmlspecialchars($s['sname']) ?></td>
        <td><?= $s['m'] ?>/<?= $s['mx'] ?></td>
        <td><?= $s['pct'] ?>%</td>
        <td><span class="grade-tag" style="background:<?= $gc ?>;color:<?= $gt ?>"><?= $s['grade'] ?></span></td>
        <td style="font-weight:700;"><?= $s['gp'] ?></td>
        <td><?= $s['cr'] ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <div class="status-bar <?= $result['pass']?'pass':'fail' ?>">
      <?= $result['pass'] ? '✓ PASS — Well done!' : '✗ FAIL — Improvement needed in one or more subjects' ?>
    </div>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
