<?php
$result = null;
$errors = [];
$data   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']    = trim($_POST['name'] ?? '');
    $data['roll']    = trim($_POST['roll'] ?? '');
    $data['dept']    = trim($_POST['dept'] ?? '');
    $data['exam']    = trim($_POST['exam'] ?? '');
    $subjects = $_POST['subjects'] ?? [];
    $marks    = $_POST['marks'] ?? [];
    $max      = $_POST['max'] ?? [];

    if (empty($data['name'])) $errors['name'] = 'Required';
    if (empty($data['roll'])) $errors['roll'] = 'Required';
    if (empty($data['dept'])) $errors['dept'] = 'Required';
    if (empty($data['exam'])) $errors['exam'] = 'Required';

    $subject_data = [];
    for ($i = 0; $i < 6; $i++) {
        $sname = trim($subjects[$i] ?? '');
        $sm    = trim($marks[$i] ?? '');
        $smx   = trim($max[$i] ?? '100');
        if ($sname !== '' && $sm !== '') {
            if (!is_numeric($sm) || $sm < 0 || $sm > $smx) { $errors["mark$i"] = "Invalid mark for subject ".($i+1); continue; }
            $subject_data[] = ['name'=>$sname,'marks'=>(float)$sm,'max'=>(float)$smx,'pct'=>round(($sm/$smx)*100,1),'grade'=>''];
        }
    }

    if (empty($subject_data) && empty($errors)) $errors['subjects'] = 'Add at least one subject';

    if (empty($errors)) {
        $total_marks = array_sum(array_column($subject_data,'marks'));
        $total_max   = array_sum(array_column($subject_data,'max'));
        $overall_pct = round(($total_marks / $total_max) * 100, 2);
        $pass = true;
        foreach ($subject_data as &$s) {
            $g = $s['pct']>=90?'O':($s['pct']>=80?'A+':($s['pct']>=70?'A':($s['pct']>=60?'B+':($s['pct']>=50?'B':($s['pct']>=40?'C':'F')))));
            $s['grade'] = $g;
            if ($s['pct'] < 40) $pass = false;
        }
        $overall_grade = $overall_pct>=90?'O':($overall_pct>=80?'A+':($overall_pct>=70?'A':($overall_pct>=60?'B+':($overall_pct>=50?'B':($overall_pct>=40?'C':'F')))));
        $result = compact('subject_data','total_marks','total_max','overall_pct','overall_grade','pass');
    }
}
$default_subjects = ['Mathematics','Physics','Chemistry','Computer Science','English','Statistics'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Examination Result Analysis System</title>
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
  /* Subjects input */
  .subject-row{display:grid;grid-template-columns:2fr 1fr 1fr;gap:10px;margin-bottom:10px;align-items:start;}
  .subject-row:first-child .sr-header{display:block;}
  .sr-header{font-size:0.7rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:5px;}
  /* Result */
  .result-summary{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:24px;}
  .rs-box{background:#fafafa;border:1px solid #e5e7eb;border-radius:12px;padding:16px;text-align:center;}
  .rs-val{font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;}
  .rs-lbl{font-size:0.7rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-top:4px;}
  .result-table{width:100%;border-collapse:collapse;}
  .result-table th{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#9ca3af;padding:10px 8px;text-align:left;border-bottom:2px solid #f0f0f0;}
  .result-table td{padding:11px 8px;font-size:0.88rem;border-bottom:1px solid #f8f8f8;}
  .result-table tr:last-child td{border-bottom:none;}
  .grade-badge{display:inline-block;padding:3px 10px;border-radius:6px;font-size:0.78rem;font-weight:700;}
  .bar-sm{height:4px;background:#f0f0f0;border-radius:50px;width:80px;display:inline-block;vertical-align:middle;margin-left:8px;}
  .bar-fill{height:100%;background:#0a0a0a;border-radius:50px;}
  .status-tag{text-align:center;border-radius:12px;padding:12px;font-family:'Outfit',sans-serif;font-size:1rem;font-weight:700;}
  .pass-tag{background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;}
  .fail-tag{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;}
  @media(max-width:560px){.grid{grid-template-columns:1fr;}.subject-row{grid-template-columns:1fr 1fr;}.result-summary{grid-template-columns:1fr;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">📝 Task 13</div>
  <h1>Examination Result<br>Analysis System</h1>
  <p class="sub">Enter marks for up to 6 subjects to generate a complete result analysis.</p>

  <div class="card">
    <form method="POST">
      <div class="sec-label">Student Information</div>
      <div class="grid" style="margin-bottom:24px;">
        <div class="fg"><label>Student Name</label>
          <input type="text" name="name" placeholder="Full name" value="<?= htmlspecialchars($data['name']??'') ?>" class="<?= isset($errors['name'])?'input-err':'' ?>">
          <?php if(isset($errors['name'])): ?><span class="err-msg"><?= $errors['name'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Roll Number</label>
          <input type="text" name="roll" placeholder="e.g. 24CS001" value="<?= htmlspecialchars($data['roll']??'') ?>" class="<?= isset($errors['roll'])?'input-err':'' ?>">
          <?php if(isset($errors['roll'])): ?><span class="err-msg"><?= $errors['roll'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Department</label>
          <select name="dept" class="<?= isset($errors['dept'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach(['Computer Science','Mathematics','Physics','Commerce','Biology'] as $d): ?>
            <option value="<?= $d ?>" <?= (($data['dept']??'')===$d)?'selected':'' ?>><?= $d ?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['dept'])): ?><span class="err-msg"><?= $errors['dept'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Examination</label>
          <select name="exam" class="<?= isset($errors['exam'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach(['Semester 1','Semester 2','Semester 3','Semester 4','Semester 5','Semester 6','Annual 2024','Annual 2025'] as $e): ?>
            <option value="<?= $e ?>" <?= (($data['exam']??'')===$e)?'selected':'' ?>><?= $e ?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['exam'])): ?><span class="err-msg"><?= $errors['exam'] ?></span><?php endif; ?></div>
      </div>

      <div class="sec-label">Subject Marks</div>
      <div class="subject-row">
        <div><div class="sr-header">Subject Name</div></div>
        <div><div class="sr-header">Marks Obtained</div></div>
        <div><div class="sr-header">Max Marks</div></div>
      </div>
      <?php for($i=0;$i<6;$i++): ?>
      <div class="subject-row">
        <input type="text" name="subjects[]" placeholder="Subject <?= $i+1 ?>" value="<?= htmlspecialchars(($_POST['subjects'][$i]??$default_subjects[$i])) ?>">
        <input type="number" name="marks[]" placeholder="e.g. 75" min="0" step="0.5" value="<?= htmlspecialchars($_POST['marks'][$i]??'') ?>" class="<?= isset($errors["mark$i"])?'input-err':'' ?>">
        <input type="number" name="max[]" placeholder="100" min="1" value="<?= htmlspecialchars($_POST['max'][$i]??'100') ?>">
      </div>
      <?php if(isset($errors["mark$i"])): ?><span class="err-msg"><?= $errors["mark$i"] ?></span><?php endif; ?>
      <?php endfor; ?>
      <?php if(isset($errors['subjects'])): ?><span class="err-msg"><?= $errors['subjects'] ?></span><?php endif; ?>
      <button type="submit" class="btn">Analyze Results →</button>
    </form>
  </div>

  <?php if($result): ?>
  <div class="card">
    <div class="sec-label"><?= htmlspecialchars($data['name']) ?> — <?= htmlspecialchars($data['exam']) ?> Results</div>
    <div class="result-summary">
      <div class="rs-box"><div class="rs-val"><?= $result['overall_pct'] ?>%</div><div class="rs-lbl">Percentage</div></div>
      <div class="rs-box"><div class="rs-val"><?= $result['overall_grade'] ?></div><div class="rs-lbl">Overall Grade</div></div>
      <div class="rs-box"><div class="rs-val"><?= $result['total_marks'] ?>/<?= $result['total_max'] ?></div><div class="rs-lbl">Total Marks</div></div>
    </div>
    <table class="result-table">
      <thead><tr><th>Subject</th><th>Marks</th><th>%</th><th>Grade</th><th>Progress</th></tr></thead>
      <tbody>
      <?php foreach($result['subject_data'] as $s):
        $gc = $s['grade']==='F'?'#fef2f2':($s['pct']>=70?'#f0fdf4':'#fffbeb');
        $gt = $s['grade']==='F'?'#dc2626':($s['pct']>=70?'#16a34a':'#d97706');
      ?>
      <tr>
        <td style="font-weight:600;"><?= htmlspecialchars($s['name']) ?></td>
        <td><?= $s['marks'] ?>/<?= $s['max'] ?></td>
        <td><?= $s['pct'] ?>%</td>
        <td><span class="grade-badge" style="background:<?= $gc ?>;color:<?= $gt ?>"><?= $s['grade'] ?></span></td>
        <td><span class="bar-sm"><span class="bar-fill" style="width:<?= $s['pct'] ?>%"></span></span></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <div class="status-tag <?= $result['pass']?'pass-tag':'fail-tag' ?>" style="margin-top:20px;">
      <?= $result['pass'] ? '✓ PASS — Congratulations!' : '✗ FAIL — Minimum 40% required in all subjects' ?>
    </div>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
