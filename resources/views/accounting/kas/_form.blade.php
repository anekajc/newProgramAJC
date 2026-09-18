<div id="page2" style="display: none" class="mainpage container-fluid">

    <div class="row" style="margin-top: 0px" id="contentContainer">
        <div class="col-8 text-left">
            {{-- <h2>Form Kas</h2> --}}
        </div>
        <div class="col-4 text-right">
            <button type="button" class="btn btn-danger btn-action-danger btn-pill-primary"
                onclick="buttonCloseForm()">CLOSE</button>
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
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Transaksi</label>
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <select id="input_add_transaksi" class="form-control form-select-lg mb-3"
                                        aria-label=".form-select-lg example" onChange="onChangeTransaksi()">
                                        <option value='BKK' selected>BKK</option>
                                        <option value='BKM'>BKM</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row" style="margin-top: -10px">

                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Kas</label>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group input-group">
                                        <input type="hidden" class="form-control" id="input_add_simbol" placeholder=""
                                            disabled>
                                        <input type="text" class="form-control" id="input_add_kodeperkiraan"
                                            placeholder="" disabled>
                                        <button class="btn btn-chip-biru text-right" id="buttonAddListPerkiraan"
                                            onclick="buttonAddListPerkiraan()"><i class="bi bi-search"></i></button>
                                    </div>
                                </div>

                                <div class="col-md-12" style="margin-top:-10px">
                                    <div class="form-group">
                                        <textarea style="width: 100%; resize: none" rows=1 class="form-control" id="input_add_keteranganperkiraan" disabled></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>No Bukti</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control"
                                                            id="input_add_nobukti" placeholder="No Bukti" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Tgl</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <input type="date" class="form-control text-center"
                                                            id="input_add_tanggal" value="{!! date('Y-m-d') !!}">
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
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="partBKK showhidePart">Kepada</label>
                                                <label class="partBKM showhidePart">Terima</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="input_add_kepadaterima"
                                                    placeholder="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: -10px" class="">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="partBKK showhidePart">Bon</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group input-group">
                                                <input type="text" class="form-control partBKK showhidePart"
                                                    id="input_add_bon" placeholder="" disabled>

                                                <button class="btn btn-chip-biru text-right partBKK showhidePart"
                                                    id="buttonAddListBon" onclick="buttonAddListBon()"><i
                                                        class="bi bi-search"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="partBKK showhidePart">Nilai Bon</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <!-- <input type="number" class="form-control text-right partBKK showhidePart" id="input_add_nilaibon" value="0.00" disabled> -->
                                                <!-- // update ribuan -->
                                                <input type="text" id="input_add_nilaibon"
                                                    class="form-control text-right input-partial-number partBKK showhidePart"
                                                    value='0.00' disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="form-group">
                                            <label></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="form-group">
                                            <label></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="form-check form-group text-left">
                                            <input class="form-check-input partBKK showhidePart" type="checkbox"
                                                value="" id="checkBoxKembaliUang"
                                                onchange="onclickKembaliUang()">
                                            <label class="form-check-label partBKK showhidePart"
                                                for="flexCheckDefault">
                                                Kembali Uang
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container-fluid">
                    <hr />
                </div>
                <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

                    <div class="dph-table-outer">
                        <div class="dph-table-wrap">
                            <table id="addTable" class="dph-tb">
                                <thead>
                                    <tr>
                                        <th scope="col">Devisi</th>
                                        <th scope="col">Perk.</th>
                                        <th scope="col">Ket. Perk</th>
                                        <th scope="col">Lawan</th>
                                        <th scope="col">Ket. Lawan</th>
                                        <th scope="col">Sumber</th>
                                        <th scope="col" class="num">Jumlah</th>
                                        <th scope="col">Keterangan</th>
                                        <th scope="col" class="num">Giro Rp</th>
                                        <th scope="col">Costing</th>
                                        <th scope="col">Sub Cost</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>

                                <tbody id="addTableData" class="">
                                    <tr>
                                        <td colspan="9" class="text-center">Belum ada data</td>
                                    </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mt-2 text-right">
                    <button id="buttonAddAddItem" type="button" class="btn btn-chip-biru"
                        onclick="buttonAddAddItem()">+ Tambah</button>
                </div>

                <div id="formAddAdd" class="container-fluid showhideitem">
                    <!-- <div class="line"></div> -->
                    <!-- <div class="row"> -->

                    <div class="col-12">
                        <hr />
                        <div class="row">
                            <div class="col-md-12">
                                {{-- <h4 id="labelAddAddItem">Add Item</h4> --}}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Devisi</label>
                                                </div>
                                            </div>
                                            <!-- <div class="col-4 text-right">

              </div> -->
                                            <div class="col-md-4">
                                                <div class="input-group form-group">
                                                    <input id="AddAddKodeDevisi" type="text" class="form-control"
                                                        value="01" disabled>

                                                    <button id="buttonAddListDevisi" type="button"
                                                        onclick="buttonAddListDevisi()"
                                                        class="btn btn-chip-biru"><i class="bi bi-search"></i></button>

                                                </div>
                                            </div>

                                            <div class="col-md-5">
                                                <div class="input-group form-group">
                                                    <input id="AddAddNamaDevisi" type="text" class="form-control"
                                                        value="Accounting" disabled>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: -10px">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Valas</label>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="col-4 text-right">

                  </div> -->
                                                    <div class="col-md-4">
                                                        <div class="input-group form-group">
                                                            <input id="AddAddValas" type="text"
                                                                class="form-control" value="IDR" disabled>
                                                            <button id="buttonAddListValas" type="button"
                                                                onclick="buttonAddListValas()"
                                                                class="btn btn-chip-biru"><i class="bi bi-search"></i></button>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Kurs</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="input-group form-group">
                                                            <input id="AddAddKurs" type="number" value="1.00"
                                                                class="text-right form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: -10px">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label>Lawan</label>
                                                                </div>
                                                            </div>
                                                            <!-- <div class="col-4 text-right">

                  </div> -->
                                                            <div class="col-md-4">
                                                                <div class="input-group form-group">
                                                                    <input id="AddAddLawan" type="text"
                                                                        class="form-control" disabled>
                                                                    <input id="AddAddKodeLawan" type="hidden"
                                                                        class="form-control" disabled>
                                                                    <button id="buttonAddListLawan" type="button"
                                                                        onclick="buttonAddListLawan()"
                                                                        class="btn btn-chip-biru"><i class="bi bi-search"></i></button>

                                                                </div>
                                                            </div>

                                                            <div class="col-md-5">
                                                                <div class="input-group form-group">
                                                                    <input id="AddAddKeteranganLawan" type="text"
                                                                        class="form-control" disabled>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="rowCustsupp" style="margin-top: -10px">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Custsupp</label>
                                                </div>
                                            </div>
                                            <!-- <div class="col-4 text-right">

          </div> -->
                                            <div class="col-md-4">
                                                <div class="input-group form-group">
                                                    <input id="AddAddKodeCustsupp" type="text"
                                                        class="form-control" disabled>
                                                    <button id="buttonAddListCustsupp" type="button"
                                                        onclick="buttonAddListCustsupp()"
                                                        class="btn btn-chip-biru"><i class="bi bi-search"></i></button>
                                                </div>
                                            </div>

                                            <div class="col-md-5">
                                                <div class="input-group form-group">
                                                    <input id="AddAddNamaCustsupp" type="text"
                                                        class="form-control" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Jumlah</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group form-group">
                                                    <input id="AddAddJumlah" type="number" value="0.00"
                                                        class="text-right form-control"
                                                        onblur='onChangeAddAddJumlah()'>
                                                    <input id="AddAddJumlahTunai" type="hidden" value="0.00"
                                                        class="text-right form-control" onblur=''>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: -10px">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Keterangan</label>
                                                </div>
                                            </div>
                                            <!-- <div class="col-4 text-right">

              </div> -->
                                            <div class="col-md-8">
                                                <div class="input-group form-group">
                                                    <input id="AddAddKeterangan" type="text" value=""
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: -10px">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Ket. Det</label>
                                                </div>
                                            </div>
                                            <!-- <div class="col-4 text-right">

              </div> -->
                                            <div class="col-md-8">
                                                <div class="input-group form-group">
                                                    <input id="AddAddKeteranganDetail" type="text" value=""
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-4"> -->
                            <!-- <div class="row">
      </div>
      </div> -->
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Departemen</label>
                                                </div>
                                            </div>
                                            <!-- <div class="col-4 text-right">

              </div> -->
                                            <div class="col-md-4">
                                                <div class="input-group form-group">
                                                    <input id="AddAddKodeDepartemen" type="text"
                                                        class="form-control" disabled>
                                                    <button id="buttonAddListDepartemen" type="button"
                                                        onclick="buttonAddListDepartemen()"
                                                        class="btn btn-primary">+</button>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group form-group">
                                                    <input id="AddAddNamaDepartemen" type="text"
                                                        class="form-control" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="rowCosting" style="margin-top: -10px">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Costing</label>
                                                </div>
                                            </div>
                                            <!-- <div class="col-4 text-right">

              </div> -->
                                            <div class="col-md-4">
                                                <div class="input-group form-group">
                                                    <input id="AddAddKodeCosting" type="text" class="form-control"
                                                        disabled>
                                                    <button id="buttonAddListCosting" type="button"
                                                        onclick="buttonAddListCosting()"
                                                        class="btn btn-primary">+</button>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group form-group">
                                                    <input id="AddAddNamaCosting" type="text" class="form-control"
                                                        disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="rowSubCosting" style="margin-top: -10px">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>SubCosting</label>
                                                </div>
                                            </div>
                                            <!-- <div class="col-4 text-right">

              </div> -->
                                            <div class="col-md-4">
                                                <div class="input-group form-group">
                                                    <input id="AddAddKodeSubCosting" type="text"
                                                        class="form-control" disabled>
                                                    <button id="buttonAddListSubCosting" type="button"
                                                        onclick="buttonAddListSubCosting()"
                                                        class="btn btn-primary">+</button>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group form-group">
                                                    <input id="AddAddNamaSubCosting" type="text"
                                                        class="form-control" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2" style="padding:0; margin: 0">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="row">
                                        </div>
                                    </div>
                                    <div class="col-md-10 text-left">
                                        <div class="row text-left">
                                            <div class="form-check form-group text-left">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="checkBoxSKB" onclick="onclickPSKB()">
                                                <label class="form-check-label" for="flexCheckDefault">
                                                    pSKB
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- </div> -->
                            </div>
                            <!-- </div> -->
                        </div>
                        <div class="row" style="margin-top: -10px">
                        </div>
                    </div>

                    <div class="row mt-2" style="margin-top: 0" id="contentContainer">
                        <div class="col-md-12 text-right mt-4">
                            <button type="button" class="btn btn-secondary btn-pill-secondary"
                                onclick="buttonAddBatal()">Batal</button>

                            <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()"
                                class="btn btn-primary btn-action-primary btn-pill-primary">Submit Add</button>

                            <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()"
                                class="btn btn-primary btn-action-primary btn-pill-primary">Submit Edit</button>
                            <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
