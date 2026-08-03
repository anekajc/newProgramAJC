@extends('newmaster')
@section('buttons')

@endsection

@section('css')
<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>


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
<div class="container-fluid" >


  <!-- <div id="qrcode"></div> -->
  <div class="row" style="margin-top: -30px">
    <div class="col-6 text-left">
      <h2>Pelunasan Hutang</h2>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600;  " onclick="buttonAdd()"  >+ Tambah DPH</button>
    </div>
  </div>
</div>

<!-- <button onclick="loadAll()">tes</button> -->
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
  <input type="hidden" id="akses_pembatalan" value="{!! $akses->pembatalan !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card">
<div class="card-header">
<div class="row">
  <nav style="width: 100%;">
    <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
      <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true" style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; text-align: left;">DPH</a>
    </div>
  </nav>
</div>
</div>
<div class="card-body" style="padding:0;">
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    <div class="row">
      <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
        <div class="container-fluid">

              <table id="tabel" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;"  scope="col">Actions</th>
                    <th style="padding: 4px 12px;"  scope="col">No. Bukti</th>
                    <th style="padding: 4px 12px;"  scope="col">Supplier</th>
                    <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                    <th style="padding: 4px 12px;"  scope="col">Valas</th>
                    <th style="padding: 4px 12px;"  scope="col">Nilai</th>
                    <th style="padding: 4px 12px;"  scope="col">K/L</th>
                    <th style="padding: 4px 12px;"  scope="col">Otorisasi</th>
                    <th style="padding: 4px 12px;"  scope="col">User Oto</th>
                    <th style="padding: 4px 12px;"  scope="col">Tgl Oto</th>
                    <th style="padding: 4px 12px;"  scope="col">User Btl</th>
                    <th style="padding: 4px 12px;"  scope="col">Tgl Btl</th>
                    
                  </tr>
                </thead>


                <tbody id="tabel_data" class="text-left" >
                  @for ($i = 0; $i < count($tempOutstanding); $i++)
                <tr>
                  <td class='text-center'>
                      <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('{{ $tempOutstanding[$i][0]->NoBukti }}' , 'detail')">
                          <i class="bi bi-info"></i>
                      </button>

                      @if ($tempOutstanding[$i][0]->IsOtorisasi1 == 1)
                          <!-- SUDAH OTORISASI -->
                          <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempOutstanding[$i][0]->NoBukti }}' , 'edit')">
                              <i class="bi bi-key"></i>
                          </button>

                          <button class="btn btn-primary btn-sm" type="button" onclick="submitPrint('{{ $tempOutstanding[$i][0]->NoBukti }}')">
                              <i class="bi bi-printer"></i>
                          </button>
                      @else
                          <!-- BELUM OTORISASI -->
                          <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $tempOutstanding[$i][0]->NoBukti }}' , 'edit')">
                              <i class="bi bi-pen"></i>
                          </button>

                          <button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('{{ $tempOutstanding[$i][0]->NoBukti }}' , 'otorisasi')">
                              <i class="bi bi-key"></i>
                          </button>
                      @endif
                  </td>
                  <td>{{ $tempOutstanding[$i][0]->NoBukti }}</td>
                  <td>{{ $tempOutstanding[$i][0]->NAMACUSTSUPP }}</td>
                  <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i][0]->Tanggal)) !!}</td>

                  <td>{{ $tempOutstanding[$i][0]->Valas }}</td>
                  <td class="text-right">{{ number_format($tempOutstanding[$i][0]->DIBAYAR , 2 ,'.' , ',') }}</td>
                  <td class="text-right">{{ number_format($tempOutstanding[$i][0]->KL , 2 ,'.' , ',') }}</td>



                  @if ($tempOutstanding[$i][0]->IsOtorisasi1)
                            <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                          @else
                          <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                          @endif
                  <td>{{ $tempOutstanding[$i][0]->OtoUser1 }}</td>
                  <td>{!! $tempOutstanding[$i][0]->TglOto1 ? date("Y/m/d", strtotime($tempOutstanding[$i][0]->TglOto1)) : '' !!}</td>
                  <td>{{ $tempOutstanding[$i][0]->Userbatal }}</td>
                  <td>{!! $tempOutstanding[$i][0]->TglBatal ? date("Y/m/d", strtotime($tempOutstanding[$i][0]->TglBatal)) : '' !!}</td>




                  
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
      <h2>Pengajuan DPH</h2>
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
      <input type="hidden" name="noUrut" id="input_add_nourut" value="" />

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

      <div class="row" style="margin-top: -10px">
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>Valas</label>
            </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-8">
              <div class="input-group form-group">
                <input id="input_add_valas" type="text" class="form-control" disabled>

                <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" disabled >+</button>

              </div>
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
              <th style="padding: 4px 12px;" scope="col">Supplier</th>
              <th style="padding: 4px 12px;" scope="col">Faktur</th>
              <th style="padding: 4px 12px;" scope="col">diBayar</th>
              <th style="padding: 4px 12px;" scope="col">kurangBayar</th>
              <th style="padding: 4px 12px;" scope="col">lebihBayar</th>
              <th style="padding: 4px 12px;" scope="col">No.Invoice</th>
              <th style="padding: 4px 12px;" scope="col">Tgl Invoice</th>


              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="addTableData" class="" >
            <tr >

                <td colspan=8 class="text-center">Belum ada data</td>

          </tr>

          </tbody>


        </table>
  </div>


  <div class="col-md-12 mt-2 text-right">
  <button id="buttonAddItem" type="button" class="btn btn-primary" onclick="buttonAddItem()" class="btn btn-secondary" style="height: 30px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;" >+ Tambah</button>
</div>


  <div id="formAddAdd" class="container-fluid showhideitem">
    <!-- <div class="line"></div> -->
    <!-- <div class="row"> -->

    <div class="col-12">


    <hr/>
    <div class="row">
      <div class="col-md-12">
        <h4 id="labelAddAddItem">Add Item</h4>
        <h4 id="labelAddEditItem">Edit Item</h4>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="row">






          <div class="col-md-2">
            <div class="form-group">
            <label>Devisi</label>
          </div>
          </div>
          <!-- <div class="col-4 text-right">

            </div> -->
          <div class="col-md-3">
            <div class="input-group form-group">
              <input id="AddAddKodeDevisi" type="text" class="form-control" disabled>

              <button id="buttonAddListDevisi" type="button" onclick="buttonAddListDevisi()" class="btn btn-primary" >+</button>

            </div>
          </div>

          <div class="col-md-3">
            <div class="input-group form-group">
              <input  id="AddAddNamaDevisi" type="text" class="form-control" disabled>

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
            <label>Valas</label>
          </div>
          </div>
          <!-- <div class="col-4 text-right">

            </div> -->
          <div class="col-md-3">
            <div class="input-group form-group">
              <input id="AddAddValas" type="text" class="form-control" value="IDR" disabled>
              <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" >+</button>

            </div>
          </div>

          <div class="col-md-1">
            <div class="form-group">
            <label>Kurs</label>
          </div>
          </div>

          <div class="col-md-2">
            <div class="input-group form-group">
              <input id="AddAddKurs" type="number"  value="1.00" class="text-right form-control" disabled>

            </div>
          </div>

        </div>
      </div>

    </div>




    <div class="row" style="margin-top: -10px">

      <div class="col-md-12">

      <div class="row">

      <div class="col-md-6">


        <div class="row">






          <div class="col-md-2">
            <div class="form-group">
            <label>Lawan</label>
          </div>
          </div>
          <!-- <div class="col-4 text-right">

            </div> -->
          <div class="col-md-3">
            <div class="input-group form-group">
              <input id="AddAddLawan" type="text" class="form-control" disabled>
              <input id="AddAddKodeLawan" type="hidden" class="form-control" disabled>
              <button id="buttonAddListLawan" type="button" onclick="buttonAddListLawan()" class="btn btn-primary" >+</button>

            </div>
          </div>

          <div class="col-md-3">
            <div class="input-group form-group">
              <input id="AddAddKeteranganLawan" type="text" class="form-control" disabled>

            </div>
          </div>

        </div>
      </div>



        <!-- <div class="col-md-3">


    <div class="row">






      <div class="col-md-4">
        <div class="form-group">
        <label>Kode Brg</label>
      </div>
      </div>
      <div class="col-md-8">
        <div class="input-group form-group">
          <input id="AddAddKodeBrg" type="text" class="form-control" disabled>
          <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button>

        </div>
      </div>

    </div>

  </div> -->

</div>
</div>

</div>

<div class="row" style="margin-top: -10px">
  <div class="col-md-6">


    <div class="row">






      <div class="col-md-2">
        <div class="form-group">
        <label>Jumlah</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-3">
        <div class="input-group form-group">
          <input id="AddAddJumlah" type="number" value="0.00" class="text-right form-control" >

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
        <label>Keterangan</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-6">
        <div class="input-group form-group">
          <input id="AddAddKeterangan" type="text" value="" class="form-control" >

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
        <label>Ket. Det</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-6">
        <div class="input-group form-group">
          <input id="AddAddKeteranganDetail" type="text" value="" class="form-control" >

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
    <label>Departemen</label>
  </div>
  </div>
  <!-- <div class="col-4 text-right">

    </div> -->
  <div class="col-md-3">
    <div class="input-group form-group">
      <input id="AddAddKodeDepartemen" type="text" class="form-control" disabled>
      <button id="buttonAddListDepartemen" type="button" onclick="buttonAddListDepartemen()" class="btn btn-primary" >+</button>

    </div>
  </div>

  <div class="col-md-3">
    <div class="input-group form-group">
      <input id="AddAddNamaDepartemen" type="text" class="form-control" disabled>

    </div>
  </div>

</div>


<div class="row" id="rowCustsupp" style="margin-top: -10px">






  <div class="col-md-2">
    <div class="form-group">
    <label>Custsupp</label>
  </div>
  </div>
  <!-- <div class="col-4 text-right">

    </div> -->
  <div class="col-md-3">
    <div class="input-group form-group">
      <input id="AddAddKodeCustsupp" type="text" class="form-control" disabled>
      <button id="buttonAddListCustsupp" type="button" onclick="buttonAddListCustsupp()" class="btn btn-primary" >+</button>

    </div>
  </div>

  <div class="col-md-3">
    <div class="input-group form-group">
      <input id="AddAddNamaCustsupp" type="text" class="form-control" disabled>

    </div>
  </div>

</div>


</div>
</div>


</div>










  <!-- <div class="col-6 ">
    <div class="row">



    </div> -->
  <!-- </div> -->




  <div class="row mt-2" style="margin-top: 0">
    <div class="col-md-12 text-right mt-4">
      <button type="button" class="btn btn-secondary" onclick="buttonAddBatal()" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Batal</button>

      <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-primary" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Submit Add</button>

      <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Submit Edit</button>


      <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
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









  </div>




  <div id="page3" style="display: none" class="mainpage container-fluid" >

    <div class="row" style="margin-top: -30px">
      <div class="col-8 text-left">
        <h2 class="page3showhide detailshowhide"> Detail Pengajuan DPH</h2>
        <h2 class="page3showhide otorisasishowhide"> Otorisasi Pengajuan DPH</h2>
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
        <!-- <input type="hidden" name="noUrut" id="input_add_nourut" value="" /> -->

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

        <div class="row" style="margin-top: -10px">
          <div class="col-md-3">
            <div class="row">
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


                </div>
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
                <th style="padding: 4px 12px;" scope="col">Supplier</th>
                <th style="padding: 4px 12px;" scope="col">Faktur</th>
                <th style="padding: 4px 12px;" scope="col">diBayar</th>
                <th style="padding: 4px 12px;" scope="col">kurangBayar</th>
                <th style="padding: 4px 12px;" scope="col">lebihBayar</th>
                <th style="padding: 4px 12px;" scope="col">No.Invoice</th>
                <th style="padding: 4px 12px;" scope="col">Tgl Invoice</th>


              </tr>
            </thead>


            <tbody id="detailTableData" class="" >
              <tr >

                  <td colspan=7 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>
    <div class="container-fluid">


    <div class="row" style="">
      <div class="col-6 text-left">
      </div>
      <div class="col-6 text-right">
        <button type="button" class="page3showhide otorisasishowhide btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600;  " onclick="submitOtorisasi()"  >Otorisasi</button>
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









    </div>





  </div>
</div>



<!--  -->

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document" style="min-width: 1400px">
    <div id="" class="modal-content ">

      <div id= "" class="">
      <div class="modal-header">

          <h5 class="modal-title" id="">DPH</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid" >
          <div class="row">


          </div>
          <div class="row showhidelistpengajuandph">
            <div class="col-md-4">
              <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                  <input type="hidden" class="form-control" id="input_modal_nourut" placeholder="" disabled>
                <input type="text" class="form-control" id="input_modal_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>
          </div>

            </div>
            <div class="col-md-4">
              <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="input_modal_tanggal" placeholder="">
              </div>
            </div>
          </div>

            </div>

          </div>

          <div class="row" style="margin-top: -10px">
            <div class="col-md-4 showhidelistpengajuandph">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>Valas</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_modal_valas" type="text" class="form-control" disabled>

                    <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" >+</button>

                  </div>
                </div>
              </div>

            </div>

            <div class="col-md-4">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>JTHTempo</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_modal_tanggaljatuhtempo" type="date" class="form-control text-center" >


                  </div>
                </div>
              </div>

            </div>


            <div class="col-md-4">
              <div class="row">
                <div class="col-md-4">
                  <div class="input-group form-group">
                  <button id="buttonRefreshListPengajuan" type="button" onclick="buttonRefreshListPengajuan()" class="btn btn-primary" >Proses</button>
                </div>
                </div>

              </div>

            </div>


          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;  max-height: 400px">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_modal" class="table table-bordered table-striped" style="overflow:auto; min-width: 1500px" >
              <thead class="text-center bg-primary text-white" style="position: sticky;
            top: 0;
            z-index: 1;">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">v</th>
                  <th style="padding: 4px 12px;" scope="col">Supplier</th>
                  <th style="padding: 4px 12px;" scope="col">JTHTempo</th>
                  <th style="padding: 4px 12px;" scope="col">Faktur</th>
                  <th style="padding: 4px 12px;" scope="col">N. Faktur</th>
                  <th style="padding: 4px 12px;" scope="col">Sdh Dibayar</th>
                  <th style="padding: 4px 12px;  " scope="col">Dibayar</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">K.Bayar</th>
                  <!-- <th style="padding: 4px 12px;" scope="col">Perkiraan</th> -->
                  <th style="padding: 4px 12px;" scope="col">No.Invoice</th>
                  <th style="padding: 4px 12px;" scope="col">TglInvoice</th>

                </tr>
              </thead>

              <tbody id="tabel_data_add_list_modal" class="text-left" >


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
        <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
      </div>
      </div>



      </div>




    </div>
  </div>

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
                    <label>No Invoice</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control " id="input_modalx_noinvoice" >
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





              </div>
              <div class="col-md-4">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Tgl Invoice</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="date" class="form-control text-center" id="input_modalx_tanggalinvoice" >

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
                    <th style="padding: 4px 12px;" scope="col">Actions</th>

                  </tr>
                </thead>


                <tbody id="tabel_data_add_list_modalx" class="text-left" >

                  <tr >

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


      <div class="modal fade" id="formXedit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                          <input type="number" class="form-control text-right" id="input_modalxedit_nilainotadibayar" disabled>
                        </div>
                      </div>

                    </div>

                  </div>

                  <div class="col-md-4">
                    <div class="row">


                    <div class="col-md-4">
                      <div class="form-group">
                        <label>No Invoice</label>
                      </div>
                    </div>
                    <div class="col-md-8">
                      <div class="form-group">
                        <input type="text" class="form-control " id="input_modalxedit_noinvoice" >
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
                      <input type="number" class="form-control text-right" id="input_modalxedit_dibayar" >
                    </div>
                  </div>
                </div>





                  </div>
                  <div class="col-md-4">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label>Tgl Invoice</label>
                        </div>
                      </div>
                      <div class="col-md-8">
                        <div class="form-group">
                          <input type="date" class="form-control text-center" id="input_modalxedit_tanggalinvoice" >

                        </div>
                      </div>
                    </div>

                  </div>


                </div>


                <div class="row mt-2" style="margin-top: 0">
                  <div class="col-md-12 text-right mt-4">

                    <button id="buttonSaveLBEdit" type="button" onclick="buttonSaveLBEdit()" class="btn btn-success" style="height: 30px;
                    border-radius: 20px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    text-transform: uppercase;">Save</button>
                    <button type="button" id="buttonAddKLEdit" class="btn btn-primary" onclick="buttonAddKLEdit()" style="height: 30px;
                    border-radius: 20px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    text-transform: uppercase;">+ KL</button>





                    <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
                  </div>

                </div>



                </div>
                <div id="formAddKLEdit" class="container-fluid showhideitemKLEdit">
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
                            <input id="input_modalxedit_kurangbayar" type="number" value="0.00" class="text-right form-control" >

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
                            <input type="text" class="form-control" id="input_modalx_perkiraankurangbayaredit" disabled>
                            <input type="text" class="form-control" id="input_modalx_namaperkiraankurangbayaredit" disabled>
                            <button id="buttonAddListPerkiraanKurangBayar" type="button" onclick="buttonAddListPerkiraanLebihBayar('kurangbayaredit')" class="btn btn-primary" >+</button>

                          </div>
                        </div>





                      </div>
                    </div>

                  </div>















              </div>



                <div class="row mt-2" style="margin-top: 0">
                  <div class="col-md-12 text-right mt-4">
                    <button type="button" class="btn btn-secondary" onclick="buttonAddBatalKLEdit()" style="height: 30px;
                    border-radius: 20px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    text-transform: uppercase;">Batal</button>

                    <button id="buttonSubmitAddKLEdit" type="button" onclick="submitAddKLEdit()" class="btn btn-primary" style="height: 30px;
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


                  <table id="tabel_add_list_modalxedit" class="table table-bordered table-striped" style="overflow:auto; " >
                    <thead class="text-center bg-primary text-white" style="position: sticky;
                  top: 0;
                  z-index: 1;">
                      <tr>
                        <th style="padding: 4px 12px;" scope="col">Kurang Bayar</th>
                        <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
                        <th style="padding: 4px 12px;" scope="col">Nama perkiraan</th>
                        <th style="padding: 4px 12px;" scope="col">Actions</th>

                      </tr>
                    </thead>


                    <tbody id="tabel_data_add_list_modalxedit" class="text-left" >

                      <tr >

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
                <!-- <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
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
let listPengajuan = []
let listCheckListPengajuan = []
let listTambahKL = []
let barangEdit = {}
let listKLEdit = []

let listPerkiraan = []
let listLawan = []
let listValas = []
let listDepartemen = []
let listDevisi = []

let listDPH = []
let listDPP = []

let listUMB = []

let tempDPPDPH = {}



let listBarang = []
let tempBarangAddAdd = {}
let tempBarangAddEdit = {}
let dataBarang = []
let tipeform = ''

$(document).ready(function(){
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
          // "searching": false,
          "columnDefs": [
          // { "type": "date", "targets": [3] },
          {  "className": "text-right", "targets": [5] },
          // "columns" : [{"width" : "20px"}]


        ]
        });

      //   $("#tabel_add_list_modal").DataTable({
      //     "lengthChange": false,
      //       "paging": false ,'order': [[1, 'asc']],
      //       "searching" : false,
      //       "columnDefs": [
      //     {"targets" :[0] , 'orderable' : false}
      //    // {  "className": "text-center", "targets": [4] },
      //  ]
      // });







        $("#tabel_add_list_custsupp").DataTable({
          "lengthChange": false,
            "paging": false ,
      });
        // const urlString = window.location.href
        // console.log(urlString)
        // const url = new URL(urlString);
        // console.log(url)
        // const searchParams = new URLSearchParams(url.search);
        // console.log(searchParams)
        //
        // const query = searchParams.get('nobukti');
        // console.log(query)


        // var xyz = jQuery.url.param("nobukti");
        // console.log(xyz)


  //   formAddListItem
});


function buttonAddListPerkiraanLebihBayar (id) {
  toId = id
  $("#formPerkiraan").modal('toggle')
}
function buttonAddPickPerkiraanLebihBayar (perkiraan , nama) {
  document.getElementById(`input_modalx_perkiraan${toId}`).value = perkiraan
  document.getElementById(`input_modalx_namaperkiraan${toId}`).value = nama
  $("#formPerkiraan").modal('toggle')

}

function buttonSaveLBEdit () {
  // let xlist = listKLEdit

  let xnilainota = $("#input_modalxinvoice_nilainotadibayar").val()
  let xtanggalinvoice = $("#input_modalxinvoice_tanggalinvoice").val()
  let xnoinvoice = $("#input_modalxinvoice_noinvoice").val()

  let xdibayar = $("#input_modalx_dibayar").val()

  let dibayar = $("#input_modalxedit_dibayar").val()
  let noinvoice = $("#input_modalxedit_noinvoice").val()
  let tanggalinvoice = $("#input_modalxedit_tanggalinvoice").val()
  let nofaktur = barangEdit.NoFaktur
  let nobukti = barangEdit.NoBukti
  let urut = barangEdit.urut
  let _token = $("#_token").val()

  let xlist = listKLEdit
  console.log(xlist)
  if (!xlist) {
    xlist = []
  }
  let xtotalKL = 0

  xlist.forEach((item, i) => {
    xtotalKL += Number(item.inputKL)


  });
  if (Number(xnilainotadibayar) < Number(xdibayar) + Number(xtotalKL)) {
    alertify.warning('KL + dibayar melebihi nilai nota ')

    return
  }


  $.ajax({
    url: "{!! url('pengajuandphtunaispupdatedphdet') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      dibayar,
      noinvoice,
      tanggalinvoice,
      nobukti,
      urut

    },
    success: function(res) {
      console.log(res)
      // $('.showhideitemKLedit').hide()

      $(".showhideitemKLEdit").hide()
      refreshDataTable(nobukti)
      alertify.success("Berhasil update DPH")

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
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
    // if (Number(xnilainotadibayar) + Number(xdibayar) < Number(kl)) {
    //   alertify.warning('KL melebihi nilai nota + dibayar')
    //
    //   return
    // }

    let xlist = listTambahKL[saveHeaderInvoice.NoFaktur]
    console.log(xlist)
    if (!xlist) {
      xlist = []
    }

    let xtotalKL = 0

    xlist.forEach((item, i) => {
      xtotalKL += Number(item.inputKL)


    });
    console.log(Number(xnilainotadibayar) , Number(xdibayar) , Number(xtotalKL))
    if (Number(xnilainotadibayar) < Number(xdibayar) + Number(xtotalKL) + Number(kl)) {
      alertify.warning('KL + dibayar melebihi nilai nota ')

      return
    }


    // let xsisa = $("#input_modalx_sisanotadibayar").val()
    // if (Number(kl) > Number(xsisa)) {
    //   alertify.warning('Melebihi sisa nota')
    //   return
    // }
    alertify.success("KL berhasil ditambah")
    let x = { ...saveHeaderInvoice }
    x.inputKL = kl


    x.inputPerkiraanKL = perkkl
    x.inputNamaPerkiraanKL = namaperkkl
    console.log(x)
    console.log(listTambahKL)
    console.log('!!!!')
    console.log(saveHeaderInvoice.NoFaktur)
    if (!listTambahKL[saveHeaderInvoice.NoFaktur]) {
      listTambahKL[saveHeaderInvoice.NoFaktur] = []
    }
    console.log(listTambahKL)
    listTambahKL[saveHeaderInvoice.NoFaktur].push(x)
    console.log(listTambahKL)
    refreshTableKL()
    $('.showhideitemKL').hide()

}


function refreshTableKLEdit (nobukti, nofaktur) {
  let _token = $("#_token").val()
  $.ajax({
      url: "{!! url('pengajuandphtunaispdetailkledit') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        nobukti,

        nofaktur,
      },
      success: function(res) {
        console.log(res)

        listKLEdit = res

        if (!res.length) {
          document.getElementById("tabel_data_add_list_modalxedit").innerHTML = `
          <tr>
            <td class="text-center" colspan=4>Belum ada data</td>
          </tr>
          `

        } else {
          rowTablex = ''
          res.forEach((item, i) => {
            // xTempTotalKL += Number(item.inputKL)
            rowTablex += `
              <tr>
                <td class="text-right">${item.NilaiK}</td>
                <td>${item.Perkiraan}</td>
                <td>${item.Keterangan}</td>
                <td class="text-center"><button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteKLEdit(${i})"><i class="bi bi-trash"></i></button></td>
              </tr>
            `

          });

          document.getElementById("tabel_data_add_list_modalxedit").innerHTML = rowTablex
          // document.getElementById(`list_proses_KL${saveHeaderIndex}`).value = parseFloat(xTempTotalKL).toFixed(2)


        }
      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

    return

  console.log(saveHeaderInvoice)
  console.log('refreshTableKL')
  console.log(saveHeaderInvoice.NoFaktur)
  console.log(listTambahKL[saveHeaderInvoice.NoFaktur])
  let xlist = listTambahKL[saveHeaderInvoice.NoFaktur]
  console.log(xlist)
  if (!xlist) {
    xlist = []
  }
  console.log(xlist)
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
          <td class="text-center"><button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteKLEdit(${i})"><i class="bi bi-trash"></i></button></td>
        </tr>
      `

    });

    document.getElementById("tabel_data_add_list_modalxedit").innerHTML = rowTablex
    document.getElementById(`list_proses_KL${saveHeaderIndex}`).value = parseFloat(xTempTotalKL).toFixed(2)


  }

}


function submitAddKLEdit () {
  let nofaktur = barangEdit.NoFaktur
  let nobukti = barangEdit.NoBukti
  let kodecustsupp = barangEdit.KodeCustSupp
  let inputKL = $('#input_modalxedit_kurangbayar').val()
  let perkiraan = $('#input_modalx_perkiraankurangbayaredit').val()
  let _token = $("#_token").val()

  if (Number(inputKL) <= 0 || !perkiraan) {

    alertify.warning("Data tidak lengkap")
    return
  }

  let xlist = listKLEdit
  console.log(xlist)
  if (!xlist) {
    xlist = []
  }
  let xtotalKL = 0
  let xnilainotadibayar = $("#input_modalxedit_nilainotadibayar").val()
  let xdibayar = $("#input_modalxedit_dibayar").val()


  xlist.forEach((item, i) => {
    xtotalKL += Number(item.inputKL)


  });
  if (Number(xnilainotadibayar) < Number(xdibayar) + Number(xtotalKL)) {
    alertify.warning('KL + dibayar melebihi nilai nota ')

    return
  }

  $.ajax({
    url: "{!! url('pengajuandphtunaispaddkledit') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nofaktur,
      nobukti,
      kodecustsupp,
      inputKL,
      perkiraan


    },
    success: function(res) {
      console.log(res)
      $('.showhideitemKLEdit').hide()
      refreshTableKLEdit(nobukti,nofaktur)
      refreshDataTable(nobukti)
      alertify.success("Berhasil menambah KL")

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })

}

function buttonDeleteKLEdit (index) {
  let nofaktur = barangEdit.NoFaktur
  let nobukti = barangEdit.NoBukti
  let xkledit = listKLEdit[index]

  let urut = xkledit.Urut
  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('pengajuandphtunaispdeletekledit') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nofaktur,
      nobukti,
      urut

    },
    success: function(res) {
      console.log(res)
      $('.showhideitemKLedit').hide()
      refreshTableKLEdit(nobukti,nofaktur)
      refreshDataTable(nobukti)
      alertify.success("Berhasil update DPH")

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })

}

function buttonDeleteKL (index) {

  listTambahKL[saveHeaderInvoice.NoFaktur].splice(index,1)

  refreshTableKL()
  $('.showhideitemKL').hide()
}



function refreshTableKL () {

  console.log(saveHeaderInvoice)
  console.log('refreshTableKL')
  console.log(saveHeaderInvoice.NoFaktur)
  console.log(listTambahKL[saveHeaderInvoice.NoFaktur])
  let xlist = listTambahKL[saveHeaderInvoice.NoFaktur]
  console.log(xlist)
  if (!xlist) {
    xlist = []
  }
  console.log(xlist)
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
          <td class="text-center"><button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteKL(${i})"><i class="bi bi-trash"></i></button></td>

        </tr>
      `

    });

    document.getElementById("tabel_data_add_list_modalx").innerHTML = rowTablex
    document.getElementById(`list_proses_KL${saveHeaderIndex}`).value = parseFloat(xTempTotalKL).toFixed(2)


  }

}


function buttonSaveLB () {
  console.log(saveHeaderInvoice)
    console.log(saveHeaderInvoice.NoFaktur)
    let xlist = listTambahKL[saveHeaderInvoice.NoFaktur]
    let check = listCheckListPengajuan.findIndex(el => el.NoFaktur === saveHeaderInvoice.NoFaktur );
    console.log(check)
    let xnilainota = $("#input_modalx_nilainotadibayar").val()
    let xtanggalinvoice = $("#input_modalx_tanggalinvoice").val()
    let xnoinvoice = $("#input_modalx_noinvoice").val()

    let xdibayar = $("#input_modalx_dibayar").val()
    // if (xnilainota < xdibayar ) {
    //   alertify.warning("Melebihi nilai nota")
    //   return
    //
    // }

    // let xlist = listTambahKL[saveHeaderInvoice.NoFaktur]
    console.log(xlist)
    if (!xlist) {
      xlist = []
    }

    let xtotalKL = 0

    xlist.forEach((item, i) => {
      xtotalKL += Number(item.inputKL)


    });

    if (Number(xnilainota) < Number(xdibayar) + Number(xtotalKL)) {
      alertify.warning('KL + dibayar melebihi nilai nota ')

      return
    }

    //pengecekan
    console.log(listCheckListPengajuan)
    console.log(listCheckListPengajuan[check])
    listCheckListPengajuan[check].diBayar = xdibayar
    listCheckListPengajuan[check].tanggalinvoice = xtanggalinvoice
    listCheckListPengajuan[check].noinvoice = xnoinvoice

    document.getElementById(`list_proses_dibayar${saveHeaderIndex}`).value = parseFloat(xdibayar).toFixed(2)
    alertify.success("Berhasil update dibayar")

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
  let kode  = 'DPH'
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
      document.getElementById("input_modal_nobukti").value = res[0].Nobukti
      document.getElementById("input_modal_nourut").value = res[0].Nourut

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


  // let date = new Date(), y = date.getFullYear(), m = date.getMonth();
  // console.log(date)
  // let firstDay = new Date(y, m, 1);
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value
  // console.log(periode_bulan)
  // let lastDay = new Date(y, Number(periode_bulan), 1);
  // console.log(lastDay)

  var lastDayOfMonth = new Date(periode_tahun, periode_bulan, 0);
  console.log(lastDayOfMonth)

  document.getElementById("input_modal_nobukti").value = ''
  document.getElementById("input_modal_nourut").value = ''
  document.getElementById("input_modal_tanggal").valueAsDate = new Date()
  document.getElementById("input_modal_valas").value = 'IDR'
  document.getElementById("input_modal_tanggaljatuhtempo").value = formatDate(lastDayOfMonth)




}


function submitAdd () {
  console.log("submitAdd")
  console.log(listCheckListPengajuan)
  let _token  = $("#_token").val()
  let choice = "I"
  let nobukti  = $("#input_modal_nobukti").val()
  let nourut  = $("#input_modal_nourut").val()
  let valas  = $("#input_modal_valas").val()
  let tipe = 'DPH'
  let checkDate = new Date($("#input_modal_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {
    console.log(checkDate.getFullYear())
    console.log(Number(periode_tahun))
    console.log((checkDate.getMonth() +1))
    console.log(Number(periode_bulan))
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }


  let tanggal  = $("#input_modal_tanggal").val()

  if(!listCheckListPengajuan.length) {
    alertify.warning("Tidak ada item dipilih")

  }
  let xlisttambahkl = []

  listCheckListPengajuan.forEach((item, i) => {
    if (listTambahKL[item.NoFaktur]) {
      let tempkl = listTambahKL[item.NoFaktur]
      tempkl.forEach((item, i) => {

          xlisttambahkl.push(item)
      });


    }
  });

  for (let i = 0; i < listCheckListPengajuan.length; i++) {
    let num = 0
    // console.log(num)
    if (Number(listCheckListPengajuan[i].JmlDibayar) > 0) {

      num = listCheckListPengajuan[i].JmlDibayar
      // console.log(num , 'b')
    } else {
      num = Number(listCheckListPengajuan[i].Kredit) - Number(listCheckListPengajuan[i].JmlDibayar)
      // console.log(num , 'a')
    }
    listCheckListPengajuan[i].tempKL = num
  }
  let jmlrecord = tipeform == "add" ? 0 : 1

  console.log({
    tempData : listCheckListPengajuan ,
    choice,
    valas,
    nobukti,
    tempDataKL : xlisttambahkl ,
    nourut,
    tipe,
    tanggal
  })

  // listCheckListPengajuan

  $.ajax({
      url: "{!! url('pengajuandphtunaispadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        tempData : listCheckListPengajuan ,
        choice,
        valas,

        tempDataKL : xlisttambahkl ,
        nobukti,
        nourut,
        tipe,
        tanggal,
        jmlrecord
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('DPH telah ditambah');

          // $('.showhideitem').hide();
          loadAll()
          // buttonCloseForm()
          tipeform = 'edit'
          // document.getElementById("buttonAddListCustomer").disabled = true
          // document.getElementById("input_add_tanggal").disabled = true

          // refreshDataTable(nobukti)

          $("#form").modal('toggle')
          buttonKoreksi(nobukti)

        }
        if (res == 4) {
          // setNewNoBukti()
          alertify.warning('Nobukti dan nofaktur sudah ada');
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
  let transaksi  = $("#input_add_transaksi").val()
  let note  = $("#input_add_kepadaterima").val()
  let kodeperkiraan  = $("#input_add_kodeperkiraan").val()
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

function submitAddEdit () {



  let checkDate = new Date($("#input_add_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  let choice = "U"
  let _token  = $("#_token").val()
  let nobukti  = $("#input_add_nobukti").val()
  let nourut  = $("#input_add_nourut").val()
  let transaksi  = $("#input_add_transaksi").val()
  let note  = $("#input_add_kepadaterima").val()
  let kodeperkiraan  = $("#input_add_kodeperkiraan").val()
  let tanggal = $("#input_add_tanggal").val()

  let lampiran = 0
  let keterangan2 = ''


  let kodedevisi  = $("#AddAddKodeDevisi").val()
  let valas  = $("#AddAddValas").val()
  let kurs  = $("#AddAddKurs").val()
  let lawan  = $("#AddAddLawan").val()
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


  let urut = tempBarangAddEdit.Urut

  let custsuppP = tempBarangAddEdit.CustSuppP
  let custsuppL = tempBarangAddEdit.CustSuppL
  let noaktivaP = tempBarangAddEdit.NoAktivaP
  let noaktivaL = tempBarangAddEdit.NoAktivaL
  let statusaktivaP = tempBarangAddEdit.StatusAktivaP
  let statusaktivaL = tempBarangAddEdit.StatusAktivaL

  let nobon = $("#input_add_bon").val()
  let kodebag = '-'

  let kodeP = tempBarangAddEdit.KodeP
  let kodeL = tempBarangAddEdit.KodeL
  let statusgiro = tempBarangAddEdit.StatusGiro
  let simbol = $("#input_add_simbol").val()
  let flagsimbol = ''
  let kodecost = ''
  let kodesubcost = ''
  let nodph = tempBarangAddEdit.NODPH
  let urutdph = tempBarangAddEdit.urutDPH
  let dppdph = ''
  let tp = ''
  let ppklx = ''
  let nofaktur = ''
  let plok = 0
  let nobons = ''
  let jmlrecord = tipeform == 'add' ? 0 : 1
  let notitipan = tempBarangAddEdit.notitipan
  let uruttitipan = tempBarangAddEdit.URUTTITIPAN
  let pSKB = 0
  let perkiraanx = transaksi == 'BBK' ? lawan : kodeperkiraan
  let lawanx = transaksi == 'BBK' ? kodeperkiraan : lawan







  console.log({
    tipeform,
    _token,
    nobukti ,
    nourut,
    transaksi,
    note,
    kodeperkiraan ,
    tanggal,
    lampiran ,
    keterangan2 ,
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
    pSKB
  })



  $.ajax({
      url: "{!! url('bankspadd') !!}",
      type: "post",
      async: false,
      data: {
        choice,
        tipeform,
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
        pSKB
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Bank telah ditambah');
          loadAll()
          // buttonCloseForm()
          tipeform = 'edit'
          // document.getElementById("buttonAddListCustomer").disabled = true
          // document.getElementById("input_add_tanggal").disabled = true
          $('.showhideitem').hide();
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




function buttonAddEdit (index) {


    barangEdit = listData[index]
    console.log(barangEdit)

    if (barangEdit.NoFaktur.match('IVRJ')) {
      console.log('z')
      alertify.warning("IVRJ tidak bisa diedit")

      return

    }

    if (barangEdit.NoFaktur.match('RPB')) {
      console.log('z')
      alertify.warning("RPB tidak bisa diedit")

      return

    }
    document.getElementById("input_modalxedit_nilainotadibayar").value = parseFloat(Number(barangEdit.NilaiNota)).toFixed(2)
    document.getElementById("input_modalxedit_dibayar").value = parseFloat(Number(barangEdit.dibayar)).toFixed(2)

    // document.getElementById("input_modalxedit_dibayar").value = parseFloat(Number(barangEdit.dibayar)).toFixed(2)

    document.getElementById("input_modalxedit_noinvoice").value = barangEdit.Noinvoice ? barangEdit.Noinvoice : ''
    console.log(barangEdit.TglInv)

    console.log(new Date(barangEdit.TglInv))
    document.getElementById("input_modalxedit_tanggalinvoice").value = formatDate(barangEdit.TglInv , '-')
    refreshTableKLEdit(barangEdit.NoBukti, barangEdit.NoFaktur)

    $('.showhideitemKLEdit').hide()
      $("#formXedit").modal('toggle')





}


function buttonAddDelete (index) {


    let barangDelete = listData[index]



    console.log(barangDelete)

    // return


    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus faktur '+ barangDelete.NoFaktur +' ?',
        function() {
          let _token = $("#_token").val()
          let choice = "D"


          let nobukti = barangDelete.NoBukti
          let nourut = ''
          let valas = ''
          let urut = barangDelete.urut
          let kodecustsupp = barangDelete.KodeCustSupp
          let tanggal = ''
          let tipe = ''
          let nofaktur = ''
          let dibayar = 0
          let perkiraan = ''
          let kl = 0
          let lb = 0
          let noinvoice = ''
          let tglinvoice = ''
          let pcopy = 0



          $.ajax({
              url: "{!! url('pengajuandphtunaispkoreksi') !!}",
              type: "post",
              async: false,
              data: {
                _token,
                choice,
                valas,
                nobukti,
                nourut,
                tipe,
                tanggal,
                urut,
                kodecustsupp,
                nofaktur,
                dibayar,
                perkiraan,
                kl,
                lb,
                noinvoice,
                tglinvoice,
                pcopy
              },
              success: function(res) {
                console.log(res ,'!')

                if (res == 1) {
                  // $("#form").modal('toggle')
                  alertify.success('DPH telah dihapus');

                  // $('.showhideitem').hide();
                  loadAll()
                  // buttonCloseForm()
                  tipeform = 'edit'

                  refreshDataTable(nobukti)

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
      alertify.warning("Qnt <= 0");
      return
    }

    $.ajax({
        url: "{!! url('kreditnotespadd') !!}",
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
            alertify.success('KN telah ditambah');
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

function buttonAddListLawan () {
  listLawan = []

  console.log('buttonAddListLawan')


  let _token = $("#_token").val();
  let perkiraan = $("#input_add_kodeperkiraan").val();
  let transaksi = $("#input_add_transaksi").val();
  if(!perkiraan) {
    alertify.warning("Pilih perkiraan terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('banklistlawan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      perkiraan,
      transaksi
    },
    success: function(res) {
      console.log(res)
      listLawan  = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.Perkiraan}</td>
        <td>${item.Keterangan}</td>
        <td>${item.Simbol}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickLawan(${i},'${item.Perkiraan}' , '${item.Keterangan}' , '${item.Simbol}', '${item.Kode}' )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_lawan").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListLawan').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Perkiraan tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function modalDPP (dataLawan) {
  listDPP = []

  console.log('modalDPP')

  console.log(dataLawan)


  let _token = $("#_token").val();
  let valas = $("#AddAddValas").val();

  $.ajax({
    url: "{!! url('banklistdpp') !!}",
    type: "get",
    async: false,
    data: {
      _token,
      valas
    },
    success: function(res) {
      console.log(res)
      listDPP = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.Nobukti}</td>
        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td class="text-right">${item.DIBAYAR ? formatAngka(parseFloat(item.DIBAYAR).toFixed(2)) : '0.00'}</td>
        <td class="text-right">${item.KL ? formatAngka(parseFloat(item.KL).toFixed(2)) : '0.00'}</td>
        <td class="text-right">${item.LB ? formatAngka(parseFloat(item.LB).toFixed(2)) : '0.00'}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickDPP(${i} , '${dataLawan.Perkiraan}', '${dataLawan.Kode}' , '${dataLawan.Keterangan}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dpp").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListDPP').show();
        // $("#form").modal('toggle')
      } else {
        alertify.warning("DPP tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function modalDPHUHTBBM (dataLawan) {


  console.log('modalDPHUHTBBM')

  console.log(dataLawan)


  let _token = $("#_token").val();
  let valas = $("#AddAddValas").val();

  $.ajax({
    url: "{!! url('banklistcustsuppumb') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KODESUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickCustDPHUHTBBM( '${item.KODESUPP}', '${item.NAMACUSTSUPP}','${dataLawan.Perkiraan}', '${dataLawan.Kode}' , '${dataLawan.Keterangan}')" type="button" ><i class="bi bi-arrow-right"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dphuhtbbm_custsupp").innerHTML = rowTable
      document.getElementById("input_dphuhtbbm_namacustsupp").value = ''
      document.getElementById("input_dphuhtbbm_kodecustsupp").value = ''

      document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = `
        <tr>
          <td colspan=10 class="text-center">Data tidak ditemukkan</td>
        </tr>
      `


      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListDPHUHTBBM').show();
        // $("#form").modal('toggle')
      } else {
        alertify.warning("C tidak ditemukkan")
      }


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


function refreshDataTableDet (nobukti) {
  console.log('refreshDataTableDet' , nobukti)
  let _token = $("#_token").val();
  listData = []
  $.ajax({
    url: "{!! url('pengajuandphtunaispdetail') !!}",
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

      // $('#formAddAdd').hide();
      if (!res.length) {
          alertify.success('Data Habis')
          // $("#form").modal('toggle')
          $('.mainpage').hide();
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
                  <td class="text-right">${item.KL ? formatAngka(parseFloat(Number(item.KL)).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.LB ? formatAngka(parseFloat(Number(item.LB)).toFixed(2)) : '0.00'}</td>



                  <td>${item.Noinvoice ? item.Noinvoice : '' }</td>
                  <td>${item.TglInv && new Date(item.TglInv).getFullYear() > 2000 ? formatDate(item.TglInv) : '' }</td>


                </tr>

              `

              // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
      });

      document.getElementById("detailTableData").innerHTML = rowTable


        document.getElementById("input_detail_nobukti").value = listData[0].NoBukti

        // document.getElementById("input_detail_transaksi").value = listData[0].NamaCustSupp
        // document.getElementById("input_detail_alamatcustomer").value = listData[0].Alamat1
        // document.getElementById("input_detail_nobukti").value = listData[0].NoBukti
        document.getElementById("input_detail_tanggal").valueAsDate = new Date(listData[0].Tanggal)
        document.getElementById("input_detail_valas").value = listData[0].Valas










    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
}




function refreshDataTable (nobukti) {
  console.log('refreshDataTable' , nobukti)
  let _token = $("#_token").val();
  listData = []
  $.ajax({
    url: "{!! url('pengajuandphtunaispdetail') !!}",
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
        // <td class="text-right">${formatAngka(parseFloat(Number(item.Nilai) - Number(item.dibayar)).toFixed(2)) }</td>
        // <td class="text-right">${formatAngka(parseFloat(item.dibayar).toFixed(2))}</td>
        // <td class="text-right">${formatAngka(parseFloat(Number(item.dibayar) - Number(item.Nilai)).toFixed(2)) }</td>
              rowTable += `
                <tr>
                  <td>${item.NamaCustSupp}</td>

                  <td>${item.NoFaktur}</td>

                  <td class="text-right">${formatAngka(parseFloat(Number(item.dibayar)).toFixed(2)) }</td>
                  <td class="text-right">${item.KL ? formatAngka(parseFloat(Number(item.KL)).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.LB ? formatAngka(parseFloat(Number(item.LB)).toFixed(2)) : '0.00'}</td>


                  <td>${item.Noinvoice ? item.Noinvoice : '' }</td>
                  <td>${item.TglInv && new Date(item.TglInv).getFullYear() > 2000 ? formatDate(item.TglInv) : '' }</td>


                  <td class="text-center">
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEdit(${i})"><i class="bi bi-pen"></i></button>
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDelete(${i})"><i class="bi bi-trash"></i></button>
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


function refreshDataTableDetail (nobukti) {
  console.log('refreshDataDetail' , nobukti)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('bankspdetail') !!}",
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
          $('#page3').hide();
          // $('#page3').hide();
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
                  <td>${item.Devisi}</td>

                  <td>${item.Perkiraan}</td>
                  <td>${item.NamaPerkiraan}</td>
                  <td>${item.Lawan }</td>
                  <td>${item.NamaLawan }</td>




                  <td>${item.TPHC}</td>
                  <td class="text-right">${item.DebetRp ?  formatAngka(parseFloat(item.DebetRp).toFixed(2)) : '0.00'}</td>
                  <td>${item.Keterangan }</td>
                  <td class="text-right">${item.JumlahGiroRp ? formatAngka(parseFloat(item.JumlahGiroRp).toFixed(2)) : '0.00'}</td>
                  <td>${item.NamaCost ? item.NamaCost : '' }</td>
                  <td>${item.NamaSubCost ? item.NamaSubCost : ''}</td>

                </tr>

              `
      });

      document.getElementById("detailTableData").innerHTML = rowTable


        document.getElementById("input_detail_transaksi").value = listData[0].TipeTransHD
        document.getElementById("input_detail_kodeperkiraan").value = listData[0].PerkiraanHd
        document.getElementById("input_detail_keteranganperkiraan").value = listData[0].NamaPerkiraanHd
        document.getElementById("input_detail_kepadaterima").value = listData[0].Note
        document.getElementById("input_detail_nobukti").value = listData[0].NoBukti

        // document.getElementById("input_detail_transaksi").value = listData[0].NamaCustSupp
        // document.getElementById("input_detail_alamatcustomer").value = listData[0].Alamat1
        // document.getElementById("input_detail_nobukti").value = listData[0].NoBukti
        document.getElementById("input_detail_tanggal").valueAsDate = new Date(listData[0].Tanggal)










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
    url: "{!! url('pengajuandphtunaispotorisasi') !!}",
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

// function buttonDetail (nobukti) {
//   document.getElementById("divOto").style.display = "none";
//
//   let _token = $("#_token").val();
//   $.ajax({
//     url: "{!! url('kreditnotespdetail') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token,
//       nobukti
//
//     },
//     success: function(res) {
//       console.log(res)
//       // listData = res
//       // console.log(res)
//       if (!res.length) {
//           alertify.success('Data tidak ditemukkan')
//           // $("#form").modal('toggle')
//           return
//       }
//       // dataTableAdd = res
//
//       let rowTable = ``
//       res.forEach((item, i) => {
//               rowTable += `
//                 <tr>
//                   <td>${item.NoInv}</td>
//                   <td>${item.Keterangan}</td>
//                   <td class="text-right">${item.Nilai ? formatAngka(parseFloat(item.Nilai).toFixed(2)) : '0.00'}</td>
//                   <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>
//
//                   <td>${item.kodeVls}</td>
//                   <td class="text-right">${item.Kurs ?  formatAngka(parseFloat(item.Kurs).toFixed(2)) : '0.00'}</td>
//                   <td class="text-right">${item.NilaiRp ? formatAngka(parseFloat(item.NilaiRp).toFixed(2)) : '0.00'}</td>
//                   <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>
//
//
//
//                 </tr>
//
//               `
//       });
//
//       document.getElementById("detailTableData").innerHTML = rowTable
//
//
//         document.getElementById("input_detail_kodecustomer").value = res[0].KodeSupp
//         document.getElementById("input_detail_namacustomer").value = res[0].NamaCustSupp
//         document.getElementById("input_detail_alamatcustomer").value = res[0].Alamat1
//         document.getElementById("input_detail_nobukti").value = res[0].NoBukti
//         document.getElementById("input_detail_tanggal").valueAsDate = new Date(res[0].tanggal)
//
//         $('#modalDetail').show();
//         $('.mainpage').hide();
//         $('#page3').show();
//
//
//
//
//
//
//
//
//     },
//     error: function (err) {
//       console.log(err)
//       alertify.warning('Terjadi kesalahan silahkan refresh browser')
//       resRefresh = 0;
//     }
//
//   })
//
// }




function buttonDetail (nobukti ) {
  console.log('buttonKoreksi' , nobukti )



  // cleanFormAdd()
  refreshDataTableDet(nobukti)
  $('.page3showhide').hide();
  $('.detailshowhide').show();
  $('.mainpage').hide();
  $('#page3').show();

}


function buttonOtorisasi (nobukti ) {
  console.log('buttonOtorisasi' , nobukti )

  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  // cleanFormAdd()
  refreshDataTableDet(nobukti)
  $('.page3showhide').hide();
  $('.otorisasishowhide').show();
  $('.mainpage').hide();
  $('#page3').show();

}




function buttonKoreksi (nobukti ) {
  console.log('buttonKoreksi' , nobukti )

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  tipeform = 'edit'
  // cleanFormAdd()
  refreshDataTable(nobukti)


    if (listData.length ) {
      if (listData[0].IsOtorisasi1 == 1) {
        alertify.warning("Data sudah diotorisasi")

      } else {
        $('#page1').hide();
        $('#page2').show();
      }
    }

  return

    // $.ajax({
    //   url: "{!! url('pengajuandphtunaispdetail') !!}",
    //   type: "post",
    //   async: false,
    //   data: {
    //     _token,
    //     tglawal,
    //     tglakhir,
    //     valas,
    //     tipe,
    //     tipelist,
    //     kodecustsupp
    //   },
    //   success: function(res) {
    //     console.log(res)
    //     dataTable = res
    //     // listPengajuan = res
    //     // listCheckListPengajuan = []
    //
    //     rowTable = ''
    //
    //     $('#tabel_add_list').DataTable().destroy();
    //     res.forEach((item, i) => {
    //       rowTable += `
    //     <tr>
    //     <td><div class="form-check text-center">
    //         <input id="pengajuanCheckList${i}" onchange="pengajuanCheckList(${i},this.id)" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
    //         </div></td>
    //     <td>${item.NamaCustSupp}</td>
    //     <td>${formatDate(item.JatuhTempo)}</td>
    //     <td>${item.NoFaktur}</td>
    //     <td class="text-right">${formatAngka(parseFloat(item.Kredit).toFixed(2))}</td>
    //     <td class="text-right">${formatAngka(parseFloat(item.JmlDibayar).toFixed(2))}</td>
    //     <td class="text-right">${formatAngka(parseFloat(item.diBayar).toFixed(2))}</td>
    //     <td class="text-right">${formatAngka(parseFloat(item.KL).toFixed(2))}</td>
    //     <td class="text-right">${formatAngka(parseFloat(item.LB).toFixed(2))}</td>
    //     <td>${ item.Perkiraan ? item.Perkiraan: ''}</td>
    //     <td>${ item.NOInvoice ? item.NOInvoice: ''}</td>
    //     <td>${ item.TglInvoice ? formatDate(item.TglInvoice) : ''}</td>
    //     </tr>`
    //     });
    //
    //
    //     document.getElementById("tabel_data_add_list_modal").innerHTML = rowTable
    //     // document.getElementById("tabel_data_add_list_modal").innerHTML = `<td colspan=12 class="text-center">Belum ada data</td>`
    //
    //     $('#page1').hide();
    //     $('#page2').show();
    //     $('#formAddAdd').show();
    //   //   $("#tabel_add_list_modal").DataTable({
    //   //     "lengthChange": false,
    //   //       "paging": false ,'order': [[1, 'asc']],
    //   //       "searching" : false,
    //   //       "columnDefs": [
    //   //     {"targets" :[0] , 'orderable' : false}
    //   //    // {  "className": "text-center", "targets": [4] },
    //   //  ]
    //   // });
    //
    //   },
    //   error: function (err) {
    //     console.log(err)
    //     alertify.warning('Terjadi kesalahan silahkan refresh browser')
    //   }
    //
    // })
    //
    //
  //
  //
  //
  // $('.mainpage').hide();
  // $('#page2').show();
}

function pengajuanCheckList ( index ,id)  {
  let data = listPengajuan[index]
  console.log(data)
  if (document.getElementById(`pengajuanCheckList${index}`).checked) {
    console.log('add baru')
    // kalo tidak ada data di adddataarray langsung add , kalo ada data cek namacustsupp

    if (!listCheckListPengajuan.length) {
      document.getElementById(`list_proses_dibayar${index}`).value = parseFloat(Number(data.Kredit) - Number(data.JmlDibayar)).toFixed(2)
      listPengajuan[index].diBayar = Number(data.Kredit) - Number(data.JmlDibayar)
      listPengajuan[index].noinvoice = data.NOInvoice
      listPengajuan[index].tanggalinvoice = formatDate(data.TglInvoice)
      listCheckListPengajuan.push(listPengajuan[index])
      console.log(listCheckListPengajuan)
      return
    }


    if (listCheckListPengajuan[0].KodeCustSupp != data.KodeCustSupp) {
      alertify.warning('Cust Supp berbeda')
      document.getElementById(`pengajuanCheckList${index}`).checked = !document.getElementById(`pengajuanCheckList${index}`).checked
      console.log(listCheckListPengajuan)
      return
    }
    document.getElementById(`list_proses_dibayar${index}`).value = parseFloat(Number(data.Kredit) - Number(data.JmlDibayar)).toFixed(2)
    listPengajuan[index].diBayar = Number(data.Kredit) - Number(data.JmlDibayar)
    listPengajuan[index].noinvoice = data.NOInvoice
    listPengajuan[index].tanggalinvoice = formatDate(data.TglInvoice)
    listCheckListPengajuan.push(listPengajuan[index])
    console.log(listCheckListPengajuan)
    // console.log(document.getElementById(`outstandingCheckList${index}`).checked)
    // console.log(!document.getElementById(`outstandingCheckList${index}`).checked)
    // document.getElementById(`outstandingCheckList${index}`).checked = !document.getElementById(`outstandingCheckList${index}`).checked
  } else {
    console.log(data)
    console.log(listCheckListPengajuan)
    let check = listCheckListPengajuan.findIndex(el => el.NoFaktur === data.NoFaktur );
    console.log('hapus')

    console.log(check)
    listCheckListPengajuan.splice(check, 1)

    document.getElementById(`list_proses_dibayar${index}`).value = '0.00'
    // document.getElementById(`list_proses_LB${index}`).value = '0.00'
    document.getElementById(`list_proses_KL${index}`).value = '0.00'
    listPengajuan[index].diBayar = 0
    console.log('---')
    console.log(listTambahKL)
    delete listTambahKL[data.NoFaktur];
  }

}

function buttonAddKL () {

  console.log(saveHeaderInvoice.NoFaktur)
  let xlist = listTambahKL[saveHeaderInvoice.NoFaktur]
  let check = listCheckListPengajuan.findIndex(el => el.NoFaktur === saveHeaderInvoice.NoFaktur );
  console.log(check)
  let xnilainota = $("#input_modalx_nilainotadibayar").val()
  let xtanggalinvoice = $("#input_modalx_tanggalinvoice").val()
  let xnoinvoice = $("#input_modalx_noinvoice").val()

  let xdibayar = $("#input_modalx_dibayar").val()
  // if (xnilainota < xdibayar ) {
  //   alertify.warning("Melebihi nilai nota")
  //   return
  //
  // }

  // let xlist = listTambahKL[saveHeaderInvoice.NoFaktur]
  console.log(xlist)
  if (!xlist) {
    xlist = []
  }

  let xtotalKL = 0

  xlist.forEach((item, i) => {
    xtotalKL += Number(item.inputKL)


  });

  if (Number(xnilainota) < Number(xdibayar) + Number(xtotalKL)) {
    alertify.warning('Save Dibayar / LB terlebih dahulu ')

    return
  }

  //pengecekan
  console.log(listCheckListPengajuan)
  console.log(listCheckListPengajuan[check])
  listCheckListPengajuan[check].diBayar = xdibayar
  listCheckListPengajuan[check].tanggalinvoice = xtanggalinvoice
  listCheckListPengajuan[check].noinvoice = xnoinvoice

  document.getElementById(`list_proses_dibayar${saveHeaderIndex}`).value = parseFloat(xdibayar).toFixed(2)
  alertify.success("Berhasil update dibayar")



  $('.showhideitemKL').show()

  document.getElementById("input_modalx_kurangbayar").value = parseFloat(Number(xnilainota) - Number(xdibayar) - Number(xtotalKL)).toFixed(2)
  document.getElementById("input_modalx_perkiraankurangbayar").value = ''
  document.getElementById("input_modalx_namaperkiraankurangbayar").value = ''
}


function buttonAddKLEdit () {

  $('.showhideitemKLEdit').show()

  document.getElementById("input_modalxedit_kurangbayar").value = '0.00'
  document.getElementById("input_modalx_perkiraankurangbayaredit").value = ''
  document.getElementById("input_modalx_namaperkiraankurangbayaredit").value = ''
}

// function updateListPengajuan

function buttonRefreshListPengajuan ( tipelist = 0 , kodecustsupp = '') {
  let tglawal = formatDate(new Date())

  let tglakhir = $("#input_modal_tanggaljatuhtempo").val();
  let valas = $("#input_modal_valas").val();


  let tipe = 'HT'


  if ( tipeform == 'edit') {
    tipelist = 1
    kodecustsupp = listData[0].KodeCustSupp
  }

  let _token = $("#_token").val();
  console.log("buttonRefreshListPengajuan")
  console.log(
    {
      tglawal,
      tglakhir,
      valas,
      tipe,
      tipelist,
      kodecustsupp
    }
  )
  $.ajax({
    url: "{!! url('pengajuandphtunaisplistpengajuan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      tglawal,
      tglakhir,
      valas,
      tipe,
      tipelist,
      kodecustsupp
    },
    success: function(res) {
      console.log(res)

      listPengajuan = res
      listCheckListPengajuan = []

      rowTable = ''

      // $('#tabel_add_list_modal').DataTable().destroy();
      res.forEach((item, i) => {
        // <input style="height:30px; width: 150px" id="list_proses_dibayar${i}" type="number" value='${parseFloat(item.diBayar).toFixed(2)}' class="form-control text-right" disabled>

        rowTable += `
      <tr>
      <td><div class="form-check text-center">
          <input id="pengajuanCheckList${i}" onchange="pengajuanCheckList(${i},this.id)" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
          </div></td>
          <td style="min-width: 250px">${item.NamaCustSupp}</td>
          <td style="min-width: 150px">${formatDate(item.JatuhTempo)}</td>
          <td>${item.NoFaktur}</td>
      <td class="text-right">${formatAngka(parseFloat(item.Kredit).toFixed(2))}</td>
      <td class="text-right">${formatAngka(parseFloat(item.JmlDibayar).toFixed(2))}</td>
      <td class="text-center">
      <div class="input-group form-group" style="min-width: 180px">
        <input style="height:30px; width: 150px" id="list_proses_dibayar${i}" type="number" value='0.00' class="form-control text-right" disabled>

        <button id="buttonChangeDibayar${i}" style="height:30px;padding: 0px; width: 25px" type="button" onclick="buttonChangeDibayar(${i})" class="btn btn-primary" >+</button>

      </div></td>

      <td class="text-center" style="text-align:center; ">
      <input style="height:30px; min-width: 150px" id="list_proses_KL${i}" type="number" value='0.00' class="form-control text-right" disabled>
      </td>

      <td>${ item.NOInvoice ? item.NOInvoice: ''}</td>
      <td>${ item.TglInvoice ? formatDate(item.TglInvoice) : ''}</td>
      </tr>`
      });
      // <td>${ item.Perkiraan ? item.Perkiraan: ''}</td>


      document.getElementById("tabel_data_add_list_modal").innerHTML = rowTable
      // document.getElementById("tabel_data_add_list_modal").innerHTML = `<td colspan=12 class="text-center">Belum ada data</td>`

    //   $("#tabel_add_list_modal").DataTable({
    //     "lengthChange": false,
    //       "paging": false ,'order': [[1, 'asc']],
    //       "searching" : false,
    //       "columnDefs": [
    //     {"targets" :[0] , 'orderable' : false} , { width: '2000px', targets: [6] }
    //    // {  "className": "text-center", "targets": [4] },
    //  ]
    // });

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}





function buttonAddBatalKL () {
  $('.showhideitemKL').hide()
}
function buttonAddBatalKLEdit () {
  $('.showhideitemKLEdit').hide()
}
function buttonChangeDibayar (index) {
  // sp_TempTerimaDPP

  let xcheck = document.getElementById(`pengajuanCheckList${index}`).checked
  if(!xcheck) {
    alertify.warning("Pilih invoice terlebih dahulu")
    return
  }


  let x = listPengajuan[index]
  console.log(x)

  if (x.NoFaktur.match('IVRJ')) {
    console.log('z')
    alertify.warning("IVRJ tidak bisa diedit")

    return

  }

  if (x.NoFaktur.match('RPB')) {
    console.log('z')
    alertify.warning("RPB tidak bisa diedit")

    return

  }
  // listPengajuan[saveHeaderIndex]
  saveHeaderInvoice = listPengajuan[index]
  saveHeaderIndex = index
  let xdibayar = $(`#list_proses_dibayar${index}`).val();
  // let xLB = $(`#list_proses_LB${index}`).val();
  // let sisa = $(`#input_modal_sisa`).val();
  console.log(saveHeaderInvoice.NoBukti)
  // console.log(listTambahKL[saveHeaderInvoice.NoBukti])

  console.log(x.TOTFAKTUR)
  console.log(formatAngka(parseFloat(x.TOTFAKTUR).toFixed(2)))
  console.log('==')
  // console.log(xdibayar, xLB , sisa)
  document.getElementById("input_modalx_nilainotadibayar").value = parseFloat(Number(x.Kredit) - Number(x.JmlDibayar)).toFixed(2)
  document.getElementById("input_modalx_dibayar").value = parseFloat(xdibayar).toFixed(2)

  document.getElementById("input_modalx_dibayar").value = parseFloat(xdibayar).toFixed(2)

  document.getElementById("input_modalx_noinvoice").value = x.noinvoice ? x.noinvoice : ''
  document.getElementById("input_modalx_tanggalinvoice").value = x.tanggalinvoice
  // document.getElementById("input_modalx_lebihbayar").value = parseFloat(xLB).toFixed(2)
  // document.getElementById("input_modalx_sisanotadibayar").value = parseFloat(sisa).toFixed(2)
  // if (xLB > 0) {
  //   document.getElementById("input_modalx_perkiraanlebihbayar").value = listTambahLB[saveHeaderInvoice.NOFAKTUR].inputPerkiraanLB
  //   document.getElementById("input_modalx_namaperkiraanlebihbayar").value = listTambahLB[saveHeaderInvoice.NOFAKTUR].inputNamaPerkiraanLB
  //
  // } else {
  //   document.getElementById("input_modalx_perkiraanlebihbayar").value = ''
  //   document.getElementById("input_modalx_namaperkiraanlebihbayar").value = ''
  //
  // }

  refreshTableKL()


  $('.showhideitemKL').hide()


  $("#formX").modal('toggle')


}

function buttonAddItem () {

  listTambahKL = []
  let kodecustsuppx = listData[0].KodeCustSupp
  let tipelist = 1

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value
  // console.log(periode_bulan)
  // let lastDay = new Date(y, Number(periode_bulan), 1);
  // console.log(lastDay)

  var lastDayOfMonth = new Date(periode_tahun, periode_bulan, 0);
  console.log(lastDayOfMonth)


  document.getElementById("input_modal_valas").value = 'IDR'
  document.getElementById("input_modal_tanggaljatuhtempo").value = formatDate(lastDayOfMonth)
  document.getElementById("input_modal_nobukti").value = $("#input_add_nobukti").val();
  document.getElementById("input_modal_tanggal").value = $("#input_add_tanggal").val();

  console.log('buttonAddItem' , tipelist, kodecustsuppx)
  $(".showhidelistpengajuandph").hide();
  buttonRefreshListPengajuan(tipelist , kodecustsuppx)


  $("#form").modal('toggle')
}

function buttonAdd (nobukti) {
  console.log('buttonAdd' , nobukti)

  let akses = $("#akses_istambah").val();
  let _token = $("#_token").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  tipeform = 'add'
  cleanModalAdd()
  setNewNoBukti()

  $(".showhidelistpengajuandph").show();
  buttonRefreshListPengajuan()

  // $('.showhideitem').hide();
  // document.getElementById("input_modal_tanggal").valueAsDate = new Date()
  // document.getElementById("input_modal_valas").value = 'IDR'
  // document.getElementById("input_modal_tanggaljatuhtempo").value = formatDate(lastDayOfMonth)




  $("#form").modal('toggle')
  return
  //
  //
  // document.getElementById("input_add_tanggal").disabled = false
  // document.getElementById("input_add_bon").disabled = false
  // document.getElementById("input_add_kepadaterima").disabled = false
  // document.getElementById("buttonAddListPerkiraan").disabled = false
  // document.getElementById("input_add_transaksi").disabled = false
  //
  //
  //
  //
  // document.getElementById("addTableData").innerHTML = `<td colspan=12 class="text-center">Belum ada data</td>`
  //
  // // unlockFormAdd()
  // $('.showhideitem').hide();
  // // $('.showhideform').hide();
  // $('#formAdd').show();
  // // $("#form").modal('toggle')
  //
  // // input_add_nobukti
  // // document.getElementById("input_add_nobukti").value = nobukti
  //
  // cleanFormAdd()
  // // setNewNoBukti()
  // document.getElementById("input_add_transaksi").value = 'BBK'
  // onChangeTransaksi()
  //
  // $('.mainpage').hide();
  // $('#page2').show();

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
  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('pengajuandphtunailoadall') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)
      let rowTable = ""





      res.tempOutstanding.forEach((item, i) => {
  console.log("====")
  console.log(item[0].IsOtorisasi1)
        rowTable += `
        <tr>

          <td class='text-center'>
            <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item[0].NoBukti}' , 'detail')">
              <i class="bi bi-info"></i>
            </button>
          
            ${
              Number(item[0].IsOtorisasi1) == 1 ? `
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${item[0].NoBukti}' , 'edit')">
                  <i class="bi bi-key"></i>
                </button>

                <button class="btn btn-primary btn-sm" type="button" onclick="submitPrint('${item[0].NoBukti}')">
                  <i class="bi bi-printer"></i>
                </button>` : `

                <button class="btn btn-success btn-sm" type="button" onclick="buttonOtorisasi('${item[0].NoBukti}' , 'edit')">
                  <i class="bi bi-pen"></i>
                </button>

                <button class="btn btn-primary btn-sm" type="button" onclick="buttonDetail('${item[0].NoBukti}' , 'otorisasi')">
                  <i class="bi bi-key"></i>
                </button>`
            }



          <td>${item[0].NoBukti}</td>
          <td>${item[0].NAMACUSTSUPP}</td>
          <td>${formatDate(item[0].Tanggal , '/') }</td>

          <td>${item[0].Valas}</td>
          <td class="text-right">${formatAngka(parseFloat(item[0].DIBAYAR).toFixed(2))}</td>
          <td class="text-right">${formatAngka(parseFloat(item[0].KL).toFixed(2))}</td>


  
          ${item[0].IsOtorisasi1 == 1 ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>' :
        '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>' }

          <td>${item[0].OtoUser1}</td>
          <td>${item[0].TglOto1 ? formatDate(item[0].TglOto1 , '/') : '' }</td>
          <td>${item[0].Userbatal ?item[0].Userbatal : '' }</td>
          <td>${item[0].TglBatal ? formatDate(item[0].TglBatal , '/') : '' }</td>

      

          </td>
        </tr>

        `







      });

      document.getElementById("tabel_data").innerHTML = rowTable
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
          // "searching": false,
          "columnDefs": [
          // { "type": "date", "targets": [3] },
          {  "className": "text-right", "targets": [5] },
          // "columns" : [{"width" : "20px"}]


        ]
        });



    }})

} 
 
function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('pengajuandphtunaidetailCetak') !!}",
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
        hdr = `<table style="width:100%; border-collapse:collapse; font-family:sans-serif; font-size:10px;">
            <thead>
             <tr>
              <td colspan="9" style="text-align:center; font-weight:bold; font-size:16px; border:none;">
                DPH
              </td>
             </tr>

              <!-- TGL DAN NOBUKTI -->
              <tr>
                <td colspan="4" style="border:1px solid;">
                  Tgl : ${tanggalOnly}
                </td>
                <td colspan="6" style="border:1px solid;">
                  No : ${dataPrint[0].NoBukti}
                </td>
              </tr>

              <!-- SUPPLIER -->
              <tr>
                <td colspan="9" style="border:1px solid;">
                  Supplier : ${dataPrint[0].Note ? dataPrint[0].Note : '-'}
                </td>
              </tr>
                  <tr>
                    <td rowspan="2" class="text-center" style="width: 1%">No.</td>
                    <td colspan="3" class="text-center" style="width: 20%">FAKTUR BELI</td>
                    <td colspan="2" class="text-center" style="width: 20%">INVOICE SUPPLIER</td>
                    <td rowspan="2" class="text-center" style="width: 25%">JUMLAH</td>
                    <td colspan="2" class="text-center" style="width: 10%">INVOICE</td>
                  </tr>
                  <tr>
                    <td class="text-center">TGL</td>
                    <td class="text-center">NO</td>
                    <td class="text-center">JTH</td>

                    <td class="text-center">TGL</td>
                    <td class="text-center">NO</td>

                    <td class="text-center">ASLI</td>
                    <td class="text-center">COPY</td>
                  </tr>
                </thead> `;

    let z = 0
    let maxRow = 8;
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalJumlah = 0;

    dataPrint.forEach(item => {

      if (item.DIBAYAR) {
        grandTotalJumlah += Number(item.DIBAYAR) || 0;
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
         <td class="text-align: center"
               style="width: 1%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 20%;  ">${itemSub.Tglbeli ? itemSub.Tglbeli.split(' ')[0] : ''}</td>
         <td class="text-align: left"
               style="width: 20%;  ">${itemSub.NOFAKTUR ?? ''}</td>
         <td class="text-align: left"
               style="width: 20%;  ">${itemSub.Tgljth ? itemSub.Tgljth.split(' ')[0] : ''}</td>
         <td class="text-align: left"
               style="width: 20%;">${itemSub.Tglinv ? itemSub.Tglinv.split(' ')[0] : ''}</td>
         <td class="text-align: left"
               style="width: 20%;">${itemSub.Noinv ?? ''}</td>
         <td style="width: 25%; text-align: right;">
            ${itemSub.DIBAYAR 
              ? Number(itemSub.DIBAYAR).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                }) 
              : ''}
          </td>
          <td style="text-align:center; font-weight:bold; font-size:14px;">
            ${itemSub.Asli == 1 ? '&#10003;' : ''}
          </td>
          <td style="text-align:center; font-weight:bold; font-size:14px;">
            ${itemSub.Copy == 1 ? '&#10003;' : ''}
          </td>
         </tr>`;

           z++;

        });

        // TAMBAHAN
        let sisaRow = maxRow - item.length;

        for (let k = 0; k < sisaRow; k++) {
          tempPrintStr += `
          <tr>
            <td style="border-top:none; border-bottom:none;">&nbsp;</td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
          </tr>`;
        }

        tempPrintStr += `
        <tr>
	  <td colspan="5" style="border:1px solid; padding:5px; font-weight:bold;">
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold;">
            Total :
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold;">
            ${grandTotalJumlah.toLocaleString('id-ID', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            })}
          </td>
	  <td colspan="3" style="border:1px solid;"></td>
        </tr>`;

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

          <!-- KIRI -->
          <div style="width:50%; font-size:15px;">
            <p class="m-0">No.Rek : ${dataPrint[0].NoAcc ?? '-'}</p>
            <p class="m-0">A/N : ${dataPrint[0].ATN ?? '-'}</p>
            <p class="m-0">Bank : ${dataPrint[0].bank ?? '-'}</p>
          </div>

          <!-- KANAN -->
          <div style="width:50%;">
            <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 20px; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%">Disetujui</td>
               <td class="no-border text-center" style="width: 20%">Diperiksa</td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
             </tr>
           </table>
          </div>
        </div>

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

function buttonBatalOtorisasi (nobukti) {

  console.log(nobukti)



  let akses = $("#akses_isbatal").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }





  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi DPH ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('pengajuandphtunaispbatalotorisasi') !!}",
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
function formatAngka (angkaString) {
  // console.log('formatAngka' , angkaString);
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




@endsection
