@extends('newmaster')
@section('buttons')

@section('page-title', 'Sales Order')

@endsection
<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

{{-- tampilan search bar 1 --}}
  @section('css')

  <style>
  #tabel_add_list_alamatkirim,
  #tabel_add_list_lokasipenerima,
  #tabel_add_list_pic,
  #tabel_add_list_refpr,
  #tabel_add_list_nopenyerahan,
  #tabel_add_list_sattax {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px;
  }

  #tabel_add_list_alamatkirim.table-bordered, #tabel_add_list_alamatkirim.table-bordered tr, #tabel_add_list_alamatkirim.table-bordered th, #tabel_add_list_alamatkirim.table-bordered td,
  #tabel_add_list_lokasipenerima.table-bordered, #tabel_add_list_lokasipenerima.table-bordered tr, #tabel_add_list_lokasipenerima.table-bordered th, #tabel_add_list_lokasipenerima.table-bordered td,
  #tabel_add_list_pic.table-bordered, #tabel_add_list_pic.table-bordered tr, #tabel_add_list_pic.table-bordered th, #tabel_add_list_pic.table-bordered td,
  #tabel_add_list_refpr.table-bordered, #tabel_add_list_refpr.table-bordered tr, #tabel_add_list_refpr.table-bordered th, #tabel_add_list_refpr.table-bordered td,
  #tabel_add_list_nopenyerahan.table-bordered, #tabel_add_list_nopenyerahan.table-bordered tr, #tabel_add_list_nopenyerahan.table-bordered th, #tabel_add_list_nopenyerahan.table-bordered td,
  #tabel_add_list_sattax.table-bordered, #tabel_add_list_sattax.table-bordered tr, #tabel_add_list_sattax.table-bordered th, #tabel_add_list_sattax.table-bordered td {
    border: none;
  }

  #tabel_add_list_alamatkirim.table-striped tbody tr:nth-of-type(odd),
  #tabel_add_list_lokasipenerima.table-striped tbody tr:nth-of-type(odd),
  #tabel_add_list_pic.table-striped tbody tr:nth-of-type(odd),
  #tabel_add_list_refpr.table-striped tbody tr:nth-of-type(odd),
  #tabel_add_list_nopenyerahan.table-striped tbody tr:nth-of-type(odd),
  #tabel_add_list_sattax.table-striped tbody tr:nth-of-type(odd) {
    background: #fff;
    --bs-table-accent-bg: transparent;
    box-shadow: none;
  }

  #tabel_add_list_alamatkirim thead th,
  #tabel_add_list_lokasipenerima thead th,
  #tabel_add_list_pic thead th,
  #tabel_add_list_refpr thead th,
  #tabel_add_list_nopenyerahan thead th,
  #tabel_add_list_sattax thead th {
    position: sticky;
    top: 0;
    z-index: 2;
    background: #FAFBFF;
    padding: 10px 14px;
    font-size: 13px;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .06em;
    border-bottom: 2px solid var(--border);
    white-space: nowrap;
    text-align: left;
  }

  #tabel_add_list_alamatkirim tbody tr.pick-row,
  #tabel_add_list_lokasipenerima tbody tr.pick-row,
  #tabel_add_list_pic tbody tr.pick-row,
  #tabel_add_list_refpr tbody tr.pick-row,
  #tabel_add_list_nopenyerahan tbody tr.pick-row,
  #tabel_add_list_sattax tbody tr.pick-row {
    border-bottom: 1px solid #F1F5F9;
    cursor: pointer;
    transition: background .12s;
  }

  #tabel_add_list_alamatkirim tbody tr.pick-row td,
  #tabel_add_list_lokasipenerima tbody tr.pick-row td,
  #tabel_add_list_pic tbody tr.pick-row td,
  #tabel_add_list_refpr tbody tr.pick-row td,
  #tabel_add_list_nopenyerahan tbody tr.pick-row td,
  #tabel_add_list_sattax tbody tr.pick-row td {
    padding: 9px 14px;
    vertical-align: middle;
  }

  #tabel_add_list_alamatkirim tbody tr.pick-row:hover td,
  #tabel_add_list_lokasipenerima tbody tr.pick-row:hover td,
  #tabel_add_list_pic tbody tr.pick-row:hover td,
  #tabel_add_list_refpr tbody tr.pick-row:hover td,
  #tabel_add_list_nopenyerahan tbody tr.pick-row:hover td,
  #tabel_add_list_sattax tbody tr.pick-row:hover td {
    background: #F8F9FF;
  }

  #tabel_add_list_alamatkirim tbody tr.pick-row td:first-child,
  #tabel_add_list_lokasipenerima tbody tr.pick-row td:first-child,
  #tabel_add_list_pic tbody tr.pick-row td:first-child,
  #tabel_add_list_refpr tbody tr.pick-row td:first-child,
  #tabel_add_list_nopenyerahan tbody tr.pick-row td:first-child,
  #tabel_add_list_sattax tbody tr.pick-row td:first-child {
    font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    font-size: 13.5px;
    color: var(--muted);
  }

  /* DataTables wraps table+search+pagination in #<tableId>_wrapper, as SIBLINGS of the
     table rather than descendants -- these have to be scoped to the wrapper id, not the
     table id, to actually reach the search box and pagination footer. */
  #tabel_add_list_alamatkirim_wrapper,
  #tabel_add_list_lokasipenerima_wrapper,
  #tabel_add_list_pic_wrapper,
  #tabel_add_list_refpr_wrapper,
  #tabel_add_list_nopenyerahan_wrapper,
  #tabel_add_list_sattax_wrapper {
    font-size: 13px;
    color: var(--rt-ink-soft);
    padding-top: 12px;
  }

  #tabel_add_list_alamatkirim_wrapper .dataTables_filter input,
  #tabel_add_list_lokasipenerima_wrapper .dataTables_filter input,
  #tabel_add_list_pic_wrapper .dataTables_filter input,
  #tabel_add_list_refpr_wrapper .dataTables_filter input,
  #tabel_add_list_nopenyerahan_wrapper .dataTables_filter input,
  #tabel_add_list_sattax_wrapper .dataTables_filter input {
    padding: 7px 10px;
    font-size: 13px;
    margin-left: 6px;
    outline: none;
  }

  #tabel_add_list_alamatkirim_wrapper .dataTables_filter input:focus,
  #tabel_add_list_lokasipenerima_wrapper .dataTables_filter input:focus,
  #tabel_add_list_pic_wrapper .dataTables_filter input:focus,
  #tabel_add_list_refpr_wrapper .dataTables_filter input:focus,
  #tabel_add_list_nopenyerahan_wrapper .dataTables_filter input:focus,
  #tabel_add_list_sattax_wrapper .dataTables_filter input:focus {
    border-color: var(--rt-blue);
    box-shadow: 0 0 0 3px var(--rt-blue-soft);
  }

  #tabel_add_list_alamatkirim_wrapper .dataTables_paginate .paginate_button,
  #tabel_add_list_lokasipenerima_wrapper .dataTables_paginate .paginate_button,
  #tabel_add_list_pic_wrapper .dataTables_paginate .paginate_button,
  #tabel_add_list_refpr_wrapper .dataTables_paginate .paginate_button,
  #tabel_add_list_nopenyerahan_wrapper .dataTables_paginate .paginate_button,
  #tabel_add_list_sattax_wrapper .dataTables_paginate .paginate_button {
    margin-left: 4px;
    padding: 4px 10px !important;
    color: var(--rt-ink) !important;
    background: #fff !important;
  }

  #tabel_add_list_alamatkirim_wrapper .dataTables_paginate .paginate_button:hover,
  #tabel_add_list_lokasipenerima_wrapper .dataTables_paginate .paginate_button:hover,
  #tabel_add_list_pic_wrapper .dataTables_paginate .paginate_button:hover,
  #tabel_add_list_refpr_wrapper .dataTables_paginate .paginate_button:hover,
  #tabel_add_list_nopenyerahan_wrapper .dataTables_paginate .paginate_button:hover,
  #tabel_add_list_sattax_wrapper .dataTables_paginate .paginate_button:hover {
    background: var(--rt-bg) !important;
    border-color: var(--rt-border) !important;
    color: var(--rt-ink) !important;
  }

  #tabel_add_list_alamatkirim_wrapper .dataTables_paginate .paginate_button.current,
  #tabel_add_list_lokasipenerima_wrapper .dataTables_paginate .paginate_button.current,
  #tabel_add_list_pic_wrapper .dataTables_paginate .paginate_button.current,
  #tabel_add_list_refpr_wrapper .dataTables_paginate .paginate_button.current,
  #tabel_add_list_nopenyerahan_wrapper .dataTables_paginate .paginate_button.current,
  #tabel_add_list_sattax_wrapper .dataTables_paginate .paginate_button.current {
    background: var(--rt-blue) !important;
    border-color: var(--rt-blue) !important;
    color: #fff !important;
  }
  </style>

  <style>
  #tabel_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel_filter label input {
      width: 150px;
      padding: 5px 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }

  #tabel_filter label {
      font-weight: 600;
      font-size: 0.9rem;
      color: #333;
    }
  </style>
{{-- end tampilan search bar 1 --}}

{{-- tampilan search bar 2 --}}
  <style>
  #tabel2_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel2_filter label input {
      width: 150px;
      padding: 5px 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }

  #tabel2_filter label {
      font-weight: 600;
      font-size: 0.9rem;
      color: #333;
    }

  #tabel2_filter input:focus {
      border-color: #007bff;
      outline: none;
    }
  </style>
{{-- end tampilan search bar 2 --}}

{{-- tampilan search bar 3 --}}
  <style>
  #tabel_oto_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel_oto_filter label input {
      width: 150px;
      padding: 5px 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }

  #tabel_oto_filter label {
      font-weight: 600;
      font-size: 0.9rem;
      color: #333;
    }

  #tabel_oto_filter input:focus {
      border-color: #007bff;
      outline: none;
    }

    #tabel_tambahsoall_filter {
        display: flex;
        align-items: flex-end;
        margin-top: 8px;
        margin-right: 10px;
        margin-bottom: -10px;
      }

    #tabel_tambahsoall_filter label input {
        width: 150px;
        padding: 5px 10px;
        border-radius: 10px;
        border: 1px solid #ccc;
        box-shadow: none;
        font-size: 0.65rem;
      }

    #tabel_tambahsoall_filter label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #333;
      }

    #tabel_tambahsoall_filter input:focus {
        border-color: #007bff;
        outline: none;
      }
  </style>
{{-- end tampilan search bar 3 --}}

{{-- tampilan search bar 4 --}}
  <style>
  #tabel7_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel7_filter label input {
      width: 150px;
      padding: 5px 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }

  #tabel7_filter label {
      font-weight: 600;
      font-size: 0.9rem;
      color: #333;
    }

  #tabel7_filter input:focus {
      border-color: #007bff;
      outline: none;
    }
    
  </style>
{{-- end tampilan search bar 4 --}}

{{-- tampilan search bar modal add pelanggan --}}
  <style>
    #tabel_add_list_refpr_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: 0px;

    }
    #tabel_add_list_refpr_filter label input {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }

    #tabel_add_list_nopelanggan_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: 0px;

    }
    #tabel_add_list_nopelanggan_filter label input {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search bar modal add pelanggan --}}

{{-- tampilan search sales --}}
  <style>
  #tabel_add_list_pelanggan_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }
  #tabel_add_list_pelanggan_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

    #tabel_add_list_sales_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_sales_filter label input {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search sales --}}

{{-- tampilan search modal barang all --}}
  <style>
    #input_search_barang_all {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
      display: flex;
      align-items: flex-end;
      margin-left: 95px;
    }
    .search-label {
    font-weight: bold;
    font-size: 0.75rem;
    margin-right: 155px;
    margin-top : -45px;
    display: inline-block;
    vertical-align: middle;
    }
  </style>
{{-- end tampilan search modal barang all --}}

{{-- tampilan search sattax --}}
  <style>
    #tabel_add_list_sattax_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_sattax_filter label input {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search sattax --}}

{{-- tampilan search lokasi penerima --}}
  <style>
    #tabel_add_list_lokasipenerima_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_lokasipenerima_filter label input {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search lokasi penerima --}}

{{-- tampilan search alamat kirim --}}
  <style>
    #tabel_add_list_alamatkirim_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_alamatkirim_filter label input {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search alamat kirim --}}

{{-- tampilan search pic --}}
  <style>
    #tabel_add_list_pic_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_pic_filter label input {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }

  /* canvas/bootstrap.css defines .bg-primary/.text-white with !important,
     which can win the cascade over Bootstrap 5's own !important version of
     the same rule depending on stylesheet load order. Pinning these table
     headers directly guarantees the intended look regardless of that. */
  .table thead.bg-primary th,
  .table thead.bg-primary td {
    background-color: #0d6efd !important;
    color: #fff !important;
    border-color: #0b5ed7 !important;
  }

  </style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

@endsection
@section('content')

<link rel="stylesheet" href="{{ URL::asset('css/tableMaster2.css') }}">

<link rel="stylesheet" href="{{ URL::asset('css/report-table.css') }}?v={{ filemtime(public_path('css/report-table.css')) }}">

<link rel="stylesheet" href="{{ URL::asset('css/so-table-header.css') }}?v={{ filemtime(public_path('css/so-table-header.css')) }}">

<style>
  .toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .page-title {
    font-size: 19px;
    font-weight: 800;
    color: #1f2430;
  }

  .custom-tabs {
    display: inline-flex;
    justify-content: flex-start;
    align-items: center;
    gap: 2px;
    background-color: #f1f3f5;
    border-radius: 20px;
    padding: 3px;
  }

  .custom-tabs .nav-link {
    display: inline-block !important;
    padding: 5px 16px !important;
    font-size: 0.75rem !important;
    border: none;
    border-radius: 17px;
    color: #495057;
    background: transparent;
    font-weight: 600;
    transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
  }

  .custom-tabs .nav-link:hover {
    background: transparent;
    color: #007bff;
  }

  .custom-tabs .nav-link.active {
    background: #007bff;
    border-color: #007bff;
    color: #fff;
    box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
  }

  /* newmaster.css punya rule .card global (align-items:center, dibuat untuk kartu menu
     dashboard) yang menimpa ini kalau tidak ditulis ulang di sini. */
  .tab-card {
    display: block !important;
    align-items: flex-start !important;
    padding: 0 !important;
    border: none !important;
    margin-bottom: 6px !important;
  }

  .tab-card .card-body {
    padding: 5px 10px !important;
  }

  #page1 .card {
    display: block !important;
    align-items: stretch !important;
    padding: 0 !important;
    text-align: left !important;
    cursor: default !important;
  }

  #page1 .card:hover {
    transform: none !important;
    box-shadow: none !important;
    border-color: var(--border) !important;
  }

  /* ---------- Kolom Aksi tabel (#tabel2/#tabel_oto) - tombol bulat kecil,
     warna pastel, sama seperti .btn-action-* di gudang/permintaanpemakaian ----------
     #tabel/#tabel7 SENGAJA tidak ikut di grup ini: aksinya dirender lewat
     .action-buttons-wrap milik tabelActionsCell()/tabel7ActionsCell() (tableMaster2.css),
     bukan lewat kelas .btn Bootstrap polos seperti #tabel2/#tabel_oto. */
  #tabel2 td:first-child,
  #tabel_oto td:first-child {
    display: flex;
    gap: 4px;
    justify-content: center;
    align-items: center;
  }

  #tabel2 td:first-child .btn,
  #tabel_oto td:first-child .btn {
    width: 30px;
    height: 30px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 7px;
    font-size: 13px;
    border: 1px solid transparent;
    box-shadow: none;
    transition: all .12s ease;
  }

  #tabel2 td:first-child .btn:hover,
  #tabel_oto td:first-child .btn:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
  }

  #tabel2 td:first-child .btn-success,
  #tabel_oto td:first-child .btn-success {
    color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
  }

  #tabel2 td:first-child .btn-warning,
  #tabel_oto td:first-child .btn-warning {
    color: #b45309; border-color: #fbe3bd; background: #fef3e0;
  }

  #tabel2 td:first-child .btn-primary,
  #tabel_oto td:first-child .btn-primary {
    color: #2563eb; border-color: #cfdcff; background: #e8edff;
  }

  #tabel2 td:first-child .btn-danger,
  #tabel_oto td:first-child .btn-danger {
    color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
  }

  #tabel2 td:first-child .btn-info,
  #tabel_oto td:first-child .btn-info {
    color: #0891b2; border-color: #a5f3fc; background: #ecfeff;
  }

  #tabel2 tbody tr:nth-of-type(odd),
  #tabel_oto tbody tr:nth-of-type(odd) {
    background-color: #fbfbfc;
  }

  #tabel2 tbody tr:hover,
  #tabel_oto tbody tr:hover {
    background-color: #f5f3ff;
  }

  /* ---------- Chip biru / Reset kolom (sudah disediakan .rt-reset-btn di CSS lain) ---------- */
  .btn-chip-biru {
    background-color: #e8edff;
    border-color: #cfdcff;
    color: #2563eb;
  }

  .btn-chip-biru:hover,
  .btn-chip-biru:focus {
    background-color: #dce6ff;
    border-color: #b9c9ff;
    color: #1d4ed8;
  }

  .btn-chip-biru:active {
    background-color: #cfdcff !important;
    border-color: #a8bdff !important;
    color: #1d4ed8 !important;
  }

  .btn-batal-add {
    background-color: #f1f3f5;
    border-color: #dee2e6;
    color: #495057;
  }

  .btn-batal-add:hover,
  .btn-batal-add:focus {
    background-color: #e9ecef;
    border-color: #ced4da;
    color: #343a40;
  }

  .btn-batal-add:active {
    background-color: #dee2e6 !important;
    border-color: #ced4da !important;
    color: #343a40 !important;
  }

  /* Dropdown "Tampilkan" di toolbar tab yang dimuat lewat AJAX (#tabel2/#tabel_oto).
     Meniru .po-len-wrap milik purchaseOrder.blade.php 1:1. */
  .po-len-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--rt-card);
    border: 1.5px solid var(--rt-border);
    border-radius: 8px;
    padding: 5px 12px;
  }

  .po-len-wrap label {
    margin: 0;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--rt-ink-soft);
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
  }

  .po-len-inp {
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 700;
    color: var(--rt-ink);
    outline: none;
    cursor: pointer;
    padding: 2px 20px 2px 0;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>");
    background-repeat: no-repeat;
    background-position: right center;
  }

  /* ---- Lapisan "sedang memuat" tabel #tabel2/#tabel_oto (dimuat lewat AJAX di loadAll) ----
     Sama seperti #tabel_wrapper/#tabelso_wrapper milik purchaseOrder.blade.php. */
  #tabel2_wrapper,
  #tabel_oto_wrapper {
    position: relative;
  }

  #tabel2_wrapper > .dataTables_processing,
  #tabel_oto_wrapper > .dataTables_processing {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: auto;
    margin: 0;
    padding: 0;
    border: 0;
    background: rgba(255, 255, 255, .62);
    z-index: 40;
    animation: soMunculLoading .34s ease-out both;
  }

  @keyframes soMunculLoading {
    0%, 45% { opacity: 0; }
    100% { opacity: 1; }
  }

  .po-loading-chip {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: inline-flex;
    align-items: center;
    gap: 9px;
    white-space: nowrap;
    padding: 9px 18px;
    border-radius: 999px;
    background: rgba(31, 36, 48, .92);
    color: #fff;
    font-size: 12.5px;
    font-weight: 600;
    box-shadow: 0 8px 22px rgba(0, 0, 0, .18);
  }

  .po-loading-spin {
    width: 14px;
    height: 14px;
    border: 2px solid rgba(255, 255, 255, .35);
    border-top-color: #fff;
    border-radius: 50%;
    animation: soPutarLoading .6s linear infinite;
  }

  @keyframes soPutarLoading {
    to { transform: rotate(360deg); }
  }

  /* ---------- Header tabel #tabel2/#tabel_oto - bersih, uppercase abu-abu (disamakan
     dengan #tabel_data_header PO) ---------- */
  #tabel2 thead th,
  #tabel_oto thead th {
    background: #f8f9fb !important;
    color: #6b7280 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #e7e9ee;
    border-top: none;
  }

  #tabel2.table-bordered th,
  #tabel2.table-bordered td,
  #tabel_oto.table-bordered th,
  #tabel_oto.table-bordered td {
    border-color: #e7e9ee !important;
  }
</style>

<style>
  /* report-table.css resets `.tb-report *` to margin:0/padding:0 so its own
     .rt-th/.rt-bar rules have a clean slate. #tabel's Actions-column buttons
     use plain Bootstrap .btn/.btn-sm classes, and #tabel7's use
     tableMaster2.css's .btn-action-sm -- neither is report-table.css's own
     button styling, so restore their padding/margin inside the wrapper. */
  .tb-report .tb tbody td .btn {
    padding: 0.25rem 0.5rem;
    margin: 0 2px 2px 0;
  }
  .tb-report .tb tbody td .btn-action-sm,
  .tb-report .tb tbody td .btn-action-md {
    margin: 2px;
  }

  /* tableMaster2.css carries a legacy #tabel thead th / #tabel tbody td
     re-skin (an ID selector applied only to this table, from before the
     interactive column engine existed). ID selectors always beat
     report-table.css's .tb-report .rt-th classes regardless of load order,
     so #tabel's header cells were getting padding on *both* the outer <th>
     (from that ID rule) and the inner .th-inner div (from report-table.css)
     -- a double layer #tabel7 never had, since it has no such ID rule. Same
     story for tbody td's font-size. Zero out just the sizing here so #tabel
     falls back to the same report-table.css-driven sizing #tabel7 already
     has; background/border coloring is left alone since that's cosmetic,
     not the reported mismatch.

     Header font-size is set to report-table.css's own literal value
     (.tb-report .tb thead th { font-size: 13px }), not `inherit` -- that
     rule targets thead th specifically rather than relying on inheritance
     from the table's 15px base, so `inherit` here would have skipped past
     it and pulled in the wrong (larger) size. tbody td has no such
     thead-style override in report-table.css, so it really does just
     inherit the table's 15px base -- `inherit` is correct there. */
  #tabel.tb thead th {
    padding: 0;
    font-size: 13px;
  }
  #tabel.tb tbody td {
    padding: 10px 14px;
    font-size: inherit;
  }

  /* Bringing page1's other three tables in line with the "fresh" header/row
     look #tabel already has (tableMaster2.css's #tabel-scoped rule, which
     never reached #tabel2/#tabel_oto/#tabel7 since it's ID-scoped to #tabel
     alone). #tabel2/#tabel_oto had no custom header/row styling at all
     before this -- plain browser defaults -- so this gives them the same
     flat gray-uppercase header + light-purple row hover #tabel/#tabel7 use.
     Values match what #tabel/#tabel7 actually render as today (accounting
     for report-table.css's nested .th-inner padding on the interactive
     tables), not tableMaster2's original numbers, which assumed a single
     flat <th>/<td> layer that only #tabel2/#tabel_oto actually have. */
  #tabel2 thead th,
  #tabel_oto thead th {
    background: #f8f9fb;
    color: #6b7280;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-bottom: 1px solid #e7e9ee;
    padding: 10px 14px;
  }
  #tabel2 tbody td,
  #tabel_oto tbody td {
    padding: 10px 14px;
    font-size: 15px;
  }
  #tabel2 tbody tr:hover,
  #tabel_oto tbody tr:hover {
    background-color: #f5f3ff;
  }

  /* #tabel7 already has a similar-but-not-identical fresh header via
     report-table.css's .tb-report .tb thead th (different background/
     letter-spacing/border values) and no row-hover at all. Align the
     colors/typography to match the other three tables exactly -- padding
     and font-size are left alone, they're already correctly tuned against
     report-table.css's .th-inner layer. #tabel7.tb's ID+class beats
     report-table.css's plain class selectors without needing !important. */
  #tabel7.tb thead th {
    background: #f8f9fb;
    color: #6b7280;
    font-weight: 600;
    letter-spacing: .04em;
    border-bottom: 1px solid #e7e9ee;
  }
  #tabel7 tbody tr:hover {
    background-color: #f5f3ff;
  }


</style>

<div id="printContainer" style="display:none">

</div>
<div id="page1" class="container-fluid">

  <div id="contentContainer" class="container-fluid">
    <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
    <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

    <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
    <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS !!}" />
    <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
    <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
    <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
    <input type="hidden" id="akses_isotorisasi5" value="{!! $akses->IsOtorisasi5 !!}" />
    <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />
    <input type="hidden" id="level" value="{!! $level !!}" />

    <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

    <style>
      .sp-length-wrap {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
      }

      .sp-filter-wrap {
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
        min-width: 100px;
      }

      .sp-filter-wrap select#input_filterso {
        min-width: 190px;
      }

      .sp-filter-wrap input[type="date"] {
        min-width: 0;
      }

      /* tableMaster2.css's .sp-search-wrap uses flex-grow:1, so it expands
         to fill whatever room is left in the toolbar (up to 340px) once the
         other controls got compacted. Shrink it and let it scale with the
         viewport instead of hard-coding one pixel width for one resolution. */
      .sp-search-wrap {
        flex: 0 1 auto;
        width: clamp(160px, 16vw, 260px);
        max-width: clamp(160px, 16vw, 260px);
      }

      .sp-length-wrap {
        margin-left: auto;
      }

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

      /* Purchase Order's layout (newmasterx.blade.php) still loads the old "canvas"
         admin theme, whose global `label { ... }` rule (public/css/canvas/style.css)
         bolds/uppercases every form label. This page's layout (newmaster.blade.php)
         dropped that theme, so labels here render as plain browser defaults unless
         restyled locally -- scoped to #page2 to match Form Purchase Order's look
         without touching the shared layout. */
      /* "Reset kolom" pill, matching purchaseOrder.blade.php's po-table-header.css
         .rt-reset-btn look. Kept OUTSIDE #rtBarTabel/#rtBarTabel7 (as a flex sibling,
         not a child) because report-table.js's renderBar() fully overwrites those
         divs' innerHTML on every drag/hide/decimal change -- a button placed inside
         them would vanish on the first re-render. */
      .rt-bar-row {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-bottom: 10px;
      }
      .rt-bar-row .rt-bar { margin-bottom: 0; }
      .rt-reset-btn {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 8px 12px;
        border: 1px solid var(--rt-border);
        border-radius: var(--rt-radius);
        background: var(--rt-card);
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        line-height: 1.2;
        cursor: pointer;
        white-space: nowrap;
        color: var(--rt-ink-soft);
      }
      .rt-reset-btn:hover {
        color: #D64550;
        border-color: #D64550;
        background: #FEF2F2;
      }

      #page2 label,
      #page3 label {
        display: inline-block;
        font-size: 13px;
        font-weight: 700;
        font-family: "Poppins", sans-serif;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #555;
        margin-bottom: 10px;
        cursor: pointer;
      }
    </style>

    {{-- Tab bar: purchaseOrder.blade.php's own card.tab-card + custom-tabs pattern,
         copied 1:1. Same two tabs/targets as before (data-bs-toggle, not PO's
         data-toggle -- this page runs real Bootstrap 5, PO's newmasterx layout
         runs the Canvas Bootstrap-4-era theme's jQuery plugin instead). --}}
    <div class="card mb-3 tab-card">
      <div class="card-body">
        <div class="nav nav-tabs border-0 custom-tabs" id="giroTab" role="tablist">
          <button class="nav-item nav-link active" id="tab-dibuka-btn" data-bs-toggle="tab" data-bs-target="#profile2" type="button" role="tab">Penawaran</button>
          <button class="nav-item nav-link" id="tab-diterima-btn" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab">SO</button>
        </div>
      </div>
    </div>

    {{-- Toolbar shared by both tabs (Penawaran/SO): one filter/search/date-range drives
         BOTH tables via one soloadall call -- that's SO's own existing behavior, kept
         exactly as-is, just restyled with PO's po-toolbar/po-search-inp/po-filter-wrap/
         po-btn-filter classes instead of report-table.css's toolbar/search-inp/filter-wrap. --}}
    <div class="card">
      <div class="card-body" style="padding:0;">
    <div class="po-toolbar">


      {{-- <div class="po-len-wrap">
        <label for="tabel_length_visual">Tampilkan</label>
        <select id="tabel_length_visual" class="po-len-inp">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
          <option value="-1">Semua</option>
        </select>
      </div> --}}

      <div class="po-filter-wrap">
        <label>Periode</label>
        <input type="date" onchange="" class="po-filter-inp" id="input_tanggalawal" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
        <span class="po-filter-sep">s/d</span>
        <input type="date" onchange="" class="po-filter-inp" id="input_tanggalakhir" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
      </div>

      <input type="search" id="tabel_filter_visual" class="po-search-inp" placeholder="Cari data">
      
      <button class="po-btn-filter" type="button" onclick="$('#modalFilter').modal('show')">
        <i class="bi bi-funnel"></i> Filter
      </button>

      <div class="po-toolbar-act">
        <button id='AddVisibility' class="btn btn-action-primary" onclick="buttonAdd()">+ Add</button>
      </div>

    </div>
      </div>
    </div>

    <div class="modal fade rt-filter" id="modalFilter" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bi bi-funnel"></i> Filter Data
              <span class="rt-active-badge" id="soFilterBadge" style="display:none">1 aktif</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="rt-section">
              <div class="rt-group-label">Status</div>
              <div class="rt-grid-2">
                <div>
                  <label class="rt-field-label">Status SO</label>
                  <select id="input_filterso" class="rt-native" aria-label="Filter SO">
                    <option value=0 selected>Semua SO</option>
                    <option value=1>SO Belum Otorisasi</option>
                    <option value=2>SO Sudah Otorisasi</option>
                    <option value=3>Belum proses</option>
                    <option value=4>Proses Sebagian</option>
                    <option value=5>Full supply</option>
                    <option value=6>SO Terclose</option>
                  </select>
                </div>
                <div>
                  <label class="rt-field-label">Tipe Bayar</label>
                  <select id="input_tipebayar" class="rt-native" aria-label="Filter Tipe Bayar">
                    <option value=4 selected>Semua Tipe</option>
                    <option value=0>CBD</option>
                    <option value=1>Kredit</option>
                    <option value=2>Termin</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="rt-reset-link" onclick="soResetFilterFields()">Reset</button>
            <div class="rt-footer-buttons">
              <button type="button" class="rt-btn rt-btn-ghost" data-bs-dismiss="modal">Batal</button>
              <button type="button" class="rt-btn rt-btn-primary" onclick="buttonFilterSO(); $('#modalFilter').modal('hide');">Terapkan</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
    <div class="card-body" style="padding:0;">
    <div class="tab-content" id="myTabContent">

      {{-- tab baru 2--}}
      <div class="tab-pane fade show active" id="profile2" role="tabpanel" aria-labelledby="profile2-tab">
        <div class="rt-bar-row">
          <button class="rt-reset-btn" type="button" title="Reset kolom" onclick="buttonHeaderTable()">
            <i class="bi bi-arrow-counterclockwise"></i> Reset kolom
          </button>
          <div id="rtBarTabel7"></div>
        </div>
        <div class="po-table-wrap">
          <table id="tabel7" class="tb data-table">

            <thead style="white-space:nowrap;"></thead>
            <tbody id="tabel7_data" class="text-left">
            </tbody>
          </table>
        </div>
        <div class="po-rt-hint">
          <i class="bi bi-info-circle"></i>
          Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
          untuk menyembunyikan kolom atau mengatur jumlah desimal.
        </div>
      </div>

      <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
        <div class="rt-bar-row">
          <button class="rt-reset-btn" type="button" title="Reset kolom" onclick="buttonHeaderTable()">
            <i class="bi bi-arrow-clockwise"></i> Reset kolom
          </button>
          <div id="rtBarTabel"></div>
        </div>
        <div class="po-table-wrap">
          <table id="tabel" class="tb data-table">
            {{-- Header content is fully JS-owned: replaceTheadWithHeader()
                 (called from renderTabelRows(), which runs on page load via
                 reinitTabel()) replaces this <thead>'s contents entirely
                 based on gcart_header, before the user ever sees it. The
                 tag itself is just a placeholder for that selector.
                 (The old markup here also had a dead, always-commented-out
                 Oto2-5 @if($level>1..4) block for a multi-level-approval
                 view that gcart_header doesn't model -- dropped with it.) --}}
            <thead style="white-space:nowrap;"></thead>

            <tbody id="tabel_data" class="text-left">
            </tbody>
          </table>
        </div>
        <div class="po-rt-hint">
          <i class="bi bi-info-circle"></i>
          Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
          untuk menyembunyikan kolom atau mengatur jumlah desimal.
        </div>
      </div>

      {{-- #tabel2/#tabel_oto stay plain tables (no drag/gear columns) -- matching
           purchaseOrder.blade.php's own "Purchase Order"/"PO Otorisasi" tabs, which are
           also plain. Neither pane has a nav button in #giroTab above, same as before this
           rebuild -- they're not currently reachable from the UI; only their internal
           markup/CSS and data-refresh wiring (loadAll -> soDataTabel2Tabel_oto()) changed. --}}
      <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
        <div class="po-table-wrap">
          <table id="tabel2" class="data-table">
            <thead style="white-space:nowrap;">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
                <th style="padding: 4px 12px;" scope="col">No Bukti</th>
                <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                <th style="padding: 4px 12px;" scope="col">Nama Pelanggan</th>
                <th style="padding: 4px 12px;" scope="col">PO Customer</th>
                <th style="padding: 4px 12px;" scope="col">DPP</th>
                <th style="padding: 4px 12px;" scope="col">PPN</th>
                <th style="padding: 4px 12px;" scope="col">Total</th>
                <th style="padding: 4px 12px;" scope="col">Oto</th>
                <th style="padding: 4px 12px;" scope="col">User Oto</th>
                <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
              </tr>
            </thead>

            {{-- Rows are JS-owned (renderTabel2Rows(), from lastTabel2Rows) so the exact
                 same markup serves both the initial page load and loadAll()'s refresh --
                 matching #tabel/#tabel7's lastTabelRows/renderTabelRows pattern. --}}
            <tbody id="tabel2_data" class="text-left">
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane fade" id="home2" role="tabpanel" aria-labelledby="home2-tab">
        <div class="po-table-wrap">
          <table id="tabel_oto" class="data-table">
            <thead style="white-space:nowrap;">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
                <th style="padding: 4px 12px;" scope="col">No Bukti</th>
                <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                <th style="padding: 4px 12px;" scope="col">Nama Pelanggan</th>
                <th style="padding: 4px 12px;" scope="col">PO Customer</th>
                <th style="padding: 4px 12px;" scope="col">DPP</th>
                <th style="padding: 4px 12px;" scope="col">PPN</th>
                <th style="padding: 4px 12px;" scope="col">Total</th>
                <th style="padding: 4px 12px;" scope="col">Open CBD</th>
                <th style="padding: 4px 12px;" scope="col">User Open CBD</th>
                <th style="padding: 4px 12px;" scope="col">Tgl Open CBD</th>
                <th style="padding: 4px 12px;" scope="col">Oto</th>
                <th style="padding: 4px 12px;" scope="col">User Oto</th>
                <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
              </tr>
            </thead>
            {{-- Rows are JS-owned (renderTabelOtoRows(), from lastTabelOtoRows) --
                 see the matching note on #tabel2_data above. --}}
            <tbody id="tabel_oto_data" class="text-left">
            </tbody>
          </table>
        </div>
      </div>

    </div>
    </div>
    </div>
  </div>
</div>

<div id="page2" class="container-fluid" style="display: none">
  <div class="row d-flex justify-content-between align-items-center">
    <div class="col-auto text-left">
      {{-- <h2>Form Sales Order</h2> --}}
    </div>
    <div class="col-auto text-right">
      <button type="button" class="btn btn-action-danger btn-lg" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              margin-bottom: 30px;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonCloseForm()">
        Close
      </button>
    </div>
  </div>
  <div id="modalBodyAddMain" class="">
    <div class="modal-body">

      <!-- <div class="container-fluid"> -->

      <div class="row g-3">

        <input type="hidden" class="form-control" id="input_add_nourut">

        <div class="col-md-3">

          <div class="row align-items-center mb-2">
            <div class="col-4">
              <label class="form-label mb-0">Pelanggan</label>
            </div>
            <div class="col-8">
              <div class="input-group position-relative">
                <input
                  type="text"
                  class="form-control text-left"
                  placeholder="Cari Pelanggan..."
                  id="input_add_kodepelanggan"
                  onkeyup="searchPelanggan(this.value)"
                  autocomplete="off">
                <div id="dropdown_pelanggan"
                  class="dropdown-menu w-100">
                </div>
              </div>
            </div>
          </div>

          <!--  <div class="col-md-4" style="margin-top:-12px;" hidden>
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-md-8" style="margin-top:-12px;" hidden>
                <div class="form-group">
                  <input type="text" class="form-control text-left" id="input_add_nobukti" placeholder="" disabled>
                </div>
              </div> -->

          <div class="row align-items-start mb-2">
            <div class="col-4">
              <label class="form-label mb-0">No PO</label>
            </div>
            <div class="col-8">
              <div class="input-group">
                <textarea type="text" onkeyup="searchNoPO(this.value)" autocomplete="off"
                  class="form-control" style="width: 100%; resize: none" rows=3 placeholder="Ketik No PO"
                  class="form-control text-left" id="input_add_nopo"></textarea>

                <input type="hidden" class="form-control text-left" id="input_add_idpo" placeholder="">
                <!-- <input type="text" class="form-control" id="input_add_nopo" onblur="onChangeNoPO()" disabled>
                    <button onclick="buttonAddListNoPo()" id="buttonAddListNoPo"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
              </div>
              <div id="dropdown_nopo" class="dropdown-menu" style="width:100%"></div>
            </div>
          </div>

        </div>

        <div class="col-md-3">

          <div class="row align-items-center mb-2">
            <div class="col-4">
              <label class="form-label mb-0">Nama Pelanggan</label>
            </div>
            <div class="col-8">
              <input type="text" class="form-control text-left" placeholder="Nama Pelanggan" id="input_add_namapelanggan" disabled>
            </div>
          </div>

          <div class="row align-items-start mb-2">
            <div class="col-4">
              <label class="form-label mb-0">Alamat Pelanggan</label>
            </div>
            <div class="col-8">
              <textarea style="width: 100%; resize: none" rows=3 placeholder="Alamat Pelanggan" class="form-control text-left" id="input_add_alamatpelanggan" disabled></textarea>
            </div>
          </div>

        </div>

        <div class="col-md-3">

          <div class="row" style="display: none">

            <div class="col-md-6" style="display: none">

              <div class="row" style="display: none">
                <div class="col-9">
                  <div class="form-group">
                    <label>Valas</label>
                  </div>
                </div>
                <div class="col-3 text-right">
                  <div class="form-group">
                  </div>

                </div>

              </div>
            </div>
            <div class="col-md-6" style="display: none">
              <div class="row">
                <div class="col-md-12">
                  <div class="input-group form-group">
                    <input type="text" class="form-control" id="input_add_valas" disabled>
                    <button onclick="buttonAddListValas()" id="buttonAddListValas" class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>

                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row" style="display: none">
            <div class="col-md-12">
              <div class="row">
                <div class="col-6">
                  <div class="form-group">
                    <label>Kurs</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control text-right" id="input_add_kurs" disabled>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row align-items-center mb-2">
            <div class="col-4">
              <label class="form-label mb-0">TOP</label>
            </div>
            <div class="col-8">
              <input type="number" class="form-control text-right" id="input_add_hari" onblur="onChangeHari()" value=0 min=0>
            </div>
          </div>

          <div class="row align-items-center mb-2">
            <div class="col-4">
              <label class="form-label mb-0">Tanggal</label>
            </div>
            <div class="col-8">
              <input type="date" class="form-control text-left" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" disabled>
            </div>
          </div>

          <div class="row align-items-center mb-2">
            <div class="col-4">
              <label class="form-label mb-0">No Bukti</label>
            </div>
            <div class="col-8">
              <input type="text" class="form-control text-left" id="input_add_nobukti" placeholder="" disabled>
            </div>
          </div>

        </div>

        <div class="col-md-3">

          <div class="row align-items-center mb-2">
            <div class="col-4">
              <label class="form-label mb-0">Pembayaran</label>
            </div>
            <div class="col-8">
              <select id="input_add_pembayaran" onchange="onChangeInputAddPembayaran()" class="form-control text-center" aria-label=".form-select-lg example">
                <option value=0 selected>Tunai/CBD</option>
                <option value=1>Kredit</option>
              </select>
            </div>
          </div>

          <div class="row align-items-center mb-2">
            <div class="col-4">
              <label class="form-label mb-0">Tgl Kirim</label>
            </div>
            <div class="col-8">
              <input type="date" class="form-control text-center" id="input_add_tanggalkirim" value="{!! date('Y-m-d') !!}" onblur="onChangeTgglKirim()">
            </div>
          </div>

          <div class="row align-items-center mb-2">
            <div class="col-4">
              <label class="form-label mb-0">PPN</label>
            </div>
            <div class="col-8">
              <select onchange="onChangeTipePPN()" id="input_add_tipeppn" class="form-control text-center" aria-label=".form-select-lg example">
                <option value=0 selected>None</option>
                <option value=1>Exclude</option>
                <option value=2>Include</option>
              </select>
            </div>
          </div>

        </div>

      </div>

      <div class="row">
        <div class="col-md-12 mt-2 text-left">
          <button type="button" class="btn btn-lg btn-show-hide-header btn-action-primary" style="
                height: 38px;
                width: 38px;
                margin-top: -35px;
                padding: 0;
                border-radius: 8px;
                font-size: 1.15rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="buttonShowHideHeader()" title="Show/Hide Header"><i class="bi bi-truck"></i></button>
        </div>
      </div>
      <div class="showhidemodalbodyaddmain mt-4" id="modalBodyAddMainHeader" style="display: none">
        <div class="row">
          <div class="col-md-3">
            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>Alamat Kirim</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
                  <button onclick="buttonAddListAlamatKirim()" id="buttonAddListAlamatKirim" class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                </div>

              </div>

              <!-- <div class="col-md-12">
                    <div class="form-group">

                    </div>
                  </div> -->
              <div class="col-md-12">
                <div class="form-group">
                  <input type="hidden" class="form-control" id="input_add_kodealamatkirim">
                  <textarea type="text" style="width: 100%; resize: none" rows=4 class="form-control" id="input_add_alamatkirim" disabled></textarea>
                </div>
              </div>

            </div>

          </div>
          <div class="col-md-3">
            <div class="row">

              <div class="col-9">
                <div class="form-group">
                  <label>Lokasi Penerima</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
                  <button onclick="buttonAddListLokasiPenerima()" id="buttonAddListLokasiPenerima" class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                </div>

              </div>

              <!-- <div class="col-md-12">
                    <div class="form-group">

                    </div>
                  </div> -->
              <div class="col-md-12">
                <div class="form-group">
                  <input type="hidden" class="form-control" id="input_add_kodelokasipenerima">
                  <textarea type="text" style="width: 100%; resize: none" rows=4 class="form-control text-left" id="input_add_alamatlokasipenerima" disabled></textarea>
                </div>
              </div>

            </div>

            <!-- <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>PIC</label>
                      </div>
                    </div>
                    <div class="col-3 text-right">
                      <div class="form-group">
                    <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                    </div>

                  </div>
                  </div> -->
            <div class="row">

              <!-- <div class="col-md-12">
                    <div class="form-group">

                    </div>
                  </div> -->

            </div>

            <div class="row">

              <!-- <div class="col-md-12">

                  </div> -->
              <!-- <div class="col-md-12">
                    <div class="form-group">
                      <input type="hidden" class="form-control" id="input_add_kodesales" >
                      <input type="text" class="form-control" id="input_add_namasales"  disabled>
                    </div>
                  </div> -->
            </div>

          </div>

          <div class="col-md-3">

            <div class="row">
              <div class="col-md-12">
                <label>Keterangan</label>
              </div>

              <div class="col-md-12">

                <div class="form-group">
                  <textarea type="text" style="width: 100%; resize: none" rows=4 class="form-control" id="input_add_catatan" onblur="onChangeCatatan()"></textarea>

                </div>
              </div>

            </div>

            <div class="row">

            </div>

            <div class="row">

            </div>

            <div class="row ">

            </div>

          </div>

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4 mb-2">
                <div class="form-group">
                  <label>DP</label>
                </div>
              </div>

              <div class="col-md-8 mb-2">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_add_dp" value='0.00' onBlur="onChangeDP()">
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label>Tgl PO</label>
                </div>
              </div>

              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_add_tanggalpo" value="{!! date('Y-m-d') !!}" onblur="onChangeTgglPO()">
                </div>
              </div>
            </div>

            <div class="row">

              <!-- <div class="col-md-12">

                  </div> -->

            </div>

          </div>
          <!-- <div class="col-md-12 mt-2 text-right" style="margin-bottom: 20px">
                <button type="button" class="btn btn-primary" id="buttonSubmitSaveHeader" onclick="submitSaveHeader()" >Save Header</button>
            </div> -->
        </div>
        {{-- Second explicit row (was previously just a wrap of the same
                   24-unit row as Alamat Kirim/Lokasi Penerima/Keterangan/DP
                   above -- splitting it out so each visual row is its own
                   clean 4x col-md-3=12, not dependent on Bootstrap's wrap
                   behavior lining up with these fields' differing heights). --}}
        <div class="row mt-2">
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-6">
                <div class="row">

                  <div class="col-9">
                    <div class="form-group">
                      <label>Inside Sales</label>
                    </div>
                  </div>
                  <div class="col-3 text-right">
                    <div class="form-group">
                      <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                    </div>

                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-12">
                    <div class="row">

                      <!-- <div class="col-4">

                      <div class="form-group">

                      </div>

                      </div> -->
                      <div class="col-12">

                        <div class="input-group form-group">
                          <input type="hidden" class="form-control" id="input_add_kodebackoffice">
                          <input type="text" class="form-control" id="input_add_namabackoffice" disabled>
                          <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus" hidden></i></button> -->

                        </div>

                      </div>
                    </div>
                    <!-- </div> -->
                  </div>
                </div>

              </div>

              <!-- <div class="row"> -->

              <!-- </div> -->

            </div>

          </div>

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-6">
                <div class="row">

                  <div class="col-9">
                    <div class="form-group">
                      <label>PIC</label>
                    </div>
                  </div>
                  <div class="col-3 text-right">
                    <div class="form-group">
                      <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                    </div>

                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-12">
                    <div class="input-group form-group">
                      <input type="hidden" class="form-control" id="input_add_kodepic">
                      <input type="text" class="form-control" id="input_add_namapic" disabled>
                      <button onclick="buttonAddListPIC()" id="buttonAddListPIC" class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>

                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <div class="col-9">
                    <div class="form-group">
                      <label>Sales</label>
                    </div>
                  </div>
                  <div class="col-3 text-right">
                    <div class="form-group">
                      <!-- <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                    </div>

                  </div>

                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group form-group">
                  <input type="hidden" class="form-control" id="input_add_kodesales">
                  <input type="text" class="form-control" id="input_add_namasales" disabled>
                  <!-- <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

                </div>
              </div>

            </div>

          </div>

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Draft PO</label>
                </div>
              </div>

              <div class="col-md-8">
                <div class="form-group">
                  <select onchange="onChangeDraftPO()" id="input_add_draftpo" class="form-control mb-3" aria-label=".form-select-lg example">
                    <option value=0 selected>Tidak</option>
                    <option value=1>Ya</option>
                  </select>
                </div>
              </div>
            </div>

          </div>

        </div>

      </div>

      <hr />
    </div>

  </div>
  <div class="showhidemodalbodyaddmain container-fluid" id="modalBodyAddMainItems">

    <!-- sinia -->

    <!-- END ADD EDIT -->

    <div class="container-fluid" style="overflow:auto;">
      <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
      <div class="row">
        <table id="tabel_add" class="table table-bordered table-hover table-striped table-responsive-lg">
          <thead class="text-center">
            <tr>
              <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
              <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
              <th style="padding: 4px 12px;" scope="col">Nama Alias</th>
              <th style="padding: 4px 12px;" scope="col">Merk</th>
              <th style="padding: 4px 12px;" scope="col">Qty</th>
              <th style="padding: 4px 12px;" scope="col">Sat</th>
              <th style="padding: 4px 12px;" scope="col">Tax</th>
              <th style="padding: 4px 12px;" scope="col">Harga</th>
              <th style="padding: 4px 12px;" scope="col">Diskon</th>
              <th style="padding: 4px 12px;" scope="col">NDPP</th>
              <th style="padding: 4px 12px;" scope="col">No SPK</th>
              <th style="padding: 4px 12px;" scope="col">Actions</th>
            </tr>
          </thead>
          <tbody id="tabel_data_add" class="text-left">
            <tr>

              <td>1</td>
              <td>1</td>
              <td>1</td>
              <td>1</td>
              <td>1</td>
              <td>1</td>
              <td>1</td>
              <td>1</td>
              <td>1</td>
              <td>1</td>
              <td>1</td>
              <td class="text-center">
                <div class="btn-group" role="group">
                  <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="">
                    <i class="bi bi-info"></i>
                  </button>
                  <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="">
                    <i class="bi bi-key-fill"></i>
                  </button>
                  <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="">
                    <i class="bi bi-pencil-fill"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>

        </table>
      </div>
      <!-- <button onclick="buttonSubKategori()">tes</button> -->

    </div>

    <div class="row d-flex justify-content-between align-items-center">
      <div class="col-auto mt-2 text-right">
        <button type="button" class="btn btn-action-primary btn-lg" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonTambahSOAll()"><b>+ Tambah Penawaran</b></button>
      </div>
      <div class="col-auto mt-2 text-right">
        <button type="button" class="btn btn-action-primary btn-lg" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonAddAddItem()"><b>+ Tambah Item</b></button>
      </div>
    </div>

    <!-- ADD add -->
    <div id="addAddItem" class="container-fluid showhide">
      <hr />
      <!-- <div class="line"></div> -->
      <!-- <hr/> -->
      <div class="row">
        <div class="col-4">
          <h4 id="h4AddAddItem">Add Item</h4>
          <h4 id="h4AddEditItem">Edit Item</h4>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">

          <div class="row">

            <!-- Barang dan Nama Produk -->
            <div class="col-md-12">
              <!-- Barang -->
              <div class="row">
                <div class="col-3">
                  <div class="form-group">
                    <label>Ref Pr</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="input-group form-group">
                    <input type="text" class="form-control" id="input_add_add_refpr" onkeypress="" disabled>
                    <button onclick="buttonAddAddListRefPr()" id="buttonAddAddListRefPr" class="btn btn-primary btn-sm text-right" tabindex="1">
                      <i class="bi bi-plus"></i>
                    </button>
                  </div>
                </div>

                <div class="col-md-5">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label>No. Penye</label>
                      </div>
                    </div>
                    <div class="col-md-9">
                      <div class="form-group input-group">
                        <input type="text" class="form-control text-right " id="input_add_add_nopenyerahan" value="" tabindex="5" disabled>
                        <button onclick="buttonAddAddListNoPenyerahan()" id="buttonAddAddListNoPenyerahan" class="btn btn-primary btn-sm text-right" tabindex="1">
                          <i class="bi bi-plus"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
              <!-- Nama Produk -->

            </div>
            <!-- Harga -->

            <!-- Disc RP -->

          </div>
        </div>
      </div>

      <div class="row">

        <!-- Barang dan Nama Produk -->
        <div class="col-md-6">
          <!-- Barang -->
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Barang</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group form-group">
                <input type="text" class="form-control" id="input_add_add_kodebarang" onkeypress="onKeyPressBarang(event)">
                <button onclick="buttonAddAddListBarang()" id="buttonAddAddListBarang" class="btn btn-primary btn-sm text-right" tabindex="1">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
            </div>
            <div class="col-md-5">
              <div class="input-group form-group">
                <input type="text" class="form-control" id="input_add_add_namabarang" disabled>

              </div>
            </div>
          </div>
          <!-- Nama Produk -->
          <div class="row mt-2">
            <div class="col-3">
              <div class="form-group">
                <label>Nama Alias</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_add_namaproduk" tabindex="2">
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <!-- Harga -->
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Qty</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <input type="text" id="input_add_add_qty" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number" tabindex="5">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Disc %</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <input type="number" class="form-control text-right" id="input_add_add_disc" onChange="onChangeInputAddAddDisc()" value="0.00" tabindex="8">
              </div>
            </div>

          </div>

          <div class="row mt-2">

            <div class="col-md-3">
              <div class="form-group">
                <label>Harga</label>
              </div>
            </div>
            <div class="col-md-3">
              <input type="text" id="input_add_add_harga" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number" onchange="onChangeInputAddAddHarga()" tabindex="6">
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Disc Rp</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <input type="text" id="input_add_add_discrp" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number" onChange="onChangeInputAddAddDiscRp()" tabindex="7">
              </div>

            </div>

          </div>
        </div>
      </div>

      <!-- Disc RP -->

      <div class="row mt-2">

        <div class="col-md-12">
          <div class="row">
            <!-- Satuan -->
            <div class="col-md-3">
              <div class="row">
                <div class="col-6">
                  <div class="form-group">
                    <label>Satuan Tax</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group input-group">
                    <input type="hidden" class="form-control" id="input_add_add_kodesattax" disabled>

                    <input type="text" class="form-control" id="input_add_add_sattax" disabled>
                    <button onclick="buttonAddAddListSattax()" id="buttonAddAddListSattax" class="btn btn-primary btn-sm text-right">
                      <i class="bi bi-plus"></i>
                    </button>
                  </div>
                </div>
              </div>

            </div>
            <div class="col-md-3">
              <div class="row">
                <!-- Satuan -->
                <div class="col-md-10 ">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Sat Alias</label>
                      </div>
                    </div>
                    <div class="col-md-8">
                      <div class="input-group form-group">
                        <input type="text" class="form-control" id="input_add_add_satuanproduk">

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="row">
                <!-- Satuan -->
                <div class="col-md-12">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Satuan</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <select id="input_add_add_nosat" onchange="onChangeInputAddAddNosat()" class="form-control mb-3" tabindex="4">
                        <option value=0 selected>Pilih Satuan</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3" style="">
              <div class="row">

                <div class="col-md-6">

                  <div class="form-group">

                    <label>Tgl Kirim</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <input type="date" class="form-control text-right" onchange="()" id="input_add_add_tglkirim" tabindex="6">

                  </div>
                </div>

              </div>
            </div>

          </div>
        </div>

      </div>

      <div class="row">
        <div class="col-md-3">
          <div class="row">
            <!-- Satuan Produk -->
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Booking</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <!-- <input type="number" class="form-control text-right" id="input_add_add_qty" value="0.00" tabindex="5"> -->
                    <select id="input_add_add_booking" class="form-control mb-3" tabindex="10">
                      <option value=0 selected>Tidak</option>
                      <option value=1>Ya</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <!-- Tambah Ke PO -->
          <div class="row">
            <div class="col-md-10">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <!-- <label>Tambah Ke PO</label> -->
                    <label>Urgent</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <select id="input_add_add_urgent" class="form-control mb-3" tabindex="11">
                    <option value=0 selected>Tidak</option>
                    <option value=1>Ya</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="col-md-3">
          <div class="row">

            <div class="col-md-6">

              <div class="form-group">

                <label>Tambah Ke PO</label>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <select id="input_add_add_tambahkepo" class="form-control mb-3" tabindex="9">
                  <option value=0 selected>Tidak</option>
                  <option value=1>Ya</option>
                </select>
              </div>
            </div>

          </div>
        </div>

        <div class="col-md-3" style="">
          <div class="row">

            <div class="col-md-6">

              <div class="form-group">

                <label>Status</label>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <select id="input_add_add_status" class="form-control mb-3" tabindex="9">
                  <option value=0 selected>R</option>
                  <option value=1>P0</option>
                  <option value=2>P1</option>
                  <option value=3>P2</option>
                </select>
              </div>
            </div>

          </div>
        </div>

      </div>

      <div class="row">
        <div class="col-md-12">

          <div id="divhargaterakhir" class="">
            <div class="row">

              <div class="col-12">
                <div class="form-group">
                  <label>Harga Terakhir</label>
                </div>
              </div>

              <div class="col-md-12 mb-4" style="overflow:auto;">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <table id="tabel_add_harga_terakhir" class="table table-bordered table-hover table-striped table-responsive-lg">
                    <thead class="text-center">
                      <tr>
                        <th colspan="6">History Harga Jual</th>
                      </tr>
                      <tr>
                        <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                        <th style="padding: 4px 12px;" scope="col">Qty</th>
                        <th style="padding: 4px 12px;" scope="col">Satuan</th>
                        <th style="padding: 4px 12px;" scope="col">Harga</th>
                        <th style="padding: 4px 12px;" scope="col">Disc Rp</th>
                        <th style="padding: 4px 12px;" scope="col">Total Diskon</th>
                      </tr>
                    </thead>

                    <tbody id="tabel_data_add_harga_terakhir" class="text-left">
                      <tr>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                      </tr>
                    </tbody>
                  </table>
                  <table id="tabel_add_harga_beli" class="table table-bordered table-hover table-striped table-responsive-lg">
                    <thead class="text-center">
                      <tr>
                        <th colspan="6">History Harga Beli</th>
                      </tr>
                      <tr>
                        <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                        <th style="padding: 4px 12px;" scope="col">Qty</th>
                        <th style="padding: 4px 12px;" scope="col">Satuan</th>
                        <th style="padding: 4px 12px;" scope="col">Harga</th>
                        <th style="padding: 4px 12px;" scope="col">Disc Rp</th>
                        <th style="padding: 4px 12px;" scope="col">Total Diskon</th>
                      </tr>
                    </thead>

                    <tbody id="tabel_data_add_harga_beli" class="text-left">
                      <tr>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                      </tr>
                    </tbody>
                  </table>

                </div>
              </div>

            </div>
          </div>

          <div class="row mt-2">
            <div class="col-md-12 d-flex justify-content-end mb-3" style="gap: 10px;">

              <button type="button" class="btn btn-action-success btn-lg" style="
                      height: 30px;
                      padding: 4px 12px;
                      border-radius: 20px;
                      font-size: 0.75rem;
                      font-weight: 600;
                      text-transform: uppercase;
                      transition: background-color 0.3s, box-shadow 0.3s;
                      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="showTableHargaTerakhir()">Histori Harga</button>

              <button type="button" class="btn btn-action-danger btn-lg" style="
                      height: 30px;
                      padding: 4px 12px;
                      border-radius: 20px;
                      font-size: 0.75rem;
                      font-weight: 600;
                      text-transform: uppercase;
                      transition: background-color 0.3s, box-shadow 0.3s;
                      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="closeShowHideAdd()">Batal</button>

              <button type="button" id="submitAddAdd" class="btn btn-action-primary btn-lg" style="
                      height: 30px;
                      padding: 4px 12px;
                      border-radius: 20px;
                      font-size: 0.75rem;
                      font-weight: 600;
                      text-transform: uppercase;
                      transition: background-color 0.3s, box-shadow 0.3s;
                      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="submitAddAdd()">Simpan Data</button>

              <button type="button" id="submitAddEdit" class="btn btn-action-primary btn-lg" style="
                      height: 30px;
                      padding: 4px 12px;
                      border-radius: 20px;
                      font-size: 0.75rem;
                      font-weight: 600;
                      text-transform: uppercase;
                      transition: background-color 0.3s, box-shadow 0.3s;
                      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="submitAddEdit()">Simpan Data</button>
            </div>
            <div id="addEditItem" class="container-fluid showhide">
              <!-- <div class="line"></div> -->
              <!-- <hr/> -->
              <div class="row">
                <div class="col-4">
                  <h4>Edit Item</h4>
                </div>
              </div>
              <div class="row">

                <div class="col-md-12">

                  <div class="row">

                    <div class="col-md-4">

                      <div class="row">
                        <div class="col-9">
                          <div class="form-group">
                            <label>Ref PR</label>
                          </div>
                        </div>
                        <div class="col-3 text-right">
                          <div class="form-group">
                            <button onclick="" class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus"></i></button>
                          </div>

                        </div>

                        <div class="col-md-12">
                          <div class="form-group">
                            <input type="text" class="form-control" id="input_add_edit_refpr" value="" disabled>
                          </div>
                        </div>

                      </div>

                    </div>

                    <div class="col-md-4">

                      <div class="row">
                        <div class="col-9">
                          <div class="form-group">
                            <label>No Penyerahan</label>
                          </div>
                        </div>
                        <div class="col-3 text-right">
                          <div class="form-group">
                            <button onclick="" class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus"></i></button>
                          </div>

                        </div>

                        <div class="col-md-12">
                          <div class="form-group">
                            <input type="text" class="form-control" id="input_add_edit_nopenyerahan" disabled>
                          </div>
                        </div>

                      </div>

                    </div>
                  </div>
                </div>

                <div class="col-md-4">

                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>Barang</label>
                      </div>
                    </div>
                    <div class="col-3 text-right">
                      <div class="form-group">
                        <button onclick="buttonAddEditListBarang()" id="buttonAddEditListBarang" class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus"></i></button>
                      </div>

                    </div>

                    <div class="col-md-12">
                      <div class="form-group">
                        <input type="hidden" class="form-control" id="input_add_edit_kodebarang">
                        <input type="text" class="form-control" id="input_add_edit_namabarang" disabled>
                      </div>
                    </div>

                  </div>

                </div>

                <!-- <div class="col-md-4">

                        <div class="row">
                          <div class="col-12">
                            <div class="form-group">
                              <label>Nama Barang</label>
                            </div>
                          </div>

                        <div class="col-md-12">
                          <div class="form-group">
                            <input type="text" class="form-control" id="input_add_edit_namabarang"  disabled>
                          </div>
                        </div>

                        </div>

                        </div> -->

                <div class="col-md-4">

                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                        <label>Nama Alias</label>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group">
                        <input type="text" class="form-control" id="input_add_edit_namaproduk">
                      </div>
                    </div>

                  </div>

                </div>

                <div class="col-md-12">

                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                        <label>Harga Terakhir</label>
                      </div>
                    </div>

                    <div class="col-md-12 mb-4">
                      <div class="form-group">
                        <table id="tabel_edit_harga_terakhir" class="table table-bordered table-striped">
                          <thead class="text-center">
                            <tr>
                              <th scope="col">Tanggal</th>
                              <th scope="col">Qty</th>
                              <th scope="col">Satuan</th>
                              <th scope="col">Valas</th>
                              <th scope="col">Kurs</th>
                              <th scope="col">Harga</th>
                              <th scope="col">Disc Rp</th>
                              <th scope="col">Total Diskon</th>

                            </tr>
                          </thead>

                          <tbody id="tabel_data_edit_harga_terakhir" class="text-left">

                            <tr>

                              <td>-</td>
                              <td>-</td>
                              <td>-</td>
                              <td>-</td>
                              <td>-</td>
                              <td>-</td>
                              <td>-</td>
                              <td>-</td>

                            </tr>
                          </tbody>

                        </table>
                      </div>
                    </div>

                  </div>

                </div>

                <div class="col-md-12">
                  <div class="row">

                    <div class="col-md-2">
                      <div class="row">

                        <div class="col-md-12">
                          <div class="form-group">
                            <label>Qty</label>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group">
                            <input type="text" id="input_add_edit_qty" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number" onChange="onChangeInputAddAddDiscRp()">
                          </div>
                        </div>

                      </div>
                    </div>

                    <div class="col-md-2">
                      <div class="row">

                        <div class="col-12">
                          <div class="form-group">
                            <label>Satuan</label>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <select id="input_add_edit_nosat" onchange="onChangeInputAddAddNosat()" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                            <option value=0 selected>Pilih Satuan</option>
                          </select>
                        </div>

                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="row">

                        <div class="col-12">
                          <div class="form-group">
                            <label>Satuan Produk</label>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group">
                            <input type="text" class="form-control" id="input_add_edit_satuanproduk">
                          </div>
                        </div>

                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="row">

                        <div class="col-12">
                          <div class="form-group">
                            <label>Harga</label>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group">
                            <input type="text" id="input_add_edit_harga" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number" onchange="onChangeInputAddAddHarga()">
                          </div>
                        </div>

                      </div>
                    </div>

                    <div class="col-md-2">
                      <div class="row">

                        <div class="col-12">
                          <div class="form-group">
                            <label>Disc %</label>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group">
                            <input type="text" id="input_add_edit_disc" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number" onChange="onChangeInputAddAddDisc()">
                          </div>
                        </div>

                      </div>
                    </div>

                    <div class="col-md-2">
                      <div class="row">

                        <div class="col-12">
                          <div class="form-group">
                            <label>Disc Rp</label>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group">
                            <input type="number" class="form-control text-right" id="input_add_edit_discrp" onChange="onChangeInputAddAddDiscRp()" value="0.00">
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="row">

                  </div>
                </div>

                <div class="col-md-2">
                  <div class="row">

                    <div class="col-12">
                      <div class="form-group">
                        <label>Tambah ke PO</label>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <select onchange="" id="input_add_edit_tambahkepo" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                        <option value=0 selected>Pilih</option>
                        <option value=1>Tidak</option>
                        <option value=2>Ya</option>
                      </select>
                    </div>

                  </div>
                </div>

                <div class="col-md-2">
                  <div class="row">

                    <div class="col-12">
                      <div class="form-group">
                        <label>Booking</label>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <select onchange="" id="input_add_edit_booking" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                        <option value=0 selected>Tidak</option>
                        <option value=1>Ya</option>
                      </select>
                    </div>

                  </div>
                </div>

                <div class="col-md-2">
                  <div class="row">

                    <div class="col-12">
                      <div class="form-group">
                        <label>Urgent</label>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <select onchange="" id="input_add_edit_urgent" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                        <option value=0 selected>Tidak</option>
                        <option value=1>Ya</option>
                      </select>
                    </div>

                  </div>
                </div>

              </div>

              <div class="row mt-2">
                <div class="col-md-12 text-right">
                  <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()">Batal</button>
                </div>

              </div>

              <hr />

            </div>

            <hr />
          </div>

        </div>

      </div>

      <!-- SINI -->
    </div>
    <div class="container-fluid" style="margin-top: 10px;">
      <div class="row">

        <div class="col">
          <div class="form-group">
            <label>Disc %</label>
            <input type="number" class="form-control text-right" id="input_add_disc" onblur="onChangeInputAddDisc()" value="0.00">
          </div>
        </div>

        <div class="col">
          <div class="form-group">
            <label>DiscRp</label>
            <input type="number" class="form-control text-right" id="input_add_discrp" onblur="onChangeInputAddDiscRp()" value="0.00">
          </div>
        </div>

        <div class="col">
          <div class="form-group">
            <label>DPP</label>
            <input type="text" class="form-control text-right" id="input_add_dpp" value="0.00" disabled>
          </div>
        </div>

        <div class="col">
          <div class="form-group">
            <label>PPN</label>
            <input type="text" class="form-control text-right" id="input_add_ppn" value="0.00" disabled>
          </div>
        </div>

        <div class="col">
          <div class="form-group">
            <label>GrandTotal</label>
            <input type="text" class="form-control text-right" id="input_add_grandtotal" value="0.00" disabled>
          </div>
        </div>

      </div>

    </div>
    <!-- QTY, dan Satuan Produk -->

    <!-- Tambahan -->

    <!-- Satuan, Harga -->

  </div>
  <!-- <div class="col-md-12"> -->

</div>

<!-- page3 -->

<div id="page3" class="container-fluid" style="display: none" >
      <div class="row d-flex justify-content-between align-items-center">
        <div class="col-auto text-left">
          <h2>Detail SO</h2>
        </div>
        <div class="col-auto text-right">
        <button id="btnOtorisasiDetail" type="button" class="btn btn-primary btn-lg" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            Otorisasi
          </button>
          <button type="button" class="btn btn-danger btn-lg" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="buttonCloseForm()">
            Close
          </button>
        </div>
      </div>


          <div id="" class="">
            <div class="modal-body" >

          <!-- <div class="container-fluid"> -->
            <div class="row">

              <input type="hidden" class="form-control" id="input_detail_nourut" >
              <div class="col-md-3">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>No Bukti</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control text-left" id="input_detail_nobukti" placeholder="" disabled>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Tanggal</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="date" class="form-control text-left" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Pelanggan</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="input-group form-group">
                      <input type="text" class="form-control text-left" id="input_detail_kodepelanggan" disabled>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Nama Pelanggan</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control text-left" id="input_detail_namapelanggan"  disabled>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Alamat Pelanggan</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <textarea  style="width: 100%; resize: none" rows=3  class="form-control text-left" id="input_detail_alamatpelanggan" disabled></textarea>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Valas</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="input-group form-group">
                      <input type="text" class="form-control text-center" id="input_detail_valas"  disabled>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Kurs</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control text-right" id="input_detail_kurs"  disabled>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>TOP</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="number" class="form-control text-right" id="input_detail_hari" disabled value=0 min=0 >
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Pembayaran</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <select  id="input_detail_pembayaran" disabled class="form-control text-left form-select-lg mb-3" aria-label=".form-select-lg example">
                        <option value=0 selected >Tunai/CBD</option>
                        <option value=1  >Kredit</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>TGL KIRIM</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="date" class="form-control text-left" id="input_detail_tanggalkirim" value="{!! date('Y-m-d') !!}" disabled>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>PPN</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <select  id="input_detail_tipeppn" class="form-control text-left form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                        <option value=0 selected>None</option>
                        <option value=1 >Exclude</option>
                        <option value=2 >Include</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>



              </div>















            <!-- </div> -->
            <!-- <hr/> -->
            <!-- <div class="row ">
              <div class="col-md-12 text-left">
                <div class="row">
                  <div class="col-md-12">

                  </div>
                </div>
              <button type="button" class="btn btn-primary" onclick="buttonAddMainHeader()" class="btn btn-secondary"  >Header</button>
              <button type="button" class="btn btn-primary" onclick="buttonAddMainItems()" class="btn btn-secondary"  >Items</button>
          </div>
          </div> -->
          <hr/>
          <div class="row ">
          <div class="col-md-12 mt-2 text-left">
            <button type="button" class="btn btn-primary btn-lg" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="buttonShowHideHeaderDetail()" class="btn btn-secondary"><b>Show Hide Header</b></button>
          </div>
          </div>
            <div class="mt-4" id="modalBodyDetailMainHeader">

            <div class="row">
              <div class="col-md-3">
                <div class="row">
                  <div class="col-9">
                    <div class="form-group">
                      <label>Alamat Kirim</label>
                    </div>
                  </div>
                  <div class="col-3 text-right">
                    <div class="form-group">
                  <!-- <button onclick="buttonAddListAlamatKirim()" id="buttonAddListAlamatKirim"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <input type="hidden" class="form-control" id="input_detail_kodealamatkirim" >
                    <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatkirim"  disabled></textarea>
                  </div>
                </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="row">
                  <div class="col-9">
                    <div class="form-group">
                      <label>Lokasi Penerima</label>
                    </div>
                  </div>
                  <div class="col-3 text-right">
                    <div class="form-group">
                  <!-- <button onclick="buttonAddListLokasiPenerima()" id="buttonAddListLokasiPenerima"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <input type="hidden" class="form-control" id="input_detail_kodelokasipenerima" >
                    <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control text-left" id="input_detail_alamatlokasipenerima"  disabled></textarea>
                  </div>
                </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="row">
                  <div class="col-md-12">
                    <label>Keterangan</label>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group" style="margin-top: 14px">
                      <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control text-left" id="input_detail_catatan" disabled></textarea>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>No PO</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input  type="text" class="form-control text-left" id="input_detail_nopo"  disabled>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>DP</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="number" class="form-control text-right" id="input_detail_dp" value='0.00' disabled>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Tgl PO</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="date" class="form-control text-left" id="input_detail_tanggalpo" value="{!! date('Y-m-d') !!}" disabled>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-3">
                <div class="row">
                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-9">
                        <div class="form-group">
                          <label>PIC</label>
                        </div>
                      </div>
                      <div class="col-3 text-right">
                        <div class="form-group">
                      <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-group form-group">
                      <input type="hidden" class="form-control" id="input_detail_kodepic"  >
                      <input type="text" class="form-control" id="input_detail_namapic"  disabled>
                      <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="row">
                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-9">
                        <div class="form-group">
                          <label>Back Office</label>
                        </div>
                      </div>
                      <div class="col-3 text-right">
                        <div class="form-group">
                      <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-group form-group">
                      <input type="hidden" class="form-control" id="input_detail_kodebackoffice" >
                      <input type="text" class="form-control" id="input_detail_namabackoffice"  disabled>
                      <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="row">
                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-9">
                        <div class="form-group">
                          <label>Sales</label>
                        </div>
                      </div>
                      <div class="col-3 text-right">
                        <div class="form-group">
                      <!-- <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-group form-group">
                      <input type="hidden" class="form-control" id="input_detail_kodesales" >
                      <input type="text" class="form-control" id="input_detail_namasales"  disabled>
                      <!-- <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Draft PO</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <select  id="input_detail_draftpo" class="form-control text-left form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                        <option value=0 selected>Tidak</option>
                        <option value=1 >Ya</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <hr/>

          </div>

          </div>
          <div class=" container-fluid" id="" style="margin-top:-40px;">

            <!-- sinia -->

          <!-- END ADD EDIT -->

          <div class="container-fluid mt-4" style="overflow:auto;">
            <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
            <div class="row" style="overflow:auto;">
              <table id="tabel_detail" class="table table-bordered table-hover table-striped table-responsive-lg">
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Alias</th>
                    <th style="padding: 4px 12px;" scope="col">Merk</th>
                    <th style="padding: 4px 12px;" scope="col">Qty</th>
                    <th style="padding: 4px 12px;" scope="col">Sat</th>
                    <th style="padding: 4px 12px;" scope="col">Tax</th>
                    <th style="padding: 4px 12px;" scope="col">Harga</th>
                    <th style="padding: 4px 12px;" scope="col">Diskon</th>
                    <th style="padding: 4px 12px;" scope="col">NDPP</th>
                    <th style="padding: 4px 12px;" scope="col">No SPK</th>
                    <!-- <th scope="col">Actions</th> -->

                  </tr>
                </thead>

                <tbody id="tabel_data_detail" class="text-left" >

                  <tr >

                    <td></td>
                    <td></td>

                      <td class="text-center">
                        <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                        <button class="btn btn-success btn-sm" type="button" ><i class="bi bi-pen"></i></button>
                        <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
                        <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-list"></i></button>
                      </td>
                </tr>
                </tbody>

              </table>
            </div>
              <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>

          <div class="row ">
          <div class="col-md-12 mt-2 text-right">
          <!-- <button type="button" class="btn btn-primary" onclick="buttonAddAddItem()" class="btn btn-secondary"  ><b>+ Tambah Item</b></button> -->
          </div>
          </div>

          <hr/>
          </div>

          <div class="container-fluid" style="margin-top: -10px;">
          <div class="row" >

          <div class="col">
            <div class="form-group">
              <label>Disc %</label>
              <input type="number" class="form-control text-right" id="input_detail_disc" disabled value ="0.00" >
            </div>
          </div>

          <div class="col">
            <div class="form-group">
              <label>DiscRp</label>
              <input type="number" class="form-control text-right" id="input_detail_discrp" value ="0.00" disabled>
            </div>
          </div>

          <div class="col">
            <div class="form-group">
              <label>DPP</label>
              <input type="text" class="form-control text-right" id="input_detail_dpp" value ="0.00" disabled>
            </div>
          </div>

          <div class="col">
            <div class="form-group">
              <label>PPN</label>
              <input type="text" class="form-control text-right" id="input_detail_ppn" value ="0.00" disabled>
            </div>
          </div>

          <div class="col">
            <div class="form-group">
              <label>GrandTotal</label>
              <input type="text" class="form-control text-right" id="input_detail_grandtotal" value ="0.00" disabled>
            </div>
          </div>

          </div>

          </div>
          </div>
</div>


<!-- page3 end input_add -->

<!-- page 4-->
<div id="page4" class="container-fluid" style="display: none" >
      <div class="row">
        <div class="col-6 text-left">

            <h2 style="margin-top: -80px;">Tambah SO</h2>
        </div>
        <div class="col-6 text-right">
        <!-- <button id="btnOtorisasiDetail" type="button" class="btn btn-primary btn-lg" style="
            height: 30px;
            margin-top: -80px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            Otorisasi
          </button> -->
          <button type="button" class="btn btn-danger btn-lg" style="
              height: 30px;
              margin-top: -80px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="buttonCloseForm()">
            Close
          </button>
        </div>
      </div>


          <div id="" class="">
            <div class="modal-body" >

          <!-- <div class="container-fluid"> -->
            <div class="row">

              <input type="hidden" class="form-control" id="input_detail_nourut" >
              <div class="col-md-6">

                <div class="row">


                <!-- <div class="col-md-12">
                  <div class="form-group">
                    <label></label>
                  </div>
                </div> -->
                <div class="col-md-4" style="margin-top:-40px;">
                  <div class="form-group">
                    <label>Kode Cust</label>
                  </div>
                </div>
                <div class="col-md-2" style="margin-top:-40px;">
                  <div class="input-group mb-3 position-relative">
                    <input
                    type="text"
                    class="form-control text-left"
                    placeholder="Cari Pelanggan..."
                    id="input_tambahso_kodepelanggan"
                    onkeyup="searchPelangganTambahSO(this.value)"
                    autocomplete="off">
                    <div id="dropdown_pelanggantambahso"
                        class="dropdown-menu w-100">
                    </div>
                  </div>
                </div>
                <div class="col-md-6" style="margin-top:-40px;">
                  <div class="form-group">
                    <input type="text" class="form-control text-left" id="input_tambahso_namapelanggan" placeholder="" disabled>
                    <input type="hidden" class="form-control text-left" id="input_tambahso_ppn" placeholder="" disabled>
                  </div>
                </div>

                <!-- <div class="col-md-12">
                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div> -->
                <div class="col-md-4" style="margin-top:-12px;">
                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-md-8 text-center" style="margin-top:-12px;">
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="input_tambahso_tanggal" value="{!! date('Y-m-d') !!}" >
                  </div>
                </div>


                <div class="col-md-4" style="margin-top:-10px;">
                  <div class="form-group">
                    <label>No PO</label>
                  </div>
                </div>


              <div class="col-md-8" style="margin-top:-10px;">
                <div class="input-group form-group">
                  <input type="text" class="form-control text-left" id="input_tambahso_nopo" onkeyup="searchNoPOTambahSO(this.value)" >
                  <input type="hidden" class="form-control text-left" id="input_tambahso_idpo"  >
                </div>
                <div id="dropdown_nopotambahso" class="dropdown-menu" style="width:100%"></div>
              </div>

                </div>

              </div>

              </div>

            <!-- </div> -->
            <!-- <hr/> -->
            <!-- <div class="row ">
              <div class="col-md-12 text-left">
                <div class="row">
                  <div class="col-md-12">

                  </div>
                </div>
              <button type="button" class="btn btn-primary" onclick="buttonAddMainHeader()" class="btn btn-secondary"  >Header</button>
              <button type="button" class="btn btn-primary" onclick="buttonAddMainItems()" class="btn btn-secondary"  >Items</button>
          </div>
          </div> -->
          <hr/>


          </div>
          <div class=" container-fluid" id="" style="margin-top:-40px;">

            <!-- sinia -->

          <!-- END ADD EDIT -->

            <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
            <div class="row" style="overflow:auto;">
              <table id="tabel_tambahso" class="table table-bordered table-hover table-striped table-responsive-lg">
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px; " class="text-center" scope="col">v</th>
                    <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                    <th style="padding: 4px 12px;" scope="col">Qty</th>
                    <th style="padding: 4px 12px;" scope="col">Satuan</th>
                    <!-- <th scope="col">Actions</th> -->

                  </tr>
                </thead>

                <tbody id="tabel_data_tambahso" class="text-left" >

                  <tr >
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>

              </table>
            </div>
              <!-- <button onclick="buttonSubKategori()">tes</button> -->


          <div class="row ">
          <div class="col-md-12 mt-2 text-right">
            <button type="button" id="submitAddTambahSO" class="btn btn-primary btn-lg" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="submitAddTambahSO()" class="btn btn-secondary">Submit Add</button>
          </div>
          </div>

          <hr/>
          </div>


          </div>
</div>
<!-- end page 4-->

<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Add</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>


        <!-- <h1>Tes Modal</h1> -->

        <div id="modalBodyAddListPelanggan" class="showhidemodalbodyadd">
          <div class="modal-body" >

          <div class="container-fluid mt-4" >
            <div class="row">
              <div class="col-md-4" style="margin-top:-40px;">
                <h3>Pelanggan</h3>
              </div>
            </div>
            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
            <div class="row">
              <div class="col-12" style="overflow:auto; margin-top:-30px;">
              <!-- <div class="container-fluid"> -->
              <table id="tabel_add_list_pelanggan" class="table table-bordered table-hover table-striped table-responsive-lg">
                <thead class="text-center bg-primary text-white">
                  <tr>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                    <th style="padding: 4px 12px;" scope="col">Kode</th>
                    <th style="padding: 4px 12px;" scope="col">Nama</th>
                    <th style="padding: 4px 12px;" scope="col">Alamat</th>
                    <th style="padding: 4px 12px;" scope="col">PKP</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_pelanggan" class="text-left" >
                  <tr >
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                      <td class="text-center">
                        <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                        <button class="btn btn-primary btn-sm" style="padding-top:10px;" type="button" ><i class="bi bi-plus"></i></button>
                      </td>
                </tr>
                </tbody>
              </table>
            <!-- </div> -->
              <!-- <button onclick="buttonSubKategori()">tes</button> -->
            </div>
              </div>
              </div>
        </div>
        <div class="modal-footer">
          <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
          <button type="button" class="btn btn-danger btn-lg"
          style="
          margin-top:-10px;
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonAddListBatal()">Batal</button>
        </div>
      </div>

      <div id="modalBodyAddListNoPenyerahan" class="showhidemodalbodyadd">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-md-4" style="margin-top:-40px;">
              <h3>No Penyerahan</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-30px;">
            <!-- <div class="container-fluid"> -->
            <table id="tabel_add_list_nopenyerahan" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">No Sample</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_nopenyerahan" class="text-left" >
                <tr class="pick-row">
                  <td>-</td>
                    <td>-</td>
              </tr>
              </tbody>
            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        <button type="button" class="btn btn-danger btn-lg"
        style="
        margin-top:-10px;
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonAddListBatal()">Batal</button>
      </div>
    </div>

    <div id="modalBodyAddListRefPR" class="showhidemodalbodyadd">
      <div class="modal-body" >

      <div class="container-fluid mt-4" >
        <div class="row">
          <div class="col-md-4" style="margin-top:-40px;">
            <h3>Ref PR</h3>
          </div>
        </div>
        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
        <div class="row">
          <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <!-- <div class="container-fluid"> -->
          <table id="tabel_add_list_refpr" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead>
              <tr>
                <th style="padding: 4px 12px;" scope="col">Nobukti</th>
                  <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_refpr" class="text-left" >
              <tr class="pick-row">
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            </tbody>
          </table>
        <!-- </div> -->
          <!-- <button onclick="buttonSubKategori()">tes</button> -->
        </div>
          </div>
          </div>
    </div>
    <div class="modal-footer">
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
      <button type="button" class="btn btn-danger btn-lg"
      style="
      margin-top:-10px;
      height: 30px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonAddListBatal()">Batal</button>
    </div>
  </div>

  <div id="modalBodyAddListBarangRefPR" class="showhidemodalbodyadd">
    <div class="modal-body" >

    <div class="container-fluid mt-4" >
      <div class="row">
        <div class="col-md-4" style="margin-top:-40px;">
          <h3>Barang</h3>
        </div>
      </div>
      <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
      <div class="row">
        <div class="col-12" style="overflow:auto; margin-top:-30px;">
        <!-- <div class="container-fluid"> -->
        <table id="tabel_add_list_barangrefpr" class="table table-bordered table-hover table-striped table-responsive-lg">
          <thead class="text-center bg-primary text-white">
            <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
              <th style="padding: 4px 12px;" scope="col">Nobukti</th>
              <th style="padding: 4px 12px;" scope="col">Tanggal</th>
              <th style="padding: 4px 12px;" scope="col">Ref Pr</th>
              <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
              <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
              <th style="padding: 4px 12px;" scope="col">Merk</th>
              <th style="padding: 4px 12px;" scope="col">Sisa</th>
              <th style="padding: 4px 12px;" scope="col">Satuan</th>
            </tr>
          </thead>
          <tbody id="tabel_data_add_list_barangrefpr" class="text-left" >
            <tr >
              <td>-</td>
              <td>-</td>
              <td>-</td>
              <td>-</td><td>-</td>
              <td>-</td>
              <td>-</td>
              <td>-</td>
                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button class="btn btn-primary btn-sm" style="padding-top:10px;" type="button" ><i class="bi bi-plus"></i></button>
                </td>
          </tr>
          </tbody>
        </table>
      <!-- </div> -->
        <!-- <button onclick="buttonSubKategori()">tes</button> -->
      </div>
        </div>
        </div>
  </div>
  <div class="modal-footer">
    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    <button type="button" class="btn btn-danger btn-lg"
    style="
    margin-top:-10px;
    height: 30px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    transition: background-color 0.3s, box-shadow 0.3s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
    onclick="buttonAddListBatal()">Batal</button>
  </div>
</div>

      <div id="modalBodyAddListNoPo" class="showhidemodalbodyadd">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-md-4" style="margin-top:-40px;">
              <h3>No PO</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-30px;">
            <!-- <div class="container-fluid"> -->
            <table id="tabel_add_list_nopo" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead class="text-center bg-primary text-white">
                <tr>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
                  <th style="padding: 4px 12px;" scope="col">ID</th>
                  <th style="padding: 4px 12px;" scope="col">Cust</th>
                  <th style="padding: 4px 12px;" scope="col">No Pesanan</th>
                  <th style="padding: 4px 12px;" scope="col">Tgl Create</th>
                  <th style="padding: 4px 12px;" scope="col">Tgl Input</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_nopo" class="text-left" >
                <tr >
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                    <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-primary btn-sm" style="padding-top:10px;" type="button" ><i class="bi bi-plus"></i></button>
                    </td>
              </tr>
              </tbody>
            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        <button type="button" class="btn btn-danger btn-lg"
        style="
        margin-top:-10px;
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonAddListBatal()">Batal</button>
      </div>
    </div>

      <div id="modalBodyAddAddListBarangAll" class="showhidemodalbodyadd">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <div class="row">
            <div id="modalBodyAddAddListBarangAllTitle" class="col-md-9" style="margin-top:-30px;">
              <h3>Barang</h3>
            </div>
            <div class="col-3 text-right form-group">
              <input id="input_search_barang_all" style="margin-top:-30px;" type="text" name="" value="" class="form-control" onkeypress="searchBarangAll(event)">
              <label for="input_search_barang_all" style="margin-top:-20px;" class="search-label" >SEARCH:</label>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-30px;">
            <!-- <div class="container-fluid"> -->
            <table id="tabel_add_list_barangall" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead class="text-center bg-primary text-white">
                <tr>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Merk</th>
                  <th style="padding: 4px 12px;" scope="col">Satuan</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_barangall" class="text-left" >
                @for ($i = 0; $i < count($listBarangAll); $i++)
                <tr >
                <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangAll('{{ $listBarangAll[$i]->Kodebrg }}')" type="button" ><i class="bi bi-plus"></i></button></td>
                  <td>{{ $listBarangAll[$i]->Kodebrg }}</td>
                  <td>{{ $listBarangAll[$i]->NamaBrg }}</td>
                  <td>{{ $listBarangAll[$i]->namamerk }}</td>
                  <td>{{ $listBarangAll[$i]->Sat1 }}</td>

              </tr>
              @endfor
              </tbody>
            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        <button type="button" class="btn btn-danger btn-lg"
        style="
        margin-top:-10px;
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonAddListBatal()">Batal</button>
      </div>
    </div>

    <div id="modalBodyAddAddListSattax" class="showhidemodalbodyadd">
      <div class="modal-body" >

      <div class="container-fluid mt-4" >
        <div class="row">
          <div id="modalBodyAddAddListSattaxTitle" class="col-md-12" style="margin-top:-20px;">
            <h3>Sattax</h3>
          </div>

        </div>
        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
        <div class="row">
          <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <!-- <div class="container-fluid"> -->
          <table id="tabel_add_list_sattax" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead>
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_sattax" class="text-left" >
              @for ($i = 0; $i < count($listSattax); $i++)

              <tr class="pick-row" onclick="buttonAddPickSattax('{{ $listSattax[$i]->KODETAX }}','{{ $listSattax[$i]->NAMATAX }}')">
                <td>{{ $listSattax[$i]->KODETAX }}</td>
                <td>{{ $listSattax[$i]->NAMATAX }}</td>
            </tr>
            @endfor
            </tbody>
          </table>
        <!-- </div> -->
          <!-- <button onclick="buttonSubKategori()">tes</button> -->
        </div>
          </div>
          </div>
    </div>
    <div class="modal-footer">
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
      <button type="button" class="btn btn-danger btn-lg"
      style="
      margin-top:-10px;
      height: 30px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonAddListBatal()">Batal</button>
    </div>
  </div>

      <div id="modalBodyAddAddListBarang" class="showhidemodalbodyadd">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Barang</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;">
            <!-- <div class="container-fluid"> -->
            <table id="tabel_add_list_barang" class="table table-bordered table-striped"  >
              <thead class="text-center">
                <tr>
                <th scope="col">Actions</th>
                  <th scope="col">Kode</th>
                  <th scope="col">Nama</th>
                  <th scope="col">Merk</th>
                  <th scope="col">Satuan</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_barang" class="text-left" >
                <tr >
                <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                    </td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>

              </tr>
              </tbody>
            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()">Batal</button>
      </div>
    </div>

      <div id="modalBodyAddListPIC" class="showhidemodalbodyadd">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-md-4" style="margin-top:-40px;">
              <h3>PIC</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-30px;">
            <!-- <div class="container-fluid"> -->
            <table id="tabel_add_list_pic" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead>
                <tr>

                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_pic" class="text-left" >
                <tr class="pick-row">
                  <td>-</td>
                  <td>-</td>
              </tr>
              </tbody>
            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        <button type="button" class="btn btn-danger btn-lg"
        style="
        margin-top:-10px;
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonAddListBatal()">Batal</button>
      </div>
    </div>

    <div id="modalBodyAddListLokasiPenerima" class="showhidemodalbodyadd">
      <div class="modal-body" >

      <div class="container-fluid mt-4" >
        <div class="row">
          <div class="col-md-4" style="margin-top:-40px;">
            <h3>Lokasi Penerima</h3>
          </div>
        </div>
        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
        <div class="row">
          <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <!-- <div class="container-fluid"> -->
          <table id="tabel_add_list_lokasipenerima" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead>
              <tr>

                <th style="padding: 4px 12px;" scope="col">Kode</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_lokasipenerima" class="text-left" >
              <tr class="pick-row">
                <td>-</td>
                <td>-</td>
            </tr>
            </tbody>
          </table>
        <!-- </div> -->
          <!-- <button onclick="buttonSubKategori()">tes</button> -->
        </div>
          </div>
          </div>
    </div>
    <div class="modal-footer">
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
      <button type="button" class="btn btn-danger btn-lg"
      style="
      margin-top:-10px;
      height: 30px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonAddListBatal()">Batal</button>
    </div>
  </div>

      <div id="modalBodyAddListAlamatKirim" class="showhidemodalbodyadd">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-md-4" style="margin-top:-40px;">
              <h3>Alamat Kirim</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-30px;">
            <!-- <div class="container-fluid"> -->
            <table id="tabel_add_list_alamatkirim" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead>
                <tr>

                  <th style="padding: 4px 12px;" scope="col">Nomor</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Alamat</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_alamatkirim" class="text-left" >
                <tr class="pick-row">
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
              </tr>
              </tbody>
            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        <button type="button" class="btn btn-danger btn-lg"
        style="
        margin-top:-10px;
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonAddListBatal()">Batal</button>
      </div>
    </div>

    <div id="modalBodyAddListBackOffice" class="showhidemodalbodyadd">
      <div class="modal-body" >

      <div class="container-fluid mt-4" >
        <div class="row">
          <div class="col-md-4" style="margin-top:-40px;">
            <h3>Back Office</h3>
          </div>
        </div>
        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
        <div class="row">
          <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <!-- <div class="container-fluid"> -->
          <table id="tabel_add_list_backoffice" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center bg-primary text-white">
              <tr>
              <th style="padding: 4px 12px;" scope="col">Actions</th>
                <th style="padding: 4px 12px;" scope="col">Kode</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_backoffice" class="text-left" >
              <tr >
                <td>-</td>
                <td>-</td>
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                  </td>
            </tr>
            </tbody>
          </table>
        <!-- </div> -->
          <!-- <button onclick="buttonSubKategori()">tes</button> -->
        </div>
          </div>
          </div>
    </div>
    <div class="modal-footer">
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
      <button type="button" class="btn btn-danger btn-lg"
      style="
      margin-top:-10px;
      height: 30px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonAddListBatal()">Batal</button>
    </div>
  </div>

  <div id="modalBodyAddListSales" class="showhidemodalbodyadd">
    <div class="modal-body" >

    <div class="container-fluid mt-4" >
      <div class="row">
        <div class="col-md-4" style="margin-top:-40px;">
          <h3>Sales</h3>
        </div>
      </div>
      <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
      <div class="row">
        <div class="col-12" style="overflow:auto; margin-top:-30px;">
        <!-- <div class="container-fluid"> -->
        <table id="tabel_add_list_sales" class="table table-bordered table-hover table-striped table-responsive-lg">
          <thead class="text-center bg-primary text-white">
            <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
              <th style="padding: 4px 12px;" scope="col">Kode</th>
              <th style="padding: 4px 12px;" scope="col">Nama</th>
            </tr>
          </thead>
          <tbody id="tabel_data_add_list_sales" class="text-left" >
            <tr >
              <td>-</td>
              <td>-</td>
                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                </td>
          </tr>
          </tbody>
        </table>
      <!-- </div> -->
        <!-- <button onclick="buttonSubKategori()">tes</button> -->
      </div>
        </div>
        </div>
  </div>
  <div class="modal-footer">
    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    <button type="button" class="btn btn-danger btn-lg"
    style="
    margin-top:-10px;
    height: 30px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    transition: background-color 0.3s, box-shadow 0.3s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
    onclick="buttonAddListBatal()">Batal</button>
  </div>
</div>

    <div id="modalBodyAddListValas" class="showhidemodalbodyadd">
      <div class="modal-body" >

      <div class="container-fluid mt-4" >
        <div class="row">
          <div class="col-md-4" style="margin-top: -40px;">
            <h3>Valas</h3>
          </div>
        </div>
        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
        <div class="row">
          <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <!-- <div class="container-fluid"> -->
          <table id="tabel_add_list_valas" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center bg-primary text-white">
              <tr>
              <th style="padding: 4px 12px;" scope="col">Actions</th>
                <th style="padding: 4px 12px;" scope="col">Kode</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>
                <th style="padding: 4px 12px;" scope="col">Kurs</th>
              </tr>
            </thead>

            <tbody id="tabel_data_add_list_valas" class="text-left" >
              <tr >
                <td>-</td>
                <td>-</td>
                <td>-</td>
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                  </td>
            </tr>
            </tbody>
          </table>
        <!-- </div> -->
          <!-- <button onclick="buttonSubKategori()">tes</button> -->
        </div>
          </div>
          </div>
    </div>
    <div class="modal-footer">
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
      <button type="button" class="btn btn-danger btn-lg"
      style="
      margin-top:-10px;
      height: 30px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonAddListBatal()">Batal</button>
    </div>
  </div>



    <div class="container-fluid" style="margin-top: -10px;">




      <div class="row justify-content-end">




  </div>



    </div>


  </div>


</div>
</div>


<div class="modal fade"  id="formTambahSo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Tambah Penawaran</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>


        <!-- <h1>Tes Modal</h1> -->
        <div class="modal-body" >

      <!-- <div class="container-fluid"> -->
        <div class="row">

          <!-- <input type="hidden" class="form-control" id="input_detail_nourut" > -->
          <div class="col-md-6">

            <div class="row">


            <!-- <div class="col-md-12">
              <div class="form-group">
                <label></label>
              </div>
            </div> -->
            <!-- <div class="col-md-4" style="">
              <div class="form-group">
                <label>Kode Cust</label>
              </div>
            </div>
            <div class="col-md-2" style="">
              <div class="input-group mb-3 position-relative">
                <input
                type="text"
                class="form-control text-left"
                placeholder="Cari Pelanggan..."
                id="input_tambahsoall_kodepelanggan"
                onkeyup="searchPelangganTambahSOAll(this.value)"
                autocomplete="off">
                <div id="dropdown_pelanggantambahso"
                    class="dropdown-menu w-100">
                </div>
              </div>
            </div> -->
            <!-- <div class="col-md-6" style="margin-top:-40px;">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_tambahsoall_namapelanggan" placeholder="" disabled> -->
                <input type="hidden" class="form-control text-left" id="input_tambahsoall_ppn" placeholder="" disabled>
              <!-- </div>
            </div> -->

            <!-- <div class="col-md-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div> -->
            <!-- <div class="col-md-4" style="margin-top:-12px;">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-md-8 text-center" style="margin-top:-12px;">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="input_tambahsoall_tanggal" value="{!! date('Y-m-d') !!}" >
              </div>
            </div> -->


            <!-- <div class="col-md-4" style="margin-top:-10px;">
              <div class="form-group">
                <label>No PO</label>
              </div>
            </div> -->


          <!-- <div class="col-md-8" style="margin-top:-10px;">
            <div class="input-group form-group">
              <input type="text" class="form-control text-left" id="input_tambahsoall_nopo" onkeyup="searchNoPOTambahSO(this.value)" >
              <input type="hidden" class="form-control text-left" id="input_tambahsoall_idpo"  >
            </div>
            <div id="dropdown_nopotambahsoall" class="dropdown-menu" style="width:100%"></div>
          </div> -->

            </div>

          </div>

          </div>

        <!-- </div> -->
        <!-- <hr/> -->
        <!-- <div class="row ">
          <div class="col-md-12 text-left">
            <div class="row">
              <div class="col-md-12">

              </div>
            </div>
          <button type="button" class="btn btn-primary" onclick="buttonAddMainHeader()" class="btn btn-secondary"  >Header</button>
          <button type="button" class="btn btn-primary" onclick="buttonAddMainItems()" class="btn btn-secondary"  >Items</button>
      </div>
      </div> -->
      <!-- <hr/> -->

      <div class=" container-fluid" id="" style="margin-top:-40px;">

        <!-- sinia -->

      <!-- END ADD EDIT -->

      <div class="container-fluid mt-4" style="overflow:auto;">
        <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
        <div class="row" style="overflow:auto; margin-top: 10px">
          <!-- <div class="row "> -->
          <div class="col-md-12 mt-2 text-right">
            <button type="button" id="submitAddTambahSOAll" class="btn btn-primary btn-lg" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="submitAddTambahSOAll()" class="btn btn-secondary">Submit Add</button>
          </div>
          <!-- </div> -->
          <table id="tabel_tambahsoall" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px; " class="text-center" scope="col">v</th>
                <th style="padding: 4px 12px;" scope="col">No Bukti</th>
                <th style="padding: 4px 12px;" scope="col">Cust</th>
                <th style="padding: 4px 12px;" scope="col">Catatan</th>
                <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Satuan</th>
                <!-- <th scope="col">Actions</th> -->

              </tr>
            </thead>

            <tbody id="tabel_data_tambahsoall" class="text-left" >

              <tr >
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            </tbody>

          </table>
        </div>
          <!-- <button onclick="buttonSubKategori()">tes</button> -->
      </div>

      <div class="row ">
      <div class="col-md-12 mt-2 text-right">
        <button type="button" id="submitAddTambahSOAllBottom" class="btn btn-primary btn-lg" style="
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="submitAddTambahSOAll()" class="btn btn-secondary">Submit Add</button>
      </div>
      </div>

      <hr/>
      </div>


      </div>



  </div>


</div>
</div>

</div>
<!-- End modal add-->

<!-- start modal print-->
<div class="modal fade" id="modalPrint" tabindex="-1">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Pilih Design Cetak</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <button class="btn btn-primary w-100 mb-2" onclick="choosePrint('default')">
            Cetak SO
          </button>

          <!-- <button class="btn btn-primary w-100 mb-2" onclick="choosePrint('design3')">
            Invoice 3
          </button>

          <button class="btn btn-primary w-100" onclick="choosePrint('jbg')">
            Cetak JBG
          </button> -->
        </div>

      </div>
    </div>
  </div>
<!-- end modal print-->



@endsection

@section('js')
<script src="{{ asset('js/report-table.js') }}"></script>
<script src="{{ asset('js/headerEngine.js') }}?v={{ filemtime(public_path('js/headerEngine.js')) }}"></script>
<script type="text/javascript">

let dataTambahSO = []
let tempDataTableTambahSO = []
let dataTableAdd = []
let dataTableEdit = []
let selectedNoBukti = ''

let dataAddAddListItem = []

let dataRefreshOutstanding = []
let dataRefreshOutstanding2 = []

let cachePelanggan = []
let isLoadingPelanggan = false

let dataRefreshPenerimaan = []
let idpocust = 0
let listAlamatKirim = []
let listnopenyerahan = []
let listRefPR = []
let listBarangRefPR = []

let tempAddAdd = {}
let tempAddEdit = {}
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}

let tipeform = ''
let tipeformitem = ''
let tempRefPr = {}
let tempNoPenyerahan = {}
// let listBarangRefPR = []

// -- #tabel/#tabel7 interactive column engine (docs/new-slider-table-guide.md port) --
// The engine itself (gcart_header state, drag/hide/decimal/total persistence,
// multi-table activate/bind) now lives in public/js/headerEngine.js so it can
// be reused by other pages/menus later -- see that file's header comment for
// its usage contract. Persistence goes through SOController::loadHeader/
// simpanHeader (SML-backed) instead of GlobalFunctionsController's MGL-backed
// originals, since the MGL connection isn't configured anywhere in this app.
HeaderEngine.configure({
  loadUrl: "{!! url('soloadheader') !!}",
  simpanUrl: "{!! url('sosimpanheader') !!}"
});

var lastTabelRows = [];
var lastTabel7Rows = [];
var lastTabel2Rows = [];
var lastTabelOtoRows = [];

// Tabel yang tabnya sedang tidak aktif tetap punya data lama tertinggal setelah
// loadAll() kalau selalu digambar ulang tanpa syarat. Ditandai di sini, baru benar-benar
// digambar ulang saat tabnya dibuka -- lihat handler shown.bs.tab di bawah. Port 1:1 dari
// poPerluGambar milik purchaseOrder.blade.php, dibatasi ke #tabel/#tabel7 (dua tab yang
// punya tombol nav dan bisa dibuka user) -- #tabel2/#tabel_oto tidak reachable lewat UI
// sama sekali (tidak punya tombol nav), jadi selalu digambar langsung begitu datanya ada.
var soPerluGambar = { tabel: false, tabel7: false, tabel2: false, tabel_oto: false };

HeaderEngine.registerTable('tabel', {
  href: 'marketingso_tabel',
  tableSel: '#tabel',
  barSel: '#rtBarTabel',
  setDefault: function () { setDefaultHeaderTabel(); },
  onChange: function () { reinitTabel(); }
});
HeaderEngine.registerTable('tabel7', {
  href: 'marketingso_tabel7',
  tableSel: '#tabel7',
  barSel: '#rtBarTabel7',
  setDefault: function () { setDefaultHeaderTabel7(); },
  onChange: function () { reinitTabel7(); }
});

// Which tab the user is actually looking at right now -- used after
// loadAll() refreshes both tables in the background regardless of which
// one is visible, so the interactive engine ends up bound to the right one.
function activeVisibleTabKey() {
  return $('#home').hasClass('active') ? 'tabel' : 'tabel7';
}

function setDefaultHeaderTabel() {
  gcart_header = [
    ['NOBUKTI',      'No. Bukti',      1, 'varchar', 0, 0],
    ['TANGGAL',      'Tanggal',        1, 'date',    0, 0],
    ['NAMACUSTSUPP', 'Nama Pelanggan', 1, 'varchar', 0, 0],
    ['NAMASALES',    'Sales',          1, 'varchar', 0, 0],
    ['NAMAPIC',      'PIC',            1, 'varchar', 0, 0],
    ['NAMABOFFICE',  'Inside Sales',   1, 'varchar', 0, 0],
    ['nopesanan',    'PO Customer',    1, 'varchar', 0, 0],
    ['TotDPP',       'DPP',            1, 'float',   0, 0],
    ['TotPPn',       'PPN',            1, 'float',   0, 0],
    ['TotNet',       'Total',          1, 'float',   0, 0],
    ['IsOtorisasi1', 'IsOto',          1, 'bool',    0, 0],
    ['OtoUser1',     'UserOto',        1, 'varchar', 0, 0],
    ['TglOto1',      'TglOto',         1, 'date',    0, 0],
    ['userunblock',  'User Open CBD',  1, 'varchar', 0, 0],
    ['tglunblock',   'Tgl Open CBD',   1, 'date',    0, 0],
  ];
}

function setDefaultHeaderTabel7() {
  gcart_header = [
    ['NOBUKTI',      'No Bukti',    1, 'varchar', 0, 0],
    ['TANGGAL',      'Tanggal',     1, 'date',    0, 0],
    ['NAMACUSTSUPP', 'Nama Cust',   1, 'varchar', 0, 0],
    ['KODEBRG',      'Kode Brg',    1, 'varchar', 0, 0],
    ['NAMABRG',      'Nama Barang', 1, 'varchar', 0, 0],
    ['QNT',          'Qty',         1, 'float',   0, 2],
    ['QntSO',        'Qty SO',      1, 'float',   0, 2],
    ['Sisa',         'Sisa',        1, 'float',   0, 2],
  ];
}

function tabelActionsCell(row) {
  var nobukti = HeaderEngine.pickCI(row, 'NOBUKTI');
  var html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn-action-sm btn-action-warning" type="button" title="Details" onclick="buttonDetail(\'' + nobukti + '\')"><i class="bi bi-info"></i></button>';
  if (Number(HeaderEngine.pickCI(row, 'IsOtorisasi1')) == 0) {
    html += '<button class="btn-action-sm btn-action-primary" type="button" onclick="buttonOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>';
    html += '<button class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit(\'' + nobukti + '\')"><i class="bi bi-pen"></i></button>';
  } else {
    html += '<button class="btn-action-sm btn-action-danger" type="button" onclick="buttonBatalOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>';
    html += '<button class="btn-action-sm btn-action-primary" title="Print" onclick="submitPrint(\'' + nobukti + '\')"><i class="bi bi-printer"></i></button>';
  }
  if (Number(HeaderEngine.pickCI(row, 'cbdneedopen')) == 1) {
    html += '<button class="btn-action-sm btn-action-success" title="Open CBD" onclick="lockCBD(\'' + nobukti + '\')"><i class="bi bi-check-square-fill"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function tabel7ActionsCell(row) {
  var nobukti = HeaderEngine.pickCI(row, 'NOBUKTI');
  var ppn = HeaderEngine.pickCI(row, 'PPN');
  var html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn-action-sm btn-action-primary" type="button" title="Buat SO" onclick="buttonTambahSO(\'' + nobukti + '\', ' + ppn + ')"><i class="bi bi-plus"></i></button>';
  html += '</div></td>';
  return html;
}

// col[5] (decimals) is user-editable via the gear menu's stepper, so float
// formatting has to read it live rather than assume a fixed precision --
// #tabel's money columns default to 0dp, #tabel7's qty columns to 2dp.
function tabelValueCell(row, col) {
  var raw = HeaderEngine.pickCI(row, col[0]);
  var type = col[3];

  if (type === 'date') {
    if (!raw) { return '<td></td>'; }
    var d = new Date(raw);
    var dd = ('0' + d.getDate()).slice(-2);
    var mm = ('0' + (d.getMonth() + 1)).slice(-2);
    return '<td>' + dd + '/' + mm + '/' + d.getFullYear() + '</td>';
  }
  if (type === 'float') {
    // Matches the Indonesian dot-thousands convention already used
    // elsewhere on this page (buttonFilterSO's .toLocaleString('id-ID')).
    var dp = Number(col[5]) || 0;
    var n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
    return '<td style="text-align:right;">' + n.toLocaleString('id-ID', { minimumFractionDigits: dp, maximumFractionDigits: dp }) + '</td>';
  }
  if (type === 'bool') {
    return Number(raw)
      ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
      : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

// rows: array of [rowObj] (same shape as $tempOutstanding1[$i][0] / item[0]
// in the existing ajax handlers). Builds <tr>s from the current gcart_header
// order/visibility so drag-reorder and hide actually change what's rendered.
// rows: array of [rowObj] (same shape as $tempOutstanding1[$i][0] / item[0]
// in the existing ajax handlers). Builds <tr>s from the current gcart_header
// order/visibility so drag-reorder and hide actually change what's rendered.
// ReportTable.init()'s bindHead(thead) attaches drag/gear listeners via
// addEventListener with no matching removeEventListener anywhere -- calling
// init() again (which reinitTabel()/reinitTabel7() do, on purpose, to
// re-bind after DataTables rebuilds) is only safe if it's binding to a
// GENUINELY NEW <thead> node each time. Mutating thead.innerHTML preserves
// the node's own identity, so repeated init() calls kept stacking a fresh
// set of listeners onto the same persistent thead -- one header change and
// the gear button would fire its click handler twice, opening the menu and
// immediately closing it again in the same click (toggle logic run twice).
// Replacing the whole <thead> element (not just its contents) means old
// listeners are discarded along with the old node instead of accumulating.
function replaceTheadWithHeader(tableSel, cols) {
  var oldThead = document.querySelector(tableSel + ' thead');
  if (!oldThead || !window.ReportTable) { return; }
  // ReportTable.headHtml() only knows about gcart_header entries, so it
  // never accounts for the fixed Actions column every body row starts
  // with. Splice a plain, non-draggable Actions <th> in as the first cell
  // so thead/tbody column counts actually match -- a mismatch here is
  // exactly what was breaking DataTables' destroy/reinit.
  var headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  var newThead = document.createElement('thead');
  newThead.setAttribute('style', 'white-space:nowrap;');
  newThead.innerHTML = headRowHtml;
  oldThead.parentNode.replaceChild(newThead, oldThead);
}

function renderTabelRows(rows) {
  if (HeaderEngine.activeKey() !== 'tabel') { HeaderEngine.activateEngineData('tabel'); }
  var cols = gcart_header.filter(function (c) { return c[2] === 1; }); // same refs -- never .map()
  var html = "";
  (rows || []).forEach(function (rowWrap) {
    var row = rowWrap[0];
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) {
      html += tabelValueCell(row, col);
    });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  replaceTheadWithHeader('#tabel', cols);
}

function renderTabel7Rows(rows) {
  if (HeaderEngine.activeKey() !== 'tabel7') { HeaderEngine.activateEngineData('tabel7'); }
  var cols = gcart_header.filter(function (c) { return c[2] === 1; }); // same refs -- never .map()
  var html = "";
  (rows || []).forEach(function (rowWrap) {
    var row = rowWrap[0];
    html += '<tr>' + tabel7ActionsCell(row);
    cols.forEach(function (col) {
      html += tabelValueCell(row, col);
    });
    html += '</tr>';
  });
  document.getElementById('tabel7_data').innerHTML = html;
  replaceTheadWithHeader('#tabel7', cols);
}

// Kotak scroll tabel dibuat setinggi sisa ruang di #content supaya halaman TIDAK perlu
// scrollbar sendiri - yang discroll hanya isi tabel. Diukur dari DOM, bukan angka mati
// seperti 65vh, karena tinggi bagian di atas/bawah kotak (kartu tab, toolbar, bar reset
// kolom, catatan kaki) berbeda antar tab dan bisa berubah.
// Port 1:1 dari poAturTinggiTabel() milik purchaseOrder.blade.php.
function soAturTinggiTabel() {
  let area = document.getElementById('content')
  let pane = document.querySelector('#myTabContent .tab-pane.active')
  if (!area || !pane) { return }
  let wrap = pane.querySelector('.po-table-wrap')
  if (!wrap) { return }

  wrap.style.maxHeight = 'none'

  let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0
  let batasBawah = area.getBoundingClientRect().bottom - padBawah
  let kotak = wrap.getBoundingClientRect()
  let bawah = pane.getBoundingClientRect().bottom - kotak.bottom

  let sisa = batasBawah - kotak.top - bawah - 4
  wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px'
}

// #tabel/#tabel7 dom string disamakan 1:1 dengan purchaseOrder.blade.php: 'po-table-wrap't
// membungkus HANYA isi tabelnya dalam kotak scroll (lihat .po-table-wrap di so-table-header.css),
// sedangkan info+pagination ('i'/'p') ada DI LUAR kotak itu supaya tidak ikut tergulung -
// menggantikan moveDataTablePagination()/.tb-pagination-outside yang dipakai sebelumnya untuk
// menyelesaikan masalah yang sama.
const SO_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel() {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({
      dom: SO_DOM_STRING,
      lengthChange: false,
      paging: true,
      order: [[1, 'asc']],
      // The custom drag/gear header now owns column interaction entirely --
      // DataTables' own native sort-arrow indicator was rendering on top of
      // it (specifically on whichever column "order" points at), looking
      // like a doubled icon. ordering:false drops that native UI/classes
      // completely while still honoring "order" for the initial sort.
      ordering: false,
      drawCallback: function () { setTimeout(soAturTinggiTabel, 0); }
    });

    // DataTables' destroy()/rebuild cycle above detaches/replaces the DOM
    // ReportTable was bound to, so re-bind it to the fresh table/thead/bar
    // every time this runs -- not just once -- or a drag/gear action after
    // any refresh throws trying to walk up from a now-detached node (the
    // "reading 'parentNode'" crash).
    HeaderEngine.bindEngineDom('tabel');
    soPerluGambar.tabel = false;
    soAturTinggiTabel();
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function reinitTabel7() {
  try {
    if ($.fn.DataTable.isDataTable('#tabel7')) { $('#tabel7').DataTable().destroy(); }
    renderTabel7Rows(lastTabel7Rows);
    $('#tabel7').DataTable({
      dom: SO_DOM_STRING,
      lengthChange: false,
      paging: true,
      order: [[1, 'asc']],
      ordering: false,
      drawCallback: function () { setTimeout(soAturTinggiTabel, 0); }
    });
    HeaderEngine.bindEngineDom('tabel7');
    soPerluGambar.tabel7 = false;
    soAturTinggiTabel();
  } catch (e) {
    console.error('reinitTabel7 failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

// #tabel2/#tabel_oto: tabel biasa (tanpa drag/gear kolom), sama seperti tab "Purchase
// Order"/"PO Otorisasi" milik purchaseOrder.blade.php -- lihat renderTabelPO() di sana.
// Barisnya JS-owned (lastTabel2Rows/lastTabelOtoRows) supaya kode yang sama melayani
// pemuatan awal halaman maupun refresh lewat loadAll().
function so2FormatTanggal(raw) {
  if (!raw) { return '' }
  let d = new Date(raw)
  if (isNaN(d.getTime())) { return raw }
  let dd = ('0' + d.getDate()).slice(-2)
  let mm = ('0' + (d.getMonth() + 1)).slice(-2)
  return dd + '/' + mm + '/' + d.getFullYear()
}

function so2FormatTanggalJam(raw) {
  if (!raw) { return '' }
  let d = new Date(raw)
  if (isNaN(d.getTime())) { return raw }
  let hh = ('0' + d.getHours()).slice(-2)
  let mi = ('0' + d.getMinutes()).slice(-2)
  let ss = ('0' + d.getSeconds()).slice(-2)
  return so2FormatTanggal(raw) + ' ' + hh + ':' + mi + ':' + ss
}

function so2Rupiah(n) {
  return Number(n || 0).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

function so2BoolCell(v) {
  return Number(v)
    ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
    : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
}

function renderTabel2Rows(rows) {
  let html = ''
  ;(rows || []).forEach(function (rowWrap) {
    let row = rowWrap[0]
    html += '<tr><td class="text-center">'
      + '<button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail(\'' + row.NOBUKTI + '\')"><i class="bi bi-info"></i></button>'
      + '<button class="btn btn-danger btn-sm" type="button" title="Cancel Authorization" onclick="buttonBatalOtorisasi(\'' + row.NOBUKTI + '\')"><i class="bi bi-key-fill"></i></button>'
      + '<button class="btn btn-success btn-sm" title="Open CBD" onclick="lockCBD(\'' + row.NOBUKTI + '\')"><i class="bi bi-check-square-fill"></i></button>'
      + '<button class="btn btn-info btn-sm" type="button" title="Print" onclick="submitPrint(\'' + row.NOBUKTI + '\')"><i class="bi bi-printer"></i></button>'
      + '</td>'
      + '<td>' + row.NOBUKTI + '</td>'
      + '<td>' + so2FormatTanggal(row.TANGGAL) + '</td>'
      + '<td>' + (row.NAMACUSTSUPP || '') + '</td>'
      + '<td>' + (row.nopesanan || '') + '</td>'
      + '<td style="text-align:right;">' + so2Rupiah(row.TotDPP) + '</td>'
      + '<td style="text-align:right;">' + so2Rupiah(row.TotPPn) + '</td>'
      + '<td style="text-align:right;">' + so2Rupiah(row.TotNet) + '</td>'
      + so2BoolCell(row.IsOtorisasi1)
      + '<td>' + (row.OtoUser1 || '') + '</td>'
      + '<td>' + so2FormatTanggalJam(row.TglOto1) + '</td>'
      + '</tr>'
  });
  document.getElementById('tabel2_data').innerHTML = html
}

function renderTabelOtoRows(rows) {
  let html = ''
  ;(rows || []).forEach(function (rowWrap) {
    let row = rowWrap[0]
    html += '<tr><td class="text-center">'
      + '<button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail(\'' + row.NOBUKTI + '\')"><i class="bi bi-info"></i></button>'
      + '<button class="btn btn-danger btn-sm" type="button" title="Cancel Authorization" onclick="buttonBatalOtorisasi(\'' + row.NOBUKTI + '\')"><i class="bi bi-key-fill"></i></button>'
      + '<button class="btn btn-info btn-sm" type="button" title="Print" onclick="submitPrint(\'' + row.NOBUKTI + '\')"><i class="bi bi-printer"></i></button>'
      + '</td>'
      + '<td>' + row.NOBUKTI + '</td>'
      + '<td>' + so2FormatTanggal(row.TANGGAL) + '</td>'
      + '<td>' + (row.NAMACUSTSUPP || '') + '</td>'
      + '<td>' + (row.nopesanan || '') + '</td>'
      + '<td style="text-align:right;">' + so2Rupiah(row.TotDPP) + '</td>'
      + '<td style="text-align:right;">' + so2Rupiah(row.TotPPn) + '</td>'
      + '<td style="text-align:right;">' + so2Rupiah(row.TotNet) + '</td>'
      + so2BoolCell(row.unblock)
      + '<td>' + (row.userunblock || '') + '</td>'
      + '<td>' + (row.tglunblock || '') + '</td>'
      + so2BoolCell(row.IsOtorisasi1)
      + '<td>' + (row.OtoUser1 || '') + '</td>'
      + '<td>' + so2FormatTanggalJam(row.TglOto1) + '</td>'
      + '</tr>'
  });
  document.getElementById('tabel_oto_data').innerHTML = html
}

function reinitTabel2() {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy(); }
    renderTabel2Rows(lastTabel2Rows);
    $('#tabel2').DataTable({
      dom: SO_DOM_STRING,
      lengthChange: false,
      paging: true,
      order: [[1, 'asc']],
      columnDefs: [{ targets: [0], orderable: false }],
      drawCallback: function () { setTimeout(soAturTinggiTabel, 0); }
    });
    soPerluGambar.tabel2 = false;
    soAturTinggiTabel();
  } catch (e) {
    console.error('reinitTabel2 failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function reinitTabelOto() {
  try {
    if ($.fn.DataTable.isDataTable('#tabel_oto')) { $('#tabel_oto').DataTable().destroy(); }
    renderTabelOtoRows(lastTabelOtoRows);
    $('#tabel_oto').DataTable({
      dom: SO_DOM_STRING,
      lengthChange: false,
      paging: true,
      autoWidth: false,
      drawCallback: function () { setTimeout(soAturTinggiTabel, 0); }
    });
    soPerluGambar.tabel_oto = false;
    soAturTinggiTabel();
  } catch (e) {
    console.error('reinitTabelOto failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function buttonHeaderTable() {
  var key = HeaderEngine.activeKey() || activeVisibleTabKey();
  alertify.confirm('Reset Kolom', 'Kembalikan kolom tabel ke tampilan default?', function () {
    HeaderEngine.activateEngineData(key);
    HeaderEngine.doSetHeader(g_modeReport, true);
    if (key === 'tabel') { reinitTabel(); } else { reinitTabel7(); }
    alertify.success('Kolom telah direset ke tampilan default');
  }, function () {});
}

jQuery(function($) {
  $('.input-partial-number').autoNumeric('init',
    {
      minimumValue : '0',
      // negativeSignCharacter: 'z'
     }
  );
});

$(document).ready(function(){

  document.getElementById('breadcrumb').innerHTML = "Sales Order";

  $('#input_add_kodepelanggan').on('keyup', function () {
    let keyword = $(this).val()
    searchPelanggan(keyword)
  })

  // $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddAddListSattax').show();

  // $("#form").modal('toggle')
  // let level = $("#level").val()
  // console.log(level)
      // Only the tab actually visible on load (#profile2/#tabel7, "Penawaran" -- see
      // "show active" on its tab-pane) is drawn immediately; #tabel/"SO" is left marked
      // soPerluGambar so it only costs a redraw once the user actually opens that tab --
      // port of purchaseOrder.blade.php's own loadAll()/poPerluGambar lazy-draw split.
      HeaderEngine.activateEngineData('tabel');
      HeaderEngine.doSetHeader(g_modeReport);
      lastTabelRows = @json($tempOutstanding1);
      soPerluGambar.tabel = true;

      HeaderEngine.activateEngineData('tabel7');
      HeaderEngine.doSetHeader(g_modeReport);
      lastTabel7Rows = @json($tempOutstanding7);
      reinitTabel7();

      // #tabel2/#tabel_oto have no nav-tab button (not reachable from the UI, same as
      // before this rebuild) so there's no shown.bs.tab moment to defer their draw to --
      // just render them immediately once their data exists.
      lastTabel2Rows = @json($tempOutstanding3);
      reinitTabel2();
      lastTabelOtoRows = @json($tempOutstanding5);
      reinitTabelOto();

      // Re-bind the interactive engine whenever the user switches tabs -- ReportTable's
      // listeners are bound to one table's DOM at a time -- and draw the tab lazily if
      // loadAll() left it marked dirty.
      $('#tab-diterima-btn').on('shown.bs.tab', function () {
        HeaderEngine.activateEngineData('tabel');
        if (soPerluGambar.tabel) { reinitTabel(); } else { HeaderEngine.bindEngineDom('tabel'); }
        soAturTinggiTabel();
      });
      $('#tab-dibuka-btn').on('shown.bs.tab', function () {
        HeaderEngine.activateEngineData('tabel7');
        if (soPerluGambar.tabel7) { reinitTabel7(); } else { HeaderEngine.bindEngineDom('tabel7'); }
        soAturTinggiTabel();
      });

        $("#tabel_tambahsoall").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[1, 'asc']],
            "columnDefs": [
                 {"targets" :[0] , 'orderable' : false}
              ]
          });


        $("#tabel_add_list_barangrefpr").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[1, 'asc']],
            "columnDefs": [
                 {"targets" :[0] , 'orderable' : false}
              ]
          });
          $("#tabel_add_list_refpr").DataTable({
            "lengthChange": false,
              "paging": false ,
              "order": [[0, 'asc']]
            });
            $("#tabel_add_list_nopenyerahan").DataTable({
              "lengthChange": false,
                "paging": false ,
                "order": [[0, 'asc']]
              });

        // #tabel2/#tabel_oto's own DataTables init now happens inside
        // reinitTabel2()/reinitTabelOto() above (called earlier in this same ready
        // block) -- this leftover direct call from before that existed would
        // re-initialize both a second time with no destroy() in between.

        $("#tabel_add_list_barang").DataTable({
          "lengthChange": false,
            "paging": false ,
        });

        $("#tabel_add_list_barangall").DataTable({
          "lengthChange": false,
            "paging": false ,
            "searching" : false,
        });

        // #tabel7's own DataTables init now happens inside reinitTabel7()
        // above (called earlier in this same ready block) -- this leftover
        // direct call from before the interactive-column engine existed was
        // re-initializing it a second time with no destroy() in between,
        // which is what threw "Cannot reinitialise DataTable".

        // Drive the shared #page1 toolbar controls across every table in
        // #page1 (masterTable.js isn't included on this page, so this isn't
        // wired up anywhere else).
        var page1Tables = ['#tabel', '#tabel2', '#tabel_oto', '#tabel7'];

        var tabelFilterVisualTimeout;
        $('#tabel_filter_visual').on('keyup', function () {
          var value = this.value;
          clearTimeout(tabelFilterVisualTimeout);
          tabelFilterVisualTimeout = setTimeout(function () {
            page1Tables.forEach(function (id) {
              if ($.fn.DataTable.isDataTable(id)) {
                $(id).DataTable().search(value).draw();
              }
            });
          }, 400);
        });

        $('#tabel_length_visual').on('change', function () {
          var len = Number(this.value);
          page1Tables.forEach(function (id) {
            if ($.fn.DataTable.isDataTable(id)) {
              $(id).DataTable().page.len(len).draw();
            }
          });
        });

    $("#tabel_add_list_pelanggan").DataTable({
      // "lengthChange": false,
      //   "paging": false ,
    });

  $("#tabel_add_list_sales").DataTable({
    "lengthChange": false,
      "paging": false ,
    });

    $("#tabel_add_list_sattax").DataTable({
    "lengthChange": false,
      "paging": false ,
    });

    $("#tabel_add_list_alamatkirim").DataTable({
    "lengthChange": false,
      "paging": false ,
    });

    $("#tabel_add_list_lokasipenerima").DataTable({
    "lengthChange": false,
      "paging": false ,
    });

    $("#tabel_add_list_pic").DataTable({
    "lengthChange": false,
      "paging": false ,
    });


  //   formAddListItem
});

// function buat input angka jadi rp

function soResetFilterFields () {
  $('#input_filterso').val('0');
  $('#input_tipebayar').val('4');
}

function buttonFilterSO () {
  console.log('buttonFilterSO')

  let _token  = $("#_token").val()
  let tglawal = $("#input_tanggalawal").val()
  let tglakhir = $("#input_tanggalakhir").val()
  let filterso = $("#input_filterso").val()
  let tipebayar = $("#input_tipebayar").val()

  let needoto = 0
  let cbdneedopen = 0

  // if (tipebayar == 4) {
  //
  //   tipebayar = 'No'
  // }
  // if (filterso == 0) {
  //   needoto = 0
  //   cbdneedopen = 0
  // } else if (filterso == 1) {
  //   cbdneedopen = 0
  //   needoto = 1
  // } else if (filterso == 2) {
  //   cbdneedopen = 0
  //   needoto = 0
  // } else if (filterso == 3) {
  //   cbdneedopen = 1
  // }
  let ketproses = 'B'
  let tipefilter = 0
  let ketclose = 0
//   - belum proses --   ketproses = 'B'
// - proses Sebagian   ketproses = 'S'
// - full supply       ketproses = 'F'
  if (filterso == 0) {
    tipefilter = 1

  } else if (filterso == 1) {
    tipefilter = 2
    needoto = 1
  } else if (filterso == 2) {
    tipefilter = 2
    needoto = 0
  } else if (filterso == 3) {
    tipefilter = 3
    ketproses = 'B'
  } else if (filterso == 4) {
    tipefilter = 3
    ketproses = 'S'
  }else if (filterso == 5) {
    tipefilter = 3
    ketproses = 'F'
  }else if (filterso == 6) {
    tipefilter = 4
    ketclose = 1
  }


  let level = $("#level").val()
  console.log({tglawal,
  tglakhir,
  filterso,
  needoto,
  cbdneedopen,
  ketproses,
  tipefilter,
  ketclose,
  tipebayar})
//   socbd cbdneedopen = 1
// so belum oto needcbd = 0 need oto = 1
// so sudah oto needcbd = 0 need oto = 0
  $.ajax({
    url: "{!! url('soloadsofilter') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      tglawal,
      tglakhir,
      filterso,
      needoto,
      cbdneedopen,
      ketproses,
      tipefilter,
      ketclose,
      tipebayar
    },
    success: function(res) {
      lastTabelRows = res;
      // Only draw #tabel immediately if it's actually the visible tab -- otherwise
      // this would silently undo the lazy-draw deferral loadAll() just set up
      // (soPerluGambar.tabel), redrawing a tab the user isn't even looking at.
      // Mirrors the same soPerluGambar check the shown.bs.tab handlers use.
      if (activeVisibleTabKey() === 'tabel') {
        reinitTabel();
      } else {
        soPerluGambar.tabel = true;
      }
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function formatRupiah(el) {
  let angka = el.value.replace(/\./g, '').replace(/[^0-9]/g, '');
  el.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

///// search no po
function searchNoPO(keyword) {
console.log('searchnopo')
 console.log($("#input_add_kodepelanggan").val(),'================')
if (keyword.length < 2) {
  $("#dropdown_nopo").hide()
  return
}

$.ajax({
  url: "{!! url('solistnopo') !!}",
  type: "post",
  data: {
    _token: $("#_token").val(),
    kodecustsupp: $("#input_add_kodepelanggan").val(),
    search: keyword
  },
  success: function(res) {
        console.log(res,'resssss')
       console.log($("#input_add_kodepelanggan").val(),'================')
    let html = ""

    res.forEach(item => {
      html += `
        <a class="dropdown-item"
          style="white-space: normal; word-break: break-word;"
          onclick="selectNoPO('${item.POCustomer}', '${item.namacustsupp}' , '${item.ID}')">
          ${item.POCustomer} - ${item.namacustsupp}
        </a>
      `
    })

    if (!res.length) {
      html = `<span class="dropdown-item text-muted">Tidak ada data</span>`
    }

    $("#dropdown_nopo").html(html).css({
      "max-height" : "250px",
      "overflow-y" : "auto",
      "overflow-x" : "hidden"
  }).show()
  }
})
}

function selectNoPO(noPo, nama , id = 0) {
  console.log(noPo, nama , id)
  $("#input_add_nopo").val(noPo)
  $("#input_add_idpo").val(id)
  $("#input_nama_pelanggan_nopo").val(nama)
  $("#dropdown_nopo").hide()
  idpocust = id

  if (tipeform == 'edit') {
    onChangeHeader('NoPesanan', noPo)
  }
}

$(document).click(function(e) {
  if (
    !$(e.target).closest('#input_add_nopo').length &&
    !$(e.target).closest('#dropdown_nopo').length
  ) {
    $('#dropdown_nopo').hide()
  }
})
//// end

/// start search
function searchPelanggan(keyword) {
  let checkDate = new Date($("#input_add_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() +1) !== Number(periode_bulan)) {
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  if (!keyword) {
    $('#dropdown_pelanggan').hide()
    return
  }

  // load data
  if (cachePelanggan.length === 0 && !isLoadingPelanggan) {

    isLoadingPelanggan = true

    $.ajax({
      url: "{!! url('solistpelanggan') !!}",
      type: "get",
      success: function(res) {
        cachePelanggan = res
        isLoadingPelanggan = false
        renderDropdown(keyword)
      },
      error: function() {
        isLoadingPelanggan = false
        alertify.warning('Gagal load pelanggan')
      }
    })

  } else {
    renderDropdown(keyword)
  }
}


function renderDropdown(keyword) {

  let html = ''

  let filtered = cachePelanggan.filter(item =>
    item.kodecustsupp.toLowerCase().includes(keyword.toLowerCase()) ||
    item.namacustsupp.toLowerCase().includes(keyword.toLowerCase())
  )

  if (filtered.length === 0) {
    html = `<span class="dropdown-item text-muted">Tidak ditemukan</span>`
  }

  filtered.slice(0, 10).forEach(item => {

    html += `
      <div class="dropdown-item"
      style="white-space: normal; word-break: break-word;"
        onclick="selectPelanggan(event,
          '${item.kodecustsupp}',
          '${item.namacustsupp}',
          '${item.alamat1}',
          ${item.PPN},
          ${item.HARI},
          '${item.KodeSls ?? ''}',
          '${item.NamaSales ?? ''}',
          '${item.BOffice ?? ''}',
          '${item.NamaBackOffice ?? ''}'
        )"
      >
        <strong>${item.kodecustsupp}</strong><br>
        <small>${item.namacustsupp}</small>
      </div>
    `
  })

  $('#dropdown_pelanggan').html(html).show()
}

function selectPelanggan(
  e,
  kode, nama, alamat, ppn, hari,
  kodeSales, namaSales, kodeBO, namaBO
) {

  e.preventDefault()
  e.stopPropagation()

  // VALIDASI
  if (!kodeSales || !namaSales) {
    alertify.warning('Tidak bisa pilih: Sales belum lengkap')
    return
  }

  if (!kodeBO || !namaBO) {
    alertify.warning('Tidak bisa pilih: Back Office belum lengkap')
    return
  }

  $('#dropdown_pelanggan').hide()

  buttonAddPickPelanggan(
    kode, nama, alamat, ppn, hari,
    kodeSales, namaSales, kodeBO, namaBO , 0
  )

  $('#input_add_kodepelanggan').val(kode)
  $('#input_add_namapelanggan').val(nama)

}

$(document).click(function(e) {
  if (
    !$(e.target).closest('#input_add_kodepelanggan').length &&
    !$(e.target).closest('#dropdown_pelanggan').length
  ) {
    $('#dropdown_pelanggan').hide()
  }
})
/// end search pelanggan

function openPrintModal(nobukti) {
  selectedNoBukti = nobukti
  $('#modalPrint').modal('show')
}

function choosePrint(type) {
  $('#modalPrint').modal('hide')

  if (type === 'default') {
    submitPrint(selectedNoBukti)
  }
  else if (type === 'design3') {
    submitPrint3(selectedNoBukti)
  }
  else if (type === 'jbg') {
    submitPrintJBG(selectedNoBukti)
  }
}

// pnya ko raymond

function submitAddTambahSO () {
  console.log('Submit Add Tambah SO')
  let _token = $("#_token").val();
  let tempDataTambahSO = []

  // console.log('TES ==========')
  // return
  console.log(dataTambahSO)
  dataTambahSO.forEach((item, i) => {
    console.log(document.getElementById(`add_checkbox${i}`).checked)
    if (document.getElementById(`add_checkbox${i}`).checked) {
      // addDataArray[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
      tempDataTambahSO.push(dataTambahSO[i])
    }
  });

  if (!tempDataTambahSO.length) {
    alertify.warning("Tidak ada item dipilih");
    return
  }
  let ppn = $("#input_tambahso_ppn").val()
  let idpo = $("#input_tambahso_idpo").val()
  let nopo = $("#input_tambahso_nopo").val()
  let kodecust = $("#input_tambahso_kodepelanggan").val()
let checkDate = new Date($("#input_tambahso_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  let tanggal = $("#input_add_tanggal").val();
console.log(kodecust , nopo)
  if (!kodecust || !nopo) {
    alertify.warning('Data tidak lengkap')
return
  }
  console.log({_token : _token,
  kodecust,
  ppn,
  idpo,
  nopo,
  tempData: tempDataTambahSO})
  $.ajax({
    url: "{!! url('sospaddtambahso') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodecust,
      ppn,
      idpo,
      nopo,
      tanggal,
      tempData: tempDataTambahSO
    },
    success: function(res) {
      console.log("========================")
      console.log(res ,'!')
      if (res) {
        alertify.success('SO telah ditambah');
        loadAll()
          $('#page4').hide();
        buttonEdit(res)
      }
      // loadAll()
      // $("#form").modal('toggle')

    }})
}



function submitAddTambahSOAll () {
  console.log('Submit Add Tambah SO All')
  let _token = $("#_token").val();
  let tempDataTambahSO = []

  // console.log('TES ==========')
  // return
  console.log(dataTambahSO)
  // dataTambahSO.forEach((item, i) => {
  //   console.log(document.getElementById(`add_checkboxAll${i}`).checked)
  //   if (document.getElementById(`add_checkboxAll${i}`).checked) {
  //     // addDataArray[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
  //     tempDataTambahSO.push(dataTambahSO[i])
  //   }
  // });
  //
  // if (!tempDataTambahSO.length) {
  //   alertify.warning("Tidak ada item dipilih");
  //   return
  // }
  // tempDataTableTambahSO
  if (!tempDataTableTambahSO.length) {
    alertify.warning("Tidak ada item dipilih");
    return
  }
  let ppn = $("#input_add_tipeppn").val()
  let idpo = $("#input_add_idpo").val()
  let nopo = $("#input_add_nopo").val()
  let nobukti = $("#input_add_nobukti").val()
  let nourut = $("#input_add_nourut").val()
  let kodecust = $("#input_add_kodepelanggan").val()
let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  let tanggal = $("#input_add_tanggal").val();
console.log(kodecust , nopo)
  if (!kodecust || !nopo) {
    alertify.warning('Data tidak lengkap')
return
  }
  console.log({
  kodecust,
  nobukti,
  nourut,
  ppn,
  idpo,
  nopo,
  tempData: tempDataTableTambahSO})
  $.ajax({
    url: "{!! url('sospaddtambahsoall') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodecust,
      nobukti,
      nourut,
      ppn,
      idpo,
      nopo,
      tanggal,
      tempData: tempDataTableTambahSO
    },
    success: function(res) {
      console.log("========================")
      console.log(res ,'!')
      if (res) {
        alertify.success('Penawaran telah ditambah ke SO');
          // $('#page4').hide();
          loadAll()
        buttonEdit(nobukti)
        $('#formTambahSo').modal('toggle');

      }
      // loadAll()
      // $("#form").modal('toggle')

    }})
}

function buttonTambahSOAll () {
  tempDataTableTambahSO = []
  console.log('buttonTambahSO')
// console.log(NOBUKTI, ppn)
let nopo = $("#input_add_nopo").val()
  let kodecust = $("#input_add_kodepelanggan").val()
  if (!kodecust || !nopo) {
    alertify.warning("Pilih cust & no po terlebih dahulu")
    return
  }
  let _token  = $("#_token").val()
    $.ajax({
      url: "{!! url('sogetdetailtambahsoall') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        kodecust
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)
        // return
        // res.header.forEach((item, i) => {
        //   console.log('a' , i)
        // });
        //
        // res.list.forEach((item, i) => {
        //   console.log('b' , i)
        // });

        if (!res) {
          alertify.warning("Data habis")
          // $("#form").modal('toggle')
          return
        } else {
          dataTambahSO = res
          $('#tabel_tambahsoall').DataTable().destroy();
          document.getElementById("input_tambahsoall_ppn").value = $("#input_tambahso_ppn").val();
		// document.getElementById("input_tambahso_tanggal").value = formatDate(new Date())
          // document.getElementById("input_tambahso_kodepelanggan").value = dataTambahSO[0].KODECUST
          // document.getElementById("input_tambahso_namapelanggan").value = dataTambahSO[0].NAMACUSTSUPP
          let rowTable = ""
          dataTambahSO.forEach((item, i) => {
            rowTable += `<tr>
            <td class="text-center"><input class="" type="checkbox" value="" id="add_checkboxAll${i}" onchange="onchangecheckboxtambahso(${i})"></td>
            <td>${item.NoBukti}</td>
            <td>${item.NAMACUSTSUPP}</td>
            <td>${item.CATATAN}</td>
            <td>${item.KodeBrg}</td>
            <td>${item.NamaBrg}</td>
            <td class="text-right">${item.Qnt ? parseFloat(item.Qnt).toFixed(2) : '0.00'}</td>
            <td>${item.Satuan}</td>

            </tr>`
          });

          if(!dataTambahSO.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="8">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_tambahsoall").innerHTML = rowTable

          $("#tabel_tambahsoall").DataTable({
            "lengthChange": false,
              "paging": false ,
              "order": [[1, 'asc']],
              "columnDefs": [
                   {"targets" :[0] , 'orderable' : false}
                ]
            });
        }

        // $('.showhidemodalbodydetail').hide();
        // $('#modalBodyAddListPelanggan').show();
        // $('#modalBodyDetailMain').show();
        // setNewNoBukti()

        // refreshDataTableAdd()
        // $("#formDetail").modal('toggle')
        // $('#page1').hide();
        // $('#page4').show();

        $('#formTambahSo').modal('toggle');


      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })

}

function onchangecheckboxtambahso (i) {
  console.log("onchangecheckboxtambahso" , i)
  if (document.getElementById(`add_checkboxAll${i}`).checked) {
    // tempDataTableTambahSO
    tempDataTableTambahSO.push(dataTambahSO[i])
  } else {
    // tempDataTableTambahSO

    const index = tempDataTableTambahSO.findIndex(item => item.NoBukti == dataTambahSO[i].NoBukti && item.KodeBrg == dataTambahSO[i].KodeBrg)
    tempDataTableTambahSO.splice(index,1)
  }
  console.log(tempDataTableTambahSO)

}

function buttonTambahSO (NOBUKTI, ppn) {
  console.log('buttonTambahSO')
console.log(NOBUKTI, ppn)
  let _token  = $("#_token").val()
    $.ajax({
      url: "{!! url('sogetdetailtambahso') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: NOBUKTI
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)
        // return
        // res.header.forEach((item, i) => {
        //   console.log('a' , i)
        // });
        //
        // res.list.forEach((item, i) => {
        //   console.log('b' , i)
        // });

        if (!res) {
          alertify.warning("Data habis")
          // $("#form").modal('toggle')
          return
        } else {
          dataTambahSO = res

          document.getElementById("input_tambahso_ppn").value = ppn
		document.getElementById("input_tambahso_tanggal").value = formatDate(new Date())
          document.getElementById("input_tambahso_kodepelanggan").value = dataTambahSO[0].KODECUST
          document.getElementById("input_tambahso_namapelanggan").value = dataTambahSO[0].NAMACUSTSUPP
          let rowTable = ""
          dataTambahSO.forEach((item, i) => {
            rowTable += `<tr>
            <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>

            <td>${item.KodeBrg}</td>
            <td>${item.NamaBrg}</td>
            <td class="text-right">${item.Qnt ? parseFloat(item.Qnt).toFixed(2) : '0.00'}</td>
            <td>${item.Satuan}</td>

            </tr>`
          });

          if(!dataTambahSO.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="5">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_tambahso").innerHTML = rowTable


        }

        // $('.showhidemodalbodydetail').hide();
        // $('#modalBodyAddListPelanggan').show();
        // $('#modalBodyDetailMain').show();
        // setNewNoBukti()

        // refreshDataTableAdd()
        // $("#formDetail").modal('toggle')
        $('#page1').hide();
        $('#page4').show();

        // $('#modalBodyAddMainHeader').show();


      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })

}

function searchNoPOTambahSO (keyword) {

if (keyword.length < 2) {
  $("#dropdown_nopo").hide()
  return
}
console.log($("#input_tambahso_kodepelanggan").val())

$.ajax({
  url: "{!! url('solistnopotambahso') !!}",
  type: "post",
  data: {
    _token: $("#_token").val(),
    kodecustsupp: $("#input_tambahso_kodepelanggan").val(),
    search: keyword
  },
  success: function(res) {

    let html = ""

    res.forEach(item => {
      html += `
        <a  class="dropdown-item"
          style="white-space: normal; word-break: break-word;"
          onclick="selectNoPOTambahSO('${item.POCustomer}', '${item.namacustsupp}' , '${item.ID}')">
          ${item.POCustomer} - ${item.namacustsupp}
        </a>
      `
    })

    if (!res.length) {
      html = `<span class="dropdown-item text-muted">Tidak ada data</span>`
    }

    $("#dropdown_nopotambahso").html(html).css({
      "max-height": "250px",
      "overflow-y": "auto",
      "overflow-x": "hidden"
    }).show()
  }
})
}


function selectNoPOTambahSO (noPo, nama , id) {
  console.log('selectNoPOTambahSO')
  console.log(noPo, nama , id)
  $("#input_tambahso_nopo").val(noPo)
  $("#input_tambahso_idpo").val(id)
  // $("#input_nama_pelanggan_nopo").val(nama)
  $("#dropdown_nopotambahso").hide()

  if (tipeform == 'edit') {
    onChangeHeader('NoPesanan', noPo)
  }
}

$(document).click(function(e) {
  if (
    !$(e.target).closest('#input_tambahso_nopo').length &&
    !$(e.target).closest('#dropdown_nopotambahso').length
  ) {
    $('#dropdown_nopotambahso').hide()
  }
})

function searchPelangganTambahSO (keyword) {
  console.log("searchPelangganTambahSO")
  let checkDate = new Date($("#input_tambahso_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() +1) !== Number(periode_bulan)) {
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  if (!keyword) {
    $('#dropdown_pelanggantambahso').hide()
    return
  }

  // load data
  console.log('1')
  if (cachePelanggan.length === 0 && !isLoadingPelanggan) {

    isLoadingPelanggan = true

    $.ajax({
      url: "{!! url('solistpelanggan') !!}",
      type: "get",
      success: function(res) {
        cachePelanggan = res
        isLoadingPelanggan = false
        renderDropdown(keyword)
      },
      error: function() {
        isLoadingPelanggan = false
        alertify.warning('Gagal load pelanggan')
      }
    })

  } else {
    renderDropdownTambahSO(keyword)
  }
}

function selectPelangganTambahSO(
              e,
              kode, nama, alamat, ppn, hari,
              kodeSales, namaSales, kodeBO, namaBO
            ) {
              console.log("selectPelangganTambahSO")

              e.preventDefault()
              e.stopPropagation()

              // VALIDASI




              $('#dropdown_pelanggantambahso').hide()

              buttonAddPickPelanggan(
                kode, nama, alamat, ppn, hari,
                kodeSales, namaSales, kodeBO, namaBO , 0 , 'a' , 1
              )

              $('#input_tambahso_kodepelanggan').val(kode)
              $('#input_tambahso_namapelanggan').val(nama)

            }

            $(document).click(function(e) {
              if (
                !$(e.target).closest('#input_tambahso_kodepelanggan').length &&
                !$(e.target).closest('#dropdown_pelanggantambahso').length
              ) {
                $('#dropdown_pelanggantambahso').hide()
              }
            })

/// end tambahan

function onChangeHeader1 (a,b,c) {
  console.log(a)
  console.log(b)
  console.log(c)
}

function buttonOtorisasi (nobukti) {

  console.log('buttonOtorisasi')
  console.log(nobukti)



  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


    let _token = $("#_token").val();

    // let nobukti = $("#input")
  console.log("SubmitOtorisasi ")
  // console.log(dataCekHarga)
  let mssg = ''
  $.ajax({
    url: "{!! url('socekhargaoto') !!}",
    type: "POST",
    data: { _token, nobukti },
    success: function(res) {
      console.log("------")
      console.log('rescekharga' ,res)

      for (var i = 0; i < res.length; i++) {
        console.log('1',i, mssg)

        console.log('a',i)
        let xtempx = 1;
        if (mssg) {
          mssg += ' , '
        }

        if (res[i].Ket != 'lanjut') {
          mssg += `
            Barang ${res[i].kodebrg} - ${res[i].Ket}
          `
        }

        // console.log(i, mssg)

      }
      console.log('mssg sini' , mssg)
      if (mssg) {
        console.log('mssg yes')
        alertify.confirm('Konfirmasi Otorisasi', mssg + '. Lanjut otorisasi ?',
            function() {
              console.log('yes')
              // return

              $.ajax({
                url: "{!! url('soupdateotorisasi') !!}",
                type: "post",
                async: false,
                data: {
                  _token,
                  nobukti

                },
                success: function(res) {
     console.log ('update ottttttttoooooo',res)
                  if (res == 0) {
                    alertify.warning("Tidak ada akses. Melebihi plafon")
                    return
                  } else if  (res == 9) {
                     alertify.warning("Customer masuk dalam daftar blacklist")

                  } else if  (res == 2) {
                     alertify.warning("Nnet melebihi plafon")





                  } else {

                    console.log("res oto" , res)
                    alertify.success('Berhasil update otorisasi')
                    loadAll()
                    $('#page3').hide();
                    $('#page1').show();
                  }



                },
                error: function (err) {
                  console.log(err)
                  alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }

              })


            }
          ,function(){
            console.log('no')
          });
          // if (xtempx == 0) {
          //   break;
          // })




      } else {
        console.log('else')
        // return
        console.log({ _token, nobukti })
        $.ajax({
          url: "{!! url('soupdateotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti

          },
          success: function(res) {
  console.log('-----------------------!')
console.log(res)
            if (res == 0) {
              alertify.warning("Tidak ada akses. Melebihi plafon")
              return
} else if  (res == 9) {
                     alertify.warning("Customer masuk dalam daftar blacklist")
} else if  (res == 2) {
                     alertify.warning("Nnet melebihi plafon")





            } else {

              console.log("res oto" , res)
              alertify.success('Berhasil update otorisasi')
              loadAll()
        $('#page3').hide();
              $('#page1').show();
            }

          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }


    }})


    return

  // alertify.confirm('Otorisasi', 'Otorisasi SO ' + nobukti + ' ?',
      // function() {







}

function lockCBD (nobukti) {
  console.log(nobukti)

  let akses = $("#akses_isotorisasi5").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Open', 'Open CBD ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('soupdatecbd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti
          },
          success: function(res) {
            console.log("res cbd" , res)
            alertify.success('Berhasil Update Unblock CBD')
            loadAll()
          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }
        })
      }
    ,function(){
      console.log('no')
    });
}

function buttonBatalOtorisasi (nobukti) {
  console.log(nobukti)



  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }





  alertify.prompt("Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
  function(evt, value) {
    // alertify.success("You entered: " + value);
    let xpket = value;

     if (xpket==''){
          alertify.warning('Keterangan harus diisi.');
          $.abort();
        }
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('soupdatebatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
            pket :value

          },
          success: function(res) {

            if (res == 0) {
              alertify.warning("Tidak ada akses. Melebihi plafon")
              return
            } else {

              console.log("res oto" , res)
              alertify.success('Berhasil batal otorisasi')
              loadAll()
	      $('#page3').hide();
              $('#page1').show();
            }



          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
      alertify.error("Action cancelled");
    });

}

function onChangeCatatan () {

  if (tipeform == 'edit') {
    let value  = $("#input_add_catatan").val()
    onChangeHeader('catatan' , value)

  }

}
function onChangeNoPO () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_nopo").val()
    onChangeHeader('NoPesanan' , value)
  }
}
function onChangeTgglPO () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_tanggalpo").val()
    onChangeHeader('TglPO' , value)
  }
}
function onChangeTgglKirim () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_tanggalkirim").val()
    onChangeHeader('TglKirim' , value)
  }
}

function onChangeTipePPN () {
  console.log('onChangeTipePPN')
  if (tipeform == 'edit') {
    let value = $("#input_add_tipeppn").val()
    console.log(value)
    onChangeHeader('TipePPn' , value)
    onChangeHeader('PPN' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    loadAll()
    refreshDataTableAdd(nobukti)
  }


}

function onChangeDP () {
  console.log('onChangeDP')
  if (tipeform == 'edit') {
    let value = $("#input_add_dp").val()
    console.log(value)
    onChangeHeader('DP' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }


}

function onChangeDraftPO () {
  console.log('onChangeDraftPO')
  if (tipeform == 'edit') {
    let value = $("#input_add_draftpo").val()
    console.log(value)
    onChangeHeader('PPO' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }


}

function onChangeHari () {
  console.log('onChangeHari')
  if (tipeform == 'edit') {
    let value = $("#input_add_hari").val()
    console.log(value)
    onChangeHeader('HARI' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }

}


function onChangeInputAddDisc () {
    // document.getElementById("input_add_discrp").value = '0.00'
    console.log('onChangeDisc')
    if (tipeform == 'edit') {
      let value = $("#input_add_disc").val()
      console.log(value)
      onChangeHeader('DISC' , value)
      refreshUpdateHeader()
      let nobukti = $("#input_add_nobukti").val()
      refreshDataTableAdd(nobukti)
    }
}


function onChangeInputAddDiscRp () {
    // document.getElementById("input_add_disc").value = '0.00'
    console.log('onChangeDiscRp')
      if (tipeform == 'edit') {
        let value = $("#input_add_discrp").val()
        console.log(dataHeaderAdd)
        let x = Number(value) / Number(dataHeaderAdd.TotSubTotal) * 100
        console.log(x)
        console.log(value)
        onChangeHeader('DISC' , x)
        refreshUpdateHeader()
        let nobukti = $("#input_add_nobukti").val()
        refreshDataTableAdd(nobukti)
      }
}

// function onChangeDisc () {
//   console.log('onChangeDisc')
//   if (tipeform == 'edit') {
//     let value = $("#input_add_disc").val()
//     console.log(value)
//     onChangeHeader('DISC' , value)
//     refreshUpdateHeader()
//     let nobukti = $("#input_add_nobukti").val()
//     refreshDataTableAdd(nobukti)
//   }
// }
//
// function onChangeDiscRp () {
//   console.log('onChangeDiscRp')
//   if (tipeform == 'edit') {
//     let value = $("#input_add_discrp").val()
//     console.log(value)
//     onChangeHeader('DISCRP' , value)
//     refreshUpdateHeader()
//     let nobukti = $("#input_add_nobukti").val()
//     refreshDataTableAdd(nobukti)
//   }
// }

function refreshUpdateHeader () {
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('sospupdateso') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      // alertify.success('update header berhasil')
      // return
      console.log('check')

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}



function onChangeHeaderSP (field, value , field1 = null , value2 = null) {
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('soonchangeheadersp') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success('update header berhasil')
      return
      console.log('check')

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}


function onChangeHeader (field, value) {
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('soonchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success(`update ${field} berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}



function submitAddAdd () {
  console.log('submitAddAdd')
  let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }





  let KodeBrg = $("#input_add_add_kodebarang").val()
  let Tanggal = $("#input_add_tanggal").val()
  let Harga = $("#input_add_add_harga").val()




  let NoSat = $("#input_add_add_nosat").val()
  let tanggaljatuhtempo = new Date($("#input_add_tanggal").val())
  let hari = $("#input_add_hari").val()
  tanggaljatuhtempo.setDate(tanggaljatuhtempo.getDate() + Number(hari))
  console.log(tanggaljatuhtempo)
  let jmlrecord = 0
  if (dataTableAdd.length) {
    jmlrecord = 1
  }

  let _token  = $("#_token").val()
  let choice = "I"
  let nobukti = $("#input_add_nobukti").val()
  let nourut = $("#input_add_nourut").val()
  let kodepelanggan = $("#input_add_kodepelanggan").val()
  let kodesales = $("#input_add_kodesales").val()
  let tanggal = $("#input_add_tanggal").val()
  let kodealamatkirim = $("#input_add_kodealamatkirim").val()
  let alamatkirim = $("#input_add_alamatkirim").val()
  let kodepic = $("#input_add_kodepic").val()
  let kodelokasipenerima = $("#input_add_kodelokasipenerima").val()
  let catatan = $("#input_add_catatan").val()
  let valas = $("#input_add_valas").val()
  let kurs = $("#input_add_kurs").val()
  let dp = $("#input_add_dp").val()
  let pembayaran = $("#input_add_pembayaran").val()
  let tipeppn = $("#input_add_tipeppn").val()
  let draftpo = $("#input_add_draftpo").val()
  //let draftpo = $("#input_add_add_tambahkepo").val()

  let nopo = $("#input_add_nopo").val()
  let tanggalpo = $("#input_add_tanggalpo").val()
  let tanggalkirim = $("#input_add_tanggalkirim").val()
  let kodebackoffice = $("#input_add_kodebackoffice").val()
  let kodesattax = $("#input_add_add_sattax").val()

  console.log (kodesattax,'=================================sattax')
  let statuspengiriman = $("#input_add_add_status").val()
  console.log(statuspengiriman)
  console.log(kodepelanggan,'*', kodesales)
  if (!kodepelanggan || !kodesales || !kodebackoffice || !kodesattax || !draftpo || !nobukti || !valas || !kodealamatkirim || !kodelokasipenerima || !nopo) {
    alertify.warning("Data tidak lengkap")
    return
  }

  let date1 = ""
  if (tanggaljatuhtempo) {
      let date = new Date(tanggaljatuhtempo);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    }
  tanggaljatuhtempo  = date1

  let refpr =  $("#input_add_add_refpr").val()
  // if (!refpr) {
    // let refpr = ''
  // }
  let nopenyerahan =  $("#input_add_add_nopenyerahan").val()
  // if (!nopenyerahan) {
    // let nopenyerahan = ''
  // }
  let kodebarang =  $("#input_add_add_kodebarang").val()
  if ((refpr && !nopenyerahan) || (!refpr && nopenyerahan) ) {
    alertify.warning("Data tidak lengkap")

  } else {

  }
  if (refpr && nopenyerahan) {

  } else {
    if (!tempAddAdd) {
      alertify.warning("Barang tidak sesuai dengan pilihan")
      return
    }

    if (kodebarang != tempAddAdd.Kodebrg) {
      alertify.warning("Barang tidak sesuai dengan pilihan")
      return
    }
    let namabarang =  $("#input_add_add_namabarang").val()
    if (namabarang != tempAddAdd.NamaBrg) {
      alertify.warning("Barang tidak sesuai dengan pilihan")
      return
    }

  }


  let namaproduk =  $("#input_add_add_namaproduk").val()
  //let qty =  $("#input_add_add_qty").val()
  let qty = Number(($("#input_add_add_qty").val() || '0').replace(/,/g, ''))
  let nosat =  $("#input_add_add_nosat").val()
  let satuanproduk =  $("#input_add_add_satuanproduk").val()
  //let harga =  $("#input_add_add_harga").val()
  let harga = Number(($("#input_add_add_harga").val() || '0').replace(/,/g, ''))
  let discDet =  $("#input_add_add_disc").val()
  //let discrpDet =  $("#input_add_add_discrp").val()
  let discrpDet = Number(($("#input_add_add_discrp").val() || '0').replace(/,/g, ''))
  let tambahkepo =  Number($("#input_add_add_tambahkepo").val())
  let booking =  $("#input_add_add_booking").val()
  let urgent =  $("#input_add_add_urgent").val()
  let urut = 0
  let disc = Number($("#input_add_disc").val())
  let discrp = Number($("#input_add_discrp").val())

  let tipediskon = 0
  if (disc) {
    tipediskon = 1
  }
  if (discrp) {
    tipediskon = 1
  }

  console.log(tempAddAdd)

  let satuan = ''
  let qnt1 = 0
  let isi =0
  if (nosat == 1) {
    qnt1 = qty * tempAddAdd.ISI1
    satuan = tempAddAdd.Sat1
    isi = tempAddAdd.ISI1
  }
  if (nosat == 2) {
    qnt1 = qty * tempAddAdd.ISI2
    satuan = tempAddAdd.Sat2
    isi = tempAddAdd.ISI2
  }
  if (nosat == 3) {
    qnt1 = qty * tempAddAdd.ISI3
    satuan = tempAddAdd.Sat3
    isi = tempAddAdd.ISI3
  }

  let pppn = 0
  if (Number(tempAddAdd.pPPN)) {
    pppn = 1
  }



  console.log({
    _token ,
    choice,
    nobukti,
    nourut,
    kodepelanggan,
    kodesales,
    tanggal,
    kodealamatkirim,
    kodepic,
    kodelokasipenerima,
    catatan,
    valas,
    kurs,
    dp,
    pembayaran,
    hari,
    tipeppn,
    draftpo,
    nopo,
    tanggalpo,
    tanggalkirim,
    kodebackoffice,
    tanggaljatuhtempo,
    jmlrecord,
    idpocust,
    kodesattax,
    statuspengiriman,

  })

  console.log({
    refpr,
    nopenyerahan,
    kodebarang,
    namaproduk,
    qty,
    nosat,
    satuanproduk,
    harga,
    discDet,
    discrpDet,
    tambahkepo,
    booking,
    urgent,
    urut,
    qnt1,
    isi,
    satuan,
    pppn
  })

  console.log({
    disc,
    discrp,
    tipediskon
  })

  console.log('==========' , Number(nosat))
  if (!kodebarang) {
    alertify.warning("Pilih Barang")
    return
  }
  if (Number(dp) < 0 || Number(hari) < 0 || Number(qty) <= 0 || Number(harga) < 0 || Number(discDet) < 0 || Number(discrpDet) < 0 || Number(disc) < 0 || Number(discrp) < 0)  {
    alertify.warning("Angka negatif / qty <= 0")
    return
  }
  console.log('submitAddAdd')


let hargaVal = Number(($("#input_add_add_harga").val() || '0').replace(/,/g, ''))
let discrpVal = Number(($("#input_add_add_discrp").val() || '0').replace(/,/g, ''))

let xppn = 0
if (Number($("#input_add_tipeppn").val()) == 2) {
    xppn = hargaVal * 0.1
}

let xhargacek = (hargaVal - discrpVal) - xppn

console.log('harga:', hargaVal)
console.log('disc:', discrpVal)
console.log('ppn:', xppn)
console.log('xhargacek:', xhargacek)


 $.ajax({
    url: "{!! url('socheckhargaddd') !!}",
    type: "get",
    async: false,
    data: { Tanggal,KodeBrg,xhargacek,NoSat,choice
    },
    success: function(res) {
      console.log ('=============================>',res,draftpo)
      flagharga = res
      if (draftpo==1) {
      flagharga='lanjut'
      }

      console.log ('=============================>',flagharga)
      if (flagharga !='lanjut'){
         alertify.confirm('' + flagharga + ' ?',


          function() {
              // return
              $.ajax({
                url: "{!! url('sospadd') !!}",
                type: "post",
                async: false,
                data: {
                  _token,
                  disc,
                  discrp,
                  tipediskon,
                  refpr,
                  nopenyerahan,
                  kodebarang,
                  namaproduk,
                  qty,
                  nosat,
                  satuanproduk,
                  harga,
                  discDet,
                  discrpDet,
                  tambahkepo,
                  booking,
                  urgent,
                  urut,
                  qnt1,
                  isi,
                  satuan,
                  pppn,
                  choice,
                  nobukti,
                  nourut,
                  kodepelanggan,
                  kodesales,
                  tanggal,
                  kodealamatkirim,
                  alamatkirim,
                  kodepic,
                  kodelokasipenerima,
                  catatan,
                  valas,
                  kurs,
                  dp,
                  pembayaran,
                  hari,
                  tipeppn,
                  draftpo,
                  nopo,
                  tanggalpo,
                  tanggalkirim,
                  kodebackoffice,
                  tanggaljatuhtempo,
                  jmlrecord,
                  idpocust,
                  kodesattax,
                  statuspengiriman,
                  tglkirimdet: tanggalkirim

                },
                success: function(res) {
                  console.log('resspsoadd', res)
                  if (res == 1) {
                        document.getElementById("input_add_nobukti").value = nobukti;
                        console.log(nobukti,'gggggggggggggggg')
                    loadAll()

                    // lockFormAdd()
                    // $('.showhide').hide();
                    // $('#buttonSubmitSaveHeader').show();
                    // unlockFormAdd()
                    tipeform = 'edit'
                    // document.getElementById("buttonAddListPelanggan").disabled = true
                    $('#divhargaterakhir').hide();
                    cleanFormAddAdd()

                    refreshDataTableAdd(nobukti)

                    alertify.success('Berhasil menambah item')
                  }
                  if(res == 2) {
                    setNewNoBukti()
                    alertify.warning('Nobukti telah direfresh silahkan submit ulang')
                  }

                  if(res == 3) {
                    alertify.warning("Barang tidak ditemukkan")
                  }

                },
                error: function (err) {
                  console.log(err)
                  alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }

              })

   }
            ,function(){
          console.log(' cancel harga minimal')

          return
          });
      }
          // SESUAI RANGE HARGA
    else {

            $.ajax({
                url: "{!! url('sospadd') !!}",
                type: "post",
                async: false,
                data: {
                  _token,
                  disc,
                  discrp,
                  tipediskon,
                  refpr,
                  nopenyerahan,
                  kodebarang,
                  namaproduk,
                  qty,
                  nosat,
                  satuanproduk,
                  harga,
                  discDet,
                  discrpDet,
                  tambahkepo,
                  booking,
                  urgent,
                  urut,
                  qnt1,
                  isi,
                  satuan,
                  pppn,
                  choice,
                  nobukti,
                  nourut,
                  kodepelanggan,
                  kodesales,
                  tanggal,
                  kodealamatkirim,
                  alamatkirim,
                  kodepic,
                  kodelokasipenerima,
                  catatan,
                  valas,
                  kurs,
                  dp,
                  pembayaran,
                  hari,
                  tipeppn,
                  draftpo,
                  nopo,
                  tanggalpo,
                  tanggalkirim,
                  kodebackoffice,
                  tanggaljatuhtempo,
                  jmlrecord,
                  idpocust,
                  kodesattax,
                  statuspengiriman,
                  tglkirimdet: tanggalkirim

                },
                success: function(res) {
                  console.log('resspsoadd', res)
                  if (res == 1) {
                        document.getElementById("input_add_nobukti").value = nobukti;
                        console.log(nobukti,'gggggggggggggggg')
                    loadAll()

                    // lockFormAdd()
                    // $('.showhide').hide();
                    // $('#buttonSubmitSaveHeader').show();
                    // unlockFormAdd()
                    tipeform = 'edit'
                    // document.getElementById("buttonAddListPelanggan").disabled = true
                    $('#divhargaterakhir').hide();
                    cleanFormAddAdd()

                    refreshDataTableAdd(nobukti)

                    alertify.success('Berhasil menambah item')
                  }
                  if(res == 2) {
                    setNewNoBukti()
                    alertify.warning('Nobukti telah direfresh silahkan submit ulang')
                  }

                  if(res == 3) {
                    alertify.warning("Barang tidak ditemukkan")
                  }

                },
                error: function (err) {
                  console.log(err)
                  alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }

              })






    }
  }
})

}



function submitAddEdit () {
  console.log('submitAddEdit')

  let checkDate = new Date($("#input_add_tanggal").val())
  let tanggaljatuhtempo = new Date($("#input_add_tanggal").val())
  let hari = $("#input_add_hari").val()
  tanggaljatuhtempo.setDate(tanggaljatuhtempo.getDate() + Number(hari))
  console.log(tanggaljatuhtempo)
  let jmlrecord = 0
  if (dataTableAdd.length) {
    jmlrecord = 1
  }

  let _token  = $("#_token").val()
  let choice = "U"
  let nobukti = $("#input_add_nobukti").val()
  let nourut = $("#input_add_nourut").val()
  let kodepelanggan = $("#input_add_kodepelanggan").val()
  let kodesales = $("#input_add_kodesales").val()
  let tanggal = $("#input_add_tanggal").val()
  let kodealamatkirim = $("#input_add_kodealamatkirim").val()
  let alamatkirim = $("#input_add_alamatkirim").val()
  let kodepic = $("#input_add_kodepic").val()
  let kodelokasipenerima = $("#input_add_kodelokasipenerima").val()
  let catatan = $("#input_add_catatan").val()
  let valas = $("#input_add_valas").val()
  let kurs = $("#input_add_kurs").val()
  let dp = $("#input_add_dp").val()
  let pembayaran = $("#input_add_pembayaran").val()
  let tipeppn = $("#input_add_tipeppn").val()
  let draftpo = $("#input_add_draftpo").val()
  let nopo = $("#input_add_nopo").val()
  let tanggalpo = $("#input_add_tanggalpo").val()
  let tanggalkirim = $("#input_add_tanggalkirim").val()
  let kodebackoffice = $("#input_add_kodebackoffice").val()

  let kodesattax = $("#input_add_add_sattax").val()
  let statuspengiriman = $("#input_add_add_status").val()
  let date1 = ""
  if (tanggaljatuhtempo) {
      let date = new Date(tanggaljatuhtempo);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    }
  tanggaljatuhtempo  = date1

  // let refpr =  $("#input_add_add_refpr").val()
  // if (!refpr) {
    let refpr = $("#input_add_add_refpr").val()
  // }
  // let nopenyerahan =  $("#input_add_add_nopenyerahan").val()
  // if (!nopenyerahan) {
    let nopenyerahan =  $("#input_add_add_nopenyerahan").val()
  // }
  let kodebarang =  $("#input_add_add_kodebarang").val()
  if ((refpr && !nopenyerahan) || (!refpr && nopenyerahan) ) {
    alertify.warning("Data tidak lengkap")

  } else {

  }

  let namaproduk =  $("#input_add_add_namaproduk").val()
  let qty =  $("#input_add_add_qty").val()
  let nosat =  $("#input_add_add_nosat").val()
  let satuanproduk =  $("#input_add_add_satuanproduk").val()
  let harga = Number(($("#input_add_add_harga").val() || '0').replace(/,/g, ''))
  let discDet = Number(($("#input_add_add_disc").val() || '0').replace(/,/g, ''))
  let discrpDet = Number(($("#input_add_add_discrp").val() || '0').replace(/,/g, ''))

  let disc = Number(($("#input_add_disc").val() || '0').replace(/,/g, ''))
  let discrp = Number(($("#input_add_discrp").val() || '0').replace(/,/g, ''))

  let tambahkepo =  Number($("#input_add_add_tambahkepo").val())
  let booking =  $("#input_add_add_booking").val()
  let urgent =  $("#input_add_add_urgent").val()
  let urut = tempAddEdit.Urut


  let tipediskon = 0
  if (disc) {
    tipediskon = 1
  }
  if (discrp) {
    tipediskon = 1
  }

console.log(!kodepelanggan ,kodesales ,kodebackoffice ,kodesattax ,draftpo ,nobukti ,valas ,kodealamatkirim ,kodelokasipenerima ,nopo)

if (!kodepelanggan || !kodesales || !kodebackoffice || !kodesattax || !draftpo || !nobukti || !valas || !kodealamatkirim || !kodelokasipenerima || !nopo) {
	alertify.warning("Data tidak lengkap")
    return
}

  console.log(tempAddEdit)

  let satuan = ''
  let qnt1 = 0
  let isi =0
  if (nosat == 1) {
    qnt1 = qty * tempAddEdit.ISI1
    satuan = tempAddEdit.SAT1
    isi = tempAddEdit.ISI1
  }
  if (nosat == 2) {
    qnt1 = qty * tempAddEdit.ISI2
    satuan = tempAddEdit.SAT2
    isi = tempAddEdit.ISI2
  }
  if (nosat == 3) {
    qnt1 = qty * tempAddEdit.ISI3
    satuan = tempAddEdit.SAT3
    isi = tempAddEdit.ISI3
  }

  let pppn = 0
  if (Number(tempAddEdit.pPPN)) {
    pppn = 1
  }
  console.log("!!!!!!!!!!!!!!!!!")

  console.log({
    _token ,
    choice,
    nobukti,
    nourut,
    kodepelanggan,
    kodesales,
    tanggal,
    kodealamatkirim,
    kodepic,
    kodelokasipenerima,
    catatan,
    valas,
    kurs,
    dp,
    pembayaran,
    hari,
    tipeppn,
    draftpo,
    nopo,
    tanggalpo,
    tanggalkirim,
    kodebackoffice,
    tanggaljatuhtempo,
    jmlrecord,
    refpr,
    nopenyerahan,
    kodebarang,
    namaproduk,
    qty,
    nosat,
    satuanproduk,
    harga,
    discDet,
    discrpDet,
    tambahkepo,
    booking,
    urgent,
    urut,
    qnt1,
    isi,
    satuan,
    pppn,
    disc,
    discrp,
    tipediskon

  })

  console.log({
    refpr,
    nopenyerahan,
    kodebarang,
    namaproduk,
    qty,
    nosat,
    satuanproduk,
    harga,
    discDet,
    discrpDet,
    tambahkepo,
    booking,
    urgent,
    urut,
    qnt1,
    isi,
    satuan,
    pppn
  })

  console.log({
    disc,
    discrp,
    tipediskon
  })

  if (Number(qty) <= 0 || Number(harga) < 0 || Number(discDet) < 0 || Number(discrpDet) < 0 || Number(disc) < 0 || Number(discrp) < 0)  {
    alertify.warning("Angka negatif")
    return
  }

let xppn=0
  let xharga=0
  if  ( $("#input_add_tipeppn").val()==2) {
      xppn= $("#input_add_add_harga").val() * 0.1
  }

 xharga= harga -  $("#input_add_discrp").val() - xppn
  console.log(kodebarang,tanggal,xharga,nosat,choice)
 $.ajax({
    url: "{!! url('socheckhargaddd') !!}",
    type: "get",
    async: false,
    data: { tanggal,kodebarang,xharga,nosat,choice
    },
    success: function(res) {
      console.log ('=============================>',res,draftpo)
      flagharga = res
      if (tambahkepo==1) {
      flagharga='lanjut';
      }

      console.log ('=============================>',flagharga)
      if (flagharga !='lanjut'){
         alertify.confirm('' + flagharga + ' ?',


          function() {


                    // return
                    $.ajax({
                      url: "{!! url('sospadd') !!}",
                      type: "post",
                      async: false,
                      data: {
                        _token,
                        disc,
                        discrp,
                        tipediskon,
                        refpr,
                        nopenyerahan,
                        kodebarang,
                        namaproduk,
                        qty,
                        nosat,
                        satuanproduk,
                        harga,
                        discDet,
                        discrpDet,
                        tambahkepo,
                        booking,
                        urgent,
                        urut,
                        qnt1,
                        isi,
                        satuan,
                        pppn,
                        choice,
                        nobukti,
                        nourut,
                        kodepelanggan,
                        kodesales,
                        tanggal,
                        kodealamatkirim,
                        alamatkirim,
                        kodepic,
                        kodelokasipenerima,
                        catatan,
                        valas,
                        kurs,
                        dp,
                        pembayaran,
                        hari,
                        tipeppn,
                        draftpo,
                        nopo,
                        tanggalpo,
                        tanggalkirim,
                        kodebackoffice,
                        tanggaljatuhtempo,
                        jmlrecord,

                        kodesattax ,
                         refpr,

                        nopenyerahan ,
                        statuspengiriman,

                      },
                      success: function(res) {
                        console.log('resspsoaddedit', res)

                        loadAll()

                        // lockFormAdd()
                        $('.showhide').hide();
                        refreshDataTableAdd(nobukti)

                        alertify.success('Berhasil edit item')

                      },
                      error: function (err) {
                        console.log(err)
                        alertify.warning('Terjadi kesalahan silahkan refresh browser')
                      }

                    })
                }
               ,function(){
              console.log(' cancel harga minimal')

          return
          });

          } else
          {

            $.ajax({
                      url: "{!! url('sospadd') !!}",
                      type: "post",
                      async: false,
                      data: {
                        _token,
                        disc,
                        discrp,
                        tipediskon,
                        refpr,
                        nopenyerahan,
                        kodebarang,
                        namaproduk,
                        qty,
                        nosat,
                        satuanproduk,
                        harga,
                        discDet,
                        discrpDet,
                        tambahkepo,
                        booking,
                        urgent,
                        urut,
                        qnt1,
                        isi,
                        satuan,
                        pppn,
                        choice,
                        nobukti,
                        nourut,
                        kodepelanggan,
                        kodesales,
                        tanggal,
                        kodealamatkirim,
                        alamatkirim,
                        kodepic,
                        kodelokasipenerima,
                        catatan,
                        valas,
                        kurs,
                        dp,
                        pembayaran,
                        hari,
                        tipeppn,
                        draftpo,
                        kodesattax,
                        nopo,
                        tanggalpo,
                        tanggalkirim,
                        kodebackoffice,
                        tanggaljatuhtempo,
                        jmlrecord

                      },
                      success: function(res) {
                        console.log('resspsoaddedit', res)

                        loadAll()

                        // lockFormAdd()
                        $('.showhide').hide();
                        refreshDataTableAdd(nobukti)

                        alertify.success('Berhasil edit item')

                      },
                      error: function (err) {
                        console.log(err)
                        alertify.warning('Terjadi kesalahan silahkan refresh browser')
                      }

                    })



          }


        }




}
 )

}



function onChangeInputAddPembayaran () {
  console.log("onChangeInputAddPembayaran")
  let check = Number($("#input_add_pembayaran").val())
  console.log(typeof check)
  console.log(check)

  if (dataTableAdd.length) {

    onChangeHeader('TIPEBAYAR' , check)
  }
  let nobukti = $("#input_add_nobukti").val()
  console.log('len',dataTableAdd.length)
  if (check) {
    let _token = $("#_token").val();
    let kodepelanggan = $("#input_add_kodepelanggan").val();

    $.ajax({
      url: "{!! url('socekkredithari') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        kodepelanggan
      },
      success: function(res) {
        console.log(res)
        if(res.length && res[0].hari) {
          document.getElementById("input_add_hari").value = res[0].hari

          if (dataTableAdd.length) {
            console.log('masokk')
            onChangeHeader('HARI' , res[0].hari)
            refreshUpdateHeader()
            // let nobukti = $("#input_add_nobukti").val()
            refreshDataTableAdd(nobukti)

          }
        }
        // onChangeHeader('TIPEBAYAR' , check)


      }})

  } else {
    document.getElementById("input_add_hari").value = 0
    // console.log('onChangeHari')
    if (tipeform == 'edit') {
      console.log('len', dataTableAdd.length)
      // console.log(value)
      // onChangeHeader('TIPEBAYAR' , check)
      if (dataTableAdd.length) {
        console.log('masokk 2')
        onChangeHeader('HARI' , 0)
        refreshUpdateHeader()
        // let nobukti = $("#input_add_nobukti").val()
        refreshDataTableAdd(nobukti)

      }
    }
  }
}

function onChangeInputAddAddDisc () {
  console.log("onChangeInputAddAddDisc")
  let harga = formatAngkaVal($("#input_add_add_harga").val());

  if (!Number(harga)) {

    document.getElementById("input_add_add_discrp").value = '0.00'
    return
  }

  let disc = $("#input_add_add_disc").val();
  let discRp = Number(harga) * Number(disc) / 100
  document.getElementById("input_add_add_discrp").value = formatAngka(parseFloat(discRp).toFixed(2))

}



function onChangeInputAddAddHarga () {
  document.getElementById("input_add_add_discrp").value = '0.00'
  document.getElementById("input_add_add_disc").value = '0.00'
}

function onChangeInputAddEditHarga () {
  document.getElementById("input_add_edit_discrp").value = '0.00'
  document.getElementById("input_add_edit_disc").value = '0.00'
}

function onChangeInputAddAddDiscRp () {
  console.log("onChangeInputAddAddDiscRp")
  let harga = formatAngkaVal($("#input_add_add_harga").val());

  if (!Number(harga)) {

    document.getElementById("input_add_add_disc").value = '0.00'
    return
  }

  let discRp = formatAngkaVal($("#input_add_add_discrp").val());
  let disc = Number(discRp) / Number(harga) * 100
  document.getElementById("input_add_add_disc").value = parseFloat(disc).toFixed(2)
}


function buttonAddAddItem () {
  tipeformitem = 'add'
  $('.showhide').hide();

  $('#divhargaterakhir').hide();




  cleanFormAddAdd()

  document.getElementById("buttonAddAddListRefPr").disabled = false
  document.getElementById("buttonAddAddListNoPenyerahan").disabled = false
  document.getElementById("input_add_add_kodebarang").disabled = false
  document.getElementById("input_add_add_namabarang").disabled = true
  document.getElementById("buttonAddAddListBarang").disabled = false
  $('#h4AddAddItem').show();
  $('#h4AddEditItem').hide();
  $('#submitAddAdd').show();
  $('#submitAddEdit').hide();
  $('#addAddItem').show();
  document.getElementById("input_add_add_namabarang").scrollIntoView();
}

function showTableHargaTerakhir () {

  if (!$("#divhargaterakhir").is(':visible')) {
    $('#divhargaterakhir').show();
  } else {
    $('#divhargaterakhir').hide();
  }
  // $("#car-2").is(':visible')
}

function buttonAddEditItem (i) {
  tipeformitem = 'edit'
  let _token = $("#_token").val();
  console.log('buttonAddEditItem')
  $('.showhide').hide();
  document.getElementById("buttonAddAddListBarang").disabled = true
  document.getElementById("buttonAddAddListRefPr").disabled = true
  document.getElementById("buttonAddAddListNoPenyerahan").disabled = true
  document.getElementById("input_add_add_kodebarang").disabled = true
  // cleanFormAddAdd()
  console.log(dataTableAdd[i])
  tempAddEdit = dataTableAdd[i]
  console.log(tempAddEdit)

if (tempAddEdit.nopl != '') {
  alertify.warning('Data sudah masuk picking list, tidak bisa di edit')
  return
}

  let selectOption = ''
  console.log('a' ,tempAddEdit.SAT1)
  if (tempAddEdit.SAT1) {
      selectOption += `<option value=1 selected>${tempAddEdit.SAT1} - ${tempAddEdit.ISI1}</option>`
    }
    if (tempAddEdit.SAT2) {
      selectOption += `<option value=2>${tempAddEdit.SAT2} - ${tempAddEdit.ISI2}</option>`
    }
    if (tempAddEdit.SAT3) {
      selectOption += `<option value=3>${tempAddEdit.SAT3} - ${tempAddEdit.ISI3}</option>`
    }


  // if (tempAddEdit.SAT1) {
  //   console.log('masuk sat 1')
  //   selectOption += `<option value='1' selected>${}</option>`
  // }
  // console.log('a' ,tempAddEdit.SAT2)
  // if (tempAddEdit.SAT2) {
  //   selectOption += `<option value='2'>${}</option>`
  // }
  // console.log('a' ,tempAddEdit.SAT3)
  // if (tempAddEdit.SAT3) {
  //   selectOption += `<option value='3'>${tempAddEdit.SAT3}</option>`

  // }
  console.log('sel' , selectOption)

  document.getElementById("input_add_add_nosat").innerHTML = selectOption

  document.getElementById("input_add_add_nosat").value = tempAddEdit.NoSat
  if (tempAddEdit.RefPR == '-' || !tempAddEdit.RefPR) {


  } else {
    document.getElementById("input_add_add_refpr").value = tempAddEdit.RefPR


  }
  if (tempAddEdit.NOserah == '-' || !tempAddEdit.NOserah) {


  } else {
    document.getElementById("input_add_add_nopenyerahan").value = tempAddEdit.NOserah


  }


  document.getElementById("input_add_add_tglkirim").value = formatDate(tempAddEdit.TglKirim)
  console.log('[][][]')
  console.log(tempAddEdit.SP)
  if (tempAddEdit.SP == '-' || !tempAddEdit.SP ) {
    console.log('a')
    document.getElementById("input_add_add_status").value = 0

  } else {
    console.log('bbbbbbbb')
    document.getElementById("input_add_add_status").value = tempAddEdit.SP

  }

  if (tempAddEdit.NAMATAX) {
    document.getElementById("input_add_add_sattax").value = tempAddEdit.NAMATAX
    document.getElementById("input_add_add_kodesattax").value = tempAddEdit.sattax
  } else {
    document.getElementById("input_add_add_sattax").value = ''
    document.getElementById("input_add_add_kodesattax").value = ''

  }

  // document.getElementById("input_add_add_status").value = tempAddEdit.xSP

  document.getElementById("input_add_add_nosat").value = tempAddEdit.NoSat

  document.getElementById("input_add_add_harga").value = Number(tempAddEdit.Harga) ? formatAngka(parseFloat(tempAddEdit.Harga).toFixed(2)) : '0.00'
  document.getElementById("input_add_add_kodebarang").value = tempAddEdit.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddEdit.NamaBrg
  document.getElementById("input_add_add_namabarang").disabled = true
  document.getElementById("input_add_add_kodebarang").disabled = true
  document.getElementById("input_add_add_namaproduk").value = tempAddEdit.pNamaBRG
  document.getElementById("input_add_add_qty").value = formatAngka(parseFloat(tempAddEdit.Qnt).toFixed(2))
  document.getElementById("input_add_add_satuanproduk").value = tempAddEdit.sATx

  // console.log(Number(tempAddEdit.DiscP1))
  document.getElementById("input_add_add_disc").value = Number(tempAddEdit.DiscP1) ?  tempAddEdit.DiscP1 : '0.00'
  document.getElementById("input_add_add_discrp").value = formatAngka(parseFloat(tempAddEdit.DiscRp1).toFixed(2))

  document.getElementById("input_add_add_tambahkepo").value = tempAddEdit.IsPO
  document.getElementById("input_add_add_booking").value = tempAddEdit.Pbooking
  document.getElementById("input_add_add_urgent").value = tempAddEdit.pUrgent

  console.log("kodecust:", $("#input_add_kodepelanggan").val())
  console.log({
  kodebarang: tempAddEdit.KodeBrg,
  kodecustsupp: tempAddEdit.KODECUST,
  kodekebun: $("#input_kodekebun").val() || tempAddEdit.KODEKEBUN
  })

  $.ajax({
    url: "{!! url('socekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddEdit.KodeBrg,
      nosat : 1,
      kodecustsupp: $("#input_add_kodepelanggan").val(),
      kodekebun: $("#input_kodekebun").val() || tempAddEdit.KODEKEBUN
    },
    success: function(res) {
      console.log("BELI RAW:", res.harga_beli)
      console.log(res)
      let jual = res.harga_jual || []
      let beli = res.harga_beli || []

      let rowTable = ``
      let rowTableBeli = ``
      jual.forEach((item, i) => {
        let date1 = ""
        if (item.tanggal) {
            let date = new Date(item.tanggal);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${date1}</td>
          <td class="text-right">${item.qnt2 ?? '-'}</td>
          <td class="text-center">${item.satuan ?? '-'}</td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.harga) || 0)}
          </td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.discrp1) || 0)}
          </td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.disctot) || 0)}
          </td>
        </tr>`})

        if(!jual.length) {
          rowTable= `<tr><td class="text-center" colspan=6>Tidak ada data</td></tr>`
        }

        beli.forEach((item) => {
        let date1 = ""
        if (item.tanggal) {
          let date = new Date(item.tanggal);
          let day = ("0" + date.getDate()).slice(-2);
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          date1 = date.getFullYear()+"/"+month+"/"+day;
        }

        rowTableBeli += `
        <tr>
          <td>${date1}</td>
          <td class="text-right">${item.qntterima ?? '-'}</td>
          <td class="text-center">${item.satuan ?? '-'}</td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.harga) || 0)}
          </td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.ndiskon) || 0)}
          </td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.disctot) || 0)}
          </td>
        </tr>`
      })

      if (!beli.length) {
        rowTableBeli = `<tr><td class="text-center" colspan=6>Tidak ada data</td></tr>`
      }

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable
      document.getElementById("tabel_data_add_harga_beli").innerHTML = rowTableBeli

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

  $('#divhargaterakhir').hide();
  $('#h4AddAddItem').hide();
  $('#h4AddEditItem').show();
  $('#submitAddAdd').hide();
  $('#submitAddEdit').show();
  $('#addAddItem').show();

  document.getElementById("input_add_add_namabarang").scrollIntoView();
}

function closeShowHideAdd () {
  $('.showhide').hide();

}


function setNewNoBukti (ppn) {
  $.ajax({
    url: "{!! url('sospnobukti') !!}",
    type: "get",
    async: false,
    data: {
      ppn
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
}


function buttonAddListPIC () {

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodepelanggan").val();

  if (!kodecustsupp) {
    alertify.warning("Isi pelanggan terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('solistpic') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp
    },
    success: function(res) {
      if ($.fn.DataTable.isDataTable('#tabel_add_list_pic')) {
        $('#tabel_add_list_pic').DataTable().destroy();
      }
      listpic = res

      let rowTable = ``
      listpic.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickPIC('${i}')">

        <td>${item.kodepic}</td>
        <td>${item.nama}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=2>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_pic").innerHTML = rowTable

      document.getElementById("tabel_data_add_list_pic").innerHTML = rowTable
      $("#tabel_add_list_pic").DataTable({
        lengthChange: false,
        paging: false,
        searching: true,
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPIC').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}



function buttonAddListNoPo () {

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodepelanggan").val();

  if (!kodecustsupp) {
    alertify.warning("Isi pelanggan terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('solistnopo') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickNoPo('${item.POCustomer}' , '${item.ID}')" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.ID}</td>
        <td>${item.namacustsupp}</td>
        <td>${item.POCustomer}</td>
        <td>${formatDate(item.TglInput , '/')}</td>
        <td>${formatDate(item.TglTerima , '/')}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=6>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_nopo").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListNoPo').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function onKeyPressBarang (e) {
  console.log("onKeyPressBarang")
  let nopenyerahan = $("#input_add_add_nopenyerahan").val()
  let refpr = $("#input_add_add_refpr").val()


  if (e.which === 13) {
    // let
    let kodebrg = $('#input_add_add_kodebarang').val();
    document.getElementById("input_search_barang_all").value = kodebrg
    console.log('kodebrg' , kodebrg)
    let search = $("#input_search_barang_all").val();
    console.log(search)
    $('#tabel_add_list_barangall').DataTable().destroy();

    $.ajax({
      url: "{!! url('solistbarang') !!}",
      type: "get",
      async: false,
      data: {
        search
      },
      success: function(res) {

        console.log(res)
        if (res.length == 1) {
          buttonAddAddPickBarangAll( res[0].Kodebrg , 1)
          $('#input_add_add_namaproduk').focus();
          return
        }
        let rowTable = ""
        res.forEach((item, i) => {

          rowTable +=          `
          <tr >
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangAll('${item.Kodebrg}')" type="button" ><i class="bi bi-plus"></i></button></td>

            <td>${item.Kodebrg}</td>
            <td>${item.NamaBrg}</td>
            <td>${item.NAMAMERK}</td>
            <td>${item.Sat1}</td>




        </tr>
        `
        });
        // $('#tabel_add_list_barangall').DataTable().destroy();

        document.getElementById("tabel_data_add_list_barangall").innerHTML = rowTable

      $("#tabel_add_list_barangall").DataTable({
        "lengthChange": false,
          "paging": false ,
          "searching" : false,
          "order": [[1, 'asc']],
        "columnDefs": [
             {"targets" :[0] , 'orderable' : false}
          ]
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddAddListBarangAll').show();

      $("#form").modal('toggle')
      }})
  }
}



function buttonAddAddListSattax () {

  // let _token = $("#_token").val();
  // let kodecustsupp = $("#input_add_kodepelanggan").val();

  // if (!kodecustsupp) {
  //   alertify.warning("Isi pelanggan terlebih dahulu")
  //   return
  // }

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddAddListSattax').show();

      $("#form").modal('toggle')



}

function buttonAddAddListBarang () {
  console.log('buttonAddAddListBarang','ggggggggggggggggggggggggggggggggggggggggggg')
  let _token = $("#_token").val();
  let nopenyerahan = $("#input_add_add_nopenyerahan").val()
  let refpr = $("#input_add_add_refpr").val()

  console.log(refpr, nopenyerahan)
    if(nopenyerahan || refpr ) {
      console.log("masuk 1")
      if(!refpr || !nopenyerahan) {
        console.log("masuk 1")
        alertify.warning("Lengkapi  refpr dan no penyerahan")

      }
      console.log("masuk 2")
      if ($.fn.DataTable.isDataTable('#tabel_add_list_barangrefpr')) {
       $('#tabel_add_list_barangrefpr').DataTable().destroy();
      }
      console.log(nopenyerahan,
      refpr)
      $.ajax({
        url: "{!! url('solistbarangrefpr') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nopenyerahan: nopenyerahan,
          noreferensi: refpr
        },
        success: function(res) {
          listBarangRefPR = res
          console.log(res)

          if (!res.length) {
            alertify.warning("Data tidak ditemukkan")
            return
          }
          // if (res.length == 1) {
          //   buttonAddAddPickBarangAll( res[0].Kodebrg , 1)
          //   $('#input_add_add_namaproduk').focus();
          //   return
          // }
          let rowTable = ""
          res.forEach((item, i) => {

            rowTable +=          `
            <tr >
            <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangAll('${item.KODEBRG}')" type="button" ><i class="bi bi-plus"></i></button></td>
            <td>${item.NOBUKTI}</td>
              <td>${item.TANGGAL}</td>
              <td>${item.REFPR}</td>
              <td>${item.KODEBRG}</td>
              <td>${item.namaBrg}</td>
              <td>${item.namamerk}</td>
              <td>${item.Sisa1}</td>
              <td>${item.SAT_1}</td>




          </tr>
          `
          });
          // $('#tabel_add_list_barangall').DataTable().destroy();
            console.log ('selesai loaddddddddddddddddddddddd')

          document.getElementById("tabel_data_add_list_barangrefpr").innerHTML = rowTable

             console.log ('selesai eeeeeeeeeeeeeeeeeeeeee')
        $("#tabel_add_list_barangrefpr").DataTable({
          "lengthChange": false,
            "paging": false ,
            "searching" : false,
            "order": [[1, 'asc']],
          "columnDefs": [
               {"targets" :[0] , 'orderable' : false}
            ]
        });

        $('.showhidemodalbodyadd').hide();
        $('#modalBodyAddListBarangRefPR').show();

        $("#form").modal('show')
        }})



      return

    }

  if (!nopenyerahan && !refpr) {
    console.log("masuk barang all")
    $('.showhidemodalbodyadd').hide();
    $('#modalBodyAddAddListBarangAll').show();

    $('#tabel_add_list_barangall').DataTable().destroy();

    document.getElementById("tabel_data_add_list_barangall").innerHTML = ''

    $("#tabel_add_list_barangall").DataTable({
      "lengthChange": false,
        "paging": false ,
        "searching" : false
    });

    document.getElementById("input_search_barang_all").value = ''
    $("#form").modal('toggle')


    document.getElementById("modalBodyAddAddListBarangAllTitle").scrollIntoView();

    $('#form').on('shown.bs.modal', function () {
    $('#input_search_barang_all').trigger('focus')
  })
    // $('#input_search_barang_all').focus();
    console.log("Masuk Tes")

  } else {
    console.log("masuk barang not all")

    $('#tabel_add_list_barang').DataTable().destroy();

    $.ajax({
      url: "{!! url('solistbarang') !!}",
      type: "get",
      async: false,
      data: {
      },
      success: function(res) {
        let rowTable = ``
        dataAddAddListItem = res
        dataAddAddListItem.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarang(${i})" type="button" ><i class="bi bi-plus"></i></button></td>
          <td>${item.Kodebrg}</td>
          <td>${item.NamaBrg}</td>
          <td>${item.namamerk}</td>
          <td>${item.Sat1}</td>


          </tr>`
        });




        if(!res.length) {
          rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
        }
        document.getElementById("tabel_data_add_list_barang").innerHTML = rowTable

        $("#tabel_add_list_barang").DataTable({
          "lengthChange": false,
            "paging": false ,
        });

        $('.showhidemodalbodyadd').hide();
        $('#modalBodyAddAddListBarang').show();

        $("#form").modal('toggle')



      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })


  }




}


function buttonAddAddListRefPr () {

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodepelanggan").val();

  if (!kodecustsupp) {
    alertify.warning("Isi pelanggan terlebih dahulu")
    return
  }
  console.log('buttonAddAddListRefPr' , kodecustsupp)
  $.ajax({
    url: "{!! url('solistrefpr') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp
    },
    success: function(res) {
      console.log('!',res)
      if (!res.length) {
        alertify.warning("Data tidak ditemukkan")
        return
      }
      listRefPR = res
      let rowTable = `<tr class="pick-row" onclick="buttonAddPickRefPr('-')">

      <td>-</td>
      <td>-</td>
      <td>-</td>

      </tr>`

      listRefPR = res

      listRefPR.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickRefPr(${i} )">

        <td>${item.nobukti}</td>
        <td>${item.tanggal}</td>
        <td>${item.refPR}</td>

        </tr>`

        // '
        // <tr>
        // <td> '+ item.nomor + '</td>
        // <td> '+ item.nama + '</td>
        // <td>+ ' + item.alamat + '</td>
        // <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickAlamatKirim( `' + item.nomor + '` , `'+ item.nama + '` , `' + item.alamat +'` )" type="button" ><i class="bi bi-plus"></i></button></td>
        //
        // </tr>'
      });




      $('#tabel_add_list_refpr').DataTable().destroy();
      document.getElementById("tabel_data_add_list_refpr").innerHTML = rowTable
      $("#tabel_add_list_refpr").DataTable({
        "lengthChange": false,
          "paging": false ,
          "order": [[0, 'asc']],
          "searching" : true
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListRefPR').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddAddListNoPenyerahan () {

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodepelanggan").val();
  let refpr = tempRefPR.nobukti

  if (!kodecustsupp ) {
    alertify.warning("Isi RefPR terlebih dahulu")
    return
  }

  console.log(refpr, kodecustsupp)
  $.ajax({
    url: "{!! url('solistnopenyerahan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp,
      refpr
    },
    success: function(res) {
      console.log(res , '@')
      listnopenyerahan = res
      if (!res.length) {
        alertify.warning("Data tidak ditemukkan")
        return
      }
      let rowTable = `<tr class="pick-row" onclick="buttonAddPickNoPenyerahan('-')">

      <td>-</td>
      <td>-</td>

      </tr>`

      listnopenyerahan = res

      listnopenyerahan.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickNoPenyerahan(${i} )">

        <td>${item.NOBUKTI}</td>
        <td>${item.NAMABRG}</td>

        </tr>`

        // '
        // <tr>
        // <td> '+ item.nomor + '</td>
        // <td> '+ item.nama + '</td>
        // <td>+ ' + item.alamat + '</td>
        // <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickAlamatKirim( `' + item.nomor + '` , `'+ item.nama + '` , `' + item.alamat +'` )" type="button" ><i class="bi bi-plus"></i></button></td>
        //
        // </tr>'
      });




      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      // }
      $('#tabel_add_list_nopenyerahan').DataTable().destroy();

      document.getElementById("tabel_data_add_list_nopenyerahan").innerHTML = rowTable
    //   $("#tabel_add_list_nopenyerahan").DataTable({
    //     "lengthChange": false,
    //       "paging": false ,
    //       // "order": [[1, 'asc']],
    //       "searching" : true,
    //       "columnDefs": [
    //            {"targets" :[0] , 'orderable' : false}
    //         ]
    // });
      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListNoPenyerahan').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}



function buttonAddListAlamatKirim () {

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodepelanggan").val();

  if (!kodecustsupp) {
    alertify.warning("Isi pelanggan terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('solistalamatkirim') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp
    },
    success: function(res) {
      if ($.fn.DataTable.isDataTable('#tabel_add_list_alamatkirim')) {
        $('#tabel_add_list_alamatkirim').DataTable().destroy();
      }

      let rowTable = `<tr class="pick-row" onclick="buttonAddPickAlamatKirim('-')">

      <td>-</td>
      <td>-</td>
      <td>-</td>

      </tr>`

      listAlamatKirim = res

      listAlamatKirim.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickAlamatKirim(${i} )">

        <td>${item.nomor}</td>
        <td>${item.nama}</td>
        <td>${item.alamat}</td>

        </tr>`
      });


      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_alamatkirim").innerHTML = rowTable

      $("#tabel_add_list_alamatkirim").DataTable({
        lengthChange: false,
        paging: false,
        searching: true,
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListAlamatKirim').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function buttonAddListLokasiPenerima () {

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodepelanggan").val();

  if (!kodecustsupp) {
    alertify.warning("Isi pelanggan terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('solistlokasipenerima') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp
    },
    success: function(res) {
      if ($.fn.DataTable.isDataTable('#tabel_add_list_lokasipenerima')) {
        $('#tabel_add_list_lokasipenerima').DataTable().destroy();
      }

      let rowTable = `<tr class="pick-row" onclick="buttonAddPickLokasiPenerima('-' , '-' )">

      <td>-</td>
      <td>-</td>

      </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickLokasiPenerima('${item.kodekebun}' , '${item.nama}' )">

        <td>${item.kodekebun}</td>
        <td>${item.nama}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=2>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_lokasipenerima").innerHTML = rowTable

      $("#tabel_add_list_lokasipenerima").DataTable({
        lengthChange: false,
        paging: false,
        searching: true,
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListLokasiPenerima').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}




function buttonAddListValas () {
  $.ajax({
    url: "{!! url('solistvalas') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickValas('${item.kodevls}' , '${item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00'}' )" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.kodevls}</td>
        <td>${item.namavls}</td>
        <td class="text-right">${item.kurs ? formatAngka(parseFloat(item.kurs).toFixed(2)) : '0.00'}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_valas").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListValas').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListPelanggan () {
  console.log('buttonAddListPelanggan')
  $('#tabel_add_list_pelanggan').DataTable().destroy();

  $.ajax({
    url: "{!! url('solistpelanggan') !!}",
    type: "get",
    async: false,
    success: function(res) {

      let rowTable = ``

      res.forEach((item, i) => {

        rowTable += `
        <tr>
          <td class="text-center">
            <button class="btn btn-primary btn-sm"
              style="margin-top:10px;"
              type="button"
              onclick="

                if (!'${item.KodeSls ?? ''}' || !'${item.NamaSales ?? ''}') {
                  alertify.warning('Warning: Sales belum lengkap untuk pelanggan ini');
                }

                if (!'${item.BOffice ?? ''}' || !'${item.NamaBackOffice ?? ''}') {
                  alertify.warning('Warning: Back Office belum lengkap untuk pelanggan ini');
                }

                buttonAddPickPelanggan(
                  '${item.kodecustsupp}',
                  '${item.namacustsupp}',
                  '${item.alamat1}',
                  ${item.PPN},
                  ${item.HARI},
                  '${item.KodeSls ?? ''}',
                  '${item.NamaSales ?? ''}',
                  '${item.BOffice ?? ''}',
                  '${item.NamaBackOffice ?? ''}'
                )
              "
            >
              <i class="bi bi-plus"></i>
            </button>
          </td>

          <td>${item.kodecustsupp}</td>
          <td>${item.namacustsupp}</td>
          <td>${item.alamat1}</td>

          ${
            Number(item.PPN)
              ? '<td class="text-success text-center"><i class="bi bi-check2"></i></td>'
              : '<td class="text-danger text-center"><i class="bi bi-x"></i></td>'
          }
        </tr>`
      });

      document.getElementById("tabel_data_add_list_pelanggan").innerHTML = rowTable

      $("#tabel_add_list_pelanggan").DataTable({
        "lengthChange": false,
        "paging": false,
        "order": [[1, 'asc']],
        "columnDefs": [
          { "targets": [0], "orderable": false }
        ]
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPelanggan').show();
      //$("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}



// function buttonAddListPelanggan () {
//   console.log('1')
//   $('#tabel_add_list_pelanggan').DataTable().destroy();
//   console.log('2')
//   $.ajax({
//     url: "{!! url('solistpelanggan') !!}",
//     type: "get",
//     async: false,
//     data: {
//
//     },
//     success: function(res) {
//       let rowTable = ``
//       res.forEach((item, i) => {
//         rowTable += `
//         <tr>
//         <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:10px;" onclick="buttonAddPickPelanggan('${item.kodecustsupp}' , '${item.namacustsupp}' , '${item.alamat1}' , ${item.PPN} , ${item.HARI})" type="button" ><i class="bi bi-plus"></i></button></td>
//
//         <td>${item.kodecustsupp}</td>
//         <td>${item.namacustsupp}</td>
//         <td>${item.alamat1}</td>
//
//         ${Number(item.PPN)  ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>'
//         :
//         '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'}
//
//
//         </tr>`
//       });
//
//
//
//
//       if(!res.length) {
//         rowTable= ``
//       }
//       document.getElementById("tabel_add_list_pelanggan").innerHTML = rowTable
//     //   $("#tabel_add_list_pelanggan").DataTable({
//     //     "lengthChange": false,
//     //       "paging": false ,
//     //       "order": [[1, 'asc']],
//     //       "columnDefs": [
//     //            {"targets" :[0] , 'orderable' : false}
//     //         ]
//     // });
//     console.log('2')
//       $('.showhidemodalbodyadd').hide();
//       console.log('2')
//       $('#modalBodyAddListPelanggan').show();
//       $("#form").modal('toggle')
//
//     },
//     error: function (err) {
//       console.log(err)
//       alertify.warning('Terjadi kesalahan silahkan refresh browser')
//     }
//
//   })
//
//
// }

function buttonAddListBackOffice () {
  $.ajax({
    url: "{!! url('solistbackoffice') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickBackOffice('${item.keynik}' , '${item.fullname}')" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.keynik}</td>
        <td>${item.fullname}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_backoffice").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListBackOffice').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}




function buttonAddListSales () {
  $('#tabel_add_list_sales').DataTable().destroy();
  $.ajax({
    url: "{!! url('solistsales') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        console.log(item.keynik)
        console.log(item.nama)
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSales('${item.keynik}' , '${String(item.nama)}')" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.keynik}</td>
        <td>${item.nama}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable
      $("#tabel_add_list_sales").DataTable({
        "lengthChange": false,
          "paging": false ,
          "order": [[1, 'asc']],
          "columnDefs": [
               {"targets" :[0] , 'orderable' : false}
            ]
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListSales').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function onChangeInputAddAddNosat () {
  console.log('onChangeInputAddAddNosat')
  let _token  = $("#_token").val()
  let nosat = $("#input_add_add_nosat").val()
  console.log(nosat)
  console.log(Number(nosat))
  let kodebarang = $("#input_add_add_kodebarang").val()

  if (!kodebarang) {
    return
  }

  console.log("kodecust:", $("#input_add_kodepelanggan").val())
  console.log({
  kodebarang: tempAddEdit.KodeBrg,
  kodecustsupp: tempAddEdit.KODECUST,
  kodekebun: $("#input_kodekebun").val() || tempAddEdit.KODEKEBUN
  })

  $.ajax({
    url: "{!! url('socekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang ,
      nosat,
      kodecustsupp: $("#input_add_kodepelanggan").val(),
      kodekebun: $("#input_kodekebun").val() || tempAddEdit.KODEKEBUN
    },
    success: function(res) {
      console.log("BELI RAW:", res.harga_beli)
      console.log(res)
      let jual = res.harga_jual || []
      let beli = res.harga_beli || []

      let rowTable = ``
      let rowTableBeli = ``

      jual.forEach((item, i) => {
        let date1 = ""
        if (item.tanggal) {
            let date = new Date(item.tanggal);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${date1}</td>
          <td class="text-right">${item.qnt2 ?? '-'}</td>
          <td class="text-center">${item.satuan ?? '-'}</td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.harga) || 0)}
          </td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.discrp1) || 0)}
          </td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.disctot) || 0)}
          </td>
        </tr>`})

        if(!jual.length) {
          rowTable= `<tr><td class="text-center" colspan=6>Tidak ada data</td></tr>`
        }

        beli.forEach((item) => {
        let date1 = ""
        if (item.tanggal) {
          let date = new Date(item.tanggal);
          let day = ("0" + date.getDate()).slice(-2);
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          date1 = date.getFullYear()+"/"+month+"/"+day;
        }

        rowTableBeli += `
        <tr>
          <td>${date1}</td>
          <td class="text-right">${item.qntterima ?? '-'}</td>
          <td class="text-center">${item.satuan ?? '-'}</td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.harga) || 0)}
          </td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.ndiskon) || 0)}
          </td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.disctot) || 0)}
          </td>
        </tr>`
      })

      if (!beli.length) {
        rowTableBeli = `<tr><td class="text-center" colspan=6>Tidak ada data</td></tr>`
      }

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable
      document.getElementById("tabel_data_add_harga_beli").innerHTML = rowTableBeli

      // let rowTable = ``
      // res.forEach((item, i) => {
      //   rowTable += `
      //   <tr>
      //   <td>${item.keynik}</td>
      //   <td>${item.nama}</td>
      //   <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSales('${item.keynik}' , '${item.nama}')" type="button" ><i class="bi bi-plus"></i></button></td>
      //
      //   </tr>`
      // });
      //
      //
      //
      //
      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      // }
      // document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable
      if (tipeformitem == 'add') {
        console.log(tempAddAdd[`Hrg${nosat}_1`])
        if (res.length && res[0].Xharga) {
          console.log('if1')
          document.getElementById("input_add_add_harga").value = res[0].Xharga
        } else {
          console.log('else1')
          if (tempAddAdd[`Hrg${nosat}_1`]) {
            console.log('if2')
            document.getElementById("input_add_add_harga").value = tempAddAdd[`Hrg${nosat}_1`]
          } else {
            console.log('else2')
            document.getElementById("input_add_add_harga").value = '0.00'
          }
        }
      } else {

      }


      // if (res.length && res[0].Xharga) {
      //   document.getElementById("input_add_add_harga").value = res[0].Xharga
      // } else {
      //   if ( nosat == 1) {
      //     if (tempAddAdd.Hrg1_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg1_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      //   if ( nosat == 2) {
      //     if (tempAddAdd.Hrg2_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg2_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      //   if ( nosat == 3) {
      //     if (tempAddAdd.Hrg3_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg3_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      // }


    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })



}


function loadAll () {
  console.log('loadall tesaiu')
  let _token = $("#_token").val();
  let level = $("#level").val()
  let tglawal = $("#input_tanggalawal").val()
  let tglakhir = $("#input_tanggalakhir").val()
  let filterso = $("#input_filterso").val()
  let tipebayar = $("#input_tipebayar").val()
  let needoto = 0
  let cbdneedopen = 0
  // if (filterso == 0) {
  //   needoto = 0
  //   cbdneedopen = 0
  // } else if (filterso == 1) {
  //   cbdneedopen = 0
  //   needoto = 1
  // } else if (filterso == 2) {
  //   cbdneedopen = 0
  //   needoto = 0
  // } else if (filterso == 3) {
  //   cbdneedopen = 1
  // }
  let ketproses = 'B'
  let tipefilter = 0
  let ketclose = 0
//   - belum proses --   ketproses = 'B'
// - proses Sebagian   ketproses = 'S'
// - full supply       ketproses = 'F'
  if (filterso == 0) {
    tipefilter = 1

  } else if (filterso == 1) {
    tipefilter = 2
    needoto = 1
  } else if (filterso == 2) {
    tipefilter = 2
    needoto = 0
  } else if (filterso == 3) {
    tipefilter = 3
    ketproses = 'B'
  } else if (filterso == 4) {
    tipefilter = 3
    ketproses = 'S'
  }else if (filterso == 5) {
    tipefilter = 3
    ketproses = 'F'
  }else if (filterso == 6) {
    tipefilter = 4
    ketclose = 1
  }

  let dataRefreshOutstanding = []
      let dataRefreshOutstanding7 = []
      let dataRefreshOutstanding3 = []
      let dataRefreshOutstanding5 = []
      console.log({tglawal,
      tglakhir,
      tipefilter,
      needoto,
      cbdneedopen})
  $.ajax({
    url: "{!! url('soloadall') !!}",
    type: "post",
    async: false,
    data: {

        _token,
        tglawal,
        tglakhir,
        filterso,
        needoto,
        cbdneedopen,
        tipebayar
    },
    success: function(res) {
      console.log('res loadall')
      console.log(res)
      dataRefreshOutstanding = res.tempOutstanding1
      dataRefreshOutstanding3 = res.tempOutstanding3
      dataRefreshOutstanding5 = res.tempOutstanding5
      dataRefreshOutstanding7 = res.tempOutstanding7

    }})

    lastTabelRows = dataRefreshOutstanding;
    lastTabel7Rows = dataRefreshOutstanding7;

    // Only the tab the user is actually looking at gets redrawn immediately -- the other
    // is left marked soPerluGambar and only costs a redraw once its tab is actually opened
    // (see the shown.bs.tab handlers). Port of purchaseOrder.blade.php's own
    // loadAll()/poPerluGambar lazy-draw split.
    var visibleKey = activeVisibleTabKey();
    if (visibleKey === 'tabel') {
      reinitTabel();
      soPerluGambar.tabel7 = true;
    } else {
      reinitTabel7();
      soPerluGambar.tabel = true;
    }

    // #tabel2/#tabel_oto have no nav-tab button to defer to -- always refreshed in place.
    lastTabel2Rows = dataRefreshOutstanding3;
    reinitTabel2();
    lastTabelOtoRows = dataRefreshOutstanding5;
    reinitTabelOto();

    buttonFilterSO()

    // reinitTabel()/reinitTabel2()/reinitTabelOto() above may have rebound the
    // interactive engine's DOM to a table other than the one actually visible --
    // restore it to match what the user is looking at.
    HeaderEngine.activateEngineData(visibleKey);
    HeaderEngine.bindEngineDom(visibleKey);
}


function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('sodetailCetak') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {
        console.log(res)

        dataPrint = res
        console.log(res[0])
        console.log(res[0][0])

        // console.log(res[0][0].IsOtorisasi1)
      }
    })

    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=8) {
      let tempArray = dataPrint.slice(i,i+8)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0].split('-').reverse().join('-');
    let tanggalJthTempo = dataPrint[0].TglJatuhTempo.split(' ')[0].split('-').reverse().join('-');

    css = `<style type="text/css">
      body {
        font-family: sans-serif;
        font-size: 11px !important;
      }

      table {
        margin: 20px auto;
        border-collapse: collapse;
      }

      table th,
      table td {
        border: 1px solid #3c3c3c;
        height: 24px;
        padding: 1px 5px 0px;
        overflow: hidden;
      }

      a {
        background: blue;
        color: #fff;
        padding: 8px 10px;
        text-decoration: none;
        border-radius: 2px;
      }

      .ttd-place {
        height: 80px;
        text-align: center;
      }

      #ttd {
        width: 1000px;
        border: none;
      }

      .ttd-header {
        padding-top: 40px;
      }

      .body-main-print {
        padding: 1rem;
        padding-top: 1rem;

      }

      .header-ba {
        margin-bottom: 2rem;
        text-decoration: underline;
        margin-top: 2rem;
      }

      .detail-spb-table {
        margin: 0;
      }

      .no-border {
        border: none;
      }

      .detail-ba-div {
      }

      .vertical-align-baseline {
        vertical-align: baseline;
      }

      .mt-2rem {
        margin-top: 2rem;
      }

      .mb-3 {
        margin-bottom: 0.5rem;
      }

      .fw-bold {
        font-weight: bold;
      }

      .mb-1 {
        margin-bottom: 0.25rem;
      }

      .mb-2 {
        margin-bottom: 0.5rem;
      }

      .mb-3 {
        margin-bottom: 1rem;
      }

      .mb-4 {
        margin-bottom: 1.5rem;
      }

      .mb-5 {
        margin-bottom: 3rem;
      }

      .mt-1 {
        margin-top: 0.25rem;
      }

      .mt-2 {
        margin-top: 0.5rem;
      }

      .mt-3 {
        margin-top: 1rem;
      }

      .mt-4 {
        margin-top: 1.5rem;
      }

      .mt-5 {
        margin-top: 3rem;
      }

      .ms-1 {
        margin-left: 0.25rem;
      }

      .ms-2 {
        margin-left: 0.5rem;
      }

      .ms-3 {
        margin-left: 1rem;
      }

      .ms-4 {
        margin-left: 1.5rem;
      }

      .ms-5 {
        margin-left: 3rem;
      }

      .me-1 {
        margin-right: 0.25rem;
      }

      .me-2 {
        margin-right: 0.5rem;
      }

      .me-3 {
        margin-right: 1rem;
      }

      .me-4 {
        margin-right: 1.5rem;
      }

      .me-5 {
        margin-right: 3rem;
      }

      .my-1 {
        margin-top: 0.25rem;
        margin-bottom: 0.25rem;
      }

      .my-2 {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
      }

      .my-3 {
        margin-top: 1rem;
        margin-bottom: 1rem;
      }

      .my-4 {
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
      }

      .my-5 {
        margin-top: 3rem;
        margin-bottom: 3rem;
      }

      .pb-1 {
        padding-bottom: 0.25rem;
      }

      .pb-2 {
        padding-bottom: 0.5rem;
      }

      .pb-3 {
        padding-bottom: 1rem;
      }

      .pb-4 {
        padding-bottom: 1.5rem;
      }

      .pb-5 {
        padding-bottom: 3rem;
      }

      .pt-1 {
        padding-top: 0.25rem;
      }

      .pt-2 {
        padding-top: 0.5rem;
      }

      .pt-3 {
        padding-top: 1rem;
      }

      .pt-4 {
        padding-top: 1.5rem;
      }

      .pt-5 {
        padding-top: 3rem;
      }

      .ps-0 {
        padding-left: 0;
      }

      .ps-1 {
        padding-left: 0.25rem;
      }

      .ps-2 {
        padding-left: 0.5rem;
      }

      .ps-3 {
        padding-left: 1rem;
      }

      .ps-4 {
        padding-left: 1.5rem;
      }

      .ps-5 {
        padding-left: 3rem;
      }

      .pe-1 {
        padding-right: 0.25rem;
      }

      .pe-2 {
        padding-right: 0.5rem;
      }

      .pe-3 {
        padding-right: 1rem;
      }

      .pe-4 {
        padding-right: 1.5rem;
      }

      .pe-5 {
        padding-right: 3rem;
      }

      .py-1 {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
      }

      .py-1-5 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-3 {
        padding-top: 1rem;
        padding-bottom: 1rem;
      }

      .py-4 {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
      }

      .py-5 {
        padding-top: 3rem;
        padding-bottom: 3rem;
      }

      .px-1 {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
      }

      .px-1-5 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-2 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-3 {
        padding-left: 1rem;
        padding-right: 1rem;
      }

      .px-4 {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
      }

      .px-5 {
        padding-left: 3rem;
        padding-right: 3rem;
      }

      .text-left {
        text-align: left;
      }

      .text-center {
        text-align: center;
      }

      .text-right {
        text-align: right;
      }

      .text-decoration-underline {
        text-decoration: underline;
      }

      ul {
        margin: 0;
        padding-left: 10px;
      }

      .note {
        width: 75%;
      }

      .w-15 {
        width: 16%;
      }

      .w-25 {
        width: 30%;
      }

      .w-10 {
        width: 4%;
      }

      .w-1 {
        width: 1%;
      }

      .m-0 {
        margin: 0;
      }

      .body-main-prints {
        width: 21cm;
        height: 14cm;
        position: relative;
      }

      .footer-sign {
        padding-top: 5px;
        position: absolute;
        width: 100%;
        bottom: 12px;
      }

      .footer-print-date {
        width: 100%;
        bottom: 5px;
        position: absolute;
      }

       .solid{
        border-left: 0px red solid;
        height: 225px;
        width: 0px;
        display: inline-block;
        padding-left: 0px;
        }

      </style>`;
        hdr = `<div style="width:100%; font-family:sans-serif; font-size:11px;">

          <!-- HEADER ATAS -->
          <div style="display:flex; justify-content:space-between; width:100%;">

            <!-- KIRI -->
            <div style="width:55%;">
              <div style="display:flex;">
                <div style="width:15%; margin-top:10px;">
                  ${imageContent}
                </div>
                <div style="width:85%; padding-left:10px;">
                  <h2 style="margin:0;">CV. SINAR MAHAKAM LESTARI</h2>
                  <div>JL. AMPERA PERGUDANGAN MANGKUPALAS BISNIS CENTRE BLOK D NO.18</div>
                  <div>RT.022 SIMPANG PASIR PALARAN SAMARINDA</div>
                  <div>Telp: (0541) 4104142 | Fax: (0541) 4104195</div>
                  <div>Email: sml@indo.net.id</div>
                </div>
              </div>
            </div>

            <!-- KANAN -->
            <div style="width:40%;">
              <h2 style="margin:0;">SALES ORDER</h2>

              <div style="display:flex;">
                <div style="width:40%;">Marketing</div>
                <div style="width:5%;">:</div>
                <div style="width:55%;">${dataPrint[0].NamaSls ?? ''}</div>
              </div>

              <div style="display:flex;">
                <div style="width:40%;">No. Bukti SO</div>
                <div style="width:5%;">:</div>
                <div style="width:55%;">${dataPrint[0].NoBukti ?? ''}</div>
              </div>

              <div style="display:flex;">
                <div style="width:40%;">No. PO Customer</div>
                <div style="width:5%;">:</div>
                <div style="width:55%;">${dataPrint[0].NoPO ?? ''}</div>
              </div>

              <div style="display:flex;">
                <div style="width:40%;">Tanggal SO</div>
                <div style="width:5%;">:</div>
                <div style="width:55%;">${tanggalOnly}</div>
              </div>

              <div style="display:flex;">
                <div style="width:40%;">Tanggal Kirim</div>
                <div style="width:5%;">:</div>
                <div style="width:55%;">${tanggalJthTempo}</div>
              </div>

              <div style="display:flex;">
                <div style="width:40%;">DP</div>
                <div style="width:5%;">:</div>
                <div style="width:55%;">${dataPrint[0].DP ? Number(dataPrint[0].DP).toLocaleString('id-ID',{
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                }) : '0.00'}</div>
              </div>
            </div>

          </div>

          <!-- BARIS BAWAH (CUSTOMER + ALAMAT KIRIM) -->
          <div style="display:flex;">

            <!-- CUSTOMER -->
            <div style="width:50%;">
              <div><b>Customer :</b> PT. ${dataPrint[0].namapkp ?? ''}</div>
              <div>${dataPrint[0].Alamat ?? ''}</div>
            </div>

            <!-- ALAMAT KIRIM -->
            <div style="width:50%; margin-left:160px;">
              <div><b>Alamat Kirim :</b></div>
              <div>${dataPrint[0].AlamatKirim ?? '-'}</div>
            </div>

          </div>

          <!-- PIC + PENERIMA -->
          <div style="display:flex; margin-top:5px;">

            <div style="width:50%;">
              <b>PIC Cust :</b> ${dataPrint[0].PIC ?? '-'}
            </div>

            <div style="width:50%; margin-left:160px;">
              <b>Penerima :</b> ${dataPrint[0].Kebun ?? '-'}
            </div>

          </div>

        </div>

        <table style="width:100%; border-collapse:collapse; font-family:sans-serif; font-size:10px;">
            <thead>
              </tr>
                  <tr>
                    <td rowspan="2" class="text-center" style="width: 1%">No.</td>
                    <td rowspan="2" class="text-center" style="width: 12%">KODE BARANG</td>
                    <td rowspan="2" class="text-center" style="width: 35%">NAMA BARANG</td>
                    <td rowspan="2" class="text-center" style="width: 5%">QTY</td>
                    <td rowspan="2" class="text-center" style="width: 5%">SAT</td>
                    <td rowspan="2" class="text-center" style="width: 5%">SAT TAX</td>
                    <td rowspan="2" class="text-center" style="width: 10%">HARGA JUAL</td>
                    <td rowspan="2" class="text-center" style="width: 12%">TOTAL</td>
                    <td rowspan="2" class="text-center" style="width: 2%">ST</td>
                    <td rowspan="2" class="text-center" style="width: 8%">TGL KIRIM</td>
                  </tr>
                </thead> `;

    let z = 0
    let maxRow = 8;
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalJumlah = 0;

    dataPrint.forEach(item => {

      if (item.SubTotal) {
        grandTotalJumlah += Number(item.SubTotal) || 0;
      }

    });
    // end
    tempPrintStr += `<html>
    <head>
      <title></title>
    </head>

    <body onload="window.print()">
      ` + css

      arrayDataPrint.forEach((item, i) => {
        console.log('arrayDataPrint' , i)
        if (i == 0) {

          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:5px">`
        // } else if ( i < 1) {
        //   tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; padding-top:15px; page-break-before: always">`
        } else {
          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px;padding-top:7px; ">`
        }
        tempPrintStr += hdr
        item.forEach((itemSub, j) => {
          tempPrintStr += ``


         tempPrintStr += `
         <tr>
         <td class="text-center"
               style="border-left:1px solid black; border-right:1px solid black; border-bottom:none; width: 1%; ">${z+1}</td>
         <td class="text-align: left"
               style="border-left:1px solid black; border-right:1px solid black; border-bottom:none; width: 12%;  ">${itemSub.KodeBrg ?? ''}</td>
         <td class="text-align: left"
               style="border-left:1px solid black; border-right:1px solid black; border-bottom:none; width: 35%;  ">${itemSub.namabrg ?? ''}</td>
         <td class="text-right"
               style="border-left:1px solid black; border-right:1px solid black; border-bottom:none; width: 5%;  ">${itemSub.Qnt ? parseFloat(itemSub.Qnt).toFixed(2) : ''}</td>
         <td class="text-center"
               style="border-left:1px solid black; border-right:1px solid black; border-bottom:none; width: 5%;  ">${itemSub.Satuan ?? ''}</td>
         <td class="text-center"
               style="border-left:1px solid black; border-right:1px solid black; border-bottom:none; width: 5%;  ">${itemSub.SATTAX ?? ''}</td>
         <td style="border-left:1px solid black; border-right:1px solid black; border-bottom:none; width: 10%; text-align: right;">
            ${itemSub.harga
              ? Number(itemSub.harga).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                })
              : ''}
          </td>
          <td style="border-left:1px solid black; border-right:1px solid black; border-bottom:none; width: 12%; text-align: right;">
            ${itemSub.Jumlah
              ? Number(itemSub.Jumlah).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                })
              : ''}
          </td>
          <td class="text-center"
               style="border-left:1px solid black; border-right:1px solid black; border-bottom:none; width: 2%;  ">${itemSub.SP ?? ''}</td>
          <td class="text-right" style="border-left:1px solid black; border-right:1px solid black; border-bottom:none; width: 8%;">
            ${itemSub.TGLKIRIM ? itemSub.TGLKIRIM.split(' ')[0] : ''}
          </td>
         </tr>`;

           z++;

        });

        // TAMBAHAN
        let sisaRow = maxRow - item.length;

        for (let k = 0; k < sisaRow; k++) {
          tempPrintStr += `
          <tr>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
	    <td style="border-top:none; border-bottom:none;"></td>
	    <td style="border-top:none; border-bottom:none;"></td>
          </tr>`;
        }

        // total berada di paling bawah
        console.log(i, arrayDataPrint.length)
        if(i == arrayDataPrint.length - 1){

        tempPrintStr += `
        <tr>
          <td colspan="6" style="border:1px solid; padding:5px; font-weight:bold;">
          </td>
          <td style="border:1px solid; text-align:left; font-weight:bold;">
            SUB TOTAL
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold;">
          ${Number(dataPrint[0].TJumlah || 0).toLocaleString('id-ID', {minimumFractionDigits:2})}
          </td>
	  <td colspan="2" style="border:1px solid;"></td>
        </tr>

        <!-- DISKON -->
        <tr>
          <td colspan="6" style="border:none;"></td>
          <td style="border:1px solid; text-align:left; font-weight:bold;">
            DISKON
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold;">
            ${Number(dataPrint[0].Tdiskon || 0).toLocaleString('id-ID', {minimumFractionDigits:2})}
          </td>
        </tr>

        <!-- DPP -->
        <tr>
          <td colspan="6" style="border:none;"></td>
          <td style="border:1px solid; text-align:left; font-weight:bold;">
            DPP
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold;">
            ${Number(dataPrint[0].TNDPPRp || 0).toLocaleString('id-ID', {minimumFractionDigits:2})}
          </td>
        </tr>
        <!-- PPN -->
        <tr>
          <td colspan="6" style="border:none;"></td>
          <td style="border:1px solid; text-align:left; font-weight:bold;">
            PPN
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold;">
            ${Number(dataPrint[0].TNPPNRp || 0).toLocaleString('id-ID', {minimumFractionDigits:2})}
          </td>
        </tr>
        <!-- TOTAL -->
        <tr>
          <td colspan="6" style="border:none;"></td>
          <td style="border:1px solid; text-align:left; font-weight:bold;">
            TOTAL
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold;">
            ${Number(dataPrint[0].TNNETRp || 0).toLocaleString('id-ID', {minimumFractionDigits:2})}
          </td>
        </tr>`};
        // end

         tempPrintStr += `</tbody>`;

         tempPrintStr += `</table>

         <div class="footer-sign font-family: sans-serif;
           font-size: 10px ">

         <div class="row mt-3" style="text-align: left;font-family: sans-serif;
         font-size: 12px ">
         <span style="float: left; display: block; clear: left;">
         </span>


         <div style="width:100%; display:flex; font-weight:bold; margin-top:5px;">

          </div>

         </div>


         <div style="display:flex; justify-content:space-between; width:100%; font-family:sans-serif; font-size:10px;">

         <!-- KIRI -->
         <div style="width:50%;">
          <table
             class="detail-spb-table mb-2"
             style="width: 100%; font-family: sans-serif;
             font-size: 10px; margin-top: 50px;">
             <tr>
               <td class="no-border text-center" style="width: 20%">Dibuat Oleh,</td>
               <td class="no-border text-center" style="width: 20%">Disetujui Oleh,</td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
		 <p class="m-0" style="text-align: center;">(........................)</p>
               </td>
               <td class="no-border px-2">
		 <p class="m-0" style="text-align: center;">(........................)</p>
               </td>
             </tr>
           </table>
          </div>

          <!-- KANAN -->
          <div style="width:50%;">
          <table
             class="detail-spb-table mb-2"
             style="width: 100%; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%"></td>
               <td class="no-border text-center" style="width: 20%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
		            <p class="m-0" style="text-align: center;"></p>
               </td>
               <td class="no-border px-2">
               </td>
             </tr>
           </table>
          </div>
        </div>

         </div>


         <div class="footer-print-date">
           <table class="m-0" style="width: 100% ; font-family: sans-serif;
           font-size: 10px ">
             <tr>
               <td class="no-border"></td>
               <td class="no-border text-right">Page ${i+1} of ${arrayDataPrint.length}</td>
             </tr>
           </table>

         </div>`


        tempPrintStr += `</div>`
      });


      tempPrintStr +=  `</body></html>`



    w=window.open(' ')
    w.document.write(tempPrintStr)

    w.print()
    w.close()

  }

function buttonAddAddPickBarangAll (kodebrg , x = 0) {

  let _token  = $("#_token").val()

  $.ajax({
    url: "{!! url('sodetailbarangall') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebrg : kodebrg,
      nosat : 1
    },
    success: function(res) {
      console.log(res)

      if (!res.barang.length) {

        alertify.warning("Terjadi kesalahan pada server")
        return
      }

      tempAddAdd = res.barang[0]
      document.getElementById("input_add_add_kodebarang").value = tempAddAdd.Kodebrg
      document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
      document.getElementById("input_add_add_namaproduk").value = tempAddAdd.NamaBrg
      document.getElementById("input_add_add_satuanproduk").value = tempAddAdd.Sat1 ? tempAddAdd.Sat1 : ''
      document.getElementById("input_add_add_discrp").value = '0.00'
      let selectOption = ''
      if (tempAddAdd.Sat1) {
        selectOption += `<option value=1 selected>${tempAddAdd.Sat1} - ${tempAddAdd.ISI1}</option>`
      }
      if (tempAddAdd.Sat2) {
        selectOption += `<option value=2>${tempAddAdd.Sat2} - ${tempAddAdd.ISI2}</option>`
      }
      if (tempAddAdd.Sat3) {
        selectOption += `<option value=3>${tempAddAdd.Sat3} - ${tempAddAdd.ISI3}</option>`
      }
      document.getElementById("input_add_add_nosat").innerHTML = selectOption


      console.log(res.harga)
      let rowTable = ``
      res.harga.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
        <td>${date1}</td>
        <td>-</td>
        <td>${item.SATUAN}</td>
        <td class="text-right">${Number(item.Xharga) ? parseFloat(item.Xharga).toFixed(2) : '0.00'}</td>
        <td>-</td>
        <td>-</td>

        </tr>`
      });




      if(!res.harga.length) {
        rowTable= `<tr><td class="text-center" colspan=6>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.harga.length && Number(res.harga[0].Xharga)) {
        document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(res.harga[0].Xharga).toFixed(2))
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(tempAddAdd.Hrg1_1).toFixed(2))
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
        }
      }

      buttonAddListBatal(x)
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refres.hargah browser')
    }

  })
}

function buttonAddAddPickBarang (index , pEdit = 0) {
  let _token  = $("#_token").val()
  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]
  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.Kodebrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_disc").value = '0.00'
  document.getElementById("input_add_add_discrp").value = '0.00'
  let selectOption = ''
  if (tempAddAdd.Sat1) {
    selectOption += `<option value=1 selected>${tempAddAdd.Sat1}</option>`
  }
  if (tempAddAdd.Sat2) {
    selectOption += `<option value=2>${tempAddAdd.Sat2}</option>`
  }
  if (tempAddAdd.Sat3) {
    selectOption += `<option value=3>${tempAddAdd.Sat3}</option>`
  }
  document.getElementById("input_add_add_nosat").innerHTML = selectOption


  console.log("kodecust:", $("#input_add_kodepelanggan").val())
  console.log({
  kodebarang: tempAddEdit.KodeBrg,
  kodecustsupp: tempAddEdit.KODECUST,
  kodekebun: $("#input_kodekebun").val() || tempAddEdit.KODEKEBUN
  })

  $.ajax({
    url: "{!! url('socekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.Kodebrg,
      nosat : 1,
      kodecustsupp: $("#input_add_kodepelanggan").val(),
      kodekebun: $("#input_kodekebun").val() || tempAddEdit.KODEKEBUN
    },
    success: function(res) {
      console.log("BELI RAW:", res.harga_beli)
      console.log(res)
      let jual = res.harga_jual || []
      let beli = res.harga_beli || []

      let rowTable = ``
      let rowTableBeli = ``
      jual.forEach((item, i) => {
        let date1 = ""
        if (item.tanggal) {
            let date = new Date(item.tanggal);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${date1}</td>
          <td class="text-right">${item.qnt2 ?? '-'}</td>
          <td class="text-center">${item.satuan ?? '-'}</td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.harga) || 0)}
          </td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.discrp1) || 0)}
          </td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.disctot) || 0)}
          </td>
        </tr>`})

        if(!jual.length) {
          rowTable= `<tr><td class="text-center" colspan=6>Tidak ada data</td></tr>`
        }

        beli.forEach((item) => {
        let date1 = ""
        if (item.tanggal) {
          let date = new Date(item.tanggal);
          let day = ("0" + date.getDate()).slice(-2);
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          date1 = date.getFullYear()+"/"+month+"/"+day;
        }

        rowTableBeli += `
        <tr>
          <td>${date1}</td>
          <td class="text-right">${item.qntterima ?? '-'}</td>
          <td class="text-center">${item.satuan ?? '-'}</td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.harga) || 0)}
          </td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.ndiskon) || 0)}
          </td>
          <td class="text-right">
            ${new Intl.NumberFormat('id-ID').format(Number(item.disctot) || 0)}
          </td>
        </tr>`
      })

      if (!beli.length) {
        rowTableBeli = `<tr><td class="text-center" colspan=6>Tidak ada data</td></tr>`
      }

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable
      document.getElementById("tabel_data_add_harga_beli").innerHTML = rowTableBeli




      if (res.length && Number(res[0].Xharga)) {
        document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(res[0].Xharga).toFixed(2))
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(tempAddAdd.Hrg1_1).toFixed(2))
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
        }
      }

      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })



  // console.log




}


function buttonAddPickRefPr (index ) {
  console.log("buttonAddPickRefPr" , index)
  let _token  = $("#_token").val()


  if (index == '-') {
    document.getElementById("input_add_add_refpr").value = ''
    document.getElementById("input_add_add_nopenyerahan").value = ''
    document.getElementById("input_add_add_kodebarang").value = ''
    document.getElementById("input_add_add_kodebarang").disabled = false
    buttonAddListBatal()
    return
  }
  console.log(listRefPR[index])
  tempRefPR = listRefPR[index]
  console.log(tempRefPR)
  console.log(tempRefPR.refPR)
  document.getElementById("input_add_add_refpr").value = tempRefPR.refPR
  document.getElementById("input_add_add_nopenyerahan").value = ''
  document.getElementById("input_add_add_kodebarang").value = ''
  document.getElementById("input_add_add_kodebarang").disabled = true

  buttonAddListBatal()


}


function buttonAddPickNoPenyerahan (index ) {
  console.log("buttonAddPickNoPenyerahan" , index)
  let _token  = $("#_token").val()


  if (index == '-') {
    document.getElementById("input_add_add_nopenyerahan").value = ''
    document.getElementById("input_add_add_kodebarang").value = ''
    document.getElementById("input_add_add_kodebarang").disabled = false
    buttonAddListBatal()
    return
  }
  console.log(listnopenyerahan[index])
  tempNoPenyerahan = listnopenyerahan[index]
  console.log(tempNoPenyerahan)
  document.getElementById("input_add_add_nopenyerahan").value = tempNoPenyerahan.NOBUKTI
  document.getElementById("input_add_add_kodebarang").value = ''
  document.getElementById("input_add_add_kodebarang").disabled = true

  buttonAddListBatal()


}



function buttonAddPickPelanggan (kode, nama , alamat , ppn , hari, kodeSales = '', namaSales = '', kodeBO = '', namaBO = '' , pmodal = 1 , inputtarget = 0) {
  console.log('buttonAddPickPelanggan')
  console.log(kode,nama,alamat, ppn , hari, kodeSales, namaSales, kodeBO, namaBO,pmodal,inputtarget)
  console.log('ax')
  console.log(inputtarget)
  if (inputtarget) {
    // console.log()
    console.log('a')
document.getElementById("input_tambahso_kodepelanggan").value = kode
document.getElementById("input_tambahso_namapelanggan").value = nama


} else {
document.getElementById("input_add_kodepelanggan").value = kode
document.getElementById("input_add_namapelanggan").value = nama
document.getElementById("input_add_alamatpelanggan").value = alamat
document.getElementById("input_add_pembayaran").value = 0
document.getElementById("input_add_hari").value = Number(hari)
// document.getElementById("input_add_ppn").value = ppn
document.getElementById("input_add_tipeppn").value = ppn
}

  if (Number(hari) > 0) {
    document.getElementById("input_add_pembayaran").value = 1
  }

  if (Number(ppn)) {
    document.getElementById("input_add_tipeppn").innerHTML = `
    <option value=1 selected >Exclude</option>
    <option value=2 >Include</option>
    `

  } else {
    document.getElementById("input_add_tipeppn").innerHTML = `
    <option value=0 selected>None</option>
    `
  }


  if (inputtarget) {

} else {
  document.getElementById("input_add_kodealamatkirim").value = '-'
  document.getElementById("input_add_alamatkirim").value = '-'
  document.getElementById("input_add_kodepic").value = ''
  document.getElementById("input_add_namapic").value = ''
  document.getElementById("input_add_kodelokasipenerima").value = '-'
  document.getElementById("input_add_alamatlokasipenerima").value = '-'
  // SALES
  document.getElementById("input_add_kodesales").value = kodeSales
  document.getElementById("input_add_namasales").value = namaSales

  // BO
  document.getElementById("input_add_kodebackoffice").value = kodeBO
  document.getElementById("input_add_namabackoffice").value = namaBO
}

	if (pmodal) {

  buttonAddListBatal()
}
  setNewNoBukti(ppn)
  // $("#form").modal('toggle')
}


function buttonAddPickAlamatKirim (index) {

  if (index == '-') {
    console.log('buttonAddPickAlamatKirim')
    // console.log(kode,nama,alamat)
    if (tipeform == 'edit') {
      onChangeHeader('NoAlamatKirim' , '-')
      onChangeHeader('AlamatKirim' , '-')
    }

    document.getElementById("input_add_kodealamatkirim").value = '-'
    document.getElementById("input_add_alamatkirim").value = '-'
    buttonAddListBatal()
  } else {
    let itemX = listAlamatKirim[index]
    console.log('buttonAddPickAlamatKirim')
    // console.log(kode,nama,alamat)
    if (tipeform == 'edit') {
      onChangeHeader('NoAlamatKirim' , itemX.nomor)
      onChangeHeader('AlamatKirim' , itemX.alamat)
    }

    document.getElementById("input_add_kodealamatkirim").value = itemX.nomor
    document.getElementById("input_add_alamatkirim").value = itemX.alamat
    buttonAddListBatal()
  }


}

function buttonAddPickLokasiPenerima (kode, nama ) {
  console.log('buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODEKEBUN' , kode)

  }
  document.getElementById("input_add_kodelokasipenerima").value = kode
  document.getElementById("input_add_alamatlokasipenerima").value = nama

  buttonAddListBatal()
  document.getElementById("input_add_kodelokasipenerima").scrollIntoView();
}

function buttonAddPickPIC (index) {
  console.log('buttonAddPickPIC')
  // console.log(kode,nama)
  let pic = listpic[index]
  if (tipeform == 'edit') {
    onChangeHeader('KodePF' , pic.kode)

  }
  document.getElementById("input_add_kodepic").value = pic.kode
  document.getElementById("input_add_namapic").value = pic.nama
  buttonAddListBatal()
}
function buttonAddPickSattax (kode, nama ) {
  console.log('buttonAddPickSattax')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    // onChangeHeader('KodePF' , kode)

  }
  document.getElementById("input_add_add_kodesattax").value = kode
  document.getElementById("input_add_add_sattax").value = nama
  buttonAddListBatal()
}


function buttonAddPickNoPo (pocust, idpocust ) {
  console.log('buttonAddPickPIC')
  // console.log(kode,nama)

  idpocust = idpocust

  if (tipeform == 'edit') {
    onChangeHeader('ppo' , 1)
    onChangeHeader('idpocust' , idpocust)

  }
  document.getElementById("input_add_nopo").value = pocust
  // document.getElementById("input_add_namapic").value = nama
  buttonAddListBatal()
}

function buttonAddPickValas (kode, kurs) {
  console.log('buttonAddPickValas')
  console.log(kode,kurs)
  if (tipeform == 'edit') {
    onChangeHeader('KODEVLS' , kode)
    onChangeHeader('KURS' , kurs)
  }
  document.getElementById("input_add_valas").value = kode
  document.getElementById("input_add_kurs").value = kurs
  buttonAddListBatal()
}

function buttonAddPickSales (kode, nama ) {
  console.log('buttonAddPickSales')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODESLS' , kode)

  }
  document.getElementById("input_add_kodesales").value = kode
  document.getElementById("input_add_namasales").value = nama
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickBackOffice (kode, nama ) {
  console.log('buttonAddPickBackOffice')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('Boffice' , kode)

  }
  document.getElementById("input_add_kodebackoffice").value = kode
  document.getElementById("input_add_namabackoffice").value = nama
  buttonAddListBatal()


  document.getElementById("input_add_kodebackoffice").scrollIntoView();

}



function buttonAddListBatal (x = 0) {
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();
  if (x == 1) {
    return
  }
  $("#form").modal('toggle')
}

function cleanFormAddAdd () {
  document.getElementById("input_add_add_refpr").value = ''
  document.getElementById("input_add_add_kodesattax").value = ''
  document.getElementById("input_add_add_sattax").value = ''
  document.getElementById("input_add_add_nopenyerahan").value = ''
  document.getElementById("input_add_add_kodebarang").value = ''
  document.getElementById("input_add_add_namabarang").value = ''
  document.getElementById("input_add_add_namaproduk").value = ''
  document.getElementById("input_add_add_qty").value = ''
  document.getElementById("input_add_add_nosat").innerHTML = '<option value=0 selected>Pilih Satuan</option>'
  document.getElementById("input_add_add_satuanproduk").value = ''
  document.getElementById("input_add_add_harga").value = '0.00'
  document.getElementById("input_add_add_disc").value = '0.00'
  document.getElementById("input_add_add_discrp").value = '0.00'
  document.getElementById("input_add_add_tambahkepo").value = 0
  document.getElementById("input_add_add_booking").value = 0
  document.getElementById("input_add_add_urgent").value = 0


}

function lockFormAdd () {
  document.getElementById("input_add_tipeppn").disabled = true
  document.getElementById("input_add_pembayaran").disabled = true
  document.getElementById("input_add_dp").disabled = true
  // document.getElementById("input_add_nopo").disabled = true
  document.getElementById("input_add_catatan").disabled = true
  document.getElementById("input_add_tanggalpo").disabled = true
  document.getElementById("input_add_tanggalkirim").disabled = true
  document.getElementById("input_add_hari").disabled = true
  document.getElementById("input_add_draftpo").disabled = true

  // document.getElementById("buttonAddListPelanggan").disabled = true
  document.getElementById("buttonAddListAlamatKirim").disabled = true
  //document.getElementById("buttonAddListSales").disabled = true
  document.getElementById("buttonAddListValas").disabled = true
  document.getElementById("buttonAddListPIC").disabled = true
  document.getElementById("buttonAddListLokasiPenerima").disabled = true
  //document.getElementById("buttonAddListBackOffice").disabled = true

  document.getElementById("input_add_disc").disabled = true
  document.getElementById("input_add_discrp").disabled = true
}

function buttonShowHideHeader () {
  var modal = document.getElementById("modalBodyAddMainHeader");
  console.log($('#modalBodyAddMainHeader').css('display'))
  if($('#modalBodyAddMainHeader').css('display') === 'block') {
    modal.style.display = "none";
  } else {
    modal.style.display = "block";
  }



}

function buttonShowHideHeaderDetail () {
  var modal = document.getElementById("modalBodyDetailMainHeader");
  console.log($('#modalBodyDetailMainHeader').css('display'))
  if($('#modalBodyDetailMainHeader').css('display') === 'block') {
    modal.style.display = "none";
  } else {
    modal.style.display = "block";
  }

}

function unlockFormAdd () {
  document.getElementById("input_add_tipeppn").disabled = false
  document.getElementById("input_add_pembayaran").disabled = false
  document.getElementById("input_add_dp").disabled = false
  // document.getElementById("input_add_nopo").disabled = false
  document.getElementById("input_add_catatan").disabled = false
  document.getElementById("input_add_tanggalpo").disabled = false
  document.getElementById("input_add_tanggalkirim").disabled = false
  document.getElementById("input_add_hari").disabled = false
  document.getElementById("input_add_draftpo").disabled = false

  // document.getElementById("buttonAddListPelanggan").disabled = false
  document.getElementById("buttonAddListAlamatKirim").disabled = false
  //document.getElementById("buttonAddListSales").disabled = false
  document.getElementById("buttonAddListValas").disabled = false
  document.getElementById("buttonAddListPIC").disabled = false
  document.getElementById("buttonAddListLokasiPenerima").disabled = false
  //document.getElementById("buttonAddListBackOffice").disabled = false

  document.getElementById("input_add_disc").disabled = false
  document.getElementById("input_add_discrp").disabled = false
}

function cleanFormAdd () {
  document.getElementById("input_add_tanggalpo").valueAsDate = new Date()
  document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  document.getElementById("input_add_kodepelanggan").value = ''
  document.getElementById("input_add_namapelanggan").value = ''
  document.getElementById("input_add_alamatpelanggan").value = ''
  document.getElementById("input_add_kodealamatkirim").value = '-'
  document.getElementById("input_add_alamatkirim").value = '-'
  document.getElementById("input_add_kodepic").value = ''
  document.getElementById("input_add_namapic").value = ''
  document.getElementById("input_add_kodelokasipenerima").value = '-'
  document.getElementById("input_add_alamatlokasipenerima").value = '-'
  document.getElementById("input_add_catatan").value = ''
  document.getElementById("input_add_valas").value = ''
  document.getElementById("input_add_kurs").value = 'IDR'
  document.getElementById("input_add_dp").value = '0.00'
  document.getElementById("input_add_nopo").value = ''
  document.getElementById("input_add_kodebackoffice").value = ''
  document.getElementById("input_add_namabackoffice").value = ''
  document.getElementById("input_add_tipeppn").value = 0
  document.getElementById("input_add_pembayaran").value = 0
  document.getElementById("input_add_kodesales").value = ''
  document.getElementById("input_add_namasales").value = ''
  document.getElementById("input_add_hari").value = 0
  document.getElementById("input_add_draftpo").value = 0

  document.getElementById("input_add_tipeppn").disabled = false
  document.getElementById("input_add_pembayaran").disabled = false
  document.getElementById("input_add_dp").disabled = false
  // document.getElementById("input_add_nopo").disabled = false
  document.getElementById("input_add_catatan").disabled = false
  document.getElementById("input_add_tanggalpo").disabled = false
  document.getElementById("input_add_tanggalkirim").disabled = false
  document.getElementById("input_add_hari").disabled = false
  document.getElementById("input_add_draftpo").disabled = false

  document.getElementById("input_add_kodepelanggan").disabled = true
  document.getElementById("buttonAddListAlamatKirim").disabled = false
  //document.getElementById("buttonAddListSales").disabled = false
  document.getElementById("buttonAddListValas").disabled = false
  document.getElementById("buttonAddListPIC").disabled = false
  document.getElementById("buttonAddListLokasiPenerima").disabled = false
  //document.getElementById("buttonAddListBackOffice").disabled = false

  document.getElementById("input_add_disc").disabled = false
  document.getElementById("input_add_discrp").disabled = false

  document.getElementById("input_add_disc").value = '0.00'
  document.getElementById("input_add_discrp").value = '0.00'
  document.getElementById("input_add_ppn").value = '0.00'
  document.getElementById("input_add_dpp").value = '0.00'
  document.getElementById("input_add_grandtotal").value = '0.00'
}

function buttonEdit (NOBUKTI ) {

let pcekglobal = 0
  // $.ajax({
  //   url: "{!! url('ceklockperiode') !!}",
  //   type: "get",
  //   async: false,
  //   data: {
  //   },
  //   success: function(res) {
  //     if (res.length ) {
  //       pcekglobal = 1
  //     }
  //   },
  //   error: function (err) {
  //     console.log(err)
  //     alertify.warning('Terjadi kesalahan silahkan refresh browser')
  //   }
  //
  // })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return
}

  tipeform = 'edit'
  console.log('buttonEdit' , NOBUKTI)

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').show();
  unlockFormAdd()

  document.getElementById("input_add_nopo").disabled = true
  document.getElementById("input_add_kodepelanggan").disabled = true
  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let _token  = $("#_token").val()
  let oto = 1

  $.ajax({
    url: "{!! url('socekotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      oto = res[0].isOtorisasi1




    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

  if (oto == 1) {
    alertify.warning("Sudah diotorisasi")
    return
  }



  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListPelanggan').show();
  $('#modalBodyAddMain').show();
  console.log("-------------------------===")
  refreshDataTableAdd(NOBUKTI)
  // let _token = $("#_token").val();
  let bulan = new Date(dataHeaderAdd.Tanggal).getMonth() + 1
  let tahun = new Date(dataHeaderAdd.Tanggal).getFullYear()
  console.log(bulan, tahun)
  $.ajax({
    url: "{!! url('ceklockperiodeinput') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      bulan,
      tahun,
    },
    success: function(res) {
      console.log(res)
      if (res.length ) {
        pcekglobal = 1
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
  if (pcekglobal) {
    alertify.warning("Periode sudah dikunci")
    return
  }

  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
}


function buttonAdd () {


let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {


    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1

      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return

}

  $('#divhargaterakhir').hide();
  idpocust = 0
  tipeform = 'add'
  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').hide();
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  dataTableAdd = []
  cleanFormAdd()
   document.getElementById("input_add_nopo").disabled = false
   document.getElementById("input_add_kodepelanggan").disabled = false
  // $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListPelanggan').show();
  // $('#modalBodyAddMain').show();


  document.getElementById("input_add_nobukti").value = ''
  // setNewNoBukti()

  refreshDataTableAdd()
  document.getElementById("input_add_valas").value = 'IDR'
  document.getElementById("input_add_kurs").value = '1.00'
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
  $('#modalBodyAddMainHeader').show();
  // lockFormAdd()
}

function buttonCloseForm () {
  $('#page4').hide();
  $('#page3').hide();
  $('#page2').hide();
  $('#page1').show();

}

function buttonCloseFormDetail () {
  $('#page3').hide();
  $('#page1').show();

}




function submitAdd () {

  // document.getElementById("input_add_tanggalpo").valueAsDate = new Date()
  let alamatpelanggan = $("#input_add_alamatpelanggan").val();
  console.log(alamatpelanggan)
  let catatan = $("#input_add_catatan").val();
  console.log(catatan)

  // return


}



function buttonAddMainHeader() {
  $('.showhidemodalbodyaddmain').hide();
  $('#modalBodyAddMainHeader').show();
  // $('#buttonAddListPelanggan').hide();
}

function buttonAddMainItems() {
  $('.showhide').hide();
  $('.showhidemodalbodyaddmain').hide();
  $('#modalBodyAddMainItems').show();
}

function buttonDetailMainHeader() {
  $('.showhidemodalbodydetailmain').hide();
  $('#modalBodyDetailMainHeader').show();
  // $('#buttonDetailListPelanggan').hide();
}

function buttonDetailMainItems() {
  $('.showhide').hide();
  $('.showhidemodalbodydetailmain').hide();
  $('#modalBodyDetailMainItems').show();
}

function buttonDetail (NOBUKTI) {
  console.log('buttonDetail' , NOBUKTI)
  // $('.showhide').hide();
  // $('.showhidemodalbodydetailmain').hide();

  let _token  = $("#_token").val()


  $.ajax({
    url: "{!! url('sogetdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log('aaa')
      console.log('res' , res)

      // res.header.forEach((item, i) => {
      //   console.log('a' , i)
      // });
      //
      // res.list.forEach((item, i) => {
      //   console.log('b' , i)
      // });

      if (!res.list) {
        alertify.warning("Data habis")
        // $("#form").modal('toggle')
        return
      } else {
        let dataHeaderDetail = res.header[0]
        let dataTableDetail = res.list
        let nobukti = dataHeaderDetail.NoBukti;

        console.log('IsOtorisasi:', dataHeaderDetail.isotorisasi1);

        if (Number(dataHeaderDetail.isotorisasi1) === 1) {
          $('#btnOtorisasiDetail')
            .removeClass('btn-primary')
            .addClass('btn-danger')
            .text('Batal Otorisasi')
            .attr('onclick', `buttonBatalOtorisasi('${nobukti}')`);
        } else {
          $('#btnOtorisasiDetail')
            .removeClass('btn-danger')
            .addClass('btn-primary')
            .text('Otorisasi')
            .attr('onclick', `buttonOtorisasi('${nobukti}')`);
        }

        let rowTable = ""
        dataTableDetail.forEach((item, i) => {
          rowTable += `<tr>
          <td>${item.KodeBrg}</td>
          <td>${item.NamaBrg}</td>
          <td>${item.namabrgalias}</td>
          <td>${item.namamerk}</td>
          <td class="text-right">${item.Qnt ? parseFloat(item.Qnt).toFixed(2) : '0.00'}</td>
          <td>${item.Satuan}</td>
          <td>${item.NAMATAX ? item.NAMATAX : ''}</td>
          <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
          <td class="text-right">${item.DiscRp1 ? formatAngka(parseFloat(item.DiscRp1).toFixed(2)) : '0.00'}</td>
          <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
          <td>${item.noserah ? item.noserah : ''}</td>

          </tr>`
        });

        if(!dataTableDetail.length) {
          rowTable = `<tr>
          <td class="text-center" colspan="5">Belum ada barang</td>
          </tr>`
        }
        document.getElementById("tabel_data_detail").innerHTML = rowTable
        document.getElementById("input_detail_nobukti").value = dataHeaderDetail.NoBukti
        document.getElementById("input_detail_namapelanggan").value = dataHeaderDetail.NamaCust
        document.getElementById("input_detail_kodepelanggan").value = dataHeaderDetail.KodeCUST
        document.getElementById("input_detail_alamatpelanggan").value = dataHeaderDetail.ALAMAT
        console.log('a')
        document.getElementById("input_detail_kodesales").value = dataHeaderDetail.kodesls
        document.getElementById("input_detail_namasales").value = dataHeaderDetail.NamaSls
        document.getElementById("input_detail_kodepic").value = dataHeaderDetail.kodePF
        document.getElementById("input_detail_namapic").value = dataHeaderDetail.NamaPF
        document.getElementById("input_detail_valas").value = dataHeaderDetail.KodeVls
        document.getElementById("input_detail_kurs").value = dataHeaderDetail.Kurs
        console.log('b')
        document.getElementById("input_detail_kodebackoffice").value = dataHeaderDetail.Boffice
        document.getElementById("input_detail_namabackoffice").value = dataHeaderDetail.NamaBoFFice
        document.getElementById("input_detail_dp").value = dataHeaderDetail.DP ? parseFloat(dataHeaderDetail.DP).toFixed(2) : '0.00'
        document.getElementById("input_detail_catatan").value = dataHeaderDetail.Catatan
        console.log('c')
        document.getElementById("input_detail_kodealamatkirim").value = dataHeaderDetail.NoAlamatKirim
        console.log('d')
        document.getElementById("input_detail_alamatkirim").value = dataHeaderDetail.AlamatKirim
        document.getElementById("input_detail_kodelokasipenerima").value = dataHeaderDetail.KODEKEBUN
        document.getElementById("input_detail_alamatlokasipenerima").value = dataHeaderDetail.NAMAKEBUN
        document.getElementById("input_detail_nopo").value = dataHeaderDetail.NoPesanan
        document.getElementById("input_detail_hari").value = dataHeaderDetail.Hari
        document.getElementById("input_detail_pembayaran").value = dataHeaderDetail.TipeBayar
        document.getElementById("input_detail_ppn").value = dataHeaderDetail.PPN
        document.getElementById("input_detail_draftpo").value = dataHeaderDetail.PPO
        document.getElementById("input_detail_tanggal").value = formatDate(dataHeaderDetail.Tanggal)
        document.getElementById("input_detail_tanggalpo").value = formatDate(dataHeaderDetail.TglPO)
        document.getElementById("input_detail_tanggalkirim").value = formatDate(dataHeaderDetail.TglKirim)

        document.getElementById("input_detail_disc").value = parseFloat(dataHeaderDetail.Disc).toFixed(2)
        document.getElementById("input_detail_discrp").value = parseFloat(dataHeaderDetail.TotDiskon).toFixed(2)
        document.getElementById("input_detail_dpp").value = formatAngka(parseFloat(dataHeaderDetail.TotDPP).toFixed(2))
        document.getElementById("input_detail_ppn").value = formatAngka(parseFloat(dataHeaderDetail.TotPPN).toFixed(2))
        document.getElementById("input_detail_grandtotal").value = formatAngka(parseFloat(dataHeaderDetail.TotNet).toFixed(2))
        console.log(dataHeaderDetail.PPN)
        if (Number(dataHeaderDetail.PPN)) {

          document.getElementById("input_detail_tipeppn").innerHTML = `
          <option value=1  >Exclude</option>
          <option value=2 >Include</option>
          `

        } else {
          document.getElementById("input_detail_tipeppn").innerHTML = `
          <option value=0 >None</option>
          `
        }

        document.getElementById("input_detail_tipeppn").value = dataHeaderDetail.PPN
      }

      // $('.showhidemodalbodydetail').hide();
      // $('#modalBodyAddListPelanggan').show();
      // $('#modalBodyDetailMain').show();
      // setNewNoBukti()

      // refreshDataTableAdd()
      // $("#formDetail").modal('toggle')
      $('#page1').hide();
      $('#page3').show();



    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })




}




function refreshDataTableAdd (NOBUKTI = "") {
  console.log('refereshdatatableadd' , NOBUKTI)
  console.log('x')
  console.log('refreshDataTableAdd' , NOBUKTI)
  if (!NOBUKTI) {


    // if(!dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="12">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()


    console.log("masuk so get detail")
    $.ajax({
      url: "{!! url('sogetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: NOBUKTI
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)

        // res.header.forEach((item, i) => {
        //   console.log('a' , i)
        // });
        if (!res.list.length) {
          alertify.warning("Data habis")
          //  $("#form").modal('toggle')
          $('#page3').hide();
          $('#page2').hide();
          $('#page1').show();
        } else {
          console.log("cini")
          dataHeaderAdd = res.header[0]
          console.log(dataHeaderAdd)
          dataTableAdd = res.list

          let rowTable = ""
          dataTableAdd.forEach((item, i) => {
            console.log(item , 'ini')
            console.log(item.xSP)

            rowTable += `<tr>
            <td>${item.KodeBrg}</td>
            <td>${item.NamaBrg}</td>
            <td>${item.namabrgalias}</td>
            <td>${item.namamerk}</td>
            <td class="text-right">${item.Qnt ? parseFloat(item.Qnt).toFixed(2) : '0.00'}</td>
            <td>${item.Satuan}</td>
            <td>${item.NAMATAX ? item.NAMATAX : ''}</td>
            <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
            <td class="text-right">${item.DiscRp1 ? formatAngka(parseFloat(item.DiscRp1).toFixed(2)) : '0.00'}</td>
            <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
            <td>${item.noserah ? item.noserah : ''}</td>
            <td class="text-center">
            <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
             <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button></td>
            </tr>`
          });

          if(!dataTableAdd.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="12">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_add").innerHTML = rowTable

          document.getElementById("input_add_nobukti").value = dataHeaderAdd.NoBukti
          document.getElementById("input_add_namapelanggan").value = dataHeaderAdd.NamaCust
          document.getElementById("input_add_kodepelanggan").value = dataHeaderAdd.KodeCUST
          document.getElementById("input_add_alamatpelanggan").value = dataHeaderAdd.ALAMAT
          document.getElementById("input_add_kodesales").value = dataHeaderAdd.kodesls
          document.getElementById("input_add_idpo").value = dataHeaderAdd.idpocust
          console.log("id po cust")
          console.log(dataHeaderAdd.idpocust)

          document.getElementById("input_add_namasales").value = dataHeaderAdd.NamaSls
          document.getElementById("input_add_kodepic").value = dataHeaderAdd.kodePF
          document.getElementById("input_add_namapic").value = dataHeaderAdd.NamaPF
          document.getElementById("input_add_valas").value = dataHeaderAdd.KodeVls
          document.getElementById("input_add_kurs").value = dataHeaderAdd.Kurs
          document.getElementById("input_add_kodebackoffice").value = dataHeaderAdd.Boffice
          document.getElementById("input_add_namabackoffice").value = dataHeaderAdd.NamaBoFFice
          document.getElementById("input_add_dp").value = dataHeaderAdd.DP ? parseFloat(dataHeaderAdd.DP).toFixed(2) : '0.00'
          document.getElementById("input_add_catatan").value = dataHeaderAdd.Catatan
          document.getElementById("input_add_kodealamatkirim").value = dataHeaderAdd.NoAlamatKirim
          document.getElementById("input_add_alamatkirim").value = dataHeaderAdd.AlamatKirim
          document.getElementById("input_add_kodelokasipenerima").value = dataHeaderAdd.KODEKEBUN
          document.getElementById("input_add_alamatlokasipenerima").value = dataHeaderAdd.NAMAKEBUN
          document.getElementById("input_add_nopo").value = dataHeaderAdd.NoPesanan

          document.getElementById("input_add_hari").value = dataHeaderAdd.Hari
          document.getElementById("input_add_pembayaran").value = dataHeaderAdd.TipeBayar
          if (Number(dataHeaderAdd.PPN)) {
            document.getElementById("input_add_tipeppn").innerHTML = `
            <option value=1  >Exclude</option>
            <option value=2 >Include</option>
            `

          } else {
            document.getElementById("input_add_tipeppn").innerHTML = `
            <option value=0 >None</option>
            `
          }

          document.getElementById("input_add_tipeppn").value = dataHeaderAdd.PPN
          document.getElementById("input_add_draftpo").value = dataHeaderAdd.PPO
          document.getElementById("input_add_tanggal").value = formatDate(dataHeaderAdd.Tanggal)
          document.getElementById("input_add_tanggalpo").value = formatDate(dataHeaderAdd.TglPO)
          document.getElementById("input_add_tanggalkirim").value = formatDate(dataHeaderAdd.TglKirim)
          document.getElementById("input_add_disc").value = parseFloat(dataHeaderAdd.Disc).toFixed(2)
          document.getElementById("input_add_discrp").value = parseFloat(dataHeaderAdd.TotDiskon).toFixed(2)
          document.getElementById("input_add_dpp").value = formatAngka(parseFloat(dataHeaderAdd.TotDPP).toFixed(2))
          document.getElementById("input_add_ppn").value = formatAngka(parseFloat(dataHeaderAdd.TotPPN).toFixed(2))
          document.getElementById("input_add_grandtotal").value = formatAngka(parseFloat(dataHeaderAdd.TotNet).toFixed(2))

        }





      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })




  }


}

function buttonAddDeleteItem (i) {
  console.log('buttonAddDeleteItem',i)

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  console.log(dataTableAdd[i])
  let dataDelete = dataTableAdd[i]


  if (dataDelete.nopl != '') {
  alertify.warning('Data sudah masuk picking list, tidak bisa di hapus')
  return
}


  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + dataDelete.NamaBrg + ' ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"

        let nobukti = dataDelete.NoBukti
        let urut = dataDelete.Urut

        $.ajax({
          url: "{!! url('sospadd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            disc:0,
            discrp:0,
            tipediskon:0,
            refpr: '',
            nopenyerahan: '',
            kodebarang : '',
            namaproduk: '',
            qty : 0,
            nosat :0,
            satuanproduk:'',
            harga:0,
            discDet:0,
            discrpDet:0,
            tambahkepo:0,
            booking:0,
            urgent:0,
            urut,
            qnt1:0,
            isi:0,
            satuan:0,
            pppn:0,
            choice,
            nobukti,
            nourut:'',
            kodepelanggan:'',
            kodesales:'',
            tanggal:'',
            kodealamatkirim:'',
            alamatkirim:'',
            kodepic:'',
            kodelokasipenerima:'',
            catatan:'',
            valas:'',
            kurs:0,
            dp:0,
            pembayaran:0,
            hari:0,
            tipeppn:0,
            draftpo:0,
            nopo:0,
            tanggalpo:'',
            tanggalkirim:'',
            kodebackoffice:'',
            tanggaljatuhtempo:'',
            jmlrecord:0

          },
          success: function(res) {
            console.log('resdelete', res)
            loadAll()

            // lockFormAdd()
            $('.showhide').hide();
            refreshDataTableAdd(nobukti)

            alertify.success('Berhasil menghapus item')
            loadAll()
          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
    });
}

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join('-');
}

function searchBarangAll (e) {
  // console.log('searchBarangAll')
  // console.log(e)
  // console.log(e.which)
  // console.log(e.key)

  if (e.which == 13) {
    console.log('enter')

    let search = $("#input_search_barang_all").val();

    $('#tabel_add_list_barangall').DataTable().destroy();

    $.ajax({
      url: "{!! url('solistbarang') !!}",
      type: "get",
      async: false,
      data: {
        search
      },
      success: function(res) {

        console.log(res)

        let rowTable = ""
        res.forEach((item, i) => {

          rowTable +=          `
          <tr >
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangAll('${item.Kodebrg}')" type="button" ><i class="bi bi-plus"></i></button></td>

            <td>${item.Kodebrg}</td>
            <td>${item.NamaBrg}</td>
            <td>${item.NAMAMERK ?? '-'}</td>
            <td>${item.Sat1 ?? '-'}</td>

        </tr>
        `
        });
        // $('#tabel_add_list_barangall').DataTable().destroy();

        document.getElementById("tabel_data_add_list_barangall").innerHTML = rowTable

      $("#tabel_add_list_barangall").DataTable({
        "lengthChange": false,
          "paging": false ,
          "searching" : false,
          "order": [[1, 'asc']],
        "columnDefs": [
             {"targets" :[0] , 'orderable' : false}
          ]
      });
      }})

  }

}

function generateInputNumber (id , style, classes, onchange) {
        return `<input type="text" id="${id}" onchange="${onchange}" style="${style}" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number ${classes}">`
      }

      function formatAngkaX (angka) {
        if (!angka) {
          return '0.00'
        } else {
          return formatAngka(parseFloat(angka).toFixed(2))
        }

      }

      function formatAngkaParse (angka) {

        return parseFloat(angka).toFixed(2)
      }

      function formatAngkaVal (angka) {
        return Number(angka.split(',').join(''))
      }

      function formatAngka (angkaString) {
  // console.log('formatAngka' , angkaString);
        let tempAngka = angkaString.split('.')

        if (tempAngka[0][0] == '-') {
          let temp2=''

          let tempAngka1 = tempAngka[0].split('-')
          for (let i = 0; i < tempAngka1[1].length; i++) {
            if (i != 0 && i % 3 == 0) {
              temp2 = ',' + temp2
            }
            temp2 = tempAngka1[1][tempAngka1[1].length - i -1] + temp2
            // console.log(i, temp2)
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
          temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
          // console.log(i, temp1)
        }
        temp1 += '.' + tempAngka[1]
        return temp1
      }


      function renderDropdownTambahSO(keyword) {

  let html = ''

  let filtered = cachePelanggan.filter(item =>
    item.kodecustsupp.toLowerCase().includes(keyword.toLowerCase()) ||
    item.namacustsupp.toLowerCase().includes(keyword.toLowerCase())
  )

  if (filtered.length === 0) {
    html = `<span class="dropdown-item text-muted">Tidak ditemukan</span>`
  }

  filtered.slice(0, 10).forEach(item => {

    html += `
      <div class="dropdown-item"
      style="white-space: normal; word-break: break-word;"
        onclick="selectPelangganTambahSO(event,
          '${item.kodecustsupp}',
          '${item.namacustsupp}',
          '${item.alamat1}',
          ${item.PPN},
          ${item.HARI},
          '${item.KodeSls ?? ''}',
          '${item.NamaSales ?? ''}',
          '${item.BOffice ?? ''}',
          '${item.NamaBackOffice ?? ''}'
        )"
      >
        <strong>${item.kodecustsupp}</strong><br>
        <small>${item.namacustsupp}</small>
      </div>
    `
  })

  $('#dropdown_pelanggantambahso').html(html).show()
}



</script>

@endsection