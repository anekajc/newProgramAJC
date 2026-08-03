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
    <span class="sp-crumb-active">Perkiraan</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Perkiraan</h1>
    </div>
    <button class="btn btn-primary" onclick="buttonAdd()">+ Add Perkiraan</button>
  </div> --}}

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

@include('master.partials.headerTableMaster')

  <div class="table-outer">
    <div class="table-wrap">
      <table class="tb" id="tabel">
        <thead>
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
            <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
            <th style="padding: 4px 12px;" scope="col">Keterangan</th>
            <th style="padding: 4px 12px;" scope="col">Kelompok</th>
            <th style="padding: 4px 12px;" scope="col">Tipe</th>
            <th style="padding: 4px 12px;" scope="col">Transaksi</th>
            <th style="padding: 4px 12px;" scope="col">Valas</th>
            <th style="padding: 4px 12px;" scope="col">Simbol</th>
            <th style="padding: 4px 12px;" scope="col">PPN</th>
            <th style="padding: 4px 12px;" scope="col">Status</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-right">
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document">
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
              <div class="col-3 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Perkiraan</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_perkiraan" placeholder="Perkiraan" onblur="onChangePerkiraan()">
                </div>
              </div>
              <div class="col-2 ">
                <div class="form-group text-center">
                  <label class="text-left">PPN</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select id="input_add_isppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                    <option value=0>False</option>
                    <option value=1>True</option>
                  </select>
                </div>
              </div>

            </div>

          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Keterangan</label>
              </div>
            </div>
            <div class="col-9">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_keterangan" placeholder="Keterangan">
              </div>
            </div>

            <!-- <div class="col-2 text-right">
              <div class="form-group">
            <button onclick="resetScannerKode2()" class="btn btn-success btn-sm text-right"><i class="bi bi-arrow-clockwise"></i></button>
            </div>
            </div> -->
          </div>

          <div class="row mt-3">
            <div class="col-3">
              <div class="form-group">
                <label>Kelompok</label>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <select id="input_add_kelompok" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  <option value=0 selected >Aktiva</option>
                  <option value=1 >Kewajiban</option>
                  <option value=2 >Modal</option>
                  <option value=3 >Pendapatan</option>
                  <option value=4 >Biaya</option>
                </select>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group text-center">
                <label>Tipe</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <select id="input_add_tipe" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  <option value=0 >General</option>
                  <option value=1 >Detail</option>
                </select>
              </div>
            </div>

          </div>
          <!-- <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Tipe</label>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_kode2" placeholder="Barcode Lokasi" onkeypress="enterScannerKode2(event)">
              </div>
            </div>

          </div> -->
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Debet/Kredit</label>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <select id="input_add_debetkredit" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  <option value=0 >Debet</option>
                  <option value=1 >Kredit</option>
                </select>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group text-center">
                <label>Valas</label>
              </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                  <select id="input_add_valas" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                    @foreach ($listDataValas as $valas)
                        <option value="{{ $valas->KODEVLS }}" data-kurs="{{ $valas->Simbol }}">{{ $valas->KODEVLS }}</option>
                     @endforeach
                    </select>
                </div>
            </div>

          </div>
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Status</label>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <select id="input_add_status" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  <option value=0 >Active</option>
                  <option value=1 >Inactive</option>
                </select>
              </div>
            </div>

            <div class="col-2">
              <div class="form-group text-center">
                <label>Simbol</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_simbol" placeholder="Simbol">
              </div>
            </div>

          </div>



        <!-- <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary" onclick="buttonAddItem()" class="btn btn-secondary"  >Add Item</button>
        </div>

        </div>



        </div> -->

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
<div class="modal fade" id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Perkiraan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

            <div class="row">
              <div class="col-3 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Perkiraan</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_perkiraan" placeholder="Perkiraan" disabled>
                </div>
              </div>
              <div class="col-2 ">
                <div class="form-group text-center">
                  <label class="text-left">PPN</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select id="input_edit_isppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                    <option value=0>False</option>
                    <option value=1>True</option>
                  </select>
                </div>
              </div>



            </div>

          <div class="row ">
            <div class="col-3">
              <div class="form-group">
                <label>Keterangan</label>
              </div>
            </div>
            <div class="col-9">
              <div class="form-group">
                <input type="text" class="form-control" id="input_edit_keterangan" placeholder="Keterangan">
              </div>
            </div>

            <!-- <div class="col-2 text-right">
              <div class="form-group">
            <button onclick="resetScannerKode2()" class="btn btn-success btn-sm text-right"><i class="bi bi-arrow-clockwise"></i></button>
            </div>
            </div> -->
          </div>

          <div class="row mt-3">
            <div class="col-3">
              <div class="form-group">
                <label>Kelompok</label>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <select id="input_edit_kelompok" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  <option value=0 selected >Aktiva</option>
                  <option value=1 >Kewajiban</option>
                  <option value=2 >Modal</option>
                  <option value=3 >Pendapatan</option>
                  <option value=4 >Biaya</option>
                </select>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group text-center">
                <label>Tipe</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <select id="input_edit_tipe" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  <option value=0 >General</option>
                  <option value=1 >Detail</option>
                </select>
              </div>
            </div>

          </div>
          <!-- <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Tipe</label>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <input type="text" class="form-control" id="input_edit_kode2" placeholder="Barcode Lokasi" onkeypress="enterScannerKode2(event)">
              </div>
            </div>

          </div> -->
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Debet/Kredit</label>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <select id="input_edit_debetkredit" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  <option value=0 >Debet</option>
                  <option value=1 >Kredit</option>
                </select>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group text-center">
                <label>Valas</label>
              </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <select id="input_edit_valas" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                    @foreach ($listDataValas as $valas)
                        <option value="{{ $valas->KODEVLS }}" data-kurs="{{ $valas->Simbol }}">{{ $valas->KODEVLS }}</option>
                     @endforeach
                    </select>
                </div>
            </div>

          </div>
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Status</label>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <select id="input_edit_status" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  <option value=0 >Active</option>
                  <option value=1 >Inactive</option>
                </select>
              </div>
            </div>

            <div class="col-2">
              <div class="form-group text-center">
                <label>Simbol</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_edit_simbol" placeholder="Simbol">
              </div>
            </div>

          </div>



        <!-- <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary" onclick="buttonAddItem()" class="btn btn-secondary"  >Add Item</button>
        </div>

        </div>



        </div> -->

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


function onChangePerkiraan () {
  let perkiraan = $("#input_add_perkiraan").val()
  let xperkiraan = perkiraan.slice(0, perkiraan.length - 1)
  // let xperkiraan2 = perkiraan.slice(0 , perkiraan.length - 2)
  // return
  console.log(perkiraan, xperkiraan)
  // let xperkiraan3 = '1'
  // if(perkiraan.length) {
  //   xperkiraan3 = perkiraan.slice(0 , 1)
  // }
  // console.log(perkiraan)
  // console.log(xperkiraan, xperkiraan2)
  $.ajax({
    url: "{!! url('newperkiraangetallperkiraan') !!}",
    type: "get",
    async: false,
    data: {
      xperkiraan,
      // xperkiraan2,
      // xperkiraan3
    },
    success: function(res) {
      console.log(res)
      let xdata = {}
      // if (res.list.length) {
      //   xdata = res.list[res.list.length - 1]
      //
      // } else {
      //   if (res.list2.length) {
      //     xdata = res.list2[res.list2.length - 1]
      //
      //   } else {
      //
      //   }
      //
      // }
      // console.log(xdata)
      // if( xdata == {}) {
      //   console.log('if bawah')
      //   if (res.list3.length) {
      //
      //       xdata = res.list3[res.list3.length - 1]
      //   }
      // }
      //
      // if(xdata == {}) {
      //
      // } else {

        if (!res.length) {
          return
        }
        xdata = res[res.length - 1]
        document.getElementById("input_add_keterangan").value = xdata.Keterangan


        document.getElementById("input_add_isppn").value = Number(xdata.IsPPN)
        document.getElementById("input_add_kelompok").value = xdata.Kelompok
        document.getElementById("input_add_valas").value = xdata.Valas
        document.getElementById("input_add_tipe").value = xdata.Tipe
        document.getElementById("input_add_simbol").value = xdata.Simbol
        document.getElementById("input_add_debetkredit").value = xdata.DK
        let tempStatus = 1
        if (xdata.Status === "Aktif") {
          tempStatus = 0
        }
        document.getElementById("input_add_status").value = tempStatus



    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }})


}

  function buttonAdd () {
            document.getElementById("input_add_perkiraan").value = ''
            document.getElementById("input_add_isppn").value = 0
            document.getElementById("input_add_keterangan").value = ''
            document.getElementById("input_add_kelompok").value = 0
            document.getElementById("input_add_tipe").value = 0
            document.getElementById("input_add_debetkredit").value = 0
            document.getElementById("input_add_status").value = 0
            document.getElementById("input_add_simbol").value = ''
        $("#form").modal('toggle')

  }

  // function loadValas() {
  //     // Fetch data from the server
  //     $.ajax({
  //         url: "{!! url('newperkiraanloadvalas') !!}",
  //         type: "get",
  //         async: false,
  //         success: function(res) {
  //             console.log(res);
  //             populateValasDropdown(res);
  //         },
  //         error: function(xhr, status, error) {
  //             console.error(xhr.responseText);
  //         }
  //     });
  // }
  //
  // function populateValasDropdown(data) {
  //     let valasOptions = '';
  //     data.forEach((item) => {
  //         valasOptions += `<option value="${item.KODEVLS}">${item.KODEVLS}</option>`;
  //     });
  //
  //     // Set the HTML content of the select dropdown
  //     $('#input_add_valas').html(valasOptions);
  // }
  //
  // // function loadValasEdit() {
  // //     // Fetch data from the server
  // //     $.ajax({
  // //         url: "{!! url('newperkiraanloadvalas') !!}",
  // //         type: "get",
  // //         async: false,
  // //         success: function(res) {
  // //             console.log(res);
  // //             populateValasDropdownEdit(res);
  // //         },
  // //         error: function(xhr, status, error) {
  // //             console.error(xhr.responseText);
  // //         }
  // //     });
  // // }
  // //
  // // function populateValasDropdownEdit(data) {
  // //     let valasOptions = '';
  // //     data.forEach((item) => {
  // //         valasOptions += `<option value="${item.KODEVLS}">${item.KODEVLS}</option>`;
  // //     });
  // //     $('#input_edit_valas').html(valasOptions);
  // // }

  function loadAll () {
    console.log('asd')
    let _token = $("#_token").val();

     document.getElementById('breadcrumb').innerHTML = "Master Perkiraan"

    $('#tabel').DataTable().destroy();

    $.ajax({
      url: "{!! url('newperkiraanloadall') !!}",
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
      if (item.IsPPN == 0) {
        temp = '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
      } else {
        temp = '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
      }
      rowTable += `<tr>
        
    <td style="white-space:nowrap;" class='text-center'>
      <div class="action-buttons-wrap">
          <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.Perkiraan}')"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.Perkiraan}')"><i class="bi bi-trash"></i></button>
      </div>
    </td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
      <td>${item.mKelompok}</td>
      <td>${item.mtipe}</td>
      <td>${item.mDK}</td>
      <td>${item.Valas}</td>
      <td>${item.Simbol}</td>
      `+ temp +`
      ${
          item.Status == 'Tidak Aktif'
              ? '<td><span class="sp-badge is-user">Tidak Aktif</span></td>'
              : '<td><span class="sp-badge is-supervisor">Aktif</span></td>'
      }
      </tr>`
    });


    document.getElementById("tabel_data").innerHTML = rowTable

    $("#tabel").DataTable({
      "lengthChange": true,
      "paging": true,
      "searching": true,
      "dom": 'tip'
    });

  }
  
$("#tabel_filter_visual").on("keyup", function () {
  $("#tabel").DataTable().search(this.value).draw();
});

$("#tabel_length_visual").on("change", function () {
  $("#tabel").DataTable().page.len(Number(this.value)).draw();
});


  function buttonEdit (perkiraan) {

      console.log(perkiraan)
        let _token = $("#_token").val();
      console.log('a')
        $.ajax({
          url: "{!! url('newdetailperkiraan') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            perkiraan
          },
          success: function(res) {
            console.log('tes')
            console.log(res ,'!')

            if (!res.length) {
              alertify.warning("Data tidak ditemukkan")
              return
            }
            document.getElementById("input_edit_perkiraan").value = res[0].Perkiraan
            document.getElementById("input_edit_keterangan").value = res[0].Keterangan


            document.getElementById("input_edit_isppn").value = Number(res[0].IsPPN)
            document.getElementById("input_edit_kelompok").value = res[0].Kelompok
            document.getElementById("input_edit_valas").value = res[0].Valas
            document.getElementById("input_edit_tipe").value = res[0].Tipe
            document.getElementById("input_edit_simbol").value = res[0].Simbol
            document.getElementById("input_edit_debetkredit").value = res[0].DK
            let tempStatus = 1
            if (res[0].Status === "Aktif") {
              tempStatus = 0
            }
            document.getElementById("input_edit_status").value = tempStatus

          }})




        $("#formEdit").modal('toggle')

  }

  function submitEdit () {
    let _token = $("#_token").val();
    let choice = "U"
    let perkiraan = $("#input_edit_perkiraan").val();
    let keterangan = $("#input_edit_keterangan").val();
    let kelompok = $("#input_edit_kelompok").val();
    let tipe = $("#input_edit_tipe").val();
    let valas = $("#input_edit_valas").val();
    let debetkredit = $("#input_edit_debetkredit").val();
    let neraca = "tes"
    let simbol = $("#input_edit_simbol").val();
    let isppn = $("#input_edit_isppn").val();
    let lokasi = 0
    let isaktif = $("#input_edit_status").val();

      if (!simbol) {
    simbol = '-';
  }

  if (simbol.length > 3) {
    alertify.warning("Simbol Hanya boleh 3 huruf");
    return;
  }

    // console.log('perkiraan' ,perkiraan)
    // console.log('isppn' ,isppn)
    // console.log('keterangan' ,keterangan)
    // console.log('kelompok' ,kelompok)
    // console.log('tipe' ,tipe)
    // console.log('debetkredit' ,debetkredit)
    // console.log('valas' ,valas)
    // console.log('status' ,status)
    // console.log('simbol' ,simbol)
    // console.log('isaktif' ,isaktif)


    $.ajax({
      url: "{!! url('newaddperkiraan') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        choice ,
        perkiraan ,
        keterangan ,
        kelompok ,
        tipe ,
        valas ,
        debetkredit ,
        neraca ,
        simbol ,
        isppn ,
        lokasi ,
        isaktif
      },
      success: function(res) {
        console.log(res ,'!')
        $("#formEdit").modal('toggle')
        alertify.success("Perkiraan telah diedit");
        loadAll ()
      }})
  }


  function submitAdd() {

    console.log('asd')
    let _token = $("#_token").val();
    let choice = "I"
    let perkiraan = $("#input_add_perkiraan").val();
    let keterangan = $("#input_add_keterangan").val();
    let kelompok = $("#input_add_kelompok").val();
    let tipe = $("#input_add_tipe").val();
    let valas = $("#input_add_valas").val();
    let debetkredit = $("#input_add_debetkredit").val();
    let neraca = "tes"
    let simbol = $("#input_add_simbol").val();
    let isppn = $("#input_add_isppn").val();
    let lokasi = 0
    let isaktif = $("#input_add_status").val();
    if (!perkiraan) {
      alertify.warning("Perkiraan tidak boleh kosong");
      return
    }
    // console.log('perkiraan' ,perkiraan)
    // console.log('isppn' ,isppn)
    // console.log('keterangan' ,keterangan)
    // console.log('kelompok' ,kelompok)
    // console.log('tipe' ,tipe)
    // console.log('debetkredit' ,debetkredit)
    // console.log('valas' ,valas)
    // console.log('status' ,status)
    // console.log('simbol' ,simbol)
    // console.log('isaktif' ,isaktif)
  if (!simbol) {
    simbol = '-';
  }

  if (simbol.length > 3) {
    alertify.warning("Simbol Hanya boleh 3 huruf");
    return;
  }

    $.ajax({
      url: "{!! url('newaddperkiraan') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        choice ,
        perkiraan ,
        keterangan ,
        kelompok ,
        tipe ,
        valas ,
        debetkredit ,
        neraca ,
        simbol ,
        isppn ,
        lokasi ,
        isaktif
      },
      success: function(res) {
        console.log(res ,'!')
        if (res == 0) {
          alertify.warning("Perkiraan sudah ada");
        } else {
          alertify.success("Perkiraan telah ditambah");
          loadAll ()
          
            document.getElementById("input_add_perkiraan").value = ''
            document.getElementById("input_add_isppn").value = 0
            document.getElementById("input_add_keterangan").value = ''
            document.getElementById("input_add_kelompok").value = 0
            document.getElementById("input_add_tipe").value = 0
            document.getElementById("input_add_debetkredit").value = 0
            document.getElementById("input_add_status").value = 0
            document.getElementById("input_add_simbol").value = ''

        }
      }})
  }

  function buttonDelete(perkiraan) {



      let _token = $("#_token").val();

    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus perkiraan ' + perkiraan + ' ?',
        function() {
          console.log('yes')
          let choice = "D"

          $.ajax({
            url: "{!! url('newaddperkiraan') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              choice ,
              perkiraan ,
              keterangan: '' ,
              kelompok: 0,
              tipe :0,
              valas :'IDR',
              debetkredit: 0 ,
              neraca: 'tes' ,
              simbol: 'tes' ,
              isppn: 0 ,
              lokasi: 0 ,
              isaktif: 0
            },
            success: function(res) {

              if (res != 1) {
                alertify.warning(res);
              } else {
                console.log(res)
                loadAll()
                alertify.success("Perkiraan telah dihapus");
              }


              // console.log(res ,'!')
              // alertify.success("Perkiraan telah didelete");
              // loadAll ()
            }})
        }
      ,function(){
        console.log('no')
      });
  }

window.onload = function(){
  loadAll();
}

</script>

@endsection
