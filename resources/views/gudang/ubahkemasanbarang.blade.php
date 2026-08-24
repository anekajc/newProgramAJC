@extends('gudang.newmaster')
@section('buttons')

@endsection

@section('css')
@endsection

@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="pageHome" class="container-fluid">
    	<div class="">
    	  <div class="row">
    	    <div class="col-6 text-left">
    	      <h2 style="margin-top:-85px;">Ubah Kemasan Barang</h2>
    	    </div>
    	    <div class="col-6 text-right">
    	      <button type="button" class="btn btn-primary btn-lg button-action" style="margin-top: -150px;" onclick="buttonAdd()">Add KMBJ</button>
    	    </div>
    	  </div>
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
          <div class="card-header" style="margin-top:-55px;">
            <div class="row">
              <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
                <a class="nav-item nav-link active buttonnav-active" id="nav-home-tab" href="#tabhome" role="tab" aria-controls="nav-home" aria-selected="true" onclick="doSetActiveNavTab('home')">
                  Ubah Kemasan Barang
                </a>
                <a class="nav-item nav-link buttonnav-inactive" id="nav-listsdhoto-tab" href="#tablistsdhoto" role="tab" aria-controls="nav-listsdhoto" aria-selected="false" onclick="doSetActiveNavTab('listsdhoto')">
                  Sudah Otorisasi
                </a>
              </div>
            </div>
          </div>

          <div class="card-body" style="padding:0;">
            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="tabhome" role="tabpanel" aria-labelledby="home-tab">
                <div class="row">
                  <div class="col-md-12">
                    <div class="container-fluid col-sm-12 customTable" style="padding:0; margin:0; width:100%;">
                      <table id="tabelhome" class="table table-bordered table-hover table-striped table-responsive-lg">
                        <thead class="text-center bg-primary text-white">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Actions</th>
                            <th style="padding: 4px 12px;" scope="col">Group No. Bukti</th>
                            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                          </tr>
                        </thead>
                        <tbody id="tabelhome_data" class="text-left">
                          @if (count($listKMBJ) > 0)
                            @for ($i = 0; $i < count($listKMBJ); $i++)
                              <tr>
                                <td class="text-center">
                                  <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('{{ $listKMBJ[$i]->Nobukti }}')">
                                    <i class="bi bi-info"></i>
                                  </button>
                                  <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit('{{ $listKMBJ[$i]->Nobukti }}')">
                                    <i class="bi bi-pencil-fill"></i>
                                  </button>
                                  <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi('{{ $listKMBJ[$i]->Nobukti }}')">
                                    <i class="bi bi-key-fill"></i>
                                  </button>
                                </td>

                                <td>{{ $listKMBJ[$i]->GroupNobukti }}</td>
                                <td>{!! date("Y/m/d", strtotime($listKMBJ[$i]->Tanggal)) !!}</td>

                                {{-- <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td> --}}
                              </tr>
                            @endfor
                          @else
                            <tr>
                              <td colspan="3" class="text-center text-muted">Tidak ada transaksi</td>
                              <td style="display: none;"></td>
                              <td style="display: none;"></td>
                              {{-- <td style="display: none;"></td> --}}
                            </tr>
                          @endif
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="tab-pane fade" id="tablistsdhoto" role="tabpanel" aria-labelledby="listsdhoto-tab">
                <div class="row">
                  <div class="col-md-12">
                    <div class="container-fluid col-sm-12 customTable" style="padding:0; margin:0; width:100%;">
                      <table id="tabellistsdhoto" class="table table-bordered table-hover table-striped table-responsive-lg">
                        <thead class="text-center bg-primary text-white">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Actions</th>
                            <th style="padding: 4px 12px;" scope="col">Group No. Bukti</th>
                            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                            {{-- <th style="padding: 4px 12px;" scope="col">Oto</th> --}}
                            <th style="padding: 4px 12px;" scope="col">User Oto</th>
                            <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
                          </tr>
                        </thead>
                        <tbody id="tabellistsdhoto_data" class="text-left">
                          @if (count($listSdhOto) > 0)
                            @for ($i = 0; $i < count($listSdhOto); $i++)
                              <tr>
                                <td class="text-center">
                                  <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('{{ $listSdhOto[$i]->Nobukti }}')">
                                    <i class="bi bi-info"></i>
                                  </button>
                                  <button class="btn btn-danger btn-sm" type="button" title="Otorisasi" onclick="buttonBatalOtorisasi('{{ $listSdhOto[$i]->Nobukti }}')">
                                    <i class="bi bi-key-fill"></i>
                                  </button>
				  <button style="" class="btn btn-primary btn-sm" type="button"   onclick="submitPrint('{{$listSdhOto[$i]->Nobukti}}')" ><i class="bi bi-printer"></i></button>
                                </td>

                                <td>{{ $listSdhOto[$i]->GroupNobukti }}</td>
                                <td>{!! date("Y/m/d", strtotime($listSdhOto[$i]->Tanggal)) !!}</td>

                                {{-- <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td> --}}
                                <td>{{ $listSdhOto[$i]->OtoUser1 }}</td>
                                <td>{!! date("Y/m/d", strtotime($listSdhOto[$i]->TglOto1)) !!}</td>
                              </tr>
                            @endfor
                          @else
                            <tr>
                              <td colspan="5" class="text-center text-muted">Tidak ada transaksi</td>
                              <td style="display: none;"></td>
                              <td style="display: none;"></td>
                              <td style="display: none;"></td>
                              <td style="display: none;"></td>
                              {{-- <td style="display: none;"></td> --}}
                            </tr>
                          @endif
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

<div id="pageForm" class="container-fluid" style="display: none" >
      <div class="row">
        <div class="col-6 text-left">
          <h2 style="margin-top: -80px;">Form Ubah Kemasan Barang</h2>
        </div>
        <div class="col-6 text-right">
          <button type="button" class="btn btn-danger btn-lg button-action" style="margin-top: -120px;" onclick="buttonCloseForm()">Close</button>
        </div>
      </div>

      <div id="modalBodyAddMain" class="">
        <div class="modal-body" style="margin-top:-60px;">
          <div class="row">

            <input type="hidden" class="form-control" id="input_nourut">

            <div class="col-md-12">
              <div class="row">
                <div class="col-md-1">
                  <div class="form-group">
                    <label>No Bukti</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <input type="text" class="form-control text-left" id="input_nobukti" placeholder="" disabled>
                  </div>
                </div>

                <div class="col-md-1" hidden>
                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-md-2" hidden>
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="input_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                  </div>
                </div>

                <div class="col-1">
                  <div class="form-group">
                    <label>Gudang</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="input-group form-group">
                    <input type="text" class="form-control" id="input_gudang"  disabled>
                    <button id="buttonbrowse_gudang" onclick="doBrowseMaster('Gudang', '{!! $gudang !!}')" class="btn btn-primary btn-sm text-right lockableHeader lockableModeDetail">
                      <i class="bi bi-plus"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <div class="row">
                <div class="col-md-1">
                  <div class="form-group">
                    <label>Keterangan</label>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-group">
                    <textarea style="width: 100%; resize: none" rows="2" placeholder="Keterangan" class="form-control text-left lockableHeader lockableModeDetail" id="input_keterangan" onblur="onChangeKeterangan()"></textarea>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="showhidemodalbodyaddmain container-fluid" id="modalBodyAddMainItems">
        <div class="container-fluid" style="overflow:auto;">
          <div class="row">
            <table id="tabelitem" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead id="tabelitem_header" class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Deskripsi</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>
                  <th style="padding: 4px 12px;" scope="col">Qty Asal</th>
                  <th style="padding: 4px 12px;" scope="col">Qty Jadi</th>
                  <th style="padding: 4px 12px;" scope="col">HPP</th>
                  <th style="padding: 4px 12px;" scope="col">Rp Kredit</th>
                  <th style="padding: 4px 12px;" scope="col">Rp Debet</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabelitem_data" class="text-left lockableModeDetail" >
              </tbody>
            </table>
          </div>
        </div>

        <div class="row ">
          <div class="col-md-12 mt-2 text-right">
            <button type="button" class="btn btn-primary btn-lg button-action hideableModeDetail" onclick="buttonItemAdd()"><b>+ Tambah Item</b></button>
          </div>
        </div>

        <div id="formItem" class="container-fluid showhide">
          <hr/>
          <div class="row">
            <div class="col-4">
              <h4 id="formItem_labelAdd" style="margin-left:-35px;">Add Item</h4>
              <h4 id="formItem_labelEdit" style="margin-left:-35px;">Edit Item</h4>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="row">

                <!-- START OF ITEM Kode Barang & Nama Barang -->
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-12">

                      <input type="text" class="form-control" id="inputitem_urut" hidden>

                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>Kode Barang</label>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="input-group form-group">
                            <input type="text" class="form-control lockableItemModeEdit" id="inputitem_kodebrg" onkeypress="doBrowseDirectFilter('Barang', '{!! $barang !!}', 'inputitem_kodebrg')">
                            <button onclick="doBrowseMaster('Barang', '{!! $barang !!}')" class="btn btn-primary btn-sm text-right lockableItemModeEdit" id="btnitem_kodebrg">
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>Nama Barang</label>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="inputitem_namabrg" disabled>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- END OF ITEM Kode Barang & Nama Barang -->


                <!-- START OF ITEM Qty Asal & Qty Jadi -->
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-12">

                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>Qty Asal</label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="input-group form-group">
                            <input type="number" class="form-control text-right" id="inputitem_qtyasal" onblur="onChangeQtyAsal()">
                            <input type="number" class="form-control" id="inputitem_qtylama" hidden>
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="form-group">
                            <label>Satuan</label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="input-group form-group">
                            <select id="inputitem_satuanasal" class="form-control form-select-lg mb-3 text-center" aria-label=".form-select-lg example" disabled>
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-3" style="margin-top:-10px">
                          <div class="form-group">
                            <label>Qty Jadi</label>
                          </div>
                        </div>
                        <div class="col-md-3" style="margin-top:-10px">
                          <div class="input-group form-group">
                            <input type="number" class="form-control text-right" id="inputitem_qtyjadi" onblur="onChangeQtyJadi()">
                          </div>
                        </div>

                        <div class="col-md-3" style="margin-top:-10px">
                          <div class="form-group">
                            <label>Satuan</label>
                          </div>
                        </div>
                        <div class="col-md-3" style="margin-top:-10px">
                          <div class="input-group form-group">
                            <select id="inputitem_satuanjadi" class="form-control form-select-lg mb-3 text-center" aria-label=".form-select-lg example" disabled>
                            </select>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- END OF ITEM Qty Asal & Qty Jadi -->


                <!-- START OF ITEM HPP & Biaya -->
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-12">

                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>HPP</label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="input-group form-group">
                            <input type="number" step="any" class="form-control text-right" id="inputitem_hpp">
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>Biaya</label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="input-group form-group">
                            <input type="number" step="any" class="form-control text-right" id="inputitem_biaya">
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- END OF ITEM HPP & Biaya -->

              </div>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-danger btn-lg button-action" onclick="closeFormItem()" class="btn btn-secondary">Batal</button>
              <button type="button" id="buttonSubmitItem" class="btn btn-primary btn-lg button-action" onclick="submitItem()" class="btn btn-secondary">Submit</button>
            </div>

          </div>
        </div>
      </div>
</div>

@include('gudang.modalbrowsemaster')

@endsection

@section('js')
<script src="{!! URL::asset('js/ajc-func-core.js') !!}"></script>
<script src="{!! URL::asset('js/ajc-browsemaster.js') !!}"></script>
<script>
  const BASE_URL = "{{ url('/') }}";

  const g_tipeformNone = "", g_tipeformAdd = "add", g_tipeformEdit = "edit", g_tipeformDetail = "detail";
  var   gtipeform = g_tipeformNone;

  const g_tipeformitemNone = "", g_tipeformitemAdd = "add", g_tipeformitemEdit = "edit",
        g_tipeformitemDelete = "delete";
  var   gtipeformitem = g_tipeformitemNone;

  const g_modalNone = "";
  var   gmodemodal = g_modalNone;

  var dataItem = [];
  var dataBrowse = {};

  $(document).ready(function(){
    $("#tabelhome").DataTable({
      "lengthChange": false,
      "paging": false ,
    });

    $("#tabellistsdhoto").DataTable({
      "lengthChange": false,
      "paging": false ,
    });
  });

  function loadAll() {
    let listKMBJ = [], listSdhOto = [];
    let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('kmbjloadall') !!}",
      type: "get",
      async: false,
      data: {
      },
      success: function(res) {
        listKMBJ = res.listKMBJ;
        listSdhOto = res.listSdhOto;
    }});

    // === listKMBJ === //
    console.log(listSdhOto, "tabellistsdhoto", false);
    loadKMBJ(listKMBJ, "tabelhome", true);
    loadKMBJ(listSdhOto, "tabellistsdhoto", false);
  }

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('kmbjdetailCetak') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {
        console.log(res)

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
    let tanggalOnly = dataPrint[0].tanggal.split(' ')[0];

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
                  <div class="pb-1" style="width: 100%">No Bukti : `+dataPrint[0].Nobukti+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Tanggal : `+tanggalOnly+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Keterangan : ${dataPrint[0].note ?? '-'}</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">UBAH KEMASAN</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Gudang</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].NamaGDG+`</div>
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
                    <td class="text-center" style="width: 30%">KODE BARANG</td>
                    <td class="text-center" style="width: 50%">NAMA BARANG</td>
                    <td class="text-center" style="width: 10%">SAT</td>
                    <td class="text-center" style="width: 15%">HASIL</td>
                    <td class="text-center" style="width: 15%">BAHAN</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalHasil = 0;
    let grandTotalBahan = 0;

    dataPrint.forEach(item => {

      if (item.Qntdb) {
        grandTotalHasil += Number(item.Qntdb) || 0;
      }

      if (item.QntCr) {
        grandTotalBahan += Number(item.QntCr) || 0;
      }
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



         tempPrintStr += `
         <tr>
         <td class="text-align: center"
               style="width: 2%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 30%;  ">${itemSub.kodebrg}</td>
         <td class="text-align: left"
               style="width: 50%;">${itemSub.namaBrg}</td>
         <td class="text-align: text-center"
               style="width: 10%;">${itemSub.Satuan}</td>
         <td class="text-align: text-right"
               style="width: 15%;">${itemSub.Qntdb ? parseFloat(itemSub.Qntdb).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 15%;">${itemSub.QntCr ? parseFloat(itemSub.QntCr).toFixed(2) : ''}</td>
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

     
 
         <div style="width:100%; display:flex; font-weight:bold; margin-top:-130px;">

            <div style="width:77%; text-align:right; padding-right:10px;">
              Total :
            </div>

            <div style="width:11%; text-align:right;">
              ${grandTotalHasil.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

            <div style="width:12%; text-align:right;">
              ${grandTotalBahan.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

          </div>
 
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 20px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Dibuat Oleh</td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Disetujui Oleh</td>
               <td class="no-border text-center" style="width: 10%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
              <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
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

  function loadKMBJ(_list, _table, _isBlmOto) {
    if ($.fn.DataTable.isDataTable("#" + _table)) {
      $("#" + _table).DataTable().destroy();
    }

    let rowTable = "";
    if (_list.length > 0) {
      _list.forEach((item, i) => {
        let dateKMBJ = doSetFormatDate(item.Tanggal, "/");
        let dateOto1 = item.TglOto1 ? doSetFormatDate(item.TglOto1, "/") : "";

	console.log("NB:", item.Nobukti);
        console.log("FULL ITEM:", item);

        rowTable += "<tr>";
	console.log( _isBlmOto ? 1:2);
        rowTable += _isBlmOto 
          ? doSetActionBlmOto(item.Nobukti) 
          : doSetActionSdhOto1(item.Nobukti);

        rowTable += "<td>" + item.GroupNobukti + "</td>";
        rowTable += "<td>" + dateKMBJ + "</td>";

        if (!_isBlmOto) {
          rowTable += "<td>" + (item.OtoUser1 ?? "") + "</td>";
          rowTable += "<td>" + (dateOto1 ?? "") + "</td>";
        }

        rowTable += "</tr>";
      });
    } else {
      let cHeader = _isBlmOto ? 3 : 5;
      rowTable += doSetEmptyTable(cHeader, "Tidak ada transaksi");
    }

    $("#" + _table + "_data").html(rowTable);

    $("#" + _table).DataTable({
      "lengthChange": false,
      "paging": false
    });
  }

    function doSetActionSdhOto1(_nb) {
        console.log('aa,..');
        let rowTable = "";
        rowTable += '<td class="text-center">';
        rowTable += '<button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail(\'' + _nb + '\')"><i class="bi bi-info"></i></button>';
        rowTable += '<button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' + _nb + '\')"><i class="bi bi-key-fill"></i></button>';
        rowTable += '<button class="btn btn-primary btn-sm" type="button" title="Submit Print" onclick="submitPrint(\'' + _nb + '\')"><i class="bi bi-printer"></i></button>';
        rowTable += "</td>";
        
        return rowTable;
      }

  function closeFormItem() {
    $('.showhide').hide();
    doUnlockHeader();
    doUnlockModeEdit();
    $("#buttonbrowse_gudang").prop("disabled", dataItem.length > 0);
  }

  function refreshForm(_nobukti = "") {
    let rowTable = "";
    dataItem = [];

    if (_nobukti == "") {
      let cHide = (gtipeform == g_tipeformDetail) ? 1 : 0;
      rowTable += doSetEmptyTable(9 - cHide, "Belum ada barang");
    } else {
      let _token  = $("#_token").val();
      $.ajax({
        url: "{!! url('kmbjgetdetail') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti: _nobukti
        },
        success: function(res) {
          if (!res.list.length) {
            alertify.warning("Data habis");
            $('#pageForm').hide();
            $('#pageHome').show();
          } else {
            let dataHeader = res.header[0];
            dataItem = res.list;

            dataItem.forEach((item, i) => {
              rowTable += `<tr>
                <td>${nullToEmpty(item.kodebrg)}</td>
                <td>${nullToEmpty(item.namaBrg)}</td>
                <td class="text-center">${nullToEmpty(item.Satuan)}</td>
                <td class="text-right">${item.QntCr ? formatCurrency(item.QntCr) : '0.00'}</td>
                <td class="text-right">${item.Qntdb ? formatCurrency(item.Qntdb) : '0.00'}</td>
                <td class="text-right">${item.Hpp ? formatCurrency(item.Hpp) : '0.00'}</td>
                <td class="text-right">${item.HrgADO ? formatCurrency(item.HrgADO) : '0.00'}</td>
                <td class="text-right">${item.HrgAdi ? formatCurrency(item.HrgAdi) : '0.00'}</td>
                ${gtipeform == g_tipeformDetail ? `` :
                `<td class="text-center">
                  <button class="btn btn-success btn-sm" type="button" onclick="buttonItemEdit(${i})"><i class="bi bi-pen"></i></button>
                  <button class="btn btn-danger btn-sm" type="button" onclick="buttonItemDelete(${i})"><i class="bi bi-trash"></i></button>
                </td>`}
              </tr>`;
            });

            $("#input_nobukti").val(dataHeader.Nobukti);
            $("#input_nourut").val(dataHeader.NOURUT);
            $("#input_tanggal").val(doSetFormatDate(dataHeader.tanggal, "-"));
            $("#input_gudang").val(dataHeader.Kodegdg);
            $("#input_keterangan").val(dataHeader.note);

            dataBrowse['gudang']  = dataHeader.Kodegdg;
          }
        },
        error: function (err) {
          console.log(err)
          console.log(err.status)
          console.log(err.statusText)
          alertify.warning('Terjadi kesalahan, silahkan refresh browser')
        }
      });
    }

    $("#tabelitem_data").html(rowTable);

    let rowHeader = `
    <tr>
      <th style="padding: 4px 12px;" scope="col">Kode</th>
      <th style="padding: 4px 12px;" scope="col">Deskripsi</th>
      <th style="padding: 4px 12px;" scope="col">Sat</th>
      <th style="padding: 4px 12px;" scope="col">Qty Asal</th>
      <th style="padding: 4px 12px;" scope="col">Qty Jadi</th>
      <th style="padding: 4px 12px;" scope="col">HPP</th>
      <th style="padding: 4px 12px;" scope="col">Rp Kredit</th>
      <th style="padding: 4px 12px;" scope="col">Rp Debet</th>
      ${gtipeform == g_tipeformDetail ? `` : `<th style="padding: 4px 12px;" scope="col">Actions</th>`}
    </tr>
    `;
    $("#tabelitem_header").html(rowHeader);
  }

  function buttonCloseForm() {
    gtipeform = g_tipeformNone;
    cleanFormItem();
    doUnlockModeDetail();
    $('#pageForm').hide();
    $('#pageHome').show();
  }

  function buttonAdd() {
    if (!doCekAkses("akses_istambah")) return;
    if (!doCekPeriode("periode_bulan", "periode_tahun", "input_tanggal")) return;

    gtipeform = g_tipeformAdd;
    $('.showhide').hide();

    cleanFormHeader();
    refreshForm();
    doUnlockHeader();
    
    let nb = doGenerateNoBukti("KMBJ");
    $("#input_nobukti").val(nb.Nobukti);
    $("#input_nourut").val(nb.Nourut);

    dataBrowse['gudang']  = "";

    $('#pageHome').hide();
    $('#pageForm').show();
  }

  function buttonEdit(_nb) {
    if (!doCekAkses("akses_iskoreksi")) return;
    if (!doCekOtorisasi(_nb, "kmbjcekotorisasi")) return;

    gtipeform = g_tipeformEdit;
    $('.showhide').hide();

    refreshForm(_nb);
    doUnlockHeader();
    $("#buttonbrowse_gudang").prop("disabled", dataItem.length > 0);

    $('#pageHome').hide();
    $('#pageForm').show();
  }

  function buttonDetail(_nb) {
    if (!doCekAkses("akses_iskoreksi")) return;
    gtipeform = g_tipeformDetail;
    $('.showhide').hide();

    refreshForm(_nb);
    doLockModeDetail();

    $('#pageHome').hide();
    $('#pageForm').show();
  }

  function buttonOtorisasi(_nb) {
    if (!doCekAkses("akses_isotorisasi1")) return;

    doOtorisasi("Ubah Kemasan Barang", _nb, "kmbjupdateotorisasi", function (res) {
      if (res.status === 1) {
        alertify.success(res.msg);
        loadAll();
      } else {
        alertify.warning(res.msg);
      }
    });
  }

  function buttonBatalOtorisasi(_nb) {
    if (!doCekAkses("akses_isbatal")) return;

    doBatalOtorisasi("Ubah Kemasan Barang", _nb, "kmbjupdatebatalotorisasi", function (res) {
      if (res.status === 1) {
        alertify.success(res.msg);
        loadAll();
      } else {
        alertify.warning(res.msg);
      }
    });
  }

  function cleanFormHeader() {
    $("#input_nobukti").val("");
    $("#input_nourut").val("");
    $("#input_gudang").val("");
    $("#input_keterangan").val("");
  }

  function onChangeKeterangan() {
    if (gtipeform != g_tipeformEdit) return;

    let nb  = $("#input_nobukti").val();
    let value  = $("#input_keterangan").val();
    doOnChangeHeader(nb, "NOTE", value, "kmbjonchangeheader");
  }

  function onChangeQtyAsal() {
    let lock = cekNotZero("inputitem_qtyasal", false); // jika tidak 0, kunci
    $('#inputitem_qtyjadi').prop('disabled', lock);
    $('#inputitem_satuanjadi').prop('disabled', lock);
    $('#inputitem_biaya').prop('disabled', lock);
  }

  function onChangeQtyJadi() {
    let lock = cekNotZero("inputitem_qtyjadi", false); // jika tidak 0, kunci
    $('#inputitem_qtyasal').prop('disabled', lock);
    $('#inputitem_satuanasal').prop('disabled', lock);
  }

  function buttonItemAdd() {
    if (!cekNotEmpty("input_gudang")) {
      return alertify.warning(messageRequired("Gudang"));
    }
    if (dataBrowse['gudang'] !== $("#input_gudang").val()) {
      return alertify.warning("Data Gudang tidak sesuai");
    }

    gtipeformitem = g_tipeformitemAdd;
    $('.showhide').hide();
    $('#formItem_labelAdd').show();
    $('#formItem_labelEdit').hide();

    doLockHeader();
    cleanFormItem();

    dataBrowse['barang']  = "";

    $('#formItem').show();
    
    $("#btnitem_kodebrg").prop("disabled", false);
    $("#inputitem_kodebrg").prop("disabled", false);
    $("#inputitem_qtyasal").prop("disabled", false);
    $("#inputitem_satuanasal").prop("disabled", false);
    $("#inputitem_qtyjadi").prop("disabled", false);
    $("#inputitem_satuanjadi").prop("disabled", false);

    document.getElementById("inputitem_kodebrg").scrollIntoView();
  }

  function buttonItemEdit(_urut) {
    gtipeformitem = g_tipeformitemEdit;
    $('.showhide').hide();
    $('#formItem_labelEdit').show();
    $('#formItem_labelAdd').hide();

    doLockHeader();
    doLockModeEdit();
    cleanFormItem();

    let item = dataItem[_urut];
    $("#inputitem_urut").val(item.urut);
    $("#inputitem_hpp").val(parseFloat(item.Hpp).toFixed(2));
    $("#inputitem_biaya").val(parseFloat(item.HargaIn).toFixed(2));

    if (item.NamaBrg != "") {
      showSatuanBarang(item.nosat, item.kodebrg, item.namaBrg,
        item.brgSat1, item.brgSat2, item.brgSat3,
        item.brgIsi1, item.brgIsi2, item.brgIsi3,
        item.QntCr, item.Qntdb);
    }

    dataBrowse['barang']  = item.kodebrg;

    $('#formItem').show();

    document.getElementById("inputitem_kodebrg").scrollIntoView();
  }

  function buttonItemDelete(_urut) {
    if (!doCekAkses("akses_ishapus")) return;

    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + nullToEmpty(dataItem[_urut].NamaBrg) + ' ?',
      function() {
        gtipeformitem = g_tipeformitemDelete;
        $("#inputitem_urut").val(dataItem[_urut].urut);
        submitItem();
      },
      function(){
        console.log('Penghapusan dibatalkan')
      }
    );
  }

  function cleanFormItem() {
    $("#inputitem_urut").val(0);
    $("#inputitem_kodebrg").val("");
    $("#inputitem_namabrg").val("");
    $("#inputitem_qtyasal").val(0);
    $("#inputitem_qtyjadi").val(0);
    $("#inputitem_qtylama").val(0);

    $('#inputitem_satuanasal').empty();
    $('#inputitem_satuanasal').prop("disabled", true);
    $('#inputitem_satuanjadi').empty();
    $('#inputitem_satuanjadi').prop("disabled", true);

    $("#inputitem_hpp").val(0);
    $("#inputitem_biaya").val(0);
  }

  function buttonBrowsePickGudang(_kode) {
    $("#input_gudang").val(_kode);
    dataBrowse['gudang'] = _kode;
  }

  function doBrowseBarang() {
    bm_filterMode = true;
    return true;
  }

  function buttonBrowsePickBarang(_kode, _nama, _sat1, _sat2, _sat3, _isi1, _isi2, _isi3) {
    $("#inputitem_kodebrg").val(_kode);
    $("#inputitem_namabrg").val(_nama);

    let satuanAsalSelect = $("#inputitem_satuanasal");
    satuanAsalSelect.empty(); // Clear previous options

    let satuanJadiSelect = $("#inputitem_satuanjadi");
    satuanJadiSelect.empty(); // Clear previous options

    // Array of satuan objects with number and value
    const satuanList = [
      { nosat: 1, sat: _sat1, isi: _isi1 },
      { nosat: 2, sat: _sat2, isi: _isi2 },
      { nosat: 3, sat: _sat3, isi: _isi3 }
    ];

    // Append valid satuan options
    let added = 0;
    satuanList.forEach(item => {
      if (item.sat && item.sat.trim() !== '') {
        satuanAsalSelect.append(`<option value="${item.nosat}||${item.sat}||${item.isi}">${item.nosat} - ${item.sat}</option>`);
        satuanJadiSelect.append(`<option value="${item.nosat}||${item.sat}||${item.isi}">${item.nosat} - ${item.sat}</option>`);
        added++;
      }
    });

    satuanAsalSelect.prop("disabled", added === 0);
    satuanJadiSelect.prop("disabled", added === 0);

    dataBrowse['barang'] = _kode;
  }

  function showSatuanBarang(_nosat, _kode, _nama, _sat1, _sat2, _sat3, _isi1, _isi2, _isi3, _qtyasal, _qtyjadi) {
    buttonBrowsePickBarang(_kode, _nama, _sat1, _sat2, _sat3, _isi1, _isi2, _isi3);

    const satuanSelect = $("#inputitem_satuan");

    // Find and select the matching option
    satuanSelect.find("option").each(function () {
      const value = $(this).val(); // e.g., "2||Box||12"
      const parts = value.split("||");

      if (parseInt(parts[0]) === _nosat) {
        satuanSelect.val(value); // set this option as selected
        return false; // break loop
      }
    });

    $("#inputitem_qtyasal").val(parseFloat(_qtyasal).toFixed(2));
    $("#inputitem_qtyjadi").val(parseFloat(_qtyjadi).toFixed(2));
    $("#inputitem_qtylama").val(parseFloat(_qtyasal).toFixed(2));

    onChangeQtyAsal();
    onChangeQtyJadi();
  }

  function getStockAkhir(_nosat, _date, _kodegdg, _kodebrg) {
    let stock = [];
    $.ajax({
      url: "{!! url('spgetstockakhir') !!}",
      type: "get",
      async: false,
      data: {
        nosat   : _nosat,
        date    : _date,
        kodegdg : _kodegdg,
        kodebrg : _kodebrg
      },
      success: function(res) {
        stock = res.stock;
    }});

    return (stock.length > 0) ? stock[0].SALDOQNT : 0;
  }

  function cekValidate(_choice) {
    /* Kolom yang wajib diisi harus dicek. Kolom yang tidak wajib di-set ke default value jika perlu.
       Return pesan jika ada yang tidak valid. Return object cart jika semuanya sudah valid. */

    let cart = {};

    cart["choice"]       = _choice;
    cart["nobukti"]      = $("#input_nobukti").val();
    if (_choice == "D") {
      cart["urut"]       = $("#inputitem_urut").val(); 
      return cart;
    }

    // CEK EMPTY
    if (!cekNotEmpty("inputitem_kodebrg")) {
      return messageRequired("Barang");
    }

    // CEK VALIDASI KODE / NO BUKTI
    if (dataBrowse['gudang'] !== $("#input_gudang").val()) {
      return "Data Gudang tidak sesuai";
    }
    if (dataBrowse['barang'] !== $("#inputitem_kodebrg").val()) {
      return "Data Barang tidak sesuai";
    }

    // CEK LAIN-LAIN
    let brg = $("#inputitem_kodebrg").val();
    if (_choice === "I") {
      if (!cekNotDuplicate(dataItem, "kodebrg", brg)) {
        return messageDuplicate("Barang " + brg);
      }
    }

    cart["tanggal"]      = $("#input_tanggal").val();
    cart["note"]         = $("#input_keterangan").val();
    cart["urut"]         = $("#inputitem_urut").val();
    cart["kodebrg"]      = brg;
    cart["kodegdg"]      = $("#input_gudang").val();

    let satuan = $('#inputitem_satuanasal').val();
    if (satuan && satuan.trim() !== "") {
      satuan = satuan.split('||').map((v, i) => i === 2 ? parseFloat(v) : v);
      cart["satuan"]     = satuan[1];
      cart["nosat"]      = satuan[0];
      cart["isi"]        = satuan[2];
    } else {
      cart["satuan"]     = "";
      cart["nosat"]      = 0;
      cart["isi"]        = 0.00;
    }

    let stock = getStockAkhir(cart["nosat"], cart["tanggal"], cart["kodegdg"], cart["kodebrg"]);

    let qtylama = Number($("#inputitem_qtylama").val());
    if (Number($("#inputitem_qtyasal").val()) <= (Number(stock) + qtylama)) {
      cart["qntdb"]      = setEmptyNumberToZero("inputitem_qtyjadi");
      cart["qntcr"]      = setEmptyNumberToZero("inputitem_qtyasal");
    } else {
      return "Qty Barang " + cart["kodebrg"] + " melebihi stok yang ada di gudang";
    }

    cart["nourut"]       = $("#input_nourut").val();
    cart["biaya"]        = setEmptyNumberToZero("inputitem_biaya");
    cart["hpp"]          = setEmptyNumberToZero("inputitem_hpp");

    cart["jmlrecord"]    = (dataItem.length) ? 1 : 0;

    return cart;
  }

  function submitItem() {
    doSubmitItem("item", "kmbjspadd", "KMBJ");
  }

  function successAdd(_nobukti) {
    loadAll();
    cleanFormItem();
    refreshForm(_nobukti);

    gtipeform = g_tipeformEdit;
  }

  function successEdit(_nobukti) {
    loadAll();
    $('.showhide').hide();
    cleanFormItem();
    refreshForm(_nobukti);
  }

  function successDelete(_nobukti) {
    loadAll();
    $('.showhide').hide();
    refreshForm(_nobukti);
  }

function doExtractDataFromTable(_cart, _item) {
    let _strValue = "";
    let _strAction = "";

     // field center
    let centerFields = ['_sat1', '_sat2'];

    _cart.forEach((itemcart) => {
      // itemcart: [0] nama kolom, [1] nama header, [2] tipe data, [3] isDesimal, [4] isParameter
      let _data;

      if (itemcart[2] === "date") {
        _data = format_date(_item[itemcart[0]]);
      } else if (itemcart[2] === "float") {
        let _value = currencyNormalizer(_item[itemcart[0]]);
        let _decimal = itemcart[3];
        _data = format_number(_value, _decimal);
      } else if (itemcart[2] === "int") {
        _data = currencyNormalizer(_item[itemcart[0]]);
      } else {
        _data = nullToEmpty(_item[itemcart[0]]);
      }

      // table cell
      if (itemcart[1] !== "") {
        if (itemcart[1].toLowerCase().includes('sat')) {
          _strValue += `<td class="text-center align-middle">${_data}</td>`;
        } else {
          _strValue += `<td>${_data}</td>`;
        }
      }

      // action values
      if (itemcart[4] === 1) {
        if (_strAction !== "") _strAction += ",";

        if (itemcart[2] === "date") {
          _strAction += `'${_data}'`;
        } else if (itemcart[2] === "varchar") {
          _strAction += `'${stringHtmlNormalizer(_data)}'`;
        } else {
          _strAction += _data;
        }
      }
    });

    return {
      strValue: _strValue,
      strAction: _strAction
    };
  }

</script>

@endsection