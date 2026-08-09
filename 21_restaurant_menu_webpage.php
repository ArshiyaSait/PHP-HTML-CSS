<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Restaurant Menu Web Page</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  /* Nav */
  nav{background:#fff;border-bottom:1px solid #e5e7eb;padding:16px 40px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:10;}
  .nav-brand{font-family:'Outfit',sans-serif;font-weight:800;font-size:1.2rem;letter-spacing:-0.3px;}
  .nav-links{display:flex;gap:20px;}
  .nav-links a{text-decoration:none;color:#6b7280;font-size:0.88rem;font-weight:500;transition:color 0.2s;}
  .nav-links a:hover{color:#0a0a0a;}
  /* Hero */
  .hero{background:#0a0a0a;color:#fff;padding:70px 40px;text-align:center;}
  .hero .eyebrow{font-size:11px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:#9ca3af;margin-bottom:14px;}
  .hero h1{font-family:'Outfit',sans-serif;font-size:2.8rem;font-weight:800;line-height:1.1;letter-spacing:-1px;max-width:500px;margin:0 auto;}
  .hero p{color:#9ca3af;margin-top:14px;font-size:0.95rem;max-width:400px;margin:14px auto 0;}
  .hero-badges{display:flex;justify-content:center;gap:10px;margin-top:24px;flex-wrap:wrap;}
  .hb{background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);padding:6px 14px;border-radius:50px;font-size:0.78rem;font-weight:600;}
  /* Main */
  .main{max-width:1100px;margin:0 auto;padding:50px 20px 80px;}
  /* Category filter */
  .filter-bar{display:flex;gap:10px;margin-bottom:30px;flex-wrap:wrap;}
  .filter-btn{padding:8px 20px;border:1.5px solid #e5e7eb;border-radius:50px;font-size:0.82rem;font-weight:600;cursor:pointer;background:#fff;transition:all 0.2s;font-family:'Inter',sans-serif;}
  .filter-btn.active,.filter-btn:hover{background:#0a0a0a;color:#fff;border-color:#0a0a0a;}
  /* Section */
  .menu-section{margin-bottom:40px;}
  .menu-section-title{font-family:'Outfit',sans-serif;font-size:1.3rem;font-weight:800;margin-bottom:16px;display:flex;align-items:center;gap:10px;}
  .menu-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;}
  /* Item card */
  .menu-item{background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;transition:all 0.2s;}
  .menu-item:hover{box-shadow:0 8px 32px rgba(0,0,0,0.1);transform:translateY(-2px);}
  .item-img{width:100%;height:140px;object-fit:cover;background:#f8f8f8;display:flex;align-items:center;justify-content:center;font-size:3.5rem;}
  .item-body{padding:16px;}
  .item-tags{display:flex;gap:6px;margin-bottom:8px;flex-wrap:wrap;}
  .tag{padding:2px 8px;border-radius:50px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;}
  .veg{background:#dcfce7;color:#16a34a;}
  .non-veg{background:#fee2e2;color:#dc2626;}
  .spicy{background:#fef9c3;color:#a16207;}
  .bestseller{background:#e0e7ff;color:#4338ca;}
  .item-name{font-family:'Outfit',sans-serif;font-size:1rem;font-weight:700;margin-bottom:4px;}
  .item-desc{font-size:0.78rem;color:#6b7280;line-height:1.45;margin-bottom:12px;}
  .item-footer{display:flex;justify-content:space-between;align-items:center;}
  .item-price{font-family:'Outfit',sans-serif;font-size:1.1rem;font-weight:800;}
  .add-btn{padding:6px 14px;background:#0a0a0a;color:#fff;border:none;border-radius:8px;font-size:0.78rem;font-weight:600;cursor:pointer;font-family:'Outfit',sans-serif;transition:background 0.2s;}
  .add-btn:hover{background:#333;}
  /* Footer */
  footer{background:#0a0a0a;color:#9ca3af;text-align:center;padding:24px;font-size:0.82rem;}
  @media(max-width:600px){.hero{padding:40px 20px;}.hero h1{font-size:2rem;}nav{padding:14px 20px;}.nav-links{gap:12px;}}
</style>
</head>
<body>
<nav>
  <div class="nav-brand">🍽 La Maison</div>
  <div class="nav-links">
    <a href="#">Menu</a>
    <a href="#">About</a>
    <a href="#">Reservations</a>
    <a href="#">Contact</a>
  </div>
</nav>

<div class="hero">
  <div class="eyebrow">📋 Task 21 — Restaurant Menu</div>
  <h1>Fresh Food, Bold Flavours</h1>
  <p>Explore our handcrafted dishes made with the finest local ingredients.</p>
  <div class="hero-badges">
    <span class="hb">🕐 Open 10AM – 11PM</span>
    <span class="hb">📍 123, MG Road</span>
    <span class="hb">📞 +91 98765 43210</span>
  </div>
</div>

<div class="main">
  <div class="filter-bar">
    <button class="filter-btn active" onclick="filterMenu('all',this)">All Items</button>
    <button class="filter-btn" onclick="filterMenu('starter',this)">Starters</button>
    <button class="filter-btn" onclick="filterMenu('main',this)">Main Course</button>
    <button class="filter-btn" onclick="filterMenu('dessert',this)">Desserts</button>
    <button class="filter-btn" onclick="filterMenu('beverages',this)">Beverages</button>
  </div>

  <?php
  $menu = [
    'Starters' => [
      ['icon'=>'🥗','name'=>'Garden Fresh Salad','desc'=>'Crisp vegetables, cherry tomatoes, feta & balsamic dressing','price'=>180,'tags'=>['veg'],'cat'=>'starter'],
      ['icon'=>'🍗','name'=>'Crispy Chicken Wings','desc'=>'6 wings tossed in spicy buffalo sauce, served with dip','price'=>280,'tags'=>['non-veg','spicy','bestseller'],'cat'=>'starter'],
      ['icon'=>'🧀','name'=>'Cheese Garlic Bread','desc'=>'Toasted ciabatta with herb butter and melted mozzarella','price'=>160,'tags'=>['veg','bestseller'],'cat'=>'starter'],
      ['icon'=>'🍢','name'=>'Paneer Tikka','desc'=>'Cottage cheese marinated in spices, grilled to perfection','price'=>220,'tags'=>['veg','spicy'],'cat'=>'starter'],
    ],
    'Main Course' => [
      ['icon'=>'🍛','name'=>'Butter Chicken','desc'=>'Tender chicken in a rich, creamy tomato-based curry','price'=>320,'tags'=>['non-veg','bestseller'],'cat'=>'main'],
      ['icon'=>'🫓','name'=>'Paneer Tikka Masala','desc'=>'Grilled paneer cubes simmered in a tangy spiced gravy','price'=>280,'tags'=>['veg','bestseller'],'cat'=>'main'],
      ['icon'=>'🍝','name'=>'Spaghetti Carbonara','desc'=>'Al dente pasta with pancetta, eggs, parmesan & black pepper','price'=>350,'tags'=>['non-veg'],'cat'=>'main'],
      ['icon'=>'🌮','name'=>'Veg Burrito Bowl','desc'=>'Seasoned rice, black beans, salsa, guacamole & sour cream','price'=>250,'tags'=>['veg'],'cat'=>'main'],
      ['icon'=>'🍖','name'=>'Mutton Biryani','desc'=>'Slow-cooked mutton with fragrant basmati rice & saffron','price'=>420,'tags'=>['non-veg','spicy','bestseller'],'cat'=>'main'],
      ['icon'=>'🍕','name'=>'Margherita Pizza','desc'=>'San Marzano tomatoes, fresh mozzarella, basil on thin crust','price'=>310,'tags'=>['veg'],'cat'=>'main'],
    ],
    'Desserts' => [
      ['icon'=>'🍰','name'=>'Chocolate Lava Cake','desc'=>'Warm dark chocolate cake with a molten centre, served with ice cream','price'=>200,'tags'=>['veg','bestseller'],'cat'=>'dessert'],
      ['icon'=>'🍮','name'=>'Classic Crème Brûlée','desc'=>'Silky vanilla custard with a perfectly caramelised sugar top','price'=>220,'tags'=>['veg'],'cat'=>'dessert'],
      ['icon'=>'🍨','name'=>'Mango Kulfi','desc'=>'Traditional Indian ice cream infused with Alphonso mango pulp','price'=>120,'tags'=>['veg'],'cat'=>'dessert'],
    ],
    'Beverages' => [
      ['icon'=>'☕','name'=>'Signature Cold Coffee','desc'=>'Espresso blended with milk, ice cream & chocolate drizzle','price'=>140,'tags'=>['veg','bestseller'],'cat'=>'beverages'],
      ['icon'=>'🍋','name'=>'Virgin Mojito','desc'=>'Fresh lime, mint, sugar syrup & sparkling water on ice','price'=>120,'tags'=>['veg'],'cat'=>'beverages'],
      ['icon'=>'🥤','name'=>'Mango Lassi','desc'=>'Chilled yoghurt blended with fresh Alphonso mango','price'=>110,'tags'=>['veg'],'cat'=>'beverages'],
      ['icon'=>'🫖','name'=>'Masala Chai','desc'=>'Aromatic spiced tea brewed with fresh ginger and cardamom','price'=>60,'tags'=>['veg'],'cat'=>'beverages'],
    ],
  ];
  foreach ($menu as $section => $items):
  ?>
  <div class="menu-section" data-section="<?= strtolower(str_replace(' ','-',$section)) ?>">
    <div class="menu-section-title">
      <?= $section === 'Starters' ? '🥗' : ($section === 'Main Course' ? '🍛' : ($section === 'Desserts' ? '🍰' : '🥤')) ?>
      <?= $section ?>
    </div>
    <div class="menu-grid">
      <?php foreach($items as $item): ?>
      <div class="menu-item" data-cat="<?= $item['cat'] ?>">
        <div class="item-img"><?= $item['icon'] ?></div>
        <div class="item-body">
          <div class="item-tags">
            <?php foreach($item['tags'] as $tag): ?>
            <span class="tag <?= $tag ?>"><?= ucfirst($tag) ?></span>
            <?php endforeach; ?>
          </div>
          <div class="item-name"><?= $item['name'] ?></div>
          <div class="item-desc"><?= $item['desc'] ?></div>
          <div class="item-footer">
            <span class="item-price">₹<?= $item['price'] ?></span>
            <button class="add-btn" onclick="addToCart(this,'<?= htmlspecialchars($item['name']) ?>')">+ Add</button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<footer>La Maison Restaurant &copy; <?= date('Y') ?> &nbsp;·&nbsp; Restaurant Menu Web Page &nbsp;·&nbsp; 24SBCS053</footer>

<script>
function filterMenu(cat, btn) {
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  if (cat === 'all') {
    document.querySelectorAll('.menu-section').forEach(s => s.style.display = '');
  } else {
    document.querySelectorAll('.menu-section').forEach(s => {
      s.style.display = s.dataset.section === cat ? '' : 'none';
    });
  }
}
function addToCart(btn, name) {
  btn.textContent = '✓ Added';
  btn.style.background = '#16a34a';
  setTimeout(() => { btn.textContent = '+ Add'; btn.style.background = ''; }, 1500);
}
</script>
</body>
</html>
