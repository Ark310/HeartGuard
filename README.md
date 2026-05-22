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
