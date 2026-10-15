<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>

  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="author" content="SemiColonWeb" />

  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600&display=swap" rel="stylesheet" />

  <title>AnekaJC - Login</title>

    <link rel="stylesheet" href="{!! URL::asset('css/loginpage.css') !!}?v={{ filemtime(public_path('css/loginpage.css')) }}">

</head>

<body class='background-login'>
  <div class="login-wrapper">

    <!-- Left: Branding Panel -->
    <div class="left-panel">
      {{-- Ikon memakai Logo NPWP 1 dari menu Set Nomor Transaksi, sama seperti sidebar newmasterTest
           (storage/app/gambar-perusahaan/logoperusahaan/npwp1.*, cadangan file lama di resources).
           Route /setnomortransaksilogo wajib login, jadi di halaman login gambarnya di-embed sebagai
           data URI. Belum ada logo = ikon grid lama. --}}
      @php
        $fileLogoLogin = \App\Http\Controllers\Berkas\SetNomorTransaksiController::fileLogoSidebar();
        $srcLogoLogin  = '';
        if ($fileLogoLogin) {
          $mimeLogoLogin = [
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'bmp'  => 'image/bmp',
            'webp' => 'image/webp',
          ][strtolower(pathinfo($fileLogoLogin, PATHINFO_EXTENSION))] ?? null;
          $isiLogoLogin = $mimeLogoLogin ? @file_get_contents($fileLogoLogin) : false;
          if ($isiLogoLogin !== false && $isiLogoLogin !== null) {
            $srcLogoLogin = 'data:' . $mimeLogoLogin . ';base64,' . base64_encode($isiLogoLogin);
          }
        }
      @endphp
      @if ($srcLogoLogin !== '')
        <div class="brand-icon" style="background:#fff; overflow:hidden;">
          <img src="{{ $srcLogoLogin }}" alt="Logo" style="width:100%; height:100%; object-fit:contain;">
        </div>
      @else
        <div class="brand-icon">
          <svg viewBox="0 0 24 24">
            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
          </svg>
        </div>
      @endif
      {{-- Nama mengikuti Inisial Perusahaan di menu Set Nomor Transaksi (DBNOMOR.ALIAS).
           Kalau query gagal / ALIAS kosong, nama dikosongkan. --}}
      @php
        try {
          $inisialPerusahaan = trim((string) (\DB::connection('SML')->select('select top 1 ALIAS from DBNOMOR')[0]->ALIAS ?? ''));
        } catch (\Throwable $e) {
          $inisialPerusahaan = '';
        }
      @endphp
      <p class="brand-name">{{ $inisialPerusahaan }}</p>
      <p class="brand-subtitle">ERP</p>
      {{-- <div class="feature-list">
        <div class="feature-item">
          <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Secure access
        </div>
        <div class="feature-item">
          <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          Real-time reports
        </div>
        <div class="feature-item">
          <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          Multi-user support
        </div>
      </div> --}}
    </div>

    <!-- Right: Login Form -->
    <div class="right-panel">
      <p class="form-title">Login</p>
      <p class="form-subtitle">Masukan Username dan Password</p>

      <form action="{{ url('checkLogin') }}" method="post" id="login-form">
        {{-- onsubmit="return checkOnline()" --}}

        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />

        <div class="form-group">
          <label for="username">Username</label>
          <div class="input-wrapper">
            <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <input type="text" id="username" name="username" placeholder="User" required autofocus autocomplete="off" />
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrapper has-toggle">
            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input type="password" id="password" name="password" placeholder="Password" />
            <button type="button" class="toggle-password" id="toggle-password" aria-label="Tampilkan password" aria-pressed="false">
              <svg class="icon-eye" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="icon-eye-off" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-login" id="login-form-submit">Sign in</button>
      </form>

      <p class="form-footer">AnekaJC &copy; 2025 &mdash; All rights reserved</p>
    </div>

  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/default.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>

  <script type="text/javascript">

    $(document).ready(function(){
      @if($errors->any())
        if(JSON.parse({!! $errors !!}) == "1") {
          alertify.alert('Login gagal!', 'Username dan Password tidak cocok.', function(){ });
        }
      @endif

      $.ajax({
        url     : "{{ url('updateIdle') }}",
        type    : "GET",
        async   : false
      });
    });

    // Show / hide password
    $('#toggle-password').on('click', function () {
      var $input  = $('#password');
      var visible = $input.attr('type') === 'password';
      $input.attr('type', visible ? 'text' : 'password');
      $(this).toggleClass('is-visible', visible)
             .attr('aria-pressed', visible)
             .attr('aria-label', visible ? 'Sembunyikan password' : 'Tampilkan password');
    });

    // Loading state: prevent double taps while the request is in flight
    var $loginBtn = $('#login-form-submit');
    $('#login-form').on('submit', function () {
      $loginBtn.prop('disabled', true).text('Memproses...');
    });
    // Restore the button if the page is restored from the back/forward cache
    $(window).on('pageshow', function () {
      $loginBtn.prop('disabled', false).text('Sign in');
    });

    function checkOnline() {
      var _token    = $("#_token").val();
      var _username = $("#username").val();
      var check     = 0;
      $.ajax({
        url     : "{{ url('checkOnline') }}",
        type    : "POST",
        async   : false,
        data    : { _token: _token, username: _username },
        success : function(result) { check = result; }
      });
      if (check == 0) { return true; }
      else { alertify.alert('Login gagal!', 'User sudah login.', function(){ }); return false; }
    }

  </script>


</body>
</html>
