</main>

<?php include __DIR__ . '/footer-partial.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {

    /* ── Navbar scroll effect (glass-morphism shadow) ── */
    var topbar = document.getElementById('topbarNav');
    if (topbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 40) {
                topbar.classList.add('scrolled');
            } else {
                topbar.classList.remove('scrolled');
            }
        });
    }

    /* ── Scroll Reveal (IntersectionObserver) ── */
    var reveals = document.querySelectorAll('.reveal');
    if (reveals.length && 'IntersectionObserver' in window) {
        var revealObs = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
        reveals.forEach(function(el) { revealObs.observe(el); });
    } else {
        /* Fallback: show all immediately */
        reveals.forEach(function(el) { el.classList.add('visible'); });
    }

    /* ── Stat Card Counter Animation with easing ── */
    var statValues = document.querySelectorAll('.stat-value[data-count]');
    if (statValues.length && 'IntersectionObserver' in window) {
        var counterObs = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var el = entry.target;
                    var target = parseInt(el.getAttribute('data-count'), 10);
                    if (isNaN(target) || target === 0) return;
                    el.textContent = '0';
                    var current = 0;
                    var totalFrames = 40;
                    var frame = 0;
                    var timer = setInterval(function() {
                        frame++;
                        /* Ease-out cubic */
                        var progress = 1 - Math.pow(1 - frame / totalFrames, 3);
                        current = Math.round(progress * target);
                        el.textContent = current;
                        if (frame >= totalFrames) {
                            el.textContent = target;
                            clearInterval(timer);
                        }
                    }, 18);
                    counterObs.unobserve(el);
                }
            });
        }, { threshold: 0.5 });
        statValues.forEach(function(el) { counterObs.observe(el); });
    }

    /* ── Button Ripple Effect ── */
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn');
        if (!btn) return;
        var rect = btn.getBoundingClientRect();
        var x = ((e.clientX - rect.left) / rect.width * 100).toFixed(1);
        var y = ((e.clientY - rect.top) / rect.height * 100).toFixed(1);
        btn.style.setProperty('--ripple-x', x + '%');
        btn.style.setProperty('--ripple-y', y + '%');
    });

    /* ── Upload Zone Click-to-Trigger ── */
    var zones = document.querySelectorAll('.upload-zone');
    zones.forEach(function(zone) {
        var fileInput = zone.querySelector('input[type="file"]');
        if (!fileInput) return;
        fileInput.style.display = 'none';
        zone.addEventListener('click', function() { fileInput.click(); });
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length) {
                zone.querySelector('.upload-text').textContent = '📎 ' + fileInput.files[0].name;
                zone.querySelector('.upload-hint').textContent = 'Klik untuk ganti file';
                zone.style.borderColor = '#2AA198';
                zone.style.background = '#E0F5F3';
            }
        });
    });

    /* ── Stagger children utility ── */
    var staggers = document.querySelectorAll('.stagger');
    staggers.forEach(function(container) {
        var children = container.children;
        for (var i = 0; i < children.length; i++) {
            children[i].style.animationDelay = (i * 0.06) + 's';
        }
    });
});
</script>

</body>
</html>
