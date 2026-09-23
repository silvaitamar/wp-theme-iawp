/* IA+WP – Scroll Reveal + Bento Spotlight */
(function () {
  'use strict';

  // ── Scroll Reveal ────────────────────────────────────────────────────────
  var revealEls = document.querySelectorAll('.reveal-up');
  if ('IntersectionObserver' in window) {
    revealEls.forEach(function (el) {
      el.classList.add('is-hidden');
    });
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var delay = entry.target.dataset.delay || 0;
          setTimeout(function () {
            entry.target.classList.remove('is-hidden');
            entry.target.classList.add('is-visible');
          }, delay);
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    revealEls.forEach(function (el) { io.observe(el); });
  }

  // ── Bento Spotlight ──────────────────────────────────────────────────────
  var bentoItems = document.querySelectorAll('.bento-item');
  bentoItems.forEach(function (item) {
    item.addEventListener('mousemove', function (e) {
      var rect = item.getBoundingClientRect();
      var x = ((e.clientX - rect.left) / rect.width * 100).toFixed(1) + '%';
      var y = ((e.clientY - rect.top) / rect.height * 100).toFixed(1) + '%';
      item.style.setProperty('--mx', x);
      item.style.setProperty('--my', y);
    });
  });


})();
