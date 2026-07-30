
@section('content')
@include('master/partials/sidebarPosting')

<link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}">

<style>
  .sp-page-wrap {
    margin-right: 280px; /* clears the fixed posting-sidebar so content doesn't run under it */
  }

  .sp-page-wrap .sp-page-head,
  .sp-page-wrap #contentContainer {
    max-width: 900px;   /* stops the table from stretching edge-to-edge */
    margin-left: auto;
    margin-right: auto;
  }

  @media (max-width: 768px) {
    .sp-page-wrap {
      margin-right: 0; /* posting-sidebar slides off-screen on mobile, no need to reserve space */
    }
  }
</style>

<div class="sp-page-wrap">

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Satuan</span>
  </div> --}}

  <div class="sp-page-head">
    <div>
      <h1>Master Posting Kas</h1>
    </div>
    <button class="btn btn-primary" onclick="buttonAdd()">+ Add Posting Kas</button>
  </div>

  <div id="contentContainer" class="container-fluid">

    <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

    @include('master.partials.headerTableMaster')

    <div class="table-outer">
      <div class="table-wrap">
        <table class="tb" id="tabel">
          <thead>
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
            </tr>
          </thead>
          <tbody id="tabel_data" class="text-right">
          </tbody>
        </table>
      </div>
    </div>

  </div>

</div>


<script src="{{ asset('js/masterTable.js') }}"></script>

   let currentLength = $("#tabel_length_visual").val() ? Number($("#tabel_length_visual").val()) : 10;
       let currentLength = $("#tabel_length_visual").val() ? Number($("#tabel_length_visual").val()) : 10;
      document.getElementById("tabel_data").innerHTML = rowTable
      $("#tabel").DataTable({
        "lengthChange": false,
        "paging": true,
        "searching": true,
        "dom": 'tip',
        "pageLength": currentLength
      });