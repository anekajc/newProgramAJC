@extends('newmaster')
@section('buttons')

@endsection
@section('content')

<link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}">

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Utilitas</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Setup Periode Kerja</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Setup Periode Kerja</h1>
    </div>
  </div> --}}

<div id="printContainer" style="display:none">

</div>

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <div class="kp-wrap">
    <div class="kp-card">

      <div class="kp-header">
        <div class="kp-header-icon">
          <i class="bi bi-calendar-week-fill"></i>
        </div>
        <div>
          <div class="kp-header-title">Setup Periode Kerja</div>
          <div class="kp-header-subtitle">Pilih Bulan dan Tahun, Klik Simpan</div>
        </div>
      </div>

      <div class="kp-body">

        <div class="kp-periode-row">
          <div class="kp-periode-field">
            <label for="input_periodekerja_bulan">Bulan</label>
            <select id="input_periodekerja_bulan" class="form-select kp-periode-select">
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>
            </select>
          </div>

          <div class="kp-periode-field">
            <label for="input_periodekerja_tahun">Tahun</label>
            <input type="number" class="form-control kp-tahun-input" id="input_periodekerja_tahun" placeholder="Tahun">
          </div>
        </div>

        <div class="kp-footer">
          <button type="button" class="kp-ok-btn" onclick="submitPeriodeKerja()">
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

  .kp-periode-row {
    display: flex;
    gap: 16px;
    margin-bottom: 24px;
  }

  .kp-periode-field {
    flex: 1;
  }

  .kp-periode-field label {
    display: block;
    font-weight: 600;
    font-size: 13.5px;
    color: #333;
    margin-bottom: 6px;
  }

  .kp-periode-select,
  .kp-tahun-input {
    width: 100%;
    padding: 8px 10px;
    border-radius: 6px;
    border: 1px solid #ced4da;
    font-weight: 600;
    color: #0d6efd;
    background: #eef4ff;
    text-align: center;
  }

  .kp-periode-select:focus,
  .kp-tahun-input:focus {
    outline: none;
    border-color: #0d6efd;
    box-shadow: 0 0 0 2px rgba(13,110,253,0.15);
  }

  .kp-footer {
    display: flex;
    justify-content: flex-end;
  }

  .kp-ok-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #2563eb;
    color: #fff;
    border: none;
    padding: 9px 22px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    box-shadow: 0 3px 8px rgba(37,99,235,0.25);
    transition: background 0.12s ease;
  }

  .kp-ok-btn:hover {
    background: #1d4ed8;
  }

</style>
@endsection

@section('js')
<script type="text/javascript">

$(document).ready(function () {
  let tahun = $("#periode_tahun").val();
  let bulan = $("#periode_bulan").val();

  document.getElementById("input_periodekerja_tahun").value = tahun;
  document.getElementById("input_periodekerja_bulan").value = bulan;

  document.getElementById('breadcrumb').innerHTML = "Setup Periode Kerja"
});

function submitPeriodeKerja () {
  let tahun = Number($("#input_periodekerja_tahun").val());
  let bulan = Number($("#input_periodekerja_bulan").val());
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('newsetupperiodekerjaupdate') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      bulan,
      tahun
    },
    success: function (res) {
      let el = document.getElementById("period-badge");
      if (el) {
        el.innerHTML = `Username: {{ Auth::user()->username }} &nbsp;–&nbsp; Periode: ${bulan} / ${tahun}`;
      }

      // Also keep the hidden periode_bulan/periode_tahun inputs on THIS page
      // in sync, so re-submitting on the same page uses the fresh values.
      document.getElementById("periode_bulan").value = bulan;
      document.getElementById("periode_tahun").value = tahun;

      alertify.success('Periode kerja telah disimpan');
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

</script>
@endsection