
<!-- start modal subgroup -->
<div class="modal fade"  id="formAddBOM" tabindex="-1" role="dialog" aria-labelledby="formAddBOMTitle" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formAddBOMTitle">Tambah BOM</h5>
        
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid" style='height:32px;'>         
          <div class="row align-items-center">
            <input type="hidden" id="input_subgroup_kodegroup" value="" />
            {{-- NoUrut (2 digit, suffix Kode BOM) -- dihitung sekali di buttonAdd()
                 (produksibom.blade.php, lewat produksibomnextkodebom) lalu disimpan di sini
                 supaya submitAddProses() tidak perlu hitung ulang terpisah dari Kode BOM
                 yang sudah ditampilkan. --}}
            <input type="hidden" id="input_BOM_noUrut" value="" />
            <div class="col-2">
              <div class="form-group mb-0">
                <label class="mb-0">Kode BOM</label>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group mb-0">
                <input type="text" class="form-control" id="input_BOM_kodeBOM" placeholder="Kode BOM" disabled>
              </div>
            </div>

            <div class="col-2 pl-4">
              <div class="form-group mb-0">
                <div class="form-check d-flex align-items-center p-0 m-0">
                  <label class="form-check-label mb-0 ml-2" for="input_BOM_default">Default  </label>
                  <input type="checkbox" class="form-check-input position-static m-0" id="input_BOM_default" name="input_BOM_default" value="1">
                </div>
              </div>
            </div>

            <div class="col-2 ml-auto text-right">
              <button type="button" class="btn btn-sm btn-chip-biru" onclick="buttonAddProses()">Tambah Proses</button>
            </div>
          </div>
        </div>

    <!-- ADD PROSES -->

    <div id="addProses" class="container-fluid showhide">
        <div id="formBsGrid" class="form-card mt-3">
          <div class="form-card-title">Tambah Proses</div>
          <div class="bs-form bs-form-lebar">
            <label for="input_add_perkPers">Kode Proses</label>
            <div class="input-group">
              <input type="text" class="form-control" id="input_BOM_kodeProses" disabled>
              <div class="input-group-append">
                <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonKodeProses()" title="Cari Kode Proses"><i class="bi bi-search"></i></button>
              </div>
            </div>
          </div>
          <div class="text-right mt-3">
            <button type="button" class="btn btn-sm btn-danger" onclick="closeShowHideSubGroup()">Batal</button>
            <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddProses()">Simpan</button>
          </div>
        </div>
      </div>

    <!-- END ADD PROSES -->

    <!-- EDIT SUBGROUP -->

    <div id="editSubGroup" class="container-fluid showhide">
        <div id="formBsGrid" class="form-card mt-3">
          <div class="form-card-title">Edit Sub Group</div>
          <div class="bs-form bs-form-lebar">
            <label for="input_subgroup_edit_kodesubgroup">Kode Subgroup</label>
            <input id="input_subgroup_edit_kodesubgroup" type="text" class="form-control" disabled>
            <label for="input_edit_perkPers">Perk. Persiapan</label>
            <div class="input-group">
              <input type="text" class="form-control" id="input_edit_perkPers" disabled>
              <div class="input-group-append">
                <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonPerkiraanSubGroup('3')" title="Cari"><i class="bi bi-search"></i></button>
              </div>
            </div>

            <label for="input_subgroup_edit_namasubgroup">Nama Subgroup</label>
            <input id="input_subgroup_edit_namasubgroup" type="text" class="form-control">
            <label for="input_edit_perkJual">Perk. Jual</label>
            <div class="input-group">
              <input type="text" class="form-control" id="input_edit_perkJual" disabled>
              <div class="input-group-append">
                <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonPerkiraanSubGroup('4')" title="Cari"><i class="bi bi-search"></i></button>
              </div>
            </div>
          </div>
          <div class="text-right mt-3">
            <button type="button" class="btn btn-sm btn-danger" onclick="closeShowHideSubGroup()">Batal</button>
            <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEditSubGroup()">Simpan</button>
          </div>
        </div>
      </div>

    <!-- END EDIT SUBGROUP -->

        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            {{-- .pb-sub-table -- didefinisikan di <style> produksibom.blade.php (gaya PO:
                 thead abu-abu, garis tipis per baris, bukan kotak Bootstrap bawaan). --}}
            <table id="tabel_proses_modal" class="pb-sub-table"  >
              <thead class="text-center">
                <tr id='theadCustom'>
                  <th scope="col">Urut</th>
                  <th scope="col">Kode Proses</th>
                  <th scope="col">Actions</th>

                </tr>
              </thead>


              <tbody id="tabel_data_proses_modal" class="text-left" >

                <tr >

                  <td></td>
                  <td></td>


                    <td class="text-center">
                      <button class="btn-action-sm btn-action-danger" type="button"><i class="bi bi-trash"></i></button>
                    </td>
              </tr>
              </tbody>


            </table>
          </div>

    </div>
    <hr>
    <div class="container-fluid" style='height:32px;'>         
      <div class="row align-items-center">
        <input type="hidden" id="input_subgroup_kodegroup" value="" />
          <div class="col-2">
            <div class="form-group mb-0">
              <label class="mb-0">Detail BOM</label>
            </div>
          </div>

        <div class="col-2 ml-auto text-right">
          <button type="button" class="btn btn-sm btn-chip-biru" onclick="buttonDetailBom()">Tambah Detail</button>
        </div>
      </div>
    </div>

    <!-- ADD DETAIL BOM -->

    <div id="addDetailBom" class="container-fluid showhide">
        <div id="formBsGridDetail" class="form-card mt-3">
          <div class="bs-form bs-form-lebar">
            {{-- Urut kosong = Tambah (Choice 'I'); terisi = Edit baris itu (Choice 'U'), lihat
                 buttonEditDetailBom()/submitAddDetailBom() di @section('js' ) bawah. --}}
            <input type="hidden" id="input_BOM_detUrut" value="" />

            {{-- Kode Proses TIDAK dipilih di form ini -- diambil otomatis dari baris yang
                 sedang dipilih (disorot) di grid "Daftar Proses" di atas, lihat
                 pbModalKodePrsAktif/pbModalPilihProses() di @section('js' ) bawah. --}}
            <div class="form-group row">

              <label for="input_BOM_detKodeProses" class="col-sm-2 col-form-label">Kode Proses</label>
              <div class="col-sm-2">
                <input type="text" class="form-control" id="input_BOM_detKodeProses" readonly>
              </div>

              <label for="input_BOM_detKodeBarang" class="col-sm-2 col-form-label">Kode Barang</label>
              <div class="col-sm-3">
                <div class="input-group">
                  <input type="text" class="form-control" id="input_BOM_detKodeBarang">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonKodeBarangDetail()" title="Cari Kode Barang"><i class="bi bi-search"></i></button>
                  </div>
                </div>
              </div>
              <div class="col-sm-3">
                <input type="text" class="form-control" id="input_BOM_namaBarang">
              </div>

            </div>

            {{-- type="number" tidak bisa menampilkan separator ribuan (browser menolak
                 karakter koma) -- diganti type="text" + formatAngkaKetik()/formatAngkaInput()
                 (disalin dari kas.js, didefinisikan di produksibom.blade.php). Nilai mentah
                 (tanpa koma) diambil lagi lewat unformatAngka() di submitAddDetailBom(). --}}
            <div class="form-group row">
              <label for="input_BOM_detNumerator" class="col-sm-2 col-form-label">Numerator</label>
              <div class="col-sm-2">
                <input type="text" inputmode="decimal" class="form-control" id="input_BOM_detNumerator" value="0.00" oninput="formatAngkaKetik(this)" onblur="formatAngkaInput(this)">
              </div>
              <label for="input_BOM_detDenominator" class="col-sm-2 col-form-label">Denominator</label>
              <div class="col-sm-2">
                <input type="text" inputmode="decimal" class="form-control" id="input_BOM_detDenominator" value="0.00" oninput="formatAngkaKetik(this)" onblur="formatAngkaInput(this)">
              </div>
              <label for="input_BOM_detLossRatio" class="col-sm-2 col-form-label">Loss Ratio</label>
              <div class="col-sm-2">
                <input type="text" inputmode="decimal" class="form-control" id="input_BOM_detLossRatio" value="0.00" oninput="formatAngkaKetik(this)" onblur="formatAngkaInput(this)">
              </div>
            </div>

          </div>

          <div class="text-right mt-3">
            <button type="button" class="btn btn-sm btn-danger" onclick="closeShowHideSubGroup()">Batal</button>
            <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddDetailBom()">Simpan</button>
          </div>
        </div>
      </div>

    <!-- END ADD DETAIL BOM -->

        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_detailbom" class="pb-sub-table"  >
              <thead class="text-center">
                <tr id='theadCustom'>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Satuan</th>
                  <th scope="col">Numerator</th>
                  <th scope="col">Denominator</th>
                  <th scope="col">Loss Ratio</th>
                  <th scope="col">Actions</th>

                </tr>
              </thead>

              <tbody id="tabel_data_detailbom" class="text-left" >

                <tr>

                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>


                    <td class="text-center">
                      <button class="btn-action-sm btn-action-success" type="button"><i class="bi bi-pen"></i></button>
                      <button class="btn-action-sm btn-action-danger" type="button"><i class="bi bi-trash"></i></button>
                    </td>
              </tr>
              </tbody>


            </table>
          </div>

    </div>
  </div>

</div>
</div>
</div>
<!-- End modal subgroup-->

<!-- Picker "Kode Proses" (DBPRoses) -- struktur sama dengan modal picker standalone SO
     (so.blade.php, mis. buttonAddListPIC()/#modalBodyAddListPIC): modal-body berisi
     container-fluid.mt-4 > row > col-12 overflow:auto > table.data-table dua kolom
     (Kode/Nama) dengan baris class="pick-row", modal-footer tombol Batal btn-batal-add,
     DataTables-nya pickerKasInit() (sama seperti dipakai SO untuk picker sejenis). -->
<div class="modal fade" id="formKodeProses" tabindex="-1" role="dialog" aria-labelledby="formKodeProsesLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formKodeProsesLabel">Kode Proses</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-30px;">
              <table id="tabel_kodeproses" class="data-table">
                <thead><tr><th style="padding: 4px 12px;" scope="col">Kode</th><th style="padding: 4px 12px;" scope="col">Nama</th></tr></thead>
                <tbody id="tabel_data_kodeproses" class="text-left"><tr><td>-</td><td>-</td></tr></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-lg btn-batal-add" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End picker Kode Proses -->

<!-- Picker "Kode Barang" (DBBARANG, 18rb+ baris) -- dipakai Tambah Detail BOM
     (buttonKodeBarangDetail()). Beda dengan picker Kode Proses di atas (muat semua sekaligus,
     paging client-side lewat pickerKasInit): tabelnya DataTable serverSide:true lewat
     produksibomlistkodebarang (ROW_NUMBER()/COUNT() OVER(), pola sama dengan #tabel di
     produksibom.blade.php) -- lihat buttonKodeBarangDetail()/tabelKodeBarangDetDT di
     @section('js' ) bawah. Kotak cari di sini TETAP cari-ketik-Enter (bukan live-search tiap
     ketikan) supaya tidak nembak query server-side tiap huruf. -->
<div class="modal fade" id="formKodeBarangDetail" tabindex="-1" role="dialog" aria-labelledby="formKodeBarangDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formKodeBarangDetailLabel">Kode Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-2">
          <div class="row">
            <div class="col-12">
              <input type="text" class="form-control" id="input_cari_kodebarangdet" placeholder="Ketik Kode/Nama Barang lalu Enter&hellip; (kosongkan untuk Barang Jadi/WIP)">
            </div>
          </div>
        </div>
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-30px;">
              <table id="tabel_kodebarangdet" class="data-table">
                <thead><tr><th style="padding: 4px 12px;" scope="col">Kode</th><th style="padding: 4px 12px;" scope="col">Nama</th></tr></thead>
                <tbody id="tabel_data_kodebarangdet" class="text-left"><tr><td>-</td><td>-</td></tr></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-lg btn-batal-add" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End picker Kode Barang -->

<script>
function buttonAddProses () {
  $('.showhide').hide();
  console.log('buttonAddSubGroup')

  // document.getElementById("input_subgroup_add_kodesubgroup").value = ''
  // document.getElementById("input_subgroup_add_namasubgroup").value = ''
  // document.getElementById("input_add_perkPers").value = ''
  // document.getElementById("input_add_perkJual").value = ''

  $('#addProses').show();
}


// Picker Kode Proses (DBPRoses) -- isi tabel #tabel_kodeproses lalu buka #formKodeProses.
// Kirim kodebom supaya proses yang sudah dipakai KodeBOM ini di DBBOMProses tidak ikut
// ditampilkan (lihat ProduksiBomController::listKodeProses()) -- mencegah Tambah Proses
// pilih KodePrs yang sama dua kali untuk satu BOM.
function buttonKodeProses () {
  $.ajax({
    url: "{!! url('produksibomlistkodeproses') !!}",
    type: "get",
    data: { kodebom: $('#input_BOM_kodeBOM').val() },
    success: function (res) {
      let rowTable = ''
      res.forEach((item) => {
        rowTable += `
          <tr class="pick-row" onclick="buttonPilihKodeProses('${item.KodePrs}', '${item.Keterangan}')">
            <td>${item.KodePrs}</td>
            <td>${item.Keterangan}</td>
          </tr>
        `
      });
      if (!res.length) {
        rowTable = '<tr><td colspan="2" class="text-center text-muted">Tidak ada data</td></tr>'
      }
      document.getElementById('tabel_data_kodeproses').innerHTML = rowTable

      pickerKasInit('tabel_kodeproses')
      $('#formKodeProses').modal('show')
    },
    error: function () {
      alertify.error('Gagal memuat Kode Proses')
    }
  });
}

function buttonPilihKodeProses (kodeprs, keterangan) {
  document.getElementById('input_BOM_kodeProses').value = kodeprs
  $('#formKodeProses').modal('hide')
}

// Simpan Tambah Proses -- sp_BOMProses Choice='I'. Kalau KodeBOM-nya belum ada di DBBOM
// (baru pertama kali Tambah untuk barang ini), SP ikut insert header DBBOM pakai
// @NoUrut/@IsDefault/@TglAwal/@TglAkhir yang dikirim di sini; Urut baris DBBOMProses-nya
// sendiri DIHITUNG ULANG di dalam SP (MAX(Urut)+1 per KodeBOM), jadi @Urut yang dikirim
// cuma pengisi parameter wajib (SP mengabaikannya untuk Choice='I').
function submitAddProses () {
  let kodeproses = $('#input_BOM_kodeProses').val()
  if (!kodeproses) {
    alertify.warning('Pilih Kode Proses terlebih dahulu')
    return
  }

  $.ajax({
    url: "{!! url('produksibomspbomproses') !!}",
    type: "post",
    data: {
      _token: $('#_token').val(),
      choice: 'I',
      kodebom: $('#input_BOM_kodeBOM').val(),
      kodebrghd: pbKodeBarangAktif,
      nourut: $('#input_BOM_noUrut').val(),
      isdefault: $('#input_BOM_default').is(':checked') ? 1 : 0,
      tglawal: '1970-01-01',
      tglakhir: '1970-01-01',
      urut: 1,
      kodeprs: kodeproses,
    },
    success: function () {
      alertify.success('Proses berhasil ditambahkan')
      $('.showhide').hide()
      document.getElementById('input_BOM_kodeProses').value = ''
      pbRefreshTabelProses()
    },
    error: function () {
      alertify.error('Gagal menyimpan proses')
    }
  });
}

function buttonHapusProses (kodebom, urut, kodeprs) {
  alertify.confirm('Hapus Proses', 'Apakah yakin ingin menghapus proses "' + kodeprs + '" dari BOM ini?',
    function () {
      $.ajax({
        url: "{!! url('produksibomspbomproses') !!}",
        type: "post",
        data: {
          _token: $('#_token').val(),
          choice: 'D',
          kodebom: kodebom,
          kodebrghd: '',
          nourut: '',
          isdefault: 0,
          tglawal: '1970-01-01',
          tglakhir: '1970-01-01',
          urut: urut,
          kodeprs: kodeprs,
        },
        success: function () {
          alertify.success('Proses berhasil dihapus')
          pbRefreshTabelProses()
          pbRefreshTabelDetailBom()
          pbRefreshDetailJikaAktif()
        },
        error: function (xhr) {
          alertify.error(xhr.responseJSON?.message || 'Gagal menghapus proses')
        }
      });
    },function () {
          alertify.error('Gagal menghapus proses');
        }
  );
}

// KodePrs aktif untuk Tambah Detail BOM -- diambil dari baris yang disorot (klik) di grid
// "Daftar Proses" modal ini, BUKAN dipilih ulang di form Tambah Detail (lihat
// buttonDetailBom() di bawah). Direset tiap kali grid-nya dimuat ulang (pbRefreshTabelProses)
// supaya tidak kebawa dari KodeBOM sebelumnya kalau user pindah BOM lain tanpa memilih
// proses baru dulu.
let pbModalKodePrsAktif = null

function pbModalPilihProses (kodeprs, tr) {
  pbModalKodePrsAktif = kodeprs
  $('#tabel_data_proses_modal tr').removeClass('pb-row-selected')
  if (tr) { $(tr).addClass('pb-row-selected') }
  pbRefreshTabelDetailBom(kodeprs)
}

// Isi ulang grid "Daftar Proses" modal (#tabel_data_proses_modal -- id sengaja beda dari
// panel "Daftar Proses" di halaman utama (#tabel_data_proses di produksibom.blade.php):
// partial modal ini dirender (lewat include produksibom.blade.php) SEBELUM section
// content-nya sendiri, jadi dua elemen ber-id sama akan membuat document.getElementById()
// di KEDUA file selalu kena elemen modal yang duluan muncul di DOM, bukan panel aslinya --
// lihat juga catatan hasil debug "Daftar Proses tidak muncul".
// DBBOMProses difilter KodeBOM, KodeBOM-nya sendiri sudah dibentuk dari DBBOM (lihat
// input_BOM_kodeBOM, diisi buttonAdd()/produksibomnextkodebom di produksibom.blade.php).
// Pakai endpoint yang sama dengan panel "Daftar Proses" di halaman utama
// (produksibomlistproses). Baris bisa diklik (pbModalPilihProses) -- itu yang menentukan
// KodePrs dipakai di Tambah Detail BOM.
// autoPilihKodePrs (opsional) -- dipakai buttonTambahDetail() di produksibom.blade.php:
// tombol "+" di panel "Daftar Proses" HALAMAN UTAMA sudah tahu KodePrs mana yang diklik,
// jadi begitu grid modal ini selesai dimuat, baris yang sama langsung disorot (tanpa user
// klik ulang di dalam modal) lalu sub-form Tambah Detail BOM langsung dibuka.
function pbRefreshTabelProses (autoPilihKodePrs) {
  let kodebom = $('#input_BOM_kodeBOM').val()
  if (!kodebom) { return }

  pbModalKodePrsAktif = null

  $.ajax({
    url: "{!! url('produksibomlistproses') !!}",
    type: "get",
    data: { kodebom },
    success: function (res) {
      let rowTable = ''
      res.forEach((item) => {
        rowTable += `
          <tr class="pb-modal-pilih-proses" data-kodeprs="${item.KodePrs}" onclick="pbModalPilihProses('${item.KodePrs}', this)">
            <td>${item.Urut}</td>
            <td>${item.KodePrs}</td>
            <td class="text-center">
              <button class="btn-action-sm btn-action-danger" type="button" onclick="event.stopPropagation(); buttonHapusProses('${kodebom}', ${item.Urut}, '${item.KodePrs}')"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
        `
      });
      if (!res.length) {
        rowTable = '<tr><td colspan="3" class="text-center text-muted">Belum ada proses</td></tr>'
      }
      document.getElementById('tabel_data_proses_modal').innerHTML = rowTable

      if (autoPilihKodePrs) {
        let tr = document.querySelector('#tabel_data_proses_modal tr[data-kodeprs="' + autoPilihKodePrs + '"]')
        if (tr) {
          pbModalPilihProses(autoPilihKodePrs, tr)
          buttonDetailBom()
        }
      }
    },
    error: function () {
      alertify.error('Gagal memuat Daftar Proses')
    }
  });
}

// Tambah Detail BOM -- KodePrs WAJIB sudah dipilih (diklik) di grid Daftar Proses di atas
// (pbModalKodePrsAktif, lihat pbModalPilihProses()), tidak dipilih ulang di form ini.
function buttonDetailBom () {
  if (!pbModalKodePrsAktif) {
    alertify.warning('Klik salah satu baris di Daftar Proses terlebih dahulu')
    return
  }

  $('.showhide').hide();

  document.getElementById('input_BOM_detUrut').value = ''
  document.getElementById('input_BOM_detKodeProses').value = pbModalKodePrsAktif
  document.getElementById('input_BOM_detKodeBarang').value = ''
  document.getElementById('input_BOM_detNumerator').value = '0.00'
  document.getElementById('input_BOM_detDenominator').value = '0.00'
  document.getElementById('input_BOM_detLossRatio').value = '0.00'

  $('#addDetailBom').show()
}

// Edit Detail BOM -- dipicu tombol pen di panel "Detail BOM" halaman utama
// (produksibom.blade.php, pbPilihProses()) ATAU grid "Detail BOM" di dalam modal ini
// (pbRefreshTabelDetailBom()). Modal #formAddBOM dibuka/disetel ulang ke KodeBOM yang
// bersangkutan (perlu, kalau dipanggil dari halaman utama modal belum tentu sedang
// terbuka untuk KodeBOM itu), lalu sub-form Tambah Detail BOM dibuka dalam mode Edit
// (input_BOM_detUrut terisi -> submitAddDetailBom() kirim Choice='U').
function buttonEditDetailBom (kodebom, kodeprs, urut, kodebrg, numerator, denominator, lossratio) {
  $('.showhide').hide();

  document.getElementById('formAddBOMTitle').textContent = 'Edit BOM'
  document.getElementById('input_BOM_kodeBOM').value = kodebom
  document.getElementById('input_BOM_noUrut').value = kodebom.split('-').pop()

  $('#formAddBOM').modal('show')
  pbRefreshTabelProses()
  pbRefreshTabelDetailBom(kodeprs)

  document.getElementById('formAddDetailBomTitle').textContent = 'Edit Detail BOM'
  document.getElementById('input_BOM_detUrut').value = urut
  document.getElementById('input_BOM_detKodeProses').value = kodeprs
  document.getElementById('input_BOM_detKodeBarang').value = kodebrg
  document.getElementById('input_BOM_detNumerator').value = formatAngka(Number(numerator ?? 0).toFixed(2))
  document.getElementById('input_BOM_detDenominator').value = formatAngka(Number(denominator ?? 0).toFixed(2))
  document.getElementById('input_BOM_detLossRatio').value = formatAngka(Number(lossratio ?? 0).toFixed(2))

  $('#addDetailBom').show()
}

// Hapus Detail BOM -- sp_BOM Choice='D', dikunci KodeBOM+Urut+KodePrs (lihat body SP dari
// senior -- field lain diabaikan untuk Choice='D', tidak perlu dikirim benar).
function buttonHapusDetailBom (kodebom, kodeprs, urut, namabrg) {
  alertify.confirm('Hapus Detail BOM', 'Apakah yakin ingin menghapus "' + namabrg + '" dari BOM ini?',
    function () {
      $.ajax({
        url: "{!! url('produksibomspbom') !!}",
        type: "post",
        data: {
          _token: $('#_token').val(),
          choice: 'D',
          kodebom: kodebom,
          kodeprs: kodeprs,
          urut: urut,
        },
        success: function () {
          alertify.success('Detail BOM berhasil dihapus')
          pbRefreshTabelDetailBom(kodeprs)
          pbRefreshDetailJikaAktif()
        },
        error: function () {
          alertify.error('Gagal menghapus Detail BOM')
        }
      });
    }, 
    function () {
          alertify.error('Gagal menghapus Detail BOM')
        }
  );
}

// Picker Kode Barang (DBBARANG, 18rb+ baris) -- diinisialisasi SEKALI sebagai DataTable
// serverSide:true (pola sama dengan #tabel di produksibom.blade.php/loadAll(), tapi tanpa
// pager custom MasterList -- modal kecil ini cukup pagination bawaan DataTables), bukan muat
// semua baris lalu paging di client seperti pickerKasInit (tidak kuat untuk 18rb baris).
// Kotak cari sendiri (#input_cari_kodebarangdet, cari-ketik-Enter) dipakai menggantikan kotak
// cari bawaan DataTables (dom 'ltip' -- 'f' dilepas) supaya tidak nembak request tiap
// ketikan. initialFilter (opsional) -- dipakai handler Enter di #input_BOM_detKodeBarang di
// bawah, supaya kode barang yang baru diketik user langsung jadi kata kunci pencarian di sini.
let tabelKodeBarangDetDT = null

function buttonKodeBarangDetail (initialFilter) {
  document.getElementById('input_cari_kodebarangdet').value = initialFilter || ''

  if (!tabelKodeBarangDetDT) {
    document.getElementById('tabel_data_kodebarangdet').innerHTML = ''
    tabelKodeBarangDetDT = $('#tabel_kodebarangdet').DataTable({
      serverSide: true,
      processing: true,
      searching: true,
      ordering: false,
      dom: 'ltip',
      ajax: {
        url: "{!! url('produksibomlistkodebarang') !!}",
        type: 'GET',
      },
      columns: [
        { data: 'KODEBRG' },
        { data: 'NAMABRG' },
      ],
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
      pageLength: 10,
      language: {
        lengthMenu: 'Tampilkan _MENU_',
        emptyTable: 'Tidak ada data',
        zeroRecords: 'Tidak ada data yang cocok dengan pencarian',
        processing: 'Memuat...'
      },
      // createdRow pakai data asli lewat closure (bukan interpolasi string ke onclick="...")
      // supaya kode/nama barang yang mengandung tanda kutip tidak bisa merusak atribut HTML --
      // lihat catatan serupa di buttonHapusDetailBom().
      createdRow: function (row, data) {
        $(row).addClass('pick-row').on('click', function () {
          buttonPilihKodeBarangDetail(data.KODEBRG, data.NAMABRG)
        })
      }
    })
  }

  tabelKodeBarangDetDT.search(initialFilter || '').draw()
  $('#formKodeBarangDetail').modal('show')
}

$(document).on('keyup', '#input_cari_kodebarangdet', function (e) {
  if (e.key === 'Enter' && tabelKodeBarangDetDT) {
    tabelKodeBarangDetDT.search($(this).val()).draw()
  }
});

// Enter di input_BOM_detKodeBarang (sekarang bisa diketik langsung, tidak disabled lagi --
// lihat markup di atas) -- buka picker Kode Barang dengan teks yang baru diketik user
// langsung jadi kata kunci pencarian, tidak perlu klik ikon cari dulu lalu ketik ulang.
$(document).on('keyup', '#input_BOM_detKodeBarang', function (e) {
  if (e.key === 'Enter') { buttonKodeBarangDetail($(this).val()) }
});

function buttonPilihKodeBarangDetail (kodebrg, namabrg) {
  document.getElementById('input_BOM_detKodeBarang').value = kodebrg
  document.getElementById('input_BOM_namaBarang').value = namabrg
  $('#formKodeBarangDetail').modal('hide')
}

// Simpan Tambah/Edit Detail BOM -- sp_BOM Choice='I' (input_BOM_detUrut kosong, dari
// buttonDetailBom()) atau Choice='U' (terisi, dari buttonEditDetailBom()). @Urut dan @Qnt
// yang dikirim untuk Choice='I' cuma pengisi parameter wajib -- sp_BOM sendiri menghitung
// ulang keduanya (MAX(Urut)+1 per KodeBOM+KodePrs, lalu Qnt=Numerator/Denominator); untuk
// Choice='U', @Urut WAJIB benar (dipakai SP buat kunci baris yang diupdate: KodeBOM+Urut+
// KodePrs) tapi @Qnt tetap dihitung ulang di dalam SP, bukan dari nilai yang dikirim. Field
// lain yang belum ada input-nya di form ini (@PlaceCD, @NoSat, @Satuan, @Isi) diisi default
// di controller sesuai instruksi senior -- lihat ProduksiBomController::spBOM() (diabaikan
// SP untuk Choice='U', SP cuma update KodeBrg/Qnt/Numerator/Denominator/LossRatio/PlaceCD).
// Kolom Harga SENGAJA tidak dikirim, sp_BOM tidak punya parameter harga.
function submitAddDetailBom () {
  let urut = $('#input_BOM_detUrut').val()
  let kodeprs = $('#input_BOM_detKodeProses').val()
  let kodebrg = $('#input_BOM_detKodeBarang').val()
  let choice = urut ? 'U' : 'I'

  if (!kodeprs) {
    alertify.warning('Klik salah satu baris di Daftar Proses terlebih dahulu')
    return
  }
  if (!kodebrg) {
    alertify.warning('Pilih Kode Barang terlebih dahulu')
    return
  }

  $.ajax({
    url: "{!! url('produksibomspbom') !!}",
    type: "post",
    data: {
      _token: $('#_token').val(),
      choice: choice,
      kodebom: $('#input_BOM_kodeBOM').val(),
      kodeprs: kodeprs,
      urut: urut,
      kodebrg: kodebrg,
      numerator: unformatAngka($('#input_BOM_detNumerator').val()),
      denominator: unformatAngka($('#input_BOM_detDenominator').val()),
      lossratio: unformatAngka($('#input_BOM_detLossRatio').val()),
    },
    success: function () {
      alertify.success(choice === 'U' ? 'Detail BOM berhasil diubah' : 'Detail BOM berhasil ditambahkan')
      // $('.showhide').hide()
      pbRefreshTabelDetailBom(kodeprs)
      pbRefreshDetailJikaAktif()
    },
    error: function () {
      alertify.error('Gagal menyimpan Detail BOM')
    }
  });
}

// Isi ulang grid "Detail BOM" modal (#tabel_data_detailbom) -- dipanggil buttonEditBom()
// (produksibom.blade.php). kodeprs (opsional): kalau diisi -- baris Daftar Proses modal lagi
// dipilih (pbModalPilihProses()) -- grid difilter ke proses itu saja lewat produksibomlistdetail
// (sama endpoint dengan panel "Detail BOM" halaman utama). Kalau kosong (belum ada proses
// dipilih, mis. modal baru dibuka lewat buttonEditBom()), tampilkan semua detail lintas
// proses lewat produksibomlistdetailbybom, sebagai overview awal saja.
// DBBOMDET tidak punya kolom harga, jadi kolom Harga diisi '-' (placeholder, belum ada
// sumber datanya).
function pbRefreshTabelDetailBom (kodeprs) {
  let kodebom = $('#input_BOM_kodeBOM').val()
  if (!kodebom) { return }

  $.ajax({
    url: kodeprs ? "{!! url('produksibomlistdetail') !!}" : "{!! url('produksibomlistdetailbybom') !!}",
    type: "get",
    data: kodeprs ? { kodebom, kodeprs } : { kodebom },
    success: function (res) {
      let rowTable = ''
      res.forEach((item) => {
        rowTable += `
          <tr>
            <td>${item.KodeBrg}</td>
            <td>${item.NAMABRG ?? ''}</td>
            <td>${item.SAT1 ?? ''}</td>
            <td>${formatAngka(Number(item.Numerator ?? 0).toFixed(2))}</td>
            <td>${formatAngka(Number(item.Denominator ?? 0).toFixed(2))}</td>
            <td>${formatAngka(Number(item.LossRatio ?? 0).toFixed(2))}</td>
            <td class="text-center">
              <button class="btn-action-sm btn-action-success" type="button" onclick="buttonEditDetailBom('${kodebom}', '${item.KodePrs}', ${item.Urut}, '${item.KodeBrg}', ${item.Numerator ?? 0}, ${item.Denominator ?? 0}, ${item.LossRatio ?? 0})"><i class="bi bi-pen"></i></button>
              <button class="btn-action-sm btn-action-danger" type="button" onclick="buttonHapusDetailBom('${kodebom}', '${item.KodePrs}', ${item.Urut}, '${String(item.NAMABRG ?? item.KodeBrg ?? '').replace(/'/g, "\\'").replace(/"/g, '&quot;')}')"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
        `
      });
      if (!res.length) {
        rowTable = '<tr><td colspan="8" class="text-center text-muted">Belum ada detail</td></tr>'
      }
      document.getElementById('tabel_data_detailbom').innerHTML = rowTable
    },
    error: function () {
      alertify.error('Gagal memuat Detail BOM')
    }
  });
}

function closeShowHideSubGroup () {
  $('.showhide').hide();
}
</script>