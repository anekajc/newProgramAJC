@extends('newmasterTest')
{{-- @extends('accounting.newmaster') --}}
@section('page-title', 'Kas')
@section('buttons')

@endsection

@section('css')
<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

{{-- Gudang-style list view (#page1) plus .dph-tb skin on #page2/#page3 and the entity-picker
     modals — see docs/new-design-gudang-style-guide.md. newmasterTest doesn't load
     report-table.css/tableMaster2.css itself — added here, page-local, so no other page on
     this shared layout is affected. tableMaster2.css's .btn-action-* rules carry !important
     (fixed there, not here — see cetakpengajuandph.blade.php's note on the same collision),
     so the pill/action buttons below render correctly on this layout without a page-local
     override. The ~18 tabel_add_list_* picker modals keep their existing DataTables-driven
     search/paging behavior untouched (per the style guide's §13 ask-first note on existing
     picker patterns) — only their table skin (.dph-tb) and footer buttons were restyled. --}}
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
</style>
@endsection


@section('content')
@include('accounting.kas._list')
@include('accounting.kas._form')
@include('accounting.kas._detail')
@include('accounting.kas._pickers')
@include('accounting.kas._tunai-modal')
@endsection

@section('js')
<script>
  window.KAS_ROUTES = {
    banklistcosting: "{!! url('banklistcosting') !!}",
    banklistsubcosting: "{!! url('banklistsubcosting') !!}",
    globalfunctions_doLoadHeader: "{!! url('globalfunctions_doLoadHeader') !!}",
    globalfunctions_doSimpanHeader: "{!! url('globalfunctions_doSimpanHeader') !!}",
    kaschangekembaliuang: "{!! url('kaschangekembaliuang') !!}",
    kasdetailCetak: "{!! url('kasdetailCetak') !!}",
    kasgetnourutaktiva: "{!! url('kasgetnourutaktiva') !!}",
    kaslistaktiva: "{!! url('kaslistaktiva') !!}",
    kaslistakumulasi: "{!! url('kaslistakumulasi') !!}",
    kaslistakumulasiinput: "{!! url('kaslistakumulasiinput') !!}",
    kaslistbiayainput: "{!! url('kaslistbiayainput') !!}",
    kaslistbon: "{!! url('kaslistbon') !!}",
    kaslistcustsupp: "{!! url('kaslistcustsupp') !!}",
    kaslistcustsupptunai: "{!! url('kaslistcustsupptunai') !!}",
    kaslistcustsuppumb: "{!! url('kaslistcustsuppumb') !!}",
    kaslistdepartemen: "{!! url('kaslistdepartemen') !!}",
    kaslistdevisi: "{!! url('kaslistdevisi') !!}",
    kaslistdph: "{!! url('kaslistdph') !!}",
    kaslistdphuht: "{!! url('kaslistdphuht') !!}",
    kaslistdpp: "{!! url('kaslistdpp') !!}",
    kaslistkasheader: "{!! url('kaslistkasheader') !!}",
    kaslistlawan: "{!! url('kaslistlawan') !!}",
    kaslisttunai: "{!! url('kaslisttunai') !!}",
    kaslisttunaix: "{!! url('kaslisttunaix') !!}",
    kaslistvalas: "{!! url('kaslistvalas') !!}",
    kasloadall: "{!! url('kasloadall') !!}",
    kasprosesumb: "{!! url('kasprosesumb') !!}",
    kasspadd: "{!! url('kasspadd') !!}",
    kasspaddaktiva: "{!! url('kasspaddaktiva') !!}",
    kasspadddppdph: "{!! url('kasspadddppdph') !!}",
    kasspaddnewaktiva: "{!! url('kasspaddnewaktiva') !!}",
    kasspaddtemprumjual: "{!! url('kasspaddtemprumjual') !!}",
    kasspbatalotorisasi: "{!! url('kasspbatalotorisasi') !!}",
    kasspdeletetemprumjual: "{!! url('kasspdeletetemprumjual') !!}",
    kasspdetail: "{!! url('kasspdetail') !!}",
    kasspotorisasi: "{!! url('kasspotorisasi') !!}",
    kassptemphutpiut: "{!! url('kassptemphutpiut') !!}",
    kasspupdatetemprumjual: "{!! url('kasspupdatetemprumjual') !!}",
    kreditnotelistinvoice: "{!! url('kreditnotelistinvoice') !!}",
    kreditnotespadd: "{!! url('kreditnotespadd') !!}",
    kreditnotespdetail: "{!! url('kreditnotespdetail') !!}",
    perintahreturjuallistnobeli: "{!! url('perintahreturjuallistnobeli') !!}",
    perintahreturjuallistnoinvoice: "{!! url('perintahreturjuallistnoinvoice') !!}",
    spnobuktisimbol: "{!! url('spnobuktisimbol') !!}",
  };
  window.KAS_INITIAL_ROWS = @json($tempOutstanding);
</script>
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/kas.js') !!}?v={{ @filemtime(base_path('public/js/kas.js')) ?: '1' }}"></script>
@endsection
