/* ============================
   SELECTORS
============================ */
const nav           = document.querySelector('nav');
const mobileToggle  = document.querySelector('[data-mobile-toggle]');
const mobileMenu    = document.querySelector('[data-mobile-menu]');
const revealItems   = document.querySelectorAll('.reveal');
const skillBars     = document.querySelectorAll('.skill-bar');
const typingTarget  = document.querySelector('[data-typing]');
const filterButtons = document.querySelectorAll('[data-filter]');
const projectCards  = document.querySelectorAll('.project-card');
const themeToggle   = document.querySelector('[data-theme-toggle]');
const themeLabel    = document.querySelector('[data-theme-label]');
const scrollBar     = document.getElementById('scroll-progress');
const cursorGlow    = document.getElementById('cursor-glow');
const navLinks      = document.querySelectorAll('.nav-link[data-section]');
const sections      = document.querySelectorAll('section[id]');
const counters      = document.querySelectorAll('[data-counter]');

/* ============================
   THEME
============================ */
const applyTheme = (mode) => {
  document.body.classList.toggle('light', mode === 'light');
  if (themeLabel) themeLabel.textContent = mode === 'light' ? '☀️' : '🌙';
};

applyTheme(localStorage.getItem('theme') || 'dark');

if (themeToggle) {
  themeToggle.addEventListener('click', () => {
    const next = document.body.classList.contains('light') ? 'dark' : 'light';
    localStorage.setItem('theme', next);
    applyTheme(next);
  });
}

/* ============================
   SCROLL PROGRESS BAR
============================ */
if (scrollBar) {
  window.addEventListener('scroll', () => {
    const scrolled = window.scrollY;
    const total = document.documentElement.scrollHeight - window.innerHeight;
    scrollBar.style.width = (total > 0 ? (scrolled / total) * 100 : 0) + '%';
  }, { passive: true });
}

/* ============================
   CURSOR GLOW
============================ */
if (cursorGlow) {
  window.addEventListener('mousemove', (e) => {
    cursorGlow.style.left = e.clientX + 'px';
    cursorGlow.style.top  = e.clientY + 'px';
  }, { passive: true });
}

/* ============================
   NAV — SCROLL STYLE + ACTIVE SECTION
============================ */
if (nav) {
  window.addEventListener('scroll', () => {
    nav.classList.toggle('nav-scrolled', window.scrollY > 20);
  }, { passive: true });
}

if (mobileToggle && mobileMenu) {
  mobileToggle.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
  // Close mobile menu on nav link click
  mobileMenu.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => mobileMenu.classList.add('hidden'));
  });
}

// Active section highlight in nav
if (navLinks.length && sections.length) {
  const sectionObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const id = entry.target.id;
          navLinks.forEach((link) =>
            link.classList.toggle('active-section', link.dataset.section === id)
          );
        }
      });
    },
    { threshold: 0.35, rootMargin: '-60px 0px -60px 0px' }
  );
  sections.forEach((s) => sectionObserver.observe(s));
}

/* ============================
   REVEAL ANIMATIONS
============================ */
const revealObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) entry.target.classList.add('visible');
    });
  },
  { threshold: 0.15 }
);
revealItems.forEach((item) => revealObserver.observe(item));

/* ============================
   SKILL BARS
============================ */
const skillsObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const bar = entry.target;
        setTimeout(() => { bar.style.width = bar.dataset.level + '%'; }, 100);
      }
    });
  },
  { threshold: 0.3 }
);
skillBars.forEach((bar) => skillsObserver.observe(bar));

/* ============================
   COUNTER ANIMATION
============================ */
if (counters.length) {
  const counterObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const el      = entry.target;
        const target  = parseInt(el.dataset.counter, 10) || 0;
        const suffix  = el.dataset.suffix || '';
        const dur     = 1400;
        const start   = performance.now();

        const tick = (now) => {
          const t = Math.min((now - start) / dur, 1);
          const ease = 1 - Math.pow(1 - t, 3);
          el.textContent = Math.round(ease * target) + suffix;
          if (t < 1) requestAnimationFrame(tick);
        };

        requestAnimationFrame(tick);
        counterObserver.unobserve(el);
      });
    },
    { threshold: 0.5 }
  );
  counters.forEach((c) => counterObserver.observe(c));
}

/* ============================
   TYPING ANIMATION
============================ */
if (typingTarget) {
  const roles = JSON.parse(typingTarget.dataset.roles || '[]');
  let roleIndex = 0;
  let charIndex = 0;
  let isDeleting = false;

  const tick = () => {
    const current   = roles[roleIndex] || '';
    const displayed = isDeleting
      ? current.substring(0, charIndex - 1)
      : current.substring(0, charIndex + 1);
    typingTarget.textContent = displayed;

    if (!isDeleting && displayed === current) {
      isDeleting = true;
      setTimeout(tick, 1400);
      return;
    }

    if (isDeleting && displayed === '') {
      isDeleting = false;
      roleIndex = (roleIndex + 1) % roles.length;
    }

    charIndex = isDeleting ? charIndex - 1 : charIndex + 1;
    setTimeout(tick, isDeleting ? 38 : 75);
  };

  tick();
}

/* ============================
   PROJECT FILTERING
============================ */
filterButtons.forEach((button) => {
  button.addEventListener('click', () => {
    const filter = button.dataset.filter;
    filterButtons.forEach((btn) => btn.classList.remove('is-active'));
    button.classList.add('is-active');

    projectCards.forEach((card) => {
      const tags       = card.dataset.tags.split(',');
      const isFeatured = card.dataset.featured === '1';
      const match =
        filter === 'all' ||
        (filter === 'featured' && isFeatured) ||
        tags.includes(filter);

      card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
      if (match) {
        card.classList.remove('hidden');
        card.style.opacity = '1';
        card.style.transform = '';
      } else {
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        setTimeout(() => card.classList.add('hidden'), 300);
      }
    });
  });
});

/* ============================
   CONTACT FORM
============================ */
const contactForm = document.querySelector('[data-contact-form]');
if (contactForm) {
  contactForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const btn = contactForm.querySelector('[type="submit"]');
    const orig = btn.textContent;
    btn.textContent = '...';
    btn.disabled = true;

    try {
      const res  = await fetch('/portfolio/api/contact.php', { method: 'POST', body: new FormData(contactForm) });
      const data = await res.json();
      showToast(data.message, data.success);
      if (data.success) contactForm.reset();
    } catch {
      showToast('Something went wrong. Please try again.', false);
    } finally {
      btn.textContent = orig;
      btn.disabled = false;
    }
  });
}

function showToast(message, success) {
  const toast = document.createElement('div');
  toast.className = `toast fixed bottom-6 right-6 px-6 py-4 rounded-2xl text-white text-sm font-medium shadow-2xl z-50 backdrop-blur-md ${
    success ? 'bg-teal-500/90 border border-teal-400/40' : 'bg-red-500/90 border border-red-400/40'
  }`;
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(8px)'; }, 3600);
  setTimeout(() => toast.remove(), 4000);
}
