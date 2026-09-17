@extends('accounting.newmaster')
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
      <h2>Memorial Koreksi</h2>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600;  " onclick="buttonAdd()"  >+ Memorial/Koreksi</button>
    </div>
  </div>
<!-- <button onclick="loadAll()">tes</button> -->

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

  <div class="row">
      <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
        <div class="container-fluid">

              <table id="tabel" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;"  scope="col">No. Bukti</th>
                    <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                    <th style="padding: 4px 12px;"  scope="col">Trans</th>
                    <th style="padding: 4px 12px;"  scope="col">Perkiraan</th>
                    <th style="padding: 4px 12px;"  scope="col">Keterangan</th>
                    <th style="padding: 4px 12px;"  scope="col">Jumlah Rp</th>
                    <th style="padding: 4px 12px;"  scope="col">Oto</th>
                    <th style="padding: 4px 12px;"  scope="col">User Oto</th>
                    <th style="padding: 4px 12px;"  scope="col">Tgl Oto</th>
                    <th style="padding: 4px 12px;"  scope="col">Actions</th>
                  </tr>
                </thead>


                <tbody id="tabel_data" class="text-left" >
                  @for ($i = 0; $i < count($tempOutstanding); $i++)
                <tr>
                  <td>{{ $tempOutstanding[$i][0]->NoBukti }}</td>
                  <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i][0]->Tanggal)) !!}</td>
                  <td>{{ $tempOutstanding[$i][0]->TipeTransHd }}</td>

                  <td>{{ $tempOutstanding[$i][0]->Perkiraan }}</td>
                  <td>{{ $tempOutstanding[$i][0]->Note }}</td>
                  <td class="text-right">{{ number_format($tempOutstanding[$i][0]->TotalRp , 2 ,'.' , ',') }}</td>



                  @if ($tempOutstanding[$i][0]->IsOtorisasi1)
                            <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                          @else
                          <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                          @endif

                  <td>{{ $tempOutstanding[$i][0]->OtoUser1 }}</td>
                  <td>{!! $tempOutstanding[$i][0]->TglOto1 ? date("Y/m/d", strtotime($tempOutstanding[$i][0]->TglOto1)) : '' !!}</td>


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

                          <button class="btn btn-primary btn-sm" type="button" onclick="buttonDetail('{{ $tempOutstanding[$i][0]->NoBukti }}' , 'otorisasi')">
                              <i class="bi bi-key"></i>
                          </button>
                      @endif
                  </td>

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

<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row" style="margin-top: -30px">
    <div class="col-8 text-left">
      <h2>Memorial Koreksi</h2>
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

        <div class="col-md-2">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>Transaksi</label>
            </div>
            </div>

            <div class="col-md-8">
              <select id="input_add_transaksi" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onChange="onChangeTransaksi()">
                <option value='BMM' selected>BMM</option>
                <option value='BJK' >BJK</option>
              </select>
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


      </div>

      <div class="row" style="margin-top: -10px">

        <div class="col-md-2">
          <div class="row">


        <div class="col-md-4">
          <div class="form-group">
            <label>Tanggal</label>
          </div>
        </div>
        <div class="col-md-8">
          <div class="form-group">
            <input type="date" class="form-control text-center" id="input_add_tanggal" placeholder="" >
          </div>
        </div>
      </div>

        </div>


        <div class="col-md-6">
          <div class="row">


        <div class="col-md-2">
          <div class="form-group">
            <label>Note</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">

            <input type="text" class="form-control" id="input_add_note" placeholder="" >
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
              <th style="padding: 4px 12px;" scope="col">Devisi</th>
              <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
              <th style="padding: 4px 12px;" scope="col">Lawan</th>
              <th style="padding: 4px 12px;" scope="col">Keterangan</th>
              <th style="padding: 4px 12px;" scope="col">DebetRp</th>
              <th style="padding: 4px 12px;" scope="col">KreditRp</th>
              <th style="padding: 4px 12px;" scope="col">Debet</th>
              <th style="padding: 4px 12px;" scope="col">Kredit</th>
              <th style="padding: 4px 12px;" scope="col">Valas</th>
              <th style="padding: 4px 12px;" scope="col">Kurs</th>


              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="addTableData" class="" >
            <tr >

                <td colspan=11 class="text-center">Belum ada data</td>

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
        <h4 id="labelAddItem" class="showhideitem">Add Item</h4>
        <h4 id="labelEditItem" class="showhideitem">Edit Item</h4>
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
            <div class="col-md-4">
              <select id="AddAddKodeDevisi" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onChange="">
                <option value='' selected>Pilih Devisi</option>
                  @for ($i = 0; $i < count($devisi); $i++)
                <option value='{{ $devisi[$i]->devisi }}' >{{ $devisi[$i]->namadevisi  }}</option>

                @endfor
              </select>
            </div>
          <!-- <div class="col-md-3">
            <div class="input-group form-group">
              <input id="AddAddKodeDevisi" type="text" class="form-control" disabled>

              <button id="buttonAddListDevisi" type="button" onclick="buttonAddListDevisi()" class="btn btn-primary" >+</button>

            </div>
          </div>

          <div class="col-md-3">
            <div class="input-group form-group">
              <input  id="AddAddNamaDevisi" type="text" class="form-control" disabled>

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

  <div class="col-md-12">

  <div class="row">

  <div class="col-md-6">


    <div class="row">






      <div class="col-md-2">
        <div class="form-group">
        <label>Debet</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-3">
        <div class="input-group form-group">
          <input id="AddAddDebet" type="text" class="form-control" disabled>
          <input id="AddAddKodeDebet" type="hidden" class="form-control" disabled>
          <button id="buttonAddListDebet" type="button" onclick="buttonAddListPerkiraan('Debet')" class="btn btn-primary" >+</button>

        </div>
      </div>

      <div class="col-md-3">
        <div class="input-group form-group">
          <input id="AddAddKeteranganDebet" type="text" class="form-control" disabled>

        </div>
      </div>

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
        <label>Kredit</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-3">
        <div class="input-group form-group">
          <input id="AddAddKredit" type="text" class="form-control" disabled>
          <input id="AddAddKodeKredit" type="hidden" class="form-control" disabled>
          <button id="buttonAddListKredit" type="button" onclick="buttonAddListPerkiraan('Kredit')" class="btn btn-primary" >+</button>

        </div>
      </div>

      <div class="col-md-3">
        <div class="input-group form-group">
          <input id="AddAddKeteranganKredit" type="text" class="form-control" disabled>

        </div>
      </div>

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

      <button id="buttonSubmitAdd" type="button" onclick="submitAdd()" class="btn btn-primary showhideitem" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Submit Add</button>

      <button id="buttonSubmitEdit" type="button" onclick="submitEdit()" class="btn btn-primary showhideitem" style="height: 30px;
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
        <h2>Memorial Koreksi</h2>
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
        <input type="hidden" name="noUrut" id="input_add_nourut" value="" />

        <div class="row">

          <div class="col-md-2">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                <label>Transaksi</label>
              </div>
              </div>

              <div class="col-md-8">
                <select id="input_detail_transaksi" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                  <option value='BMM' selected>BMM</option>
                  <option value='BJK' >BJK</option>
                </select>
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
                <input type="hidden" class="form-control" id="input_detail_nourut" placeholder="" disabled>
              <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Bukti" disabled>
            </div>
          </div>
        </div>

          </div>


        </div>

        <div class="row" style="margin-top: -10px">

          <div class="col-md-2">
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


          <div class="col-md-6">
            <div class="row">


          <div class="col-md-2">
            <div class="form-group">
              <label>Note</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">

              <input type="text" class="form-control" id="input_detail_note" placeholder="" disabled>
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
                <th style="padding: 4px 12px;" scope="col">Devisi</th>
                <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
                <th style="padding: 4px 12px;" scope="col">Lawan</th>
                <th style="padding: 4px 12px;" scope="col">Keterangan</th>
                <th style="padding: 4px 12px;" scope="col">DebetRp</th>
                <th style="padding: 4px 12px;" scope="col">KreditRp</th>
                <th style="padding: 4px 12px;" scope="col">Debet</th>
                <th style="padding: 4px 12px;" scope="col">Kredit</th>
                <th style="padding: 4px 12px;" scope="col">Valas</th>
                <th style="padding: 4px 12px;" scope="col">Kurs</th>

              </tr>
            </thead>


            <tbody id="detailTableData" class="" >
              <tr >

                  <td colspan=10 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>

    <div class="col-md-12 mt-2 text-right showhidepage3 page3otorisasi">
    <button id="buttonOtorisasi" type="button" class="btn btn-primary" onclick="submitOtorisasi()" class="btn btn-secondary" style="height: 30px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;" >Otorisasi</button>
  </div>






    </div>
  </div>

      </div>









    </div>





  </div>
</div>



<!--  -->

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">
    <div id="" class="modal-content ">

      <div id= "modalAddListValas" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">Valas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Valas</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_valas" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Kurs</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_valas" class="text-left" >

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
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>

      <div id= "modalAddListPerkiraan" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Perkiraan</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_perkiraan" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_perkiraan" class="text-left" >

                <tr >

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
















      </div>







    </div>
  </div>

<!-- End modal add-->








@endsection

@section('js')
<script type="text/javascript">

let listData = []
let itemEdit = {}
let listPerkiraan = []
let listValas = []
let tipeform = ''

$(document).ready(function(){

    // $("#page2").show()
    // $("#page1").hide()

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

        $("#tabel_add_list_modal").DataTable({
          "lengthChange": false,
            "paging": false ,'order': [[1, 'asc']],
            "searching" : false,
            "columnDefs": [
          {"targets" :[0] , 'orderable' : false}
         // {  "className": "text-center", "targets": [4] },
       ]
      });







        $("#tabel_add_list_custsupp").DataTable({
          "lengthChange": false,
            "paging": false ,
      });
});



function submitOtorisasi () {

  let _token = $("#_token").val();
  let nobukti = $("#input_detail_nobukti").val();
  $.ajax({
    url: "{!! url('memorialkoreksispotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      alertify.success('Berhasil update otorisasi')
      loadAll()
      buttonCloseForm()




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function loadAll () {


  $.ajax({
    url: "{!! url('memorialkoreksiloadall') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res.tempOutstanding)
      $('#tabel').DataTable().destroy();
      let rowTable = ""





      res.tempOutstanding.forEach((item, i) => {
        console.log(item)
        rowTable += `
        <tr>
          <td>${item[0].NoBukti}</td>
          <td>${formatDate(item[0].Tanggal)}</td>
          <td>${item[0].TipeTransHd}</td>

          <td>${item[0].Perkiraan ? item[0].Perkiraan : ''}</td>
          <td>${item[0].Note}</td>
          <td class="text-right">${formatAngkaX(item[0].TotalRp)}</td>



          ${item[0].IsOtorisasi1 == 1 ?
            `<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>`
            :
            `<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>`}

          <td>${item[0].OtoUser1}</td>
          <td>${item[0].TglOto1 ? formatDate(item[0].TglOto1 ,'/') : '' }</td>



	  <td class='text-center'>
            <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item[0].NoBukti}' , 'detail')">
              <i class="bi bi-info"></i>
            </button>

            ${
              item[0].IsOtorisasi1 == 1 ? `
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${item[0].NoBukti}' , 'edit')">
                  <i class="bi bi-key"></i>
                </button>

                <button class="btn btn-primary btn-sm" type="button" onclick="submitPrint('${item[0].NoBukti}')">
                  <i class="bi bi-printer"></i>
                </button>` : `

                <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item[0].NoBukti}' , 'edit')">
                  <i class="bi bi-pen"></i>
                </button>

                <button class="btn btn-primary btn-sm" type="button" onclick="buttonDetail('${item[0].NoBukti}' , 'otorisasi')">
                  <i class="bi bi-key"></i>
                </button>`
            }
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

  }
  })

}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('memorialkoreksidetailCetak') !!}",
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
    for (let i = 0; i < dataPrint.length; i+=10) {
      let tempArray = dataPrint.slice(i,i+10)
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
            <!-- JUDUL -->
              <tr>
                <td colspan="4" rowspan="2" style="border:1px solid; text-align:center; font-weight:bold; font-size:18px;">
                  BUKTI JURNAL MEMORIAL
                </td>
                <td style="border:1px solid; width:15%;">No. Bukti</td>
                <td style="border:1px solid; width:25%;">${dataPrint[0].NoBukti}</td>
              </tr>

              <!-- TANGGAL -->
              <tr>
                <td style="border:1px solid;">Tanggal</td>
                <td style="border:1px solid;">${tanggalOnly}</td>
              </tr>

              <!-- KEPADA -->
              <tr>
                <td style="border:1px solid;">Kepada</td>
                <td colspan="3" style="border:1px solid;">${dataPrint[0].Note ? dataPrint[0].Note : '-'}</td>
                <td colspan="2" style="border:1px solid;">
                  ${dataPrint[0].ketperk}
                </td>
              </tr>
                  <tr>
                    <td rowspan="2" class="text-center" style="width: 1%">No.</td>
                    <td rowspan="2" class="text-center" style="width: 10%">KODE</td>
                    <td rowspan="2" class="text-center" style="width: 20%">NAMA</td>
                    <td rowspan="2" class="text-center" style="width: 40%">KETERANGAN</td>
                    <td rowspan="2" class="text-center" style="width: 10%">DEBET</td>
                    <td rowspan="2" class="text-center" style="width: 10%">KREDIT</td>
                  </tr>
                </thead> `;

    let z = 0
    let maxRow = 8;
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalDebet = 0;
    let grandTotalKredit = 0;

    dataPrint.forEach(item => {

      if (item.debetx) {
        grandTotalDebet += Number(item.debetx) || 0;
      }

      if (item.Kreditx) {
        grandTotalKredit += Number(item.Kreditx) || 0;
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
               style="width: 10%;  ">${itemSub.Perk}</td>
         <td class="text-align: left"
               style="width: 20%;">${itemSub.ketperk}</td>
         <td class="text-align: left"
               style="width: 40%;">${itemSub.keterangan}</td>
               <td style="width: 10%; text-align: right;">
            ${itemSub.debetx 
              ? Number(itemSub.debetx).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                }) 
              : ''}
          </td>
         <td style="width: 10%; text-align: right;">
            ${itemSub.Kreditx 
              ? Number(itemSub.Kreditx).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                }) 
              : ''}
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
          </tr>`;
        }

        tempPrintStr += `
        <tr>
          <td colspan="3" style="border:1px solid; padding:5px; font-weight:bold;">
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold;">
            Total :
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold;">
            ${grandTotalDebet.toLocaleString('id-ID', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            })}
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold;">
            ${grandTotalKredit.toLocaleString('id-ID', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            })}
          </td>
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


         <table
            class="detail-spb-table mb-2"
            style="
              width: 100%;
              margin-top: 20px;
              font-family: sans-serif;
              font-size: 10px;
              border-collapse: collapse;">
            <tr>
              <td style="width: 20%; border: 1px solid; text-align: center;">Menyetujui</td>
              <td style="width: 20%; border: 1px solid; text-align: center;">Mengetahui</td>
              <td style="width: 20%; border: 1px solid; text-align: center;">Kasir</td>
              <td style="width: 20%; border: 1px solid; text-align: center;"></td>
            </tr>

            <tr style="height: 60px;">
              <td style="border: 1px solid;"></td>
              <td style="border: 1px solid;"></td>
              <td style="border: 1px solid;"></td>
              <td style="border: 1px solid;"></td>
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

function buttonBatalOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi Kas ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('memorialkoreksispbatalotorisasi') !!}",
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

function submitEdit () {

  let _token  = $("#_token").val()
  let nobukti  = $("#input_add_nobukti").val()
  let nourut  = $("#input_add_nourut").val()

  let tanggal  = $("#input_add_tanggal").val()

  let transaksi  = $("#input_add_transaksi").val()
  let note  = $("#input_add_note").val()
  let lampiran = 0
  let keterangan2 = ''
  let choice = "U"

  let kodedevisi  = $("#AddAddKodeDevisi").val()
  let valas  = $("#AddAddValas").val()
  let kurs  = $("#AddAddKurs").val()
  let lawan  = $("#AddAddKredit").val()
  let perkiraan  = $("#AddAddDebet").val()
  let jumlah  = $("#AddAddJumlah").val()
  let debet = Number(jumlah)
  let kredit = 0
  let keterangan  = $("#AddAddKeterangan").val()
  let keterangandetail  = $("#AddAddKeteranganDetail").val()
  let debetRp = Number(jumlah) * Number(kurs)
  let kreditRp = 0
  let tphc = 'C'
  if (transaksi == 'BJK') {
    tphc = 'X'
  }

  if (Number(jumlah) <= 0) {
    alertify.warning("Jumlah =< 0")
    return
  }

  let urut = itemEdit.Urut

  let custsuppP = ''
  let custsuppL = ''
  let noaktivaP = ''
  let noaktivaL = ''
  let statusaktivaP = ''
  let statusaktivaL = ''
  let kodebag = ''
  let nobon = ''
  let kodeP = ''
  let kodeL = ''
  let statusgiro = ''
  let simbol = ''
  let jmlrecord = tipeform == 'add' ? 0 : 1
  let notitipan = ''
  let uruttitipan = 0

  if (!perkiraan || !lawan || !kodedevisi || !note) {
    alertify.warning("Data tidak lengkap")
    return

  }

  console.log({
    choice,
    nobukti,
    nourut,
    tanggal ,
    note,
    lampiran,
    kodedevisi ,
    perkiraan ,
    lawan ,
    keterangan,
    keterangan2,
    debet,
    kredit,
    valas,
    kurs,
    debetRp,
    kreditRp,
    transaksi,
    tphc,
    custsuppP,
    custsuppL,
    urut,
    noaktivaP,
    noaktivaL,
    statusaktivaP,
    statusaktivaL,
    nobon,
    kodebag,
    kodeP,
    kodeL,
    statusgiro,
    simbol,
    notitipan,
    uruttitipan,
    keterangandetail

  })




  $.ajax({
      url: "{!! url('memorialkoreksispadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        nobukti,
        nourut,
        tanggal ,
        note,
        lampiran,
        kodedevisi ,
        perkiraan ,
        lawan ,
        keterangan,
        keterangan2,
        debet,
        kredit,
        valas,
        kurs,
        debetRp,
        kreditRp,
        transaksi,
        tphc,
        custsuppP,
        custsuppL,
        urut,
        noaktivaP,
        noaktivaL,
        statusaktivaP,
        statusaktivaL,
        nobon,
        kodebag,
        kodeP,
        kodeL,
        statusgiro,
        simbol,
        notitipan,
        uruttitipan,
        keterangandetail,
        jmlrecord,
        tipeform
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Memorial telah diedit');

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

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}

function submitAdd () {


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

  let tanggal  = $("#input_add_tanggal").val()

  let transaksi  = $("#input_add_transaksi").val()
  let note  = $("#input_add_note").val()
  let lampiran = 0
  let keterangan2 = ''
  let choice = "I"

  let kodedevisi  = $("#AddAddKodeDevisi").val()
  let valas  = $("#AddAddValas").val()
  let kurs  = $("#AddAddKurs").val()
  let lawan  = $("#AddAddKredit").val()
  let perkiraan  = $("#AddAddDebet").val()
  let jumlah  = $("#AddAddJumlah").val()
  let debet = Number(jumlah)
  let kredit = 0
  let keterangan  = $("#AddAddKeterangan").val()
  let keterangandetail  = $("#AddAddKeteranganDetail").val()
  let debetRp = Number(jumlah) * Number(kurs)
  let kreditRp = 0
  let tphc = 'C'
  if (transaksi == 'BJK') {
    tphc = 'X'
  }

  if (Number(jumlah) <= 0) {
    alertify.warning("Jumlah <= 0")
    return
  }

  let urut = 0

  let custsuppP = ''
  let custsuppL = ''
  let noaktivaP = ''
  let noaktivaL = ''
  let statusaktivaP = ''
  let statusaktivaL = ''
  let kodebag = ''
  let nobon = ''
  let kodeP = ''
  let kodeL = ''
  let statusgiro = ''
  let simbol = ''
  let jmlrecord = tipeform == 'add' ? 0 : 1
  let notitipan = ''
  let uruttitipan = 0

  if (!perkiraan || !lawan || !kodedevisi || !note) {
    alertify.warning("Data tidak lengkap")
    return

  }

  console.log({
    choice,
    nobukti,
    nourut,
    tanggal ,
    note,
    lampiran,
    kodedevisi ,
    perkiraan ,
    lawan ,
    keterangan,
    keterangan2,
    debet,
    kredit,
    valas,
    kurs,
    debetRp,
    kreditRp,
    transaksi,
    tphc,
    custsuppP,
    custsuppL,
    urut,
    noaktivaP,
    noaktivaL,
    statusaktivaP,
    statusaktivaL,
    nobon,
    kodebag,
    kodeP,
    kodeL,
    statusgiro,
    simbol,
    notitipan,
    uruttitipan,
    keterangandetail

  })




  $.ajax({
      url: "{!! url('memorialkoreksispadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        nobukti,
        nourut,
        tanggal ,
        note,
        lampiran,
        kodedevisi ,
        perkiraan ,
        lawan ,
        keterangan,
        keterangan2,
        debet,
        kredit,
        valas,
        kurs,
        debetRp,
        kreditRp,
        transaksi,
        tphc,
        custsuppP,
        custsuppL,
        urut,
        noaktivaP,
        noaktivaL,
        statusaktivaP,
        statusaktivaL,
        nobon,
        kodebag,
        kodeP,
        kodeL,
        statusgiro,
        simbol,
        notitipan,
        uruttitipan,
        keterangandetail,
        jmlrecord,
        tipeform
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Memorial telah ditambah');

          $('.showhideitem').hide();
          loadAll()
          // buttonCloseForm()
          tipeform = 'edit'
          // document.getElementById("buttonAddListCustomer").disabled = true
          // document.getElementById("input_add_tanggal").disabled = true
          lockFormAdd()
          refreshDataTable(nobukti)

          // $("#form").modal('toggle')

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

function buttonAddListValas () {
  listValas= []

  console.log('buttonAddListValas')


  let _token = $("#_token").val();


  $.ajax({
    url: "{!! url('memorialkoreksilistvalas') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      listValas = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KODEVLS}</td>
        <td>${item.NAMAVLS}</td>
        <td class="text-right">${parseFloat(item.KURS).toFixed(2)}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickValas(${i},'${item.KODEVLS}' , '${item.NAMAVLS}' , '${item.KURS}' )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_valas").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListValas').show();
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

function buttonAddPickValas(index,kode , nama , kurs) {
  console.log('buttonAddPickValas')


  document.getElementById("AddAddValas").value = kode
  document.getElementById("AddAddKurs").value = parseFloat(kurs).toFixed(2)

  buttonAddListBatal()
}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddMain').show();

  $("#form").modal('toggle')
}

function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();

}

function buttonAddBatal () {
  $('.showhideitem').hide()
}

function cleanFormAdd () {
  document.getElementById("input_add_note").value = ''
  document.getElementById("input_add_transaksi").value = 'BMM'
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("addTableData").innerHTML = `<tr><td colspan=11 class="text-center">Belum ada data</td></tr>`


}

function cleanFormAddAdd () {
  document.getElementById("AddAddKodeDevisi").value = ''
  document.getElementById("AddAddValas").value = 'IDR'
  document.getElementById("AddAddKurs").value = '1.00'
  document.getElementById("AddAddJumlah").value = '0.00'
  document.getElementById("AddAddKeterangan").value = ''
  document.getElementById("AddAddKeteranganDetail").value = ''
  document.getElementById("AddAddDebet").value = ''
  document.getElementById("AddAddKeteranganDebet").value = ''
  document.getElementById("AddAddKredit").value = ''
  document.getElementById("AddAddKeteranganKredit").value = ''
}

function lockFormAddAdd (value = true) {
  document.getElementById("AddAddKodeDevisi").disabled = value
  document.getElementById("buttonAddListValas").disabled = value
  document.getElementById("buttonAddListDebet").disabled = value
  document.getElementById("buttonAddListKredit").disabled = value
}



function unlockFormAdd () {
  document.getElementById("input_add_tanggal").disabled = false
  document.getElementById("input_add_transaksi").disabled = false
  document.getElementById("input_add_note").disabled = false

}

function lockFormAdd () {
  document.getElementById("input_add_tanggal").disabled = true
  document.getElementById("input_add_transaksi").disabled = true
  document.getElementById("input_add_note").disabled = true

}




function buttonAddListPerkiraan (idTujuan) {
  listPerkiraan = []

  console.log('buttonAddListPerkiraan')


  let _token = $("#_token").val();
  // let perkiraan = $("#input_add_kodeperkiraan").val();
  let transaksi = $("#input_add_transaksi").val();


  $.ajax({
    url: "{!! url('memorialkoreksilistperkiraan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
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
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPerkiraan(${i},'${item.Perkiraan}' , '${item.Keterangan}' , '${idTujuan}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_perkiraan").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListPerkiraan').show();
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


function buttonAddPickPerkiraan (index, perkiraan, keterangan , idTujuan) {

  console.log(index, perkiraan, keterangan , idTujuan)
  document.getElementById(`AddAdd${idTujuan}`).value = perkiraan
  document.getElementById(`AddAddKeterangan${idTujuan}`).value = keterangan
  buttonAddListBatal()

}



function buttonDeleteItem (i) {

  let itemDelete = listData[i]
  console.log(itemDelete)

  alertify.confirm('Hapus Item', `Apakah yakin ingin menghapus Memorial ${itemDelete.namaPerkiraan} - ${itemDelete.NamaLawan} ?`,
      function() {
        let _token  = $("#_token").val()
        let nobukti  = $("#input_add_nobukti").val()
        let nourut  = $("#input_add_nourut").val()

        let tanggal  = $("#input_add_tanggal").val()

        let transaksi  = $("#input_add_transaksi").val()
        let note  = $("#input_add_note").val()
        let lampiran = 0
        let keterangan2 = ''
        let choice = "D"

        let kodedevisi  = $("#AddAddKodeDevisi").val()
        let valas  = $("#AddAddValas").val()
        let kurs  = $("#AddAddKurs").val()
        let lawan  = $("#AddAddKredit").val()
        let perkiraan  = $("#AddAddDebet").val()
        let jumlah  = $("#AddAddJumlah").val()
        let debet = Number(jumlah)
        let kredit = 0
        let keterangan  = $("#AddAddKeterangan").val()
        let keterangandetail  = $("#AddAddKeteranganDetail").val()
        let debetRp = Number(jumlah) * Number(kurs)
        let kreditRp = 0
        let tphc = 'C'
        if (transaksi == 'BJK') {
          tphc = 'X'
        }


        let urut = itemDelete.Urut

        let custsuppP = ''
        let custsuppL = ''
        let noaktivaP = ''
        let noaktivaL = ''
        let statusaktivaP = ''
        let statusaktivaL = ''
        let kodebag = ''
        let nobon = ''
        let kodeP = ''
        let kodeL = ''
        let statusgiro = ''
        let simbol = ''
        let jmlrecord = tipeform == 'add' ? 0 : 1
        let notitipan = ''
        let uruttitipan = 0




        $.ajax({
            url: "{!! url('memorialkoreksispadd') !!}",
            type: "post",
            async: false,
            data: {
              _token,
              choice,
              nobukti,
              nourut,
              tanggal ,
              note,
              lampiran,
              kodedevisi ,
              perkiraan ,
              lawan ,
              keterangan,
              keterangan2,
              debet,
              kredit,
              valas,
              kurs,
              debetRp,
              kreditRp,
              transaksi,
              tphc,
              custsuppP,
              custsuppL,
              urut,
              noaktivaP,
              noaktivaL,
              statusaktivaP,
              statusaktivaL,
              nobon,
              kodebag,
              kodeP,
              kodeL,
              statusgiro,
              simbol,
              notitipan,
              uruttitipan,
              keterangandetail,
              jmlrecord,
              tipeform
            },
            success: function(res) {
              console.log(res ,'!')

              if (res == 1) {
                // $("#form").modal('toggle')
                alertify.success('Memorial telah diedit');

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

function buttonEditItem (i) {
  console.log(listData[i])
  itemEdit = listData[i]
  document.getElementById("AddAddKodeDevisi").value = itemEdit.Devisi
  document.getElementById("AddAddValas").value = itemEdit.Valas
  document.getElementById("AddAddKurs").value = parseFloat(itemEdit.Kurs).toFixed(2)
  document.getElementById("AddAddJumlah").value = parseFloat(itemEdit.JumlahRp).toFixed(2)
  document.getElementById("AddAddKeterangan").value = itemEdit.Keterangan
  document.getElementById("AddAddKeteranganDetail").value = itemEdit.KetDetail
  document.getElementById("AddAddDebet").value = itemEdit.Perkiraan
  document.getElementById("AddAddKeteranganDebet").value = itemEdit.namaPerkiraan
  document.getElementById("AddAddKredit").value = itemEdit.Lawan
  document.getElementById("AddAddKeteranganKredit").value = itemEdit.NamaLawan



  lockFormAddAdd(true)
  $('.showhideitem').hide();
  $('#labelEditItem').show();
  $('#buttonEditItem').show();
  $('#buttonSubmitEdit').show();
  $('#formAddAdd').show();
}

function buttonAddItem () {
  lockFormAddAdd(false)
  cleanFormAddAdd()
  $('.showhideitem').hide();
  $('#labelAddItem').show();

  $('#buttonSubmitAdd').show();
  $('#buttonAddItem').show();
  $('#formAddAdd').show();
}



function refreshDataTable (nobukti) {
  console.log('refreshDataTable' , nobukti)
  let _token = $("#_token").val();
  listData = []
  $.ajax({
    url: "{!! url('memorialkoreksispdetail') !!}",
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
          $('.mainpage').hide();
          $('#page1').show();
          return
      }
      // dataTableAdd = res

      let rowTable = ``
      listData.forEach((item, i) => {


              rowTable += `
                <tr>
                  <td>${item.NamaDevisi}</td>

                  <td>${item.namaPerkiraan}</td>
                  <td>${item.NamaLawan}</td>
                  <td>${item.Note}</td>
                  <td class="text-right">${formatAngka(parseFloat(item.DebetRp).toFixed(2))}</td>
                  <td class="text-right">${formatAngka(parseFloat(item.KreditRp).toFixed(2))}</td>
                  <td class="text-right">${formatAngka(parseFloat(item.Debet).toFixed(2))}</td>
                  <td class="text-right">${formatAngka(parseFloat(item.Kredit).toFixed(2))}</td>
                  <td>${item.Valas}</td>

                  <td class="text-right">${formatAngka(parseFloat(item.Kurs).toFixed(2))}</td>


                  <td class="text-center">
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonEditItem(${i} )"><i class="bi bi-pen"></i></button>
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteItem(${i} )"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>

              `

              // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
      });

      document.getElementById("addTableData").innerHTML = rowTable

      document.getElementById("input_add_transaksi").value = listData[0].TipeTrans

        document.getElementById("input_add_nobukti").value = listData[0].NoBukti

        document.getElementById("input_add_tanggal").valueAsDate = new Date(listData[0].Tanggal)
        document.getElementById("input_add_note").value = listData[0].Note
        document.getElementById("input_add_nourut").value = listData[0].nourut



    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
}

function refreshDataTableDetail (nobukti) {
  console.log('refreshDataTableDetail' , nobukti)
  let _token = $("#_token").val();
  listData = []
  $.ajax({
    url: "{!! url('memorialkoreksispdetail') !!}",
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


              rowTable += `
                <tr>
                  <td>${item.NamaDevisi}</td>

                  <td>${item.namaPerkiraan}</td>
                  <td>${item.NamaLawan}</td>
                  <td>${item.Note}</td>
                  <td class="text-right">${formatAngka(parseFloat(item.DebetRp).toFixed(2))}</td>
                  <td class="text-right">${formatAngka(parseFloat(item.KreditRp).toFixed(2))}</td>
                  <td class="text-right">${formatAngka(parseFloat(item.Debet).toFixed(2))}</td>
                  <td class="text-right">${formatAngka(parseFloat(item.Kredit).toFixed(2))}</td>
                  <td>${item.Valas}</td>

                  <td class="text-right">${formatAngka(parseFloat(item.Kurs).toFixed(2))}</td>

                </tr>

              `

              // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
      });

      document.getElementById("detailTableData").innerHTML = rowTable

      document.getElementById("input_detail_transaksi").value = listData[0].TipeTrans

        document.getElementById("input_detail_nobukti").value = listData[0].NoBukti

        document.getElementById("input_detail_tanggal").valueAsDate = new Date(listData[0].Tanggal)
        document.getElementById("input_detail_note").value = listData[0].Note
        document.getElementById("input_detail_nourut").value = listData[0].nourut



    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
}

function buttonDetail (nobukti , tipe = 'detail') {

  let _token = $("#_token").val();




  refreshDataTableDetail(nobukti)
  if(!listData.length) {
    alertify.warning("Data tidak ditemukkan")
    return
  } else {

  }

  if (tipe == 'otorisasi') {
    let akses = $("#akses_isotorisasi1").val();
    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }

    $('#buttonOtorisasi').show()
  } else {
    $('#buttonOtorisasi').hide()
  }

  $('.mainpage').hide()
  $('#page3').show()


}


function buttonKoreksi (nobukti) {

  let akses = $("#akses_iskoreksi").val();
  let _token = $("#_token").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  lockFormAdd()
  tipeform = 'edit'

  refreshDataTable(nobukti)
  if(!listData.length) {
    alertify.warning("Data tidak ditemukkan")
    return
  } else {
    $('.mainpage').hide()
    $('#page2').show()
  }


}

function buttonAdd () {

  let akses = $("#akses_istambah").val();
  let _token = $("#_token").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  cleanFormAdd()
  setNewNoBukti()
  unlockFormAdd()
  $(".showhideitem").hide()

  $(".mainpage").hide()
  $("#page2").show()


}

function onChangeTransaksi () {
  setNewNoBukti()
}

function setNewNoBukti () {
  console.log('setNewNoBukti')
  let _token  = $("#_token").val()
  let kode  = $("#input_add_transaksi").val()
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



function formatDate (date , pemisah = '-') {
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
