<?php
$result = null;
$data   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['product']  = trim($_POST['product'] ?? '');
    $data['price']    = trim($_POST['price'] ?? '');
    $data['qty']      = trim($_POST['qty'] ?? '1');
    $data['discount'] = trim($_POST['discount'] ?? '0');
    $data['tax']      = trim($_POST['tax'] ?? '18');
    $data['dtype']    = trim($_POST['dtype'] ?? 'percent');

    if (is_numeric($data['price']) && $data['price'] > 0) {
        $price    = (float)$data['price'];
        $qty      = max(1,(int)$data['qty']);
        $disc_val = (float)$data['discount'];
        $tax_rate = (float)$data['tax'];

        $subtotal  = $price * $qty;
        $disc_amt  = $data['dtype'] === 'percent' ? round($subtotal * $disc_val / 100, 2) : min($disc_val, $subtotal);
        $after_disc= $subtotal - $disc_amt;
        $tax_amt   = round($after_disc * $tax_rate / 100, 2);
        $total     = round($after_disc + $tax_amt, 2);
        $savings   = $disc_amt;
        $result    = compact('price','qty','subtotal','disc_amt','after_disc','tax_amt','total','savings','tax_rate','disc_val');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sales Calculator</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:620px;margin:0 auto;padding:48px 20px 80px;}
  .chip{display:inline-flex;background:#0a0a0a;color:#fff;padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;}
  h1{font-family:'Outfit',sans-serif;font-size:2.3rem;font-weight:800;letter-spacing:-0.5px;line-height:1.15;}
  .sub{color:#6b7280;margin-top:8px;font-size:0.95rem;margin-bottom:36px;}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:36px;box-shadow:0 8px 40px rgba(0,0,0,0.08);margin-bottom:20px;}
  .sec-label{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#9ca3af;margin-bottom:18px;padding-bottom:8px;border-bottom:1px solid #e5e7eb;}
  .form-col{display:flex;flex-direction:column;gap:14px;}
  .grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
  .fg{display:flex;flex-direction:column;gap:5px;}
  label{font-size:0.77rem;font-weight:600;letter-spacing:0.4px;text-transform:uppercase;}
  input,select{background:#fafafa;border:1.5px solid #e5e7eb;border-radius:10px;padding:11px 15px;color:#0a0a0a;font-size:0.94rem;width:100%;transition:all 0.2s;appearance:none;}
  input:focus,select:focus{outline:none;border-color:#0a0a0a;background:#fff;box-shadow:0 0 0 3px rgba(10,10,10,0.08);}
  select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;padding-right:40px;}
  .disc-row{display:grid;grid-template-columns:2fr 1fr;gap:10px;}
  .btn{width:100%;padding:14px;background:#0a0a0a;color:#fff;border:none;border-radius:10px;font-size:0.96rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;margin-top:8px;transition:transform 0.15s,box-shadow 0.15s;}
  .btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,0,0,0.18);}
  /* Result */
  .total-display{background:#0a0a0a;color:#fff;border-radius:14px;padding:24px;text-align:center;margin-bottom:16px;}
  .td-label{font-size:0.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;}
  .td-amount{font-family:'Outfit',sans-serif;font-size:3.5rem;font-weight:800;line-height:1;}
  .td-product{font-size:0.82rem;color:#9ca3af;margin-top:6px;}
  .breakdown-rows{display:flex;flex-direction:column;gap:0;}
  .brow{display:flex;justify-content:space-between;padding:11px 0;border-bottom:1px solid #f0f0f0;font-size:0.88rem;}
  .brow:last-child{border-bottom:none;}
  .brow .lbl{color:#6b7280;}
  .brow .val{font-weight:600;}
  .brow.total-row{font-size:0.95rem;font-weight:700;border-top:2px solid #0a0a0a;padding-top:14px;margin-top:4px;border-bottom:none;}
  .savings-badge{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;text-align:center;margin-top:14px;font-size:0.88rem;color:#16a34a;font-weight:600;}
  @media(max-width:480px){.grid2{grid-template-columns:1fr;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">🧮 Task 22</div>
  <h1>Sales Calculator</h1>
  <p class="sub">Calculate sale price with discounts, taxes and profit margins.</p>

  <div class="card">
    <div class="sec-label">Product Details</div>
    <form method="POST">
      <div class="form-col">
        <div class="fg"><label>Product / Item Name</label>
          <input type="text" name="product" placeholder="e.g. Laptop, T-Shirt" value="<?= htmlspecialchars($data['product']??'') ?>"></div>
        <div class="grid2">
          <div class="fg"><label>Unit Price (₹)</label>
            <input type="number" name="price" placeholder="e.g. 5000" min="0" step="0.01" value="<?= htmlspecialchars($data['price']??'') ?>"></div>
          <div class="fg"><label>Quantity</label>
            <input type="number" name="qty" placeholder="1" min="1" value="<?= htmlspecialchars($data['qty']??'1') ?>"></div>
        </div>
        <div class="fg"><label>Discount</label>
          <div class="disc-row">
            <input type="number" name="discount" placeholder="e.g. 10" min="0" step="0.01" value="<?= htmlspecialchars($data['discount']??'0') ?>">
            <select name="dtype">
              <option value="percent" <?= (($data['dtype']??'percent')==='percent')?'selected':'' ?>>% Percent</option>
              <option value="flat" <?= (($data['dtype']??'')==='flat')?'selected':'' ?>>₹ Flat</option>
            </select>
          </div></div>
        <div class="fg"><label>Tax Rate (%)</label>
          <select name="tax">
            <option value="0"   <?= (($data['tax']??'')==='0')?'selected':'' ?>>0% — No Tax</option>
            <option value="5"   <?= (($data['tax']??'')==='5')?'selected':'' ?>>5% GST</option>
            <option value="12"  <?= (($data['tax']??'')==='12')?'selected':'' ?>>12% GST</option>
            <option value="18"  <?= (($data['tax']??'18')==='18')?'selected':'' ?>>18% GST</option>
            <option value="28"  <?= (($data['tax']??'')==='28')?'selected':'' ?>>28% GST</option>
          </select></div>
        <button type="submit" class="btn">Calculate →</button>
      </div>
    </form>
  </div>

  <?php if($result): ?>
  <div class="card">
    <div class="total-display">
      <div class="td-label">Total Payable</div>
      <div class="td-amount">₹<?= number_format($result['total'],2) ?></div>
      <div class="td-product"><?= htmlspecialchars($data['product'] ?: 'Product') ?> &nbsp;·&nbsp; Qty: <?= $result['qty'] ?></div>
    </div>
    <div class="sec-label">Price Breakdown</div>
    <div class="breakdown-rows">
      <div class="brow"><span class="lbl">Unit Price</span><span class="val">₹<?= number_format($result['price'],2) ?></span></div>
      <div class="brow"><span class="lbl">Quantity</span><span class="val">× <?= $result['qty'] ?></span></div>
      <div class="brow"><span class="lbl">Subtotal</span><span class="val">₹<?= number_format($result['subtotal'],2) ?></span></div>
      <div class="brow"><span class="lbl">Discount (<?= $data['dtype']==='percent' ? $result['disc_val'].'%' : '₹'.number_format($result['disc_val'],2) ?>)</span><span class="val" style="color:#dc2626;">- ₹<?= number_format($result['disc_amt'],2) ?></span></div>
      <div class="brow"><span class="lbl">After Discount</span><span class="val">₹<?= number_format($result['after_disc'],2) ?></span></div>
      <div class="brow"><span class="lbl">Tax (<?= $result['tax_rate'] ?>% GST)</span><span class="val">+ ₹<?= number_format($result['tax_amt'],2) ?></span></div>
      <div class="brow total-row"><span>Total Amount</span><span>₹<?= number_format($result['total'],2) ?></span></div>
    </div>
    <?php if($result['savings'] > 0): ?>
    <div class="savings-badge">🎉 You save ₹<?= number_format($result['savings'],2) ?> on this purchase!</div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
