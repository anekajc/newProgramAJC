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
    <span class="sp-crumb-active">Karyawan</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Karyawan</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Karyawan</button>
  </div> --}}

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  @include('master.partials.headerTableMaster')

          <div class="table-outer">
            <div class="table-wrap">
              <table class="tb" id="tabel">
                <thead>
                  <tr>
                    <th scope="col">Actions</th>
                    <th scope="col">ID</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Kelamin</th>
                    <th scope="col">Tmp Lahir</th>
                    <th scope="col">Tgl Lahir</th>
                    <th scope="col">Agama</th>
                    <th scope="col">Tinggi</th>
                    <th scope="col">Berat</th>
                    <th scope="col">Nomor KTP</th>
                    <th scope="col">Tgl Masuk</th>
                    <th scope="col">Telepon HP</th>
                  </tr>
                </thead>
                <tbody id="tabel_data" class="text-right">
              </tbody>
              </table>
            </div>
        </div>

</div>

<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 900px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama Lengkap</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_NamaLengkap">
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Alamat</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_Alamat">
                </div>
              </div>
            </div>

            <div class="row mt-2">

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NIK</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_Nik">
                </div>
              </div>
 
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Telepon HP</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_HP">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode Pos</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_KodePos">
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kelamin</label>
                </div>
              </div>
              <div class="col-4">
                <div class='form-group'>
                  <select name="formkelamin" class='form-control' id="input_add_Kelamin">
                      <option value="L">Laki-Laki</option>
                      <option value="P">Perempuan</option>
                    </select>
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nomor KTP</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_KTP">
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">E-mail</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_Email">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tempat Lahir</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_TempatLahir">
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Lahir</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_add_TanggalLahir">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Agama</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_Agama">
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Pendidikan Akhir</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_PendidikanAkhir">
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tinggi</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" class="form-control" id="input_add_Tinggi">
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Berat</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" class="form-control" id="input_add_Berat">
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tgl Masuk</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_add_TglMasuk">
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NPWP</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_NPWP">
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Status</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select name="formstatus" class='form-control' id="input_add_Status">
                    <option value=1>Sales</option>
                    <option value=0>Non-Sales</option>
                  </select>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Aktif</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select name="formaktif" class='form-control' id="input_add_Aktif">
                    <option value=1>Aktif</option>
                    <option value=0>Tidak Aktif</option>
                  </select>
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Produksi</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select name="formproduksi" class='form-control' id="input_add_Produksi">
                    <option value="0">Produksi</option>
                    <option value="1">Non Produksi</option>
                    <option value="2">Finishing</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Penagih</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_Penagih">
                </div>
              </div>
            </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->


<!-- start modal add -->
<div class="modal fade"  id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 900px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_edit_noUrut" value="" />

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama Lengkap</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_NamaLengkap">
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Alamat</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_Alamat">
                </div>
              </div>
            </div>

            <div class="row mt-2">

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NIK</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_Nik" disabled>
                </div>
              </div>
 
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Telepon HP</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_HP">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode Pos</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_KodePos">
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kelamin</label>
                </div>
              </div>
              <div class="col-4">
                <div class='form-group'>
                  <select name="formkelamin" class='form-control' id="input_edit_Kelamin">
                      <option value="L">Laki-Laki</option>
                      <option value="P">Perempuan</option>
                    </select>
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nomor KTP</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_KTP">
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">E-mail</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_Email">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tempat Lahir</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_TempatLahir">
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Lahir</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_edit_TanggalLahir">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Agama</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_Agama">
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Pendidikan Akhir</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_PendidikanAkhir">
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tinggi</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" class="form-control" id="input_edit_Tinggi">
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Berat</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" class="form-control" id="input_edit_Berat">
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tgl Masuk</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_edit_TglMasuk">
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NPWP</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_NPWP">
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Status</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select name="formstatus" class='form-control' id="input_edit_Status">
                    <option value=1>Sales</option>
                    <option value=0>Non-Sales</option>
                  </select>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Aktif</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select name="formaktif" class='form-control' id="input_edit_Aktif">
                    <option value=1>Aktif</option>
                    <option value=0>Tidak Aktif</option>
                  </select>
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Produksi</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select name="formproduksi" class='form-control' id="input_edit_Produksi">
                    <option value="0">Produksi</option>
                    <option value="1">Non Produksi</option>
                    <option value="2">Finishing</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Penagih</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_Penagih">
                </div>
              </div>
            </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-primary" onclick="submitEdit()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->



@endsection

@section('js')
<script src="{{ asset('js/masterTable.js') }}"></script>
<script type="text/javascript">

let dataRefresh = []

function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();

  document.getElementById('breadcrumb').innerHTML = "Master Karyawan"

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('masterdaftarkaryawanloadall') !!}",
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
    let dbDateString = item.TglLahir;
    let dbDateString2 = item.TglMasuk;

    // Convert the database date string to a JavaScript Date object
    let dateObject = new Date(dbDateString);
    let dateObject2 = new Date(dbDateString2);

    // Extract day, month, and year components
    let day = dateObject.getDate();
    let month = dateObject.getMonth() + 1; // Months are 0-based, so add 1
    let year = dateObject.getFullYear();
    let day2 = dateObject2.getDate();
    let month2 = dateObject2.getMonth() + 1; // Months are 0-based, so add 1
    let year2 = dateObject2.getFullYear();

    // Format the components
    let formattedDay = (day < 10) ? `0${day}` : day;
    let formattedMonth = (month < 10) ? `0${month}` : month;
    let formattedYear = (year < 10) ? `0${year}` : year;

    let formattedDay2 = (day2 < 10) ? `0${day2}` : day2;
    let formattedMonth2 = (month2 < 10) ? `0${month2}` : month2;
    let formattedYear2 = (year2 < 10) ? `0${year2}` : year2;
    // Assemble the formatted components into the desired format 'DD-MM-YY'
    let formattedDate = `${formattedDay}-${formattedMonth}-${formattedYear}`;
    let formattedDate2 = `${formattedDay2}-${formattedMonth2}-${formattedYear2}`;

    rowTable += `<tr>
    <td class="text-center">
      <div class="action-buttons-wrap">
          <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.NIK}')"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.NIK}')"><i class="bi bi-trash"></i></button>
      </div> 
    </td>
    <td>${item.NIK}</td>
    <td>${item.Nama}</td>
    <td>${item.Kelamin}</td>
    <td>${item.TmpLahir}</td>
    <td>${formattedDate}</td>
    <td>${item.Agama}</td>
    <td>${item.Tinggi}</td>
    <td>${item.Berat}</td>
    <td>${item.NomorKTP}</td>
    <td>${formattedDate2}</td>
    <td>${item.TeleponHP}</td>
    </tr>`
  });

  document.getElementById("tabel_data").innerHTML = rowTable
    $("#tabel").DataTable({
      "lengthChange": false,
      "paging": true,
      "searching": true,
      "dom": 'tip'
  });

}

function buttonAdd () {

  $("#form").modal('toggle')

}

function buttonEdit (Nik) {
  console.log(Nik);
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masterdaftarkaryawanspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      Nik: Nik
    },
    success: function (res) {
      console.log(res);
      document.getElementById("input_edit_Nik").value = res[0].NIK;
      document.getElementById("input_edit_NamaLengkap").value = res[0].Nama;
      document.getElementById("input_edit_Alamat").value = res[0].AlamatKTP;
      document.getElementById("input_edit_HP").value = res[0].TeleponHP;
      document.getElementById("input_edit_KodePos").value = res[0].KodePosRmh;
      document.getElementById("input_edit_Kelamin").value = res[0].Kelamin;
      document.getElementById("input_edit_KTP").value = res[0].NomorKTP;
      document.getElementById("input_edit_TempatLahir").value = res[0].TmpLahir;
      document.getElementById("input_edit_Email").value = res[0].Email;

      // Format TanggalLahir
      const tglLahir = new Date(res[0].TglLahir + 'Z');
      const formattedTglLahir = tglLahir.toISOString().split('T')[0];
      document.getElementById("input_edit_TanggalLahir").value = formattedTglLahir;

      document.getElementById("input_edit_Agama").value = res[0].Agama;
      document.getElementById("input_edit_PendidikanAkhir").value = res[0].KetPendAkhir;
      document.getElementById("input_edit_Tinggi").value = res[0].Tinggi;
      document.getElementById("input_edit_Berat").value = res[0].Berat;

      // Format TglMasuk
      const tglMasuk = new Date(res[0].TglMasuk + 'Z');
      const formattedTglMasuk = tglMasuk.toISOString().split('T')[0];
      document.getElementById("input_edit_TglMasuk").value = formattedTglMasuk;

      document.getElementById("input_edit_NPWP").value = res[0].NPWP;
      document.getElementById("input_edit_Status").value = res[0].IsSales;
      document.getElementById("input_edit_Aktif").value = res[0].Aktif;
      document.getElementById("input_edit_Produksi").value = res[0].Produksi;
      document.getElementById("input_edit_Penagih").value = res[0].Penagih;
    }
  });
  $("#formEdit").modal('toggle');
}


function buttonDelete (Nik) {
  console.log(Nik)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Area', 'Apakah yakin ingin menghapus Data ' + Nik + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterdaftarkaryawanspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            Nik
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Data Daftar Karyawan telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}
//
function submitEdit () {

  let _token = $("#_token").val();
  let Nik = $("#input_edit_Nik").val();
  let NamaLengkap = $("#input_edit_NamaLengkap").val();
  let Alamat = $("#input_edit_Alamat").val();
  let HP = $("#input_edit_HP").val();
  let KodePos = $("#input_edit_KodePos").val();
  let Kelamin = $("#input_edit_Kelamin").val();
  let KTP = $("#input_edit_KTP").val();
  let TempatLahir = $("#input_edit_TempatLahir").val();
  let Email = $("#input_edit_Email").val();
  let TanggalLahir = $("#input_edit_TanggalLahir").val();
  let Agama = $("#input_edit_Agama").val();
  let PendidikanAkhir = $("#input_edit_PendidikanAkhir").val();
  let Tinggi = $("#input_edit_Tinggi").val();
  let Berat = $("#input_edit_Berat").val();
  let TglMasuk = $("#input_edit_TglMasuk").val();
  let NPWP = $("#input_edit_NPWP").val();
  let Status = $("#input_edit_Status").val();
  let Aktif = $("#input_edit_Aktif").val();
  let Produksi = $("#input_edit_Produksi").val();
  let Penagih = $("#input_edit_Penagih").val();

  if (!Nik) {
  alertify.warning("Nik harus diisi");
  return;
  }

  if (!NamaLengkap) {
    alertify.warning("Nama Lengkap harus diisi");
    return;
  }

  if (!Alamat) {
    alertify.warning("Alamat harus diisi");
    return;
  }

  if (!HP) {
    alertify.warning("HP harus diisi");
    return;
  }

  if (!KodePos) {
    alertify.warning("Kode Pos harus diisi");
    return;
  }

  if (!Kelamin) {
    alertify.warning("Kelamin harus diisi");
    return;
  }

  if (!KTP) {
    alertify.warning("No KTP harus diisi");
    return;
  }

  if (!TempatLahir) {
    alertify.warning("Tempat Lahir harus diisi");
    return;
  }

  if (!Email) {
    alertify.warning("Email harus diisi");
    return;
  }

  if (!TanggalLahir) {
    alertify.warning("Tanggal Lahir harus diisi");
    return;
  }

  if (!Agama) {
    alertify.warning("Agama harus diisi");
    return;
  }

  if (!PendidikanAkhir) {
    alertify.warning("Pendidikan Akhir harus diisi");
    return;
  }

  if (!Tinggi) {
    alertify.warning("Tinggi harus diisi");
    return;
  }

  if (!Berat) {
    alertify.warning("Berat harus diisi");
    return;
  }

  if (!TglMasuk) {
    alertify.warning("Tanggal Masuk harus diisi");
    return;
  }

  if (!NPWP) {
    alertify.warning("NPWP harus diisi");
    return;
  }

  if (!Status) {
    alertify.warning("Status harus diisi");
    return;
  }

  if (!Aktif) {
    alertify.warning("Aktif harus diisi");
    return;
  }

  if (!Produksi) {
    alertify.warning("Produksi harus diisi");
    return;
  }

  if (!Penagih) {
    alertify.warning("Penagih harus diisi");
    return;
  }

  $.ajax({
    url: "{!! url('masterdaftarkaryawanspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      Nik,
      NamaLengkap,
      Alamat,
      HP,
      KodePos,
      Kelamin,
      KTP,
      TempatLahir,
      Email,
      TanggalLahir,
      Agama,
      PendidikanAkhir,
      Tinggi,
      Berat,
      TglMasuk,
      NPWP,
      Status,
      Aktif,
      Produksi,
      Penagih
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}

function submitAdd () {

  let _token = $("#_token").val();

  let Nik = $("#input_add_Nik").val();
    if (Nik.length > 20) {
    alertify.warning("NIK maximal 20 angka.");
    return;
  }

  let NamaLengkap = $("#input_add_NamaLengkap").val();
  let Alamat = $("#input_add_Alamat").val();
  let HP = $("#input_add_HP").val();
  let KodePos = $("#input_add_KodePos").val();
  let Kelamin = $("#input_add_Kelamin").val();
  let KTP = $("#input_add_KTP").val();
  let TempatLahir = $("#input_add_TempatLahir").val();
  let Email = $("#input_add_Email").val();
  let TanggalLahir = $("#input_add_TanggalLahir").val();
  let Agama = $("#input_add_Agama").val();
  let PendidikanAkhir = $("#input_add_PendidikanAkhir").val();
  let Tinggi = $("#input_add_Tinggi").val();
  let Berat = $("#input_add_Berat").val();
  let TglMasuk = $("#input_add_TglMasuk").val();
  let NPWP = $("#input_add_NPWP").val();
  let Status = $("#input_add_Status").val();
  let Aktif = $("#input_add_Aktif").val();
  let Produksi = $("#input_add_Produksi").val();
  let Penagih = $("#input_add_Penagih").val();

  console.log(Status)

    if (!Nik) {
      Nik = "";
    }

    if (!NamaLengkap) {
      NamaLengkap = "";
    }

    if (!Alamat) {
      Alamat = "";
    }

    if (!HP) {
      HP = "";
    }

    if (!KodePos) {
      KodePos = "";
    }

    if (!Kelamin) {
      Kelamin = "";
    }

    if (!KTP) {
      KTP = "";
    }

    if (!TempatLahir) {
      TempatLahir = "";
    }

    if (!Email) {
      Email = "";
    }

    if (!TanggalLahir) {
      TanggalLahir = "";
    }

    if (!Agama) {
      Agama = "";
    }

    if (!PendidikanAkhir) {
      PendidikanAkhir = "";
    }

    if (!Tinggi) {
      Tinggi = "";
    }

    if (!Berat) {
      Berat = "";
    }

    if (!TglMasuk) {
      TglMasuk = "";
    }

    if (!NPWP) {
      NPWP = "";
    }

    if (!Status) {
      Status = "";
    }

    if (!Aktif) {
      Aktif = "";
    }

    if (!Produksi) {
      Produksi = "";
    }

    if (!Penagih) {
      Penagih = "";
    }

    let keyNIKTemp = 0

  $.ajax({
    url: "{!! url('masterKaryawanGetKeyNIK') !!}",
    type: "get",
    async: false,
    success: function(res){
      keyNIKTemp = parseInt(res[0].KeyNIK) + 1
    }})

  $.ajax({
    url: "{!! url('masterdaftarkaryawanspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      keyNIK : keyNIKTemp,
      Nik,
      NamaLengkap,
      Alamat,
      HP,
      KodePos,
      Kelamin,
      KTP,
      TempatLahir,
      Email,
      TanggalLahir,
      Agama,
      PendidikanAkhir,
      Tinggi,
      Berat,
      TglMasuk,
      NPWP,
      Status,
      Aktif,
      Produksi,
      Penagih
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Jenis telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

window.onload = function (){
  loadAll();
};

</script>




@endsection
