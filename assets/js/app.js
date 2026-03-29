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

// Responsive navbar toggle + submenu control
const navToggle = document.querySelector('.nav-toggle');
const navMenu = document.getElementById('navMenu');
const menuGroups = document.querySelectorAll('.menu-group');

navToggle?.addEventListener('click', () => {
  const willOpen = !navMenu?.classList.contains('open');
  navMenu?.classList.toggle('open', willOpen);
  navToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
});

menuGroups.forEach((group) => {
  const trigger = group.querySelector('.menu-trigger');
  trigger?.addEventListener('click', () => {
    if (window.innerWidth > 640) return;
    const willOpen = !group.classList.contains('open');
    menuGroups.forEach((item) => {
      item.classList.remove('open');
      const itemTrigger = item.querySelector('.menu-trigger');
      itemTrigger?.setAttribute('aria-expanded', 'false');
    });
    group.classList.toggle('open', willOpen);
    trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
  });
});

document.querySelectorAll('.nav-menu a').forEach((link) => {
  link.addEventListener('click', () => {
    if (window.innerWidth > 640) return;
    navMenu?.classList.remove('open');
    navToggle?.setAttribute('aria-expanded', 'false');
    menuGroups.forEach((group) => {
      group.classList.remove('open');
      const trigger = group.querySelector('.menu-trigger');
      trigger?.setAttribute('aria-expanded', 'false');
    });
  });
});

// Layanan chart + AJAX filter (no full page reload)
const layananFilterForm = document.getElementById('layananFilterForm');
const chartLayananEl = document.getElementById('chartLayanan');
let layananChart = null;

const renderLayananChart = (items) => {
  if (!chartLayananEl || typeof Chart === 'undefined') return;
  const labels = items.map((item) => `${item.opd} (${item.bulan}/${item.tahun})`);

  if (!layananChart) {
    layananChart = new Chart(chartLayananEl, {
      type: 'bar',
      data: {
        labels: [],
        datasets: [
          { label: 'Realisasi Fisik (%)', data: [], backgroundColor: '#004a99' },
          { label: 'Realisasi Keuangan (%)', data: [], backgroundColor: '#00a3d7' }
        ]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });
  }

  layananChart.data.labels = labels;
  layananChart.data.datasets[0].data = items.map((item) => Number(item.realisasi_fisik || 0));
  layananChart.data.datasets[1].data = items.map((item) => Number(item.realisasi_keuangan || 0));
  layananChart.update();
};

const initialLayananData = Array.isArray(window.initialLayananData) ? window.initialLayananData : [];
if (chartLayananEl) {
  renderLayananChart(initialLayananData);
}

if (layananFilterForm) {
  const requestLayananData = async () => {
    const params = new URLSearchParams(new FormData(layananFilterForm));
    params.delete('page');

    try {
      const res = await fetch(`layanan_data.php?${params.toString()}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const payload = await res.json();
      if (!res.ok || !payload.ok || !Array.isArray(payload.data)) return;
      renderLayananChart(payload.data);
    } catch (err) {
      // Keep current chart data on network error.
    }
  };

  layananFilterForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    await requestLayananData();
  });

  layananFilterForm.querySelectorAll('select').forEach((field) => {
    field.addEventListener('change', () => {
      requestLayananData();
    });
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

// Protected document download flow
const protectedDocButtons = document.querySelectorAll('.btn-protected-doc');
protectedDocButtons.forEach((btn) => {
  btn.addEventListener('click', async () => {
    const docId = btn.dataset.id;
    const password = prompt('Masukkan password dokumen terbatas:');
    if (!password) {
      alert('Password wajib diisi untuk dokumen terbatas.');
      return;
    }

    try {
      const formData = new FormData();
      formData.append('id', docId);
      formData.append('password', password);

      const res = await fetch('download_dokumen.php', {
        method: 'POST',
        body: formData,
      });

      if (!res.ok) {
        alert('Password salah, silahkan meminta Akses melalui Kontak Resmi.');
        return;
      }

      const blob = await res.blob();
      const objectUrl = URL.createObjectURL(blob);
      const tempLink = document.createElement('a');
      tempLink.href = objectUrl;

      const disposition = res.headers.get('Content-Disposition') || '';
      const match = disposition.match(/filename="?([^";]+)"?/i);
      tempLink.download = match ? match[1] : `dokumen-${docId}`;

      document.body.appendChild(tempLink);
      tempLink.click();
      document.body.removeChild(tempLink);
      URL.revokeObjectURL(objectUrl);
    } catch (err) {
      alert('Terjadi kesalahan saat mengunduh dokumen. Silakan coba lagi.');
    }
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
