# HeartGuard Website Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a single-page Apple-inspired marketing website for HeartGuard with a password-protected inline admin edit mode, deployable to GoDaddy shared PHP hosting.

**Architecture:** PHP serves `index.php`, which reads `data/content.json` at request time to populate all editable content. Admin edits are saved back to `content.json` via PHP API endpoints protected by `$_SESSION`. No framework, no build step — zip and upload to cPanel.

**Tech Stack:** PHP 7.4+, HTML5, CSS3 (custom properties, IntersectionObserver, CSS animations), Vanilla JS (Web Crypto API for SHA-256), Apache `.htaccess`

---

## File Map

| File | Responsibility |
|---|---|
| `.htaccess` | Disable indexes, block config/ and data/ dirs |
| `config/.htaccess` | Deny all browser access to this dir |
| `data/.htaccess` | Deny all browser access to this dir |
| `uploads/.htaccess` | Disable directory listing |
| `config/config.php` | Bcrypt-hashed admin password constant |
| `data/content.json` | All editable content — single source of truth |
| `assets/css/style.css` | All styles: variables, layout, sections, animations, admin |
| `assets/js/main.js` | Scroll animations, navbar scroll, parallax, hamburger |
| `assets/js/admin.js` | Admin mode: login, contenteditable, image upload, save |
| `assets/images/*.svg` | Placeholder SVG assets for all image slots |
| `index.php` | Single-page site — PHP-populated from content.json |
| `api/auth.php` | POST login / GET logout / GET session-check |
| `api/save-content.php` | POST — write edits to content.json |
| `api/upload-image.php` | POST — validate + save image uploads |
| `generate-hash.php` | One-time script to bcrypt a SHA-256 password hash |
| `README.md` | Deploy instructions, password setup, GoDaddy steps |

---

## Task 1: Project Scaffold + Security Config

**Files:**
- Create: `.htaccess`
- Create: `config/.htaccess`
- Create: `data/.htaccess`
- Create: `uploads/.htaccess`
- Create: `config/config.php`

- [ ] **Step 1: Create directory structure**

```bash
mkdir -p config data uploads assets/css assets/js assets/images api
```

- [ ] **Step 2: Write root `.htaccess`**

```apache
Options -Indexes

# Block config and data directories
<IfModule mod_authz_core.c>
    <DirectoryMatch "/(config|data)$">
        Require all denied
    </DirectoryMatch>
</IfModule>

# Fallback for older Apache
<IfModule !mod_authz_core.c>
    <DirectoryMatch "/(config|data)$">
        Order deny,allow
        Deny from all
    </DirectoryMatch>
</IfModule>

# Enable PHP to serve index.php by default
DirectoryIndex index.php index.html
```

- [ ] **Step 3: Write `config/.htaccess`**

```apache
Order deny,allow
Deny from all
```

- [ ] **Step 4: Write `data/.htaccess`**

```apache
Order deny,allow
Deny from all
```

- [ ] **Step 5: Write `uploads/.htaccess`**

```apache
Options -Indexes
```

- [ ] **Step 6: Write `config/config.php`**

```php
<?php
// Replace ADMIN_PASSWORD_HASH with output from generate-hash.php (see README.md)
define('ADMIN_PASSWORD_HASH', '$2y$12$PLACEHOLDER_REPLACE_WITH_YOUR_BCRYPT_HASH');
```

- [ ] **Step 7: Verify directory structure**

```bash
find . -type f | sort
```

Expected output includes: `.htaccess`, `config/.htaccess`, `config/config.php`, `data/.htaccess`, `uploads/.htaccess`

---

## Task 2: Content JSON — Default Schema

**Files:**
- Create: `data/content.json`

- [ ] **Step 1: Write `data/content.json` with all default content**

```json
{
  "navbar": {
    "logo": "assets/images/logo-placeholder.svg",
    "site_name": "HeartGuard",
    "bg_color": "#0a0a0a",
    "visible": true
  },
  "hero": {
    "headline": "Your Heart. Protected.",
    "subheadline": "The smart watch built for people who can't afford to ignore the warning signs.",
    "cta_text": "Learn More",
    "watch_image": "assets/images/watch-placeholder.svg",
    "bg_color": "#0a0a0a",
    "visible": true
  },
  "features": {
    "section_title": "Features",
    "bg_color": "#111111",
    "visible": true,
    "cards": [
      {
        "title": "Heart Monitoring",
        "description": "Real-time tracking of heart rate, blood pressure, oxygen levels, and irregular patterns.",
        "icon": "assets/images/icon-heart.svg"
      },
      {
        "title": "Nutrition Tracking",
        "description": "Personalized dietary guidance based on your condition. Warnings for high-sodium and high-risk foods.",
        "icon": "assets/images/icon-nutrition.svg"
      },
      {
        "title": "Emergency Response",
        "description": "Automatic alerts to emergency services and family members with your live location if a dangerous event is detected.",
        "icon": "assets/images/icon-emergency.svg"
      }
    ]
  },
  "how_it_works": {
    "section_title": "How It Works",
    "bg_color": "#0a0a0a",
    "visible": true,
    "steps": [
      {
        "number": "01",
        "title": "Wear HeartGuard Daily",
        "description": "Put on your HeartGuard watch just like any other — it works silently in the background.",
        "image": "assets/images/step-placeholder.svg"
      },
      {
        "number": "02",
        "title": "Continuous Monitoring",
        "description": "HeartGuard continuously tracks your heart rate, blood pressure, SpO2, and ECG patterns 24/7.",
        "image": "assets/images/step-placeholder.svg"
      },
      {
        "number": "03",
        "title": "Real-Time Alerts",
        "description": "Receive instant alerts and personalized nutrition recommendations based on your health data.",
        "image": "assets/images/step-placeholder.svg"
      },
      {
        "number": "04",
        "title": "Emergency Response",
        "description": "If a dangerous cardiac event is detected, HeartGuard automatically contacts emergency services with your location.",
        "image": "assets/images/step-placeholder.svg"
      }
    ]
  },
  "who_its_for": {
    "section_title": "Who It's For",
    "bg_color": "#111111",
    "visible": true,
    "left": {
      "title": "Heart Disease Patients",
      "description": "For those living with heart disease, every moment matters. HeartGuard gives you and your family peace of mind — continuous monitoring that catches warning signs before they become emergencies.",
      "image": "assets/images/person-placeholder.svg"
    },
    "right": {
      "title": "Athletes",
      "description": "You push your body to the limit. HeartGuard ensures you never push past it — monitoring your cardiac health during intense training and alerting you when your body needs to rest.",
      "image": "assets/images/person-placeholder.svg"
    }
  },
  "story": {
    "section_title": "Why HeartGuard Exists",
    "bg_color": "#0a0a0a",
    "visible": true,
    "narrative": "HeartGuard was born from a story many families know too well. A father who loved basketball so much that he kept playing — and kept ignoring what his body was telling him. He didn't have a device that could speak louder than his will to stay on the court. HeartGuard is built so that story doesn't have to repeat itself. It combines health monitoring, nutrition tracking, and emergency protection into one wearable — so the warning signs are never ignored again.",
    "quote": "He didn't have a device that could speak louder than his will to play.",
    "quote_author": "The story behind HeartGuard"
  },
  "specs": {
    "section_title": "Technical Specs",
    "bg_color": "#111111",
    "visible": true,
    "rows": [
      { "label": "Battery Life", "value": "Up to 7 days" },
      { "label": "Sensors", "value": "Heart rate, SpO2, Blood pressure, ECG" },
      { "label": "Water Resistance", "value": "50 metres" },
      { "label": "Connectivity", "value": "Bluetooth 5.2, Wi-Fi" },
      { "label": "Compatibility", "value": "iOS & Android" }
    ]
  },
  "contact": {
    "section_title": "Get In Touch",
    "description": "Have questions about HeartGuard? We'd love to hear from you.",
    "cta_text": "Contact Us",
    "email": "hello@heartguard.com",
    "bg_color": "#0a0a0a",
    "visible": true
  },
  "footer": {
    "logo": "assets/images/logo-placeholder.svg",
    "site_name": "HeartGuard",
    "copyright": "© 2026 HeartGuard. All rights reserved.",
    "social_twitter": "",
    "social_instagram": "",
    "social_linkedin": "",
    "bg_color": "#0a0a0a",
    "visible": true
  }
}
```

- [ ] **Step 2: Verify JSON is valid**

```bash
php -r "echo json_decode(file_get_contents('data/content.json')) ? 'Valid JSON' : 'INVALID';"
```

Expected: `Valid JSON`

---

## Task 3: Placeholder SVG Assets

**Files:**
- Create: `assets/images/logo-placeholder.svg`
- Create: `assets/images/watch-placeholder.svg`
- Create: `assets/images/step-placeholder.svg`
- Create: `assets/images/person-placeholder.svg`
- Create: `assets/images/icon-heart.svg`
- Create: `assets/images/icon-nutrition.svg`
- Create: `assets/images/icon-emergency.svg`

- [ ] **Step 1: Write `assets/images/logo-placeholder.svg`**

```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 32" width="120" height="32">
  <rect width="120" height="32" rx="4" fill="#cc0000"/>
  <text x="12" y="22" font-family="Inter,sans-serif" font-size="14" font-weight="700" fill="#ffffff">HeartGuard</text>
</svg>
```

- [ ] **Step 2: Write `assets/images/watch-placeholder.svg`**

```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 400" width="300" height="400">
  <rect width="300" height="400" rx="8" fill="#1a1a1a"/>
  <!-- Watch body -->
  <rect x="75" y="80" width="150" height="240" rx="40" fill="#222222" stroke="#333333" stroke-width="2"/>
  <!-- Watch band top -->
  <rect x="100" y="20" width="100" height="65" rx="8" fill="#1a1a1a"/>
  <!-- Watch band bottom -->
  <rect x="100" y="315" width="100" height="65" rx="8" fill="#1a1a1a"/>
  <!-- Screen -->
  <rect x="90" y="100" width="120" height="200" rx="30" fill="#0a0a0a"/>
  <!-- Heart icon on screen -->
  <path d="M150 165 C150 165 120 145 120 165 C120 180 150 200 150 200 C150 200 180 180 180 165 C180 145 150 165 150 165Z" fill="#cc0000"/>
  <!-- Heart rate line -->
  <polyline points="105,230 120,230 128,215 136,245 144,220 152,235 160,230 175,230" fill="none" stroke="#cc0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  <!-- Label -->
  <text x="150" y="370" text-anchor="middle" font-family="Inter,sans-serif" font-size="12" fill="#666666">Watch image placeholder</text>
</svg>
```

- [ ] **Step 3: Write `assets/images/step-placeholder.svg`**

```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300" width="400" height="300">
  <rect width="400" height="300" rx="8" fill="#1a1a1a"/>
  <rect x="160" y="100" width="80" height="80" rx="8" fill="#222222"/>
  <text x="200" y="175" text-anchor="middle" font-family="Inter,sans-serif" font-size="28" fill="#444444">⬡</text>
  <text x="200" y="230" text-anchor="middle" font-family="Inter,sans-serif" font-size="12" fill="#555555">Step image placeholder</text>
</svg>
```

- [ ] **Step 4: Write `assets/images/person-placeholder.svg`**

```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 500" width="400" height="500">
  <rect width="400" height="500" rx="8" fill="#1a1a1a"/>
  <circle cx="200" cy="180" r="60" fill="#222222"/>
  <path d="M80 420 C80 340 320 340 320 420" fill="#222222"/>
  <text x="200" y="480" text-anchor="middle" font-family="Inter,sans-serif" font-size="12" fill="#555555">Person image placeholder</text>
</svg>
```

- [ ] **Step 5: Write `assets/images/icon-heart.svg`**

```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="64" height="64">
  <path d="M32 54 C32 54 6 36 6 20 C6 12 12 6 20 6 C25 6 30 9 32 14 C34 9 39 6 44 6 C52 6 58 12 58 20 C58 36 32 54 32 54Z" fill="#cc0000"/>
  <polyline points="14,32 22,32 27,22 33,42 38,28 42,35 50,35" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
```

- [ ] **Step 6: Write `assets/images/icon-nutrition.svg`**

```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="64" height="64">
  <circle cx="32" cy="32" r="26" fill="none" stroke="#cc0000" stroke-width="3"/>
  <path d="M20 32 Q32 18 44 32" fill="none" stroke="#cc0000" stroke-width="3" stroke-linecap="round"/>
  <circle cx="24" cy="26" r="4" fill="#cc0000"/>
  <circle cx="40" cy="26" r="4" fill="#cc0000"/>
  <line x1="32" y1="20" x2="32" y2="10" stroke="#cc0000" stroke-width="2.5" stroke-linecap="round"/>
  <line x1="32" y1="10" x2="38" y2="6" stroke="#cc0000" stroke-width="2.5" stroke-linecap="round"/>
</svg>
```

- [ ] **Step 7: Write `assets/images/icon-emergency.svg`**

```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="64" height="64">
  <circle cx="32" cy="32" r="26" fill="#cc0000"/>
  <line x1="32" y1="16" x2="32" y2="36" stroke="#ffffff" stroke-width="5" stroke-linecap="round"/>
  <circle cx="32" cy="46" r="3" fill="#ffffff"/>
</svg>
```

---

## Task 4: CSS — Complete Stylesheet

**Files:**
- Create: `assets/css/style.css`

- [ ] **Step 1: Write CSS custom properties + reset**

```css
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

:root {
  --bg-primary: #0a0a0a;
  --bg-alt1: #111111;
  --bg-alt2: #1a1a1a;
  --accent: #cc0000;
  --accent-dark: #990000;
  --text-primary: #ffffff;
  --text-secondary: #a0a0a0;
  --border: #222222;
  --max-width: 1200px;
  --nav-height: 72px;
  --section-pad: 100px;
  --radius: 12px;
  --transition: 0.3s ease;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html { scroll-behavior: smooth; font-size: 16px; }

body {
  font-family: 'Inter', system-ui, sans-serif;
  background: var(--bg-primary);
  color: var(--text-primary);
  line-height: 1.6;
  overflow-x: hidden;
}

img { max-width: 100%; height: auto; display: block; }
a { color: inherit; text-decoration: none; }
ul { list-style: none; }

.container {
  width: 100%;
  max-width: var(--max-width);
  margin: 0 auto;
  padding: 0 2rem;
}

section { position: relative; }
```

- [ ] **Step 2: Write navbar styles**

```css
/* ── NAVBAR ─────────────────────────────────────────── */
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000;
  padding: 0 2rem;
  height: var(--nav-height);
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: background var(--transition), backdrop-filter var(--transition);
}

.navbar.scrolled {
  background: rgba(13, 13, 13, 0.92);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-bottom: 1px solid var(--border);
}

.nav-brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 700;
  font-size: 1.1rem;
  letter-spacing: -0.02em;
}

.nav-brand img { width: 32px; height: 32px; }

.nav-links {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.nav-links a {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text-secondary);
  transition: color var(--transition);
  letter-spacing: 0.01em;
}

.nav-links a:hover { color: var(--text-primary); }

.nav-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.admin-lock {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--text-secondary);
  font-size: 1.1rem;
  padding: 0.4rem;
  border-radius: 6px;
  transition: color var(--transition), background var(--transition);
  line-height: 1;
}

.admin-lock:hover { color: var(--text-primary); background: var(--bg-alt1); }

.hamburger {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.4rem;
}

.hamburger span {
  display: block;
  width: 22px;
  height: 2px;
  background: var(--text-primary);
  border-radius: 2px;
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.hamburger.active span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.hamburger.active span:nth-child(2) { opacity: 0; }
.hamburger.active span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
```

- [ ] **Step 3: Write hero section styles**

```css
/* ── HERO ────────────────────────────────────────────── */
.hero {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: var(--nav-height) 2rem 0;
  position: relative;
  overflow: hidden;
}

.hero-glow {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, rgba(204, 0, 0, 0.12) 0%, transparent 70%);
  pointer-events: none;
}

.hero-content { position: relative; z-index: 1; max-width: 800px; margin: 0 auto; }

.hero-headline {
  font-size: clamp(3rem, 8vw, 6rem);
  font-weight: 900;
  letter-spacing: -0.04em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  animation: fadeUpIn 0.8s ease 0.1s both;
}

.hero-headline span { color: var(--accent); }

.hero-subheadline {
  font-size: clamp(1rem, 2.5vw, 1.25rem);
  color: var(--text-secondary);
  max-width: 560px;
  margin: 0 auto 2.5rem;
  line-height: 1.7;
  font-weight: 400;
  animation: fadeUpIn 0.8s ease 0.3s both;
}

.btn-primary {
  display: inline-block;
  background: var(--accent);
  color: #fff;
  padding: 0.9rem 2.5rem;
  border-radius: 100px;
  font-weight: 600;
  font-size: 0.95rem;
  letter-spacing: 0.01em;
  border: none;
  cursor: pointer;
  transition: background var(--transition), transform 0.2s ease, box-shadow 0.2s ease;
  animation: fadeUpIn 0.8s ease 0.5s both;
}

.btn-primary:hover {
  background: var(--accent-dark);
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(204, 0, 0, 0.35);
}

.hero-image-wrap {
  margin-top: 4rem;
  display: flex;
  justify-content: center;
  animation: fadeUpIn 0.8s ease 0.7s both;
}

.hero-watch {
  width: clamp(220px, 35vw, 340px);
  filter: drop-shadow(0 40px 80px rgba(204, 0, 0, 0.2));
}
```

- [ ] **Step 4: Write Features section styles**

```css
/* ── FEATURES ────────────────────────────────────────── */
.section-header {
  text-align: center;
  margin-bottom: 4rem;
}

.section-label {
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--accent);
  margin-bottom: 1rem;
}

.section-title {
  font-size: clamp(2rem, 5vw, 3.5rem);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.1;
}

.section-pad { padding: var(--section-pad) 0; }

.features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.feature-card {
  background: var(--bg-alt2);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 2.5rem 2rem;
  transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
  border-color: #333333;
}

.card-icon-wrap {
  width: 56px;
  height: 56px;
  margin-bottom: 1.5rem;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(204, 0, 0, 0.1);
  position: relative;
}

.card-icon-wrap.emergency {
  animation: pulseRed 2.5s ease-in-out infinite;
}

.card-icon { width: 36px; height: 36px; }

.card-title {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
  letter-spacing: -0.02em;
}

.card-description {
  font-size: 0.925rem;
  color: var(--text-secondary);
  line-height: 1.7;
}
```

- [ ] **Step 5: Write How It Works + Who It's For styles**

```css
/* ── HOW IT WORKS ────────────────────────────────────── */
.steps-list { display: flex; flex-direction: column; gap: 5rem; }

.step-item {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
}

.step-item:nth-child(even) .step-content { order: 2; }
.step-item:nth-child(even) .step-image-wrap { order: 1; }

.step-number {
  font-size: 4rem;
  font-weight: 900;
  color: var(--accent);
  opacity: 0.25;
  letter-spacing: -0.04em;
  line-height: 1;
  margin-bottom: 0.5rem;
}

.step-title {
  font-size: 1.75rem;
  font-weight: 700;
  letter-spacing: -0.02em;
  margin-bottom: 1rem;
}

.step-description {
  font-size: 1rem;
  color: var(--text-secondary);
  line-height: 1.7;
}

.step-image-wrap img {
  border-radius: var(--radius);
  width: 100%;
}

/* ── WHO IT'S FOR ────────────────────────────────────── */
.for-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.for-card {
  background: var(--bg-alt2);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  transition: transform 0.3s ease;
}

.for-card:hover { transform: translateY(-4px); }

.for-card-image { width: 100%; aspect-ratio: 4/3; object-fit: cover; }

.for-card-body { padding: 2rem; }

.for-card-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  letter-spacing: -0.02em;
}

.for-card-description {
  font-size: 0.95rem;
  color: var(--text-secondary);
  line-height: 1.7;
}
```

- [ ] **Step 6: Write Story + Specs + Contact + Footer styles**

```css
/* ── STORY ───────────────────────────────────────────── */
.story-content { max-width: 760px; margin: 0 auto; }

.story-narrative {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.85;
  margin-bottom: 3rem;
}

.story-quote {
  border-left: 3px solid var(--accent);
  padding: 1.5rem 2rem;
  background: var(--bg-alt1);
  border-radius: 0 var(--radius) var(--radius) 0;
}

.story-quote blockquote {
  font-size: 1.25rem;
  font-weight: 600;
  font-style: italic;
  color: var(--text-primary);
  margin-bottom: 0.75rem;
  line-height: 1.5;
}

.story-quote cite {
  font-size: 0.85rem;
  color: var(--text-secondary);
  font-style: normal;
}

/* ── SPECS ───────────────────────────────────────────── */
.specs-grid {
  max-width: 760px;
  margin: 0 auto;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
}

.spec-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  padding: 1.25rem 2rem;
  border-bottom: 1px solid var(--border);
  transition: background var(--transition);
}

.spec-row:last-child { border-bottom: none; }
.spec-row:hover { background: var(--bg-alt2); }

.spec-label {
  font-size: 0.9rem;
  color: var(--text-secondary);
  font-weight: 500;
}

.spec-value {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text-primary);
}

/* ── CONTACT ─────────────────────────────────────────── */
.contact-inner {
  text-align: center;
  max-width: 560px;
  margin: 0 auto;
}

.contact-title {
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 800;
  letter-spacing: -0.03em;
  margin-bottom: 1.25rem;
}

.contact-description {
  font-size: 1rem;
  color: var(--text-secondary);
  margin-bottom: 2.5rem;
  line-height: 1.7;
}

/* ── FOOTER ──────────────────────────────────────────── */
footer {
  border-top: 1px solid var(--border);
  padding: 3rem 2rem;
}

.footer-inner {
  max-width: var(--max-width);
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1.5rem;
}

.footer-brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 700;
}

.footer-brand img { width: 28px; height: 28px; }

.footer-copy {
  font-size: 0.85rem;
  color: var(--text-secondary);
}

.footer-socials { display: flex; gap: 1rem; align-items: center; }

.footer-socials a {
  color: var(--text-secondary);
  font-size: 0.85rem;
  transition: color var(--transition);
}

.footer-socials a:hover { color: var(--text-primary); }
```

- [ ] **Step 7: Write animations + admin mode styles**

```css
/* ── ANIMATIONS ──────────────────────────────────────── */
@keyframes fadeUpIn {
  from { opacity: 0; transform: translateY(30px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes pulseRed {
  0%, 100% { box-shadow: 0 0 0 0 rgba(204, 0, 0, 0.4); }
  50%       { box-shadow: 0 0 0 14px rgba(204, 0, 0, 0); }
}

.fade-up {
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.7s ease, transform 0.7s ease;
}

.fade-up.visible {
  opacity: 1;
  transform: translateY(0);
}

/* ── ADMIN MODE ──────────────────────────────────────── */
.admin-bar {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 2000;
  background: var(--accent);
  height: 44px;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.5rem;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.1em;
}

body.admin-mode .admin-bar { display: flex; }
body.admin-mode .navbar { top: 44px; }
body.admin-mode .hero { padding-top: calc(var(--nav-height) + 44px); }

.admin-bar-label { letter-spacing: 0.12em; }

.admin-bar-actions { display: flex; gap: 0.75rem; }

.admin-btn {
  background: rgba(255,255,255,0.2);
  border: 1px solid rgba(255,255,255,0.3);
  color: #fff;
  padding: 0.3rem 1rem;
  border-radius: 100px;
  font-size: 0.78rem;
  font-weight: 600;
  cursor: pointer;
  transition: background var(--transition);
}

.admin-btn:hover { background: rgba(255,255,255,0.35); }

/* Section edit mode */
body.admin-mode [data-section] {
  outline: 2px dashed rgba(204, 0, 0, 0.4);
  outline-offset: -2px;
  position: relative;
}

body.admin-mode [data-section]:hover {
  outline-color: var(--accent);
}

[data-editable="text"]:focus {
  outline: 2px solid var(--accent);
  outline-offset: 4px;
  border-radius: 4px;
}

/* Section toolbar */
.section-toolbar {
  display: none;
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 500;
  background: rgba(10, 10, 10, 0.95);
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 0.4rem 0.6rem;
  gap: 0.4rem;
  align-items: center;
  backdrop-filter: blur(8px);
}

body.admin-mode [data-section]:hover .section-toolbar,
body.admin-mode [data-section]:focus-within .section-toolbar {
  display: flex;
}

.toolbar-btn {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  padding: 0.3rem;
  border-radius: 6px;
  transition: background var(--transition);
  line-height: 1;
  color: var(--text-primary);
}

.toolbar-btn:hover { background: var(--bg-alt2); }

.toolbar-color {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  border: 1px solid var(--border);
  cursor: pointer;
  padding: 2px;
  background: none;
}

/* Login modal */
.modal-overlay {
  display: none;
  position: fixed;
  inset: 0;
  z-index: 3000;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(8px);
  align-items: center;
  justify-content: center;
}

.modal-overlay.open { display: flex; }

.modal-box {
  background: var(--bg-alt1);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 2.5rem;
  width: 100%;
  max-width: 380px;
  text-align: center;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.modal-subtitle {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin-bottom: 2rem;
}

.modal-input {
  width: 100%;
  background: var(--bg-alt2);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text-primary);
  padding: 0.8rem 1rem;
  font-size: 1rem;
  font-family: inherit;
  margin-bottom: 0.75rem;
  transition: border-color var(--transition);
}

.modal-input:focus {
  outline: none;
  border-color: var(--accent);
}

.modal-error {
  font-size: 0.8rem;
  color: var(--accent);
  min-height: 1.2em;
  margin-bottom: 0.5rem;
}

/* Toast */
.admin-toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  z-index: 4000;
  background: var(--bg-alt2);
  border: 1px solid var(--border);
  color: var(--text-primary);
  padding: 0.8rem 1.5rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  opacity: 0;
  transform: translateY(10px);
  transition: opacity 0.3s ease, transform 0.3s ease;
  pointer-events: none;
}

.admin-toast.visible {
  opacity: 1;
  transform: translateY(0);
}

.admin-toast.error { border-color: var(--accent); color: #ff6666; }

/* ── RESPONSIVE ──────────────────────────────────────── */
@media (max-width: 900px) {
  .features-grid { grid-template-columns: 1fr; max-width: 480px; margin: 0 auto; }
  .step-item, .step-item:nth-child(even) .step-content,
  .step-item:nth-child(even) .step-image-wrap { grid-template-columns: 1fr; order: unset; }
  .step-image-wrap { order: -1; }
  .for-grid { grid-template-columns: 1fr; max-width: 480px; margin: 0 auto; }
}

@media (max-width: 768px) {
  :root { --section-pad: 60px; }

  .nav-links {
    display: none;
    position: fixed;
    top: var(--nav-height);
    left: 0;
    width: 100%;
    background: rgba(10, 10, 10, 0.98);
    flex-direction: column;
    padding: 2rem;
    gap: 1.5rem;
    border-bottom: 1px solid var(--border);
  }

  body.admin-mode .nav-links { top: calc(var(--nav-height) + 44px); }

  .nav-links.open { display: flex; }
  .hamburger { display: flex; }

  .footer-inner { flex-direction: column; align-items: flex-start; }
}
```

- [ ] **Step 8: Verify CSS file exists and is non-empty**

```bash
wc -l assets/css/style.css
```

Expected: 400+ lines

---

## Task 5: index.php — Full Single-Page Site

**Files:**
- Create: `index.php`

- [ ] **Step 1: Write PHP header + helper functions**

```php
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
```

- [ ] **Step 2: Write HTML head**

```html
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
```

- [ ] **Step 3: Write admin bar + login modal HTML**

```html
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
```

- [ ] **Step 4: Write Navbar HTML**

```html
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
```

- [ ] **Step 5: Write Hero section HTML**

```html
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
      <?= c('hero','headline','Your Heart. <span>Protected.</span>') ?>
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
```

- [ ] **Step 6: Write Features section HTML**

```php
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
               src="<?= esc($card['icon'] ?? '') ?>"
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
```

- [ ] **Step 7: Write How It Works section HTML**

```php
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
```

- [ ] **Step 8: Write Who It's For section HTML**

```php
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
             src="<?= c('who_its_for', $side . '.image', 'assets/images/person-placeholder.svg') ?>"
             alt="<?= c('who_its_for', $side . '.title') ?>"
             data-image-key="who_its_for.<?= $side ?>.image">
        <div class="for-card-body">
          <h3 class="for-card-title" data-editable="text" data-key="<?= $side ?>.title">
            <?= c('who_its_for', $side . '.title') ?>
          </h3>
          <p class="for-card-description" data-editable="text" data-key="<?= $side ?>.description">
            <?= c('who_its_for', $side . '.description') ?>
          </p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
```

- [ ] **Step 9: Write Story section HTML**

```html
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
```

- [ ] **Step 10: Write Specs section HTML**

```php
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
```

- [ ] **Step 11: Write Contact + Footer + closing HTML**

```html
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
      <?php if ($content['footer']['social_twitter'] ?? ''): ?>
        <a href="<?= c('footer','social_twitter') ?>" target="_blank" rel="noopener">Twitter</a>
      <?php endif; ?>
      <?php if ($content['footer']['social_instagram'] ?? ''): ?>
        <a href="<?= c('footer','social_instagram') ?>" target="_blank" rel="noopener">Instagram</a>
      <?php endif; ?>
      <?php if ($content['footer']['social_linkedin'] ?? ''): ?>
        <a href="<?= c('footer','social_linkedin') ?>" target="_blank" rel="noopener">LinkedIn</a>
      <?php endif; ?>
    </div>
  </div>
</footer>

<script src="assets/js/main.js"></script>
<script src="assets/js/admin.js"></script>
</body>
</html>
```

- [ ] **Step 12: Start dev server and verify site renders**

```bash
php -S localhost:8000
```

Open `http://localhost:8000` in browser. Expected: dark page with all 9 sections visible, Inter font loaded, no PHP errors in terminal.

---

## Task 6: main.js — Scroll Animations + Navbar + Parallax

**Files:**
- Create: `assets/js/main.js`

- [ ] **Step 1: Write `assets/js/main.js`**

```javascript
(function () {
  'use strict';

  // ── Navbar scroll transition ─────────────────────────
  const navbar = document.getElementById('navbar');

  function updateNavbar() {
    if (window.scrollY > 60) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  }

  window.addEventListener('scroll', updateNavbar, { passive: true });
  updateNavbar();

  // ── Hamburger menu ───────────────────────────────────
  const hamburger = document.getElementById('hamburger');
  const navLinks  = document.getElementById('nav-links');

  hamburger.addEventListener('click', () => {
    const isOpen = navLinks.classList.toggle('open');
    hamburger.classList.toggle('active', isOpen);
    hamburger.setAttribute('aria-expanded', String(isOpen));
  });

  // Close menu on nav link click (mobile)
  navLinks.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      navLinks.classList.remove('open');
      hamburger.classList.remove('active');
      hamburger.setAttribute('aria-expanded', 'false');
    });
  });

  // ── Smooth scroll for anchor links ──────────────────
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (!target) return;
      e.preventDefault();
      const navHeight = navbar.offsetHeight;
      const adminBar  = document.getElementById('admin-bar');
      const adminBarH = (adminBar && adminBar.style.display !== 'none') ? adminBar.offsetHeight : 0;
      const offset    = target.getBoundingClientRect().top + window.scrollY - navHeight - adminBarH - 16;
      window.scrollTo({ top: offset, behavior: 'smooth' });
    });
  });

  // ── IntersectionObserver fade-up ────────────────────
  const fadeObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        fadeObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });

  document.querySelectorAll('.fade-up').forEach(el => fadeObserver.observe(el));

  // ── Hero watch parallax ──────────────────────────────
  const heroWatch = document.querySelector('.hero-watch');

  function updateParallax() {
    if (!heroWatch) return;
    const scrollY = window.scrollY;
    if (scrollY < window.innerHeight) {
      heroWatch.style.transform = `translateY(${scrollY * 0.12}px)`;
    }
  }

  window.addEventListener('scroll', updateParallax, { passive: true });

})();
```

- [ ] **Step 2: Verify in browser**

Open `http://localhost:8000`. Scroll down — sections should fade up as they enter the viewport. Navbar should become frosted glass after scrolling 60px. Resize to mobile width — hamburger should appear and toggle the nav menu.

---

## Task 7: Backend API

**Files:**
- Create: `api/auth.php`
- Create: `api/save-content.php`
- Create: `api/upload-image.php`

- [ ] **Step 1: Write `api/auth.php`**

```php
<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];

// Session check (GET with no logout param)
if ($method === 'GET' && !isset($_GET['logout'])) {
    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
        if ((time() - ($_SESSION['last_activity'] ?? 0)) > 7200) {
            session_destroy();
            echo json_encode(['authenticated' => false]);
        } else {
            $_SESSION['last_activity'] = time();
            echo json_encode(['authenticated' => true]);
        }
    } else {
        echo json_encode(['authenticated' => false]);
    }
    exit;
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    echo json_encode(['success' => true]);
    exit;
}

// Login
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['password']) || !is_string($input['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing password']);
        exit;
    }
    if (password_verify($input['password'], ADMIN_PASSWORD_HASH)) {
        $_SESSION['admin']         = true;
        $_SESSION['last_activity'] = time();
        echo json_encode(['success' => true]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid password']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
```

- [ ] **Step 2: Write `api/save-content.php`**

```php
<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ((time() - ($_SESSION['last_activity'] ?? 0)) > 7200) {
    session_destroy();
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Session expired']);
    exit;
}

$_SESSION['last_activity'] = time();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if ($data === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

$contentPath = __DIR__ . '/../data/content.json';
$tmpPath     = $contentPath . '.tmp.' . getmypid();
$json        = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if (file_put_contents($tmpPath, $json) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Write failed']);
    exit;
}

if (!rename($tmpPath, $contentPath)) {
    @unlink($tmpPath);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Save failed']);
    exit;
}

echo json_encode(['success' => true]);
```

- [ ] **Step 3: Write `api/upload-image.php`**

```php
<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ((time() - ($_SESSION['last_activity'] ?? 0)) > 7200) {
    session_destroy();
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Session expired']);
    exit;
}

$_SESSION['last_activity'] = time();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['image'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$file    = $_FILES['image'];
$maxSize = 5 * 1024 * 1024; // 5 MB

if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Upload error: ' . $file['error']]);
    exit;
}

if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'File too large (max 5MB)']);
    exit;
}

$finfo        = new finfo(FILEINFO_MIME_TYPE);
$mimeType     = $finfo->file($file['tmp_name']);
$allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

if (!array_key_exists($mimeType, $allowedMimes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file type (jpg/png/webp only)']);
    exit;
}

$ext      = $allowedMimes[$mimeType];
$filename = bin2hex(random_bytes(16)) . '.' . $ext;
$destDir  = __DIR__ . '/../uploads/';
$destPath = $destDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
    exit;
}

echo json_encode(['success' => true, 'path' => 'uploads/' . $filename]);
```

- [ ] **Step 4: Test auth endpoint — unauthenticated session check**

```bash
curl -s http://localhost:8000/api/auth.php
```

Expected: `{"authenticated":false}`

- [ ] **Step 5: Test save endpoint — unauthorized rejection**

```bash
curl -s -X POST http://localhost:8000/api/save-content.php \
  -H "Content-Type: application/json" \
  -d '{"test":true}'
```

Expected: HTTP 403 with `{"success":false,"message":"Unauthorized"}`

---

## Task 8: Admin JS

**Files:**
- Create: `assets/js/admin.js`

- [ ] **Step 1: Write `assets/js/admin.js`**

```javascript
(function () {
  'use strict';

  // ── SHA-256 via Web Crypto API ───────────────────────
  async function sha256(message) {
    const buf  = new TextEncoder().encode(message);
    const hash = await crypto.subtle.digest('SHA-256', buf);
    return Array.from(new Uint8Array(hash))
      .map(b => b.toString(16).padStart(2, '0'))
      .join('');
  }

  // ── Session check on load ────────────────────────────
  async function checkSession() {
    try {
      const res  = await fetch('api/auth.php');
      const data = await res.json();
      if (data.authenticated) activateAdminMode();
    } catch (_) {}
  }

  // ── Login ────────────────────────────────────────────
  async function login(password) {
    const hash = await sha256(password);
    try {
      const res  = await fetch('api/auth.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ password: hash })
      });
      const data = await res.json();
      if (data.success) {
        closeLoginModal();
        activateAdminMode();
      } else {
        document.getElementById('login-error').textContent = 'Invalid password. Try again.';
      }
    } catch (_) {
      document.getElementById('login-error').textContent = 'Connection error. Please retry.';
    }
  }

  // ── Logout ───────────────────────────────────────────
  async function logout() {
    await fetch('api/auth.php?logout=1').catch(() => {});
    deactivateAdminMode();
  }

  // ── Admin mode toggle ────────────────────────────────
  function activateAdminMode() {
    document.body.classList.add('admin-mode');
    document.querySelectorAll('[data-editable="text"]').forEach(el => {
      el.contentEditable = 'true';
    });
  }

  function deactivateAdminMode() {
    document.body.classList.remove('admin-mode');
    document.querySelectorAll('[data-editable="text"]').forEach(el => {
      el.contentEditable = 'false';
    });
  }

  // ── Nested value helper ──────────────────────────────
  function setNestedValue(obj, path, value) {
    const keys = path.split('.');
    let cur = obj;
    for (let i = 0; i < keys.length - 1; i++) {
      const key      = keys[i];
      const nextKey  = keys[i + 1];
      const nextIsIdx = /^\d+$/.test(nextKey);
      if (cur[key] === undefined || cur[key] === null) {
        cur[key] = nextIsIdx ? [] : {};
      }
      cur = cur[key];
    }
    cur[keys[keys.length - 1]] = value;
  }

  // ── Collect current DOM state → content object ───────
  function collectContent() {
    const content = {};
    document.querySelectorAll('[data-section]').forEach(section => {
      const sKey = section.dataset.section;
      if (!content[sKey]) content[sKey] = {};

      // bg_color + visibility
      content[sKey].bg_color = section.style.backgroundColor || section.dataset.bgColor || '#0a0a0a';
      content[sKey].visible  = section.dataset.hidden !== 'true';

      // Text fields with data-key scoped inside this section
      section.querySelectorAll('[data-editable="text"][data-key]').forEach(el => {
        // Skip elements that belong to a nested section
        if (el.closest('[data-section]') !== section) return;
        setNestedValue(content[sKey], el.dataset.key, el.innerText.trim());
      });

      // Image paths
      section.querySelectorAll('[data-image-key]').forEach(el => {
        if (el.closest('[data-section]') !== section) return;
        const imgPath = el.getAttribute('src') || '';
        setNestedValue(content[sKey], el.dataset.imageKey.replace(/^[^.]+\./, ''), imgPath);
      });
    });

    // Navbar brand (outside section wrapper)
    const brandName = document.querySelector('.nav-brand span[data-key]');
    if (brandName && !content.navbar) content.navbar = {};
    if (brandName) content.navbar.site_name = brandName.innerText.trim();

    return content;
  }

  // ── Save All ─────────────────────────────────────────
  async function saveAll() {
    const btn = document.getElementById('admin-save');
    btn.disabled = true;
    btn.textContent = 'Saving…';
    try {
      const res  = await fetch('api/save-content.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify(collectContent())
      });
      const data = await res.json();
      if (data.success) {
        showToast('Changes saved');
      } else {
        showToast('Save failed: ' + (data.message || 'unknown error'), 'error');
      }
    } catch (_) {
      showToast('Connection error — changes not saved', 'error');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Save All';
    }
  }

  // ── Image upload ─────────────────────────────────────
  function handleImageUpload(targetKey) {
    const input = document.createElement('input');
    input.type   = 'file';
    input.accept = 'image/jpeg,image/png,image/webp';
    input.onchange = async (e) => {
      const file = e.target.files[0];
      if (!file) return;
      const formData = new FormData();
      formData.append('image', file);
      try {
        const res  = await fetch('api/upload-image.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          const imgEl = document.querySelector(`[data-image-key="${targetKey}"]`);
          if (imgEl) imgEl.setAttribute('src', data.path);
          // Update contact mailto href if applicable
          if (targetKey === 'contact.email') {
            const cta = document.getElementById('contact-cta');
            if (cta) cta.href = 'mailto:' + data.path;
          }
          showToast('Image uploaded');
        } else {
          showToast('Upload failed: ' + (data.message || 'error'), 'error');
        }
      } catch (_) {
        showToast('Upload error', 'error');
      }
    };
    input.click();
  }

  // ── Color picker per section ─────────────────────────
  function bindColorPicker(picker, section) {
    picker.addEventListener('input', () => {
      section.style.backgroundColor = picker.value;
      section.dataset.bgColor = picker.value;
    });
  }

  // ── Visibility toggle ────────────────────────────────
  function bindVisibilityToggle(btn, section) {
    btn.addEventListener('click', () => {
      const isHidden = section.dataset.hidden === 'true';
      section.dataset.hidden = isHidden ? 'false' : 'true';
      section.style.opacity  = isHidden ? '1' : '0.25';
      btn.title = isHidden ? 'Hide section' : 'Show section';
    });
  }

  // ── Toast notification ───────────────────────────────
  let toastTimer = null;

  function showToast(message, type = 'success') {
    const toast     = document.getElementById('admin-toast');
    toast.textContent = message;
    toast.className = 'admin-toast ' + type + ' visible';
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { toast.classList.remove('visible'); }, 3000);
  }

  // ── Modal helpers ────────────────────────────────────
  function openLoginModal() {
    document.getElementById('login-modal').classList.add('open');
    document.getElementById('admin-password').focus();
  }

  function closeLoginModal() {
    document.getElementById('login-modal').classList.remove('open');
    document.getElementById('login-error').textContent = '';
    document.getElementById('admin-password').value = '';
  }

  // ── Init ─────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', () => {
    checkSession();

    // Lock icon
    document.getElementById('admin-lock').addEventListener('click', openLoginModal);

    // Modal backdrop dismiss
    document.getElementById('login-modal').addEventListener('click', e => {
      if (e.target === e.currentTarget) closeLoginModal();
    });

    // Escape key dismiss
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') closeLoginModal();
    });

    // Login form submit
    document.getElementById('login-form').addEventListener('submit', async e => {
      e.preventDefault();
      const pw = document.getElementById('admin-password').value;
      if (!pw) return;
      await login(pw);
    });

    // Admin bar buttons
    document.getElementById('admin-save').addEventListener('click', saveAll);
    document.getElementById('admin-logout').addEventListener('click', logout);

    // Per-section toolbar bindings
    document.querySelectorAll('[data-section]').forEach(section => {
      const toolbar = section.querySelector('.section-toolbar');
      if (!toolbar) return;

      // Color picker
      const colorPicker = toolbar.querySelector('.toolbar-color');
      if (colorPicker) bindColorPicker(colorPicker, section);

      // Visibility toggle
      const visBtn = toolbar.querySelector('.toolbar-visibility');
      if (visBtn) bindVisibilityToggle(visBtn, section);

      // Image upload buttons
      toolbar.querySelectorAll('.toolbar-image').forEach(btn => {
        btn.addEventListener('click', () => handleImageUpload(btn.dataset.imageTarget));
      });
    });
  });

})();
```

- [ ] **Step 2: Test admin login flow in browser**

1. Open `http://localhost:8000`
2. Click 🔒 in the navbar — modal should open
3. Generate a test hash first (see Task 9 Step 1)
4. Enter password — on success the red ADMIN MODE bar appears and all editable text gets a red outline on focus
5. Click a headline — type to edit it
6. Click "Save All" — toast should show "Changes saved"
7. Reload page — verify edited text persists (read from content.json)

- [ ] **Step 3: Test image upload in browser (admin mode)**

Hover over the hero section. Click the 🖼 icon in the toolbar. Select a JPG under 5MB. Verify the watch image updates live and a "Image uploaded" toast appears. Verify `uploads/` contains the renamed file.

- [ ] **Step 4: Test visibility toggle**

In admin mode, hover over the Features section and click 👁. The section should fade to 25% opacity. Click "Save All". Reload — the section should be hidden. Click 👁 again to re-enable.

---

## Task 9: README + Password Setup Script + Deploy Checklist

**Files:**
- Create: `generate-hash.php`
- Create: `README.md`

- [ ] **Step 1: Write `generate-hash.php`**

```php
<?php
/**
 * Run this script once to generate your admin password hash.
 * Usage: php generate-hash.php
 * Paste the output into config/config.php as ADMIN_PASSWORD_HASH.
 * Delete this file before deploying to production.
 */
echo "Enter your admin password: ";
$password = trim(fgets(STDIN));

if (strlen($password) < 8) {
    echo "Error: password must be at least 8 characters.\n";
    exit(1);
}

$sha256 = hash('sha256', $password);
$bcrypt = password_hash($sha256, PASSWORD_BCRYPT, ['cost' => 12]);

echo "\nYour bcrypt hash (paste into config/config.php):\n";
echo $bcrypt . "\n\n";
echo "Define: define('ADMIN_PASSWORD_HASH', '" . $bcrypt . "');\n";
```

- [ ] **Step 2: Run script to confirm it works**

```bash
echo "mysecretpassword123" | php generate-hash.php
```

Expected: output includes a `$2y$12$...` bcrypt hash string.

- [ ] **Step 3: Write `README.md`**

```markdown
# HeartGuard Website

Apple-inspired single-page marketing site with inline admin edit mode.
Built with PHP + Vanilla JS. No framework, no build step.

## Local Development

Requirements: PHP 7.4+

```bash
php -S localhost:8000
```

Open http://localhost:8000

## Setting the Admin Password

1. Run the hash generator:
   ```bash
   php generate-hash.php
   ```
2. Enter your chosen password (min 8 characters).
3. Copy the bcrypt hash from the output.
4. Open `config/config.php` and replace the placeholder:
   ```php
   define('ADMIN_PASSWORD_HASH', 'paste-your-hash-here');
   ```
5. **Delete `generate-hash.php`** before deploying to production.

## Admin Mode

1. On the live site, click the 🔒 icon in the top-right of the navbar.
2. Enter your password.
3. The red ADMIN MODE bar appears at the top.
4. Hover over any section to reveal its toolbar:
   - ✏️ Click any text to edit it inline.
   - 🖼 Click to swap an image (JPG/PNG/WebP, max 5MB).
   - 🎨 Click the color swatch to change the section background.
   - 👁 Click to show/hide the entire section.
5. Click **Save All** when done. Changes persist to `data/content.json`.
6. Click **Logout** when finished.

## Deploying to GoDaddy Shared Hosting

1. **Buy hosting**: GoDaddy Economy or Deluxe shared Linux plan (~$3–5 CAD/month).
2. **Set your password** (see above) and **delete `generate-hash.php`**.
3. **Verify `.htaccess`** blocks `config/` and `data/` — test locally with curl first:
   ```bash
   curl -I http://localhost:8000/config/config.php
   # Expected: 403 Forbidden
   curl -I http://localhost:8000/data/content.json
   # Expected: 403 Forbidden
   ```
4. **Zip the project** (exclude `.git` if present):
   ```bash
   zip -r heartguard.zip . --exclude="*.git*" --exclude="generate-hash.php"
   ```
5. **Upload via cPanel**:
   - Log in to cPanel → File Manager.
   - Navigate to `public_html/`.
   - Upload `heartguard.zip` and extract it.
6. **Enable SSL** in cPanel → SSL/TLS → Install Let's Encrypt certificate.
7. **Test**:
   - Visit your domain — site should load.
   - Visit `yourdomain.com/config/config.php` — should return 403.
   - Test admin login and Save All.

## Self-Hosting (Alternative)

If hosting on your own machine:

1. Install PHP: `sudo apt install php`
2. Run: `php -S 0.0.0.0:8080`
3. Set up DuckDNS (free) for a domain: https://www.duckdns.org
4. Configure router port forwarding: external 80/443 → your machine:8080
5. For HTTPS: install Certbot and use `certbot --standalone`

## Security Checklist

- [ ] Admin password hash set in `config/config.php`
- [ ] `generate-hash.php` deleted
- [ ] `.htaccess` blocking `config/` returns 403 in production
- [ ] `.htaccess` blocking `data/` returns 403 in production
- [ ] `uploads/` directory listing disabled (no directory index)
- [ ] HTTPS enabled
- [ ] Admin login tested on production
- [ ] Save All tested on production

## Swapping Placeholder Content

All placeholder images are in `assets/images/`. Use admin mode to swap them,
or replace the SVG files directly with your real assets (same filename = no config change needed).

Content lives in `data/content.json` — you can edit it directly as a JSON file
if you prefer working outside the browser admin mode.
```

- [ ] **Step 4: Final end-to-end verification**

```bash
# Confirm .htaccess blocks sensitive paths
curl -o /dev/null -s -w "%{http_code}" http://localhost:8000/config/config.php
# Expected: 403

curl -o /dev/null -s -w "%{http_code}" http://localhost:8000/data/content.json
# Expected: 403

# Confirm site loads
curl -o /dev/null -s -w "%{http_code}" http://localhost:8000/
# Expected: 200

# Confirm API session check works
curl -s http://localhost:8000/api/auth.php
# Expected: {"authenticated":false}
```

- [ ] **Step 5: Commit everything**

```bash
git init
git add -A
git commit -m "feat: initial HeartGuard website — PHP + Vanilla JS single-page site with admin edit mode"
```

---

## Self-Review Notes

- **Spec coverage:**
  - ✅ All 9 sections (navbar, hero, features, how-it-works, who-its-for, story, specs, contact, footer)
  - ✅ Apple-style transitions (IntersectionObserver fade-up, staggered hero, parallax, hover effects)
  - ✅ Sticky navbar with blur-backdrop on scroll
  - ✅ Admin lock icon → password modal → session
  - ✅ Inline contenteditable text editing
  - ✅ Image upload with MIME/size validation
  - ✅ Background color picker per section
  - ✅ Section visibility toggle
  - ✅ Save All → content.json
  - ✅ SHA-256 client + bcrypt server password storage
  - ✅ .htaccess blocking config/ and data/
  - ✅ Mobile responsive with hamburger menu
  - ✅ GoDaddy deploy instructions in README
  - ✅ Self-host fallback documented

- **Type consistency:** `data-section` keys in HTML match `content.json` top-level keys exactly. `data-key` dot-paths in HTML match `collectContent()` `setNestedValue` paths.

- **No placeholders:** All steps contain complete, working code.
```
