/* ==========================================================================
   Biznie Admin — UI behaviour layer
   --------------------------------------------------------------------------
   Loaded after template.js. Responsibilities:
     1. Top progress bar for wire:navigate + Livewire roundtrips
     2. Sidebar quick-filter
     3. Contextual page title in the top bar
     4. Remember the folded/expanded sidebar state
     5. Re-initialise template plugins after a wire:navigate swap
        (feather icons, tooltips, select2, scrollbar, sidebar handlers)
   ========================================================================== */
(function () {
    'use strict';

    if (window.__bzAdmin) { return; }
    window.__bzAdmin = true;

    var FOLD_KEY = 'bz:sidebar-folded';
    var $ = window.jQuery;

    /* ----------------------------------------------------------------------
       1. Progress bar
       ---------------------------------------------------------------------- */
    var bar, barTimer, barValue = 0;

    function ensureBar() {
        bar = document.getElementById('bz-progress');
        if (!bar) {
            bar = document.createElement('div');
            bar.id = 'bz-progress';
            document.body.appendChild(bar);
        }
        return bar;
    }

    function startBar() {
        ensureBar();
        clearInterval(barTimer);
        barValue = 8;
        bar.classList.add('is-active');
        bar.style.width = barValue + '%';
        barTimer = setInterval(function () {
            // Ease towards 90% so the bar always feels alive but never completes early.
            barValue += Math.max(0.4, (90 - barValue) * 0.06);
            if (barValue > 90) { barValue = 90; }
            bar.style.width = barValue + '%';
        }, 120);
    }

    function stopBar() {
        clearInterval(barTimer);
        // A wire:navigate swap replaces <body>, so re-resolve the element.
        ensureBar();
        bar.classList.add('is-active');
        bar.style.width = '100%';
        setTimeout(function () {
            bar.classList.remove('is-active');
            setTimeout(function () { if (bar) { bar.style.width = '0%'; } }, 260);
        }, 140);
    }

    document.addEventListener('livewire:navigate', startBar);
    document.addEventListener('livewire:navigated', function () {
        stopBar();
        onNavigated();
    });

    /* ----------------------------------------------------------------------
       2. Sidebar quick-filter
       ---------------------------------------------------------------------- */
    function filterSidebar(query) {
        var body = document.querySelector('.sidebar .sidebar-body');
        var root = body && body.querySelector('.nav');
        if (!root) { return; }

        var wrap = body.querySelector('.bz-sb-search');
        var empty = body.querySelector('.bz-sb-empty');
        var q = (query || '').trim().toLowerCase();

        if (wrap) { wrap.classList.toggle('is-filled', q.length > 0); }

        // Reset
        body.querySelectorAll('.bz-hidden').forEach(function (el) { el.classList.remove('bz-hidden'); });

        if (!q) {
            body.classList.remove('is-filtering');
            if (empty) { empty.classList.remove('is-visible'); }
            return;
        }

        body.classList.add('is-filtering');
        var visible = 0;

        Array.prototype.forEach.call(root.children, function (node) {
            if (node.classList.contains('nav-category')) { return; }

            if (node.classList.contains('collapse')) {
                // Handled together with its trigger below.
                return;
            }
            if (!node.classList.contains('nav-item')) { return; }

            var link = node.querySelector(':scope > .nav-link');
            if (!link) { node.classList.add('bz-hidden'); return; }

            var label = (link.textContent || '').trim().toLowerCase();
            var parentHit = label.indexOf(q) !== -1;

            // Find the collapse panel this item toggles.
            var href = link.getAttribute('href') || '';
            var panel = href.charAt(0) === '#' ? root.querySelector(href) : null;

            if (!panel) {
                node.classList.toggle('bz-hidden', !parentHit);
                if (parentHit) { visible++; }
                return;
            }

            var childHit = 0;
            panel.querySelectorAll('.sub-menu > .nav-item').forEach(function (child) {
                var text = (child.textContent || '').trim().toLowerCase();
                var hit = parentHit || text.indexOf(q) !== -1;
                child.classList.toggle('bz-hidden', !hit);
                if (text.indexOf(q) !== -1) { childHit++; }
            });

            var show = parentHit || childHit > 0;
            node.classList.toggle('bz-hidden', !show);
            panel.classList.toggle('bz-hidden', !show);
            if (show) { visible++; }
        });

        if (empty) { empty.classList.toggle('is-visible', visible === 0); }
    }

    // Delegated so it survives Livewire DOM swaps.
    document.addEventListener('input', function (e) {
        if (e.target && e.target.matches('.bz-sb-search input')) {
            filterSidebar(e.target.value);
        }
    });

    document.addEventListener('click', function (e) {
        var clear = e.target.closest && e.target.closest('.bz-sb-search__clear');
        if (!clear) { return; }
        e.preventDefault();
        var input = clear.parentElement.querySelector('input');
        if (input) { input.value = ''; input.focus(); }
        filterSidebar('');
    });

    document.addEventListener('keydown', function (e) {
        var input = document.querySelector('.bz-sb-search input');
        if (!input) { return; }

        // "/" focuses the filter unless the user is already typing somewhere.
        var tag = (document.activeElement && document.activeElement.tagName) || '';
        var typing = tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' ||
            (document.activeElement && document.activeElement.isContentEditable);

        if (e.key === '/' && !typing && !e.metaKey && !e.ctrlKey && !e.altKey) {
            e.preventDefault();
            input.focus();
            input.select();
        } else if (e.key === 'Escape' && document.activeElement === input) {
            input.value = '';
            filterSidebar('');
            input.blur();
        }
    });

    /* ----------------------------------------------------------------------
       2b. Notification chime (delegated — survives Livewire re-renders)
       ---------------------------------------------------------------------- */
    document.addEventListener('click', function (e) {
        if (!e.target.closest || !e.target.closest('#notificationDropdown')) { return; }
        var sound = document.getElementById('notificationSound');
        if (sound) { sound.currentTime = 0; sound.play().catch(function () { /* autoplay blocked */ }); }
    });

    /* ----------------------------------------------------------------------
       2c. Broken thumbnails degrade to a neutral placeholder
       ----------------------------------------------------------------------
       A missing file makes Chrome size the <img> by its alt text, which breaks
       row rhythm. Capture phase, because `error` does not bubble.
       ---------------------------------------------------------------------- */
    function markBroken(el) {
        if (!el || el.tagName !== 'IMG' || el.classList.contains('bz-img-fallback')) { return; }
        el.classList.add('bz-img-fallback');
        el.removeAttribute('alt');
    }

    document.addEventListener('error', function (e) { markBroken(e.target); }, true);

    // Images that already failed before this script ran never fire `error`.
    function sweepBrokenImages() {
        document.querySelectorAll('img:not(.bz-img-fallback)').forEach(function (img) {
            if (img.complete) {
                if (img.naturalWidth === 0) { markBroken(img); }
            } else {
                img.addEventListener('error', function () { markBroken(img); }, { once: true });
            }
        });
    }

    /* ----------------------------------------------------------------------
       3. Contextual page title in the top bar
       ---------------------------------------------------------------------- */
    function syncTopbarTitle() {
        var slot = document.querySelector('.bz-topbar-title__main');
        if (!slot) { return; }

        // Pages set: "<App Name> | <Page Title>"
        var parts = (document.title || '').split('|');
        var title = parts.length > 1 ? parts.slice(1).join('|').trim() : '';

        if (!title) {
            var heading = document.querySelector('.page-content .card-title h4, .page-content h4');
            title = heading ? heading.textContent.trim() : '';
        }

        slot.textContent = title || 'Dashboard';
    }

    /* ----------------------------------------------------------------------
       4. Sidebar folded state persistence
       ---------------------------------------------------------------------- */
    function applyFoldedState() {
        if (!window.matchMedia('(min-width: 992px)').matches) { return; }
        if (localStorage.getItem(FOLD_KEY) === '1') {
            document.body.classList.add('sidebar-folded');
            var handle = document.querySelector('.sidebar-header .sidebar-toggler');
            if (handle) { handle.classList.add('active'); handle.classList.remove('not-active'); }
        }
    }

    // template.js toggles the class; mirror the result into storage.
    document.addEventListener('click', function (e) {
        if (!e.target.closest || !e.target.closest('.sidebar-toggler')) { return; }
        // Runs after template.js's own handler has flipped the class.
        setTimeout(function () {
            localStorage.setItem(FOLD_KEY, document.body.classList.contains('sidebar-folded') ? '1' : '0');
        }, 0);
    });

    /* ----------------------------------------------------------------------
       5. Re-initialise template plugins after a wire:navigate swap
       ----------------------------------------------------------------------
       Livewire replaces <body> but does not re-run scripts it has already
       executed, so every element-bound handler from template.js is lost after
       the first navigation. Rebuild them here.
       ---------------------------------------------------------------------- */
    function reinitPlugins() {
        if (window.feather) { window.feather.replace(); }

        if (window.bootstrap) {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                if (!window.bootstrap.Tooltip.getInstance(el)) { new window.bootstrap.Tooltip(el); }
            });
            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
                if (!window.bootstrap.Popover.getInstance(el)) { new window.bootstrap.Popover(el); }
            });
        }

        if (window.PerfectScrollbar && document.querySelector('.sidebar .sidebar-body')) {
            try { new window.PerfectScrollbar('.sidebar .sidebar-body'); } catch (err) { /* noop */ }
        }

        if (!$) { return; }

        // select2 (mirrors assets/js/select2.js)
        if ($.fn && $.fn.select2) {
            $('.js-example-basic-single, .js-example-basic-multiple, .select2').each(function () {
                if (!$(this).hasClass('select2-hidden-accessible')) { $(this).select2(); }
            });
        }

        // Sidebar: close sibling submenus when one opens
        var $sidebar = $('.sidebar');
        $sidebar.off('show.bs.collapse.bz').on('show.bs.collapse.bz', '.collapse', function () {
            $sidebar.find('.collapse.show').collapse('hide');
        });

        // Sidebar toggler (element-bound in template.js)
        $('.sidebar-toggler').off('click.bz').on('click.bz', function (e) {
            e.preventDefault();
            $('.sidebar-header .sidebar-toggler').toggleClass('active not-active');
            if (window.matchMedia('(min-width: 992px)').matches) {
                $('body').toggleClass('sidebar-folded');
            } else {
                $('body').toggleClass('sidebar-open');
            }
        });

        // Reveal the folded sidebar on hover
        $('.sidebar .sidebar-body').off('mouseenter.bz mouseleave.bz')
            .on('mouseenter.bz', function () {
                if ($('body').hasClass('sidebar-folded')) { $('body').addClass('open-sidebar-folded'); }
            })
            .on('mouseleave.bz', function () {
                if ($('body').hasClass('sidebar-folded')) { $('body').removeClass('open-sidebar-folded'); }
            });
    }

    function onNavigated() {
        applyFoldedState();
        reinitPlugins();
        syncTopbarTitle();
        sweepBrokenImages();
        var input = document.querySelector('.bz-sb-search input');
        if (input && input.value) { filterSidebar(input.value); }
    }

    /* ----------------------------------------------------------------------
       Boot
       ---------------------------------------------------------------------- */
    function boot() {
        ensureBar();
        applyFoldedState();
        syncTopbarTitle();
        sweepBrokenImages();
        // template.js already wires everything on the first load — don't double-bind.
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
