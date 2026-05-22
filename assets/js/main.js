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
