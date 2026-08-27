@extends('gudang.newmaster')
@section('buttons')

@endsection
{{-- tampilan search bar 1 --}}
  @section('css')
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
  </style>
{{-- end tampilan search bar 3 --}}

{{-- tampilan search perkiraan --}}
  <style>
    #tabel_add_list_perkiraan_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_perkiraan_filter label input {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search perkiraan --}}

{{-- tampilan search costing --}}
  <style>
    #tabel_add_list_costing_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_costing_filter label input {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search costing --}}

{{-- tampilan search subcosting --}}
  <style>
    #tabel_add_list_subcosting_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_subcosting_filter label input {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search subcosting --}}

@endsection
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid mainpage">
<div class="" style="margin-top:-85px;">

  <!-- <div id="qrcode"></div> -->
  <div class="row">
    <div class="col-6 text-left" >
      <h2>Pembebanan Pemakaian</h2>
    </div>
    <div class="col-6 text-right">
      <!-- <button type="button" class="btn btn-primary btn-lg" style="
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
        Add SO
      </button> -->
    </div>
  </div>
<!-- <button onclick="loadAll()">tes</button> -->
</div>

<div id="printContainer" style="display:none">


</div>
<div id="contentContainer" class="" >
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
    <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
      <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true"
         style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; text-align: left;">
        PP Belum Otorisasi
      </a>
      <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"
         style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
        PP Sudah Otorisasi
      </a>
    </div>
  </nav>
</div>
</div>
<div class="card-body" style="padding:0;">
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    <div class="row">
      <div class="col-md-12">
        <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
          <table id="tabel" class="table table-bordered table-striped"  >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;"  scope="col">Actions</th>
                <th style="padding: 4px 12px;"  scope="col">No.Bukti</th>
                <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                <th style="padding: 4px 12px;"  scope="col">Keterangan</th>
                <th style="padding: 4px 12px;"  scope="col">Perkiraan</th>
              </tr>
            </thead>
            <tbody id="tabel_data" class="text-left" >
              @for ($i = 0; $i < count($outstandingArray); $i++)
            <tr>
              <td class='text-center'>
                <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('{{ $outstandingArray[$i][0]->NOBUKTI }}' )"><i class="bi bi-info"></i></button>
                <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $outstandingArray[$i][0]->NOBUKTI }}' , 'edit')"><i class="bi bi-pen"></i></button> 
                <button class="btn btn-info btn-sm" title="Otorisasi" onclick="buttonOtorisasi('{{ $outstandingArray[$i][0]->NOBUKTI }}', '{{ $outstandingArray[$i][0]->IsOtorisasi1 }}')">
                  <i class="bi bi-key"></i>
                </button>
              </td>
              <td>{{ $outstandingArray[$i][0]->NOBUKTI}}</td>
              <td>{!! date("Y/m/d", strtotime($outstandingArray[$i][0]->TANGGAL)) !!}</td>
              <td>{{ $outstandingArray[$i][0]->Keterangan}}</td>
              <td>{{ $outstandingArray[$i][0]->Perkiraan}}</td>
            </tr>
              @endfor
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  {{-- Tab belum oto --}}
  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
    <div class="row">
      <div class="col-md-12">
        <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
          <table id="tabel2" class="table table-bordered table-striped"  >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;"  scope="col">Actions</th>
                <th style="padding: 4px 12px;"  scope="col">No.Bukti</th>
                <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                <th style="padding: 4px 12px;"  scope="col">Keterangan</th>
                <th style="padding: 4px 12px;"  scope="col">Perkiraan</th>
                <th style="padding: 4px 12px;"  scope="col">User Oto</th>
                <th style="padding: 4px 12px;"  scope="col">Tanggal Oto</th>
                {{-- <th style="padding: 4px 12px;"  scope="col">Oto</th> --}}
              </tr>
            </thead>


            <tbody id="tabel2_data" class="text-left" >
              @for ($i = 0; $i < count($penerimaanArray); $i++)
            <tr>
              <td class='text-center'>
                <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('{{ $penerimaanArray[$i][0]->NOBUKTI }}' )"><i class="bi bi-info"></i></button>
               <button class="btn btn-danger btn-sm" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('{{ $penerimaanArray[$i][0]->NOBUKTI}}','{{ $penerimaanArray[$i][0]->IsOtorisasi1 }}')">
                  <i class="bi bi-key"></i>
                </button>
		<button style="" class="btn btn-primary btn-sm" type="button"   onclick="submitPrint('{{$penerimaanArray[$i][0]->NOBUKTI}}')" ><i class="bi bi-printer"></i></button>
              </td>
              <td>{{ $penerimaanArray[$i][0]->NOBUKTI}}</td>
              <td>{!! date("Y/m/d", strtotime($penerimaanArray[$i][0]->TANGGAL)) !!}</td>
              <td>{{ $penerimaanArray[$i][0]->Keterangan}}</td>
              <td>{{ $penerimaanArray[$i][0]->Perkiraan}}</td>
              <td>{{ $penerimaanArray[$i][0]->OtoUser1}}</td>
              <td>
                @if ($penerimaanArray[$i][0]->TglOto1)
                  {{ \Carbon\Carbon::parse($penerimaanArray[$i][0]->TglOto1)->format('Y/m/d') }}
                @endif
              </td>

              {{-- @if ($tempPenerimaan[$i]->IsOtorisasi1)
                  <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                  @else
                  <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                  @endif --}}
            </tr>
              @endfor
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  {{-- Tab sudah oto --}}
  {{-- <div class="tab-pane fade" id="home2" role="tabpanel" aria-labelledby="home2-tab">
          <div class="row">
            <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
              <div class="container-fluid">
                <table id="tabel_oto" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th style="padding: 4px 12px;"  scope="col">Actions</th>
                      <th style="padding: 4px 12px;"  scope="col">No. Urut</th>
                      <th style="padding: 4px 12px;"  scope="col">No. Bukti</th>
                      <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                      <th style="padding: 4px 12px;"  scope="col">Sales</th>
                      <th style="padding: 4px 12px;"  scope="col">User</th>
                      <th style="padding: 4px 12px;"  scope="col">No.Ref</th>
                      <th style="padding: 4px 12px;"  scope="col">User Oto</th>
                      <th style="padding: 4px 12px;"  scope="col">Tgl Oto</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_oto_data" class="text-left">
                     @for ($i = 0; $i < count($tempPenerimaan2); $i++)
                    <tr>
                      <td class='text-center'>
                        <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('{{ $tempPenerimaan2[$i]->NOBUKTI }}' )"><i class="bi bi-info"></i></button>
                        @if ($tempPenerimaan2[$i]->IsOtorisasi1 == 1)
                        <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempPenerimaan2[$i]->NOBUKTI }}' , 'edit')"><i class="bi bi-key"></i></button>
                        @else
                        <button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('{{ $tempPenerimaan2[$i]->NOBUKTI }}' , 'add')"><i class="bi bi-key"></i></button>
                        @endif
                      </td>
                      <td>{{ $tempPenerimaan2[$i]->NOURUT}}</td>
                      <td>{{ $tempPenerimaan2[$i]->NOBUKTI}}</td>
                      <td>{!! date("Y/m/d", strtotime($tempPenerimaan2[$i]->TANGGAL)) !!}</td>
                      <td>{{ $tempPenerimaan2[$i]->NAMASLS}}</td>
                      <td>{{ $tempPenerimaan2[$i]->IDUSER}}</td>
                      <td>{{ $tempPenerimaan2[$i]->RefPR}}</td>
                      <td>{{ $tempPenerimaan2[$i]->OtoUser1 }}</td>
                      <td>{!! $tempPenerimaan2[$i]->TglOto1 ? date("Y/m/d", strtotime($tempPenerimaan2[$i]->TglOto1)) : '' !!}</td>
                    </tr>
                @endfor
                </tbody> 
                </table>
              </div>
            </div>
          </div>
        </div> --}}
      {{-- end tab sudah oto --}}
  
</div>
</div>
</div>
</div>
</div>





<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row" style="margin-top:-80px">
    <div class="col-8 text-left">
      <h2>Form Penyerahan Sample</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; margin-top:5px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div class="container-fluid">
    <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
    <div class="row">
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nobukti" placeholder="" disabled>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}"  >
              </div>
            </div>
          </div>
          </div>
        </div>
      </div>
    </div>
    <hr/>
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

              <table id="addTable" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Serahkan</th>
                    <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Qty</th>
                    <th style="padding: 4px 12px;" scope="col">Satuan</th>
                    <th style="padding: 4px 12px;" scope="col">Stock</th>
                  </tr>
                </thead>
                <tbody id="addTableData" class="" >
                  <tr>
                    <td colspan=8 class="text-center">Belum ada data</td>
                  </tr>
                </tbody>
              </table>
    </div>
    <div class="row mt-2" style="margin-top: 0">
      <div class="col-md-12 text-right mt-4">
        <button id="buttonSubmitAdd" type="button" onclick="submitAdd()" class="btn btn-primary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Submit</button>
        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      </div>
    </div>
  </div>
</div>


<div id="page3" style="display: none" class="mainpage container-fluid" >
  <div class="row" style="margin-top:-80px">
    <div class="col-8 text-left">
      <h2>Koreksi Pembebanan Pemakaian</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; margin-top:5px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div class="container-fluid">
    {{-- <input type="hidden" name="noUrut" id="input_koreksi_nourut" value="" /> --}}
    <div class="row">
        <input type="hidden" class="form-control" id="input_koreksi_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label" style="margin-top:-5px;">No Bukti</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-left" id="input_koreksi_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
              <table id="koreksiTable" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                <tr>
                  <th colspan="6">Deskripsi Barang</th>
                  <th colspan="1"></th>
                </tr>
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Satuan</th>
                  <th scope="col">Perkiraan</th>
                  <th scope="col">Nama Perkiraan</th>
                  <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody id="koreksiTableData" class="" >
                  <tr>
                    <td colspan=11 class="text-center">Belum ada data</td>
                </tr>
                </tbody>
              </table>
    </div>

    <div id="formKoreksiEdit" class="container-fluid showhideitem">
    <div class="row">
      <div class="col-4">
        <h4 id="h4KoreksiEditItem" style="margin-left:-15px;">Edit Item</h4>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-3" style="margin-top:5px;">
            <label class="form-label fw-bold">Perkiraan</label>
          </div>
          <div class="col-md-4">
            <div class="input-group mb-3">
            <input id="KoreksiEditPerkiraan" type="text" class="form-control text-left bg-light" placeholder="Perkiraan" disabled>
            <button type="button" id="buttonKoreksiListPerkiraan" onclick="buttonKoreksiListPerkiraan()" class="btn btn-primary btn-sm rounded-end shadow-sm"><i class="bi bi-plus"></i></button>
            <input type="hidden" id="KoreksiEditNamaPerkiraan">
            </div>
          </div>
        </div>

        <div class="row" style="margin-top:-10px;">
          <div class="col-md-3" style="margin-top:5px;">
            <label class="form-label fw-bold">Costing</label>
          </div>
          <div class="col-md-4">
            <div class="input-group mb-3">
            <input id="KoreksiEditCosting" type="text" class="form-control text-left bg-light" placeholder="Costing" disabled>
            <button type="button" id="buttonKoreksiListCosting" onclick="buttonKoreksiListCosting()" class="btn btn-primary btn-sm rounded-end shadow-sm"><i class="bi bi-plus"></i></button>
            <input type="hidden" id="input_costing">
            </div>
          </div>
        </div>

        <div class="row" style="margin-top:-10px;">
          <div class="col-md-3" style="margin-top:5px;">
            <label class="form-label fw-bold">Sub Costing</label>
          </div>
          <div class="col-md-4">
            <div class="input-group mb-3">
            <input id="KoreksiEditSubCosting" type="text" class="form-control text-left bg-light" placeholder="Sub Costing" disabled>
            <button type="button" id="buttonKoreksiListSubCosting" onclick="buttonKoreksiListSubCosting()" class="btn btn-primary btn-sm rounded-end shadow-sm"><i class="bi bi-plus"></i></button>
            <input type="hidden" id="input_sub_costing">
          </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" onclick="buttonKoreksiItemBatal()" style="
          height: 30px; 
          padding: 4px 12px; 
          border-radius: 20px; 
          font-size: 0.75rem; 
          font-weight: 600; 
          text-transform: uppercase; 
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">Batal</button>

        <button id="buttonSubmitKoreksiEdit" type="button" onclick="submitKoreksiEdit()" class="btn btn-primary" style="
          height: 30px; 
          padding: 4px 12px; 
          border-radius: 20px; 
          font-size: 0.75rem; 
          font-weight: 600; 
          text-transform: uppercase; 
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">Submit Edit</button>
      </div>
    </div>
  </div>
    <hr/>

  </div>
</div>
</div>
{{-- Start Modal List perkiraan --}}
  <div class="modal fade" id="modalAddListPerkiraan" role="dialog" aria-labelledby="labelPerkiraan" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="labelPerkiraan"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" style="margin-top:-30px;">
          <div class="container-fluid px-3 mt-4">
            <div class="row">
              <div class="table-responsive">
                <table id="tabel_add_list_perkiraan" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th>Actions</th>
                      <th>Perkiraan</th>
                      <th>Keterangan</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_perkiraan" class="text-left">
                    <tr>
                      <td class="text-center">
                        <button class="btn btn-primary btn-sm" type="button"><i class="bi bi-plus"></i></button>
                      </td>
                      <td>-</td>
                      <td>-</td> 
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
              <button type="button" class="btn btn-danger btn-lg"
                style="height: 30px; padding: 4px 12px; border-radius: 20px;
                font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"
                onclick="buttonAddListBatal()">Batal</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{{-- End Modal List perkiraan --}}

{{-- Start Modal List costing --}}
  <div class="modal fade" id="modalAddListCosting" role="dialog" aria-labelledby="labelCosting" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="labelCosting"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" style="margin-top:-30px;">
          <div class="container-fluid px-3 mt-4">
            <div class="row">
              <div class="table-responsive">
                <table id="tabel_add_list_costing" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th>Actions</th>
                      <th>Kode Cost</th>
                      <th>Nama Cost</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_costing" class="text-left">
                    <tr>
                      <td class="text-center">
                        <button class="btn btn-primary btn-sm" type="button"><i class="bi bi-plus"></i></button>
                      </td>
                      <td>-</td>
                      <td>-</td>   
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
              <button type="button" class="btn btn-danger btn-lg"
                style="height: 30px; padding: 4px 12px; border-radius: 20px;
                font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"
                onclick="buttonAddListBatal()">Batal</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{{-- End Modal List costing --}}

{{-- Start Modal List subcosting --}}
  <div class="modal fade" id="modalAddListSubCosting" role="dialog" aria-labelledby="labelSubCosting" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="labelSubCosting"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" style="margin-top:-30px;">
          <div class="container-fluid px-3 mt-4">
            <div class="row">
              <div class="table-responsive">
                <table id="tabel_add_list_subcosting" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th>Actions</th>
                      <th>Kode Cost</th>
                      <th>Kode Sub Cost</th>
                      <th>Nama Sub Cost</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_subcosting" class="text-left">
                    <tr>
                      <td class="text-center">
                        <button class="btn btn-primary btn-sm" type="button"><i class="bi bi-plus"></i></button>
                      </td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>   
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
              <button type="button" class="btn btn-danger btn-lg"
                style="height: 30px; padding: 4px 12px; border-radius: 20px;
                font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"
                onclick="buttonAddListBatal()">Batal</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{{-- End Modal List subcosting --}}

<div id="page4" style="display: none" class="mainpage container-fluid" >
  <div class="row" style="margin-top:-80px">
    <div class="col-8 text-left">
      <h2>Detail Pembebanan Pemakaian</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; margin-top:5px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
        <input type="hidden" class="form-control" id="input_detailkoreksi_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">No Bukti</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-left" id="input_detailkoreksi_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
        </div>
        </div>
    <hr/>
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
              <table id="detailKoreksiTable" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                <tr>
                  <th colspan="6">Deskripsi Barang</th>
                </tr>
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Satuan</th>
                  <th scope="col">Perkiraan</th>
                  <th scope="col">Nama Perkiraan</th>
                </tr>
                </thead>
                <tbody id="detailKoreksiTableData" class="" >
                  <tr>
                    <td colspan=6 class="text-center">Belum ada data</td>
                </tr>
                </tbody>
              </table>
    </div>
  </div>
</div>





@endsection

@section('js')
<script type="text/javascript">



let dataTableAdd = []
let dataTableKoreksi = []
let barangKoreksiEdit = {}


$(document).ready(function(){
  $("#tabel").DataTable({
    "lengthChange": false,
      "paging": false ,
    });

  $("#tabel2").DataTable({
    "lengthChange": false,
      "paging": false ,
      "autoWidth": false,
    });

  //   formAddListItem
});

function buttonKoreksiListPerkiraan () {
  console.log('buttonKoreksiListPerkiraan');
  if ($.fn.DataTable.isDataTable('#tabel_add_list_perkiraan')) {
    $('#tabel_add_list_perkiraan').DataTable().destroy();
  }

  $.ajax({
    url: "{{ url('pembebananpemakaianlistperkiraan') }}",
    type: "get",
    async: false,
    success: function(res) {
      console.log(res);

      let rowTable = ``;
      res.forEach((item, i) => {
        rowTable += `
          <tr>
            <td class="text-center">
              <button class="btn btn-primary btn-sm" type="button"
                onclick="buttonAddPickPerkiraan('${item.Perkiraan}', '${item.Keterangan}')">
                <i class="bi bi-plus"></i>
              </button>
            </td>
            <td>${item.Perkiraan ?? ''}</td>
            <td>${item.Keterangan ?? ''}</td>
          </tr>`;
      });

      if (!res.length) {
        rowTable = `<tr><td class="text-center" colspan="4">Tidak ada data</td></tr>`;
      }

      document.getElementById("tabel_data_add_list_perkiraan").innerHTML = rowTable;
      $("#tabel_add_list_perkiraan").DataTable({
        "lengthChange": false,
        "paging": false,
      });

      $('#modalAddListPerkiraan').modal('show');
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan saat mengambil data perkiraan.');
    }
  });
}

function buttonAddPickPerkiraan(Perkiraan, Keterangan) {
  $('#KoreksiEditPerkiraan').val(Perkiraan);
  $('#KoreksiEditNamaPerkiraan').val(Keterangan);
  $('#modalAddListPerkiraan').modal('hide');

  // reset
  $('#KoreksiEditCosting').val("");
  $('#input_costing').val("");
  $('#KoreksiEditSubCosting').val("");
  $('#input_subcosting').val("");
}


function buttonKoreksiListCosting() {
  let perkiraan = $('#KoreksiEditPerkiraan').val();

  if (!perkiraan) {
    alertify.warning('Silakan pilih perkiraan terlebih dahulu.');
    return;
  }

  $.ajax({
    url: "{{ url('pembebananpemakaianlistcosting') }}",
    type: "get",
    data: { perkiraan: perkiraan },
    success: function(res) {
      console.log("Costing result:", res);

      if (!res.length) {
        $('#KoreksiEditCosting').val(""); 
        $('#input_costing').val(""); 
        alertify.message('Perkiraan ini tidak memiliki costing.');
        return;
      }

      if ($.fn.DataTable.isDataTable('#tabel_add_list_costing')) {
        $('#tabel_add_list_costing').DataTable().destroy();
      }

      let rowTable = ``;
      res.forEach((item, i) => {
        rowTable += `
          <tr>
            <td class="text-center">
              <button class="btn btn-primary btn-sm" type="button"
                onclick="buttonAddPickCosting('${item.KodeCost}', '${item.NamaCost}')">
                <i class="bi bi-plus"></i>
              </button>
            </td>
            <td>${item.KodeCost}</td>
            <td>${item.NamaCost}</td>
          </tr>`;
      });

      document.getElementById("tabel_data_add_list_costing").innerHTML = rowTable;
      $("#tabel_add_list_costing").DataTable({
        "lengthChange": false,
        "paging": false,
      });

      $('#modalAddListCosting').modal('show');
    },
    error: function(err) {
      console.log("AJAX error costing:", err);
      alertify.warning('Terjadi kesalahan saat mengambil data costing.');
    }
  });
}


function buttonAddPickCosting(KodeCost, NamaCost) {
  $('#KoreksiEditCosting').val(KodeCost);
  $('#input_costing').val(NamaCost);
  $('#modalAddListCosting').modal('hide');

  // reset
  $('#KoreksiEditSubCosting').val("");
  $('#input_subcosting').val("");
}

function buttonKoreksiListSubCosting () {
  let kodeCost = $('#KoreksiEditCosting').val();

  if (!kodeCost) {
    alertify.warning('Silakan pilih costing terlebih dahulu.');
    return;
  }

  $.ajax({
    url: "{{ url('pembebananpemakaianlistsubcosting') }}", 
    type: "get",
    data: { kodeCost: kodeCost },
    success: function(res) {
      console.log("SubCosting result:", res);

      if (!res.length) {
        $('#KoreksiEditSubCosting').val(""); 
        $('#input_subcosting').val(""); 
        alertify.message('Costing ini tidak memiliki sub costing.');
        return;
      }

      if ($.fn.DataTable.isDataTable('#tabel_add_list_subcosting')) {
        $('#tabel_add_list_subcosting').DataTable().destroy();
      }

      let rowTable = ``;
      res.forEach((item, i) => {
        rowTable += `
          <tr>
            <td class="text-center">
              <button class="btn btn-primary btn-sm" type="button"
                onclick="buttonAddPickSubCosting('${item.KodeSubCost}', '${item.NamaSubCost}')">
                <i class="bi bi-plus"></i>
              </button>
            </td>
            <td>${item.KodeCost}</td>
            <td>${item.KodeSubCost}</td>
            <td>${item.NamaSubCost}</td>
          </tr>`;
      });

      document.getElementById("tabel_data_add_list_subcosting").innerHTML = rowTable;
      $("#tabel_add_list_subcosting").DataTable({
        "lengthChange": false,
        "paging": false,
      });

      $('#modalAddListSubCosting').modal('show');
    },
    error: function(err) {
      console.log("AJAX error subcosting:", err);
      alertify.warning('Terjadi kesalahan saat mengambil data sub costing.');
    }
  });
}


function buttonAddPickSubCosting(KodeSubCost, NamaSubCost) {
  $('#KoreksiEditSubCosting').val(KodeSubCost);
  $('#input_subcosting').val(NamaSubCost);
  $('#modalAddListSubCosting').modal('hide');
}



function buttonAddListBatal() {
  $('#modalAddListPerkiraan').modal('hide');
  $('#modalAddListCosting').modal('hide');
  $('#modalAddListSubCosting').modal('hide');
}


function buttonKoreksiEditItem (i) {
  let barang = dataTableKoreksi[i];
  barangKoreksiEdit = barang;

  document.getElementById("KoreksiEditPerkiraan").value = barang.KodePerkiraan;
  document.getElementById("KoreksiEditNamaPerkiraan").value = barang.namaPerkiraan;

  document.getElementById("KoreksiEditCosting").value = barang.KODECOST;
  document.getElementById("input_costing").value = barang.NamaCost;

  document.getElementById("KoreksiEditSubCosting").value = barang.KODESUBCOST;
  document.getElementById("input_sub_costing").value = barang.NamaSubCost;

  $('#formKoreksiEdit').show();
}

function refreshDataTableKoreksi (nobukti) {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('pembebananpemakaiangetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: nobukti
    },
    success: function (res) {
      console.log('res', res);

      if (!res || res.length === 0) {
        buttonCloseForm();
        alertify.warning("Data tidak ditemukan");
        return;
      }

      dataTableKoreksi = res;

      let rowTable = "";

      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td>${item.KodeBrg}</td>
          <td>${item.NamaBrg}</td>
          <td class="text-right">${parseFloat(item.Qnt).toLocaleString()}</td>
          <td class="text-center">${item.Satuan}</td>
          <td>${item.KodePerkiraan ?? ''}</td>
          <td>${item.namaPerkiraan ?? ''}</td>
          <td class="text-center">
            <button class="btn btn-success btn-sm" onclick="buttonKoreksiEditItem(${i})"><i class="bi bi-pen"></i></button>
          </td>
        </tr>`;
      });

      document.getElementById("koreksiTableData").innerHTML = rowTable;

      let header = res[0];

      $("#input_koreksi_nobukti").val(header.NoBukti || header.NOBUKTI);

      buttonKoreksiItemBatal();
    },
    error: function (err) {
      console.error(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}



function buttonOtorisasi(nobukti, isOtorisasi) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  if (Number(isOtorisasi) > 0) {
    alertify.warning('Sudah diotorisasi');
    return;
  }

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('pembebananpemakaianspotorisasi') !!}",
    type: "post",
    dataType: "json",
    data: {
      _token,
      nobukti,
      otorisasi: 1
    },
    success: function (res) {
      if (res.status > 0) {
        alertify.success(res.msg);
        loadAll();
      } else {
        alertify.warning(res.msg);
      }
    },
    error: function (err) {
      console.log(err);
      alertify.error('Terjadi kesalahan. Silakan refresh browser.');
    }
  });
}

function buttonBatalOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
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
        url: "{!! url('pembebananpemakaianspbatalotorisasi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti,
          pket :value
        },
        success: function (res) {
          alertify.success('Berhasil batal otorisasi');
          loadAll();
        },
        error: function (err) {
          console.error(err);
          alertify.warning('Terjadi kesalahan, silakan refresh browser');
        }
      });
    },
    function () {
      console.log('Batal otorisasi dibatalkan');
      alertify.error("Action cancelled");
    }
  );
}


function submitKoreksiEdit () {
  let _token = $("#_token").val();

  if (!barangKoreksiEdit) {
    alertify.warning('Tidak ada data yang dipilih untuk koreksi.');
    return;
  }

  let nobukti = barangKoreksiEdit.NoBukti || barangKoreksiEdit.NOBUKTI;
  let urut    = barangKoreksiEdit.Urut || barangKoreksiEdit.URUT;

  let kodePerkiraan = ($('#KoreksiEditPerkiraan').val() || '').trim();
  let kodeCost      = ($('#KoreksiEditCosting').val() || '').trim();     
  let kodeSubCost   = ($('#KoreksiEditSubCosting').val() || '').trim();   

  if (!nobukti || !urut) {
    alertify.warning('Data item tidak lengkap (NoBukti/Urut).');
    return;
  }
  if (!kodePerkiraan) {
    alertify.warning('Perkiraan wajib diisi.');
    return;
  }

  $.ajax({
    url: "{!! url('pembebananpemakaianspkoreksi') !!}",
    type: "post",
    data: {
      _token,
      nobukti,
      urut,
      kodeperkiraan: kodePerkiraan,
      kodecost: kodeCost,
      kodesubcost: kodeSubCost
    },
    success: function (res) {
      if (res && res.success) {
        $("#modalKoreksiEdit").modal("hide");
        refreshDataTableKoreksi(nobukti);
        loadAll();
        alertify.success(res.message || 'Koreksi akun berhasil disimpan.');
      } else {
        alertify.warning(res.message || 'Koreksi gagal disimpan.');
      }
    },
    error: function (err) {
      console.log(err);
      const msg = err?.responseJSON?.message || 'Terjadi kesalahan, silakan refresh browser';
      alertify.warning(msg);
    }
  });
}



function buttonKoreksiItemBatal () {

  $('.showhideitem').hide();
}


function buttonKoreksi (nobukti) {
  console.log('buttonKoreksi', nobukti);

  let akses = $("#akses_iskoreksi").val();
  $('.showhideitem').hide();

  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('pembebananpemakaiangetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: nobukti
    },
    success: function (res) {
      console.log('res', res);

      if (!res || res.length === 0) {
        alertify.warning("Data tidak ditemukan");
        return;
      }

      const data = res[0];

      if (data.IsOtorisasi1 == 1) {
        alertify.warning("Data sudah diotorisasi");
        return;
      }

      dataTableKoreksi = res;

      // Isi Tabel Item
      let rowTable = "";
      res.forEach((item, i) => {
        rowTable += `<tr> 
          <td>${item.KodeBrg}</td>
          <td>${item.NamaBrg}</td>
          <td class="text-right">${parseFloat(item.Qnt).toLocaleString()}</td>
          <td class="text-center">${item.Satuan}</td>
          <td>${item.KodePerkiraan ?? ''}</td>
          <td>${item.namaPerkiraan ?? ''}</td>
          <td class="text-center">
            <button class="btn btn-success btn-sm" onclick="buttonKoreksiEditItem(${i})"><i class="bi bi-pen"></i></button>
          </td>
        </tr>`;
      });

      $("#koreksiTableData").html(rowTable);

      // Isi Form Header
      $("#input_koreksi_nobukti").val(data.NoBukti);

      const tanggal = data.TANGGAL;
      $("#input_koreksi_tanggal").val(tanggal);

      $('.mainpage').hide();
      $('#page3').show();
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

function buttonDetailKoreksi (nobukti) {
  console.log('buttonDetailKoreksi', nobukti);

  $('.showhideitem').hide();

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('pembebananpemakaiangetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function (res) {
      console.log('res', res);

      if (!res || res.length === 0) {
        alertify.warning("Data tidak ditemukan");
        return;
      }

      // Isi Tabel Detail Item
      let rowTable = "";
      res.forEach((item, i) => {
        rowTable += `<tr>
          <td>${item.KodeBrg}</td>
          <td>${item.NamaBrg}</td>
          <td class="text-right">${parseFloat(item.Qnt).toLocaleString()}</td>
          <td class="text-center">${item.Satuan}</td>
          <td>${item.KodePerkiraan ?? ''}</td>
          <td>${item.namaPerkiraan ?? ''}</td>
        </tr>`;
      });

      $("#detailKoreksiTableData").html(rowTable);

      // Isi Header Detail
      const data = res[0];

      $("#input_detailkoreksi_nobukti").val(data.NoBukti);

      $('.mainpage').hide();
      $('#page4').show();
    },
    error: function (err) {
      console.error('Error response:', err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}


function loadAll () {
  $.ajax({
    url: "{!! url('pembebananpemakaianloadall') !!}",
    type: "get",
    async: false,
    success: function (res) {
      // ===================== TAB 1 (Outstanding) =====================
      if ($.fn.DataTable.isDataTable('#tabel')) {
        $('#tabel').DataTable().clear().destroy();
      }
      let rowTable = '';

      res.tempOutstanding.forEach((group) => {
      let item = group[0];
        rowTable += `
          <tr>
            <td class="text-center">
              <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('${item.NOBUKTI}')">
                <i class="bi bi-info"></i>
              </button>
              <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item.NOBUKTI}', 'edit')">
                <i class="bi bi-pen"></i>
              </button>
              <button class="btn btn-info btn-sm" title="Otorisasi" onclick="buttonOtorisasi('${item.NOBUKTI}')">
                <i class="bi bi-key"></i>
              </button>
            </td>
            <td>${item.NOBUKTI}</td>
            <td>${item.TANGGAL ? formatDate(item.TANGGAL, '/') : ''}</td>
            <td>${item.Keterangan || ''}</td>
            <td>${item.Perkiraan || ''}</td>
          </tr>`;
      });

      document.getElementById("tabel_data").innerHTML = rowTable;
      $("#tabel").DataTable({
        lengthChange: false,
        paging: false
      });

      // ===================== TAB 2 =====================
      if ($.fn.DataTable.isDataTable('#tabel2')) {
        $('#tabel2').DataTable().clear().destroy();
      }
      let rowTable2 = '';

      res.tempPenerimaan.forEach((group) => {
      let item = group[0];
        rowTable2 += `
          <tr>
            <td class="text-center">
              <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('${item.NOBUKTI}')">
                <i class="bi bi-info"></i>
              </button>
              ${
                item.IsOtorisasi1 == 1
                  ? `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${item.NOBUKTI}', 'edit')">
                      <i class="bi bi-key"></i>
                    </button>`
                  : `<button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('${item.NOBUKTI}', 'add')">
                      <i class="bi bi-key"></i>
                    </button>`
              }
	      <button class="btn btn-primary btn-sm" title="Print" onclick="submitPrint('${item.NOBUKTI}')">
                <i class="bi bi-printer"></i>
              </button>
            </td>
            <td>${item.NOBUKTI}</td>
            <td>${item.TANGGAL ? formatDate(item.TANGGAL, '/') : ''}</td>
            <td>${item.Keterangan || ''}</td>
            <td>${item.Perkiraan || ''}</td>
            <td>${item.OtoUser1 || ''}</td>
            <td>${item.TglOto1 ? formatDate(item.TglOto1, '/') : ''}</td>
          </tr>`;
      });

      document.getElementById("tabel2_data").innerHTML = rowTable2;
      $("#tabel2").DataTable({
        lengthChange: false,
        paging: false
      });
    }
  });
}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('pembebananpemakaiandetailCetak') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {
        console.log(res,'zzzzz')

        dataPrint = res
        console.log(res[0])
        console.log(res[0][0])
        
        // console.log(res[0][0].IsOtorisasi1)

      }
    })
    
    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=7) {
      let tempArray = dataPrint.slice(i,i+7)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0];

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
        height: 13.5cm;
        position: relative;
      }

      .footer-sign {
        padding-top: 5px;
        position: absolute;
        width: 100%;
        bottom: 12px;
      }

      .footer-print-date {
        position: absolute;
        width: 100%;
        bottom: 5px;
      }

       .solid{
        border-left: 0px red solid;
        height: 225px;
        width: 0px;
        display: inline-block;
        padding-left: 0px;
        }

      </style>`;
    hdr = `<div class="" style="display: flex; width: 100%">
              <div class="pe-1" style="width: 50%">
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 15%; margin-top: 15px">
                    `+ imageContent +`
                  </div>
                  <div class="pb-1 ps-3" style="width: 85%; ">
                    <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                  </div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Departemen : </div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Untuk Keperluan : </div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">BUKTI PEMAKAIAN INTERNAL ACC</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].NoBukti+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Tanggal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+tanggalOnly+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>
              <div
                style="
                  width: 12%;
                  height: 80px;
                  overflow: hidden;
                "
                >
                `+printContent+`
              </div>
            </div>
      <table

                class="detail-spb-table"
                style="width: 100%; height: 225px; max-height: 225px;font-family: sans-serif;  display: table;
                font-size: 10px">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%">No.</td>
                    <td class="text-center" style="width: 50%">URAIAN BARANG</td>
                    <td class="text-center" style="width: 5%">SATUAN</td>
                    <td class="text-center" style="width: 5%">QTY</td>
                    <td class="text-center" style="width: 10%">HARGA SAT</td>
                    <td class="text-center" style="width: 10%">TOTAL</td>
                    <td class="text-center" style="width: 10%">COST</td>
                    <td class="text-center" style="width: 10%">COA</td>
                    <td class="text-center" style="width: 50%">COA</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotal = 0;
    arrayDataPrint.forEach(group => {
      group.forEach(item => {
        if (item.Total) {
          grandTotal += parseFloat(item.Total) || 0;
        }
      });
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
        tempPrintStr += `<tbody border="1">`;
        item.forEach((itemSub, j) => {
          tempPrintStr += ``
	console.log('oooo',itemSub);


         tempPrintStr += `
         <tr>
         <td class="text-align: center"
               style="width: 2%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 50%;  ">${itemSub.NamaBrg}</td>
         <td class="text-align: text-center"
               style="width: 5%;">${itemSub.Sat}</td>
         <td class="text-align: text-right"
               style="width: 5%;  ">${itemSub.Qnt ? parseFloat(itemSub.Qnt).toFixed(2) : ''}</td>
         <td style="width: 10%; text-align: right;">
            ${itemSub.HPP 
              ? Number(itemSub.HPP).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                }) 
              : ''}
         </td>
         <td style="width: 10%; text-align: right;">
            ${itemSub.Total 
              ? Number(itemSub.Total).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                }) 
              : ''}
         </td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.KODESUBCOST ? parseFloat(itemSub.KODESUBCOST).toFixed(2) : ''}</td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.KodePerkiraan}</td>
         <td class="text-align: left"
               style="width: 50%;">${itemSub.NamaPerkiraan}</td>
         </tr>`;

           z++;

        });
        tempPrintStr +=`
          <tr style>

          </tr>`;

         tempPrintStr += `</tbody>`;

         tempPrintStr += `</table>
         
          <hr style="margin-top: -6px" />

         <div class="footer-sign font-family: sans-serif;
           font-size: 10px ">

         <div class="row mt-3" style="text-align: left;font-family: sans-serif;
         font-size: 12px ">
         <span style="float: left; display: block; clear: left;">
         </span>

         <span style="float: left; display: block; clear: left;">
          <h5>
          Total : ${grandTotal.toLocaleString('id-ID', {minimumFractionDigits: 2,maximumFractionDigits: 2
                })}
          </h5>
         </span>
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -15px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%"></td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Dibuat Oleh</td>
               <td class="no-border text-center" style="width: 10%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
              <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
               <td class="no-border px-2">
               </td>
             </tr>
           </table>
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

function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();
  loadAll();
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


</script>

{{-- script buat hover belum otorisasi dan sudah otorisasi --}}
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
{{-- script buat hover belum otorisasi dan sudah otorisasi --}}



@endsection
