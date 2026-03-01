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
