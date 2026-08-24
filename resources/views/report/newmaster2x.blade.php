@php
    // Route-driven sidebar state — no page-specific hardcoding, so all 121 pages that
    // may eventually extend this layout get correct active-module/breadcrumb behavior
    // for free. $akses['href'] is the exact route slug each report controller passed to
    // cekAkses() (e.g. "reportaccountinghutangkartu"), and it matches DBMENUREPORT.href
    // exactly — no request()-path guessing needed here, unlike the purchasing sidebar.
    $currentHref = trim($akses['href'] ?? '', '/');

    // Report menus go up to 4 levels deep (menu0->menu1->menu2->menu3), so find the
    // chain of ancestor GROUPS down to the current page recursively, rather than
    // hand-checking a fixed number of levels. Tracked by KODEMENU (always present and
    // unique per row — used throughout NewMenuController's own hierarchy building),
// not href: several pure category-header rows share a blank href, and href-based
// path membership would let those collide with each other.
$activePath = [];

$findPath = function ($nodes, $target) use (&$findPath) {
    foreach ($nodes as $node) {
        $nodeHref = trim($node->href ?? '', '/');
        if ($nodeHref !== '' && strcasecmp($nodeHref, $target) === 0) {
            return []; // matched leaf itself — no ancestor groups left to add here
        }
        // ?? []: NewMenuController::getMenuL0Report() only assigns ->child to
        // menu0/menu1/menu2 -- the deepest level (menu3) is pushed into its
        // parent's child array but never gets ->child set on itself. Reading it
            // there returns null (Eloquent's magic __get), and count(null) is a fatal
        // TypeError under PHP 8. Missing child is structurally "no children" here
        // (menu3 is the deepest level in this schema), so default to [].
        if (count($node->child ?? []) > 0) {
            $childPath = $findPath($node->child ?? [], $target);
            if ($childPath !== null) {
                return array_merge([$node['KODEMENU']], $childPath);
            }
        }
    }
    return null;
};

if ($currentHref !== '') {
    foreach ($akses['menul0'] as $m0) {
        $path = $findPath([$m0], $currentHref);
        if ($path !== null) {
            $activePath = $path;
            break;
        }
    }
}

// Icon dictionary keyed by Keterangan — shared vocabulary with the purchasing
// sidebar (docs/sidebar-navigation-migration.md §3.4): DBMENUREPORT's top-level
    // Keterangan values are the same module names as DBMENU's.
$iconMap = [
    'Berkas' =>
        '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M21 8v13H3V8M1 3h22v5H1zM10 12h4"/></svg>',
    'Master' =>
        '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path stroke-linecap="round" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
    'Accounting' =>
        '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><path stroke-linecap="round" d="M1 10h22"/></svg>',
    'Pengadaan' =>
        '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line stroke-linecap="round" x1="3" y1="6" x2="21" y2="6"/><path stroke-linecap="round" d="M16 10a4 4 0 01-8 0"/></svg>',
    'Marketing' =>
        '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline stroke-linecap="round" points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline stroke-linecap="round" points="17 6 23 6 23 12"/></svg>',
    'Gudang' =>
        '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path stroke-linecap="round" d="M9 22V12h6v10"/></svg>',
    'Report' =>
        '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M18 20V10M12 20V4M6 20v-6"/></svg>',
    ];
@endphp
<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="author" content="SemiColonWeb" />

    <!-- Stylesheets
  ============================================= -->
    <link
        href="https://fonts.googleapis.com/css?family=Lato:300,400,400i,700|Poppins:300,400,500,600,700|PT+Serif:400,400i&display=swap"
        rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{!! URL::asset('css/semantic.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/select2.min.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/datatables.min.css') !!}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.css">
    <link rel="stylesheet" href="{!! URL::asset('css/jquery-ui.min.css') !!}">

    <link rel="stylesheet" href="{!! URL::asset('css/canvas/bootstrap.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/style.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/dark.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/font-icons.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/animate.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/magnific-popup.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/custom.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/alertify.css') !!}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}">

    <!-- Shared styling for .tb-report styled report tables (used by report* pages) -->
    <link rel="stylesheet"
        href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">

    <!-- Modal "Atur Kolom" (#formCustomizeTable.ct-modal) milik masterreport2 -->
    <link rel="stylesheet"
        href="{!! URL::asset('css/customize-table.css') !!}?v={{ @filemtime(base_path('public/css/customize-table.css')) ?: '1' }}">

    {{-- Header tabel interaktif + skin modal Filter/picker: sudah DIGABUNG ke
         public/css/report-table.css (di-link di atas). public/css/report-table-v2.css
         masih ada di disk sebagai arsip, tapi TIDAK dimuat lagi. --}}

    <!-- Sidebar/header design — shared with the other PT.SPL codebase's layout
         (see references/newmaster.blade.php + docs/sidebar-navigation-migration.md §8).
         Defines .sidebar/.nav-group/.nav-item/.nav-children/.nav-child, .main/.header/
         .titleText/.period-badge/.avatar/.logout-link, .card-grid, etc. -->
    <link rel="stylesheet"
        href="{!! URL::asset('css/newmaster.css') !!}?v={{ @filemtime(base_path('public/css/newmaster.css')) ?: '1' }}">

    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Document Title
  ============================================= -->
    <title>@yield('title', $akses['namamenu'])</title>

    @yield('css')
    <style>
        table,
        th,
        td {
            border: 1px solid black !important;
            border-collapse: collapse !important;
            /* supaya garis tidak dobel */
        }

        /* Header kolom abu-abu muda */
        .tabel_header_kolom th {
            background-color: #646668 !important;
            color: white !important;
            text-align: center;
            font-weight: bold;
        }

        /* newmaster.css (the shared design) still has the CSS-hover behavior this
           project deliberately removed twice already — .nav-group:hover>.nav-item
           .nav-chevron and .nav-group:hover .nav-children (moving down the collapsed
           rail flashes every folder open in turn). Its hover selectors have HIGHER
           specificity than a plain override (`:hover` counts as a class), so `!important`
           is required here, not just later-in-source-wins. Also switched to a
           direct-child selector so nesting can't leak .open state (see
           docs/sidebar-navigation-migration.md §6 for why that matters). */
        .nav-group>.nav-item .nav-chevron {
            transform: none !important;
        }

        .nav-group.open>.nav-item .nav-chevron {
            transform: rotate(90deg) !important;
        }

        /* Blanket-suppress first (kills newmaster.css's .nav-group:hover .nav-children
           600px rule via !important, regardless of source order), then re-open only
           for a genuinely .open group with the sidebar actually expanded — higher
           specificity (5 simple selectors) than the blanket rule (1) so it wins over
           it despite both being !important. newmaster.css's own
           .sidebar:not(:hover) .nav-children guard still applies underneath this. */
        .nav-children {
            max-height: 0 !important;
        }

        .sidebar:hover .nav-group.open>.nav-children {
            max-height: 2000px !important;
        }

        /* Old content area sits inside the new flex shell but keeps its own
           Bootstrap container/row markup untouched so page content that relies on
           Bootstrap's grid classes isn't affected. */
        #content {
            flex: 1;
            overflow-y: auto;
        }

        /* -- Flyout panels for menu depth beyond the group/child tier (report menus
           nest up to 4 levels; newmaster.css's design only models 3) --
           Rendered OUTSIDE <aside class="sidebar"> (appended into #flyout-root at the
           end of <body>) so the sidebar's own overflow:hidden/max-height accordion
           mechanics can't clip them, regardless of the flyout's own position/z-index. */
        .nav-child.has-sub {
            justify-content: flex-start;
        }

        .nav-child.has-sub .nav-child-label {
            flex: 1;
            text-align: left;
        }

        .nav-child-arrow {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
            opacity: 0.6;
            margin-left: auto;
            transition: opacity 0.15s ease;
        }

        .nav-child.has-sub:hover .nav-child-arrow {
            opacity: 1;
        }

        .nav-flyout {
            position: fixed !important;
            min-width: 200px;
            max-width: 280px;
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            padding: 6px;
            z-index: 9999 !important;
            margin: 0;
            opacity: 0;
            visibility: hidden;
            transform: translateX(-4px);
            transition: opacity 0.12s ease, transform 0.12s ease, visibility 0.12s ease;
            pointer-events: none;
        }

        .nav-flyout.flyout-visible {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
            pointer-events: auto;
        }

        .nav-flyout-caption {
            padding: 8px 12px 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #9ca3af;
        }

        .nav-flyout-item {
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            white-space: nowrap;
            color: #333;
            cursor: pointer;
            transition: background 0.12s ease;
        }

        .nav-flyout-item:hover {
            background: rgba(0, 0, 0, 0.06);
        }

        /* Keep the sidebar pinned wide while a flyout is open, and keep the owning
           group visually expanded, even though the cursor has left the rail to reach
           the flyout panel. */
        #sidebar.flyout-pinned {
            width: var(--sidebar-exp, 240px) !important;
        }

        #sidebar.flyout-pinned .logo-text,
        #sidebar.flyout-pinned .nav-label,
        #sidebar.flyout-pinned .nav-chevron {
            opacity: 1 !important;
        }

        #sidebar.flyout-pinned .nav-group.flyout-owner .nav-children {
            max-height: 2000px !important;
        }

        #sidebar.flyout-pinned .nav-group.flyout-owner>.nav-item .nav-chevron {
            transform: rotate(90deg);
        }
    </style>
</head>

<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo" onclick="window.location.href='{{ url('home') }}'">
            <div class="logo-icon">AJC</div>
            <span class="logo-text">{{ $akses['program'] ?? 'Report' }}</span>
        </div>
        <nav class="sidebar-nav" id="nav">
            @foreach ($akses['menul0'] as $menu0)
                @include('report.partials.sidebar-nav-node', [
                    'node' => $menu0,
                    'depth' => 0,
                    'activePath' => $activePath,
                    'currentHref' => $currentHref,
                    'iconMap' => $iconMap,
                ])
            @endforeach
        </nav>
    </aside>

    {{-- Flyout panels for menu depth 3/4 — deliberately rendered OUTSIDE <aside>,
         appended near the end of <body>, so the sidebar's own overflow:hidden/
         max-height accordion can't clip them (see the CSS comment above and
         docs/sidebar-navigation-migration.md §8). One panel per depth-1 (.nav-child)
         row that has children; a depth-2 node that itself has children (real 4th
         level) is flattened into the SAME panel as a captioned sub-list rather than
         opening a second nested flyout. --}}
    <div id="flyout-root">
        @foreach ($akses['menul0'] as $m0)
            @foreach ($m0->child ?? [] as $m1)
                @if (count($m1->child ?? []) > 0)
                    <div class="nav-flyout" id="flyout-{{ $m1['KODEMENU'] }}">
                        @foreach ($m1->child as $m2)
                            @if (count($m2->child ?? []) > 0)
                                <div class="nav-flyout-caption">{{ $m2['Keterangan'] }}</div>
                                @foreach ($m2->child as $m3)
                                    <div class="nav-flyout-item" onclick="window.location.href='{{ $m3->href }}'">
                                        {{ $m3['Keterangan'] }}</div>
                                @endforeach
                            @else
                                <div class="nav-flyout-item" onclick="window.location.href='{{ $m2->href }}'">
                                    {{ $m2['Keterangan'] }}</div>
                            @endif
                        @endforeach
                    </div>
                @endif
            @endforeach
        @endforeach
    </div>

    <div class="main">
        <header class="header">
            <div class="titleText" style="font-weight: bold" id="breadcrumb">@yield('breadcrumb_title', $akses['namamenu'])</div>
            <div class="header-right">
                <div class="period-badge">
                    Username: {{ \Auth::user()->username }} &ndash;
                    Periode: {!! $akses['periode']->bulan !!} / {!! $akses['periode']->tahun !!}
                </div>
                <div class="avatar">{{ strtoupper(\Auth::user()->username[0]) }}</div>
                <a class="logout-link" href="{!! url('logout') !!}">
                    <i class="bi bi-power"></i> Log Out
                </a>
            </div>
        </header>

        <!-- Content
    ============================================= -->
        <section id="content" class="mt-3 mb-6">
            <div class="content-wrap">
                <div class="container-fluid px-5 clearfix">
                    <div class="row gutter-40 col-mb-80">
                        @yield('content')
                    </div>
                </div>
            </div>
        </section>
        <!-- #content end -->
    </div>

    <!-- External JavaScripts
    ============================================= -->

    <script src="{!! URL::asset('js/canvas/jquery.js') !!}"></script>
    <script src="{!! URL::asset('js/jquery.min.js') !!}"></script>
    <script src="{!! URL::asset('js/jquery-3.3.1.min.js') !!}"></script>
    <script src="{!! URL::asset('js/select2.min.js') !!}"></script>
    <script src="{!! URL::asset('js/popper.min.js') !!}"></script>
    <script src="{!! URL::asset('js/bootstrap.min.js') !!}"></script>
    <script src="{!! URL::asset('js/alertify.js') !!}"></script>
    <script src="{!! URL::asset('js/autoNumeric.js') !!}"></script>
    <script src="{!! URL::asset('js/datatables.min.js') !!}"></script>
    <script src="{!! URL::asset('js/jquery-ui.min.js') !!}"></script>
    <script src="{!! URL::asset('js/qrcode.min.js') !!}"></script>
    <script src="{!! URL::asset('js/browsemaster.js') !!}"></script>

    <!-- Footer Scripts
  ============================================= -->
    <script src="{!! URL::asset('js/canvas/functions.js') !!}"></script>
    <script src="{!! URL::asset('js/canvas/JsBarcode.all.min.js') !!}"></script>

    <script type="text/javascript">
        document.onkeydown = function(e) {
            if (event.keyCode == 123) {
                return false;
            }
            if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
                return false;
            }
            if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
                return false;
            }
            if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
                return false;
            }

            if (e.ctrlKey && e.shiftKey && e.altKey && e.keyCode === 90) {
                // Ctrl + Shift + Alt + Z
                e.preventDefault(); // Prevent any default action
                doAdminMenu();
            }
        }
        $("button").addClass('btn-sm');
        $(".form-control").addClass('form-control-sm');
        $(document).on('hidden.bs.modal', '.modal', function() {
            $('.modal:visible').length && $(document.body).addClass('modal-open');
        });
        $('.modal').modal({
            show: false,
            keyboard: false,
            backdrop: 'static'
        });
        $("title").html($("#title_page").html());
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });
        $("[rel='tooltip']").tooltip();

        // Sidebar accordion: open the clicked group and close whichever sibling group
        // (at the SAME nesting level only) was open before — reports nest up to 4
        // levels deep, so this is scoped per-level, not globally like the 2-level
        // purchasing sidebar's version (see docs/sidebar-navigation-migration.md).
        function toggleNavGroup(itemEl) {
            var group = itemEl.closest(".nav-group");
            var parent = group.parentElement; // #nav (top level) or a .nav-children (nested)
            var wasOpen = group.classList.contains("open");
            parent.querySelectorAll(":scope > .nav-group.open").forEach(function(g) {
                if (g !== group) g.classList.remove("open");
            });
            group.classList.toggle("open", !wasOpen);
        }

        // Position + show/hide flyout panels (menu depth 3/4) on hover, using real
        // coordinates. Ported from references/newmaster.blade.php. The panels live in
        // #flyout-root (appended near the end of <body>, outside .sidebar) specifically
        // so the sidebar's own overflow:hidden/max-height accordion can't clip them —
        // see the CSS comment above .nav-flyout and docs/sidebar-navigation-migration.md §8.
        function attachFlyoutHoverHandlers() {
            const allFlyouts = Array.from(document.querySelectorAll(".nav-flyout"));
            const hideTimers = new Map(); // flyoutEl -> timer id, one per flyout
            const HIDE_DELAY = 400; // ms grace period to travel from row to flyout
            const sidebarEl = document.getElementById("sidebar");

            function anyFlyoutOpen() {
                return allFlyouts.some(f => f.classList.contains("flyout-visible"));
            }

            function syncSidebarPin() {
                if (!sidebarEl) return;
                const open = anyFlyoutOpen();
                sidebarEl.classList.toggle("flyout-pinned", open);
                // Only the nav-group owning the currently-open flyout should stay
                // visually expanded; every other nav-group's "flyout-owner" class
                // gets cleared so they don't pop open along with it.
                document.querySelectorAll(".nav-group.flyout-owner").forEach(g => {
                    if (!open) g.classList.remove("flyout-owner");
                });
            }

            function hideAllExcept(keepEl) {
                allFlyouts.forEach(f => {
                    if (f !== keepEl) {
                        clearTimeout(hideTimers.get(f));
                        f.classList.remove("flyout-visible");
                    }
                });
                syncSidebarPin();
            }

            document.querySelectorAll(".nav-child.has-sub").forEach(rowEl => {
                const flyoutId = rowEl.getAttribute("data-flyout-id");
                const flyoutEl = document.getElementById(flyoutId);
                if (!flyoutEl) return;

                function showFlyout() {
                    clearTimeout(hideTimers.get(flyoutEl));
                    hideAllExcept(flyoutEl);

                    const ownerGroup = rowEl.closest(".nav-group");
                    if (ownerGroup) ownerGroup.classList.add("flyout-owner");

                    const rect = rowEl.getBoundingClientRect();
                    const OVERLAP = 6;

                    // Temporarily lay it out invisibly so offsetWidth/Height are real.
                    flyoutEl.style.visibility = "hidden";
                    flyoutEl.style.opacity = "0";
                    flyoutEl.style.display = "block";

                    const flyoutWidth = flyoutEl.offsetWidth || 220;
                    const flyoutHeight = flyoutEl.offsetHeight || 0;

                    flyoutEl.style.display = "";
                    flyoutEl.style.visibility = "";
                    flyoutEl.style.opacity = "";

                    let left = rect.right - OVERLAP;
                    let top = rect.top;

                    if (left + flyoutWidth > window.innerWidth) {
                        left = rect.left - flyoutWidth + OVERLAP;
                    }
                    if (top + flyoutHeight > window.innerHeight) {
                        top = Math.max(8, window.innerHeight - flyoutHeight - 8);
                    }

                    flyoutEl.style.left = left + "px";
                    flyoutEl.style.top = top + "px";
                    flyoutEl.classList.add("flyout-visible");
                    syncSidebarPin();
                }

                function scheduleHide() {
                    const t = setTimeout(() => {
                        flyoutEl.classList.remove("flyout-visible");
                        syncSidebarPin();
                    }, HIDE_DELAY);
                    hideTimers.set(flyoutEl, t);
                }

                rowEl.addEventListener("mouseenter", showFlyout);
                rowEl.addEventListener("mouseleave", scheduleHide);
                flyoutEl.addEventListener("mouseenter", () => clearTimeout(hideTimers.get(flyoutEl)));
                flyoutEl.addEventListener("mouseleave", scheduleHide);
            });

            // No sidebar-wide "mouseleave closes everything" listener on purpose: the
            // flyout lives outside #sidebar in the DOM, so moving the cursor from the
            // row into the flyout already counts as leaving #sidebar. A sidebar-wide
            // listener would force-close the flyout the moment you tried to enter it.
        }
        attachFlyoutHoverHandlers();

        function doAdminMenu() {
            doUpdateDBMENUREPORT();
        }

        function doUpdateDBMENUREPORT() {
            $.ajax({
                url: "{!! url('globalfunctions_doUpdateDBMENUREPORT') !!}",
                type: "get",
                async: false,
                success: function(res) {
                    if (res == "1") {
                        alertify.success("Update berhasil");
                    }
                }
            })
        }

        function onEnterSetup(_id_name) {
            var input = document.getElementById(_id_name);
            input.addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    /* Buatlah function bernama onEnterFunction */
                    /* Isinya berupa Switch Case per nama ID */
                    onEnterFunction(_id_name);
                }
            });
        }


        function padZero(num) {
            return num.toString().padStart(2, '0');
        }

        function getDateHours() {
            let dateNow = new Date();
            return padZero(dateNow.getHours());
        }

        function getDateMinutes() {
            let dateNow = new Date();
            return padZero(dateNow.getMinutes());
        }

        function getDateSeconds() {
            let dateNow = new Date();
            return padZero(dateNow.getSeconds());
        }

        function getDateIndo() {
            let dateNow = new Date();
            return padZero(dateNow.toLocaleDateString('id-ID'));
        }

        function getDateNow(_separator = "") {
            var now = new Date();

            var day = ("0" + now.getDate()).slice(-2);
            var month = ("0" + (now.getMonth() + 1)).slice(-2);

            var today = now.getFullYear() + _separator + month + _separator + day;

            return today;
        }

        function getTimeNow(_separator = ":") {
            return getDateHours() + _separator + getDateMinutes() + _separator + getDateSeconds();
        }


        function numberWithCommas(n) {
            var parts = n.toString().split(".");
            return parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (parts[1] ? "." + parts[1] : "");
        }

        function numberNoCommas(n) {
            return n.replace(/,/g, "");
        }

        // function numberNoDecimals(n) { var _num = (n==".00") ? "0" : n.replace(".00", ""); return _num; }
        function numberNoDecimals(n) {
            var _isDesimal = false;
            for (var i = 0; i < n.length; i++) {
                if (n[i] == ".") {
                    _isDesimal = true;
                    break;
                }
            }
            if (!_isDesimal) {
                return (n == "") ? "0" : n;
            }

            var _num = n;

            while (_num != "") {
                var lastChar = _num[_num.length - 1];
                if (lastChar == "0") {
                    // menghilangkan semua nol di belakang koma
                    _num = _num.substring(0, _num.length - 1);
                } else {
                    // hilangkan jika koma, ada kemungkinan nilainya jadi empty
                    if (lastChar == ".") {
                        _num = _num.substring(0, _num.length - 1);
                    }

                    // jika ketemu yang bukan nol, loop berhenti
                    break;
                }
            }

            return (_num == "") ? "0" : _num;
        }

        function currencyNormalizer(n) {
            if (n == null) {
                return 0.0;
            }
            var _isDesimal = false;
            for (var i = 0; i < n.length; i++) {
                if (n[i] == ".") {
                    _isDesimal = true;
                    break;
                }
            }
            if (!_isDesimal) {
                return (n == "") ? 0.0 : parseFloat(n);
            }

            var _num = n;

            while (_num != "") {
                var lastChar = _num[_num.length - 1];
                if (lastChar == "0") {
                    // menghilangkan semua nol di belakang koma
                    _num = _num.substring(0, _num.length - 1);
                } else {
                    // hilangkan jika koma, ada kemungkinan nilainya jadi empty
                    if (lastChar == ".") {
                        _num = _num.substring(0, _num.length - 1);
                    }

                    // jika ketemu yang bukan nol, loop berhenti
                    break;
                }
            }

            return (_num == "") ? 0.0 : parseFloat(_num);
        }

        function removeSpasi(n) {
            return n.replace(/ /g, "");
        }

        function numbersWithDividers(n) {
            if (isNaN(n)) return ""; // Handle non-numeric input gracefully

            // Use only the integer part of the number
            let integerPart = Math.floor(n).toString();

            // Format the integer part with dot separators
            return integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function toInteger(n) {
            return parseInt(n.replace(/,/g, ""));
        }

        function toFloat(n) {
            n = (n.substring(0, 1) == "." ? "0" : "") + n;
            return parseFloat(n.replace(/,/g, ""));
        }

        function round(value, precision) {
            var multiplier = Math.pow(10, precision || 0);
            return Math.round(value * multiplier) / multiplier;
        }

        function middleTD() {}

        function format_timestamp(date) {
            if (date == "" || date == null) return "";
            tgl = date.split(" ")[0];
            waktu = date.split(" ")[1];
            return tgl.split("-")[2] + "/" + tgl.split("-")[1] + "/" + tgl.split("-")[0] + " " + waktu;
        }

        function format_date(date, isMonthName = false, separatorOld = "-", separatorNew = "/", withDay = true) {
            if (date == "" || date == null) {
                return "";
            }

            let monthNames = [
                "JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE",
                "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"
            ];

            date = date.substring(0, 10);
            let _day = date.split(separatorOld)[2];
            let _month = (isMonthName) ? monthNames[date.split(separatorOld)[1] - 1] : date.split(separatorOld)[1];
            let _year = date.split(separatorOld)[0];
            let _separator = (isMonthName) ? " " : separatorNew;

            return (withDay) ? _day + _separator + _month + _separator + _year : _month + _separator + _year
        }

        function format_number(n, _decimal = 0, _comma = true) {
            return (_comma) ? numberWithCommas(n.toFixed(_decimal)) : n.toFixed(_decimal);
        }

        function messageRequired(_input_name) {
            return "Kolom " + _input_name + " harus terisi.";
        }

        function messageHiddenRequired(_input_name) {
            return "Terjadi kesalahan. " + _input_name + " tidak ditemukan. Silahkan refresh halaman.";
        }

        function messageNotZero(_input_name) {
            return "Kolom " + _input_name + " tidak boleh nol.";
        }

        function messageMustNumber(_input_name) {
            return "Kolom " + _input_name + " harus angka.";
        }

        function messageNotEmptyCart(_menu_name) {
            return "Detail " + _menu_name + " tidak boleh kosong";
        }

        function nullToEmpty(_val) {
            return (_val == null) ? "" : _val;
        }

        function nullToZero(_val) {
            return (_val == null) ? 0 : _val;
        }

        function nullToStrip(_val) {
            return (_val == null) ? "-" : _val;
        }

        function emptyToZero(_val) {
            return (_val == "") ? "0" : _val;
        }

        function cekRequiredNotEmpty(_id_name) {
            if ($("#" + _id_name).val() != "") {
                return true;
            } else {
                $("#" + _id_name).focus();
                return false;
            }
        }

        function cekRequiredNotZero(_id_name) {
            var _nominal = $("#" + _id_name).val();
            if (_nominal != "" && _nominal != "0.00" && _nominal != "0") {
                return true;
            } else {
                $("#" + _id_name).focus();
                return false;
            }
        }

        function setEmptyNumberToZero(_id_name) {
            return ($("#" + _id_name).val() != "") ? $("#" + _id_name).val().replace(/,/g, '') : "0";
        }

        function setFocus(_modal, _item) {
            $('#' + _modal).on('shown.bs.modal', function() {
                $('#' + _item).focus();
            })
        }

        function doSelectRow(_id_data, _row, _oldrow, _tr = "row", _bgcolor = "blue", _color = "white") {
            $('#' + _id_data + ' > tr').each(function() {
                $(this).css('background-color', '');
                $(this).css('color', '');
            });

            if (_row != _oldrow) {
                $("#" + _row + "-tr" + _tr).css('background-color', _bgcolor);
                $("#" + _row + "-tr" + _tr).css('color', _color);

                return _row;
            } else {
                return "";
            }
        }

        function trWithSelectRow(_id_kode_urut, _name = "row", _addition_str = "") {
            let id_kode = ($.isNumeric(_id_kode_urut)) ? _id_kode_urut : removeSpasi(_id_kode_urut);
            return '<tr id="' + id_kode + '-tr' + _name + '" onclick="select' + _name + '(\'' + id_kode + '\', ' +
                _addition_str + ')">';
        }
    </script>
    @yield('js')
</body>

</html>
