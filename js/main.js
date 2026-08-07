/**
 * AppForge Pro — Main JavaScript
 * Vanilla JS, no jQuery dependency.
 */
(function () {
  'use strict';

  // ============================================================
  // CAROUSEL
  // ============================================================
  var carousel = (function () {
    var container = document.getElementById('featuredCarousel');
    if (!container) return;

    var slides   = container.querySelectorAll('.carousel-slide');
    var dots     = container.querySelectorAll('.carousel-dot');
    var prevBtn  = container.querySelector('#carouselPrev');
    var nextBtn  = container.querySelector('#carouselNext');
    var current  = 0;
    var total    = slides.length;
    var interval = null;
    var DELAY    = 5000;

    function goTo(index) {
      slides[current].classList.remove('active');
      if (dots[current]) {
        dots[current].classList.remove('active');
        dots[current].setAttribute('aria-selected', 'false');
      }
      current = (index + total) % total;
      slides[current].classList.add('active');
      if (dots[current]) {
        dots[current].classList.add('active');
        dots[current].setAttribute('aria-selected', 'true');
      }
    }

    function startAuto() {
      interval = setInterval(function () { goTo(current + 1); }, DELAY);
    }

    function stopAuto() {
      clearInterval(interval);
    }

    if (prevBtn) prevBtn.addEventListener('click', function () { stopAuto(); goTo(current - 1); startAuto(); });
    if (nextBtn) nextBtn.addEventListener('click', function () { stopAuto(); goTo(current + 1); startAuto(); });

    dots.forEach(function (dot) {
      dot.addEventListener('click', function () {
        stopAuto();
        goTo(parseInt(this.dataset.slide, 10));
        startAuto();
      });
    });

    // Pause on hover / focus
    container.addEventListener('mouseenter', stopAuto);
    container.addEventListener('mouseleave', startAuto);
    container.addEventListener('focusin',    stopAuto);
    container.addEventListener('focusout',   startAuto);

    startAuto();
  }());

  // ============================================================
  // CATEGORY HERO CAROUSEL — 3-up scroll row (category archive pages)
  // ============================================================
  (function () {
    var section = document.getElementById('catHeroCarousel');
    if (!section) return;

    var track   = section.querySelector('[data-cat-hero-track]');
    var prevBtn = section.querySelector('[data-cat-hero-prev]');
    var nextBtn = section.querySelector('[data-cat-hero-next]');
    if (!track) return;

    function scrollByCard(dir) {
      var card = track.querySelector('.cat-hero-card');
      if (!card) return;
      var trackStyle = window.getComputedStyle(track);
      var gap = parseFloat(trackStyle.columnGap || trackStyle.gap) || 0;
      track.scrollBy({ left: dir * (card.getBoundingClientRect().width + gap), behavior: 'smooth' });
    }

    if (prevBtn) prevBtn.addEventListener('click', function () { scrollByCard(-1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { scrollByCard(1); });
  }());

  // ============================================================
  // INLINE HEADER SEARCH — small bar, expands to center on focus
  // ============================================================
  var navSearch = (function () {
    var bar    = document.getElementById('navSearchBar');
    var nav    = document.getElementById('site-navigation');
    var input  = bar ? bar.querySelector('input[type="search"]') : null;
    var isOpen = false;

    if (!bar || !input) return;

    function open() {
      if (isOpen) return;
      isOpen = true;
      bar.classList.add('is-expanded');
      if (nav) nav.setAttribute('hidden', '');
    }

    function close() {
      if (!isOpen) return;
      isOpen = false;
      bar.classList.remove('is-expanded');
      if (nav) nav.removeAttribute('hidden');
    }

    input.addEventListener('focus', open);

    // Collapse once focus leaves the bar entirely (covers outside clicks
    // and tabbing away) — checked a tick later so the new activeElement
    // has settled, since opening synchronously shifts layout mid-click.
    bar.addEventListener('focusout', function () {
      setTimeout(function () {
        if (!bar.contains(document.activeElement)) close();
      }, 0);
    });

    // Collapse on Escape
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && isOpen) { close(); input.blur(); }
    });
  }());

  // ============================================================
  // CATEGORY BAR — scroll fade hints (left/right edges)
  // ============================================================
  (function () {
    var bar   = document.querySelector('.cat-nav-bar');
    var inner = document.querySelector('.cat-nav-inner');
    if (!bar || !inner) return;

    function update() {
      var maxScroll = inner.scrollWidth - inner.clientWidth;
      bar.classList.toggle('has-scroll-left', inner.scrollLeft > 1);
      bar.classList.toggle('has-scroll-right', inner.scrollLeft < maxScroll - 1);
    }

    inner.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    update();
  }());

  // ============================================================
  // BACK TO TOP
  // ============================================================
  var backToTop = (function () {
    var btn = document.getElementById('backToTop');
    if (!btn) return;

    function toggle() {
      if (window.scrollY > 400) {
        btn.classList.add('visible');
        btn.removeAttribute('hidden');
      } else {
        btn.classList.remove('visible');
        setTimeout(function () {
          if (!btn.classList.contains('visible')) {
            btn.setAttribute('hidden', '');
          }
        }, 300);
      }
    }

    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    window.addEventListener('scroll', toggle, { passive: true });
    toggle();
  }());

  // ============================================================
  // SMOOTH SCROLL (anchor links)
  // ============================================================
  document.querySelectorAll('a[href^="#"]').forEach(function (link) {
    link.addEventListener('click', function (e) {
      var target = document.querySelector(this.getAttribute('href'));
      if (!target) return;
      e.preventDefault();
      var offset = 70 + 16; // nav height + spacing
      var top = target.getBoundingClientRect().top + window.scrollY - offset;
      window.scrollTo({ top: top, behavior: 'smooth' });
      target.setAttribute('tabindex', '-1');
      target.focus({ preventScroll: true });
    });
  });

  // ============================================================
  // LAZY IMAGES — IntersectionObserver polyfill-free fade-in
  // ============================================================
  if ('IntersectionObserver' in window) {
    var imgObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          imgObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('img[loading="lazy"]').forEach(function (img) {
      img.style.opacity = '0';
      img.style.transition = 'opacity 0.4s ease';
      img.addEventListener('load', function () { this.style.opacity = '1'; });
      imgObserver.observe(img);
    });
  }

  // ============================================================
  // READ MORE — App description collapse/expand
  // ============================================================
  document.querySelectorAll('.app-read-more').forEach(function (btn) {
    var targetId = btn.dataset.target;
    var target   = targetId ? document.getElementById(targetId) : null;
    if (!target) return;

    btn.addEventListener('click', function () {
      var expanded = target.classList.toggle('expanded');
      btn.textContent = expanded ? 'Read less' : 'Read more';
    });
  });

  // ============================================================
  // TABLE OF CONTENTS — collapsible toggle
  // ============================================================
  document.querySelectorAll('.toc').forEach(function (toc) {
    var toggle = toc.querySelector('.toc__toggle');
    if (!toggle) return;

    toggle.addEventListener('click', function () {
      var expanded = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      toc.classList.toggle('is-collapsed', expanded);
    });
  });

  // ============================================================
  // CATEGORY SECTION TABS — Latest Update / Downloads / Rating
  // ============================================================
  document.querySelectorAll('.cat-tabs').forEach(function (tabs) {
    var section = tabs.closest('.cat-section');
    if (!section) return;
    var buttons = tabs.querySelectorAll('.cat-tab');
    var panels  = section.querySelectorAll('.cat-tab-panel');

    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var target = btn.dataset.tab;

        buttons.forEach(function (b) {
          b.classList.toggle('active', b === btn);
          b.setAttribute('aria-selected', b === btn ? 'true' : 'false');
        });
        panels.forEach(function (panel) {
          var match = panel.dataset.panel === target;
          panel.classList.toggle('active', match);
          if (match) panel.removeAttribute('hidden');
          else panel.setAttribute('hidden', '');
        });
      });
    });
  });

  // ============================================================
  // CARD HOVER — Tilt effect on app cards (subtle)
  // ============================================================
  document.querySelectorAll('.app-card, .post-card').forEach(function (card) {
    card.addEventListener('mousemove', function (e) {
      var rect = this.getBoundingClientRect();
      var x = (e.clientX - rect.left) / rect.width  - 0.5;
      var y = (e.clientY - rect.top)  / rect.height - 0.5;
      this.style.transform = 'translateY(-4px) rotateX(' + (-y * 4) + 'deg) rotateY(' + (x * 4) + 'deg)';
    });
    card.addEventListener('mouseleave', function () {
      this.style.transform = '';
    });
  });

  // ============================================================
  // REPORT MODAL (single-app.php)
  // ============================================================
  (function () {
    var modal = document.getElementById('appReportModal');
    if (!modal) return;

    var form     = modal.querySelector('#appReportForm');
    var msg      = modal.querySelector('.report-modal__msg');
    var submit   = modal.querySelector('.btn-report-submit');
    var openBtns = document.querySelectorAll('[data-report-open]');

    function open() {
      modal.removeAttribute('hidden');
    }

    function close() {
      modal.setAttribute('hidden', '');
      form.reset();
      msg.textContent = '';
      submit.disabled = false;
    }

    openBtns.forEach(function (btn) { btn.addEventListener('click', open); });

    modal.querySelectorAll('[data-report-close]').forEach(function (el) {
      el.addEventListener('click', close);
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !modal.hasAttribute('hidden')) close();
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (typeof appforgeData === 'undefined') return;

      submit.disabled = true;
      msg.textContent = '';

      var data = new FormData(form);
      data.append('action', 'appforge_report_app');
      data.append('nonce', appforgeData.nonce);

      fetch(appforgeData.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' })
        .then(function (res) { return res.json(); })
        .then(function (json) {
          msg.textContent = (json.data && json.data.message) || '';
          if (json.success) {
            setTimeout(close, 1800);
          } else {
            submit.disabled = false;
          }
        })
        .catch(function () {
          msg.textContent = 'Something went wrong. Please try again.';
          submit.disabled = false;
        });
    });
  }());

}());
