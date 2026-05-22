# HeartGuard Website — Design Spec

**Date:** 2026-05-22
**Status:** Approved

---

## Overview

A single-page marketing website for HeartGuard — a smart health watch for heart disease patients and athletes. The site is professional, Apple-inspired in aesthetic and motion, and includes a password-protected inline admin mode for editing content without touching code. Built with PHP + Vanilla JS, deployable to GoDaddy shared hosting for under $10 CAD/month.

---

## Architecture

**Stack:** PHP (backend + templating), HTML/CSS/Vanilla JS (frontend)
**Hosting target:** GoDaddy shared hosting (cPanel) — no VPS or Node runtime required
**Content persistence:** `data/content.json` — single source of truth for all editable content, read server-side on page load via PHP include

### File Structure

```
heartguard/
├── index.php                   # Single-page site (all sections, PHP-populated)
├── admin.php                   # Admin login + edit mode UI
├── api/
│   ├── save-content.php        # POST — writes edits to content.json
│   ├── upload-image.php        # POST — handles image uploads
│   └── auth.php                # Session-based login/logout
├── config/
│   └── config.php              # Bcrypt-hashed admin password (blocked by .htaccess)
├── data/
│   └── content.json            # All editable content (blocked from direct browser access)
├── uploads/                    # Admin-uploaded images (directory listing disabled)
├── assets/
│   ├── css/
│   │   └── style.css           # All styles + CSS custom properties for theming
│   ├── js/
│   │   ├── main.js             # Scroll animations, transitions, nav behavior
│   │   └── admin.js            # Admin edit mode logic
│   └── images/                 # Static assets (logo, placeholder images)
├── .htaccess                   # Blocks config/, data/ from browser; enables clean URLs
└── README.md                   # Deploy instructions + how to set admin password
```

---

## Visual Design

### Color Palette
| Role | Value |
|---|---|
| Background (primary) | `#0a0a0a` |
| Background (alternate sections) | `#111111`, `#1a1a1a` |
| Accent | `#cc0000` (crimson red) |
| Text primary | `#ffffff` |
| Text secondary/muted | `#a0a0a0` |
| Borders / dividers | `#222222` |

### Typography
- **Headings:** `Inter` (Google Fonts, free) — large, bold, tight letter-spacing
- **Body:** `Inter` or `system-ui` — comfortable line-height
- Fallback stack: `Inter → system-ui → sans-serif`

### Transitions & Animations
- **Scroll-triggered fade-up:** Every section's elements fade in + translate up 30px as they enter the viewport (IntersectionObserver API, no library)
- **Navbar:** Transparent on hero → solid `#0d0d0d` + blur backdrop on scroll
- **Smooth scroll:** `scroll-behavior: smooth` + JS refinement for nav link clicks
- **Feature cards:** Scale + shadow on hover
- **Hero watch image:** Slow CSS parallax drift on scroll
- **Emergency icon:** CSS keyframe pulse animation in red
- **Page load:** Staggered fade-in on hero headline, subheadline, and CTA

### Layout
- Full-width sections, max content width `1200px` centered
- Mobile-first responsive — hamburger menu mobile, horizontal nav desktop
- Generous vertical section padding (80–120px) for breathing room

---

## Admin Edit Mode

### Access
- Discreet lock icon in the navbar (far right) — not prominent to visitors
- Clicking opens a minimal modal overlay with a password field
- Password hashed client-side (SHA-256) before sending; compared server-side against bcrypt hash in `config.php`
- On success: PHP `$_SESSION` created, page reloads in edit mode
- Session timeout: 2 hours of inactivity

### Edit Mode UI
- Persistent top bar: `"ADMIN MODE — EDITING"` in red with **Save All** and **Logout** buttons
- Each section: red dashed border + hover toolbar with:
  - ✏️ **Edit text** — inline `contenteditable`
  - 🖼 **Swap image** — file upload picker
  - 🎨 **Background color** — native `<input type="color">`
  - 👁 **Toggle visibility** — show/hide entire section

### Saving
- **Save All** POSTs full page state (text, image paths, bg colors, visibility) to `save-content.php`
- Server writes to `data/content.json`
- Success toast: `"Changes saved"` (bottom-right)
- No auto-save — deliberate save only

### Security
- All write endpoints check for valid PHP session before executing
- Uploaded images: MIME type validated (jpg/png/webp only), max 5MB, renamed to random hash
- `config/` and `data/` blocked by `.htaccess`
- No password ever stored client-side

### Password Setup
- Admin sets password by running a one-line PHP script locally that outputs a bcrypt hash
- Hash is pasted into `config/config.php`
- Instructions in `README.md`

---

## Page Sections

All sections are single-page, scroll-anchored. All text, images, background colors, and visibility are editable via admin mode.

### 1. Navbar
- Logo image slot + site name text
- Nav links: Features, How It Works, Who It's For, Story, Specs, Contact
- Lock icon (admin access) — far right
- Sticky with blur-backdrop transition on scroll

### 2. Hero
- Headline: *"Your Heart. Protected."*
- Subheadline: *"The smart watch built for people who can't afford to ignore the warning signs."*
- CTA button: *"Learn More"* → scrolls to Features
- Large centered watch image placeholder
- Background: `#0a0a0a` with faint red radial glow behind watch

### 3. Features (3-column card grid)
| Card | Title | Default Description |
|---|---|---|
| 1 | Heart Monitoring | Real-time tracking of heart rate, blood pressure, oxygen levels, and irregular patterns. |
| 2 | Nutrition Tracking | Personalized dietary guidance based on your condition. Warnings for high-sodium and high-risk foods. |
| 3 | Emergency Response | Automatic alerts to emergency services and family members with your live location if a dangerous event is detected. |

### 4. How It Works (numbered steps, alternating text/image)
1. Wear HeartGuard daily
2. It monitors your vitals continuously
3. Get real-time alerts and recommendations
4. Emergency services notified automatically if needed

Each step has an image/icon placeholder slot.

### 5. Who It's For (2-column split)
- Left column: Heart disease patients — copy + image placeholder
- Right column: Athletes — copy + image placeholder

### 6. Story / Testimonials
- Section headline: *"Why HeartGuard Exists"*
- Full editable narrative block (the story behind the product)
- Optional quote pullout with editable text

### 7. Specs (grid/table layout)
Editable value fields (not add/remove rows — layout-safe):
| Spec | Placeholder Value |
|---|---|
| Battery Life | Up to 7 days |
| Sensors | Heart rate, SpO2, Blood pressure, ECG |
| Water Resistance | 50 metres |
| Connectivity | Bluetooth 5.2, Wi-Fi |
| Compatibility | iOS & Android |

### 8. Contact
- Headline + subtext (editable)
- CTA button: *"Get In Touch"* — `mailto:` link (email address editable in admin)

### 9. Footer
- Logo + copyright text
- Social icon links (URLs editable in admin)
- Minimal 2-row layout

---

## Hosting & Deployment

### GoDaddy Shared Hosting (Recommended)
- Cost: ~$3–5 CAD/month (Economy or Deluxe shared plan)
- Upload via cPanel File Manager or FTP — zip the project, upload, extract
- PHP runs natively, no configuration needed
- Enable SSL (free via GoDaddy or Let's Encrypt via cPanel)

### Self-hosted (Fallback)
- Install PHP locally (`php -S localhost:8000`)
- Bind to domain via router port forwarding + dynamic DNS (e.g., DuckDNS — free)
- Use Certbot for SSL if exposing publicly

### Deploy checklist
- [ ] Set admin password hash in `config/config.php`
- [ ] Verify `.htaccess` blocks `config/` and `data/`
- [ ] Set `uploads/` to not list directory contents
- [ ] Enable HTTPS
- [ ] Test admin login and Save All flow

---

## Security Summary

| Concern | Mitigation |
|---|---|
| Password exposure | Bcrypt hash in server-only config file; blocked by .htaccess |
| Session hijacking | PHP session with 2-hour timeout; HTTPS required |
| Unauthorized writes | All write endpoints validate session before executing |
| Malicious uploads | MIME check + size limit + random filename on all uploads |
| Directory traversal | Uploads renamed to hash; uploads/ directory listing disabled |
| Direct config access | .htaccess denies browser access to config/ and data/ |
| XSS in editable content | Output escaped with `htmlspecialchars()` on render |
