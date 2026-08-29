document.addEventListener('DOMContentLoaded', () => {
  const body = document.body;
  const menuBtn = document.getElementById('menuBtn');
  const mobileNav = document.getElementById('mobileNav');
  const closeMobileNav = document.getElementById('closeMobileNav');
  const navOverlay = document.getElementById('navOverlay');
  const siteHeader = document.getElementById('siteHeader');

  const setMenu = (open) => {
    if (!menuBtn || !mobileNav) return;
    menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    mobileNav.setAttribute('aria-hidden', open ? 'false' : 'true');
    mobileNav.classList.toggle('is-open', open);
    navOverlay?.classList.toggle('is-open', open);
    body.classList.toggle('menu-open', open);
  };

  menuBtn?.addEventListener('click', () => setMenu(!mobileNav.classList.contains('is-open')));
  closeMobileNav?.addEventListener('click', () => setMenu(false));
  navOverlay?.addEventListener('click', () => setMenu(false));
  mobileNav?.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setMenu(false)));
  document.addEventListener('keydown', e => { if (e.key === 'Escape') setMenu(false); });

  const updateHeader = () => siteHeader?.classList.toggle('scrolled', window.scrollY > 12);
  updateHeader();
  window.addEventListener('scroll', updateHeader, { passive: true });

  // Database-driven hero slider. No popups and no repeated anniversary banners.
  const slides = [...document.querySelectorAll('.hero-slide')];
  const dots = [...document.querySelectorAll('.hero-dot')];
  if (slides.length) {
    const animations = ['slide-in-left', 'slide-in-right', 'zoom-in', 'fade-up'];
    let current = Math.max(0, slides.findIndex(s => s.classList.contains('active')));
    const show = (index) => {
      current = (index + slides.length) % slides.length;
      slides.forEach((slide, i) => {
        slide.classList.toggle('active', i === current);
        slide.classList.remove(...animations);
        if (i === current) {
          void slide.offsetWidth;
          slide.classList.add(animations[Math.floor(Math.random() * animations.length)]);
        }
      });
      dots.forEach((dot, i) => dot.classList.toggle('is-active', i === current));
    };
    dots.forEach(dot => dot.addEventListener('click', () => show(Number(dot.dataset.slide))));
    show(current);
    if (slides.length > 1) setInterval(() => show(current + 1), 6500);
  }
});
