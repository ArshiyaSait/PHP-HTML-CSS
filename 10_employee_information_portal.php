<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Employee Information Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  nav{background:#0a0a0a;color:#fff;padding:16px 40px;display:flex;align-items:center;justify-content:space-between;}
  .nav-brand{font-family:'Outfit',sans-serif;font-weight:800;font-size:1.1rem;}
  .nav-badge{background:rgba(255,255,255,0.1);padding:4px 12px;border-radius:50px;font-size:0.75rem;font-weight:600;}
  .hero{background:#fff;border-bottom:1px solid #e5e7eb;padding:40px;}
  .hero .chip{display:inline-flex;background:#f4f4f4;color:#6b7280;padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:12px;}
  .hero h1{font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;letter-spacing:-0.5px;}
  .hero p{color:#6b7280;margin-top:6px;font-size:0.9rem;}
  .main{max-width:1100px;margin:0 auto;padding:40px 20px 80px;}
  /* Search */
  .search-bar{display:flex;gap:10px;margin-bottom:30px;}
  .search-bar input{flex:1;background:#fff;border:1.5px solid #e5e7eb;border-radius:10px;padding:12px 16px;font-size:0.94rem;color:#0a0a0a;transition:all 0.2s;}
  .search-bar input:focus{outline:none;border-color:#0a0a0a;box-shadow:0 0 0 3px rgba(10,10,10,0.08);}
  .search-bar select{background:#fff;border:1.5px solid #e5e7eb;border-radius:10px;padding:12px 16px;font-size:0.88rem;color:#0a0a0a;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px;cursor:pointer;}
  .search-bar select:focus{outline:none;border-color:#0a0a0a;}
  /* Stats */
  .stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:30px;}
  .stat-card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:20px;text-align:center;}
  .stat-card .sv{font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;}
  .stat-card .sl{font-size:0.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-top:4px;}
  /* Employee cards */
  .emp-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;}
  .emp-card{background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:24px;transition:box-shadow 0.2s,transform 0.2s;}
  .emp-card:hover{box-shadow:0 8px 32px rgba(0,0,0,0.1);transform:translateY(-2px);}
  .emp-avatar{width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Outfit',sans-serif;font-size:1.2rem;font-weight:800;color:#fff;margin-bottom:14px;}
  .emp-name{font-family:'Outfit',sans-serif;font-size:1rem;font-weight:700;margin-bottom:2px;}
  .emp-role{font-size:0.82rem;color:#6b7280;margin-bottom:12px;}
  .emp-tag{display:inline-block;padding:3px 10px;border-radius:50px;font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;}
  .emp-details{display:flex;flex-direction:column;gap:5px;}
  .emp-detail{display:flex;gap:8px;font-size:0.8rem;color:#6b7280;}
  .emp-detail span:first-child{font-weight:600;color:#0a0a0a;min-width:50px;}
  .status-dot{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:4px;}
  .active{background:#16a34a;} .inactive{background:#dc2626;} .on-leave{background:#f59e0b;}
  footer{background:#0a0a0a;color:#9ca3af;text-align:center;padding:24px;font-size:0.82rem;}
  @media(max-width:700px){.stats-row{grid-template-columns:1fr 1fr;}.search-bar{flex-direction:column;}}
</style>
</head>
<body>
<nav>
  <div class="nav-brand">EmployeePortal</div>
  <div class="nav-badge">Task 10 — HR Module</div>
</nav>

<div class="hero">
  <div class="chip">👥 Information Portal</div>
  <h1>Employee Information Portal</h1>
  <p>Browse and search employee profiles across all departments.</p>
</div>

<div class="main">
  <div class="stats-row">
    <div class="stat-card"><div class="sv">12</div><div class="sl">Total Employees</div></div>
    <div class="stat-card"><div class="sv">9</div><div class="sl">Active</div></div>
    <div class="stat-card"><div class="sv">2</div><div class="sl">On Leave</div></div>
    <div class="stat-card"><div class="sv">5</div><div class="sl">Departments</div></div>
  </div>

  <div class="search-bar">
    <input type="text" id="searchInput" placeholder="🔍  Search by name, ID or role..." oninput="filterCards()">
    <select id="deptFilter" onchange="filterCards()">
      <option value="">All Departments</option>
      <option>Computer Science</option>
      <option>Human Resources</option>
      <option>Finance</option>
      <option>Marketing</option>
      <option>Operations</option>
    </select>
  </div>

  <div class="emp-grid" id="empGrid">
    <?php
    $colors = ['#0a0a0a','#1d4ed8','#16a34a','#9333ea','#dc2626','#f59e0b','#0891b2','#db2777'];
    $employees = [
      ['id'=>'EMP-001','name'=>'Ravi Kumar','role'=>'Senior Software Engineer','dept'=>'Computer Science','email'=>'ravi.kumar@company.com','phone'=>'9876543210','exp'=>'5 Years','status'=>'Active','join'=>'2019-06-15'],
      ['id'=>'EMP-002','name'=>'Priya Sharma','role'=>'HR Manager','dept'=>'Human Resources','email'=>'priya.sharma@company.com','phone'=>'9876543211','exp'=>'7 Years','status'=>'Active','join'=>'2017-03-10'],
      ['id'=>'EMP-003','name'=>'Arjun Patel','role'=>'Financial Analyst','dept'=>'Finance','email'=>'arjun.patel@company.com','phone'=>'9876543212','exp'=>'4 Years','status'=>'On Leave','join'=>'2020-08-22'],
      ['id'=>'EMP-004','name'=>'Meena Nair','role'=>'Marketing Lead','dept'=>'Marketing','email'=>'meena.nair@company.com','phone'=>'9876543213','exp'=>'6 Years','status'=>'Active','join'=>'2018-01-05'],
      ['id'=>'EMP-005','name'=>'Suresh Raj','role'=>'Operations Manager','dept'=>'Operations','email'=>'suresh.raj@company.com','phone'=>'9876543214','exp'=>'9 Years','status'=>'Active','join'=>'2015-11-20'],
      ['id'=>'EMP-006','name'=>'Divya Singh','role'=>'UI/UX Designer','dept'=>'Computer Science','email'=>'divya.singh@company.com','phone'=>'9876543215','exp'=>'3 Years','status'=>'Active','join'=>'2021-07-01'],
      ['id'=>'EMP-007','name'=>'Kiran Bose','role'=>'Accountant','dept'=>'Finance','email'=>'kiran.bose@company.com','phone'=>'9876543216','exp'=>'5 Years','status'=>'Active','join'=>'2019-02-14'],
      ['id'=>'EMP-008','name'=>'Anita Reddy','role'=>'Recruiter','dept'=>'Human Resources','email'=>'anita.reddy@company.com','phone'=>'9876543217','exp'=>'2 Years','status'=>'Active','join'=>'2022-09-30'],
      ['id'=>'EMP-009','name'=>'Mohan Das','role'=>'DevOps Engineer','dept'=>'Computer Science','email'=>'mohan.das@company.com','phone'=>'9876543218','exp'=>'4 Years','status'=>'On Leave','join'=>'2020-04-18'],
      ['id'=>'EMP-010','name'=>'Latha Venkat','role'=>'Digital Marketer','dept'=>'Marketing','email'=>'latha.venkat@company.com','phone'=>'9876543219','exp'=>'3 Years','status'=>'Active','join'=>'2021-12-01'],
      ['id'=>'EMP-011','name'=>'Raj Mohan','role'=>'QA Engineer','dept'=>'Computer Science','email'=>'raj.mohan@company.com','phone'=>'9876543220','exp'=>'2 Years','status'=>'Active','join'=>'2022-03-15'],
      ['id'=>'EMP-012','name'=>'Sita Devi','role'=>'Supply Chain Analyst','dept'=>'Operations','email'=>'sita.devi@company.com','phone'=>'9876543221','exp'=>'6 Years','status'=>'Active','join'=>'2018-07-22'],
    ];
    foreach ($employees as $i => $e):
      $initials = implode('', array_map(fn($w) => strtoupper($w[0]), explode(' ', $e['name'])));
      $color    = $colors[$i % count($colors)];
      $stCls    = $e['status']==='Active'?'active':($e['status']==='On Leave'?'on-leave':'inactive');
      $tagBg    = $e['status']==='Active'?'#f0fdf4':($e['status']==='On Leave'?'#fffbeb':'#fef2f2');
      $tagC     = $e['status']==='Active'?'#16a34a':($e['status']==='On Leave'?'#d97706':'#dc2626');
    ?>
    <div class="emp-card" data-name="<?= strtolower($e['name']) ?>" data-dept="<?= $e['dept'] ?>" data-id="<?= strtolower($e['id']) ?>" data-role="<?= strtolower($e['role']) ?>">
      <div class="emp-avatar" style="background:<?= $color ?>"><?= $initials ?></div>
      <div class="emp-name"><?= $e['name'] ?></div>
      <div class="emp-role"><?= $e['role'] ?></div>
      <span class="emp-tag" style="background:<?= $tagBg ?>;color:<?= $tagC ?>">
        <span class="status-dot <?= $stCls ?>"></span><?= $e['status'] ?>
      </span>
      <div class="emp-details">
        <div class="emp-detail"><span>ID</span><?= $e['id'] ?></div>
        <div class="emp-detail"><span>Dept</span><?= $e['dept'] ?></div>
        <div class="emp-detail"><span>Exp</span><?= $e['exp'] ?></div>
        <div class="emp-detail"><span>Email</span><?= $e['email'] ?></div>
        <div class="emp-detail"><span>Phone</span><?= $e['phone'] ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<footer>EmployeePortal &copy; <?= date('Y') ?> &nbsp;·&nbsp; Employee Information Portal &nbsp;·&nbsp; 24SBCS053</footer>

<script>
function filterCards() {
  const q    = document.getElementById('searchInput').value.toLowerCase();
  const dept = document.getElementById('deptFilter').value;
  document.querySelectorAll('.emp-card').forEach(card => {
    const matchQ    = !q || card.dataset.name.includes(q) || card.dataset.id.includes(q) || card.dataset.role.includes(q);
    const matchDept = !dept || card.dataset.dept === dept;
    card.style.display = (matchQ && matchDept) ? '' : 'none';
  });
}
</script>
</body>
</html>
