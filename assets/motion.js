/* Motion — reveal no scroll + spotlight (discreto; respeita reduced-motion) */
(function () {
  "use strict";

  var reduce =
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  document.querySelectorAll(".spotlight, .bento-item").forEach(function (item) {
    item.addEventListener("mousemove", function (e) {
      var rect = item.getBoundingClientRect();
      var x = (((e.clientX - rect.left) / rect.width) * 100).toFixed(1) + "%";
      var y = (((e.clientY - rect.top) / rect.height) * 100).toFixed(1) + "%";
      item.style.setProperty("--mx", x);
      item.style.setProperty("--my", y);
    });
  });

  document.documentElement.classList.add("js-motion");
  if (reduce) {
    document.documentElement.classList.add("js-motion-reduce");
    return;
  }

  var selectors = [
    ".page-hero .wrap > *",
    ".section-header",
    ".posts-hub > *",
    ".posts-hub__grid > *",
    ".bento-item",
    ".sobre-pillar",
    ".about-cols > *",
    ".cta-inner > *",
    ".archive-foot",
    ".error-404__inner > *",
    ".single-header__inner > *",
    ".single-content-col > *",
    ".single-sidebar-sticky > *",
  ];

  var vh = window.innerHeight;
  var observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add("is-inview");
        observer.unobserve(entry.target);
      });
    },
    { root: null, rootMargin: "0px 0px -8% 0px", threshold: 0.12 }
  );

  var seen = typeof WeakSet !== "undefined" ? new WeakSet() : null;
  var parentIdx = typeof WeakMap !== "undefined" ? new WeakMap() : null;

  document.querySelectorAll(selectors.join(",")).forEach(function (el) {
    if (seen) {
      if (seen.has(el)) return;
      seen.add(el);
    }
    if (el.closest && el.closest(".hero__inner")) return;

    var delay = 0;
    if (parentIdx && el.parentElement) {
      var n = parentIdx.get(el.parentElement) || 0;
      delay = Math.min(n * 60, 240);
      parentIdx.set(el.parentElement, n + 1);
      el.style.setProperty("--reveal-delay", delay + "ms");
    }

    el.classList.add("reveal");
    var rect = el.getBoundingClientRect();
    if (rect.top < vh * 0.92 && rect.bottom > 0) {
      el.classList.add("is-inview");
    } else {
      observer.observe(el);
    }
  });
})();
