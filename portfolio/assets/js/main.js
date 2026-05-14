const nav = document.querySelector('nav');
const mobileToggle = document.querySelector('[data-mobile-toggle]');
const mobileMenu = document.querySelector('[data-mobile-menu]');
const revealItems = document.querySelectorAll('.reveal');
const skillBars = document.querySelectorAll('.skill-bar');
const typingTarget = document.querySelector('[data-typing]');
const filterButtons = document.querySelectorAll('[data-filter]');
const projectCards = document.querySelectorAll('.project-card');
const themeToggle = document.querySelector('[data-theme-toggle]');
const themeLabel = document.querySelector('[data-theme-label]');

const applyTheme = (mode) => {
  document.body.classList.toggle('light', mode === 'light');
  if (themeLabel) {
    themeLabel.textContent = mode === 'light' ? '☀️' : '🌙';
  }
};

const savedTheme = localStorage.getItem('theme') || 'dark';
applyTheme(savedTheme);

if (themeToggle) {
  themeToggle.addEventListener('click', () => {
    const next = document.body.classList.contains('light') ? 'dark' : 'light';
    localStorage.setItem('theme', next);
    applyTheme(next);
  });
}

if (nav) {
  window.addEventListener('scroll', () => {
    if (window.scrollY > 20) {
      nav.classList.add('nav-scrolled');
    } else {
      nav.classList.remove('nav-scrolled');
    }
  });
}

if (mobileToggle && mobileMenu) {
  mobileToggle.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
  });
}

const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
      }
    });
  },
  { threshold: 0.2 }
);

revealItems.forEach((item) => observer.observe(item));

const skillsObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const bar = entry.target;
        bar.style.width = bar.dataset.level + '%';
      }
    });
  },
  { threshold: 0.3 }
);

skillBars.forEach((bar) => skillsObserver.observe(bar));

if (typingTarget) {
  const roles = JSON.parse(typingTarget.dataset.roles || '[]');
  let roleIndex = 0;
  let charIndex = 0;
  let isDeleting = false;

  const tick = () => {
    const current = roles[roleIndex] || '';
    const displayed = isDeleting
      ? current.substring(0, charIndex - 1)
      : current.substring(0, charIndex + 1);
    typingTarget.textContent = displayed;

    if (!isDeleting && displayed === current) {
      isDeleting = true;
      setTimeout(tick, 1200);
      return;
    }

    if (isDeleting && displayed === '') {
      isDeleting = false;
      roleIndex = (roleIndex + 1) % roles.length;
    }

    charIndex = isDeleting ? charIndex - 1 : charIndex + 1;
    setTimeout(tick, isDeleting ? 40 : 80);
  };

  tick();
}

filterButtons.forEach((button) => {
  button.addEventListener('click', () => {
    const filter = button.dataset.filter;
    filterButtons.forEach((btn) => btn.classList.remove('bg-teal-500', 'text-black'));
    button.classList.add('bg-teal-500', 'text-black');

    projectCards.forEach((card) => {
      const tags = card.dataset.tags.split(',');
      const isFeatured = card.dataset.featured === '1';
      const match =
        filter === 'all' ||
        (filter === 'featured' && isFeatured) ||
        tags.includes(filter);
      card.classList.toggle('hidden', !match);
    });
  });
});

const contactForm = document.querySelector('[data-contact-form]');
if (contactForm) {
  contactForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const formData = new FormData(contactForm);
    try {
      const response = await fetch('/portfolio/api/contact.php', {
        method: 'POST',
        body: formData,
      });
      const data = await response.json();
      showToast(data.message, data.success);
      if (data.success) {
        contactForm.reset();
      }
    } catch (error) {
      showToast('Something went wrong. Please try again.', false);
    }
  });
}

function showToast(message, success) {
  const toast = document.createElement('div');
  toast.className = `toast fixed bottom-6 right-6 px-6 py-4 rounded-xl text-white font-medium shadow-lg z-50 ${
    success ? 'bg-teal-500' : 'bg-red-500'
  }`;
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 4000);
}
