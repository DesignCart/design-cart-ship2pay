/**
 * Design Cart Ship2pay
 *
 * Firma: Design Cart
 * Url firmy: https://www.designcart.pl
 * Autor: Paweł Nosko
 * Url autora: https://www.designcart.pl/pawel-nosko.html
 */
(function () {
  'use strict';

  function qs(sel, root) {
    return (root || document).querySelector(sel);
  }

  function qsAll(sel, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(sel));
  }

  function escAttr(value) {
    if (window.CSS && typeof CSS.escape === 'function') {
      return CSS.escape(value);
    }
    return String(value).replace(/["\\]/g, '\\$&');
  }

  function setActiveShip(root, rateId) {
    qsAll('.dc-ship2pay-ship', root).forEach(function (btn) {
      var on = btn.getAttribute('data-ship') === rateId;
      btn.classList.toggle('is-active', on);
      btn.setAttribute('aria-pressed', on ? 'true' : 'false');
    });

    qsAll('.dc-ship2pay-panel', root).forEach(function (panel) {
      var on = panel.getAttribute('data-ship-panel') === rateId;
      panel.classList.toggle('is-active', on);
      if (on) {
        panel.removeAttribute('hidden');
      } else {
        panel.setAttribute('hidden', 'hidden');
      }
    });
  }

  function updateCount(root, rateId) {
    var panel = qs('.dc-ship2pay-panel[data-ship-panel="' + escAttr(rateId) + '"]', root);
    var badge = qs('.dc-ship2pay-count[data-ship-count="' + escAttr(rateId) + '"]', root);
    if (!panel || !badge) {
      return;
    }
    var boxes = qsAll('.dc-toggle__input[type="checkbox"]', panel);
    var on = boxes.filter(function (el) { return el.checked; }).length;
    badge.textContent = on + '/' + boxes.length;
  }

  function boot() {
    var root = qs('[data-dc-ship2pay-mapper]');
    if (!root) {
      return;
    }

    qsAll('.dc-ship2pay-ship', root).forEach(function (btn) {
      btn.addEventListener('click', function () {
        var rateId = btn.getAttribute('data-ship');
        if (rateId) {
          setActiveShip(root, rateId);
        }
      });
    });

    qsAll('.dc-ship2pay-panel', root).forEach(function (panel) {
      panel.addEventListener('change', function () {
        var rateId = panel.getAttribute('data-ship-panel');
        if (rateId) {
          updateCount(root, rateId);
        }
      });
    });

    qsAll('.dc-ship2pay-bulk', root).forEach(function (btn) {
      btn.addEventListener('click', function () {
        var turnOn = btn.getAttribute('data-bulk') === 'on';
        var panel = qs('.dc-ship2pay-panel.is-active', root);
        if (!panel) {
          return;
        }
        qsAll('.dc-toggle__input[type="checkbox"]', panel).forEach(function (el) {
          el.checked = turnOn;
        });
        var rateId = panel.getAttribute('data-ship-panel');
        if (rateId) {
          updateCount(root, rateId);
        }
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
