/**
 * Back to top — mobile only (≤799). Shows after scroll threshold.
 * Desktop: CSS keeps .iawp-back-to-top { display:none }.
 */
(function () {
  "use strict";

  var mq =
    window.matchMedia && window.matchMedia("(max-width: 799px)");
  var btn = document.querySelector(".iawp-back-to-top");
  if (!btn) return;

  var THRESHOLD = 320;
  var reduce =
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var ticking = false;

  function mobile() {
    return !mq || mq.matches;
  }

  function y() {
    return window.scrollY || window.pageYOffset || 0;
  }

  function sync() {
    if (!mobile()) {
      btn.classList.remove("is-visible");
      return;
    }
    btn.classList.toggle("is-visible", y() > THRESHOLD);
  }

  function onScroll() {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(function () {
      sync();
      ticking = false;
    });
  }

  btn.addEventListener("click", function (e) {
    var href = btn.getAttribute("href") || "#main-content";
    var id = href.charAt(0) === "#" ? href.slice(1) : "";
    var target = id ? document.getElementById(id) : null;
    if (target) {
      e.preventDefault();
      if (reduce) {
        target.scrollIntoView();
      } else {
        target.scrollIntoView({ behavior: "smooth", block: "start" });
      }
      if (typeof target.focus === "function") {
        try {
          target.setAttribute("tabindex", "-1");
          target.focus({ preventScroll: true });
        } catch (err) {
          /* ignore */
        }
      }
    }
  });

  window.addEventListener("scroll", onScroll, { passive: true });
  if (mq && typeof mq.addEventListener === "function") {
    mq.addEventListener("change", sync);
  } else if (mq && typeof mq.addListener === "function") {
    mq.addListener(sync);
  }
  sync();
})();
