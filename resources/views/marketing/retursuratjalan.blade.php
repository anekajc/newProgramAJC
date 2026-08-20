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

  #tabel_add_list_nosj_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;

  }
  #tabel_add_list_nosj_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }
</style>

<style>

  #tabel_add_list_custsupp_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;

  }
  #tabel_add_list_custsupp_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }
</style>
{{-- end tampilan search bar modal add pelanggan --}}
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
<div id="page1" class="container-fluid">


<div class="container-fluid">

  <!-- <div id="qrcode"></div> -->
  <div class="row">
    <div class="col-6 text-left">
      <h2 style="margin-top:-85px;">Retur SJ</h2>
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
        + RSPB
      </button>
    </div>
  </div>
<!-- <button onclick="loadAll()">tes</button> -->
</div>

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
  <div class="card" style="margin-top:-55px;">
<div class="card-header" >
<div class="row">
  <nav style="width: 100%;">
    <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
      <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true"
         style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; text-align: left; border: 2px solid #007bff;">
        Retur SJ Belum Otorisasi
      </a>
      <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"
         style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
        Retur SJ Sudah Otorisasi
      </a>
    </div>
  </nav>

</div>
</div>
<div class="card-body" style="padding:0;" >
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    <div class="row">
      <div class="col-12" style="overflow:auto;">
        <div class="container-fluid" style="padding:0; margin:0; width:100%;">

              <table id="tabel" class="table table-bordered table-hover table-striped table-responsive-lg"  >
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Actions</th>
                    <th style="padding: 4px 12px;" scope="col">No. Bukti</th>
                    <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                    <th style="padding: 4px 12px;" scope="col">No SPB</th>
                    <th style="padding: 4px 12px;" scope="col">Tgl SPB</th>
                    <th style="padding: 4px 12px;" scope="col">Customer</th>
                  </tr>
                </thead>


                <tbody id="tabel_data" class="text-left" >
                  @for ($i = 0; $i < count($tempOutstanding); $i++)
                <tr>
                  <td class='text-center'>
                    <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('{{ $tempOutstanding[$i]->NOBUKTI }}' , 'detail')"><i class="bi bi-info"></i></button>
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $tempOutstanding[$i]->NOBUKTI }}' , '{{ $tempOutstanding[$i]->IsOtorisasi1 }}')"><i class="bi bi-pen"></i></button>
                    @if ($tempOutstanding[$i]->IsOtorisasi1 == 1)
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOto('{{ $tempOutstanding[$i]->NOBUKTI }}' , 'edit')"><i class="bi bi-key"></i></button>
                    @else
                    <button class="btn btn-primary btn-sm" type="button" onclick="buttonOto('{{ $tempOutstanding[$i]->NOBUKTI }}' , 'add')"><i class="bi bi-key"></i></button>

                    @endif

                  </td>
                  <td>{{ $tempOutstanding[$i]->NOBUKTI }}</td>
                  <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i]->TANGGAL)) !!}</td>
                  <td>{{ $tempOutstanding[$i]->NOSPB }}</td>

                  <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i]->TglSPB)) !!}</td>
                  <td>{{ $tempOutstanding[$i]->NamaCustSupp }}</td>


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

              <table id="tabel2" class="table table-bordered table-hover table-striped table-responsive-lg"  >
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Actions</th>
                    <th style="padding: 4px 12px;" scope="col">No. Bukti</th>
                    <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                    <th style="padding: 4px 12px;" scope="col">No SPB</th>
                    <th style="padding: 4px 12px;" scope="col">Tgl SPB</th>
                    <th style="padding: 4px 12px;" scope="col">Customer</th>
                    <th style="padding: 4px 12px;" scope="col">User Oto1</th>
                    <th style="padding: 4px 12px;" scope="col">Tgl Oto1</th>
                  </tr>
                </thead>


                <tbody id="tabel2_data" class="text-left" >
                  @for ($i = 0; $i < count($tempOutstanding2); $i++)
                <tr>
                  <td class='text-center'>
                    <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('{{ $tempOutstanding2[$i]->NOBUKTI }}' , 'detail')"><i class="bi bi-info"></i></button>
                    @if ($tempOutstanding2[$i]->IsOtorisasi1 == 1)
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOto('{{ $tempOutstanding2[$i]->NOBUKTI }}' , 'edit')"><i class="bi bi-key"></i></button>
                    @else
                    <button class="btn btn-primary btn-sm" type="button" onclick="buttonOto('{{ $tempOutstanding2[$i]->NOBUKTI }}' , 'add')"><i class="bi bi-key"></i></button>

                    @endif

                  </td>
                  <td>{{ $tempOutstanding2[$i]->NOBUKTI }}</td>
                  <td>{!! date("Y/m/d", strtotime($tempOutstanding2[$i]->TANGGAL)) !!}</td>
                  <td>{{ $tempOutstanding2[$i]->NOSPB }}</td>

                  <td>{!! date("Y/m/d", strtotime($tempOutstanding2[$i]->TglSPB)) !!}</td>
                  <td>{{ $tempOutstanding2[$i]->NamaCustSupp }}</td>

                  <td>{{ $tempOutstanding2[$i]->OtoUser1 }}</td>
                  <td>{!! $tempOutstanding2[$i]->TglOto1 ? date("Y/m/d", strtotime($tempOutstanding2[$i]->TglOto1)) : '' !!}</td>



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

<div id="page2" class="container-fluid" style="display: none">

  <div class="container-fluid">

    <!-- <div id="qrcode"></div> -->
    <div class="row" style="margin-top: -50px">
      <div class="col-6 text-left">
        <h1>Form Retur SJ</h1>
      </div>
      <div class="col-6 text-right">
        <button type="button" class="btn btn-primary btn-lg" style="
            height: 30px;
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
  <!-- <button onclick="loadAll()">tes</button> -->
  </div>
  <div id="modalBodyAddMain" class="">
  <div class="modal-body">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid">
      <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
      <div class="row">

        <div class="col-3">
          <div class="row">


          <div class="col-4">
            <div class="form-group">
              <label>No SJ</label>
            </div>
          </div>
          <!-- <div class="col-3 text-right">
            <div class="form-group">
          </div> -->
          <div class="col-8">
            <div class="form-group input-group">
              <input type="text" class="form-control" id="input_add_nosj" placeholder="" disabled>
              <button class="btn btn-primary btn-sm text-right" id="buttonAddListNoSJ" onclick="buttonAddListNoSJ()"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>
          </div>

          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_nobukti" placeholder="No SO" disabled>
                </div>
              </div>
            </div>
          </div>

          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>Tanggal</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" >
                </div>
              </div>
            </div>
          </div>


        </div>






      </div>

      <hr/>

      <div class="container-fluid">


      <div class="row">
        <div class="col-3">
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <div class="col-8">
                <div class="form-group input-group">
                  <input type="text" class="form-control" id="input_add_kodecustomer" placeholder="" disabled>
                  <button class="btn btn-primary btn-sm text-right" id="buttonAddListCustSupp" onclick="buttonAddListCustSupp()"><i class="bi bi-plus"></i></button>
                </div>
            </div>
            <!-- <div class="col-2">
              <div class="form-group">


              </div>

            </div> -->
          </div>
            <div class="row" style="margin-top: -10px">

            <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_customer"  disabled></textarea>
              </div>
            </div>
          </div>

        </div>
        <div class="col-3">
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>No SO</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <!-- <input type="hidden" class="form-control" id="input_add_kodegdg" placeholder="" > -->
                <input type="text" class="form-control" id="input_add_noso" placeholder="" disabled>
              </div>
            </div>
          </div>
            <div class="row" style="margin-top: -10px">


            <div class="col-4">
              <div class="form-group">

                <label>Catatan</label>
              </div>

            </div>
            <div class="col-8">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none; " rows=4  class="form-control" id="input_add_catatan"  ></textarea>
              </div>
            </div>

          </div>
        </div>

        <div class="col-3">
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Tgl SJ</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <!-- <input type="hidden" class="form-control" id="input_add_kodegdg" placeholder="" > -->
              <input type="date" class="form-control" id="input_add_tanggalsj" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

          </div>
            <div class="row" style="margin-top: -10px">



            <!-- <div class="row"> -->
              <div class="col-4">
                <div class="form-group">
                  <label>Gudang</label>
                </div>
              </div>
              <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_gudang" placeholder="" disabled>
                  </div>
              </div>

            <!-- </div> -->

          </div>
        </div>

        <div class="col-3">
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Tgl SC</label>
              </div>
            </div>

            <div class="col-8">
              <div class="form-group">
                <!-- <input type="hidden" class="form-control" id="input_add_kodegdg" placeholder="" > -->

              <input type="date" class="form-control" id="input_add_tanggalsc" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

          </div>
            <div class="row" style="margin-top: -10px">


            <div class="col-4">
              <div class="form-group">
                <label>No Pol Kend</label>
              </div>
            </div>
            <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_nopol" placeholder="" >
                </div>
            </div>

          </div>
        </div>


      </div>

      <div class="row">
        <div class="col-3">

        </div>

        <div class="col-8">
          <div class="row">
            <div class="col-6">

            </div>

            <div class="col-6">
              <div class="row">


              </div>
            </div>

            <div class="col-12">
              <div class="row">


              </div>

            </div>



          </div>

        </div>

      </div>

      </div>



    </div>
    <div class="container-fluid">
      <hr/>

    </div>

  <div class="container-fluid mt-4" style="overflow-x: auto;">

        <table id="addTable" class="table table-bordered table-hover table-striped table-responsive-lg"  >
          <thead class="text-center bg-primary text-white">
            <tr>
              <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
              <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
              <th style="padding: 4px 12px;" scope="col">Qty1</th>
              <th style="padding: 4px 12px;" scope="col">Sat1</th>
              <th style="padding: 4px 12px;" scope="col">Qty2</th>
              <th style="padding: 4px 12px;" scope="col">Sat2</th>
              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="addTableData" class="text-right" >
            <tr >

                <td colspan=7 class="text-center">Belum ada data</td>

          </tr>

          </tbody>


        </table>
  </div>



  <div class="col-12">

  <div class="row">
    <div class="col-12  text-right">

      <button type="button" class="btn btn-primary btn-lg" style="
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonAddAdd()" ><b>+ Tambah Item</b></button>
      </div>
    </div>

  </div>
</div>
<div id="formAddAdd" class="container-fluid showhide mb-2">
  <!-- <div class="line"></div> -->
  <hr/>
  <div class="row">
    <div class="col-12">
      <h4>Add Item</h4>
    </div>
  </div>
  <div class="row">

    <div class="col-3">



  <div class="row">
    <div class="col-4">
      <div class="form-group">
      <label>Kode Barang</label>
    </div>
    </div>
    <!-- <div class="col-3 text-right">

      </div> -->
    <div class="col-8">
      <div class="form-group input-group">

        <input id="AddAddKodeBrg" type="text" class="form-control" disabled>
        <button type="button" id="buttonAddListBarang" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button>
      </div>
    </div>

  </div>

</div>

<div class="col-6">
  <div class="row">
    <div class="col-2">
      <div class="form-group">
      <label>Nama Barang</label>
    </div>
    </div>
    <div class="col-6">
      <input id="AddAddNamaBrg" type="text" class="form-control" disabled>
    </div>
  </div>
</div>

<div class="col-3">

</div>

</div>
<div class="row" style="margin-top: -10px">

<div class="col-3 ">
  <div class="row">
    <div class="col-4">
      <div class="form-group">
      <label>Retur Supp</label>
    </div>
    </div>
    <!-- <div class="col-4 text-right">

        <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
      </div> -->
    <div class="col-8">
      <select  id="AddAddReturSupp" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
        <option value=0 selected>Tidak</option>
        <option value=1 >Ya</option>
      </select>
      <!-- <input id="AddAddNamaGdg" type="text" class="form-control" disabled>
      <input id="AddAddKodeGdg" type="hidden" class="form-control" disabled> -->
    </div>

  </div>
</div>
<div class="col-6 ">
  <div class="row">
    <div class="col-2">
      <div class="form-group">
      <label>Qty</label>
    </div>
    </div>
    <div class="col-4">
      <input id="AddAddInputQty" type="number" value='0.00' class="form-control text-right">
    </div>
    <div class="col-2">
      <input id="AddAddInputSatuan" type="text"  class="form-control  " disabled>
      <input id="AddAddInputIsi" type="hidden"  class="form-control  " disabled>
      <input id="AddAddInputSatuan1" type="hidden"  class="form-control  " disabled>
    </div>


  </div>
</div>
</div>



  <div class="row mt-2">
    <div class="col-md-12 text-right mt-4">
      <button type="button" class="btn btn-secondary btn-lg" style="
      height: 30px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonBatalAdd()" class="btn btn-secondary">Batal</button>

      <button type="button" id="buttonSubmitAddAdd" class="btn btn-primary btn-lg" style="
      height: 30px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="submitAddAdd()" class="btn btn-secondary">Submit Add</button>
</div>

  </div>
  <!-- <div class="line"></div> -->
  <!-- <hr/> -->
</div>

<div id="formAddEdit" class="container-fluid showhide mb-2">
  <!-- <div class="line"></div> -->
  <hr/>
  <div class="row">
    <div class="col-12">
      <h4>Edit Item</h4>
    </div>
  </div>
  <div class="row">

    <div class="col-3">



  <div class="row">
    <div class="col-4">
      <div class="form-group">
      <label>Kode Barang</label>
    </div>
    </div>
    <!-- <div class="col-4 text-right">

    </div> -->
    <div class="col-8">
      <input id="AddEditKodeBrg" type="text" class="form-control" disabled>
    </div>

  </div>

</div>

<div class="col-6">
  <div class="row">
    <div class="col-2">
      <div class="form-group">
      <label>Nama Barang</label>
    </div>
    </div>
    <div class="col-6">
      <input id="AddEditNamaBrg" type="text" class="form-control" disabled>
    </div>
  </div>
</div>

<div class="col-3">

</div>

</div>

<div class="row" style="margin-top: -10px">

<div class="col-3 ">
  <div class="row">
    <div class="col-4">
      <div class="form-group">
      <label>Retur Supp</label>
    </div>
    </div>
    <!-- <div class="col-4 text-right">

        <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
      </div> -->
    <div class="col-8">
      <select  id="AddEditReturSupp" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
        <option value=0 selected>Tidak</option>
        <option value=1 >Ya</option>
      </select>
      <!-- <input id="AddEditNamaGdg" type="text" class="form-control" disabled>
      <input id="AddEditKodeGdg" type="hidden" class="form-control" disabled> -->
    </div>

  </div>
</div>
<div class="col-6">
  <div class="row">
    <div class="col-2">
      <div class="form-group">
      <label>Qty</label>
    </div>
    </div>
    <div class="col-4">
      <input id="AddEditInputQty" type="number" value='0.00' class="form-control text-right">
    </div>
    <div class="col-2">
      <input id="AddEditInputSatuan" type="text"  class="form-control  " disabled>
      <input id="AddEditInputIsi" type="hidden"  class="form-control  " disabled>
      <input id="AddEditInputSatuan1" type="hidden"  class="form-control  " disabled>
    </div>


  </div>
</div>
</div>



  <div class="row mt-2">
    <div class="col-md-12 text-right mt-4">
      <!-- <button type="button" class="btn btn-secondary" onclick="buttonBatalAdd()" >Batal</button>

      <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->

      <button type="button" class="btn btn-secondary btn-lg" style="
      height: 30px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonBatalAdd()" class="btn btn-secondary">Batal</button>

      <button type="button" id="buttonSubmitAddEdit" class="btn btn-primary btn-lg" style="
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
  <!-- <div class="line"></div> -->
  <!-- <hr/> -->
</div>

  </div>

  </div>



  <div id="page3" class="container-fluid" style="display: none">

    <div class="container-fluid">

      <!-- <div id="qrcode"></div> -->
      <div class="row">
        <div class="col-6 text-left">
          <h1 class="" id="modalTitleDetail">Detail</h1>

            <h1 class="" id="modalTitleOto">Otorisasi</h1>
        </div>
        <div class="col-6 text-right">
          <button type="button" class="btn btn-primary btn-lg" style="
              height: 30px;
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
    <!-- <button onclick="loadAll()">tes</button> -->
    </div>
    <div id="" class="">
    <div class="modal-body">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_detail_nourut" value="" />
        <div class="row">

          <div class="col-3">
            <div class="row">


            <div class="col-4">
              <div class="form-group">
                <label>No SJ</label>
              </div>
            </div>
            <!-- <div class="col-3 text-right">
              <div class="form-group">
            </div> -->
            <div class="col-8">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_detail_nosj" placeholder="No SJ" disabled>
                <!-- <button class="btn btn-primary btn-sm text-right" id="buttonAddListNoSJ" onclick="buttonAddListNoSJ()"><i class="bi bi-plus"></i></button> -->
              </div>
            </div>
          </div>
            </div>

            <div class="col-3">
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>No Bukti</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No SO" disabled>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-3">
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="date" class="form-control" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}"  disabled>
                  </div>
                </div>
              </div>
            </div>


          </div>






        </div>

        <hr/>

        <div class="container-fluid">


        <div class="row">
          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>Customer</label>
                </div>
              </div>
              <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_kodecustomer" placeholder="" disabled>
                  </div>
              </div>




              </div>
              <div class="row" style="margin-top: -10px">
              <!-- <div class="col-2">
                <div class="form-group">
                div

                </div>

              </div> -->
              <div class="col-12">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_customer"  disabled></textarea>
                </div>
              </div>
            </div>

          </div>
          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>No SO</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <!-- <input type="hidden" class="form-control" id="input_detail_kodegdg" placeholder="" > -->
                  <input type="text" class="form-control" id="input_detail_noso" placeholder="" disabled>
                </div>
              </div>


              </div>
              <div class="row" style="margin-top: -10px">

              <div class="col-4">
                <div class="form-group">

                  <label>Catatan</label>
                </div>

              </div>
              <div class="col-8">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none; " rows=4  class="form-control" id="input_detail_catatan"  disabled></textarea>
                </div>
              </div>

            </div>
          </div>

          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>Tgl SJ</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <!-- <input type="hidden" class="form-control" id="input_detail_kodegdg" placeholder="" > -->
                <input type="date" class="form-control" id="input_detail_tanggalsj" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>

              </div>
              <div class="row" style="margin-top: -10px">

              <!-- <div class="row"> -->
                <div class="col-4">
                  <div class="form-group">
                    <label>Gudang</label>
                  </div>
                </div>
                <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_detail_gudang" placeholder="" disabled>
                    </div>
                </div>

              <!-- </div> -->

            </div>
          </div>

          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>Tgl SC</label>
                </div>
              </div>

              <div class="col-8">
                <div class="form-group">
                  <!-- <input type="hidden" class="form-control" id="input_detail_kodegdg" placeholder="" > -->

                <input type="date" class="form-control" id="input_detail_tanggalsc" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>


              </div>
              <div class="row" style="margin-top: -10px">

              <div class="col-4">
                <div class="form-group">
                  <label>No Pol Kend</label>
                </div>
              </div>
              <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_nopol" placeholder="" disabled>
                  </div>
              </div>

            </div>
          </div>


        </div>

        <div class="row">
          <div class="col-3">

          </div>

          <div class="col-8">
            <div class="row">
              <div class="col-6">

              </div>

              <div class="col-6">
                <div class="row">


                </div>
              </div>

              <div class="col-12">
                <div class="row">


                </div>

              </div>



            </div>

          </div>

        </div>

        </div>



      </div>





    <div class="container-fluid" style="overflow-x: auto;">

          <table id="detailTable" class="table table-bordered table-hover table-striped table-responsive-lg"  >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                <th style="padding: 4px 12px;" scope="col">Qty1</th>
                <th style="padding: 4px 12px;" scope="col">Sat1</th>
                <th style="padding: 4px 12px;" scope="col">Qty2</th>
                <th style="padding: 4px 12px;" scope="col">Sat2</th>
                <!-- <th scope="col">Actions</th> -->

              </tr>
            </thead>


            <tbody id="detailTableData" class="text-right" >
              <tr >

                  <td colspan=6 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>

    <div id="" class="container-fluid ">
      <div class="row">
        <div class="col-12 text-right">
          <button type="button" id="buttonSubmitOto" class="btn btn-primary btn-lg" style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="submitOto()" class="btn btn-secondary">Otorisasi</button>

        </div>

      </div>
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    </div>

    </div>

    </div>


</div>


<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="modalBodyAddListBarang" class="showhidemodalbodyadd">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12" style="margin-top: -40px">
              <h3>Barang</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row" style="margin-top: -20px">
            <div class="col-12" style="overflow:auto;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_barang" class="table table-bordered table-hover table-striped table-responsive-lg"  >
              <thead class="text-center bg-primary text-white">
                <tr>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>

                  <th style="padding: 4px 12px;" scope="col">Qty</th>

                  <th style="padding: 4px 12px;" scope="col">Sat</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_barang" class="text-left" >

                <tr >

                  <td>-</td>
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
    <div id="modalBodyAddListNoSJ" class="showhidemodalbodyadd">
      <div class="modal-body" >

      <div class="container-fluid" >
        <div class="row">
          <div class="col-12">
            <h3>SJ</h3>
          </div>
        </div>
        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
        <div class="row" style="margin-top: -60px">
          <div class="col-12" style="overflow:auto;">
          <!-- <div class="container-fluid"> -->


          <table id="tabel_add_list_nosj" class="table table-bordered table-hover table-striped table-responsive-lg"  >
            <thead class="text-center bg-primary text-white">
              <tr>

                <th style="padding: 4px 12px;" scope="col">Actions</th>
                <th style="padding: 4px 12px;" scope="col">No SPB</th>
                <th style="padding: 4px 12px;" scope="col">Tgl</th>

                <th style="padding: 4px 12px;" scope="col">Tipe</th>
                <th style="padding: 4px 12px;" scope="col">Kode Cust</th>
                <th style="padding: 4px 12px;" scope="col">Cust</th>

              </tr>
            </thead>


            <tbody id="tabel_data_add_list_nosj" class="text-left" >

              <tr >
                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                </td>
                <td>-</td>
                <td>-</td>

                <td>-</td>
                <td>-</td>
                <td>-</td>



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


  <div id="modalBodyAddListCust" class="showhidemodalbodyadd">
    <div class="modal-body" >

    <div class="container-fluid" >
      <div class="row">
        <div class="col-12">
          <h3>Custsupp</h3>
        </div>
      </div>
      <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
      <div class="row" style="margin-top: -60px">
        <div class="col-12" style="overflow:auto;">
        <!-- <div class="container-fluid"> -->


        <table id="tabel_add_list_custsupp" class="table table-bordered table-hover table-striped table-responsive-lg"  >
          <thead class="text-center bg-primary text-white">
            <tr>

              <th style="padding: 4px 12px;" scope="col">Actions</th>
              <th style="padding: 4px 12px;" scope="col">Kode Cust</th>
              <th style="padding: 4px 12px;" scope="col">Cust</th>

            </tr>
          </thead>


          <tbody id="tabel_data_add_list_custsupp" class="text-left" >

            <tr >
              <td class="text-center">
                <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
              </td>
              <td>-</td>
              <td>-</td>



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


      <div id="modalBodyFooterList" class="modal-footer showhidemodalfooteradd">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        <!-- <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()">Batal</button> -->
      </div>

      <div id="modalBodyFooterMain" class="modal-footer showhidemodalfooteradd">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        <!-- <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->









@endsection

@section('js')
<script type="text/javascript">

let listSJ = []
let listBarang = []
let listCust = []

let dataTableAddHeader = {}
let dataTableAdd = []
let dataForm = {}

let dataTableDetailHeader = {}
let dataTableDetail = []
let dataFormDetail = {}

let dataBarangAdd = {}
let dataBarangEdit = {}

$(document).ready(function(){
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
          "order": [[1, 'asc']],
          "columnDefs": [
              {"targets" :[0] , 'orderable' : false},
          // { "type": "date", "targets": [3] },
          // {  "className": "text-right", "targets": [9,10,11,12] },
          // "columns" : [{"width" : "20px"}]
        ]
        });
        $("#tabel2").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[1, 'asc']],
            "columnDefs": [
                {"targets" :[0] , 'orderable' : false},
            // { "type": "date", "targets": [3] },
            // {  "className": "text-right", "targets": [9,10,11,12] },
            // "columns" : [{"width" : "20px"}]
          ]
          });
        $("#tabel_add_list_nosj").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[1, 'asc']],
            "columnDefs": [
                 {"targets" :[0] , 'orderable' : false}
              ]
        });

        $("#tabel_add_list_custsupp").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[1, 'asc']],
            "columnDefs": [
                 {"targets" :[0] , 'orderable' : false}
              ]
        });

});


function buttonAddListBatal () {


  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();
  $('.showhidemodalfooteradd').hide();
  $('#modalBodyFooterMain').show();
}

function buttonCloseForm () {
  $('#page3').hide();
  $('#page2').hide();
  $('#page1').show();

}


function buttonBatalOto (nobukti) {
  console.log('buttonBatalOtorisasi' , nobukti)

  let akses = $("#akses_isotorisasi1").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi RSPB ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();



        $.ajax({
          url: "{!! url('retursuratjalanspbataloto') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti

          },
          success: function(res) {
            console.log('!', res)
            loadAll()

            // lockFormAdd()

            alertify.success('Berhasil Batal Otorisasi RSPB')

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

function submitOto () {

  console.log(dataTableDetailHeader.NOBUKTI)


  let _token = $("#_token").val();



  $.ajax({
    url: "{!! url('retursuratjalanspoto') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti : dataTableDetailHeader.NOBUKTI

    },
    success: function(res) {
      console.log('!', res)
      loadAll()

      // lockFormAdd()
      // $("#formDetail").modal('toggle')
      $('#page3').hide();
      $('#page1').show();

      alertify.success('Berhasil Otorisasi RSPB')


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function loadAll () {
  console.log('loadall')
  let _token = $("#_token").val();


  $.ajax({
    url: "{!! url('retursuratjalanloadall') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)
        $('#tabel').DataTable().destroy();


        let rowTable = ''
        res.tempOutstanding.forEach((item, i) => {
          rowTable += `<tr>
          <td class='text-center'>
              <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item.NOBUKTI }' , 'detail')"><i class="bi bi-info"></i></button>
              <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item.NOBUKTI }' , '${item.IsOtorisasi1 }')"><i class="bi bi-pen"></i></button>
              ${item.IsOtorisasi1 == 1 ? `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOto('${item.NOBUKTI }' , 'edit')"><i class="bi bi-key"></i></button>` : `<button class="btn btn-primary btn-sm" type="button" onclick="buttonOto('${item.NOBUKTI }' , 'add')"><i class="bi bi-key"></i></button>`}



            </td>
            <td>${item.NOBUKTI }</td>
            <td>${formatDate(item.TANGGAL, '/')}</td>
            <td>${item.NOSPB }</td>

            <td>${formatDate(item.TglSPB, '/')}</td>
            <td>${item.NamaCustSupp }</td>


          </tr>`


        });


        document.getElementById("tabel_data").innerHTML = rowTable

        $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
          "order": [[1, 'asc']],
          "columnDefs": [
              {"targets" :[0] , 'orderable' : false},
          // { "type": "date", "targets": [3] },
          // {  "className": "text-right", "targets": [9,10,11,12] },
          // "columns" : [{"width" : "20px"}]
        ]
        });



        $('#tabel2').DataTable().destroy();


        let rowTable2 = ''
        res.tempOutstanding2.forEach((item, i) => {
          rowTable2 += `<tr>
          <td class='text-center'>
              <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item.NOBUKTI }' , 'detail')"><i class="bi bi-info"></i></button>
              ${item.IsOtorisasi1 == 1 ? `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOto('${item.NOBUKTI }' , 'edit')"><i class="bi bi-key"></i></button>` : `<button class="btn btn-primary btn-sm" type="button" onclick="buttonOto('${item.NOBUKTI }' , 'add')"><i class="bi bi-key"></i></button>`}



            </td>
            <td>${item.NOBUKTI }</td>
            <td>${formatDate(item.TANGGAL, '/')}</td>
            <td>${item.NOSPB }</td>

            <td>${formatDate(item.TglSPB, '/')}</td>
            <td>${item.NamaCustSupp }</td>

            <td>${item.OtoUser1 }</td>
            <td>${ item.TglOto1 ? formatDate(item.TglOto1,'/') : '' }</td>



          </tr>`


        });


        document.getElementById("tabel2_data").innerHTML = rowTable2

        $("#tabel2").DataTable({
        "lengthChange": false,
          "paging": false ,
          "order": [[1, 'asc']],
          "columnDefs": [
              {"targets" :[0] , 'orderable' : false},
          // { "type": "date", "targets": [3] },
          // {  "className": "text-right", "targets": [9,10,11,12] },
          // "columns" : [{"width" : "20px"}]
        ]
        });
    }})


}

function buttonAddPickBarang (index) {
  dataBarangAdd = listBarang[index]
  console.log(dataBarangAdd)

  document.getElementById("AddAddKodeBrg").value = dataBarangAdd.kodebrg
  document.getElementById("AddAddNamaBrg").value = dataBarangAdd.Namabrg
  document.getElementById("AddAddInputSatuan").value = dataBarangAdd.Satuan
  document.getElementById("AddAddInputQty").value = parseFloat(dataBarangAdd.Qty).toFixed(2)


  buttonAddListBatal()
  $("#form").modal('toggle')
}

function buttonAddPickCust (index) {
  dataCust = listCust[index]
  console.log(dataCust)

  // document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_customer").value = dataCust.NAMACUSTSUPP + '\n' + dataCust.Alamat
  document.getElementById("input_add_kodecustomer").value = dataCust.KodeCustSupp
  document.getElementById("input_add_nosj").value = ''

  // document.getElementById("input_add_nopol").value = ''
  //
  // document.getElementById("input_add_tanggal").value = formatDate(new Date())
    // document.getElementById("input_add_catatan").value = ''
  $('#buttonAddListNoSJ').show();
  // $('#buttonAddListCustSupp').hide();
  buttonAddListBatal()

  $('.showhide').hide();
  $("#form").modal('toggle')
}

function buttonAddPickNoSJ (index) {
  dataForm = listSJ[index]
  console.log(dataForm)

  // document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_nosj").value = dataForm.NoSPB
  document.getElementById("input_add_noso").value = dataForm.NoSC
  // document.getElementById("input_add_customer").value = dataForm.NAMACUSTSUPP + '\n' + dataForm.Alamat
  // document.getElementById("input_add_kodecustomer").value = dataForm.KodeCustSupp
  document.getElementById("input_add_gudang").value = dataForm.Nama
  // document.getElementById("input_add_nopol").value = ''
  //
  // document.getElementById("input_add_tanggal").value = formatDate(new Date())
  document.getElementById("input_add_tanggalsj").value = formatDate(dataForm.TglSPB)
  document.getElementById("input_add_tanggalsc").value = formatDate(dataForm.TglSC)
  // document.getElementById("input_add_catatan").value = ''
  setNewNoBukti(dataForm.PPNCUST)
  // $('#buttonAddListNoSJ').hide();
  buttonAddListBatal()

  $('.showhide').hide();
  $("#form").modal('toggle')
}

function buttonOto (nobukti) {


  console.log('buttonOto' , nobukti)

  let akses = $("#akses_isotorisasi1").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  let _token = $("#_token").val();
  $('#modalTitleDetail').hide();
  $('#modalTitleOto').show();

  $('#buttonSubmitOto').show();
  $.ajax({
    url: "{!! url('retursuratjalanspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nobukti,


    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res.detail.length == 0) {
          // addTableData

          alertify.warning('Data habis/ tidak ditemukan')
          // $("#form").modal('toggle')
          return
      }
      dataTableDetail = res.detail
      dataTableDetailHeader = res.header[0]
      dataFormDetail = res.dataForm[0]
      console.log(formatDate(dataTableDetailHeader.TANGGAL))
      document.getElementById("input_detail_tanggal").value = formatDate(dataTableDetailHeader.TANGGAL)
      document.getElementById("input_detail_nopol").value = dataTableDetailHeader.NoPolKend
      document.getElementById("input_detail_catatan").value = dataTableDetailHeader.Catatan

      document.getElementById("input_detail_nobukti").value = nobukti
      document.getElementById("input_detail_nourut").value = dataTableDetailHeader.NOURUT

      document.getElementById("input_detail_nosj").value = dataFormDetail.NoSPB
      document.getElementById("input_detail_noso").value = dataFormDetail.NoSC
      document.getElementById("input_detail_customer").value = dataFormDetail.NAMACUSTSUPP + '\n' + dataFormDetail.Alamat
      document.getElementById("input_detail_kodecustomer").value = dataFormDetail.KodeCustSupp
      document.getElementById("input_detail_gudang").value = dataFormDetail.Nama
      // document.getElementById("input_detail_nopol").value = ''
      //
      // document.getElementById("input_detail_tanggal").value = formatDate(new Date())
      document.getElementById("input_detail_tanggalsj").value = formatDate(dataFormDetail.TglSPB)
      document.getElementById("input_detail_tanggalsc").value = formatDate(dataFormDetail.TglSC)


      let rowTable = ''

      dataTableDetail.forEach((item, i) => {
        rowTable += `
          <tr class="text-left">
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td class='text-right'>${parseFloat(item.QNT).toFixed(2)}</td>
            <td>${item.SAT_1}</td>
            <td class='text-right'>${parseFloat(item.QNT2).toFixed(2)}</td>
            <td>${item.SAT_2}</td>

          </tr>
        `
      });


      document.getElementById("detailTableData").innerHTML = rowTable
       // $("#formDetail").modal('toggle')
       $('#page1').hide();
       $('#page3').show();

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })




  $("#formDetail").modal('toggle')
}

function buttonDetail (nobukti) {
  console.log('buttonDetail' , nobukti)
  let _token = $("#_token").val();
  $('#modalTitleDetail').show();

  $('#buttonSubmitOto').hide();
  $('#modalTitleOto').hide();
  $.ajax({
    url: "{!! url('retursuratjalanspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nobukti,


    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res.detail.length == 0) {
          // addTableData

          alertify.warning('Data habis/ tidak ditemukan')
          // $("#form").modal('toggle')
          return
      }
      dataTableDetail = res.detail
      dataTableDetailHeader = res.header[0]
      dataFormDetail = res.dataForm[0]
      console.log(formatDate(dataTableDetailHeader.TANGGAL))
      document.getElementById("input_detail_tanggal").value = formatDate(dataTableDetailHeader.TANGGAL)
      document.getElementById("input_detail_nopol").value = dataTableDetailHeader.NoPolKend
      document.getElementById("input_detail_catatan").value = dataTableDetailHeader.Catatan

      document.getElementById("input_detail_nobukti").value = nobukti
      document.getElementById("input_detail_nourut").value = dataTableDetailHeader.NOURUT

      document.getElementById("input_detail_nosj").value = dataFormDetail.NoSPB
      document.getElementById("input_detail_noso").value = dataFormDetail.NoSC
      document.getElementById("input_detail_customer").value = dataFormDetail.NAMACUSTSUPP + '\n' + dataFormDetail.Alamat
      document.getElementById("input_detail_kodecustomer").value = dataFormDetail.KodeCustSupp
      document.getElementById("input_detail_gudang").value = dataFormDetail.Nama
      // document.getElementById("input_detail_nopol").value = ''
      //
      // document.getElementById("input_detail_tanggal").value = formatDate(new Date())
      document.getElementById("input_detail_tanggalsj").value = formatDate(dataFormDetail.TglSPB)
      document.getElementById("input_detail_tanggalsc").value = formatDate(dataFormDetail.TglSC)


      let rowTable = ''

      dataTableDetail.forEach((item, i) => {
        rowTable += `
          <tr class="text-left">
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td class='text-right'>${parseFloat(item.QNT).toFixed(2)}</td>
            <td>${item.SAT_1}</td>
            <td class='text-right'>${parseFloat(item.QNT2).toFixed(2)}</td>
            <td>${item.SAT_2}</td>

          </tr>
        `
      });


      document.getElementById("detailTableData").innerHTML = rowTable
       // $("#formDetail").modal('toggle')
       $('#page1').hide();
       $('#page3').show();


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })




  // $("#formDetail").modal('toggle')
}

function refreshDataTableAdd (nobukti) {
  console.log('refreshDataTableAdd')
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('retursuratjalanspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nobukti,


    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res.detail.length == 0) {
          // addTableData

          alertify.warning('Data habis/ tidak ditemukan')
          // $("#form").modal('toggle')

          $('#page2').hide();
          $('#page1').show();

          return
      }
      dataTableAdd = res.detail
      dataTableAddHeader = res.header[0]
      dataForm = res.dataForm[0]
      console.log(formatDate(dataTableAddHeader.TANGGAL))
      document.getElementById("input_add_tanggal").value = formatDate(dataTableAddHeader.TANGGAL)
      document.getElementById("input_add_nopol").value = dataTableAddHeader.NoPolKend
      document.getElementById("input_add_catatan").value = dataTableAddHeader.Catatan

      document.getElementById("input_add_nobukti").value = nobukti
      document.getElementById("input_add_nourut").value = dataTableAddHeader.NOURUT

      document.getElementById("input_add_nosj").value = dataForm.NoSPB
      document.getElementById("input_add_noso").value = dataForm.NoSC
      document.getElementById("input_add_customer").value = dataForm.NAMACUSTSUPP + '\n' + dataForm.Alamat
      document.getElementById("input_add_kodecustomer").value = dataForm.KodeCustSupp
      document.getElementById("input_add_gudang").value = dataForm.Nama
      // document.getElementById("input_add_nopol").value = ''
      //
      // document.getElementById("input_add_tanggal").value = formatDate(new Date())
      document.getElementById("input_add_tanggalsj").value = formatDate(dataForm.TglSPB)
      document.getElementById("input_add_tanggalsc").value = formatDate(dataForm.TglSC)

      document.getElementById("input_add_catatan").disabled = true;
      document.getElementById("input_add_nopol").disabled = true;
      document.getElementById("input_add_tanggal").disabled = true;

      let rowTable = ''

      dataTableAdd.forEach((item, i) => {
        rowTable += `
          <tr class="text-left">
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td class='text-right'>${parseFloat(item.QNT).toFixed(2)}</td>
            <td>${item.SAT_1}</td>
            <td class='text-right'>${parseFloat(item.QNT2).toFixed(2)}</td>
            <td>${item.SAT_2}</td>
            <td class='text-center'>
              <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEdit(${i})"><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDelete('${item.NOBUKTI}' ,  ${item.URUT})"><i class="bi bi-trash"></i></button>


            </td>
          </tr>
        `
      });


      document.getElementById("addTableData").innerHTML = rowTable


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}


function submitAddEdit () {
  let tempKodeBarang = $("#AddEditKodeBrg").val();

  let tempNamaBarang = $("#AddEditNamaBrg").val();
  let choice = 'U'
  let _token = $("#_token").val();

  console.log(dataForm)
  console.log(dataTableAddHeader)
  console.log(dataBarangEdit)

  let tempQnt = $("#AddEditInputQty").val();
  console.log(tempQnt)

  if (Number(tempQnt) <= 0) {
    alertify.warning("Qty <= 0")
    return
  }




  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let tanggal = $("#input_add_tanggal").val();
  let nopol = $("#input_add_nopol").val();
  let catatan = $("#input_add_catatan").val();
  let retursupp = $("#AddEditReturSupp").val();

  let flagtipe = 0
  if (Number(dataBarangEdit.FlagTipe)) {
    flagtipe = 1
  }

  let tempQnt1 = tempQnt * dataBarangEdit.ISI

  console.log({
    _token : _token,
    choice,
    nobukti,
    nourut,
    tanggal,
    nosj : dataBarangEdit.NoSC,
    kodecustsupp: dataForm.KodeCustSupp,
    nopol,
    container: '',
    nocontainer : '',
    noseal : '',
    catatan,
    urut : dataBarangEdit.URUT,
    urutsj : dataBarangEdit.UrutSC,
    kodebrg : tempKodeBarang,
    qnt: tempQnt1,
    qnt2: tempQnt,
    sat1: dataBarangEdit.SAT_1,
    sat2:  dataBarangEdit.SAT_2,
    nosat : dataBarangEdit.NOSAT,
    isi :  dataBarangEdit.ISI,
    netw : 0,
    grossw: 0,
    isempty: dataTableAdd.length,
    namabrg: tempNamaBarang,
    flagmenu: 0,
    flagtipe,
    retursupp

  })
  // return

  $.ajax({
    url: "{!! url('retursuratjalanspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      nosj : dataBarangEdit.NoSC,
      kodecustsupp: dataForm.KodeCustSupp,
      nopol,
      container: '',
      nocontainer : '',
      noseal : '',
      catatan,
      urut : dataBarangEdit.URUT,
      urutsj : dataBarangEdit.UrutSC,
      kodebrg : tempKodeBarang,
      qnt: tempQnt1,
      qnt2: tempQnt,
      sat1: dataBarangEdit.SAT_1,
      sat2:  dataBarangEdit.SAT_2,
      nosat : dataBarangEdit.NOSAT,
      isi :  dataBarangEdit.ISI,
      netw : 0,
      grossw: 0,
      isempty: dataTableAdd.length,
      namabrg: tempNamaBarang,
      flagmenu: 0,
      flagtipe,
      retursupp

    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res == 1) {
        // loadAll()
        // refreshDataTableKoreksi(nobukti)
        refreshDataTableAdd(nobukti)
        $(".showhide").hide()
        // $("#form").modal('toggle')
        alertify.success('Item telah diedit');
        // console.log('before ===========')
        // loadAll()
        // console.log("after ===========")
      }




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })



}


function submitAddAdd () {
  let tempKodeBarang = $("#AddAddKodeBrg").val();

  let tempNamaBarang = $("#AddAddNamaBrg").val();
  let choice = 'I'
  let _token = $("#_token").val();

  if (!tempKodeBarang) {
    alertify.warning("Pilih barang terlebih dahulu")

    return
  }



  console.log(dataForm)
  console.log(dataBarangAdd)

  let tempQnt = $("#AddAddInputQty").val();
  console.log(tempQnt)

  if (Number(tempQnt) <= 0) {
    alertify.warning("Qty <= 0")
    return
  }




  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let tanggal = $("#input_add_tanggal").val();
  let nopol = $("#input_add_nopol").val();
  let catatan = $("#input_add_catatan").val();
  let retursupp = $("#AddAddReturSupp").val();

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  let checkDate = new Date(tanggal)

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let flagtipe = 0
  if (Number(dataForm.FlagTipe)) {
    flagtipe = 1
  }

  console.log({
    choice,
    nobukti,
    nourut,
    tanggal,
    nosj : dataBarangAdd.nobukti,
    kodecustsupp: dataForm.KodeCustSupp,
    nopol,
    container: '',
    nocontainer : '',
    noseal : '',
    catatan,
    urut : 0,
    urutsj : dataBarangAdd.urut,
    kodebrg : tempKodeBarang,
    qnt: dataBarangAdd.qnt,
    qnt2: dataBarangAdd.qnt2,
    sat1: dataBarangAdd.Sat_1,
    sat2:  dataBarangAdd.sat_2,
    nosat : dataBarangAdd.nosat,
    isi :  dataBarangAdd.isi,
    netw : 0,
    grossw: 0,
    isempty: dataTableAdd.length,
    namabrg: tempNamaBarang,
    flagmenu: 0,
    flagtipe,
    retursupp
  })

  let tempQnt1 = tempQnt * dataBarangAdd.isi

  $.ajax({
    url: "{!! url('retursuratjalanspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      nosj : dataBarangAdd.nobukti,
      kodecustsupp: dataForm.KodeCustSupp,
      nopol,
      container: '',
      nocontainer : '',
      noseal : '',
      catatan,
      urut : 0,
      urutsj : dataBarangAdd.urut,
      kodebrg : tempKodeBarang,
      qnt: tempQnt1,
      qnt2: tempQnt,
      sat1: dataBarangAdd.Sat_1,
      sat2:  dataBarangAdd.sat_2,
      nosat : dataBarangAdd.nosat,
      isi :  dataBarangAdd.isi,
      netw : 0,
      grossw: 0,
      isempty: dataTableAdd.length,
      namabrg: tempNamaBarang,
      flagmenu: 0,
      flagtipe,
      retursupp

    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res == 1) {
        loadAll()
        // refreshDataTableKoreksi(nobukti)
        document.getElementById("input_add_catatan").disabled = true;
        document.getElementById("input_add_nopol").disabled = true;

        document.getElementById("buttonAddListNoSJ").disabled = true;
        document.getElementById("buttonAddListCustSupp").disabled = true;
        refreshDataTableAdd(nobukti)
        $(".showhide").hide()
        // $("#form").modal('toggle')
        alertify.success('Item telah ditambah');
        // console.log('before ===========')
        // loadAll()
        // console.log("after ===========")
      }

      if (res == 2) {
        setNewNoBukti(dataForm.PPNCUST)
        alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}


function buttonAddDelete (nobukti , urut) {

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  console.log('buttonAddDelete')
  console.log(nobukti, urut)


  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"

        $.ajax({
          url: "{!! url('retursuratjalanspadd') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            choice,
            nobukti,
            nourut : '',
            tanggal: '',
            nosj : '',
            kodecustsupp: '',
            nopol: '' ,
            container: '',
            nocontainer : '',
            noseal : '',
            catatan : '',
            urut : urut,
            urutsj : 0,
            kodebrg : '',
            qnt: 0,
            qnt2: 0,
            sat1: '',
            sat2:  '',
            nosat : 1,
            isi :  1,
            netw : 0,
            grossw: 0,
            isempty: 1,
            namabrg: '',
            flagmenu: 0,
            flagtipe: 0,
            retursupp : 0

          },
          success: function(res) {
            console.log('res delete', res)
            loadAll()
            refreshDataTableAdd(nobukti)
            // lockFormAdd()
            $('.showhide').hide();
            // refreshDataTableAdd(nobukti)

            alertify.success('Berhasil menghapus item')

          },
          error: function (err) {
            console.log('err delete')
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
    });
}

function buttonAddListBarang () {

  let tempNoSJ = $("#input_add_nosj").val();
  if (!tempNoSJ) {
    alertify.warning("Pilih SJ terlebih dahulu")

    return
  }


  // $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListBarang').show();
  // $('.showhidemodalfooteradd').hide();
  // $('#modalBodyFooterList').show();

  listBarang = []
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('retursuratjalanlistbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti : tempNoSJ,
    },
    success: function(res) {
      console.log(res)
      listBarang = res

      if (!res.length) {
        alertify.warning('Tidak ada barang untuk ditambah')
        return
      }
      let rowTable = ``
      listBarang.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickBarang(${i})" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.kodebrg}</td>
        <td>${item.Namabrg}</td>
        <td class="text-right">${parseFloat(item.Qty).toFixed(2)}</td>
        <td>${item.Satuan}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_barang").innerHTML = rowTable
      loadAll()
      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListBarang').show();
      // showhidemodalfooteradd
      $('.showhidemodalfooteradd').hide();
      $('#modalBodyFooterList').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}



function buttonAddListCustSupp () {

  listCust = []
  $.ajax({
    url: "{!! url('retursuratjalanlistcustsuppbaru') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      console.log(res)
      $('#tabel_add_list_custsupp').DataTable().destroy();

      listCust = res
      let rowTable = ``
      listCust.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickCust(${i})" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.KodeCustSupp}</td>
        <td>${item.NAMACUSTSUPP}</td>
        </tr>`
      });




      if(!res.length) {
        rowTable= ``
      }
      document.getElementById("tabel_data_add_list_custsupp").innerHTML = rowTable

      $("#tabel_add_list_custsupp").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[1, 'asc']],
            "columnDefs": [
                 {"targets" :[0] , 'orderable' : false}
              ]
        });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListCust').show();
      // showhidemodalfooteradd
      $('.showhidemodalfooteradd').hide();
      $('#modalBodyFooterList').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function buttonAddListNoSJ () {
  let xkodecust = $("#input_add_kodecustomer").val()

  if (!xkodecust) {

    alertify.warning("Pilih Cust Terlebih dahulu")
    return
  }
  let _token = $("#_token").val()

  listSJ = []
  $.ajax({
    url: "{!! url('retursuratjalanlistsj') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp : xkodecust

    },
    success: function(res) {
      console.log(res)
      $('#tabel_add_list_nosj').DataTable().destroy();

      listSJ = res
      let rowTable = ``
      listSJ.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickNoSJ(${i})" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.NoSPB}</td>
        <td>${formatDate(item.TglSPB , '/')}</td>
        <td class="text-right">${item.PPNCUST}</td>
        <td>${item.KodeCustSupp}</td>
        <td>${item.NAMACUSTSUPP}</td>
        </tr>`
      });




      if(!res.length) {
        rowTable= ``
      }
      document.getElementById("tabel_data_add_list_nosj").innerHTML = rowTable

      $("#tabel_add_list_nosj").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[1, 'asc']],
            "columnDefs": [
                 {"targets" :[0] , 'orderable' : false}
              ]
        });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListNoSJ').show();
      // showhidemodalfooteradd
      $('.showhidemodalfooteradd').hide();
      $('#modalBodyFooterList').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
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




function buttonKoreksi (nobukti , oto) {
  console.log('buttonKoreksi')
  console.log(nobukti)
  console.log(oto)

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  if (Number(oto)) {
    alertify.warning('RSPB sudah diotorisasi')
    return
  }
  dataTableAdd = []
  dataForm = {}

  resetForm()

  // $('#buttonAddListNoSJ').hide();
  $('.showhide').hide();
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();
  document.getElementById("input_add_catatan").disabled = true;
  document.getElementById("input_add_nopol").disabled = true;
  document.getElementById("input_add_tanggal").disabled = true;
  refreshDataTableAdd(nobukti)
  document.getElementById("buttonAddListNoSJ").disabled = true;
  document.getElementById("buttonAddListCustSupp").disabled = true;
  $('.showhidemodalfooteradd').hide();
  $('#modalBodyFooterMain').show();
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();

}

function buttonAdd () {

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  dataTableAdd = []
  resetForm()
  // setNewNoBukti()
  $('#buttonAddListNoSJ').show();
  $('.showhide').hide();
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();
  document.getElementById("input_add_nobukti").value = '';

  document.getElementById("input_add_catatan").disabled = false;
  document.getElementById("input_add_nopol").disabled = false;
  document.getElementById("input_add_tanggal").disabled = false;
  document.getElementById("buttonAddListNoSJ").disabled = false;
  document.getElementById("buttonAddListCustSupp").disabled = false;
  document.getElementById("addTableData").innerHTML = `<tr >

      <td colspan=7 class="text-center">Belum ada data</td>

</tr>`;

  $('.showhidemodalfooteradd').hide();
  $('#modalBodyFooterMain').show();
  // $("#form").modal('toggle')
  $('#page2').show();
  $('#page1').hide();

}

function resetFormAddAdd () {
  document.getElementById("AddAddKodeBrg").value = ''
  document.getElementById("AddAddNamaBrg").value = ''
  document.getElementById("AddAddInputSatuan").value = ''
  document.getElementById("AddAddInputQty").value = '0.00'
  document.getElementById("AddAddReturSupp").value = 0
}

function resetForm () {
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_nosj").value = ''
  document.getElementById("input_add_noso").value = ''
  document.getElementById("input_add_customer").value = ''
  document.getElementById("input_add_kodecustomer").value = ''
  document.getElementById("input_add_gudang").value = ''
  document.getElementById("input_add_nopol").value = ''

  document.getElementById("input_add_tanggal").value = formatDate(new Date())
  document.getElementById("input_add_tanggalsj").value = ''
  document.getElementById("input_add_tanggalsc").value = ''
  document.getElementById("input_add_catatan").value = ''

}

function buttonAddEdit (i) {
  console.log('buttonAddEdit')
  console.log(dataTableAdd[i])
  dataBarangEdit = dataTableAdd[i]
  // return
  document.getElementById("AddEditReturSupp").value = Number(dataBarangEdit.FlagKembali)

  document.getElementById("AddEditKodeBrg").value = dataBarangEdit.KODEBRG
  document.getElementById("AddEditNamaBrg").value = dataBarangEdit.NAMABRG
  if (dataBarangEdit.NOSAT == 1) {

    document.getElementById("AddEditInputSatuan").value = dataBarangEdit.SAT_1
    document.getElementById("AddEditInputQty").value = parseFloat(dataBarangEdit.QNT).toFixed(2)
  } else {
    document.getElementById("AddEditInputSatuan").value = dataBarangEdit.SAT_2
    document.getElementById("AddEditInputQty").value = parseFloat(dataBarangEdit.QNT2).toFixed(2)
  }

  $('.showhide').hide();
  $('#formAddEdit').show();

  document.getElementById("AddEditKodeBrg").scrollIntoView();
}

function buttonAddAdd () {
  console.log('buttonAddAdd')
    // tipeformitem = 'Add'
    // document.getElementById("AddAddKodeBrg").value = ''
    // document.getElementById("AddAddNamaBrg").value = ''
    // document.getElementById("AddAddInputSatuan").value = ''
    // document.getElementById("AddAddInputQty").value = '0.00'
    // document.getElementById("AddAddReturSupp").value = 0

    if (!$("#input_add_nosj").val()) {
      alertify.warning("Pilih SJ terlebih dahulu")

      return
    }

    resetFormAddAdd()
    $('.showhide').hide();
    $('#formAddAdd').show();
    document.getElementById("AddAddKodeBrg").scrollIntoView();

}

function buttonBatalAdd () {

    $('.showhide').hide();
}



function setNewNoBukti (ppn) {
  $.ajax({
    url: "{!! url('retursuratjalanspnobukti') !!}",
    type: "get",
    async: false,
    data: {
      ppn
    },
    success: function(res) {
      console.log(res)

      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}


</script>

<script>
  // const tabHome = document.getElementById('nav-home-tab');
  // const tabProfile = document.getElementById('nav-profile-tab');

  function setActiveTab(idNav) {
    $(".nav-item").css("background-color", "#f8f9fa");
    $(".nav-item").css("color", "#007bff");
    console.log(idNav)
    document.getElementById(idNav).style.backgroundColor = '#007bff';
    document.getElementById(idNav).style.color = '#fff';

  }

  // Default warna tab
  setActiveTab("nav-home-tab");

  // buat ganti tab
  document.getElementById('nav-home-tab').addEventListener('click', function () {
    setActiveTab("nav-home-tab");
  });

  document.getElementById('nav-profile-tab').addEventListener('click', function () {
    setActiveTab("nav-profile-tab");
  });

</script>




@endsection
