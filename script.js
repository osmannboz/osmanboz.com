// Mobile menu toggle
const menuBtn = document.querySelector('.menu-toggle');
const nav = document.getElementById('site-nav');
const backdrop = document.getElementById('nav-backdrop');

function setMenuOpen(isOpen) {
  if (!menuBtn || !nav) return;
  nav.classList.toggle('open', isOpen);
  menuBtn.classList.toggle('is-open', isOpen);
  menuBtn.setAttribute('aria-expanded', String(isOpen));
  if (backdrop) backdrop.classList.toggle('open', isOpen);
  document.body.classList.toggle('scroll-lock', isOpen);
}

if (menuBtn && nav) {
  menuBtn.addEventListener('click', () => {
    const isOpen = !nav.classList.contains('open');
    setMenuOpen(isOpen);
  });

  // Close menu when a nav link is clicked (mobile UX)
  nav.addEventListener('click', (e) => {
    const target = e.target;
    if (target instanceof Element && target.tagName.toLowerCase() === 'a') {
      setMenuOpen(false);
    }
  });

  // Backdrop click closes
  if (backdrop) {
    backdrop.addEventListener('click', () => setMenuOpen(false));
  }

  // ESC key closes
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && nav.classList.contains('open')) setMenuOpen(false);
  });

  // On resize to desktop, reset menu
  window.addEventListener('resize', () => {
    if (window.innerWidth > 991 && nav.classList.contains('open')) {
      setMenuOpen(false);
    }
  });
}

// Footer year
const yearEl = document.getElementById('year');
if (yearEl) yearEl.textContent = new Date().getFullYear();

// Simple client-side validation with a11y-friendly feedback
const form = document.querySelector('.contact-form');
const statusEl = document.querySelector('.form-status');

function setInvalid(el, invalid) {
  el.setAttribute('aria-invalid', invalid ? 'true' : 'false');
}

if (form) {
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const name = form.querySelector('#name');
    const email = form.querySelector('#email');
    const message = form.querySelector('#message');

    let valid = true;

    if (!name.value.trim()) { setInvalid(name, true); valid = false; } else setInvalid(name, false);
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email.value)) { setInvalid(email, true); valid = false; } else setInvalid(email, false);
    if (!message.value.trim()) { setInvalid(message, true); valid = false; } else setInvalid(message, false);

    if (!valid) {
      if (statusEl) statusEl.textContent = 'Lütfen vurgulanan alanları düzeltin.';
      return;
    }

    // Demo: simulate success without backend
    if (statusEl) statusEl.textContent = 'Teşekkürler! Mesajınız gönderildi (demo).';
    form.reset();
    [name, email, message].forEach(el => setInvalid(el, false));
  });
}

// Respect reduced motion: no heavy JS animations used
const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function makeReveal() {
  const elements = document.querySelectorAll('.reveal');
  if (!elements.length) return;

  if (prefersReduced || !('IntersectionObserver' in window)) {
    elements.forEach(el => el.classList.add('is-visible'));
    return;
  }

  const io = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        obs.unobserve(entry.target);
      }
    });
  }, { root: null, rootMargin: '0px 0px -10% 0px', threshold: 0.15 });

  elements.forEach(el => io.observe(el));
}

// Init on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', makeReveal);
} else {
  makeReveal();
}
