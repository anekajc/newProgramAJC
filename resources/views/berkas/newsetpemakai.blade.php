@extends('newmaster')
@section('buttons')

@endsection
@section('content')

  <link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}">

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Berkas</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Set Pemakai</span>
  </div> --}}

  <div class="sp-page-head">
    <div>
      <h1>Set Pemakai</h1>
      <p>Master — manajemen pengguna &amp; akses sistem</p>
    </div>
    <button class="btn btn-primary" onclick="buttonAdd()">+ Add User</button>
  </div>


  <div id="contentContainer" class="container-fluid">

    <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

    <!-- Stat summary cards -->
    <div class="sp-stats-row">
      <div class="sp-stat-card">
        <div class="sp-stat-icon is-purple"><i class="bi bi-people"></i></div>
        <div>
          <div class="sp-stat-label">Total User</div>
          <div class="sp-stat-value">{{ count($users) }}</div>
        </div>
      </div>
      <div class="sp-stat-card">
        <div class="sp-stat-icon is-green"><i class="bi bi-check-circle"></i></div>
        <div>
          <div class="sp-stat-label">User Aktif</div>
          <div class="sp-stat-value">{{ collect($users)->where('STATUS', 1)->count() }}</div>
          <div class="sp-stat-sub">{{ collect($users)->where('STATUS', '!=', 1)->count() }} Tidak Aktif</div>
        </div>
      </div>
      <div class="sp-stat-card">
        <div class="sp-stat-icon is-blue"><i class="bi bi-shield-check"></i></div>
        <div>
          <div class="sp-stat-label">Administrator</div>
          <div class="sp-stat-value">{{ collect($users)->where('TINGKAT', 2)->count() }}</div>
        </div>
      </div>
    </div>

    <!-- Toolbar: search box (visual only — table search stays disabled per existing DataTables config) -->
      @include('master.partials.headerTableMaster')

            <div class="table-outer">
              <div class="table-wrap">
                <table class="tb" id="tabel">
                  <thead>
                    <tr>
                      <th scope="col">Actions</th>
                      <th scope="col">Username</th>
                      <th scope="col">Nama</th>
                      <th scope="col">Level</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data" class="text-right" >
                  @for ($i = 0; $i < count($users); $i++)
                  <tr>
                    
                <td style="text-align:center;">
                  <div class="action-buttons-wrap">
                      <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" title="Koreksi" type="button" onclick="editUser('{{ $users[$i]->username }}', '{{ $users[$i]->FullName }}', '{{ $users[$i]->kodeBag }}', '{{ $users[$i]->KodeJab }}', '{{ $users[$i]->KodeKasir }}', '{{ $users[$i]->limit }}', '{{ $users[$i]->STATUS }}', '{{ $users[$i]->TINGKAT }}', '{{ $users[$i]->keynik }}')" ><i class="bi bi-pen"></i></button>
                      <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-primary" type="button" onclick="editAkses('{{ $users[$i]->username }}')"><i class="bi bi-card-checklist"></i></button>
                      <button data-toggle="tooltip" data-placement="top" title="Set Report" class="btn-action-sm btn-action-primary" type="button" onclick="editAksesReport('{{ $users[$i]->username }}')"><i class="bi bi-card-list"></i></button>
                      <button data-toggle="tooltip" data-placement="top" title="Akses Gudang" class="btn-action-sm btn-action-primary" type="button" onclick="editAkses('{{ $users[$i]->username }}')"><i class="bi bi-box2"></i></button>
                      <button data-toggle="tooltip" data-placement="top" title="Akses COA" class="btn-action-sm btn-action-primary" type="button" onclick="editCOA('{{ $users[$i]->username }}')"><i class="bi bi-card-heading"></i></button>
                      <button data-toggle="tooltip" data-placement="top" title="COA Report" class="btn-action-sm btn-action-primary" type="button" onclick="editCOA('{{ $users[$i]->username }}')"><i class="bi bi-postcard"></i></button>
                      <button data-toggle="tooltip" data-placement="top" title="Akses COA BS" class="btn-action-sm btn-action-primary" type="button" onclick="editCOA('{{ $users[$i]->username }}')"><i class="bi bi-postcard"></i></button> 
                      <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="deleteUser('{{ $users[$i]->username }}')"><i class="bi bi-trash"></i></button>
                  </div>
                </td>
                    <td>{{ $users[$i]->USERID }}</td>
                    <td>{{ $users[$i]->FullName }}</td>
                    <td>
                      @if($users[$i]->TINGKAT == 0)
                      <span class="sp-badge is-user">User</span>
                      @elseif($users[$i]->TINGKAT == 1)
                      <span class="sp-badge is-supervisor">Supervisor</span>
                      @elseif($users[$i]->TINGKAT == 2)
                      <span class="sp-badge is-admin">Administrator</span>
                      @endif
                    </td>

                  </tr>
                  @endfor
                </tbody>
                </table>
              </div>
          </div>
  </div>

</div>

<!-- start modal akses menu -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Akses Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />
          <div class="row">

            <div class="col-2">
              <div class="form-group">
                <label>Username</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_username" placeholder="Username" disabled>
              </div>
            </div>
          </div>

          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



      </div>
        <div class="container-fluid" style="overflow-x: auto;">

              <table id="addTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th style="" scope="col">Kode Menu</th>
                    <th scope="col">Ket</th>
                    <th scope="col">ACCESS</th>
                    <th scope="col">TAMBAH</th>
                    <th scope="col">KOREKSI</th>
                    <th scope="col">HAPUS</th>
                    <th scope="col">CETAK</th>
                    <th scope="col">EXPORT</th>
                    <th scope="col">OTO1</th>
                    <th scope="col">OTO2</th>
                    <th scope="col">OTO3</th>
                    <th scope="col">OTO4</th>
                    <th scope="col">OTO5</th>
                    <th scope="col">BATAL</th>
                  </tr>
                </thead>


                <tbody id="addTableData" class="text-left" >
                  <tr>

                      <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
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

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
        {{-- <button type="button" class="btn btn-primary" onclick="submitAkses()">Submit</button> --}}
      </div>
    </div>
  </div>
</div>
<!-- End modal akses menu-->

<!-- start modal akses menu report -->
<div class="modal fade" id="formReport" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Akses Menu Report</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <div class="container-fluid">
          <div class="row">


            <div class="col-2">
              <div class="form-group">
                <label>Username</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_report_username" placeholder="Username" disabled>
              </div>
            </div>
          </div>


          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



      </div>
        <div class="container-fluid" style="overflow-x: auto;">

              <table id="reportTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th style="" scope="col">Kode Menu</th>
                    <th scope="col">Ket</th>
                    <th scope="col">ACCESS</th>
                    <th scope="col">DESIGN</th>
                    <th scope="col">EXPORT</th>
                  </tr>
                </thead>


                <tbody id="reportTableData" class="text-left" >
                  <tr >

                      <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                </tr>

                </tbody>


              </table>
        </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
        {{-- <button type="button" class="btn btn-primary" onclick="submitAksesReport()">Submit</button> --}}
      </div>
    </div>
  </div>
</div>
<!-- End modal akses menu report-->


<!-- start modal akses coa -->
<div class="modal fade" id="formCOA" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit COA</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <div class="container-fluid">
          <div class="row">


            <div class="col-2">
              <div class="form-group">
                <label>Username</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_coa_username" placeholder="Username" disabled>
              </div>
            </div>
          </div>

          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



      </div>
        <div class="container-fluid" >

              <div class="row text-left justify-content-center">
                <div class="col-5 ">
                  <label>Perkiraan yang tersedia</label>
                </div>
                <div class="col-1">
                </div>
                <div class="col-5">
                  <label>Akses Perkiraan yang diberikan</label>
                </div>
              </div>
              <div class="row justify-content-center" style="maxrgin-bottom: 30px">
                <div class="col-5 bg-primary" style="overflow-y: scroll; height: 500px" >
                  <table id="tableCOA" class="table table-bordered table-striped"  >
                    <thead class="text-center">
                      <tr>
                        <th style="" scope="col">Perkiraan</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col">Pilih</th>
                      </tr>
                    </thead>


                    <tbody id="tableDataCOA" class="text-left" >
                      <tr >

                          <td>-</td>
                          <td>-</td>
                          <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
                    </tr>

                    </tbody>


                  </table>
                </div>
                <div class="col-1 my-auto text-center" >
                    <div class="row">
                      <div class="col-12">
                        <button  data-toggle="tooltip" data-placement="top"  class="btn btn-success btn-sm" type="button" onclick="buttonAddAllCOA()"><i class="bi bi-chevron-double-right"></i></button>
                      </div>

                    </div>
                    <div class="row mt-4">

                      <div class="col-12">
                        <button  data-toggle="tooltip" data-placement="top" class="btn btn-success btn-sm" type="button" onclick="buttonDeleteAllCOA()"><i class="bi bi-chevron-double-left"></i></button>
                      </div>
                    </div>
                    <div class="row mt-4">

                      <div class="col-12">
                        <button  data-toggle="tooltip" data-placement="top" class="btn btn-success btn-sm" type="button" onclick="buttonAddCOA()"><i class="bi bi-chevron-right"></i></button>
                      </div>
                    </div>
                    <div class="row mt-4">

                      <div class="col-12">
                        <button  data-toggle="tooltip" data-placement="top" class="btn btn-success btn-sm" type="button" onclick="buttonDeleteCOA()"><i class="bi bi-chevron-left"></i></button>
                      </div>
                    </div>
                </div>
                <div class="col-5 bg-warning" style="overflow-y: scroll; height: 500px" >
                  <table id="tableAksesCOA" class="table table-bordered table-striped"  >
                    <thead class="text-center">
                      <tr>
                        <th style="" scope="col">Pilih</th>
                        <th scope="col">Perkiraan</th>
                        <th scope="col">Keterangan</th>
                      </tr>
                    </thead>


                    <tbody id="tableDataAksesCOA" class="text-left" >
                      <tr >

                          <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
                          <td>-</td>
                          <td>-</td>
                    </tr>


                    </tbody>


                  </table>
                </div>
              </div>
        </div>



    </div>
  </div>
</div>
<!-- End modal akses coa-->

<!-- start modal add -->
<div class="modal fade"  id="formAddUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
      </div>
      <div class="modal-body">

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
                <div class="col-4">
                    <label class="form-label">NIK</label>
                </div>
                <div class="col-8">
                    <div class="input-group">
                        <input type="text" class="form-control" id="input_add_NIK">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary btn-select" onclick="buttonNIK()">+</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
              <div class="col-4">
                  <label class="form-label">User</label>
              </div>
              <div class="col-8">
                <div class="input-group">
                  <input type="text" class="form-control" id="input_add_user">
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-4">
                  <label class="text-left">Password</label>
              </div>
              <div class="col-8">
                <div class="input-group">
                  <input type="password" class="form-control" id="input_add_password" maxlength="12">
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                  <label class="text-left">Konfirmasi PW</label>
              </div>
              <div class="col-8">
                  <input type="password" class="form-control" id="input_add_passwordConfirm" onchange="checkPassword()" maxlength="12">
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                  <label class="text-left">Nama Lengkap</label>
              </div>
              <div class="col-8">
                  <input type="text" class="form-control" id="input_add_namaLengkap">
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-4">
                <label class="text-left">Departemen</label>
              </div>
              <div class="col-8">
                <div class="input-group">
                  <input type="text" class="form-control" id="input_add_departemen">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary btn-select" onclick="buttonDepartemen()">+</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-4">
                  <label class="text-left">Jabatan</label>
              </div>
              <div class="col-8">
                <div class="input-group">
                  <input type="text" class="form-control" id="input_add_jabatan">
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" onclick="buttonJabatan()">+</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Level</label>
                  <select class="form-control" id="input_add_level">
                    <option value="0">User</option>
                    <option value="1">Supervisor</option>
                    <option value="2">Administrator</option>
                  </select>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Status</label>
                  <select class="form-control" id="input_add_status">
                    <option value="0">Offline</option>
                    <option value="1">Online</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                  <label class="text-left">Kode Kasir</label>
              </div>
              <div class="col-8">
                  <input type="text" class="form-control" id="input_add_kodeKasir">
                </div>
            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                  <label class="text-left">Limit</label>
              </div>
              <div class="col-8">
                <input type="number" class="form-control text-right" id="input_add_limit" value = '0.00'>
              </div>
            </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitAddUserData()">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->


<!-- start modal add -->
<div class="modal fade" id="formEditUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
      </div>
      <div class="modal-body">

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_edit_noUrut" value="" />

            <div class="row">
                <div class="col-4">
                    <label class="form-label">NIK</label>
                </div>
                <div class="col-8">
                    <div class="input-group">
                        <input type="text" class="form-control" id="input_edit_NIK">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary btn-select" onclick="buttonNIK()">+</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
              <div class="col-4">
                  <label class="form-label">User</label>
              </div>
              <div class="col-8">
                <div class="input-group">
                  <input type="text" class="form-control" id="input_edit_user" disabled>
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                  <label class="text-left">Nama Lengkap</label>
              </div>
              <div class="col-8">
                  <input type="text" class="form-control" id="input_edit_namaLengkap">
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-4">
                <label class="text-left">Departemen</label>
              </div>
              <div class="col-8">
                <div class="input-group">
                  <input type="text" class="form-control" id="input_edit_departemen">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary btn-select" onclick="buttonDepartemen()">+</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-4">
                  <label class="text-left">Jabatan</label>
              </div>
              <div class="col-8">
                <div class="input-group">
                  <input type="text" class="form-control" id="input_edit_jabatan">
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" onclick="buttonJabatan()">+</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Level</label>
                  <select class="form-control" id="input_edit_level">
                    <option value="0">User</option>
                    <option value="1">Supervisor</option>
                    <option value="2">Administrator</option>
                  </select>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Status</label>
                  <select class="form-control" id="input_edit_status">
                    <option value="0">Offline</option>
                    <option value="1">Online</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                  <label class="text-left">Kode Kasir</label>
              </div>
              <div class="col-8">
                  <input type="text" class="form-control" id="input_edit_kodeKasir">
                </div>
            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                  <label class="text-left">Limit</label>
              </div>
              <div class="col-8">
                <input type="number" class="form-control text-right" id="input_edit_limit" value = '0.00'>
              </div>
            </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitAddUserEdit()">Submit Edit</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->


@endsection

@section('js')
<script src="{{ asset('js/masterTable.js') }}"></script>
<script type="text/javascript">


// addEventListener("beforeunload", (event) => {
//   window.location.href = "{{ url('logout')}}";
// });

  let listAkses = []
  let listAksesReport = []
  let listCOA = []
  let listAksesCOA = []


  // Cosmetic search box wired to the existing DataTable instance.
  // Table searching itself stays governed by the DataTables config above;
  // this just gives users a visible field that filters client-side.
  $("#tabel_filter_visual").on("keyup", function () {
    $("#tabel").DataTable().search(this.value).draw();
  });

  function deleteUser (username ) {
    console.log('deleteUser')
    let _token = $("#_token").val()
    console.log(username)


    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus user ' + username + ' ?',
        function() {
          let _token = $("#_token").val();
          let choice = "D"

          $.ajax({
            url: "{!! url('newsetpemakaideleteuser') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              username: username
            },
            success: function(res) {
              console.log(res)
              // res.forEach((item, i) => {
              //   console.log(item)
              // });
              alertify.success("Berhasil delete user")
              loadAll()

            }
          })
        }
      ,function(){
        console.log('no')
      });

  }

  function loadAll () {

    $.ajax({
      url: "{!! url('newsetpemakailoadall') !!}",
      type: "get",
      async: false,
      data: {
        // _token : _token,
        // username: username
      },
      success: function(res) {
        console.log(res)
        // res.forEach((item, i) => {
        //   console.log(item)
        // });
        rowTable = ''
        res.forEach((item, i) => {
          rowTable +=
          `<tr>
            <td>${item.USERID}</td>
            <td>${item.FullName}</td>
            <td>
            `
            if (Number(item.TINGKAT) == 0) {
              rowTable += '<span class="sp-badge is-user">User</span>'
            } else if (Number(item.TINGKAT) == 1) {
              rowTable += '<span class="sp-badge is-supervisor">Supervisor</span>'
            } else {
              rowTable += '<span class="sp-badge is-admin">Administrator</span>'
            }
            rowTable +=
            `
            </td>
                <td class="text-center">
                <div class="action-buttons-wrap">
                <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" title="Koreksi" type="button" onclick="editUser('${item.username}', '${item.FullName}', '${item.kodeBag}', '${item.KodeJab}', '${item.KodeKasir}', '${item.limit}', '${item.STATUS}', '${item.TINGKAT}', '${item.keynik}')" ><i class="bi bi-pen"></i></button>
                <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-primary" type="button" onclick="editAkses('${item.username}')"><i class="bi bi-card-checklist"></i></button>
                <button data-toggle="tooltip" data-placement="top" title="Set Report" class="btn-action-sm btn-action-primary" type="button" onclick="editAksesReport('${item.username}')"><i class="bi bi-card-list"></i></button>
                <button data-toggle="tooltip" data-placement="top" title="Akses Gudang" class="btn-action-sm btn-action-primary" type="button" onclick="editAkses('${item.username}')"><i class="bi bi-box2"></i></button>
                <button data-toggle="tooltip" data-placement="top" title="Akses COA" class="btn-action-sm btn-action-primary" type="button" onclick="editCOA('${item.username}')"><i class="bi bi-card-heading"></i></button>
                <button data-toggle="tooltip" data-placement="top" title="COA Report" class="btn-action-sm btn-action-primary" type="button" onclick="editCOA('${item.username}')"><i class="bi bi-postcard"></i></button>
                <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="deleteUser('${item.username}')"><i class="bi bi-trash"></i></button>
                </div>
                </td>
          </tr>
          `
        });
        document.getElementById("tabel_data").innerHTML = rowTable
        

      }
    })

  }

  function checkAll (index , kodemenu , field) {
    console.log(index, kodemenu, field)

    // console.log(checkBox)
    let checkBox = document.getElementById(`akses_checkbox_${field}${index}`).checked
    let kodeLength = kodemenu.length
    for (let i = Number(index) + 1 ; i < listAkses.length; i++) {
      // console.log(listAkses[i].KODEMENU.slice(0,kodeLength) , kodeLen)
      console.log(kodeLength)
      console.log(listAkses[i].KODEMENU.slice(0,kodeLength) , kodemenu)
      if (listAkses[i].KODEMENU.slice(0,kodeLength) !== kodemenu || listAkses[i].L0 == 0 ) {
        break
      }
      document.getElementById(`akses_checkbox_${field}${i}`).checked = checkBox
      console.log(listAkses[i].KODEMENU)
    }
  }


  function refreshCOA (username) {
    console.log('refreshCOA')
    console.log(username)
    let _token = $("#_token").val();


    $.ajax({
      url: "{!! url('newsetpemakailistcoa') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username: username
      },
      success: function(res) {
        console.log(res)
        // res.forEach((item, i) => {
        //   console.log(item)
        // });
        listCOA = res.listCoa
        listAksesCOA = res.listAksesCoa

      }
    })
    let rowTable = ""
    listCOA.forEach((item, i) => {
      rowTable += `
      <tr>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
      <td class="text-center"><input type="checkbox" onclick="clickCheckboxCOA('${item.Perkiraan}', '${i}')"  id="COA_checkbox${i}"></input></td>
      </tr>
      `
    });
    document.getElementById("tableDataCOA").innerHTML = rowTable

    let rowTableAkses = ""
    listAksesCOA.forEach((item, i) => {
      rowTableAkses += `
      <tr>
      <td class="text-center"><input type="checkbox" onclick="clickCheckboxAksesCOA('${item.Perkiraan}', '${i}')" id="aksesCOA_checkbox${i}"></input></td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
      </tr>
      `
    });

    document.getElementById("tableDataAksesCOA").innerHTML = rowTableAkses

  }

  function editCOA (username) {

    console.log('editCOA')
    console.log(username)

    let _token = $("#_token").val();

    document.getElementById("input_coa_username").value = username

    $.ajax({
      url: "{!! url('newsetpemakailistcoa') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username: username
      },
      success: function(res) {
        console.log(res)
        // res.forEach((item, i) => {
        //   console.log(item)
        // });
        listCOA = res.listCoa
        listAksesCOA = res.listAksesCoa

      }
    })
    let rowTable = ""
    listCOA.forEach((item, i) => {
      rowTable += `
      <tr>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
      <td class="text-center"><input type="checkbox" onclick="clickCheckboxCOA('${item.Perkiraan}', '${i}')"  id="COA_checkbox${i}"></input></td>
      </tr>
      `
    });
    document.getElementById("tableDataCOA").innerHTML = rowTable

    let rowTableAkses = ""
    listAksesCOA.forEach((item, i) => {
      rowTableAkses += `
      <tr>
      <td class="text-center"><input type="checkbox" onclick="clickCheckboxAksesCOA('${item.Perkiraan}', '${i}')" id="aksesCOA_checkbox${i}"></input></td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
      </tr>
      `
    });

    document.getElementById("tableDataAksesCOA").innerHTML = rowTableAkses
    $("#formCOA").modal('toggle')

  }

  function buttonDeleteAllCOA () {
    // newsetpemakaideleteallaksescoa

    let _token = $("#_token").val();
    let username = $("#input_coa_username").val();
    $.ajax({
      url: "{!! url('newsetpemakaideleteallaksescoa') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username,
      },
      success: function(res) {
        console.log(res)
        refreshCOA(username)
      }
    })

  }

  function buttonDeleteCOA () {
    deleteAksesCOA = []

    let _token = $("#_token").val();
    let username = $("#input_coa_username").val();
    listAksesCOA.forEach((item, i) => {
        if (document.getElementById(`aksesCOA_checkbox${i}`).checked) {
          deleteAksesCOA.push(item)
        }
    });
    console.log(deleteAksesCOA)
    // newsetpemakaideleteaksescoa
    $.ajax({
      url: "{!! url('newsetpemakaideleteaksescoa') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username,
        perkiraan: deleteAksesCOA
      },
      success: function(res) {
        console.log(res)
        refreshCOA(username)

      }
    })
  }

  function buttonAddAllCOA () {
    // newsetpemakaiupdateaddallcoa
    let username = $("#input_coa_username").val();

    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('newsetpemakaiupdateaddallcoa') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username
      },
      success: function(res) {
        console.log(res)
        refreshCOA(username)

      }
    })
  }

  function buttonAddCOA () {
    // newsetpemakaiupdateaddcoa
    let username = $("#input_coa_username").val();

    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('newsetpemakaiupdateaddcoa') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username
      },
      success: function(res) {
        console.log(res)
        refreshCOA(username)

      }
    })
  }

  function clickCheckboxCOA (perkiraan, index) {
    console.log('clickCheckboxCOA')
    let username = $("#input_coa_username").val();

    let _token = $("#_token").val();
    let nilai = 0
    if (document.getElementById(`COA_checkbox${index}`).checked) {
      nilai = 1
    }

    console.log(perkiraan, username, index, nilai)

    $.ajax({
      url: "{!! url('newsetpemakaiupdateiskirimcoa') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        perkiraan,
        nilai
      },
      success: function(res) {
        console.log(res)

      }
    })

  }

  function clickCheckboxAksesCOA (perkiraan, index) {
    console.log('clickCheckboxAksesCOA')
    let username = $("#input_coa_username").val();

    let nilai = 0
    if (document.getElementById(`aksesCOA_checkbox${index}`).checked) {
      nilai = 1
    }

    console.log(perkiraan , username, index , nilai)
  }

  function editAkses (username) {
    console.log(username)
    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('newsetpemakailistakses') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        userid: username
      },
      success: function(res) {
        console.log(res)
        res.forEach((item, i) => {
          console.log(item)
        });
        listAkses = res

      }
    })

    let rowTable = ""
    listAkses.forEach((item, i) => {
      if (item.L0 == 0) {
        rowTable += `<tr style="background-color: #FDFD96">
        <td>${item.KODEMENU}</td>
        <td>${item.Keterangan}</td>
        <td class="text-center"><input onclick='clickUpdateAksesMenuHeader("${i}" ,"${item.KODEMENU}","hasaccess")' class="" type="checkbox" value="" id="akses_checkbox_hasaccess${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenuHeader("${i}" ,"${item.KODEMENU}","istambah")' class="" type="checkbox" value="" id="akses_checkbox_istambah${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenuHeader("${i}" ,"${item.KODEMENU}","isKoreksi")' class="" type="checkbox" value="" id="akses_checkbox_isKoreksi${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenuHeader("${i}" ,"${item.KODEMENU}","isHapus")' class="" type="checkbox" value="" id="akses_checkbox_isHapus${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenuHeader("${i}" ,"${item.KODEMENU}","isCetak")' class="" type="checkbox" value="" id="akses_checkbox_isCetak${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenuHeader("${i}" ,"${item.KODEMENU}","isExport")' class="" type="checkbox" value="" id="akses_checkbox_isExport${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenuHeader("${i}" ,"${item.KODEMENU}","isOtorisasi1")' class="" type="checkbox" value="" id="akses_checkbox_isOtorisasi1${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenuHeader("${i}" ,"${item.KODEMENU}","isOtorisasi2")' class="" type="checkbox" value="" id="akses_checkbox_isOtorisasi2${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenuHeader("${i}" ,"${item.KODEMENU}","isOtorisasi3")' class="" type="checkbox" value="" id="akses_checkbox_isOtorisasi3${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenuHeader("${i}" ,"${item.KODEMENU}","isOtorisasi4")' class="" type="checkbox" value="" id="akses_checkbox_isOtorisasi4${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenuHeader("${i}" ,"${item.KODEMENU}","isOtorisasi5")' class="" type="checkbox" value="" id="akses_checkbox_isOtorisasi5${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenuHeader("${i}" ,"${item.KODEMENU}","isBatal")' class="" type="checkbox" value="" id="akses_checkbox_isBatal${i}"></td>
        </tr>`
      } else {
        rowTable += `<tr>
        <td>${item.KODEMENU}</td>
        <td>${item.Keterangan}</td>
        <td class="text-center"><input onclick='clickUpdateAksesMenu("${i}","${item.KODEMENU}")' class="" type="checkbox" value="" id="akses_checkbox_hasaccess${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenu("${i}","${item.KODEMENU}")' class="" type="checkbox" value="" id="akses_checkbox_istambah${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenu("${i}","${item.KODEMENU}")' class="" type="checkbox" value="" id="akses_checkbox_isKoreksi${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenu("${i}","${item.KODEMENU}")' class="" type="checkbox" value="" id="akses_checkbox_isHapus${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenu("${i}","${item.KODEMENU}")' class="" type="checkbox" value="" id="akses_checkbox_isCetak${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenu("${i}","${item.KODEMENU}")' class="" type="checkbox" value="" id="akses_checkbox_isExport${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenu("${i}","${item.KODEMENU}")' class="" type="checkbox" value="" id="akses_checkbox_isOtorisasi1${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenu("${i}","${item.KODEMENU}")' class="" type="checkbox" value="" id="akses_checkbox_isOtorisasi2${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenu("${i}","${item.KODEMENU}")' class="" type="checkbox" value="" id="akses_checkbox_isOtorisasi3${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenu("${i}","${item.KODEMENU}")' class="" type="checkbox" value="" id="akses_checkbox_isOtorisasi4${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenu("${i}","${item.KODEMENU}")' class="" type="checkbox" value="" id="akses_checkbox_isOtorisasi5${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenu("${i}","${item.KODEMENU}")' class="" type="checkbox" value="" id="akses_checkbox_isBatal${i}"></td>
        </tr>`
      }

    });

    document.getElementById("input_add_username").value = username

    document.getElementById("addTableData").innerHTML = rowTable

    listAkses.forEach((item, i) => {
      if (item.HASACCESS == 1) {
        document.getElementById(`akses_checkbox_hasaccess${i}`).checked = true;
      }
      if (item.ISTAMBAH == 1) {
        document.getElementById(`akses_checkbox_istambah${i}`).checked = true;
      }
      if (item.ISKOREKSI == 1) {
        document.getElementById(`akses_checkbox_isKoreksi${i}`).checked = true;
      }
      if (item.IsOtorisasi1 == 1) {
        document.getElementById(`akses_checkbox_isOtorisasi1${i}`).checked = true;
      }
      if (item.IsOtorisasi2 == 1) {
        document.getElementById(`akses_checkbox_isOtorisasi2${i}`).checked = true;
      }
      if (item.IsOtorisasi3 == 1) {
        document.getElementById(`akses_checkbox_isOtorisasi3${i}`).checked = true;
      }
      if (item.IsOtorisasi4 == 1) {
        document.getElementById(`akses_checkbox_isOtorisasi4${i}`).checked = true;
      }
      if (item.IsOtorisasi5 == 1) {
        document.getElementById(`akses_checkbox_isOtorisasi5${i}`).checked = true;
      }
      if (item.ISHAPUS == 1) {
        document.getElementById(`akses_checkbox_isHapus${i}`).checked = true;
      }
      if (item.ISCETAK == 1) {
        document.getElementById(`akses_checkbox_isCetak${i}`).checked = true;
      }
      if (item.ISEXPORT == 1) {
        document.getElementById(`akses_checkbox_isExport${i}`).checked = true;
      }
      if (item.IsBatal == 1) {
        document.getElementById(`akses_checkbox_isBatal${i}`).checked = true;
      }
    });

    $("#form").modal('toggle')

  }

  function editAksesReport (username) {
    console.log(username)
    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('newsetpemakailistaksesreport') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        userid: username
      },
      success: function(res) {
        console.log(res)
        // res.forEach((item, i) => {
        //   console.log(item)
        // });
        listAksesReport = res

      }
    })

    let rowTable = ""
    listAksesReport.forEach((item, i) => {
        rowTable += `<tr>
        <td>${item.KODEMENU}</td>
        <td>${item.Keterangan}</td>
        <td class="text-center"><input onclick='clickUpdateAksesMenuReport("${i}" ,"${item.KODEMENU}")' class="" type="checkbox" value="" id="akses_report_checkbox_hasaccess${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenuReport("${i}" ,"${item.KODEMENU}")' class="" type="checkbox" value="" id="akses_report_checkbox_isdesign${i}"></td>
        <td class="text-center"><input onclick='clickUpdateAksesMenuReport("${i}" ,"${item.KODEMENU}")' class="" type="checkbox" value="" id="akses_report_checkbox_isexport${i}"></td>
        </tr>`

    });



    document.getElementById("input_report_username").value = username
    //
    document.getElementById("reportTableData").innerHTML = rowTable
    //
    listAksesReport.forEach((item, i) => {
      if (item.Access == 1) {
        document.getElementById(`akses_report_checkbox_hasaccess${i}`).checked = true;
      }
      if (item.IsDesign == 1) {
        document.getElementById(`akses_report_checkbox_isdesign${i}`).checked = true;
      }
      if (item.Isexport == 1) {
        document.getElementById(`akses_report_checkbox_isexport${i}`).checked = true;
      }
    });

    $("#formReport").modal('toggle')

  }

  function clickUpdateAksesMenuHeader (index, kodemenu, field) {
    console.log('clickUpdateAksesMenuHeader')
    console.log(index, kodemenu, field)

    let _token = $("#_token").val();
    let username = $("#input_add_username").val();
    let nilai = 0

    if (document.getElementById(`akses_checkbox_${field}${index}`).checked) {
      nilai = 1
    }

    let checkBox = document.getElementById(`akses_checkbox_${field}${index}`).checked
    let kodeLength = kodemenu.length
    for (let i = Number(index) + 1 ; i < listAkses.length; i++) {
      if (listAkses[i].KODEMENU.slice(0,kodeLength) !== kodemenu || listAkses[i].L0 == 0 ) {
        break
      }
      document.getElementById(`akses_checkbox_${field}${i}`).checked = checkBox
    }


    console.log(kodemenu , nilai , username , field)

    // newsetpemakaispupdateaksesheader

    $.ajax({
      url: "{!! url('newsetpemakaispupdateaksesheader') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username: username,
        nilai,
        field,
        headermenu: kodemenu
      },
      success: function(res) {
        console.log(res)


      }
    })
  }

  function clickUpdateAksesMenu (index , kodemenu) {

    console.log('clickUpdateAksesMenu')
    console.log(index, kodemenu)
    let _token = $("#_token").val();
    let akses = []
    let aksesObj = {}
    let username = $("#input_add_username").val();

    let tempData = {
      KODEMENU: listAkses[index].KODEMENU,
      Keterangan: listAkses[index].Keterangan,
      HASACCESS: 0,
      ISTAMBAH: 0,
      ISKOREKSI: 0,
      ISHAPUS: 0,
      ISCETAK: 0,
      ISBATAL: 0,
      ISEXPORT: 0,
      ISOTO1: 0,
      ISOTO2: 0,
      ISOTO3: 0,
      ISOTO4: 0,
      ISOTO5: 0,

    }
    if (document.getElementById(`akses_checkbox_hasaccess${index}`).checked) {
      tempData.HASACCESS = 1
    }
    if (document.getElementById(`akses_checkbox_istambah${index}`).checked) {
      tempData.ISTAMBAH = 1
    }
    if (document.getElementById(`akses_checkbox_isKoreksi${index}`).checked) {
      tempData.ISKOREKSI = 1
    }
    if (document.getElementById(`akses_checkbox_isHapus${index}`).checked) {
      tempData.ISHAPUS = 1
    }
    if (document.getElementById(`akses_checkbox_isCetak${index}`).checked) {
      tempData.ISCETAK = 1
    }
    if (document.getElementById(`akses_checkbox_isBatal${index}`).checked) {
      tempData.ISBATAL = 1
    }
    if (document.getElementById(`akses_checkbox_isExport${index}`).checked) {
      tempData.ISEXPORT = 1
    }
    if (document.getElementById(`akses_checkbox_isOtorisasi1${index}`).checked) {
      tempData.ISOTO1 = 1
    }
    if (document.getElementById(`akses_checkbox_isOtorisasi2${index}`).checked) {
      tempData.ISOTO2 = 1
    }
    if (document.getElementById(`akses_checkbox_isOtorisasi3${index}`).checked) {
      tempData.ISOTO3 = 1
    }
    if (document.getElementById(`akses_checkbox_isOtorisasi4${index}`).checked) {
      tempData.ISOTO4 = 1
    }
    if (document.getElementById(`akses_checkbox_isOtorisasi5${index}`).checked) {
      tempData.ISOTO5 = 1
    }
    console.log(username)
    console.log(tempData)

    $.ajax({
      url: "{!! url('newsetpemakaispupdateakses') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username: username,
        tempData: tempData
      },
      success: function(res) {
        console.log(res)

      }
    })

  }

  function clickUpdateAksesMenuReport (index , kodemenu) {

    console.log('clickUpdateAksesMenuReport')
    console.log(index, kodemenu)
    let _token = $("#_token").val();
    let akses = []
    let aksesObj = {}
    let username = $("#input_report_username").val();

    let tempData = {
      KODEMENU: listAksesReport[index].KODEMENU,
      Keterangan: listAksesReport[index].Keterangan,
      HASACCESS: 0,
      ISDESIGN: 0,
      ISEXPORT: 0

    }

        if (document.getElementById(`akses_report_checkbox_hasaccess${index}`).checked) {
          tempData.HASACCESS = 1
        }
        if (document.getElementById(`akses_report_checkbox_isdesign${index}`).checked) {
          tempData.ISDESIGN = 1
        }
        if (document.getElementById(`akses_report_checkbox_isexport${index}`).checked) {
          tempData.ISEXPORT = 1
        }


    console.log(username)
    console.log(tempData)

    $.ajax({
      url: "{!! url('newsetpemakaispupdateaksesreport') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username: username,
        tempData: tempData
      },
      success: function(res){
        console.log(res)


      }
    })


  }

function buttonAdd () {
  $("#formAddUser").modal('toggle')
}

function editUser (user, fullname, kodebag, kodejab, kodekasir, limit, status, tingkat, nik) {
 let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('newsetpemakaidetailuser') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      username: user
    },
    success: function(res) {
      console.log("==========")

      document.getElementById('input_edit_NIK').value = res[0].keynik;
      document.getElementById('input_edit_user').value = res[0].USERID;
      document.getElementById('input_edit_namaLengkap').value = res[0].FullName;
      document.getElementById('input_edit_departemen').value = res[0].kodeBag;
      document.getElementById('input_edit_jabatan').value = res[0].KodeJab;
      document.getElementById('input_edit_level').value = res[0].TINGKAT;
      document.getElementById('input_edit_status').value = res[0].STATUS;
      document.getElementById('input_edit_kodeKasir').value = res[0].KodeKasir
      document.getElementById('input_edit_limit').value = Number(res[0].limit) ? res[0].limit : '0.00'

      $("#formEditUser").modal('toggle')

    }
  })


}

function buttonNIK () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('newsetpemakaiLoadKaryawan') !!}",
    type: "get",
    async: false,
    data: {
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
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonSelectKaryawan('${item.NIK}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.NIK}</td>
      <td>${item.Nama}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">NIK</th>
    <th scope="col">Nama</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'NIK Karyawan'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });

  $("#formModalOpen").modal('toggle')
}

function buttonSelectKaryawan(karyawan){
  document.getElementById('input_add_NIK').value = karyawan;
  // document.getElementById('input_edit_NIK').value = karyawan;

  $("#formModalOpen").modal("hide");
}

function buttonJabatan () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('newsetpemakaiLoadJabatan') !!}",
    type: "get",
    async: false,
    data: {
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
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonSelectJabatan('${item.KODEJAB}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.KODEJAB}</td>
      <td>${item.NamaJab}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">Kode</th>
    <th scope="col">Jabatan</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Jabatan'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });

  $("#formModalOpen").modal('toggle')
}

function buttonSelectJabatan(jabatan){
  document.getElementById('input_add_jabatan').value = jabatan;
  // document.getElementById('input_edit_NIK').value = jabatan;

  $("#formModalOpen").modal("hide");
}

function buttonDepartemen () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('newsetpemakaiLoadDepartemen') !!}",
    type: "get",
    async: false,
    data: {
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
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonSelectDepartemen('${item.KDDEP}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.KDDEP}</td>
      <td>${item.NMDEP}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">Kode</th>
    <th scope="col">Departemen</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Departemen'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });

  $("#formModalOpen").modal('toggle')
}

function buttonSelectDepartemen(departemen){
  document.getElementById('input_add_departemen').value = departemen;
  // document.getElementById('input_edit_NIK').value = jabatan;

  $("#formModalOpen").modal("hide");
}

let passwordCheckState = 0

function checkPassword (){

  var password = document.getElementById("input_add_password").value
  var passwordConfirm = document.getElementById("input_add_passwordConfirm").value

  if (password == passwordConfirm){
    passwordCheckState = 1
    alertify.success('Konfirmasi Password Sama')
  }
  else if (password != passwordConfirm){
    passwordCheckState = 0
    alertify.error('Konfirmasi Password Salah')
  }

}


function submitAddUserEdit () {
  let choice = 'U'
  let _token = $("#_token").val();
  let nik = $("#input_edit_NIK").val();
  let user = $("#input_edit_user").val();
  let password = $("#input_edit_password").val();
  let namaLengkap = $("#input_edit_namaLengkap").val();
  let departemen = $("#input_edit_departemen").val();
  let jabatan = $("#input_edit_jabatan").val();
  let level = $("#input_edit_level").val();
  let status = $("#input_edit_status").val();
  let kodeKasir = $("#input_edit_kodeKasir").val();
  let limit = $("#input_edit_limit").val();

  if (!nik) {
    alertify.warning("NIK harus diisi");
    return
  }

  if( !user || !namaLengkap || !departemen || !jabatan || !level || !status || !kodeKasir ) {
    alertify.warning("Data tidak lengkap");
    return
  }



  $.ajax({
    url: "{!! url('newsetpemakaiAddUser') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token, choice,
      nik, user, password, namaLengkap, departemen, jabatan, level, status, kodeKasir, limit
    },
    success: function(res) {
      console.log(res)
      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data User telah diedit");
        loadAll()
        $("#formEditUser").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}


function submitAddUserData () {
  let choice = 'I'
  let _token = $("#_token").val();
  let nik = $("#input_add_NIK").val();
  let user = $("#input_add_user").val();
  let password = $("#input_add_password").val();
  let namaLengkap = $("#input_add_namaLengkap").val();
  let departemen = $("#input_add_departemen").val();
  let jabatan = $("#input_add_jabatan").val();
  let level = $("#input_add_level").val();
  let status = $("#input_add_status").val();
  let kodeKasir = $("#input_add_kodeKasir").val();
  let limit = $("#input_add_limit").val();

  if (!nik) {
    alertify.warning("NIK harus diisi");
    return
  }

  if (!password) {
    alertify.warning("Password harus diisi");
    return
  }

  if (passwordCheckState == 0){
    alertify.error('Password tidak cocok, silahkan konfirmasi ulang.')
  }

  $.ajax({
    url: "{!! url('newsetpemakaiAddUser') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token, choice,
      nik, user, password, namaLengkap, departemen, jabatan, level, status, kodeKasir, limit
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data User telah ditambah");
        loadAll()
        $("#formAddUser").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

</script>

      <!-- start modal select modal open ( 1 modal buat beberapa fungsi, jadi tinggal inject data ) -->
      <div class="modal fade" id="formModalOpen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1200px">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="namaModalOpen"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <table id="tabelModalOpen" class="table table-bordered table-striped">
                <thead id='theadOpen' class="text-center bg-primary text-white">
                  <tr></tr>
                </thead>
                <tbody id="tabel_dataModalOpen" class="text-left">
                  <tr></tr>
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            </div>
          </div>
        </div>
      </div>
      <!-- End modal select modal open ( 1 modal buat beberapa fungsi, jadi tinggal inject data )-->


@endsection