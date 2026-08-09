<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PHP Tasks — 24SBCS053</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  nav{background:#0a0a0a;padding:18px 40px;display:flex;align-items:center;justify-content:space-between;}
  .nav-brand{font-family:'Outfit',sans-serif;font-weight:800;font-size:1rem;color:#fff;}
  .nav-tag{background:rgba(255,255,255,0.1);color:#9ca3af;padding:4px 12px;border-radius:50px;font-size:0.75rem;font-weight:600;}
  .hero{background:#fff;border-bottom:1px solid #e5e7eb;padding:48px 40px;}
  .hero .chip{display:inline-flex;background:#f4f4f4;color:#6b7280;padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:14px;}
  .hero h1{font-family:'Outfit',sans-serif;font-size:2.4rem;font-weight:800;letter-spacing:-0.5px;}
  .hero p{color:#6b7280;margin-top:8px;font-size:0.95rem;}
  .hero-stats{display:flex;gap:28px;margin-top:20px;flex-wrap:wrap;}
  .hs{text-align:left;}
  .hs .val{font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;line-height:1;}
  .hs .lbl{font-size:0.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-top:3px;}
  .main{max-width:1100px;margin:0 auto;padding:40px 20px 80px;}
  .search-row{display:flex;gap:10px;margin-bottom:28px;}
  .search-row input{flex:1;background:#fff;border:1.5px solid #e5e7eb;border-radius:10px;padding:12px 16px;font-size:0.94rem;color:#0a0a0a;transition:all 0.2s;}
  .search-row input:focus{outline:none;border-color:#0a0a0a;box-shadow:0 0 0 3px rgba(10,10,10,0.08);}
  .task-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px;}
  .task-card{background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:22px;text-decoration:none;color:#0a0a0a;display:block;transition:all 0.2s;position:relative;overflow:hidden;}
  .task-card::before{content:'';position:absolute;top:0;left:0;width:3px;height:100%;background:#0a0a0a;}
  .task-card:hover{box-shadow:0 8px 32px rgba(0,0,0,0.1);transform:translateY(-2px);}
  .task-num{font-family:'Outfit',sans-serif;font-size:0.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;}
  .task-name{font-family:'Outfit',sans-serif;font-size:1rem;font-weight:700;margin-bottom:6px;line-height:1.3;}
  .task-desc{font-size:0.78rem;color:#6b7280;line-height:1.5;}
  .task-icon{font-size:1.4rem;margin-bottom:10px;}
  .task-arrow{position:absolute;bottom:20px;right:20px;color:#9ca3af;font-size:1rem;transition:all 0.2s;}
  .task-card:hover .task-arrow{color:#0a0a0a;transform:translateX(3px);}
  footer{background:#0a0a0a;color:#9ca3af;text-align:center;padding:24px;font-size:0.82rem;}
  @media(max-width:600px){.hero{padding:32px 20px;}.hero h1{font-size:1.8rem;}nav{padding:14px 20px;}}
</style>
</head>
<body>
<nav>
  <div class="nav-brand">PHP Web Tasks</div>
  <div class="nav-tag">24SBCS053 · MAHEESA SHAFRIN M S</div>
</nav>

<div class="hero">
  <div class="chip">🎓 PHP Assignment Portfolio</div>
  <h1>28 PHP Web Tasks</h1>
  <p>B.Sc Computer Science · HTML + CSS + PHP · White/Black Premium Interface</p>
  <div class="hero-stats">
    <div class="hs"><div class="val">28</div><div class="lbl">Total Tasks</div></div>
    <div class="hs"><div class="val">HTML</div><div class="lbl">Markup</div></div>
    <div class="hs"><div class="val">CSS</div><div class="lbl">Styling</div></div>
    <div class="hs"><div class="val">PHP</div><div class="lbl">Backend</div></div>
  </div>
</div>

<div class="main">
  <div class="search-row">
    <input type="text" id="searchInput" placeholder="🔍  Search tasks..." oninput="filterTasks()">
  </div>

  <div class="task-grid" id="taskGrid">
    <?php
    $tasks = [
      ['01','01_admission_application_system/','🎓','Admission Application System','Multi-step application form with eligibility validation and status display.'],
      ['02','02_applicant_validation_system/','✅','Applicant Validation System','Validates applicant data including age, marks, and eligibility criteria.'],
      ['03','03_attendance_processing_system/','📅','Attendance Processing System','Calculates attendance percentage with SVG visual progress indicators.'],
      ['04','04_bmi_calculator/','⚖️','BMI Calculator','Computes Body Mass Index with health category display and visual scale.'],
      ['05','05_course_information_webpage/','📚','Course Information Webpage','Filterable course catalog with categories, duration and fee details.'],
      ['06','06_course_registration_system/','📝','Course Registration System','Multi-select course enrollment with credit limit validation.'],
      ['07','07_customer_registration_system/','👤','Customer Registration System','Customer profile form with full validation and confirmation receipt.'],
      ['08','08_electricity_bill_calculator/','⚡','Electricity Bill Calculator','Slab-based electricity billing with GST and consumption breakdown.'],
      ['09','09_employee_email_id_generator/','📧','Employee Email ID Generator','Generates 5 email format variations from employee name and department.'],
      ['10','10_employee_information_portal/','👥','Employee Information Portal','Browseable employee directory with search and department filter.'],
      ['11','11_employee_performance_evaluation/','⭐','Employee Performance Evaluation','Weighted multi-criteria performance scoring with grade output.'],
      ['12','12_employee_salary_processing/','💰','Employee Salary Processing','Payslip generator with earnings, deductions, PF, ESI and net pay.'],
      ['13','13_examination_result_analysis/','📝','Examination Result Analysis','Multi-subject result analyser with grade, pass/fail and bar charts.'],
      ['14','14_insurance_premium_calculator/','🛡️','Insurance Premium Calculator','Risk-based premium calculation for 5 insurance types with term options.'],
      ['15','15_library_membership_registration/','📚','Library Membership Registration','Library membership enrollment with plan selection and fee receipt.'],
      ['16','16_mobile_bill_generator/','📱','Mobile Bill Generator','Monthly mobile bill with usage charges, GST and plan-based free limits.'],
      ['17','17_online_banking_login/','🏦','Online Banking Login System','Session-based secure banking login with 3-attempt lockout and dashboard.'],
      ['18','18_parent_teacher_meeting_registration/','🤝','Parent–Teacher Meeting Registration','PTM slot booking with teacher selection, mode and agenda details.'],
      ['19','19_password_generator/','🔐','Password Generator','Secure password generator with strength meter and copy-to-clipboard.'],
      ['20','20_patient_registration_system/','🏥','Patient Registration System','Hospital patient registration with ward, doctor and patient ID generation.'],
      ['21','21_restaurant_menu_webpage/','🍽️','Restaurant Menu Webpage','Filterable restaurant menu with categories, prices and add-to-cart.'],
      ['22','22_sales_calculator/','🧮','Sales Calculator','Sales price calculator with discount, GST and detailed breakdown.'],
      ['23','23_string_analysis_system/','🔤','String Analysis System','Deep text analyser: characters, words, vowels, palindrome, frequency.'],
      ['24','24_student_profile_webpage/','🎓','Student Profile Webpage','Comprehensive student profile with GPA, skills, courses and achievements.'],
      ['25','25_student_registration_form/','📋','Student Registration Form','Full student enrollment form with academic, personal and guardian info.'],
      ['26','26_student_result_processing/','📊','Student Result Processing','SGPA calculator with grade-point system and detailed marksheet.'],
      ['27','27_supermarket_billing_system/','🛒','Supermarket Billing System','Interactive cart-based billing with GST and printable receipt.'],
      ['28','28_travel_package_booking/','✈️','Travel Package Booking','Package tour booking with accommodation, transport and cost breakdown.'],
    ];
    foreach ($tasks as $t) {
      echo '<a class="task-card" href="'.$t[1].'" data-name="'.strtolower($t[2].' '.$t[3].' '.$t[4]).'">';
      echo '<div class="task-icon">'.$t[2].'</div>';
      echo '<div class="task-num">Task '.$t[0].'</div>';
      echo '<div class="task-name">'.$t[3].'</div>';
      echo '<div class="task-desc">'.$t[4].'</div>';
      echo '<div class="task-arrow">→</div>';
      echo '</a>';
    }
    ?>
  </div>
</div>

<footer>24SBCS053 &nbsp;·&nbsp; MAHEESA SHAFRIN M S &nbsp;·&nbsp; B.Sc Computer Science &nbsp;·&nbsp; PHP Web Tasks Portfolio</footer>

<script>
function filterTasks() {
  const q = document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('.task-card').forEach(card => {
    card.style.display = !q || card.dataset.name.includes(q) ? '' : 'none';
  });
}
</script>
</body>
</html>
