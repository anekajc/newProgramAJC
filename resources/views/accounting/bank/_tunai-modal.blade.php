  {{-- rt-picker-v2 (docs/new-cust-supp-modal-guide.md): tanpa kolom Actions, seluruh baris diklik.
       Baris faktur -> tanya nominal pelunasan; baris pelunasan biru (.is-selected, milik bukti ini)
       -> konfirmasi lalu batalkan. Lihat tunaiRowHtml()/buttonKlikTunai() di public/js/bank.js. --}}
  <div class="modal fade rt-picker-v2" id="formTunai" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="min-width: 1400px">
      <div id="" class="modal-content ">

        <div id= "" class="">
        <div class="modal-header">


            <h5 class="modal-title" id="">Pelunasan Hutang</h5>
          <button type="button" class="close" onclick="selesaiTunai()" aria-label="Close">
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
                      <label>Nobukti</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control " id="input_tunai_nobukti" disabled>
                    </div>
                  </div>

                </div>

              </div>

              <div class="col-md-4">
                <div class="row">


                <div class="col-md-4">
                  <div class="form-group">
                    <label>Custsupp</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control " id="input_tunai_custsupp" disabled>
                  </div>
                </div>

              </div>
              </div>

            </div>








            </div>




            <div class="row" style="margin-top:20px">
              <div class="col-12">
                <p class="text-muted small mb-2">Klik baris faktur untuk membayar; klik baris
                  pelunasan (biru) untuk membatalkan.</p>
              </div>
              <div class="col-12" style="overflow:auto;  max-height: 400px">
              <!-- <div class="container-fluid"> -->


              <table id="tabel_add_list_tunai" class="dph-tb">
                <thead>
                  <tr>
                    <th scope="col">NoFaktur</th>
                    <th scope="col">NoRetur</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">NoPelunasan</th>
                    <th scope="col">Debet</th>
                    <th scope="col">Kredit</th>
                    <th scope="col">Saldo</th>
                    <th scope="col">Valas</th>
                    <th scope="col">Kurs</th>

                  </tr>
                </thead>


                <tbody id="tabel_data_add_list_tunai" class="text-left" >

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



          {{-- Hanya menutup: tiap pelunasan sudah tersimpan (sementara) saat barisnya diklik
               dan dibukukan saat item disimpan. Lihat selesaiTunai() di public/js/bank.js. --}}
          <button type="button" class="btn btn-primary btn-action-primary btn-pill-primary" onclick="selesaiTunai()">Selesai</button>
          <!-- <button type="button" class="btn btn-primary" onclick="submitAddModalX()">Submit</button> -->
        </div>
        </div>

        </div>

      </div>

<!-- End modal add-->








