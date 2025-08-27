// Main interactions: mobile nav, scroll reveal, scrollspy, year
(function () {
  const d = document;

  // Theme toggle removed — default theme is set in HTML attribute

  // Year
  const y = d.getElementById('year');
  if (y) y.textContent = new Date().getFullYear();

  // Mobile nav
  const toggle = d.querySelector('.nav__toggle');
  const menu = d.getElementById('navMenu');
  if (toggle && menu) {
    toggle.addEventListener('click', () => {
      const expanded = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(!expanded));
      menu.classList.toggle('is-open');
    });

    // Close on outside click
    d.addEventListener('click', (e) => {
      if (!menu.classList.contains('is-open')) return;
      if (e.target instanceof Element && !menu.contains(e.target) && !toggle.contains(e.target)) {
        toggle.setAttribute('aria-expanded', 'false');
        menu.classList.remove('is-open');
      }
    });

    // Close on link click (mobile)
    menu.querySelectorAll('a').forEach((a) => {
      a.addEventListener('click', () => {
        toggle.setAttribute('aria-expanded', 'false');
        menu.classList.remove('is-open');
      });
    });
  }

  // Theme toggle removed

  // Scroll reveal using IntersectionObserver
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!prefersReduced && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      for (const entry of entries) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      }
    }, { threshold: 0.15 });

    d.querySelectorAll('.reveal').forEach((el) => io.observe(el));
  } else {
    d.querySelectorAll('.reveal').forEach((el) => el.classList.add('is-visible'));
  }

  // Scrollspy for nav
  const sections = Array.from(d.querySelectorAll('main > section[id]'));
  const navLinks = Array.from(d.querySelectorAll('.nav__menu a[href^="#"]'));
  const byId = (id) => sections.find((s) => s.id === id);

  const setActive = (id) => {
    navLinks.forEach((a) => a.removeAttribute('aria-current'));
    const link = navLinks.find((a) => a.getAttribute('href') === '#' + id);
    if (link) link.setAttribute('aria-current', 'page');
  };

  const spy = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) setActive(entry.target.id);
    });
  }, { rootMargin: '-40% 0px -55% 0px', threshold: 0.01 });

  sections.forEach((s) => spy.observe(s));
})();

// Updates data loader: card grid or slider fallback
(function () {
  const d = document;
  const grid = d.getElementById('updatesList');
  const slider = d.querySelector('.slider');
  const viewport = slider?.querySelector('.slider__viewport');
  const track = slider?.querySelector('.slider__track');
  const dotsWrap = slider?.querySelector('.slider__dots');

  async function getUpdates() {
    try {
      const local = localStorage.getItem('updates');
      if (local) return JSON.parse(local);
    } catch {}
    try {
      const res = await fetch('assets/data/updates.json', { cache: 'no-store' });
      if (!res.ok) throw new Error('Failed to fetch updates');
      return await res.json();
    } catch (e) {
      console.error(e);
      return [];
    }
  }

  function sortByDateDesc(arr) {
    try {
      return [...arr].sort((a, b) => {
        const da = new Date(a.displayDate || a.date || 0).getTime();
        const db = new Date(b.displayDate || b.date || 0).getTime();
        return isNaN(db) - isNaN(da) || db - da;
      });
    } catch { return arr; }
  }

  function fmtDateTime(v) {
    try {
      const d = new Date(v);
      if (isNaN(d.getTime())) return v;
      return d.toLocaleString(undefined, {
        year: 'numeric', month: 'short', day: '2-digit',
        hour: '2-digit', minute: '2-digit'
      });
    } catch { return v; }
  }

  function renderGrid(updates) {
    if (!grid) return;
    grid.innerHTML = '';
    if (!Array.isArray(updates) || updates.length === 0) {
      const empty = d.createElement('div');
      empty.className = 'empty';
      empty.setAttribute('role', 'note');
      empty.textContent = 'Henüz duyuru yok.';
      grid.appendChild(empty);
      return;
    }
    sortByDateDesc(updates).forEach((u) => {
      const art = d.createElement('article');
      art.className = 'card reveal';
      art.setAttribute('role', 'listitem');
      art.innerHTML = `
        <header class="meta">
          <h3>${u.title}</h3>
          ${u.date ? `<time datetime="${u.date}">${fmtDateTime(u.displayDate || u.date)}</time>` : ''}
        </header>
        ${u.summary ? `<p>${u.summary}</p>` : ''}
        ${u.link ? `<div class="card__actions"><a class="link" href="${u.link}">${u.linkText || 'Read more'}</a></div>` : ''}
      `;
      grid.appendChild(art);
    });
  }

  function renderSlider(updates) {
    if (!slider) return;
    track.innerHTML = '';
    dotsWrap.innerHTML = '';
    updates.forEach((u, i) => {
      const li = d.createElement('li');
      li.className = 'slide' + (i === 0 ? ' is-active' : '');
      li.setAttribute('aria-label', `${i + 1} of ${updates.length}`);
      li.innerHTML = `
        <article class="card">
          <header>
            <h3>${u.title}</h3>
            <time datetime="${u.date}">${fmtDateTime(u.displayDate || u.date)}</time>
          </header>
          ${u.summary ? `<p>${u.summary}</p>` : ''}
          ${u.link ? `<div class="card__actions"><a class="link" href="${u.link}">${u.linkText || 'Read more'}</a></div>` : ''}
        </article>`;
      track.appendChild(li);

      const dot = d.createElement('button');
      dot.className = 'dot' + (i === 0 ? ' is-active' : '');
      dot.setAttribute('role', 'tab');
      dot.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
      dot.setAttribute('aria-controls', 'updatesViewport');
      dot.setAttribute('aria-label', `Slide ${i + 1}`);
      dotsWrap.appendChild(dot);
    });
  }

  function initSlider() {
    const slides = Array.from(slider.querySelectorAll('.slide'));
    const prevBtn = slider.querySelector('.slider__btn.prev');
    const nextBtn = slider.querySelector('.slider__btn.next');
    const dots = Array.from(slider.querySelectorAll('.dot'));

    let index = 0;
    let timer = null;
    let interval = 6000;
    try {
      const fromLS = localStorage.getItem('updates_interval');
      interval = Number(fromLS || slider.getAttribute('data-interval') || 6000) || 6000;
    } catch { interval = Number(slider.getAttribute('data-interval') || 6000) || 6000; }
    const autoplayEnabled = slider.getAttribute('data-autoplay') === 'true';
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function update() {
      const offset = -index * 100;
      track.style.transform = `translateX(${offset}%)`;
      slides.forEach((s, i) => {
        const active = i === index;
        s.classList.toggle('is-active', active);
        s.setAttribute('aria-hidden', active ? 'false' : 'true');
        s.setAttribute('aria-label', `${i + 1} of ${slides.length}`);
      });
      dots.forEach((d, i) => {
        const active = i === index;
        d.classList.toggle('is-active', active);
        d.setAttribute('aria-selected', active ? 'true' : 'false');
      });
    }

    function goTo(i) { index = (i + slides.length) % slides.length; update(); }
    function next() { goTo(index + 1); }
    function prev() { goTo(index - 1); }

    function startAutoplay() {
      if (!autoplayEnabled || prefersReduced) return;
      stopAutoplay();
      timer = setInterval(next, interval);
    }
    function stopAutoplay() { if (timer) { clearInterval(timer); timer = null; } }

    nextBtn?.addEventListener('click', () => { next(); stopAutoplay(); startAutoplay(); });
    prevBtn?.addEventListener('click', () => { prev(); stopAutoplay(); startAutoplay(); });
    dots.forEach((dot, i) => dot.addEventListener('click', () => { goTo(i); stopAutoplay(); startAutoplay(); }));
    viewport?.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowRight') { e.preventDefault(); next(); stopAutoplay(); startAutoplay(); }
      if (e.key === 'ArrowLeft') { e.preventDefault(); prev(); stopAutoplay(); startAutoplay(); }
    });
    ['mouseenter', 'focusin'].forEach((ev) => slider.addEventListener(ev, stopAutoplay));
    ['mouseleave', 'focusout'].forEach((ev) => slider.addEventListener(ev, startAutoplay));

    let startX = 0; let dx = 0; let isTouching = false;
    track.addEventListener('touchstart', (e) => {
      isTouching = true; startX = e.touches[0].clientX; dx = 0; stopAutoplay();
    }, { passive: true });
    track.addEventListener('touchmove', (e) => { if (!isTouching) return; dx = e.touches[0].clientX - startX; }, { passive: true });
    track.addEventListener('touchend', () => {
      if (!isTouching) return; isTouching = false;
      if (Math.abs(dx) > 40) { if (dx < 0) next(); else prev(); }
      startAutoplay();
    });

    update();
    startAutoplay();
  }

  async function loadAndRender() {
    const data = await getUpdates();
    if (grid) {
      renderGrid(data);
    } else if (slider) {
      if (!Array.isArray(data) || data.length === 0) return;
      renderSlider(sortByDateDesc(data));
      initSlider();
    }
  }

  (async function () { await loadAndRender(); })();

  // Live update when updates in localStorage change (editing in admin.html)
  window.addEventListener('storage', (e) => {
    if (e.key === 'updates' || e.key === 'updates_touch') {
      loadAndRender();
    }
  });

  // Reload when returning to the tab or regaining focus
  document.addEventListener('visibilitychange', () => { if (!document.hidden) loadAndRender(); });
  window.addEventListener('focus', () => { loadAndRender(); });
})();
