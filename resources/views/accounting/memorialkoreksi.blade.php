@extends('newmasterTest')
@section('page-title', 'Memorial/Koreksi')

@section('css')

{{-- Header tabel interaktif (geser kolom + roda gigi sembunyikan kolom + bar kolom
     tersembunyi + modal filter) - sama seperti menu purchasing / pengajuandpp. --}}
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
{{-- Scrollbar auto-hide: tidak terlihat sampai kursor ada di area yang bisa di-scroll --}}
<link rel="stylesheet" href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">

<!-- Blok gaya lama pencarian tabel Bootstrap (#tabel_filter dkk) sudah tidak dipakai -
     kotak cari sekarang #mkSearch di toolbar. Dibiarkan sebagai komentar, tidak
     dihapus, supaya jejak halaman lama masih terbaca.
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


end tampilan search bar 1

tampilan search bar 2

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
-->
<style>
  .btn .bi-plus {
    font-size: 1.5rem;
    line-height: 0;
    vertical-align: middle;
  }
/* Jarak kartu ke bar atas. Layout memberi .content padding 28px; halaman purchasing
   memakai 12px, dan halaman ini mengikutinya supaya seragam. */
#content { padding-top: 12px; }

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

{{-- ==========================================================================
     Tampilan baru daftar DPP - menyalin pola menu purchasing
     (pembelianpermintaandebetnote.blade.php).

     Layout accounting.newmaster tidak mendefinisikan .data-table seperti
     newmasterTest, jadi gaya dasar tabelnya ditulis di sini supaya halaman ini
     tidak bergantung pada layout. Semua var CSS diberi nilai cadangan.
     ========================================================================== --}}
<style>
/* Rule .card global di sebagian layout (flex + align-items:center + efek melayang
   saat hover) diperuntukkan kartu menu dashboard, bukan kartu berisi tabel. */
#page1 .card {
  display: block !important;
  align-items: stretch !important;
  padding: 0 !important;
  text-align: left !important;
  cursor: default !important;
}

#page1 .card:hover {
  transform: none !important;
  box-shadow: none !important;
  border-color: var(--border, #e5e7eb) !important;
}

/* ---------- Gaya dasar tabel daftar ---------- */
.data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

.data-table thead th {
  background: #f9fafb;
  padding: 11px 16px;
  text-align: left;
  font-weight: 600;
  font-size: 12px;
  color: var(--text-muted, #6b7280);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  border-bottom: 1px solid var(--border, #e5e7eb);
}

.data-table tbody td {
  padding: 12px 16px;
  border-bottom: 1px solid #f3f4f6;
  color: var(--text-main, #1f2937);
}

.data-table tbody tr:last-child td { border-bottom: none; }
.data-table tbody tr:hover td { background: #f9fafb; }

/* DataTables (autoWidth bawaan = true) selalu menulis hasil pengukurannya sebagai
   inline style pada <table>, yang mengalahkan `.data-table { width: 100% }`.
   Dipakai min-width, BUKAN width. */
#tabel { min-width: 100%; }

/* ---------- Kolom Aksi - tombol bulat kecil warna pastel ---------- */
#tabel td:first-child:not([colspan]) { vertical-align: middle; }

#tabel td:first-child .po-aksi-wrap {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
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

#tabel td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabel td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
#tabel td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
#tabel td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
#tabel td:first-child .btn-info    { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

/* Tombol di kolom Aksi baru muncul saat barisnya di-hover. */
table.data-table.po-aksi-hover tbody td:first-child .btn {
  visibility: hidden;
  opacity: 0;
  transition: opacity .12s ease;
}
table.data-table.po-aksi-hover tbody tr:hover td:first-child .btn {
  visibility: visible;
  opacity: 1;
}

/* ---------- Dropdown "Tampilkan" (jumlah baris per halaman) di toolbar ---------- */
.po-len-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--rt-card, #fff);
  border: 1.5px solid var(--rt-border, #E7E8F0);
  border-radius: 8px;
  padding: 5px 12px;
}

.po-len-wrap label {
  margin: 0;
  font-size: 11.5px;
  font-weight: 700;
  color: var(--rt-ink-soft, #6B7180);
  text-transform: uppercase;
  letter-spacing: .05em;
  white-space: nowrap;
}

.po-len-inp {
  border: none;
  background: transparent;
  font-size: 13px;
  font-weight: 700;
  color: var(--rt-ink, #1D2130);
  outline: none;
  cursor: pointer;
  padding: 2px 20px 2px 0;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>");
  background-repeat: no-repeat;
  background-position: right center;
}

/* ---------- Tombol utama halaman (Tambah DPP / Close / Otorisasi) ---------- */
.btn-dpp-utama {
  height: 36px;
  border-radius: 8px;
  font-size: 0.78rem;
  font-weight: 600;
  padding: 0 16px;
  background-color: #e8edff;
  border: 1px solid #cfdcff;
  color: #2563eb;
  box-shadow: none;
}
.btn-dpp-utama:hover,
.btn-dpp-utama:focus { background-color: #dce6ff; border-color: #b9c9ff; color: #1d4ed8; }
.btn-dpp-utama:active { background-color: #cfdcff !important; border-color: #a8bdff !important; color: #1d4ed8 !important; }

/* Tombol Close di page2/page3. Warnanya mengikuti menu purchasing
   (pembelianpermintaanagen / pembelianpermintaannonagen), yang tombol Closenya memakai
   .btn-danger: saat diam merah muda dari layout (#fef2f2 / #fecaca / #b91c1c), saat
   hover jadi merah pekat khas btn-danger Bootstrap (#bb2d3b, teks putih). */
.btn-dpp-tutup {
  height: 30px;
  border-radius: 20px !important;
  font-size: 0.75rem !important;
  font-weight: 600;
  padding: 4px 12px !important;
  text-transform: uppercase;
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  color: #b91c1c;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.btn-dpp-tutup:hover,
.btn-dpp-tutup:focus { background-color: #bb2d3b; border-color: #b02a37; color: #fff; }
.btn-dpp-tutup:active { background-color: #b02a37 !important; border-color: #a52834 !important; color: #fff !important; }

/* Tombol chip (latar tint muda + teks berwarna) untuk Submit / Batal di form. */
.btn-chip-biru {
  background-color: #e8edff;
  border-color: #cfdcff;
  color: #2563eb;
}
.btn-chip-biru:hover,
.btn-chip-biru:focus { background-color: #dce6ff; border-color: #b9c9ff; color: #1d4ed8; }
.btn-chip-biru:active { background-color: #cfdcff !important; border-color: #a8bdff !important; color: #1d4ed8 !important; }

.btn-batal-add {
  background-color: #f1f3f5;
  border-color: #dee2e6;
  color: #495057;
}
.btn-batal-add:hover,
.btn-batal-add:focus { background-color: #e9ecef; border-color: #ced4da; color: #343a40; }
.btn-batal-add:active { background-color: #dee2e6 !important; border-color: #ced4da !important; color: #343a40 !important; }

/* ---------- Tombol browsing (kaca pembesar) di sebelah input ---------- */
.btn-browsing {
  height: 38px;
  width: 40px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 0 6px 6px 0;
  background-color: #e8edff;
  border: 1px solid #cfdcff;
  color: #2563eb;
  font-size: 15px;
  box-shadow: none;
}
.btn-browsing:hover,
.btn-browsing:focus { background-color: #dce6ff; border-color: #b9c9ff; color: #1d4ed8; }
.btn-browsing:disabled { background-color: #f1f3f5; border-color: #dee2e6; color: #adb5bd; }

/* ---------- #addTable (form Add) dan #detailTable (form Detail) ---------- */
#addTable thead th,
#detailTable thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee;
  border-top: none;
}

#addTable tbody tr:nth-of-type(odd),
#detailTable tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#addTable tbody tr:hover,
#detailTable tbody tr:hover { background-color: #f5f3ff; }

#addTable td .btn {
  width: 30px; height: 30px; padding: 0;
  display: inline-flex; align-items: center; justify-content: center;
  border-radius: 7px; font-size: 13px;
  border: 1px solid transparent; box-shadow: none; transition: all .12s ease;
}
#addTable td .btn:hover { filter: brightness(0.97); transform: translateY(-1px); }
#addTable td .btn-success { color:#16a34a; border-color:#cdebd7; background:#e7f7ed; }
#addTable td .btn-danger  { color:#dc2626; border-color:#f7cfcf; background:#fdeaea; }

/* ---------- Modal lookup DPP (#form) - baris diklik langsung ---------- */
#tabel_add_list_modal thead th,
#tabel_add_list_perkiraan thead th,
#tabel_add_list_titipan thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee !important;
  border-top: none !important;
}

#tabel_add_list_modal tbody td,
#tabel_add_list_perkiraan tbody td,
#tabel_add_list_titipan tbody td {
  border-top: none !important;
  border-bottom: 1px solid #f1f3f5 !important;
  font-size: 13px;
  vertical-align: middle;
}

#tabel_add_list_modal tbody tr.pick-row,
#tabel_add_list_perkiraan tbody tr.pick-row,
#tabel_add_list_titipan tbody tr.pick-row {
  cursor: pointer;
  transition: background-color .12s;
}
#tabel_add_list_modal tbody tr.pick-row:hover td,
#tabel_add_list_perkiraan tbody tr.pick-row:hover td,
#tabel_add_list_titipan tbody tr.pick-row:hover td { background-color: #eef2ff; }
#tabel_add_list_modal tbody tr.pick-row.row-terpilih td,
#tabel_add_list_perkiraan tbody tr.pick-row.row-terpilih td,
#tabel_add_list_titipan tbody tr.pick-row.row-terpilih td { background-color: #e8edff; }

/* Modal browse Perkiraan (#form) dipakai bareng pane Valas yang modal-dialog-nya
   modal-xl - saat pane Perkiraan yang tampil, sempitkan ke ukuran modal-lg supaya
   sama seperti modal Perkiraan di pelunasanpiutangdpp. Kelas ditambah/dilepas lewat
   JS (lihat buttonAddListPerkiraan() / buttonAddListBatal()). */
#form .modal-dialog.mk-dialog-perkiraan { max-width: 800px; }

/* Blok No Titipan berada di col-md-6 kedua, jadi mulainya di titik 50% padahal isi blok
   Debet sudah habis di 33,3%. Ditarik 2 kolom grid ke kiri supaya rapat dengan Debet.
   Hanya di layar >= md; di bawah itu kolomnya menumpuk dan tidak boleh digeser. */
@media (min-width: 768px) {
  #rowNoTitipan { margin-left: -16.666667%; }
}

/* Kotak cari di modal Perkiraan - meniru .cari-modal-pdpp di pelunasanpiutangdpp. */
.cari-modal-mk {
  width: 260px;
  max-width: 100%;
  font-size: 13px;
  padding: 7px 10px 7px 32px;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  outline: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: 10px center;
}
.cari-modal-mk:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px #e8edff;
}

/* Kolom "v" (pilih baris): header dan kotak centang sama-sama rata tengah.
   .form-check bawaan Bootstrap 5 memberi padding-left pada wadah dan float:left
   pada inputnya, jadi text-center saja tidak cukup - floatnya harus dimatikan. */
#tabel_add_list_modal th.kolom-pilih,
#tabel_add_list_modal tbody td.kolom-pilih {
  text-align: center;
}
#tabel_add_list_modal tbody td.kolom-pilih .form-check-input {
  float: none;
  margin: 0;
  vertical-align: middle;
}

#input_search_pengajuan_dpp {
  width: 260px;
  max-width: 100%;
  font-size: 13px;
  padding: 7px 10px 7px 32px;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  outline: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: 10px center;
}
#input_search_pengajuan_dpp:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px #e8edff;
}

/* Modal filter (.rt-filter) memakai tombol close Bootstrap bawaan - kembalikan
   gayanya di dalam .rt-filter saja supaya tidak tertimpa aturan halaman. */
.rt-filter .modal-header .close {
  opacity: .5;
  text-shadow: 0 1px 0 #fff;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
  color: #000;
  background: transparent;
  border: 0;
  padding: 1rem;
  margin: -1rem -1rem -1rem auto;
}
.rt-filter .modal-header .close:hover { opacity: .75; }

/* ---------- Tombol aksi di dalam form (page2/page3) dan modal ----------
   Sebagian tombol lama membawa inline style yang hanya mengatur UKURAN
   (height/border-radius/font). Warnanya tidak inline, jadi cukup ditimpa di sini;
   border-radius perlu !important karena inline style menang atas CSS biasa.
   Tombol ikon di kolom Aksi tabel daftar ada di #page1 sehingga tidak terkena. */
#page2 .btn-primary,
#page3 .btn-primary,
.modal-body .btn-primary,
.modal-footer .btn-primary {
  background-color: #e8edff;
  border-color: #cfdcff;
  color: #2563eb;
  border-radius: 8px !important;
  text-transform: none !important;
  box-shadow: none;
}

#page2 .btn-primary:hover,
#page3 .btn-primary:hover,
.modal-body .btn-primary:hover,
.modal-footer .btn-primary:hover {
  background-color: #dce6ff;
  border-color: #b9c9ff;
  color: #1d4ed8;
}

#page2 .btn-secondary,
#page3 .btn-secondary,
.modal-body .btn-secondary,
.modal-footer .btn-secondary {
  background-color: #f1f3f5;
  border-color: #dee2e6;
  color: #495057;
  border-radius: 8px !important;
  text-transform: none !important;
  box-shadow: none;
}

#page2 .btn-secondary:hover,
#page3 .btn-secondary:hover,
.modal-body .btn-secondary:hover,
.modal-footer .btn-secondary:hover {
  background-color: #e9ecef;
  border-color: #ced4da;
  color: #343a40;
}

/* Tombol browsing tetap menyatu dengan input di sebelah kirinya. */
#page2 .input-group .btn-browsing,
#page3 .input-group .btn-browsing,
.modal-body .input-group .btn-browsing {
  border-radius: 0 6px 6px 0 !important;
}
/* Samakan tinggi kaca pembesar dengan input di sampingnya. */
#page2 .input-group .form-control,
#page2 .input-group .btn-browsing {
  height: 38px;
}

/* ---------- Tumpukan modal: hanya modal teratas yang terlihat ----------
   Pola sama dengan penerimaandpp/pelunasanpiutangdpp - tidak ada main z-index,
   modal induk disembunyikan lewat class supaya isian & handler-nya tidak terpicu. */
.modal.mk-modal-tertimbun { display: none !important; }
.modal-backdrop.mk-backdrop-tertimbun { display: none !important; }

/* ---------- Modal Kartu Piutang ---------- */
#formMkKartuPT .mk-kartu-cust {
  border-bottom: 1px solid #dee2e6;
  padding-bottom: 8px;
  margin-bottom: 12px;
}
#formMkKartuPT .mk-kartu-cust .kode {
  font-size: 1.05rem;
  font-weight: 600;
  color: #212529;
}
#formMkKartuPT .mk-kartu-cust .nama {
  color: #6c757d;
}

/* Tombol tambah baris faktur: plus biru soft, ukuran besar. */
.btn-mk-tambah {
  background-color: #eaf1ff;
  border: 1px solid #c7dbff;
  color: #1d4ed8;
  border-radius: 8px !important;
  padding: 6px 14px;
  font-size: 1.25rem;
  line-height: 1;
  box-shadow: none;
}
.btn-mk-tambah:hover {
  background-color: #dce6ff;
  border-color: #b9c9ff;
  color: #1d4ed8;
}
.btn-mk-tambah:disabled {
  opacity: .5;
}

/* Ringkasan Total / Dibayar / Sisa di kaki tabel kartu. */
#formMkKartuPT .mk-ringkas {
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 10px 12px;
}
#formMkKartuPT .mk-ringkas label {
  margin-bottom: 0;
  color: #6c757d;
  font-size: .85rem;
}
#formMkKartuPT .mk-ringkas .nilai {
  font-weight: 600;
  text-align: right;
}

/* Panel form tambah faktur di dalam modal kartu. */
#mkKartuFormTambah {
  border-top: 1px solid #dee2e6;
  margin-top: 14px;
  padding-top: 14px;
}
#formMkKartuPT .mk-baris-total td {
  font-weight: 600;
  background-color: #f8f9fa;
}
</style>
@endsection


@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid mainpage">

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
    <div class="card-body" style="padding:0;">

      <div class="po-toolbar">
        <div class="po-filter-wrap">
          <label>Periode</label>
          <input type="date" class="po-filter-inp" id="mkTglAwal" value="{!! $mkTglAwal !!}">
          <span class="po-filter-sep">s/d</span>
          <input type="date" class="po-filter-inp" id="mkTglAkhir" value="{!! $mkTglAkhir !!}">
        </div>
        <input type="search" id="mkSearch" class="po-search-inp" placeholder="Cari data">
        {{-- Jumlah baris per halaman - lihat mkIkatPanjangHalaman(). --}}
        <div class="po-len-wrap">
          <label for="mkLen">Tampilkan</label>
          <select id="mkLen" class="po-len-inp">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">Semua</option>
          </select>
        </div>
        <button class="po-btn-filter" type="button" id="mkBtnFilter" onclick="$('#modalFilterMk').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
        <div class="po-toolbar-act">
          <button type="button" class="btn btn-dpp-utama" onclick="buttonAdd()">Tambah</button>
        </div>
      </div>

      {{-- #rtBar diisi lewat JS oleh ReportTable.init() - lihat mkInitReportTableSekali(). --}}
      <div id="rtBar"></div>

      <table id="tabel" class="data-table po-aksi-hover">
        <thead id="tabel_header" class="text-center">
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
            <th style="padding: 4px 12px;" scope="col">No Bukti</th>
            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
            <th style="padding: 4px 12px;" scope="col">Trans</th>
            <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
            <th style="padding: 4px 12px;" scope="col">Keterangan</th>
            <th style="padding: 4px 12px;" scope="col">Jumlah Rp</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left">
          {{-- Baris digambar renderTabelMk() lewat JS, supaya susunan kolom hasil
               geser/sembunyi selalu konsisten dengan hasil render ulang. --}}
        </tbody>
      </table>

      <div class="po-rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
        untuk menyembunyikan kolom.
      </div>

    </div>
  </div>

</div>
</div>

<!-- modal filter otorisasi -->
<div class="modal fade rt-filter" id="modalFilterMk">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter Memorial/Koreksi
          <span class="rt-active-badge" id="mkFilterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterMk').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="mkModalOtorisasi">Otorisasi</label>
              <select class="rt-native" id="mkModalOtorisasi">
                <option value="SEMUA">Semua</option>
                <option value="Sudah">Sudah</option>
                <option value="Belum">Belum</option>
              </select>
            </div>
            <div>
              <label class="rt-field-label" for="mkModalTrans">Trans</label>
              <select class="rt-native" id="mkModalTrans">
                <option value="SEMUA">Semua</option>
                <option value="BMM">BMM</option>
                <option value="BJK">BJK</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="mkResetFilter()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilterMk').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="mkTerapkanFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- end modal filter otorisasi -->

<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row">
    <div class="col-8 text-left">
      <h2></h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-dpp-tutup" onclick="buttonCloseForm()">Close</button>
    </div>
  </div>

  <div id= "formAdd" class="">



  <div id="" class="">
  <div class="">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid">
      <input type="hidden" name="noUrut" id="input_add_nourut" value="" />

      <div class="row g-2 align-items-center">

        <div class="col-md-3 d-flex align-items-center">
          <label class="mb-0 me-3 text-nowrap" style="flex:0 0 100px">Transaksi</label>
          <select id="input_add_transaksi" class="form-control" aria-label=".form-select-lg example" onChange="onChangeTransaksi()" style="flex:0 0 150px">
            <option value='BMM' selected>BMM</option>
            <option value='BJK' >BJK</option>
          </select>
        </div>

        <div class="col-md-4 d-flex align-items-center">
          <label class="mb-0 me-3 text-nowrap" style="flex:0 0 100px">No Bukti</label>
          <input type="hidden" class="form-control" id="input_add_nourut" placeholder="" disabled>
          <input type="text" class="form-control" id="input_add_nobukti" placeholder="No Bukti" disabled style="flex:0 0 230px">
        </div>

      </div>

      <div class="row g-2 align-items-center mt-2">

        <div class="col-md-3 d-flex align-items-center">
          <label class="mb-0 me-3 text-nowrap" style="flex:0 0 100px">Tanggal</label>
          <input type="date" class="form-control text-center" id="input_add_tanggal" placeholder="" style="flex:0 0 150px">
        </div>

        <div class="col-md-6 d-flex align-items-center">
          <label class="mb-0 me-3 text-nowrap" style="flex:0 0 100px">Note</label>
          <input type="text" class="form-control" id="input_add_note" placeholder="" style="flex:0 0 390px">
        </div>

      </div>

      </div>



<div class="container-fluid">
  <hr/>

</div>



  <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

        <table id="addTable" class="data-table">
          <thead class="text-center">
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
  <button id="buttonAddItem" type="button" class="btn btn-chip-biru" onclick="buttonAddItem()">Tambah</button>
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

              <button id="buttonAddListDevisi" type="button" onclick="buttonAddListDevisi()" class="btn btn-browsing"><i class="bi bi-search"></i></button>

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
              {{-- Valas jadi dropdown (bukan lagi picker + tombol +), disamakan dengan
                   purchaseOrder.blade.php. Modal pencarian lama (#modalAddListValas) dan
                   tombol +-nya dibiarkan di bawah sebagai komentar, tidak dihapus. --}}
              <select class="form-control" id="AddAddValas" onchange="onChangeValasAdd()"></select>
              <!-- <input id="AddAddValas" type="text" class="form-control" value="IDR" disabled>
              <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" >+</button> -->

            </div>
          </div>

          <div class="col-md-1">
            <div class="form-group">
            <label>Kurs</label>
          </div>
          </div>

          <div class="col-md-2">
            <div class="input-group form-group">
              <input id="AddAddKurs" type="text"  value="1.00" class="text-right form-control" disabled>

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
          <input id="AddAddJumlah" type="text" value="0.00" class="text-right form-control" onblur="formatAngkaInput(this); mkAturTombolBrowse()" oninput="formatAngkaKetik(this); mkAturTombolBrowse()">

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
          <button id="buttonAddListDebet" type="button" onclick="buttonAddListPerkiraan('Debet')" class="btn btn-browsing"><i class="bi bi-search"></i></button>

        </div>
      </div>

      <div class="col-md-3">
        <div class="input-group form-group">
          <input id="AddAddKeteranganDebet" type="text" class="form-control" disabled>

        </div>
      </div>

    </div>
  </div>

  {{-- No Titipan: hanya muncul kalau Debet = 113400 (Titipan Customer). Disembunyikan &
       direset lewat mkResetTitipan() - lihat buttonAddPickPerkiraan()/cleanFormAddAdd(). --}}
  <div class="col-md-6" id="rowNoTitipan" style="display:none">
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
        <label>No Titipan</label>
      </div>
      </div>
      <div class="col-md-6">
        <div class="input-group form-group">
          <input id="AddAddNoTitipan" type="text" class="form-control" disabled>
          <input id="AddAddUrutTitipan" type="hidden" disabled>
          <input id="AddAddCustsuppTitipan" type="hidden" disabled>
          <input id="AddAddSisaTitipan" type="hidden" disabled>
          <button id="buttonAddListTitipan" type="button" onclick="buttonAddListTitipan()" class="btn btn-browsing"><i class="bi bi-search"></i></button>
        </div>
      </div>
    </div>
  </div>

  {{-- Customer piutang: hanya muncul kalau Debet = perkiraan ber-Kode 'PT'. Disembunyikan &
       direset lewat mkResetPT() - lihat buttonAddPickPerkiraan()/cleanFormAddAdd(). Tombol
       kaca pembesar membuka lagi rantai Customer -> Kartu Piutang. --}}
  <div class="col-md-6" id="rowCustomerPT" style="display:none">
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
        <label>Customer</label>
      </div>
      </div>
      <div class="col-md-3">
        <div class="input-group form-group">
          <input id="AddAddCustsuppPT" type="text" class="form-control" disabled>
          <input id="AddAddKodePT" type="hidden" disabled>
          <input id="AddAddNamaCustPT" type="hidden" disabled>
          <input id="AddAddNoMskPT" type="hidden" disabled>
          <button id="buttonAddListCustomerPT" type="button" onclick="buttonAddListCustomerPT()" class="btn btn-browsing"><i class="bi bi-search"></i></button>
        </div>
      </div>
      <div class="col-md-3">
        <div class="input-group form-group">
          <input id="AddAddNamaCustPTView" type="text" class="form-control" disabled>
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
          <button id="buttonAddListKredit" type="button" onclick="buttonAddListPerkiraan('Kredit')" class="btn btn-browsing"><i class="bi bi-search"></i></button>

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
      <button type="button" class="btn btn-batal-add" onclick="buttonAddBatal()">Batal</button>

      <button id="buttonSubmitAdd" type="button" onclick="submitAdd()" class="btn btn-chip-biru showhideitem">Simpan</button>

      <button id="buttonSubmitEdit" type="button" onclick="submitEdit()" class="btn btn-chip-biru showhideitem">Simpan</button>


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

    <div class="row">
      <div class="col-8 text-left">
        <h2></h2>
      </div>
      <div class="col-4 text-right">
        <button type="button" class="btn btn-dpp-tutup" onclick="buttonCloseForm()">Close</button>
      </div>
    </div>

    <div id= "" class="">

    <div id="" class="">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_add_nourut" value="" />

        <div class="row g-2 align-items-center">

          <div class="col-md-3 d-flex align-items-center">
            <label class="mb-0 me-3 text-nowrap" style="flex:0 0 100px">Transaksi</label>
            <select id="input_detail_transaksi" class="form-control" aria-label=".form-select-lg example" disabled style="flex:0 0 150px">
              <option value='BMM' selected>BMM</option>
              <option value='BJK' >BJK</option>
            </select>
          </div>

          <div class="col-md-4 d-flex align-items-center">
            <label class="mb-0 me-3 text-nowrap" style="flex:0 0 100px">No Bukti</label>
            <input type="hidden" class="form-control" id="input_detail_nourut" placeholder="" disabled>
            <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Bukti" disabled style="flex:0 0 230px">
          </div>

        </div>

        <div class="row g-2 align-items-center mt-2">

          <div class="col-md-3 d-flex align-items-center">
            <label class="mb-0 me-3 text-nowrap" style="flex:0 0 100px">Tanggal</label>
            <input type="date" class="form-control text-center" id="input_detail_tanggal" placeholder="" disabled style="flex:0 0 150px">
          </div>

          <div class="col-md-6 d-flex align-items-center">
            <label class="mb-0 me-3 text-nowrap" style="flex:0 0 100px">Note</label>
            <input type="text" class="form-control" id="input_detail_note" placeholder="" disabled style="flex:0 0 390px">
          </div>

        </div>

        </div>

  <div class="container-fluid">
    <hr/>

  </div>

    <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

          <table id="detailTable" class="data-table">
            <thead class="text-center">
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
    <button id="buttonOtorisasi" type="button" class="btn btn-dpp-utama" onclick="submitOtorisasi()">Otorisasi</button>
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
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document">
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
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

          {{-- Kotak pencarian tabel modal - diikat lewat mkIkatSearchPerkiraan(). --}}
          <div class="row mb-2">
            <div class="col-12 d-flex justify-content-end">
              <input id="input_search_perkiraan" type="search" class="form-control cari-modal-mk" placeholder="Cari data">
            </div>
          </div>

          <div class="row">
            <div class="col-12" style="overflow:auto; max-height: 400px">
            <!-- <div class="container-fluid"> -->

            {{-- Tidak ada kolom Actions: barisnya diklik langsung untuk memilih
                 (lihat buttonAddPickPerkiraan()). --}}
            <table id="tabel_add_list_perkiraan" class="data-table" style="overflow:auto; " >
              <thead class="text-center" style="position: sticky;
            top: 0;
            z-index: 1;">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                </tr>
              </thead>

              {{-- Sengaja dikosongkan - lihat catatan di modal DPP soal _DT_CellIndex. --}}
              <tbody id="tabel_data_add_list_perkiraan" class="text-left" >
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

      <div id= "modalAddListTitipan" class="showhidemodalbodyadd">
      <div class="modal-header">

          <h5 class="modal-title" id="">No Titipan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >

          {{-- Kotak pencarian tabel modal - diikat lewat mkIkatSearchTitipan(). --}}
          <div class="row mb-2">
            <div class="col-12 d-flex justify-content-end">
              <input id="input_search_titipan" type="search" class="form-control cari-modal-mk" placeholder="Cari data">
            </div>
          </div>

          <div class="row">
            <div class="col-12" style="overflow:auto; max-height: 400px">

            {{-- Tidak ada kolom Actions: barisnya diklik langsung untuk memilih
                 (lihat buttonAddPickTitipan()). --}}
            <table id="tabel_add_list_titipan" class="data-table" style="overflow:auto; " >
              <thead class="text-center" style="position: sticky;
            top: 0;
            z-index: 1;">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">No Bukti</th>
                  <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                  <th style="padding: 4px 12px;" scope="col">Customer</th>
                  <th style="padding: 4px 12px;" scope="col">Keterangan</th>
                  <th style="padding: 4px 12px;" scope="col">Jumlah Rp</th>
                  <th style="padding: 4px 12px;" scope="col">Sisa</th>
                </tr>
              </thead>

              {{-- Sengaja dikosongkan - lihat catatan di modal DPP soal _DT_CellIndex. --}}
              <tbody id="tabel_data_add_list_titipan" class="text-left" >
              </tbody>

            </table>
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


{{-- ============================================================================
     Modal 2: browse Customer untuk perkiraan piutang usaha (Debet ber-Kode 'PT').
     Dibuka dari buttonAddPickPerkiraan() saat modal #form (pane Perkiraan) masih
     terbuka - jadi ia menjadi modal bertumpuk di atasnya.
     ============================================================================ --}}
<div class="modal fade" id="formMkCustomerPT" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-12 d-flex justify-content-end">
              <input id="input_search_customerpt" type="search" class="form-control cari-modal-mk" placeholder="Cari data">
            </div>
          </div>
          <div class="row">
            <div class="col-12" style="overflow:auto; max-height: 400px">
              {{-- Tidak ada kolom Actions: barisnya diklik langsung untuk memilih. --}}
              <table id="tabel_mk_customerpt" class="data-table" style="overflow:auto;">
                <thead class="text-center" style="position: sticky; top: 0; z-index: 1;">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Kode</th>
                    <th style="padding: 4px 12px;" scope="col">Nama</th>
                    <th style="padding: 4px 12px;" scope="col">Alamat</th>
                    <th style="padding: 4px 12px;" scope="col">Kota</th>
                  </tr>
                </thead>
                {{-- Sengaja dikosongkan - lihat catatan soal _DT_CellIndex. --}}
                <tbody id="tabel_data_mk_customerpt" class="text-left"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>

{{-- ============================================================================
     Modal 3: Kartu Piutang - faktur outstanding customer (informasi, tanpa aksi)
     plus baris faktur yang ditambahkan user. Panel form tambah ada di dalam modal
     ini sendiri (bukan modal ke-4), muncul saat tombol + ditekan.
     ============================================================================ --}}
<div class="modal fade" id="formMkKartuPT" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Penambahan Piutang <span id="mkKartuJudulPerkiraan"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">

          {{-- Identitas customer terpilih --}}
          <div class="row">
            <div class="col-12 mk-kartu-cust">
              <div class="kode" id="mkKartuKodeCust"></div>
              <div class="nama" id="mkKartuNamaCust"></div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-6">
              {{-- Tombol tambah baris faktur baru - sengaja di luar kolom Actions. --}}
              <button type="button" id="mkKartuButtonTambah" class="btn btn-mk-tambah" onclick="mkKartuBukaFormTambah()" title="Tambah faktur">
                <i class="bi bi-plus-lg"></i>
              </button>
            </div>
            <div class="col-6 d-flex justify-content-end">
              <input id="input_search_kartupt" type="search" class="form-control cari-modal-mk" placeholder="Cari data">
            </div>
          </div>

          <div class="row">
            <div class="col-12" style="overflow:auto; max-height: 320px">
              {{-- Kolom Aksi hanya berisi tombol Hapus, dan hanya untuk baris tambahan user
                   (ditandai NoInvoice='TBH'). Baris outstanding bawaan tidak bisa dihapus. --}}
              <table id="tabel_mk_kartupt" class="data-table" style="overflow:auto;">
                <thead class="text-center" style="position: sticky; top: 0; z-index: 1;">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">No. Faktur</th>
                    <th style="padding: 4px 12px;" scope="col">No. Retur</th>
                    <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                    <th style="padding: 4px 12px;" scope="col">Jatuh Tempo</th>
                    <th style="padding: 4px 12px;" scope="col">Debet</th>
                    <th style="padding: 4px 12px;" scope="col">Kredit</th>
                    <th style="padding: 4px 12px;" scope="col">Saldo</th>
                    <th style="padding: 4px 12px;" scope="col">Valas</th>
                    <th style="padding: 4px 12px;" scope="col">Aksi</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_mk_kartupt" class="text-left"></tbody>
              </table>
            </div>
          </div>

          {{-- Baris total kolom, meniru kaki tabel di form lama --}}
          <div class="row mt-1">
            <div class="col-12" style="overflow:auto;">
              <table class="data-table" style="width:100%">
                <tbody>
                  <tr class="mk-baris-total">
                    <td style="width:40%">Total</td>
                    <td class="text-right" id="mkKartuTotalDebet">0,00</td>
                    <td class="text-right" id="mkKartuTotalKredit">0,00</td>
                    <td class="text-right" id="mkKartuTotalSaldo">0,00</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          {{-- Total = Jumlah item memorial, Dibayar = akumulasi baris tambahan, Sisa = selisih.
               Sesuai permintaan: hanya informasi, tidak ada validasi apa pun. --}}
          <div class="row mt-2">
            <div class="col-12">
              <div class="row mk-ringkas">
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-5"><label>Total</label></div>
                    <div class="col-7 nilai" id="mkKartuRingkasTotal">0,00</div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-5"><label>Dibayar</label></div>
                    <div class="col-7 nilai" id="mkKartuRingkasDibayar">0,00</div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-5"><label>Sisa</label></div>
                    <div class="col-7 nilai" id="mkKartuRingkasSisa">0,00</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- Panel tambah faktur (gambar 2) - di dalam modal kartu, bukan modal terpisah. --}}
          <div id="mkKartuFormTambah" style="display:none">
            <div class="row">
              <div class="col-md-6">
                <div class="row" style="margin-top:-6px">
                  <div class="col-md-4"><div class="form-group"><label>No. Faktur</label></div></div>
                  <div class="col-md-8">
                    <div class="input-group form-group">
                      <input id="mkKartuNoFaktur" type="text" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="row" style="margin-top:-10px">
                  <div class="col-md-4"><div class="form-group"><label>Tanggal Bukti</label></div></div>
                  <div class="col-md-8">
                    <div class="input-group form-group">
                      <input id="mkKartuTanggal" type="date" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="row" style="margin-top:-10px">
                  <div class="col-md-4"><div class="form-group"><label>Tanggal Jatuh Tempo</label></div></div>
                  <div class="col-md-8">
                    <div class="input-group form-group">
                      <input id="mkKartuJatuhTempo" type="date" class="form-control">
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="row" style="margin-top:-6px">
                  <div class="col-md-4"><div class="form-group"><label>Valas</label></div></div>
                  <div class="col-md-3">
                    {{-- Valas & Kurs ikut item memorial, dikunci seperti alur lama. --}}
                    <div class="input-group form-group">
                      <input id="mkKartuValas" type="text" class="form-control" disabled>
                    </div>
                  </div>
                  <div class="col-md-2"><div class="form-group"><label>Kurs</label></div></div>
                  <div class="col-md-3">
                    <div class="input-group form-group">
                      <input id="mkKartuKurs" type="text" class="form-control text-right" disabled>
                    </div>
                  </div>
                </div>
                <div class="row" style="margin-top:-10px">
                  <div class="col-md-4"><div class="form-group"><label>Jumlah</label></div></div>
                  <div class="col-md-8">
                    <div class="input-group form-group">
                      <input id="mkKartuJumlah" type="text" class="form-control text-right">
                    </div>
                  </div>
                </div>
                <div class="row" style="margin-top:-10px">
                  <div class="col-md-4"><div class="form-group"><label>Catatan</label></div></div>
                  <div class="col-md-8">
                    <div class="input-group form-group">
                      <input id="mkKartuCatatan" type="text" class="form-control">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="mkKartuTutupFormTambah()">Tutup</button>
                <button type="button" class="btn btn-chip-biru" onclick="mkKartuSimpanTambah()">Simpan</button>
              </div>
            </div>
          </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>






@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">

let listData = []
let itemEdit = {}
let listPerkiraan = []
let listValas = []
let tipeform = ''

// Dipakai mkAturTombolBrowse() - true selama item sedang diedit (lockFormAddAdd(true) dari
// buttonEditItem()), supaya tombol browse Debet/Kredit tetap disabled seperti semula walau
// Jumlah terisi.
let mkItemTerkunci = false

$(document).ready(function(){

    // Tabel #tabel dibangun renderTabelMk() lewat report-table.js (geser & sembunyikan
    // kolom) - tidak lagi diinisialisasi manual di sini.
    muatDropdownValas()
    mkInitReportTableSekali()
    loadAll()

        $("#tabel_add_list_modal").DataTable({
          "lengthChange": false,
            "paging": false ,'order': [[1, 'asc']],
            "searching" : false,
            "columnDefs": [
          {"targets" :[0] , 'orderable' : false}
         // {  "className": "text-center", "targets": [4] },
       ]
      });

        mkIkatSearchPerkiraan()

        $("#tabel_add_list_custsupp").DataTable({
          "lengthChange": false,
            "paging": false ,
      });
});




// NONAKTIF - alur otorisasi lama (buka form detail dulu, lalu klik tombol Otorisasi
// di page3). Sejak otorisasi dilakukan langsung dari baris tabel lewat
// buttonOtorisasiRow(), tidak ada lagi yang memanggil buttonDetail(..., 'otorisasi')
// sehingga tombol #buttonOtorisasi tidak pernah tampil dan fungsi ini tidak terpakai.
// Kode sengaja tidak dihapus supaya alur lama bisa dihidupkan lagi bila diperlukan.
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
      if (res == 1) {
        alertify.success('Berhasil update otorisasi')
      } else {
        alertify.warning('Otorisasi tidak berhasil, silahkan coba lagi')
      }
      loadAll()
      buttonCloseForm()


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

/* ==========================================================================
   Tabel daftar Memorial/Koreksi - pola ReportTable (geser kolom + sembunyikan kolom + bar
   kolom tersembunyi), disalin dari pembelianpermintaandebetnote.blade.php.

   Dipatok, bukan diambil dari window.location - harus sama persis dengan
   MemorialKoreksiController::HREF.
   ========================================================================== */
const MK_HREF = 'memorialkoreksi'

let mkCart = []
let dataMk = []

function mkBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered) {
  let cart = []
  ;(headers || []).forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let tipeNama = { 0 : 'varchar', 1 : 'float', 2 : 'date' }
    let label = h
    if (aliasordered && aliasordered[i] && aliasordered[i].alias) {
      label = aliasordered[i].alias
    }
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null)
      ? Number(desimals[i])
      : (tipe === 1 ? 2 : 0)

    cart.push([
      tipe === 0 ? h : values[i],
      label,
      Number(isshowns[i]) === 1 ? 1 : 0,
      tipeNama[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
      h,
      values[i],
      tipe
    ])
  });
  return cart
}

function mkKolomTampil () {
  return (mkCart || []).filter(c => Number(c[2]) === 1)
}

function mkKolomRender (c) {
  return { field : c[0], label : c[1], tipe : Number(c[8]), desimal : Number(c[5]) }
}

function mkFormatAngkaDes (nilai, des) {
  let d = Number(des)
  if (isNaN(d) || d < 0) { d = 0 }

  let mentah = (nilai === null || nilai === undefined || nilai === '') ? 0 : nilai
  let angka = Number(String(mentah).split(',').join(''))
  if (isNaN(angka)) {
    return (nilai === null || nilai === undefined) ? '' : nilai
  }

  let teks = angka.toFixed(d)
  let minus = teks.charAt(0) === '-'
  if (minus) { teks = teks.substring(1) }

  let bagian = teks.split('.')
  let bulat = bagian[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',')
  return (minus ? '-' : '') + bulat + (bagian[1] ? '.' + bagian[1] : '')
}

function mkRenderNilai (col, item) {
  let nilai = item[col.field]
  if (col.tipe === 1) {
    return mkFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? formatDate(nilai) : ""
  }
  return (nilai === null || nilai === undefined) ? "" : nilai
}

function mkHeadHtml (cols) {
  if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
    return ReportTable.headHtml(cols)
  }
  console.warn('report-table.js tidak termuat - fitur geser & sembunyikan kolom dimatikan. Pastikan public/js/report-table.js ada di server.')
  let html = '<tr>'
  cols.forEach((c) => {
    html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>`
  });
  return html + '</tr>'
}

let mkRtSudahInit = false

function mkInitReportTableSekali () {
  if (mkRtSudahInit || typeof ReportTable === 'undefined') { return }
  mkRtSudahInit = true

  ReportTable.init({
    table    : '#tabel',
    bar      : '#rtBar',
    onChange : renderTabelMk
  })

  // Sebagian layout memasang penangan klik sendiri di <thead>; teruskan klik pada
  // roda gigi / pegangan geser ke penangan milik ReportTable.
  let mkGuardUlangKlik = false
  let thead = document.getElementById('tabel_header')
  if (thead) {
    thead.addEventListener('click', function (e) {
      if (mkGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      mkGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles : false, cancelable : true, view : window })
      Object.defineProperty(ulang, 'target', { value : interaktif, configurable : true })
      thead.dispatchEvent(ulang)
      mkGuardUlangKlik = false
    }, true)
  }
}

function mkPindahBar () {
  let bar = document.getElementById('rtBar')
  let tabel = document.getElementById('tabel')
  if (!bar || !tabel) { return }

  let acuan = tabel
  if ($.fn.DataTable.isDataTable('#tabel')) {
    acuan = document.getElementById('tabel_wrapper') || tabel
  }

  if (acuan.previousElementSibling !== bar) {
    acuan.parentNode.insertBefore(bar, acuan)
  }
}

function mkIkatSearch () {
  let input = document.getElementById('mkSearch')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    $('#tabel').DataTable().search(input.value).draw()
  })
}

let mkPanjangHalaman = 10
function mkIkatPanjangHalaman () {
  let sel = document.getElementById('mkLen')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(mkPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    mkPanjangHalaman = (n === -1 || n > 0) ? n : 10
    $('#tabel').DataTable().page.len(mkPanjangHalaman).draw()
  })
}

function mkIkatPeriode () {
  let awal  = document.getElementById('mkTglAwal')
  let akhir = document.getElementById('mkTglAkhir')
  if (!awal || !akhir || awal.dataset.rtBound) { return }
  awal.dataset.rtBound = '1'

  let onUbah = function () {
    if (!awal.value || !akhir.value) { return }
    if (awal.value > akhir.value) {
      alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
      return
    }
    loadAll()
  }

  awal.addEventListener('change', onUbah)
  akhir.addEventListener('change', onUbah)
}

/* ---------- Filter (menggantikan tab otorisasi) ---------- */
let mkFilterOtorisasi = 'SEMUA'
let mkFilterTrans = 'SEMUA'

function mkOtorisasi (item) {
  return Number(item.IsOtorisasi1) === 1 ? 'Sudah' : 'Belum'
}

function mkUpdateFilterBadge () {
  let jml = (mkFilterOtorisasi !== 'SEMUA' ? 1 : 0) + (mkFilterTrans !== 'SEMUA' ? 1 : 0)
  let badge = document.getElementById('mkFilterBadge')
  if (badge) { badge.textContent = jml + ' aktif' }
}

function mkTerapkanFilter () {
  mkFilterOtorisasi = $('#mkModalOtorisasi').val() || 'SEMUA'
  mkFilterTrans = $('#mkModalTrans').val() || 'SEMUA'
  mkUpdateFilterBadge()
  $('#modalFilterMk').modal('hide')
  renderTabelMk()
}

function mkResetFilter () {
  mkFilterOtorisasi = 'SEMUA'
  mkFilterTrans = 'SEMUA'
  $('#mkModalOtorisasi').val('SEMUA')
  $('#mkModalTrans').val('SEMUA')
  mkUpdateFilterBadge()
  $('#modalFilterMk').modal('hide')
  renderTabelMk()
}

/* ---------- Simpan / muat susunan kolom ---------- */
window.g_href = MK_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  mkCart.forEach((c) => {
    header.push(c[6])
    value.push(c[7])
    isnumber.push(c[8])
    isshown.push(Number(c[2]) === 1 ? 1 : 0)
    desimal.push(Number(c[5]) || 0)
  });

  $.ajax({
    url   : "{!! url('saveheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token   : $("#_token").val(),
      header   : JSON.stringify(header),
      isnumber : JSON.stringify(isnumber),
      tipe     : JSON.stringify(desimal),
      value    : JSON.stringify(value),
      isshown  : JSON.stringify(isshown),
      href     : MK_HREF
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

// Tombol "Reset kolom". Memakai endpoint milik menu ini sendiri karena
// HeaderTableController belum punya cabang untuk href 'memorialkoreksi'.
window.doSetHeader = function (mode, reset) {
  if (!reset) { return }

  $.ajax({
    url   : "{!! url('memorialkoreksiresetheader') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val()
    },
    success : function (res) {
      mkCart = mkBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = mkCart
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

function loadAll () {
  document.getElementById('tabel_data').innerHTML =
    '<tr><td colspan="20" class="text-center">' + loadingHtml('Memuat data...') + '</td></tr>'

  $.ajax({
    url: "{!! url('memorialkoreksiloadall') !!}",
    type: "get",
    async: true,
    data: {
      tglawal: $('#mkTglAwal').val(),
      tglakhir: $('#mkTglAkhir').val()
    },
    success: function(res) {
      mkCart = mkBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = mkCart
      dataMk = res.tempOutstanding || []
      renderTabelMk()
    },
    error: function (err) {
      console.error("Load failed:", err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function renderTabelMk () {
  window.g_modeReport = 1
  window.gcart_header = mkCart

  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy()
  }

  let cols = mkKolomTampil()
  let kolomRender = cols.map(mkKolomRender)

  let thead = document.getElementById('tabel_header')
  thead.innerHTML = mkHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
    baris.insertAdjacentHTML('beforeend', `
      <th style="padding: 4px 12px;" scope="col">Oto</th>
      <th style="padding: 4px 12px;" scope="col">User Oto</th>
      <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
    `)
  }

  let dataTampil = dataMk || []
  if (mkFilterOtorisasi !== 'SEMUA') {
    dataTampil = dataTampil.filter(function (item) { return mkOtorisasi(item) === mkFilterOtorisasi })
  }
  if (mkFilterTrans !== 'SEMUA') {
    dataTampil = dataTampil.filter(function (item) { return item.Trans === mkFilterTrans })
  }

  let rowTable = ''
  dataTampil.forEach((item) => {
    let isOtorisasi = Number(item.IsOtorisasi1) || 0

    let tombolAksi = `<button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail('${item.NoBukti}' , 'detail')"><i class="bi bi-info"></i></button>`
    if (isOtorisasi === 1) {
      tombolAksi += `
        <button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${item.NoBukti}' , 'edit')"><i class="bi bi-key"></i></button>
        <button class="btn btn-primary btn-sm" type="button" title="Cetak" onclick="submitPrint('${item.NoBukti}')"><i class="bi bi-printer"></i></button>
      `
    } else {
      // Otorisasi langsung dari baris tabel (pola pembelianpermintaanagen) - tidak lagi
      // membuka form detail dulu. Alur lama lewat buttonDetail(..., 'otorisasi') +
      // tombol #buttonOtorisasi di page3 dinonaktifkan, baris aslinya disimpan di bawah
      // supaya bisa dipakai lagi kalau nanti diperlukan.
      // <button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonDetail('${item.NoBukti}' , 'otorisasi')"><i class="bi bi-key"></i></button>
      tombolAksi += `
        <button class="btn btn-success btn-sm" type="button" title="Koreksi" onclick="buttonKoreksi('${item.NoBukti}' , 'edit')"><i class="bi bi-pen"></i></button>
        <button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasiRow('${item.NoBukti}', '${item.IsOtorisasi1}')"><i class="bi bi-key"></i></button>
      `
    }

    rowTable += `<tr><td class="text-center"><div class="po-aksi-wrap">${tombolAksi}</div></td>`
    kolomRender.forEach((c) => {
      if (c.tipe === 1) {
        rowTable += `<td style="text-align: right;">${mkRenderNilai(c, item)}</td>`
      } else {
        rowTable += `<td>${mkRenderNilai(c, item)}</td>`
      }
    });
    rowTable += `
      ${isOtorisasi ?
          '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
        :
          '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
      }
      <td>${item.OtoUser1 || ''}</td>
      <td>${item.TglOto1 ? formatDate(item.TglOto1) : ''}</td>
    </tr>`
  });

  document.getElementById("tabel_data").innerHTML = rowTable

  $('#tabel').DataTable({
    lengthChange: false,
    pageLength: mkPanjangHalaman,
    order: [],
    columnDefs: [{ targets: [0], orderable: false }],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  });

  mkPindahBar()
  mkIkatSearch()
  mkIkatPanjangHalaman()
  mkIkatPeriode()
  let inputSearch = document.getElementById('mkSearch')
  if (inputSearch && inputSearch.value) {
    $('#tabel').DataTable().search(inputSearch.value).draw()
  }
  mkAturTinggiTabel()
}

// Tinggi tabel mengikuti sisa ruang layar. Aman bila layout tidak punya #content -
// fungsinya berhenti diam-diam.
function mkAturTinggiTabel () {
  let area = document.getElementById('content')
  let wrap = document.querySelector('#page1 .po-table-wrap')
  if (!area || !wrap) { return }

  wrap.style.maxHeight = 'none'

  let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0
  let batasBawah = area.getBoundingClientRect().bottom - padBawah
  let kotak = wrap.getBoundingClientRect()
  let pageEl = document.getElementById('page1')
  let bawah = pageEl.getBoundingClientRect().bottom - kotak.bottom

  let sisa = batasBawah - kotak.top - bawah - 4
  wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px'
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

// Otorisasi langsung dari baris tabel - mengikuti buttonOtorisasi() di menu
// pembelianpermintaanagen: tanpa membuka form detail dan tanpa dialog konfirmasi.
// Nama fungsi sengaja diberi akhiran "Row" karena id tombol Otorisasi di page3
// (alur lama) sudah memakai nama "buttonOtorisasi".
function buttonOtorisasiRow (nobukti, isOtorisasi) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  if (Number(isOtorisasi) > 0) {
    alertify.warning('Sudah diotorisasi')
    return
  }

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('memorialkoreksispotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      if (res > 0) {
        alertify.success('Berhasil otorisasi')
        loadAll()
      } else {
        alertify.warning('Gagal otorisasi')
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function buttonBatalOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  // Batal otorisasi wajib disertai keterangan - mengikuti pola menu
  // pembelianpermintaanagen: keterangan dikirim sebagai 'pket' dan dicatat oleh
  // LoggingData di spBatalOtorisasi.
  alertify.prompt('Masukkan keterangan batal otorisasi nomor   ' + nobukti, '',
      function(evt, value) {
        let xpket = (value || '').trim()

        if (xpket == '') {
          alertify.warning('Keterangan harus diisi.')
          return false
        }

        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('memorialkoreksispbatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
            pket: xpket
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
      console.log('Batal konfirmasi batal otorisasi')
      alertify.error('Action cancelled')
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
  let kurs  = unformatAngka($("#AddAddKurs").val())
  let lawan  = $("#AddAddKredit").val()
  let perkiraan  = $("#AddAddDebet").val()
  let jumlah  = unformatAngka($("#AddAddJumlah").val())
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

  let titipan = mkAmbilTitipan(perkiraan, jumlah)
  if (!titipan) { return }
  notitipan = titipan.notitipan
  uruttitipan = titipan.uruttitipan
  custsuppP = titipan.custsuppP

  // Item piutang usaha yang sedang diedit: CustSuppP/KodeP dipertahankan apa adanya supaya
  // update tidak menghapus keterkaitan baris DBHUTPIUT milik item ini.
  if ((itemEdit.KodeP || '').trim() === 'PT') {
    custsuppP = itemEdit.CustSuppP || ''
    kodeP = 'PT'
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
  let kurs  = unformatAngka($("#AddAddKurs").val())
  let lawan  = $("#AddAddKredit").val()
  let perkiraan  = $("#AddAddDebet").val()
  let jumlah  = unformatAngka($("#AddAddJumlah").val())
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

  if (perkiraan.trim() === lawan.trim()) {
    alertify.warning("Debet dan Kredit tidak boleh perkiraan yang sama")
    return
  }

  let titipan = mkAmbilTitipan(perkiraan, jumlah)
  if (!titipan) { return }
  notitipan = titipan.notitipan
  uruttitipan = titipan.uruttitipan
  custsuppP = titipan.custsuppP

  // Debet = perkiraan piutang usaha: customer terpilih disimpan ke dbTransaksi.CustSuppP dan
  // KodeP diisi 'PT'. KodeP inilah yang dipakai spAdd() sebagai penanda untuk membersihkan
  // dbTempHutPiut setelah sp_TransaksiMemorial memindahkan barisnya ke DBHUTPIUT.
  let piutang = mkAmbilPiutang(perkiraan)
  if (!piutang) { return }
  if (piutang.kodeP === 'PT') {
    custsuppP = piutang.custsuppP
    kodeP = piutang.kodeP
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

// Valas sudah jadi dropdown (lihat muatDropdownValas() / onChangeValasAdd()), jadi
// picker modal lama ini tidak lagi dipanggil dari mana pun. Dibiarkan sebagai
// komentar, tidak dihapus.
/*
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
        $("#form").modal('show')
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
*/

let listValasDropdown = []

function muatDropdownValas () {
  $.ajax({
    url: "{!! url('memorialkoreksilistvalas') !!}",
    type: "get",
    async: false,
    data: {},
    success: function (res) {
      listValasDropdown = res || []
      let sel = document.getElementById('AddAddValas')
      let terpilih = sel.value
      sel.innerHTML = ''
      listValasDropdown.forEach((item) => {
        let opt = document.createElement('option')
        opt.value = item.KODEVLS
        opt.textContent = `${item.KODEVLS} - ${item.NAMAVLS}`
        sel.appendChild(opt)
      })
      if (listValasDropdown.some(item => item.KODEVLS === terpilih)) { sel.value = terpilih }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function onChangeValasAdd () {
  let kode = document.getElementById('AddAddValas').value
  let itemX = listValasDropdown.find(item => item.KODEVLS === kode)
  document.getElementById('AddAddKurs').value =
    formatAngka(itemX && itemX.KURS ? parseFloat(itemX.KURS).toFixed(2) : '0.00')
}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddMain').show();
  $('#form .modal-dialog').removeClass('mk-dialog-perkiraan');

  // Dulu 'toggle'. Sejak ada tumpukan modal (mkTumpukanModal) perintahnya harus eksplisit -
  // 'toggle' pada modal yang sedang tampil memicu hide dan mem-pop tumpukan secara keliru.
  $("#form").modal('hide')
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
  mkResetTitipan()
  mkResetPT()
  mkAturTombolBrowse()
}

// ---------- Tumpukan modal ----------
// Hanya satu modal yang terlihat pada satu waktu. Saat modal anak dibuka, modal
// induk disembunyikan lewat class (bukan .modal('hide'), supaya isian form dan
// handler hidden.bs.modal milik induk tidak ikut terpicu). Saat anak ditutup -
// lewat Batal, tombol x, Esc, maupun klik backdrop - induk muncul lagi.
// Pola sama persis dengan penerimaandpp.blade.php / pelunasanpiutangdpp.blade.php.
var mkTumpukanModal = []

function mkSisakanSatuBackdrop () {
  var backdrop = $('.modal-backdrop')
  backdrop.addClass('mk-backdrop-tertimbun')
  backdrop.last().removeClass('mk-backdrop-tertimbun')
}

$(document).on('show.bs.modal', '.modal', function () {
  var induk = $('.modal.show').not(this).not('.mk-modal-tertimbun').last()
  if (induk.length) {
    mkTumpukanModal.push(induk)
    induk.addClass('mk-modal-tertimbun')
  }
})

$(document).on('shown.bs.modal', '.modal', function () {
  mkSisakanSatuBackdrop()
})

$(document).on('hidden.bs.modal', '.modal', function () {
  var induk = mkTumpukanModal.pop()
  if (induk) induk.removeClass('mk-modal-tertimbun')
  // BS4 melepas .modal-open dari <body> begitu satu modal tertutup, padahal masih
  // ada modal lain yang terbuka - pasang lagi supaya scroll body tetap terkunci.
  if ($('.modal.show').length) $('body').addClass('modal-open')
  mkSisakanSatuBackdrop()
})


/* ==========================================================================================
   PENAMBAHAN PIUTANG USAHA - Debet = perkiraan ber-Kode 'PT' di dbPOSTHUTPIUT
   ------------------------------------------------------------------------------------------
   Rantai modal: #form (pane Perkiraan) -> #formMkCustomerPT -> #formMkKartuPT.
   Baris faktur yang ditambahkan user ditulis langsung ke dbTempHutPiut (sp_TempHutPiut 'I'),
   dan nanti dipindahkan ke DBHUTPIUT oleh sp_TransaksiMemorial saat item memorial disimpan.
   ========================================================================================== */

let listCustomerPT = []   // hasil browse customer
let listKartuPT = []      // isi tabel kartu yang sedang tampil

// Perkiraan PT yang sedang dipakai - dipakai semua endpoint kartu.
function mkPerkiraanPT () {
  return ($("#AddAddDebet").val() || '').trim()
}

// Urut item memorial yang dikirim ke server. Rantai piutang ini hanya bisa dipicu saat
// MENAMBAH item - waktu edit item, tombol browse perkiraan dikunci oleh lockFormAddAdd() -
// jadi nilainya 0 dan server yang menghitung NoMsk lewat MAX(Urut)+1, rumus yang sama dengan
// yang dipakai sp_TransaksiMemorial saat choice 'I'.
function mkUrutItemPT () {
  return (mkItemTerkunci && itemEdit && itemEdit.Urut) ? Number(itemEdit.Urut) : 0
}

function buttonAddListCustomerPT () {
  listCustomerPT = []
  let _token = $("#_token").val()
  let perkiraan = mkPerkiraanPT()

  if (!perkiraan) { alertify.warning("Perkiraan Debet belum dipilih"); return }

  $.ajax({
    url: "{!! url('memorialkoreksilistcustomerpt') !!}",
    type: "post",
    async: false,
    data: { _token, perkiraan },
    success: function (res) {
      listCustomerPT = res

      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="mkPickCustomerPT(${i})">
        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td>${item.ALAMAT || ''}</td>
        <td>${item.NAMAKOTA || ''}</td>
        </tr>`
      })

      document.getElementById("tabel_data_mk_customerpt").innerHTML = rowTable

      let inputCari = document.getElementById('input_search_customerpt')
      if (inputCari) { inputCari.value = '' }

      if (res.length) {
        $('#formMkCustomerPT').modal('show')
      } else {
        alertify.warning("Customer untuk perkiraan ini tidak ditemukkan")
      }

      mkIkatSearchCustomerPT()
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function mkIkatSearchCustomerPT () {
  let input = document.getElementById('input_search_customerpt')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    let cari = input.value.toLowerCase()
    let baris = document.querySelectorAll('#tabel_data_mk_customerpt tr')
    baris.forEach(function (tr) {
      tr.style.display = tr.textContent.toLowerCase().indexOf(cari) !== -1 ? '' : 'none'
    })
  })
}

function mkPickCustomerPT (index) {
  let item = listCustomerPT[index]
  if (!item) { return }

  // Customer yang sama dipilih lagi: JANGAN seed ulang, karena seed menghapus seluruh baris
  // kerja - termasuk faktur yang sudah ditambahkan user. Cukup buka lagi kartunya apa adanya.
  let custLama = ($("#AddAddCustsuppPT").val() || '').trim()
  let custBaru = (item.KODECUSTSUPP || '').trim()
  let customerSama = custLama !== '' && custLama === custBaru

  document.getElementById("AddAddCustsuppPT").value = item.KODECUSTSUPP
  document.getElementById("AddAddNamaCustPT").value = item.NAMACUSTSUPP
  document.getElementById("AddAddNamaCustPTView").value = item.NAMACUSTSUPP
  document.getElementById("AddAddKodePT").value = 'PT'
  $('#rowCustomerPT').show()

  // Modal Customer dibiarkan terbuka sebagai induk: modal Kartu ditumpuk di atasnya,
  // supaya Batal di Kartu mengembalikan user ke daftar customer.
  if (customerSama) {
    mkKartuBukaLagi()
  } else {
    mkKartuBuka()
  }
}

// Buka kembali modal Kartu tanpa seed ulang - dipakai kalau customer yang dipilih sama dengan
// yang sedang aktif, supaya baris faktur yang sudah ditambahkan tidak ikut terhapus.
function mkKartuBukaLagi () {
  document.getElementById("mkKartuJudulPerkiraan").innerHTML = mkPerkiraanPT()
  document.getElementById("mkKartuKodeCust").innerHTML = $("#AddAddCustsuppPT").val()
  document.getElementById("mkKartuNamaCust").innerHTML = '[ ' + ($("#AddAddNamaCustPT").val() || '') + ' ]'

  mkKartuRefresh()
  mkKartuTutupFormTambah()
  $('#formMkKartuPT').modal('show')
  mkIkatSearchKartuPT()
}

// Buka modal Kartu Piutang: seed faktur outstanding customer ini ke dbTempHutPiut lalu
// tampilkan. Hanya dipanggil sekali per pemilihan customer - refresh berikutnya memakai
// mkKartuRefresh() supaya baris tambahan user tidak ikut terhapus.
function mkKartuBuka () {
  let _token = $("#_token").val()
  let perkiraan = mkPerkiraanPT()
  let kodecustsupp = $("#AddAddCustsuppPT").val()
  let nobukti = $("#input_add_nobukti").val()
  let urut = mkUrutItemPT()

  $.ajax({
    url: "{!! url('memorialkoreksiloadkartupt') !!}",
    type: "post",
    async: false,
    data: { _token, perkiraan, kodecustsupp, nobukti, urut },
    success: function (res) {
      // NoMsk yang dipakai baris temp - disimpan supaya tambah/hapus memakai nilai yang sama.
      document.getElementById("AddAddNoMskPT").value = res.nomsk

      document.getElementById("mkKartuJudulPerkiraan").innerHTML = perkiraan
      document.getElementById("mkKartuKodeCust").innerHTML = kodecustsupp
      document.getElementById("mkKartuNamaCust").innerHTML = '[ ' + ($("#AddAddNamaCustPT").val() || '') + ' ]'

      mkKartuRender(res.data)
      mkKartuTutupFormTambah()
      $('#formMkKartuPT').modal('show')
      mkIkatSearchKartuPT()
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

// Ambil ulang isi kartu tanpa seed ulang.
function mkKartuRefresh () {
  let _token = $("#_token").val()
  let perkiraan = mkPerkiraanPT()

  $.ajax({
    url: "{!! url('memorialkoreksigetkartupt') !!}",
    type: "post",
    async: false,
    data: { _token, perkiraan },
    success: function (res) { mkKartuRender(res) },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

// Gambar isi tabel kartu. Tombol Hapus HANYA untuk baris tambahan user - penandanya
// NoInvoice = 'TBH', konvensi yang sama dengan data memorial lama. Baris faktur outstanding
// bawaan tampil tanpa tombol apa pun karena itu data transaksi lain.
function mkKartuRender (res) {
  listKartuPT = res || []

  let rowTable = ``
  listKartuPT.forEach((item, i) => {
    let bisaHapus = (item.NoInvoice || '').trim() === 'TBH'

    let aksi = bisaHapus
      ? `<button class="btn btn-danger btn-sm" type="button" onclick="mkKartuHapus(${i})" title="Hapus"><i class="bi bi-trash"></i></button>`
      : ``

    rowTable += `
    <tr>
    <td>${item.NoFaktur}</td>
    <td>${item.NoRetur || ''}</td>
    <td>${formatDate(item.Tanggal)}</td>
    <td>${formatDate(item.JatuhTempo)}</td>
    <td class="text-right">${formatAngka(parseFloat(item.Debet).toFixed(2))}</td>
    <td class="text-right">${formatAngka(parseFloat(item.Kredit).toFixed(2))}</td>
    <td class="text-right">${formatAngka((parseFloat(item.Debet) - parseFloat(item.Kredit)).toFixed(2))}</td>
    <td>${item.Valas}</td>
    <td class="text-center">${aksi}</td>
    </tr>`
  })

  document.getElementById("tabel_data_mk_kartupt").innerHTML = rowTable

  mkKartuHitung()
}

// Total kolom + ringkasan Total/Dibayar/Sisa.
// Total  = Jumlah di form item memorial
// Dibayar= akumulasi baris tambahan user (NoInvoice='TBH') saja
// Sisa   = Total - Dibayar. Murni informasi, tidak ada validasi apa pun.
function mkKartuHitung () {
  let totalDebet = 0
  let totalKredit = 0
  let dibayar = 0

  listKartuPT.forEach(function (item) {
    let debet = parseFloat(item.Debet) || 0
    let kredit = parseFloat(item.Kredit) || 0
    totalDebet += debet
    totalKredit += kredit
    if ((item.NoInvoice || '').trim() === 'TBH') { dibayar += debet }
  })

  document.getElementById("mkKartuTotalDebet").innerHTML = formatAngka(totalDebet.toFixed(2))
  document.getElementById("mkKartuTotalKredit").innerHTML = formatAngka(totalKredit.toFixed(2))
  document.getElementById("mkKartuTotalSaldo").innerHTML = formatAngka((totalDebet - totalKredit).toFixed(2))

  let total = Number(unformatAngka($("#AddAddJumlah").val()) || 0) * Number(unformatAngka($("#AddAddKurs").val()) || 1)

  document.getElementById("mkKartuRingkasTotal").innerHTML = formatAngka(total.toFixed(2))
  document.getElementById("mkKartuRingkasDibayar").innerHTML = formatAngka(dibayar.toFixed(2))
  document.getElementById("mkKartuRingkasSisa").innerHTML = formatAngka((total - dibayar).toFixed(2))
}

function mkIkatSearchKartuPT () {
  let input = document.getElementById('input_search_kartupt')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    let cari = input.value.toLowerCase()
    let baris = document.querySelectorAll('#tabel_data_mk_kartupt tr')
    baris.forEach(function (tr) {
      tr.style.display = tr.textContent.toLowerCase().indexOf(cari) !== -1 ? '' : 'none'
    })
  })
}

// Panel tambah faktur. Jumlah-nya mengikuti Jumlah di form item memorial, sesuai permintaan -
// bukan mengikuti Sisa. Valas & Kurs ikut item dan dikunci.
function mkKartuBukaFormTambah () {
  document.getElementById("mkKartuNoFaktur").value = ''
  document.getElementById("mkKartuTanggal").value = $("#input_add_tanggal").val()
  document.getElementById("mkKartuJatuhTempo").value = $("#input_add_tanggal").val()
  document.getElementById("mkKartuValas").value = $("#AddAddValas").val()
  document.getElementById("mkKartuKurs").value = $("#AddAddKurs").val()
  document.getElementById("mkKartuJumlah").value = $("#AddAddJumlah").val()
  document.getElementById("mkKartuCatatan").value = ''

  $('#mkKartuFormTambah').show()
}

function mkKartuTutupFormTambah () {
  $('#mkKartuFormTambah').hide()
}

function mkKartuSimpanTambah () {
  let _token = $("#_token").val()
  let nofaktur = ($("#mkKartuNoFaktur").val() || '').trim()
  let tanggal = $("#mkKartuTanggal").val()
  let jatuhtempo = $("#mkKartuJatuhTempo").val()
  let jumlah = unformatAngka($("#mkKartuJumlah").val())

  if (!nofaktur) { alertify.warning("No. Faktur belum diisi"); return }
  if (!tanggal || !jatuhtempo) { alertify.warning("Tanggal belum lengkap"); return }
  if (Number(jumlah) <= 0) { alertify.warning("Jumlah <= 0"); return }

  $.ajax({
    url: "{!! url('memorialkoreksiaddkartupt') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nofaktur,
      tanggal,
      jatuhtempo,
      jumlah,
      valas: $("#AddAddValas").val(),
      kurs: unformatAngka($("#AddAddKurs").val()),
      catatan: $("#mkKartuCatatan").val(),
      perkiraan: mkPerkiraanPT(),
      kodecustsupp: $("#AddAddCustsuppPT").val(),
      nobukti: $("#input_add_nobukti").val(),
      urut: mkUrutItemPT()
    },
    success: function (res) {
      mkKartuRender(res)
      mkKartuTutupFormTambah()
      alertify.success("Faktur ditambahkan")
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function mkKartuHapus (index) {
  let item = listKartuPT[index]
  if (!item) { return }

  alertify.confirm('Hapus Faktur', `Apakah yakin ingin menghapus faktur ${item.NoFaktur} ?`,
    function () {
      let _token = $("#_token").val()

      $.ajax({
        url: "{!! url('memorialkoreksideletekartupt') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nofaktur: item.NoFaktur,
          noretur: item.NoRetur || '',
          tipetrans: item.TipeTrans,
          kodecustsupp: item.KodeCustSupp,
          nobukti: item.NoBukti,
          nomsk: item.NoMsk,
          urut: item.Urut,
          tanggal: item.Tanggal,
          jatuhtempo: item.JatuhTempo,
          valas: item.Valas,
          kurs: item.Kurs,
          perkiraan: item.Perkiraan
        },
        success: function (res) {
          mkKartuRender(res)
          alertify.success("Faktur dihapus")
        },
        error: function (err) {
          console.log(err)
          alertify.warning('Terjadi kesalahan silahkan refresh browser')
        }
      })
    },
    function () {}).set('labels', { ok: 'Ya', cancel: 'Batal' })
}

// Bersihkan pilihan customer + baris kerja di dbTempHutPiut. Dipanggil kalau Debet berubah
// jadi bukan perkiraan PT, dan dari cleanFormAddAdd().
function mkResetPT (bersihkanTemp = true) {
  document.getElementById("AddAddCustsuppPT").value = ''
  document.getElementById("AddAddNamaCustPT").value = ''
  document.getElementById("AddAddNamaCustPTView").value = ''
  document.getElementById("AddAddKodePT").value = ''
  document.getElementById("AddAddNoMskPT").value = ''
  $('#rowCustomerPT').hide()

  listKartuPT = []

  if (bersihkanTemp) {
    $.ajax({
      url: "{!! url('memorialkoreksiclearkartupt') !!}",
      type: "post",
      async: false,
      data: { _token: $("#_token").val() },
      error: function (err) { console.log(err) }
    })
  }
}

// No Titipan hanya berlaku kalau Debet = 113400 (Titipan Customer). Dipanggil dari
// cleanFormAddAdd(), buttonAddPickPerkiraan(), dan buttonEditItem() saat Debet-nya
// bukan/tidak lagi 113400, supaya notitipan/uruttitipan/custsuppP tidak ikut tersimpan.
function mkResetTitipan () {
  document.getElementById("AddAddNoTitipan").value = ''
  document.getElementById("AddAddUrutTitipan").value = ''
  document.getElementById("AddAddCustsuppTitipan").value = ''
  document.getElementById("AddAddSisaTitipan").value = ''
  $('#rowNoTitipan').hide();
}

// Dipanggil dari submitAdd()/submitEdit() sesaat sebelum kirim data. Kalau Debet bukan
// 113400, notitipan/uruttitipan/custsuppP dikirim kosong seperti semula. Kalau 113400,
// wajib sudah pilih titipan lewat browse, dan Jumlah tidak boleh melebihi Sisa titipan
// -- kecuali Sisa kosong (fallback: gagal mengambil sisa efektif, lihat mkAmbilSisaTitipan()).
function mkAmbilTitipan (perkiraan, jumlah) {
  if (perkiraan !== '113400') {
    return { notitipan: '', uruttitipan: 0, custsuppP: '' }
  }
  let notitipan = $("#AddAddNoTitipan").val()
  let uruttitipan = Number($("#AddAddUrutTitipan").val() || 0)
  let custsuppP = $("#AddAddCustsuppTitipan").val()
  let sisa = $("#AddAddSisaTitipan").val()
  if (!notitipan) {
    alertify.warning("Pilih No Titipan")
    return null
  }
  if (sisa !== '' && Number(jumlah) > Number(sisa)) {
    alertify.warning("Jumlah melebihi sisa titipan (" + formatAngka(parseFloat(sisa).toFixed(2)) + ")")
    return null
  }
  return { notitipan, uruttitipan, custsuppP }
}

// Dipanggil dari submitAdd() sesaat sebelum kirim data. Kalau Debet bukan perkiraan piutang
// usaha, kodeP/custsuppP dikirim kosong seperti semula. Kalau perkiraan piutang usaha, customer
// wajib sudah dipilih lewat rantai browse Customer -> Kartu Piutang, karena tanpa CustSuppP
// baris piutangnya tidak punya pemilik.
function mkAmbilPiutang (perkiraan) {
  let kodeP = ($("#AddAddKodePT").val() || '').trim()

  if (kodeP !== 'PT') {
    return { kodeP: '', custsuppP: '' }
  }

  let custsuppP = ($("#AddAddCustsuppPT").val() || '').trim()
  if (!custsuppP) {
    alertify.warning("Pilih Customer untuk perkiraan piutang")
    return null
  }

  return { kodeP, custsuppP }
}

// Dipanggil dari buttonEditItem() saat item yang dibuka Perkiraan-nya 113400. Mengambil sisa
// EFEKTIF titipan (endpoint memorialkoreksisisatitipan / MemorialKoreksiController::sisaTitipan)
// -- yaitu sisa titipan seandainya baris yang sedang diedit ini sendiri dikecualikan dari
// perhitungan -- supaya mkAmbilTitipan() tetap bisa menolak Jumlah baru yang melebihi sisa.
// Kalau gagal diambil (jaringan/endpoint error) atau notitipan kosong, AddAddSisaTitipan
// dikosongkan supaya validasi dilewati (fallback) daripada memblokir user mengedit item.
function mkAmbilSisaTitipan (item) {
  let notitipan = item.NOTITIPAN
  let uruttitipan = item.URUTTITIPAN
  document.getElementById("AddAddSisaTitipan").value = ''

  if (!notitipan) { return }

  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('memorialkoreksisisatitipan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      notitipan,
      uruttitipan,
      nobukti: item.NoBukti,
      urut: item.Urut
    },
    success: function (res) {
      if (res && res.length) {
        document.getElementById("AddAddSisaTitipan").value = res[0].Sisa
      }
    },
    error: function (err) {
      console.log(err)
    }
  })
}

function lockFormAddAdd (value = true) {
  document.getElementById("AddAddKodeDevisi").disabled = value
  document.getElementById("AddAddValas").disabled = value
  document.getElementById("buttonAddListTitipan").disabled = value
  // Browse Customer piutang ikut dikunci saat edit item, sejalan dengan browse Titipan dan
  // browse Debet/Kredit - rincian piutang hanya boleh disusun waktu item dibuat.
  document.getElementById("buttonAddListCustomerPT").disabled = value

  mkItemTerkunci = value
  mkAturTombolBrowse()
}

// Tombol browse Debet & Kredit hanya boleh aktif kalau item sedang tidak dikunci (bukan mode
// edit item, lihat lockFormAddAdd()) DAN kolom Jumlah sudah diisi (> 0) - dipanggil ulang setiap
// kali Jumlah berubah, supaya urutan pengisiannya wajib Jumlah dulu baru pilih perkiraan.
function mkAturTombolBrowse () {
  let jumlah = Number(unformatAngka($("#AddAddJumlah").val()) || 0)
  let matikan = mkItemTerkunci || !(jumlah > 0)

  let tombolDebet = document.getElementById("buttonAddListDebet")
  let tombolKredit = document.getElementById("buttonAddListKredit")

  tombolDebet.disabled = matikan
  tombolKredit.disabled = matikan

  let title = (!mkItemTerkunci && !(jumlah > 0)) ? 'Isi Jumlah dulu sebelum memilih perkiraan' : ''
  tombolDebet.title = title
  tombolKredit.title = title
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
      transaksi,
      // Sisi browse menentukan boleh/tidaknya perkiraan piutang usaha (Kode 'PT') muncul -
      // hanya Debet yang dibuka, lihat MemorialKoreksiController::listPerkiraan().
      sisi: idTujuan
    },
    success: function(res) {
      console.log(res)
      listLawan  = res

      // Perkiraan yang sudah dipakai di sisi lawan tidak boleh dipilih lagi (Debet dan
      // Kredit tidak boleh sama) - baris itu langsung disembunyikan dari daftar.
      let idLawan = idTujuan === 'Debet' ? 'AddAddKredit' : 'AddAddDebet'
      let perkiraanLawan = ($("#" + idLawan).val() || '').trim()

      let rowTable = ``
      let jumlahBaris = 0
      res.forEach((item, i) => {
        if (perkiraanLawan && item.Perkiraan === perkiraanLawan) { return }
        jumlahBaris++
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickPerkiraan(${i},'${item.Perkiraan}' , '${item.Keterangan}' , '${idTujuan}', '${item.Kode || ''}')">
        <td>${item.Perkiraan}</td>
        <td>${item.Keterangan}</td>
        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_perkiraan").innerHTML = rowTable

      let inputCariPerkiraan = document.getElementById('input_search_perkiraan')
      if (inputCariPerkiraan) { inputCariPerkiraan.value = '' }

      if (jumlahBaris) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListPerkiraan').show();
        $('#form .modal-dialog').addClass('mk-dialog-perkiraan');
        $("#form").modal('show')
      } else {
        alertify.warning("Perkiraan tidak ditemukkan")
      }

      mkIkatSearchPerkiraan()

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

// Kotak cari di modal browse Perkiraan - barisnya digambar langsung (bukan DataTable),
// jadi penyaringannya menyembunyikan baris secara langsung, sama seperti
// pldIkatCariPerkiraanModal() di pelunasanpiutangdpp.blade.php.
function mkIkatSearchPerkiraan () {
  let input = document.getElementById('input_search_perkiraan')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    let cari = input.value.toLowerCase()
    let baris = document.querySelectorAll('#tabel_data_add_list_perkiraan tr')
    baris.forEach(function (tr) {
      tr.style.display = tr.textContent.toLowerCase().indexOf(cari) !== -1 ? '' : 'none'
    })
  })
}


function buttonAddPickPerkiraan (index, perkiraan, keterangan , idTujuan, kode) {

  console.log(index, perkiraan, keterangan , idTujuan, kode)

  // Penjaga lapis kedua - seharusnya baris ini sudah disembunyikan oleh
  // buttonAddListPerkiraan(), tapi tetap ditolak kalau entah bagaimana masih terklik.
  let idLawan = idTujuan === 'Debet' ? 'AddAddKredit' : 'AddAddDebet'
  let perkiraanLawan = ($("#" + idLawan).val() || '').trim()
  if (perkiraanLawan && perkiraan.trim() === perkiraanLawan) {
    alertify.warning("Debet dan Kredit tidak boleh perkiraan yang sama")
    return
  }

  document.getElementById(`AddAdd${idTujuan}`).value = perkiraan
  document.getElementById(`AddAddKeterangan${idTujuan}`).value = keterangan

  if (idTujuan === 'Debet') {
    // Kalau sebelumnya sudah ada titipan terpilih, Jumlah kemungkinan masih berisi Sisa
    // titipan lama itu - reset supaya tidak salah kebawa ke perkiraan yang baru dipilih.
    let adaTitipanLama = !!$("#AddAddNoTitipan").val()
    if (adaTitipanLama) {
      document.getElementById("AddAddJumlah").value = '0.00'
      mkAturTombolBrowse()
    }

    if (perkiraan === '113400') {
      mkResetTitipan()
      mkResetPT()
      $('#rowNoTitipan').show();
      buttonAddListTitipan(true)
      return
    } else {
      mkResetTitipan()
    }

    // Perkiraan piutang usaha: lanjut ke browse Customer, lalu Kartu Piutang. Modal #form
    // (pane Perkiraan) sengaja DIBIARKAN terbuka supaya jadi induk di tumpukan modal -
    // jangan panggil buttonAddListBatal() di cabang ini.
    if ((kode || '').trim() === 'PT') {
      mkResetPT()
      document.getElementById("AddAddKodePT").value = 'PT'
      buttonAddListCustomerPT()
      return
    } else {
      mkResetPT()
    }
  }

  buttonAddListBatal()

}

// Browse No Titipan (dbTransaksi.NOTITIPAN/URUTTITIPAN) - hanya dipanggil saat Debet = 113400.
// Query & endpoint: MemorialKoreksiController::listTitipan() / memorialkoreksilisttitipan.
//
// lanjutDariPerkiraan = true dipanggil dari buttonAddPickPerkiraan() saat modal #form MASIH
// TERBUKA (pane Perkiraan) - jadi hanya menukar pane, tidak toggle modal lagi. Dipanggil tanpa
// argumen dari tombol browse manual #buttonAddListTitipan, modalnya belum terbuka.
let listTitipan = []
function buttonAddListTitipan (lanjutDariPerkiraan = false) {
  listTitipan = []
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('memorialkoreksilisttitipan') !!}",
    type: "post",
    async: false,
    data: { _token },
    success: function (res) {
      listTitipan = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickTitipan(${i})">
        <td>${item.NOBUKTI}</td>
        <td>${formatDate(item.TANGGAL)}</td>
        <td>${item.namaCustSupp}</td>
        <td>${item.Keterangan}</td>
        <td class="text-right">${formatAngka(parseFloat(item.JumlahRp).toFixed(2))}</td>
        <td class="text-right">${formatAngka(parseFloat(item.Sisa).toFixed(2))}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_titipan").innerHTML = rowTable

      let inputCariTitipan = document.getElementById('input_search_titipan')
      if (inputCariTitipan) { inputCariTitipan.value = '' }

      if (res.length) {
        $('.showhidemodalbodyadd').hide();
        $('#modalAddListTitipan').show();
        $('#form .modal-dialog').removeClass('mk-dialog-perkiraan');
        if (!lanjutDariPerkiraan) { $("#form").modal('show') }
      } else {
        alertify.warning("Titipan tidak ditemukkan")
        if (lanjutDariPerkiraan) { buttonAddListBatal() }
      }

      mkIkatSearchTitipan()
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function mkIkatSearchTitipan () {
  let input = document.getElementById('input_search_titipan')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    let cari = input.value.toLowerCase()
    let baris = document.querySelectorAll('#tabel_data_add_list_titipan tr')
    baris.forEach(function (tr) {
      tr.style.display = tr.textContent.toLowerCase().indexOf(cari) !== -1 ? '' : 'none'
    })
  })
}

function buttonAddPickTitipan (index) {
  let item = listTitipan[index]
  console.log(item)

  document.getElementById("AddAddNoTitipan").value = item.NOBUKTI
  document.getElementById("AddAddUrutTitipan").value = item.URUT
  document.getElementById("AddAddCustsuppTitipan").value = item.KOdeCustSupp
  document.getElementById("AddAddSisaTitipan").value = item.Sisa

  document.getElementById("AddAddJumlah").value = formatAngka(parseFloat(item.Sisa).toFixed(2))
  mkAturTombolBrowse()

  if (item.Valas && $("#AddAddValas").val() && item.Valas !== $("#AddAddValas").val()) {
    alertify.warning("Valas titipan (" + item.Valas + ") berbeda dengan Valas yang dipilih")
  }

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
        let kurs  = unformatAngka($("#AddAddKurs").val())
        let lawan  = $("#AddAddKredit").val()
        let perkiraan  = $("#AddAddDebet").val()
        let jumlah  = unformatAngka($("#AddAddJumlah").val())
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
  document.getElementById("AddAddKurs").value = formatAngka(parseFloat(itemEdit.Kurs).toFixed(2))
  document.getElementById("AddAddJumlah").value = formatAngka(parseFloat(itemEdit.JumlahRp).toFixed(2))
  document.getElementById("AddAddKeterangan").value = itemEdit.Keterangan
  document.getElementById("AddAddKeteranganDetail").value = itemEdit.KetDetail
  document.getElementById("AddAddDebet").value = itemEdit.Perkiraan
  document.getElementById("AddAddKeteranganDebet").value = itemEdit.namaPerkiraan
  document.getElementById("AddAddKredit").value = itemEdit.Lawan
  document.getElementById("AddAddKeteranganKredit").value = itemEdit.NamaLawan

  // No Titipan: kolomnya NOTITIPAN/URUTTITIPAN (huruf besar, hasil select a.* dari dbTransaksi).
  // AddAddSisaTitipan diisi lewat mkAmbilSisaTitipan() (sisa efektif yang mengecualikan baris
  // ini sendiri), supaya Jumlah tetap dibatasi <= sisa titipan sewaktu diedit.
  if (itemEdit.Perkiraan === '113400') {
    document.getElementById("AddAddNoTitipan").value = itemEdit.NOTITIPAN || ''
    document.getElementById("AddAddUrutTitipan").value = itemEdit.URUTTITIPAN || ''
    document.getElementById("AddAddCustsuppTitipan").value = itemEdit.CustSuppP || ''
    mkAmbilSisaTitipan(itemEdit)
    $('#rowNoTitipan').show();
  } else {
    mkResetTitipan()
  }

  // Item dengan Debet = perkiraan piutang usaha: customer-nya ditampilkan sebagai informasi.
  // dbTempHutPiut sengaja DIBERSIHKAN saat masuk mode edit. Rincian piutang tidak disusun ulang
  // di sini, dan sp_TransaksiMemorial choice 'U' menghapus lalu menulis ulang baris DBHUTPIUT
  // berdasarkan isi temp - kalau ada baris sisa dari alur yang ditinggalkan sebelumnya, baris itu
  // bisa ikut tertulis ke item ini. Dengan temp kosong, 'U' tidak menyentuh piutang sama sekali.
  mkResetPT()

  if ((itemEdit.KodeP || '').trim() === 'PT') {
    document.getElementById("AddAddKodePT").value = 'PT'
    document.getElementById("AddAddCustsuppPT").value = itemEdit.CustSuppP || ''
    document.getElementById("AddAddNamaCustPT").value = itemEdit.NamaCustSuppP || ''
    document.getElementById("AddAddNamaCustPTView").value = itemEdit.NamaCustSuppP || ''
    $('#rowCustomerPT').show();
  }

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

  // Cabang 'otorisasi' dipertahankan tapi sudah tidak dipakai - lihat catatan di
  // submitOtorisasi(). Tombol Otorisasi di tabel sekarang memanggil buttonOtorisasiRow().
  if (tipe == 'otorisasi') {
    // Gate akses_isotorisasi1 dinonaktifkan - disamakan dengan pelunasanpiutangdpp
    // yang tidak melakukan pengecekan ini di sisi client. Hak akses halaman tetap
    // dijaga oleh HASACCESS di MemorialKoreksiController@index.
    // let akses = $("#akses_isotorisasi1").val();
    // if (!Number(akses)) {
    //   alertify.warning('No access')
    //   return
    // }

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

function unformatAngka (angka) {
  if (!angka) return 0
  return parseFloat(String(angka).replace(/,/g, '')) || 0
}

function formatAngkaInput (el) {
  el.value = formatAngka(unformatAngka(el.value).toFixed(2))
}

function unformatAngkaInput (el) {
  el.value = unformatAngka(el.value).toFixed(2)
}

// Dipasang di oninput #AddAddJumlah supaya separator ribuan langsung muncul sambil mengetik,
// tidak menunggu pindah fokus (onblur formatAngkaInput() tetap jalan untuk menormalkan ke 2
// desimal). Tidak memakai formatAngka() biasa karena nilai yang sedang diketik boleh belum
// punya titik desimal atau baru diketik sebagian - formatAngka() mengasumsikan keduanya sudah
// lengkap. Posisi kursor dihitung ulang dari jarak ke kanan supaya tidak melompat ke ujung
// setiap kali jumlah koma bertambah.
function formatAngkaKetik (el) {
  let posDariKanan = el.value.length - el.selectionStart
  let minus = el.value.trim().startsWith('-') ? '-' : ''
  let raw = el.value.replace(/[^0-9.]/g, '')

  let titikIndex = raw.indexOf('.')
  let bulat = titikIndex === -1 ? raw : raw.slice(0, titikIndex)
  let desimal = titikIndex === -1 ? '' : raw.slice(titikIndex + 1).replace(/\./g, '').slice(0, 2)

  bulat = bulat.replace(/^0+(?=\d)/, '')
  if (bulat === '') { bulat = '0' }

  let bulatFormatted = ''
  for (let i = 0; i < bulat.length; i++) {
    if (i != 0 && (bulat.length - i) % 3 == 0) { bulatFormatted += ',' }
    bulatFormatted += bulat[i]
  }

  el.value = minus + bulatFormatted + (titikIndex !== -1 ? '.' + desimal : '')

  let posBaru = Math.max(0, el.value.length - posDariKanan)
  el.setSelectionRange(posBaru, posBaru)
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
