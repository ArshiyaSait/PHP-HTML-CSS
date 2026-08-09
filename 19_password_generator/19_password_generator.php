<?php
$password = '';
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['length']  = (int)($_POST['length'] ?? 12);
    $data['upper']   = isset($_POST['upper']);
    $data['lower']   = isset($_POST['lower']);
    $data['numbers'] = isset($_POST['numbers']);
    $data['symbols'] = isset($_POST['symbols']);
    $data['count']   = (int)($_POST['count'] ?? 1);

    $data['length'] = max(6, min(64, $data['length']));
    $data['count']  = max(1, min(10, $data['count']));

    $pool = '';
    if ($data['upper'])   $pool .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if ($data['lower'])   $pool .= 'abcdefghijklmnopqrstuvwxyz';
    if ($data['numbers']) $pool .= '0123456789';
    if ($data['symbols']) $pool .= '!@#$%^&*()-_=+[]{}|;:,.<>?';

    if (empty($pool)) $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    $passwords = [];
    for ($p = 0; $p < $data['count']; $p++) {
        $pwd = '';
        for ($i = 0; $i < $data['length']; $i++) {
            $pwd .= $pool[random_int(0, strlen($pool) - 1)];
        }
        // Strength
        $strength = 0;
        if (preg_match('/[A-Z]/', $pwd)) $strength++;
        if (preg_match('/[a-z]/', $pwd)) $strength++;
        if (preg_match('/[0-9]/', $pwd)) $strength++;
        if (preg_match('/[^A-Za-z0-9]/', $pwd)) $strength++;
        if (strlen($pwd) >= 12) $strength++;
        if (strlen($pwd) >= 16) $strength++;
        $level = $strength <= 2 ? 'Weak' : ($strength <= 4 ? 'Medium' : 'Strong');
        $passwords[] = ['pwd' => $pwd, 'level' => $level, 'strength' => $strength];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Password Generator</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:640px;margin:0 auto;padding:48px 20px 80px;}
  .chip{display:inline-flex;background:#0a0a0a;color:#fff;padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;}
  h1{font-family:'Outfit',sans-serif;font-size:2.3rem;font-weight:800;letter-spacing:-0.5px;line-height:1.15;}
  .sub{color:#6b7280;margin-top:8px;font-size:0.95rem;margin-bottom:36px;}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:36px;box-shadow:0 8px 40px rgba(0,0,0,0.08);margin-bottom:20px;}
  .sec-label{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#9ca3af;margin-bottom:18px;padding-bottom:8px;border-bottom:1px solid #e5e7eb;}
  .option-row{display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid #f8f8f8;}
  .option-row:last-child{border-bottom:none;}
  .opt-label{font-size:0.9rem;font-weight:500;}
  .opt-desc{font-size:0.75rem;color:#9ca3af;margin-top:1px;}
  /* Toggle switch */
  .toggle{position:relative;display:inline-block;width:44px;height:24px;}
  .toggle input{opacity:0;width:0;height:0;}
  .slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:#e5e7eb;border-radius:50px;transition:.25s;}
  .slider:before{position:absolute;content:"";height:18px;width:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.25s;box-shadow:0 1px 4px rgba(0,0,0,0.2);}
  input:checked + .slider{background:#0a0a0a;}
  input:checked + .slider:before{transform:translateX(20px);}
  /* Range input */
  .range-row{margin:16px 0;}
  .range-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;}
  .range-label{font-size:0.88rem;font-weight:600;}
  .range-val{font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:800;}
  input[type=range]{width:100%;accent-color:#0a0a0a;height:4px;}
  .count-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:16px;}
  .count-row select{background:#fafafa;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:0.9rem;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px;cursor:pointer;font-family:'Inter',sans-serif;color:#0a0a0a;}
  .count-row select:focus{outline:none;border-color:#0a0a0a;}
  .fg label{font-size:0.77rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:5px;}
  .btn{width:100%;padding:14px;background:#0a0a0a;color:#fff;border:none;border-radius:10px;font-size:0.96rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;margin-top:20px;transition:transform 0.15s,box-shadow 0.15s;}
  .btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,0,0,0.18);}
  /* Password display */
  .pwd-item{margin-bottom:14px;}
  .pwd-box{display:flex;align-items:center;gap:10px;background:#fafafa;border:1.5px solid #e5e7eb;border-radius:12px;padding:14px 16px;}
  .pwd-text{font-family:'JetBrains Mono',monospace;font-size:0.98rem;font-weight:600;flex:1;word-break:break-all;letter-spacing:0.5px;}
  .copy-btn{padding:6px 14px;background:#0a0a0a;border:none;border-radius:8px;color:#fff;font-size:0.76rem;font-weight:600;cursor:pointer;white-space:nowrap;transition:background 0.2s;}
  .copy-btn:hover{background:#333;}
  .strength-bar{height:4px;border-radius:50px;margin-top:6px;}
  .strength-label{font-size:0.72rem;color:#9ca3af;margin-top:3px;}
  .weak .strength-bar{background:#dc2626;width:33%;}
  .medium .strength-bar{background:#f59e0b;width:66%;}
  .strong .strength-bar{background:#16a34a;width:100%;}
  .weak .strength-label{color:#dc2626;}
  .medium .strength-label{color:#f59e0b;}
  .strong .strength-label{color:#16a34a;}
  @media(max-width:480px){.card{padding:22px;}.count-row{grid-template-columns:1fr;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">🔐 Task 19</div>
  <h1>Password Generator</h1>
  <p class="sub">Generate strong, secure passwords with custom rules instantly.</p>

  <div class="card">
    <form method="POST">
      <div class="sec-label">Password Options</div>

      <div class="range-row">
        <div class="range-header">
          <span class="range-label">Password Length</span>
          <span class="range-val" id="lenVal"><?= $data['length'] ?? 12 ?></span>
        </div>
        <input type="range" name="length" min="6" max="64" value="<?= $data['length'] ?? 12 ?>"
          oninput="document.getElementById('lenVal').textContent=this.value">
      </div>

      <div class="option-row">
        <div><div class="opt-label">Uppercase Letters</div><div class="opt-desc">A B C D ... Z</div></div>
        <label class="toggle"><input type="checkbox" name="upper" <?= ($data['upper']??true)?'checked':'' ?>><span class="slider"></span></label>
      </div>
      <div class="option-row">
        <div><div class="opt-label">Lowercase Letters</div><div class="opt-desc">a b c d ... z</div></div>
        <label class="toggle"><input type="checkbox" name="lower" <?= ($data['lower']??true)?'checked':'' ?>><span class="slider"></span></label>
      </div>
      <div class="option-row">
        <div><div class="opt-label">Numbers</div><div class="opt-desc">0 1 2 3 ... 9</div></div>
        <label class="toggle"><input type="checkbox" name="numbers" <?= ($data['numbers']??true)?'checked':'' ?>><span class="slider"></span></label>
      </div>
      <div class="option-row">
        <div><div class="opt-label">Special Symbols</div><div class="opt-desc">! @ # $ % ^ & * ...</div></div>
        <label class="toggle"><input type="checkbox" name="symbols" <?= ($data['symbols']??false)?'checked':'' ?>><span class="slider"></span></label>
      </div>

      <div class="count-row">
        <div class="fg"><label>Number of Passwords</label>
          <select name="count">
            <?php foreach([1,2,3,5,10] as $n): ?>
            <option value="<?= $n ?>" <?= (($data['count']??1)===$n)?'selected':'' ?>><?= $n ?> password<?= $n>1?'s':'' ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="display:flex;align-items:flex-end;">
          <button type="submit" class="btn" style="margin-top:0;">Generate →</button>
        </div>
      </div>
    </form>
  </div>

  <?php if (!empty($passwords)): ?>
  <div class="card">
    <div class="sec-label">Generated Passwords</div>
    <?php foreach($passwords as $i => $pw): ?>
    <div class="pwd-item <?= strtolower($pw['level']) ?>">
      <div class="pwd-box">
        <span class="pwd-text" id="pwd<?= $i ?>"><?= htmlspecialchars($pw['pwd']) ?></span>
        <button class="copy-btn" onclick="copyPwd('pwd<?= $i ?>',this)">Copy</button>
      </div>
      <div class="strength-bar"></div>
      <div class="strength-label">Strength: <?= $pw['level'] ?></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<script>
function copyPwd(id, btn) {
  navigator.clipboard.writeText(document.getElementById(id).textContent).then(() => {
    btn.textContent = 'Copied!';
    setTimeout(() => btn.textContent = 'Copy', 2000);
  });
}
</script>
</body>
</html>
