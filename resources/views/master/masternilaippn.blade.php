@extends('master.newmaster')
@section('buttons')

@endsection
@section('content')

<div class="container-fluid">

  <!-- <div id="qrcode"></div> -->
<div class="row mt-4">
      <div class="col-6 text-left">
        <h2 style="margin-top:-85px;">Master Nilai PPN</h2>
      </div>
      <div class="col-6 text-right">
        <button type="button" class="btn btn-primary btn-lg" style="
            height: 30px; 
            margin-top: -150px; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="buttonAdd()">
          Add Nilai PPN
        </button>
      </div>
      {{-- <div class="col-6 text-right">
        <button type="button" class="btn btn-primary btn-lg" style="
            height: 30px; 
            margin-top: -150px; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="loadAll()">
          tes load all
        </button>
      </div> --}}
    </div>
<!-- <button onclick="loadAll()">tes</button> -->
</div>

<div id="printContainer" style="display:none">

</div>
<div id="contentContainer" class="container-fluid" style="max-width: 900px; margin-top:-95px;">
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
          <div class="row mt-4">
              <!-- <div class="col-12 text-right">
                  <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonAdd()"  >Add Koreksi Stock Gudang</button>
              </div> -->
          </div>
          <div class="row mt-3">
            <div class="col-12" style="overflow:auto;">
              <div class="">

                    <table id="tabel" class="table table-bordered table-striped"  >
                      <thead id='theadCustom' class="text-center">
                        <tr>
                          <th scope="col">Action</th>
                          <th scope="col">PPN</th>
                          <th scope="col">Tanggal Awal</th>
                          <th scope="col">Tanggal Akhir</th>

                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-left">
                      </tbody>


                    </table>
              </div>
            </div>
          </div>


</div>


<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Urut</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kode" placeholder="No. Urut PPN"disabled>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Awal</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_add_tglAwal">
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Akhir</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_add_tglAkhir">
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">PPN</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_PPN" placeholder="PPN">
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





<!-- start modal edit -->
<div class="modal fade"  id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Urut PPN</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_kode" disabled>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Awal</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_edit_tglAwal">
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal Akhir</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_edit_tglAkhir">
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">PPN</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_PPN" placeholder="PPN">
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
<!-- End modal edit-->







@endsection

@section('js')
<script type="text/javascript">

let dataRefresh = []

function loadAll () {
  let _token = $("#_token").val();


  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('masternilaippnloadall') !!}",
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
    let dbDateString = item.TglAwal;
    let dbDateString2 = item.TglAkhir;

    // Convert the database date string to a JavaScript Date object
    let dateObject = new Date(dbDateString);
    let dateObject2 = new Date(dbDateString2);

    // Extract day, month, and year components
    let day = dateObject.getDate();
    let month = dateObject.getMonth() + 1; // Months are 0-based, so add 1
    let year = dateObject.getFullYear(); // Getting last 2 digits of year
    let day2 = dateObject2.getDate();
    let month2 = dateObject2.getMonth() + 1; // Months are 0-based, so add 1
    let year2 = dateObject2.getFullYear(); // Getting last 2 digits of year

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
      <button class="btn btn-success btn-sm hover-tooltip" data-tooltip='Edit Nilai PPN' type="button" onclick="buttonEdit('${item.Urut}')"><i class="bi bi-pen"></i></button>
      <button class="btn btn-danger btn-sm hover-tooltip" data-tooltip='Delete Nilai PPN' type="button" onclick="buttonDelete('${item.Urut}')"><i class="bi bi-trash"></i></button>
    </td>
    <td>${item.NilaiPPN}</td>
    <td>${formattedDate}</td>
    <td>${formattedDate2}</td>
    </tr>`
  });





  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable({
    "lengthChange": false,
      "paging": false ,
    });

}
//
function buttonAdd () {


  $("#form").modal('toggle')

}

function buttonEdit(kode) {
  console.log(kode);
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masternilaippnspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kode: kode
    },
    success: function (res) {
      console.log(res);
      document.getElementById("input_edit_kode").value = res[0].Urut;

      // Convert datetime values to Date objects
      const tglAwal = new Date(res[0].TglAwal + 'Z'); // Add 'Z' to indicate UTC time
      const tglAkhir = new Date(res[0].TglAkhir + 'Z');

      // Format the date for input elements
      const formattedTglAwal = tglAwal.toISOString().split('T')[0];
      const formattedTglAkhir = tglAkhir.toISOString().split('T')[0];

      document.getElementById("input_edit_tglAwal").value = formattedTglAwal;
      document.getElementById("input_edit_tglAkhir").value = formattedTglAkhir;
      document.getElementById("input_edit_PPN").value = res[0].NilaiPPN;
    }
  });
  $("#formEdit").modal('toggle');
}



function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Area', 'Apakah yakin ingin menghapus Jenis ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masternilaippnspdelete') !!}",
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
              alertify.success("Jenis telah dihapus");

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
  let kode = $("#input_edit_kode").val();
  let tglAwal = $("#input_edit_tglAwal").val();
  let tglAkhir = $("#input_edit_tglAkhir").val();
  let PPN = $("#input_edit_PPN").val();
  console.log(kode)

  $.ajax({
    url: "{!! url('masternilaippnspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      tglAwal,
      tglAkhir,
      PPN
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
//
function submitAdd () {

  let _token = $("#_token").val();
  let kode = $("#input_add_kode").val();
  let tglAwal = $("#input_add_tglAwal").val();
  let tglAkhir = $("#input_add_tglAkhir").val();
  let PPN = $("#input_add_PPN").val();

  console.log(kode)
  console.log(tglAwal)
  console.log(tglAkhir)
  console.log(PPN)

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!tglAwal) {
    alertify.warning("Tanggal harus diisi");
    return
  }

  if (!tglAkhir) {
    alertify.warning("Tanggal harus diisi");
    return
  }

  if (!PPN) {
    alertify.warning("PPN harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masternilaippnspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      tglAwal,
      tglAkhir,
      PPN
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Nilai PPN telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

// Function to set the default Urut value
function setDefaultUrut() {
  // Make an asynchronous request to get the highest Urut value from the database
  $.ajax({
    url: 'masternilaippndefault',
    method: 'GET',
    success: function (data) {
      const highestUrut = Number(data.highestUrut) || 0;
      const newUrut = highestUrut + 1;

      console.log('Retrieved highestUrut:', highestUrut);
      console.log('New Urut:', newUrut);
      $("#input_add_kode").val(newUrut);
    },
    error: function (error) {
      console.error('Error fetching highest Urut value:', error);
    },
  });
}

// Call the setDefaultUrut function when the modal is opened
$("#form").on("show.bs.modal", function () {
  setDefaultUrut();
});


window.onload = function (){
  loadAll();
};


</script>




@endsection
