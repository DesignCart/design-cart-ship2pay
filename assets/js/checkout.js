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

  var cfg = window.dcShip2pay;
  if (!cfg || !cfg.enabled) {
    return;
  }

  function pushRateId(ids, value) {
    if (typeof value === 'string' && value) {
      ids.push(value);
      return;
    }
    if (value && typeof value === 'object') {
      var rateId = value.rate_id || value.rateId || value.id;
      if (rateId) {
        ids.push(String(rateId));
      }
    }
  }

  function selectedRateIds(args) {
    var ids = [];
    var selected = args && (args.selectedShippingMethods || (args.shippingData && args.shippingData.selectedRates));

    if (selected && typeof selected === 'object') {
      Object.keys(selected).forEach(function (key) {
        pushRateId(ids, selected[key]);
      });
    }

    if (!ids.length && window.wp && wp.data && wp.data.select) {
      var cart = wp.data.select('wc/store/cart');
      if (cart && typeof cart.getShippingRates === 'function') {
        (cart.getShippingRates() || []).forEach(function (pkg) {
          var rates = pkg.shipping_rates || pkg.shippingRates || [];
          rates.forEach(function (rate) {
            if (rate.selected || rate.isSelected) {
              pushRateId(ids, rate.rate_id || rate.rateId || '');
            }
          });
        });
      }
    }

    return ids;
  }

  function isAllowed(paymentId, rateIds) {
    if (!rateIds.length) {
      return true;
    }

    return rateIds.every(function (rateId) {
      var row = cfg.map && cfg.map[rateId];
      if (!row) {
        return true;
      }
      if (typeof row[paymentId] === 'undefined') {
        return true;
      }
      return !!row[paymentId];
    });
  }

  function registerBlocksCallbacks() {
    var registry = window.wc && wc.wcBlocksRegistry;
    if (!registry || typeof registry.registerPaymentMethodExtensionCallbacks !== 'function') {
      return;
    }

    var callbacks = {};
    var ids = (cfg.gateways || []).slice();
    Object.keys(cfg.map || {}).forEach(function (rateId) {
      Object.keys(cfg.map[rateId] || {}).forEach(function (gatewayId) {
        if (ids.indexOf(gatewayId) === -1) {
          ids.push(gatewayId);
        }
      });
    });

    ids.forEach(function (gatewayId) {
      callbacks[gatewayId] = function (args) {
        return isAllowed(gatewayId, selectedRateIds(args));
      };
    });

    try {
      registry.registerPaymentMethodExtensionCallbacks('design-cart-ship2pay', callbacks);
    } catch (e) {
      // Payment methods may not be registered yet in some builds.
    }
  }

  function boot() {
    registerBlocksCallbacks();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
