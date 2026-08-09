<?php
$result = null;
$data   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['text'] = $_POST['text'] ?? '';

    if (!empty(trim($data['text']))) {
        $str  = $data['text'];
        $len  = strlen($str);
        $words= str_word_count($str);
        $chars_no_space = strlen(str_replace(' ', '', $str));
        $sentences = preg_match_all('/[.!?]+/', $str, $m);
        $paragraphs= count(array_filter(explode("\n", $str)));
        $upper = preg_match_all('/[A-Z]/', $str);
        $lower = preg_match_all('/[a-z]/', $str);
        $digits= preg_match_all('/[0-9]/', $str);
        $spaces= preg_match_all('/\s/', $str);
        $specials = $len - $upper - $lower - $digits - $spaces;
        $vowels = preg_match_all('/[aeiouAEIOU]/', $str);
        $consonants = preg_match_all('/[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]/', $str);
        $reversed = strrev($str);
        $palindrome = (strtolower(preg_replace('/\s+/','',$str)) === strtolower(preg_replace('/\s+/','',strrev($str))));
        $unique_chars = count(array_unique(str_split(strtolower($str))));

        // Word frequency
        $word_arr = str_word_count(strtolower($str), 1);
        $freq = array_count_values($word_arr);
        arsort($freq);
        $top5 = array_slice($freq, 0, 5, true);

        $result = compact('len','words','chars_no_space','sentences','paragraphs','upper','lower','digits','spaces','specials','vowels','consonants','reversed','palindrome','unique_chars','top5');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>String Analysis System</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
  body{background:#f4f4f4;font-family:'Inter',sans-serif;color:#0a0a0a;min-height:100vh;}
  .wrap{max-width:780px;margin:0 auto;padding:48px 20px 80px;}
  .chip{display:inline-flex;background:#0a0a0a;color:#fff;padding:5px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;}
  h1{font-family:'Outfit',sans-serif;font-size:2.3rem;font-weight:800;letter-spacing:-0.5px;line-height:1.15;}
  .sub{color:#6b7280;margin-top:8px;font-size:0.95rem;margin-bottom:36px;}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:36px;box-shadow:0 8px 40px rgba(0,0,0,0.08);margin-bottom:20px;}
  .sec-label{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#9ca3af;margin-bottom:18px;padding-bottom:8px;border-bottom:1px solid #e5e7eb;}
  label{font-size:0.77rem;font-weight:600;letter-spacing:0.4px;text-transform:uppercase;display:block;margin-bottom:6px;}
  textarea{background:#fafafa;border:1.5px solid #e5e7eb;border-radius:12px;padding:16px;color:#0a0a0a;font-size:0.94rem;font-family:'Inter',sans-serif;width:100%;min-height:140px;resize:vertical;transition:all 0.2s;line-height:1.6;}
  textarea:focus{outline:none;border-color:#0a0a0a;background:#fff;box-shadow:0 0 0 3px rgba(10,10,10,0.08);}
  .btn{width:100%;padding:14px;background:#0a0a0a;color:#fff;border:none;border-radius:10px;font-size:0.96rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;margin-top:14px;transition:transform 0.15s,box-shadow 0.15s;}
  .btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,0,0,0.18);}
  /* Stats grid */
  .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;}
  .stat-box{background:#fafafa;border:1px solid #e5e7eb;border-radius:12px;padding:16px;text-align:center;}
  .stat-box .sv{font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;}
  .stat-box .sl{font-size:0.7rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-top:4px;}
  .detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px;}
  .det-row{display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#fafafa;border:1px solid #e5e7eb;border-radius:10px;font-size:0.87rem;}
  .det-row .lbl{color:#6b7280;}
  .det-row .val{font-weight:700;font-family:'Outfit',sans-serif;}
  .reversed-box{background:#0a0a0a;color:#fff;border-radius:12px;padding:16px;margin-bottom:14px;}
  .reversed-box .rl{font-size:0.7rem;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;}
  .reversed-box .rv{font-family:'JetBrains Mono',monospace;font-size:0.9rem;word-break:break-all;line-height:1.6;}
  .pal-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 16px;border-radius:50px;font-size:0.82rem;font-weight:700;margin-bottom:14px;}
  .pal-yes{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;}
  .pal-no{background:#fef2f2;color:#dc2626;border:1px solid #fecaca;}
  .top-words{display:flex;flex-wrap:wrap;gap:8px;}
  .word-badge{background:#fafafa;border:1px solid #e5e7eb;border-radius:8px;padding:6px 12px;font-size:0.82rem;font-weight:600;display:flex;align-items:center;gap:6px;}
  .word-cnt{background:#0a0a0a;color:#fff;border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:700;}
  @media(max-width:580px){.stats-grid{grid-template-columns:repeat(2,1fr);}.detail-grid{grid-template-columns:1fr;}.card{padding:22px;}}
</style>
</head>
<body>
<div class="wrap">
  <div class="chip">🔤 Task 23</div>
  <h1>String Analysis System</h1>
  <p class="sub">Analyse any text string for detailed character, word and linguistic statistics.</p>

  <div class="card">
    <form method="POST">
      <label>Enter Text to Analyse</label>
      <textarea name="text" placeholder="Type or paste your text here..."><?= htmlspecialchars($data['text']??'') ?></textarea>
      <button type="submit" class="btn">Analyse Text →</button>
    </form>
  </div>

  <?php if($result): ?>
  <div class="card">
    <div class="sec-label">Overview Statistics</div>
    <div class="stats-grid">
      <div class="stat-box"><div class="sv"><?= $result['len'] ?></div><div class="sl">Characters</div></div>
      <div class="stat-box"><div class="sv"><?= $result['words'] ?></div><div class="sl">Words</div></div>
      <div class="stat-box"><div class="sv"><?= $result['sentences'] ?></div><div class="sl">Sentences</div></div>
      <div class="stat-box"><div class="sv"><?= $result['paragraphs'] ?></div><div class="sl">Paragraphs</div></div>
    </div>

    <div class="sec-label">Character Breakdown</div>
    <div class="detail-grid">
      <div class="det-row"><span class="lbl">Uppercase Letters</span><span class="val"><?= $result['upper'] ?></span></div>
      <div class="det-row"><span class="lbl">Lowercase Letters</span><span class="val"><?= $result['lower'] ?></span></div>
      <div class="det-row"><span class="lbl">Vowels</span><span class="val"><?= $result['vowels'] ?></span></div>
      <div class="det-row"><span class="lbl">Consonants</span><span class="val"><?= $result['consonants'] ?></span></div>
      <div class="det-row"><span class="lbl">Digits</span><span class="val"><?= $result['digits'] ?></span></div>
      <div class="det-row"><span class="lbl">Spaces</span><span class="val"><?= $result['spaces'] ?></span></div>
      <div class="det-row"><span class="lbl">Special Characters</span><span class="val"><?= $result['specials'] ?></span></div>
      <div class="det-row"><span class="lbl">Chars (no spaces)</span><span class="val"><?= $result['chars_no_space'] ?></span></div>
      <div class="det-row"><span class="lbl">Unique Characters</span><span class="val"><?= $result['unique_chars'] ?></span></div>
    </div>

    <div class="pal-badge <?= $result['palindrome']?'pal-yes':'pal-no' ?>">
      <?= $result['palindrome'] ? '✓ Palindrome' : '✗ Not a Palindrome' ?>
    </div>

    <div class="reversed-box">
      <div class="rl">Reversed String</div>
      <div class="rv"><?= htmlspecialchars($result['reversed']) ?></div>
    </div>

    <?php if(!empty($result['top5'])): ?>
    <div class="sec-label">Top 5 Most Frequent Words</div>
    <div class="top-words">
      <?php foreach($result['top5'] as $w => $cnt): ?>
      <div class="word-badge"><?= htmlspecialchars($w) ?><span class="word-cnt"><?= $cnt ?></span></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
