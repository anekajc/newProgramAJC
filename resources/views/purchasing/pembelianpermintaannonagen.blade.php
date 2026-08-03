@extends('newmaster')
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
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<input type="hidden" id="periode_tahun" value="{{ $periode->tahun }}">
<input type="hidden" id="periode_bulan" value="{{ $periode->bulan }}">
<input type="hidden" id="akses_istambah" value="{{ $akses->ISTAMBAH }}">
<input type="hidden" id="akses_ishapus" value="{{ $akses->ISHAPUS }}">
<input type="hidden" id="akses_iskoreksi" value="{{ $akses->ISKOREKSI }}">
<input type="hidden" id="akses_iscetak" value="{{ $akses->ISCETAK }}">
<input type="hidden" id="akses_isotorisasi1" value="{{ $akses->IsOtorisasi1 }}">
<input type="hidden" id="akses_isbatal" value="{{ $akses->IsBatal }}">
<input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">

<link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}">

<div id="page1">

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Purchasing</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Permintaan Pembelian (Non-Agen)</span>
  </div> --}}

  
<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

      <div class="sp-toolbar">
        <div class="row" style="width: 100%; margin: 0;">
          <div class="col-2">
            <button class="btn btn-primary" onclick="buttonAdd()">+ Tambah PR</button>
          </div>

          <div class="col-2">
            <div class="form-group">
              <input type="date" style="height: 40px" class="form-control text-center" id="input_tanggalawal" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
            </div>
          </div>

          <div class="col-2">
            <div class="form-group">
              <input type="date" style="height: 40px" class="form-control text-center" id="input_tanggalakhir" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
            </div>
          </div>

          <div class="col-2">
            <div class="form-group">
              <select id="input_filter" style="height: 40px" class="form-control form-select mb-2" aria-label=".form-select-lg example">
                <option value="2" selected>Semua PR</option>
                <option value="0">PR Belum Otorisasi</option>
                <option value="1">PR Sudah Otorisasi</option>
              </select>
            </div>
          </div>

          <div class="col-2 text-left">
            <div class="form-group">
              <button class="btn btn-success btn-lg" type="button" title="Details" onclick="buttonFilter()">
                <i class="bi bi-search"></i>
              </button>
              <button class="btn btn-secondary btn-lg" type="button" title="Details" onclick="buttonHeaderTable()">
                <i class="bi bi-table"></i>
              </button>
            </div>
          </div>

          <div class="col-2">
            <div class="sp-search-wrap">
              <i class="bi bi-search sp-search-icon"></i>
              <input type="text" id="tabel_filter_visual">
            </div>
          </div>
        </div>
      </div>

        <div class="table-outer">
            <div class="table-wrap">
              <table class="tb" id="tabel">
                <thead>
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Actions</th>
                    @for ($i = 0; $i < count($headertableheader); $i++)
                    @if ($isshown[$i] == 1)
                      <th style="padding: 4px 12px;" scope="col">{{ $headertableheader[$i] }}</th>
                      @endif
                  @endfor
                    <th style="padding: 4px 12px;" scope="col">Authorized</th>
                    <th style="padding: 4px 12px;" scope="col">User Oto</th>
                    <th style="padding: 4px 12px;" scope="col">Tanggal Oto</th>
                  </tr>
                </thead>
                <tbody id="tabel_data" class="text-right">
                  
        @foreach ($listData2 as $item)
          <tr>
            <td class="text-center">
                <div class="action-buttons-wrap">
              <button class="btn-action-sm btn-action-warning" title="Details" onclick="buttonDetail('{{ $item[0]->NoBukti }}')">
                <i class="bi bi-info"></i>
              </button>

              @if ($item[0]->IsOtorisasi1 == 1)
              <button class="btn-action-sm btn-action-danger" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('{{ $item[0]->NoBukti }}', '{{ $item[0]->IsOtorisasi1 }}')"><i class="bi bi-key"></i></button>
              <button class="btn-action-sm btn-action-primary" title="Print" onclick="submitPrint('{{ $item[0]->NoBukti }}')">
                <i class="bi bi-printer"></i>
              </button>
              @else
              <button class="btn-action-sm btn-action-info" title="Otorisasi" onclick="buttonOtorisasi('{{ $item[0]->NoBukti }}', '{{ $item[0]->IsOtorisasi1 }}')"><i class="bi bi-key"></i></button>

              <button class="btn-action-sm btn-action-success" title="Edit" onclick="buttonEdit('{{ $item[0]->NoBukti }}')">
                            <i class="bi bi-pen"></i>
                          </button>

              @endif

              </div>
            </td>

            @for ($i = 0; $i < count($headertableheader); $i++)
            @if ($isshown[$i] == 1)
            @if ($isnumeric[$i] == 0)
              <td>{{ $item[0]->{$headertablevalue[$i]} }}</td>
          @elseif ($isnumeric[$i] == 1)
          <td style="text-align: right;">{{ number_format($item[0]->{$headertablevalue[$i]}->QNT, 2, '.', ',') }}</td>
          @elseif ($isnumeric[$i] == 2)
              <td>{{ $item[0]->{$headertablevalue[$i]} ? date("Y/m/d", strtotime($item[0]->{$headertablevalue[$i]})) : '' }}</td>
          @endif

          @endif
            @endfor

            <td class="text-center">
              @if ($item[0]->IsOtorisasi1 == 0)
                <span class="text-danger"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></span>
              @else
                <span class="text-success"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></span>
              @endif
            </td>
            <td>{{ $item[0]->OtoUser1 }}</td>
            <td>{{ $item[0]->TglOto1 ? date("Y/m/d", strtotime($item[0]->TglOto1)) : '' }}</td>
          </tr>
            @endforeach

              </tbody>
              </table>
            </div>
        </div>

</div>

</div>

<!-- start modal add -->

<div id="page2" class="container-fluid" style="display:none;" >
  <div class="page-title">
    <div class="row">
      <div class="col-6 text-left">
    Permintaan Pembelian Non-Agen</div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="
        height: 30px;
            margin-top: 20px;
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

    </div>
  <div class="row">
    <!-- <div class="col-6 text-left">
      <h1>Form Pembelian Non-Agen</h1>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="
        height: 30px;
            margin-top: 20px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonCloseForm()">Close</button>
    </div> -->
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <div class="row">
            <!-- <input type="hidden" id="input_kodegroup" value="" /> -->
            <input type="hidden" class="form-control" id="input_add_nourut" placeholder="No Urut" disabled>
          <div class="col-md-4">
            <div class="row">
            <div class="col-md-3" style="margin-top:5px;">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>
          </div>
          </div>
          <div class="col-md-4">
            <div class="row">
            <div class="col-md-3" style="margin-top:5px;">
              <div class="form-group">
                <label>Departemen</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <select id="input_add_kodedepartemen" class="form-control text-left" aria-label="Default select example">
                  <option selected value="" disabled>Pilih Dept</option>
                </select>
              </div>
            </div>
          </div>
          </div>
          <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-left" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>
            </div>
          </div>
          </div>
          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="buttonAddAddItem()" class="btn btn-secondary">Add Item</button>
        </div>
      </div>
    </div>
    <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_add" class="table table-bordered table-striped"  >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Sat</th>
                  <th scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add" class="text-left" >
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button class="btn btn-success btn-sm" type="button" title="Edit"><i class="bi bi-pen"></i></button>
                    <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
                    <button class="btn btn-primary btn-sm" type="button" title="Details"><i class="bi bi-list"></i></button>
                  </td>
              </tr>
              </tbody>
            </table>
          </div>
          {{-- <div class="text-right">
            <button type="button" class="btn btn-primary" style="
            height: 30px;
            margin-top: 20px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="submitAdd()">Submit</button>
        </div> --}}
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
    </div>
    <!-- ADD SUBGROUP -->
    <div id="addAddItem" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4 id="h4AddAddItem" style="margin-left:-35px;">Add Item</h4>
                <h4 id="h4AddEditItem" style="margin-left:-35px;">Edit Item</h4>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-3" style="margin-top:5px;">
                    <div class="form-group">
                      <label>Kode Barang</label>
                    </div>
                  </div>
                <div class="col-md-4">
                  <div class="input-group mb-3">
                  <input id="input_add_add_kodebarang" type="text" class="form-control text-left" placeholder="Kode Barang">
                  <button type="button" id="buttonAddListKodeBarang" onclick="buttonAddListKodeBarang()" class="btn btn-primary btn-sm rounded-end shadow-sm"><i class="bi bi-plus"></i></button>
                  </div>
                </div>
              </div>
            <div class="row" style="margin-top:-15px;">
              <div class="col-md-3" style="margin-top:5px;">
                <div class="form-group">
                <label>Nama Barang</label>
              </div>
              </div>
              <div class="col-md-8">
                <input id="input_add_add_keterangannama" type="text" class="form-control text-left" disabled>
              </div>
            </div>
              <div class="row" style="margin-top:-15px;">
                <div class="col-md-3" style="margin-top:5px;">
                  <div class="form-group">
                  <label>Quantity</label>
                </div>
                </div>
                <div class="col-md-3">
                  <input id="input_add_add_qnt" type="text" value=0.00 class="form-control text-right input-partial-number">
                </div>
                <div class="col-md-2" style="margin-top:5px;">
                  <label for="input_add_add_satuan">Satuan</label>
                </div>
                <div class="col-md-3">
                  <select id="input_add_add_satuan" class="form-control">
                    <option value="" disabled selected>Pilih Satuan</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-6">
            <div class="row">
              <div class="col-md-3" style="margin-top:5px;">
                <div class="form-group">
                  <label>Keterangan</label>
                </div>
              </div>
                <div class="col-md-8">
                  <textarea type="text" style="width: 100%; resize: none" rows=3  class="form-control text-left" id="input_add_add_keterangan"></textarea>
                </div>
            </div>
          </div>
          </div>
            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" style="
                height: 30px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="closeShowHideAdd()" >Batal</button>

                <button type="button" id="submitAddAdd" class="btn btn-primary" style="
                height: 30px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitAddAdd()">Submit Add</button>

                <button type="button" id="submitAddEdit" class="btn btn-primary btn-lg" style="
                height: 30px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitAddEdit()" style="display: none;">Submit Edit</button>
              </div>
            </div>
          </div>

    <!-- END ADD ADD -->

    <!-- ADD EDIT -->

    <div id="addEditItem" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Edit Item Kedua</h4>
              </div>
            </div>

            {{-- <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Ref SO</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_add_edit_refso" type="text" class="form-control" value="-" disabled>
              </div>
              <div class="col-1 text-right">

                <button type="button" disabled onclick="" disabled class="btn btn-primary" >+</button>
              </div>
              <div class="col-2">
                <div class="form-group">
                <label>No PO Cust</label>
              </div>
              </div>
              <div class="col-4">

                <input id="input_add_edit_nopocust" type="text" class="form-control" disabled>
              </div>
            </div> --}}
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Kode Barang</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_add_edit_kodebarang" type="text" class="form-control" disabled>
              </div>
              <div class="col-1 text-right">
                <button type="button" disabled onclick="" class="btn btn-primary">+</button>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Ket. Barang</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_add_edit_keterangannama" type="text" class="form-control text-left" disabled>
              </div>

            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Quantity</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_add_edit_qnt" type="number" value=0.00 class="form-control text-right">
              </div>
              <div class="col-md-2">
                <label for="input_add_edit_satuan">Satuan</label>
              </div>
              <div class="col-md-4">
                <select id="input_add_edit_satuan" class="form-control" name="satuan">
                  <option value="" selected disabled>Pilih Satuan</option>
                </select>
              </div>
            </div>
            <div class="row">


            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Keterangan</label>
              </div>
              </div>
              <div class="col-10">
                <input id="input_add_edit_keterangan" type="text" class="form-control">
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()">Batal</button>
                {{-- <button type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> --}}
              </div>
            </div>
          </div>

    <!-- END ADD EDIT -->

  </div>
    <!--   -->
</div>

</div> {{-- end page 2 --}}


<!-- End modal add-->

<!-- start modal list item add -->
<div class="modal fade" id="formAddListItem" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></h5>
        <button type="button" class="btn btn-sm btn-danger rounded-circle shadow-sm ms-auto"
          data-dismiss="modal" aria-label="Close"
          style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
          <span aria-hidden="true" style="font-size: 1.2rem; font-weight: bold;">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="container-fluid mt-4">

          <div class="row mb-2" style="margin-top:-30px;">
            <div class="col-12 d-flex justify-content-end" style="padding-right: 0px;">
              <div class="d-flex align-items-center">
                <label for="input_search_barang_all" class="me-2 mb-0">Search:</label>
                <input id="input_search_barang_all" type="text" class="form-control"
                  style="max-width: 250px;" onkeypress="searchBarangAll(event)">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="table-responsive">
            <table id="tabel_add_list_item" class="table table-bordered table-striped">
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Merk</th>
                  <th scope="col">Part Number</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_item" class="text-left">
                <tr>
                  <td class="text-center" colspan="5">Silakan ketik pencarian</td>
                </tr>
              </tbody>
            </table>
          </div>
          </div>

          {{-- <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-danger btn-lg"
              style="height: 30px; padding: 4px 12px; border-radius: 20px;
              font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="closeListItemAdd()">Close</button>
          </div> --}}

        </div>
      </div>

    </div>
  </div>
</div>
<!-- End modal list item add-->

<!-- start modal detail -->
<div id="page3" class="container-fluid" style="display:none;">
        <div class="row">
          <div class="col-6 text-left">
            <h2>Detail Pembelian Non-Agen</h2>
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-danger btn-lg" style="
            height: 30px;
            margin-top: 20px;
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
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-4">
              <div class="row">
                <div class="col-md-3" style="margin-top:5px;">
                  <div class="form-group">
                    <label>No Bukti</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Bukti" disabled>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="row">
                <div class="col-md-3" style="margin-top:5px;">
                  <div class="form-group">
                    <label>Departemen</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <select disabled id="input_detail_kodedepartemen" class="form-control" aria-label="Default select example">
                      <!-- <option selected value="" disabled>Pilih Dept</option> -->
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="row">
                <div class="col-md-3" style="margin-top:5px;">
                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_detail" class="table table-bordered table-striped"  >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Sat</th>
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
  </div>
  <div class="modal-footer">
    <!--   -->
    <!-- <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
  </div>
</div>

<div class="modal fade" id="formHeaderTable" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Table Setting</h5>
        <button type="button" class="btn btn-sm btn-danger rounded-circle shadow-sm ms-auto"
          data-dismiss="modal" aria-label="Close"
          style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
          <span aria-hidden="true" style="font-size: 1.2rem; font-weight: bold;">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="container-fluid mt-4">



          <div class="row">
            <div class="table-responsive">
            <table id="tabel_headertable" class="table table-bordered table-striped">
              <thead id="tabel_header_headertable" class="text-center bg-primary text-white">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">Kolom</th>
                  <th scope="col">Tampil</th>
                </tr>
              </thead>
              <tbody id="tabel_data_headertable" class="text-left">
                <tr>
                  <td class="text-center" colspan="3">Silakan ketik pencarian</td>
                </tr>
              </tbody>
            </table>
          </div>
          </div>



        </div>

        <div class="row ">
          <div class="col-md-12 text-right">
            <div class="row">
              <div class="col-md-12">

              </div>
            </div>

          <button type="button" class="btn btn-primary" onclick="saveHeaderTable()" class="btn btn-secondary"  >Save</button>
      </div>
      </div>
      </div>

    </div>
  </div>
</div>


</div>
</div>
<!-- End modal detail-->


@endsection

@section('js')
<script type="text/javascript">
let dataAddListItem = []
let dataRefresh = []

let dataTableAdd = []
let dataTableEdit = []

let dataEditListItem = []

let xisshown = []
let xheadertableheader = []
let xheadertablevalue = []
let xisnumeric = []



let tempAdd = {} /// kalau di so tempAddAdd
let tempEdit = {} //// kalau di so tempAddEdit
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}
let tipeform = ''
let tipeformitem = ''

jQuery(function($) {
  $('.input-partial-number').autoNumeric('init',
    {
      minimumValue : '0',
      // negativeSignCharacter: 'z'
     }
  );
});

$(document).ready(function(){
  // grid = new gridjs.Grid({
  //       search: true,
  //       pagination: true,
  //       sort: true,
  //       columns: [
  //           "Kode Barang",
  //           "Nama Barang",
  //           "Qty"
  //       ],
  //       data: []
  //   });
  //
  //   grid.render(document.getElementById('tabel_wrapper'));

//   $('#tabel').DataTable({
//     destroy: true,
//     responsive: true,
//     lengthChange: false,
//     paging: false,
//     autoWidth: false
// });

  $("#tabel2").DataTable({
    "lengthChange": false,
    "paging": false,
  });

  // === buat search barang di field inputan ===
  document.getElementById("input_add_add_kodebarang").addEventListener("keypress", function (e) {
    if (e.which == 13) {
      let search = this.value.trim();

      if (!search) {
        alertify.warning("Silakan ketik kode atau nama barang terlebih dahulu.");
        return;
      }

      if ($.fn.DataTable.isDataTable('#tabel_add_list_item')) {
        $('#tabel_add_list_item').DataTable().clear().destroy();
      }

      $('#tabel_data_add_list_item').empty().append(`
        <tr><td class="text-center" colspan="5">Mencari data...</td></tr>
      `);

      $.ajax({
        url: "{!! url('pembelianpermintaannonagenlistbarang') !!}",
        type: "get",
        async: false,
        data: {
          search: search,
          isagen: 0
        },
        success: function(res) {
          dataAddListItem = res;

          if (!res.length) {
            $('#formAddListItem').modal('show');
            $('#tabel_data_add_list_item').empty().append(`
              <tr><td class="text-center" colspan="5">Tidak ada data</td></tr>
            `);
            return;
          }

          if (res.length === 1) {
            buttonAddAddInsertItem(0);
            return;
          }

          $('#formAddListItem').modal('show');

          let rowTable = "";
          res.forEach((item, i) => {
            rowTable += `
              <tr>
                <td class="text-center">
                  <button class="btn btn-primary btn-sm" onclick="buttonAddAddInsertItem(${i})" type="button">
                    <i class="bi bi-plus"></i>
                  </button>
                </td>
                <td>${item.KODEBRG}</td>
                <td>${item.NAMABRG}</td>
                <td>${item.NAMAMERK ?? ''}</td>
                <td>${item.PartNumber ?? ''}</td>
              </tr>`;
          });

          $('#tabel_data_add_list_item').empty().append(rowTable);

          $('#tabel_add_list_item').DataTable({
            lengthChange: false,
            paging: false,
            searching: false
          });
        },
        error: function(err) {
          console.log(err);
          alertify.warning('Terjadi kesalahan, silakan refresh browser');
        }
      });
    }
  });

  console.log("ready");

  let wrapper = document.getElementById('tabel2_wrapper');

  if (wrapper) {

    let defaultRow = wrapper.querySelector(':scope > .row:first-child');
    if (defaultRow) {
      defaultRow.style.display = 'none';
    }

    let toolbarHtml = `<div class="row" style="margin-top: 10px">
      <div class="col-2">
        <button class="btn btn-primary" onclick="buttonAdd()">+ Tambah PR</button>
      </div>

      <div class="col-2">
        <div class="form-group">
          <input type="date" style="height: 40px" onchange="" class="form-control text-center" id="input_tanggalawal" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
        </div>
      </div>

      <div class="col-2">
        <div class="form-group">
          <input type="date" style="height: 40px" onchange="" class="form-control text-center" id="input_tanggalakhir" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
        </div>
      </div>

      <div class="col-2">
        <div class="form-group">
          <select id="input_filter" style="height: 40px" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
            <option value=2 selected>Semua PR</option>
            <option value=0>PR Belum Otorisasi</option>
            <option value=1>PR Sudah Otorisasi</option>
          </select>
        </div>
      </div>

      <div class="col-2 text-left">
        <div class="form-group">
          <button class="btn btn-success btn-lg" type="button" title="Details" onclick="buttonFilter()">
            <i class="bi bi-search"></i>
          </button>
          <button class="btn btn-secondary btn-lg" type="button" title="Details" onclick="buttonHeaderTable()">
            <i class="bi bi-table"></i>
          </button>
        </div>
      </div>
    </div>`;

    wrapper.insertAdjacentHTML('afterbegin', toolbarHtml);
  } else {
    console.error('#tabel2_wrapper not found — DataTables may not have initialized #tabel2 yet.');
  }

  // loadAll();
});

function saveHeaderTable () {
  let href = window.location.pathname.split('/').filter(Boolean)[1];
  let _token = $("#_token").val();
  // console.log()

  console.log(JSON.stringify(xisshown))
  console.log(JSON.stringify(xheadertableheader))
  console.log(JSON.stringify(xheadertablevalue))
  console.log(JSON.stringify(xisnumeric))
  console.log(href)
  $.ajax({
    url: "{!! url('saveheadertable') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      header : JSON.stringify(xheadertableheader) ,
       isnumber : JSON.stringify(xisnumeric) ,
       tipe : '',
         value : JSON.stringify(xheadertablevalue) ,
          isshown : JSON.stringify(xisshown) ,

        href : href
    },
    success: function(res) {
      loadAll()
        $("#formHeaderTable").modal('toggle')

    }})


}

function buttonChangeOrder (type = 0, index =0) {
  console.log("buttonChangeOrder")
  console.log(type , index)

  // let xisshown = []
  // let xheadertableheader = []
  // let xheadertablevalue = []
  // let xisnumeric = []
  if (type == 0) {
    //naikkin posisi -1
    let tempisshown =  xisshown[index]
    let tempheadertableheader =  xheadertableheader[index]
    let tempheadertablevalue =  xheadertablevalue[index]
    let tempisnumeric =  xisnumeric[index]

    xisshown[index] = xisshown[index - 1]
    xheadertableheader[index] = xheadertableheader[index - 1]
    xheadertablevalue[index] = xheadertablevalue[index - 1]
    xisnumeric[index] = xisnumeric[index - 1]

    xisshown[index - 1] = tempisshown
    xheadertableheader[index - 1] = tempheadertableheader
    xheadertablevalue[index - 1] = tempheadertablevalue
    xisnumeric[index - 1] = tempisnumeric
    refreshHeaderTable()
  } else {
    // nurunin posisi +1
    let tempisshown =  xisshown[index]
    let tempheadertableheader =  xheadertableheader[index]
    let tempheadertablevalue =  xheadertablevalue[index]
    let tempisnumeric =  xisnumeric[index]

    xisshown[index] = xisshown[index + 1]
    xheadertableheader[index] = xheadertableheader[index + 1]
    xheadertablevalue[index] = xheadertablevalue[index + 1]
    xisnumeric[index] = xisnumeric[index + 1]

    xisshown[index + 1] = tempisshown
    xheadertableheader[index + 1] = tempheadertableheader
    xheadertablevalue[index + 1] = tempheadertablevalue
    xisnumeric[index + 1] = tempisnumeric
    refreshHeaderTable()

  }
}

function onclickcheckboxheadertable (index) {

    if (document.getElementById(`headertable_checkbox${index}`).checked) {
      xisshown[index] = 1

    } else {
      xisshown[index] = 0

    }

    console.log(xisshown)
}

function refreshHeaderTable () {
  // let href = window.location.pathname.split('/').filter(Boolean)[1];
  // console.log(href)
  // let _token = $("#_token").val();
  //
  // $.ajax({
  //   url: "{!! url('getheadertable') !!}",
  //   type: "post",
  //   async: false,
  //   data: {
  //     _token : _token,
  //     href
  //   },
  //   success: function(res) {
  //       console.log('======xxxxx==========')
  //     console.log(res)
  //     console.log(JSON.parse(res.isshown))
  //     console.log(JSON.parse(res.headertableheader))
  //     console.log(JSON.parse(res.headertablevalue))
  //     console.log(JSON.parse(res.isnumeric))
  //     xisshown = JSON.parse(res.isshown)
  //     xheadertableheader = JSON.parse(res.headertableheader)
  //     xheadertablevalue = JSON.parse(res.headertablevalue)
  //     xisnumeric = JSON.parse(res.isnumeric)
      let rowTable = ''
      console.log('len' , xheadertableheader.length)
      xheadertableheader.forEach((item, i) => {
        console.log(i)
        rowTable +=  `<tr>`
        rowTable += `<td class="text-center">`
        if (i != 0) {
          console.log("button down")
          rowTable +=  `<button class="btn btn-primary btn-sm" title="" onclick="buttonChangeOrder(0 , ${i})"><i class="bi bi-arrow-up"></i></button>`
        } else {
            rowTable +=  `<button class="btn btn-secondary btn-sm" title="" onclick=""><i class="bi bi-arrow-up" disabled></i></button>`
        }
        if (i != xheadertableheader.length - 1 ) {

            console.log("button up")
            rowTable += `<button class="btn btn-primary btn-sm" title="" onclick="buttonChangeOrder(1 , ${i})"><i class="bi bi-arrow-down"></i></button>`
        } else {
            rowTable += `<button class="btn btn-secondary btn-sm" title="" onclick=""><i class="bi bi-arrow-down" disabled></i></button>`
        }

        rowTable += `</td>`
          rowTable+= `  <td>${item}</td>
            <td class="text-center"><input class="" type="checkbox" value="" onchange='onclickcheckboxheadertable(${i})' id="headertable_checkbox${i}"></td>





        `
        rowTable += `</tr>`
      });



      document.getElementById("tabel_data_headertable").innerHTML = rowTable
      console.log(xisshown)
      xheadertableheader.forEach((item, i) => {
        console.log(xisshown[i])
        console.log(Number(xisshown[i]))
        if (Number(xisshown[i])) {

          document.getElementById(`headertable_checkbox${i}`).checked = true
        }

      });
      // $("#formHeaderTable").modal('toggle')



  //   }
  // })

}

function buttonHeaderTable () {
    let href = window.location.pathname.split('/').filter(Boolean)[1];
    console.log(href)
    let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('getheadertable') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        href
      },
      success: function(res) {
          console.log('======xxxxx==========')
        console.log(res)

        if (res.isparsed == 0) {
          xisshown = JSON.parse(res.isshown)
          xheadertableheader = JSON.parse(res.headertableheader)
          xheadertablevalue = JSON.parse(res.headertablevalue)
          xisnumeric = JSON.parse(res.isnumeric)


        } else {
          xisshown = res.isshown
          xheadertableheader = res.headertableheader
          xheadertablevalue = res.headertablevalue
          xisnumeric = res.isnumeric


        }
        // console.log(JSON.parse(res.isshown))
        // console.log(JSON.parse(res.headertableheader))
        // console.log(JSON.parse(res.headertablevalue))
        // console.log(JSON.parse(res.isnumeric))

        refreshHeaderTable()
        $("#formHeaderTable").modal('toggle')

      }
    })

}

function buttonFilter() {
  let href = window.location.pathname.split('/').filter(Boolean)[1];

  let _token   = $("#_token").val();
  let tglawal  = $("#input_tanggalawal").val();
  let tglakhir = $("#input_tanggalakhir").val();
  let isoto    = $("#input_filter").val() || 2;

  let laheadertable      = [];
  let laheadertablevalue = [];
  let laisnumeric        = [];
  let laisshown          = [];
  let dataRefreshOutstanding2 = [];

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenloadall') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      tglawal,
      tglakhir,
      isoto,
      href
    },
    success: function (res) {
      laisshown          = res.isshown;
      laheadertable      = res.headertableheader;
      laheadertablevalue = res.headertablevalue;
      laisnumeric        = res.isnumeric;

      dataRefreshOutstanding2 = res.listData1 || [];
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });

  // ===================== BUILD HEADER =====================
  let headerTable = '<tr><th style="padding: 4px 12px;" scope="col">Actions</th>';

  laheadertable.forEach((label, i) => {
    if (laisshown[i]) {
      headerTable += `<th style="padding: 4px 12px;" scope="col">${label}</th>`;
    }
  });

  headerTable += `
    <th style="padding: 4px 12px;" scope="col">Authorized</th>
    <th style="padding: 4px 12px;" scope="col">User Oto</th>
    <th style="padding: 4px 12px;" scope="col">Tanggal Oto</th>
  </tr>`;

  // ===================== BUILD ROWS =====================
  let rowTable2 = "";

  dataRefreshOutstanding2.forEach((item) => {
    let data = item[0];
    let isOtorisasi = Number(data.IsOtorisasi1) || 0;

    let statusOtorisasi = isOtorisasi === 0
      ? '<td class="text-center text-danger"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
      : '<td class="text-center text-success"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>';

    let actionButtons = isOtorisasi === 1
      ? `
        <button class="btn-action-sm btn-action-danger" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${data.NoBukti}', '${data.IsOtorisasi1}')"><i class="bi bi-key"></i></button>
        <button class="btn-action-sm btn-action-primary" title="Print" onclick="submitPrint('${data.NoBukti}')"><i class="bi bi-printer"></i></button>
      `
      : `
        <button class="btn-action-sm btn-action-info" title="Otorisasi" onclick="buttonOtorisasi('${data.NoBukti}', '${data.IsOtorisasi1}')"><i class="bi bi-key"></i></button>
        <button class="btn-action-sm btn-action-success" title="Edit" onclick="buttonEdit('${data.NoBukti}')"><i class="bi bi-pen"></i></button>
      `;

    let formattedOto = data.TglOto1
      ? new Date(data.TglOto1).toLocaleDateString('en-CA') // yyyy-mm-dd, swap format below if you need y/m/d with slashes
      : "";

    let dynamicCells = "";
    laheadertable.forEach((_, i) => {
      if (Number(laisshown[i]) !== 1) return;

      let key = laheadertablevalue[i];

      if (laisnumeric[i] == 0) {
        dynamicCells += `<td>${data[key] ?? ''}</td>`;
      } else if (laisnumeric[i] == 1) {
        dynamicCells += `<td style="text-align: right;">${formatAngka(data[key])}</td>`;
      } else {
        dynamicCells += `<td>${data[key] ? formatDate(data[key]) : ''}</td>`;
      }
    });

    rowTable2 += `
      <tr>
        <td class="text-center">
          <div class="action-buttons-wrap">
            <button class="btn-action-sm btn-action-warning" title="Details" onclick="buttonDetail('${data.NoBukti}')"><i class="bi bi-info"></i></button>
            ${actionButtons}
          </div>
        </td>
        ${dynamicCells}
        ${statusOtorisasi}
        <td>${data.OtoUser1 || '-'}</td>
        <td>${formattedOto}</td>
      </tr>
    `;
  });

  document.getElementById("tabel_header").innerHTML = headerTable;
  document.getElementById("tabel_data").innerHTML = rowTable2;

  $("#tabel").DataTable({ "lengthChange": false, "paging": false });
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


function loadAll () {
  let href = window.location.pathname.split('/').filter(Boolean)[1];

  console.log('loadall xx')
  let _token = $("#_token").val();
  // $('#tabel').DataTable().destroy();
  $('#tabel').DataTable().destroy();
  // let _token  = $("#_token").val()
  let tglawal = $("#input_tanggalawal").val()
  let tglakhir = $("#input_tanggalakhir").val()
  let isoto = $("#input_filter").val()
  let dataRefresh = [];
  let dataRefreshOutstanding2 = [];
  if (!isoto) {
    isoto = 2
  }
  console.log({tglawal,
  tglakhir,
  isoto,})

  // let laisshown = []
  let laheadertable = []

  let laheadertablevalue = []
  let laisnumeric = []
  let laisparsed = 0
  let laisshown = []
  $.ajax({
    url: "{!! url('pembelianpermintaannonagenloadall') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      tglawal,
      tglakhir,
      isoto,
      href
    },
    success: function(res) {
      console.log('loadall res')
      console.log(res)
      // laisshown = []

      console.log("asd")
      // if (res.isparsed == 0) {
      //   laisshown = JSON.parse(res.isshown)
      //   laheadertable = JSON.parse(res.headertableheader)
      //   laheadertablevalue = JSON.parse(res.headertablevalue)
      //   laisnumeric = JSON.parse(res.isnumeric)
      //
      //
      // } else {
        laisshown = res.isshown
        laheadertable = res.headertableheader
        laheadertablevalue = res.headertablevalue
        laisnumeric = res.isnumeric


      // }

      console.log(laisshown)
      // console.log(laheadertableheader)
      console.log(laheadertablevalue)
      console.log(laisnumeric)
      console.log("edw")
      dataRefresh = res.listData2 || [];
      dataRefreshOutstanding2 = res.listData1 || [];
      console.log("BOOO")
    }
  });
  console.log("BUUU")

  let headerTable = '<tr><th style="padding: 4px 12px;" scope="col">Actions</th>'
  console.log("Cek error")
  console.log(laheadertable)
  laheadertable.forEach((item, i) => {
    if (laisshown[i]) {

      headerTable += `<th style="padding: 4px 12px;" scope="col">${laheadertable[i]}</th>`
    }
  });
  headerTable += `<th style="padding: 4px 12px;" scope="col">Authorized</th>
  <th style="padding: 4px 12px;" scope="col">User Oto</th>
  <th style="padding: 4px 12px;" scope="col">Tanggal Oto</th>
</tr>`

  // ===================== TABEL BELUM OTORISASI =====================
  // let rowTable = "";
  // dataRefresh.forEach((item) => {
  //   let isOtorisasi = Number(item[0].isOtorisasi1) || 0;
  //   let statusOtorisasi = isOtorisasi === 0
  //     ? '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
  //     : '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>';
  //
  //   let date = item[0].Tanggal ? new Date(item[0].Tanggal) : null;
  //   let formattedDate = date ? `${date.getFullYear()}/${("0"+(date.getMonth()+1)).slice(-2)}/${("0"+date.getDate()).slice(-2)}` : "";
  //
  //   rowTable += `
  //     <tr>
  //       <td class="text-center">
  //         <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item[0].NoBukti}')"><i class="bi bi-info"></i></button>
  //         <button class="btn btn-success btn-sm" type="button" onclick="buttonEdit('${item[0].NoBukti}')"><i class="bi bi-pen"></i></button>
  //         <button class="btn btn-info btn-sm" type="button" onclick="buttonOtorisasi('${item[0].NoBukti}', '${item[0].isOtorisasi1}')"><i class="bi bi-key"></i></button>
  //       </td>
  //       <td>${item[0].NoBukti}</td>
  //       <td>${formattedDate}</td>
  //       ${statusOtorisasi}
  //     </tr>
  //   `;
  // });
  //
  // document.getElementById("tabel_data").innerHTML = rowTable;
  // $("#tabel").DataTable({ "lengthChange": false, "paging": false });

  // ===================== TABEL SUDAH OTORISASI =====================
  let rowTable2 = "";

  console.log("BEEE")

  dataRefreshOutstanding2.forEach((item) => {
  const isOtorisasi = Number(item[0].IsOtorisasi1) || 0;

  const statusOtorisasi = isOtorisasi == 0
    ? '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
    : '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>';
  const buttonOtorisasi = isOtorisasi == 1
  ? `<button class="btn btn-danger btn-sm"  type="button" onclick="buttonBatalOtorisasi('${item[0].NoBukti}', '${item[0].isOtorisasi1}')"><i class="bi bi-key"></i></button><button class="btn btn-primary btn-sm"  type="button" onclick="submitPrint('${item[0].NoBukti}')"><i class="bi bi-printer"></i></button>`
  : `<button class="btn btn-info btn-sm" title="Otorisasi" onclick="buttonOtorisasi('${item[0].NoBukti }', '${item[0].IsOtorisasi1 }')"><i class="bi bi-key"></i></button>

  <button class="btn btn-success btn-sm" title="Edit" onclick="buttonEdit('${item[0].NoBukti }')">
                <i class="bi bi-pen"></i>
              </button>`

  const tglInput = item[0].Tanggal   ? new Date(item[0].Tanggal)  : null;
  const tglOto   = item[0].TglOto1   ? new Date(item[0].TglOto1)  : null;

  const formattedInput = tglInput ? `${tglInput.getFullYear()}/${("0"+(tglInput.getMonth()+1)).slice(-2)}/${("0"+tglInput.getDate()).slice(-2)}` : "";
  const formattedOto   = tglOto   ? `${tglOto.getFullYear()}/${("0"+(tglOto.getMonth()+1)).slice(-2)}/${("0"+tglOto.getDate()).slice(-2)}`     : "";

  rowTable2 += `
    <tr>
      <td class="text-center">
        <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item[0].NoBukti}')"><i class="bi bi-info"></i></button>
        ${buttonOtorisasi}
        `
        // if (Number(item[0].IsOtorisasi1) == 1) {
        //   rowTable2 += `
        //   <button class="btn btn-danger btn-sm" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${item[0].NoBukti }', '${item[0].IsOtorisasi1 }')"><i class="bi bi-key"></i></button>
        //   <button class="btn btn-primary btn-sm" title="Print" onclick="submitPrint('${item[0].NoBukti }')">
        //     <i class="bi bi-printer"></i>
        //   </button>
        //   `
        // } else {
        //   rowTable2 += `
        //   <button class="btn btn-info btn-sm" title="Otorisasi" onclick="buttonOtorisasi('${item[0].NoBukti }', '${item[0].IsOtorisasi1 }')"><i class="bi bi-key"></i></button>
        //
        //   <button class="btn btn-success btn-sm" title="Edit" onclick="buttonEdit('${item[0].NoBukti }')">
        //                 <i class="bi bi-pen"></i>
        //               </button>
        //   `
        //
        // }
        console.log("BAAAAAAAAAAAAA")
      rowTable2 += `</td>`
      console.log(laheadertable)
      laheadertable.forEach((itemx, i) => {
        console.log('foreach')
        console.log(laisshown , i)
        console.log(laisshown[1])
        console.log(Number(laisshown[i]) == 1)
        if(Number(laisshown[i]) == 1) {
          console.log("masuk isshown")
          console.log(laisnumeric[i])
          if (laisnumeric[i] == 0 )
          {

              // ${laheadertablevalue[i]}
              // let xvalx = laheadertablevalue[i]
              let xvalx = itemx

            rowTable2 += `
              <td>${item[0][xvalx] }</td>

            `

          } else if (laisnumeric[i] == 1 ) {
            console.log("masuk isnum 1")

            console.log(laheadertablevalue)
            console.log(laheadertablevalue[i])

            let xvalx = laheadertablevalue[i]
            console.log('xvalx',xvalx)
            rowTable2 += `<td style="text-align: right;">${ formatAngka(item[0][xvalx])}</td>
  `

          } else {
            console.log("masuk isnum else")

            console.log(laheadertablevalue)
            console.log(laheadertablevalue[i])
            let xvaluedate = item[0][laheadertablevalue[i]];
  rowTable2 += `<td>${xvaluedate ? formatDate(xvaluedate) : ""}</td>`;

          }
        }


      });


      // <td>${item[0].NoBukti}</td>
      // <td>${formattedInput}</td>
      rowTable2 += `${statusOtorisasi}
      <td>${item[0].OtoUser1 || '-'}</td>
      <td>${formattedOto}</td>
    </tr>
    `;
  })
  document.getElementById("tabel_header").innerHTML = headerTable;

  document.getElementById("tabel_data").innerHTML = rowTable2;
  $("#tabel").DataTable({ "lengthChange": false, "paging": false });
}

function buttonOtorisasi (nobukti, isOtorisasi) {
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
    url: "{!! url('pembelianpermintaannonagenupdateotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      otorisasi: 1
    },
    success: function (res) {
      if (res > 0) {
        alertify.success('Berhasil otorisasi');
        loadAll();
      } else {
        alertify.warning('Gagal otorisasi');
      }
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
    }
  });
}


function buttonBatalOtorisasi (nobukti, isOtorisasi) {

console.log( $("#akses_isbatal").val());
let akses = $("#akses_isbatal").val();
  if (!Number(akses)) {
    alertify.warning('No access batal');
    return;
  }

  if (Number(isOtorisasi) === 0) {
    alertify.warning('Belum diotorisasi');
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
        url: "{!! url('pembelianpermintaannonagenupdatebatalotorisasi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti,
          otorisasi: 0,
          pket :value
        },
        success: function (res) {
          if (res > 0) {
            alertify.success('Berhasil batal otorisasi');
            loadAll();
          } else {
            alertify.warning('Gagal batal otorisasi');
          }
        },
        error: function (err) {
          console.log(err);
          alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
        }
      });
  },
  function() {
    alertify.error("Action cancelled");
  }
);




}


function submitAddEdit () {
    console.log('submitAddEdit');

    let checkDate = new Date($("#input_add_tanggal").val());
    let periode_bulan = document.getElementById("periode_bulan").value;
    let periode_tahun = document.getElementById("periode_tahun").value;

    if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() + 1) !== Number(periode_bulan)) {
        alertify.warning("Tanggal tidak sesuai periode");
        return;
    }

    let jmlrecord = (tipeform === "edit") ? 1 : 0;

    let _token = $("#_token").val();
    let choice = "U";
    let nobukti = $("#input_add_nobukti").val();
    let nourut = $("#input_add_nourut").val();
    let tanggal = $("#input_add_tanggal").val();
    let kodebarang = $("#input_add_add_kodebarang").val();
    let keterangannama = $("#input_add_add_keterangannama").val();
    let satuan = $("#input_add_add_satuan").val();
    let qnt = formatAngkaVal($("#input_add_add_qnt").val())
    let keterangan = $("#input_add_add_keterangan").val();
    let kodedepartemen = $("#input_add_kodedepartemen").val();

    if (!kodebarang || !satuan || qnt <= 0 || !kodedepartemen) {
        alertify.warning("Lengkapi semua data wajib");
        return;
    }

    let barang = tempEdit;
    let isi = 0;
    let nosat = parseInt(satuan);
    let qnt1 = 0;

    console.log('Satuan dipilih:', satuan);
    console.log('SAT1:', tempEdit.SAT1, 'ISI1:', tempEdit.ISI1);
    console.log('SAT2:', tempEdit.SAT2, 'ISI2:', tempEdit.ISI2);
    console.log('SAT3:', tempEdit.SAT3, 'ISI3:', tempEdit.ISI3);

    if (nosat === 1) {
    qnt1 = qnt * tempEdit.ISI1;
    satuan = tempEdit.SAT1;
    isi = parseFloat(String(tempEdit.ISI1).replace(/\./g,''));
    } else if (nosat === 2) {
        qnt1 = qnt * tempEdit.ISI2;
        satuan = tempEdit.SAT2;
        isi = parseFloat(String(tempEdit.ISI2).replace(/\./g,''));
    } else if (nosat === 3) {
        qnt1 = qnt * tempEdit.ISI3;
        satuan = tempEdit.SAT3;
        isi = parseFloat(String(tempEdit.ISI3).replace(/\./g,''));
    } else {
        alertify.warning("Satuan tidak valid");
        return;
    }

    console.log('Nosat:', nosat, 'Isi:', isi, 'Qnt1:', qnt1);

    keterangannama = keterangannama.replace(/["']/g, '');
    keterangan = keterangan ? keterangan.replace(/["']/g, '') : '';

    console.log("Data yang akan dikirim:", {
        choice, nobukti, nourut, tanggal, kodedepartemen,
        kodebarang, keterangannama, satuan, qnt, nosat, isi,
        keterangan, urut: tempEdit.Urut, jmlrecord
    });

    $.ajax({
        url: "{!! url('pembelianpermintaannonagenspadd') !!}",
        type: "POST",
        async: false,
        data: {
            _token,
            choice,
            nobukti,
            nourut,
            tanggal,
            kodedepartemen,
            isjasa: 0,
            pagen: 0,
            pjasa: 0,
            kodebarang,
            keterangannama,
            satuan,
            qnt,
            keterangan,
            nosat,
            isi,
            urut: tempEdit.Urut,
            isclose: 0,
            isclosed: 0,
            noso: '',
            urutso: 0,
            nopocust: '',
            jmlrecord
        },
        success: function(res) {
            console.log('respoedit', res);
            loadAll();
            $('.showhide').hide();
            refreshDataTableAdd(nobukti);
            alertify.success('Berhasil edit item');
        },
        error: function(err) {
            console.log('Error saat submit:', err);
            alertify.warning('Terjadi kesalahan, silakan refresh browser');
        }
    });
}

function buttonAddEditItem (index) {
  tipeformitem = 'edit';
  let _token = $("#_token").val();
  console.log('buttonAddEditItem');

  $('.showhide').hide();
  document.getElementById("buttonAddListKodeBarang").disabled = true;
  document.getElementById("input_add_add_kodebarang").disabled = true;

  tempEdit = dataTableAdd[index];
  tempIndexEdit = index;

  // Isi dropdown satuan
  let selectOption = '<option value=0 selected>Pilih Satuan</option>';
  if (tempEdit.SAT1) {
    selectOption += `<option value=1>${tempEdit.SAT1} - ${Number(String(tempEdit.ISI1||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  }
  if (tempEdit.SAT2) {
    selectOption += `<option value=2>${tempEdit.SAT2} - ${Number(String(tempEdit.ISI2||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  }
  if (tempEdit.SAT3) {
    selectOption += `<option value=3>${tempEdit.SAT3} - ${Number(String(tempEdit.ISI3||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  }
  document.getElementById("input_add_add_satuan").innerHTML = selectOption;

  // Isi input
  document.getElementById("input_add_add_kodebarang").value = tempEdit.KodeBrg || '';
  document.getElementById("input_add_add_keterangannama").value = tempEdit.NamaBrg || '';
  document.getElementById("input_add_add_qnt").value = parseFloat(tempEdit.Qnt || 0).toFixed(2);
  document.getElementById("input_add_add_keterangan").value = tempEdit.Keterangan || '';
  document.getElementById("input_add_add_satuan").value = String(tempEdit.NoSat || 0);

  // Tampilkan mode edit
  $('#h4AddAddItem').hide();
  $('#h4AddEditItem').show();
  $('#submitAddAdd').hide();
  $('#submitAddEdit').show();
  $('#addAddItem').show();

  document.getElementById("input_add_add_kodebarang").scrollIntoView();
}

function buttonEdit (nobukti) {




  tipeform = 'edit'
  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  $('.showhide').hide();
  lockFormAdd()

  // document.getElementById("input_add_tanggal").disabled = true

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenlistdepartemen') !!}",
    type: "get",
    async: false,
    data: {
      // isagen: 0
    },
    success: function(res) {
      console.log('dept' , res)
      let selectDept = ``
      res.forEach((item, i) => {
        selectDept += `<option value="${item.KDDEP}">${item.KDDEP} - ${item.NMDEP}</option>`
      });

      document.getElementById("input_add_kodedepartemen").innerHTML = selectDept
    }})

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspdetail') !!}",
    type: "get",
    async: false,
    data: {
      nobukti
    },
    success: function(res) {
      console.log(res)
    //seharusnya ini pakai data table add
      dataTableAdd = res
  }})
  let rowTable = ``
  dataTableAdd.forEach((item, i) => {
    rowTable += `<tr>
    <td>${item.KodeBrg}</td>
    <td>${item.NamaBrg}</td>
    <td class="text-right">${formatAngka(item.Qnt)}</td>
    <td class="text-center">${item.Satuan}</td>
    <td class="text-center">
      <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem('${i}')"><i class="bi bi-pen"></i></button>
      <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem('${i}')"><i class="bi bi-trash"></i></button>
    </td>
    </tr>`
  });

  let date = new Date(dataTableAdd[0].Tanggal);
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  date1 = date.getFullYear()+"-"+(month)+"-"+(day);
  $('#input_add_tanggal').val(date1)

  document.getElementById("tabel_data_add").innerHTML  = rowTable
  document.getElementById("input_add_nobukti").value  = dataTableAdd[0].NoBukti
  document.getElementById("input_add_nourut").value  = dataTableAdd[0].Nourut
  document.getElementById("input_add_kodedepartemen").value  = dataTableAdd[0].KDDep
  // document.getElementById("input_detail_tanggal").value  = res[0].Tanggal
  $('#page1').hide();
  $('#page2').show();
}



function buttonAddDeleteItem (index) {

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  let data = dataTableAdd[index]

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + data.KodeBrg + ' ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"
        let nourut = $("#input_add_nourut").val();
        let nobukti = $("#input_add_nobukti").val();
        let tanggal = $("#input_add_tanggal").val();
        let isjasa = 0
        let pagen = 0
        let pjasa = 0
        let urut = data.Urut
        let kodebarang = data.KodeBrg
        let qnt = data.Qnt
        let nosat = data.NoSat
        let satuan = data.Satuan
        let isi = data.Isi
        let keterangan = data.Keterangan
        let isclose = 0
        let isclosed =0
        let kddep = "TEMP"
        let keterangannama = data.NamaBrg
        let noso = data.NOSO
        let urutso = data.URUTSO
        let nopocust = data.NoSOCust
        let jmlrecord = 0

        $.ajax({
          url: "{!! url('pembelianpermintaannonagenspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nourut,
            nobukti,
            tanggal,
            isjasa,
            pagen,
            pjasa,
            urut,
            kodebarang,
            qnt,
            nosat,
            satuan,
            isi,
            keterangan,
            isclose,
            isclosed,
            kddep,
            keterangannama,
            noso,
            urutso,
            nopocust,
            jmlrecord,
          },
          success: function(res) {
            alertify.success("Item sudah di delete");
            refreshDataTableAdd(nobukti)
            loadAll()


        }})
      }
    ,function(){
      console.log('no')
    });
}


function buttonDetail (nobukti) {
  $.ajax({
    url: "{!! url('pembelianpermintaannonagenlistdepartemen') !!}",
    type: "get",
    async: false,
    data: {
      // isagen: 0
    },
    success: function(res) {
      console.log('dept' , res)
      let selectDept = ``
      res.forEach((item, i) => {
        selectDept += `<option value="${item.KDDEP}">${item.KDDEP} - ${item.NMDEP}</option>`
      });

      document.getElementById("input_detail_kodedepartemen").innerHTML = selectDept

    }})

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspdetail') !!}",
    type: "get",
    async: false,
    data: {
      nobukti
    },
    success: function(res) {

      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `<tr>
        <td>${item.KodeBrg}</td>
        <td>${item.NamaBrg}</td>
        <td class="text-right">${formatAngka(item.Qnt)}</td>
        <td class="text-center">${item.Satuan}</td>
        </tr>`
      });

      let date = new Date(res[0].Tanggal);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#input_detail_tanggal').val(date1)

      document.getElementById("tabel_data_detail").innerHTML  = rowTable
      document.getElementById("input_detail_nobukti").value  = res[0].NoBukti
      document.getElementById("input_detail_kodedepartemen").value = res[0].KDDep
      // document.getElementById("input_detail_tanggal").value  = res[0].Tanggal

  }})
  $("#page3").show();
  $("#page1").hide();
}


function setNewNoBukti () {
  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
}


function buttonAdd () {


let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {


    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1

      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return

}



  tipeform = 'add'
  $('.showhide').hide();
  cleanFormAdd()
  unlockFormAdd();

  let akses = $("#akses_istambah").val();
  console.log("Akses" , akses)
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  // pembelianpermintaannonagenspnobukti
  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
    dataTableAdd = []
    cleanFormAdd()

    $.ajax({
      url: "{!! url('pembelianpermintaannonagenlistdepartemen') !!}",
      type: "get",
      async: false,
      data: {
        // isagen: 0
      },
      success: function(res) {
        console.log('dept' , res)
        let selectDept = `<option selected value="" disabled>Pilih Dept</option>`
        res.forEach((item, i) => {
          selectDept += `<option value="${item.KDDEP}">${item.KDDEP} - ${item.NMDEP}</option>`
        });

        document.getElementById("input_add_kodedepartemen").innerHTML = selectDept

      }})

  refreshDataTableAdd()
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
}

function closeListItemAdd () {
  $("#formAddListItem").modal('toggle')
  // document.getElementById("input_add_add_kodebarang").value = dataAddListItem[i].KODEBRG
  // document.getElementById("input_add_add_keterangannama").value = dataAddListItem[i].NAMABRG
  var modal = document.getElementById("page2");
  modal.style.display = "block";

}

function closeListItemEdit () {
  $("#formEditListItem").modal('toggle')
  // document.getElementById("input_add_add_kodebarang").value = dataAddListItem[i].KODEBRG
  // document.getElementById("input_add_add_keterangannama").value = dataAddListItem[i].NAMABRG
  var modal = document.getElementById("formEdit");
  modal.style.display = "block";
}

function buttonCloseForm () {
  $('#page2').hide();
  $('#page3').hide();
  $('#page1').show();
}

function buttonAddListKodeBarang () {
  if ($.fn.DataTable.isDataTable('#tabel_add_list_item')) {
    $('#tabel_add_list_item').DataTable().destroy();
  }

  $('#tabel_data_add_list_item').empty().append(`
    <tr>
      <td class="text-center" colspan="5">Silakan ketik pencarian</td>
    </tr>`);

  $('#formAddListItem').modal('show');
}

// Reset input search ketika modal ditutup
$('#formAddListItem').on('hidden.bs.modal', function () {
  $('#input_search_barang_all').val('');
});


function searchBarangAll (e) {
  if (e.which == 13) {
    let search = $("#input_search_barang_all").val().trim();

    if (!search) {
      if ($.fn.DataTable.isDataTable('#tabel_add_list_item')) {
        $('#tabel_add_list_item').DataTable().clear().destroy();
      }

      $('#tabel_data_add_list_item').empty().append(`
        <tr><td class="text-center" colspan="5">Silakan ketik pencarian</td></tr>
      `);
      return;
    }

    if ($.fn.DataTable.isDataTable('#tabel_add_list_item')) {
      $('#tabel_add_list_item').DataTable().clear().destroy();
    }

    $('#tabel_data_add_list_item').empty().append(`
      <tr><td class="text-center" colspan="5">Mencari data...</td></tr>
    `);

    $.ajax({
      url: "{!! url('pembelianpermintaannonagenlistbarang') !!}",
      type: "get",
      async: false,
      data: {
        search,
        isagen : 0
      },
      success: function (res) {
        dataAddListItem = res;
        let rowTable = "";

        if (!res.length) {
          rowTable = `<tr><td class="text-center" colspan="5">Tidak ada data</td></tr>`;
          $('#tabel_data_add_list_item').empty().append(rowTable);
          return;
        }

        res.forEach((item, i) => {
          rowTable += `
            <tr>
              <td class="text-center">
                <button class="btn btn-primary btn-sm" onclick="buttonAddAddInsertItem(${i})" type="button">
                  <i class="bi bi-plus"></i>
                </button>
              </td>
              <td>${item.KODEBRG}</td>
              <td>${item.NAMABRG}</td>
              <td>${item.NAMAMERK ?? ''}</td>
              <td>${item.PartNumber ?? ''}</td>
            </tr>`;
        });

        $('#tabel_data_add_list_item').empty().append(rowTable);

        $('#tabel_add_list_item').DataTable({
          lengthChange: false,
          paging: false,
          searching: false
        });
      },
      error: function (err) {
        console.log(err);
        alertify.warning('Terjadi kesalahan, silakan refresh browser');
      }
    });
  }
}

function buttonAddAddInsertItem (i) {
  console.log('index:', i);
  console.log('dataAddListItem:', dataAddListItem);

  if (!dataAddListItem[i]) {
    alertify.warning('Data tidak valid');
    return;
  }

  let item = dataAddListItem[i];

  $('#input_add_add_kodebarang').val(item.KODEBRG);
  $('#input_add_add_keterangannama').val(item.NAMABRG);

  let satuanOptions = '';
  if (item.SAT1) satuanOptions += `<option value="${item.SAT1}">${item.SAT1} - ${Number(String(item.ISI1||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  if (item.SAT2) satuanOptions += `<option value="${item.SAT2}">${item.SAT2} - ${Number(String(item.ISI2||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  if (item.SAT3) satuanOptions += `<option value="${item.SAT3}">${item.SAT3} - ${Number(String(item.ISI3||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  $('#input_add_add_satuan').html(satuanOptions);

  $('#formAddListItem').modal('hide');

  setTimeout(() => {
    document.getElementById("input_add_add_qnt").focus();
    document.getElementById("input_add_add_qnt").select();
  }, 300);
}

  function submitAddAdd () {
    console.log('submitAddAdd');

    let checkDate = new Date($("#input_add_tanggal").val())

    let periode_bulan = document.getElementById("periode_bulan").value
    let periode_tahun = document.getElementById("periode_tahun").value

    if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

        alertify.warning("Tanggal tidak sesuai periode");
        return
    }

    let jmlrecord = 0
    if (tipeform == 'edit'){
      jmlrecord = 1
    }
    let _token = $("#_token").val();
    let choice = "I";
    let nobukti = $("#input_add_nobukti").val();
    let nourut = $("#input_add_nourut").val();
    let tanggal = $("#input_add_tanggal").val();
    let kodebarang = $("#input_add_add_kodebarang").val();
    let keterangannama = $("#input_add_add_keterangannama").val();
    let satuan = $("#input_add_add_satuan").val();
    let qnt = formatAngkaVal($("#input_add_add_qnt").val())
    let keterangan = $("#input_add_add_keterangan").val();
    let kodedepartemen = $("#input_add_kodedepartemen").val();

    if (!kodebarang || !satuan || qnt <= 0 || !kodedepartemen) {
        alertify.warning("Lengkapi semua data wajib");
        return;
    }

    let barang = dataAddListItem.find(item => item.KODEBRG === kodebarang);
    if (!barang) {
        alertify.warning("Barang tidak ditemukan di daftar");
        return;
    }

    let isi = 0;
    let nosat = 0;
    if (satuan === barang.SAT1) {
        isi = parseFloat(String(barang.ISI1).replace(/\./g,''));
        nosat = 1;
    } else if (satuan === barang.SAT2) {
        isi = parseFloat(String(barang.ISI2).replace(/\./g,''));
        nosat = 2;
    } else if (satuan === barang.SAT3) {
        isi = parseFloat(String(barang.ISI3).replace(/\./g,''));
        nosat = 3;
    } else {
        alertify.warning("Satuan tidak valid");
        return;
    }

    // untuk mencegah SQL error (hapus tanda kutip)
    keterangannama = keterangannama.replace(/["']/g, '');
    keterangan = keterangan ? keterangan.replace(/["']/g, '') : '';

    console.log({
          _token,
          choice,
          nobukti,
          nourut,
          tanggal,
          kodedepartemen,
          isjasa: 0,
          pagen: 0,
          pjasa: 0,
          kodebarang,
          keterangannama,
          satuan,
          qnt,
          keterangan,
          nosat,
          isi,
          urut: 0,
          isclose: 0,
          isclosed: 0,
          noso: '',
          urutso: 0,
          nopocust: '',
          jmlrecord})

    $.ajax({
        url: "{!! url('pembelianpermintaannonagenspadd') !!}",
        type: "POST",
        async: false,
        data: {
          _token,
          choice,
          nobukti,
          nourut,
          tanggal,
          kodedepartemen,
          isjasa: 0,
          pagen: 0,
          pjasa: 0,
          kodebarang,
          keterangannama,
          satuan,
          qnt,
          keterangan,
          nosat,
          isi,
          urut: 0,
          isclose: 0,
          isclosed: 0,
          noso: '',
          urutso: 0,
          nopocust: '',
          jmlrecord
        },
        success: function (res) {
            console.log('respoadd', res)
            if (res == 1) {
                loadAll()
                tipeform = 'edit'
                cleanFormAddAdd()
                refreshDataTableAdd(nobukti)
                alertify.success('Berhasil menambah item')
              }
              else if (res == 2) {
                setNewNoBukti()
                alertify.warning('Nobukti telah direfresh silahkan submit ulang')
              }
            },
            error: function (err) {
              console.log(err)
              alertify.warning('Terjadi kesalahan silahkan refresh browser')
              }
          });
      }


function buttonEditAddItem () {

let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {


    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1

      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return

}

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  $('.showhideedit').hide();

  tempEditAdd = {}
  document.getElementById("input_edit_add_refso").value = "-"
  document.getElementById("input_edit_add_nopocust").value = ""
  document.getElementById("input_edit_add_kodebarang").value = ""
  document.getElementById("input_edit_add_keterangannama").value = ""
  document.getElementById("input_edit_add_qnt").value = "0.00"
  document.getElementById("input_edit_add_keterangan").value = ""
  document.getElementById("input_edit_add_satuan").innerHTML = '<option value=0 selected>Pilih Satuan</option>'

  $('#editAddItem').show();
}

function buttonAddAddItem () {
  tipeformitem = 'add'
  $('.showhide').hide();
  tempAdd = {}
  // document.getElementById("inlineRadio1").checked = false
  // document.getElementById("input_add_add_refso").value = "-"
  // document.getElementById("input_add_add_nopocust").value = ""
  document.getElementById("buttonAddListKodeBarang").disabled = false;
  document.getElementById("input_add_add_kodebarang").disabled = false;
  document.getElementById("input_add_add_kodebarang").value = ""
  document.getElementById("input_add_add_keterangannama").value = ""
  document.getElementById("input_add_add_qnt").value = "0.00"
  document.getElementById("input_add_add_keterangan").value = ""
  // Menentukan isi dropdown berdasarkan tempAdd
  let satuanOptions = `<option value="" selected disabled>Pilih Satuan</option>`;

  if (tempAdd.SAT1) {
    satuanOptions += `<option value="${tempAdd.SAT1}">[1] ${tempAdd.SAT1} - ${Number(String(tempAdd.ISI1||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  }
  if (tempAdd.SAT2) {
    satuanOptions += `<option value="${tempAdd.SAT2}">[2] ${tempAdd.SAT2} - ${Number(String(tempAdd.ISI2||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  }
  if (tempAdd.SAT3) {
    satuanOptions += `<option value="${tempAdd.SAT3}">[3] ${tempAdd.SAT3} - ${Number(String(tempAdd.ISI3||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  }
  document.getElementById("input_add_add_satuan").innerHTML = satuanOptions;


  $('#h4AddAddItem').show();
  $('#h4AddEditItem').hide();
  $('#submitAddAdd').show();
  $('#submitAddEdit').hide();
  $('#addAddItem').show();
}

function closeShowHideAdd () {
  $('.showhide').hide();
}

function closeShowHideEdit () {
  $('.showhideedit').hide();
}


function refreshDataTableAdd (NOBUKTI = "") {
  console.log('refreshDataTableAdd', NOBUKTI)

  if (!NOBUKTI) {
    let rowTable = `<tr>
      <td class="text-center" colspan="8">Belum ada barang</td>
    </tr>`
    document.getElementById("tabel_data_add").innerHTML = rowTable
    return
  }

  let _token = $("#_token").val()

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log('res', res)

      if (!res.length) {
        alertify.warning("Data habis")
        $('#page3').hide();
        $('#page2').hide();
        $('#page1').show();
        return
      }

      dataTableAdd = res
      dataHeaderAdd = res[0]

      let rowTable = ""
      dataTableAdd.forEach((item, i) => {
        rowTable += `
          <tr>
            <td>${item.KodeBrg}</td>
            <td>${item.NamaBrg}</td>
            <td>${formatAngka(item.Qnt)}</td>
            <td>${item.Satuan}</td>
            <td class="text-center">
              <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button>
            </td>
          </tr>`
      });

      if (!dataTableAdd.length) {
        rowTable = '<tr><td colspan="6" class="text-center">Belum ada item</td></tr>';
      }

      document.getElementById("tabel_data_add").innerHTML = rowTable
      // document.getElementById("input_add_nobukti").value = dataHeaderAdd.nobukti
      // document.getElementById("input_add_kodedepartemen").value = dataHeaderAdd.kodedepartemen
    }
  })
}


function cleanFormAddAdd (){
  document.getElementById("input_add_add_kodebarang").value = ''
  document.getElementById("input_add_add_keterangannama").value = ''
  document.getElementById("input_add_add_qnt").value = '0.00'
  document.getElementById("input_add_add_satuan").innerHTML = '<option value=0 selected>Pilih Satuan</option>'
  document.getElementById("input_add_add_keterangan").value = ''
}

function cleanFormAdd (){
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_kodedepartemen").value = ''
}

function lockFormAdd (){
  document.getElementById("input_add_tanggal").disabled = true
  document.getElementById("input_add_kodedepartemen").disabled = true
}

function unlockFormAdd () {
  document.getElementById("input_add_kodedepartemen").disabled = false
}


function formatAngka (angkaString) {
      if (!Number(angkaString)) {
        return '0.00'
      }
      angkastring = parseFloat(angkaString).toFixed(2)

      let tempAngka = angkaString.split('.')

      if (tempAngka[0][0] == '-') {
        let temp2=''

        let tempAngka1 = tempAngka[0].split('-')
        for (let i = 0; i < tempAngka1[1].length; i++) {
          if (i != 0 && i % 3 == 0) {
            temp2 = ',' + temp2
          }
          temp2 = tempAngka1[1][tempAngka1[1].length - i -1] + temp2

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

      }
      temp1 += '.' + tempAngka[1]
      return temp1
    }



function submitPrint (nobukti) {

  let _token = $('#_token').val()

  let namaTtdCetak = ''


    $.ajax({
      url: "{!! url('purchaseRequestCetak') !!}",
      type: "get",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {

        dataPrint = res

        console.log(dataPrint)

      }
    })

    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=7)
    {
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


    const now = new Date()
    const jamCetak = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })

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

      .border-bottom {
        border: bottom;
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
    hdr = `<div style="display: flex; justify-content: space-between; width: 100%">

                  <div class="pe-1" style="width: 60%">
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 15%; margin-top: 15px">
                        `+ imageContent +`
                      </div>
                      <div class="pb-1 ps-3" style="width: 85%">
                        <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                        <div class="pb-1" style="width: 100%">JL. PRAMUKA NO. 63 RT. 11 BANJARMASIN 70249</div>
                        <div class="pb-1" style="width: 100%">TELP : 0511 - 3269593 | FAX : 0511 - 3272142</div>
                        <div class="pb-1" style="width: 100%">E-Mail : spl@indo.net</div>
                      </div>
                    </div>
                  </div>

                  <div style="width: 40%">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">SURAT PERMINTAAN PEMBELIAN</h2>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">No. SPP</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].Nobukti}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">No. Ref SO</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NOSO}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">NO. PO Cust</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NoSOCust || ''}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Tanggal</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${tanggalOnly}</div>
                    </div>
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 100%; height: 225px; max-height: 225px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    <td colspan=6>Harap disediakan bahan / barang sebagai berikut :</td>
                  </tr>
                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 5%">Kode Barang</td>
                    <td class="text-center" style="width: 30%">Nama Barang</td>
                    <td class="text-center" style="width: 5%">Sat</td>
                    <td class="text-center" style="width: 5%">PR</td>
                    <td class="text-center" style="width: 5%">Keterangan</td>
                  </tr>
                </thead> `;

    let z = 0
    let jumlahTotal = 0
    let diskonTotal = 0
    let subTotal = 0
    let ppnTotal = 0
    let totalTotal = 0

    let tempPrintStr = ``
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
  tempPrintStr += `
    <tr>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 2%;">${z+1}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.kodebrg}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 30%;">${itemSub.NamaBrg}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; width: 5%; text-align: center;">${itemSub.Sat}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; width: 5%; text-align: right;">${itemSub.Qnt ? parseFloat(itemSub.Qnt).toFixed(2) : ''}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.keterangan}</td>
    </tr>`;
  z++;
});

// Fill remaining empty rows Ã¯Â¿Â½ table is 225px, each row ~24px, header ~24px = ~8 total slots
const maxRows = 7;
const fillerCount = Math.max(0, maxRows - item.length);
for (let f = 0; f < fillerCount; f++) {
  tempPrintStr += `
    <tr style="height: 24px;">
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
    </tr>`;
}

  tempPrintStr += `</tbody>`;
  tempPrintStr += `</table>`;

  tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 10px;">

  <div style="width: 40%; font-family: sans-serif; font-size: 10px;">
    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">
      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">Dibuat Oleh,</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Nama</p>
        </td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Tgl</p>
        </td>
      </tr>
    </table>
  </div>

  <div style="width: 60%; font-family: sans-serif; font-size: 10px;">

    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">
      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">Disetujui oleh,</td>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">Diterima oleh,</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Nama</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Nama</p>
        </td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Tgl</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Tgl</p>
        </td>
      </tr>
    </table>
  </div>

</div>
`


    tempPrintStr += `</div>`
  });


      tempPrintStr +=  `</body></html>`

    w=window.open(' ')
    w.document.write(tempPrintStr)
    w.print()
    w.close()
    }


      function formatAngkaVal (angka) {
        return Number(angka.split(',').join(''))
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
    // setActiveTab(true);

    // buat ganti tab
    // tabHome.addEventListener('click', function () {
    //   setActiveTab(true);
    // });
    //
    // tabProfile.addEventListener('click', function () {
    //   setActiveTab(false);
    // });
  </script>

@endsection
