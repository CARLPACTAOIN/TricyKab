import './bootstrap';
import './admin/maps';

import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';

window.Alpine = Alpine;
window.ApexCharts = ApexCharts;

Alpine.start();

// ---------------------------------------------------------------------------
// Global, consistent delete confirmation (Stitch admin layout)
// ---------------------------------------------------------------------------
(() => {
  const modal = document.getElementById('confirm-delete-modal');
  if (!modal) return;

  const backdrop = document.getElementById('confirm-delete-backdrop');
  const btnClose = document.getElementById('confirm-delete-close');
  const btnCancel = document.getElementById('confirm-delete-cancel');
  const btnConfirm = document.getElementById('confirm-delete-confirm');
  const msg = document.getElementById('confirm-delete-message');

  /** @type {HTMLFormElement | null} */
  let pendingForm = null;

  function close() {
    modal.classList.add('hidden');
    pendingForm = null;
  }

  function open(form, message) {
    pendingForm = form;
    if (msg) msg.textContent = message;
    modal.classList.remove('hidden');
    btnConfirm?.focus?.();
  }

  btnClose?.addEventListener('click', close);
  btnCancel?.addEventListener('click', close);
  backdrop?.addEventListener('click', close);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) close();
  });

  btnConfirm?.addEventListener('click', () => {
    const form = pendingForm;
    close();
    if (form) form.submit();
  });

  document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (!form.hasAttribute('data-confirm-delete')) return;

    e.preventDefault();

    const item = form.getAttribute('data-confirm-delete-item') || 'this item';
    const message = `Delete ${item}? This cannot be undone.`;
    open(form, message);
  });
})();
