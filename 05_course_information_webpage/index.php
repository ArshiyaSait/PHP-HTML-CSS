<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Course Information Web Page</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  /* Nav */
  nav{background:#fff;border-bottom:1px solid #e5e7eb;padding:16px 40px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:10;}
  .nav-brand{font-family:'Outfit',sans-serif;font-weight:800;font-size:1.1rem;letter-spacing:-0.3px;}
  .nav-links{display:flex;gap:24px;}
  .nav-links a{text-decoration:none;color:#6b7280;font-size:0.88rem;font-weight:500;transition:color 0.2s;}
  .nav-links a:hover{color:#0a0a0a;}
  /* Hero */
  .hero{background:#0a0a0a;color:#fff;padding:80px 40px;text-align:center;}
  .hero .eyebrow{font-size:11px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:#9ca3af;margin-bottom:16px;}
  .hero h1{font-family:'Outfit',sans-serif;font-size:3rem;font-weight:800;line-height:1.1;letter-spacing:-1px;max-width:600px;margin:0 auto;}
  .hero p{color:#9ca3af;margin-top:16px;font-size:1rem;max-width:480px;margin:16px auto 0;}
  .hero-stats{display:flex;justify-content:center;gap:48px;margin-top:40px;flex-wrap:wrap;}
  .hero-stat .n{font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;}
  .hero-stat .l{font-size:0.75rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-top:2px;}
  /* Courses */
  .section{max-width:1100px;margin:0 auto;padding:60px 20px;}
  .section-title{font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;margin-bottom:8px;letter-spacing:-0.3px;}
  .section-sub{color:#6b7280;font-size:0.9rem;margin-bottom:36px;}
  .courses-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;}
  .course-card{background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:28px;
    transition:box-shadow 0.2s,transform 0.2s;cursor:pointer;}
  .course-card:hover{box-shadow:0 8px 32px rgba(0,0,0,0.1);transform:translateY(-2px);}
  .course-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;margin-bottom:16px;}
  .course-tag{display:inline-block;padding:3px 10px;border-radius:50px;font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px;}
  .course-card h3{font-family:'Outfit',sans-serif;font-size:1.05rem;font-weight:700;margin-bottom:6px;}
  .course-card p{font-size:0.84rem;color:#6b7280;line-height:1.55;}
  .course-meta{display:flex;gap:16px;margin-top:16px;padding-top:16px;border-top:1px solid #f0f0f0;}
  .meta-item{font-size:0.78rem;color:#9ca3af;display:flex;align-items:center;gap:4px;}
  /* Filter */
  .filter-bar{display:flex;gap:10px;margin-bottom:30px;flex-wrap:wrap;}
  .filter-btn{padding:7px 18px;border:1.5px solid #e5e7eb;border-radius:50px;font-size:0.82rem;
    font-weight:600;cursor:pointer;background:#fff;transition:all 0.2s;font-family:'Inter',sans-serif;}
  .filter-btn.active,.filter-btn:hover{background:#0a0a0a;color:#fff;border-color:#0a0a0a;}
  /* Footer */
  footer{background:#0a0a0a;color:#9ca3af;text-align:center;padding:28px 20px;font-size:0.83rem;margin-top:40px;}
</style>
</head>
<body>
<nav>
  <div class="nav-brand">EduPortal</div>
  <div class="nav-links">
    <a href="#">Courses</a>
    <a href="#">Faculty</a>
    <a href="#">Admissions</a>
    <a href="#">Contact</a>
  </div>
</nav>

<div class="hero">
  <div class="eyebrow">📚 Task 05 — Course Catalog</div>
  <h1>Course Information Web Page</h1>
  <p>Explore our diverse range of academic programs designed to shape future professionals.</p>
  <div class="hero-stats">
    <div class="hero-stat"><div class="n">28+</div><div class="l">Courses</div></div>
    <div class="hero-stat"><div class="n">500+</div><div class="l">Students</div></div>
    <div class="hero-stat"><div class="n">40+</div><div class="l">Faculty</div></div>
    <div class="hero-stat"><div class="n">98%</div><div class="l">Placement</div></div>
  </div>
</div>

<div class="section">
  <div class="section-title">Available Courses</div>
  <div class="section-sub">Choose from a wide range of programs across multiple disciplines.</div>

  <div class="filter-bar">
    <button class="filter-btn active" onclick="filterCourse('all',this)">All Courses</button>
    <button class="filter-btn" onclick="filterCourse('ug',this)">Undergraduate</button>
    <button class="filter-btn" onclick="filterCourse('pg',this)">Postgraduate</button>
    <button class="filter-btn" onclick="filterCourse('cert',this)">Certificate</button>
  </div>

  <div class="courses-grid" id="coursesGrid">
    <?php
    $courses = [
      ['icon'=>'💻','color'=>'#eff6ff','tag-bg'=>'#dbeafe','tag-c'=>'#1d4ed8','tag'=>'UG','level'=>'ug','name'=>'B.Sc Computer Science','desc'=>'3-year program covering programming, algorithms, databases, AI and software engineering.','dur'=>'3 Years','fee'=>'₹45,000/yr','seats'=>60],
      ['icon'=>'🖥️','color'=>'#f0fdf4','tag-bg'=>'#dcfce7','tag-c'=>'#16a34a','tag'=>'UG','level'=>'ug','name'=>'BCA','desc'=>'Bachelor of Computer Applications — focused on software development and IT fundamentals.','dur'=>'3 Years','fee'=>'₹40,000/yr','seats'=>60],
      ['icon'=>'🧮','color'=>'#fefce8','tag-bg'=>'#fef9c3','tag-c'=>'#a16207','tag'=>'UG','level'=>'ug','name'=>'B.Sc Mathematics','desc'=>'Pure and applied mathematics, statistics and mathematical computing.','dur'=>'3 Years','fee'=>'₹35,000/yr','seats'=>50],
      ['icon'=>'💼','color'=>'#fff7ed','tag-bg'=>'#ffedd5','tag-c'=>'#c2410c','tag'=>'UG','level'=>'ug','name'=>'B.Com','desc'=>'Commerce fundamentals, accounting, business law, taxation and finance.','dur'=>'3 Years','fee'=>'₹30,000/yr','seats'=>80],
      ['icon'=>'🔬','color'=>'#fdf4ff','tag-bg'=>'#fae8ff','tag-c'=>'#7e22ce','tag'=>'PG','level'=>'pg','name'=>'MCA','desc'=>'Master of Computer Applications — advanced software engineering and system design.','dur'=>'2 Years','fee'=>'₹55,000/yr','seats'=>40],
      ['icon'=>'📊','color'=>'#f0f9ff','tag-bg'=>'#e0f2fe','tag-c'=>'#0369a1','tag'=>'PG','level'=>'pg','name'=>'MBA','desc'=>'Master of Business Administration with specializations in Finance, Marketing and HR.','dur'=>'2 Years','fee'=>'₹80,000/yr','seats'=>40],
      ['icon'=>'🌐','color'=>'#fef2f2','tag-bg'=>'#fee2e2','tag-c'=>'#b91c1c','tag'=>'Cert','level'=>'cert','name'=>'Web Development','desc'=>'Intensive certificate course in HTML, CSS, JavaScript, PHP and modern frameworks.','dur'=>'6 Months','fee'=>'₹15,000','seats'=>30],
      ['icon'=>'🤖','color'=>'#f8fafc','tag-bg'=>'#f1f5f9','tag-c'=>'#334155','tag'=>'Cert','level'=>'cert','name'=>'AI & Machine Learning','desc'=>'Practical certificate program in Python, ML algorithms, and deep learning.','dur'=>'6 Months','fee'=>'₹18,000','seats'=>25],
      ['icon'=>'🔒','color'=>'#f0fdf4','tag-bg'=>'#dcfce7','tag-c'=>'#15803d','tag'=>'Cert','level'=>'cert','name'=>'Cyber Security','desc'=>'Ethical hacking, network security, cryptography and digital forensics.','dur'=>'6 Months','fee'=>'₹16,000','seats'=>25],
    ];
    foreach($courses as $c): ?>
    <div class="course-card" data-level="<?= $c['level'] ?>">
      <div class="course-icon" style="background:<?= $c['color'] ?>"><?= $c['icon'] ?></div>
      <span class="course-tag" style="background:<?= $c['tag-bg'] ?>;color:<?= $c['tag-c'] ?>"><?= $c['tag'] ?></span>
      <h3><?= $c['name'] ?></h3>
      <p><?= $c['desc'] ?></p>
      <div class="course-meta">
        <span class="meta-item">⏱ <?= $c['dur'] ?></span>
        <span class="meta-item">💰 <?= $c['fee'] ?></span>
        <span class="meta-item">👥 <?= $c['seats'] ?> Seats</span>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<footer>
  <p>EduPortal &copy; <?= date('Y') ?> &nbsp;·&nbsp; Course Information Web Page &nbsp;·&nbsp; 24SBCS053</p>
</footer>

<script>
function filterCourse(level, btn) {
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.course-card').forEach(card => {
    card.style.display = (level === 'all' || card.dataset.level === level) ? '' : 'none';
  });
}
</script>
</body>
</html>
