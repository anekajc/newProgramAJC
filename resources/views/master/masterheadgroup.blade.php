@extends('newmaster')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">

 <link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}?v={{ filemtime(public_path('css/tableMaster2.css')) }}">


  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Head Group</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Head Group</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Head Group</button>
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
                    <th scope="col">Kode Head Group</th>
                    <th scope="col">Nama Head Group</th>
                    <th scope="col">Group</th>
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
                  <label class="text-left">Kode HDGroup</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kode" placeholder="Kode Group">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama HDGroup</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_nama" placeholder="Nama Group">
                </div>
              </div>

            </div>
            <div class="row mt-2">
              <div class="col-4">
                <div class="form-group text-left">
                  <label class="text-left">Kode Group</label>
                </div>
              </div>
              <div class="col-8">
                <select id="input_add_kodegroup" class="form-control" aria-label="Default select example">
                  <option selected>Open this select menu</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
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
                  <label class="text-left">Kode HDGroup</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" disabled id="input_edit_kode" placeholder="Kode Group">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama HDGroup</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_nama" placeholder="Nama Group">
                </div>
              </div>

            </div>
            <div class="row mt-2">
              <div class="col-4">
                <div class="form-group text-left">
                  <label class="text-left">Kode Group</label>
                </div>
              </div>
              <div class="col-8">
                <select id="input_edit_kodegroup" class="form-control" aria-label="Default select example">

                </select>
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


<!-- start modal subgroup -->
<div class="modal fade"  id="formSubGroup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Sub Group</h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid" style='height:32px;'>
          <div class="row">
            <input type="hidden" id="input_subgroup_kodegroup" value="" />
            <div class="col-2">
              <div class="form-group">
                <label>Kode Head Group</label>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group">
                <input type="text" class="form-control" id="input_subgroup_kodehdgroup" placeholder="Kode Headgroup" disabled>
              </div>
            </div>

            <div class="col-2 ml-auto text-right">
              <button type="button" class="btn btn-primary" onclick="buttonAddSubGroup()">Add Item</button>
            </div>
          </div>
        </div>

    <!-- ADD SUBGROUP -->

    <div id="addSubGroup" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row mt-2">
              <div class="col-4">
                <h4>Add Subgroup</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Kode Subgroup</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_subgroup_add_kodesubgroup" type="text" class="form-control">
              </div>
              <div class="col-2">
                <div class="form-group">
                <label>Perkiraan Persiapan</label>
              </div>
              </div>
              
              <div class="col-md-4">
                  <div class="input-group">
                      <input type="text" class="form-control" id="input_add_perkPers">
                      <div class="input-group-append">
                          <button type="button" class="btn btn-primary btn-select" onclick="buttonPerkiraanSubGroup('1')">+</button>
                      </div>
                  </div>
              </div>

            </div>
            <div class="row mt-2">
              <div class="col-2">
                <div class="form-group">
                <label>Nama Subgroup</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_subgroup_add_namasubgroup" type="text" class="form-control">
              </div>
              <div class="col-2">
                <div class="form-group">
                <label>Perkiraan Jual</label>
              </div>
              </div>
              
              <div class="col-md-4">
                  <div class="input-group">
                      <input type="text" class="form-control" id="input_add_perkJual">
                      <div class="input-group-append">
                          <button type="button" class="btn btn-primary btn-select" onclick="buttonPerkiraanSubGroup('2')">+</button>
                      </div>
                  </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowHideSubGroup()" >Batal</button>
                <button type="button" onclick="submitAddSubGroup()" class="btn btn-primary" >Add</button>
              </div>

            </div>
          </div>

    <!-- END ADD SUBGROUP -->

    <!-- EDIT SUBGROUP -->

    <div id="editSubGroup" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Edit Subgroup</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Kode Subgroup</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_subgroup_edit_kodesubgroup" type="text" class="form-control" disabled>
              </div>
              <div class="col-2">
                <div class="form-group">
                <label>Perkiraan Persiapan</label>
              </div>
              </div>
              <div class="col-4">
                  <div class="input-group">
                      <input type="text" class="form-control" id="input_edit_perkPers">
                      <div class="input-group-append">
                          <button type="button" class="btn btn-primary btn-select" onclick="buttonPerkiraanSubGroup('3')">+</button>
                      </div>
                  </div>
              </div>

            </div>
            
            <div class="row mt-2">
              <div class="col-2">
                <div class="form-group">
                <label>Nama Subgroup</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_subgroup_edit_namasubgroup" type="text" class="form-control">
              </div>
              <div class="col-2">
                <div class="form-group">
                  <label>Perkiraan Jual</label>
                </div>
              </div>
              <div class="col-4">
                  <div class="input-group">
                      <input type="text" class="form-control" id="input_edit_perkJual">
                      <div class="input-group-append">
                          <button type="button" class="btn btn-primary btn-select" onclick="buttonPerkiraanSubGroup('4')">+</button>
                      </div>
                  </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowHideSubGroup()" >Batal</button>
                <button type="button" onclick="submitEditSubGroup()" class="btn btn-primary" >Edit</button>
              </div>

            </div>
          </div>

    <!-- END EDIT SUBGROUP -->

        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_subgroup" class="table table-bordered table-striped"  >
              <thead class="text-center">
                <tr id='theadCustom'>
                  <th scope="col">Actions</th>
                  <th scope="col">Kode Sub Group</th>
                  <th scope="col">Nama Sub Group</th>

                </tr>
              </thead>


              <tbody id="tabel_data_subgroup" class="text-left" >

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
  </div>

</div>
</div>
</div>
<!-- End modal subgroup-->

<!-- start modal subkategori -->
<div class="modal fade"  id="formSubKategori" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Sub Kategori</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <div class="row">

            <!-- <input type="hidden" id="input_subkategori_kodegroup" value="" /> -->
            <div class="col-2">
              <div class="form-group">
                <label>Kode SubGroup</label>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group">
                <input type="text" class="form-control" id="input_subkategori_kodesubgroup" placeholder="Kode Subgroup" disabled>
              </div>
            </div>
            
            <div class="col-md-2 ml-auto text-right">
              <button type="button" class="btn btn-primary" onclick="buttonAddSubKategori()" class="btn btn-secondary"  >Add Item</button>
            </div>
          </div>
    </div>

    <!-- ADD SUBGROUP -->

    <div id="addSubKategori" class="container-fluid showhidekategori">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Add Subkategori</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Kode SubKategori</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_subkategori_add_kodesubkategori" type="text" class="form-control">
              </div>

            </div>
            <div class="row">

              <div class="col-2">
                <div class="form-group">
                <label>Nama SubKategori</label>
              </div>

              </div>
              <div class="col-4">
                <input id="input_subkategori_add_namasubkategori" type="text" class="form-control">
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowHideSubKategori()" >Batal</button>
                <button type="button" onclick="submitAddSubKategori()" class="btn btn-primary" >Add</button>
              </div>

            </div>
          </div>

    <!-- END ADD SUBGROUP -->

    <!-- EDIT SUBGROUP -->

    <div id="editSubKategori" class="container-fluid showhidekategori">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Edit Kategori</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Kode Kategori</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_subkategori_edit_kodesubkategori" type="text" class="form-control" disabled>
              </div>
              <!-- <div class="col-2">
                <div class="form-group">
                <label>Perkiraan Persiapan</label>
              </div>
              </div>
              <div class="col-4">

                <select id="input_subkategori_edit_perkpers" class="form-control" aria-label="Default select example">
                  <option selected>Open this select menu</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div> -->

            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Nama Kategori</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_subkategori_edit_namasubkategori" type="text" class="form-control">
              </div>
              <!-- <div class="col-2">
                <div class="form-group">
                <label>Perkiraan Jual</label>
              </div>
              </div>
              <div class="col-4">

                <select id="input_subkategori_edit_perkjual" class="form-control" aria-label="Default select example">
                  <option selected>Open this select menu</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div> -->
            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowHideSubKategori()" >Batal</button>
                <button type="button" onclick="submitEditSubKategori()" class="btn btn-primary" >Edit</button>
              </div>

            </div>
          </div>

    <!-- END EDIT SUBGROUP -->

        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_subkategori" class="table table-bordered table-striped"  >
              <thead class="text-center">
                <tr id='theadCustom'>
                  <th scope="col">Actions</th>
                  <th scope="col">Kode Sub Kategori</th>
                  <th scope="col">Nama Sub Kategori</th>

                </tr>
              </thead>


              <tbody id="tabel_data_subkategori" class="text-left" >

                <tr >

                  <td></td>
                  <td></td>


                    <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    </td>
              </tr>
              </tbody>


            </table>
          </div>
            <!-- <button onclick="buttonSubKategori()">tes</button> -->


    </div>
  </div>
  <!-- <div class="modal-footer">
     
    <button type="button" class="btn btn-primary" onclick="">Submit</button>
  </div> -->
</div>
</div>
</div>
<!-- End modal subkategori-->


<!-- start modal select add akumulasi penyusutan -->
<div class="modal fade"  id="formAddBiayaPenyusutan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Biaya Penyusutan</h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelAddBiayaPenyusutan" class="table table-bordered table-striped"  >
          <thead id='theadCustom' class="text-center">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
            </tr>
          </thead>

          <tbody id="tabel_dataBiayaPenyusutan" class="text-left" >
            <tr>
              <td>-</td>
              <td>-</td>
              <td>-</td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal select add akumulasi penyusutan-->



@endsection

@section('js')
<script src="{{ asset('js/masterTable.js') }}"></script>
<script type="text/javascript">

let dataRefresh = []

$('#formSubKategori').on('hidden.bs.modal', function () {
  $('#formSubGroup').css('opacity', 1);
});

function closeSubKategoriForm() {
   $('#formSubGroup').css('opacity', 1);
   $("#formSubKategori").modal('toggle')
}

function submitAddSubKategori () {
  console.log('submitAddSubKategori')
  let _token = $("#_token").val();
  let kodesubgroup = $("#input_subkategori_kodesubgroup").val();
  let kodesubkategori = $("#input_subkategori_add_kodesubkategori").val();
  let namasubkategori = $("#input_subkategori_add_namasubkategori").val();
  let kodehdgroup = $("#input_subgroup_kodehdgroup").val();
  let kodegroup = $("#input_subgroup_kodegroup").val();

  console.log('kodegroup' , kodegroup)
  console.log('kodehdgroup' , kodehdgroup)
  console.log('kodesubgroup' , kodesubgroup)
  console.log('kodesubkategori' , kodesubkategori)
  console.log('namasubkategori' , namasubkategori)

  if (!kodesubkategori) {
    alertify.warning("Kode  harus diisi");
    return
  }
  if (!namasubkategori) {
    alertify.warning("Nama  harus diisi");
    return
  }
  console.log("TESSSS ==========")
  // return

  $.ajax({
    url: "{!! url('masterheadgroupspaddsubkategori') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodesubgroup,
      kodesubkategori,
      namasubkategori,
      kodehdgroup,
      kodegroup
    },
    success: function(res) {

      console.log(res)
      if (res != 1) {
        alertify.warning(res);
      }  else {
        // console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Sub Kategori telah ditambah");
        refreshSubKategori()
        // $("#form").modal('toggle')
        closeShowHideSubKategori()
      }

    }})


}


function submitEditSubKategori () {
  console.log('submitEditSubKategori')
  let _token = $("#_token").val();

  let kodesubgroup = $("#input_subkategori_kodesubgroup").val();
  let kodesubkategori = $("#input_subkategori_edit_kodesubkategori").val();
  let namasubkategori = $("#input_subkategori_edit_namasubkategori").val();
  let kodehdgroup = $("#input_subgroup_kodehdgroup").val();
  let kodegroup = $("#input_subgroup_kodegroup").val();

  console.log('kodesubgroup' , kodesubgroup)
  console.log('kodesubkategori' , kodesubkategori)
  console.log('namasubkategori' , namasubkategori)
  console.log('kodehdgroup' , kodehdgroup)
  console.log('kodegroup' , kodegroup)

  if (!kodesubkategori) {
    alertify.warning("Kode  harus diisi");
    return
  }
  if (!namasubkategori) {
    alertify.warning("Nama  harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masterheadgroupspeditsubkategori') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodesubgroup,
      kodesubkategori,
      namasubkategori,
      kodehdgroup,
      kodegroup
    },
    success: function(res) {

      console.log(res)
      if (res != 1) {
        alertify.warning(res);
      }  else {
        // console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Sub Kategori telah diedit");
        refreshSubKategori()
        // $("#form").modal('toggle')
        closeShowHideSubKategori()
      }

    }})
}

function submitAddSubGroup () {
  let _token = $("#_token").val();
  let kodehdgroup = $("#input_subgroup_kodehdgroup").val();
  let kodesubgroup = $("#input_subgroup_add_kodesubgroup").val();
  let perkpers = $("#input_add_perkPers").val();
  let perkjual = $("#input_add_perkJual").val();
  let namasubgroup = $("#input_subgroup_add_namasubgroup").val();
  let kodegroup = $("#input_subgroup_kodegroup").val();

  if (!kodehdgroup) {
    alertify.warning("Kode  harus diisi");
    return
  }

  if (!namasubgroup) {
    alertify.warning("Nama  harus diisi");
    return
  }

  if (!perkpers) {
    alertify.warning("Perkiraan  harus diisi");
    return
  }
  if (!perkjual) {
    alertify.warning("Perkiraan  harus diisi");
    return
  }

  console.log('kodehdgroup',kodehdgroup)
  console.log('kodesubgroup',kodesubgroup)
  console.log('namasubgroup',namasubgroup)
  console.log('perkpers',perkpers)
  console.log('perkjual',perkjual)
  console.log('kodegroup',kodegroup)


  $.ajax({
    url: "{!! url('masterheadgroupspaddsubgroup') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodehdgroup,
      kodegroup,
      kodesubgroup,
      namasubgroup,
      perkpers,
      perkjual
    },
    success: function(res) {

      console.log(res)
      if (res != 1) {
        alertify.warning(res);
      }  else {
        // console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Sub Group telah ditambah");
        refreshSubGroup()
        // $("#form").modal('toggle')
        closeShowHideSubGroup()
      }

    }})



}

function submitEditSubGroup () {
  let _token = $("#_token").val();
  let kodehdgroup = $("#input_subgroup_kodehdgroup").val();
  let kodesubgroup = $("#input_subgroup_edit_kodesubgroup").val();
  let perkpers = $("#input_edit_perkPers").val();
  let perkjual = $("#input_edit_perkJual").val();
  let namasubgroup = $("#input_subgroup_edit_namasubgroup").val();
  let kodegroup = $("#input_subgroup_kodegroup").val();

  if (!kodehdgroup) {
    alertify.warning("Kode  harus diisi");
    return
  }

  if (!namasubgroup) {
    alertify.warning("Nama  harus diisi");
    return
  }

  if (!perkpers) {
    alertify.warning("Perkiraan  harus diisi");
    return
  }
  if (!perkjual) {
    alertify.warning("Perkiraan  harus diisi");
    return
  }

  console.log('kodehdgroup',kodehdgroup)
  console.log('kodesubgroup',kodesubgroup)
  console.log('namasubgroup',namasubgroup)
  console.log('perkpers',perkpers)
  console.log('perkjual',perkjual)
  console.log('kodegroup',kodegroup)


  $.ajax({
    url: "{!! url('masterheadgroupspeditsubgroup') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodehdgroup,
      kodegroup,
      kodesubgroup,
      namasubgroup,
      perkpers,
      perkjual
    },
    success: function(res) {

      console.log(res)
      if (res != 1) {
        alertify.warning(res);
      }  else {
        // console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Sub Group telah diedit");
        refreshSubGroup()
        // $("#form").modal('toggle')
        closeShowHideSubGroup()
      }

    }})

}

function buttonAddSubGroup () {
  $('.showhide').hide();
  console.log('buttonAddSubGroup')

  document.getElementById("input_subgroup_add_kodesubgroup").value = ''
  document.getElementById("input_subgroup_add_namasubgroup").value = ''
  document.getElementById("input_add_perkPers").value = ''
  document.getElementById("input_add_perkJual").value = ''

  $('#addSubGroup').show();
}

function buttonDeleteSubKategori (kodesubkategori) {
  let _token = $("#_token").val();
  let kodesubgroup = $("#input_subkategori_kodesubgroup").val();
  let kodehdgroup = $("#input_subgroup_kodehdgroup").val();
  let kodegroup = $("#input_subgroup_kodegroup").val();

  console.log('kodegroup' , kodegroup)
  console.log('kodehdgroup' , kodehdgroup)
  console.log('kodesubgroup' , kodesubgroup)
  console.log('kodesubkategori' , kodesubkategori)
  // return
  // masterheadgroupspdeletesubkategori
  alertify.confirm('Hapus Kategori', 'Apakah yakin ingin menghapus kategori ' + kodesubkategori + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterheadgroupspdeletesubkategori') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kodesubgroup,
            kodesubkategori,
            kodegroup,
            kodehdgroup
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              refreshSubKategori()
              alertify.success("Sub Kategori telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });
}

function buttonDeleteSubGroup (kodesubgroup) {
  console.log(kodesubgroup)
  console.log('buttonDeleteSubGroup')
  let _token = $("#_token").val();
  let kodehdgroup = $("#input_subgroup_kodehdgroup").val();



  alertify.confirm('Hapus Group', 'Apakah yakin ingin menghapus SubGroup ' + kodesubgroup + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterheadgroupspdeletesubgroup') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kodesubgroup,
            kodehdgroup
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              refreshSubGroup()
              alertify.success("Sub Group telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });

}

function buttonEditSubGroup (kodesubgroup) {
  $('.showhide').hide();
  let kodehdgroup = $("#input_subgroup_kodehdgroup").val();
  console.log('buttonEditSubGroup')
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('masterheadgroupspdetailsubgroup') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode: kodesubgroup,
      kodehdgroup
    },
    success: function(res) {
      console.log('DETAIL')
      console.log(res)
      console.log(res[0].KodeSubGrp)
      console.log(res[0].NamaSubGrp)
      document.getElementById("input_subgroup_edit_kodesubgroup").value = res[0].KodeSubGrp
      document.getElementById("input_subgroup_edit_namasubgroup").value = res[0].NamaSubGrp

      document.getElementById("input_edit_perkPers").value = res[0].PerkPers
      document.getElementById("input_edit_perkJual").value = res[0].PerkH

    }})

  $('#editSubGroup').show();
}


function closeShowHideSubGroup () {
  $('.showhide').hide();
}

function refreshSubGroup () {
  console.log('refreshSubGroup')
  let kode = $("#input_subgroup_kodehdgroup").val();
  $('.showhide').hide();
  $.ajax({
    url: "{!! url('masterheadgrouplistsubgroup') !!}",
    type: "get",
    async: false,
    data: {
      // _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center">
          <button class="btn-action-sm btn-action-success" type="button" onclick="buttonEditSubGroup('${item.KodeSubGrp}')" ><i class="bi bi-pen"></i></button>
          <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonSubKategori('${item.KodeSubGrp}')" ><i class="bi bi-list"></i></button>
          <button class="btn-action-sm btn-action-danger" type="button" onclick="buttonDeleteSubGroup('${item.KodeSubGrp}')" ><i class="bi bi-trash"></i></button>
        </td>
        <td>${item.KodeSubGrp}</td>
        <td>${item.NamaSubGrp}</td>
        </tr>
        `
      });

      if (!res.length) {
        rowTable =`<tr><td colspan=3 class="text-center" >Belum ada data</td></tr>`
      }
      // document.getElementById("input_subgroup_kodegroup").value = kodegroup
      document.getElementById("input_subgroup_kodehdgroup").value = res[0].KodeHDGrp
      document.getElementById("tabel_data_subgroup").innerHTML = rowTable


    }})

}

function buttonSubGroup (kode, kodegroup) {

  console.log(kode , kodegroup)

  $('.showhide').hide();
  $.ajax({
    url: "{!! url('masterheadgrouplistsubgroup') !!}",
    type: "get",
    async: false,
    data: {
      // _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td style="white-space:nowrap;" class='text-center'>
          <div class="action-buttons-wrap">
              <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-primary" type="button" onclick="buttonSubKategori('${item.KodeSubGrp}')"><i class="bi bi-list"></i></button>
              <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-success" type="button" onclick="buttonEditSubGroup('${item.KodeSubGrp}')"><i class="bi bi-pen"></i></button>
              <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDeleteSubGroup('${item.KodeSubGrp}')"><i class="bi bi-trash"></i></button>
          </div>
        </td>
        <td>${item.KodeSubGrp}</td>
        <td>${item.NamaSubGrp}</td>
        </tr>
        `
      });
      if (!res.length) {
        rowTable =`<tr><td colspan=3 class="text-center">Belum ada data</td></tr>`
      }
      document.getElementById("input_subgroup_kodegroup").value = kodegroup
      document.getElementById("input_subgroup_kodehdgroup").value = kode
      document.getElementById("tabel_data_subgroup").innerHTML = rowTable

    }})

  $("#formSubGroup").modal('toggle')
}

function refreshSubKategori () {
  $('.showhidekategori').hide();
  let kodesubgroup = $("#input_subkategori_kodesubgroup").val();
  let kodehdgroup = $("#input_subgroup_kodehdgroup").val();

  $.ajax({
    url: "{!! url('masterheadgrouplistsubkategori') !!}",
    type: "get",
    async: false,
    data: {
      // _token : _token,
      kodesubgroup,
      kodehdgroup
    },
    success: function(res) {

      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center">
          <button class="btn-action-sm btn-action-success" type="button" onclick="buttonEditSubKategori('${item.Urut}')" ><i class="bi bi-pen"></i></button>
          <button class="btn-action-sm btn-action-danger" type="button" onclick="buttonDeleteSubKategori('${item.Urut}')" ><i class="bi bi-trash"></i></button>
        </td>
        <td>${item.Urut}</td>
        <td>${item.Keterangan}</td>
        </tr>
        `
      });
      if (!res.length) {
        rowTable =`<tr><td colspan=3 class="text-center" >Belum ada data</td></tr>`
      }


      document.getElementById("tabel_data_subkategori").innerHTML = rowTable
      // document.getElementById("input_subkategori_kodesubgroup").value = kodesubgroup


    }})
}

function buttonSubKategori (kodesubgroup) {
  console.log('buttonSubKategori')
  $('.showhidekategori').hide();
  $('#formSubGroup').css('opacity', .5);
  let kodehdgroup = $("#input_subgroup_kodehdgroup").val();
  console.log('kodehdgroup' , kodehdgroup)
  console.log(kodesubgroup)

  $.ajax({
    url: "{!! url('masterheadgrouplistsubkategori') !!}",
    type: "get",
    async: false,
    data: {
      // _token : _token,
      kodesubgroup,
      kodehdgroup
    },
    success: function(res) {

      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center">
          <button class="btn-action-sm btn-action-success"type="button" onclick="buttonEditSubKategori('${item.Urut}')" ><i class="bi bi-pen"></i></button>
          <button class="btn-action-sm btn-action-danger" type="button" onclick="buttonDeleteSubKategori('${item.Urut}')" ><i class="bi bi-trash"></i></button>
        </td>
        <td>${item.Urut}</td>
        <td>${item.Keterangan}</td>
        </tr>
        `
      });
      if (!res.length) {
        rowTable =`<tr><td colspan=3 class="text-center" >Belum ada data</td></tr>`
      }

      document.getElementById("tabel_data_subkategori").innerHTML = rowTable
      document.getElementById("input_subkategori_kodesubgroup").value = kodesubgroup


    }})

  $("#formSubKategori").modal('toggle')


  // console.log(kode , kodegroup)
  // $('.showhide').hide();
  // $.ajax({
  //   url: "{!! url('masterheadgrouplistsubgroup') !!}",
  //   type: "get",
  //   async: false,
  //   data: {
  //     // _token : _token,
  //     kode
  //   },
  //   success: function(res) {
  //
  //     console.log(res)
  //     let rowTable = ``
  //     res.forEach((item, i) => {
  //       rowTable += `
  //       <tr>
  //       <td>${item.KodeSubGrp}</td>
  //       <td>${item.NamaSubGrp}</td>
  //       <td class="text-center">
  //         <button class="btn btn-success btn-sm" type="button" onclick="buttonEditSubGroup('${item.KodeSubGrp}')" ><i class="bi bi-pen"></i></button>
  //         <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteSubGroup('${item.KodeSubGrp}')" ><i class="bi bi-trash"></i></button>
  //         <button class="btn btn-primary btn-sm" type="button" onclick="buttonSubKategori('${item.KodeSubGrp}')" ><i class="bi bi-list"></i></button>
  //       </td>
  //       </tr>
  //       `
  //     });
  //     document.getElementById("input_subgroup_kodegroup").value = kodegroup
  //     document.getElementById("input_subgroup_kodehdgroup").value = res[0].KodeHDGrp
  //     document.getElementById("tabel_data_subgroup").innerHTML = rowTable
  //
  //
  //   }})
  //
  // $("#formSubGroup").modal('toggle')
}

function buttonAddSubKategori () {
  closeShowHideSubKategori()
  $('#addSubKategori').show();
}

function buttonEditSubKategori (kodesubkategori) {
  closeShowHideSubKategori()
  let kodesubgroup = $("#input_subkategori_kodesubgroup").val();
  let kodehdgroup = $("#input_subgroup_kodehdgroup").val();
  let kodegroup = $("#input_subgroup_kodegroup").val();

  console.log('kodegroup' , kodegroup)
  console.log('kodehdgroup' , kodehdgroup)
  console.log(kodesubkategori, kodesubgroup)
  $.ajax({
    url: "{!! url('masterheadgroupspdetailsubkategori') !!}",
    type: "get",
    async: false,
    data: {
      // _token : _token,
      kodesubkategori,
      kodesubgroup,
      kodehdgroup,
      kodegroup
    },
    success: function(res) {

      console.log(res)
      // let rowTable = ``
      // res.forEach((item, i) => {
      //   rowTable += `
      //   <tr>
      //   <td>${item.Urut}</td>
      //   <td>${item.Keterangan}</td>
      //   <td class="text-center">
      //     <button class="btn btn-success btn-sm" type="button" onclick="buttonEditSubKategori('${item.Urut}')" ><i class="bi bi-pen"></i></button>
      //     <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteSubKategori('${item.Urut}')" ><i class="bi bi-trash"></i></button>
      //
      //   </td>
      //   </tr>
      //   `
      // });

      document.getElementById("input_subkategori_edit_namasubkategori").value = res[0].Keterangan
      document.getElementById("input_subkategori_edit_kodesubkategori").value = res[0].Urut
      // document.getElementById("tabel_data_subkategori").innerHTML = rowTable


    }})
  $('#editSubKategori').show();
}

function closeShowHideSubKategori () {
  $('.showhidekategori').hide();
}

function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();

  document.getElementById('breadcrumb').innerHTML = "Master Head Group"

  $('#tabel').DataTable().destroy();  

  $.ajax({
    url: "{!! url('masterheadgrouploadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      console.log(res)
      dataRefresh = res.hdgroup
  }})

  let rowTable = ""
  dataRefresh.forEach((item, i) => {
    let temp = ""

    rowTable += `<tr>
    <td style="white-space:nowrap;" class='text-center'>
      <div class="action-buttons-wrap">
          <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KODEHDGRP}')"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Sub Group" class="btn-action-sm btn-action-primary" type="button" onclick="buttonSubGroup('${item.KODEHDGRP}','${item.KODEGRP}')"><i class="bi bi-list"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KODEHDGRP}')"><i class="bi bi-trash"></i></button>
      </div>
    </td>
    <td>${item.KODEHDGRP}</td>
    <td>${item.NAMAHDGRP}</td>
    <td>${item.KODEGRP}</td>
    </tr>`
  });

   let currentLength = $("#tabel_length_visual").val() ? Number($("#tabel_length_visual").val()) : 10;
      document.getElementById("tabel_data").innerHTML = rowTable
      $("#tabel").DataTable({
        "lengthChange": false,
        "paging": true,
        "searching": true,
        "dom": 'tip',
        "pageLength": currentLength
      });

}

function buttonAdd () {

  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masterheadgrouplistgroup') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_kode").value = ''
      document.getElementById("input_add_nama").value = ''

      let rowSelect = `<option selected disabled value="">Pilih Group</option>`
      res.forEach((item, i) => {
        rowSelect += `
          <option value="${item.KODEGRP}">${item.KODEGRP} - ${item.NAMA}</option>
        `
      });
      document.getElementById("input_add_kodegroup").innerHTML = rowSelect

    }})

  $("#form").modal('toggle')

}

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('masterheadgrouplistgroup') !!}",
    type: "get",
    async: false,
    data: {
      // _token : _token,
    },
    success: function(res) {

      console.log(res)
      // document.getElementById("input_edit_kode").value = res[0].KODEGRP
      // document.getElementById("input_edit_nama").value = res[0].NAMA

      let rowSelect = `<option selected value="">Pilih Group</option>`
      res.forEach((item, i) => {
        rowSelect += `
          <option value="${item.KODEGRP}">${item.KODEGRP} - ${item.NAMA}</option>
        `
      });
      document.getElementById("input_edit_kodegroup").innerHTML = rowSelect

    }})

  $.ajax({
    url: "{!! url('masterheadgroupspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kode").value = res[0].KODEHDGRP
      document.getElementById("input_edit_nama").value = res[0].NAMAHDGRP
      let temp = ""
      if (res[0].KODEGRP){
        temp = res[0].KODEGRP

      }
      document.getElementById("input_edit_kodegroup").value = temp


    }})
    $("#formEdit").modal('toggle')
}


function buttonPerkiraanSubGroup (kodeBiaya) {

  $("#formAddBiayaPenyusutan").modal('toggle')

  console.log('asd');
  let _token = $("#_token").val();

  if ($.fn.DataTable.isDataTable('#tabelAddBiayaPenyusutan')) {
    $('#tabelAddBiayaPenyusutan').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterheadgrouploadperkiraansubgroup') !!}",
    type: "post",
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
    rowTable += `<tr>
      <td class="text-center">
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonPilihBiayaPenyusutan('${item.Perkiraan}', '${item.Keterangan}', '${kodeBiaya}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });
  document.getElementById("tabel_dataBiayaPenyusutan").innerHTML = rowTable;

  $("#tabelAddBiayaPenyusutan").DataTable({
    "lengthChange": true,
    "paging": true
  });
}

function buttonPilihBiayaPenyusutan (perkiraan, keterangan, kodeBiaya) {
  if (kodeBiaya == '1') {
    document.getElementById("input_add_perkPers").value = perkiraan
  } else if (kodeBiaya == '2') {
    document.getElementById("input_add_perkJual").value = perkiraan
  } else if (kodeBiaya == '3') {
    document.getElementById("input_edit_perkPers").value = perkiraan
  } else if (kodeBiaya == '4') {
    document.getElementById("input_edit_perkJual").value = perkiraan
  }

  $("#formAddBiayaPenyusutan").modal('hide')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Group', 'Apakah yakin ingin menghapus Group ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterheadgroupspdelete') !!}",
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
              alertify.success("Group telah dihapus");

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
  let nama = $("#input_edit_nama").val();
  let kodegroup = $("#input_edit_kodegroup").val();
  console.log(kode,nama, kodegroup)
  if (!kode) {
    alertify.warning("Kode  harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama  harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masterheadgroupspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      kodegroup
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Group telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}
//
function submitAdd () {
  console.log('submit add kodegroup')
  let _token = $("#_token").val();
  let kode = $("#input_add_kode").val();
  let nama = $("#input_add_nama").val();
  let kodegroup = $("#input_add_kodegroup").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama harus diisi");
    return
  }
  if (kodegroup == 0 || !kodegroup) {
    alertify.warning("Kode Group harus diisi");
    return
  }

  console.log('submit add kodegroup',kode,nama,kodegroup)

  $.ajax({
    url: "{!! url('masterheadgroupspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      kodegroup
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Group telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

window.onload = function (){
  loadAll();
}

</script>




@endsection
