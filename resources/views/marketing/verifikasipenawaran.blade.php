@extends('newmaster')
@section('buttons')

@endsection


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

{{-- tampilan search bar modal add pelanggan --}}
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
</style>

<style>
  #tabel_add_list_lokasi_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;

  }
  #tabel_add_list_lokasi_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
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
</style>

<style>
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

{{-- report-table.css powers the .tb-report/.toolbar/.search-inp/.table-wrap classes
     used to reskin page1 below -- same file so.blade.php/penawaranso.blade.php link
     for their own reskinned tables. No JS from that pair (report-table.js,
     headerEngine.js) is loaded here -- this page keeps its existing server-rendered
     @for-loop table and GET-reload search exactly as before, only restyled. --}}
<link rel="stylesheet" href="{{ asset('css/report-table.css') }}?v={{ filemtime(public_path('css/report-table.css')) }}">

<style>
  /* Tab bar container, matching purchaseOrder.blade.php's .card.tab-card + .custom-tabs. */
  .tab-card {
    display: block !important;
    align-items: flex-start !important;
    padding: 0 !important;
    border: none !important;
    margin-bottom: 6px !important;
  }
  .tab-card .card-body {
    padding: 5px 10px !important;
  }
  .custom-tabs {
    display: inline-flex;
    justify-content: flex-start;
    align-items: center;
    gap: 2px;
    background-color: #f1f3f5;
    border-radius: 20px;
    padding: 3px;
  }
  .custom-tabs .nav-link {
    display: inline-block !important;
    padding: 5px 16px !important;
    font-size: 0.75rem !important;
    border: none;
    border-radius: 17px;
    color: #495057;
    background: transparent;
    font-weight: 600;
    transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
  }
  .custom-tabs .nav-link:hover {
    background: transparent;
    color: #007bff;
  }
  .custom-tabs .nav-link.active {
    background: #007bff;
    border-color: #007bff;
    color: #fff;
    box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
  }

  /* #tabel body row look + circular Actions-column buttons, copied from
     purchaseOrder.blade.php's own #tabel styling -- .tb (from report-table.css)
     only styles the header, not the body rows or the existing plain
     .btn-primary/.btn-danger buttons already in the Actions column. */
  #tabel.tb tbody td {
    padding: 10px 14px;
    font-size: 15px;
    border-bottom: 1px solid #F1F5F9;
  }
  #tabel.tb tbody tr:nth-of-type(odd) {
    background-color: #fbfbfc;
  }
  #tabel.tb tbody tr:hover {
    background-color: #f5f3ff;
  }
  #tabel td:first-child .btn {
    width: 30px;
    height: 30px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 7px;
    font-size: 13px;
    border: 1px solid transparent;
    box-shadow: none;
    transition: all .12s ease;
  }
  #tabel td:first-child .btn:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
  }
  #tabel td:first-child .btn-primary {
    color: #2563eb; border-color: #cfdcff; background: #e8edff;
  }
  #tabel td:first-child .btn-danger {
    color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
  }
</style>
@endsection

@section('content')



<div id="page1" class="mainpage container-fluid">



<!-- <button onclick="loadAll()">tes</button> -->
<!-- Standalone page title dropped to match purchaseOrder.blade.php's page1, which
     doesn't show one either (its own is commented out) -- the tab pill below already
     labels this section.
<div class="container-fluid">
  <div class="row" >
    <div class="col-6 text-left">
      <h2 style="margin-top: -85px">Verifikasi Penawaran</h2>
    </div>
    <div class="col-6 text-right">
    </div>
  </div>
</div>
-->

<div id="printContainer" style="display:none">


</div>
<div id="contentContainer" class="container-fluid" >
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
  <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
  <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  {{-- Tab bar in its own card, matching purchaseOrder.blade.php's .card.tab-card --
       a dedicated card holding only the tab nav, separate from the table below it
       (which gets its own report-table.css card via .tb-report .table-wrap). Only
       one real tab exists here ("Verifikasi") -- nav-profile-tab elsewhere in this
       file's JS is dead/never wired to a second pane, so it isn't given one either. --}}
  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">
          Verifikasi
        </a>
      </div>
    </div>
  </div>

  {{-- No outer Bootstrap .card wrapping the table -- .tb-report/.table-wrap below
       already render as their own card-like panel (report-table.css), the same way
       so.blade.php/penawaranso.blade.php's tables sit directly in their tab-content
       with no surrounding .card. --}}
  <div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    <div class="tb-report main">
      {{-- Toolbar matching purchaseOrder.blade.php's .po-toolbar, rebuilt with
           report-table.css's own .toolbar/.search-inp/.action-group/.btn-load
           classes (the same ones so.blade.php/penawaranso.blade.php's toolbars
           use) instead of a bare Bootstrap row. Still a plain GET form -- same
           field ids/names/values and submit behavior as before, only restyled. --}}
      <form method="GET" class="toolbar" style="margin-bottom:10px;">
        <input type="text"
            id="search_customer"
            name="search_customer"
            value="{{ request('search_customer') }}"
            class="search-inp"
            placeholder="Search Customer">
        <input type="text"
            id="search_nobukti"
            name="search_nobukti"
            value="{{ request('search_nobukti') }}"
            class="search-inp"
            placeholder="Search No. Bukti">
        <input type="text"
            id="search_barang"
            name="search_barang"
            value="{{ request('search_barang') }}"
            class="search-inp"
            placeholder="Search Barang">
        <div class="action-group">
          <button type="submit" name="search" value="1" class="btn-load">
            <i class="bi bi-search"></i> Cari
          </button>
        </div>
      </form>

      <div class="table-outer">
      <div class="table-wrap">
          <table id="tabel" class="tb">
            <thead class="text-center">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Actions</th>
                    <th style="padding: 4px 12px;" scope="col">No. Bukti</th>
                    <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                    <th style="padding: 4px 12px;" scope="col" style="min-width: 150px">Customer</th>
                    <th style="padding: 4px 12px;" scope="col">PIC</th>
                    <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                    <th style="padding: 4px 12px;" scope="col">Verifikasi</th>
                    <th style="padding: 4px 12px;" scope="col">Tgl Verf</th>
                    <th style="padding: 4px 12px;" scope="col">User Verf</th>
                    <th style="padding: 4px 12px;" scope="col">Harga</th>
                    <th style="padding: 4px 12px;" scope="col">Tipe</th>
                    <th style="padding: 4px 12px;" scope="col">Merk</th>

                    <th style="padding: 4px 12px;" scope="col">Ket</th>
                    <th style="padding: 4px 12px;" scope="col">Qnt</th>
                    <th style="padding: 4px 12px;" scope="col">Qnt SO</th>
                    <th style="padding: 4px 12px;" scope="col">Sisa</th>

                  </tr>
                </thead>


                <tbody id="tabel_data" class="text-left" >
                  @for ($i = 0; $i < count($tempOutstanding); $i++)
                <tr>
                  <td class="text-center">
                      @if ($tempOutstanding[$i]->IsVerf == 1)
                      <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempOutstanding[$i]->NOBUKTI }}', '{{ $tempOutstanding[$i]->Urut }}')"><i class="bi bi-dash"></i></button>
                      @else
                      <button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('{{ $tempOutstanding[$i]->NOBUKTI }}' , '{{ $tempOutstanding[$i]->Urut }}')"><i class="bi bi-plus"></i></button>

                      @endif
                  </td>
                  <td>{{ $tempOutstanding[$i]->NOBUKTI }}</td>
                  <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i]->TANGGAL)) !!}</td>
                  <td>{{ $tempOutstanding[$i]->NAMACUSTSUPP }}</td>
                          <td>{{ $tempOutstanding[$i]->NAMAPIC }}</td>
                          <td>{{ $tempOutstanding[$i]->KODEBRG }}</td>
                          <td>{{ $tempOutstanding[$i]->NAMABRG }}</td>
                          @if ($tempOutstanding[$i]->IsVerf)
                          <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                          @else
                          <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                          @endif
                          <td>{!! $tempOutstanding[$i]->TglVerf ? date("Y/m/d", strtotime($tempOutstanding[$i]->TANGGAL)) : '' !!}</td>

                  <td>{{ $tempOutstanding[$i]->UserVerf }}</td>
                  <td class="text-right">{{ number_format($tempOutstanding[$i]->HARGA , 2 ,'.' , ',') }}</td>

                  <td>{{ $tempOutstanding[$i]->tipe }}</td>
                  <td>{{ $tempOutstanding[$i]->NamaMerk }}</td>
                  <td>{{ $tempOutstanding[$i]->ketdet }}</td>
                  <td class="text-right">{{ number_format($tempOutstanding[$i]->QNT , 2 ,'.' , ',') }}</td>
                  <td class="text-right">{{ number_format($tempOutstanding[$i]->QntSO , 2 ,'.' , ',') }}</td>
                  <td class="text-right">{{ number_format($tempOutstanding[$i]->Sisa , 2 ,'.' , ',') }}</td>




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
{{-- closes #page1 --}}
</div>




<div class="container-fluid mainpage" id="page2" style="display: none">

  <div id="" class="">
  <!-- <div class="modal-header">


      <h5 class="modal-title" id="">Add</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div> -->

  <div class="container-fluid">


    <!-- <div id="qrcode"></div> -->
    <div class="row" style="margin-top: -60px">
      <div class="col-6 text-left">
        <h1>Form Invoice</h1>
      </div>
      <div class="col-6 text-right">
        <!-- <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button> -->
        <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>

      </div>
    </div>
  <!-- <button onclick="loadAll()">tes</button> -->
  </div>




  <div id="" class="">
  <div class="">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid mt-4">
      <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
      <div class="row">

        <div class="col-md-3">
          <div class="row">


          <div class="col-md-4">
            <div class="form-group">
              <label>Customer</label>
            </div>
          </div>
          <!-- <div class="col-md-3 text-right">
            <div class="form-group">
          </div>
        </div> -->
          <div class="col-md-8">
            <div class="form-group input-group">
              <input type="text" class="form-control" id="input_add_kodecustomer" placeholder="" disabled>
              <button class="btn btn-primary btn-sm text-right" id="buttonAddListCustomer" onclick="buttonAddListCustomer()"><i class="bi bi-plus"></i></button>
            </div>
          </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Tgl</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}"  >
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
            <div class="row">


            </div>
        </div>

      </div>
    </div>
    <div class="container-fluid">

      <hr/ style="margin-top: -8px">
    </div>
    <div class="container-fluid mt-4">
      <div class="row">



        <div class="col-md-3">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_namacustomer"  disabled></textarea>
              </div>
            </div>
          </div>
            <div class="row" style="margin-top: -10px">

            <div class="col-12">

              <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_add_alamatcustomer"  disabled></textarea>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="row">


            <!-- <div class="col-md-6">
              <div class="row"> -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Tipe PPN</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <select id="input_add_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangePPN('ppn' , 'input_add_tipeppn')">
                      <option value=0 selected>None</option>
                      <option value=1 >Exclude</option>

                      <option value=2 >Include</option>
                    </select>
                  </div>
                </div>

              </div>
                <div class="row" style="margin-top: -10px">


                <div class="col-md-4">
                  <div class="form-group">
                    <label>Sales</label>
                  </div>
                </div>
                <!-- <div class="col-md-3">
                  <div class="row">

                  <div class="form-group">
                </div>
              </div>

                </div> -->
                <div class="col-md-8">
                  <div class="form-group input-group">
                    <input type="text" class="form-control" id="input_add_sales" value="" disabled >
                    <input type="hidden" class="form-control text-center" id="input_add_kodesales" value="" disabled >
                    <button class="btn btn-primary btn-sm text-right" id="buttonAddListSales" onclick="buttonAddListSales()"><i class="bi bi-plus"></i></button>
                  </div>
                </div>

              </div>
                <div class="row" style="margin-top: -10px">



              <!-- </div>

            </div> -->

            <!-- <div class="col-md-6">
              <div class="row"> -->






                    <div class="col-12">
                      <div class="form-group">
                        <label>Lokasi Penerima</label>
                      </div>
                    </div>

                  </div>
                    <div class="row">


                    <div class="col-12" style="margin-top: -10px">
                      <div class="form-group input-group">
                        <input type="hidden" class="form-control text-center" id="input_add_kodelokasipenerima" value="" disabled >
                        <input type="text" class="form-control" id="input_add_lokasipenerima" value="" disabled >
                        <button class="btn btn-primary btn-sm text-right" id="buttonAddListLokasiPenerima" onclick="buttonAddListLokasiPenerima()"><i class="bi bi-plus"></i></button>
                      </div>
                    </div>
                  <!-- </div>
                </div> -->


              <!-- </div>

            </div> -->




          </div>

        </div>

        <div class="col-md-3">
          <div class="row">
            <!-- <div class="col-md-6">
              <div class="row"> -->

              <!-- </div>
            </div> -->


            <!-- <div class="col-md-6">
              <div class="row"> -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>No PO</label>
                  </div>
                </div>


                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_nopo" value="" onblur="onChangeHeader('pono' , 'input_add_nopo' , 'No PO')"  >
                  </div>
                </div>
              <!-- </div>
            </div> -->

          </div>
            <div class="row" style="margin-top: -10px">


            <!-- <div class="col-md-6">
              <div class="row"> -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Uang Muka</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="number" class="form-control text-right" id="input_add_uangmuka" value="0.00"  onblur="onChangeUangMuka('nuangmuka' , 'input_add_uangmuka' , 'uang muka')">
                  </div>
                </div>

              </div>
                <div class="row" style="margin-top: -10px">


                <div class="col-12">
                  <div class="form-group">
                    <label>Catatan</label>
                  </div>
                </div>

              </div>
                <div class="row" style="margin-top: -10px">


                <div class="col-12">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_catatan" value="" onblur="onChangeHeader('footnote' , 'input_add_catatan', 'catatan')" >
                  </div>
                </div>
              <!-- </div>
            </div> -->

          </div>

        </div>





        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Pembayaran</label>
              </div>
            </div>


            <div class="col-md-8">
              <div class="form-group">
                <select id="input_add_pembayaran" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangePembayaran()">
                  <option value=0 selected>Tunai</option>
                  <option value=1 >Kredit</option>
                </select>
              </div>
            </div>
          <!-- </div>
        </div> -->

      </div>
        <div class="row" style="margin-top: -10px">


        <!-- <div class="col-md-6">
          <div class="row"> -->
            <div class="col-md-4">
              <div class="form-group">
                <label>Hari</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="number" class="form-control  text-right" id="input_add_hari" value="0"  onblur="onChangeHeader('hari' , 'input_add_hari')">
                <input type="hidden" class="form-control  text-right" id="input_add_harikredit" value="0"  >
              </div>
            </div>

          </div>
            <div class="row" style="margin-top: -10px">


            <!-- <div class="col-md-6">
              <div class="row"> -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Valas</label>
                  </div>
                </div>


                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_valas" value="IDR" disabled >
                  </div>
                </div>
              <!-- </div>
            </div> -->


            </div>
            <div class="row" style="margin-top: -10px">

            <!-- <div class="col-md-6">
              <div class="row"> -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Kurs</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="number" class="form-control text-right" id="input_add_kurs" value="1.00"  onblur="onChangeKurs('kurs' , 'input_add_kurs')">
                  </div>
                </div>


          </div>
        </div>

        <div class="col-md-3 mt-2">
          <div class="row">



          </div>
        </div>

        <div class="col-md-3 mt-2">
          <div class="row">


          </div>
        </div>








      </div>










      </div>



    </div>

    <!-- <div class="row "> -->

<!-- </div> -->
<div class="container-fluid">
  <hr/>

</div>

  <div class="container-fluid mt-4" style="overflow-x: auto;padding:0; margin:0; width:100%;" >

        <table id="addTable" class="table table-bordered table-striped"  >
          <thead class="text-center bg-primary text-white">
            <tr>
              <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
              <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
              <th style="padding: 4px 12px;" scope="col">Qty</th>
              <th style="padding: 4px 12px;" scope="col">Harga</th>
              <th style="padding: 4px 12px;" scope="col">Sub Total</th>
              <th style="padding: 4px 12px;" scope="col">Keterangan</th>

              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="addTableData" class="" >
            <tr >

                <td colspan=7 class="text-center">Belum ada data</td>

          </tr>

          </tbody>


        </table>
  </div>

  </div>




  <div id="" class="container-fluid">
    <div class="row">
      <div class="col-md-12 mt-2 text-right">
      <!-- <button type="button" class="btn btn-primary" onclick="buttonAddAddItem()" class="btn btn-secondary"  >+ Tambah Item</button> -->
      <button type="button" class="btn btn-primary btn-lg" style="
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
    <!-- <button id="" type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
  </div>

  <div id="formAddAdd" class="container-fluid showhideitem">
    <!-- <div class="line"></div> -->
    <hr/>
    <div class="row">
      <div class="col-12">
        <h4>Add Item</h4>
      </div>
    </div>
    <div class="row">

      <div class="col-md-6">



    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
        <label>Barang</label>
      </div>
      </div>
      <!-- <div class="col-md-4 text-right">

        </div> -->
      <div class="col-md-3">
        <div class="input-group form-group">

          <input id="AddAddKodeBrg" type="text" class="form-control" onkeypress="onKeyPressBarang(event)">
          <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button>
        </div>
      </div>

      <div class="col-md-5">
        <div class="input-group form-group">
          <input id="AddAddNamaBrg" type="text" class="form-control" disabled>
        </div>
      </div>

    </div>

  </div>




</div>
  <div class="row" style="margin-top: -10px">

  <div class="col-md-6">
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
        <label>Qty</label>
      </div>
      </div>


      <div class="col-md-3">
        <input id="AddAddInputQty" type="number" value='0.00' class="form-control text-right">
      </div>

      <div class="col-md-2">
        <div class="form-group">
        <label>Harga</label>
      </div>
      </div>

      <div class="col-md-3">
        <input id="AddAddInputHarga" type="number" value='0.00' class="form-control text-right">
        <input id="AddAddInputIsi" type="hidden"  class="form-control  " disabled>
        <input id="AddAddInputSatuan" type="hidden"  class="form-control  " disabled>
      </div>

    </div>
  </div>


  <div class="col-md-4">

  </div>

</div>
  <div class="row" style="margin-top: -10px">


  <div class="col-md-6 ">
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
        <label>Keterangan</label>
      </div>
      </div>
      <!-- <div class="col-md-4 text-right">

          <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
        </div> -->
      <div class="col-md-8">
        <input id="AddAddKeterangan" type="text" class="form-control" >
        <!-- <input id="AddAddKodeGdg" type="hidden" class="form-control" disabled> -->
      </div>

    </div>
  </div>
  </div>



    <div class="row mt-2">
      <div class="col-md-12 text-right mt-4">
        <!-- <button type="button" class="btn btn-secondary" onclick="closeShowHideItem()" >Batal</button> -->
        <button id="" type="button" onclick="closeShowHideItem()" class="btn btn-secondary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Batal</button>
        <!-- <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-primary" >Add</button> -->
        <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-primary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Submit Add</button>

        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      </div>

    </div>
    <!-- <div class="line"></div> -->
    <!-- <hr/> -->
  </div>



  <div id="formAddEdit" class="container-fluid showhideitem">
    <!-- <div class="line"></div> -->
    <hr/>
    <div class="row">
      <div class="col-12">
        <h4>Edit Item</h4>
      </div>
    </div>
    <div class="row">

      <div class="col-md-4">



    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
        <label>Kode Barang</label>
      </div>
      </div>
      <!-- <div class="col-md-4 text-right"> -->

          <!-- <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button> -->
        <!-- </div> -->
      <div class="col-md-8">
        <input id="AddEditKodeBrg" type="text" class="form-control" disabled>
      </div>

    </div>

  </div>

  <div class="col-md-4">
    <!-- <div class="row"> -->
      <div class="row">
        <div class="col-md-4 ">
          <div class="form-group">
          <label>Nama Barang</label>
        </div>
        </div>
        <div class="col-md-8">
          <div class="form-group">

            <input id="AddEditNamaBrg" type="text" class="form-control" disabled>
          </div>
        </div>
      </div>

    <!-- </div> -->

  </div>

  <div class="col-md-4">

  </div>


  </div>
  <div class="row" style="margin-top: -10px">

  <div class="col-md-4">
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
        <label>Qty</label>
      </div>
      </div>


      <div class="col-md-8">
        <input id="AddEditInputQty" type="number" value='0.00' class="form-control text-right">
      </div>



    </div>
  </div>


  <div class="col-md-4">

    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
        <label>Harga</label>
      </div>
      </div>
      <div class="col-md-8">
        <input id="AddEditInputUrutItem" type="hidden"  class="form-control  " disabled>
        <input id="AddEditInputHarga" type="number" value='0.00' class="form-control text-right">
        <input id="AddEditInputIsi" type="hidden"  class="form-control  " disabled>
        <input id="AddEditInputSatuan" type="hidden"  class="form-control  " disabled>
      </div>
    </div>

  </div>
  <!-- <div class="col-md-6">

  </div> -->

  <div class="col-md-4">

  </div>


  </div>
  <div class="row" style="margin-top: -10px">

  <div class="col-md-8">
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
        <label>Keterangan</label>
      </div>
      </div>
      <!-- <div class="col-md-4 text-right">

          <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
        </div> -->
      <div class="col-md-4">
        <input id="AddEditKeterangan" type="text" class="form-control" >
        <!-- <input id="AddAddKodeGdg" type="hidden" class="form-control" disabled> -->
      </div>

    </div>
  </div>
  </div>



    <div class="row mt-2">
      <div class="col-md-12 text-right mt-4">
        <!-- <button type="button" class="btn btn-secondary" onclick="closeShowHideItem()" >Batal</button>

        <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->

        <button id="" type="button" onclick="closeShowHideItem()" class="btn btn-secondary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Batal</button>
        <!-- <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-primary" >Add</button> -->
        <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Submit Edit</button>

        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      </div>

    </div>
    <!-- <div class="line"></div> -->
    <!-- <hr/> -->
  </div>



  </div>



</div>


<div class="container-fluid mainpage" id="page3" style="display: none">

  <div id="" class="container-fluid ">


    <div id="" class="">
      <div class="container-fluid">

        <!-- <div id="qrcode"></div> -->
        <div class="row" style="margin-top: -60px">
          <div class="col-6 text-left">
            <h1>Otorisasi</h1>
          </div>
          <div class="col-6 text-right">
            <!-- <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonCloseForm()"  >Close</button> -->
            <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>

          </div>
        </div>
      <!-- <button onclick="loadAll()">tes</button> -->
      </div>


    <div id="" class="">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <!-- <input type="hidden" name="noUrut" id="input_add_nourut" value="" /> -->
        <div class="row">

          <div class="col-md-3">
            <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <!-- <div class="col-md-3 text-right">
              <div class="form-group">
            <button class="btn btn-primary btn-sm text-right" id="buttonAddListCustomer" onclick="buttonAddListCustomer()"><i class="bi bi-plus"></i></button>
            </div>
          </div> -->
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_otorisasi_kodecustomer" placeholder="" disabled>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_otorisasi_nobukti" placeholder="No Bukti" disabled>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Tgl</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_otorisasi_tanggal" value="{!! date('Y-m-d') !!}"  disabled>
                </div>
              </div>
            </div>
          </div>



        </div>
      </div>
      <div class="container-fluid" >
        <hr/ style="margin-top: -8px">

      </div>

      <div class="container-fluid mt-4">
      <div class="row">





          <div class="col-md-3">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_otorisasi_namacustomer"  disabled></textarea>
                </div>
              </div>
              <div class="col-12" style="margin-top: -10px">

                <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_otorisasi_alamatcustomer"  disabled></textarea>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="row">



          <div class="col-md-6">
            <div class="row">
              <!-- <div class="col-md-6">
                <div class="row"> -->


              <!-- <div class="col-md-6">
                <div class="row"> -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Tipe PPN</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <select id="input_otorisasi_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled >
                        <option value=0 selected>None</option>
                        <option value=1 >Exclude</option>

                        <option value=2 >Include</option>
                      </select>
                    </div>
                  </div>


                <!-- </div>

              </div> -->

              <!-- <div class="col-md-6">
                <div class="row"> -->

              </div>
                <div class="row" style="margin-top: -10px">

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Sales</label>
                    </div>
                  </div>
                  <!-- <div class="col-md-3">
                    <div class="row">

                    <div class="form-group">
                  <button class="btn btn-primary btn-sm text-right" id="buttonAddListSales" onclick="buttonAddListSales()"><i class="bi bi-plus"></i></button>
                  </div>
                </div>

                  </div> -->
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_otorisasi_sales" value="" disabled >
                      <input type="hidden" class="form-control text-center" id="input_otorisasi_kodesales" value="" disabled >
                    </div>
                  </div>


                <!-- </div>

              </div> -->




            </div>

          </div>

          <div class="col-md-6">
            <div class="row" >



              <!-- <div class="col-md-6">
                <div class="row"> -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>No PO</label>
                    </div>
                  </div>


                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_otorisasi_nopo" value="" disabled  >
                    </div>
                  </div>
                <!-- </div>
              </div> -->

              <!-- <div class="col-md-6">
                <div class="row"> -->

              </div>
                <div class="row" style="margin-top: -10px">


                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Uang Muka</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="number" class="form-control text-right" id="input_otorisasi_uangmuka" value="0.00"  disabled>
                    </div>
                  </div>
                <!-- </div>
              </div> -->

            </div>

          </div>

          <div class="col-12">
            <div class="row" style="margin-top: -10px">
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Lokasi Penerima</label>
                    </div>
                  </div>
                </div>
                <div class="row" style="margin-top: -10px">
                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="hidden" class="form-control text-center" id="input_otorisasi_kodelokasipenerima" value="" disabled >
                      <input type="text" class="form-control" id="input_otorisasi_lokasipenerima" value="" disabled >
                    </div>
                  </div>

                </div>
              </div>





              <div class="col-md-6">
                <div class="row">

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Catatan</label>
                    </div>
                  </div>

                </div>

                <div class="row" style="margin-top: -10px">
                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_otorisasi_catatan" value="" disabled >
                    </div>
                  </div>

                </div>

              </div>

            </div>




          </div>
        </div>







      </div>

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Valas</label>
                </div>
              </div>


              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_otorisasi_valas" value="IDR" disabled >
                </div>
              <!-- </div>
            </div> -->
          </div>



        </div>
          <div class="row" style="margin-top:-10px">


          <!-- <div class="col-md-6">
            <div class="row"> -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Kurs</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_otorisasi_kurs" value="1.00" disabled>
                </div>
              </div>
            <!-- </div>
          </div> -->

        </div>
          <div class="row" style="margin-top: -10px">




          <!-- <div class="col-md-6">
            <div class="row"> -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Pembayaran</label>
                </div>
              </div>


              <div class="col-md-8">
                <div class="form-group">
                  <select id="input_otorisasi_pembayaran" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                    <option value=0 selected>Tunai</option>
                    <option value=1 >Kredit</option>
                  </select>
                </div>
              </div>
            <!-- </div>
          </div> -->

          <!-- <div class="col-md-6">
            <div class="row"> -->

          </div>
            <div class="row" style="margin-top: -10px">


              <div class="col-md-4">
                <div class="form-group">
                  <label>Hari</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="number" class="form-control  text-right" id="input_otorisasi_hari" value="0" disabled>
                  <input type="hidden" class="form-control  text-right" id="input_otorisasi_harikredit" value="0" disabled >
                </div>
              </div>
            <!-- </div>
          </div> -->
            </div>

          </div>

          <div class="col-md-3">
            <div class="row">



            </div>
          </div>










        </div>










        </div>



      </div>

      <!-- <div class="row "> -->

  <!-- </div> -->




  <div class="container-fluid">
    <hr/>

  </div>


    <div class="container-fluid mt-4" style="overflow:auto; margin-top:-35px;">
      <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
      <div class="row">
        <table id="otorisasiTable" class="table table-bordered table-hover table-striped table-responsive-lg">
          <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                <th style="padding: 4px 12px;" scope="col">Keterangan</th>


              </tr>
            </thead>


            <tbody id="otorisasiTableData" class="" >
              <tr >

                  <td colspan=6 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>

    </div>


    <div id="" class="container-fluid mt-2">
      <div class="row">
        <div class="text-right col-12">
          <button type="button" class="btn btn-primary btn-lg" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="submitOtorisasi()" class="btn btn-secondary"><b>Otorisasi</b></button>
        </div>

      </div>
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    </div>
    </div>

    </div>


</div></div>
<!-- </div> -->


<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document">
    <div id="" class="modal-content ">


      <div id="" class="showhideform">
      <div class="modal-header">


          <h5 class="modal-title" id="">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12" style="margin-top:-30px;">
              <h3>Detail</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="padding:0; margin:0; width:100%; overflow:auto; margin-top:-60px;">
              <table id="tabel_add_list_lokasi" class="table table-bordered table-hover table-striped table-responsive-lg">
                <thead class="text-center bg-primary text-white">
                <!-- <tr><th style="padding: 4px 12px;" scope="col">Actions</th> -->

                  <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                  <th style="padding: 4px 12px;" scope="col">Gdg</th>
                  <th style="padding: 4px 12px;" scope="col">Stock</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_lokasi" class="text-left" >

                <tr >

                  <td>-</td>
                  <td>-</td>
                  <td>-</td>


                    <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                    </td>
              </tr>
              </tbody>


            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>




        </div>





      </div>


      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()" >Batal</button>
      </div>
      </div>


















      </div>


    </div>
  </div>

<!-- End modal add-->






<div class="modal fade" id="formOtorisasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">








    </div>
  </div>

<!-- End modal oto-->





@endsection

@section('js')
<script type="text/javascript">
let xppncust = 0

let tipeform = 'add'

let dataTableAdd = []
let dataTableKoreksi = []
let dataEdit = {}

let tempKode = ''

$(document).ready(function(){

  $("#tabel2").DataTable({
    lengthChange: false,
    paging: false,
    order: [[1, 'asc']],
    columnDefs: [
      { targets: [0], orderable: false }
    ]
  });

});

function renderTable(data) {

  // bersihkan DataTable dengan benar
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().clear().destroy();
  }

  // kosongkan tbody agar tidak numpuk
  $('#tabel_data').empty();

  let html = '';

  data.forEach(item => {

    html += `
      <tr id="row_${item.NOBUKTI}_${item.Urut}">

        <td class="text-center action">
          ${item.IsVerf == 1
            ? `<button class="btn btn-danger btn-sm"
                onclick="buttonBatalOtorisasi('${item.NOBUKTI}','${item.Urut}')">-</button>`
            : `<button class="btn btn-primary btn-sm"
                onclick="buttonOtorisasi('${item.NOBUKTI}','${item.Urut}')">+</button>`
          }
        </td>

        <td>${item.NOBUKTI ?? ''}</td>
        <td>${formatDate(item.TANGGAL,'/') ?? ''}</td>
        <td>${item.NAMACUSTSUPP ?? ''}</td>
        <td>${item.NAMAPIC ?? ''}</td>
        <td>${item.KODEBRG ?? ''}</td>
        <td>${item.NAMABRG ?? ''}</td>
	${Number(item.IsVerf) ?
          '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>'
          :
          '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'
        }
        <td>${item.TglVerf ? formatDate(item.TglVerf,'/') : ''}</td>
        <td>${item.UserVerf ?? ''}</td>
        <td class="text-right">${item.HARGA ?? ''}</td>
        <td>${item.tipe ?? ''}</td>
        <td>${item.NamaMerk ?? ''}</td>
        <td>${item.ketdet ?? ''}</td>
        <td class="text-right">${item.QNT ?? ''}</td>
        <td class="text-right">${item.QntSO ?? ''}</td>
        <td class="text-right">${item.Sisa ?? ''}</td>

      </tr>
    `;
  });

  $('#tabel_data').html(html);

  // init ulang DataTable
  $('#tabel').DataTable({
    dom: 't',
    lengthChange: false,
    paging: false,
    order: [[1, 'asc']],
    columnDefs: [
      { targets: [0], orderable: false }
    ]
  });
}

function searchData() {

  let nobukti  = $('#search_nobukti').val();
  let customer = $('#search_customer').val();
  let barang   = $('#search_barang').val();

  $.ajax({
    url: "{!! url('verifikasipenawaranloadall') !!}",
    type: "GET",
    dataType: "json",
    cache: false, 
    data: {
      search_nobukti: nobukti,
      search_customer: customer,
      search_barang: barang,
      _t: Date.now() 
    },
    success: function(res) {

      if (!res || !res.tempOutstanding) {
        renderTable([])
        return
      }

      renderTable(res.tempOutstanding);

    },
    error: function(err) {
      console.log(err)
      alertify.warning('Gagal load data')
    }
  });
}


function buttonDetail (kodebrg) {
  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('verifikasipenawarandetailbarang') !!}",
    type: "post",
    async: false,
    data: {
      kodebrg,
      _token
    },
    success: function(res) {

      // $('#tabel_').DataTable().destroy();

      let rowTable = ''

      if (!res.length) {


        alertify.warning("Data tidak ditemukkan")
        return
      }


      res.forEach((item, i) => {
        rowTable += `<tr>

          <td>${ item.KODEBRG }</td>
          <td>${ item.NAMABRG }</td>
          <td>${ item.KODEGDG }</td>
                  <td class="text-right">${item.SaldoQnt ? formatAngka(item.SaldoQnt ) : '' }</td>


        </tr>
          `
      });

      document.getElementById("tabel_data_add_list_lokasi").innerHTML = rowTable

      $("#form").modal("toggle");


},
error: function (err) {
  console.log(err)
  console.log(err.status)
  console.log(err.statusText)
  alertify.warning('Terjadi kesalahan silahkan refresh browser')
}

})
}


function buttonBatalOtorisasi(nobukti, urut) {

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('verifikasipenawaranbatalotorisasi') !!}",
    type: "POST",
    data: {
      _token,
      nobukti,
      urut
    },
    success: function(res) {

      alertify.success('Berhasil Batal Verifikasi')

      searchData()

    },
    error: function(err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan')
    }
  });

}

function buttonOtorisasi(nobukti, urut) {

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('verifikasipenawaranotorisasi') !!}",
    type: "POST",
    data: {
      _token,
      nobukti,
      urut
    },
    success: function(res) {

      alertify.success('Berhasil Verifikasi')

      searchData()

    },
    error: function(err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan')
    }
  });

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

function formatAngka (angkaString) {
  angkaString = parseFloat(angkaString).toFixed(2)
  // console.log('formatAngka' , angkaString);
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
}
</script>


<script>

  $(document).ready(function () {

  const urlParams = new URLSearchParams(window.location.search)

  if (urlParams.has('search')) {

  }

})


</script>


<script>
  // const tabHome = document.getElementById('nav-home-tab');
  // const tabProfile = document.getElementById('nav-profile-tab');

  function setActiveTab(idNav , flag = 0) {
    $(".nav-item").css("background-color", "#f8f9fa");
    $(".nav-item").css("color", "#007bff");
    if (flag) {


      document.getElementById(idNav).style.backgroundColor = '#007bff';
      document.getElementById(idNav).style.color = '#fff';
      return
    }
    if(idNav == 'nav-profile-tab') {

      loadAllInfo()

      console.log(idNav)
      document.getElementById(idNav).style.backgroundColor = '#007bff';
      document.getElementById(idNav).style.color = '#fff';
    } else {
      loadAll()

      console.log(idNav)
      document.getElementById(idNav).style.backgroundColor = '#007bff';
      document.getElementById(idNav).style.color = '#fff';
    }

    // if (homeActive) {
    //   tabHome.style.backgroundColor = '#007bff';
    //   tabHome.style.color = '#fff';
    //   tabProfile.style.backgroundColor = '#f8f9fa';
    //   tabProfile.style.color = '#007bff';
    // } else {
    //   tabProfile.style.backgroundColor = '#007bff';
    //   tabProfile.style.color = '#fff';
    //   tabHome.style.backgroundColor = '#f8f9fa';
    //   tabHome.style.color = '#007bff';
    // }
  }

  // Default warna tab
  document.getElementById('nav-home-tab').addEventListener('click', function () {
    setActiveTab("nav-home-tab");
  });

  // buat ganti tab

</script>




@endsection
