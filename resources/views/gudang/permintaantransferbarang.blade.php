@extends('gudang.newmaster')
@section('buttons')

@endsection
{{-- tampilan search bar 1 --}}
  @section('css')
  
  <style>
  .rodokNdukurTitik{
    margin-top:-12px;
  }
  </style>

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

{{-- tampilan search bar modal add pelanggan --}}
  <style>
    #tabel_add_list_pelanggan_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;

    }
    #tabel_add_list_pelanggan_filter label input {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem; 
    }
  </style>
{{-- end tampilan search bar modal add pelanggan --}}

{{-- tampilan search sales --}}
  <style>
    #tabel_add_list_sales_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_sales_filter label input {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search sales --}}

{{-- tampilan search modal barang all --}}
  <style>
    #input_search_barang_all {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem;
      display: flex;
      align-items: flex-end;
      margin-left: 95px;
    }
    .search-label {
    font-weight: bold;
    font-size: 0.75rem;
    margin-right: 155px;
    margin-top : -45px;
    display: inline-block;
    vertical-align: middle;
    }
  </style>
{{-- end tampilan search modal barang all --}}
@endsection
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid">
  <div class="">
    <!-- <div id="qrcode"></div> -->
    <div class="row">
      <div class="col-6 text-left">
        <h2 style="margin-top:-85px;">Permintaan Transfer Barang</h2>
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
          Add PRT
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
  </div>

  <div id="contentContainer" class="">
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
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true" 
              style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
              PRT Belum Otorisasi
            </a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false" 
              style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
              PRT Sudah Otorisasi
            </a>
            {{-- <a class="nav-item nav-link" id="nav-profile1-tab" data-toggle="tab" href="#profile1" role="tab" aria-controls="nav-profile1" aria-selected="false" 
              style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
              ihh gantengnyo
            </a> --}}
          </div>
        </div>
      </div>

      <div class="card-body" style="padding:0;">
        <div class="tab-content" id="myTabContent">
          {{-- start belum otorisasi --}}
          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <table id="tabel" class="table table-bordered table-hover table-striped table-responsive-lg">
                    <thead class="text-center bg-primary text-white">
                      <tr>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Actions</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">No. Bukti</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Tanggal</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Keterangan</th>
                      </tr>
                    </thead>
                    <tbody id="tabel_data" class="text-left">
                      @foreach($tempOutstanding as $dataUtama)
                      <tr>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('{{ $dataUtama->nobukti }}')">
                              <i class="bi bi-info"></i>
                            </button> 
                            @if($dataUtama->IsOtorisasi1 == 0)
                            <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit('{{ $dataUtama->nobukti }}')">
                              <i class="bi bi-pencil"></i>
                            </button>
                            @endif
                            @if($dataUtama->IsOtorisasi1 == 0)
                            <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi('{{ $dataUtama->nobukti }}')">
                              <i class="bi bi-key"></i>
                            </button>
                            @else
                            <button class="btn btn-danger btn-sm" type="button" title="Otorisasi" onclick="buttonBatalOtorisasi('{{ $dataUtama->nobukti }}')">
                              <i class="bi bi-key"></i>
                            </button>
                            @endif
                        </td>
                        <td>{{ $dataUtama->nobukti }}</td>
                        <td>{!! date("Y/m/d", strtotime($dataUtama->Tanggal)) !!}</td>
                        <td>{{ $dataUtama->Keterangan }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          {{-- start sudah otorisasi --}}
          <div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <table id="tabel2" class="table table-bordered table-hover table-striped table-responsive-lg">
                    <thead class="text-center bg-primary text-white">
                      <tr>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Actions</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">No. Bukti</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Tanggal</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Keterangan</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">User Oto</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Tanggal Oto</th>
                      </tr>
                    </thead>
                    <tbody id="tabel2_data" class="text-left">
                      @foreach($tempOutstanding2 as $dataUtama)
                      <tr>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('{{ $dataUtama->nobukti }}')">
                              <i class="bi bi-info"></i>
                            </button>
                            @if($dataUtama->IsOtorisasi1 == 0)
                            <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi('{{ $dataUtama->nobukti }}')">
                              <i class="bi bi-key"></i>
                            </button>
                            @else
                            <button class="btn btn-danger btn-sm" type="button" title="Otorisasi" onclick="buttonBatalOtorisasi('{{ $dataUtama->nobukti }}')">
                              <i class="bi bi-key"></i>
                            </button>
                            @endif
                            @if($dataUtama->IsOtorisasi1 == 0)
                            <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit('{{ $dataUtama->nobukti }}')">
                              <i class="bi bi-pencil"></i>
                            </button>
                            @endif
			<button style="" class="btn btn-primary btn-sm" type="button"   onclick="submitPrint('{{$dataUtama->nobukti}}')" ><i class="bi bi-printer"></i></button>
                        </td>
                        <td>{{ $dataUtama->nobukti }}</td>
                        <td>{!! date("Y/m/d", strtotime($dataUtama->Tanggal)) !!}</td>
                        <td>{{ $dataUtama->Keterangan }}</td>
                        <td>{{ $dataUtama->OtoUser1 }}</td>
                        <td>
                          @if($dataUtama->TglOto1 === null)
                            -
                          @else
                            {{ \Carbon\Carbon::parse($dataUtama->TglOto1)->format('d/m/Y') }}
                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>{{-- end sudah otorisasi --}}
        </div>
      </div>

    </div>
  </div>
  
</div>

<div id="page2" class="container-fluid" style="display: none" >
  <div class="row">
    <div class="col-6 text-left">
      <h2 id="formTitle" style="margin-top: -80px;">Form Permintaan Transfer Barang</h2>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-danger btn-lg" style="
          height: 30px; 
          margin-top: -150px; 
          padding: 4px 12px; 
          border-radius: 20px; 
          font-size: 0.75rem; 
          font-weight: 600; 
          text-transform: uppercase; 
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonCloseForm()">
        Close
      </button>
    </div>
  </div>

  <div id="modalBodyAddMain" class="">
    <div class="modal-body" style="margin-top:-60px;">
      <div class="row"> 
        <div class="col-md-3">
          <div class="row">
            <input type="hidden" class="form-control" id="input_add_nourut" placeholder="No Urut" disabled>
            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_nobukti" placeholder="" disabled>
              </div>
            </div>

            {{-- <div class="col-md-4" style="margin-top:-12px;">
              <div class="form-group">
                <label>No Urut</label>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-12px;">
              <div class="form-group">
                <input type="text" class="form-control text-center" id="input_add_nourut" placeholder="" readonly>
              </div>
            </div> --}}
            <div class="col-md-8" style="margin-top:-12px;" hidden>
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_urut" placeholder="" readonly>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-12px;">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-12px;">
              <div class="form-group">
                <input type="date" class="form-control text-left" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>
            
          </div>
        </div>

        <div class="col-md-3">
          <div class="row">

            <div class="col-md-6">
              <div class="form-group">
                <label>Gudang Asal</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group mb-3">
                <input type="text" class="form-control text-left" value='-' id="input_add_kodeGudangAsal" disabled>
                <button class="btn btn-primary btn-sm rounded-end shadow-sm" id="buttonAddListGudangAsal" onclick="buttonAddListGudangAsal()">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
            </div>

            <div class="col-md-12" style="margin-top:-15px;">
              <div class="form-group">
                <textarea style="width: 100%; resize: none;" rows=3 placeholder="Gudang Asal" class="form-control text-left align-items-center" id="input_add_namaGudangAsal"  disabled></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="row">

            <div class="col-md-6">
              <div class="form-group">
                <label>Gudang Tujuan</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group mb-3">
                <input type="text" class="form-control text-left" value='-' id="input_add_kodeGudangTujuan" disabled>
                <button class="btn btn-primary btn-sm rounded-end shadow-sm" id="buttonAddListGudangTujuan" onclick="buttonAddListGudangTujuan()">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
            </div>

            <div class="col-md-12" style="margin-top:-15px;">
              <div class="form-group">
                <textarea style="width: 100%; resize: none;" rows=3 placeholder="Gudang Tujuan" class="form-control text-left align-items-center" id="input_add_namaGudangTujuan"  disabled></textarea>
              </div>
            </div>
          </div>
        </div>

      <div class="col-md-3">
        <div class="row">
          <div class="col-md-12" style="margin-top:-5px;">
            <div class="form-group">
              <textarea style="width: 100%; resize: none;" rows=5 onblur="onChangeKeterangan()" placeholder="Keterangan" class="form-control" id="input_add_keterangan"></textarea>
            </div>
          </div>
        </div>
      </div>

    </div>

          <hr/>

          <div class="row ">
            <div class="col-md-12 mt-2 text-left" hidden>
              <button type="button" class="btn btn-primary btn-lg" style="
                height: 30px; 
                margin-top: -35px;
                padding: 4px 12px; 
                border-radius: 20px; 
                font-size: 0.75rem; 
                font-weight: 600; 
                text-transform: uppercase; 
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="buttonShowHideHeader()" class="btn btn-secondary"><b>Show/Hide Header</b></button>
            </div>
          </div>

            <div class="showhidemodalbodyaddmain mt-4" id="modalBodyAddMainHeader" style="display: none">
              <div class="row">

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Dikirim Ke</label>
                      </div>
                    </div>
                    <div class="form-group row">
                      <input class="form-control col-8" id="input_add_kodealamatkirim" readonly >
                      <button onclick="buttonAddListGudang()" id="buttonAddListGudang"  style="height:32px;" class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                    </div>
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_alamatkirim"  disabled></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Ekspedisi</label>
                      </div>
                    </div>
                    <div class="form-group row">
                      <input class="form-control col-8" id="input_add_kodeekspedisi" readonly >
                      <button onclick="buttonAddListLokasiPenerima()" id="buttonAddListLokasiPenerima" style="height:32px;" class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_ekspedisi"  disabled></textarea>
                      </div>
                    </div>
                  </div>
                </div>
  
                <div class="col-md-3">
                  <div class="row">

                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-4" style="margin-top:-10px">
                          <div class="form-group">
                            <label>No. PO Cust</label>
                          </div>
                        </div>
                        <div class="col-8" style="margin-top:-15px">
                          <div class="form-group">
                            <input type="text" class="form-control" id="input_add_nopocust" onBlur="onChangeDP()" readonly>
                          </div>
                        </div>
                      </div>
                    </div>
  
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-4" style="margin-top:-10px">
                          <div class="form-group">
                            <label>Tgl Kirim</label>
                          </div>
                        </div>
                        <div class="col-8" style="margin-top:-15px">
                          <div class="form-group">
                            <input type="date" class="form-control text-left" id="input_add_tanggalkirim" value="{!! date('Y-m-d') !!}" onblur="onChangeTgglKirim()">
                          </div>
                        </div>
                      </div>
                    </div>
  
                  </div>
                </div>

            <div class="col-md-3" hidden>
              <div class="row">

                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>Back Office</label>
                      </div>
                    </div>
                    <div class="col-3 text-right">
                      <div class="form-group">
                    <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                      </div>
                    </div>
                  </div>
                </div>
  
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-12">
                          <div class="input-group form-group">
                            <input type="hidden" class="form-control" id="input_add_kodebackoffice" >
                            <input type="text" class="form-control" id="input_add_namabackoffice"  disabled>
                            <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
  
              </div>
            </div>
  
            {{-- budi sementara 2 --}}
            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>PIC</label>
                      </div>
                    </div>
                    <div class="col-3 text-right">
                      <div class="form-group">
                    <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <input type="hidden" class="form-control" id="input_add_kodepic"  >
                        <input type="text" class="form-control" id="input_add_namapic"  disabled>
                        <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
  
            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>Sales</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group form-group">
                    <input type="hidden" class="form-control" id="input_add_kodesales" >
                    <input type="text" class="form-control" id="input_add_namasales"  disabled>
                    <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                  </div>
                </div>
              </div>
            </div>
  
            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-6" style="margin-top:-40px">
                  <div class="form-group">
                    <label>Draft PO</label>
                  </div>
                </div>
  
                <div class="col-md-6" style="margin-top:-40px">
                  <select onchange="onChangeDraftPO()" id="input_add_draftpo" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                    <option value=0 selected>Tidak</option>
                    <option value=1 >Ya</option>
                  </select>
                </div>
              </div>
            </div>
  
          </div>
            
        </div>
            <hr/>
      </div>

    </div>
    
      <div class="showhidemodalbodyaddmain container-fluid" id="modalBodyAddMainItems">
        <div class="container-fluid" style="overflow:auto; margin-top:-35px;">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_add" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px; width:120px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Qnt</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>
                  <th id="thAction" style="padding: 4px 12px;" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add" class="text-left" >
                <tr>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td class="text-center tdAction">
                    <div class="btn-group" role="group">
                      <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="">
                        <i class="bi bi-info-circle-fill"></i>
                      </button>
                      <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="">
                        <i class="bi bi-key-fill"></i>
                      </button>
                      <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="">
                        <i class="bi bi-pencil-fill"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mt-2 text-right">
            <button id='buttonPlusTambahItem'type="button" class="btn btn-primary btn-lg" style="
              height: 30px; 
              padding: 4px 12px; 
              border-radius: 20px; 
              font-size: 0.75rem; 
              font-weight: 600; 
              text-transform: uppercase; 
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="buttonAddAddItem()" class="btn btn-secondary"><b>+ Tambah Item</b></button>
          </div>
        </div>

        <!-- ADD add -->
        <div id="addAddItem" class="container-fluid showhide">
          <hr/>
          
            <div class="row">
              <div class="col-4">
                <h4 id="h4AddAddItem" style="margin-left:-35px;">Add Item</h4>
                <h4 id="h4AddEditItem" style="margin-left:-35px;">Edit Item</h4>
              </div>
            </div>

          <div class="row">
            <div class="col-md-12">
              <div class="row">

                  <div class="col-md-6">

                    <div class="row">
                      <div class="col-3" style="margin-top:-10px;">
                        <div class="form-group">
                          <label>Kode Barang</label>
                        </div>
                      </div>
                      <div class="col-md-4" style="margin-top:-10px;"> 
                        <div class="input-group form-group">
                          <input type="text" class="form-control" id="input_add_add_kodebarang">
                          <button onclick="buttonAddAddListBarang()" id="buttonAddAddListBarang" class="btn btn-primary btn-sm text-right" tabindex="1">
                            <i class="bi bi-plus"></i>
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- Nama Produk -->
                    <div class="row">
                      <div class="col-3" style="margin-top:-10px;">
                        <div class="form-group">
                          <label>Nama Barang</label>
                        </div>
                      </div>
                      <div class="col-md-6" style="margin-top:-10px;">
                        <div class="form-group">
                          <input id="input_add_add_namabarang" type="text" class="form-control text-center" disabled>
                        </div>
                      </div>
                    </div>
                    
                  </div>

                {{-- <div class="col-md-6" style="margin-left:-50px;"> --}}

                  <div class="row" style="margin-top:-15px;">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Quantity</label>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <input type="number" class="form-control text-right"  id="input_add_add_qty" value="0.00" tabindex="6">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Satuan</label>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <select id="input_add_add_nosat" class="form-control text-center form-select-lg mb-3" tabindex="9">
                          <option value=0 selected>Tidak</option>
                        </select>
                      </div>
                    </div>
                  </div>
{{-- onchange="onChangeInputAddAddHarga()" --}}
                    

                {{-- </div> --}}

              </div>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col-md-12 text-right" style="margin-top:-40px;">
              <button type="button" class="btn btn-danger btn-lg" style="
              height: 30px; 
              padding: 4px 12px; 
              border-radius: 20px; 
              font-size: 0.75rem; 
              font-weight: 600; 
              text-transform: uppercase; 
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="closeShowHideAdd()" class="btn btn-secondary">Batal</button>

              <button type="button" id="submitAddAdd" class="btn btn-primary btn-lg" style="
              height: 30px; 
              padding: 4px 12px; 
              border-radius: 20px; 
              font-size: 0.75rem; 
              font-weight: 600; 
              text-transform: uppercase; 
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="submitAddAdd()" class="btn btn-secondary">Submit Add</button>

              <button type="button" id="submitAddEdit" class="btn btn-primary btn-lg" style="
              height: 30px; 
              padding: 4px 12px; 
              border-radius: 20px; 
              font-size: 0.75rem; 
              font-weight: 600; 
              text-transform: uppercase; 
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="submitAddEdit()" class="btn btn-secondary">Submit Edit</button>
            </div>

          </div>

        </div>

        <div  id="addEditItem" class="container-fluid showhide">
            <div class="row">
              <div class="col-4">
                <h4>Edit Item</h4>
              </div>
            </div>
            <div class="row">

              <div class="col-md-12">

              <div class="row">

            <div class="col-md-4">

            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>Ref PR</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick=""  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus" ></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_edit_refpr" value=""  disabled>
              </div>
            </div>

            </div>

            </div>


            <div class="col-md-4">


            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>No Penyerahan</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick=""  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus"></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_edit_nopenyerahan"  disabled>
              </div>
            </div>

            </div>

            </div>
            </div>
            </div>

            <div class="col-md-4">


            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>Barang</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick="buttonAddEditListBarang()" id="buttonAddEditListBarang"  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus"></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="hidden" class="form-control" id="input_add_edit_kodebarang" >
                <input type="text" class="form-control" id="input_add_edit_namabarang"  disabled>
              </div>
            </div>

            </div>

            </div>

            <div class="col-md-4">


            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label>Nama Produk</label>
                </div>
              </div>


            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_edit_namaproduk" >
              </div>
            </div>

            </div>

            </div>

<div class="col-md-12">
  <div class="row">

    <div class="col-12">
      <div class="form-group">
        <label>Harga Terakhir</label>
      </div>
    </div>

    <div class="col-md-12 mb-4">
      <div class="form-group">
        <table id="tabel_edit_harga_terakhir" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Tanggal</th>
              <th scope="col">Qnt</th>
              <th scope="col">Satuan</th>
              <th scope="col">Valas</th>
              <th scope="col">Kurs</th>
              <th scope="col">Harga</th>
              <th scope="col">Disc Rp</th>
              <th scope="col">Total Diskon</th>
            </tr>
          </thead>
          <tbody id="tabel_data_edit_harga_terakhir" class="text-left" >
            <tr>
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
    </div>

  </div>
</div>


            <div class="col-md-12">
              <div class="row">


              <div class="col-md-2">
                <div class="row">

              <div class="col-md-12">
                <div class="form-group">
                  <label>Qty</label>
                </div>
              </div>


            <div class="col-md-12">
              <div class="form-group">
                <input type="number" class="form-control text-right" id="input_add_edit_qty" value ="0.00" >
              </div>
            </div>

            </div>
          </div>

            <div class="col-md-2">
              <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>Satuan</label>
              </div>
            </div>


            <div class="col-md-12">
              <select id="input_add_edit_nosat" onchange="onChangeInputAddAddNosat()" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value=0 selected>Pilih Satuan</option>
              </select>
            </div>

          </div>
        </div>

        <div class="col-md-2 row">
          <div class="col-12">
            <div class="form-group">
              <label>Satuan Produk</label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_edit_satuanproduk" >
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Harga</label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
              <input type="number" class="form-control text-right" onchange="onChangeInputAddAddHarga()" id="input_add_edit_harga" value ="0.00" >
              </div>
            </div>
          </div>
        </div>


    <div class="col-md-2">
      <div class="row">

        <div class="col-12">
          <div class="form-group">
            <label>Disc %</label>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_add_edit_disc" onChange="onChangeInputAddAddDisc()" value ="0.00" >
          </div>
        </div>

      </div>
    </div>

    <div class="col-md-2">
      <div class="row">

        <div class="col-12">
          <div class="form-group">
            <label>Disc Rp</label>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_add_edit_discrp" onChange="onChangeInputAddAddDiscRp()" value ="0.00" >
          </div>
        </div>

      </div>
    </div>

        </div>
        </div>
        <div class="col-md-12">
        <div class="row">


        </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Tambah ke PO</label>
              </div>
            </div>
            <div class="col-md-12">
              <select onchange="" id="input_add_edit_tambahkepo" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value=0 selected>Pilih</option>
                <option value=1 >Tidak</option>
                <option value=2 >Ya</option>
              </select>
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Booking</label>
              </div>
            </div>
            <div class="col-md-12">
            <select onchange="" id="input_add_edit_booking" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
              <option value=0 selected>Tidak</option>
              <option value=1 >Ya</option>
            </select>
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
              <label>Urgent</label>
              </div>
            </div>
            <div class="col-md-12">
                <select onchange="" id="input_add_edit_urgent" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value=0 selected>Tidak</option>
                <option value=1 >Ya</option>
              </select>
            </div>
          </div>
        </div>
      </div>



          <div class="row mt-2">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()" >Batal</button>
            </div>
          </div>


          <hr/>

          </div>


        <hr/>
    </div>
{{-- budi sementara --}}
  <div class="container-fluid" style="margin-top: -10px;" hidden>
    <div class="row" >

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px;">Disc %</label>
          </div>
          <div class="col-9" style="margin-left:-25px;"> 
            <input type="number" class="form-control text-right" id="input_add_disc" onblur="onChangeInputAddDisc()" value="0.00">
          </div>
        </div>
      </div>

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px;">DiscRp</label>
          </div>
          <div class="col-9" style="margin-left:-25px;"> 
            <input type="number" class="form-control text-right" id="input_add_discrp" onblur="onChangeInputAddDiscRp()" value ="0.00" >
          </div>
        </div>
      </div>

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px;">DPP</label>
          </div>
          <div class="col-9" style="margin-left:-45px;"> 
            <input type="text" class="form-control text-right" id="input_add_dpp" value ="0.00" disabled>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px; margin-left:-25px;">PPN</label>
          </div>
          <div class="col-9" style="margin-left:-65px;">
            <input type="text" class="form-control text-right" id="input_add_ppn" value ="0.00" disabled>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px; margin-left:-50px;">GrandTotal</label>
          </div>
          <div class="col-9" style="margin-left:-25px;">
            <input type="text" class="form-control text-right" id="input_add_grandtotal" value ="0.00" disabled>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>

<!-- page3 -->

<div id="page3" class="container-fluid" style="display: none" >
      <div class="row">
        <div class="col-6 text-left">
          <h2>Detail SO</h2>
        </div>
        <div class="col-6 text-right">
          <button type="button" class="btn btn-danger btn-lg" style="
          height: 30px;  
          padding: 4px 12px; 
          border-radius: 20px; 
          font-size: 0.75rem; 
          font-weight: 600; 
          text-transform: uppercase; 
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" 
          onclick="buttonCloseForm()">Close</button>
        </div>
      </div>

<div id="" class="">
  <div class="modal-body" >
    <div class="row">
      <input type="hidden" class="form-control" id="input_detail_nourut" >
      <div class="col-md-3">

        <div class="row">

          <div class="col-md-4" style="margin-top:-40px;">
            <div class="form-group">
              <label>No Bukti</label>
            </div>
          </div>
          <div class="col-md-8" style="margin-top:-40px;">
            <div class="form-group">
              <input type="text" class="form-control text-left" id="input_detail_nobukti" placeholder="" disabled>
            </div>
          </div>

        <div class="col-md-4" style="margin-top:-12px;">
          <div class="form-group">
            <label>Tanggal</label>
          </div>
        </div>
        <div class="col-md-8" style="margin-top:-12px;">
          <div class="form-group">
            <input type="date" class="form-control text-left" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
          </div>
        </div>

        <div class="col-md-4" style="margin-top:-10px;">
          <div class="form-group">
            <label>Pelanggan</label>
          </div>
        </div>

        <div class="col-md-8" style="margin-top:-10px;">
          <div class="input-group form-group">
            <input type="text" class="form-control text-left" id="input_detail_kodepelanggan" disabled>
          </div>
        </div>

        </div>

      </div>

    <div class="col-md-3">
      <div class="row">

        <div class="col-md-12" style="margin-top:-40px;">
          <div class="form-group">
            <input type="text" class="form-control text-left" id="input_detail_namapelanggan"  disabled>
          </div>
        </div>

        <div class="col-md-12" style="margin-top:-10px;">
          <div class="form-group">
            <textarea  style="width: 100%; resize: none" rows=3  class="form-control text-left" id="input_detail_alamatpelanggan" disabled></textarea>
          </div>
        </div>
        
      </div>
    </div>

    <div class="col-md-3">
      <div class="row">
        <div class="col-md-6">

        <div class="row">
          <div class="col-md-4" style="margin-top:-40px;">
            <div class="form-group">
              <label>Valas</label>
            </div>
          </div>
          <div class="col-3 text-right">
            <div class="form-group">
          <!-- <button onclick="buttonAddListValas()" id="buttonAddListValas"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
          </div>

        </div>


      </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12" style="margin-top:-40px;">
          <div class="input-group form-group">
            <input type="text" class="form-control text-center" id="input_detail_valas"  disabled>
            <!-- <button onclick="buttonAddListValas()" id="buttonAddListValas"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>
        </div>
      </div>
    </div>
    <div class="col-md-12" style="margin-top:-20px;">
      <div class="row">
        <div class="col-6">
          <div class="form-group">
            <label>Kurs</label>
          </div>
        </div>
          <div class="col-md-6">
            <div class="form-group">
              <input type="text" class="form-control text-left" id="input_detail_kurs"  disabled>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-12" style="margin-top:-12px;">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label>TOP</label>
            </div>
          </div>

        <div class="col-md-6">
          <div class="form-group">
            <input type="number" class="form-control text-left" id="input_detail_hari" disabled value=0 min=0 >
          </div>
        </div>
        </div>
    </div>

      </div>

    </div>



    <div class="col-md-3">
      <div class="row">

        <div class="col-md-12" style="margin-top:-40px;">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label>Pembayaran</label>
            </div>
          </div>

        <div class="col-md-6">
          <div class="form-group">
          <select  id="input_detail_pembayaran" disabled class="form-control text-left form-select-lg mb-3" aria-label=".form-select-lg example">
            <option value=0 selected >Non-Kredit</option>
            <option value=1 >Kredit</option>
          </select>
        </div>
        </div>
        </div>
        </div>

        <div class="col-md-12" style="margin-top:-12px;">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>TGL KIRIM</label>
              </div>
            </div>
            <div class="col-md-6">
                <input type="date" class="form-control text-left" id="input_detail_tanggalkirim" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>
          </div>

        <div class="col-md-12" style="margin-top:-12px;">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>PPN</label>
              </div>
            </div>
            <div class="col-md-6">
              <select id="input_detail_tipeppn" class="form-control text-left form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                <option value=0 selected>None</option>
                <option value=1 >Exclude</option>
                <option value=2 >Include</option>
              </select>
            </div>
          </div>
        </div>


      </div>

    </div>
  </div>

  <!-- </div> -->
  <!-- <hr/> -->
  <!-- <div class="row ">
    <div class="col-md-12 text-left">
      <div class="row">
        <div class="col-md-12">

        </div>
      </div>
    <button type="button" class="btn btn-primary" onclick="buttonAddMainHeader()" class="btn btn-secondary"  >Header</button>
    <button type="button" class="btn btn-primary" onclick="buttonAddMainItems()" class="btn btn-secondary"  >Items</button>
</div>
</div> -->
<hr/>
<div class="row ">
<div class="col-md-12 mt-2 text-left">
  <button type="button" class="btn btn-primary btn-lg" style="
  height: 30px; 
  margin-top: -40px;
  padding: 4px 12px; 
  border-radius: 20px; 
  font-size: 0.75rem; 
  font-weight: 600; 
  text-transform: uppercase; 
  transition: background-color 0.3s, box-shadow 0.3s;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
  onclick="buttonShowHideHeaderDetail()" class="btn btn-secondary"><b>Show Hide Header</b></button>
</div>
</div>
  <div class="mt-4" id="modalBodyDetailMainHeader">

  <div class="row">
    <div class="col-md-3">
      <div class="row">

        <div class="col-md-6" style="margin-top:-20px;">
          <div class="form-group">
            <label>Alamat Kirim</label>
          </div>
        </div>

        <div class="col-md-12" style="margin-top:-15px;">
          <div class="form-group">
            <input type="hidden" class="form-control text-left" id="input_detail_kodealamatkirim">
            <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatkirim"  disabled></textarea>
          </div>
        </div>

      </div>

    </div>

    <div class="col-md-3">
      <div class="row">
        <div class="col-md-8" style="margin-top:-20px;">
          <div class="form-group">
            <label>Ekspedisi</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <!-- <button onclick="buttonAddListLokasiPenerima()" id="buttonAddListLokasiPenerima"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
        </div>

      </div>

      <!-- <div class="col-md-12">
        <div class="form-group">

        </div>
      </div> -->
      <div class="col-md-12" style="margin-top:-15px;">
        <div class="form-group">
          <input type="hidden" class="form-control" id="input_detail_kodelokasipenerima" >
          <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatlokasipenerima"  disabled></textarea>
        </div>
      </div>

      </div>

      <!-- <div class="row">
        <div class="col-9">
          <div class="form-group">
            <label>PIC</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
        </div>

      </div>
      </div> -->
      <div class="row">

      <!-- <div class="col-md-12">
        <div class="form-group">

        </div>
      </div> -->

      </div>

    </div>

    <div class="col-md-3">


      <div class="row">

        <div class="col-md-10" style="margin-top:-20px;">
          <label>Keterangan</label>
        </div>

      <div class="col-md-12" style="margin-top:-15px;">
        <div class="form-group" style="margin-top: 14px">
          <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_catatan" disabled></textarea>
        </div>
      </div>

      <!-- <div class="col-md-12">

      </div> -->

      </div>

      <div class="row">

      <!-- <div class="row"> -->

      </div>

      <div class="row ">

  </div>

    </div>

    <div class="col-md-3">
      <div class="row">

        <div class="col-md-12">
          <div class="row">
            <div class="col-md-6" style="margin-top:-20px;">
              <div class="form-group">
                <label>DP</label>
              </div>
            </div>

          <div class="col-md-6" style="margin-top:-20px;">
            <div class="form-group">
              <input type="number" class="form-control text-center" id="input_detail_dp" value='0.00' disabled>
            </div>
          </div>
          </div>

        <div class="row">
          <div class="col-md-6" style="margin-top:-10px;">

            <div class="form-group">
              <label>No PO</label>
            </div>
          </div>

        <div class="col-md-6" style="margin-top:-10px;">
          <div class="form-group">
            <input  type="text" class="form-control text-center" id="input_detail_nopo"  disabled>
          </div>
        </div>
        </div>

        <div class="row">
          <div class="col-md-6" style="margin-top:-10px;">
            <div class="form-group">
              <label>Tgl PO</label>
            </div>
          </div>

        <div class="col-md-6" style="margin-top:-10px;">
          <div class="form-group">
            <input type="date" class="form-control text-center" id="input_detail_tanggalpo" value="{!! date('Y-m-d') !!}" disabled>
          </div>
        </div>
        </div>
        </div>

      </div>

      <div class="row">

      <!-- <div class="col-md-12">

      </div> -->

      </div>

    </div>
    <!-- <div class="col-md-12 mt-2 text-right" style="margin-bottom: 20px">
    <button type="button" class="btn btn-primary" id="buttonSubmitSaveHeader" onclick="submitSaveHeader()" class="btn btn-secondary"  >Save Header</button>
</div> -->

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6">
      <div class="row">

      <div class="col-md-6" style="margin-top:-10px;">
        <div class="form-group">
          <label>PIC</label>
        </div>
      </div>
      <div class="col-3 text-right">
        <div class="form-group">
      <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
      </div>

    </div>
    </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12" style="margin-top:-10px;">
          <div class="input-group form-group">
            <input type="hidden" class="form-control" id="input_detail_kodepic"  >
            <input type="text" class="form-control" id="input_detail_namapic"  disabled>
            <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>
        </div>
      </div>
    </div>

  </div>

</div>

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6">
      <div class="row">

      <div class="col-md-10" style="margin-top:-10px;">
        <div class="form-group">
          <label>Back Office</label>
        </div>
      </div>
      <div class="col-3 text-right">
        <div class="form-group">
      <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
      </div>

    </div>
    </div>
    </div>

    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12">
          <div class="row">

          <!-- <div class="col-4">

          <div class="form-group">

          </div>

          </div> -->
          <div class="col-md-12" style="margin-top:-10px;">
          <div class="input-group form-group">
            <input type="hidden" class="form-control" id="input_detail_kodebackoffice" >
            <input type="text" class="form-control" id="input_detail_namabackoffice"  disabled>
            <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>

          </div>
          </div>
        <!-- </div> -->
        </div>
      </div>

    </div>

    <!-- <div class="row"> -->

    <!-- </div> -->

  </div>

</div>

<div class="col-md-3">
  <div class="row">

  <div class="col-md-12">
    <div class="row">
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-8" style="margin-top:-10px;">
            <div class="form-group">
              <label>Sales</label>
            </div>
          </div>
          <div class="col-3 text-right">
            <div class="form-group">
          <!-- <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
          </div>

        </div>

        </div>
      </div>
      <div class="col-md-6" style="margin-top:-10px;">
        <div class="input-group form-group">
          <input type="hidden" class="form-control" id="input_detail_kodesales" >
          <input type="text" class="form-control" id="input_detail_namasales"  disabled>
          <!-- <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

        </div>
      </div>

    </div>

  </div>
  <!-- <div class="col-md-12">
    <div class="form-group">
      <input type="hidden" class="form-control" id="input_detail_kodesales" >
      <input type="text" class="form-control" id="input_detail_namasales"  disabled>
    </div>
  </div> -->
  </div>

</div>

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6" style="margin-top:-25px;">
      <div class="form-group">
        <label>Draft PO</label>
      </div>
    </div>

  <div class="col-md-6" style="margin-top:-25px;">
    <select  id="input_detail_draftpo" class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
      <option value=0 selected>Tidak</option>
      <option value=1 >Ya</option>
    </select>
  </div>
  </div>

</div>

  </div>
  <hr/>

</div>

</div>
<div class=" container-fluid" id="" style="margin-top:-40px;">

  <!-- sinia -->

<!-- END ADD EDIT -->

<div class="container-fluid mt-4" style="overflow:auto;">
  <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
  <div class="row" style="overflow:auto;">
    <table id="tabel_detail" class="table table-bordered table-hover table-striped table-responsive-lg">
      <thead class="text-center bg-primary text-white">
        <tr>
          <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
          <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
          <th style="padding: 4px 12px;" scope="col">Qty</th>
          <th style="padding: 4px 12px;" scope="col">Sat</th>
          <th style="padding: 4px 12px;" scope="col">Harga</th>
          <th style="padding: 4px 12px;" scope="col">Diskon</th>
          <th style="padding: 4px 12px;" scope="col">NDPP</th>
          <!-- <th scope="col">Actions</th> -->

        </tr>
      </thead>

      <tbody id="tabel_data_detail" class="text-left" >

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

<div class="row ">
<div class="col-md-12 mt-2 text-right">
<!-- <button type="button" class="btn btn-primary" onclick="buttonAddAddItem()" class="btn btn-secondary"  ><b>+ Tambah Item</b></button> -->
</div>
</div>

<hr/>
</div>

  <div class="container-fluid" style="margin-top: -10px;">
    <div class="row" >

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label>Disc %</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-top:-50px; margin-left:60px;">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_detail_disc" disabled value ="0.00" >
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-10px;">DiscRp</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-left:-25px;">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_detail_discrp" disabled value ="0.00" >
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-10px;">DPP</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-left:-50px;">
          <div class="form-group">
            <input type="text" class="form-control text-right" id="input_detail_dpp" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-40px;">PPN</label>
          </div>
        </div>

        <div class="col-md-9" style="margin-left:-80px;">
          <div class="form-group">
          <input type="text" class="form-control text-right" id="input_detail_ppn" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-8">
          <div class="form-group">
            <label style="margin-left:-75px;">Grand Total</label>
          </div>
        </div>

        <div class="col-md-10" style="margin-left:45px; margin-top:-50px;">
          <div class="form-group">
          <input type="text" class="form-control text-right" id="input_detail_grandtotal" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    </div>

  </div>
</div>
</div>

<!-- page3 end input_add -->

@include('gudang.modals/modalPRTAdd')

@endsection

@section('js')
<script type="text/javascript">

let dataTableAdd = []
let dataTableEdit = []
let dataAddAddListItem = []

let dataRefreshOutstanding = []
let dataRefreshOutstanding2 = []
let dataRefreshPenerimaan = []
let listAlamatKirim = []

let tempAddAdd = {}
let tempAddEdit = {}
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}
let tempSatuanBarang = []

let tipeform = ''
let tipeformitem = ''


$(document).ready(function(){
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
        });

        $("#tabel2").DataTable({
          "lengthChange": false,
            "paging": false ,
          });


        $("#tabel_add_list_barang").DataTable({
          "lengthChange": false,
            "paging": false ,
        });

        $("#tabel_add_list_barangall").DataTable({
          "lengthChange": false,
            "paging": false ,
            "searching" : false,
        });

    $("#tabel_add_list_pelanggan").DataTable({
      "lengthChange": false,
        "paging": false ,
    });

  $("#tabel_add_list_sales").DataTable({
    "lengthChange": false,
      "paging": false ,
    });

    // === SEARCH BARANG via ENTER ===
    $("#input_add_add_kodebarang")
    .off("keypress")
    .on("keypress", function (e) {
      if (e.which !== 13) return;
      e.preventDefault();

      const search = $(this).val().trim();
      if (!search) {
        alertify.warning("Silakan ketik kode atau nama barang terlebih dahulu.");
        return;
      }

      $.ajax({
        url: "{!! url('prtlistbarang') !!}",
        type: "get",
        async: false,
        data: { search },
        success: function (res) {
          dataAddAddListItem = Array.isArray(res) ? res : [];

          if (dataAddAddListItem.length === 0) {
            buttonAddAddListBarang();
            $("#tabel_data_add_list_barangall").html(
              `<tr><td class="text-center" colspan="3">Tidak ada data</td></tr>`
            );
            return;
          }

          if (dataAddAddListItem.length === 1) {
            buttonAddAddPickBarangAll(0);
            return; 
          }

          buttonAddAddListBarang();

          const rows = dataAddAddListItem
            .map(
              (item, i) => `
              <tr>
                <td class="text-center">
                  <button class="btn btn-primary btn-sm" type="button"
                          onclick="buttonAddAddPickBarangAll(${i})">
                    <i class="bi bi-plus"></i>
                  </button>
                </td>
                <td>${item.KODEBRG}</td>
                <td>${item.NAMABRG}</td>
              </tr>`
            )
            .join("");

          $("#tabel_data_add_list_barangall").html(rows);

          let table;
          if ($.fn.DataTable.isDataTable("#tabel_add_list_barangall")) {
            table = $("#tabel_add_list_barangall").DataTable();
            table.clear().rows.add($("#tabel_data_add_list_barangall tr")).draw();
          } else {
            $("#tabel_add_list_barangall").DataTable({
              lengthChange: false,
              paging: false,
              searching: false,
            });
          }
        },
        error: function (err) {
          console.log(err);
          alertify.warning("Terjadi kesalahan saat mencari data barang.");
        },
      });
    });
  //   formAddListItem
});

function buttonOtorisasi (nobukti) {
  console.log(nobukti);

  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('prtupdateotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      alertify.success('Berhasil update otorisasi');
      loadAll();
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan silahkan refresh browser');
    }
  });
}



function buttonBatalOtorisasi (nobukti) {
  console.log(nobukti)

  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
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
          url: "{!! url('prtupdatebatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
          pket :value

          },
          success: function(res) {
            alertify.success('Berhasil batal otorisasi')
            loadAll()

          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
      alertify.error("Action cancelled");
    });



}

function onChangeKeterangan () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_keterangan").val()
    onChangeHeader('NOTE' , value)
  }
}

function onChangeTgglKirim () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_tanggalkirim").val()
    onChangeHeader('TglKirim' , value)
  }
}

function onChangeTipePPN () {
  console.log('onChangeTipePPN')
  if (tipeform == 'edit') {
    let value = $("#input_add_tipeppn").val()
    console.log(value)
    onChangeHeader('TipePPn' , value)
    onChangeHeader('PPN' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }


}

function onChangeDP () {
  console.log('onChangeDP')
  if (tipeform == 'edit') {
    let value = $("#input_add_nopocust").val()
    console.log(value)
    onChangeHeader('DP' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }


}

function onChangeDraftPO () {
  console.log('onChangeDraftPO')
  if (tipeform == 'edit') {
    let value = $("#input_add_draftpo").val()
    console.log(value)
    onChangeHeader('PPO' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }


}

function onChangeHari () {
  console.log('onChangeHari')
  if (tipeform == 'edit') 
  {
    let value = $("#input_add_hari").val()
    console.log(value)
    onChangeHeader('HARI' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }
}

function onChangeInputAddDisc () {
    // document.getElementById("input_add_discrp").value = '0.00'
    console.log('onChangeDisc')
    if (tipeform == 'edit') {
      let value = $("#input_add_disc").val()
      console.log(value)
      onChangeHeader('DISC' , value)
      refreshUpdateHeader()
      let nobukti = $("#input_add_nobukti").val()
      refreshDataTableAdd(nobukti)
    }
}

function onChangeInputAddDiscRp () 
{
  // document.getElementById("input_add_disc").value = '0.00'
  console.log('onChangeDiscRp')
    if (tipeform == 'edit') {
      let value = $("#input_add_discrp").val()
      console.log(dataHeaderAdd)
      let x = Number(value) / Number(dataHeaderAdd.TotSubTotal) * 100
      console.log(x)
      console.log(value)
      onChangeHeader('DISC' , x)
      refreshUpdateHeader()
      let nobukti = $("#input_add_nobukti").val()
      refreshDataTableAdd(nobukti)
    }
}

function refreshUpdateHeader () 
{
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('pospupdatepo') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      // alertify.success('update header berhasil')
      // return
      console.log('check')

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function onChangeHeaderSP (field, value , field1 = null , value2 = null) 
{
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('soonchangeheadersp') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success('update header berhasil')
      return
      console.log('check')

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function onChangeHeader (field, value) {
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('prtonchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success(`update ${field} berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function submitAddAdd () {

  console.log('submitAddAdd')

  let checkDate = new Date($("#input_add_tanggal").val())
  
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() +1) !== Number(periode_bulan)) {
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let _token  = $("#_token").val()
  let Choice = "I"
  let Nobukti = $("#input_add_nobukti").val()
  let NoUrut = $("#input_add_nourut").val()
  let Tanggal = $("#input_add_tanggal").val()
  let Note = $("#input_add_keterangan").val()
  let Urut = 0
  let Kodebrg =  $("#input_add_add_kodebarang").val()
  let GdgAsal =  $("#input_add_kodeGudangAsal").val()
  let GdgTujuan =  $("#input_add_kodeGudangTujuan").val()
  // let Sat_1 =  $("#input_add_add_nosat").val()
  // let Sat_2 =  $("#input_add_add_nosat").val()
  // let Qnt =  $("#input_add_add_nosat").val()
  let QNt2 = parseInt($("#input_add_add_qty").val())
  let NoSat =  parseInt($("#input_add_add_nosat").val())
  //IDUSER dari controller
  //noPenyerahan kosong

  let Sat_1 = ''
  let Sat_2 = ''
  let Qnt = 0
  let Isi = 0

  if (NoSat == 1) {
    Qnt = QNt2 * parseInt(tempSatuanBarang[0].ISI1)
    Sat_1 = tempSatuanBarang[0].SAT1
    Sat_2 = tempSatuanBarang[0].SAT1
    Isi = parseInt(tempSatuanBarang[0].ISI1)
  }

  if (NoSat == 2) {
    Qnt = QNt2 * parseInt(tempSatuanBarang[0].ISI2)
    Sat_1 = tempSatuanBarang[0].SAT2
    Sat_2 = tempSatuanBarang[0].SAT2
    Isi = parseInt(tempSatuanBarang[0].ISI2)
  }

  if (NoSat == 3) {
    Qnt = QNt2 * parseInt(tempSatuanBarang[0].ISI3)
    Sat_1 = tempSatuanBarang[0].SAT3
    Sat_2 = tempSatuanBarang[0].SAT3
    Isi = parseInt(tempSatuanBarang[0].ISI3)
  }

  if (!Note) {
    Note = '-'
  }

  if (!GdgAsal || !GdgTujuan || !Kodebrg) {
    alertify.warning("Data belum lengkap")
    return
  }
  if (Number(Qnt) < 0)  {
    alertify.warning("Angka negatif")
    return
  }

  $.ajax({
    url: "{!! url('prtspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      Choice,
      Nobukti,
      NoUrut,
      Tanggal,
      Note,
      Urut,
      Kodebrg,
      GdgAsal,
      GdgTujuan,
      Sat_1,
      Sat_2,
      Qnt,
      QNt2,
      NoSat,
      Isi
      //IDUSEr
      //noPenyerahan

    },
    success: function(res) {
      if (res == 1) {

        loadAll()
        tipeform = 'edit'
        unlockFormAdd()
        cleanFormAddAdd()
        refreshDataTableAdd(Nobukti)

        alertify.success('Berhasil menambah item')
      }
      if(res == 2) {
        setNewNoBukti()
        alertify.warning('Nobukti telah direfresh silahkan submit ulang')
      }

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function submitAddEdit () {

  console.log('submitAddEdit')

  let checkDate = new Date($("#input_add_tanggal").val())
  
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() +1) !== Number(periode_bulan)) {
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let _token  = $("#_token").val()
  let Choice = "U"
  let Nobukti = $("#input_add_nobukti").val()
  let NoUrut = $("#input_add_nourut").val()
  let Tanggal = $("#input_add_tanggal").val()
  let Note = $("#input_add_keterangan").val()
  let Urut = $("#input_add_urut").val()
  let Kodebrg =  $("#input_add_add_kodebarang").val()
  let GdgAsal =  $("#input_add_kodeGudangAsal").val()
  let GdgTujuan =  $("#input_add_kodeGudangTujuan").val()
  // let Sat_1 =  $("#input_add_add_nosat").val()
  // let Sat_2 =  $("#input_add_add_nosat").val()
  // let Qnt =  $("#input_add_add_nosat").val()
  let QNt2 = parseInt($("#input_add_add_qty").val())
  let NoSat =  parseInt($("#input_add_add_nosat").val())
  //IDUSER dari controller
  //noPenyerahan kosong

  let Sat_1 = ''
  let Sat_2 = ''
  let Qnt = 0
  let Isi = 0

  if (NoSat == 1) {
    Qnt = QNt2 * parseInt(tempSatuanBarang[0].ISI1)
    Sat_1 = tempSatuanBarang[0].SAT1
    Sat_2 = tempSatuanBarang[0].SAT1
    Isi = parseInt(tempSatuanBarang[0].ISI1)
  }

  if (NoSat == 2) {
    Qnt = QNt2 * parseInt(tempSatuanBarang[0].ISI2)
    Sat_1 = tempSatuanBarang[0].SAT2
    Sat_2 = tempSatuanBarang[0].SAT2
    Isi = parseInt(tempSatuanBarang[0].ISI2)
  }

  if (NoSat == 3) {
    Qnt = QNt2 * parseInt(tempSatuanBarang[0].ISI3)
    Sat_1 = tempSatuanBarang[0].SAT3
    Sat_2 = tempSatuanBarang[0].SAT3
    Isi = parseInt(tempSatuanBarang[0].ISI3)
  }

  if (!Note) {
    Note = '-'
  }

  console.log({
    _token,
    Choice,
    Nobukti,
    NoUrut,
    Tanggal,
    Note,
    Urut,
    Kodebrg,
    GdgAsal,
    GdgTujuan,
    Sat_1,
    Sat_2,
    Qnt,
    QNt2,
    NoSat,
    Isi
    //IDUSEr
    //noPenyerahan
    })

  if (!GdgAsal || !GdgTujuan || !Kodebrg) {
    alertify.warning("Data belum lengkap")
    return
  }
  if (Number(Qnt) < 0)  {
    alertify.warning("Angka negatif")
    return
  }

  $.ajax({
    url: "{!! url('prtspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      Choice,
      Nobukti,
      NoUrut,
      Tanggal,
      Note,
      Urut,
      Kodebrg,
      GdgAsal,
      GdgTujuan,
      Sat_1,
      Sat_2,
      Qnt,
      QNt2,
      NoSat,
      Isi
      //IDUSEr
      //noPenyerahan

    },
    success: function(res) {
      if (res == 1) {

        loadAll()
        tipeform = 'edit'
        cleanFormAddAdd()

        refreshDataTableAdd(Nobukti)

        alertify.success('Berhasil menambah item')
      }
      if(res == 2) {
        setNewNoBukti()
        alertify.warning('Nobukti telah direfresh silahkan submit ulang')
      }

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function onChangeInputAddPembayaran () {
  console.log("onChangeInputAddPembayaran")
  let check = Number($("#input_add_pembayaran").val())
  console.log(typeof check)
  console.log(check)

  if (dataTableAdd.length) {

    onChangeHeader('TIPEBAYAR' , check)
  }
  let nobukti = $("#input_add_nobukti").val()
  console.log('len',dataTableAdd.length)
  if (check) {
    let _token = $("#_token").val();
    let kodesupplier = $("#input_add_kodesupplier").val();

    $.ajax({
      url: "{!! url('socekkredithari') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        kodesupplier
      },
      success: function(res) {
        console.log(res)
        if(res.length && res[0].hari) {
          document.getElementById("input_add_hari").value = res[0].hari

          if (dataTableAdd.length) {
            console.log('masokk')
            onChangeHeader('HARI' , res[0].hari)
            refreshUpdateHeader()
            // let nobukti = $("#input_add_nobukti").val()
            refreshDataTableAdd(nobukti)

          }
        }

      }})

  } else {
    document.getElementById("input_add_hari").value = 0
    // console.log('onChangeHari')
    if (tipeform == 'edit') {
      console.log('len', dataTableAdd.length)
      console.log(value)
      // onChangeHeader('TIPEBAYAR' , check)
      if (dataTableAdd.length) {
        console.log('masokk 2')
        onChangeHeader('HARI' , 0)
        refreshUpdateHeader()
        // let nobukti = $("#input_add_nobukti").val()
        refreshDataTableAdd(nobukti)

      }
    }
  }
}

function onChangeInputAddAddDisc () {
  console.log("onChangeInputAddAddDisc")
  let harga = $("#input_add_add_harga").val();

  if (!Number(harga)) {

    document.getElementById("input_add_add_discrp").value = '0.00'
    return
  }

  let disc = $("#input_add_add_disc").val();
  let discRp = Number(harga) * Number(disc) / 100
  document.getElementById("input_add_add_discrp").value = parseFloat(discRp).toFixed(2)

}

function onChangeInputAddAddHarga () {
  document.getElementById("input_add_add_discrp").value = '0.00'
  document.getElementById("input_add_add_disc").value = '0.00'
}

function onChangeInputAddEditHarga () {
  document.getElementById("input_add_edit_discrp").value = '0.00'
  document.getElementById("input_add_edit_disc").value = '0.00'
}

function onChangeInputAddAddDiscRp () {
  console.log("onChangeInputAddAddDiscRp")
  let harga = $("#input_add_add_harga").val();

  if (!Number(harga)) {

    document.getElementById("input_add_add_disc").value = '0.00'
    return
  }

  let discRp = $("#input_add_add_discrp").val();
  let disc = Number(discRp) / Number(harga) * 100
  document.getElementById("input_add_add_disc").value = parseFloat(disc).toFixed(2)
}

function buttonAddAddItem () {
  tipeformitem = 'add'
  $('.showhide').hide();

  cleanFormAddAdd()
  unlockFormAddAdd()

  $('#h4AddAddItem').show();
  $('#h4AddEditItem').hide();
  $('#submitAddAdd').show();
  $('#submitAddEdit').hide();
  $('#addAddItem').show();
  document.getElementById("input_add_add_namabarang").scrollIntoView();
}

function buttonAddEditItem (i) {
  
  tipeformitem = 'edit'
  let _token = $("#_token").val();
  $('.showhide').hide();

  tempAddEdit = dataTableAdd[i]

  console.log(tempAddEdit.URUT)

  cekSatuanBarang(tempAddEdit.KODEBRG)

  console.log(tempSatuanBarang)

  document.getElementById("buttonAddAddListBarang").disabled = true;
  document.getElementById("input_add_add_kodebarang").disabled = true;

  let selectOption = ''
  // Change this line too:
  if (tempSatuanBarang[0].SAT1) {
    selectOption += `<option value=1 selected>1-${tempSatuanBarang[0].SAT1}</option>`
  }
  if (tempSatuanBarang[0].SAT2) {
    selectOption += `<option value=2>2-${tempSatuanBarang[0].SAT2}</option>`
  }
  if (tempSatuanBarang[0].SAT3) {
    selectOption += `<option value=1>3-${tempSatuanBarang[0].SAT3}</option>`
  }

  // Use tempAddEdit instead of dataTableAdd:
  document.getElementById("input_add_add_kodebarang").value = tempAddEdit.KODEBRG
  document.getElementById("input_add_add_namabarang").value = tempAddEdit.NamaBrg
  document.getElementById("input_add_add_qty").value = tempAddEdit.QNT
  document.getElementById("input_add_urut").value = tempAddEdit.URUT
  document.getElementById("input_add_add_nosat").innerHTML = selectOption

  $('#h4AddAddItem').hide();
  $('#h4AddEditItem').show();
  $('#submitAddAdd').hide();
  $('#submitAddEdit').show();
  $('#addAddItem').show();

  document.getElementById("input_add_add_namabarang").scrollIntoView();
}

function closeShowHideAdd () {
  $('.showhide').hide();

}

function setNewNoBukti () {
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('spnobukti') !!}",
    type: "post",
    async: false,
    data: {
      kode:'PRT',
      _token
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
}


function buttonAddListPIC () {

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodesupplier").val();

  if (!kodecustsupp) {
    alertify.warning("Isi pelanggan terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('solistpic') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.kodepic}</td>
        <td>${item.nama}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPIC('${item.kodepic}' , '${item.nama}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_pic").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPIC').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddAddListPWO () {

  let _token = $("#_token").val();
  let noSo = $("#input_add_noso").val();

  if (!noSo) {
    alertify.warning("Isi Nomor SO terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('polistpwo') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      noSo
    },
    success: function(res) {
      let rowTable = `
        <tr>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPWO('-' , '-')" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td>${item.no_bukti}</td>
          <td>${item.tanggal}</td>
          <td>${item.supplier}</td>
          <td>${item.kode}</td>
          <td>${item.NAMABRG}</td>
          <td class="text-right">${item.qty}</td>
          <td class="text-center">${item.satuan}</td>
          <td class="text-right">${item.harga}</td>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPWO('${item.no_bukti}' , '${item.tanggal}')" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=9>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_pwo").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPWO').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();

  $("#form").modal('toggle')
}

function buttonAddListGudang () {

  let _token = $("#_token").val();

  $('#tabel_add_list_alamatkirim').DataTable().destroy();
  $.ajax({
    url: "{!! url('polistgudang') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      let rowTable = ``

      listAlamatKirim = res

      listAlamatKirim.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KODEGDG}</td>
        <td>${item.NAMA}</td>
        <td>${item.Alamat}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="buttonAddPickAlamatKirim(${i} )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`

        // '
        // <tr>
        // <td> '+ item.nomor + '</td>
        // <td> '+ item.nama + '</td>
        // <td>+ ' + item.alamat + '</td>
        // <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickAlamatKirim( `' + item.nomor + '` , `'+ item.nama + '` , `' + item.alamat +'` )" type="button" ><i class="bi bi-plus"></i></button></td>
        //
        // </tr>'
      });


      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_alamatkirim").innerHTML = rowTable
      $("#tabel_add_list_alamatkirim").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListAlamatKirim').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}


function buttonAddListLokasiPenerima () {

  let _token = $("#_token").val();

  $('#tabel_add_list_lokasipenerima').DataTable().destroy();

  $.ajax({
    url: "{!! url('polistlokasipenerima') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KodeCustsupp}</td>
        <td>${item.NamaCust}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickLokasiPenerima('${item.KodeCustsupp}' , '${item.NamaCust}' )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_lokasipenerima").innerHTML = rowTable
      $("#tabel_add_list_lokasipenerima").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListLokasiPenerima').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListValas () {
  
  $('#tabel_add_list_valas').DataTable().destroy();
  $.ajax({
    url: "{!! url('polistvalas') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.kodevls}</td>
        <td>${item.namavls}</td>
        <td>${item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00'}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickValas('${item.kodevls}' , '${item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00'}' )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_valas").innerHTML = rowTable
      $("#tabel_add_list_valas").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListValas').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListGudangAsal () 
{

  testValue = document.getElementById('input_add_kodeGudangTujuan').value

  document.getElementById("titleGudang").innerHTML = "Gudang Asal"

  $('#tabel_add_list_gudang').DataTable().destroy();
  $.ajax({
    url: "{!! url('prtlistgudangasal') !!}",
    type: "get",
    async: false,
    data: {
      kodeGudangTujuan : testValue
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:10px;" onclick="buttonAddPickGudangAsal('${item.KodeGdg}' , '${item.NamaGdg}')" type="button" ><i class="bi bi-plus"></i></button>
        </td>
        <td>${item.KodeGdg}</td>
        <td>${item.NamaGdg}</td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_gudang").innerHTML = rowTable
      $("#tabel_add_list_gudang").DataTable({
        "lengthChange": false,
          "paging": false ,
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListGudang').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListGudangTujuan () 
{

  testValue = document.getElementById('input_add_kodeGudangAsal').value
  document.getElementById("titleGudang").innerHTML = "Gudang Tujuan"

  $('#tabel_add_list_gudang').DataTable().destroy();
  $.ajax({
    url: "{!! url('prtlistgudangtujuan') !!}",
    type: "get",
    async: false,
    data: {
      kodeGudangAsal : testValue
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:10px;" onclick="buttonAddPickGudangTujuan('${item.KodeGdg}' , '${item.NamaGdg}')" type="button" ><i class="bi bi-plus"></i></button>
        </td>
        <td>${item.KodeGdg}</td>
        <td>${item.NamaGdg}</td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_gudang").innerHTML = rowTable
      $("#tabel_add_list_gudang").DataTable({
        "lengthChange": false,
          "paging": false,
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListGudang').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListBackOffice () {
  $.ajax({
    url: "{!! url('solistbackoffice') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.keynik}</td>
        <td>${item.fullname}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickBackOffice('${item.keynik}' , '${item.fullname}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_backoffice").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListBackOffice').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListSales () {
  $('#tabel_add_list_sales').DataTable().destroy();
  $.ajax({
    url: "{!! url('solistsales') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        console.log(item.keynik)
        console.log(item.nama)
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSales('${item.keynik}' , '${String(item.nama)}')" type="button" ><i class="bi bi-plus"></i></button></td>
        <td>${item.keynik}</td>
        <td>${item.nama}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable
      $("#tabel_add_list_sales").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListSales').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function onChangeInputAddAddNosat () {
  console.log('onChangeInputAddAddNosat')
  let _token  = $("#_token").val()
  let nosat = $("#input_add_add_nosat").val()
  console.log(nosat)
  console.log(Number(nosat))
  let kodebarang = $("#input_add_add_kodebarang").val()

  if (!kodebarang) {
    return
  }

  $.ajax({
    url: "{!! url('socekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang ,
      nosat
    },
    success: function(res) {
      console.log(res)

      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.TANGGAL}</td>
        <td>-</td>
        <td class="text-center">${item.SATUAN}</td>
        <td>-</td>
        <td>-</td>
        <td class="text-right">${item.Xharga}</td>
        <td>-</td>
        <td>-</td>

        </tr>`
      });


      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=8>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      // let rowTable = ``
      // res.forEach((item, i) => {
      //   rowTable += `
      //   <tr>
      //   <td>${item.keynik}</td>
      //   <td>${item.nama}</td>
      //   <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSales('${item.keynik}' , '${item.nama}')" type="button" ><i class="bi bi-plus"></i></button></td>
      //
      //   </tr>`
      // });
      //
      //
      //
      //
      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      // }
      // document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable

      if (tipeformitem == 'add') {
        console.log(tempAddAdd[`Hrg${nosat}_1`])
        if (res.length && res[0].Xharga) {
          console.log('if1')
          document.getElementById("input_add_add_harga").value = res[0].Xharga
        } else {
          console.log('else1')
          if (tempAddAdd[`Hrg${nosat}_1`]) {
            console.log('if2')
            document.getElementById("input_add_add_harga").value = tempAddAdd[`Hrg${nosat}_1`]
          } else {
            console.log('else2')
            document.getElementById("input_add_add_harga").value = '0.00'
          }
        }
      } else {

      }

      // if (res.length && res[0].Xharga) {
      //   document.getElementById("input_add_add_harga").value = res[0].Xharga
      // } else {
      //   if ( nosat == 1) {
      //     if (tempAddAdd.Hrg1_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg1_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      //   if ( nosat == 2) {
      //     if (tempAddAdd.Hrg2_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg2_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      //   if ( nosat == 3) {
      //     if (tempAddAdd.Hrg3_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg3_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      // }


    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}
      
function loadAll () {
  let _token = $("#_token").val();

  $('#tabel').DataTable().destroy();
  $('#tabel2').DataTable().destroy();

  $.ajax({
    url: "{!! url('prtloadall') !!}",
    type: "get",
    async: false,
    success: function(res) {

      let dataBelum = res.tempOutstanding;
      let dataSudah = res.tempOutstanding2;

      let rowTable = "";
      let rowTable2 = "";

      // === Belum Otorisasi ===
      dataBelum.forEach((item, i) => {
        let date1 = "";
        if (item.Tanggal) {
          let date = new Date(item.Tanggal);
          let day = ("0" + date.getDate()).slice(-2);
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          date1 = date.getFullYear()+"/"+month+"/"+day;
        }

        rowTable += `
          <tr>
            <td class="text-center">
              <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('${item.nobukti}')">
                <i class="bi bi-info"></i>
              </button>
              <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit('${item.nobukti}')">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi('${item.nobukti}')">
                <i class="bi bi-key"></i>
              </button>
            </td>
            <td>${item.nobukti}</td>
            <td>${date1}</td>
            <td>${item.Keterangan || ""}</td>
          </tr>
        `;
      });

      document.getElementById("tabel_data").innerHTML = rowTable;

      $("#tabel").DataTable({
        "lengthChange": false,
        "paging": false,
      });

      // === Sudah Otorisasi ===
      dataSudah.forEach((item, i) => {
        let date1 = "";
        if (item.Tanggal) {
          let date = new Date(item.Tanggal);
          let day = ("0" + date.getDate()).slice(-2);
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          date1 = date.getFullYear()+"/"+month+"/"+day;
        }

        rowTable2 += `
          <tr>
            <td class="text-center">
              <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('${item.nobukti}')">
                <i class="bi bi-info"></i>
              </button>
              <button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${item.nobukti}')">
                <i class="bi bi-key"></i>
              </button>
	    <button class="btn btn-primary btn-sm" title="Print" onclick="submitPrint('${item.nobukti}')">
                <i class="bi bi-printer"></i>
            </button>
            </td>
            <td>${item.nobukti}</td>
            <td>${date1}</td>
            <td>${item.Keterangan || ""}</td>
            <td>${item.OtoUser1}</td>
            <td>${formatDateTime(item.TglOto1)}</td>
          </tr>
        `;
      });

      document.getElementById("tabel2_data").innerHTML = rowTable2;

      $("#tabel2").DataTable({
        "lengthChange": false,
        "paging": false,
      });
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan saat load data');
    }
  });

  console.log('load all selesai');
  setFormMode('');
}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('prtdetailCetak') !!}",
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
                  <div class="pb-1" style="width: 100%">Keterangan : `+dataPrint[0].NOTE+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">BUKTI PERMINTAAN TRANSFER BARANG</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No Bukti</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].Nobukti+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Tanggal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+tanggalOnly+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Gudang Asal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].GDGASAL+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Gudang Tujuan</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].GDGTUJUAN+`</div>
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
                    <td class="text-center" style="width: 20%">DIMINTA</td>
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



         tempPrintStr += `
         <tr>
         <td class="text-align: center"
               style="width: 2%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 30%;  ">${itemSub.KODEBRG}</td>
         <td class="text-align: left"
               style="width: 50%;">${itemSub.NAMABRG}</td>
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.QNT1 ? parseFloat(itemSub.QNT1).toFixed(2) + ' ' + itemSub.SAT_1 : ''}</td>
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
               <td class="no-border text-center" style="width: 35%">Diminta Oleh</td>
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

function formatDateTime (dateString) {
  if (!dateString) return '-';
  
  const date = new Date(dateString);
  
  // Format: DD/MM/YYYY - HH:MM:SS
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  // const hours = String(date.getHours()).padStart(2, '0');
  // const minutes = String(date.getMinutes()).padStart(2, '0');
  // const seconds = String(date.getSeconds()).padStart(2, '0');
  
  return `${day}/${month}/${year}`;
  // return `${day}/${month}/${year} - ${hours}:${minutes}:${seconds}`;
}

function buttonAddAddListBarang () {
  console.log("masuk barang all")
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddAddListBarangAll').show();

  if ($.fn.DataTable.isDataTable("#tabel_add_list_barangall")) {
    $("#tabel_add_list_barangall").DataTable().clear().destroy();
  }

  document.getElementById("tabel_data_add_list_barangall").innerHTML = ''

  $("#tabel_add_list_barangall").DataTable({
    lengthChange: false,
    paging: false,
    searching: false
  });

  document.getElementById("input_search_barang_all").value = ''
  $("#form").modal('toggle')

  document.getElementById("modalBodyAddAddListBarangAllTitle").scrollIntoView();

  $('#form').on('shown.bs.modal', function () {
    $('#input_search_barang_all').trigger('focus')
  })
  console.log("Masuk Tes")
}

  function searchBarangAll (e) {
  if (e.which == 13) {
    console.log('enter')

    let search = $("#input_search_barang_all").val();

    if ($.fn.DataTable.isDataTable("#tabel_add_list_barangall")) {
      $("#tabel_add_list_barangall").DataTable().clear().destroy();
    }

    $.ajax({
      url: "{!! url('prtlistbarang') !!}",
      type: "get",
      async: false,
      data: { search },
      success: function(res) {
        console.log(res)

        dataAddAddListItem = res;

        if (!res.length) {
          document.getElementById("tabel_data_add_list_barangall").innerHTML =
            `<tr><td class="text-center" colspan="3">Tidak ada data</td></tr>`;
          return;
        }

        let rowTable = ""
        res.forEach((item, i) => {
          rowTable += `
            <tr>
              <td class="text-center">
                <button class="btn btn-primary btn-sm"
                  onclick="buttonAddAddPickBarangAll(${i})" type="button">
                  <i class="bi bi-plus"></i>
                </button>
              </td>
              <td>${item.KODEBRG}</td>
              <td>${item.NAMABRG}</td>
            </tr>`;
        });

        document.getElementById("tabel_data_add_list_barangall").innerHTML = rowTable

        $("#tabel_add_list_barangall").DataTable({
          lengthChange: false,
          paging: false,
          searching: false
        });
      }
    })
  }
}

function buttonAddAddPickBarangAll (index) {
  let item = dataAddAddListItem[index];

  cekSatuanBarang(item.KODEBRG);

  document.getElementById("input_add_add_kodebarang").value = item.KODEBRG;
  document.getElementById("input_add_add_namabarang").value = item.NAMABRG;

  let selectOption = '<option value="0" disabled>-- Pilih Satuan --</option>';

  if (tempSatuanBarang && tempSatuanBarang.length > 0) {
    let satuan = tempSatuanBarang[0];
    if (satuan.SAT1) selectOption += `<option value="1">1 - ${satuan.SAT1}</option>`;
    if (satuan.SAT2) selectOption += `<option value="2">2 - ${satuan.SAT2}</option>`;
    if (satuan.SAT3) selectOption += `<option value="3">3 - ${satuan.SAT3}</option>`;
  } else {
    alertify.warning("Barang ini tidak memiliki data satuan.");
  }

  document.getElementById("input_add_add_nosat").innerHTML = selectOption;

  if (tempSatuanBarang.length > 0 && tempSatuanBarang[0].SAT1) {
    document.getElementById("input_add_add_nosat").value = "1";
  }

  if ($("#form").hasClass("show")) {
    $("#form").modal("hide");
  }

  setTimeout(() => {
    document.getElementById("input_add_add_qty").focus();
    document.getElementById("input_add_add_qty").select();
  }, 300);
}


function cekSatuanBarang (KodeBrg) {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('ceksatuanbarang') !!}",
    type: "post",
    async: false,
    data: { _token, KodeBrg },
    success: function(res) {
      tempSatuanBarang = Array.isArray(res) ? res : [];
    },
    error: function (err) {
      console.log("Error cekSatuanBarang:", err);
      tempSatuanBarang = [];
    }
  });
}


function buttonAddPickGudangAsal (kode, nama) {
  console.log('buttonAddPickPelanggan')
  console.log(kode,nama)
  document.getElementById("input_add_kodeGudangAsal").value = kode
  document.getElementById("input_add_namaGudangAsal").value = nama
  buttonAddListBatal()
}

function buttonAddPickGudangTujuan (kode, nama) {
  console.log('buttonAddPickPelanggan')
  console.log(kode,nama)
  document.getElementById("input_add_kodeGudangTujuan").value = kode
  document.getElementById("input_add_namaGudangTujuan").value = nama
  buttonAddListBatal()
}

function buttonAddPickAlamatKirim (index) {

  let itemX = listAlamatKirim[index]
  console.log(itemX)
  // console.log(kode,nama,alamat)
  if (tipeform == 'edit') {
    onChangeHeader('NoAlamatKirim' , itemX.KODEGDG)
    onChangeHeader('AlamatKirim' , itemX.Alamat)
  }

  document.getElementById("input_add_kodealamatkirim").value = itemX.KODEGDG
  document.getElementById("input_add_alamatkirim").value = itemX.Alamat
  buttonAddListBatal()

}

function buttonAddPickNoSO (kode, nama) {
  console.log('buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODEKEBUN' , kode)

  }
  document.getElementById("input_add_noso").value = kode
  document.getElementById("input_add_nopocust").value = nama

  buttonAddListBatal()
}

function buttonAddPickLokasiPenerima (kode, nama ) {
  console.log('buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODEKEBUN' , kode)

  }
  document.getElementById("input_add_kodeekspedisi").value = kode
  document.getElementById("input_add_ekspedisi").value = nama

  buttonAddListBatal()
  document.getElementById("input_add_kodeekspedisi").scrollIntoView();
}

function buttonAddPickPIC (kode, nama ) {
  console.log('buttonAddPickPIC')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KodePF' , kode)
  }
  document.getElementById("input_add_kodepic").value = kode
  document.getElementById("input_add_namapic").value = nama
  buttonAddListBatal()
}

function buttonAddPickPWO (kode, nama) {
  console.log('buttonAddPickPWO')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KodePF' , kode)
  }
  document.getElementById("input_add_kodepic").value = kode
  document.getElementById("input_add_namapic").value = nama
  buttonAddListBatal()
}

function buttonAddPickValas (kode, kurs) {
  console.log('buttonAddPickValas')
  console.log(kode,kurs)
  if (tipeform == 'edit') {
    onChangeHeader('KODEVLS' , kode)
    onChangeHeader('KURS' , kurs)
  }
  document.getElementById("input_add_valas").value = kode
  document.getElementById("input_add_kurs").value = kurs
  buttonAddListBatal()
}

function buttonAddPickSales (kode, nama ) {
  console.log('buttonAddPickSales')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODESLS' , kode)

  }
  document.getElementById("input_add_kodesales").value = kode
  document.getElementById("input_add_namasales").value = nama
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickBackOffice (kode, nama ) {
  console.log('buttonAddPickBackOffice')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('Boffice' , kode)

  }
  document.getElementById("input_add_kodebackoffice").value = kode
  document.getElementById("input_add_namabackoffice").value = nama
  buttonAddListBatal()

  document.getElementById("input_add_kodebackoffice").scrollIntoView();

}

function unlockFormAddAdd () {
  document.getElementById("input_add_add_kodebarang").disabled = false;
  document.getElementById("buttonAddAddListBarang").disabled = false;
}

function cleanFormAddAdd () {
  document.getElementById("input_add_add_kodebarang").value = ''
  document.getElementById("input_add_add_namabarang").value = ''
  document.getElementById("input_add_add_qty").value = '0.00'
  document.getElementById("input_add_add_nosat").innerHTML = '<option value=0 selected>Pilih Satuan</option>'
  // document.getElementById("input_add_add_kodebarang").value = ''
  // document.getElementById("input_add_add_namabarang").value = ''
  // document.getElementById("input_add_add_nopnwpo").value = '-'
  // document.getElementById("input_add_add_qty").value = '0.00'
  // document.getElementById("input_add_add_nosat").innerHTML = '<option value=0 selected>Pilih Satuan</option>'
  // document.getElementById("input_add_add_satuanproduk").value = ''
  // document.getElementById("input_add_add_harga").value = '0.00'
  // document.getElementById("input_add_add_disc").value = '0.00'
  // document.getElementById("input_add_add_discrp").value = '0.00'
  // document.getElementById("input_add_add_discpersen1").value = '0.00'
  // document.getElementById("input_add_add_discpersen2").value = '0.00'
  // document.getElementById("input_add_add_discpersen3").value = '0.00'
  // document.getElementById("input_add_add_tambahkepo").value = 0


}

function lockFormAdd () {
  document.getElementById("input_add_keterangan").disabled = true;
  document.getElementById("buttonPlusTambahItem").hidden = true;
  document.getElementById("buttonAddListGudangAsal").hidden = true;
  document.getElementById("buttonAddListGudangTujuan").hidden = true;
}

function buttonShowHideHeader () 
{
  var modal = document.getElementById("modalBodyAddMainHeader");
  console.log($('#modalBodyAddMainHeader').css('display'))
  if($('#modalBodyAddMainHeader').css('display') === 'block') {
    modal.style.display = "none";
  } else {
    modal.style.display = "block";
  }
}

function buttonShowHideHeaderDetail () {
  var modal = document.getElementById("modalBodyDetailMainHeader");
  console.log($('#modalBodyDetailMainHeader').css('display'))
  if($('#modalBodyDetailMainHeader').css('display') === 'block') {
    modal.style.display = "none";
  } else {
    modal.style.display = "block";
  }
}

function unlockFormAdd () {
  document.getElementById("input_add_keterangan").disabled = false;
  document.getElementById("buttonPlusTambahItem").hidden = false;
  document.getElementById("buttonAddListGudangAsal").hidden = false;
  document.getElementById("buttonAddListGudangTujuan").hidden = false;
}

function cleanFormAdd () {
  document.getElementById("input_add_kodeGudangAsal").value = '-'
  document.getElementById("input_add_namaGudangAsal").value = ''
  document.getElementById("input_add_kodeGudangTujuan").value = '-'
  document.getElementById("input_add_namaGudangTujuan").value = ''
  document.getElementById("input_add_keterangan").value = ''
}

function buttonEdit (NOBUKTI) {
  tipeform = 'edit'
  console.log('buttonEdit' , NOBUKTI)

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').show();
  unlockFormAdd()

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let _token  = $("#_token").val()
  let oto = 1

  $.ajax({
    url: "{!! url('prtcekotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      oto = res[0].isOtorisasi
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

  if (oto == 1) {
    alertify.warning("Sudah diotorisasi")
    return
  }

  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListPelanggan').show();
  $('#modalBodyAddMain').show();
  refreshDataTableAdd(NOBUKTI)
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
}

function buttonAdd () {
  tipeform = 'add'
  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').hide();
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  dataTableAdd = []
  cleanFormAdd()
  cleanFormAddAdd()
  unlockFormAdd()
  setNewNoBukti()

  refreshDataTableAdd()

  $('#page1').hide();
  $('#page2').show();

}

function buttonCloseForm () {
  loadAll()
  $('#page3').hide();
  $('#page2').hide();
  $('#page1').show();

}

function buttonCloseFormDetail () {
  $('#page3').hide();
  $('#page1').show();

}

function submitAdd () 
{

  let alamatpelanggan = $("#input_add_alamatsupplier").val();
  console.log(alamatpelanggan)
  let catatan = $("#input_add_keterangan").val();
  console.log(catatan)

}

function buttonAddMainHeader() {
  $('.showhidemodalbodyaddmain').hide();
  $('#modalBodyAddMainHeader').show();
  // $('#buttonAddListPelanggan').hide();
}

function buttonAddMainItems() {
  $('.showhide').hide();
  $('.showhidemodalbodyaddmain').hide();
  $('#modalBodyAddMainItems').show();
}

function buttonDetailMainHeader() {
  $('.showhidemodalbodydetailmain').hide();
  $('#modalBodyDetailMainHeader').show();
  // $('#buttonDetailListPelanggan').hide();
}

function buttonDetailMainItems() {
  $('.showhide').hide();
  $('.showhidemodalbodydetailmain').hide();
  $('#modalBodyDetailMainItems').show();
}


function buttonDetail (NOBUKTI) {
  tipeform = 'detail'
  console.log('buttonEdit' , NOBUKTI)

  lockFormAdd()

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').show();

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let _token  = $("#_token").val()
  let oto = 1

  $.ajax({
    url: "{!! url('prtcekotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      oto = res[0].isOtorisasi
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

  if (oto == 1) {
    alertify.warning("Sudah diotorisasi")
    return
  }

  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListPelanggan').show();
  $('#modalBodyAddMain').show();
  refreshDataTableAdd(NOBUKTI)
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();

  setFormMode('detail')
}

function setFormMode(mode) {
  let title = "Form Permintaan Transfer Barang";
  if (mode === 'detail') title = "Detail Permintaan Transfer Barang";
  if (mode === 'edit') title = "Edit Permintaan Transfer Barang";

  document.getElementById("formTitle").innerText = title;

  const thAction = document.getElementById("thAction");
  const tdActions = document.querySelectorAll(".tdAction");

  const hideActions = (mode === 'detail');
  if (thAction) thAction.style.display = hideActions ? "none" : "";
  tdActions.forEach(td => td.style.display = hideActions ? "none" : "");
}



function refreshDataTableAdd (NOBUKTI) {

  console.log('refreshDataTableAdd' , NOBUKTI)
  if (!NOBUKTI) {
    
    // if(!dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="5">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()

    $.ajax({
      url: "{!! url('prtgetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: NOBUKTI
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)

        if (!res.list.length) {
          alertify.warning("Data habis")
          //  $("#form").modal('toggle')
          $('#page3').hide();
          $('#page2').hide();
          $('#page1').show();
        } else {
          dataHeaderAdd = res.list[0]
          dataTableAdd = res.list

          let rowTable = ""
          dataTableAdd.forEach((item, i) => {

            rowTable += 
            `<tr>
              <td>${item.KODEBRG}</td>
              <td>${item.NamaBrg}</td>
              <td class="text-right">${item.QNT2 ? parseFloat(item.QNT2).toFixed(2) : '0.00'}</td>
              <td class="text-center">${item.SAT_1}</td>
              <td class="text-center tdAction">
                ${tipeform == 'edit' ? 
                `<button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button>`
                : `-`
                }
              </td>
            </tr>`
          });

          if(!dataTableAdd.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="5">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_add").innerHTML = rowTable

          document.getElementById("input_add_nobukti").value = dataHeaderAdd.NOBUKTI
          document.getElementById("input_add_nourut").value = dataHeaderAdd.NOURUT
          document.getElementById("input_add_tanggal").value = formatDate(dataHeaderAdd.TANGGAL)
          document.getElementById("input_add_kodeGudangAsal").value = dataHeaderAdd.gdgAsal
          document.getElementById("input_add_namaGudangAsal").value = dataHeaderAdd.NamaGgdAsal
          document.getElementById("input_add_kodeGudangTujuan").value = dataHeaderAdd.gdgTujuan
          document.getElementById("input_add_namaGudangTujuan").value = dataHeaderAdd.NamaGgdTujuan
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.Keterangan
        }

      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })
  }
  console.log('refresh gagal')
}

function buttonAddDeleteItem (i) {
  console.log(i)

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  console.log(dataTableAdd[i])
  let dataDelete = dataTableAdd[i]

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + dataDelete.NamaBrg + ' ?',
      function() {

        let _token = $("#_token").val();
        let Choice = "D"

        let Nobukti = dataDelete.NOBUKTI
        let Urut = dataDelete.URUT

        $.ajax({
          url: "{!! url('prtspadd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
              Choice,
              Nobukti,
              NoUrut:'',
              Tanggal:'',
              Note:'',
              Urut,
              Kodebrg:'',
              GdgAsal:'',
              GdgTujuan:'',
              Sat_1:'',
              Sat_2:'',
              Qnt:0,
              QNt2:0,
              NoSat:0,
              Isi:''
              //IDUSEr
              //noPenyerahan

          },
          success: function(res) {
            console.log('delete nigga', res)
            loadAll()

            // lockFormAdd()
            $('.showhide').hide();

            refreshDataTableAdd(Nobukti)

            alertify.success('Berhasil menghapus item')

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

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join('-');
}


function formatAngka (angkaString) {
  console.log('formatAngka' , angkaString);
  let tempAngka = angkaString.split('.')
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
};

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
