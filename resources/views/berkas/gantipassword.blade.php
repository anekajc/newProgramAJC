@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Ganti Password')
@section('content')

{{-- Menu Berkas > Ganti Password (DBMENUWEB 0005). Halaman ini belum ada di newProgramAJC
     (dulu hanya ada di programajc), jadi menunya selalu 500. Tampilan kartu disamakan dengan
     Setup Periode Kerja / Kunci Periode. --}}

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="kp-wrap">
    <div class="kp-card">

      <div class="kp-header">
        <div class="kp-header-icon">
          <i class="bi bi-key-fill"></i>
        </div>
        <div>
          <div class="kp-header-title">Ganti Password</div>
          <div class="kp-header-subtitle">User {{ Auth::user()->username }}</div>
        </div>
      </div>

      <div class="kp-body">

        <div class="kp-field">
          <label for="input_gantipassword_lama">Password Lama</label>
          <input type="password" class="form-control" id="input_gantipassword_lama" placeholder="Password lama" autocomplete="current-password">
        </div>

        <div class="kp-field">
          <label for="input_gantipassword_password">Password Baru</label>
          <input type="password" class="form-control" id="input_gantipassword_password" placeholder="Password baru" maxlength="12" autocomplete="new-password">
        </div>

        <div class="kp-field">
          <label for="input_gantipassword_password2">Ulangi Password Baru</label>
          <input type="password" class="form-control" id="input_gantipassword_password2" placeholder="Ulangi password baru" maxlength="12" autocomplete="new-password">
        </div>

        <div class="kp-footer">
          <button type="button" class="kp-ok-btn" onclick="submitGantiPassword()">
            <i class="bi bi-check-lg"></i> Simpan
          </button>
        </div>

      </div>

    </div>
  </div>

</div>

@endsection

@section('css')
<style>

  .kp-wrap {
    display: flex;
    justify-content: center;
    padding: 30px 0;
  }

  .kp-card {
    width: 100%;
    max-width: 420px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.06);
  }

  .kp-header {
    display: flex;
    align-items: center;
    gap: 14px;
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #fff;
    padding: 18px 20px;
  }

  .kp-header-icon {
    width: 42px;
    height: 42px;
    flex-shrink: 0;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .kp-header-title {
    font-size: 17px;
    font-weight: 700;
  }

  .kp-header-subtitle {
    font-size: 12.5px;
    color: rgba(255,255,255,0.85);
    margin-top: 2px;
  }

  .kp-body {
    padding: 24px 22px 22px;
  }

  .kp-field {
    margin-bottom: 14px;
  }

  .kp-field label {
    display: block;
    font-weight: 600;
    font-size: 13.5px;
    color: #333;
    margin-bottom: 6px;
  }

  .kp-field .form-control {
    height: 38px !important;
    border-radius: 6px;
  }

  .kp-footer {
    display: flex;
    justify-content: flex-end;
    margin-top: 22px;
  }

  /* Tombol soft seperti tombol utama memorialkoreksi (.btn-dpp-utama). */
  .kp-ok-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #e8edff;
    color: #2563eb;
    border: 1px solid #cfdcff;
    padding: 7px 18px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: background 0.12s ease;
  }

  .kp-ok-btn:hover {
    background: #dce6ff;
    color: #1d4ed8;
  }

</style>
@endsection

@section('js')
<script type="text/javascript">

function submitGantiPassword () {

  let _token = $("#_token").val();
  let passwordLama = $("#input_gantipassword_lama").val();
  let password = $("#input_gantipassword_password").val();
  let password2 = $("#input_gantipassword_password2").val();

  if (!passwordLama) {
    alertify.warning('Password lama harus diisi')
    return
  }

  if (!password) {
    alertify.warning('Password baru harus diisi')
    return
  }

  if (password != password2) {
    alertify.warning('Ulangi password baru harus sama')
    return
  }

  $.ajax({
    url: "{!! url('gantipassworduser') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      passwordLama,
      password,
      password2,
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        alertify.success("Password telah diganti");
        document.getElementById("input_gantipassword_lama").value = ''
        document.getElementById("input_gantipassword_password").value = ''
        document.getElementById("input_gantipassword_password2").value = ''
      }

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan, silakan refresh browser')
    }})

}

</script>
@endsection
