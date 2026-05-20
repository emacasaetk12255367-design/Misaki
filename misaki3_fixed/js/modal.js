/**
 * Misaki Custom Modal System
 * Replaces native alert() and confirm() with styled dialogs.
 */
(function () {
  'use strict';

  /* ── Toast Container ──────────────────────────────────────── */
  let toastWrap = null;
  function getToastWrap() {
    if (!toastWrap) {
      toastWrap = document.createElement('div');
      toastWrap.className = 'mk-toast-wrap';
      document.body.appendChild(toastWrap);
    }
    return toastWrap;
  }

  /**
   * Show a brief toast notification.
   * @param {string} msg
   * @param {'success'|'error'|'info'} type
   */
  function mkToast(msg, type = 'info') {
    const icons = { success: '✓', error: '✕', info: '❀' };
    const wrap = getToastWrap();
    const t = document.createElement('div');
    t.className = 'mk-toast';
    t.innerHTML = `<span class="mk-toast-icon">${icons[type] || '❀'}</span><span>${msg}</span>`;
    wrap.appendChild(t);
    setTimeout(() => {
      t.classList.add('out');
      t.addEventListener('animationend', () => t.remove(), { once: true });
    }, 3200);
  }

  /**
   * Show a styled confirm dialog.
   * Returns a Promise<boolean>.
   */
  function mkConfirm(msg, { title = 'Are you sure?', confirmText = 'Confirm', cancelText = 'Cancel', danger = false } = {}) {
    return new Promise((resolve) => {
      const overlay = document.createElement('div');
      overlay.className = 'mk-overlay';
      overlay.innerHTML = `
        <div class="mk-dialog" role="dialog" aria-modal="true">
          <div class="mk-dialog-icon">${danger ? '⚠️' : '❀'}</div>
          <h4>${title}</h4>
          <p>${msg}</p>
          <div class="mk-dialog-btns">
            <button class="mk-btn mk-btn-cancel">${cancelText}</button>
            <button class="mk-btn ${danger ? 'mk-btn-danger' : 'mk-btn-confirm'}">${confirmText}</button>
          </div>
        </div>`;
      document.body.appendChild(overlay);
      requestAnimationFrame(() => overlay.classList.add('open'));

      function close(result) {
        overlay.classList.remove('open');
        overlay.addEventListener('transitionend', () => overlay.remove(), { once: true });
        resolve(result);
      }

      overlay.querySelector('.mk-btn-cancel').addEventListener('click', () => close(false));
      overlay.querySelector(`.mk-btn.${danger ? 'mk-btn-danger' : 'mk-btn-confirm'}`).addEventListener('click', () => close(true));
      overlay.addEventListener('click', (e) => { if (e.target === overlay) close(false); });
      document.addEventListener('keydown', function esc(e) {
        if (e.key === 'Escape') { close(false); document.removeEventListener('keydown', esc); }
      });
    });
  }

  /**
   * Show a styled alert dialog.
   * Returns a Promise<void>.
   */
  function mkAlert(msg, { title = 'Notice', icon = '❀' } = {}) {
    return new Promise((resolve) => {
      const overlay = document.createElement('div');
      overlay.className = 'mk-overlay';
      overlay.innerHTML = `
        <div class="mk-dialog" role="dialog" aria-modal="true">
          <div class="mk-dialog-icon">${icon}</div>
          <h4>${title}</h4>
          <p>${msg}</p>
          <div class="mk-dialog-btns">
            <button class="mk-btn mk-btn-confirm">Got it</button>
          </div>
        </div>`;
      document.body.appendChild(overlay);
      requestAnimationFrame(() => overlay.classList.add('open'));

      function close() {
        overlay.classList.remove('open');
        overlay.addEventListener('transitionend', () => overlay.remove(), { once: true });
        resolve();
      }

      overlay.querySelector('.mk-btn-confirm').addEventListener('click', close);
      overlay.addEventListener('click', (e) => { if (e.target === overlay) close(); });
      document.addEventListener('keydown', function esc(e) {
        if (e.key === 'Escape') { close(); document.removeEventListener('keydown', esc); }
      });
    });
  }

  /* ── Intercept native confirm() calls via data attributes ──── */
  /* Forms using onsubmit="return confirm('...')" are patched below */
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-confirm]').forEach(form => {
      form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const msg  = form.dataset.confirm || 'Are you sure?';
        const isDanger = form.dataset.danger !== undefined;
        const ok = await mkConfirm(msg, { danger: isDanger, confirmText: isDanger ? 'Delete' : 'Confirm' });
        if (ok) form.submit();
      });
    });
  });

  /* ── Expose globally ─────────────────────────────────────────── */
  window.mkToast   = mkToast;
  window.mkConfirm = mkConfirm;
  window.mkAlert   = mkAlert;
})();
