<?php
session_start();
$errors  = [];
$success = false;
$locked  = false;

// Demo accounts
$accounts = [
    'ACC001' => ['pin'=>'1234','name'=>'Ravi Kumar',   'balance'=>125430.50,'type'=>'Savings'],
    'ACC002' => ['pin'=>'5678','name'=>'Priya Sharma', 'balance'=>87250.00, 'type'=>'Current'],
    'ACC003' => ['pin'=>'9012','name'=>'Arjun Patel',  'balance'=>250000.75,'type'=>'Savings'],
];

// Handle logout
if (isset($_GET['logout'])) { session_destroy(); header('Location: '.$_SERVER['PHP_SELF']); exit; }

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['logged_in'])) {
    $accno = trim($_POST['accno'] ?? '');
    $pin   = trim($_POST['pin'] ?? '');

    if (empty($accno)) $errors['accno'] = 'Account number required';
    if (empty($pin) || !preg_match('/^[0-9]{4}$/',$pin)) $errors['pin'] = '4-digit PIN required';

    if (empty($errors)) {
        if (!isset($accounts[$accno])) {
            $errors['accno'] = 'Account not found';
        } elseif ($accounts[$accno]['pin'] !== $pin) {
            $errors['pin'] = 'Incorrect PIN';
            $_SESSION['attempts'] = ($_SESSION['attempts']??0) + 1;
            if ($_SESSION['attempts'] >= 3) $locked = true;
        } else {
            $_SESSION['logged_in'] = true;
            $_SESSION['accno']     = $accno;
            $_SESSION['attempts']  = 0;
            $success = true;
        }
    }
}

$user    = null;
$account = null;
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    $account = $accounts[$_SESSION['accno']] ?? null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Banking Login System</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;display:flex;align-items:center;justify-content:center;}
  .outer{width:100%;max-width:420px;padding:20px;}
  .chip{display:inline-flex;background:#0a0a0a;color:#fff;padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;}
  h1{font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;letter-spacing:-0.5px;margin-bottom:6px;}
  .sub{color:#6b7280;font-size:0.88rem;margin-bottom:32px;}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:36px;box-shadow:0 12px 50px rgba(0,0,0,0.1);}
  .sec-label{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#9ca3af;margin-bottom:18px;padding-bottom:8px;border-bottom:1px solid #e5e7eb;}
  .fg{display:flex;flex-direction:column;gap:5px;margin-bottom:16px;}
  label{font-size:0.77rem;font-weight:600;letter-spacing:0.4px;text-transform:uppercase;}
  input{background:#fafafa;border:1.5px solid #e5e7eb;border-radius:10px;padding:13px 15px;color:#0a0a0a;font-size:0.94rem;width:100%;transition:all 0.2s;}
  input:focus{outline:none;border-color:#0a0a0a;background:#fff;box-shadow:0 0 0 3px rgba(10,10,10,0.08);}
  input::placeholder{color:#9ca3af;letter-spacing:0;}
  input[name=pin]{letter-spacing:0.5rem;font-size:1.2rem;font-weight:700;}
  .err-msg{color:#dc2626;font-size:0.76rem;font-weight:500;}
  .input-err{border-color:#dc2626!important;}
  .btn{width:100%;padding:14px;background:#0a0a0a;color:#fff;border:none;border-radius:10px;font-size:0.96rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;margin-top:8px;transition:transform 0.15s,box-shadow 0.15s;}
  .btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,0,0,0.18);}
  .lock-box{background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:16px;text-align:center;margin-top:12px;}
  .lock-box p{color:#dc2626;font-size:0.88rem;font-weight:600;}
  .demo-hint{background:#f8f8f8;border:1px solid #e5e7eb;border-radius:10px;padding:14px;margin-bottom:20px;font-size:0.82rem;color:#6b7280;}
  .demo-hint strong{color:#0a0a0a;}
  /* Dashboard */
  .dash-wrap{width:100%;max-width:860px;padding:20px;}
  .dash-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;flex-wrap:wrap;gap:12px;}
  .dash-header h2{font-family:'Outfit',sans-serif;font-size:1.3rem;font-weight:800;}
  .logout-btn{padding:8px 18px;border:1.5px solid #0a0a0a;border-radius:50px;font-size:0.82rem;font-weight:600;cursor:pointer;background:transparent;font-family:'Outfit',sans-serif;transition:all 0.2s;}
  .logout-btn:hover{background:#0a0a0a;color:#fff;}
  .balance-card{background:#0a0a0a;color:#fff;border-radius:18px;padding:32px;margin-bottom:20px;}
  .bc-label{font-size:0.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;}
  .bc-balance{font-family:'Outfit',sans-serif;font-size:3rem;font-weight:800;line-height:1;}
  .bc-info{display:flex;gap:24px;margin-top:20px;flex-wrap:wrap;}
  .bc-info-item .il{font-size:0.65rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;}
  .bc-info-item .iv{font-size:0.92rem;font-weight:600;margin-top:2px;}
  .quick-actions{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;}
  .qa-btn{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px 12px;text-align:center;cursor:pointer;transition:all 0.2s;}
  .qa-btn:hover{border-color:#0a0a0a;box-shadow:0 4px 16px rgba(0,0,0,0.08);}
  .qa-icon{font-size:1.4rem;margin-bottom:8px;}
  .qa-label{font-size:0.76rem;font-weight:600;color:#0a0a0a;}
  .txn-card{background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:24px;}
  .txn-row{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #f8f8f8;}
  .txn-row:last-child{border-bottom:none;}
  .txn-left{display:flex;align-items:center;gap:12px;}
  .txn-icon{width:36px;height:36px;background:#f4f4f4;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;}
  .txn-name{font-size:0.88rem;font-weight:600;}
  .txn-date{font-size:0.75rem;color:#9ca3af;margin-top:2px;}
  .txn-amount{font-family:'Outfit',sans-serif;font-size:0.95rem;font-weight:700;}
  .cr{color:#16a34a;} .dr{color:#dc2626;}
  @media(max-width:600px){.quick-actions{grid-template-columns:repeat(2,1fr);}body{align-items:flex-start;}}
</style>
</head>
<body>
<?php if($account): ?>
<div class="dash-wrap">
  <div class="dash-header">
    <div><div style="font-size:0.75rem;color:#9ca3af;">Welcome back,</div><h2><?= htmlspecialchars($account['name']) ?></h2></div>
    <a href="?logout=1"><button class="logout-btn">Sign Out</button></a>
  </div>
  <div class="balance-card">
    <div class="bc-label">Available Balance</div>
    <div class="bc-balance">₹<?= number_format($account['balance'],2) ?></div>
    <div class="bc-info">
      <div class="bc-info-item"><div class="il">Account No.</div><div class="iv"><?= htmlspecialchars($_SESSION['accno']) ?></div></div>
      <div class="bc-info-item"><div class="il">Account Type</div><div class="iv"><?= htmlspecialchars($account['type']) ?></div></div>
      <div class="bc-info-item"><div class="il">Status</div><div class="iv">● Active</div></div>
    </div>
  </div>
  <div class="quick-actions">
    <div class="qa-btn"><div class="qa-icon">💸</div><div class="qa-label">Transfer</div></div>
    <div class="qa-btn"><div class="qa-icon">💳</div><div class="qa-label">Pay Bills</div></div>
    <div class="qa-btn"><div class="qa-icon">📊</div><div class="qa-label">Statement</div></div>
    <div class="qa-btn"><div class="qa-icon">⚙️</div><div class="qa-label">Settings</div></div>
  </div>
  <div class="txn-card">
    <div class="sec-label">Recent Transactions</div>
    <?php
    $txns = [
      ['icon'=>'🛒','name'=>'Online Shopping','date'=>'09 Aug 2026','amount'=>-2500,'type'=>'dr'],
      ['icon'=>'💰','name'=>'Salary Credit','date'=>'01 Aug 2026','amount'=>55000,'type'=>'cr'],
      ['icon'=>'⚡','name'=>'Electricity Bill','date'=>'28 Jul 2026','amount'=>-1250,'type'=>'dr'],
      ['icon'=>'🏠','name'=>'Rent Payment','date'=>'01 Jul 2026','amount'=>-15000,'type'=>'dr'],
      ['icon'=>'💸','name'=>'Fund Transfer Received','date'=>'15 Jul 2026','amount'=>10000,'type'=>'cr'],
    ];
    foreach($txns as $t): ?>
    <div class="txn-row">
      <div class="txn-left">
        <div class="txn-icon"><?= $t['icon'] ?></div>
        <div><div class="txn-name"><?= $t['name'] ?></div><div class="txn-date"><?= $t['date'] ?></div></div>
      </div>
      <div class="txn-amount <?= $t['type'] ?>"><?= $t['amount']>0?'+':'' ?>₹<?= number_format(abs($t['amount']),2) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php else: ?>
<div class="outer">
  <div class="chip">🏦 Task 17</div>
  <h1>Online Banking<br>Login System</h1>
  <p class="sub">Secure access to your bank account.</p>

  <div class="card">
    <div class="demo-hint">
      <strong>Demo Accounts:</strong><br>
      ACC001 / PIN: 1234 &nbsp;|&nbsp; ACC002 / PIN: 5678 &nbsp;|&nbsp; ACC003 / PIN: 9012
    </div>

    <?php if($locked): ?>
    <div class="lock-box"><p>🔒 Account temporarily locked after 3 failed attempts.</p></div>
    <?php else: ?>
    <form method="POST">
      <div class="sec-label">Sign In to Your Account</div>
      <div class="fg">
        <label>Account Number</label>
        <input type="text" name="accno" placeholder="e.g. ACC001" value="<?= htmlspecialchars($_POST['accno']??'') ?>" autocomplete="off" class="<?= isset($errors['accno'])?'input-err':'' ?>">
        <?php if(isset($errors['accno'])): ?><span class="err-msg"><?= $errors['accno'] ?></span><?php endif; ?>
      </div>
      <div class="fg">
        <label>4-Digit PIN</label>
        <input type="password" name="pin" placeholder="••••" maxlength="4" autocomplete="off" class="<?= isset($errors['pin'])?'input-err':'' ?>">
        <?php if(isset($errors['pin'])): ?><span class="err-msg"><?= $errors['pin'] ?></span><?php endif; ?>
      </div>
      <?php if($_SESSION['attempts']??0 > 0): ?>
      <p style="color:#f59e0b;font-size:0.78rem;margin-bottom:8px;">⚠ <?= 3 - ($_SESSION['attempts']??0) ?> attempt(s) remaining</p>
      <?php endif; ?>
      <button type="submit" class="btn">Sign In →</button>
    </form>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>
</body>
</html>
