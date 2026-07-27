@extends('newmaster')
@section('buttons')

@endsection
@section('content')

<link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}">

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Ekspedisi</span>
  </div> --}}

  <div class="sp-page-head">
    <div>
      <h1>Master Ekspedisi</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Ekspedisi</button>
  </div>

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />


  @include('master.partials.headerTableMaster')

  <div class="table-outer">
    <div class="table-wrap">
      <table class="tb" id="tabel">
        <thead>
          <tr>
            <th scope="col">Actions</th>
            <th scope="col">Kode</th>
            <th scope="col">Bentuk Usaha</th>
            <th scope="col">Nama</th>
            <th scope="col">Alamat</th>
            <th scope="col">Kota</th>
            <th scope="col">Kode Pos</th>
            <th scope="col">Negara</th>
            <th scope="col">Telepon</th>
            <th scope="col">Fax</th>
            <th scope="col">Email</th>
            <th scope="col">Pph 23</th>
            <th scope="col">Pph 21</th>
            <th scope="col">PPN</th>
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
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 800px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row mb-1">

              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Kode</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_kode" placeholder="Kode">
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Tipe Usaha</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_bentukUsaha" placeholder="Bentuk Usaha">
                </div>
              </div>

            </div>

            <div class="row mb-1">

              <div class="col-6 text-left">
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">PPN</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <select id='input_add_ppn' class='form-control'>
                    <option value=0>Tidak</option>
                    <option value=1>Iya</option>
                  </select>
                </div>
              </div>

            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Nama</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_nama" placeholder="Nama">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Alamat</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_alamat" placeholder="Alamat 1">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_alamat2" placeholder="Alamat 2">
                </div>
              </div>

            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Kota</label>
                </div>
              </div>
              <div class="col-10">
                  <div class="input-group mb-1">
                      <input type="text" class="form-control" id="input_add_kota" placeholder="Kode Kota">
                      <div class="input-group-append">
                          <button type="button" class="btn btn-primary btn-select" onclick="buttonKota()">+</button>
                      </div>
                  </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Nama Kota</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_namaArea" placeholder="Nama Kota" readonly>
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Kode Area</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_kodeArea" placeholder="Kode Area" readonly>
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Kode Pos</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_kodePos" placeholder="Kode Pos">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Negara</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_negara" placeholder="Negara">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Telepon</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_telepon" placeholder="Telepon">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">PPH 23</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group mb-1">
                  <input type="number" class="form-control text-right" id="input_add_pph23" placeholder="PPH 23">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">No. Fax</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_noFax" placeholder="Nomor Fax">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">PPH 21</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group mb-1">
                  <input type="number" class="form-control text-right" id="input_add_pph21" placeholder="PPH 21">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">E-Mail</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_email" placeholder="E-Mail">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Aktif</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group mb-1">
                  <select id="input_add_aktif" class='form-control'>
                    <option value=0 selected>Non-Aktif</option>
                    <option value=1>Aktif</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Att</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_att" placeholder="Att">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Att Phone</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_attPhone" placeholder="Att Phone">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Att Depart</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_attDepart" placeholder="Att Depart">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Bank</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_bank" placeholder="Bank">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Acc. No</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_accNo" placeholder="No. Acc">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">A/N</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_atasNama" placeholder="Atas Nama">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-12 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left text-primary">Data Pajak</label>
                  <hr>
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">NPWP</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_NPWP" placeholder="NPWP">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Nama</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_namaPajak" placeholder="Atas Nama">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Alamat</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_alamatPajak" placeholder="Alamat Pajak 1">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_alamatPajak2" placeholder="Alamat Pajak 2">
                </div>
              </div>
            </div>

            
            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Kota</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_pajakKota" placeholder="Kota">
                </div>
              </div>
            </div>

    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
    <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->

<!-- start modal edit -->
<div class="modal fade"  id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 800px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_edit_noUrut" value="" />

            <div class="row mb-1">

              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Kode</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_kode" placeholder="Kode" disabled>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Tipe Usaha</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_bentukUsaha" placeholder="Bentuk Usaha">
                </div>
              </div>

            </div>

            <div class="row mb-1">
              <div class="col-6 text-left">
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">PPN</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <select id='input_edit_isPpn' class='form-control'>
                    <option value=0>Tidak</option>
                    <option value=1>Iya</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Nama</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_nama" placeholder="Nama">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Alamat</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_alamat" placeholder="Alamat 1">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_alamat2" placeholder="Alamat 2">
                </div>
              </div>

            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Kota</label>
                </div>
              </div>
              <div class="col-10">
                  <div class="input-group mb-1">
                      <input type="text" class="form-control" id="input_edit_kota" placeholder="Kode Kota">
                      <div class="input-group-append">
                          <button type="button" class="btn btn-primary btn-select" onclick="buttonKota()">+</button>
                      </div>
                  </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Nama Kota</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_namaArea" placeholder="Nama Kota" readonly>
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Kode Area</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_kodeArea" placeholder="Kode Area" readonly>
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Kode Pos</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_kodePos" placeholder="Kode Pos">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Negara</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_negara" placeholder="Negara">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Telepon</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_telepon" placeholder="Telepon">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">PPH 23</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group mb-1">
                  <input type="number" class="form-control text-right" id="input_edit_pph23" placeholder="PPH 23">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">No. Fax</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_noFax" placeholder="Nomor Fax">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">PPH 21</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group mb-1">
                  <input type="number" class="form-control text-right" id="input_edit_pph21" placeholder="PPH 21">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">E-Mail</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_email" placeholder="E-Mail">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Aktif</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group mb-1">
                  <select id="input_edit_aktif" class='form-control'>
                    <option value=0 selected>Non-Aktif</option>
                    <option value=1>Aktif</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Att</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_att" placeholder="Att">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Att Phone</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_attPhone" placeholder="Att Phone">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Att Depart</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_attDepart" placeholder="Att Depart">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Bank</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_bank" placeholder="Bank">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Acc. No</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_accNo" placeholder="No. Acc">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">A/N</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_atasNama" placeholder="Atas Nama">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-12 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left text-primary">Data Pajak</label>
                  <hr>
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">NPWP</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_NPWP" placeholder="NPWP">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Nama</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_namaPajak" placeholder="Atas Nama">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Alamat</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_alamatPajak" placeholder="Alamat Pajak 1">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_alamatPajak2" placeholder="Alamat Pajak 2">
                </div>
              </div>
            </div>

            
            <div class="row mb-1">
              <div class="col-2 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Kota</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_pajakKota" placeholder="Kota">
                </div>
              </div>
            </div>

    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
    <button type="button" class="btn btn-primary" onclick="submitEdit()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal edit-->

<!-- start modal detail akun -->
<div class="modal fade"  id="formDetailAkun" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Akun</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <div class="row">

            <input type="hidden" id="input_harga_kodegroup" value="" />
            <div class="col-2">
              <div class="form-group">
                <label>Kode Cust Supp</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_target_kodesales" placeholder="Kode Cust Supp" disabled>
              </div>
            </div>
          
            <div class="col-2 ml-auto text-right">
              <button type="button" class="btn btn-primary" onclick="buttonAddDetailAkun()" class="btn btn-secondary">Add Detail Akun</button>
            </div>

          </div>
    </div>

    <!-- ADD SUBGROUP -->

    <div id="addDetailAkun" class="container-fluid showhide">

            <div class="row">
              <div class="col-4">
                <h4>Add Detail Akun</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Hutang / Piutang</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_detailAkun_add_hutPiut" placeholder="Hutang Piutang">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary btn-select" onclick="buttonHutangPiutang()">+</button>
                    </div>
                </div>
            </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowSales()" >Batal</button>
                <button type="button" onclick="submitAddDetailAkun()" class="btn btn-primary" >Add</button>
              </div>

            </div>
      </div>

    <!-- END ADD SUBGROUP -->

    <!-- EDIT SUBGROUP -->

    <div id="editDetailAkun" class="container-fluid showhide">

            <div class="row">
              <div class="col-4">
                <h4>Edit Detail Akun</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Hutang / Piutang</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_detailAkun_edit_hutPiut" placeholder="Hutang Piutang">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary btn-select" onclick="buttonHutangPiutang()">+</button>
                    </div>
                </div>
            </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowSales()" >Batal</button>
                <button type="button" onclick="submitEditDetailAkun()" class="btn btn-primary" >Edit</button>
              </div>

            </div>
      </div>

    <!-- END EDIT SUBGROUP -->

        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_detailAkun" class="table table-bordered table-striped"  >
              <thead id='theadCustom' class="text-center">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">Kode</th>
                  <th scope="col">Nama</th>
                  <th scope="col">Perkiraan</th>

                </tr>
              </thead>

              <tbody id="tabel_data_detailAkun" class="text-left" >
              </tbody>

            </table>
          </div>

    </div>
  </div>

</div>
</div>
</div>
<!-- End modal detail akun-->

<!-- start modal alamat kirim -->
<div class="modal fade"  id="formAlamatKirim" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Akun</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <div class="row">

            <input type="hidden" id="input_harga_kodegroup" value="" />
            <div class="col-2">
              <div class="form-group">
                <label>Kode Cust Supp</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_alamatKirim_kodeCustSupp" placeholder="Kode Cust Supp" disabled>
              </div>
            </div>
          
            <div class="col-2 ml-auto text-right">
              <button type="button" class="btn btn-primary" onclick="buttonAddAlamatKirim()" class="btn btn-secondary">Add Alamat Kirim</button>
            </div>

          </div>
    </div>

    <!-- ADD SUBGROUP -->

    <div id="addAlamatKirim" class="container-fluid showhide">

            <div class="row">
              <div class="col-4">
                <h4>Add Alamat Kirim</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Nama</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_nama" placeholder="Nama">
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                 <label>Up</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_up">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Alamat</label>
                </div>
              </div>
              <div class="col-10">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_alamat" placeholder="Alamat">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Telepon</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_telepon" placeholder="Telepon">
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                 <label>Fax</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_fax" placeholder="Fax">
                </div>
              </div>
            </div>
            

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowSales()">Batal</button>
                <button type="button" onclick="submitAddAlamatKirim()" class="btn btn-primary">Add</button>
              </div>

            </div>
      </div>

    <!-- END ADD SUBGROUP -->

    <!-- EDIT SUBGROUP -->

    <div id="editAlamatKirim" class="container-fluid showhide">

            <div class="row">
              <div class="col-4">
                <h4>Edit Detail Akun</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Nama</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_edit_nama" placeholder="Nama">
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                 <label>Up</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_edit_up">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Alamat</label>
                </div>
              </div>
              <div class="col-10">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_edit_alamat" placeholder="Alamat">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Telepon</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_edit_telepon" placeholder="Telepon">
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                 <label>Fax</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_edit_fax" placeholder="Fax">
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowSales()" >Batal</button>
                <button type="button" onclick="submitEditAlamatKirim()" class="btn btn-primary" >Edit</button>
              </div>

            </div>
      </div>

    <!-- END EDIT SUBGROUP -->

        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_alamatKirim" class="table table-bordered table-striped"  >
              <thead id='theadCustom' class="text-center">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">Nama</th>
                  <th scope="col">Alamat</th>
                  <th scope="col">Telepon</th>
                  <th scope="col">Fax</th>

                </tr>
              </thead>

              <tbody id="tabel_data_alamatKirim" class="text-left" >
              </tbody>

            </table>
          </div>

    </div>
  </div>

</div>
</div>
</div>
<!-- End modal alamat kirim-->

@endsection

@section('js')
<script src="{{ asset('js/masterTable.js') }}"></script>
<script type="text/javascript">

let dataRefresh = []

function loadAll () {
  let _token = $("#_token").val();

  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
}

  $.ajax({
    url: "{!! url('masterekspedisiloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      console.log(res)
      dataRefresh = res
  }})

  let rowTable = ""
  dataRefresh.forEach((item, i) => {
    let temp = ""

    let statusCell = '';
    if (item.IsPpn == 0) {
      statusCell = `<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>`;
    } else {
      statusCell = `<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>`;
    }
    rowTable += `<tr>
    <td class='text-center'>
      <div class="action-buttons-wrap">
        <button class="btn-action-sm btn-action-success hover-tooltip" type="button" onclick="buttonEdit('${item.KODECUSTSUPP}')" data-tooltip="Edit Ekspedisi">
              <i class="bi bi-pen"></i>
            </button>
        <button class="btn-action-sm btn-action-danger hover-tooltip" type="button" onclick="buttonDelete('${item.KODECUSTSUPP}')" data-tooltip="Delete Ekspedisi">
              <i class="bi bi-trash"></i>
            </button>
      </div>
    </td>
    <td>${item.KODECUSTSUPP || ''}</td>
    <td>${item.USAHA || ''}</td>
    <td>${item.NAMACUSTSUPP || ''}</td>
    <td>${item.ALAMAT1 || ''}</td>
    <td>${item.namaKota || ''}</td>
    <td>${item.KODEPOS || ''}</td>
    <td>${item.NEGARA || ''}</td>
    <td>${item.TELPON || ''}</td>
    <td>${item.FAX || ''}</td>
    <td>${item.EMAIL || ''}</td>
    <td>${item.NPPH23 || ''}</td>
    <td>${item.NPPH22 || ''}</td>
    ${statusCell}
    </tr>`

  });

  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable({
    "lengthChange": true,
    "paging": true,
    "searching": true,
    "dom": 'tip'
  });

}

function buttonAdd () {

  $("#form").modal('toggle')

}

function buttonEdit (kode) {
  let _token = $("#_token").val();
  let kotaTemp = ''
  $.ajax({
    url: "{!! url('masterEkspedisiLoadDetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      document.getElementById("input_edit_kode").value = res[0].KODECUSTSUPP
      document.getElementById("input_edit_nama").value = res[0].NAMACUSTSUPP
      document.getElementById("input_edit_bentukUsaha").value = res[0].USAHA
      document.getElementById("input_edit_alamat").value = res[0].ALAMAT1
      document.getElementById("input_edit_alamat2").value = res[0].ALAMAT2
      document.getElementById("input_edit_kota").value = res[0].Kota
      document.getElementById("input_edit_isPpn").value = res[0].IsPpn
      document.getElementById("input_edit_kodePos").value = res[0].KODEPOS
      document.getElementById("input_edit_negara").value = res[0].NEGARA
      document.getElementById("input_edit_telepon").value = res[0].TELPON
      document.getElementById("input_edit_pph23").value = res[0].NPPH23
      document.getElementById("input_edit_noFax").value = res[0].FAX
      document.getElementById("input_edit_pph21").value = res[0].NPPH22
      document.getElementById("input_edit_email").value = res[0].EMAIL
      document.getElementById("input_edit_aktif").value = res[0].IsAktif
      document.getElementById("input_edit_att").value = res[0].Att
      document.getElementById("input_edit_attPhone").value = res[0].AttPhone
      document.getElementById("input_edit_attDepart").value = res[0].AttDepart
      document.getElementById("input_edit_bank").value = res[0].bank
      document.getElementById("input_edit_accNo").value = res[0].NoAcc
      document.getElementById("input_edit_atasNama").value = res[0].ATN
      document.getElementById("input_edit_NPWP").value = res[0].NPWP
      document.getElementById("input_edit_pajakKota").value = res[0].KOTAPKP
      document.getElementById("input_edit_namaPajak").value = res[0].NAMAPKP
      document.getElementById("input_edit_alamatPajak").value = res[0].ALAMATPKP1
      document.getElementById("input_edit_alamatPajak2").value = res[0].ALAMATPKP2
      
      // document.getElementById("input_edit_kurs").value = parseFloat(res[0].KURS).toFixed(2);

      kotaTemp = res[0].Kota 
    }})

    $.ajax({
    url: "{!! url('masterEkspedisiLoadKotaEdit') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kodeKota: kotaTemp
    },
    success: function(res) {

      document.getElementById("input_edit_namaArea").value = res[0].NamaKota
      document.getElementById("input_edit_kodeArea").value = res[0].KodeArea

    }})

    $("#formEdit").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();

  alertify.confirm('Hapus Valas', 'Apakah yakin ingin menghapus Data Ekspedisi ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterEkspedisiSubmitDelete') !!}",
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
              alertify.success("Data Ekspedisi telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });

}

function submitEdit () {

  let _token = $("#_token").val();
  
  let kodeCustSupp = $("#input_edit_kode").val(); //KodeCustSupp
  let bentukUsaha = $("#input_edit_bentukUsaha").val(); //Usaha
  let namaCustSupp = $("#input_edit_nama").val(); //NamaCustSupp
  let alamat1 = $("#input_edit_alamat").val(); //Alamat1
  let alamat2 = $("#input_edit_alamat2").val(); //Alamat2
  let kodeKota = $("#input_edit_kota").val(); //KodeKota
  let pCounter = $("#input_edit_counter").val(); //pCounter
  let isPpn = $("#input_edit_isPpn").val(); //IsPpn

  let kodePos = $("#input_edit_kodePos").val(); //KodePos
  let negara = $("#input_edit_negara").val(); //Negara

  let telepon = $("#input_edit_telepon").val(); //Telpon
  let npph23 = $("#input_edit_pph23").val(); //NPPH23
  let noFax = $("#input_edit_noFax").val(); //Fax
  let npph21 = $("#input_edit_pph21").val(); //NPPH22
  let email = $("#input_edit_email").val(); //Email
  let aktif = $("#input_edit_aktif").val(); //IsAktif

  let att = $("#input_edit_att").val(); //Att
  let attPhone = $("#input_edit_attPhone").val(); //AttPhone
  let attDepart = $("#input_edit_attDepart").val(); //AttDepart
  let bank = $("#input_edit_bank").val(); //Bank
  let accNo = $("#input_edit_accNo").val(); //NoACC
  let atasNama = $("#input_edit_atasNama").val(); //ATN

  let npwp = $("#input_edit_NPWP").val(); //NPWP
  let kotaPkp = $("#input_edit_pajakKota").val(); //KotaPKP
  let namaPkp = $("#input_edit_namaPajak").val(); //NamaPKP
  let alamatPkp = $("#input_edit_alamatPajak").val(); //AlamatPkp1
  let alamatPkp2 = $("#input_edit_alamatPajak2").val() //AlamatPkp2

  $.ajax({
    url: "{!! url('masterEkspedisiSubmitEdit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodeCustSupp,
      bentukUsaha,
      namaCustSupp,
      alamat1,
      alamat2,
      
      kodeKota,
      counter: pCounter,
      isPpn,
      kodePos,
      negara,
      telepon,
      npph23,
      
      fax: noFax,
      npph21,
      email,
      isAktif : aktif,
      att,
      attPhone,
      
      attDepart,
      bank,
      accNo,
      atasNama,
      npwp,
      kotaPkp,
      
      namaPkp,
      alamatPkp,
      alamatPkp2
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Data Ekspedisi berhasil dikoreksi.");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}

function submitAdd () {

  let _token = $("#_token").val();
  
  let kodeCustSupp = $("#input_add_kode").val(); //KodeCustSupp
  let bentukUsaha = $("#input_add_bentukUsaha").val(); //Usaha
  let namaCustSupp = $("#input_add_nama").val(); //NamaCustSupp
  let alamat1 = $("#input_add_alamat").val(); //Alamat1
  let alamat2 = $("#input_add_alamat2").val(); //Alamat2
  let kodeKota = $("#input_add_kota").val(); //KodeKota
  let IsPpn = $("#input_add_ppn").val(); //IsPpn

  let kodePos = $("#input_add_kodePos").val(); //KodePos
  let negara = $("#input_add_negara").val(); //Negara

  let telepon = $("#input_add_telepon").val(); //Telpon
  let npph23 = $("#input_add_pph23").val(); //NPPH23
  let noFax = $("#input_add_noFax").val(); //Fax
  let npph21 = $("#input_add_pph21").val(); //NPPH22
  let email = $("#input_add_email").val(); //Email
  let aktif = $("#input_add_aktif").val(); //IsAktif

  let att = $("#input_add_att").val(); //Att
  let attPhone = $("#input_add_attPhone").val(); //AttPhone
  let attDepart = $("#input_add_attDepart").val(); //AttDepart
  let bank = $("#input_add_bank").val(); //Bank
  let accNo = $("#input_add_accNo").val(); //NoACC
  let atasNama = $("#input_add_atasNama").val(); //An

  let npwp = $("#input_add_NPWP").val(); //NPWP
  let kotaPkp = $("#input_add_pajakKota").val(); //KotaPKP
  let namaPkp = $("#input_add_namaPajak").val(); //NamaPKP
  let alamatPkp = $("#input_add_alamatPajak").val(); //AlamatPkp1
  let alamatPkp2 = $("#input_add_alamatPajak2").val() //AlamatPkp2

  if(!attPhone){
    alertify.warning('Att Phone Wajib Diisi')
  }

  $.ajax({
    url: "{!! url('masterEkspedisiSubmitAdd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodeCustSupp,
      bentukUsaha,
      namaCustSupp,
      alamat1,
      alamat2,
      
      kodeKota,
      IsPpn,
      kodePos,
      negara,
      telepon,
      npph23,
      
      fax: noFax,
      npph21,
      email,
      isAktif : aktif,
      att,
      attPhone,
      
      attDepart,
      bank,
      accNo,
      atasNama,
      npwp,
      kotaPkp,
      
      namaPkp,
      alamatPkp,
      alamatPkp2
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Data Ekspedisi berhasil ditambahkan.");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

}

let kodeCustSuppTemp = ''

function buttonEditDetailAkun (kodeCustSupp, urutData) {

urutDetailAkunTemp = urutData;

  $('.showhide').hide();
  $.ajax({
    url: "{!! url('masterEkspedisiLoadDetailAkunEdit') !!}",
    type: "get",
    async: false,
    data: {
      kodeCustSupp,
      urutData,
    },
    success: function(res) {
      document.getElementById("input_detailAkun_edit_hutPiut").value = res[0].Perkiraan
    }})

  $('#editDetailAkun').show()
}

function buttonDetailAkun (kodeCustSupp) {
  
  kodeCustSuppTemp = kodeCustSupp;
  
  $('.showhide').hide();

    $.ajax({
      url: "{!! url('masterEkspedisiLoadDetailAkun') !!}",
      type: "get",
      async: false,
      data: {
        kodeCustSupp
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn btn-success btn-sm hover-tooltip" onclick="buttonEditDetailAkun('${item.KodeCustSupp}', '${item.Urut}')" type="button" data-tooltip="Edit Detail Akun"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm hover-tooltip" onclick="buttonDeleteDetailAkun('${item.KodeCustSupp}', '${item.Urut}')" type="button" data-tooltip="Delete Detail Akun" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.KodeCustSupp}</td>
          <td>${item.NamaPerkiraan}</td>
          <td>${item.Perk_Perkiraan}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_detailAkun").innerHTML = rowTable
        document.getElementById("input_target_kodesales").value = kodeCustSupp

      }})


  $("#formDetailAkun").modal('toggle')
}

function buttonAddDetailAkun () {

  document.getElementById('input_detailAkun_add_hutPiut').value = ''

  $('.showhide').hide();
  $('#addDetailAkun').show()
}

function submitAddDetailAkun () {

  let _token = $("#_token").val();

  let kodeCustSupp = $("#input_target_kodesales").val();
  let Perkiraan = $("#input_detailAkun_add_hutPiut").val();
  let Choice = "I"

  $.ajax({
    url: "{!! url('masterEkspedisiAddDetailAkun') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodeCustSupp,
      Perkiraan,
      choice: Choice
    },
    success: function(res) {
      console.log(res)
      $('.showhide').hide();
      refreshTableDetailAkun(kodeCustSuppTemp)

    }})

}

function refreshTableDetailAkun (kodeCustSupp){

$.ajax({
      url: "{!! url('masterEkspedisiLoadDetailAkun') !!}",
      type: "get",
      async: false,
      data: {
        kodeCustSupp
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn btn-success btn-sm hover-tooltip" onclick="buttonEditDetailAkun('${item.KodeCustSupp}', '${item.Urut}')" type="button" data-tooltip="Edit Detail Akun"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm hover-tooltip" onclick="buttonDeleteDetailAkun('${item.KodeCustSupp}', '${item.Urut}')" type="button" data-tooltip="Delete Detail Akun" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.KodeCustSupp}</td>
          <td>${item.NamaPerkiraan}</td>
          <td>${item.Perk_Perkiraan}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_detailAkun").innerHTML = rowTable
        document.getElementById("input_target_kodesales").value = kodeCustSupp

      }})

}

function buttonDeleteDetailAkun (kodeCustSupp, urutData) {
  
  let _token = $("#_token").val();
  let Choice = 'D';

  alertify.confirm('Hapus Harga', 'Apakah yakin ingin menghapus Sales ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterEkspedisiDeleteDetailAkun') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kodeCustSupp,
            urutData,
            choice : Choice
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              refreshTableDetailAkun(kodeCustSupp)
              alertify.success("Data Detail Akun telah dihapus");

          }
          }})
      }
    ,function(){
      console.log('no')
    });

}

urutDetailAkunTemp = 0

function submitEditDetailAkun () {

  let _token = $("#_token").val();

  let kodeCustSupp = $("#input_target_kodesales").val();
  let OldKodeCustSupp = $("#input_target_kodesales").val();
  let Perkiraan = $("#input_detailAkun_edit_hutPiut").val();
  let Choice = 'U'

$.ajax({
  url: "{!! url('masterEkspedisiSubmitEditDetailAkun') !!}",
  type: "post",
  async: false,
  data: {
    _token,
    choice : Choice,
    kodeCustSupp,
    OldKodeCustSupp,
    Perkiraan,
    urutData : urutDetailAkunTemp
  },
  success: function(res) {
    console.log(res)
    $('.showhide').hide();
    refreshTableDetailAkun(kodeCustSupp)
    alertify.success("Data Detail Akun telah dikoreksi");
  }})

}

function refreshTableAlamatKirim (kodeCustSupp){

$.ajax({
      url: "{!! url('masterEkspedisiLoadAlamatKirim') !!}",
      type: "get",
      async: false,
      data: {
        kodeCustSupp
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn btn-success btn-sm hover-tooltip" onclick="buttonEditAlamatKirim('${item.KodeCustSupp}', '${item.Nomor}')" type="button" data-tooltip="Edit Detail Akun"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm hover-tooltip" onclick="buttonDeleteAlamatKirim('${item.KodeCustSupp}', '${item.Nomor}')" type="button" data-tooltip="Delete Detail Akun" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.Nama}</td>
          <td>${item.AlamatTxt}</td>
          <td>${item.Telp}</td>
          <td>${item.Fax}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_alamatKirim").innerHTML = rowTable
        document.getElementById("input_alamatKirim_kodeCustSupp").value = kodeCustSupp

      }})

}

function buttonAlamatKirim (kodeCustSupp) {
  
  kodeCustSuppTemp = kodeCustSupp;
  
  $('.showhide').hide();

    $.ajax({
      url: "{!! url('masterEkspedisiLoadAlamatKirim') !!}",
      type: "get",
      async: false,
      data: {
        kodeCustSupp
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn btn-success btn-sm hover-tooltip" onclick="buttonEditAlamatKirim('${item.KodeCustSupp}', '${item.Nomor}')" type="button" data-tooltip="Edit Detail Akun"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm hover-tooltip" onclick="buttonDeleteAlamatKirim('${item.KodeCustSupp}', '${item.Nomor}')" type="button" data-tooltip="Delete Detail Akun" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.Nama}</td>
          <td>${item.AlamatTxt}</td>
          <td>${item.Telp}</td>
          <td>${item.Fax}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_alamatKirim").innerHTML = rowTable
        document.getElementById("input_alamatKirim_kodeCustSupp").value = kodeCustSupp

      }})


  $("#formAlamatKirim").modal('toggle')
}

function buttonAddAlamatKirim () {

  $('.showhide').hide();
  $('#addAlamatKirim').show()
}

function buttonEditAlamatKirim (kodeCustSupp, urutData) {

urutDetailAkunTemp = urutData;

  $('.showhide').hide();
  $.ajax({
    url: "{!! url('masterEkspedisiLoadDetailAkunEdit') !!}",
    type: "get",
    async: false,
    data: {
      kodeCustSupp,
      urutData,
    },
    success: function(res) {
      document.getElementById("input_detailAkun_edit_hutPiut").value = res[0].Perkiraan
    }})

  $('#editDetailAkun').show()
}

function submitAddAlamatKirim () {

  let _token = $("#_token").val();

  let kodeCustSupp = $("#input_alamatKirim_kodeCustSupp").val();

  let nomorUrutTemp = 0

  $.ajax({
      url: "{!! url('masterEkspedisiGetNomorAlamatKirim') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        kodeCustSupp
      },
      success: function(res) {
        nomorUrutTemp = parseInt(res[0].Nomor) + 1
      }})

  let Alamat = $("#input_alamatKirim_alamat").val();
  let Telp = $("#input_alamatKirim_telepon").val();
  let Fax = $("#input_alamatKirim_fax").val();
  let up = $("#input_alamatKirim_up").val();
  let Nama = $("#input_alamatKirim_nama").val();

  $.ajax({
    url: "{!! url('masterEkspedisiAddAlamatKirim') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodeCustSupp,
      Nama,
      Alamat,
      Telp,
      Fax,
      Nomor : nomorUrutTemp
    },
    success: function(res) {
      console.log(res)
      $('.showhide').hide();
      refreshTableAlamatKirim(kodeCustSuppTemp)

    }})

}

function buttonDeleteAlamatKirim (kodeCustSupp, nomor) {
  
  let _token = $("#_token").val();

  alertify.confirm('Hapus Data Alamat Kirim', 'Apakah yakin ingin menghapus Alamat Kirim ini?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterEkspedisiDeleteAlamatKirim') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kodeCustSupp,
            Nomor : nomor
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              refreshTableAlamatKirim(kodeCustSupp)
              alertify.success("Data Alamat Kirim telah dihapus");

          }
          }})
      }
    ,function(){
      console.log('no')
    });

}

function buttonEditAlamatKirim (kodeCustSupp, nomor) {

  nomorUrutTemp = nomor

  $('.showhide').hide();
  $.ajax({
    url: "{!! url('masterEkspedisiLoadAlamatKirimEdit') !!}",
    type: "get",
    async: false,
    data: {
      kodeCustSupp,
      Nomor: nomor,
    },
    success: function(res) {
      document.getElementById("input_alamatKirim_edit_nama").value = res[0].Nama
      document.getElementById("input_alamatKirim_edit_up").value = res[0].UP
      document.getElementById("input_alamatKirim_edit_alamat").value = res[0].Alamat
      document.getElementById("input_alamatKirim_edit_telepon").value = res[0].Telp
      document.getElementById("input_alamatKirim_edit_fax").value = res[0].Fax
    }})

  $('#editAlamatKirim').show()
}

function submitEditAlamatKirim () {

  let _token = $("#_token").val();

  let kodeCustSupp = $("#input_alamatKirim_kodeCustSupp").val();
  let nama = $("#input_alamatKirim_edit_nama").val();
  let up = $("#input_alamatKirim_edit_up").val();
  let alamat = $("#input_alamatKirim_edit_alamat").val();
  let telp = $("#input_alamatKirim_edit_telepon").val();
  let fax = $("#input_alamatKirim_edit_fax").val();
  
$.ajax({
  url: "{!! url('masterEkspedisiSubmitEditAlamatKirim') !!}",
  type: "post",
  async: false,
  data: {
    _token,
    kodeCustSupp,
    nama,
    up,
    alamat,
    telp,
    fax,
    nomor : nomorUrutTemp
  },
  success: function(res) {
    console.log(res)
    $('.showhide').hide();
    refreshTableAlamatKirim(kodeCustSupp)
    alertify.success("Data Alamat Kirim telah dikoreksi");
  }})

}

window.onload = function(){
  loadAll();
};

function closeShowSales () {
  $('.showhide').hide();
}

function buttonHutangPiutang () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterEkspedisiLoadHutangPiutang') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kodeCustSuppTemp
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
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectHutangPiutang('${item.Perkiraan}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">Perkiraan</th>
    <th scope="col">Keterangan</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Merk'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectHutangPiutang(perkiraan){
  document.getElementById('input_detailAkun_add_hutPiut').value = perkiraan;
  document.getElementById('input_detailAkun_edit_hutPiut').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

function buttonKota(searchValue = '') {

    if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
        $('#tabelModalOpen').DataTable().destroy();
    }

    $.ajax({
    url: "{!! url('masterEkspedisiLoadKota') !!}",
    type: "get",
    async: false,
    data: {
      kodeCustSuppTemp
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
                <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectKota('${item.KodeKota}', '${item.NamaKota}', '${item.KodeArea}')">
                    <i class="bi bi-plus"></i>
                </button>
            </td>
            <td>${item.KodeKota}</td>
            <td>${item.NamaKota}</td>
            <td>${item.KodeArea}</td>
        </tr>`;
    });

    document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

    let headerTable = `
        <tr>
            <th scope="col">Actions</th>
            <th scope="col">Kode Kota</th>
            <th scope="col">Nama Kota</th>
            <th scope="col">Kode Area</th>
        </tr>
    `;
    document.querySelector("#theadOpen").innerHTML = headerTable;
    document.getElementById("namaModalOpen").innerHTML = 'Kota';

    // Initialize DataTable
    currentDataTable = $("#tabelModalOpen").DataTable({
        "lengthChange": true,
        "paging": true,
    });

    // If search value is provided, search and auto-select
    if (searchValue) {
        handleSearchAndAutoSelect(searchValue);
        if (testModalOpener === 1){
          $("#formModalOpen").modal('toggle');
        }
    }
    
}

function buttonSelectKota(kode, nama, area){
  document.getElementById('input_add_kota').value = kode;
  document.getElementById('input_add_namaArea').value = nama;
  document.getElementById('input_add_kodeArea').value = area;

  document.getElementById('input_edit_kota').value = kode;
  document.getElementById('input_edit_namaArea').value = nama;
  document.getElementById('input_edit_kodeArea').value = area;

  $("#formModalOpen").modal("hide");
}

let testModalOpener = 0

function handleSearchAndAutoSelect(searchValue) {
    if (!currentDataTable) return;

    // Search in DataTable
    currentDataTable.search(searchValue).draw();

    // Get filtered data immediately (no timeout)
    const filteredData = currentDataTable.rows({ search: 'applied' }).data().toArray();
    
    // If exactly one match found, auto-select it
    if (filteredData.length === 1) {
        // Get the matched row data
        const rowData = currentDataTable.row({ search: 'applied' }).data();
        if (rowData && rowData.length >= 4) {
            // Extract data from the row (skip the first column which is the button)
            const kodeKota = $(rowData[1]).text() || rowData[1];
            const namaKota = $(rowData[2]).text() || rowData[2];
            const kodeArea = $(rowData[3]).text() || rowData[3];
            
            // Auto-select the matched item
            buttonSelectKota(kodeKota, namaKota, kodeArea);
            testModalOpener = 0
            console.log(testModalOpener)
        }
    } else if (filteredData.length !== 1){
        testModalOpener = 1
        console.log(testModalOpener)
    }
}

// Reusable function to setup Enter key functionality for any input
function setupEnterKeySearch(inputId, buttonFunction, options = {}) {
    const input = document.getElementById(inputId);
    
    if (!input) {
        console.error(`Input with ID '${inputId}' not found`);
        return;
    }

    // Enter key functionality
    input.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            
            const searchValue = this.value.trim();
            
            // Only proceed if there's a value (unless allowEmpty is true)
            if (searchValue || options.allowEmpty) {
                if (typeof buttonFunction === 'function') {
                    buttonFunction(searchValue);
                } else if (typeof window[buttonFunction] === 'function') {
                    window[buttonFunction](searchValue);
                } else {
                    console.error(`Function '${buttonFunction}' not found`);
                }
            }
        }
    });

    // Find the button and add click functionality
    const button = input.parentElement.querySelector('button');
    
    if (button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            
            const searchValue = input.value.trim(); // Get value from the input, not this
            
            // Only proceed if there's a value (unless allowEmpty is true)
            if (searchValue || options.allowEmpty) {
                if (typeof buttonFunction === 'function') {
                    buttonFunction(searchValue);
                } else if (typeof window[buttonFunction] === 'function') {
                    window[buttonFunction](searchValue);
                } else {
                    console.error(`Function '${buttonFunction}' not found`);
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    setupEnterKeySearch('input_add_kota', 'buttonKota');
    setupEnterKeySearch('input_edit_kota', 'buttonKota');
});


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
