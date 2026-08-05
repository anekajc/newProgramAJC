@extends('newmaster')
@section('buttons')

@endsection
@section('content')

<link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}">

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Seting Perusahaan</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Atur Nomor Transaksi dan Perusahaan</h1>
    </div>
  </div> --}}

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="stp-card">

    <div class="stp-body">

      <div class="stp-side">
        <div class="stp-side-badge">
          <i class="bi bi-buildings"></i>
        </div>
      </div>

      <div class="stp-main">

        <ul class="nav nav-tabs stp-main-tabs" id="stpMainTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-perusahaan-btn" data-bs-toggle="tab" data-bs-target="#tab-perusahaan" type="button" role="tab">Perusahaan</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-nomortransaksi-btn" data-bs-toggle="tab" data-bs-target="#tab-nomortransaksi" type="button" role="tab">Set Nomor Transaksi</button>
          </li>
        </ul>

        <div class="tab-content stp-tab-content">

          <!-- ===================== TAB: PERUSAHAAN ===================== -->
          <div class="tab-pane fade show active" id="tab-perusahaan" role="tabpanel">

            <div class="stp-row">
              <label class="stp-label">Nama</label>
              <input type="text" class="form-control form-control-sm" id="input_nama">
            </div>

            <div class="stp-row">
              <label class="stp-label">Alamat</label>
              <div class="stp-field-stack">
                <input type="text" class="form-control form-control-sm mb-1" id="input_alamat1">
                <input type="text" class="form-control form-control-sm" id="input_alamat2">
              </div>
            </div>

            <div class="stp-row">
              <label class="stp-label">Kota</label>
              <input type="text" class="form-control form-control-sm" id="input_kota">
            </div>

            <div class="stp-row">
              <label class="stp-label">Telpon</label>
              <input type="text" class="form-control form-control-sm stp-input-narrow" id="input_telpon">
            </div>

            <div class="stp-row">
              <label class="stp-label">Fax</label>
              <div class="d-flex align-items-center stp-field-stack" style="gap: 16px;">
                <input type="text" class="form-control form-control-sm stp-input-narrow" id="input_fax">
                <label class="stp-label stp-inline-label mb-0">E-Mail</label>
                <input type="text" class="form-control form-control-sm" id="input_email">
              </div>
            </div>

            <ul class="nav nav-tabs stp-sub-tabs mt-2" id="stpNpwpTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-npwp1-btn" data-bs-toggle="tab" data-bs-target="#tab-npwp1" type="button" role="tab">NPWP 1</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-npwp2-btn" data-bs-toggle="tab" data-bs-target="#tab-npwp2" type="button" role="tab">NPWP 2</button>
              </li>
            </ul>

            <div class="tab-content stp-npwp-content">

              <!-- ---------- NPWP 1 ---------- -->
              <div class="tab-pane fade show active" id="tab-npwp1" role="tabpanel">

                <div class="stp-row">
                  <label class="stp-label">Nama PKP</label>
                  <input type="text" class="form-control form-control-sm" id="input_namapkp_1">
                </div>

                <div class="stp-row">
                  <label class="stp-label">Alamat PKP</label>
                  <div class="stp-field-stack">
                    <input type="text" class="form-control form-control-sm mb-1" id="input_alamatpkp1_1">
                    <input type="text" class="form-control form-control-sm" id="input_alamatpkp2_1">
                  </div>
                </div>

                <div class="stp-row">
                  <label class="stp-label">Kota PKP</label>
                  <input type="text" class="form-control form-control-sm" id="input_kotapkp_1">
                </div>

                <div class="stp-row">
                  <label class="stp-label">NPWP</label>
                  <input type="text" class="form-control form-control-sm stp-input-narrow" id="input_npwp_1">
                </div>

                <div class="stp-row">
                  <label class="stp-label">Tanggal Pengukuhan</label>
                  <input type="date" class="form-control form-control-sm stp-input-narrow" id="input_tglpengukuhan_1">
                </div>

                <div class="stp-row">
                  <label class="stp-label">Penandatanganan FPJ</label>
                  <input type="text" class="form-control form-control-sm" id="input_penandatangan_1">
                </div>

                <div class="stp-row">
                  <label class="stp-label">Jabatan</label>
                  <input type="text" class="form-control form-control-sm stp-input-narrow" id="input_jabatan_1">
                </div>

                <div class="stp-row">
                  <label class="stp-label">&nbsp;</label>
                  <div class="d-flex" style="gap: 32px;">

                    <div>
                      <div class="mb-1 stp-upload-label">Ttd Trading Director</div>
                      <div class="d-flex align-items-start" style="gap: 10px;">
                        <label class="btn btn-sm btn-outline-secondary mb-0">
                          <i class="bi bi-folder2-open"></i> Cari
                          <input type="file" accept="image/*" hidden onchange="previewUploadImage(this, 'stp_ttd_box_1')">
                        </label>
                        <div class="stp-upload-box" id="stp_ttd_box_1">
                          <i class="bi bi-image stp-upload-placeholder"></i>
                        </div>
                      </div>
                    </div>

                    <div>
                      <div class="mb-1 stp-upload-label">Logo</div>
                      <div class="d-flex align-items-start" style="gap: 10px;">
                        <label class="btn btn-sm btn-outline-secondary mb-0">
                          <i class="bi bi-folder2-open"></i> Cari
                          <input type="file" accept="image/*" hidden onchange="previewUploadImage(this, 'stp_logo_box_1')">
                        </label>
                        <div class="stp-upload-box" id="stp_logo_box_1">
                          <i class="bi bi-image stp-upload-placeholder"></i>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>

              </div>

              <!-- ---------- NPWP 2 ---------- -->
              <div class="tab-pane fade" id="tab-npwp2" role="tabpanel">

                <div class="stp-row">
                  <label class="stp-label">Nama PKP</label>
                  <input type="text" class="form-control form-control-sm" id="input_namapkp_2">
                </div>

                <div class="stp-row">
                  <label class="stp-label">Alamat PKP</label>
                  <div class="stp-field-stack">
                    <input type="text" class="form-control form-control-sm mb-1" id="input_alamatpkp1_2">
                    <input type="text" class="form-control form-control-sm" id="input_alamatpkp2_2">
                  </div>
                </div>

                <div class="stp-row">
                  <label class="stp-label">Kota PKP</label>
                  <input type="text" class="form-control form-control-sm" id="input_kotapkp_2">
                </div>

                <div class="stp-row">
                  <label class="stp-label">NPWP</label>
                  <input type="text" class="form-control form-control-sm stp-input-narrow" id="input_npwp_2">
                </div>

                <div class="stp-row">
                  <label class="stp-label">Tanggal Pengukuhan</label>
                  <input type="date" class="form-control form-control-sm stp-input-narrow" id="input_tglpengukuhan_2">
                </div>

                <div class="stp-row">
                  <label class="stp-label">Penandatanganan FPJ</label>
                  <input type="text" class="form-control form-control-sm" id="input_penandatangan_2">
                </div>

                <div class="stp-row">
                  <label class="stp-label">Jabatan</label>
                  <input type="text" class="form-control form-control-sm stp-input-narrow" id="input_jabatan_2">
                </div>

                <div class="stp-row">
                  <label class="stp-label">&nbsp;</label>
                  <div class="d-flex" style="gap: 32px;">

                    <div>
                      <div class="mb-1 stp-upload-label">Ttd Trading Director</div>
                      <div class="d-flex align-items-start" style="gap: 10px;">
                        <label class="btn btn-sm btn-outline-secondary mb-0">
                          <i class="bi bi-folder2-open"></i> Cari
                          <input type="file" accept="image/*" hidden onchange="previewUploadImage(this, 'stp_ttd_box_2')">
                        </label>
                        <div class="stp-upload-box" id="stp_ttd_box_2">
                          <i class="bi bi-image stp-upload-placeholder"></i>
                        </div>
                      </div>
                    </div>

                    <div>
                      <div class="mb-1 stp-upload-label">Logo</div>
                      <div class="d-flex align-items-start" style="gap: 10px;">
                        <label class="btn btn-sm btn-outline-secondary mb-0">
                          <i class="bi bi-folder2-open"></i> Cari
                          <input type="file" accept="image/*" hidden onchange="previewUploadImage(this, 'stp_logo_box_2')">
                        </label>
                        <div class="stp-upload-box" id="stp_logo_box_2">
                          <i class="bi bi-image stp-upload-placeholder"></i>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>

              </div>

            </div>

          </div>

          <!-- ===================== TAB: SET NOMOR TRANSAKSI ===================== -->
          <div class="tab-pane fade" id="tab-nomortransaksi" role="tabpanel">

            <div class="stn-wrap">

              <!-- ---------- LEFT: KODE TRANSAKSI ---------- -->
              <div class="stn-left">
                <div class="stn-section-title">Kode Transaksi</div>

                <div class="stn-grid">

                  <div class="stn-cell">
                    <label class="stn-label">Kas Masuk</label>
                    <input type="text" class="form-control form-control-sm stn-code" id="input_kode_kasmasuk" value="BKM">
                    <input type="text" class="form-control form-control-sm stn-number" id="input_nomor_kasmasuk">
                  </div>
                  <div class="stn-cell">
                    <label class="stn-label">Kas Keluar</label>
                    <input type="text" class="form-control form-control-sm stn-code" id="input_kode_kaskeluar" value="BKK">
                    <input type="text" class="form-control form-control-sm stn-number" id="input_nomor_kaskeluar">
                  </div>

                  <div class="stn-cell">
                    <label class="stn-label">Bank Masuk</label>
                    <input type="text" class="form-control form-control-sm stn-code" id="input_kode_bankmasuk" value="BBM">
                    <input type="text" class="form-control form-control-sm stn-number" id="input_nomor_bankmasuk">
                  </div>
                  <div class="stn-cell">
                    <label class="stn-label">Bank Keluar</label>
                    <input type="text" class="form-control form-control-sm stn-code" id="input_kode_bankkeluar" value="BBK">
                    <input type="text" class="form-control form-control-sm stn-number" id="input_nomor_bankkeluar">
                  </div>

                  <div class="stn-cell">
                    <label class="stn-label">Bukti Memorial</label>
                    <input type="text" class="form-control form-control-sm stn-code" id="input_kode_buktimemorial" value="BMM">
                    <input type="text" class="form-control form-control-sm stn-number" id="input_nomor_buktimemorial">
                  </div>
                  <div class="stn-cell"></div>

                  <div class="stn-cell">
                    <label class="stn-label">Jurnal koreksi</label>
                    <input type="text" class="form-control form-control-sm stn-code" id="input_kode_jurnalkoreksi" value="BJK">
                    <input type="text" class="form-control form-control-sm stn-number" id="input_nomor_jurnalkoreksi">
                  </div>
                  <div class="stn-cell"></div>

                </div>

                <div class="stn-grid mt-4">

                  <div class="stn-cell">
                    <label class="stn-label">SO</label>
                    <input type="text" class="form-control form-control-sm stn-code" id="input_kode_so" value="SO">
                    <input type="text" class="form-control form-control-sm stn-number" id="input_nomor_so">
                  </div>
                  <div class="stn-cell">
                    <label class="stn-label">Perintah Pengiriman</label>
                    <input type="text" class="form-control form-control-sm stn-code" id="input_kode_perintahkirim" value="DO">
                    <input type="text" class="form-control form-control-sm stn-number" id="input_nomor_perintahkirim">
                  </div>

                  <div class="stn-cell">
                    <label class="stn-label">Pengiriman Barang</label>
                    <input type="text" class="form-control form-control-sm stn-code" id="input_kode_pengirimanbarang" value="SJ">
                    <input type="text" class="form-control form-control-sm stn-number" id="input_nomor_pengirimanbarang">
                  </div>
                  <div class="stn-cell">
                    <label class="stn-label">Invoice Penjualan</label>
                    <input type="text" class="form-control form-control-sm stn-code" id="input_kode_invoicepenjualan" value="INVC">
                    <input type="text" class="form-control form-control-sm stn-number" id="input_nomor_invoicepenjualan">
                  </div>

                  <div class="stn-cell"></div>
                  <div class="stn-cell">
                    <label class="stn-label">Retur Penjualan</label>
                    <input type="text" class="form-control form-control-sm stn-code" id="input_kode_returpenjualan" value="RINVC">
                    <input type="text" class="form-control form-control-sm stn-number" id="input_nomor_returpenjualan">
                  </div>

                </div>

                <div class="stn-inisial-row mt-4">
                  <label class="stn-label">Inisial Perusahaan</label>
                  <input type="text" class="form-control form-control-sm stn-code" id="input_kode_inisialperusahaan" value="SML">
                  <label class="stn-label stn-tag-label">Tag</label>
                  <input type="text" class="form-control form-control-sm stn-code" id="input_kode_tag" value="SMX">
                </div>

              </div>

              <!-- ---------- RIGHT: KONFIGURASI ---------- -->
              <div class="stn-right">
                <div class="stn-section-title">Konfigurasi</div>

                <div class="stn-config-row">
                  <label class="stn-config-label">Pemisah</label>
                  <select class="form-select form-select-sm" id="input_pemisah" onchange="updateContohFormat()">
                    <option value="/">/</option>
                    <option value="-">-</option>
                    <option value=".">.</option>
                    <option value="">(tanpa pemisah)</option>
                  </select>
                </div>

                <div class="stn-config-row mt-3">
                  <label class="stn-config-label">Format Nomor Transaksi</label>

                  <select class="form-select form-select-sm mb-2" id="input_format1" onchange="updateContohFormat()">
                    <option>Inisial Perusahaan</option>
                    <option>Kode Transaksi</option>
                    <option>Nomor Urut</option>
                    <option>MMYY</option>
                    <option>YYMM</option>
                    <option>Tag</option>
                  </select>

                  <select class="form-select form-select-sm mb-2" id="input_format2" onchange="updateContohFormat()">
                    <option>Kode Transaksi</option>
                    <option>Inisial Perusahaan</option>
                    <option>Nomor Urut</option>
                    <option>MMYY</option>
                    <option>YYMM</option>
                    <option>Tag</option>
                  </select>

                  <select class="form-select form-select-sm mb-2" id="input_format3" onchange="updateContohFormat()">
                    <option>Nomor Urut</option>
                    <option>Inisial Perusahaan</option>
                    <option>Kode Transaksi</option>
                    <option>MMYY</option>
                    <option>YYMM</option>
                    <option>Tag</option>
                  </select>

                  <select class="form-select form-select-sm" id="input_format4" onchange="updateContohFormat()">
                    <option>MMYY</option>
                    <option>YYMM</option>
                    <option>Inisial Perusahaan</option>
                    <option>Kode Transaksi</option>
                    <option>Nomor Urut</option>
                    <option>Tag</option>
                  </select>
                </div>

                <div class="stn-config-row mt-3">
                  <label class="stn-config-label">Reset Nomor Per</label>
                  <select class="form-select form-select-sm" id="input_resetnomor" onchange="updateContohFormat()">
                    <option>Bulan</option>
                    <option>Tahun</option>
                    <option>Tidak Pernah</option>
                  </select>
                </div>

                <div class="stn-config-row mt-3">
                  <label class="stn-config-label">Contoh Format</label>
                  <input type="text" class="form-control form-control-sm" id="input_contohformat" disabled>
                </div>

                <div class="stn-config-row mt-3">
                  <label class="stn-config-label">No. Seri Faktur Pajak</label>
                  <textarea class="form-control form-control-sm" id="input_noserifakturpajak" rows="3"></textarea>
                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

    <div class="stp-footer">
      <button type="button" class="btn stp-btn-ok" onclick="submitSetingPerusahaan()">
        <i class="bi bi-check-lg"></i> Ok
      </button>
      <button type="button" class="btn stp-btn-cancel" onclick="batalSetingPerusahaan()">
        <i class="bi bi-x-lg"></i> Batal
      </button>
    </div>

  </div>

</div>

@endsection

@section('css')
<style>

  .stp-card {
    background: #fff;
    border: 1px solid #d9d9d9;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    overflow: hidden;
  }

  .stp-body {
    display: flex;
  }

  .stp-side {
    width: 170px;
    flex-shrink: 0;
    background: linear-gradient(160deg, #0d1b3d 0%, #1c3f8f 55%, #4f83e0 100%);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .stp-side-badge {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: rgba(255,255,255,0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    color: #fff;
  }

  .stp-main {
    flex: 1;
    min-width: 0;
    padding: 14px 22px 0 22px;
  }

  .stp-main-tabs .nav-link {
    font-weight: 600;
    font-size: 14px;
    color: #555;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 8px 14px;
  }

  .stp-main-tabs .nav-link.active {
    color: #0d1b3d;
    border-bottom: 2px solid #0d1b3d;
    background: transparent;
  }

  .stp-tab-content {
    padding-top: 12px;
  }

  .stp-row {
    display: flex;
    align-items: flex-start;
    margin-bottom: 7px;
  }

  .stp-label {
    width: 150px;
    flex-shrink: 0;
    font-size: 13px;
    color: #333;
    padding-top: 5px;
  }

  .stp-inline-label {
    width: auto;
    padding-top: 5px;
    white-space: nowrap;
  }

  .stp-field-stack {
    flex: 1;
  }

  .stp-input-narrow {
    max-width: 240px;
  }

  .stp-sub-tabs .nav-link {
    font-size: 13px;
    padding: 5px 14px;
    color: #555;
    border: 1px solid transparent;
  }

  .stp-sub-tabs .nav-link.active {
    background: #f4f6fb;
    border: 1px solid #d9d9d9;
    border-bottom-color: #f4f6fb;
    color: #0d1b3d;
    font-weight: 600;
  }

  .stp-npwp-content {
    background: #f4f6fb;
    border: 1px solid #d9d9d9;
    border-top: none;
    border-radius: 0 0 6px 6px;
    padding: 14px 16px 16px;
  }

  .stp-upload-label {
    font-size: 12px;
    color: #333;
  }

  .stp-upload-box {
    width: 110px;
    height: 90px;
    border: 1px solid #c9c9c9;
    border-radius: 6px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
  }

  .stp-upload-box img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }

  .stp-upload-placeholder {
    color: #bbb;
    font-size: 22px;
  }

  .stp-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 12px 22px;
    border-top: 1px solid #e5e5e5;
    background: #fafafa;
  }

  .stp-btn-ok {
    background: #f1f1f1;
    border: 1px solid #b9b9b9;
    color: #1a7a1a;
    font-weight: 600;
    padding: 5px 20px;
    font-size: 13px;
  }

  .stp-btn-ok:hover {
    background: #e6f4e6;
  }

  .stp-btn-cancel {
    background: #f1f1f1;
    border: 1px solid #b9b9b9;
    color: #b02a2a;
    font-weight: 600;
    padding: 5px 20px;
    font-size: 13px;
  }

  .stp-btn-cancel:hover {
    background: #fbe9e9;
  }

  /* ===================== SET NOMOR TRANSAKSI ===================== */

  .stn-wrap {
    display: flex;
    gap: 24px;
    padding-bottom: 20px;
  }

  .stn-left {
    flex: 1;
    min-width: 0;
  }

  .stn-right {
    width: 260px;
    flex-shrink: 0;
    border-left: 1px solid #e2e2e2;
    padding-left: 22px;
  }

  .stn-section-title {
    font-size: 13px;
    font-weight: 700;
    color: #333;
    text-decoration: underline;
    margin-bottom: 14px;
  }

  .stn-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    row-gap: 10px;
    column-gap: 24px;
  }

  .stn-cell {
    display: flex;
    align-items: center;
    gap: 6px;
    min-height: 28px;
  }

  .stn-label {
    width: 130px;
    flex-shrink: 0;
    font-size: 13px;
    color: #333;
  }

  .stn-code {
    width: 56px;
    flex-shrink: 0;
    text-align: center;
    font-weight: 600;
  }

  .stn-number {
    width: 100px;
    flex-shrink: 0;
  }

  .stn-inisial-row {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .stn-tag-label {
    width: auto;
    margin-left: 14px;
    margin-right: 0;
  }

  .stn-config-row {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .stn-config-label {
    font-size: 12.5px;
    font-weight: 600;
    color: #333;
  }

</style>
@endsection

@section('js')
<script type="text/javascript">

$(document).ready(function () {
  document.getElementById('breadcrumb').innerHTML = "Atur Nomor Transaksi dan Perusahaan"
});

function previewUploadImage(inputEl, targetBoxId) {
  let file = inputEl.files && inputEl.files[0];
  if (!file) return;

  let reader = new FileReader();
  reader.onload = function (e) {
    document.getElementById(targetBoxId).innerHTML = `<img src="${e.target.result}" alt="preview">`;
  };
  reader.readAsDataURL(file);
}

function batalSetingPerusahaan() {
  // TODO: decide navigation/close behavior later
  console.log('batalSetingPerusahaan - UI only for now');
}

// ── Set Nomor Transaksi: live "Contoh Format" preview ─────────────────
const formatSampleMap = {
  'Inisial Perusahaan': 'SML',
  'Kode Transaksi':     'LPB',
  'Nomor Urut':         '00000',
  'MMYY':               '0116',
  'YYMM':               '1601',
  'Tag':                'SMX'
};

function updateContohFormat() {
  let pemisah = document.getElementById('input_pemisah').value;
  let parts = ['input_format1', 'input_format2', 'input_format3', 'input_format4']
    .map(id => formatSampleMap[document.getElementById(id).value] || '');

  document.getElementById('input_contohformat').value = parts.join(pemisah);
}

function loadAll(){
  
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('dbnomorspdetail') !!}",
    type: "get",
    async: false,
    success: function(res) {

      console.log(res)
      document.getElementById("input_nama").value = res[0].NAMA
      document.getElementById("input_alamat1").value = res[0].ALAMAT1
      document.getElementById("input_alamat2").value = res[0].ALAMAT2
      document.getElementById("input_kota").value = res[0].KOTA
      document.getElementById("input_telpon").value = res[0].Telpon
      document.getElementById("input_fax").value = res[0].Fax
      document.getElementById("input_email").value = res[0].email

      document.getElementById("input_namapkp_1").value = res[0].NAMAPKP
      document.getElementById("input_alamatpkp1_1").value = res[0].ALAMATPKP1
      document.getElementById("input_alamatpkp2_1").value = res[0].ALAMATPKP2
      document.getElementById("input_kotapkp_1").value = res[0].KOTAPKP
      document.getElementById("input_npwp_1").value = res[0].NPWP
      document.getElementById("input_tglpengukuhan_1").value = new Date(res[0].TGLPENGUKUHAN).toLocaleDateString('en-CA');
      document.getElementById("input_penandatangan_1").value = res[0].Direksi
      document.getElementById("input_jabatan_1").value = res[0].Jabatan

      document.getElementById("input_namapkp_2").value = res[0].NAMAPKP1
      document.getElementById("input_alamatpkp1_2").value = res[0].ALAMATPKP21
      document.getElementById("input_alamatpkp2_2").value = res[0].ALAMATPKP22
      document.getElementById("input_kotapkp_2").value = res[0].KOTAPKP1
      document.getElementById("input_npwp_2").value = res[0].NPWP1
      document.getElementById("input_tglpengukuhan_2").value = new Date(res[0].TGLPENGUKUHAN1).toLocaleDateString('en-CA');
      document.getElementById("input_penandatangan_2").value = res[0].Direksi
      document.getElementById("input_jabatan_2").value = res[0].Jabatan

    }})

}

function submitSetingPerusahaan() {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('setnomortransaksispedit') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nama:           $("#input_nama").val(),
      alamat1:        $("#input_alamat1").val(),
      alamat2:        $("#input_alamat2").val(),
      kota:           $("#input_kota").val(),
      telpon:         $("#input_telpon").val(),
      fax:            $("#input_fax").val(),
      email:          $("#input_email").val(),

      namapkp:        $("#input_namapkp_1").val(),
      alamatpkp1:     $("#input_alamatpkp1_1").val(),
      alamatpkp2:     $("#input_alamatpkp2_1").val(),
      kotapkp:        $("#input_kotapkp_1").val(),
      npwp:           $("#input_npwp_1").val(),
      tglpengukuhan:  $("#input_tglpengukuhan_1").val(),

      namapkp1:        $("#input_namapkp_2").val(),
      alamatpkp21:     $("#input_alamatpkp1_2").val(),
      alamatpkp22:     $("#input_alamatpkp2_2").val(),
      kotapkp1:        $("#input_kotapkp_2").val(),
      npwp1:           $("#input_npwp_2").val(),
      tglpengukuhan1:  $("#input_tglpengukuhan_2").val(),

      direksi: $("#input_penandatangan_1").val(),
      jabatan: $("#input_jabatan_1").val(),
    },
    success: function (res) {
      if (res != 1) {
        alertify.warning(res);
      } else {
        alertify.success("Data Perusahaan telah disimpan");
        loadAll();
      }
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

window.onload = function(){
  loadAll();
  updateContohFormat();
};

</script>
@endsection