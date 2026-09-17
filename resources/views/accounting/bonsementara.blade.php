@extends('accounting.newmaster')
@section('buttons')

@endsection

@section('css')
<style>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
</style>


<style>

#tabel_add_list_customer_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}

#tabel_add_list_customer_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

#tabel_add_list_noinvoice_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}
#tabel_add_list_noinvoice_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

#tabel_add_list_barang_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}
#tabel_add_list_barang_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

#tabel_add_list_nobeli_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}
#tabel_add_list_nobeli_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

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
@endsection


@section('content')


<div id="page1" class="container-fluid mainpage">
<div class="container-fluid" >



  <!-- <div id="qrcode"></div> -->
  <div class="row" style="margin-top: -30px">
    <div class="col-md-6 text-left">
      <h2>Bon Sementara</h2>
    </div>
    <div class="col-md-6 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600;  " onclick="buttonAdd()"  >+ Tambah Bon</button>
    </div>
  </div>

  <div class="row" style="">
    <div class="col-md-6 text-left">
      <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label>Perkiraan</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group input-group">
              <select id="input_perkiraan" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="loadAll()">
                      <!-- <option value='BKK' selected>BKK</option>
                      <option value='BKM' >BKM</option> -->
                      @for ($i = 0; $i < count($perkiraan); $i++)
                        <option value='{{ $perkiraan[$i]->Perkiraan }}' >{{ $perkiraan[$i]->Keterangan }} ( {{ $perkiraan[$i]->Perkiraan }} )</option>

                        @endfor

                    </select>
            </div>
          </div>
      </div>

    </div>
    <div class="col-6 text-right">
      <!-- <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600;  " onclick="buttonAdd()"  >+ Tambah Bon</button> -->
    </div>

  </div>
<!-- <button onclick="loadAll()">tes</button> -->
<!-- <button onclick="tesConcat()">tes</button> -->

<!-- <button onclick="setNewNoBukti()">tes</button> -->
<!-- <button onclick="buttonAdd('nobukti')">Add Tes</button> -->
</div>

<div id="printContainer" style="display:none">



</div>
<div id="contentContainer" class="container-fluid">
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
  <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
  <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card">
<div class="card-header">
<div class="row">
  <nav style="width: 100%;">
    <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
      <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true" style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; text-align: left;">Penambahan Bon</a>
      <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"
         style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
        Outstanding Bon
      </a>
    </div>
  </nav>
</div>
</div>
<div class="card-body" style="padding:0;">
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    <div class="row">
      <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
        <div class="container-fluid">

              <table id="tabel" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;"  scope="col">No. Bon</th>
                    <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                    <th style="padding: 4px 12px;"  scope="col">Penerima</th>
                    <th style="padding: 4px 12px;"  scope="col">Keterangan</th>
                    <th style="padding: 4px 12px;"  scope="col">Debet</th>
                    <th style="padding: 4px 12px;"  scope="col">Kredit</th>
                    <th style="padding: 4px 12px;"  scope="col">Saldo</th>
                    <th style="padding: 4px 12px;"  scope="col">Actions</th>
                  </tr>
                </thead>


                <tbody id="tabel_data" class="text-left" >
                  @for ($i = 0; $i < count($tempOutstanding); $i++)
                <tr>
                  <td>{{ $tempOutstanding[$i]->NoBukti }}</td>
                  <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i]->Tanggal)) !!}</td>

                  <td>{{ $tempOutstanding[$i]->Penerima }}</td>
                  <td>{{ $tempOutstanding[$i]->Keterangan }}</td>
                  <td class="text-right">{{ number_format($tempOutstanding[$i]->Debet , 2 ,'.' , ',') }}</td>
                  <td class="text-right">{{ number_format($tempOutstanding[$i]->Kredit , 2 ,'.' , ',') }}</td>
                  <td class="text-right">{{ number_format($tempOutstanding[$i]->Saldo , 2 ,'.' , ',') }}</td>





                  <td class='text-center'>
                    <button class="btn btn-primary btn-sm" type="button" onclick="buttonAddKredit('{{ $tempOutstanding[$i]->NoBukti }}','{{ $tempOutstanding[$i]->Penerima }}')"><i class="bi bi-file-earmark-minus" title="+ Kredit"></i></button>
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $tempOutstanding[$i]->NoBukti }}','{{ $tempOutstanding[$i]->Urut }}','{{ $tempOutstanding[$i]->Perkiraan }}')"><i class="bi bi-pen" title="Koreksi"></i></button>
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete('{{ $tempOutstanding[$i]->NoBukti }}','{{ $tempOutstanding[$i]->Urut }}','{{ $tempOutstanding[$i]->Perkiraan }}')"><i class="bi bi-trash" title="Hapus"></i></button>

                  </td>
                </tr>
                  @endfor
                </tbody>


              </table>
        </div>
      </div>
    </div>
  </div>

  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
    <div class="row">
      <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
        <div class="container-fluid">
          <table id="tabel2" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center bg-primary text-white">

              <tr>
                <th style="padding: 4px 12px;"  scope="col">No. Bon</th>
                <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                <th style="padding: 4px 12px;"  scope="col">Penerima</th>
                <th style="padding: 4px 12px;"  scope="col">Keterangan</th>
                <th style="padding: 4px 12px;"  scope="col">Debet</th>
                <th style="padding: 4px 12px;"  scope="col">Kredit</th>
                <th style="padding: 4px 12px;"  scope="col">Saldo</th>
                <th style="padding: 4px 12px;"  scope="col">Actions</th>
              </tr>
            </thead>

            <tbody id="tabel2_data" class="text-left">
              @for ($i = 0; $i < count($tempPenerimaan); $i++)
              <tr>
                <td>{{ $tempPenerimaan[$i]->NoBukti }}</td>
                <td>{!! date("Y/m/d", strtotime($tempPenerimaan[$i]->Tanggal)) !!}</td>
                <td>{{ $tempPenerimaan[$i]->Penerima }}</td>
                <td>{{ $tempPenerimaan[$i]->Keterangan }}</td>
                <td class="text-right">{{ number_format($tempPenerimaan[$i]->Debet , 2 ,'.' , ',') }}</td>
                <td class="text-right">{{ number_format($tempPenerimaan[$i]->Kredit , 2 ,'.' , ',') }}</td>
                <td class="text-right">{{ number_format($tempPenerimaan[$i]->Saldo , 2 ,'.' , ',') }}</td>

              <td class="text-center">
                    <button class="btn btn-primary btn-sm" type="button" onclick="buttonAddKredit('{{ $tempPenerimaan[$i]->NoBukti }}','{{ $tempPenerimaan[$i]->Penerima }}')"><i class="bi bi-file-earmark-minus" title="+ Kredit"></i></button>

                </td>
              </tr>
              @endfor
            </tbody>
          </table>

        </div>
      </div>
    </div>
  </div>




</div>
</div>
</div>


</div>
</div>












<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered"  role="document" style="">
    <div id="" class="modal-content ">

      <div id= "" class="">
      <div class="modal-header">


          <h5 class="modal-title" id="">Bon</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid" >
          <div class="row">


        <div class="col-md-2">
          <div class="form-group">
            <label>Tanggal</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <input type="date" class="form-control text-center" id="input_add_tanggal" placeholder="" >
          </div>
        </div>
      </div>

      <div class="row" style="margin-top: -10px">


        <div class="col-md-2">
          <div class="form-group">
            <label>No</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <input type="text" class="form-control" id="input_add_nobon" placeholder="" disabled>
          </div>
        </div>
      </div>

      <div class="row" style="margin-top: -10px">


        <div class="col-md-2">
          <div class="form-group">
            <label>Penerima</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <input type="text" class="form-control" id="input_add_penerima" placeholder="" >
          </div>
        </div>
      </div>

      <div class="row" style="margin-top: -10px">


        <div class="col-md-2">
          <div class="form-group">
            <label>Ket</label>
          </div>
        </div>
        <div class="col-md-10">
          <div class="form-group">
            <input type="text" class="form-control" id="input_add_keterangan" placeholder="" >
          </div>
        </div>
      </div>

      <div class="row" style="margin-top: -10px">


        <div class="col-md-2">
          <div class="form-group">
            <label>Jumlah</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_add_jumlah" placeholder="" >
          </div>
        </div>

        <div class="col-md-2">
          <div class="form-group">
            <label>Kredit</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_add_kredit" placeholder="" >
            <input type="hidden" class="form-control text-right" id="input_add_sisa" placeholder="" >
          </div>
        </div>
      </div>





          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

            </div>




        </div>





      </div>


      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" id="buttonSubmitAdd" class="btn btn-primary" onclick="submitAdd()">Submit</button>
        <button type="button" id="buttonSubmitEdit" class="btn btn-primary" onclick="submitEdit()">SubmitE</button>
      </div>
      </div>


      </div>

    </div>
  </div>

<!-- End modal add-->


  </div>


@endsection

@section('js')
<script type="text/javascript">

let dataBon = {}
let tipeform = ''
let tipeadd = ''
let tipeedit = ''

$(document).ready(function(){
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
          "columnDefs": [
          {  "className": "text-right", "targets": [] },
        ]
        });


        $("#tabel2").DataTable({
          "lengthChange": false,
            "paging": false ,
            "columnDefs": [
            {  "className": "text-right", "targets": [] },
          ]
          });



});


function submitEdit () {
  console.log('submitEdit')


  let checkDate = new Date($("#input_add_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let _token  = $("#_token").val()
  let tanggal  = $("#input_add_tanggal").val()
  let nobon  = $("#input_add_nobon").val()
  let penerima  = $("#input_add_penerima").val()
  let keterangan  = $("#input_add_keterangan").val()
  let jumlah  = $("#input_add_jumlah").val()
  let kredit  = $("#input_add_kredit").val()
  let perkiraan  = $("#input_perkiraan").val()
  let choice = "U"
  let urut = dataBon.Urut
  let devisi = '01'
  let valas = dataBon.KodeVls
  let kurs = dataBon.Kurs
  let debetd = 0
  let kreditd = 0
  let tglinput = new Date()

  if (Number(jumlah) > 0 && Number(kredit) > 0 ) {
    alertify.warning('DB/CR')
    return
  } else if (Number(jumlah) <= 0 && Number(kredit) <= 0) {
    alertify.warning("DB/CR")
    return
  }



  $.ajax({
      url: "{!! url('bonsementaraspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        nobon,
        tanggal,
        penerima,
        keterangan,
        jumlah,
        kredit,
        urut,
        devisi,
        tglinput,
        debetd,
        kreditd,
        perkiraan,
        tipeadd
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Bon telah ditambah');

          loadAll()

          $("#form").modal('toggle')

        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}


function submitAdd () {
  console.log('submitAdd')


  let checkDate = new Date($("#input_add_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let _token  = $("#_token").val()
  let tanggal  = $("#input_add_tanggal").val()
  let nobon  = $("#input_add_nobon").val()
  let penerima  = $("#input_add_penerima").val()
  let keterangan  = $("#input_add_keterangan").val()
  let jumlah  = $("#input_add_jumlah").val()
  let kredit  = $("#input_add_kredit").val()
  let perkiraan  = $("#input_perkiraan").val()
  let choice = "I"
  let urut = 0
  let devisi = '01'
  let valas = 'IDR'
  let kurs = 1
  let debetd = 0
  let kreditd = 0
  let tglinput = new Date()

  if (!penerima) {
    alertify.warning('Penerima harus diisi')
    return
  }

  let sisa = $("#input_add_sisa").val()

  if (tipeadd == 'kredit') {
    if (Number(kredit) <= 0 ) {

      alertify.warning("Jumlah <= 0")
      return
    }

    if (Number(kredit) > Number(sisa)) {
      alertify.warning("Kredit melebihi debet")
      return
    }


  } else {
    if (Number(jumlah) <= 0 ) {

      alertify.warning("Jumlah <= 0")
      return
    }
  }




  $.ajax({
      url: "{!! url('bonsementaraspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        nobon,
        tanggal,
        penerima,
        keterangan,
        jumlah,
        kredit,
        urut,
        devisi,
        tglinput,
        debetd,
        kreditd,
        perkiraan,
        tipeadd
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Bon telah ditambah');

          loadAll()

          $("#form").modal('toggle')

        }
        if (res == 2) {
          setNewNoBukti()
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}

function cleanFormAdd () {

  document.getElementById('input_add_nobon').value = ''
  document.getElementById('input_add_tanggal').valueAsDate = new Date()
  document.getElementById('input_add_penerima').value = ''
  document.getElementById('input_add_keterangan').value = ''
  document.getElementById('input_add_jumlah').value = '0.00'
  document.getElementById('input_add_kredit').value = '0.00'

}

function lockFormAdd (value= false) {
  document.getElementById('input_add_penerima').disabled = value
  document.getElementById('input_add_jumlah').disabled = value
  document.getElementById('input_add_kredit').disabled = !value
  // document.getElementById('input_add_tanggal').disabled = value

}



function buttonDelete (nobukti, urut, perkiraan) {
  // tipeform = 'edit'
  dataBon = {}
  console.log(nobukti, urut, perkiraan)
  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('bonsementaraspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      urut,
      perkiraan

    },
    success: function(res) {
      console.log(res)
      if (!res.detail.length) {
        alertify.warning("Data tidak ditemukkan")
        return
      } else {


        dataBon = res.detail[0]
        let tempAngka = 0
        if (Number(dataBon.Debet) > 0) {
          tipeedit = 'debet'
          tempAngka = parseFloat(dataBon.Debet).toFixed(2)
        } else {
          tipeedit = 'kredit'
          tempAngka = parseFloat(dataBon.Kredit).toFixed(2)
        }
        console.log(tipeedit)

        if( tipeedit == 'debet' && res.check.length > 0) {
          alertify.warning("Sudah ada transaksi kredit, tidak bisa hapus debet")
          return
        }




        alertify.confirm('Hapus Bon', 'Hapus Bon ' + nobukti + ` ${tipeedit} ${tempAngka} ?`,
            function() {
              let _token  = $("#_token").val()
              let tanggal  = $("#input_add_tanggal").val()
              let nobon  = nobukti
              let penerima  = $("#input_add_penerima").val()
              let keterangan  = $("#input_add_keterangan").val()
              let jumlah  = $("#input_add_jumlah").val()
              let kredit  = $("#input_add_kredit").val()
              let perkiraan  = $("#input_perkiraan").val()
              let choice = "D"
              let urut = dataBon.Urut
              let devisi = '01'
              let valas = dataBon.KodeVls
              let kurs = dataBon.Kurs
              let debetd = 0
              let kreditd = 0
              let tglinput = new Date()





              $.ajax({
                  url: "{!! url('bonsementaraspadd') !!}",
                  type: "post",
                  async: false,
                  data: {
                    _token,
                    choice,
                    nobon,
                    tanggal,
                    penerima,
                    keterangan,
                    jumlah,
                    kredit,
                    urut,
                    devisi,
                    tglinput,
                    debetd,
                    kreditd,
                    perkiraan,
                    tipeadd
                  },
                  success: function(res) {
                    console.log(res ,'!')

                    if (res == 1) {
                      // $("#form").modal('toggle')
                      alertify.success('Bon telah dihapus');

                      loadAll()


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

      }





    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function buttonKoreksi (nobukti, urut, perkiraan) {
  tipeform = 'edit'
  dataBon = {}
  lockFormAdd(true)

  $("#buttonSubmitAdd").hide()
  $("#buttonSubmitEdit").show()
  console.log(nobukti, urut, perkiraan)
  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('bonsementaraspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      urut,
      perkiraan

    },
    success: function(res) {
      console.log(res)
      if (!res.detail.length) {
        alertify.warning("Data tidak ditemukkan")
        return
      } else {


        dataBon = res.detail[0]

        document.getElementById('input_add_nobon').value = nobukti
        document.getElementById('input_add_tanggal').valueAsDate = new Date(dataBon.Tanggal)
        document.getElementById('input_add_jumlah').value = parseFloat(dataBon.Debet).toFixed(2)
        document.getElementById('input_add_kredit').value = parseFloat(dataBon.Kredit).toFixed(2)
        document.getElementById('input_add_penerima').value = dataBon.Penerima

        document.getElementById('input_add_keterangan').value = dataBon.Keterangan
        if (Number(dataBon.Debet) > 0) {
          document.getElementById("input_add_jumlah").disabled = false
          document.getElementById("input_add_kredit").disabled = true
          tipeedit = 'debet'
        } else {
          document.getElementById("input_add_jumlah").disabled = true
          document.getElementById("input_add_kredit").disabled = false
          tipeedit = 'kredit'
        }
        console.log(tipeedit)

        if( tipeedit == 'debet' && res.check.length > 0) {
          alertify.warning("Sudah ada transaksi kredit, tidak bisa dikoreksi")
          return
        } else {
          $("#form").modal('toggle')

        }

      }





    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function setNewNoBukti () {
  console.log('setNewNoBukti')
  let _token  = $("#_token").val()
  let bulan = $("#periode_bulan").val()
  let tahun = $("#periode_tahun").val()
  console.log(bulan, tahun)
  bulan = '0' + bulan
  let xbulan = bulan.slice(-2)
  let xtahun = tahun.slice(-2)
  console.log(xbulan, xtahun)
  let perkiraan  = $("#input_perkiraan").val()
  $.ajax({
    url: "{!! url('bonsementaraspnobukti') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      perkiraan
    },
    success: function(res) {

      console.log(res)
      if (res.length == 0) {
        document.getElementById("input_add_nobon").value = xtahun + xbulan + '001'

      } else {
        document.getElementById("input_add_nobon").value = Number(res[0].NoBukti) + 1

      }

    }})
}



function buttonAdd () {
  tipeform = 'add'
  tipeadd = 'nonkredit'
  lockFormAdd(false)
  cleanFormAdd()
  setNewNoBukti()
  $("#buttonSubmitAdd").show()
  $("#buttonSubmitEdit").hide()
  $("#form").modal('toggle')

}

function buttonAddKredit (nobukti, penerima) {
  lockFormAdd(true)
  cleanFormAdd()
  tipeadd = 'kredit'

    document.getElementById("input_add_nobon").value = nobukti
    document.getElementById("input_add_penerima").value = penerima
    let _token = $("#_token").val()
    let perkiraan = $("#input_perkiraan").val()
    $.ajax({
      url: "{!! url('bonsementaraspdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti,
        urut: 0,
        perkiraan

      },
      success: function(res) {


        document.getElementById("input_add_kredit").value = res.sisa[0].sisa
        document.getElementById("input_add_sisa").value = res.sisa[0].sisa




      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })
    $("#buttonSubmitAdd").show()
    $("#buttonSubmitEdit").hide()
  $("#form").modal('toggle')

}


function loadAll () {

  console.log('loadall')
  let _token = $("#_token").val();
  let perkiraan = $("#input_perkiraan").val()
  console.log(perkiraan)
  $.ajax({
    url: "{!! url('bonsementaraloadall') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      perkiraan
    },
    success: function(res) {
      console.log(res)
      let rowTable = ""
      let rowTable2 = ""

      res.tempOutstanding.forEach((item, i) => {
        rowTable += `
          <tr>
            <td>${item.NoBukti}</td>
            <td>${formatDate(item.Tanggal)}</td>

            <td>${item.Penerima}</td>
            <td>${item.Keterangan}</td>
            <td class="text-right">${formatAngkaX(item.Debet)}</td>
            <td class="text-right">${formatAngkaX(item.Kredit)}</td>
            <td class="text-right">${formatAngkaX(item.Saldo)}</td>

            <td class='text-center'>
            <button class="btn btn-primary btn-sm" type="button" onclick="buttonAddKredit('${item.NoBukti}','${item.Penerima}')"><i class="bi bi-file-earmark-minus" title="+ Kredit"></i></button>
            <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item.NoBukti}','${item.Urut}','${item.Perkiraan}')"><i class="bi bi-pen" title="Koreksi"></i></button>
            <button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete('${item.NoBukti}','${item.Urut}','${item.Perkiraan}')"><i class="bi bi-trash" title="Hapus"></i></button>

            </td>
          </tr>
        `

      });


      res.tempPenerimaan.forEach((item, i) => {

        rowTable2 += `
        <tr>
          <td>${item.NoBukti}</td>
          <td>${formatDate(item.Tanggal)}</td>
          <td>${item.Penerima}</td>
          <td>${item.Keterangan}</td>
          <td class="text-right">${formatAngkaX(item.Debet)}</td>
          <td class="text-right">${formatAngkaX(item.Kredit)}</td>
          <td class="text-right">${formatAngkaX(item.Saldo)}</td>

        <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonAddKredit('${item.NoBukti}','${item.Penerima}')"><i class="bi bi-file-earmark-minus" title="+ Kredit"></i></button>

          </td>
        </tr>
        `

      });


      $('#tabel').DataTable().destroy();
      $('#tabel2').DataTable().destroy();

      document.getElementById("tabel2_data").innerHTML = rowTable2
      document.getElementById("tabel_data").innerHTML = rowTable
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
          "columnDefs": [
          {  "className": "text-right", "targets": [] },
        ]
        });


        $("#tabel2").DataTable({
          "lengthChange": false,
            "paging": false ,
            "columnDefs": [
            {  "className": "text-right", "targets": [] },
          ]
          });



    }})

}

function formatDate(date , pemisah = '-') {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join(pemisah);
}


function formatAngkaX (angka) {
  if (!angka) {
    return '0.00'
  } else {
    return formatAngka(parseFloat(angka).toFixed(2))
  }

}

function formatAngka (angkaString) {

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


</script>

<script>
    const tabHome = document.getElementById('nav-home-tab');
    const tabProfile = document.getElementById('nav-profile-tab');

    function setActiveTab(homeActive) {
      if (homeActive) {
        tabHome.style.backgroundColor = '#007bff';
        tabHome.style.color = '#fff';
        tabProfile.style.backgroundColor = '#f8f9fa';
        tabProfile.style.color = '#007bff';
      } else {
        tabProfile.style.backgroundColor = '#007bff';
        tabProfile.style.color = '#fff';
        tabHome.style.backgroundColor = '#f8f9fa';
        tabHome.style.color = '#007bff';
      }
    }

    // Default warna tab
    setActiveTab(true);

    // buat ganti tab
    tabHome.addEventListener('click', function () {
      setActiveTab(true);
    });

    tabProfile.addEventListener('click', function () {
      setActiveTab(false);
    });
  </script>




@endsection
