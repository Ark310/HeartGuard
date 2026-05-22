<?php
session_start();

$contentFile = __DIR__ . '/data/content.json';
$rawContent  = file_get_contents($contentFile);
$content     = json_decode($rawContent, true);

function esc($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function c($section, $key, $default = '') {
    global $content;
    return esc($content[$section][$key] ?? $default);
}

function sectionVisible($section) {
    global $content;
    return ($content[$section]['visible'] ?? true) ? '' : 'style="display:none"';
}

function sectionBg($section) {
    global $content;
    $bg = $content[$section]['bg_color'] ?? '#0a0a0a';
    return esc($bg);
}

function sectionAttrs($section) {
    global $content;
    $bg      = esc($content[$section]['bg_color'] ?? '#0a0a0a');
    $hidden  = ($content[$section]['visible'] ?? true) ? 'false' : 'true';
    return "data-section=\"{$section}\" style=\"background-color:{$bg}\" data-bg-color=\"{$bg}\" data-hidden=\"{$hidden}\"";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="HeartGuard — The smart watch built for heart disease patients and athletes. Continuous heart monitoring, nutrition tracking, and emergency response.">
  <title>HeartGuard — Your Heart. Protected.</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Admin Bar -->
<div class="admin-bar" id="admin-bar">
  <span class="admin-bar-label">ADMIN MODE — EDITING</span>
  <div class="admin-bar-actions">
    <button class="admin-btn" id="admin-save">Save All</button>
    <button class="admin-btn" id="admin-logout">Logout</button>
  </div>
</div>

<!-- Login Modal -->
<div class="modal-overlay" id="login-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
  <div class="modal-box">
    <p class="modal-title" id="modal-title">Admin Access</p>
    <p class="modal-subtitle">Enter your password to edit this page.</p>
    <form id="login-form">
      <input class="modal-input" type="password" id="admin-password" placeholder="Password" autocomplete="current-password" required>
      <p class="modal-error" id="login-error" role="alert"></p>
      <button class="btn-primary" type="submit" style="width:100%">Sign In</button>
    </form>
  </div>
</div>

<!-- Toast -->
<div class="admin-toast" id="admin-toast" role="status"></div>

<!-- Navbar -->
<nav class="navbar" id="navbar">
  <a class="nav-brand" href="#hero">
    <img src="<?= c('navbar','logo','assets/images/logo-placeholder.svg') ?>"
         alt="<?= c('navbar','site_name','HeartGuard') ?> logo"
         data-image-key="navbar.logo">
    <span data-editable="text" data-key="site_name" data-section-ref="navbar">
      <?= c('navbar','site_name','HeartGuard') ?>
    </span>
  </a>

  <ul class="nav-links" id="nav-links">
    <li><a href="#features">Features</a></li>
    <li><a href="#how-it-works">How It Works</a></li>
    <li><a href="#who-its-for">Who It's For</a></li>
    <li><a href="#story">Story</a></li>
    <li><a href="#specs">Specs</a></li>
    <li><a href="#contact">Contact</a></li>
  </ul>

  <div class="nav-right">
    <button class="admin-lock" id="admin-lock" title="Admin access" aria-label="Admin login">🔒</button>
    <button class="hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- Hero -->
<section id="hero" <?= sectionAttrs('hero') ?>>
  <div class="section-toolbar" role="toolbar">
    <button class="toolbar-btn toolbar-image" data-image-target="hero.watch_image" title="Swap watch image">🖼</button>
    <input class="toolbar-color" type="color" value="<?= sectionBg('hero') ?>" title="Background color">
    <button class="toolbar-btn toolbar-visibility" title="Toggle visibility">👁</button>
  </div>
  <div class="hero-glow"></div>
  <div class="hero-content container">
    <h1 class="hero-headline" data-editable="text" data-key="headline">
      <?= c('hero','headline','Your Heart. Protected.') ?>
    </h1>
    <p class="hero-subheadline" data-editable="text" data-key="subheadline">
      <?= c('hero','subheadline') ?>
    </p>
    <a href="#features" class="btn-primary" data-editable="text" data-key="cta_text">
      <?= c('hero','cta_text','Learn More') ?>
    </a>
    <div class="hero-image-wrap">
      <img class="hero-watch"
           src="<?= c('hero','watch_image','assets/images/watch-placeholder.svg') ?>"
           alt="HeartGuard smart watch"
           data-image-key="hero.watch_image">
    </div>
  </div>
</section>

<!-- Features -->
<section id="features" class="section-pad" <?= sectionAttrs('features') ?>>
  <div class="section-toolbar" role="toolbar">
    <input class="toolbar-color" type="color" value="<?= sectionBg('features') ?>" title="Background color">
    <button class="toolbar-btn toolbar-visibility" title="Toggle visibility">👁</button>
  </div>
  <div class="container">
    <div class="section-header fade-up">
      <p class="section-label">What It Does</p>
      <h2 class="section-title" data-editable="text" data-key="section_title">
        <?= c('features','section_title','Features') ?>
      </h2>
    </div>
    <div class="features-grid">
      <?php
      $cards = $content['features']['cards'] ?? [];
      $cardClasses = ['', '', 'emergency'];
      foreach ($cards as $i => $card):
        $cls = $cardClasses[$i] ?? '';
      ?>
      <div class="feature-card fade-up" style="transition-delay:<?= $i * 0.15 ?>s">
        <div class="card-icon-wrap <?= esc($cls) ?>">
          <img class="card-icon"
               src="<?= esc($card['icon'] ?? 'assets/images/icon-heart.svg') ?>"
               alt="<?= esc($card['title'] ?? '') ?>"
               data-image-key="features.cards.<?= $i ?>.icon">
        </div>
        <h3 class="card-title" data-editable="text" data-key="cards.<?= $i ?>.title">
          <?= esc($card['title'] ?? '') ?>
        </h3>
        <p class="card-description" data-editable="text" data-key="cards.<?= $i ?>.description">
          <?= esc($card['description'] ?? '') ?>
        </p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- How It Works -->
<section id="how-it-works" class="section-pad" <?= sectionAttrs('how_it_works') ?>>
  <div class="section-toolbar" role="toolbar">
    <input class="toolbar-color" type="color" value="<?= sectionBg('how_it_works') ?>" title="Background color">
    <button class="toolbar-btn toolbar-visibility" title="Toggle visibility">👁</button>
  </div>
  <div class="container">
    <div class="section-header fade-up">
      <p class="section-label">The Process</p>
      <h2 class="section-title" data-editable="text" data-key="section_title">
        <?= c('how_it_works','section_title','How It Works') ?>
      </h2>
    </div>
    <div class="steps-list">
      <?php foreach ($content['how_it_works']['steps'] ?? [] as $i => $step): ?>
      <div class="step-item fade-up">
        <div class="step-content">
          <p class="step-number"><?= esc($step['number'] ?? sprintf('%02d', $i + 1)) ?></p>
          <h3 class="step-title" data-editable="text" data-key="steps.<?= $i ?>.title">
            <?= esc($step['title'] ?? '') ?>
          </h3>
          <p class="step-description" data-editable="text" data-key="steps.<?= $i ?>.description">
            <?= esc($step['description'] ?? '') ?>
          </p>
        </div>
        <div class="step-image-wrap">
          <img src="<?= esc($step['image'] ?? 'assets/images/step-placeholder.svg') ?>"
               alt="<?= esc($step['title'] ?? '') ?>"
               data-image-key="how_it_works.steps.<?= $i ?>.image">
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Who It's For -->
<section id="who-its-for" class="section-pad" <?= sectionAttrs('who_its_for') ?>>
  <div class="section-toolbar" role="toolbar">
    <input class="toolbar-color" type="color" value="<?= sectionBg('who_its_for') ?>" title="Background color">
    <button class="toolbar-btn toolbar-visibility" title="Toggle visibility">👁</button>
  </div>
  <div class="container">
    <div class="section-header fade-up">
      <p class="section-label">Built For You</p>
      <h2 class="section-title" data-editable="text" data-key="section_title">
        <?= c('who_its_for','section_title',"Who It's For") ?>
      </h2>
    </div>
    <div class="for-grid">
      <?php foreach (['left','right'] as $side): ?>
      <div class="for-card fade-up">
        <img class="for-card-image"
             src="<?= esc($content['who_its_for'][$side]['image'] ?? 'assets/images/person-placeholder.svg') ?>"
             alt="<?= esc($content['who_its_for'][$side]['title'] ?? '') ?>"
             data-image-key="who_its_for.<?= $side ?>.image">
        <div class="for-card-body">
          <h3 class="for-card-title" data-editable="text" data-key="<?= $side ?>.title">
            <?= esc($content['who_its_for'][$side]['title'] ?? '') ?>
          </h3>
          <p class="for-card-description" data-editable="text" data-key="<?= $side ?>.description">
            <?= esc($content['who_its_for'][$side]['description'] ?? '') ?>
          </p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Story -->
<section id="story" class="section-pad" <?= sectionAttrs('story') ?>>
  <div class="section-toolbar" role="toolbar">
    <input class="toolbar-color" type="color" value="<?= sectionBg('story') ?>" title="Background color">
    <button class="toolbar-btn toolbar-visibility" title="Toggle visibility">👁</button>
  </div>
  <div class="container">
    <div class="section-header fade-up">
      <p class="section-label">Our Origin</p>
      <h2 class="section-title" data-editable="text" data-key="section_title">
        <?= c('story','section_title','Why HeartGuard Exists') ?>
      </h2>
    </div>
    <div class="story-content">
      <p class="story-narrative fade-up" data-editable="text" data-key="narrative">
        <?= c('story','narrative') ?>
      </p>
      <div class="story-quote fade-up">
        <blockquote data-editable="text" data-key="quote">
          "<?= c('story','quote') ?>"
        </blockquote>
        <cite data-editable="text" data-key="quote_author">
          — <?= c('story','quote_author') ?>
        </cite>
      </div>
    </div>
  </div>
</section>

<!-- Specs -->
<section id="specs" class="section-pad" <?= sectionAttrs('specs') ?>>
  <div class="section-toolbar" role="toolbar">
    <input class="toolbar-color" type="color" value="<?= sectionBg('specs') ?>" title="Background color">
    <button class="toolbar-btn toolbar-visibility" title="Toggle visibility">👁</button>
  </div>
  <div class="container">
    <div class="section-header fade-up">
      <p class="section-label">Under The Hood</p>
      <h2 class="section-title" data-editable="text" data-key="section_title">
        <?= c('specs','section_title','Technical Specs') ?>
      </h2>
    </div>
    <div class="specs-grid fade-up">
      <?php foreach ($content['specs']['rows'] ?? [] as $i => $row): ?>
      <div class="spec-row">
        <span class="spec-label" data-editable="text" data-key="rows.<?= $i ?>.label">
          <?= esc($row['label'] ?? '') ?>
        </span>
        <span class="spec-value" data-editable="text" data-key="rows.<?= $i ?>.value">
          <?= esc($row['value'] ?? '') ?>
        </span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Contact -->
<section id="contact" class="section-pad" <?= sectionAttrs('contact') ?>>
  <div class="section-toolbar" role="toolbar">
    <input class="toolbar-color" type="color" value="<?= sectionBg('contact') ?>" title="Background color">
    <button class="toolbar-btn toolbar-visibility" title="Toggle visibility">👁</button>
  </div>
  <div class="container">
    <div class="contact-inner fade-up">
      <h2 class="contact-title" data-editable="text" data-key="section_title">
        <?= c('contact','section_title','Get In Touch') ?>
      </h2>
      <p class="contact-description" data-editable="text" data-key="description">
        <?= c('contact','description') ?>
      </p>
      <a class="btn-primary"
         href="mailto:<?= c('contact','email','hello@heartguard.com') ?>"
         data-editable="text"
         data-key="cta_text"
         id="contact-cta">
        <?= c('contact','cta_text','Contact Us') ?>
      </a>
    </div>
  </div>
</section>

<!-- Footer -->
<footer <?= sectionAttrs('footer') ?>>
  <div class="section-toolbar" role="toolbar">
    <input class="toolbar-color" type="color" value="<?= sectionBg('footer') ?>" title="Background color">
    <button class="toolbar-btn toolbar-visibility" title="Toggle visibility">👁</button>
  </div>
  <div class="footer-inner">
    <div class="footer-brand">
      <img src="<?= c('footer','logo','assets/images/logo-placeholder.svg') ?>"
           alt="<?= c('footer','site_name','HeartGuard') ?>"
           data-image-key="footer.logo">
      <span data-editable="text" data-key="site_name"><?= c('footer','site_name','HeartGuard') ?></span>
    </div>
    <p class="footer-copy" data-editable="text" data-key="copyright">
      <?= c('footer','copyright','© 2026 HeartGuard. All rights reserved.') ?>
    </p>
    <div class="footer-socials">
      <?php if (!empty($content['footer']['social_twitter'])): ?>
        <a href="<?= c('footer','social_twitter') ?>" target="_blank" rel="noopener">Twitter</a>
      <?php endif; ?>
      <?php if (!empty($content['footer']['social_instagram'])): ?>
        <a href="<?= c('footer','social_instagram') ?>" target="_blank" rel="noopener">Instagram</a>
      <?php endif; ?>
      <?php if (!empty($content['footer']['social_linkedin'])): ?>
        <a href="<?= c('footer','social_linkedin') ?>" target="_blank" rel="noopener">LinkedIn</a>
      <?php endif; ?>
    </div>
  </div>
</footer>

<script src="assets/js/main.js"></script>
<script src="assets/js/admin.js"></script>
</body>
</html>
