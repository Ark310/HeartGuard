(function () {
  'use strict';

  const navbar    = document.getElementById('navbar');
  const heroWatch = document.querySelector('.hero-watch');

  // ── Scroll progress bar ───────────────────────────────
  const progressBar = document.createElement('div');
  progressBar.className = 'scroll-progress';
  document.body.prepend(progressBar);

  // ── Scroll-driven UI updates ──────────────────────────
  function updateNavbar() {
    navbar.classList.toggle('scrolled', window.scrollY > 60);
  }

  function updateProgress() {
    const scrollable = document.documentElement.scrollHeight - window.innerHeight;
    progressBar.style.width = scrollable > 0
      ? (window.scrollY / scrollable * 100) + '%'
      : '0%';
  }

  function updateParallax() {
    if (!heroWatch || window.scrollY >= window.innerHeight) return;
    heroWatch.style.transform = `translateY(${window.scrollY * 0.12}px)`;
  }

  // Single rAF-batched scroll handler — no jank
  let rafId = null;
  function onScroll() {
    if (rafId) return;
    rafId = requestAnimationFrame(() => {
      updateNavbar();
      updateProgress();
      updateParallax();
      rafId = null;
    });
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  updateNavbar();
  updateProgress();

  // ── Hamburger menu ───────────────────────────────────
  const hamburger = document.getElementById('hamburger');
  const navLinks  = document.getElementById('nav-links');

  hamburger.addEventListener('click', () => {
    const isOpen = navLinks.classList.toggle('open');
    hamburger.classList.toggle('active', isOpen);
    hamburger.setAttribute('aria-expanded', String(isOpen));
  });

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
      const adminBar  = document.getElementById('admin-bar');
      const adminBarH = document.body.classList.contains('admin-mode') ? adminBar.offsetHeight : 0;
      const offset    = target.getBoundingClientRect().top + window.scrollY - navbar.offsetHeight - adminBarH - 16;
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

})();
