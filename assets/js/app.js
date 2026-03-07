document.querySelectorAll('[data-confirm]').forEach((el) => {
  el.addEventListener('click', (e) => {
    if (!confirm(el.dataset.confirm)) {
      e.preventDefault();
    }
  });
});

// Entry animation on each refresh
if (document.body && !document.body.classList.contains('admin-page')) {
  document.body.classList.add('page-enter');
  window.addEventListener('load', () => {
    requestAnimationFrame(() => {
      document.body.classList.add('page-enter-active');
    });
  });
}

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
const sliders = document.querySelectorAll('[data-structure-slider]');

sliders.forEach((slider) => {
  const targetId = slider.getAttribute('id');
  const prevBtn = document.querySelector(`[data-slide="prev"][data-target="${targetId}"]`);
  const nextBtn = document.querySelector(`[data-slide="next"][data-target="${targetId}"]`);

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

  requestAnimationFrame(() => {
    slider.scrollLeft = Math.max(0, slider.scrollWidth - slider.clientWidth);
  });

  let autoSlide = setInterval(slideRight, 4500);
  slider.addEventListener('mouseenter', () => clearInterval(autoSlide));
  slider.addEventListener('mouseleave', () => {
    autoSlide = setInterval(slideRight, 4500);
  });
});

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

// Protected document download flow
const protectedDocButtons = document.querySelectorAll('.btn-protected-doc');
protectedDocButtons.forEach((btn) => {
  btn.addEventListener('click', () => {
    const docId = btn.dataset.id;
    const password = prompt('Masukkan password dokumen terbatas:');
    if (!password) {
      alert('Password wajib diisi untuk dokumen terbatas.');
      return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'download_dokumen.php';
    form.target = '_blank';

    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'id';
    idInput.value = docId;

    const passInput = document.createElement('input');
    passInput.type = 'hidden';
    passInput.name = 'password';
    passInput.value = password;

    form.appendChild(idInput);
    form.appendChild(passInput);
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
  });
});


// Rich text editor for admin kegiatan
const editor = document.getElementById('kontenEditor');
const kontenInput = document.getElementById('kontenInput');
const kegiatanForm = document.getElementById('kegiatanForm');
const fontSizeSelect = document.getElementById('fontSizeSelect');

if (editor && kontenInput && kegiatanForm) {
  document.querySelectorAll('[data-editor-cmd]').forEach((btn) => {
    btn.addEventListener('click', () => {
      document.execCommand(btn.dataset.editorCmd, false, null);
      editor.focus();
    });
  });

  fontSizeSelect?.addEventListener('change', () => {
    document.execCommand('fontSize', false, fontSizeSelect.value);
    editor.focus();
  });

  kegiatanForm.addEventListener('submit', () => {
    kontenInput.value = editor.innerHTML;
  });
}
