<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Profile Web Page</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  /* Nav */
  nav{background:#0a0a0a;padding:16px 40px;display:flex;align-items:center;justify-content:space-between;}
  .nav-brand{font-family:'Outfit',sans-serif;font-weight:800;font-size:1.1rem;color:#fff;}
  .nav-tag{background:rgba(255,255,255,0.1);color:#9ca3af;padding:4px 12px;border-radius:50px;font-size:0.75rem;font-weight:600;}
  /* Cover */
  .cover{height:220px;background:#f0f0f0;border-bottom:1px solid #e5e7eb;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;}
  .cover-pattern{position:absolute;inset:0;background:repeating-linear-gradient(45deg,#e5e7eb 0,#e5e7eb 1px,transparent 0,transparent 50%) #f8f8f8;background-size:20px 20px;}
  .cover-text{position:relative;font-family:'Outfit',sans-serif;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:3px;color:#9ca3af;}
  /* Profile */
  .profile-wrap{max-width:900px;margin:0 auto;padding:0 30px 60px;}
  .profile-top{display:flex;align-items:flex-end;gap:24px;margin-top:-48px;margin-bottom:28px;flex-wrap:wrap;}
  .avatar{width:100px;height:100px;border-radius:20px;background:#0a0a0a;border:4px solid #fff;display:flex;align-items:center;justify-content:center;font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;color:#fff;flex-shrink:0;}
  .profile-info{padding-bottom:4px;}
  .profile-name{font-family:'Outfit',sans-serif;font-size:1.6rem;font-weight:800;line-height:1.2;}
  .profile-sub{color:#6b7280;font-size:0.88rem;margin-top:4px;}
  .profile-tags{display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;}
  .ptag{background:#f4f4f4;border:1px solid #e5e7eb;padding:3px 10px;border-radius:50px;font-size:0.72rem;font-weight:600;color:#6b7280;}
  /* Main grid */
  .main-grid{display:grid;grid-template-columns:1fr 2fr;gap:20px;}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:24px;margin-bottom:16px;}
  .sec-label{font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#9ca3af;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid #f0f0f0;}
  .info-list{display:flex;flex-direction:column;gap:10px;}
  .info-item{display:flex;flex-direction:column;gap:2px;}
  .info-item .il{font-size:0.7rem;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;}
  .info-item .iv{font-size:0.88rem;font-weight:600;}
  /* Skills */
  .skill-item{margin-bottom:12px;}
  .skill-header{display:flex;justify-content:space-between;margin-bottom:5px;font-size:0.84rem;}
  .skill-name{font-weight:600;}
  .skill-pct{font-weight:700;font-family:'Outfit',sans-serif;}
  .skill-bar{height:6px;background:#f0f0f0;border-radius:50px;overflow:hidden;}
  .skill-fill{height:100%;background:#0a0a0a;border-radius:50px;}
  /* GPA display */
  .gpa-box{text-align:center;padding:20px;}
  .gpa-num{font-family:'Outfit',sans-serif;font-size:3.5rem;font-weight:800;line-height:1;}
  .gpa-lbl{font-size:0.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-top:4px;}
  /* Courses */
  .course-item{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f8f8f8;font-size:0.85rem;}
  .course-item:last-child{border-bottom:none;}
  .course-name{font-weight:600;}
  .course-grade{font-family:'Outfit',sans-serif;font-weight:800;font-size:1rem;}
  /* Achievements */
  .ach-item{display:flex;align-items:flex-start;gap:12px;padding:10px 0;border-bottom:1px solid #f8f8f8;}
  .ach-item:last-child{border-bottom:none;}
  .ach-icon{font-size:1.3rem;margin-top:2px;}
  .ach-name{font-size:0.87rem;font-weight:600;}
  .ach-desc{font-size:0.76rem;color:#9ca3af;margin-top:2px;}
  @media(max-width:680px){.main-grid{grid-template-columns:1fr;}.profile-top{margin-top:-36px;}}
</style>
</head>
<body>
<nav>
  <div class="nav-brand">StudentPortal</div>
  <div class="nav-tag">Task 24 — Profile</div>
</nav>

<div class="cover">
  <div class="cover-pattern"></div>
  <div class="cover-text">B.Sc Computer Science · 2022–2025</div>
</div>

<div class="profile-wrap">
  <div class="profile-top">
    <div class="avatar">MS</div>
    <div class="profile-info">
      <div class="profile-name">Maheesa Shafrin M S</div>
      <div class="profile-sub">Roll No: 24SBCS053 &nbsp;·&nbsp; B.Sc Computer Science &nbsp;·&nbsp; Semester VI</div>
      <div class="profile-tags">
        <span class="ptag">Computer Science</span>
        <span class="ptag">Active Student</span>
        <span class="ptag">2022 Batch</span>
      </div>
    </div>
  </div>

  <div class="main-grid">
    <!-- Left column -->
    <div>
      <div class="card">
        <div class="sec-label">Personal Info</div>
        <div class="info-list">
          <div class="info-item"><div class="il">Full Name</div><div class="iv">Maheesa Shafrin M S</div></div>
          <div class="info-item"><div class="il">Roll Number</div><div class="iv">24SBCS053</div></div>
          <div class="info-item"><div class="il">Date of Birth</div><div class="iv">15 March 2004</div></div>
          <div class="info-item"><div class="il">Gender</div><div class="iv">Female</div></div>
          <div class="info-item"><div class="il">Blood Group</div><div class="iv">B+</div></div>
          <div class="info-item"><div class="il">Phone</div><div class="iv">+91 98765 43210</div></div>
          <div class="info-item"><div class="il">Email</div><div class="iv">maheesa@example.com</div></div>
          <div class="info-item"><div class="il">City</div><div class="iv">Chennai, Tamil Nadu</div></div>
        </div>
      </div>

      <div class="card">
        <div class="gpa-box">
          <div class="gpa-num">8.7</div>
          <div class="gpa-lbl">Current CGPA</div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;text-align:center;margin-top:8px;">
          <div style="background:#f4f4f4;border-radius:8px;padding:10px;">
            <div style="font-family:'Outfit',sans-serif;font-size:1.2rem;font-weight:800;">95%</div>
            <div style="font-size:0.68rem;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;margin-top:2px;">Attendance</div>
          </div>
          <div style="background:#f4f4f4;border-radius:8px;padding:10px;">
            <div style="font-family:'Outfit',sans-serif;font-size:1.2rem;font-weight:800;">VI</div>
            <div style="font-size:0.68rem;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;margin-top:2px;">Semester</div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="sec-label">Technical Skills</div>
        <?php
        $skills = ['PHP'=>85,'HTML/CSS'=>92,'JavaScript'=>78,'Python'=>70,'MySQL'=>80,'Java'=>65];
        foreach($skills as $s => $p): ?>
        <div class="skill-item">
          <div class="skill-header"><span class="skill-name"><?= $s ?></span><span class="skill-pct"><?= $p ?>%</span></div>
          <div class="skill-bar"><div class="skill-fill" style="width:<?= $p ?>%"></div></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Right column -->
    <div>
      <div class="card">
        <div class="sec-label">About</div>
        <p style="font-size:0.9rem;line-height:1.7;color:#374151;">A dedicated and enthusiastic Computer Science student with a strong foundation in web development technologies including HTML, CSS, PHP and JavaScript. Passionate about building practical solutions and continuously learning new technologies. Currently working on a comprehensive web development project as part of the curriculum.</p>
      </div>

      <div class="card">
        <div class="sec-label">Current Semester Courses</div>
        <?php
        $courses = [
          ['name'=>'Web Technologies','grade'=>'A+','credits'=>4,'marks'=>'94/100'],
          ['name'=>'Database Management Systems','grade'=>'A','credits'=>4,'marks'=>'88/100'],
          ['name'=>'Software Engineering','grade'=>'A+','credits'=>3,'marks'=>'91/100'],
          ['name'=>'Computer Networks','grade'=>'B+','credits'=>3,'marks'=>'82/100'],
          ['name'=>'Operating Systems','grade'=>'A','credits'=>4,'marks'=>'85/100'],
          ['name'=>'Project Work','grade'=>'A+','credits'=>6,'marks'=>'96/100'],
        ];
        foreach($courses as $c): ?>
        <div class="course-item">
          <div>
            <div class="course-name"><?= $c['name'] ?></div>
            <div style="font-size:0.74px;color:#9ca3af;margin-top:1px;font-size:0.74rem;"><?= $c['credits'] ?> Credits &nbsp;·&nbsp; <?= $c['marks'] ?></div>
          </div>
          <div class="course-grade"><?= $c['grade'] ?></div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="card">
        <div class="sec-label">Achievements & Activities</div>
        <?php
        $achievements = [
          ['icon'=>'🏆','name'=>'Best Project Award','desc'=>'Awarded for outstanding web development project — 2024'],
          ['icon'=>'🥇','name'=>'Department Rank 1','desc'=>'Top performer in Semester IV examination'],
          ['icon'=>'💻','name'=>'Hackathon Participant','desc'=>'Participated in National Level Coding Hackathon 2024'],
          ['icon'=>'📚','name'=>'Library Volunteer','desc'=>'Active volunteer at college library — 2023 to present'],
          ['icon'=>'🎤','name'=>'Technical Seminar Speaker','desc'=>'Presented on "Modern Web Technologies" at college symposium'],
        ];
        foreach($achievements as $a): ?>
        <div class="ach-item">
          <div class="ach-icon"><?= $a['icon'] ?></div>
          <div><div class="ach-name"><?= $a['name'] ?></div><div class="ach-desc"><?= $a['desc'] ?></div></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>
