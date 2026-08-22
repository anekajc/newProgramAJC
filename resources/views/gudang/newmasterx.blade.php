@php
    // Route-driven sidebar state, same approach as purchasing/newmasterx.blade.php and
    // report/newmaster2x.blade.php: no page-specific hardcoding, so every one of the 22
    // gudang pages that may extend this layout gets correct active-module/header-title
    // behavior for free. Gudang controllers pass $menul0 (bare, from
    // NewMenuController::getMenuL0(6)) and $periode — there is no $akses['href'] here like
    // the report module has, so the current page is resolved from request()->path()
    // against DBMENU.href instead.
    $currentHref = trim(request()->path(), '/');

    // Gudang menus go 3 levels deep (menu0->menu1->menu2), one shallower than report's 4.
    // Same recursive walk as report/newmaster2x.blade.php to find the chain of ancestor
    // GROUPS down to the current page, tracked by KODEMENU (unique per row) rather than
    // href — several pure category-header rows can share a blank href, and href-based
    // path membership would let those collide with each other.
    $activePath = [];
    $autoPageTitle = null;

    $findPath = function ($nodes, $target) use (&$findPath) {
        foreach ($nodes as $node) {
            $nodeHref = trim($node->href ?? '', '/');
            if ($nodeHref !== '' && strcasecmp($nodeHref, $target) === 0) {
                return ['path' => [], 'title' => $node['Keterangan']];
            }
            // ?? []: NewMenuController::getMenuL0() only assigns ->child to menu0/menu1 --
            // the deepest level (menu2) is pushed into its parent's child array but never
            // gets ->child set on itself. Reading it there returns null (Eloquent's magic
            // __get), and count(null) is a fatal TypeError under PHP 8. Missing child is
            // structurally "no children" here (menu2 is the deepest level), so default to [].
            if (count($node->child ?? []) > 0) {
                $found = $findPath($node->child ?? [], $target);
                if ($found !== null) {
                    return ['path' => array_merge([$node['KODEMENU']], $found['path']), 'title' => $found['title']];
                }
            }
        }
        return null;
    };

    if ($currentHref !== '') {
        foreach ($menul0 as $m0) {
            $found = $findPath([$m0], $currentHref);
            if ($found !== null) {
                $activePath = $found['path'];
                $autoPageTitle = $found['title'];
                break;
            }
        }
    }

    // Icon dictionary keyed by Keterangan — shared vocabulary with the purchasing and
    // report sidebars (docs/sidebar-navigation-migration.md §3.4/§8): DBMENU's top-level
    // Keterangan values are the same module names across all three menu tables.
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

    <link rel="stylesheet" href="{!! URL::asset('public/css/semantic.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/datatables.min.css') !!}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.css">
    <link rel="stylesheet" href="{!! URL::asset('public/css/jquery-ui.min.css') !!}">

    <link rel="stylesheet" href="{!! URL::asset('public/css/canvas/bootstrap.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/canvas/style.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/canvas/dark.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/canvas/font-icons.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/canvas/animate.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/canvas/magnific-popup.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/canvas/custom.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/alertify.css') !!}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{!! URL::asset('public/css/style.css') !!}">

    {{-- Styling untuk tabel interaktif ala report (drag kolom, gear menu, dst) —
         lihat docs/new-slider-table-guide.md. --}}
    <link rel="stylesheet"
        href="{!! URL::asset('public/css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
    <link rel="stylesheet"
        href="{!! URL::asset('public/css/tableMaster2.css') !!}?v={{ @filemtime(base_path('public/css/tableMaster2.css')) ?: '1' }}">

    <!-- Sidebar/header design — shared with the report module's layout and the other
         PT.SPL codebase (see references/newmaster.blade.php + report/newmaster2x.blade.php +
         docs/sidebar-navigation-migration.md §9). Defines .sidebar/.nav-group/.nav-item/
         .nav-children/.nav-child, .main/.header/.titleText/.period-badge/.avatar/
         .logout-link, etc. Loaded LAST so its body{display:flex;height:100vh} wins over
         canvas/style.css. -->
    <link rel="stylesheet"
        href="{!! URL::asset('public/css/newmaster.css') !!}?v={{ @filemtime(base_path('public/css/newmaster.css')) ?: '1' }}">

    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Document Title
 ============================================= -->
    <title>@yield('title', 'Gudang')</title>

    @yield('css')
    <style>
        table.dataTable tbody th,
        table.dataTable tbody td {
            padding: 0 10px !important;
        }

        table tbody th,
        table tbody td {
            padding: 0 10px !important;
        }

        /* newmaster.css still has the CSS-hover behavior this project deliberately
           removed already (see report/newmaster2x.blade.php and
           docs/sidebar-navigation-migration.md §8) — .nav-group:hover>.nav-item
           .nav-chevron and .nav-group:hover .nav-children (moving down the collapsed
           rail flashes every folder open in turn). Its hover selectors have HIGHER
           specificity than a plain override (`:hover` counts as a class), so
           `!important` is required, not just later-in-source-wins. Direct-child
           selectors so nesting can't leak .open state. */
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

        #content {
            flex: 1;
            overflow-y: auto;
        }

        /* -- Flyout panel for menu depth beyond the group/child tier -- rendered OUTSIDE
           <aside class="sidebar"> (appended into #flyout-root at the end of <body>) so
           the sidebar's own overflow:hidden/max-height accordion mechanics can't clip
           it, regardless of the flyout's own position/z-index. Gudang menus only go 3
           levels deep, so there is a single flyout tier here (no 4th-level flatten,
           unlike report's). */
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
        <div class="sidebar-logo" onclick="window.location.href='{{ url('home') }}'" style="cursor:pointer">
            <div class="logo-icon">AJC</div>
            <span class="logo-text">Gudang</span>
        </div>
        <nav class="sidebar-nav" id="nav">
            {{-- Not present in $menul0 (DBMENU) — pinned in manually so Set Periode stays
                 reachable, same as the hardcoded "BERKAS" entry in gudang/newmaster.blade.php. --}}
            <div class="nav-group">
                <div class="nav-item" onclick="window.location.href='{{ url('setperiode') }}'">
                    <span class="nav-icon">{!! $iconMap['Berkas'] ?? '' !!}</span>
                    <span class="nav-label">Set Periode</span>
                </div>
            </div>

            @foreach ($menul0 as $menu0)
                @include('gudang.partials.sidebar-nav-node', [
                    'node' => $menu0,
                    'depth' => 0,
                    'activePath' => $activePath,
                    'currentHref' => $currentHref,
                    'iconMap' => $iconMap,
                ])
            @endforeach
        </nav>
    </aside>

    {{-- Flyout panels for menu depth 2 (the deepest gudang level) — rendered OUTSIDE
         <aside>, appended near the end of <body>, so the sidebar's own
         overflow:hidden/max-height accordion can't clip them. One panel per depth-1
         (.nav-child) row that has children. --}}
    <div id="flyout-root">
        @foreach ($menul0 as $m0)
            @foreach ($m0->child ?? [] as $m1)
                @if (count($m1->child ?? []) > 0)
                    <div class="nav-flyout" id="flyout-{{ $m1['KODEMENU'] }}">
                        @foreach ($m1->child as $m2)
                            <div class="nav-flyout-item" onclick="window.location.href='{{ url($m2->href) }}'">
                                {{ $m2['Keterangan'] }}</div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        @endforeach
    </div>

    <div class="main">
        <header class="header">
            <div class="titleText" style="font-weight: bold" id="breadcrumb">
                @yield('breadcrumb_title', $autoPageTitle ?: 'Gudang')
            </div>
            <div class="header-right">
                <ul class="navbar-nav" style="list-style:none; display:flex; align-items:center; margin:0;">
                    @yield('buttons')
                </ul>
                <div class="period-badge">
                    Username: {{ \Auth::user()->username }} &ndash;
                    Periode: {!! $periode->bulan !!} / {!! $periode->tahun !!}
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

    <script src="{!! URL::asset('public/js/canvas/jquery.js') !!}"></script>
    <script src="{!! URL::asset('public/js/jquery.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/jquery-3.3.1.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/select2.min.js') !!}"></script>
    {{-- Pinned to Bootstrap 4.6.2 (bundle = Popper 1.16.1 + Bootstrap JS), matching the
         v4.5.0 canvas/bootstrap.css loaded above. This layout is intentionally NOT on the
         shared public/js/bootstrap.min.js (v4.0.0) used elsewhere in the app — see
         docs/bootstrap4-version-alignment-guide.md before touching this. --}}
    <script src="{!! URL::asset('public/js/bootstrap.bundle-4.6.2.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/alertify.js') !!}"></script>
    <script src="{!! URL::asset('public/js/autoNumeric.js') !!}"></script>
    <script src="{!! URL::asset('public/js/datatables.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/jquery-ui.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/qrcode.min.js') !!}"></script>

    <!-- Footer Scripts
 ============================================= -->
    <script src="{!! URL::asset('public/js/canvas/functions.js') !!}"></script>
    <script src="{!! URL::asset('public/js/canvas/JsBarcode.all.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/AutoNumeric.js') !!}" type="text/javascript"></script>

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
        // (at the SAME nesting level only) was open before. Ported from
        // report/newmaster2x.blade.php.
        function toggleNavGroup(itemEl) {
            var group = itemEl.closest(".nav-group");
            var parent = group.parentElement; // #nav (top level) or a .nav-children (nested)
            var wasOpen = group.classList.contains("open");
            parent.querySelectorAll(":scope > .nav-group.open").forEach(function(g) {
                if (g !== group) g.classList.remove("open");
            });
            group.classList.toggle("open", !wasOpen);
        }

        // Position + show/hide flyout panels (menu depth 2) on hover, using real
        // coordinates. Ported from report/newmaster2x.blade.php / references/newmaster.blade.php.
        // The panels live in #flyout-root (appended near the end of <body>, outside
        // .sidebar) specifically so the sidebar's own overflow:hidden/max-height
        // accordion can't clip them.
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
        }
        attachFlyoutHoverHandlers();

        function formatAngkaParse(angka) {

            return parseFloat(angka).toFixed(2)
        }

        function formatAngkaVal(angka) {
            return Number(angka.split(',').join(''))
        }


        function formatAngka(angkaString) {
            let tempAngka = angkaString.split('.')

            if (tempAngka[0][0] == '-') {
                let temp2 = ''

                let tempAngka1 = tempAngka[0].split('-')
                for (let i = 0; i < tempAngka1[1].length; i++) {
                    if (i != 0 && i % 3 == 0) {
                        temp2 = ',' + temp2
                    }
                    temp2 = tempAngka1[1][tempAngka1[1].length - i - 1] + temp2
                }
                temp2 += '.' + tempAngka[1]
                temp2 = '-' + temp2

                return temp2
            }
            let temp1 = ''
            for (let i = 0; i < tempAngka[0].length; i++) {
                if (i != 0 && i % 3 == 0) {
                    temp1 = ',' + temp1
                }
                temp1 = tempAngka[0][tempAngka[0].length - i - 1] + temp1
            }
            temp1 += '.' + tempAngka[1]
            return temp1
        }


        function numberWithCommas(n) {
            var parts = n.toString().split(".");
            return parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (parts[1] ? "." + parts[1] : "");
        }

        function toInteger(n) {
            return parseInt(n.replace(/,/g, ""));
        }

        function toFloat(n) {
            return parseFloat(n.replace(/,/g, ""));
        }

        function round(value, precision) {
            var multiplier = Math.pow(10, precision || 0);
            return Math.round(value * multiplier) / multiplier;
        }

        function middleTD() {}

        function format_date(date) {
            if (date == "" || date == null) {
                return "";
            }
            return date.split("-")[2] + "/" + date.split("-")[1] + "/" + date.split("-")[0];
        }

        function format_timestamp(date) {
            if (date == "" || date == null) return "";
            tgl = date.split(" ")[0];
            waktu = date.split(" ")[1];
            return tgl.split("-")[2] + "/" + tgl.split("-")[1] + "/" + tgl.split("-")[0] + " " + waktu;
        }
    </script>

    {{-- window.ReportTable — tabel interaktif ala report (drag kolom, gear menu, dst),
         lihat docs/new-slider-table-guide.md. --}}
    <script src="{!! URL::asset('public/js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>

    @yield('js')
</body>

</html>
