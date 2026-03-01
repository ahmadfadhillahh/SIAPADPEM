document.querySelectorAll('[data-confirm]').forEach((el) => {
  el.addEventListener('click', (e) => {
    if (!confirm(el.dataset.confirm)) {
      e.preventDefault();
    }
  });
});

// Entry animation on each refresh
if (document.body) {
  document.body.classList.add('page-enter');
  window.addEventListener('load', () => {
    requestAnimationFrame(() => {
      document.body.classList.add('page-enter-active');
    });
  });
}


// Theme toggle (light/dark)
const root = document.documentElement;
const themeToggle = document.getElementById('themeToggle');
const savedTheme = localStorage.getItem('siapadpem-theme');

if (savedTheme) {
  root.setAttribute('data-theme', savedTheme);
}

const updateThemeLabel = () => {
  if (!themeToggle) return;
  const dark = root.getAttribute('data-theme') === 'dark';
  themeToggle.textContent = dark ? '☀️ Mode Terang' : '🌙 Mode Gelap';
};
updateThemeLabel();

themeToggle?.addEventListener('click', () => {
  const current = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
  root.setAttribute('data-theme', current === 'light' ? '' : 'dark');
  if (current === 'light') {
    localStorage.removeItem('siapadpem-theme');
  } else {
    localStorage.setItem('siapadpem-theme', 'dark');
  }
  updateThemeLabel();
});

// Navbar shadow on scroll
const navbar = document.querySelector('.navbar');
if (navbar) {
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 10);
  });
}

// Reveal effect on scroll
const revealItems = document.querySelectorAll('.reveal');
if (revealItems.length) {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('active');
      }
    });
  }, { threshold: 0.2 });

  revealItems.forEach((item) => observer.observe(item));
}

// Struktur slider controls + auto-slide to the right
const slider = document.getElementById('strukturSlider');
const prevBtn = document.querySelector('[data-slide="prev"]');
const nextBtn = document.querySelector('[data-slide="next"]');

if (slider) {
  const getStep = () => Math.max(240, Math.floor(slider.clientWidth * 0.75));
  prevBtn?.addEventListener('click', () => slider.scrollBy({ left: -getStep(), behavior: 'smooth' }));
  nextBtn?.addEventListener('click', () => slider.scrollBy({ left: getStep(), behavior: 'smooth' }));

  const slideRight = () => {
    const maxLeft = slider.scrollWidth - slider.clientWidth;
    if (maxLeft <= 0) return;

    if (slider.scrollLeft <= 10) {
      slider.scrollTo({ left: maxLeft, behavior: 'smooth' });
      return;
    }

    slider.scrollBy({ left: -getStep(), behavior: 'smooth' });
  };

  // Start from end so card movement appears to the right.
  requestAnimationFrame(() => {
    slider.scrollLeft = Math.max(0, slider.scrollWidth - slider.clientWidth);
  });

  let autoSlide = setInterval(slideRight, 4500);

  slider.addEventListener('mouseenter', () => clearInterval(autoSlide));
  slider.addEventListener('mouseleave', () => {
    autoSlide = setInterval(slideRight, 4500);
  });
}


// Login modal on-click (without navigating to login.php page)
const loginButton = document.getElementById('loginButton');
const loginModal = document.getElementById('loginModal');
const loginForm = document.getElementById('loginForm');
const loginMsg = document.getElementById('loginMsg');

const toggleLoginModal = (show) => {
  if (!loginModal) return;
  loginModal.classList.toggle('show', show);
  loginModal.setAttribute('aria-hidden', show ? 'false' : 'true');
};

loginButton?.addEventListener('click', () => {
  toggleLoginModal(true);
});

document.querySelectorAll('[data-close-login]').forEach((el) => {
  el.addEventListener('click', () => toggleLoginModal(false));
});

loginForm?.addEventListener('submit', async (e) => {
  e.preventDefault();
  if (loginMsg) loginMsg.textContent = 'Memproses login...';

  try {
    const formData = new FormData(loginForm);
    const res = await fetch('admin/login_action.php', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });

    const data = await res.json();
    if (!res.ok || !data.ok) {
      if (loginMsg) loginMsg.textContent = data.message || 'Login gagal';
      return;
    }

    window.location.href = data.redirect || 'admin/index.php';
  } catch (err) {
    if (loginMsg) loginMsg.textContent = 'Terjadi kesalahan koneksi';
  }
});

