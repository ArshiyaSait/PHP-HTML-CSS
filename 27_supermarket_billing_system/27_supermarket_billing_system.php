<?php
$cart    = [];
$total   = 0;
$receipt = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $products   = $_POST['products']  ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $prices     = $_POST['prices']     ?? [];
    $gst_rates  = $_POST['gst']        ?? [];
    $discount   = (float)($_POST['discount'] ?? 0);
    $payment    = trim($_POST['payment'] ?? '');
    $customer   = trim($_POST['customer'] ?? 'Walk-in Customer');

    foreach ($products as $i => $pname) {
        $pname = trim($pname);
        $qty   = (int)($quantities[$i] ?? 0);
        $price = (float)($prices[$i] ?? 0);
        $gst   = (float)($gst_rates[$i] ?? 0);
        if ($pname && $qty > 0 && $price > 0) {
            $subtotal   = $price * $qty;
            $gst_amt    = round($subtotal * $gst / 100, 2);
            $item_total = $subtotal + $gst_amt;
            $cart[] = compact('pname','qty','price','gst','subtotal','gst_amt','item_total');
            $total += $item_total;
        }
    }

    if (!empty($cart)) {
        $disc_amt  = round($total * $discount / 100, 2);
        $net       = round($total - $disc_amt, 2);
        $bill_no   = 'BILL-'.date('Ymd').'-'.rand(100,999);
        $receipt   = compact('cart','total','disc_amt','net','discount','payment','customer','bill_no');
    }
}
$catalog = [
    ['name'=>'Rice (5 kg)',       'price'=>250, 'gst'=>5 ],
    ['name'=>'Milk (1 L)',        'price'=>65,  'gst'=>0 ],
    ['name'=>'Cooking Oil (1 L)', 'price'=>180, 'gst'=>5 ],
    ['name'=>'Bread',             'price'=>45,  'gst'=>0 ],
    ['name'=>'Sugar (1 kg)',      'price'=>55,  'gst'=>5 ],
    ['name'=>'Coffee Powder',     'price'=>120, 'gst'=>5 ],
    ['name'=>'Biscuits (Pack)',   'price'=>40,  'gst'=>12],
    ['name'=>'Shampoo',           'price'=>210, 'gst'=>18],
    ['name'=>'Soap (Pack of 3)',  'price'=>95,  'gst'=>18],
    ['name'=>'Toothpaste',        'price'=>80,  'gst'=>12],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Supermarket Billing System</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:900px;margin:0 auto;padding:48px 20px 80px;}
  .chip{display:inline-flex;background:#0a0a0a;color:#fff;padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;}
  h1{font-family:'Outfit',sans-serif;font-size:2.3rem;font-weight:800;letter-spacing:-0.5px;line-height:1.15;}
  .sub{color:#6b7280;margin-top:8px;font-size:0.95rem;margin-bottom:36px;}
  .main-layout{display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:28px;box-shadow:0 8px 40px rgba(0,0,0,0.08);margin-bottom:16px;}
  .sec-label{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#9ca3af;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #e5e7eb;}
  /* Catalog */
  .catalog-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:8px;margin-bottom:16px;}
  .cat-item{border:1.5px solid #e5e7eb;border-radius:10px;padding:10px;cursor:pointer;transition:all 0.15s;background:#fafafa;}
  .cat-item:hover{border-color:#0a0a0a;background:#fff;}
  .cat-item.selected{border-color:#0a0a0a;background:#0a0a0a;color:#fff;}
  .cat-item .cn{font-size:0.84rem;font-weight:600;margin-bottom:3px;}
  .cat-item .cp{font-size:0.78rem;opacity:0.7;}
  /* Cart rows */
  .cart-item{display:grid;grid-template-columns:2fr 80px 90px auto;gap:8px;align-items:center;padding:10px 0;border-bottom:1px solid #f8f8f8;}
  .cart-item:last-child{border-bottom:none;}
  .ci-name{font-size:0.88rem;font-weight:600;}
  .ci-price{font-size:0.8rem;color:#6b7280;}
  .qty-input{background:#fafafa;border:1.5px solid #e5e7eb;border-radius:8px;padding:7px 10px;text-align:center;font-size:0.9rem;font-weight:600;width:100%;font-family:'Inter',sans-serif;color:#0a0a0a;}
  .qty-input:focus{outline:none;border-color:#0a0a0a;}
  .ci-total{font-family:'Outfit',sans-serif;font-size:0.95rem;font-weight:700;text-align:right;}
  .rm-btn{background:none;border:none;cursor:pointer;color:#9ca3af;font-size:1.1rem;padding:4px;transition:color 0.2s;}
  .rm-btn:hover{color:#dc2626;}
  /* Summary */
  .sum-row{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #f8f8f8;font-size:0.9rem;}
  .sum-row:last-child{border-bottom:none;}
  .sum-row .lbl{color:#6b7280;}
  .sum-row .val{font-weight:600;}
  .sum-total{font-size:1rem;font-weight:800;border-top:2px solid #0a0a0a;padding-top:12px;margin-top:4px;}
  .discount-input{display:flex;gap:8px;margin-bottom:14px;align-items:center;}
  .discount-input input{background:#fafafa;border:1.5px solid #e5e7eb;border-radius:8px;padding:9px 12px;width:80px;font-size:0.9rem;color:#0a0a0a;text-align:center;font-family:'Inter',sans-serif;}
  .discount-input input:focus{outline:none;border-color:#0a0a0a;}
  .discount-input label{font-size:0.82rem;font-weight:600;color:#6b7280;}
  .payment-sel{background:#fafafa;border:1.5px solid #e5e7eb;border-radius:8px;padding:10px 14px;width:100%;margin-bottom:14px;appearance:none;font-size:0.9rem;font-family:'Inter',sans-serif;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px;color:#0a0a0a;}
  .pay-btn{width:100%;padding:13px;background:#0a0a0a;color:#fff;border:none;border-radius:10px;font-size:0.96rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;transition:transform 0.15s,box-shadow 0.15s;}
  .pay-btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,0,0,0.18);}
  /* Receipt */
  .receipt-wrap{font-family:'JetBrains Mono',monospace;background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:28px;font-size:0.8rem;line-height:1.7;}
  .receipt-title{text-align:center;font-family:'Outfit',sans-serif;font-size:1.2rem;font-weight:800;margin-bottom:4px;}
  .receipt-sub{text-align:center;font-size:0.75rem;color:#6b7280;margin-bottom:16px;}
  .r-div{border-top:1px dashed #e5e7eb;margin:10px 0;}
  .r-row{display:flex;justify-content:space-between;}
  .r-total-row{display:flex;justify-content:space-between;font-size:0.9rem;font-weight:700;border-top:2px solid #0a0a0a;padding-top:8px;margin-top:4px;}
  .r-footer{text-align:center;margin-top:12px;font-size:0.72rem;color:#9ca3af;}
  .new-btn{display:block;width:100%;text-align:center;padding:11px;border:1.5px solid #0a0a0a;border-radius:10px;font-size:0.86rem;font-weight:600;cursor:pointer;background:transparent;margin-top:14px;font-family:'Outfit',sans-serif;transition:all 0.2s;}
  .new-btn:hover{background:#0a0a0a;color:#fff;}
  @media(max-width:720px){.main-layout{grid-template-columns:1fr;}.catalog-grid{grid-template-columns:repeat(2,1fr);}}.cart-item:has(.qty-input[value="0"]){display:none;}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">🛒 Task 27</div>
  <h1>Supermarket Billing<br>System</h1>
  <p class="sub">Select items, set quantities and generate a detailed billing receipt with GST.</p>

  <?php if($receipt): ?>
  <div style="max-width:480px;margin:0 auto;">
    <div class="receipt-wrap">
      <div class="receipt-title">🛒 SuperMart</div>
      <div class="receipt-sub">123 Main Street &nbsp;·&nbsp; Tel: 98765 43210</div>
      <div class="receipt-sub" style="margin-top:-10px;">Bill No: <?= $receipt['bill_no'] ?> &nbsp;·&nbsp; <?= date('d M Y h:i A') ?></div>
      <div class="r-div"></div>
      <div class="r-row" style="font-weight:700;font-size:0.75rem;color:#9ca3af;"><span>ITEM</span><span>QTY</span><span>AMOUNT</span></div>
      <div class="r-div"></div>
      <?php foreach($receipt['cart'] as $item): ?>
      <div class="r-row"><span><?= htmlspecialchars($item['pname']) ?></span><span>×<?= $item['qty'] ?></span><span>₹<?= number_format($item['item_total'],2) ?></span></div>
      <div style="font-size:0.7rem;color:#9ca3af;padding-left:2px;">@₹<?= $item['price'] ?> + GST <?= $item['gst'] ?>%</div>
      <?php endforeach; ?>
      <div class="r-div"></div>
      <div class="r-row"><span>Subtotal (before disc.)</span><span>₹<?= number_format($receipt['total'],2) ?></span></div>
      <?php if($receipt['disc_amt'] > 0): ?>
      <div class="r-row"><span>Discount (<?= $receipt['discount'] ?>%)</span><span>-₹<?= number_format($receipt['disc_amt'],2) ?></span></div>
      <?php endif; ?>
      <div class="r-row"><span>Payment Mode</span><span><?= htmlspecialchars($receipt['payment']) ?></span></div>
      <div class="r-div"></div>
      <div class="r-total-row"><span>TOTAL PAYABLE</span><span>₹<?= number_format($receipt['net'],2) ?></span></div>
      <div class="r-footer">Thank you for shopping at SuperMart!<br>Please visit again 😊</div>
    </div>
    <button class="new-btn" onclick="window.location='<?= $_SERVER['PHP_SELF'] ?>'">New Bill →</button>
  </div>
  <?php else: ?>
  <form method="POST" id="billForm">
    <div class="main-layout">
      <!-- Left: Products -->
      <div>
        <div class="card">
          <div class="sec-label">Quick Select Products</div>
          <div class="catalog-grid">
            <?php foreach($catalog as $i => $item): ?>
            <div class="cat-item" onclick="addToCart(<?= $i ?>,'<?= htmlspecialchars($item['name'],ENT_QUOTES) ?>',<?= $item['price'] ?>,<?= $item['gst'] ?>)">
              <div class="cn"><?= $item['name'] ?></div>
              <div class="cp">₹<?= $item['price'] ?> · GST <?= $item['gst'] ?>%</div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="card">
          <div class="sec-label">Cart Items</div>
          <div id="cartContainer">
            <p style="color:#9ca3af;font-size:0.88rem;">No items added yet. Select from above or add manually.</p>
          </div>
          <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;">
            <input type="text" id="customProduct" placeholder="Product name" style="flex:1;min-width:120px;background:#fafafa;border:1.5px solid #e5e7eb;border-radius:8px;padding:9px 12px;font-size:0.88rem;font-family:'Inter',sans-serif;color:#0a0a0a;" onfocus="this.style.borderColor='#0a0a0a'" onblur="this.style.borderColor='#e5e7eb'">
            <input type="number" id="customPrice" placeholder="Price" style="width:80px;background:#fafafa;border:1.5px solid #e5e7eb;border-radius:8px;padding:9px 10px;font-size:0.88rem;font-family:'Inter',sans-serif;color:#0a0a0a;" onfocus="this.style.borderColor='#0a0a0a'" onblur="this.style.borderColor='#e5e7eb'">
            <input type="number" id="customGst" placeholder="GST%" style="width:65px;background:#fafafa;border:1.5px solid #e5e7eb;border-radius:8px;padding:9px 10px;font-size:0.88rem;font-family:'Inter',sans-serif;color:#0a0a0a;" onfocus="this.style.borderColor='#0a0a0a'" onblur="this.style.borderColor='#e5e7eb'">
            <button type="button" onclick="addCustom()" style="padding:9px 18px;background:#0a0a0a;color:#fff;border:none;border-radius:8px;font-weight:600;font-size:0.84rem;cursor:pointer;font-family:'Outfit',sans-serif;">+ Add</button>
          </div>
        </div>
      </div>

      <!-- Right: Summary -->
      <div>
        <div class="card" style="position:sticky;top:20px;">
          <div class="sec-label">Bill Summary</div>
          <div id="cartSummary" style="color:#9ca3af;font-size:0.88rem;margin-bottom:14px;">Cart is empty</div>

          <div class="discount-input">
            <label>Discount:</label>
            <input type="number" name="discount" id="discountInput" placeholder="0" min="0" max="100" value="0" oninput="updateSummary()">
            <span style="font-size:0.82rem;color:#6b7280;">%</span>
          </div>

          <select name="payment" class="payment-sel" required>
            <option value="">Payment Method</option>
            <option>Cash</option><option>UPI / GPay</option><option>Credit Card</option><option>Debit Card</option><option>Net Banking</option>
          </select>
          <input type="text" name="customer" placeholder="Customer name (optional)" style="width:100%;background:#fafafa;border:1.5px solid #e5e7eb;border-radius:8px;padding:9px 12px;font-size:0.88rem;margin-bottom:14px;font-family:'Inter',sans-serif;color:#0a0a0a;">

          <div id="hiddenInputs"></div>
          <button type="submit" class="pay-btn" id="payBtn" disabled>Generate Bill →</button>
        </div>
      </div>
    </div>
  </form>
  <?php endif; ?>
</div>

<script>
let cartItems = [];

function addToCart(idx, name, price, gst) {
  const exists = cartItems.find(i => i.name === name);
  if (exists) { exists.qty++; }
  else { cartItems.push({ name, price, gst, qty: 1 }); }
  renderCart();
}

function addCustom() {
  const name  = document.getElementById('customProduct').value.trim();
  const price = parseFloat(document.getElementById('customPrice').value) || 0;
  const gst   = parseFloat(document.getElementById('customGst').value) || 0;
  if (!name || price <= 0) { alert('Enter a product name and price.'); return; }
  cartItems.push({ name, price, gst, qty: 1 });
  document.getElementById('customProduct').value = '';
  document.getElementById('customPrice').value = '';
  document.getElementById('customGst').value = '';
  renderCart();
}

function removeItem(idx) {
  cartItems.splice(idx, 1);
  renderCart();
}

function updateQty(idx, val) {
  cartItems[idx].qty = Math.max(0, parseInt(val) || 0);
  if (cartItems[idx].qty === 0) { cartItems.splice(idx, 1); }
  renderCart();
}

function renderCart() {
  const container = document.getElementById('cartContainer');
  if (cartItems.length === 0) {
    container.innerHTML = '<p style="color:#9ca3af;font-size:0.88rem;">No items added yet.</p>';
    updateSummary(); return;
  }
  let html = '';
  cartItems.forEach((item, i) => {
    const sub = item.price * item.qty;
    const gstAmt = sub * item.gst / 100;
    const tot = (sub + gstAmt).toFixed(2);
    html += `<div class="cart-item">
      <div><div class="ci-name">${item.name}</div><div class="ci-price">₹${item.price} + ${item.gst}% GST</div></div>
      <input class="qty-input" type="number" min="0" value="${item.qty}" onchange="updateQty(${i},this.value)">
      <div class="ci-total">₹${tot}</div>
      <button type="button" class="rm-btn" onclick="removeItem(${i})">✕</button>
    </div>`;
  });
  container.innerHTML = html;
  updateSummary();
}

function updateSummary() {
  let total = 0;
  cartItems.forEach(item => {
    const sub = item.price * item.qty;
    total += sub + sub * item.gst / 100;
  });
  const disc = parseFloat(document.getElementById('discountInput')?.value || 0);
  const discAmt = (total * disc / 100).toFixed(2);
  const net = (total - discAmt).toFixed(2);
  const sumEl = document.getElementById('cartSummary');

  if (cartItems.length === 0) {
    sumEl.innerHTML = '<p style="color:#9ca3af;font-size:0.88rem;">Cart is empty</p>';
    document.getElementById('payBtn').disabled = true;
    document.getElementById('hiddenInputs').innerHTML = '';
    return;
  }

  sumEl.innerHTML = `
    <div class="sum-row"><span class="lbl">${cartItems.length} item(s)</span><span class="val">₹${total.toFixed(2)}</span></div>
    ${disc > 0 ? `<div class="sum-row"><span class="lbl">Discount (${disc}%)</span><span class="val" style="color:#dc2626;">-₹${discAmt}</span></div>` : ''}
    <div class="sum-row sum-total"><span>Total Payable</span><span>₹${net}</span></div>
  `;

  // Populate hidden inputs for form submit
  let hidden = '';
  cartItems.forEach((item, i) => {
    hidden += `<input type="hidden" name="products[]" value="${item.name}">`;
    hidden += `<input type="hidden" name="quantities[]" value="${item.qty}">`;
    hidden += `<input type="hidden" name="prices[]" value="${item.price}">`;
    hidden += `<input type="hidden" name="gst[]" value="${item.gst}">`;
  });
  document.getElementById('hiddenInputs').innerHTML = hidden;
  document.getElementById('payBtn').disabled = false;
}
</script>
</body>
</html>
