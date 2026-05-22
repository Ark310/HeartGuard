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

      // Text fields
      section.querySelectorAll('[data-editable="text"][data-key]').forEach(el => {
        if (el.closest('[data-section]') !== section) return;
        setNestedValue(content[sKey], el.dataset.key, el.innerText.trim());
      });

      // Image paths
      section.querySelectorAll('[data-image-key]').forEach(el => {
        if (el.closest('[data-section]') !== section) return;
        const fullKey = el.dataset.imageKey;
        const dotIdx  = fullKey.indexOf('.');
        const fieldKey = dotIdx !== -1 ? fullKey.slice(dotIdx + 1) : fullKey;
        setNestedValue(content[sKey], fieldKey, el.getAttribute('src') || '');
      });
    });

    // Navbar brand name (outside data-section wrapper)
    const brandSpan = document.querySelector('.nav-brand span[data-key]');
    if (brandSpan) {
      if (!content.navbar) content.navbar = {};
      content.navbar.site_name = brandSpan.innerText.trim();
    }

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
    const toast       = document.getElementById('admin-toast');
    toast.textContent = message;
    toast.className   = 'admin-toast ' + type + ' visible';
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

    // Login form
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

      const colorPicker = toolbar.querySelector('.toolbar-color');
      if (colorPicker) bindColorPicker(colorPicker, section);

      const visBtn = toolbar.querySelector('.toolbar-visibility');
      if (visBtn) bindVisibilityToggle(visBtn, section);

      toolbar.querySelectorAll('.toolbar-image').forEach(btn => {
        btn.addEventListener('click', () => handleImageUpload(btn.dataset.imageTarget));
      });
    });
  });

})();
