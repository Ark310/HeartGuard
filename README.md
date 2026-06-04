<h1 align="center">HeartGuard</h1>
<p align="center"><em>An Apple-inspired single-page marketing site with an inline admin edit mode — PHP, no framework, no build step.</em></p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat-square&logo=php&logoColor=white">
  <img src="https://img.shields.io/badge/Vanilla_JS-ES6-F7DF1E?style=flat-square&logo=javascript&logoColor=black">
  <img src="https://img.shields.io/badge/license-MIT-blue?style=flat-square">
  <img src="https://img.shields.io/badge/status-finished-brightgreen?style=flat-square">
</p>

> A fully self-contained product marketing site for a heart-health smart watch — built entirely in PHP and vanilla JS with zero dependencies. Ship it to any shared host in minutes; non-technical owners can update every word and image directly in the browser through a password-protected inline edit mode.

_📸 Screenshot coming soon._

## 🎯 The Problem

Static marketing pages go stale the moment they leave the developer's hands. The owner either waits on a developer for every copy tweak, or pays for a heavyweight CMS that needs maintenance of its own. Shared hosting is cheap and common, yet most modern frameworks refuse to run on it without complex configuration.

## 💡 The Solution

HeartGuard is a single `index.php` that renders a polished Apple-style product page from a `data/content.json` file. Clicking the 🔒 icon unlocks an inline admin mode: click any text to rewrite it, swap any image, change section backgrounds, or hide entire sections — then hit **Save All** and the JSON is updated server-side. No CMS, no database, no npm, no build step.

## ✨ Features

- **Inline text editing** — click any heading or paragraph in admin mode to edit it in-place
- **Image swapping** — replace hero, feature card, and step images via upload (JPG/PNG/WebP, max 5 MB)
- **Per-section background color** — color picker on every section toolbar; changes persist immediately
- **Section show/hide** — toggle entire sections on or off without deleting content
- **bcrypt-protected admin login** — password is stored only as a bcrypt hash; never in plain text
- **JSON content store** — all copy lives in `data/content.json`; editable in the browser or directly as a file
- **Zero dependencies** — pure PHP 7.4+ and vanilla JS; deploys to any shared host (GoDaddy, cPanel, etc.)
- **Scroll animations** — `IntersectionObserver`-based fade-up entrance on every section

## 🛠️ Tech Stack

`PHP 7.4+` · `Vanilla JS (ES6)` · `CSS Custom Properties` · `bcrypt` · `JSON`

## 🚀 Quickstart

### Local development

Requirements: PHP 7.4+

```bash
git clone https://github.com/Ark310/HeartGuard.git
cd HeartGuard
php -S localhost:8000
```

Open http://localhost:8000.

### Set the admin password

```bash
php generate-hash.php
# Enter your chosen password (min 8 characters).
# Copy the bcrypt hash from the output.
```

Open `config/config.php` and replace the placeholder:

```php
define('ADMIN_PASSWORD_HASH', 'paste-your-hash-here');
```

**Delete `generate-hash.php`** before going to production.

## 🧠 How It Works

`index.php` reads `data/content.json` at request time and renders every editable value through `htmlspecialchars` before output. When a visitor clicks the 🔒 navbar icon, a modal POSTs the password to `api/auth.php`, which runs `password_verify` against the bcrypt hash in `config/config.php` and starts a PHP session on success. Admin JS then attaches `contenteditable` to every marked element and reveals per-section toolbars. Clicking **Save All** POSTs the updated content tree to `api/save-content.php`, which writes the new JSON atomically. Image uploads go to `api/upload-image.php`, which validates MIME type and file size before moving the file into `uploads/`. The `.htaccess` file blocks direct HTTP access to `config/` and `data/`, so the hash and raw content are never publicly readable.

### Deploying to GoDaddy shared hosting

1. **Buy hosting**: GoDaddy Economy or Deluxe shared Linux plan (~$3–5 CAD/month).
2. **Set your password** (see above) and **delete `generate-hash.php`**.
3. **Verify `.htaccess`** blocks sensitive directories — test locally with curl first:
   ```bash
   curl -I http://localhost:8000/config/config.php
   # Expected: 403 Forbidden
   curl -I http://localhost:8000/data/content.json
   # Expected: 403 Forbidden
   ```
4. **Zip the project** (exclude `.git` and the hash generator):
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
   - Test admin login and **Save All**.

### Self-hosting (alternative)

```bash
sudo apt install php
php -S 0.0.0.0:8080
```

For a public domain: set up [DuckDNS](https://www.duckdns.org) (free), forward external port 80/443 to your machine, then run `certbot --standalone` for HTTPS.

### Swapping placeholder content

All placeholder images live in `assets/images/`. Use admin mode to swap them, or replace the SVG files directly with your real assets (same filename = no config change needed).

Content also lives in `data/content.json` — edit it directly as a JSON file if you prefer working outside the browser.

## 🔒 Security Checklist

Before going live, verify each item:

- [ ] Admin password hash set in `config/config.php`
- [ ] `generate-hash.php` deleted from the server
- [ ] `.htaccess` blocking `config/` returns 403 in production
- [ ] `.htaccess` blocking `data/` returns 403 in production
- [ ] `uploads/` directory listing disabled (no directory index)
- [ ] HTTPS enabled
- [ ] Admin login tested on production
- [ ] Save All tested on production

## 📄 License

MIT © Abdul Raqeeb Khatri
