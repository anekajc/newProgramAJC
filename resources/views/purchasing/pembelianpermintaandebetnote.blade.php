@extends('purchasing.newmaster')
@section('buttons')

@endsection

@section('css')
{{-- Scrollbar auto-hide: tidak terlihat sampai kursor ada di area yang bisa di-scroll --}}
<link rel="stylesheet" href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">

{{-- tampilan search bar 1 --}}
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
{{-- end tampilan search bar 2 --}}
@endsection

@section('content')

<div id="page1" class="container-fluid mainpage">
<div class="container-fluid" >
  <!-- <div id="qrcode"></div> -->
  <div class="row" style="margin-top: -30px">
    <div class="col-6 text-left">
      <h2>Debet Note</h2>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600;  " onclick="buttonAdd()">ADD DN</button>
    </div>
  </div>
<!-- <button onclick="loadAll()">tes</button> -->

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
      <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab"
           aria-controls="nav-home" aria-selected="true"
           style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem;">
          Debet Note Belum Otorisasi
        </a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab"
           aria-controls="nav-profile" aria-selected="false"
           style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff;">
          Debet Note Sudah Otorisasi
        </a>
      </div>
    </div>
  </div>

  <div class="card-body">
    <div class="tab-content" id="myTabContent">
      {{-- Belum Otorisasi --}}
      <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
        <table id="tabel" class="table table-bordered table-striped">
          <thead class="text-center bg-primary text-white">
            <tr>
              <th>No Bukti</th>
              <th>Tanggal</th>
              <th>Authorized</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="tabel_data" class="text-left"></tbody>
        </table>
      </div>

      {{-- Sudah Otorisasi --}}
      <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
        <table id="tabel2" class="table table-bordered table-striped">
          <thead class="text-center bg-primary text-white">
            <tr>
              <th>No Bukti</th>
              <th>Tanggal</th>
              <th>Authorized</th>
              <th>User Oto</th>
              <th>Tanggal Oto</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="tabel2_data" class="text-left"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</div>
</div>

<div id="page2" style="display: none" class="mainpage container-fluid" >
  <div class="row" style="margin-top: -30px">
    <div class="col-8 text-left">
      <h2>Form Debit Note</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>
  <div id= "modalAdd" class="">
  <div id="" class="">
  <div class="">
    <!-- <h1>Tes Modal</h1> -->
    <div class="container-fluid">
      <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
      <div class="row">
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-12" >
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
          </div>
        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-12" >
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
      <div class="row" style="margin-top: -10px">
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-12">
              <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Supplier</label>
            </div>
          </div>
          <!-- <div class="col-3 text-right">
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
          <div class="col-md-12" style="margin-top:-10px">
            <div class="form-group">
              <textarea  style="width: 100%; resize: none" rows=1  class="form-control" id="input_add_namacustomer"  disabled></textarea>
            </div>
          </div>
          </div>
        </div>
        <div class="col-md-3" style="margin-top: 10px">
          <div class="row">
            <div class="col-md-12" style="margin-top:-10px">
              <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_add_alamatcustomer"  disabled></textarea>
            </div>
          </div>
        </div>
      </div>
      </div>
<div class="container-fluid">
  <hr/>
</div>
  <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
        <table id="addTable" class="table table-bordered table-striped"  >
          <thead class="text-center bg-primary text-white">
            <tr>
              <th style="padding: 4px 12px;" scope="col">No Inv</th>
              <th style="padding: 4px 12px;" scope="col">Keterangan</th>
              <th style="padding: 4px 12px;" scope="col">Nilai Debet Note</th>
              <th style="padding: 4px 12px;" scope="col">Nilai Inv</th>
              <th style="padding: 4px 12px;" scope="col">Valas</th>
              <th style="padding: 4px 12px;" scope="col">Kurs</th>
              <th style="padding: 4px 12px;" scope="col">Nilai DN Rp</th>
              <th style="padding: 4px 12px;" scope="col">Nilai Invoice Rp</th>
              <th style="padding: 4px 12px;" scope="col">Actions</th>
            </tr>
          </thead>
          <tbody id="addTableData" class="" >
            <tr >
              <td colspan=9 class="text-center">Belum ada data</td>
          </tr>
          </tbody>
        </table>
  </div>
  <div class="col-md-12 mt-2 text-right">
  <button id="buttonAddListInvoice" type="button" class="btn btn-primary" onclick="buttonAddListInvoice()" class="btn btn-secondary" style="height: 30px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;">+ Tambah Invoice</button>
</div>
  </div>
</div>
<!-- </div> -->


<!-- ADD EDIT -->

<div id="formAddEdit" class="container-fluid showhideitem">
  <!-- <div class="line"></div> -->
  <!-- <div class="row"> -->

  <div class="col-12">
  <hr/>
  <div class="row">
    <div class="col-12">
      <h4>Edit Item</h4>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="row">
    <div class="col-md-4">
  <div class="row">
    <div class="col-md-2">
      <div class="form-group">
      <label>No Inv</label>
    </div>
    </div>
    <!-- <div class="col-4 text-right">
      </div> -->
    <div class="col-md-6">
      <div class="input-group form-group">
        <input id="AddEditNoInv" type="text" class="form-control" disabled>
        <!-- <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button> -->
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-2">
      <div class="form-group">
      <label>Ket.</label>
    </div>
    </div>
    <!-- <div class="col-4 text-right">

      </div> -->
    <div class="col-md-6">
      <div class="input-group form-group">
        <input id="AddEditKeterangan" type="text" class="form-control">
        <!-- <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button> -->

      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
<div class="row" style="margin-top: -10px">
  <div class="col-md-2" >
    <div class="row">
  <div class="col-md-4">
    <div class="form-group">
    <label>Valas</label>
  </div>
  </div>
  <div class="col-md-8">
    <input id="AddEditValas" type="text" class="form-control" disabled>
  </div>
</div>
</div>
</div>
<div class="row" style="margin-top: -10px">
  <div class="col-md-2" >
    <div class="row">
  <div class="col-md-4">
    <div class="form-group">
    <label>Kurs</label>
  </div>
  </div>
  <div class="col-md-8">
    <input id="AddEditKurs" type="number" value="1.00" class="form-control text-right" onBlur="onChangeNilaiKursItem()">
  </div>
</div>
</div>
</div>
<div class="row" style="margin-top: -10px">
  <div class="col-md-2" >
    <div class="row">
  <div class="col-md-4">
    <div class="form-group">
    <label>Nilai</label>
  </div>
  </div>
  <div class="col-md-8">
    <input id="AddEditNilai" type="number" value="0.00" class="form-control text-right" onBlur="onChangeNilaiKursItem()">
  </div>
</div>
</div>
<div class="col-md-2" >
  <div class="row">
<div class="col-md-8">
  <input id="AddEditNilaiRp" type="number" value="0.00" class="form-control text-right" disabled>
</div>
</div>
</div>
</div>
<div class="row" style="margin-top: -10px">
  <div class="col-md-2" >
    <div class="row">
  <div class="col-md-4">
    <div class="form-group">
    <label>Nilai Inv</label>
  </div>
  </div>
  <div class="col-md-8">
    <input id="AddEditNilaiInv" type="number" value="0.00" class="form-control text-right" disabled>
  </div>
</div>
</div>
<div class="col-md-2" >
  <div class="row">
<div class="col-md-8">
  <input id="AddEditNilaiInvRp" type="number" value="0.00" class="form-control text-right" disabled>
</div>
</div>
</div>
</div>

  <div class="row mt-2">
    <div class="col-md-12 text-right mt-4">
      <button type="button" class="btn btn-secondary" onclick="buttonAddBatal()" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Batal</button>

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
<!-- </div> -->
    </div>

    <!-- <div class="row "> -->

<!-- </div> -->
  </div>
  <div id="page3" style="display: none" class="mainpage container-fluid" >
    <div class="row" style="margin-top: -30px">
      <div class="col-8 text-left">
        <h2>Form Debet Note</h2>
      </div>
      <div class="col-4 text-right">
        <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
      </div>
    </div>

    <div id= "modalDetail" class="">
    <div id="" class="">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->
      <div class="container-fluid">
        <!-- <input type="hidden" name="noUrut" id="input_add_nourut" value="" /> -->
        <div class="row">
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-12" >
                <div class="row">
              <div class="col-md-4">
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
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-12" >
                <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Tanggal</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}"  disabled>
                </div>
              </div>
            </div>
          </div>
            </div>
          </div>
        </div>
        <div class="row" style="margin-top: -10px">
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-12">
                <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <!-- <div class="col-3 text-right">
              <div class="form-group">
            </div>
          </div> -->
            <div class="col-md-8">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_detail_kodecustomer" placeholder="" disabled>
                <!-- <button class="btn btn-primary btn-sm text-right" id="buttonAddListCustomer" onclick="buttonAddListCustomer()"><i class="bi bi-plus"></i></button> -->
              </div>
            </div>
            </div>
            </div>
            <div class="col-md-12" style="margin-top:-10px">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=1  class="form-control" id="input_detail_namacustomer"  disabled></textarea>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3" style="margin-top: 10px">
            <div class="row">
              <div class="col-md-12" style="margin-top:-10px">
                <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_detail_alamatcustomer"  disabled></textarea>
              </div>
            </div>
          </div>
        </div>
        </div>
  <div class="container-fluid">
    <hr/>
  </div>
    <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
          <table id="detailTable" class="table table-bordered table-striped"  >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">No Inv</th>
                <th style="padding: 4px 12px;" scope="col">Keterangan</th>
                <th style="padding: 4px 12px;" scope="col">Nilai Debet Note</th>
                <th style="padding: 4px 12px;" scope="col">Nilai Inv</th>
                <th style="padding: 4px 12px;" scope="col">Valas</th>
                <th style="padding: 4px 12px;" scope="col">Kurs</th>
                <th style="padding: 4px 12px;" scope="col">Nilai Debet Note</th>
                <th style="padding: 4px 12px;" scope="col">Nilai Invoice Rp</th>
              </tr>
            </thead>
            <tbody id="detailTableData" class="" >
              <tr >
                  <td colspan=9 class="text-center">Belum ada data</td>
            </tr>
            </tbody>
          </table>
    </div>
    <div class="col-md-12 mt-2 text-right" id="divOto">
      <button id="submitOtorisasi" type="button" class="btn btn-primary" onclick="submitOtorisasi()" class="btn btn-secondary" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Otorisasi</button>
    </div>
  </div>
  </div>
  <!-- </div> -->
  <!-- ADD EDIT -->
  <!-- </div> -->
      </div>
      <!-- <div class="row "> -->
  <!-- </div> -->
    </div>
  </div>
</div>

<!--  -->

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">
    <div id="" class="modal-content ">
      <div id= "modalAddListCustomer" class="showhidemodalbodyadd">
      <div class="modal-header">
          <h5 class="modal-title" id="">Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="" class="">
      <div class="modal-body">
        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Customer</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->
            <table id="tabel_add_list_customer" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Alamat</th>
                  <th style="padding: 4px 12px;" scope="col">Kota</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_customer" class="text-left" >
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
      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>
      <div id= "modalAddListInvoice" class="showhidemodalbodyadd">
      <div class="modal-header">
          <h5 class="modal-title" id="">Invoice</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Invoice</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_invoice" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th class="text-center" style="padding: 4px 12px;" scope="col">v</th>
                  <th style="padding: 4px 12px;" scope="col">No Faktur</th>
                  <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                  <th style="padding: 4px 12px;" scope="col">Jatuh Tempo</th>
                  <th style="padding: 4px 12px;" scope="col">Valas</th>
                  <th style="padding: 4px 12px;" scope="col">Nilai Debet Note</th>
                  <th style="padding: 4px 12px;" scope="col">Kurs</th>
                  <th style="padding: 4px 12px;" scope="col">Nilai DN (Rp)</th>
                  <th style="padding: 4px 12px;" scope="col">Piutang (Valas)</th>
                  <th style="padding: 4px 12px;" scope="col">Piutang (Rp)</th>
                  <th style="padding: 4px 12px;" scope="col">Keterangan</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_invoice" class="text-left" >

                <tr >

                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
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
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
        </div>
      </div>
      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="buttonAddPickInvoice()" >Submit</button>
      </div>
      </div>

      </div>

    </div>
  </div>

<!-- End modal add-->



@endsection

@section('js')
<script type="text/javascript">
let listInvoice = []
// let tempNoBukti = ''
let listData = []


let listBarang = []
let tempBarangAddAdd = {}
let tempBarangAddEdit = {}
let dataBarang = []
let tipeform = ''

$(document).ready(function() {
  $("#tabel").DataTable({ lengthChange: false, paging: false });
  $("#tabel2").DataTable({ lengthChange: false, paging: false });

  loadAll();
});

function setNewNoBukti () {
  $.ajax({
    url: "{!! url('debetnotespnobukti') !!}",
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


function cleanFormAddAdd () {
  document.getElementById("AddAddKodeBrg").value = ''
  document.getElementById("AddAddNamaBrg").value = ''
  document.getElementById("AddAddNoBeli").value = ''
  document.getElementById("AddAddUrutBeli").value = 0
  document.getElementById("AddAddInputQty").value = '0.00'
  document.getElementById("AddAddInputNosat").value = 0

  document.getElementById("AddAddInputIsi").value = 0

  document.getElementById("AddAddReturSupp").value = 0

  document.getElementById("AddAddKeterangan").value = ''
  tempBarangAddAdd = {}
  // AddAddReturSupp
}


function closeShowHideAdd () {
  $('.showhide').hide();

}

function cleanFormAdd () {
  document.getElementById("input_add_kodecustomer").value = ''
  document.getElementById("input_add_namacustomer").value = ''
  document.getElementById("input_add_alamatcustomer").value = ''
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
}


function submitAdd () {
  console.log("submitAdd")
  let _token  = $("#_token").val()
  let nobukti  = $("#input_add_nobukti").val()

  let tanggal  = $("#input_add_tanggal").val()
  let nopajak  = $("#input_add_nopajak").val()

  console.log(nobukti , tanggal , nopajak)
}


function submitAddEdit () {
  let barang = tempBarangAddEdit
  let _token  = $("#_token").val()
  let choice = "U"
  let urut = barang.Urut
  let kodecustsupp = barang.KodeSupp
  let nobukti  = $("#input_add_nobukti").val()
  let nourut = $("#input_add_nourut").val();
  let tanggal  = $("#input_add_tanggal").val()
  let noinvoice  = $("#AddEditNoInv").val()
  let nilai  = $("#AddEditNilai").val()
  let nilairp  = $("#AddEditNilaiRp").val()
  let kodevls  = $("#AddEditValas").val()
  let kurs  = $("#AddEditKurs").val()
  let keterangan  = $("#AddEditKeterangan").val()

  if (Number(nilai) < 0 || Number(kurs) < 0 ) {
    alertify.warning("Nilai < 0")
    return
  }

  console.log({
    choice,
    nobukti,
    tanggal,
    urut,
    noinvoice,
    kodecustsupp,
    nilai,
    kodevls,
    kurs,
    nilairp,
    keterangan,
    nourut
  })

  $.ajax({
    url: "{!! url('debetnotespkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      choice,
      nobukti,
      tanggal,
      urut,
      noinvoice,
      kodecustsupp,
      nilai,
      kodevls,
      kurs,
      nilairp,
      keterangan,
      nourut
    },
    success: function(res) {
      if (res == 1) {
        $('.showhideitem').hide();
        loadAll()
        refreshDataTable(nobukti)
        alertify.success('Berhasil Edit Invoice')
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}


function buttonAddDelete (i) {
  console.log(i)

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let barang = listData[i]

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus Invoice ' + barang.NoInv + ' ?',
      function() {
        let _token  = $("#_token").val()

        let choice = "D"
        let urut = barang.Urut
        let kodecustsupp = barang.KodeSupp
        let nobukti  = $("#input_add_nobukti").val()
        let tanggal  = $("#input_add_tanggal").val()
        let noinvoice  = $("#AddEditNoInv").val()
        let nilai  = $("#AddEditNilai").val()
        let nilairp  = $("#AddEditNilaiRp").val()
        let kodevls  = $("#AddEditValas").val()
        let kurs  = $("#AddEditKurs").val()
        console.log(nobukti, urut)
        $.ajax({
          url: "{!! url('debetnotespkoreksi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nobukti,
            tanggal,
            urut,
            noinvoice,
            kodecustsupp,
            nilai,
            kodevls,
            kurs,
            nilairp
          },
          success: function(res) {
            console.log('resspadd', res)
            // return
            if (res == 1) {

              // tipeform = 'edit'
              // lockFormAdd()
              $('.showhideitem').hide();
              // $('.showhideform').hide();
              // $('#modalAdd').show();
              loadAll()
              refreshDataTable(nobukti)
              alertify.success('Berhasil menghapus Invoice')
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

function buttonAddPickInvoice () {
  let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value
  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();

  let tanggal = $("#input_add_tanggal").val();
    let kodecustsupp = $("#input_add_kodecustomer").val();

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  console.log(nourut)
  let _token = $("#_token").val();
  console.log("buttonAddPickInvoice")
  let tempData = []
  // let checkQnt = 0
  let checkMinus = 0
  console.log(listInvoice)
    listInvoice.forEach((item, i) => {
      console.log(document.getElementById(`add_checkbox${i}`).checked)
      if (document.getElementById(`add_checkbox${i}`).checked) {

        let checkNilai = $(`#add_inputQnt${i}`).val();
        let checkKurs = $(`#add_inputKurs${i}`).val();
        let checkNilaiRp = $(`#add_inputQntRp${i}`).val();
        // add_inputKeterangan
        listInvoice[i].Keterangan = $(`#add_inputKeterangan${i}`).val();
        listInvoice[i].inputNilai = checkNilai
        listInvoice[i].inputKurs = checkKurs
        listInvoice[i].inputNilaiRp = checkNilaiRp
        if (Number(checkNilai) < 0 || Number(checkKurs) < 0 ) {
          checkMinus = 1
        }

        tempData.push(listInvoice[i])
      }

    });
    console.log(tempData)

    if (!tempData.length) {
      alertify.warning("Tidak ada item dipilih");
      return
    }

    if (checkMinus) {
      alertify.warning("Qnt < 0");
      return
    }

    $.ajax({
        url: "{!! url('debetnotespadd') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          tempData,
          tanggal: tanggal,
          nobukti,
          nourut,
          kodecustsupp,
          tipeform,
          nourut
        },
        success: function(res) {
          console.log(res ,'!')

          if (res == 1) {
            // $("#form").modal('toggle')
            alertify.success('DN telah ditambah');
            loadAll()
            // buttonCloseForm()
            tipeform = 'edit'
            document.getElementById("buttonAddListCustomer").disabled = true
            document.getElementById("input_add_tanggal").disabled = true

            refreshDataTable(nobukti)

            $("#form").modal('toggle')

          }
          if (res == 2) {
            setNewNoBukti()
            alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
          }
          //
          // if (res == 3 ) {
          //   alertify.warning('Stok gudang tidak mencukupi');
          // }

        },
        error: function (err) {
          console.log(err)
          alertify.warning('Terjadi kesalahan silahkan refresh browser')
        }
      })
}


function buttonAddListInvoice () {
  listInvoice = []

  console.log('buttonAddListInvoice')

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();

  if (!kodecustsupp  ) {
    alertify.warning("Pilih Supplier terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('debetnotelistinvoice') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp,
    },
    success: function(res) {
      console.log(res)
      listInvoice = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += 
        `<tr>
        <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>
        <td>${item.NoFaktur}</td>
        <td>${formatDate(item.Tanggal,'/')}</td>
        <td>${formatDate(item.JatuhTempo,'/')}</td>
        <td>${item.KodeVls}</td>
        <td><input id="add_inputQnt${i}" style="height:30px; min-width: 130px" type="number" value='0.00' class="form-control text-right" onBlur="onChangeNilaiKurs(${i})"></td>

        <td><input id="add_inputKurs${i}" style="height:30px; min-width: 90px" type="number" value='1.00' class="form-control text-right" onBlur="onChangeNilaiKurs(${i})"></td>
        <td><input style="height:30px; min-width: 130px" id="add_inputQntRp${i}" type="number" value='0.00' class="form-control text-right"  disabled></td>

        <td class="text-right">${formatAngka(parseFloat(item.SaldoD).toFixed(2))}</td>
        <td class="text-right">${formatAngka(parseFloat(item.Saldo).toFixed(2))}</td>

        <td><input style="height:30px; min-width: 200px" id="add_inputKeterangan${i}" type="text" value='' class="form-control text-left" ></td>

        </tr>`
      });

      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_invoice").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListInvoice').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Tidak ada invoice untuk ditambah")
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}



function buttonAddListNoBeli () {
  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();
  let noinvoice = $("#input_add_noinvoice").val();
  let noso = $("#input_add_noso").val();
  let kodebrg = $("#AddAddKodeBrg").val();

  if (!kodebrg ) {
    alertify.warning("Pilih barang terlebih dahulu")
    return
  }

  $('#tabel_add_list_nobeli').DataTable().destroy();
  $.ajax({
    url: "{!! url('perintahreturjuallistnobeli') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebrg,
      noso
    },
    success: function(res) {
      let rowTable = ``
      rowTable += `<tr>
      <td>-</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickNoBeli('-' , 0)" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.NOBUKTI}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickNoBeli('${item.NOBUKTI}' ,${item.urut} )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });
      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_nobeli").innerHTML = rowTable
      $("#tabel_add_list_nobeli").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListNoBeli').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function onChangeNilaiKursItem () {
  console.log('onChangeNilaiKursItem' )
  let onChangeQnt = $(`#AddEditNilai`).val();
  let onChangeKurs = $(`#AddEditKurs`).val();

  document.getElementById(`AddEditNilaiRp`).value = parseFloat(Number(onChangeQnt) * Number(onChangeKurs)).toFixed(2)
}

function onChangeNilaiKurs (index) {
  console.log('onChangeNilaiKurs' , index)
  let onChangeQnt = $(`#add_inputQnt${index}`).val();
  let onChangeKurs = $(`#add_inputKurs${index}`).val();

  document.getElementById(`add_inputQntRp${index}`).value = parseFloat(Number(onChangeQnt) * Number(onChangeKurs)).toFixed(2)
}


function buttonAddListCustomer () {
  console.log('buttonAddListCustomer')
  $('#tabel_add_list_customer').DataTable().destroy();
  $.ajax({
    url: "{!! url('debetnotelistcustomer') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td>${item.ALAMAT}</td>
        <td>${item.NamaKota}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickCustomer('${item.KODECUSTSUPP}' , '${item.NAMACUSTSUPP}' , '${item.ALAMAT1}')" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      })
      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_customer").innerHTML = rowTable
      $("#tabel_add_list_customer").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListCustomer').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}


function buttonAddPickCustomer (kode, nama , alamat) {
  console.log('buttonAddPickCustomer')
  console.log(kode,nama,alamat)
  document.getElementById("input_add_kodecustomer").value = kode
  document.getElementById("input_add_namacustomer").value = nama
  document.getElementById("input_add_alamatcustomer").value = alamat

  $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function onChangeQty () {

  console.log('onChangeQty')
  console.log('tempBarangAddAdd' , tempBarangAddAdd)
  let qty = $("#AddAddInputQty").val();
  let nosat = $("#AddAddInputNosat").val();
  console.log('qty' , qty)
  console.log('nosat' , nosat)

  if (jQuery.isEmptyObject(tempBarangAddAdd)) {
    console.log('gk ada barang')
  } else {

    console.log('ada barang')
    let tempIsi = nosat == 1 ? tempBarangAddAdd.Isi1 : tempBarangAddAdd.Isi2
    console.log(tempIsi)
    let tempTotalQty = Number(tempIsi) * Number(qty)

    document.getElementById("AddAddInputQty1").value = tempTotalQty / tempBarangAddAdd.Isi1
    document.getElementById("AddAddInputQty2").value = tempTotalQty / tempBarangAddAdd.Isi2
  }

}


function buttonAddPickNoInvoice (nobukti, noso , kodegdg, flagtipe, ppn) {
  console.log('buttonAddPickNoInvoice')
  document.getElementById("input_add_noinvoice").value = nobukti
  document.getElementById("input_add_noso").value = noso
  document.getElementById("input_add_gudang").value = kodegdg
  document.getElementById("input_add_flagtipe").value = flagtipe
  document.getElementById("input_add_ppn").value = ppn
  $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddBatal () {
  $('.showhideitem').hide();
}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddMain').show();
  $("#form").modal('toggle')
}

// function buttonAddListCustomer () {
//
//   $('.showhidemodalbodyadd').hide();
//   $('#modalBodyAddListValas').show();
//
//   $("#form").modal('toggle')
// }


function closeShowHideItem () {
  $('.showhideitem').hide();
}

function unlockFormAdd () {
  document.getElementById("input_add_catatan").disabled = false
  document.getElementById("input_add_tanggal").disabled = false
  document.getElementById("buttonAddListCustomer").disabled = false
  document.getElementById("buttonAddListNoInvoice").disabled = false
}

function lockFormAdd () {
  document.getElementById("input_add_catatan").disabled = true
  document.getElementById("input_add_tanggal").disabled = true
  document.getElementById("buttonAddListCustomer").disabled = true
  document.getElementById("buttonAddListNoInvoice").disabled = true
}

function refreshDataTable (nobukti) {
  console.log('refreshDataTable' , nobukti)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('debetnotespdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      console.log(res)
      listData = res
      // console.log(res)
      if (!res.length) {
          alertify.success('Data Habis')
          // $("#form").modal('toggle')
          $('#page2').hide();
          $('#page1').show();
          return
      }
      // dataTableAdd = res

      let rowTable = ``
      listData.forEach((item, i) => {
              rowTable += `
                <tr>
                  <td>${item.NoInv}</td>
                  <td>${item.Keterangan ? item.Keterangan : ''}</td>
                  <td class="text-right">${item.Nilai ? formatAngka(parseFloat(item.Nilai).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">0.00</td>

                  <td>${item.kodeVls}</td>
                  <td class="text-right">${item.Kurs ?  formatAngka(parseFloat(item.Kurs).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.NilaiRp ? formatAngka(parseFloat(item.NilaiRp).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>
                  <td class="text-center">
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDelete(${i}  )"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>      `
      });

      document.getElementById("addTableData").innerHTML = rowTable
        document.getElementById("input_add_kodecustomer").value = listData[0].KodeSupp
        document.getElementById("input_add_namacustomer").value = listData[0].NamaCustSupp
        document.getElementById("input_add_alamatcustomer").value = listData[0].Alamat1
        document.getElementById("input_add_nobukti").value = listData[0].NoBukti
        document.getElementById("input_add_tanggal").valueAsDate = new Date(listData[0].tanggal)
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
}

function submitOtorisasi () {
  let _token = $("#_token").val();
  let nobukti = $("#input_detail_nobukti").val();

  $.ajax({
    url: "{!! url('debetnotespotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      alertify.success('Berhasil update otorisasi');
      loadAll();
      buttonCloseForm();
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser.');
    }
  });
}


function buttonDetail (nobukti) {
  document.getElementById("divOto").style.display = "none";
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('debetnotespdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      console.log(res)
      // listData = res
      // console.log(res)
      if (!res.length) {
          alertify.success('Data tidak ditemukkan')
          // $("#form").modal('toggle')
          return
      }
      // dataTableAdd = res

      let rowTable = ``
      res.forEach((item, i) => {
              rowTable += `
                <tr>
                  <td>${item.NoInv}</td>
                  <td>${item.Keterangan}</td>
                  <td class="text-right">${item.Nilai ? formatAngka(parseFloat(item.Nilai).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>  
                  <td>${item.kodeVls}</td>
                  <td class="text-right">${item.Kurs ?  formatAngka(parseFloat(item.Kurs).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.NilaiRp ? formatAngka(parseFloat(item.NilaiRp).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>
                </tr>`
      });

      document.getElementById("detailTableData").innerHTML = rowTable
        document.getElementById("input_detail_kodecustomer").value = res[0].KodeSupp
        document.getElementById("input_detail_namacustomer").value = res[0].NamaCustSupp
        document.getElementById("input_detail_alamatcustomer").value = res[0].Alamat1
        document.getElementById("input_detail_nobukti").value = res[0].NoBukti
        document.getElementById("input_detail_tanggal").valueAsDate = new Date(res[0].tanggal)

        $('#modalDetail').show();
        $('.mainpage').hide();
        $('#page3').show();
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }
  })
}

function buttonOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  alertify.confirm(
    'Konfirmasi Otorisasi',
    'Yakin Ingin Melakukan Otorisasi ' + nobukti + '?', function () {
    let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('debetnotespotorisasi') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti
      },
      success: function (res) {
        alertify.success('Berhasil update otorisasi');
        loadAll();
      },
      error: function (err) {
        console.log(err);
        alertify.warning('Terjadi kesalahan, silakan refresh browser.');
      }
    });
  }, function () {
    alertify.message('Dibatalkan');
  });
}



function buttonKoreksi (nobukti ) {
  console.log('buttonKoreksi' , nobukti )

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  tipeform = 'edit'
  cleanFormAdd()
  document.getElementById("buttonAddListCustomer").disabled = true
  document.getElementById("input_add_tanggal").disabled = true

  refreshDataTable(nobukti)
  if (!listData.length) {
    alertify.warning("Data tidak ditemukkan")
    return
  }
  console.log(listData)
  console.log(listData[0].isOtorisasi1)
  if (listData[0].IsOtorisasi1 == 1) {
    alertify.warning("DN sudah diotorisasi")
    return
  }

  $('.showhideitem').hide();
  $('.showhideform').hide();
  $('#modalAdd').show();
  $('.mainpage').hide();
  $('#page2').show();
}

function buttonAdd (nobukti) {
  console.log('buttonAdd' , nobukti)

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  document.getElementById("input_add_tanggal").disabled = false
  document.getElementById("buttonAddListCustomer").disabled = false
  document.getElementById("addTableData").innerHTML = `<td colspan=9 class="text-center">Belum ada data</td>`
  tipeform = 'add'
  // unlockFormAdd()
  $('.showhideitem').hide();
  $('.showhideform').hide();
  $('#modalAdd').show();
  // $("#form").modal('toggle')

  // input_add_nobukti
  // document.getElementById("input_add_nobukti").value = nobukti

  cleanFormAdd()
  setNewNoBukti()

  $('.mainpage').hide();
  $('#page2').show();
}

function buttonAddAddItem () {
    $('.showhideitem').hide();
    cleanFormAddAdd()
    $('#formAddAdd').show();
}

function buttonAddEditItem (i) {
  tempBarangAddEdit = listData[i]
  console.log(tempBarangAddEdit)
  document.getElementById("AddEditNoInv").value = tempBarangAddEdit.NoInv
  document.getElementById("AddEditValas").value = tempBarangAddEdit.kodeVls
  document.getElementById("AddEditKurs").value = tempBarangAddEdit.Kurs
  document.getElementById("AddEditNilai").value = tempBarangAddEdit.Nilai

  document.getElementById("AddEditKeterangan").value = tempBarangAddEdit.Keterangan

  document.getElementById("AddEditNilaiRp").value = tempBarangAddEdit.NilaiRp
  document.getElementById("AddEditNilaiInv").value = tempBarangAddEdit.Saldo
  document.getElementById("AddEditNilaiInvRp").value = tempBarangAddEdit.Saldo

  // document.getElementById("AddEditKodeBrg").value = tempBarangAddEdit.KODEBRG
  // document.getElementById("AddEditReturSupp").value = tempBarangAddEdit.FlagKembali
  // document.getElementById("AddEditNoBeli").value = tempBarangAddEdit.nobeli
  // document.getElementById("AddEditUrutBeli").value = tempBarangAddEdit.urutbeli
  // document.getElementById("AddEditNamaBrg").value = tempBarangAddEdit.NAMABRG
  // document.getElementById("AddEditInputQty").value = parseFloat(tempBarangAddEdit.QNT).toFixed(2)
  // document.getElementById("AddEditInputQty1").value = parseFloat(tempBarangAddEdit.QNT1).toFixed(2)
  // document.getElementById("AddEditInputQty2").value = parseFloat(tempBarangAddEdit.QNT2).toFixed(2)
  // document.getElementById("AddEditInputSat1").value = tempBarangAddEdit.SAT_1
  // document.getElementById("AddEditInputSat2").value = tempBarangAddEdit.SAT_2
  //
  // document.getElementById("AddEditKeterangan").value = tempBarangAddEdit.KetDetail
  //
  // let selectOption = ''
  // if (tempBarangAddEdit.SAT_1) {
  //   selectOption += `<option value=1 ${tempBarangAddEdit.NoSat == 1 ? 'selected' : ''}>SAT1 - ${tempBarangAddEdit.SAT_1}</option>`
  // }
  // if (tempBarangAddEdit.SAT_2) {
  //   selectOption += `<option value=2 ${tempBarangAddEdit.NoSat == 2 ? 'selected' : ''}>SAT2 - ${tempBarangAddEdit.SAT_2}</option>`
  // }
  // document.getElementById("AddEditInputNosat").innerHTML = selectOption
  //
  //
  $('.showhideitem').hide();
  $('#formAddEdit').show();
}


function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();
}

function loadAll () {
  $('#tabel').DataTable().destroy();
  $('#tabel2').DataTable().destroy();

  let listData1 = [], listData2 = [];

  $.ajax({
    url: "{{ url('debetnoteloadall') }}",
    type: "GET",
    async: false,
    success: function(res) {
      console.log(res);
      listData1 = res.listData1 || [];
      listData2 = res.listData2 || [];
    },
    error: function(xhr, status, error) {
      console.error("Load failed:", error);
      alert("Gagal memuat data Debet Note.");
    }
  });

  let html1 = "", html2 = "";

  listData1.forEach(item => {
    let tglStr = formatDate(item.Tanggal);
    html1 += `
      <tr>
        <td>${item.NoBukti || '-'}</td>
        <td>${tglStr}</td>
        <td class="text-center">
          <span class="text-danger"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></span>
        </td>
        <td class="text-center">
          <button class="btn btn-warning btn-sm" onclick="buttonDetail('${item.NoBukti}')"><i class="bi bi-info"></i></button>
          <button class="btn btn-success btn-sm" onclick="buttonKoreksi('${item.NoBukti}')"><i class="bi bi-pen"></i></button>
          <button class="btn btn-info btn-sm" onclick="buttonOtorisasi('${item.NoBukti}')"><i class="bi bi-key"></i></button>
        </td>
      </tr>`;
  });

  listData2.forEach(item => {
    let tglStr = formatDate(item.Tanggal);
    let tglOtoStr = item.TglOto1 ? formatDate(item.TglOto1) : '-';

    html2 += `
      <tr>
        <td>${item.NoBukti || '-'}</td>
        <td>${tglStr}</td>
        <td class="text-center">
          <span class="text-success"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></span>
        </td>
        <td>${item.OtoUser1 || '-'}</td>
        <td>${tglOtoStr}</td>
        <td class="text-center">
          <button class="btn btn-warning btn-sm" onclick="buttonDetail('${item.NoBukti}')"><i class="bi bi-info"></i></button>
          <button class="btn btn-danger btn-sm" onclick="buttonBatalOtorisasi('${item.NoBukti}', '${item.IsOtorisasi1}')"><i class="bi bi-key"></i></button>
        </td>
      </tr>`;
  });

  $("#tabel_data").html(html1);
  $("#tabel2_data").html(html2);

  $('#tabel').DataTable({ lengthChange: false, paging: false });
  $('#tabel2').DataTable({ lengthChange: false, paging: false });
}


function buttonBatalOtorisasi(nobukti) {
  let akses = $("#akses_isotorisasi1").val();

  // if (!Number(akses)) {
  //   alertify.warning('No access');
  //   return;
  // }

  alertify.confirm(
    'Konfirmasi Batal Otorisasi',
    'Yakin Ingin Membatalkan Otorisasi ' + nobukti + '?',
    function() {
      let _token = $("#_token").val();

      $.ajax({
        url: "{!! url('debetnotespbatalotorisasi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti
        },
        success: function(res) {
          alertify.success('Berhasil batal otorisasi');
          loadAll();
        },
        error: function(err) {
          console.log(err);
          alertify.warning('Terjadi kesalahan, silakan refresh browser.');
        }
      });
    },
    function() {
      console.log('Pembatalan otorisasi dibatalkan');
    }
  );
}

function formatDate(date , pemisah = '/') {
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
}

</script>

{{-- hover belum otorisasi dan sudah otorisasi --}}
  <script>
  const tabHome = document.getElementById('nav-home-tab');
  const tabProfile = document.getElementById('nav-profile-tab');

  function setActiveTab(isHome) {
    if (isHome) {
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

  // Default
  setActiveTab(true);

  tabHome.addEventListener('click', () => setActiveTab(true));
  tabProfile.addEventListener('click', () => setActiveTab(false));
  </script>
{{-- hover belum otorisasi dan sudah otorisasi --}}
@endsection
