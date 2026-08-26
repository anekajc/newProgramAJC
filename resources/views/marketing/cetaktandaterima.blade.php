@extends('newmasterTest')
@section('buttons')

@endsection

@section('css')
<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>
 

<style>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}


/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
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
@endsection


@section('content')


<div id="page1" class="container-fluid mainpage">
  <!--mulai-->
  <div class="modal fade" id="formAldok" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document">
    <div id="" class="modal-content ">
      <div id="modalListCustomer" class="showhideform">

      <div class="modal-header">
          <h5 class="modal-title" id="">Data alamat dan penerima dokumen</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
      </div>


      <div id="" class="">
      <div class="modal-body">





      <div class="col-md-4">
          <div class="form-group">
            <label>Tanggal terima</label>
          </div>
        </div>
        <div class="col-md-8">
          <div class="form-group">
            <input type="date" class="form-control text-left" id="input_add_tglterima" placeholder="" >
          </div>
        </div>

      <div class="col-md-4">
          <div class="form-group">
            <label>Nama Penerima</label>
          </div>
        </div>

        <div class="col-md-8">
          <div class="form-group">
            <input type="text" class="form-control" id="input_add_nama" placeholder="Penerima" >
          </div>
        </div>


        <div class="col-4 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase "
      onclick="updatealdok()"  >Simpan</button>
       </div>







      </div>
    </div>
  </div>
</div>


    </div>
  </div>
  <!--end-->






<div class="container-fluid" >



  <!-- <div id="qrcode"></div> -->
  <div class="row" style="margin-top: -80px">
    <div class="col-6 text-left">
      <h2>Cetak Tanda Terima Invoice</h2>
    </div>
    <div class="col-6 text-right">
      <!-- <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600;  " onclick="buttonAdd()"  >+ Cetak DPH</button> -->
    </div>
  </div>
<!-- <button onclick="loadAll()">tes</button>
<button onclick="tesConcat()">tes</button> -->

<!-- <button onclick="setNewNoBukti()">tes</button> -->
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
  <nav style="width: 100%;">
    <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
      <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true" style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; text-align: left;">Nota belum cetak tanda terima</a>
      <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"
         style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
        Nota sudah cetak tanda terima
      </a>
    </div>
  </nav>
</div>
</div>
<div class="card-body" style="padding:0;">
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    <div class="row">
      <div class="col-12">
        <div class="table-responsive">

              <table id="tabel" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;"  scope="col">No. Bukti</th>
                    <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                    <th style="padding: 4px 12px;"  scope="col">Customer</th>
                    <th style="padding: 4px 12px;"  scope="col">Actions</th>
                  </tr>
                </thead>


                <tbody id="tabel_data" class="text-left" >
                  @for ($i = 0; $i < count($tempOutstanding); $i++)
                <tr>
                  <td>{{ $tempOutstanding[$i][0]->nobukti }}</td>
                  <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i][0]->tanggal)) !!}</td>
                  <td>{{ $tempOutstanding[$i][0]->namacustsupp }}</td>

                  <td class='text-center'>
                    <button class="btn btn-primary btn-sm" type="button" onclick="buttonAdd('{{ $tempOutstanding[$i][0]->kodecustsupp }}')"><i class="bi bi-plus"></i></button>

                  </td>
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
      <div class="col-12">
        <div class="table-responsive">
          <table id="tabel2" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center bg-primary text-white">

              <tr>
                <th style="padding: 4px 12px;"  scope="col">Action</th>
                <th style="padding: 4px 12px;"  scope="col">No. Tanda Terima</th>
                <th style="padding: 4px 12px;"  scope="col">Nobukti</th>
                <th style="padding: 4px 12px;"  scope="col">Tgl Cetak</th>
                <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                <th style="padding: 4px 12px;"  scope="col">Nama Customer</th>
                <th style="padding: 4px 12px;"  scope="col">Tgl Terima</th>
                <th style="padding: 4px 12px;"  scope="col">Nama Penerima</th>
                <th style="padding: 4px 12px;"  scope="col">Oto</th>
                <th style="padding: 4px 12px;"  scope="col">OtoUser</th>
                <th style="padding: 4px 12px;"  scope="col">TglOto</th>
                <th style="padding: 4px 12px;"  scope="col">User Cetak</th>
                <th style="padding: 4px 12px;"  scope="col">Nama Dok</th>
                <th style="padding: 4px 12px;"  scope="col">Alamat Dok</th>


                <!-- <th style="padding: 4px 12px;"  scope="col">Actions</th> -->
              </tr>
            </thead>

            <tbody id="tabel2_data" class="text-left">
              @for ($i = 0; $i < count($tempPenerimaan); $i++)
              <tr>
                <td class='text-center'>
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonAldok('{{ $tempPenerimaan[$i][0]->nocetak }}','{!! date("Y/m/d", strtotime($tempPenerimaan[$i][0]->tglterima)) !!}','{{ $tempPenerimaan[$i][0]->namapenerima }}')"><i class="bi bi-pen"></i></button>
                     <button style="" class="btn btn-primary btn-sm" type="button"   onclick="submitPrintUlang('{{ $tempPenerimaan[$i][0]->nocetak }}')" ><i class="bi bi-printer"></i></button>

                  </td>
           
                <td>{{ $tempPenerimaan[$i][0]->nocetak }}</td>
                <td>{{ $tempPenerimaan[$i][0]->nobukti }}</td>
                <td>{!! date("Y/m/d", strtotime($tempPenerimaan[$i][0]->tglcetak)) !!}</td>
                <td>{!! date("Y/m/d", strtotime($tempPenerimaan[$i][0]->tanggal)) !!}</td>
                <td>{{ $tempPenerimaan[$i][0]->namacustsupp }}</td>
                <td>{!! date("Y/m/d", strtotime($tempPenerimaan[$i][0]->tglterima)) !!}</td>
                <td>{{ $tempPenerimaan[$i][0]->namapenerima }}</td>
                 @if ($tempPenerimaan[$i][0]->IsOtorisasi1)
                            <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                @else
                          <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                @endif
                <td>{{ $tempPenerimaan[$i][0]->OtoUser1 }}</td>
                <td>{!! $tempPenerimaan[$i][0]->TglOto1 ? date("Y/m/d", strtotime($tempPenerimaan[$i][0]->TglOto1))  : '' !!}</td>

                <td>{{ $tempPenerimaan[$i][0]->usercetak }}</td>


                <td>{{ $tempPenerimaan[$i][0]->namadok }}</td>
                <td>{{ $tempPenerimaan[$i][0]->alamatdok }}</td>











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



<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row" style="margin-top: -30px">
    <div class="col-8 text-left">
      <h2>Cetak Tanda Terima Invoice</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
    </div>
  </div>

  <div id= "formAdd" class="">



  <div id="" class="">
  <div class="">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid">

      <div class="row">

        <div class="col-md-3">
          <div class="row">

            <div class="col-md-4">
          <div class="form-group">
            <label>No Urut</label>
          </div>
        </div>

        <div class="col-md-8">
          <div class="form-group">
              <input type="text" name="noUrut" id="input_add_nourut" value="" disabled>
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
              <input type="hidden" class="form-control" id="input_add_nourut" placeholder="" disabled>
            <input type="text" class="form-control" id="input_add_nobukti" placeholder="No Bukti" disabled>
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
            <input type="date" class="form-control text-center" id="input_add_tanggal" placeholder="" disabled>
          </div>
        </div>
      </div>

        </div>

      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="row" style="margin-top: -10px">
            <div class="col-md-6">
              <!-- <div class="row">


                <div class="col-md-4">
                  <div class="form-group">
                  <label>BKM/BBM</label>
                </div>

                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_add_nobkmbbm" type="text" class="form-control" disabled>
                  </div>
                </div>

              </div> -->


            <!-- <div class="row" style="margin-top: -10px">



                <div class="col-md-4">
                  <div class="form-group">
                  <label>Valas</label>
                </div>
                </div>

                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_add_valas" type="text" class="form-control" disabled>


                  </div>
                </div>
                </div>

            </div> -->

            <!-- <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                      <label>Customer</label>
                    </div>
                    </div>

                    <div class="col-md-8">
                      <div class="input-group form-group">
                        <input id="input_add_kodecust" type="text" class="form-control" disabled>


                      </div>
                    </div>

            </div> -->

            <!-- <div class="row" style="margin-top: -10px"> -->

              <!-- <div class="col-md-4">
              </div> -->

              <!-- <div class="col-md-8">
                <div class="input-group form-group">
                  <input id="input_add_namacust" type="text" class="form-control" disabled>
                </div>
              </div> -->

            <!-- </div> -->



          </div>


        </div>

      </div>


      </div>

      <div class="row" style="margin-top: -10px">
        <div class="col-md-3">
              <!-- <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>Jumlah</label>
                </div>
                </div>

                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_add_jumlah" type="number" class="form-control text-right" disabled>


                  </div>
                </div>

             </div> -->
      </div>

      <div class="col-md-3">
            <div class="row">
              <!-- <div class="col-md-4">
                <div class="form-group">
                <label>Dibayar</label>
              </div>
              </div> -->

              <!-- <div class="col-md-8">
                <div class="input-group form-group">
                  <input id="input_add_dibayar" type="number" class="form-control text-right" disabled>


                </div>
              </div> -->

      </div>
    </div>



    <!-- <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>Sisa</label>
            </div>
            </div>

            <div class="col-md-8">
              <div class="input-group form-group">
                <input id="input_add_sisa" type="number" class="form-control text-right" disabled>


              </div>
            </div>

          </div>
    </div> -->
</div>




<div class="container-fluid">
  <hr/>

</div>

<div class="col-md-12 mt-2 text-right">
<button type="button" class="btn btn-primary" onclick="previewPrint()" class="btn btn-secondary" style="height: 30px;
border-radius: 20px;
font-size: 0.75rem;
font-weight: 600;
text-transform: uppercase;" >+ Preview</button>



<button type="button" class="btn btn-primary" onclick="submitPrint()" class="btn btn-secondary" style="height: 30px;
border-radius: 20px;
font-size: 0.75rem;
font-weight: 600;
text-transform: uppercase;" >+ Cetak</button>
</div>


  <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

        <table id="addTable" class="table table-bordered table-striped"  >
          <thead class="text-center bg-primary text-white">
            <tr><th style="padding: 4px 12px;" scope="col">v</th>
              <th style="padding: 4px 12px;" scope="col">No. Invoice</th>
              <th style="padding: 4px 12px;" scope="col">Tanggal</th>
              <th style="padding: 4px 12px;" scope="col">No. PO</th>
              <th style="padding: 4px 12px;" scope="col">Penerima</th>
            </tr>
          </thead>


          <tbody id="addTableData" class="" >
            <tr >

                <!-- <td colspan=9 class="text-center">Belum ada data</td> -->

          </tr>

          </tbody>


        </table>
  </div>

  <div class="modal" id="formAldok1" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Modal body text goes here.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


  <div class="modal fade"  id="" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Add</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
  </div>
  <div id="modalBodyAddListPelanggan" class="">
          <div class="modal-body" >

          <div class="container-fluid mt-4" >
            <div class="row">
              <div class="col-md-4" style="margin-top:-40px;">
                <h3>Pelanggan</h3>
              </div>
            </div>
            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
            <div class="row">
              <div class="col-12" style="overflow:auto; margin-top:-60px;">
              <!-- <div class="container-fluid"> -->
              <table id="tabel_add_list_pelanggan" class="table table-bordered table-hover table-striped table-responsive-lg">
                <thead class="text-center bg-primary text-white">
                  <tr>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                    <th style="padding: 4px 12px;" scope="col">Kode</th>
                    <th style="padding: 4px 12px;" scope="col">Nama</th>
                    <th style="padding: 4px 12px;" scope="col">Alamat</th>
                    <th style="padding: 4px 12px;" scope="col">PKP</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_pelanggan" class="text-left" >
                  <tr >
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                      <td class="text-center">
                        <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                        <button class="btn btn-primary btn-sm" style="padding-top:10px;" type="button" ><i class="bi bi-plus"></i></button>
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
        <div class="modal-footer">
          <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
          <button type="button" class="btn btn-danger btn-lg"
          style="
          margin-top:-10px;
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonAddListBatal()">Batal</button>
        </div>
      </div>
</div>




  <div id="formAddAdd" class="container-fluid showhideitem">
    <!-- <div class="line"></div> -->
    <!-- <div class="row"> -->

    <div class="col-12">


    <hr/>
    <div class="row">
      <div class="col-md-12">
        <h4 id="labelAddEditItem">Edit Item</h4>
      </div>
    </div>


    <div class="row">
      <div class="col-md-3">
        <div class="row">


        <div class="col-md-4">
          <div class="form-group">
          <label>Faktur</label>
        </div>
        </div>
        <!-- <div class="col-4 text-right">

          </div> -->
        <div class="col-md-8">
          <div class="input-group form-group">
            <input id="AddAddFaktur" type="text" class="form-control" disabled>


          </div>
        </div>
        </div>

      </div>

    </div>

    <div class="row" style="margin-top: -10px">
      <div class="col-md-3">
        <div class="row">


        <div class="col-md-4">
          <div class="form-group">
          <label>Dibayar</label>
        </div>
        </div>
        <!-- <div class="col-4 text-right">

          </div> -->
        <div class="col-md-8">
          <div class="input-group form-group">
            <input id="AddAddDibayar" type="number" class="form-control text-right" disabled>

 
          </div>
        </div>
        </div>

      </div>
      <div class="col-md-3">
        <div class="row">


        <div class="col-md-4">
          <div class="form-group">
          <label>Lebih Bayar</label>
        </div>
        </div>
        <!-- <div class="col-4 text-right">

          </div> -->
        <div class="col-md-8">
          <div class="input-group form-group">
            <input id="AddAddLebihBayar" type="number" class="form-control text-right" disabled>


          </div>
        </div>
        </div>

      </div>

    </div>


    <div class="row" style="margin-top: -10px">
      <div class="col-md-3">
        <div class="row">


        <div class="col-md-4">
          <div class="form-group">
          <label>Kurang Bayar</label>
        </div>
        </div>
        <!-- <div class="col-4 text-right">

          </div> -->
        <div class="col-md-8">
          <div class="input-group form-group">
            <input id="AddAddKurangBayar" type="number" class="form-control text-right" disabled>


          </div>
        </div>
        </div>

      </div>


    </div>

    <div class="row" style="margin-top: -10px">
      <div class="col-md-3">
        <div class="row">


        <div class="col-md-4">
          <div class="form-group">
          <label>Perkiraan</label>
        </div>
        </div>
        <!-- <div class="col-4 text-right">

          </div> -->
        <div class="col-md-8">
          <div class="input-group form-group">
            <input id="AddAddKodePerkiraan" type="text" class="form-control" disabled>
            <input type="text" class="form-control" id="AddAddNamaPerkiraan" disabled>

            <!-- <button id="buttonAddListPerkiraan" type="button" onclick="" class="btn btn-primary" >+</button> -->

          </div>
        </div>
        </div>

      </div>

    </div>



</div>





<div class="row mt-2" style="margin-top: 0">
  <div class="col-md-12 text-right mt-4">
    <button type="button" class="btn btn-secondary" onclick="buttonAddBatal()" style="height: 30px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;">Batal</button>


    <button id="buttonSubmitEdit" type="button" onclick="submitEdit()" class="btn btn-primary" style="height: 30px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;">Submit Edit</button>


  </div>

</div>




</div>



</div>




</div>








    <!-- <div class="line"></div> -->
    <!-- <hr/> -->
  </div>
</div>
<!-- </div> -->


<!-- ADD EDIT -->


<!-- </div> -->



    </div>

    <!-- <div class="row "> -->

<!-- </div> -->









  <!-- </div> -->

  <!-- <h2 class="page3showhide detailshowhide"> Detail Pengajuan DPH</h2>
  <h2 class="page3showhide otorisasishowhide"> Otorisasi Pengajuan DPH</h2> -->



    <!-- <div class="col-6 text-right">
      <button type="button" class="page3showhide otorisasishowhide btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600;  " onclick="submitOtorisasi()"  >Otorisasi</button>
    </div> -->


    <div id="page3" style="display: none" class="mainpage container-fluid" >

      <div class="row" style="margin-top: -30px">
        <div class="col-8 text-left">
          <h2 class="page3showhide detailshowhide"> Detail Piutang DPP</h2>
          <h2 class="page3showhide otorisasishowhide"> Otorisasi Piutang DPP</h2>
        </div>
        <div class="col-4 text-right">
          <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
        </div>
      </div>

      <div id= "" class="">



      <div id="" class="">
      <div class="">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">

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
                  <input type="hidden" class="form-control" id="input_detail_nourut" placeholder="" disabled>
                <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Bukti" disabled>
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
                <input type="date" class="form-control text-center" id="input_detail_tanggal" placeholder="" disabled>
              </div>
            </div>
          </div>

            </div>

          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="row" style="margin-top: -10px">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                      <label>BKM/BBM</label>
                    </div>
                    </div>
                    <!-- <div class="col-4 text-right">

                      </div> -->
                    <div class="col-md-8">
                      <div class="input-group form-group">
                        <input id="input_detail_nobkmbbm" type="text" class="form-control" disabled>


                      </div>
                    </div>

                  </div>
                    <div class="row" style="margin-top: -10px">



                    <div class="col-md-4">
                      <div class="form-group">
                      <label>Valas</label>
                    </div>
                    </div>
                    <!-- <div class="col-4 text-right">

                      </div> -->
                    <div class="col-md-8">
                      <div class="input-group form-group">
                        <input id="input_detail_valas" type="text" class="form-control" disabled>

                        <!-- <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" disabled >+</button> -->

                      </div>
                    </div>
                    </div>

                </div>

                <div class="col-md-6">
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group">
                          <label>Customer</label>
                        </div>
                        </div>
                        <!-- <div class="col-4 text-right">

                          </div> -->
                        <div class="col-md-8">
                          <div class="input-group form-group">
                            <input id="input_detail_kodecust" type="text" class="form-control" disabled>


                          </div>
                        </div>

                </div>

                <div class="row" style="margin-top: -10px">
                  <div class="col-md-4">
                  </div>
                  <!-- <div class="col-4 text-right">

                    </div> -->
                  <div class="col-md-8">
                    <div class="input-group form-group">
                      <input id="input_detail_namacust" type="text" class="form-control" disabled>


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
                    <div class="col-md-4">
                      <div class="form-group">
                      <label>Jumlah</label>
                    </div>
                    </div>
                    <!-- <div class="col-4 text-right">

                      </div> -->
                    <div class="col-md-8">
                      <div class="input-group form-group">
                        <input id="input_detail_jumlah" type="number" class="form-control text-right" disabled>


                      </div>
                    </div>

            </div>
          </div>

          <div class="col-md-3">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                    <label>Dibayar</label>
                  </div>
                  </div>
                  <!-- <div class="col-4 text-right">

                    </div> -->
                  <div class="col-md-8">
                    <div class="input-group form-group">
                      <input id="input_detail_dibayar" type="number" class="form-control text-right" disabled>


                    </div>
                  </div>

          </div>
        </div>



        <div class="col-md-3">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>Sisa</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_detail_sisa" type="number" class="form-control text-right" disabled>


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
                  <th style="padding: 4px 12px;" scope="col">No.invoice</th>
                  <th style="padding: 4px 12px;" scope="col">Supplier</th>
                  <th style="padding: 4px 12px;" scope="col">Valas</th>
                  <th style="padding: 4px 12px;" scope="col">Dibayar</th>
                  <th style="padding: 4px 12px;" scope="col">KL</th>


                </tr>
              </thead>


              <tbody id="detailTableData" class="" >
                <tr >

                    <td colspan=8 class="text-center">Belum ada data</td>

              </tr>

              </tbody>


            </table>
      </div>

      <div class="container-fluid">
        <div class="row">

                <div class="col-12 text-right">
                  <button type="button" class="page3showhide otorisasishowhide btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600;  " onclick="submitOtorisasi()"  >Otorisasi</button>
                </div>
        </div>

      </div>










    </div>




    </div>








        <!-- <div class="line"></div> -->
        <!-- <hr/> -->
      </div>
    </div>
    <!-- </div> -->


    <!-- ADD EDIT -->


    <!-- </div> -->



        </div>






<!--  -->

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="min-width: 1400px">
    <div id="" class="modal-content ">

      <div id= "" class="">
      <div class="modal-header">


          <h5 class="modal-title" id="">Proses Terima DPP</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid" >

          <div class="row">
            <div class="col-md-4">
              <div class="row" >
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Jumlah</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="number" class="form-control text-right" id="input_modal_jumlah" disabled>
                  </div>
                </div>
              </div>
              <div class="row" style="margin-top: -10px">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Dibayar</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="number" class="form-control text-right" id="input_modal_dibayar" disabled>
                  </div>
                </div>
              </div>
              <div class="row" style="margin-top: -10px">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Sisa</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="number" class="form-control text-right" id="input_modal_sisa" disabled>
                  </div>
                </div>
              </div>

            </div>


            <div class="col-md-4">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>No Bukti</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_modal_nobukti" placeholder="" disabled>
                  </div>
                </div>
              </div>


              <div class="row" style="margin-top: -10px">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Nama Cust</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_modal_namacust" placeholder="" disabled>
                  </div>
                </div>

              </div>

            </div>

          </div>




          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;  max-height: 400px">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_modal" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center bg-primary text-white" style="position: sticky;
            top: 0;
            z-index: 1;">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">v</th>
                  <th style="padding: 4px 12px;" scope="col">No Faktur</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Customer</th>
                  <th style="padding: 4px 12px;" scope="col">N. Faktur</th>
                  <th style="padding: 4px 12px;" scope="col">SdhBayar</th>
                  <th style="padding: 4px 12px;" scope="col">Dibayar</th>
                  <th style="padding: 4px 12px;" scope="col">L.Bayar</th>
                  <th style="padding: 4px 12px;" scope="col">K.Bayar</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_modal" class="text-left" >

                <tr >

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


      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
      </div>
      </div>
















































      </div>







    </div>
  </div>

<!-- End modal add-->





<div class="modal fade" id="formX" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="min-width: 1400px">
    <div id="" class="modal-content ">

      <div id= "" class="">
      <div class="modal-header">


          <h5 class="modal-title" id="">Change Invoice</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid" >

          <div class="row">
            <div class="col-md-4">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Nilai Nota</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="number" class="form-control text-right" id="input_modalx_nilainotadibayar" disabled>
                  </div>
                </div>

              </div>

            </div>

            <div class="col-md-4">
              <div class="row">


              <div class="col-md-4">
                <div class="form-group">
                  <label>Sisa Nota</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_modalx_sisanotadibayar" disabled>
                </div>
              </div>

            </div>
            </div>

          </div>

          <div class="row" style="margin-top: -10px">
            <div class="col-md-4">
          <div class="row" >
            <div class="col-md-4">
              <div class="form-group">
                <label>Dibayar</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="number" class="form-control text-right" id="input_modalx_dibayar" >
              </div>
            </div>
          </div>
          <div class="row" style="margin-top: -10px">
            <div class="col-md-4">
              <div class="form-group">
                <label>Lebih Bayar</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="number" class="form-control text-right" id="input_modalx_lebihbayar" >
              </div>
            </div>
          </div>

          <div class="row" style="margin-top: -10px">
            <div class="col-md-4">
              <div class="form-group">
                <label>Perk LB</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group input-group">
                <input type="text" class="form-control text-right" id="input_modalx_perkiraanlebihbayar" disabled>

                <input type="text" class="form-control" id="input_modalx_namaperkiraanlebihbayar" disabled>
                <button id="buttonAddListPerkiraanLebihBayar" type="button" onclick="buttonAddListPerkiraanLebihBayar('lebihbayar')" class="btn btn-primary" >+</button>

              </div>
            </div>
          </div>


            </div>

          </div>


          <div class="row mt-2" style="margin-top: 0">
            <div class="col-md-12 text-right mt-4">

              <button id="buttonSaveLB" type="button" onclick="buttonSaveLB()" class="btn btn-success" style="height: 30px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;">Save</button>
              <button type="button" id="buttonAddKL" class="btn btn-primary" onclick="buttonAddKL()" style="height: 30px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;">+ KL</button>





              <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
            </div>

          </div>



          </div>
          <div id="formAddKL" class="container-fluid showhideitemKL">
            <!-- <div class="line"></div> -->
            <!-- <div class="row"> -->

            <div class="col-12">


            <hr/>
            <div class="row">
              <div class="col-md-12">
                <h4 id="">Add KL</h4>
              </div>
            </div>


            <div class="row" >
              <div class="col-md-6">


                <div class="row">






                  <div class="col-md-2">
                    <div class="form-group">
                    <label>Jumlah</label>
                  </div>
                  </div>
                  <!-- <div class="col-4 text-right">

                    </div> -->
                  <div class="col-md-4">
                    <div class="input-group form-group">
                      <input id="input_modalx_kurangbayar" type="number" value="0.00" class="text-right form-control" >

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
                      <label>Perk KL</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group input-group">
                      <input type="text" class="form-control" id="input_modalx_perkiraankurangbayar" disabled>
                      <input type="text" class="form-control" id="input_modalx_namaperkiraankurangbayar" disabled>
                      <button id="buttonAddListPerkiraanKurangBayar" type="button" onclick="buttonAddListPerkiraanLebihBayar('kurangbayar')" class="btn btn-primary" >+</button>

                    </div>
                  </div>





                </div>
              </div>

            </div>















        </div>



          <div class="row mt-2" style="margin-top: 0">
            <div class="col-md-12 text-right mt-4">
              <button type="button" class="btn btn-secondary" onclick="buttonAddBatalKL()" style="height: 30px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;">Batal</button>

              <button id="buttonSubmitAddKL" type="button" onclick="submitAddKL()" class="btn btn-primary" style="height: 30px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;">Submit Add</button>




              <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
            </div>

          </div>

        </div>



          <div class="row" style="margin-top:20px">
            <div class="col-12" style="overflow:auto;  max-height: 400px">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_modalx" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center bg-primary text-white" style="position: sticky;
            top: 0;
            z-index: 1;">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kurang Bayar</th>
                  <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
                  <th style="padding: 4px 12px;" scope="col">Nama perkiraan</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_modalx" class="text-left" >

                <tr >

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








          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

            </div>




        </div>





      </div>


      <div class="modal-footer">



        <button type="button" class="btn btn-secondary" data-dismiss="modal"
        >Batal</button>
        <!-- <button type="button" class="btn btn-primary" onclick="submitAddModalX()">Submit</button> -->
      </div>
      </div>

      </div>

    </div>




    <div class="modal fade" id="formPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="min-width: 1400px">
        <div id="" class="modal-content ">

          <div id= "" class="">
          <div class="modal-header">


              <h5 class="modal-title" id="">Perkiraan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>


          <div id="" class="">
          <div class="modal-body">

            <div class="container-fluid" >
              <div class="row">
                <div class="col-12">
                  <h3>Perkiraan</h3>
                </div>


              </div>



              <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
              <div class="row">
                <div class="col-12" style="overflow:auto;  max-height: 400px">
                <!-- <div class="container-fluid"> -->


                <table id="tabel_add_list_perkiraan" class="table table-bordered table-striped" style="overflow:auto; " >
                  <thead class="text-center bg-primary text-white" style="position: sticky;
                top: 0;
                z-index: 1;">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
                      <th style="padding: 4px 12px;" scope="col">Nama</th>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>

                    </tr>
                  </thead>


                  <tbody id="tabel_data_add_list_perkiraan" class="text-left" >

                    @for ($i = 0; $i < count($tempListPerkiraan); $i++)
                    <tr>
                      <td>{{ $tempListPerkiraan[$i]->Perkiraan }}</td>
                      <td>{{ $tempListPerkiraan[$i]->Keterangan }}</td>


                        <td class="text-center">
                          <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                          <button class="btn btn-primary btn-sm" onclick="buttonAddPickPerkiraanLebihBayar('{{ $tempListPerkiraan[$i]->Perkiraan }}' , '{{ $tempListPerkiraan[$i]->Keterangan }}')" type="button" ><i class="bi bi-plus"></i></button>
                        </td>
                  </tr>
                  @endfor
                  </tbody>


                </table>
              <!-- </div> -->
                <!-- <button onclick="buttonSubKategori()">tes</button> -->
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
      </div>





  </div>

  <div class="modal" id="formAldo2k" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Modal body text goes here.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>









@endsection

@section('js')
<script type="text/javascript">
let listData = []
let listOutstanding = []
// let addTableData = []
let tipeform = ''
let xnotacetak = ''
let xtglterima = ''
let xnamapenerima = ''
let listProsesTerimaDPP = []
let penerimaanHeader = {}
let listPenerimaan = []
let listTambah = []
let listTambahLB = []
let listTambahKL = []
let saveHeaderInvoice = {}
let saveHeaderIndex = 0
let toId = ''
let urutTrans = 0
let dataEdit = {}

$(document).ready(function(){
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
          "columnDefs": [
          {  "className": "text-right", "targets": [] },
        ]
        });


        $("#tabel2").DataTable({
          "lengthChange": false,
            "paging": false ,
            "columnDefs": [
            {  "className": "text-right", "targets": [] },
          ]
          });

      //   $("#tabel_add_list_modal").DataTable({
      //     "lengthChange": false,
      //       "paging": false ,'order': [[1, 'asc']],
      //       "searching" : false,
      //       "columnDefs": [
      //     {"targets" :[0] , 'orderable' : false}
      //  ]
      // });

      // $('#page1').hide()
      // $('#page2').show()

        $("#tabel_add_list_custsupp").DataTable({
          "lengthChange": false,
            "paging": false ,
      });

});

// function testes () {
//   $("#formX").modal('toggle')
// }

function buttonAddListPerkiraanLebihBayar (id) {
  toId = id
  $("#formPerkiraan").modal('toggle')
}

function buttonAddPickPerkiraanLebihBayar (perkiraan , nama) {
  document.getElementById(`input_modalx_perkiraan${toId}`).value = perkiraan
  document.getElementById(`input_modalx_namaperkiraan${toId}`).value = nama
  $("#formPerkiraan").modal('toggle')

}

function buttonSaveLB () {
    let xnilainota = $("#input_modalx_nilainotadibayar").val()
    let xdibayar = $("#input_modalx_dibayar").val()
    let xlebihbayar = $("#input_modalx_lebihbayar").val()
    let xperkiraanlebihbayar = $("#input_modalx_perkiraanlebihbayar").val()
    let xnamaperkiraanlebihbayar = $("#input_modalx_namaperkiraanlebihbayar").val()
    let xsisa = $("#input_modalx_sisanotadibayar").val()

    let checksisadibayar = Number(listProsesTerimaDPP[saveHeaderIndex].DIBAYAR)
    let checksisalb = Number(listTambahLB[listProsesTerimaDPP[saveHeaderIndex].NOFAKTUR]) ? Number(listTambahLB[listProsesTerimaDPP[saveHeaderIndex].NOFAKTUR]) : 0
    let checksisa = Number(checksisadibayar) + Number(checksisalb)
    console.log(xdibayar)
    console.log(xlebihbayar)
    console.log(xperkiraanlebihbayar)
    console.log(xnamaperkiraanlebihbayar)
    console.log(xsisa)
    console.log(checksisadibayar)
    console.log(checksisalb)
    console.log(checksisa)
    console.log("<3")
    let xlist = listTambahKL[saveHeaderInvoice.NOFAKTUR]
    let xTempTotalKL = 0
    if (!xlist) {
      xlist = []
    }
    xlist.forEach((item, i) => {
      xTempTotalKL += Number(item.inputKL)
    })
    console.log(Number(checksisalb) , Number(xlebihbayar) , Number(xsisa))
    if (Number(xdibayar) + Number(xTempTotalKL) > Number(xnilainota)) {
      alertify.warning("Dibayar + KL melebihi nilai nota")
      return
    }

    if (  Number(xlebihbayar) + Number(xdibayar) > Number(checksisa) + Number(xsisa) ) {
      alertify.warning("Melebihi sisa nota")
      return
    }
    // totfaktur
    if ( Number(xdibayar) <= 0 && Number(xlebihbayar) <= 0) {
      alertify.warning("Jumlah <= 0")
      return
    }
    if (Number(xnilainota) < Number(xdibayar)) {
      alertify.warning("Dibayar > Nilai Nota")
      return
    }

    // if ()

    if (Number(xlebihbayar) > 0 && !xperkiraanlebihbayar) {
      alertify.warning("Perk lebih bayar belum diisi")
      return
    }
    if ( Number(xlebihbayar) <= 0 && xperkiraanlebihbayar) {
      alertify.warning("Lebih bayar belum diisi")
      return
    }

    if (Number(xlebihbayar) + Number(xdibayar) > Number(xsisa) + Number(checksisa)) {
      alertify.warning("Jumlah melebihi sisa")
      return
    }

    if (Number(xlebihbayar) > 0) {


      let x = { ...saveHeaderInvoice }
      x.inputLB = xlebihbayar
      x.inputPerkiraanLB = xperkiraanlebihbayar
      x.inputNamaPerkiraanLB = xnamaperkiraanlebihbayar
      // x.DIBAYAR = 0
      // x.indexHeader = saveHeaderIndex
      listTambahLB[saveHeaderInvoice.NOFAKTUR] = x

    }
    console.log(listTambahLB)

    listProsesTerimaDPP[saveHeaderIndex].DIBAYAR = xdibayar

    // listTambahLB
    // listTambah
    refreshSisa()


}

function prosesCheckbox (index) {

 // id="list_proses_checkbox${i}"

 let xdata = listProsesTerimaDPP[index]
 console.log(xdata)
 // console.log(listProsesTerimaDPP[index])
 let xcheck = document.getElementById(`list_proses_checkbox${index}`).checked
 let xsisa = Number($("#input_modal_sisa").val())
 let xdibayar = Number($(`#list_proses_dibayar${index}`).val())
 console.log(xcheck)


 if (xcheck && xsisa <= 0) {
   document.getElementById(`list_proses_checkbox${index}`).checked = false
   alertify.warning("Sisa nota habis")
   return
 }

 if (Number(xdata.TOTFAKTUR) - Number(xdata.SDHBAYAR) <= 0) {
   console.log("bcd")

   return
 }

 if (xcheck) {
   console.log('1' , Number(xsisa))
   if(Number(xsisa) > 0) {

       console.log('2' , Number(xdibayar))
     if (Number(xdibayar) == 0) {
       if (Number(xdata.TOTFAKTUR) - Number(xdata.SDHBAYAR) < Number(xsisa)) {
         document.getElementById(`list_proses_dibayar${index}`).value = Number(xdata.TOTFAKTUR) - Number(xdata.SDHBAYAR)
         listProsesTerimaDPP[index].DIBAYAR = Number(xdata.TOTFAKTUR) - Number(xdata.SDHBAYAR)
       } else {
         document.getElementById(`list_proses_dibayar${index}`).value = Number(xsisa)
         listProsesTerimaDPP[index].DIBAYAR = Number(xsisa)
       }
       refreshSisa()
     }


   }
 } else {
  document.getElementById(`list_proses_dibayar${index}`).value = '0.00'
  document.getElementById(`list_proses_LB${index}`).value = '0.00'
  document.getElementById(`list_proses_KL${index}`).value = '0.00'
  listProsesTerimaDPP[index].DIBAYAR = 0
  delete listTambahLB[xdata.NOFAKTUR];
  delete listTambahKL[xdata.NOFAKTUR];
  refreshSisa()

 }
 console.log("^^^^^^^^^^^^^^^^^^^^^^^")
 console.log(listTambahLB)
 console.log(listProsesTerimaDPP)
 console.log("^^^^^^^^^^^^^^^^^^^^^^^")


}

function refreshSisa () {
  console.log('refreshSisa')
  let totdibayar = Number($("#input_add_dibayar").val())
  let totlb = 0
  listProsesTerimaDPP.forEach((item, i) => {
    console.log('wwwwwwwwwwwww')
    console.log('listProsesTerimaDPP' , listTambahLB[item.NOFAKTUR])
    totdibayar += Number(item.DIBAYAR)

    if (listTambahLB[item.NOFAKTUR]) {
      totlb += Number(listTambahLB[item.NOFAKTUR].inputLB)
      document.getElementById(`list_proses_LB${i}`).value = parseFloat(listTambahLB[item.NOFAKTUR].inputLB).toFixed(2)
    }

    document.getElementById(`list_proses_dibayar${i}`).value = parseFloat(item.DIBAYAR).toFixed(2)
    console.log(totdibayar)
    console.log(totlb)

  });
  let xtot = Number(totdibayar) + Number(totlb)
  let xjumlah = $("#input_modal_jumlah").val()

  console.log(parseFloat(xtot).toFixed(2))
  console.log(parseFloat(Number(xjumlah) - xtot).toFixed(2))
  document.getElementById(`input_modal_dibayar`).value = parseFloat(xtot).toFixed(2)
  document.getElementById(`input_modal_sisa`).value = parseFloat(Number(xjumlah) - xtot).toFixed(2)
  document.getElementById(`input_modalx_sisanotadibayar`).value = parseFloat(Number(xjumlah) - xtot).toFixed(2)



}

function buttonAddKL () {

  $('.showhideitemKL').show()

  document.getElementById("input_modalx_kurangbayar").value = '0.00'
  document.getElementById("input_modalx_perkiraankurangbayar").value = ''
  document.getElementById("input_modalx_namaperkiraankurangbayar").value = ''
}


function submitAddKL () {
    let kl = $("#input_modalx_kurangbayar").val()
    let perkkl = $("#input_modalx_perkiraankurangbayar").val()
    let namaperkkl = $("#input_modalx_namaperkiraankurangbayar").val()
    // perkkl = '444'
    // namaperkkl = 'TESTESwiu'

    if (Number(kl) <= 0 || !perkkl) {

      alertify.warning("Data tidak lengkap")
      return
    }
    let xnilainotadibayar = $("#input_modalx_nilainotadibayar").val()
    let xdibayar = $("#input_modalx_dibayar").val()
    if (Number(xnilainotadibayar) + Number(xdibayar) < Number(kl)) {
      alertify.warning('KL melebihi nilai nota + dibayar')

      return
    }


    let xsisa = $("#input_modalx_sisanotadibayar").val()
    if (Number(kl) > Number(xsisa)) {
      alertify.warning('Melebihi sisa nota')
      return
    }
    alertify.success("KL berhasil ditambah")
    let x = { ...saveHeaderInvoice }
    x.inputKL = kl
    x.inputPerkiraanKL = perkkl
    x.inputNamaPerkiraanKL = namaperkkl
    console.log(x)
    console.log(listTambahKL)
    console.log('!!!!')
    console.log(saveHeaderInvoice.NOFAKTUR)
    if (!listTambahKL[saveHeaderInvoice.NOFAKTUR]) {
      listTambahKL[saveHeaderInvoice.NOFAKTUR] = []
    }
    console.log(listTambahKL)
    listTambahKL[saveHeaderInvoice.NOFAKTUR].push(x)
    console.log(listTambahKL)
    refreshTableKL()
    $('.showhideitemKL').hide()

}

function buttonAddBatalKL () {
  $('.showhideitemKL').hide()
}

function onChangeTransaksi () {
  document.getElementById("input_add_kodeperkiraan").value = ''
  document.getElementById("input_add_keteranganperkiraan").value = ''
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_kepadaterima").value = ''

  document.getElementById("input_add_bon").value = ''
  document.getElementById("input_add_nilaibon").value = '0.00'

    console.log("onChangeTransaksi")
    $('.showhideitem').hide();
    $('.showhidePart').hide();
    let value = $("#input_add_transaksi").val()
    console.log(value)
    $(`.part${value}`).show();


}

function setNewNoBukti () {
  console.log('setNewNoBukti')
  let _token  = $("#_token").val()
  let kode  = 'TTI'
  $.ajax({
    url: "{!! url('spnobukti') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
}


function cleanFormAddAdd () {
  document.getElementById("AddAddKodeDevisi").value = ''
  document.getElementById("AddAddNamaDevisi").value = ''
  document.getElementById("AddAddValas").value = 'IDR'
  document.getElementById("AddAddKurs").value = '1.00'
  document.getElementById("AddAddLawan").value = ''
  document.getElementById("AddAddKeteranganLawan").value = ''
  document.getElementById("AddAddJumlah").value = '0.00'
  document.getElementById("AddAddKeterangan").value = ''
  document.getElementById("AddAddKeteranganDetail").value = ''
  document.getElementById("AddAddKodeDepartemen").value = ''
  document.getElementById("AddAddNamaDepartemen").value = ''

}


function closeShowHideAdd () {
  $('.showhide').hide();

}

function cleanModalAdd () {


  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value


  var lastDayOfMonth = new Date(periode_tahun, periode_bulan, 0);
  console.log(lastDayOfMonth)

  document.getElementById("input_modal_nobukti").value = ''
  document.getElementById("input_modal_nourut").value = ''
  document.getElementById("input_modal_tanggal").valueAsDate = new Date()
  document.getElementById("input_modal_valas").value = 'IDR'
  document.getElementById("input_modal_tanggaljatuhtempo").value = formatDate(lastDayOfMonth)

}

function tesConcat () {
  let x = []
  let y = ['a' , 'b']
  let z = [1,2,3]

  let a = x.concat(y)
  console.log(a)
  a = a.concat(z)
  console.log(a)
}


function submitAdd () {

  let _token  = $("#_token").val()
  let choice = "I"
  let nobukti  = $("#input_add_nobukti").val()
  let nourut  = $("#input_add_nourut").val()
  // let valas  = $("#input_add_valas").val()
  let tipe = 'DPP'

  let kodecustsupp  = $("#input_add_kodecust").val()
  let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value
// let nobkmbbm = $("#input_add_nobkmbbm").val();
  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  let tanggal  = $("#input_add_tanggal").val()
  let jmlrecord = tipeform == "add" ? 0 : 1

  let xlisttambah = []
  let xlisttambahlb = []
  let xlisttambahkl = []

  console.log('[][][][][][][][]')
  console.log(listTambahLB)
  console.log(listTambahKL)
  console.log(listTambah)




  console.log('[][][][][][][][]')

  listProsesTerimaDPP.forEach((item, i) => {
    if (document.getElementById(`list_proses_checkbox${i}`).checked) {
      xlisttambah.push(listProsesTerimaDPP[i])
      if (listTambahLB[listProsesTerimaDPP[i].NOFAKTUR]) {
        console.log(listTambahLB[listProsesTerimaDPP[i].NOFAKTUR])
        xlisttambahlb.push(listTambahLB[listProsesTerimaDPP[i].NOFAKTUR])
      }
      if (listTambahKL[listProsesTerimaDPP[i].NOFAKTUR]) {
        console.log(xlisttambahkl)
        console.log(listTambahKL[listProsesTerimaDPP[i].NOFAKTUR])
        let tempkl = listTambahKL[listProsesTerimaDPP[i].NOFAKTUR]
        tempkl.forEach((item, i) => {
          xlisttambahkl.push(item)
        });

        // xlisttambahkl.concat(tempkl)
        console.log("11111111")
        console.log(xlisttambahkl)
      }

    }
  });
  console.log('tambah')
  console.log(xlisttambah)
  console.log('tambahkl')
  console.log(xlisttambahkl)
  console.log('tambahlb')
  console.log(xlisttambahlb)

  if (!xlisttambah.length ) {
    alertify.warning("Tidak ada data dipilih")
    return
  }


  $.ajax({
      url: "{!! url('pelunasanpiutangdppspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        tempData : xlisttambah ,
        tempDataKL : xlisttambahkl ,
        tempDataLB : xlisttambahlb ,
        nobukti,
        nourut,
        tipe,
        tanggal,
        jmlrecord,
        kodecustsupp,
        urutTrans
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          alertify.success('DPH telah ditambah');
          document.getElementById("input_add_tanggal").disabled = true

          tipeform = 'edit'
          $("#form").modal('toggle')
          refreshTableKoreksi(nobukti ,nobkmbbm)
          loadAll()

        }
        if (res == 2) {
          setNewNoBukti()
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })





  return


  // let _token  = $("#_token").val()
  // let choice = "I"
  // let nobukti  = $("#input_modal_nobukti").val()
  // let nourut  = $("#input_modal_nourut").val()
  // let valas  = $("#input_modal_valas").val()
  // let tipe = 'DPP'
  // let checkDate = new Date($("#input_modal_tanggal").val())
  // let periode_bulan = document.getElementById("periode_bulan").value
  // let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {
    console.log(checkDate.getFullYear())
    console.log(Number(periode_tahun))
    console.log((checkDate.getMonth() +1))
    console.log(Number(periode_bulan))
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }


  // let tanggal  = $("#input_modal_tanggal").val()

  if(!listCheckListPengajuan.length) {
    alertify.warning("Tidak ada item dipilih")
  }

  // let jmlrecord = tipeform == "add" ? 0 : 1

  console.log({
    tempData : listCheckListPengajuan ,
    choice,
    valas,
    nobukti,
    nourut,
    tipe,
    tanggal
  })


  $.ajax({
      url: "{!! url('pengajuandphspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        tempData : listCheckListPengajuan ,
        choice,
        valas,
        nobukti,
        nourut,
        tipe,
        tanggal,
        jmlrecord
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          alertify.success('DPH telah ditambah');

          tipeform = 'edit'
          $("#form").modal('toggle')
          buttonKoreksi(nobukti)

        }
        if (res == 2) {
          setNewNoBukti()
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}


function submitAddAdd () {

  let checkDate = new Date($("#input_add_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let _token  = $("#_token").val()
  let nobukti  = $("#input_add_nobukti").val()
  let nourut  = $("#input_add_nourut").val()
  // let transaksi  = $("#input_add_transaksi").val()
  // let note  = $("#input_add_kepadaterima").val()
  // let kodeperkiraan  = $("#input_add_kodeperkiraan").val()
  let tanggal = $("#input_add_tanggal").val()

  let lampiran = 0
  let keterangan2 = ''
  let choice = "I"


  let kodedevisi  = $("#AddAddKodeDevisi").val()
  let valas  = $("#AddAddValas").val()
  let kurs  = $("#AddAddKurs").val()
  let lawan  = $("#AddAddLawan").val()
  // let kodelawan  = $("#AddAddKodeLawan").val()
  let jumlah  = $("#AddAddJumlah").val()
  let keterangan  = $("#AddAddKeterangan").val()
  let keterangandetail  = $("#AddAddKeteranganDetail").val()
  let kodedepartemen  = $("#AddAddKodeDepartemen").val()

  let kredit = 0
  let kreditrp = 0

  let tphc = 'C'

  if (!kodedevisi || !valas || !lawan || !kodedepartemen || !keterangan) {
    alertify.warning("Data tidak lengkap")
    return
  }

  if (jumlah < 0) {
    alertify.warning("Jumlah < 0")
    return
  }

  let jumlahrp = Number(jumlah) * Number(kurs)


  let urut = 0

  let custsuppP = ''
  let custsuppL = ''
  let noaktivaP = ''
  let noaktivaL = ''
  let statusaktivaP = ''
  let statusaktivaL = ''

  let nobon = $("#input_add_bon").val()
  let kodebag = '-'

  let kodeP = ''
  let kodeL = ''
  let statusgiro = ''
  let simbol = $("#input_add_simbol").val()
  let flagsimbol = ''
  let kodecost = ''
  let kodesubcost = ''
  let nodph = ''
  let urutdph = 0
  let dppdph = ''
  let tp = ''
  let ppklx = ''
  let nofaktur = ''
  let plok = 0
  let nobons = ''
  let jmlrecord = tipeform == 'add' ? 0 : 1
  let notitipan = ''
  let uruttitipan = 0
  let pSKB = 0
  let perkiraanx = transaksi == 'BBK' ? lawan : kodeperkiraan
  let lawanx = transaksi == 'BBK' ? kodeperkiraan : lawan

  let kodeFlag = $("#AddAddKodeLawan").val()
  let custsupp = ''






  if (kodeFlag == "HT" || kodeFlag == 'UHT') {
    if (transaksi == 'BBK') {
      kodeP = kodeFlag
      statusaktivaP = "HT-"
      custsuppP = tempDPPDPH.KODECUSTSUPP
      custsupp = tempDPPDPH.KODECUSTSUPP
      dppdph = 'DPH'
      nodph = tempDPPDPH.Nobukti

    } else if (transaksi == 'BBM' && kodeFlag == 'HT') {
      kodeL = kodeFlag
      statusaktivaL = "HT-"
      custsuppL = tempDPPDPH.KODECUSTSUPP
      custsupp = tempDPPDPH.KODECUSTSUPP
      dppdph = 'DPP'
      nodph = tempDPPDPH.Nobukti

    }



  }


  if ( kodeFlag == "UHT" && transaksi == "BBM") {


    custsuppL =  $("#input_dphuhtbbm_kodecustsupp").val();
    custsupp = $("#input_dphuhtbbm_kodecustsupp").val();
    statusAktivaL = 'UHT-'
    kodeL = kodeFlag







  }




  if (lawan == '113400') {

    custsupp = $("#AddAddKodeCustsupp").val()
    if (!custsupp) {
      alertify.warning("Pilih customer")
      return
    }
    custsuppP = custsupp
    custsuppL = custsupp




  }


  if (transaksi == 'BBK') {
    kodeP = $("#AddAddKodeLawan").val()
    if (kodeP == "HT") {
      statusaktivaP = "HT-"
    }
  } else {
    kodeL = $("#AddAddKodeLawan").val()
    if (kodeL == "HT") {
      statusaktivaL = "HT-"
    }

  }



  if (transaksi == 'BBM' && kodeFlag == 'UHT') {
    $.ajax({
        url: "{!! url('bankspadd') !!}",
        type: "post",
        async: false,
        data: {
          tipeform,
          choice,
          _token,
          nobukti ,
          nourut,
          transaksi,
          note,
          kodeperkiraan ,
          tanggal,

          lampiran ,
          keterangan2 ,
          perkiraanx,
          lawanx,

          kodedevisi ,
          valas ,
          kurs  ,
          lawan ,
          jumlah,
          keterangan  ,
          keterangandetail ,
          kodedepartemen  ,

          kredit,
          kreditrp,

          tphc,
          jumlahrp ,


          urut ,

          custsuppP ,
          custsuppL,
          noaktivaP ,
          noaktivaL ,
          statusaktivaP ,
          statusaktivaL ,

          nobon ,
          kodebag ,

          kodeP ,
          kodeL ,
          statusgiro ,
          simbol,
          flagsimbol ,
          kodecost ,
          kodesubcost ,
          nodph ,
          urutdph,
          dppdph,
          tp,
          ppklx ,
          nofaktur,
          plok ,
          nobons,
          jmlrecord,
          notitipan ,
          uruttitipan,
          pSKB,
          custsupp
        },
        success: function(res) {
          console.log(res ,'!')

          if (res == 1) {
            // $("#form").modal('toggle')
            alertify.success('Bank telah ditambah');

            $('.showhideitem').hide();
            loadAll()
            // buttonCloseForm()
            tipeform = 'edit'
            // document.getElementById("buttonAddListCustomer").disabled = true
            // document.getElementById("input_add_tanggal").disabled = true

            refreshDataTable(nobukti)

            // $("#form").modal('toggle')

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

  } else if (kodeFlag == 'HT' || kodeFlag == 'UHT') {
    $.ajax({
        url: "{!! url('bankspadddppdph') !!}",
        type: "post",
        async: false,
        data: {
          tipeform,
          choice,
          _token,
          nobukti ,
          nourut,
          transaksi,
          note,
          kodeperkiraan ,
          tanggal,

          lampiran ,
          keterangan2 ,
          perkiraanx,
          lawanx,

          kodedevisi ,
          valas ,
          kurs  ,
          lawan ,
          jumlah,
          keterangan  ,
          keterangandetail ,
          kodedepartemen  ,

          kredit,
          kreditrp,

          tphc,
          jumlahrp ,


          urut ,

          custsuppP ,
          custsuppL,
          noaktivaP ,
          noaktivaL ,
          statusaktivaP ,
          statusaktivaL ,

          nobon ,
          kodebag ,

          kodeP ,
          kodeL ,
          statusgiro ,
          simbol,
          flagsimbol ,
          kodecost ,
          kodesubcost ,
          nodph ,
          urutdph,
          dppdph,
          tp,
          ppklx ,
          nofaktur,
          plok ,
          nobons,
          jmlrecord,
          notitipan ,
          uruttitipan,
          pSKB,
          custsupp
        },
        success: function(res) {
          console.log(res ,'!')

          if (res == 1) {
            // $("#form").modal('toggle')
            alertify.success('Bank telah ditambah');
            loadAll()
            // buttonCloseForm()
            $('.showhideitem').hide();
            tipeform = 'edit'
            lockFormAdd()
            // document.getElementById("buttonAddListCustomer").disabled = true
            // document.getElementById("input_add_tanggal").disabled = true

            refreshDataTable(nobukti)

            // $("#form").modal('toggle')

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
  } else {
    $.ajax({
        url: "{!! url('bankspadd') !!}",
        type: "post",
        async: false,
        data: {
          tipeform,
          choice,
          _token,
          nobukti ,
          nourut,
          transaksi,
          note,
          kodeperkiraan ,
          tanggal,

          lampiran ,
          keterangan2 ,
          perkiraanx,
          lawanx,

          kodedevisi ,
          valas ,
          kurs  ,
          lawan ,
          jumlah,
          keterangan  ,
          keterangandetail ,
          kodedepartemen  ,

          kredit,
          kreditrp,

          tphc,
          jumlahrp ,


          urut ,

          custsuppP ,
          custsuppL,
          noaktivaP ,
          noaktivaL ,
          statusaktivaP ,
          statusaktivaL ,

          nobon ,
          kodebag ,

          kodeP ,
          kodeL ,
          statusgiro ,
          simbol,
          flagsimbol ,
          kodecost ,
          kodesubcost ,
          nodph ,
          urutdph,
          dppdph,
          tp,
          ppklx ,
          nofaktur,
          plok ,
          nobons,
          jmlrecord,
          notitipan ,
          uruttitipan,
          pSKB,
          custsupp
        },
        success: function(res) {
          console.log(res ,'!')

          if (res == 1) {
            // $("#form").modal('toggle')
            alertify.success('Bank telah ditambah');

            $('.showhideitem').hide();
            loadAll()
            // buttonCloseForm()
            tipeform = 'edit'
            // document.getElementById("buttonAddListCustomer").disabled = true
            // document.getElementById("input_add_tanggal").disabled = true

            refreshDataTable(nobukti)

            // $("#form").modal('toggle')

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












}

function submitEdit () {






  let choice = "U"
  let _token  = $("#_token").val()
  let nobukti  = $("#input_add_nobukti").val()
  let nourut  = $("#input_add_nourut").val()
  let tanggal  = $("#input_add_tanggal").val()

  // let nobkmbbm = $("#input_add_nobkmbbm").val()
  let dibayar  = $("#AddAddDibayar").val()
  // let kl  = $("#AddAddKurangBayar").val()
  // let lb = $("#AddAddLebihBayar").val()
  // let perkiraan = $("#AddAddKodePerkiraan").val()

  if (Number(dibayar) > 0 || Number(lb) > 0 || Number(kl) >0 ) {

  } else {
    alertify.warning("Nilai <= 0")
    return
  }
  let urut = dataEdit.URUT
  console.log({

    choice,
    _token,
    nobukti ,
    nourut,
    dibayar,
    perkiraan,
    kl,
    lb,
    tanggal,
    urut
  })
// return



  $.ajax({
      url: "{!! url('pelunasanpiutangdppspkoreksi') !!}",
      type: "post",
      async: false,
      data: {

        choice,
        _token,
        nobukti ,
        nourut,
        dibayar,
        perkiraan,
        kl,
        lb,
        tanggal,
        urut
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('DPP telah diedit');
          loadAll()
          // buttonCloseForm()
          tipeform = 'edit'
          // document.getElementById("buttonAddListCustomer").disabled = true
          // document.getElementById("input_add_tanggal").disabled = true
          $('.showhideitem').hide();
          refreshTableKoreksi(nobukti, nobkmbbm)

          // $("#form").modal('toggle')

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
  document.getElementById("input_add_tanggal").disabled = true
  document.getElementById("input_add_bon").disabled = true
  document.getElementById("input_add_kepadaterima").disabled = true
  document.getElementById("buttonAddListPerkiraan").disabled = true
  document.getElementById("input_add_transaksi").disabled = true

}

function lockFormAddAdd () {
  document.getElementById("buttonAddListDepartemen").disabled = true
  document.getElementById("buttonAddListLawan").disabled = true
  document.getElementById("buttonAddListValas").disabled = true
  document.getElementById("buttonAddListDevisi").disabled = true

}

function unlockFormAddAdd () {
  document.getElementById("buttonAddListDepartemen").disabled = false
  document.getElementById("buttonAddListLawan").disabled = false
  document.getElementById("buttonAddListValas").disabled = false
  document.getElementById("buttonAddListDevisi").disabled = false
}




function refreshDataTable (nobukti) {
  console.log('refreshDataTable' , nobukti)
  let _token = $("#_token").val();
  listData = []
  $.ajax({
    url: "{!! url('pengajuandphspdetail') !!}",
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

      $('#formAddAdd').hide();
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

        // <td>${item.TipeTrans == 'BBK' ? item.Lawan : item.Perkiraan}</td>
        // <td>${item.TipeTrans == 'BBK' ? item.NamaLawan : item.NamaPerkiraan}</td>
        // <td>${item.TipeTrans == 'BBK' ? item.Perkiraan : item.Lawan }</td>
        // <td>${item.TipeTrans == 'BBK' ?  item.NamaPerkiraan : item.NamaLawan }</td>

              rowTable += `
                <tr>
                  <td>${item.NamaCustSupp}</td>

                  <td>${item.NoFaktur}</td>
                  <td class="text-right">${formatAngka(parseFloat(item.dibayar).toFixed(2))}</td>
                  <td class="text-right">${formatAngka(parseFloat(Number(item.Nilai) - Number(item.dibayar)).toFixed(2)) }</td>
                  <td class="text-right">${formatAngka(parseFloat(Number(item.dibayar) - Number(item.Nilai)).toFixed(2)) }</td>


                  <td>${item.Perkiraan ? item.Perkiraan : '' }</td>

                  <td>${item.Noinvoice ? item.Noinvoice : '' }</td>
                  <td>${item.TglInv ? formatDate(item.TglInv) : '' }</td>


                  <td class="text-center">

                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete(${i}  )"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>

              `

              // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
      });

      document.getElementById("addTableData").innerHTML = rowTable


        document.getElementById("input_add_nobukti").value = listData[0].NoBukti

        // document.getElementById("input_add_transaksi").value = listData[0].NamaCustSupp
        // document.getElementById("input_add_alamatcustomer").value = listData[0].Alamat1
        // document.getElementById("input_add_nobukti").value = listData[0].NoBukti
        document.getElementById("input_add_tanggal").valueAsDate = new Date(listData[0].Tanggal)
        document.getElementById("input_add_valas").value = listData[0].Valas










    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
}


function updatealdok () {
  console.log('aldok')
  let _token = $("#_token").val();
  let notacetak = xnotacetak;
  // $tempOutstanding[$i][0]->notacetak;
  let namapenerima = $("#input_add_nama").val();
  let tglterima = $("#input_add_tglterima").val();
  console.log(notacetak)
  console.log(namapenerima)
  console.log(tglterima)

  $.ajax({
    url: "{!! url('cetaktandaterimaspkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      namapenerima,
      tglterima,
      notacetak

    },
    success: function(res) {
      if (res == 1) {
        alertify.success('Berhasil update data penerima dokumen')
        loadAll()
        $('.mainpage').hide();
        $('#page1').show();
      }





    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
  $("#formAldok").modal('toggle')

}



function submitOtorisasi () {

  let _token = $("#_token").val();
  let nobukti = $("#input_detail_nobukti").val();
  $.ajax({
    url: "{!! url('pelunasanpiutangdppspotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      if (res == 1) {
        alertify.success('Berhasil update otorisasi')
        loadAll()
        $('.mainpage').hide();
        $('#page1').show();
      }





    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

















function buttonAddItem () {
  let _token = $("#_token").val();

  let kodecust = $("#input_add_kodecust").val();
  // let nobkmbbm = $("#input_add_nobkmbbm").val();
  listTambah = []
  listTambahKL = []
  $.ajax({
    url: "{!! url('pelunasanpiutangdppgetlistterimadpp') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nodpp : nobkmbbm,
      kodecust

    },
    success: function(res) {
      console.log(res)

      listProsesTerimaDPP = res

      let rowTable = ``

      listProsesTerimaDPP.forEach((item, i) => {
        console.log(i)
        rowTable += `
          <tr>
            <td class="text-center"><input class="" type="checkbox" onchange="prosesCheckbox(${i})" value="" id="list_proses_checkbox${i}"></td>
            <td>${item.NOFAKTUR}</td>
            <td>${item.namacust}</td>
            <td class="text-right">${formatAngka(parseFloat(item.TOTFAKTUR).toFixed(2))}</td>
            <td class="text-right">${formatAngka(parseFloat(item.SDHBAYAR).toFixed(2))}</td>

            <td class="text-center">
            <div class="input-group form-group">
              <input style="height:30px; width:160px" id="list_proses_dibayar${i}" type="number" value='${parseFloat(item.DIBAYAR).toFixed(2)}' class="form-control text-right" disabled>

              <button id="buttonChangeDibayar${i}" style="height:30px; padding: 0px; width: 25px" type="button" onclick="buttonChangeDibayar(${i})" class="btn btn-primary" >+</button>

            </div></td>
            <td class="text-center">
            <input style="height:30px; width:160px" id="list_proses_LB${i}" type="number" value='${parseFloat(item.LB).toFixed(2)}' class="form-control text-right" disabled>
            </td>
            <td class="text-center">
            <input style="height:30px; width:160px" id="list_proses_KL${i}" type="number" value='0.00' class="form-control text-right" disabled>
            </td>
          </tr>
        `
      });


      // <td class="text-right">${formatAngka(parseFloat(item.LB).toFixed(2))}</td>
      // <td class="text-right">${formatAngka(parseFloat(item.KL).toFixed(2))}</td>


      if (!listProsesTerimaDPP.length) {
        rowTable = `
          <tr><td colspan=8 class="text-center">Data tidak ditemukkan</td></tr>
        `

      }
      document.getElementById("input_modal_nobukti").value = nobkmbbm
      document.getElementById("input_modal_namacust").value = $("#input_add_namacust").val();
      document.getElementById("input_modal_jumlah").value = $("#input_add_jumlah").val();
      document.getElementById("input_modal_dibayar").value = $("#input_add_dibayar").val();
      document.getElementById("input_modal_sisa").value = $("#input_add_sisa").val();
      document.getElementById("tabel_data_add_list_modal").innerHTML = rowTable


        $("#form").modal('toggle')
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


  // buttonRefreshListPengajuan()


}


function refreshTableKL () {

  console.log(saveHeaderInvoice)
  console.log('refreshTableKL')
  console.log(saveHeaderInvoice.NOFAKTUR)
  console.log(listTambahKL[saveHeaderInvoice.NOFAKTUR])
  let xlist = listTambahKL[saveHeaderInvoice.NOFAKTUR]
  console.log(xlist)
  if (!xlist) {
    xlist = []
  }
  console.log(xlist)
  console.log('weeewoooo')
  if (!xlist.length) {
    document.getElementById("tabel_data_add_list_modalx").innerHTML = `
    <tr>
      <td class="text-center" colspan=3>Belum ada data</td>
    </tr>
    `

  } else {
    rowTablex = ''
    let xTempTotalKL = 0
    xlist.forEach((item, i) => {
      xTempTotalKL += Number(item.inputKL)
      rowTablex += `
        <tr>
          <td class="text-right">${item.inputKL}</td>
          <td>${item.inputPerkiraanKL}</td>
          <td>${item.inputNamaPerkiraanKL}</td>
        </tr>
      `

    });

    document.getElementById("tabel_data_add_list_modalx").innerHTML = rowTablex
    document.getElementById(`list_proses_KL${saveHeaderIndex}`).value = parseFloat(xTempTotalKL).toFixed(2)


  }

}


function buttonChangeDibayar (index) {
  // sp_TempTerimaDPP

  let xcheck = document.getElementById(`list_proses_checkbox${index}`).checked
  if(!xcheck) {
    alertify.warning("Pilih invoice terlebih dahulu")
    return
  }
  let x = listProsesTerimaDPP[index]
  console.log(x)
  // listProsesTerimaDPP[saveHeaderIndex]
  saveHeaderInvoice = listProsesTerimaDPP[index]
  saveHeaderIndex = index
  let xdibayar = $(`#list_proses_dibayar${index}`).val();
  let xLB = $(`#list_proses_LB${index}`).val();
  let sisa = $(`#input_modal_sisa`).val();
  console.log(saveHeaderInvoice.NoBukti)
  console.log(listTambahKL[saveHeaderInvoice.NoBukti])

  console.log(x.TOTFAKTUR)
  console.log(formatAngka(parseFloat(x.TOTFAKTUR).toFixed(2)))
  console.log('==')
  console.log(xdibayar, xLB , sisa)
  document.getElementById("input_modalx_nilainotadibayar").value = parseFloat(Number(x.TOTFAKTUR) - Number(x.SDHBAYAR)).toFixed(2)
  document.getElementById("input_modalx_dibayar").value = parseFloat(xdibayar).toFixed(2)

  document.getElementById("input_modalx_lebihbayar").value = parseFloat(xLB).toFixed(2)
  document.getElementById("input_modalx_sisanotadibayar").value = parseFloat(sisa).toFixed(2)
  if (xLB > 0) {
    document.getElementById("input_modalx_perkiraanlebihbayar").value = listTambahLB[saveHeaderInvoice.NOFAKTUR].inputPerkiraanLB
    document.getElementById("input_modalx_namaperkiraanlebihbayar").value = listTambahLB[saveHeaderInvoice.NOFAKTUR].inputNamaPerkiraanLB

  } else {
    document.getElementById("input_modalx_perkiraanlebihbayar").value = ''
    document.getElementById("input_modalx_namaperkiraanlebihbayar").value = ''

  }

  refreshTableKL()


  $('.showhideitemKL').hide()


  $("#formX").modal('toggle')


}


function buttonDeleteItem (index) {
  let akses = $("#akses_ishapus").val();
  tipeform = 'edit'
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let dataEdit = listPenerimaan[index]


  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus faktur '+ dataEdit.NOFAKTUR +' ?',
      function() {

        let choice = "D"
        let _token  = $("#_token").val()
        let nobukti  = $("#input_add_nobukti").val()
        let nourut  = $("#input_add_nourut").val()
        let tanggal  = $("#input_add_tanggal").val()
        // let nobkmbbm = $("#input_add_nobkmbbm").val()

        let dibayar  = 0
        let kl  = 0
        let lb = 0
        let perkiraan = ''
        let urut = dataEdit.URUT

          $.ajax({
              url: "{!! url('pelunasanpiutangdppspkoreksi') !!}",
              type: "post",
              async: false,
              data: {

                choice,
                _token,
                nobukti ,
                nourut,
                dibayar,
                perkiraan,
                kl,
                lb,
                tanggal,
                urut
              },
              success: function(res) {
                console.log(res ,'!')

                if (res == 1) {
                  // $("#form").modal('toggle')
                  alertify.success('Faktur telah dihapus');
                  loadAll()
                  // buttonCloseForm()
                  tipeform = 'edit'
                  // document.getElementById("buttonAddListCustomer").disabled = true
                  // document.getElementById("input_add_tanggal").disabled = true
                  $('.showhideitem').hide();
                  refreshTableKoreksi(nobukti , nobkmbbm)

                  // $("#form").modal('toggle')

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
    ,function(){
      console.log('no')
    });




}

function buttonEditItem (index) {

  console.log(listPenerimaan[index])
  dataEdit = listPenerimaan[index]
  let editDibayar = dataEdit.DIBAYAR
  let editLB = dataEdit.LB
  let editKL = dataEdit.KL
  document.getElementById("AddAddFaktur").value = dataEdit.NOFAKTUR
  document.getElementById("AddAddKodePerkiraan").value = dataEdit.perkiraan
  document.getElementById("AddAddNamaPerkiraan").value = dataEdit.NamaPerkiraan
  document.getElementById("AddAddDibayar").value = '0.00'
  document.getElementById("AddAddKurangBayar").value = '0.00'
  document.getElementById("AddAddLebihBayar").value = '0.00'
  document.getElementById("AddAddDibayar").disabled = true
  document.getElementById("AddAddKurangBayar").disabled = true
  document.getElementById("AddAddLebihBayar").disabled = true
  if (Number(editDibayar) > 0) {

    document.getElementById("AddAddDibayar").disabled = false
    document.getElementById("AddAddDibayar").value = parseFloat(editDibayar).toFixed(2)

  } else if (Number(editKL) > 0) {
    document.getElementById("AddAddKurangBayar").disabled = false
    document.getElementById("AddAddKurangBayar").value = parseFloat(editKL).toFixed(2)

  } else {
    document.getElementById("AddAddLebihBayar").disabled = false
    document.getElementById("AddAddLebihBayar").value = parseFloat(editLB).toFixed(2)

  }
  $('.showhideitem').show()



}


function refreshTableKoreksi ( nobukti , nodpp) {

let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('pelunasanpiutangdppspdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      nodpp

    },
    success: function(res) {
      console.log(res)
      penerimaanHeader = res.header[0]
      listPenerimaan = res.detail
      if (!listPenerimaan.length) {
        resRefresh = 0

        $(".showhideitem").hide()
        $(".mainpage").hide()
        $("#page1").show()

        alertify.warning("Data habis")
        return
      }

      let xxx = 0
      if ( Number(res.X[0].Dibayar) ) {
        xxx += Number(res.X[0].Dibayar)
      }
      if ( Number(res.X[0].LB)) {
        xxx += Number(res.X[0].LB)
      }

      // document.getElementById("input_add_nobkmbbm").value = res.header[0].NoDPP
      document.getElementById("input_add_nobukti").value = res.header[0].NoBukti

      document.getElementById("input_add_tanggal").value = formatDate(res.header[0].Tanggal , '-')
      document.getElementById("input_add_kodecust").value = res.header[0].KODECUSTSUPP
      document.getElementById("input_add_namacust").value = res.header[0].NamaCustSupp
      document.getElementById("input_add_valas").value = res.detail[0].Valas
      let dibayarx = parseFloat(xxx).toFixed(2)
      let jumlahx = res.header[0].Debet ? parseFloat(res.header[0].Debet ).toFixed(2) : '0.00'
      console.log(dibayarx , jumlahx)
      document.getElementById("input_add_dibayar").value = parseFloat(xxx).toFixed(2)
      document.getElementById("input_add_jumlah").value = res.header[0].Debet ? parseFloat(res.header[0].Debet ).toFixed(2) : '0.00'
      document.getElementById("input_add_sisa").value =  parseFloat(Number(jumlahx) - Number(xxx)).toFixed(2)
      let totaldibayarx = 0
      let totallbx = 0
      let totalklx = 0
      let rowTable = ``
      res.detail.forEach((item, i) => {
         totaldibayarx += Number(item.DIBAYAR)
         totallbx += Number(item.LB)
         totalklx += Number(item.KL)

        rowTable += `
          <tr>
            <td>${item.NamaKasBank}</td>
            <td>${item.NOFAKTUR}</td>
            <td class="text-right">${item.DIBAYAR ? formatAngka(parseFloat(item.DIBAYAR).toFixed(2)) : '0.00' }</td>
            <td class="text-right">${item.LB ? formatAngka(parseFloat(item.LB).toFixed(2)) : '0.00' }</td>
            <td class="text-right">${item.KL ? formatAngka(parseFloat(item.KL).toFixed(2)) : '0.00' }</td>
            <td>${item.perkiraan}</td>
            <td>${item.KodeCustSuppD}</td>
            <td>${item.NamaCustSuppD ? item.NamaCustSuppD : ''}</td>
            <td class="text-center">
              <button class="btn btn-success btn-sm" type="button" onclick="buttonEditItem('${i}' )"><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteItem('${i}' )"><i class="bi bi-trash"></i></button>
            </td>
          </tr>

        `
      });

      rowTable += `
        <tr>
          <td colspan=2 class="text-right">Total:</td>
          <td class="text-right">${formatAngka(parseFloat(totaldibayarx).toFixed(2))}</td>
          <td class="text-right">${formatAngka(parseFloat(totallbx).toFixed(2))}</td>
          <td class="text-right">${formatAngka(parseFloat(totalklx).toFixed(2))}</td>
          <td colspan=4 ></td>
        </tr>
      `


      document.getElementById("addTableData").innerHTML = rowTable
      urutTrans = res.detail[0].UrutDPP





      console.log('a')
      resRefresh = 1



    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}


function buttonDetail (nobukti , tipe = 0 , nodpp = '') {
  console.log('buttonDetail')
  console.log(nobukti , tipe , nodpp )
  resRefresh = 0
  // let akses = $("#akses_iskoreksi").val();
  // tipeform = 'edit'
  // if (!Number(akses)) {
  //   alertify.warning('No access')
  //   return
  // }


  // document.getElementById("input_add_tanggal").disabled = true

  console.log("buttonDetail")





  let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('pelunasanpiutangdppspdetailpenerimaan') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti,
        nodpp

      },
      success: function(res) {
        console.log(res)
        // penerimaanHeader = res.header[0]
        // listPenerimaan = res.detail
        document.getElementById("input_detail_nobkmbbm").value = res.header[0].NoDPP
        document.getElementById("input_detail_nobukti").value = res.header[0].NoBukti
        let xxx = 0
        if ( Number(res.X[0].Dibayar) ) {
          xxx += Number(res.X[0].Dibayar)
        }
        if ( Number(res.X[0].LB)) {
          xxx += Number(res.X[0].LB)
        }
        document.getElementById("input_detail_tanggal").value = formatDate(res.header[0].Tanggal , '-')
        document.getElementById("input_detail_kodecust").value = res.header[0].KODECUSTSUPP
        document.getElementById("input_detail_namacust").value = res.header[0].NamaCustSupp
        document.getElementById("input_detail_valas").value = res.detail[0].Valas
        let dibayarx = parseFloat(xxx).toFixed(2)
        let jumlahx = res.header[0].Debet ? parseFloat(res.header[0].Debet ).toFixed(2) : '0.00'
        console.log(dibayarx , jumlahx)
        document.getElementById("input_detail_dibayar").value = parseFloat(xxx).toFixed(2)
        document.getElementById("input_detail_jumlah").value = res.header[0].Debet ? parseFloat(res.header[0].Debet ).toFixed(2) : '0.00'
        document.getElementById("input_detail_sisa").value =  parseFloat(Number(jumlahx) - Number(xxx)).toFixed(2)

        let rowTable = ``
        res.detail.forEach((item, i) => {
          rowTable += `
            <tr>
              <td>${item.NamaKasBank}</td>
              <td>${item.NOFAKTUR}</td>
              <td class="text-right">${item.DIBAYAR ? formatAngka(parseFloat(item.DIBAYAR).toFixed(2)) : '0.00' }</td>
              <td class="text-right">${item.LB ? formatAngka(parseFloat(item.LB).toFixed(2)) : '0.00' }</td>
              <td class="text-right">${item.KL ? formatAngka(parseFloat(item.KL).toFixed(2)) : '0.00' }</td>
              <td>${item.perkiraan}</td>
              <td>${item.KodeCustSuppD}</td>
              <td>${item.NamaCustSuppD ? item.NamaCustSuppD : ''}</td>

            </tr>

          `
        });

        document.getElementById("detailTableData").innerHTML = rowTable
        // urutTrans = res.detail[0].UrutDPP




        $(".page3showhide").hide()

        if (tipe) {
          $(".otorisasishowhide").show()
        } else {
          $(".detailshowhide").show()
        }



        $(".mainpage").hide()
        $("#page3").show()
      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })

}


function buttonKoreksi (nobukti , nodpp) {
  resRefresh = 0
  let akses = $("#akses_iskoreksi").val();
  tipeform = 'edit'
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  document.getElementById("input_add_tanggal").disabled = true







  let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('pelunasanpiutangdppspdetailpenerimaan') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti,
        nodpp

      },
      success: function(res) {

        console.log(res)
        penerimaanHeader = res.header[0]
        listPenerimaan = res.detail

        if ( res.detail[0].IsOtorisasi1 == 1) {
          alertify.warning("Data sudah diotorisasi")
          return
        }
        // document.getElementById("input_add_nobkmbbm").value = res.header[0].NoDPP
        document.getElementById("input_add_nobukti").value = res.header[0].NoBukti
        let xxx = 0
        if ( Number(res.X[0].Dibayar) ) {
          xxx += Number(res.X[0].Dibayar)
        }
        if ( Number(res.X[0].LB)) {
          xxx += Number(res.X[0].LB)
        }
        document.getElementById("input_add_tanggal").value = formatDate(res.header[0].Tanggal , '-')
        document.getElementById("input_add_kodecust").value = res.header[0].KODECUSTSUPP
        document.getElementById("input_add_namacust").value = res.header[0].NamaCustSupp
        document.getElementById("input_add_valas").value = res.detail[0].Valas
        let dibayarx = parseFloat(xxx).toFixed(2)
        let jumlahx = res.header[0].Debet ? parseFloat(res.header[0].Debet ).toFixed(2) : '0.00'
        console.log(dibayarx , jumlahx)
        document.getElementById("input_add_dibayar").value = parseFloat(xxx).toFixed(2)
        document.getElementById("input_add_jumlah").value = res.header[0].Debet ? parseFloat(res.header[0].Debet ).toFixed(2) : '0.00'
        document.getElementById("input_add_sisa").value =  parseFloat(Number(jumlahx) - Number(xxx)).toFixed(2)
        let totaldibayarx = 0
        let totallbx = 0
        let totalklx = 0
        let rowTable = ``
        res.detail.forEach((item, i) => {
          totaldibayarx += Number(item.DIBAYAR)
          totallbx += Number(item.LB)
          totalklx += Number(item.KL)
          rowTable += `
            <tr>
              <td>${item.NamaKasBank}</td>
              <td>${item.NOFAKTUR}</td>
              <td class="text-right">${item.DIBAYAR ? formatAngka(parseFloat(item.DIBAYAR).toFixed(2)) : '0.00' }</td>
              <td class="text-right">${item.LB ? formatAngka(parseFloat(item.LB).toFixed(2)) : '0.00' }</td>
              <td class="text-right">${item.KL ? formatAngka(parseFloat(item.KL).toFixed(2)) : '0.00' }</td>
              <td>${item.perkiraan}</td>
              <td>${item.KodeCustSuppD}</td>
              <td>${item.NamaCustSuppD ? item.NamaCustSuppD : ''}</td>
              <td class="text-center">
                <button class="btn btn-success btn-sm" type="button" onclick="buttonEditItem('${i}' )"><i class="bi bi-pen"></i></button>
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteItem('${i}' )"><i class="bi bi-trash"></i></button>
              </td>
            </tr>

          `
        });

        rowTable += `
          <tr>
            <td colspan=2 class="text-right">Total:</td>
            <td class="text-right">${formatAngka(parseFloat(totaldibayarx).toFixed(2))}</td>
            <td class="text-right">${formatAngka(parseFloat(totallbx).toFixed(2))}</td>
            <td class="text-right">${formatAngka(parseFloat(totalklx).toFixed(2))}</td>
            <td colspan=4 ></td>
          </tr>
        `

        document.getElementById("addTableData").innerHTML = rowTable
        urutTrans = res.detail[0].UrutDPP









        $(".showhideitem").hide()
        $(".mainpage").hide()
        $("#page2").show()
      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })

}

function buttonAldok (notacetak,tglterima,namapenerima) {

  console.log('aldok')
 $("#formAldok").modal("toggle");
 console.log(notacetak)
 console.log(tglterima)
 console.log(namapenerima)
 xnotacetak = notacetak

document.getElementById("input_add_tglterima").value = tglterima
document.getElementById("input_add_nama").value = namapenerima

}



function buttonAdd (kodecustsupp) {
  console.log('buttonAdd' ,kodecustsupp)
  let akses = $("#akses_istambah").val();
  console.log(kodecustsupp)
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  tipeform = 'add'
  setNewNoBukti()
  document.getElementById("input_add_tanggal").disabled = false
  document.getElementById("input_add_tanggal").valueAsDate = new Date()

  let _token = $("#_token").val();
  $.ajax({
   url: "{!! url('cetaktandaterimadetailoutstanding') !!}",
   type: "post",
   async: false,
   data: {
     _token,
     kodecustsupp

   },
   success: function(res) {

   },
   error: function (err) {
     console.log(err)
     alertify.warning('Terjadi kesalahan silahkan refresh browser')
   }

 })

  $.ajax({
    url: "{!! url('cetaktandaterimadetailCetak') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp

    },
    success: function(res) {
      console.log(res , '!!!')

      listOutstanding = res
      let rowTable = ""


      listOutstanding.forEach((item, i) => {
        rowTable += `
      <tr>
      <td>
      <div class="form-check text-center">
        <input id="addChecklist${i}" class="form-check-input" type="checkbox" value="" >
      </div>
      </td>
        <td>${item.NoBukti }</td>
        <td>${item.Tanggal ? formatDate(item.Tanggal,'/') : ''  }</td>
        <td>${item.pono? item.pono : '' }</td>
        <td>${item.penerima }</td>
      </tr>
        `
      });

      document.getElementById("addTableData").innerHTML = rowTable


      $(".showhideitem").hide()
      $(".mainpage").hide()
      $("#page2").show()
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

  return

}

function buttonAddAddItem () {
    let value = $("#input_add_kodeperkiraan").val();
    if(!value) {
      alertify.warning("Pilih perkiraan terlebih dahulu")
      return
    }

    $('#buttonSubmitAddAdd').show();
    $('#buttonSubmitAddEdit').hide();

    $('#labelAddAddItem').show();
    $('#labelAddEditItem').hide();
    $('#rowCustsupp').hide();
    // $('#rowCustsupp').show();
    // document.getElementById("buttonSubmitAddAdd").style.display = "block";
    // // document.getElementById("buttonSubmitAddEdit").style.display = "none";
    // document.getElementById("labelAddAddItem").style.display = "block";
    // document.getElementById("labelAddEditItem").style.display = "none";
    unlockFormAddAdd()
    $('.showhideitem').hide();
    cleanFormAddAdd()
    $('#formAddAdd').show();

}

function buttonAddEditItem (i) {
  tempBarangAddEdit = listData[i]
  console.log(tempBarangAddEdit)
  lockFormAddAdd()
  cleanFormAddAdd()
  let value = $("#input_add_transaksi").val();

  console.log(value, tempBarangAddEdit.KodeL )
  if (value == 'BBM' && tempBarangAddEdit.KodeL == 'UHT' ) {
    document.getElementById("AddAddJumlah").disabled = true
  } else {
    document.getElementById("AddAddJumlah").disabled = false
  }

  document.getElementById("AddAddKodeDevisi").value = tempBarangAddEdit.Devisi
  document.getElementById("AddAddNamaDevisi").value = tempBarangAddEdit.NamaDevisi

  document.getElementById("AddAddValas").value = tempBarangAddEdit.Valas
  document.getElementById("AddAddKurs").value = parseFloat(tempBarangAddEdit.Kurs).toFixed(2)

  document.getElementById("AddAddLawan").value = tempBarangAddEdit.TipeTrans == 'BBK' ? tempBarangAddEdit.Perkiraan : tempBarangAddEdit.Lawan
  document.getElementById("AddAddKeteranganLawan").value = tempBarangAddEdit.TipeTrans == 'BBK' ? tempBarangAddEdit.NamaPerkiraan : tempBarangAddEdit.NamaLawan

  console.log(tempBarangAddEdit.Debet)
  document.getElementById("AddAddJumlah").value = parseFloat(tempBarangAddEdit.Debet).toFixed(2)
  document.getElementById("AddAddKeterangan").value = tempBarangAddEdit.Keterangan
  document.getElementById("AddAddKeteranganDetail").value = tempBarangAddEdit.KetDetail

  document.getElementById("AddAddKodeDepartemen").value = tempBarangAddEdit.KodeBag
  document.getElementById("AddAddNamaDepartemen").value = tempBarangAddEdit.NMDEP


  $('#buttonSubmitAddAdd').hide();
  $('#buttonSubmitAddEdit').show();

  $('#labelAddAddItem').hide();
  $('#labelAddEditItem').show();



  $('.showhideitem').hide();
  $('#formAddAdd').show();

}


function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();

}

function loadAll () {

  console.log('loadall')
  let _token = $("#_token").val();


  $.ajax({
    url: "{!! url('cetaktandaterimaloadall') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)
      let rowTable = ""
      let rowTable2 = ""





      res.tempOutstanding.forEach((item, i) => {

        rowTable += `
        <tr>
          <td>${item[0].nobukti}</td>
          <td>${formatDate(item[0].tanggal,'/')}</td>
          <td>${item[0].namacustsupp}</td>
          <td class='text-center'>

            <button class="btn btn-primary btn-sm" type="button" onclick="buttonAdd('${item[0].nobukti}')"><i class="bi bi-plus"></i></button>

          </td>
        </tr>

        `







      });


      res.tempPenerimaan.forEach((item, i) => {
          rowTable2 += `
          <tr>
          <td class="text-center">
                <button class="btn btn-warning btn-sm" type="button" title="isi terima" onclick="buttonAldok('${item[0].nocetak}')">
                  <i class="bi bi-pen"></i>
                </button>
                <button class="btn btn-primary btn-sm" title="Print" onclick="submitPrintUlang('${item.nobukti}')"><i class="bi bi-printer"></i>
                </button>



            </td>






            <td>${item[0].nocetak}</td>
            <td>${item[0].nobukti}</td>
            <td>${item[0].tglcetak ? formatDate(item[0].tglcetak, '/') : ''}</td>
            <td>${item[0].tanggal ? formatDate(item[0].tanggal, '/') : ''}</td>
            <td>${item[0].namacustsupp}</td>
            <td>${item[0].tglterima ? formatDate(item[0].tglterima, '/') : ''}</td>
            <td>${item[0].namapenerima}</td>
            ${item[0].IsOtorisasi1 == 1 ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>' : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'}

            <td>${item[0].OtoUser1}</td>
            <td>${item[0].TglOto1 ? formatDate(item[0].TglOto1, '/')  : '' }</td>
            <td>${item[0].usercetak}</td>
            <td>${item[0].namadok}</td>
            <td>${item[0].alamatdok}</td>








          </tr>
          `
      });


      $('#tabel').DataTable().destroy();
      $('#tabel2').DataTable().destroy();

      document.getElementById("tabel2_data").innerHTML = rowTable2
      document.getElementById("tabel_data").innerHTML = rowTable
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
          "columnDefs": [
          {  "className": "text-right", "targets": [] },
        ]
        });


        $("#tabel2").DataTable({
          "lengthChange": false,
            "paging": false ,
            "columnDefs": [
            {  "className": "text-right", "targets": [] },
          ]
          });



    }})

}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }


    let _token = $('#_token').val()
    let dataPrint = []

    for (let i = 0; i < listOutstanding.length; i++) {

      if (document.getElementById(`addChecklist${i}`).checked) {

        // row_data[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
        dataPrint.push(listOutstanding[i])
      }
    }

    let nobukti1 = $("#input_add_nobukti").val()
    let nourut = $("#input_add_nourut").val()
    let tanggal = $("#input_add_tanggal").val()
    console.log("INI" , {
    NOBUKTI: nobukti1,
    tempData: dataPrint, tanggal,
    nourut})
    $.ajax({
      url: "{!! url('cetaktandaterimaspcetak') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti1,
        tempData: dataPrint,
        nourut,
        tanggal,
        ispreview :0
      },
      success: function(res) {
        console.log(res)
        // loadAll()
        // dataPrint = res
        // console.log(res[0])

        // console.log(res[0][0].IsOtorisasi1)

      }
    })

    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=8) {
      let tempArray = dataPrint.slice(i,i+8)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0].split('-').reverse().join('/');
    let tglprint =tanggal.split(' ')[0].split('-').reverse().join('/');
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
        height: 18px;
        padding: 0px 4px;
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
        height: 14cm;
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
                  <div class="pb-1" style="width: 100%; margin-top: 15px;">Kepada YTH : ` + dataPrint[0].KodeCustSupp + ` - ` + dataPrint[0].namakebun + `</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">`+`${dataPrint[0].NamaCustSupp ?? ''}${dataPrint[0].NamaCustSupp && dataPrint[0].Alamat ? ' - ' : ''}${dataPrint[0].Alamat ?? ''}` + `</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 50%; margin-left: 20px;">
                <div style="display: flex; width: 100%; margin-top: 15px;">
                  <h2 class="m-0 pb-2">TANDA TERIMA INVOICE & FAKTUR PAJAK</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No. TT</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+nobukti1+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Tanggal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+tglprint+`</div>
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
        <table style="width:98%; border-collapse:collapse; font-family:sans-serif; font-size:10px;">
            <thead>
              </tr>
                  <tr>
                  <td rowspan="2" class="text-center" style="width: 1%; border-left:none;  border-right:none;">No.</td>
                    <td rowspan="2" class="text-center" style="width: 20%; border-left:none; border-right:none;">NO INVOICE</td>
                    <td rowspan="2" class="text-center" style="width: 20%; border-left:none; border-right:none;">TGL INVOICE</td>
                    <td rowspan="2" class="text-center" style="width: 20%; border-left:none; border-right:none;">NO FAKTUR PAJAK</td>
                    <td rowspan="2" class="text-right" style="width: 20%; border-left:none; border-right:none;">NILAI INVOICE</td>
                    <td rowspan="2" class="text-center" style="width: 20%; border-left:none; border-right:none;">NO PO CUST</td>
                  </tr>
                </thead> `;

    let z = 0
    let maxRow = 8;
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalJumlah = 0;

    dataPrint.forEach(item => {

      if (item.NNetRp) {
        grandTotalJumlah += Number(item.NNetRp) || 0;
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
        item.forEach((itemSub, j) => {
          tempPrintStr += ``



         tempPrintStr += `
         <tr>
         <td class="text-center"
               style="width: 1%; border-bottom: none; border-top: none;  border-left: none;  border-right: none;">${z+1}</td>
         <td class="text-align: center"
               style="width: 20%; text-align: center; border-bottom: none; border-top: none; border-left: none;  border-right: none;">${itemSub.NoBukti ?? ''}</td>
         <td class="text-align: center"
               style="width: 20%; text-align: center; border-bottom: none; border-top: none; border-left: none;  border-right: none;">${itemSub.Tanggal ? itemSub.Tanggal.split(' ')[0].split('-').reverse().join('/') : ''}</td>
         <td class="text-center"
               style="width: 20%; border-bottom: none; border-top: none; border-left: none;  border-right: none;">${itemSub.nopajak ?? ''}</td>
         <td class="text-right"
               style="width: 20%; border-bottom: none; border-top: none; border-left: none;  border-right: none;">${itemSub.NNetRp
              ? Number(itemSub.NNetRp).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                })
              : ''}</td>
         <td style="width: 20%; text-align: center; border-bottom: none; border-top: none; border-left: none;  border-right: none;">
              ${itemSub.pono ?? ''}
         </td>
         </tr>`;

           z++;

        });


        let sisaRow = maxRow - item.length;

        for (let k = 0; k < sisaRow; k++) {
          tempPrintStr += `
          <tr>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;">&nbsp;</td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
          </tr>`;
        }

        console.log(i , arrayDataPrint.length)
         if (i === arrayDataPrint.length - 1)  {
          console.log('masok')
          tempPrintStr += `
          <tr>
          <td colspan="3" style="border:1px solid; padding:5px; font-weight:bold; border-bottom: none; border-left: none;  border-right: none;">
            </td>
            <td style="border:1px solid; text-align:right; font-weight:bold; border-bottom: none; border-left: none;  border-right: none;">
            </td>
            <td style="border:1px solid; text-align:right; font-weight:bold; border-bottom: none; border-left: none;  border-right: none;">
              Total :
              ${grandTotalJumlah.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </td>
            <td  style="border:1px solid; border-bottom: none; border-left: none;  border-right: none;"></td>
          </tr>`
         }


         tempPrintStr += `</tbody>`;

         tempPrintStr += `</table>


         <div class="footer-sign font-family: sans-serif;
           font-size: 10px ">

         <div class="row mt-3" style="text-align: left;font-family: sans-serif;
         font-size: 12px ">
         <span style="float: left; display: block; clear: left;">
         </span>


         <div style="width:100%; display:flex; font-weight:bold; margin-top:5px;">

          </div>

         </div>


         <div style="display:flex; justify-content:space-between; width:100%; font-family:sans-serif; font-size:10px;">


          <div style="width:50%;">
          <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -120px; font-family: sans-serif;
             font-size: 10px">
             <tr>
               <td class="no-border text-center" style="width: 20%">Diterima Oleh</td>
               <td class="no-border text-center" style="width: 20%">Diserahkan Oleh</td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               <p class="m-0">Tanggal</p>
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               <p class="m-0">Tanggal</p>
               </td>
             </tr>
           </table>
            <div style="margin-top:10px; font-size:10px; font-style:italic; white-space:nowrap;">
                *Setelah ditandatangani mohon dikirim kembali ke SML atau di Fax ke NO : 0541-4104195, atau email ke : sml.accounting@yahoo.co.id
            </div>

          </div>


          <div style="width:50%;">
          <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 20px; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%"></td>
               <td class="no-border text-center" style="width: 20%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               </td>
             </tr>
           </table>
          </div>
        </div>

         </div>

 
         <div class="footer-print-date">
           <table class="m-0" style="width: 100% ; font-family: sans-serif;
           font-size: 10px; margin-left: -25px;">
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

    $(".mainpage").hide()
    $("#page1").show()
  }



function previewPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }


    let _token = $('#_token').val()
    let dataPrint = []

    for (let i = 0; i < listOutstanding.length; i++) {

      if (document.getElementById(`addChecklist${i}`).checked) {

        // row_data[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
        dataPrint.push(listOutstanding[i])
      }
    }

    let nobukti1 = $("#input_add_nobukti").val()
    let nourut = $("#input_add_nourut").val()
    let tanggal = $("#input_add_tanggal").val()
    console.log("INI" , {
    NOBUKTI: nobukti1,
    tempData: dataPrint, tanggal,
    nourut})
    // $.ajax({
    //   url: "{!! url('cetaktandaterimaspcetak') !!}",
    //   type: "post",
    //   async: false,
    //   data: {
    //     _token : _token,
    //     NOBUKTI: nobukti1,
    //     tempData: dataPrint,
    //     nourut,
    //     tanggal,
    //     ispreview :1
    //   },
    //   success: function(res) {
    //     console.log(res)
       
    //   }
    // })

    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=8) {
      let tempArray = dataPrint.slice(i,i+8)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0].split('-').reverse().join('/');
    let tglprint =tanggal.split(' ')[0].split('-').reverse().join('/');
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
        height: 18px;
        padding: 0px 4px;
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
        height: 14cm;
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
                  <div class="pb-1" style="width: 100%; margin-top: 15px;">Kepada YTH : ` + dataPrint[0].KodeCustSupp + ` - ` + dataPrint[0].namakebun + `</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">`+`${dataPrint[0].NamaCustSupp ?? ''}${dataPrint[0].NamaCustSupp && dataPrint[0].Alamat ? ' - ' : ''}${dataPrint[0].Alamat ?? ''}` + `</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 50%; margin-left: 20px;">
                <div style="display: flex; width: 100%; margin-top: 15px;">
                  <h2 class="m-0 pb-2">TANDA TERIMA INVOICE & FAKTUR PAJAK</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No. TT</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+nobukti1+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Tanggal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+tglprint+`</div>
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
        <table style="width:98%; border-collapse:collapse; font-family:sans-serif; font-size:10px;">
            <thead>
              </tr>
                  <tr>
                  <td rowspan="2" class="text-center" style="width: 1%; border-left:none;  border-right:none;">No.</td>
                    <td rowspan="2" class="text-center" style="width: 20%; border-left:none; border-right:none;">NO INVOICE</td>
                    <td rowspan="2" class="text-center" style="width: 20%; border-left:none; border-right:none;">TGL INVOICE</td>
                    <td rowspan="2" class="text-center" style="width: 20%; border-left:none; border-right:none;">NO FAKTUR PAJAK</td>
                    <td rowspan="2" class="text-right" style="width: 20%; border-left:none; border-right:none;">NILAI INVOICE</td>
                    <td rowspan="2" class="text-center" style="width: 20%; border-left:none; border-right:none;">NO PO CUST</td>
                  </tr>
                </thead> `;

    let z = 0
    let maxRow = 8;
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalJumlah = 0;

    dataPrint.forEach(item => {

      if (item.NNetRp) {
        grandTotalJumlah += Number(item.NNetRp) || 0;
      }

    });
    // end
    tempPrintStr += `<html>
    <head>
      <title></title>
    </head>

    <body>
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
        item.forEach((itemSub, j) => {
        tempPrintStr += ``

         tempPrintStr += `
         <tr>
         <td class="text-center"
               style="width: 1%; border-bottom: none; border-top: none;  border-left: none;  border-right: none;">${z+1}</td>
         <td class="text-align: center"
               style="width: 20%; text-align: center; border-bottom: none; border-top: none; border-left: none;  border-right: none;">${itemSub.NoBukti ?? ''}</td>
         <td class="text-align: center"
               style="width: 20%; text-align: center; border-bottom: none; border-top: none; border-left: none;  border-right: none;">${itemSub.Tanggal ? itemSub.Tanggal.split(' ')[0].split('-').reverse().join('/') : ''}</td>
         <td class="text-center"
               style="width: 20%; border-bottom: none; border-top: none; border-left: none;  border-right: none;">${itemSub.nopajak ?? ''}</td>
         <td class="text-right"
               style="width: 20%; border-bottom: none; border-top: none; border-left: none;  border-right: none;">${itemSub.NNetRp
              ? Number(itemSub.NNetRp).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                })
              : ''}</td>
         <td style="width: 20%; text-align: center; border-bottom: none; border-top: none; border-left: none;  border-right: none;">
              ${itemSub.pono ?? ''}
         </td>
         </tr>`;

           z++;

        });


        let sisaRow = maxRow - item.length;

        for (let k = 0; k < sisaRow; k++) {
          tempPrintStr += `
          <tr>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;">&nbsp;</td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
          </tr>`;
        }

        console.log(i , arrayDataPrint.length)
         if (i === arrayDataPrint.length - 1)  {
          console.log('masok')
          tempPrintStr += `
          <tr>
          <td colspan="3" style="border:1px solid; padding:5px; font-weight:bold; border-bottom: none; border-left: none;  border-right: none;">
            </td>
            <td style="border:1px solid; text-align:right; font-weight:bold; border-bottom: none; border-left: none;  border-right: none;">
            </td>
            <td style="border:1px solid; text-align:right; font-weight:bold; border-bottom: none; border-left: none;  border-right: none;">
              Total :
              ${grandTotalJumlah.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </td>
            <td  style="border:1px solid; border-bottom: none; border-left: none;  border-right: none;"></td>
          </tr>`
         }


         tempPrintStr += `</tbody>`;

         tempPrintStr += `</table>


         <div class="footer-sign font-family: sans-serif;
           font-size: 10px ">

         <div class="row mt-3" style="text-align: left;font-family: sans-serif;
         font-size: 12px ">
         <span style="float: left; display: block; clear: left;">
         </span>


         <div style="width:100%; display:flex; font-weight:bold; margin-top:5px;">

          </div>

         </div>


         <div style="display:flex; justify-content:space-between; width:100%; font-family:sans-serif; font-size:10px;">


          <div style="width:50%;">
          <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -120px; font-family: sans-serif;
             font-size: 10px">
             <tr>
               <td class="no-border text-center" style="width: 20%">Diterima Oleh</td>
               <td class="no-border text-center" style="width: 20%">Diserahkan Oleh</td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               <p class="m-0">Tanggal</p>
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               <p class="m-0">Tanggal</p>
               </td>
             </tr>
           </table>
            <div style="margin-top:10px; font-size:10px; font-style:italic; white-space:nowrap;">
                *Setelah ditandatangani mohon dikirim kembali ke SML atau di Fax ke NO : 0541-4104195, atau email ke : sml.accounting@yahoo.co.id
            </div>

          </div>


          <div style="width:50%;">
          <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 20px; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%"></td>
               <td class="no-border text-center" style="width: 20%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               </td>
             </tr>
           </table>
          </div>
        </div>

         </div>

 
         <div class="footer-print-date">
           <table class="m-0" style="width: 100% ; font-family: sans-serif;
           font-size: 10px; margin-left: -25px;">
             <tr>
               <td class="no-border"></td>
               <td class="no-border text-right">Page ${i+1} of ${arrayDataPrint.length}</td>
             </tr>
           </table>

         </div>`


        tempPrintStr += `</div>`
      });


      tempPrintStr +=  `</body></html>`



    let w = window.open('', '_blank');

    w.document.open();
    w.document.write(tempPrintStr);
    w.document.close();

    $(".mainpage").hide()
    $("#page1").show()
  }



//=====











  function submitPrintUlang (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }


    let _token = $('#_token').val()
    // let dataPrint = []

    // for (let i = 0; i < listOutstanding.length; i++) {

    //   if (document.getElementById(`addChecklist${i}`).checked) {

    //     // row_data[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
    //     dataPrint.push(listOutstanding[i])
    //   }
    // }
    let tanggal = $("#input_add_tanggal").val()
     let tglprint =tanggal.split(' ')[0].split('-').reverse().join('/');
    let nobukti1 = nobukti;
    // let nourut = $("#input_add_nourut").val()
    // let tanggal = $("#input_add_tanggal").val()

    // console.log("INI" , {
    // NOBUKTI: nobukti1,
    // tempData: dataPrint, tanggal,
    // nourut})

    $.ajax({
      url: "{!! url('cetaktandaterimadetailCetakUlang') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti,
        // tempData: dataPrint,
        // nourut,
        // tanggal
      },
      success: function(res) {
        console.log(res)
        // loadAll()
        dataPrint = res
        console.log(res[0])

        // console.log(res[0][0].IsOtorisasi1)

      }
    })

    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=8) {
      let tempArray = dataPrint.slice(i,i+8)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0].split('-').reverse().join('/');

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
        height: 18px;
        padding: 0px 4px;
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
        height: 14cm;
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
                  <div class="pb-1" style="width: 100%; margin-top: 15px;">Kepada YTH : ` + dataPrint[0].KodeCustSupp + ` - ` + dataPrint[0].namakebun + `</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">`+`${dataPrint[0].NamaCustSupp ?? ''}${dataPrint[0].NamaCustSupp && dataPrint[0].Alamat ? ' - ' : ''}${dataPrint[0].Alamat ?? ''}` + `</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 50%; margin-left: 20px;">
                <div style="display: flex; width: 100%; margin-top: 15px;">
                  <h2 class="m-0 pb-2">TANDA TERIMA INVOICE & FAKTUR PAJAK</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No. TT</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+nobukti1+`</div>
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
        <table style="width:98%; border-collapse:collapse; font-family:sans-serif; font-size:10px;">
            <thead>
              </tr>
                  <tr>
                  <td rowspan="2" class="text-center" style="width: 1%; border-left:none;  border-right:none;">No.</td>
                    <td rowspan="2" class="text-center" style="width: 20%; border-left:none; border-right:none;">NO INVOICE</td>
                    <td rowspan="2" class="text-center" style="width: 20%; border-left:none; border-right:none;">TGL INVOICE</td>
                    <td rowspan="2" class="text-center" style="width: 20%; border-left:none; border-right:none;">NO FAKTUR PAJAK</td>
                    <td rowspan="2" class="text-right" style="width: 20%; border-left:none; border-right:none;">NILAI INVOICE</td>
                    <td rowspan="2" class="text-center" style="width: 20%; border-left:none; border-right:none;">NO PO CUST</td>
                  </tr>
                </thead> `;

    let z = 0
    let maxRow = 8;
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalJumlah = 0;

    dataPrint.forEach(item => {

      if (item.NNetRp) {
        grandTotalJumlah += Number(item.NNetRp) || 0;
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
        item.forEach((itemSub, j) => {
          tempPrintStr += ``



         tempPrintStr += `
         <tr>
         <td class="text-center"
               style="width: 1%; border-bottom: none; border-top: none;  border-left: none;  border-right: none;">${z+1}</td>
         <td class="text-align: center"
               style="width: 20%; text-align: center; border-bottom: none; border-top: none; border-left: none;  border-right: none;">${itemSub.NoBukti ?? ''}</td>
         <td class="text-align: center"
               style="width: 20%; text-align: center; border-bottom: none; border-top: none; border-left: none;  border-right: none;">${itemSub.Tanggal ? itemSub.Tanggal.split(' ')[0].split('-').reverse().join('/') : ''}</td>
         <td class="text-center"
               style="width: 20%; border-bottom: none; border-top: none; border-left: none;  border-right: none;">${itemSub.nopajak ?? ''}</td>
         <td class="text-right"
               style="width: 20%; border-bottom: none; border-top: none; border-left: none;  border-right: none;">${itemSub.NNetRp
              ? Number(itemSub.NNetRp).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                })
              : ''}</td>
         <td style="width: 20%; text-align: center; border-bottom: none; border-top: none; border-left: none;  border-right: none;">
              ${itemSub.pono ?? ''}
         </td>
         </tr>`;

           z++;

        });


        let sisaRow = maxRow - item.length;

        for (let k = 0; k < sisaRow; k++) {
          tempPrintStr += `
          <tr>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;">&nbsp;</td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
            <td style="border-top:none; border-bottom:none; border-left: none;  border-right: none;"></td>
          </tr>`;
        }

        console.log(i , arrayDataPrint.length)
         if (i === arrayDataPrint.length - 1)  {
          console.log('masok')
          tempPrintStr += `
          <tr>
            <td colspan="3" style="border:1px solid; padding:5px; font-weight:bold; border-bottom: none; border-left: none;  border-right: none;">
            </td>
            <td style="border:1px solid; text-align:right; font-weight:bold; border-bottom: none; border-left: none;  border-right: none;">
            </td>
            <td style="border:1px solid; text-align:right; font-weight:bold; border-bottom: none; border-left: none;  border-right: none;">
              Total :
              ${grandTotalJumlah.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </td>
            <td  style="border:1px solid; border-bottom: none; border-left: none;  border-right: none;"></td>
          </tr>`
         }


         tempPrintStr += `</tbody>`;

         tempPrintStr += `</table>


         <div class="footer-sign font-family: sans-serif;
           font-size: 10px ">

         <div class="row mt-3" style="text-align: left;font-family: sans-serif;
         font-size: 12px ">
         <span style="float: left; display: block; clear: left;">
         </span>


         <div style="width:100%; display:flex; font-weight:bold; margin-top:5px;">

          </div>

         </div>


         <div style="display:flex; justify-content:space-between; width:100%; font-family:sans-serif; font-size:10px;">


          <div style="width:50%;">
          <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top:-120px; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%">Diterima Oleh</td>
               <td class="no-border text-center" style="width: 20%">Diserahkan Oleh</td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               <p class="m-0">Tanggal</p>
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               <p class="m-0">Tanggal</p>
               </td>
             </tr>
           </table>
           <div style="margin-top:10px; font-size:10px; font-style:italic; white-space:nowrap;">
                *Setelah ditandatangani mohon dikirim kembali ke SML atau di Fax ke NO : 0541-4104195, atau email ke : sml.accounting@yahoo.co.id
           </div>
          </div>


          <div style="width:50%;">
          <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 20px; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%"></td>
               <td class="no-border text-center" style="width: 20%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               </td>
             </tr>
           </table>
          </div>
        </div>

         </div>


         <div class="footer-print-date">
           <table class="m-0" style="width: 100% ; font-family: sans-serif;
           font-size: 10px; margin-left: -25px;">
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

    $(".mainpage").hide()
    $("#page1").show()
  }

function buttonBatalOtorisasi (nobukti) {

  console.log(nobukti)



  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }





  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi DPP ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('pelunasanpiutangdppspbatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti

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


function formatAngkaX (angka) {
  if (!angka) {
    return '0.00'
  } else {
    return formatAngka(parseFloat(angka).toFixed(2))
  }

}

function formatAngka (angkaString) {

  let tempAngka = angkaString.split('.')

  if (tempAngka[0][0] == '-') {
    let temp2=''

    let tempAngka1 = tempAngka[0].split('-')
    for (let i = 0; i < tempAngka1[1].length; i++) {
      if (i != 0 && i % 3 == 0) {
        temp2 = ',' + temp2
      }
      temp2 = tempAngka1[1][tempAngka1[1].length - i -1] + temp2
      // console.log(i, temp2)
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
    // console.log(i, temp1)
  }
  temp1 += '.' + tempAngka[1]
  return temp1
}


</script>

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




@endsection
