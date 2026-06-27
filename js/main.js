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
  // MOBILE NAVIGATION
  // ============================================================
  var mobileNav = (function () {
    var btn    = document.getElementById('mobileMenuBtn');
    var nav    = document.getElementById('mobileNav');
    var isOpen = false;

    if (!btn || !nav) return;

    function open() {
      isOpen = true;
      btn.classList.add('active');
      btn.setAttribute('aria-expanded', 'true');
      nav.removeAttribute('hidden');
      document.body.style.overflow = 'hidden';
    }

    function close() {
      isOpen = false;
      btn.classList.remove('active');
      btn.setAttribute('aria-expanded', 'false');
      nav.setAttribute('hidden', '');
      document.body.style.overflow = '';
    }

    btn.addEventListener('click', function () {
      isOpen ? close() : open();
    });

    // Close on outside click
    document.addEventListener('click', function (e) {
      if (isOpen && !nav.contains(e.target) && !btn.contains(e.target)) {
        close();
      }
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && isOpen) { close(); btn.focus(); }
    });
  }());

  // ============================================================
  // SEARCH OVERLAY
  // ============================================================
  var searchOverlay = (function () {
    var toggleBtn = document.getElementById('searchToggle');
    var overlay   = document.getElementById('searchOverlay');
    var closeBtn  = document.getElementById('searchClose');
    var input     = overlay ? overlay.querySelector('input[type="search"]') : null;
    var isOpen    = false;

    if (!toggleBtn || !overlay) return;

    function open() {
      isOpen = true;
      overlay.removeAttribute('hidden');
      overlay.classList.add('active');
      toggleBtn.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
      setTimeout(function () { if (input) input.focus(); }, 100);
    }

    function close() {
      isOpen = false;
      overlay.classList.remove('active');
      toggleBtn.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
      setTimeout(function () {
        overlay.setAttribute('hidden', '');
        toggleBtn.focus();
      }, 300);
    }

    toggleBtn.addEventListener('click', open);
    if (closeBtn) closeBtn.addEventListener('click', close);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && isOpen) close();
    });

    // Close on backdrop click
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) close();
    });
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

}());
