@extends('newmasterTest')
{{-- @extends('accounting.newmaster') --}}
@section('page-title', 'Bank')
@section('buttons')

@endsection

@section('css')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

{{-- Gudang-style list view (#page1) plus .dph-tb skin on #page2/#page3 and the entity-picker
     modals — see docs/new-design-gudang-style-guide.md. Same treatment as
     accounting/kas.blade.php (this page is its Bank twin — identical dbTrans/dbTransaksi
     query shape, just filtered on BBM/BBK instead of BKM/BKK). newmasterTest doesn't load
     report-table.css/tableMaster2.css itself — added here, page-local, so no other page on
     this shared layout is affected. tableMaster2.css's .btn-action-* rules carry !important
     (fixed there, not here), so the pill/action buttons below render correctly on this
     layout without a page-local override. The ~19 tabel_add_list_* picker modals keep their
     existing DataTables-driven search/paging behavior untouched (per the style guide's §13
     ask-first note on existing picker patterns) — only their table skin (.dph-tb) and footer
     buttons were restyled. --}}
<link rel="stylesheet" href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
<link rel="stylesheet" href="{!! URL::asset('css/tableMaster2.css') !!}?v={{ @filemtime(base_path('public/css/tableMaster2.css')) ?: '1' }}">
<link rel="stylesheet" href="{!! URL::asset('css/newmaster.css') !!}?v={{ @filemtime(base_path('public/css/newmaster.css')) ?: '1' }}">
<link rel="stylesheet" href="{!! URL::asset('css/pengajuandphtunai.css') !!}?v={{ @filemtime(base_path('public/css/pengajuandphtunai.css')) ?: '1' }}">

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

#tabel_add_list_bon_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}
#tabel_add_list_bon_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
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

  #tabel_add_list_lawan_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }
  #tabel_add_list_lawan_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

    #tabel_add_list_akumulasibiaya_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_akumulasibiaya_filter label input {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }
</style>
@endsection


@section('content')
@include('accounting.bank._list')
@include('accounting.bank._form')
@include('accounting.bank._detail')
@include('accounting.bank._pickers')
@include('accounting.bank._tunai-modal')
@endsection

@section('js')
<script>
  window.BANK_ROUTES = {
    bankdetailCetak: "{!! url('bankdetailCetak') !!}",
    bankgetnourutaktiva: "{!! url('bankgetnourutaktiva') !!}",
    banklistaktiva: "{!! url('banklistaktiva') !!}",
    banklistakumulasi: "{!! url('banklistakumulasi') !!}",
    banklistakumulasiinput: "{!! url('banklistakumulasiinput') !!}",
    banklistbiayainput: "{!! url('banklistbiayainput') !!}",
    banklistcosting: "{!! url('banklistcosting') !!}",
    banklistcustsupp: "{!! url('banklistcustsupp') !!}",
    banklistcustsuppumb: "{!! url('banklistcustsuppumb') !!}",
    banklistdepartemen: "{!! url('banklistdepartemen') !!}",
    banklistdevisi: "{!! url('banklistdevisi') !!}",
    banklistdph: "{!! url('banklistdph') !!}",
    banklistdphuht: "{!! url('banklistdphuht') !!}",
    banklistdpp: "{!! url('banklistdpp') !!}",
    banklistkasheader: "{!! url('banklistkasheader') !!}",
    banklistlawan: "{!! url('banklistlawan') !!}",
    banklistsubcosting: "{!! url('banklistsubcosting') !!}",
    banklisttunai: "{!! url('banklisttunai') !!}",
    banklisttunaix: "{!! url('banklisttunaix') !!}",
    banklistvalas: "{!! url('banklistvalas') !!}",
    bankloadall: "{!! url('bankloadall') !!}",
    bankprosesumb: "{!! url('bankprosesumb') !!}",
    bankspadd: "{!! url('bankspadd') !!}",
    bankspadddppdph: "{!! url('bankspadddppdph') !!}",
    bankspaddnewaktiva: "{!! url('bankspaddnewaktiva') !!}",
    bankspaddtemprumjual: "{!! url('bankspaddtemprumjual') !!}",
    bankspbatalotorisasi: "{!! url('bankspbatalotorisasi') !!}",
    bankspdeletetemprumjual: "{!! url('bankspdeletetemprumjual') !!}",
    bankspdetail: "{!! url('bankspdetail') !!}",
    bankspotorisasi: "{!! url('bankspotorisasi') !!}",
    bankspupdatetemprumjual: "{!! url('bankspupdatetemprumjual') !!}",
    globalfunctions_doLoadHeader: "{!! url('globalfunctions_doLoadHeader') !!}",
    globalfunctions_doSimpanHeader: "{!! url('globalfunctions_doSimpanHeader') !!}",
    kaslistbon: "{!! url('kaslistbon') !!}",
    kaslistcustsupptunai: "{!! url('kaslistcustsupptunai') !!}",
    kassptemphutpiut: "{!! url('kassptemphutpiut') !!}",
    kreditnotelistinvoice: "{!! url('kreditnotelistinvoice') !!}",
    kreditnotespadd: "{!! url('kreditnotespadd') !!}",
    kreditnotespdetail: "{!! url('kreditnotespdetail') !!}",
    perintahreturjuallistnobeli: "{!! url('perintahreturjuallistnobeli') !!}",
    perintahreturjuallistnoinvoice: "{!! url('perintahreturjuallistnoinvoice') !!}",
    spnobuktisimbol: "{!! url('spnobuktisimbol') !!}",
  };
  window.BANK_INITIAL_ROWS = @json($tempOutstanding);
</script>
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/bank.js') !!}?v={{ @filemtime(base_path('public/js/bank.js')) ?: '1' }}"></script>
@endsection
