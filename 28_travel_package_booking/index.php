<?php
$success = false;
$errors  = [];
$data    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']       = trim($_POST['name'] ?? '');
    $data['email']      = trim($_POST['email'] ?? '');
    $data['phone']      = trim($_POST['phone'] ?? '');
    $data['adults']     = (int)($_POST['adults'] ?? 1);
    $data['children']   = (int)($_POST['children'] ?? 0);
    $data['package']    = trim($_POST['package'] ?? '');
    $data['dest']       = trim($_POST['dest'] ?? '');
    $data['depart']     = trim($_POST['depart'] ?? '');
    $data['return']     = trim($_POST['return'] ?? '');
    $data['accomm']     = trim($_POST['accomm'] ?? '');
    $data['transport']  = trim($_POST['transport'] ?? '');
    $data['special']    = trim($_POST['special'] ?? '');

    if (empty($data['name']))    $errors['name']    = 'Required';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email';
    if (!preg_match('/^[0-9]{10}$/',$data['phone'])) $errors['phone'] = '10 digits required';
    if (empty($data['package'])) $errors['package'] = 'Required';
    if (empty($data['dest']))    $errors['dest']    = 'Required';
    if (empty($data['depart']))  $errors['depart']  = 'Required';
    if (empty($data['return']))  $errors['return']  = 'Required';
    if (empty($data['accomm']))  $errors['accomm']  = 'Required';
    if (empty($data['transport'])) $errors['transport'] = 'Required';

    if (!empty($data['depart']) && !empty($data['return'])) {
        if (strtotime($data['return']) < strtotime($data['depart'])) $errors['return'] = 'Return must be after departure';
    }

    if (empty($errors)) {
        $packages = ['Budget'=>5000,'Standard'=>9500,'Premium'=>15000,'Luxury'=>25000];
        $accomm_add = ['Hostel'=>0,'Budget Hotel'=>1500,'3-Star Hotel'=>4000,'5-Star Hotel'=>9000];
        $transport_add = ['Bus'=>0,'Train'=>1500,'Flight'=>5000];

        $base = $packages[$data['package']] ?? 5000;
        $acc  = $accomm_add[$data['accomm']] ?? 0;
        $trn  = $transport_add[$data['transport']] ?? 0;

        $days = max(1, (strtotime($data['return']) - strtotime($data['depart'])) / 86400);
        $per_adult = ($base + $acc + $trn) * $days;
        $per_child = $per_adult * 0.6;
        $subtotal  = ($per_adult * $data['adults']) + ($per_child * $data['children']);
        $gst       = round($subtotal * 0.05, 2);
        $total     = round($subtotal + $gst, 2);

        $booking_id = 'TOUR-'.strtoupper(substr(md5($data['name'].time()),0,6));
        $data['booking_id'] = $booking_id;
        $data['per_adult']  = $per_adult;
        $data['per_child']  = $per_child;
        $data['subtotal']   = $subtotal;
        $data['gst']        = $gst;
        $data['total']      = $total;
        $data['days']       = $days;
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Travel Package Booking System</title>
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
  /* Booking confirm */
  .booking-hero{background:#0a0a0a;color:#fff;border-radius:16px;padding:32px;text-align:center;margin-bottom:20px;}
  .bh-emoji{font-size:2.5rem;margin-bottom:12px;}
  .bh-id{font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:800;letter-spacing:2px;}
  .bh-name{font-size:0.88rem;color:#9ca3af;margin-top:6px;}
  .summary-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;}
  .sg-item{background:#fafafa;border:1px solid #e5e7eb;border-radius:10px;padding:14px;text-align:center;}
  .sg-val{font-family:'Outfit',sans-serif;font-size:1.3rem;font-weight:800;}
  .sg-lbl{font-size:0.7rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-top:4px;}
  .info-table{width:100%;border-collapse:collapse;}
  .info-table tr{border-bottom:1px solid #f0f0f0;}
  .info-table td{padding:9px 4px;font-size:0.87rem;}
  .info-table td:first-child{color:#6b7280;font-weight:500;width:40%;}
  .info-table td:last-child{font-weight:600;}
  .btn-out{display:inline-block;padding:10px 24px;border:1.5px solid #0a0a0a;border-radius:50px;font-size:0.86rem;font-weight:600;color:#0a0a0a;cursor:pointer;background:transparent;margin-top:18px;transition:all 0.2s;font-family:'Outfit',sans-serif;}
  .btn-out:hover{background:#0a0a0a;color:#fff;}
  .pkg-cards{display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-bottom:14px;}
  .pkg-card{border:1.5px solid #e5e7eb;border-radius:10px;padding:12px;cursor:pointer;transition:all 0.15s;background:#fafafa;}
  .pkg-card:hover{border-color:#0a0a0a;}
  .pkg-card.active{border-color:#0a0a0a;background:#0a0a0a;color:#fff;}
  .pkg-card .pn{font-size:0.88rem;font-weight:700;}
  .pkg-card .pp{font-size:0.75rem;opacity:0.7;margin-top:2px;}
  @media(max-width:560px){.grid{grid-template-columns:1fr;}.summary-grid{grid-template-columns:1fr 1fr;}.pkg-cards{grid-template-columns:1fr 1fr;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">✈️ Task 28</div>
  <h1>Travel Package<br>Booking System</h1>
  <p class="sub">Browse and book premium travel packages with accommodation and transport options.</p>

  <?php if($success): ?>
  <div class="booking-hero">
    <div class="bh-emoji">✅</div>
    <div class="bh-id"><?= htmlspecialchars($data['booking_id']) ?></div>
    <div class="bh-name">Booking confirmed for <?= htmlspecialchars($data['name']) ?></div>
  </div>

  <div class="summary-grid">
    <div class="sg-item"><div class="sg-val">₹<?= number_format($data['total'],0) ?></div><div class="sg-lbl">Total Cost</div></div>
    <div class="sg-item"><div class="sg-val"><?= $data['days'] ?></div><div class="sg-lbl">Days</div></div>
    <div class="sg-item"><div class="sg-val"><?= $data['adults']+$data['children'] ?></div><div class="sg-lbl">Travellers</div></div>
  </div>

  <div class="card">
    <div class="sec-label">Booking Details</div>
    <table class="info-table">
      <tr><td>Booking ID</td><td><?= htmlspecialchars($data['booking_id']) ?></td></tr>
      <tr><td>Traveller Name</td><td><?= htmlspecialchars($data['name']) ?></td></tr>
      <tr><td>Email</td><td><?= htmlspecialchars($data['email']) ?></td></tr>
      <tr><td>Phone</td><td><?= htmlspecialchars($data['phone']) ?></td></tr>
      <tr><td>Destination</td><td><?= htmlspecialchars($data['dest']) ?></td></tr>
      <tr><td>Package</td><td><?= htmlspecialchars($data['package']) ?></td></tr>
      <tr><td>Departure Date</td><td><?= date('d M Y', strtotime($data['depart'])) ?></td></tr>
      <tr><td>Return Date</td><td><?= date('d M Y', strtotime($data['return'])) ?></td></tr>
      <tr><td>Duration</td><td><?= $data['days'] ?> day(s)</td></tr>
      <tr><td>Adults</td><td><?= $data['adults'] ?></td></tr>
      <tr><td>Children</td><td><?= $data['children'] ?></td></tr>
      <tr><td>Accommodation</td><td><?= htmlspecialchars($data['accomm']) ?></td></tr>
      <tr><td>Transport</td><td><?= htmlspecialchars($data['transport']) ?></td></tr>
      <tr><td>Subtotal</td><td>₹<?= number_format($data['subtotal'],2) ?></td></tr>
      <tr><td>GST (5%)</td><td>₹<?= number_format($data['gst'],2) ?></td></tr>
      <tr><td><strong>Total</strong></td><td><strong>₹<?= number_format($data['total'],2) ?></strong></td></tr>
      <?php if($data['special']): ?><tr><td>Special Requests</td><td><?= htmlspecialchars($data['special']) ?></td></tr><?php endif; ?>
    </table>
    <div style="text-align:center;"><button class="btn-out" onclick="window.location='<?= $_SERVER['PHP_SELF'] ?>'">Book Another Trip</button></div>
  </div>

  <?php else: ?>
  <div class="card">
    <form method="POST">
      <div class="sec-label">Traveller Information</div>
      <div class="grid">
        <div class="fg"><label>Full Name</label>
          <input type="text" name="name" placeholder="Your full name" value="<?= htmlspecialchars($data['name']??'') ?>" class="<?= isset($errors['name'])?'input-err':'' ?>">
          <?php if(isset($errors['name'])): ?><span class="err-msg"><?= $errors['name'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Email Address</label>
          <input type="email" name="email" placeholder="email@domain.com" value="<?= htmlspecialchars($data['email']??'') ?>" class="<?= isset($errors['email'])?'input-err':'' ?>">
          <?php if(isset($errors['email'])): ?><span class="err-msg"><?= $errors['email'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Phone Number</label>
          <input type="text" name="phone" placeholder="10 digits" value="<?= htmlspecialchars($data['phone']??'') ?>" class="<?= isset($errors['phone'])?'input-err':'' ?>">
          <?php if(isset($errors['phone'])): ?><span class="err-msg"><?= $errors['phone'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Destination</label>
          <select name="dest" class="<?= isset($errors['dest'])?'input-err':'' ?>">
            <option value="">Select Destination</option>
            <?php foreach(['Ooty, Tamil Nadu','Kodaikanal, Tamil Nadu','Goa','Kerala Backwaters','Rajasthan Heritage Tour','Ladakh Adventure','Shimla & Manali','Andaman Islands','Varanasi Spiritual Tour','Mysore & Coorg'] as $d): ?>
            <option value="<?= $d ?>" <?= (($data['dest']??'')===$d)?'selected':'' ?>><?= $d ?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['dest'])): ?><span class="err-msg"><?= $errors['dest'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Adults</label>
          <select name="adults">
            <?php for($i=1;$i<=10;$i++): ?><option value="<?= $i ?>" <?= (($data['adults']??1)===$i)?'selected':'' ?>><?= $i ?> Adult<?= $i>1?'s':'' ?></option><?php endfor; ?>
          </select></div>
        <div class="fg"><label>Children (below 12)</label>
          <select name="children">
            <?php for($i=0;$i<=6;$i++): ?><option value="<?= $i ?>" <?= (($data['children']??0)===$i)?'selected':'' ?>><?= $i ?> Child<?= $i!=1?'ren':'' ?></option><?php endfor; ?>
          </select></div>
        <div class="fg"><label>Departure Date</label>
          <input type="date" name="depart" value="<?= htmlspecialchars($data['depart']??'') ?>" min="<?= date('Y-m-d') ?>" class="<?= isset($errors['depart'])?'input-err':'' ?>">
          <?php if(isset($errors['depart'])): ?><span class="err-msg"><?= $errors['depart'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Return Date</label>
          <input type="date" name="return" value="<?= htmlspecialchars($data['return']??'') ?>" min="<?= date('Y-m-d') ?>" class="<?= isset($errors['return'])?'input-err':'' ?>">
          <?php if(isset($errors['return'])): ?><span class="err-msg"><?= $errors['return'] ?></span><?php endif; ?></div>
      </div>

      <div class="sec-label sec-gap">Travel Package (per person per day base rate)</div>
      <div class="pkg-cards" id="pkgCards">
        <?php foreach(['Budget'=>'₹5,000','Standard'=>'₹9,500','Premium'=>'₹15,000','Luxury'=>'₹25,000'] as $pkg=>$rate): ?>
        <label class="pkg-card <?= (($data['package']??'')===$pkg)?'active':'' ?>" onclick="selectPkg(this,'<?= $pkg ?>')">
          <input type="radio" name="package" value="<?= $pkg ?>" <?= (($data['package']??'')===$pkg)?'checked':'' ?> style="display:none;">
          <div class="pn"><?= $pkg ?></div>
          <div class="pp"><?= $rate ?>/person/day</div>
        </label>
        <?php endforeach; ?>
      </div>
      <?php if(isset($errors['package'])): ?><span class="err-msg"><?= $errors['package'] ?></span><?php endif; ?>

      <div class="grid sec-gap">
        <div class="fg"><label>Accommodation</label>
          <select name="accomm" class="<?= isset($errors['accomm'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach(['Hostel','Budget Hotel','3-Star Hotel','5-Star Hotel'] as $a): ?><option value="<?= $a ?>" <?= (($data['accomm']??'')===$a)?'selected':'' ?>><?= $a ?></option><?php endforeach; ?>
          </select>
          <?php if(isset($errors['accomm'])): ?><span class="err-msg"><?= $errors['accomm'] ?></span><?php endif; ?></div>
        <div class="fg"><label>Mode of Transport</label>
          <select name="transport" class="<?= isset($errors['transport'])?'input-err':'' ?>">
            <option value="">Select</option>
            <?php foreach(['Bus','Train','Flight'] as $t): ?><option value="<?= $t ?>" <?= (($data['transport']??'')===$t)?'selected':'' ?>><?= $t ?></option><?php endforeach; ?>
          </select>
          <?php if(isset($errors['transport'])): ?><span class="err-msg"><?= $errors['transport'] ?></span><?php endif; ?></div>
        <div class="fg full"><label>Special Requests (Optional)</label>
          <textarea name="special" placeholder="Dietary requirements, accessibility needs, preferences..."><?= htmlspecialchars($data['special']??'') ?></textarea></div>
      </div>

      <button type="submit" class="btn">Book Now →</button>
    </form>
  </div>
  <?php endif; ?>
</div>

<script>
function selectPkg(el, pkg) {
  document.querySelectorAll('.pkg-card').forEach(c => c.classList.remove('active'));
  el.classList.add('active');
}
</script>
</body>
</html>
