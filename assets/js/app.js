(() => {
  'use strict';
  document.body.classList.add('js-ready');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const menuToggle = document.querySelector('[data-menu-toggle]');
  const overlay = document.querySelector('[data-close-menu]');
  const closeMenu = () => document.body.classList.remove('menu-open');
  if (menuToggle) menuToggle.addEventListener('click', () => document.body.classList.toggle('menu-open'));
  if (overlay) overlay.addEventListener('click', closeMenu);
  document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeMenu(); });

  document.querySelectorAll('.ripple-btn, .action-icon').forEach((control) => {
    control.addEventListener('click', (event) => {
      if (reducedMotion) return;
      const bounds = control.getBoundingClientRect();
      const bubble = document.createElement('span');
      bubble.className = 'ripple';
      bubble.style.left = `${event.clientX ? event.clientX - bounds.left : bounds.width / 2}px`;
      bubble.style.top = `${event.clientY ? event.clientY - bounds.top : bounds.height / 2}px`;
      control.appendChild(bubble);
      bubble.addEventListener('animationend', () => bubble.remove(), { once: true });
    });
  });

  document.querySelectorAll('[data-dismiss]').forEach((button) => {
    button.addEventListener('click', () => {
      const alert = button.closest('[data-dismissible]');
      if (!alert) return;
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-8px)';
      setTimeout(() => alert.remove(), reducedMotion ? 0 : 220);
    });
  });
  document.querySelectorAll('[data-toggle-password]').forEach((button) => {
    button.addEventListener('click', () => {
      const input = document.getElementById(button.dataset.togglePassword);
      if (!input) return;
      const reveal = input.type === 'password';
      input.type = reveal ? 'text' : 'password';
      button.textContent = reveal ? 'Tutup' : 'Lihat';
      button.setAttribute('aria-label', reveal ? 'Sembunyikan password' : 'Tampilkan password');
    });
  });
  document.querySelectorAll('[data-loading-form]').forEach((form) => {
    form.addEventListener('submit', () => {
      const submitButton = form.querySelector('button[type=submit]');
      if (!submitButton) return;
      submitButton.disabled = true;
      submitButton.style.opacity = '.7';
      // Browser melakukan POST setelah handler selesai; tidak menginterupsi validasi.
    });
  });
  if ('IntersectionObserver' in window && !reducedMotion) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => { if (entry.isIntersecting) { entry.target.classList.add('is-visible'); observer.unobserve(entry.target); } });
    }, { threshold: .08 });
    document.querySelectorAll('.reveal').forEach((element) => observer.observe(element));
  } else document.querySelectorAll('.reveal').forEach((element) => element.classList.add('is-visible'));
  if (!reducedMotion) {
    document.querySelectorAll('[data-count]').forEach((element) => {
      const target = Number(element.dataset.count);
      if (!Number.isSafeInteger(target) || target < 0 || target > 100000) return;
      const start = performance.now();
      const duration = 850;
      const paint = (now) => {
        const t = Math.min((now - start) / duration, 1);
        element.textContent = String(Math.round(target * (1 - Math.pow(1 - t, 3))));
        if (t < 1) requestAnimationFrame(paint);
      };
      requestAnimationFrame(paint);
    });
  }
  const dialog = document.getElementById('confirmDialog');
  if (dialog && typeof dialog.showModal === 'function') {
    let pending = null;
    document.querySelectorAll('form[data-confirm-name]').forEach((form) => {
      form.addEventListener('submit', (event) => {
        if (form.dataset.confirmed === 'yes') return;
        event.preventDefault();
        pending = form;
        const name = form.dataset.confirmName;
        document.getElementById('confirmMessage').textContent = `Data ${name} akan dihapus permanen dari database. Tindakan ini tidak dapat dibatalkan.`;
        dialog.showModal();
      });
    });
    dialog.querySelector('[data-cancel-delete]').addEventListener('click', () => {pending=null;dialog.close();});
    dialog.querySelector('[data-approve-delete]').addEventListener('click', () => {
      if (pending) { pending.dataset.confirmed='yes'; pending.requestSubmit(); }
      dialog.close();
    });
    dialog.addEventListener('close', () => { pending = null; });
  }
})();
