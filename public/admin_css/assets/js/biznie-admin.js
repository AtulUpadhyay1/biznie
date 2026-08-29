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
     6. RFQ auction countdowns ([data-bz-countdown])
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
       2d. Sidebar scrollbar
       ----------------------------------------------------------------------
       PerfectScrollbar sets `overflow: hidden` and caches the content height.
       Two things used to break the submenus: a fresh instance was created on
       every wire:navigate without destroying the last (so several of them
       fought over the same element), and none of them was told to re-measure
       after a submenu expanded — the extra height stayed clipped, which is why
       an opened menu looked empty.
       ---------------------------------------------------------------------- */
    var sidebarScrollbar = null;
    var sidebarScrollbarEl = null;

    function initSidebarScrollbar() {
        var el = document.querySelector('.sidebar .sidebar-body');
        if (!window.PerfectScrollbar || !el) { return; }

        if (sidebarScrollbar && sidebarScrollbarEl === el) {
            try { sidebarScrollbar.update(); } catch (err) { /* noop */ }
            return;
        }

        if (sidebarScrollbar) {
            try { sidebarScrollbar.destroy(); } catch (err) { /* noop */ }
            sidebarScrollbar = null;
        }

        try {
            sidebarScrollbar = new window.PerfectScrollbar(el);
            sidebarScrollbarEl = el;
        } catch (err) { /* noop */ }
    }

    function updateSidebarScrollbar() {
        if (!sidebarScrollbar) { return; }
        try { sidebarScrollbar.update(); } catch (err) { /* noop */ }
    }

    // Delegated, so it keeps working across Livewire DOM swaps. Bootstrap's
    // collapse events do bubble.
    ['shown.bs.collapse', 'hidden.bs.collapse'].forEach(function (evt) {
        document.addEventListener(evt, function (e) {
            if (e.target && e.target.closest && e.target.closest('.sidebar')) {
                updateSidebarScrollbar();
            }
        });
    });

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

        initSidebarScrollbar();

        if (!$) { return; }

        // select2 (mirrors assets/js/select2.js). Scoped to `select` because
        // select2's own wrapper carries class "select2" too — matching it makes
        // select2 re-initialise on its own output and stack duplicate controls.
        if ($.fn && $.fn.select2) {
            $('select.js-example-basic-single, select.js-example-basic-multiple, select.select2').each(function () {
                if (!$(this).hasClass('select2-hidden-accessible')) { $(this).select2(); }
            });
        }
        initLivewireSelects();

        // Sidebar: close sibling submenus when one opens. Scoped to *other*
        // panels — hiding the one that is mid-open collapses it straight back.
        var $sidebar = $('.sidebar');
        $sidebar.off('show.bs.collapse.bz').on('show.bs.collapse.bz', '.collapse', function () {
            var opening = this;
            $sidebar.find('.collapse.show').each(function () {
                if (this !== opening) { $(this).collapse('hide'); }
            });
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
        initCountdowns();
        var input = document.querySelector('.bz-sb-search input');
        if (input && input.value) { filterSidebar(input.value); }
    }

    /* ----------------------------------------------------------------------
       select2 <-> Livewire bridge  (`.bz-select2`)
       ----------------------------------------------------------------------
       A select2 control cannot be morphed by Livewire — select2 builds its own
       sibling DOM and Livewire's diff tears it apart, which is why selected
       values used to vanish. So each `.bz-select2` lives inside `wire:ignore`
       and this bridge owns it:

         DOM  -> server : on change, push the value to the property named in
                          `data-prop` (the old code guessed the property from
                          the element id, so `#sub_category` wrote to the
                          non-existent `sub_category` instead of
                          `sub_category_id` and the choice was lost).
         server -> DOM  : the component dispatches `bz-options` with the new
                          option list, since Blade can no longer reach inside
                          `wire:ignore`.
       -------------------------------------------------------------------- */
    function componentFor(el) {
        var host = el.closest('[wire\\:id]');
        if (!host || !window.Livewire) { return null; }
        try { return window.Livewire.find(host.getAttribute('wire:id')); } catch (err) { return null; }
    }

    function initLivewireSelects() {
        if (!$ || !$.fn || !$.fn.select2) { return; }

        $('select.bz-select2').each(function () {
            var $el = $(this);
            if ($el.data('bzSelectReady')) { return; }

            $el.select2({
                width: '100%',
                placeholder: $el.attr('data-placeholder') || 'Select',
                // A modal creates its own stacking context; without this the
                // dropdown renders behind the backdrop.
                dropdownParent: $el.closest('.modal').length ? $el.closest('.modal') : $(document.body)
            });
            $el.data('bzSelectReady', true);

            $el.on('change', function () {
                var prop = $el.attr('data-prop');
                if (!prop) { return; }
                var cmp = componentFor($el[0]);
                if (!cmp) { return; }
                var val = $el.val();
                cmp.set(prop, val === null ? ($el.prop('multiple') ? [] : '') : val);
            });
        });
    }

    function applyOptions(detail) {
        if (!$ || !detail || !detail.target) { return; }
        var $el = $('#' + detail.target);
        if (!$el.length) { return; }

        $el.empty();
        if (!$el.prop('multiple')) {
            $el.append(new Option(detail.placeholder || 'Select', '', false, false));
        }
        (detail.options || []).forEach(function (opt) {
            $el.append(new Option(opt.text, opt.id, false, false));
        });

        $el.val(detail.selected === undefined || detail.selected === null ? '' : detail.selected);
        // `change.select2` redraws the widget WITHOUT firing our own change
        // handler, so this cannot echo back to the server in a loop.
        $el.trigger('change.select2');
    }

    window.addEventListener('bz-options', function (e) {
        applyOptions(e.detail && e.detail.length ? e.detail[0] : e.detail);
    });

    window.addEventListener('bz-modal-close', function (e) {
        var detail = e.detail && e.detail.length ? e.detail[0] : e.detail;
        if (!detail || !detail.id || !window.bootstrap) { return; }
        var el = document.getElementById(detail.id);
        if (!el) { return; }
        var modal = window.bootstrap.Modal.getInstance(el);
        if (modal) { modal.hide(); }
    });

    /* ----------------------------------------------------------------------
       RFQ auction countdowns
       ----------------------------------------------------------------------
       An RFQ auction closes at a fixed instant, and three screens have to agree
       on how long is left. Blade can only render the number that was true when
       the response was built, and a wire:poll cheap enough to sit on a list
       page still leaves the figure visibly stale between refreshes.

       So the server renders the deadline as an epoch on `data-bz-countdown`
       and this ticker owns the digits. One interval drives every element on the
       page, and it stops itself when none are left, so an admin who navigates
       away is not paying for a timer forever.
       ---------------------------------------------------------------------- */
    var countdownTimer = null;

    function pad(n) { return n < 10 ? '0' + n : String(n); }

    function paintCountdowns() {
        var nodes = document.querySelectorAll('[data-bz-countdown]');
        if (!nodes.length) {
            clearInterval(countdownTimer);
            countdownTimer = null;
            return;
        }

        var now = Date.now();

        nodes.forEach(function (el) {
            var endsAt = parseInt(el.getAttribute('data-bz-countdown'), 10);
            if (!endsAt) { return; }

            var left = Math.max(0, Math.floor((endsAt * 1000 - now) / 1000));
            var mins = Math.floor(left / 60);
            var secs = left % 60;

            el.textContent = left > 0 ? pad(mins) + 'm : ' + pad(secs) + 's' : 'Closed';
            el.classList.toggle('text-danger', left > 0 && left <= 120);
            el.classList.toggle('text-muted', left === 0);
        });
    }

    function initCountdowns() {
        paintCountdowns();
        if (!countdownTimer && document.querySelector('[data-bz-countdown]')) {
            countdownTimer = setInterval(paintCountdowns, 1000);
        }
    }

    /* ----------------------------------------------------------------------
       Boot
       ---------------------------------------------------------------------- */
    function boot() {
        ensureBar();
        applyFoldedState();
        // The sidebar scrollbar is ours alone now (template.js no longer makes
        // one), so a single instance survives every wire:navigate swap.
        initSidebarScrollbar();
        syncTopbarTitle();
        sweepBrokenImages();
        // template.js already wires everything on the first load — don't double-bind.
        // select2.js does not know about `.bz-select2`, so this one is ours.
        initLivewireSelects();
        initCountdowns();
        document.addEventListener('livewire:initialized', initLivewireSelects);
        // A wire:poll re-render swaps the deadline nodes out from under us.
        document.addEventListener('livewire:initialized', initCountdowns);
        document.addEventListener('livewire:update', initCountdowns);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
