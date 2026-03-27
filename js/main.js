/**
 * Milan Revival Church - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {

  // ========== Theme Toggle ==========
  var themeToggle = document.getElementById('themeToggle');
  if (themeToggle) {
    themeToggle.addEventListener('click', function () {
      var current = document.documentElement.getAttribute('data-theme');
      var next = current === 'light' ? 'dark' : 'light';
      if (next === 'dark') {
        document.documentElement.removeAttribute('data-theme');
        localStorage.removeItem('theme');
      } else {
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);
      }
    });
  }

  // ========== Mobile Menu ==========
  const menuToggle = document.getElementById('menuToggle');
  const mobileMenu = document.getElementById('mobileMenu');

  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', function () {
      menuToggle.classList.toggle('active');
      mobileMenu.classList.toggle('active');
      document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
    });

    // Close menu when clicking a link
    mobileMenu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        menuToggle.classList.remove('active');
        mobileMenu.classList.remove('active');
        document.body.style.overflow = '';
      });
    });
  }

  // ========== Header Scroll Effect ==========
  const header = document.getElementById('siteHeader');
  let lastScroll = 0;

  // On non-front-page pages, always show solid header background
  var isInnerPage = document.body.classList.contains('blog') ||
                    document.body.classList.contains('single') ||
                    document.body.classList.contains('archive') ||
                    (document.body.classList.contains('page') && !document.body.classList.contains('home'));

  if (isInnerPage && header) {
    header.classList.add('scrolled');
  }

  function handleScroll() {
    const currentScroll = window.pageYOffset;

    if (header && !isInnerPage) {
      if (currentScroll > 60) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    }

    lastScroll = currentScroll;
  }

  window.addEventListener('scroll', handleScroll, { passive: true });

  // ========== Smooth Scroll for Anchor Links ==========
  document.querySelectorAll('a[data-scroll]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      var href = this.getAttribute('href');
      var hashIndex = href.indexOf('#');
      if (hashIndex === -1) return;
      var targetId = href.substring(hashIndex);

      var target = document.querySelector(targetId);
      if (target) {
        e.preventDefault();
        var headerHeight = header ? header.offsetHeight : 0;
        var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;
        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
      }
    });
  });

  // ========== Scroll to hash on page load (cross-page navigation) ==========
  if (window.location.hash) {
    var hashTarget = document.querySelector(window.location.hash);
    if (hashTarget && header) {
      setTimeout(function () {
        var headerHeight = header.offsetHeight;
        var targetPosition = hashTarget.getBoundingClientRect().top + window.pageYOffset - headerHeight;
        window.scrollTo({ top: targetPosition, behavior: 'smooth' });
      }, 100);
    }
  }

  // ========== Scroll Reveal Animations ==========
  var fadeElements = document.querySelectorAll('.fade-in');

  var observerOptions = {
    root: null,
    rootMargin: '0px 0px -60px 0px',
    threshold: 0.1
  };

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  fadeElements.forEach(function (el) {
    observer.observe(el);
  });

  // ========== YouTube: live stream fallback to latest video ==========
  var ytFrames = [document.getElementById('ytFrame'), document.getElementById('ytFramePage')];
  ytFrames.forEach(function (ytFrame) {
    if (ytFrame && typeof milanRevival !== 'undefined') {
      setTimeout(function () {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', milanRevival.ajaxUrl + '?action=get_latest_video', true);
        xhr.onload = function () {
          if (xhr.status === 200) {
            try {
              var res = JSON.parse(xhr.responseText);
              if (res.success && res.data.video_id) {
                ytFrame.src = 'https://www.youtube.com/embed/' + res.data.video_id + '?rel=0';
              }
            } catch (e) {}
          }
        };
        xhr.send();
      }, 2000);
    }
  });

  // ========== Visitor Form AJAX Submit ==========
  var visitorForm = document.getElementById('visitorForm');
  if (visitorForm && typeof milanRevival !== 'undefined') {
    visitorForm.addEventListener('submit', function (e) {
      e.preventDefault();

      var submitBtn = document.getElementById('visitorSubmitBtn');
      var formMessage = document.getElementById('formMessage');
      var originalText = submitBtn.textContent;

      submitBtn.disabled = true;
      submitBtn.textContent = '提交中...';
      formMessage.className = 'form-message';
      formMessage.textContent = '';

      var formData = new FormData(visitorForm);

      var xhr = new XMLHttpRequest();
      xhr.open('POST', milanRevival.ajaxUrl, true);
      xhr.onload = function () {
        try {
          var res = JSON.parse(xhr.responseText);
          if (res.success) {
            formMessage.textContent = res.data;
            formMessage.className = 'form-message form-message-success';
            visitorForm.reset();
          } else {
            formMessage.textContent = res.data || '提交失败，请稍后再试。';
            formMessage.className = 'form-message form-message-error';
          }
        } catch (err) {
          formMessage.textContent = '提交失败，请稍后再试。';
          formMessage.className = 'form-message form-message-error';
        }
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      };
      xhr.onerror = function () {
        formMessage.textContent = '网络错误，请检查网络后重试。';
        formMessage.className = 'form-message form-message-error';
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      };
      xhr.send(formData);
    });
  }

  // ========== Hero Parallax (subtle) ==========
  var heroImage = document.querySelector('.hero-image');
  if (heroImage) {
    window.addEventListener('scroll', function () {
      var scrolled = window.pageYOffset;
      if (scrolled < window.innerHeight) {
        heroImage.style.transform = 'translateY(' + (scrolled * 0.3) + 'px)';
      }
    }, { passive: true });
  }

});
