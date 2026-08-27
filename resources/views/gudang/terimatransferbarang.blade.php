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
@endsection
@section('content')



<div id="page1" class="container-fluid mainpage">
<div class="" style="margin-top:-85px;">

  <!-- <div id="qrcode"></div> -->
  <div class="row">
    <div class="col-6 text-left">
      <h2>Terima Transfer Barang</h2>
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
<div class="card-header" style="">
<div class="row">
    <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
      <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true"
         style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; text-align: left;">
        Transfer Barang
      </a>
      <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"
         style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
        Terima Transfer Barang
      </a>
      {{-- <a class="nav-item nav-link" id="nav-home2-tab" data-toggle="tab" href="#home2" role="tab"
          aria-controls="nav-home2" aria-selected="false"
          style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff;">
        SSK Sudah Otorisasi
      </a> --}}
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
                <th style="padding: 4px 12px;"  scope="col">Gudang Asal</th>
                <th style="padding: 4px 12px;"  scope="col">Gudang Tujuan</th>
                <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                <th style="padding: 4px 12px;"  scope="col">Keterangan</th>
              </tr>
            </thead>


            <tbody id="tabel_data" class="text-left" >
              @for ($i = 0; $i < count($tempOutstanding); $i++)
            <tr>
              <td class='text-center'>
                <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailTransferBarang('{{ $tempOutstanding[$i]->NOBUKTI }}')"><i class="bi bi-info"></i></button>
                <button class="btn btn-success btn-sm" type="button" onclick="buttonAdd('{{ $tempOutstanding[$i]->NOBUKTI }}')"><i class="bi bi-plus"></i></button>
              </td>
              <td>{{ $tempOutstanding[$i]->NOBUKTI }}</td>
              <td>{{ $tempOutstanding[$i]->NamagdgAsal }}</td>
              <td>{{ $tempOutstanding[$i]->NamagdgTujuan }}</td>
              <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i]->TANGGAL)) !!}</td>
              <td>{{ $tempOutstanding[$i]->NOTE }}</td>
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
      <div class="col-12" style="overflow:auto;">
        <div class="container-fluid" style="padding:0; margin:0; width:100%;">
          <table id="tabel2" class="table table-bordered table-striped"  >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;"  scope="col">Actions</th>
                <th style="padding: 4px 12px;"  scope="col">No. Bukti</th>
                <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                <th style="padding: 4px 12px;"  scope="col">Keterangan</th>
              </tr>
            </thead>


            <tbody id="tabel2_data" class="text-left" >
              @for ($i = 0; $i < count($tempPenerimaan); $i++)
            <tr>
              <td class='text-center'>
                <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('{{ $tempPenerimaan[$i]->NOBUKTI }}' )"><i class="bi bi-info"></i></button>
                <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $tempPenerimaan[$i]->NOBUKTI }}' , 'edit')"><i class="bi bi-pen"></i></button>
              </td>
              <td>{{ $tempPenerimaan[$i]->NOBUKTI}}</td>
              <td>{!! date("Y/m/d", strtotime($tempPenerimaan[$i]->TANGGAL)) !!}</td>
              <td>{{ $tempPenerimaan[$i]->NOTE}}</td>
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

  <div class="row" style="margin-top: -80px">
    <div class="col-8 text-left">
      <h2>Form Terima Transfer Barang</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
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
                    <th style="padding: 4px 12px;" scope="col">Terima</th>
                    <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                    <th style="padding: 4px 12px;" scope="col">No Bukti</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Satuan</th>
                    <th style="padding: 4px 12px;" scope="col">Qty Transfer</th>
                    <th style="padding: 4px 12px;" scope="col">Qty Terima</th>
                  </tr>
                </thead>
                <tbody id="addTableData" class="" >
                  <tr>
                    <td colspan=7 class="text-center">Belum ada data</td>
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


<div id="page3" style="display: none; margin-top:-80px" class="mainpage container-fluid" >
  <div class="row" >
    <div class="col-8 text-left">
      <h2>Koreksi Terima Transfer Barang</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div class="container-fluid">
    {{-- <input type="hidden" name="noUrut" id="input_koreksi_nourut" value="" /> --}}
    <div class="row">
        <input type="hidden" class="form-control" id="input_koreksi_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">No Bukti</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-center" id="input_koreksi_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Tanggal</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                </div>
            </div>
        </div>

        <!-- Tengah -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label" style="margin-top:-5px;"> Gudang Asal</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_gudangasal_nama" type="text" class="form-control text-center" placeholder="Gudang Asal" disabled>
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Keterangan</label>
                <div class="col-sm-8">
                    <textarea  style="width: 100%; resize: none" rows=3 placeholder="" class="form-control" id="input_koreksi_keterangan"  onblur="onChangeHeader('NOTE' , 'input_koreksi_keterangan')"></textarea>
                </div>
            </div>
        </div>
        <!-- Kanan -->
        <div class="col-md-4">
          <div class="mb-2 row">
            <label class="col-sm-5 col-form-label" style="margin-top:-5px;">Gudang Tujuan</label>
              <div class="col-sm-7">
                  <div class="input-group">
                      <input id="input_gudangtujuan_nama" type="text" class="form-control text-center" placeholder="Gudang Tujuan" disabled>
                  </div>
              </div>
            </div>  
          </div>
        </div>

        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
          <table id="koreksiTable" class="table table-bordered table-striped"  >
            <thead class="text-center bg-primary text-white">
            <tr>
              <th colspan="4">Deskripsi Barang</th>
              <th colspan="2">Satuan</th>
              <th colspan="1"></th>
            </tr>
            <tr>
              <th scope="col">Kode Barang</th>
              <th scope="col">Nama Barang</th>
              <th scope="col">Gudang Asal</th>
              <th scope="col">Gudang Tujuan</th>
              <th scope="col">Qty</th>
              <th scope="col">Sat</th>
              <th scope="col">Actions</th>
            </tr>
            </thead>
            <tbody id="koreksiTableData" class="" >
              <tr>
                <td colspan=7 class="text-center">Belum ada data</td>
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
            <label class="form-label fw-bold">Kode Barang</label>
          </div>
          <div class="col-md-4">
            <input id="KoreksiEditKodeBrg" type="text" class="form-control text-center bg-light" disabled>
          </div>
        </div>

        <div class="row">
          <div class="col-md-3" style="margin-top:5px;">
            <label class="form-label fw-bold">Nama Barang</label>
          </div>
          <div class="col-md-8">
            <input id="KoreksiEditNamaBrg" type="text" class="form-control text-center bg-light" disabled>
          </div>
        </div>

        <div class="row">
          <div class="col-md-3" style="margin-top:5px;">
            <label class="form-label fw-bold">Quantity</label>
          </div>
          <div class="col-md-3">
            <input id="KoreksiEditInputQty" type="number" step="0.01" class="form-control text-right" value="0.00">
          </div>
          <div class="col-md-2" style="margin-top:5px;">
            <label class="form-label fw-bold">Satuan</label>
          </div>
          <div class="col-md-3">
            <input id="KoreksiEditInputSat" type="text" class="form-control text-center bg-light" disabled>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-3" style="margin-top:5px;">
            <label class="form-label fw-bold">Gudang Asal</label>
          </div>
          <div class="col-md-8">
            <input id="KoreksiEditGudangAsal" type="text" class="form-control text-center bg-light" disabled>
            <input type="hidden" id="input_gudang_asal">
          </div>
        </div>

        <div class="row">
          <div class="col-md-3" style="margin-top:5px;">
            <label class="form-label fw-bold">Gudang Tujuan</label>
          </div>
          <div class="col-md-8">
            <input id="KoreksiEditGudangTujuan" type="text" class="form-control text-center bg-light" disabled>
            <input type="hidden" id="input_gudang_tujuan">
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
{{-- Start Modal List gudang asal --}}
  <div class="modal fade" id="modalAddListGudangAsal" role="dialog" aria-labelledby="labelGudangAsal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="labelGudangAsal">Pilih Gudang Asal</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="container-fluid px-3 mt-4">
            <div class="row">
              <div class="table-responsive">
                <table id="tabel_add_list_gudangasal" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th>Kode</th>
                      <th>Nama Gudang</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_gudangasal" class="text-left">
                    <tr>
                      <td>-</td>
                      <td>-</td>
                      <td class="text-center">
                        <button class="btn btn-primary btn-sm" type="button"><i class="bi bi-plus"></i></button>
                      </td>
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
{{-- End Modal List gudang asal --}}

{{-- Start Modal List gudang tujuan --}}
  <div class="modal fade" id="modalAddListGudangTujuan" role="dialog" aria-labelledby="labelGudangTujuan" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="labelGudangTujuan">Pilih Gudang Tujuan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="container-fluid px-3 mt-4">
            <div class="row">
              <div class="table-responsive">
                <table id="tabel_add_list_gudangtujuan" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th>Kode</th>
                      <th>Nama Gudang</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_gudangtujuan" class="text-left">
                    <tr>
                      <td>-</td>
                      <td>-</td>
                      <td class="text-center">
                        <button class="btn btn-primary btn-sm" type="button"><i class="bi bi-plus"></i></button>
                      </td>
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
{{-- End Modal List gudang tujuan --}}

<div id="page4" style="display: none; margin-top: -80px" class="mainpage container-fluid" >
  <div class="row">
    <div class="col-8 text-left">
      <h2>Detail Terima Transfer Barang</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
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
                    <input type="text" class="form-control text-center" id="input_detailkoreksi_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Tanggal</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_detailkoreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                </div>
            </div>
        </div>

        <!-- Tengah -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Gudang Asal</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_detailkoreksi_gudangasalnama" type="text" class="form-control text-center" placeholder="Gudang Asal" disabled>
                        <input id="input_detailkoreksi_gudangasal" type="hidden">
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Keterangan</label>
                <div class="col-sm-8">
                  <textarea  style="width: 100%; resize: none" rows=3 placeholder="" class="form-control" id="input_detailkoreksi_keterangan" disabled></textarea>
                </div>
            </div>
        </div>
        <!-- Kanan -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-5 col-form-label">Gudang Tujuan</label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input id="input_detailkoreksi_gudangtujuannama" type="text" class="form-control text-center" placeholder="Gudang Tujuan" disabled>
                        <input id="input_detailkoreksi_gudangtujuan" type="hidden">
                    </div>
                </div>
            </div>
          </div>
        </div>
    <hr/>
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
              <table id="detailKoreksiTable" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                <tr>
                  <th colspan="4">Deskripsi Barang</th>
                  <th colspan="2">Satuan</th>
                </tr>
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Gudang Asal</th>
                  <th scope="col">Gudang Tujuan</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Sat</th>
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

<div id="page5" style="display: none; margin-top: -80px" class="mainpage container-fluid" >
  <div class="row">
    <div class="col-8 text-left">
      <h2>Detail Transfer Barang</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
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
                    <input type="text" class="form-control text-center" id="input_detail_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
        </div>
        <!-- Tengah -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-5 col-form-label" style="margin-top:-5px;">Gudang Asal</label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input id="input_detail_gudangasalnama" type="text" class="form-control text-center" placeholder="Gudang Asal" disabled>
                        <input id="input_detail_gudangasal" type="hidden">
                    </div>
                </div>
            </div>
        </div>
        <!-- Kanan -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-6 col-form-label" style="margin-top:-5px;">Gudang Tujuan</label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input id="input_detail_gudangtujuannama" type="text" class="form-control text-center" placeholder="Gudang Tujuan" disabled>
                        <input id="input_detail_gudangtujuan" type="hidden">
                    </div>
                </div>
            </div>
          </div>
        </div>
    <hr/>
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
              <table id="detailTransferBarangTable" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Sat</th>
                  <th scope="col">Qty</th>
                </tr>
                </thead>
                <tbody id="detailTransferBarangTableData" class="" >
                  <tr>
                    <td colspan=4 class="text-center">Belum ada data</td>
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
      'order': [[1, 'asc']],
      "columnDefs": [
        { "type": "date", "targets": [2] },
        {  "className": "text-center", "targets": [0], "orderable" :false },
      ]});

  $("#tabel2").DataTable({
    "lengthChange": false,
      "paging": false ,
      "autoWidth": false,
      'order': [[1, 'asc']],
      "columnDefs": [
        { "type": "date", "targets": [2] },
        {  "className": "text-center", "targets": [0], "orderable" :false },
      ]});
    
  // $("#tabel_oto").DataTable({
  //   "lengthChange": false,
  //     "paging": false ,
  //     "autoWidth": false,
  //   });


  //   formAddListItem
});

// function buttonKoreksiListGudangAsal () {
//   console.log('buttonKoreksiListGudangAsal');
//   $('#tabel_add_list_gudangasal').DataTable().destroy();

//   $.ajax({
//     url: "{{ url('penyerahansamplelistgudangasal') }}",
//     type: "get",
//     async: false,
//     success: function(res) {
//       console.log(res);

//       let rowTable = ``;
//       res.forEach((item, i) => {
//         rowTable += `
//           <tr>
//             <td>${item.KodeGdg}</td>
//             <td>${item.NamaGdg}</td>
//             <td class="text-center">
//               <button class="btn btn-primary btn-sm" type="button"
//                 onclick="buttonAddPickGudangAsal('${item.NamaGdg}', '${item.KodeGdg}')">
//                 <i class="bi bi-plus"></i>
//               </button>
//             </td>
//           </tr>`;
//       });

//       if (!res.length) {
//         rowTable = `<tr><td class="text-center" colspan="3">Tidak ada data</td></tr>`;
//       }

//       document.getElementById("tabel_data_add_list_gudangasal").innerHTML = rowTable;
//       $("#tabel_add_list_gudangasal").DataTable({
//         "lengthChange": false,
//         "paging": false,
//       });

//       $('#modalAddListGudangAsal').modal('show');
//     },
//     error: function(err) {
//       console.log(err);
//       alertify.warning('Terjadi kesalahan saat mengambil data gudang.');
//     }
//   });
// }

// function buttonAddPickGudangAsal (nama, kode) {
//   $('#KoreksiEditGudangAsal').val(nama);
//   $('#input_gudang_asal').val(kode);
//   $('#modalAddListGudangAsal').modal('hide');
// }

// function buttonKoreksiListGudangTujuan () {
//   console.log('buttonKoreksiListGudangTujuan');
//   $('#tabel_add_list_gudangtujuan').DataTable().destroy();

//   $.ajax({
//     url: "{{ url('penyerahansamplelistgudangtujuan') }}",
//     type: "get",
//     async: false,
//     success: function(res) {
//       console.log(res);

//       let rowTable = ``;
//       res.forEach((item, i) => {
//         rowTable += `
//           <tr>
//             <td>${item.KodeGdg}</td>
//             <td>${item.NamaGdg}</td>
//             <td class="text-center">
//               <button class="btn btn-primary btn-sm" type="button"
//                 onclick="buttonAddPickGudangTujuan('${item.NamaGdg}', '${item.KodeGdg}')">
//                 <i class="bi bi-plus"></i>
//               </button>
//             </td>
//           </tr>`;
//       });

//       if (!res.length) {
//         rowTable = `<tr><td class="text-center" colspan="3">Tidak ada data</td></tr>`;
//       }

//       document.getElementById("tabel_data_add_list_gudangtujuan").innerHTML = rowTable;
//       $("#tabel_add_list_gudangtujuan").DataTable({
//         "lengthChange": false,
//         "paging": false,
//       });

//       $('#modalAddListGudangTujuan').modal('show');
//     },
//     error: function(err) {
//       console.log(err);
//       alertify.warning('Terjadi kesalahan saat mengambil data gudang.');
//     }
//   });
// }

// function buttonAddPickGudangTujuan (nama, kode) {
//   $('#KoreksiEditGudangAsal').val(nama);
//   $('#input_gudang_tujuan').val(kode);
//   $('#modalAddListGudangTujuan').modal('hide');
// }

// function buttonAddListBatal() {
//   $('#modalAddListGudangAsal').modal('hide');
//   $('#modalAddListGudangTujuan').modal('hide');
// }


function onChangeHeader (field , idvalue) {
  let _token  = $("#_token").val()
  console.log(field, idvalue)
  let onChangeValue  = $(`#${idvalue}`).val()
  let nobukti  = $(`#input_koreksi_nobukti`).val()
  console.log(onChangeValue , nobukti)


  console.log({
    _token : _token,
    field,
    nobukti,
    value: onChangeValue

  })

  $.ajax({
      url: "{!! url('terimatransferbarangonchangeheader') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        field,
        nobukti,
        value: onChangeValue

      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          alertify.warning(`${field} sudah diupdate`)
        }


      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

}


function buttonKoreksiEditItem (i) {
  let barang = dataTableKoreksi[i];
  barangKoreksiEdit = barang;

  document.getElementById("KoreksiEditKodeBrg").value = barang.KODEBRG;
  document.getElementById("KoreksiEditNamaBrg").value = barang.NAMABRG;

  document.getElementById("KoreksiEditInputQty").value = barang.QNT ? parseFloat(barang.QNT).toFixed(2) : "0.00";
  document.getElementById("KoreksiEditInputSat").value = barang.NOSAT == 1 ? barang.SAT_1 : barang.SAT_2;

  document.getElementById("KoreksiEditGudangAsal").value = barang.GdgAsal;
  document.getElementById("input_gudang_asal").value = barang.GdgAsal;

  document.getElementById("KoreksiEditGudangTujuan").value = barang.GdgTujuan;
  document.getElementById("input_gudang_tujuan").value = barang.GdgTujuan;

  $('#formKoreksiEdit').show();
} 

function refreshDataTableKoreksi (nobukti) {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('terimatransferbaranggetdetailpenerimaan') !!}",
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
        rowTable += `<tr>
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.GdgAsal}</td>
          <td>${item.GdgTujuan}</td>
          <td class="text-end">${item.QNT ? parseFloat(item.QNT).toLocaleString() : '0.00'}</td>
          <td class="text-center">${item.SAT_1}</td>
          <td class="text-center">
            <button class="btn btn-success btn-sm" onclick="buttonKoreksiEditItem(${i})">
              <i class="bi bi-pen"></i>
            </button>
            <button class="btn btn-danger btn-sm" onclick="buttonKoreksiDeleteItem(${i})">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>`;
      });

      document.getElementById("koreksiTableData").innerHTML = rowTable;

      let header = res[0];
      let tanggal = "";
      if (header.TANGGAL) {
        tanggal = header.TANGGAL.split(" ")[0];
      }
      $("#input_koreksi_tanggal").val(tanggal);
      $("#input_koreksi_nobukti").val(header.NOBUKTI);
      $("#input_koreksi_catatan").val(header.KETERANGAN);
      $("#input_koreksi_gudangtujuan_nama").val(header.GdgTujuan);
      $("#input_gudangasal_nama").val(header.GdgAsal);

      buttonKoreksiItemBatal();
    },
    error: function (err) {
      console.error(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}


function buttonKoreksiDeleteItem(i) {
  console.log(i);
  let barang = dataTableKoreksi[i];

  let akses = $("#akses_ishapus").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + barang.NAMABRG + ' ?',
    function () {
      let _token = $("#_token").val();
      let choice = "D"; 
      let qnt = 0;
      let qnt1 = 0;
      let qnt2 = 0;

      // Data dari barang
      let nobukti = barang.NOBUKTI;
      let urut = barang.URUT;
      let kodebrg = barang.KODEBRG;
      let namabrg = barang.NAMABRG;
      let kodegdgasal = barang.GDGASAL;
      let kodegdgtujuan = barang.GDGTUJUAN;
      let nosat = barang.NOSAT;
      let isi = parseFloat(barang.ISI);
      let sat1 = barang.SAT_1;
      let sat2 = barang.SAT_2;

      // Dari DBTRANSFERDET
      let noTransfer = barang.NoTransfer;
      let urutTransfer = barang.UrutTransfer;

      let keterangan = $("#input_koreksi_keterangan").val();

      // log data
      console.log({
        choice,
        qnt,
        qnt1,
        qnt2,
        namabrg,
        nobukti,
        urut,
        nosat,
        isi,
        sat1,
        sat2,
        kodebrg,
        kodegdgasal,
        kodegdgtujuan,
        note: keterangan,
        noTransfer,
        urutTransfer
      });

      $.ajax({
        url: "{!! url('terimatransferbarangspkoreksi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          choice,
          qnt,
          qnt1,
          qnt2,
          namabrg,
          nobukti,
          urut,
          nosat,
          isi,
          sat1,
          sat2,
          kodebrg,
          kodegdgasal,
          kodegdgtujuan,
          note: keterangan,
          noTransfer,
          urutTransfer
        },
        success: function (res) {
          if (res == 1) {
            refreshDataTableKoreksi(nobukti);
            loadAll();
            alertify.success('Item telah dihapus');
          } else {
            alertify.warning("Gagal menghapus item");
          }
        },
        error: function (err) {
          console.log(err);
          alertify.warning('Terjadi kesalahan, silakan refresh browser');
        }
      });
    },
    function () {
      console.log('Hapus dibatalkan');
    }
  );
}


// function buttonOtorisasi (nobukti, isOtorisasi) {
//   let akses = $("#akses_isotorisasi1").val();
//   if (!Number(akses)) {
//     alertify.warning('No access');
//     return;
//   }

//   if (Number(isOtorisasi) > 0) {
//     alertify.warning('Sudah diotorisasi');
//     return;
//   }

//   let _token = $("#_token").val();

//   $.ajax({
//     url: "{!! url('terimatransferbarangspotorisasi') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token,
//       nobukti,
//       otorisasi: 1
//     },
//     success: function (res) {
//       if (res > 0) {
//         alertify.success('Berhasil otorisasi');
//         loadAll();
//       } else {
//         alertify.warning('Gagal otorisasi');
//       }
//     },
//     error: function (err) {
//       console.log(err);
//       alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
//     }
//   });
// }

// function buttonBatalOtorisasi (nobukti) {
//   let akses = $("#akses_isotorisasi1").val();
//   if (!Number(akses)) {
//     alertify.warning('No access');
//     return;
//   }

//   alertify.confirm('Batal Otorisasi', 'Batalkan otorisasi ' + nobukti + ' ?',
//     function () {
//       let _token = $("#_token").val();

//       $.ajax({
//         url: "{!! url('terimatransferbarangspbatalotorisasi') !!}",
//         type: "post",
//         async: false,
//         data: {
//           _token,
//           nobukti
//         },
//         success: function (res) {
//           alertify.success('Berhasil batal otorisasi');
//           loadAll();
//         },
//         error: function (err) {
//           console.error(err);
//           alertify.warning('Terjadi kesalahan, silakan refresh browser');
//         }
//       });
//     },
//     function () {
//       console.log('Batal otorisasi dibatalkan');
//     }
//   );
// }

function submitKoreksiEdit () {
  let _token = $("#_token").val();
  let barang = barangKoreksiEdit;
  let choice = "U";

  let qntInput = parseFloat($("#KoreksiEditInputQty").val());
  if (isNaN(qntInput)) {
    alertify.warning("Qty tidak valid");
    return;
  }

  if (qntInput < 0) {
    alertify.warning("Qty tidak boleh kurang dari 0");
    return;
  }

  let qntAwal = parseFloat(barang.QNT);
  if (qntInput > qntAwal) {
    alertify.warning("Qty tidak boleh lebih besar dari qty awal (" + qntAwal + ")");
    return;
  }

  // Data item
  let nobukti = barang.NOBUKTI;
  let urut = barang.URUT;
  let kodebrg = barang.KODEBRG;
  let namabrg = barang.NAMABRG;

  let kodegdgasal = $("#input_gudang_asal").val();
  let kodegdgtujuan = $("#input_gudang_tujuan").val();

  let nosat = barang.NOSAT;
  let isi = parseFloat(barang.ISI) || 1;

  let sat1 = barang.SAT_1;
  let sat2 = barang.SAT_2;

  // Hitung qnt1 & qnt2 sesuai nosat
  let qnt1 = 0;
  let qnt2 = 0;

  if (nosat == 1) {
    qnt1 = qntInput;
    qnt2 = qntInput * isi;
  } else {
    qnt1 = qntInput / isi;
    qnt2 = qntInput;
  }

  // dari DBTRANSFERDET
  let noTransfer = barang.NoTransfer;
  let urutTransfer = barang.UrutTransfer;

  let keterangan = $("#input_koreksi_keterangan").val();

  $.ajax({
    url: "{!! url('terimatransferbarangspkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      choice,
      qnt: qntInput,
      qnt1,
      qnt2,
      namabrg,
      nobukti,
      urut,
      nosat,
      isi,
      sat1,
      sat2,
      kodebrg,
      kodegdgasal,
      kodegdgtujuan,
      note: keterangan,
      noTransfer,
      urutTransfer
    },
    success: function (res) {
      if (res == 1) {
        refreshDataTableKoreksi(nobukti);
        loadAll();
        alertify.success('Item telah dikoreksi');
      } else {
        alertify.warning("Koreksi gagal disimpan");
      }
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
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
    url: "{!! url('terimatransferbaranggetdetailpenerimaan') !!}",
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
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.GdgAsal}</td>
          <td>${item.GdgTujuan}</td>
          <td>${parseFloat(item.QNT).toLocaleString()}</td>
          <td>${item.SAT_1}</td>
          <td class="text-center">
            <button class="btn btn-success btn-sm" onclick="buttonKoreksiEditItem(${i})"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm" onclick="buttonKoreksiDeleteItem(${i})"><i class="bi bi-trash"></i></button>
          </td>
        </tr>`;
      });

      $("#koreksiTableData").html(rowTable);

      // Isi Form Header
      $("#input_koreksi_nobukti").val(data.NOBUKTI);
      $("#input_gudangasal_nama").val(data.GdgAsal);
      $("#input_gudangtujuan_nama").val(data.GdgTujuan);
      $("#input_koreksi_keterangan").val(data.NOTE);

      const tanggal = data.TANGGAL ? data.TANGGAL.split(" ")[0] : "";
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
    url: "{!! url('terimatransferbaranggetdetailpenerimaan') !!}",
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
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.GdgAsal}</td>
          <td>${item.GdgTujuan}</td>
          <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
          <td>${item.SAT_1}</td>
        </tr>`;
      });

      $("#detailKoreksiTableData").html(rowTable);

      // Isi Header Detail
      const data = res[0];

      $("#input_detailkoreksi_nobukti").val(data.NOBUKTI);
      $("#input_detailkoreksi_gudangasalnama").val(data.GdgAsal);
      $("#input_detailkoreksi_gudangtujuannama").val(data.GdgTujuan);
      $("#input_detailkoreksi_keterangan").val(data.KETERANGAN);

      const tanggal = data.TANGGAL ? data.TANGGAL.split(" ")[0] : "";
      $("#input_detailkoreksi_tanggal").val(tanggal);

      $('.mainpage').hide();
      $('#page4').show();
    },
    error: function (err) {
      console.error('Error response:', err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

function buttonDetailTransferBarang (nobukti) {
  console.log('buttonDetailTransferBarang', nobukti);

  $('.showhideitem').hide();

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('terimatransferbaranggetdetailtransferbarang') !!}",
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
        let kodeBrg = item.KODEBRG || '';
        let nobukti = item.NOBUKTI || '';
        let namaBrg = item.NAMABRG || '';
        let satuan = item.Satx || item.SAT_1 || '';
        let qnt = item.Qntx || item.QNT || 0;
        rowTable += `<tr>
          <td>${kodeBrg}</td>
          <td>${namaBrg}</td>
          <td>${satuan}</td>
          <td class="text-right">${qnt}</td>
        </tr>`;
      });

      $("#detailTransferBarangTableData").html(rowTable);

      // Isi Header Detail
      const data = res[0];

      $("#input_detail_nobukti").val(data.NOBUKTI);
      $("#input_detail_gudangasalnama").val(data.NamagdgAsal || '');
      $("#input_detail_gudangtujuannama").val(data.NamagdgTujuan || '');

      $('.mainpage').hide();
      $('#page5').show();
    },
    error: function (err) {
      console.error('Error response:', err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

function loadAll () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('terimatransferbarangloadall') !!}",
    type: "get",
    async: false,
    data: {},
    success: function (res) {
      $('#tabel').DataTable().destroy();
      let rowTable = '';

      res.tempOutstanding.forEach((item) => {
        rowTable += `
          <tr>
            <td class="text-center">
              <button class="btn btn-warning btn-sm" type="button"  onclick="buttonDetailTransferBarang('${item.NOBUKTI}')">
                <i class="bi bi-info"></i>
              </button>
              <button class="btn btn-success btn-sm" type="button" onclick="buttonAdd('${item.NOBUKTI}')">
                <i class="bi bi-plus"></i>
              </button>
            </td>
            <td>${item.NOBUKTI}</td>
            <td>${ item.NamagdgAsal }</td>
            <td>${ item.NamagdgTujuan }</td>
            <td>${item.TANGGAL ? formatDate(item.TANGGAL, '/') : ''}</td>
            <td>${item.NOTE || ''}</td>
          </tr>`;
      });

      document.getElementById("tabel_data").innerHTML = rowTable;
      $("#tabel").DataTable({
        'lengthChange': false,
        'paging': false,
        'order': [[1, 'asc']],
        "columnDefs": [
          { "type": "date", "targets": [2] },
          {  "className": "text-center", "targets": [0], "orderable" :false },
        ]});

      $('#tabel2').DataTable().destroy();
      let rowTable2 = '';

      res.tempPenerimaan.forEach((item) => {
        rowTable2 += `
          <tr>
            <td class="text-center">
              <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('${item.NOBUKTI}')">
                <i class="bi bi-info"></i>
              </button>
              <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item.NOBUKTI}' , 'edit')"><i class="bi bi-pen"></i></button>
            </td>
            <td>${item.NOBUKTI}</td>
            <td>${item.TANGGAL ? formatDate(item.TANGGAL, '/') : ''}</td>
            <td>${item.NOTE}</td>
          </tr>`;
      });

      document.getElementById("tabel2_data").innerHTML = rowTable2;
      $("#tabel2").DataTable({
        'lengthChange': false,
        'paging': false,
        'order': [[1, 'asc']],
        "columnDefs": [
          { "type": "date", "targets": [2] },
          {  "className": "text-center", "targets": [0], "orderable" :false },
        ]});
      }
    });
  }

function buttonAdd (nobukti) {
  console.log('buttonAdd', nobukti);

  let akses = $("#akses_istambah").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  setNewNoBukti();

  let _token = $("#_token").val();
  let tanggal = $("#input_add_tanggal").val(); 
  if (!tanggal) {
    alertify.warning("Tanggal harus diisi.");
    return;
  }

  $.ajax({
    url: "{!! url('terimatransferbaranggetdetail') !!}",
    type: "POST",
    async: false,
    data: {
      _token,
      nobukti,
      tanggal 
    },
    success: function (res) {
      console.log('res', res);

      if (!Array.isArray(res) || res.length === 0) {
        alertify.warning("Data tidak ditemukan");
        return;
      }

      dataTableAdd = res;
      let rowTable = "";

      res.forEach((item, i) => {
        let kodeBrg   = item.KODEBRG ?? '';
        let namaBrg   = item.NAMABRG ?? '';
        let nobukti   = item.NOBUKTI ?? '';
        let satuan    = item.SAT_1;
        let qnt       = item.Qntx !== undefined ? parseFloat(item.Qntx).toFixed(2) : '0.00';

        rowTable += `
          <tr>
            <td class="text-center">
              <input type="checkbox" class="add_checkbox" data-index="${i}" style="transform: scale(1.5); margin: 5px;">
            </td>
            <td>${kodeBrg}</td>
            <td>${nobukti}</td>
            <td>${namaBrg}</td>
            <td>${satuan}</td>
            <td class="text-right">${qnt}</td>
            <td class="text-center">
              <input 
                class="input_add_qnt text-right" data-index="${i}" type="number" min="0" value="" placeholder="0">
            </td>
          </tr>`;
      });

      document.getElementById("addTableData").innerHTML = rowTable;

      $('.mainpage').hide();
      $('#page2').show();
    },
    error: function (err) {
      console.error("AJAX Error", err);
      alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
    }
  });
}

function submitAdd () {
  const checkDate = new Date($("#input_add_tanggal").val());
  const periode_bulan = parseInt(document.getElementById("periode_bulan").value);
  const periode_tahun = parseInt(document.getElementById("periode_tahun").value);

  if (checkDate.getFullYear() !== periode_tahun || (checkDate.getMonth() + 1) !== periode_bulan) {
    alertify.warning("Tanggal tidak sesuai periode");
    return;
  }

  const _token = $("#_token").val();
  const tempData = [];
  let checkInvalid = false;

  const nobukti = $("#input_add_nobukti").val();
  const nourut = $("#input_add_nourut").val();
  const tanggal = $("#input_add_tanggal").val();

  $(".add_checkbox:checked").each(function() {
    const i = $(this).data("index");
    const item = dataTableAdd[i];
    const inputQty = parseFloat($(`.input_add_qnt[data-index='${i}']`).val() || 0);
    const maxQty   = parseFloat(item.Qntx || 0);

    if (inputQty < 0) {
      alertify.warning(`Qty tidak boleh negatif (row ${i+1})`);
      checkInvalid = true;
      return false; 
    }

    if (inputQty > maxQty) {
      alertify.warning(`Qty tidak boleh lebih dari Qty awal (${maxQty})`);
      checkInvalid = true;
      return false;
    }

    tempData.push({
      NOBUKTI: item.NOBUKTI, 
      URUT: item.URUT,      
      KODEBRG: item.KODEBRG,   
      inputQnt: inputQty,
      checked: true
    });
  });

  if (checkInvalid) {
    return;
  }

  if (!tempData.length) {
    alertify.warning("Tidak ada item yang dipilih");
    return;
  }

  console.log("submitAdd payload", tempData);

  $.ajax({
    url: "{!! url('terimatransferbarangspadd') !!}",
    type: "post",
    data: {
      _token,
      tempData,
      tanggal,
      nobukti,
      nourut
    },
    success: function(res) {
      console.log('submitAdd response', res);

      if (res.success) {
        alertify.success(res.message);
        loadAll();
        setTimeout(() => {
          $('.mainpage').hide();
          $('#page3').show();  
          buttonKoreksi(nobukti, 'edit');
        }, 100);
      } else {
        alertify.error(res.message || 'Gagal menyimpan data');
      }
    },
    error: function(xhr) {
      console.error(xhr.responseText);
      alertify.error("Terjadi error saat simpan data");
    }
  });
}

// function submitAdd () {
//   const checkDate = new Date($("#input_add_tanggal").val());
//   const periode_bulan = parseInt(document.getElementById("periode_bulan").value);
//   const periode_tahun = parseInt(document.getElementById("periode_tahun").value);

//   if (checkDate.getFullYear() !== periode_tahun || (checkDate.getMonth() + 1) !== periode_bulan) {
//     alertify.warning("Tanggal tidak sesuai periode");
//     return;
//   }

//   const _token = $("#_token").val();
//   const tempData = [];
//   let checkMinus = false;

//   const nobukti = $("#input_add_nobukti").val();
//   const nourut = $("#input_add_nourut").val();
//   const tanggal = $("#input_add_tanggal").val();

//   dataTableAdd.forEach((item, i) => {
//     const checkbox = document.getElementById(`add_checkbox${i}`);
//     if (checkbox && checkbox.checked) {
//       const inputQty = $(`#input_add_qnt${i}`).val();

//       if (inputQty < 0) {
//         checkMinus = true;
//       }

//       const itemData = {
//         ...item,
//         inputQnt: inputQty
//       };

//       tempData.push(itemData);
//     }
//   });

//   if (checkMinus) {
//     alertify.warning("Qty tidak boleh negatif");
//     return;
//   }

//   if (!tempData.length) {
//     alertify.warning("Tidak ada item yang dipilih");
//     return;
//   }

//   console.log("submitAdd payload", tempData);

//   $.ajax({
//     url: "{!! url('terimatransferbarangspadd') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token,
//       tempData,
//       tanggal,
//       nobukti,
//       nourut
//     },
//     success: function(res) {
//     console.log('submitAdd response', res);

//     if (res.success) {
//       alertify.success(res.message);
//       loadAll();
//       buttonCloseForm();
//     } else {
//       alertify.error(res.message || 'Gagal menyimpan data');
//     }
//   }
//     // success: function(res) {
//     // console.log('submitAdd response', res);
//     // if (res == 1) {
//     //     alertify.success('TRT telah ditambah');
//     //     loadAll();
//     //     buttonCloseForm();
//     //   // }
//     //   // if (res == 1) {
//     //   //   alertify.success('TRT telah ditambah');
//     //   //   loadAll();
//     //   //   setTimeout(() => {
//     //   //     $('.mainpage').hide();
//     //   //     $('#page3').show();  
//     //   //     buttonKoreksi(nobukti, 'edit');
//     //   //   }, 100);
//     //   } else if (res == 2) {
//     //     setNewNoBukti();
//     //     alertify.warning('Nobukti telah direfresh silahkan submit ulang');
//     //   } else {
//     //     alertify.error('Respon tidak diketahui dari server');
//     //   }
//     // }

//   });
// }


function setNewNoBukti () {
  $.ajax({
    url: "{!! url('terimatransferbarangspnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut
    }})
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

{{-- script buat hover so belum otorisasi dan sudah otorisasi --}}
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
{{-- script buat hover so belum otorisasi dan sudah otorisasi --}}


@endsection
