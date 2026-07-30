@extends('newmaster')
@section('buttons')

@endsection
@section('content')
<link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}">

      <style>
      .sp-length-wrap {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
      }

      .sp-length-wrap label {
        margin: 0; /* stops default label margin from pushing the select down/over */
      }

      .sp-length-wrap select {
        width: auto; /* stops form-select from stretching full-width and forcing a wrap */
      }
    </style>
  
{{-- <div class="sp-breadcrumb">
  <span>Beranda</span>
  <span class="sp-sep">›</span>
  <span>Master</span>
  <span class="sp-sep">›</span>
  <span class="sp-crumb-active">Giro</span>
</div> --}}

<div class="sp-page-head">
  <div>
    <h1>Master Giro</h1>
  </div>
  <div class="d-flex gap-2">
    <button id='divAddBuka' class="btn btn-action-primary" onclick="buttonAddBuka()">+ Add Giro Buka</button>
    <button id='divAddTerima' class="btn btn-action-primary" onclick="buttonAddTerima()" hidden>+ Add Giro Terima</button>
  </div>
</div>

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />
  <style>
  .radioChoiceMaster {
    display: inline-flex;
    list-style: none;
    margin: 0;
    background-color: #fff;
    border: 1px solid #e9ecef;
    border-radius: 999px;
    padding: 4px;
    gap: 4px;
  }

  .radioChoiceMaster-item {
    display: flex;
  }

  .radioChoiceMaster-btn {
    border: none;
    border-radius: 999px;
    padding: 8px 18px;
    font-size: 14px;
    font-weight: 500;
    color: #6c757d;
    background-color: transparent;
    transition: all 0.2s ease;
    white-space: nowrap;
    outline: none;
    box-shadow: none;
    cursor: pointer;
  }

  .radioChoiceMaster-btn:hover {
    color: #212529;
    background-color: rgba(0,0,0,0.04);
  }

  .radioChoiceMaster-btn:focus,
  .radioChoiceMaster-btn:focus-visible {
    outline: none;
    box-shadow: none;
  }

  .radioChoiceMaster-btn.active {
    color: #fff;
    background-color: #007bff;
    box-shadow: 0 2px 6px rgba(0,123,255,0.35);
  }

  .sp-length-wrap {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
  }

  .sp-length-wrap label {
    margin: 0;
  }

  .sp-length-wrap select {
    width: auto;
  }

  .sp-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    row-gap: 10px;
    column-gap: 12px;
  }

  .sp-filter-wrap select {
    width: auto;
    min-width: 220px;
  }

  .sp-length-wrap {
    margin-left: auto;
  }
</style>

<div class="sp-toolbar">
  <div class="sp-search-wrap">
    <i class="bi bi-search sp-search-icon"></i>
    <input type="text" id="tabel_filter_visual" placeholder="Cari user...">
  </div>

  <div class="sp-filter-wrap">
    <ul class="radioChoiceMaster" id="giroTab" role="tablist">
      <li class="radioChoiceMaster-item" role="presentation">
        <button onclick="hideButtonTerima()" class="radioChoiceMaster-btn active" id="tab-dibuka-btn" data-bs-toggle="tab" data-bs-target="#tab-dibuka" type="button" role="tab">Daftar Giro Dibuka</button>
      </li>
      <li class="radioChoiceMaster-item" role="presentation">
        <button onclick="hideButtonBuka()" class="radioChoiceMaster-btn" id="tab-diterima-btn" data-bs-toggle="tab" data-bs-target="#tab-diterima" type="button" role="tab">Daftar Giro Diterima</button>
      </li>
    </ul>
  </div>

  <div class="sp-length-wrap">
    <label for="tabel_length_visual">Tampilkan</label>
    <select id="tabel_length_visual" class="form-select form-select-sm">
      <option value="10">10</option>
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
      <option value="-1">Semua</option>
    </select>
  </div>
</div>

  <div class="tab-content">

    <!-- ---------- DAFTAR GIRO DIBUKA ---------- -->
    <div class="tab-pane fade show active" id="tab-dibuka" role="tabpanel">
      <div class="table-outer">
        <div class="table-wrap">
          <table class="tb" id="tabel_dibuka">
            <thead style="white-space:nowrap;">
              <tr>
                <th scope="col">Actions</th>
                <th scope="col">Bank</th>
                <th scope="col">No. Giro</th>
                <th scope="col">Tanggal Giro Jatuh Tempo</th>
                <th scope="col">Valas</th>
                <th scope="col">Kurs</th>
                <th scope="col">Debet Rupiah</th>
                <th scope="col">Kredit Rupiah</th>
                <th scope="col">Debet Valas</th>
                <th scope="col">Kredit Valas</th>
                <th scope="col">Tanggal Buka Giro</th>
                <th scope="col">Bukti Buka Giro</th>
                <th scope="col">Keterangan Buka Giro</th>
                <th scope="col">Tanggal Pencairan Giro</th>
                <th scope="col">Bukti Pencairan Giro</th>
                <th scope="col">Keterangan Pencairan Giro</th>
              </tr>
            </thead>
            <tbody id="tabel_dataDibuka" class="text-left"></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ---------- DAFTAR GIRO DITERIMA ---------- -->
    <div class="tab-pane fade" id="tab-diterima" role="tabpanel">
      <div class="table-outer">
        <div class="table-wrap">
          <table class="tb" id="tabel_diterima">
            <thead style="white-space:nowrap;">
              <tr>
                <th scope="col">Actions</th>
                <th scope="col">Bank</th>
                <th scope="col">No. Giro</th>
                <th scope="col">Perkiraan Kas</th>
                <th scope="col">Tanggal Giro Jatuh Tempo</th>
                <th scope="col">Valas</th>
                <th scope="col">Kurs</th>
                <th scope="col">Debet Rupiah</th>
                <th scope="col">Kredit Rupiah</th>
                <th scope="col">Debet Valas</th>
                <th scope="col">Kredit Valas</th>
                <th scope="col">Tanggal Terima Giro</th>
                <th scope="col">Bukti Terima Giro</th>
                <th scope="col">Keterangan Terima Giro</th>
                <th scope="col">Tanggal Pencairan Giro</th>
                <th scope="col">Bukti Pencairan Giro</th>
                <th scope="col">Keterangan Pencairan Giro</th>
              </tr>
            </thead>
            <tbody id="tabel_dataDiterima" class="text-left"></tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

</div>

<div id="printContainer" style="display:none"></div>


<!-- start modal add terima giro-->
<div class="modal fade"  id="formTerima" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 600px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Giro Terima</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Bank</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_bank" placeholder="Bank">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">No. Giro</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_noGiro" placeholder="No. Giro">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Giro</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_add_tglGiro">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4">
                <label class="form-label">Valas</label>
              </div>
              <div class="col-3">
                <select id="input_add_valas" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  @foreach ($listDataValas as $valas)
                      <option value="{{ $valas->KODEVLS }}" 
                              data-kurs="{{ $valas->KURS }}"
                              {{ $valas->KODEVLS == 'IDR' ? 'selected' : '' }}>
                          {{ $valas->KODEVLS }}
                      </option>
                  @endforeach
                </select>
              </div>
              <div class="col-2">
                <label class="form-label">Kurs</label>
              </div>
              <div class="col-3">
                <input type="text" class="form-control" id="input_add_kurs" placeholder="Kurs" disabled>
              </div>
            </div>
            
            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nilai Giro</label>
                </div>
              </div>

              <div class="col-4">
                <div class="form-group">
                <input type="text" class="form-control text-right" id="input_add_nilaiGiro" 
                  value="0.00" 
                  style="font-variant-numeric: tabular-nums;" 
                  oninput="formatNumber(this)">
                </div>
              </div>

              <div class="col-4">
                <div class="form-group">
                  
                <input type="text" class="form-control text-right" id="input_add_nilaiGiroRp" 
                  value="0.00" 
                  style="font-variant-numeric: tabular-nums;" 
                  oninput="formatNumber(this)">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Terima</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_add_tglTerima" >
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Bukti Terima</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_buktiTerima" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Keterangan</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_keterangan">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Cair</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_add_tglCair" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Bukti Cair</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_buktiCair" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Keterangan Cair</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_keteranganCair" disabled>
                </div>
              </div>

            </div>
            
          <div class="row mt-2 align-items-center">
            <div class="col-4">
              <label class="form-label">Perkiraan Kas</label>
            </div>
            <div class="col-8">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_perkiraanKas" placeholder="Perkiraan Kas">
                <button type="button" class="btn btn-primary btn-select" onclick="buttonSelectPerkiraanKas()">+</button>
              </div>
            </div>
          </div>

    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-primary" onclick="submitAddTerima()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add terima giro-->

<!-- start modal add buka giro-->
<div class="modal fade"  id="formBuka" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 600px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Giro Buka</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />
          
          <div class="row mt-2 align-items-center">
            <div class="col-4">
              <label class="form-label">Bank</label>
            </div>
            <div class="col-8">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add2_bank" placeholder="Bank">
                <button type="button" class="btn btn-primary btn-select" onclick="buttonSelectBank()">+</button>
              </div>
            </div>
          </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">No. Giro</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add2_noGiro" placeholder="No. Giro">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Giro</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_add2_tglGiro">
                </div>
              </div>

            </div>
            
            <div class="row mt-2">
              <div class="col-4">
                <label class="form-label">Valas</label>
              </div>
              <div class="col-3">
                <select id="input_add2_valas" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  @foreach ($listDataValas as $valas)
                      <option value="{{ $valas->KODEVLS }}" 
                              data-kurs="{{ $valas->KURS }}"
                              {{ $valas->KODEVLS == 'IDR' ? 'selected' : '' }}>
                          {{ $valas->KODEVLS }}
                      </option>
                  @endforeach
                </select>
              </div>
              <div class="col-2">
                <label class="form-label">Kurs</label>
              </div>
              <div class="col-3">
                <input type="text" class="form-control text-right" id="input_add2_kurs" placeholder="Kurs" disabled>
              </div>
            </div>

            <div class="row mt-2">

              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nilai Giro</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                <input type="text" class="form-control text-right" id="input_add2_nilaiGiro" 
                  value="0.00" 
                  style="font-variant-numeric: tabular-nums;" 
                  oninput="formatNumber(this)">
                </div>
              </div>

              <div class="col-4">
                <div class="form-group">
                  
                <input type="text" class="form-control text-right" id="input_add2_nilaiGiroRp" 
                  value="0.00" 
                  style="font-variant-numeric: tabular-nums;" 
                  oninput="formatNumber(this)">
                </div>
              </div>

            </div>


            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Buka</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_add2_tglBuka" >
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Bukti Buka</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add2_buktiBuka" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Keterangan</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add2_keterangan">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Cair</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_add2_tglCair" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Bukti Cair</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add2_buktiCair" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Keterangan Cair</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add2_keteranganCair" disabled>
                </div>
              </div>

            </div>

    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-primary" onclick="submitAddBuka()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add buka giro-->

<!-- start modal edit terima giro-->
<div class="modal fade"  id="formEditTerima" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 600px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Giro Terima</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_edit_noUrut" value="" />

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Bank</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_bank" placeholder="Bank" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">No. Giro</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_noGiro" placeholder="No. Giro" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Giro</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_edit_tglGiro">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4">
                <label class="form-label">Valas</label>
              </div>
              <div class="col-3">
                <select id="input_edit_valas" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  @foreach ($listDataValas as $valas)
                      <option value="{{ $valas->KODEVLS }}" 
                              data-kurs="{{ $valas->KURS }}"
                              {{ $valas->KODEVLS == 'IDR' ? 'selected' : '' }}>
                          {{ $valas->KODEVLS }}
                      </option>
                  @endforeach
                </select>
              </div>
              <div class="col-2">
                <label class="form-label">Kurs</label>
              </div>
              <div class="col-3">
                <input type="text" class="form-control text-right" id="input_edit_kurs" placeholder="Kurs" disabled>
              </div>
            </div>

            
            <div class="row mt-2">

              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nilai Giro</label>
                </div>
              </div>

              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_edit_nilaiGiro">
                </div>
              </div>
 
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_edit_nilaiGiroRp">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Terima</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_edit_tglTerima" >
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Bukti Terima</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_buktiTerima" placeholder="Bukti Terima" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Keterangan</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_keterangan">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Cair</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_edit_tglCair" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Bukti Cair</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_buktiCair" placeholder="Bukti Cair" disabled>
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Keterangan Cair</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_keteranganCair" disabled>
                </div>
              </div>
            </div>

          <div class="row mt-2 align-items-center">
            <div class="col-4">
              <label class="form-label">Perkiraan Kas</label>
            </div>
            <div class="col-8">
              <div class="input-group">
                <input type="text" class="form-control" id="input_edit_perkiraanKas" placeholder="Perkiraan Kas">
                <button type="button" class="btn btn-primary btn-select" onclick="buttonSelectPerkiraanKas()">+</button>
              </div>
            </div>
          </div>

    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-primary" onclick="submitEditTerima()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal edit terima giro-->

<!-- start modal edit buka giro-->
<div class="modal fade"  id="formEditBuka" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 600px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Giro Buka</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
            <div class="col-4 text-left">
              <div class="form-group text-left">
                <label class="text-left">Bank</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_edit2_bank" placeholder="Bank" disabled>
              </div>
            </div>

          </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">No. Giro</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit2_noGiro" placeholder="No. Giro" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Giro</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_edit2_tglGiro">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4">
                <label class="form-label">Valas</label>
              </div>
              <div class="col-3">
                <select id="input_edit2_valas" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  @foreach ($listDataValas as $valas)
                      <option value="{{ $valas->KODEVLS }}" 
                              data-kurs="{{ $valas->KURS }}"
                              {{ $valas->KODEVLS == 'IDR' ? 'selected' : '' }}>
                          {{ $valas->KODEVLS }}
                      </option>
                  @endforeach
                </select>
              </div>
              <div class="col-2">
                <label class="form-label">Kurs</label>
              </div>
              <div class="col-3">
                <input type="text" class="form-control text-right" id="input_edit2_kurs" placeholder="Kurs" disabled>
              </div>
            </div>

            
            <div class="row mt-2">

              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nilai Giro</label>
                </div>
              </div>

              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_edit2_nilaiGiro" placeholder="Nilai Giro">
                </div>
              </div>
 
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_edit2_nilaiGiroRp">
                </div>
              </div>

            </div>


            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Buka</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_edit2_tglBuka" >
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Bukti Buka</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit2_buktiBuka" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Keterangan</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit2_keterangan">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Cair</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_edit2_tglCair" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Bukti Cair</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit2_buktiCair" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Keterangan Cair</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit2_keteranganCair" disabled>
                </div>
              </div>

            </div>

    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-primary" onclick="submitEditBuka()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal edit buka giro-->

<!-- start modal pilih Kas -->
<div class="modal fade"  id="formSelectKas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Select Kas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelAktivaSelectPerkiraan" class="table table-bordered table-striped"  >
          <thead id='theadCustom' class="text-center">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
            </tr>
          </thead>

          <tbody id="tabel_dataAktivaSelectPerkiraan" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihPerkiraan()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal pilih Kas-->

<!-- start modal pilih Bank -->
<div class="modal fade"  id="formSelectBank" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Select Bank</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelBukaSelectBank" class="table table-bordered table-striped"  >
          <thead id='theadCustom' class="text-center">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataBukaSelectBank" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihPerkiraan()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>

    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal pilih Bank-->

@endsection

@section('js')
<script src="{{ asset('js/masterTable.js') }}"></script>
<script type="text/javascript">

let dataRefresh = []

function hideButtonBuka(){
  document.getElementById('divAddBuka').hidden = true;
  document.getElementById('divAddTerima').hidden = false;
}

function hideButtonTerima(){
  document.getElementById('divAddBuka').hidden = false;
  document.getElementById('divAddTerima').hidden = true;
}

function loadAllBuka () {
  console.log('asd')
  let _token = $("#_token").val();

  $('#tabel_dibuka').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastergiroloadallbuka') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      console.log(res)
      dataRefresh = res
  }})

  let rowTable = ""
  dataRefresh.forEach((item, i) => {
    let temp = ""

    rowTable += `<tr>
    <td style="white-space:nowrap;" class='text-center'>
      <div class="action-buttons-wrap">
          <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEditBuka('${item.NoGiro}')"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDeleteBuka('${item.NoGiro}')"><i class="bi bi-trash"></i></button>
      </div>
    </td>
    <td>${ item.Bank }</td>
    <td>${ item.NoGiro }</td>
    <td>${(new Date(item.TglGiro)).toLocaleDateString('en-CA')}</td>
    <td>${ item.Kodevls }</td>
    <td class='text-right'>${ item.Kurs }</td>
    <td class='text-right'>${ parseFloat(item.DebetRp).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }</td>
    <td class='text-right'>${ parseFloat(item.KreditRp).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }</td>
    <td class='text-right'>${ parseFloat(item.Debet).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }</td>
    <td class='text-right'>${ parseFloat(item.Kredit).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }</td>
    <td>${(new Date(item.TglBuka)).toLocaleDateString('en-CA')}</td>
    <td>${ item.BuktiBuka }</td>
    <td>${ item.Keterangan }</td>
    <td>${item.TglCair ? (new Date(item.TglCair)).toLocaleDateString('en-CA') : ''}</td>
    <td>${ item.BuktiCair }</td>
    <td>${ item.KeteranganCair }</td>

    </tr>`
  });

 let currentLength = $("#tabel_length_visual").val() ? Number($("#tabel_length_visual").val()) : 10;

  document.getElementById("tabel_dataDibuka").innerHTML = rowTable
  $("#tabel_dibuka").DataTable({
    "lengthChange": true,
    "paging": true,
    "paging": true,
    "searching": true,
    "pageLength": currentLength,
    "dom": 'tip'
  });

}

function loadAllTerima () {

  let _token = $("#_token").val();

  $('#tabel_diterima').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastergiroloadallterima') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      console.log(res)
      dataRefresh = res
  }})

  let rowTable = ""
  dataRefresh.forEach((item, i) => {
    let temp = ""

    rowTable += `
    <tr>
    <td style="white-space:nowrap;" class='text-center'>
      <div class="action-buttons-wrap">
          <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEditTerima('${item.NoGiro}')"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDeleteTerima('${item.NoGiro}')"><i class="bi bi-trash"></i></button>
      </div>
    </td>
    <td>${ item.Bank }</td>
    <td>${ item.NoGiro }</td>
    <td>${ item.Kas }</td>
    <td>${(new Date(item.TglGiro)).toLocaleDateString('en-CA')}</td>
    <td>${ item.Kodevls }</td>
    <td class='text-right'>${ parseFloat(item.Kurs).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }</td>
    <td class='text-right'>${ parseFloat(item.DebetRp).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }</td>
    <td class='text-right'>${ parseFloat(item.KreditRp).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }</td>
    <td class='text-right'>${ parseFloat(item.Debet).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }</td>
    <td class='text-right'>${ parseFloat(item.Kredit).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }</td>
    <td>${ (new Date(item.TglBuka)).toLocaleDateString('en-CA')}</td>
    <td>${ item.BuktiBuka }</td>
    <td>${ item.Keterangan }</td>
    <td>${ (new Date(item.TglCair)).toLocaleDateString('en-CA')}</td>
    <td>${ item.BuktiCair }</td>
    <td>${ item.KeteranganCair }</td>
    </tr>`
  });

  let currentLength = $("#tabel_length_visual").val() ? Number($("#tabel_length_visual").val()) : 10;
  document.getElementById("tabel_dataDiterima").innerHTML = rowTable
  $("#tabel_diterima").DataTable({
    "lengthChange": true,
    "paging": true,
    "searching": true,
    "pageLength": currentLength,
    "dom": 'tip'
  });

}


$("#tabel_filter_visual").on("keyup", function () {
  $("#tabel_dibuka").DataTable().search(this.value).draw();
  $("#tabel_diterima").DataTable().search(this.value).draw();
});

$("#tabel_length_visual").on("change", function () {
  $("#tabel_dibuka").DataTable().page.len(Number(this.value)).draw();
  $("#tabel_diterima").DataTable().page.len(Number(this.value)).draw();
});


function buttonAddBuka () {
  document.getElementById('input_add2_bank').value = ''
  document.getElementById('input_add2_noGiro').value = ''
  document.getElementById('input_add2_tglGiro').value = ''
  document.getElementById('input_add2_nilaiGiro').value = ''
  document.getElementById('input_add2_tglBuka').value = ''
  document.getElementById('input_add2_buktiBuka').value = ''
  document.getElementById('input_add2_keterangan').value = ''
  document.getElementById('input_add2_tglCair').value = ''
  document.getElementById('input_add2_buktiCair').value = ''
  document.getElementById('input_add2_keteranganCair').value = ''

  $("#formBuka").modal('toggle')

}

function buttonAddTerima () {
document.getElementById('input_add_bank').value = ''
document.getElementById('input_add_noGiro').value = ''
document.getElementById('input_add_tglGiro').value = ''
document.getElementById('input_add_nilaiGiro').value = ''
document.getElementById('input_add_tglTerima').value = ''
document.getElementById('input_add_buktiTerima').value = ''
document.getElementById('input_add_keterangan').value = ''
document.getElementById('input_add_tglCair').value = ''
document.getElementById('input_add_buktiCair').value = ''
document.getElementById('input_add_keteranganCair').value = ''
document.getElementById('input_add_perkiraanKas').value = ''

  $("#formTerima").modal('toggle')

}

let noGiroTemp = ''
let noGiroTerimaTemp = ''

function buttonEditBuka (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastergirospdetailbuka') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit2_bank").value = res[0].Bank
      document.getElementById("input_edit2_noGiro").value = res[0].NoGiro
      document.getElementById("input_edit2_tglGiro").value = new Date(res[0].TglGiro).toLocaleDateString('en-CA');
      document.getElementById("input_edit2_nilaiGiroRp").value = res[0].KreditRp
      
      document.getElementById("input_edit2_nilaiGiro").value = res[0].Kredit
      document.getElementById("input_edit2_valas").value = res[0].Kodevls
      document.getElementById("input_edit2_kurs").value = res[0].Kurs
      document.getElementById("input_edit2_keterangan").value = res[0].Keterangan
      document.getElementById("input_edit2_tglBuka").value = new Date(res[0].TglBuka).toLocaleDateString('en-CA');
      document.getElementById("input_edit2_buktiBuka").value = res[0].BuktiBuka
      document.getElementById("input_edit2_tglCair").value = new Date(res[0].TglCair).toLocaleDateString('en-CA');
      document.getElementById("input_edit2_buktiCair").value = res[0].BuktiCair
      document.getElementById("input_edit2_keteranganCair").value = res[0].KeteranganCair

      formatNumber(document.getElementById("input_edit2_nilaiGiroRp"))
      formatNumber(document.getElementById("input_edit2_nilaiGiro"))

      noGiroTemp = res[0].NoGiro

    }})
    $("#formEditBuka").modal('toggle')
}

function buttonEditTerima (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastergirospdetailterima') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {
      console.log(res)
      document.getElementById("input_edit_bank").value = res[0].Bank
      document.getElementById("input_edit_noGiro").value = res[0].NoGiro
      document.getElementById("input_edit_tglGiro").value = new Date(res[0].TglGiro).toLocaleDateString('en-CA');
      document.getElementById("input_edit_nilaiGiro").value = res[0].Debet
      document.getElementById("input_edit_nilaiGiroRp").value = res[0].DebetRp
      document.getElementById("input_edit_valas").value = res[0].Kodevls
      document.getElementById("input_edit_kurs").value = res[0].Kurs
      document.getElementById("input_edit_keterangan").value = res[0].Keterangan
      document.getElementById("input_edit_tglTerima").value = new Date(res[0].TglBuka).toLocaleDateString('en-CA');
      document.getElementById("input_edit_buktiTerima").value = res[0].BuktiBuka
      document.getElementById("input_edit_tglCair").value = new Date(res[0].TglCair).toLocaleDateString('en-CA');
      document.getElementById("input_edit_buktiCair").value = res[0].BuktiCair
      document.getElementById("input_edit_keteranganCair").value = res[0].KeteranganCair
      document.getElementById("input_edit_perkiraanKas").value = res[0].Kas
      
      formatNumber(document.getElementById("input_edit_nilaiGiroRp"))
      formatNumber(document.getElementById("input_edit_nilaiGiro"))

      noGiroTerimaTemp = res[0].NoGiro

    }})
    $("#formEditTerima").modal('toggle')

}

function buttonDeleteTerima (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Giro Terima', 'Apakah yakin ingin menghapus No. Giro ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastergirospdeleteterima') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kode
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAllBuka()
              loadAllTerima()
              alertify.success("Data Giro Terima " + kode + " telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

function buttonDeleteBuka (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Giro Buka', 'Apakah yakin ingin menghapus No. Giro  ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastergirospdeletebuka') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kode
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAllBuka()
              loadAllTerima()
              alertify.success("Data Giro Buka telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}
//
function submitEditBuka () {

  let _token = $("#_token").val();

  let noGiro = $("#input_edit2_noGiro").val();
  let bank = $("#input_edit2_bank").val();
  let tanggalGiro = $("#input_edit2_tglGiro").val();
  let nilaiGiro = parseFloat(($("#input_edit2_nilaiGiro").val() || 0).toString().replace(/,/g, '')) || 0;
  let nilaiGiroRp = parseFloat(($("#input_edit2_nilaiGiroRp").val() || 0).toString().replace(/,/g, '')) || 0;
  let valas = $("#input_edit2_valas").val();
  let kurs = $("#input_edit2_kurs").val();
  let tanggalBuka = $("#input_edit2_tglBuka").val();
  let buktiBuka = $("#input_edit2_buktiBuka").val();
  let keterangan = $("#input_edit2_keterangan").val();
  let tanggalCair = $("#input_edit2_tanggalCair").val();
  let buktiCair = $("#input_edit2_buktiCair").val();
  let keteranganCair = $("#input_edit2_keteranganCair").val();

  $.ajax({
    url: "{!! url('mastergirospeditbuka') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      noGiro,
      bank,
      tanggalGiro,
      nilaiGiro,
      nilaiGiroRp,
      valas,
      kurs,
      tanggalBuka,
      buktiBuka,
      keterangan,
      tanggalCair,
      buktiCair,
      keteranganCair,
      noGiroOld : noGiroTemp
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Giro Buka ' "  + noGiro + " ' telah diedit");
        loadAllBuka()
        loadAllTerima()
        $("#formEditBuka").modal('toggle')
      }

    }})

}

function submitEditTerima () {

  let _token = $("#_token").val();
  let noGiro = $("#input_edit_noGiro").val();
  let bank = $("#input_edit_bank").val();
  let tanggalGiro = $("#input_edit_tglGiro").val();
  let nilaiGiro = parseFloat(($("#input_edit_nilaiGiro").val() || 0).toString().replace(/,/g, '')) || 0;
  let nilaiGiroRp = parseFloat(($("#input_edit_nilaiGiroRp").val() || 0).toString().replace(/,/g, '')) || 0;
  let valas = $("#input_edit_valas").val();
  let kurs = $("#input_edit_kurs").val();
  let tanggalTerima = $("#input_edit_tglTerima").val();
  let buktiTerima = $("#input_edit_buktiTerima").val();
  let keterangan = $("#input_edit_keterangan").val();
  let tanggalCair = $("#input_edit_tanggalCair").val();
  let buktiCair = $("#input_edit_buktiCair").val();
  let keteranganCair = $("#input_edit_keteranganCair").val();
  let perkiraanKas = $("#input_edit_perkiraanKas").val();

  console.log(noGiro);
  console.log(bank);

  if (!noGiro) {
    alertify.warning("No. Giro  harus diisi");
    return
  }

  if (!bank) {
    alertify.warning("Bank  harus diisi");
    return
  }

  if (!tanggalGiro) {
    alertify.warning("Tanggal Giro  harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastergirospeditterima') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      noGiro,
      bank,
      tanggalGiro,
      nilaiGiro,
      nilaiGiroRp,
      valas,
      kurs,
      tanggalTerima,
      buktiTerima,
      keterangan,
      tanggalCair,
      buktiCair,
      keteranganCair,
      perkiraanKas,
      noGiroOld : noGiroTerimaTemp
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Giro Terima '" + noGiro + "' telah diedit");
        loadAllBuka()
        loadAllTerima()
        $("#formEditTerima").modal("hide");
      }

    }})

}
//
function submitAddTerima () {

  let _token = $("#_token").val();
  let bank = $("#input_add_bank").val();
  let noGiro = $("#input_add_noGiro").val();
  let tglGiro = $("#input_add_tglGiro").val();
  let nilaiGiro = parseFloat(($("#input_add_nilaiGiro").val() || 0).toString().replace(/,/g, '')) || 0;
  let nilaiGiroRp = parseFloat(($("#input_add_nilaiGiroRp").val() || 0).toString().replace(/,/g, '')) || 0;
  let valas = $("#input_add_valas").val();
  let kurs = $("#input_add_kurs").val();
  let tglTerima = $("#input_add_tglTerima").val();
  let buktiTerima = $("#input_add_buktiTerima").val();
  let keterangan = $("#input_add_keterangan").val();
  let tglCair = $("#input_add_tglCair").val();
  let buktiCair = $("#input_add_buktiCair").val();
  let keteranganCair = $("#input_add_keteranganCair").val();
  let perkiraanKas = $("#input_add_perkiraanKas").val();

  $.ajax({
    url: "{!! url('mastergirospaddterima') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      bank,
      noGiro,
      tglGiro,
      nilaiGiro,
      nilaiGiroRp,
      valas,
      kurs,
      tglTerima,
      buktiTerima,
      keterangan,
      tglCair,
      buktiCair,
      keteranganCair,
      perkiraanKas
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Data Giro Terima telah ditambah");
        loadAllTerima()
        $("#formTerima").modal('hide');
      }

    }})

  // console.log(kodearea, namaarea)
}

function submitAddBuka () {

  let _token = $("#_token").val();
  let bank = $("#input_add2_bank").val();
  let noGiro = $("#input_add2_noGiro").val();
  let tglGiro = $("#input_add2_tglGiro").val();
  let nilaiGiro = parseFloat(($("#input_add2_nilaiGiro").val() || 0).toString().replace(/,/g, '')) || 0;
  let nilaiGiroRp = parseFloat(($("#input_add2_nilaiGiroRp").val() || 0).toString().replace(/,/g, '')) || 0;
  let valas = $("#input_add2_valas").val();
  let kurs = $("#input_add2_kurs").val();
  let tglBuka = $("#input_add2_tglBuka").val();
  let buktiBuka = $("#input_add2_buktiBuka").val();
  let keterangan = $("#input_add2_keterangan").val();
  let tglCair = $("#input_add2_tglCair").val();
  let buktiCair = $("#input_add2_buktiCair").val();
  let keteranganCair = $("#input_add2_keteranganCair").val();

  $.ajax({
    url: "{!! url('mastergirospaddbuka') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      bank,
      noGiro,
      tglGiro,
      nilaiGiro,
      nilaiGiroRp,
      valas,
      kurs,
      tglBuka,
      buktiBuka,
      keterangan,
      tglCair,
      buktiCair,
      keteranganCair
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Data Giro Terima telah ditambah");
        loadAllBuka()
        loadAllTerima()
        $("#formBuka").modal("hide")
      }

    }})
}

function updateKurs2() {
    var selectElement = document.getElementById('input_add_valas');
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var kurs = selectedOption.getAttribute('data-kurs');
    document.getElementById('input_add_kurs').value = kurs;
}

// Set up the change event listener
document.getElementById('input_add_valas').addEventListener('change', updateKurs2);

// Trigger immediately on page load to handle pre-selected option
document.addEventListener('DOMContentLoaded', updateKurs2);

function updateKurs() {
    var selectElement = document.getElementById('input_add2_valas');
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var kurs = selectedOption.getAttribute('data-kurs');
    document.getElementById('input_add2_kurs').value = kurs;
}

// Set up the change event listener
document.getElementById('input_add2_valas').addEventListener('change', updateKurs);

// Trigger immediately on page load to handle pre-selected option
document.addEventListener('DOMContentLoaded', updateKurs);

document.getElementById('input_edit_valas').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var kurs = selectedOption.getAttribute('data-kurs');
    document.getElementById('input_edit_kurs').value = kurs;
});

document.getElementById('input_edit2_valas').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var kurs = selectedOption.getAttribute('data-kurs');
    document.getElementById('input_edit2_kurs').value = kurs;
});

function buttonSelectPerkiraanKas () {
  loadSelectKas()
  $("#formSelectKas").modal('toggle')
}

function buttonSelectBank () {
  loadSelectBank()
  $("#formSelectBank").modal('toggle')
}

function loadSelectKas() {
  let _token = $("#_token").val();

  $('#tabelAktivaSelectPerkiraan').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastergiroloadkas') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td class="text-center">
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonPilihPerkiraanKas('${item.Perkiraan}')"><i class='bi bi-plus'></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAktivaSelectPerkiraan").innerHTML = rowTable;
  $("#tabelAktivaSelectPerkiraan").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function loadSelectBank() {
  let _token = $("#_token").val();

  $('#tabelBukaSelectBank').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastergiroloadbank') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td class="text-center">
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonPilihPerkiraanBank('${item.Perkiraan}')"><i class='bi bi-plus'></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataBukaSelectBank").innerHTML = rowTable;
  $("#tabelBukaSelectBank").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function buttonPilihPerkiraanKas(selectedPerkiraan) {
  $("#input_add_perkiraanKas").val(selectedPerkiraan);
  $("#input_edit_perkiraanKas").val(selectedPerkiraan);
  $("#formSelectKas").modal("hide");
}

function buttonPilihPerkiraanBank(selectedPerkiraan) {
  $("#input_add2_bank").val(selectedPerkiraan);
  $("#input_edit2_bank").val(selectedPerkiraan);
  $("#formSelectBank").modal("hide");
}

window.onload = function(){
  loadAllBuka();
  loadAllTerima();
  hideButtonTerima();
}

// Get the DOM elements (not their values)
const inputNilaiGiroElement = document.getElementById('input_add2_nilaiGiro');
const inputNilaiGiroRpElement = document.getElementById('input_add2_nilaiGiroRp');
const inputKursElement = document.getElementById('input_add2_kurs');

const inputNilaiGiroElement2 = document.getElementById('input_add_nilaiGiro');
const inputNilaiGiroRpElement2 = document.getElementById('input_add_nilaiGiroRp');
const inputKursElement2 = document.getElementById('input_add_kurs');

// Function to format number with thousands separator
function formatWithCommas(num) {
    return num.toLocaleString('en-US');
}

// Function to parse value and remove commas
function parseValue(element) {
    return parseFloat((element.value || '0').replace(/,/g, '')) || 0;
}

// Add event listener to the input field
inputNilaiGiroElement.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiro = parseValue(inputNilaiGiroElement);
    const kurs = parseValue(inputKursElement);

    // Calculate the result
    const nilaiGiroRp = nilaiGiro * kurs;

    // Update the value with thousands separator
    inputNilaiGiroRpElement.value = formatWithCommas(nilaiGiroRp);
});

// Add event listener to the input field
inputNilaiGiroElement2.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiro = parseValue(inputNilaiGiroElement2);
    const kurs = parseValue(inputKursElement2);

    // Calculate the result
    const nilaiGiroRp = nilaiGiro * kurs;

    // Update the value with thousands separator
    inputNilaiGiroRpElement2.value = formatWithCommas(nilaiGiroRp);
});

inputNilaiGiroRpElement.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiroRp = parseValue(inputNilaiGiroRpElement);
    const kurs = parseValue(inputKursElement);

    // Calculate the result
    const nilaiGiro = nilaiGiroRp / kurs;

    // Update the value with thousands separator
    inputNilaiGiroElement.value = formatWithCommas(nilaiGiro);
});

inputNilaiGiroRpElement2.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiroRp = parseValue(inputNilaiGiroRpElement2);
    const kurs = parseValue(inputKursElement2);

    // Calculate the result
    const nilaiGiro = nilaiGiroRp / kurs;

    // Update the value with thousands separator
    inputNilaiGiroElement2.value = formatWithCommas(nilaiGiro);
});



// Optional: Add formatting to the input itself when user types
inputNilaiGiroElement.addEventListener('blur', function() {
    const value = parseValue(inputNilaiGiroElement);
    if (value > 0) {
        inputNilaiGiroElement.value = formatWithCommas(value);
    }
});

const inputEditNilaiGiroElement = document.getElementById('input_edit2_nilaiGiro');
const inputEditNilaiGiroRpElement = document.getElementById('input_edit2_nilaiGiroRp');
const inputEditKursElement = document.getElementById('input_edit2_kurs');

const inputEditNilaiGiroElement2 = document.getElementById('input_edit_nilaiGiro');
const inputEditNilaiGiroRpElement2 = document.getElementById('input_edit_nilaiGiroRp');
const inputEditKursElement2 = document.getElementById('input_edit_kurs');

// Add event listener to the input field
inputEditNilaiGiroElement.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiro = parseValue(inputEditNilaiGiroElement);
    const kurs = parseValue(inputEditKursElement);

    // Calculate the result
    const nilaiGiroRp = nilaiGiro * kurs;

    // Update the value with thousands separator
    inputEditNilaiGiroRpElement.value = formatWithCommas(nilaiGiroRp);
});

// Add event listener to the input field
inputEditNilaiGiroElement2.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiro = parseValue(inputEditNilaiGiroElement2);
    const kurs = parseValue(inputEditKursElement2);

    // Calculate the result
    const nilaiGiroRp = nilaiGiro * kurs;

    // Update the value with thousands separator
    inputEditNilaiGiroRpElement2.value = formatWithCommas(nilaiGiroRp);
});

inputEditNilaiGiroRpElement.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiroRp = parseValue(inputEditNilaiGiroRpElement);
    const kurs = parseValue(inputEditKursElement);

    // Calculate the result
    const nilaiGiro = nilaiGiroRp / kurs;

    // Update the value with thousands separator
    inputEditNilaiGiroElement.value = formatWithCommas(nilaiGiro);
});

inputEditNilaiGiroRpElement2.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiroRp = parseValue(inputEditNilaiGiroRpElement2);
    const kurs = parseValue(inputEditKursElement2);

    // Calculate the result
    const nilaiGiro = nilaiGiroRp / kurs;

    // Update the value with thousands separator
    inputEditNilaiGiroElement2.value = formatWithCommas(nilaiGiro);
});

</script>




@endsection
