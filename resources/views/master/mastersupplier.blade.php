@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Supplier')
@section('content')




  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Supplier</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Supplier</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Supplier</button>
  </div> --}}

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card">
    <div class="card-body" style="padding:0;">

  @include('master.partials.toolbarMaster')

  <table id="tabel" class="data-table po-aksi-hover">
        <thead id="tabel_header" class="text-center">
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left"></tbody>
      </table>

      <div class="po-rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom.
      </div>

    </div>
  </div>

</div>

<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 700px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="bs-form">
          <label for="input_add_kode">Kode</label>
          <input type="text" class="form-control" id="input_add_kode" placeholder="Kode">
          <label for="input_add_bentukusaha">Bentuk Usaha</label>
          <div class="d-flex align-items-center" style="gap:8px"><select id="input_add_bentukusaha" class="form-control" aria-label="Default select example" onchange=changePph23()>
                    <option selected value="PT">PT</option>
                    <option value="CV">CV</option>
                    <option value="PD">PD</option>
                    <option value="UD">UD</option>
                    <option value="TOKO">TOKO</option>
                    <option value="BAPAK">BAPAK</option>
                    <option value="IBU">IBU</option>
                    <option value="EXP">EXP</option>
                    <option value="-">-</option>
                  </select>
            <input type="checkbox" id="input_add_isppn" name="" value="">
                <label class="text-left">PPN</label></div>
        </div>

            <div class="bs-form bs-form-1">
          <label for="input_add_nama">Nama</label>
          <input type="text" class="form-control" id="input_add_nama" placeholder="Nama">

          <label for="input_add_alamat">Alamat</label>
          <input type="text" class="form-control" id="input_add_alamat" placeholder="Alamat">

          <label for="input_add_kota">Kota</label>
          <select id="input_add_kota" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Kota</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                  </select>
        </div>
            <div class="bs-form">
          <label for="input_add_kodepos">KodePos</label>
          <input type="text" class="form-control" id="input_add_kodepos" placeholder="Kode Pos">
          <label for="input_add_negara">Negara</label>
          <input type="text" class="form-control" id="input_add_negara" placeholder="Negara">
        </div>

            <div class="bs-form bs-form-1">
          <label for="input_add_telp">Telp</label>
          <input type="text" class="form-control" id="input_add_telp" placeholder="No Telp">
        </div>
            <!--DUA--><input type="hidden" class="form-control" id="input_add_fax" placeholder="No Fax"><div class="bs-form">
          <label for="input_add_pph21">Pph21</label>
          <input type="number" class="form-control text-right" id="input_add_pph21" value=0.00>
          <label for="input_add_pph23">Pph23</label>
          <input type="number" class="form-control text-right" id="input_add_pph23" value=2>


            
          <label for="input_add_email">Email</label>
          <input type="text" class="form-control" id="input_add_email" placeholder="Email">
          <label for="input_add_haripiutang">TOP</label>
          <input type="number" class="form-control text-right" id="input_add_haripiutang" value=0>
        </div>
            <div class="bs-form bs-form-1">
          <label for="input_add_isaktif">Status</label>
          <select id="input_add_isaktif" class="form-control" aria-label="Default select example">
                    <option selected value=1>Aktif</option>
                    <option value=0>Tidak Aktif</option>
                  </select>

          <label for="input_add_att">PIC</label>
          <input type="text" class="form-control" id="input_add_att" placeholder="Att">
        </div>
            <div class="bs-form">
          <label for="input_add_attphone">PIC Phone</label>
          <input type="text" class="form-control" id="input_add_attphone" placeholder="Att Phone">
          <label for="input_add_attdepart">PIC Depart</label>
          <input type="text" class="form-control" id="input_add_attdepart" placeholder="Att Depart">

            
          <label for="input_add_bank">Bank</label>
          <select id="input_add_bank" class="form-control" aria-label="Default select example">
                    <option selected value="BCA">BCA</option>
                    <option value="MANDIRI">MANDIRI</option>
                    <option value="NIAGA">NIAGA</option>
                    <option value="CIMB">CIMB</option>
                  </select>
          <label for="input_add_accno">Acc No.</label>
          <input type="text" class="form-control" id="input_add_accno" placeholder="Acc No.">
        </div>
            <div class="bs-form bs-form-1">
          <label for="input_add_atn">a/n</label>
          <input type="text" class="form-control" id="input_add_atn" placeholder="Atas Nama">
        </div>
            <br/>

            <h5>Data Pajak</h5>
            <br/>
            <div class="bs-form bs-form-1">
          <label for="input_add_npwp">NPWP</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_add_npwp" placeholder="NPWP"></div>
        </div>
            <div class="bs-form bs-form-1">
          <label for="input_add_namapkp">Nama</label>
          <input type="text" class="form-control" id="input_add_namapkp" placeholder="Nama PKP">

          <label for="input_add_alamatpkp">Alamat</label>
          <input type="text" class="form-control" id="input_add_alamatpkp" placeholder="Alamat PKP">

          <label for="input_add_kotapkp">Kota</label>
          <input type="text" class="form-control" id="input_add_kotapkp" placeholder="Kota PKP">
        </div>

    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAdd()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->

<!-- start modal edit -->
<div class="modal fade"  id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 700px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

            <div class="bs-form">
          <label for="input_edit_kode">Kode</label>
          <input type="text" class="form-control" disabled id="input_edit_kode" placeholder="Kode">
          <label for="input_edit_bentukusaha">Bentuk Usaha</label>
          <div class="d-flex align-items-center" style="gap:8px"><!-- <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_bentukusaha" placeholder="Bentuk Usaha">
                </div> -->

                <div class="form-group">
                  <select id="input_edit_bentukusaha" class="form-control" aria-label="Default select example">
                    <option selected value="PT">PT</option>
                    <option value="CV">CV</option>
                    <option value="PD">PD</option>
                    <option value="UD">UD</option>
                    <option value="TOKO">TOKO</option>
                    <option value="BAPAK">BAPAK</option>
                    <option value="IBU">IBU</option>
                    <option value="EXP">EXP</option>
                    <option value="-">-</option>
                  </select>
                </div>
            <input type="checkbox" id="input_edit_isppn" name="">
                <label class="text-left">PPN</label></div>
        </div>

            <div class="bs-form bs-form-1">
          <label for="input_edit_nama">Nama</label>
          <input type="text" class="form-control" id="input_edit_nama" placeholder="Nama">

          <label for="input_edit_alamat">Alamat</label>
          <input type="text" class="form-control" id="input_edit_alamat" placeholder="Alamat">

          <label for="input_edit_kota">Kota</label>
          <select id="input_edit_kota" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Kota</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                  </select>
        </div>
            <div class="bs-form">
          <label for="input_edit_kodepos">KodePos</label>
          <input type="text" class="form-control" id="input_edit_kodepos" placeholder="Kode Pos">
          <label for="input_edit_negara">Negara</label>
          <input type="text" class="form-control" id="input_edit_negara" placeholder="Negara">
        </div>
            <div class="bs-form bs-form-1">
          <label for="input_edit_telp">Telp</label>
          <input type="text" class="form-control" id="input_edit_telp" placeholder="No Telp">
        </div>
            <!--DUA--><input type="hidden" class="form-control" id="input_edit_fax" placeholder="No Fax"><div class="bs-form">
          <label for="input_edit_pph21">Pph21</label>
          <input type="number" class="form-control text-right" id="input_edit_pph21" value=0.00>
          <label for="input_edit_pph23">Pph23</label>
          <input type="number" class="form-control text-right" id="input_edit_pph23" value=0.00>

            
          <label for="input_edit_email">Email</label>
          <input type="text" class="form-control" id="input_edit_email" placeholder="Email">
          <label for="input_edit_haripiutang">TOP</label>
          <input type="number" class="form-control text-right" id="input_edit_haripiutang" value=0>
        </div>
            <div class="bs-form bs-form-1">
          <label for="input_edit_isaktif">Status</label>
          <select id="input_edit_isaktif" class="form-control" aria-label="Default select example">
                    <option selected value=1>Aktif</option>
                    <option value=0>Tidak Aktif</option>
                  </select>

          <label for="input_edit_att">PIC</label>
          <input type="text" class="form-control" id="input_edit_att" placeholder="Att">
        </div>
            <div class="bs-form">
          <label for="input_edit_attphone">PIC Phone</label>
          <input type="text" class="form-control" id="input_edit_attphone" placeholder="Att Phone">
          <label for="input_edit_attdepart">PIC Depart</label>
          <input type="text" class="form-control" id="input_edit_attdepart" placeholder="Att Depart">

            
          <label for="input_edit_bank">Bank</label>
          <input type="text" class="form-control" id="input_edit_bank" placeholder="Nama Bank">
          <label for="input_edit_accno">Acc No.</label>
          <input type="text" class="form-control" id="input_edit_accno" placeholder="Acc No.">
        </div>
            <div class="bs-form bs-form-1">
          <label for="input_edit_atn">a/n</label>
          <input type="text" class="form-control" id="input_edit_atn" placeholder="Atas Nama">
        </div>
            <br/>

            <h5>Data Pajak</h5>
            <br/>
            <div class="bs-form bs-form-1">
          <label for="input_edit_npwp">NPWP</label>
          <input type="text" class="form-control" id="input_edit_npwp" placeholder="NPWP">

          <label for="input_edit_namapkp">Nama</label>
          <input type="text" class="form-control" id="input_edit_namapkp" placeholder="Nama PKP">

          <label for="input_edit_alamatpkp">Alamat</label>
          <input type="text" class="form-control" id="input_edit_alamatpkp" placeholder="Alamat PKP">

          <label for="input_edit_kotapkp">Kota</label>
          <input type="text" class="form-control" id="input_edit_kotapkp" placeholder="Kota PKP">
        </div>

    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEdit()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal edit-->

@include('master/modalSupplierCustomer')


{{-- Modal Detail Akun - disalin dari mastercustomer.blade.php. Dulu tombol Detail Akun di tabel
     supplier sudah aktif dan JS-nya memakai endpoint mastercustomer*detailakun, tetapi modalnya
     tidak pernah ada di halaman ini sehingga tombol selalu error. --}}
<div class="modal fade"  id="formDetailAkun" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Akun</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <button class="btn btn-sm btn-chip-biru" type="button" onclick="buttonDetailAkunAdd()">Tambah</button>
        <table id="tabelDetailAkun" class="table table-bordered table-striped"  >
          <thead id='theadCustom' class="text-center">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Kode</th>
              <th scope="col">Perkiraan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataDetailAkun" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonDetailAkunEdit()"><i class="bi bi-pen">Select</i></button>
                  <button type="button" onclick="buttonDetailAkunDelete()"><i class="bi bi-trash">Select</i></button>
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

<div class="modal fade"  id="formDetailAkunAdd" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Detail Akun</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="bs-form bs-form-1">
          <label for="input_add_kodedetail">Kode</label>
          <input type="text" class="form-control" id="input_add_kodedetail" placeholder="Kode" disabled>
        </div>

          <div class="bs-form bs-form-1">
          <label for="input_add_perkiraandetail">Hutang/Piutang</label>
          <div class="input-group">
                <input type="text" class="form-control" id="input_add_perkiraandetail">
                <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonDetailAkunSelect()">+</button>
              </div>
            </div>
        </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddDetailAkun()">Simpan</button>
  </div>
</div>
</div>
</div>

<div class="modal fade"  id="formDetailAkunEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Detail Akun</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="bs-form bs-form-1">
          <label for="input_edit_kodedetail">Kode</label>
          <input type="text" class="form-control" id="input_edit_kodedetail" placeholder="Kode" disabled>
        </div>

          <div class="bs-form bs-form-1">
          <label for="input_edit_perkiraandetail">Hutang/Piutang</label>
          <div class="input-group">
                <input type="text" class="form-control" id="input_edit_perkiraandetail">
                <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonDetailAkunSelect()">+</button>
              </div>
            </div>
        </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddDetailAkunEdit()">Simpan</button>
  </div>
</div>
</div>
</div>

<div class="modal fade picker-kas"  id="formDetailAkunAddPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Akun</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelDetailAkunAddPerkiraan">
                <thead class="text-center">
                  <tr>
              <th scope="col">Actions</th>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
                </thead>
                <tbody id="tabel_dataDetailAkunAddPerkiraan" class="text-left"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>

@endsection

{{-- Modal pemilih mengikuti picker purchasing (picker-kas.css / pickerKasInit()). --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataRefresh = []

// Kolom tabel daftar (lebih dari 5 kolom -> bisa digeser & disembunyikan, lihat MasterList.kolom()).
// [field, label, tampil, tipe, total, desimal]
const MSP_KOLOM = [
  ['KODECUSTSUPP', 'Kode',         1, 'varchar', 0, 0],
  ['USAHA',        'Bentuk Usaha', 1, 'varchar', 0, 0],
  ['NAMACUSTSUPP', 'Nama',         1, 'varchar', 0, 0],
  ['ALAMAT1',      'Alamat',       1, 'varchar', 0, 0],
  ['namaKota',     'Kota',         1, 'varchar', 0, 0],
  ['NEGARA',       'Negara',       1, 'varchar', 0, 0],
  ['TELPON',       'Telpon',       1, 'varchar', 0, 0],
  ['EMAIL',        'Email',        1, 'varchar', 0, 0],
  ['KODEPOS',      'Kode Pos',     0, 'varchar', 0, 0],
  ['FAX',          'Fax',          0, 'varchar', 0, 0],
]

// Data dimuat DataTables lewat ajax (mastersupplierloadall). Kolom disusun ulang dari kolom yang
// tampil setiap kali kolom digeser/disembunyikan (ReportTable memanggil loadAll lewat onChange).
// Kode Pos & Fax tersembunyi secara bawaan, sama seperti sebelumnya (visible: false).
function loadAll () {
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }
  document.getElementById('tabel_data').innerHTML = ''

  let cols = MasterList.kolomTampil()
  document.getElementById('tabel_header').innerHTML = MasterList.headHtml(cols)

  $('#tabel').DataTable(MasterList.opsi({
    processing: true,
    ajax: {
      url: "{!! url('mastersupplierloadall') !!}",
      type: "GET",
      dataSrc: ''
    },
    columns: MasterList.kolomServer(cols, function (data, type, row) {
      let kode = row.KODECUSTSUPP
      return `
        <div class="action-buttons-wrap">
          <button class="btn-action-sm btn-action-success" onclick="buttonEdit('${kode}')" title="Edit Supplier">
              <i class="bi bi-pen"></i>
          </button>

          <button class="btn-action-sm btn-action-primary" onclick="buttonDetailAkun('${kode}')" title="Detail Akun">
              <i class="bi bi-card-text"></i>
          </button>

          <button class="btn-action-sm btn-action-danger" onclick="buttonDelete('${kode}')" title="Delete Supplier">
              <i class="bi bi-trash"></i>
          </button>
        </div>
      `;
    })
  }));
  MasterList.selesai('#tabel')
}


function buttonAdd () {

  //  $("#input_add_fax").modal("hide");
 console.log('add')

  // Form Add dikembalikan ke nilai bawaan HTML-nya (mis. PPh23 = 2) - dulu sisa isian tambah
  // sebelumnya ikut terbawa.
  $('#form .modal-body input[type="text"], #form .modal-body input[type="number"]').each(function () { this.value = this.defaultValue })
  $('#form .modal-body input[type="checkbox"]').prop('checked', false)

  $.ajax({
    url: "{!! url('mastersupplierlistkota') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {

      console.log(res)
      let rowTable = `<option selected disabled value=0>Pilih Kota</option>`
      res.forEach((item, i) => {
        rowTable += `
          <option value="${item.KodeKota}">${item.NamaKota}</option>
        `
      });

      document.getElementById("input_add_kota").innerHTML = rowTable
    }})


  $("#form").modal('toggle')

}

function buttonEdit (kode) {
  console.log(kode)
  // let tempbentukusaha =''
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('mastersupplierlistkota') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {

      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
          <option value="${item.KodeKota}">${item.NamaKota}</option>
        `
      });

      document.getElementById("input_edit_kota").innerHTML = rowTable
    }})

  $.ajax({
    url: "{!! url('mastersupplierspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kode").value = res[0].KODECUSTSUPP
      document.getElementById("input_edit_nama").value = res[0].NAMACUSTSUPP
      document.getElementById("input_edit_bentukusaha").value = res[0].USAHA
      // tempbentukusaha  = res[0].USAHA
      if (Number(res[0].IsPpn) == 1) {
        document.getElementById("input_edit_isppn").checked = true
      } else {
        document.getElementById("input_edit_isppn").checked = false
      }
      document.getElementById("input_edit_alamat").value = res[0].ALAMAT1
      document.getElementById("input_edit_kota").value = res[0].Kota
      document.getElementById("input_edit_kodepos").value = res[0].KODEPOS
      document.getElementById("input_edit_telp").value = res[0].TELPON
      document.getElementById("input_edit_negara").value = res[0].NEGARA
      document.getElementById("input_edit_fax").value = res[0].FAX
      document.getElementById("input_edit_email").value = res[0].EMAIL
      document.getElementById("input_edit_pph23").value = res[0].NPPH23
      document.getElementById("input_edit_pph21").value = res[0].NPPH22
      document.getElementById("input_edit_haripiutang").value = res[0].HARIHUTPIUT
      document.getElementById("input_edit_isaktif").value = res[0].IsAktif
      document.getElementById("input_edit_att").value = res[0].Att
      document.getElementById("input_edit_attphone").value = res[0].AttPhone
      document.getElementById("input_edit_attdepart").value = res[0].AttDepart
      document.getElementById("input_edit_accno").value = res[0].NoAcc
      document.getElementById("input_edit_bank").value = res[0].bank
      document.getElementById("input_edit_atn").value = res[0].ATN
      document.getElementById("input_edit_npwp").value = res[0].NPWP
      document.getElementById("input_edit_namapkp").value = res[0].NAMAPKP
      document.getElementById("input_edit_alamatpkp").value = res[0].ALAMATPKP1
      document.getElementById("input_edit_kotapkp").value = res[0].KOTAPKP


    }})
    $("#formEdit").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Supplier', 'Apakah yakin ingin menghapus Supplier ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastersupplierspdelete') !!}",
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
              loadAll()
              alertify.success("Supplier telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

function submitEdit () {

  let _token = $("#_token").val();
  let isppn = 0
  if (document.getElementById("input_edit_isppn").checked) {
    isppn= 1
  }
  console.log(isppn)
  let kode = $("#input_edit_kode").val();
  console.log('kode' , kode)
  let bentukusaha = $("#input_edit_bentukusaha").val();
  console.log('bentukusaha' , bentukusaha)
  let nama = $("#input_edit_nama").val();
  console.log('nama' , nama)
  let alamat = $("#input_edit_alamat").val();
  console.log('alamat' , alamat)
  let kota = $("#input_edit_kota").val();
  console.log('kota' , kota)
  let kodepos = $("#input_edit_kodepos").val();
  console.log('kodepos' , kodepos)
  let negara = $("#input_edit_negara").val();
  console.log('negara' , negara)
  let telp = $("#input_edit_telp").val();
  console.log('telp' , telp)
  let fax = $("#input_edit_fax").val();
  console.log('fax' , fax)
  let email = $("#input_edit_email").val();
  console.log('email' , email)
  let pph23 = $("#input_edit_pph23").val();
  console.log('pph23' , pph23)
  let pph21 = $("#input_edit_pph21").val();
  console.log('pph21' , pph21)
  let haripiutang = $("#input_edit_haripiutang").val();
  let haribiasa = $("#input_edit_haripiutang").val();
  console.log('haripiutang' , haripiutang)
  let isaktif = $("#input_edit_isaktif").val();
  console.log('isaktif' , isaktif)
  let att = $("#input_edit_att").val();
  console.log('att' , att)
  let attphone = $("#input_edit_attphone").val();
  console.log('attphone' , attphone)
  let attdepart = $("#input_edit_attdepart").val();
  console.log('attdepart' , attdepart)
  let bank = $("#input_edit_bank").val();
  console.log('bank' , bank)
  let accno = $("#input_edit_accno").val();
  console.log('accno' , accno)
  let npwp = $("#input_edit_npwp").val();
  console.log('npwp' , npwp)
  let namapkp = $("#input_edit_namapkp").val();
  console.log('namapkp' , namapkp)
  let alamatpkp = $("#input_edit_alamatpkp").val();
  console.log('alamatpkp' , alamatpkp)
  let kotapkp = $("#input_edit_kotapkp").val();
  console.log('kotapkp' , kotapkp)
  let atn = $("#input_edit_atn").val();
  console.log('atn' , atn)
  let jenis = 0

  // jenis,kode,bentukusaha, nama, alamat, kota, kodepos, negara, telp, fax, email, pph23,pph21,haripiutang, isaktif,att,attphone,attdepart, bank,accno,npwp,namapkp,alamatpkp,kotapkp,atn

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }
  if (!nama) {
    alertify.warning("Nama harus diisi");
    return
  }

  if (kota == 0 || !kota) {
    alertify.warning("Kota harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastersupplierspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      isppn, haribiasa,
      jenis,kode,bentukusaha, nama, alamat, kota, kodepos, negara, telp, fax, email, pph23,pph21,haripiutang, isaktif,att,attphone,attdepart, bank,accno,npwp,namapkp,alamatpkp,kotapkp,atn
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Supplier telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}

function submitAdd () {

  let _token = $("#_token").val();
  let isppn = 0
  if (document.getElementById("input_add_isppn").checked) {
    isppn= 1
  }
  // console.log(document.getElementById("input_add_isppn").checked)
  // return
  console.log(isppn)
  let kode = $("#input_add_kode").val();
  console.log('kode' , kode)
  let bentukusaha = $("#input_add_bentukusaha").val();
  console.log('bentukusaha' , bentukusaha)
  let nama = $("#input_add_nama").val();
  console.log('nama' , nama)
  let alamat = $("#input_add_alamat").val();
  console.log('alamat' , alamat)
  let kota = $("#input_add_kota").val();
  console.log('kota' , kota)
  let kodepos = $("#input_add_kodepos").val();
  console.log('kodepos' , kodepos)
  let negara = $("#input_add_negara").val();
  console.log('negara' , negara)
  let telp = $("#input_add_telp").val();
  console.log('telp' , telp)
  let fax = $("#input_add_fax").val();
  console.log('fax' , fax)
  let email = $("#input_add_email").val();
  console.log('email' , email)
  let pph23 = $("#input_add_pph23").val();
  console.log('pph23' , pph23)
  let pph21 = $("#input_add_pph21").val();
  console.log('pph21' , pph21)
  let haripiutang = $("#input_add_haripiutang").val();
  let haribiasa = $("#input_add_haripiutang").val();
  console.log('haripiutang' , haripiutang)
  let isaktif = $("#input_add_isaktif").val();
  console.log('isaktif' , isaktif)
  let att = $("#input_add_att").val();
  console.log('att' , att)
  let attphone = $("#input_add_attphone").val();
  console.log('attphone' , attphone)
  let attdepart = $("#input_add_attdepart").val();
  console.log('attdepart' , attdepart)
  let bank = $("#input_add_bank").val();
  console.log('bank' , bank)
  let accno = $("#input_add_accno").val();
  console.log('accno' , accno)
  let npwp = $("#input_add_npwp").val();
  console.log('npwp' , npwp)
  let namapkp = $("#input_add_namapkp").val();
  console.log('namapkp' , namapkp)
  let alamatpkp = $("#input_add_alamatpkp").val();
  console.log('alamatpkp' , alamatpkp)
  let kotapkp = $("#input_add_kotapkp").val();
  console.log('kotapkp' , kotapkp)
  let atn = $("#input_add_atn").val();
  console.log('atn' , atn)
  let jenis = 0

  // jenis,kode,bentukusaha, nama, alamat, kota, kodepos, negara, telp, fax, email, pph23,pph21,haripiutang, isaktif,att,attphone,attdepart, bank,accno,npwp,namapkp,alamatpkp,kotapkp,atn

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }
  if (!nama) {
    alertify.warning("Nama harus diisi");
    return
  }

  if (kota == 0 || !kota) {
    alertify.warning("Kota harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastersupplierspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      isppn, haribiasa,
      jenis,kode,
      bentukusaha, nama, 
      alamat, kota,
       kodepos, negara,
        telp, fax,
         email, pph23, 
         pph21,haripiutang, isaktif,att,attphone,attdepart, bank,accno,npwp,namapkp,alamatpkp,kotapkp,atn
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Supplier telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

function buttonDetailAkun (kodeDetail) {
  noBuktiDetailTemp = kodeDetail
  console.log(kodeDetail)
  loadDetailAkun(kodeDetail)
  $("#formDetailAkun").modal('toggle')
}

function loadDetailAkun (kodeDetail) {

  console.log(kodeDetail);
  let _token = $("#_token").val();
  kodeDetailPerkiraan = kodeDetail;

  console.log(kodeDetailPerkiraan + '55555')

  $('#tabelDetailAkun').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastercustomerloaddetailakun') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kodeDetail: noBuktiDetailTemp
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
        <button class="btn-action-sm btn-action-success" type="button" onclick="buttonDetailAkunEdit('${item.Perkiraan}', '${item.KodeCustSupp}' )"><i class="bi bi-pen"></i></button>
        <button class="btn-action-sm btn-action-danger" type="button" onclick="buttonDetailAkunDelete('${item.Perkiraan}', '${item.KodeCustSupp}')"><i class="bi bi-trash"></i></button>
      </td>
      <td>${item.KodeCustSupp}</td>
      <td>${item.Perkiraan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataDetailAkun").innerHTML = rowTable;
  $("#tabelDetailAkun").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function buttonDetailAkunAdd (){
  document.getElementById("input_add_kodedetail").value = noBuktiDetailTemp;
  document.getElementById("input_add_perkiraandetail").value = '';
  $("#formDetailAkunAdd").modal('toggle')
}

function buttonDetailAkunSelect (){
  loadPerkiraanDetail()
  $("#formDetailAkunAddPerkiraan").modal('toggle')
}

function loadPerkiraanDetail () {
  let _token = $("#_token").val();

  $('#tabelDetailAkunAddPerkiraan').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastercustomerloaddetailperkiraan') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
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
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonDetailAkunSelectAdd('${item.Perkiraan}')"> + </i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataDetailAkunAddPerkiraan").innerHTML = rowTable;
  pickerKasInit('tabelDetailAkunAddPerkiraan')
}

function buttonDetailAkunSelectAdd (selectedPerkiraan) {
  $("#input_add_perkiraandetail").val(selectedPerkiraan);
  $("#input_edit_perkiraandetail").val(selectedPerkiraan);
  $("#formDetailAkunAddPerkiraan").modal("hide");

}

function submitAddDetailAkun () {

  let _token = $("#_token").val();
  let kode = $("#input_add_kodedetail").val();
  let nama = $("#input_add_perkiraandetail").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Perkiraan harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastercustomerspadddetailakun') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Detail Akun telah ditambah");
        loadDetailAkun()
        $("#formDetailAkunAdd").modal('hide')
      }

    }})

  // console.log(kodearea, namaarea)
}

function buttonDetailAkunDelete (kode, kodecust) {
  console.log(kode, kodecust)
  let _token = $("#_token").val();

  alertify.confirm('Hapus Akun', 'Apakah yakin ingin menghapus Akun ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastercustomerspdeletedetailakun') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kode,
            kodecust
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadDetailAkun();
              alertify.success("Customer telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });

}

function buttonDetailAkunEdit (perkiraan, kodecust){
  document.getElementById("input_edit_kodedetail").value = noBuktiDetailTemp;
  perkiraanOldTemp = perkiraan
  loadPerkiraanEdit(perkiraan, kodecust);
  $("#formDetailAkunEdit").modal('toggle')
}

nomorTemp = ''

function loadPerkiraanEdit (perkiraan, kodecust){

    $.ajax({
    url: "{!! url('mastercustomerloadperkiraanedit') !!}",
    type: "get",
    async: false,
    data: {
      perkiraan,
      kodecust
    },
    success: function (res) {
      console.log(res);
      document.getElementById('input_edit_perkiraandetail').value = res[0].Perkiraan
    },
  });

}

function changePph23() {
  let valueUsaha = document.getElementById('input_add_bentukusaha').value 

  console.log(valueUsaha);

  if (valueUsaha == 'PT' || valueUsaha == 'CV') {
    document.getElementById('input_add_pph23').value = 2
  } else {
    document.getElementById('input_add_pph23').value = 2.5
  }
}

window.onload = function(){
  MasterList.kolom({ href: 'mastersupplier', kolom: MSP_KOLOM, onChange: loadAll })
  loadAll();
};

</script>1




@endsection
