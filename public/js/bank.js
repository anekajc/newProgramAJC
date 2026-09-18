/* ============================================================================
 * Tabel interaktif #page1 (list Bank) — port dari accounting/kas.blade.php (identical
 * dbTrans/dbTransaksi shape, filtered BBM/BBK instead of BKM/BKK), lihat
 * docs/new-design-gudang-style-guide.md. Menggantikan DataTable() polos + render gabungan
 * for-loop Blade lama plus loadAll() lama; satu fungsi renderTabel() dipakai untuk paint
 * pertama maupun tiap refresh loadAll() supaya keduanya tidak lagi bisa beda.
 * ========================================================================= */
let lastRows = (BANK_INITIAL_ROWS); // dbTrans/dbTransaksi sudah di-GROUP BY per NoBukti di controller
let globalOtorisasi = "2"; // filter modal: 2=Semua, 1=Sudah Otorisasi, 0=Belum Otorisasi

let tabelLen2 = 10;
let tabelPage2 = 1;

var g_href = 'bank';
var g_modeReport = '1';
var gcart_header = [];
var gsum_issubtotal = 0;
var gsum_isgrandtotal = 0;
var gct_desimal_max = 4;

function setDefaultHeader() {
    // [ field, label, visible, type, total, decimals ]
    gcart_header = [
        ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['TipeTransHd', 'Trans', 1, 'varchar', 0, 0],
        ['Perkiraan', 'Perk.', 1, 'varchar', 0, 0],
        ['Note', 'Ket.', 1, 'varchar', 0, 0],
        ['TotalRp', 'Jumlah Rp', 1, 'float', 0, 2],
        // 'varchar', bukan 'float' — nilainya dirender jadi badge Sudah/Belum.
        ['IsOtorisasi1', 'Otorisasi', 1, 'varchar', 0, 0],
        ['OtoUser1', 'User Oto', 1, 'varchar', 0, 0],
        ['TglOto1', 'Tgl Oto', 1, 'date', 0, 0],
    ];
}

function doSetHeader(_modereport, _isReset = false) {
    let _strHeader = (!_isReset) ? doLoadHeader(g_href, _modereport) : "";

    if (_strHeader != "") {
        gcart_header = doGetHeader(_strHeader);
    } else if ($.isFunction(window.setDefaultHeader)) {
        setDefaultHeader();
        doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
    }
}

function doLoadHeader(_href, _mode) {
    let _header = "";

    $.ajax({
        url: BANK_ROUTES.globalfunctions_doLoadHeader,
        type: "get",
        async: false,
        data: {
            href: _href,
            mode: _mode
        },
        success: function(res) {
            _header = (res.length > 0) ? res[0].header : "";
            if (res.length > 0) {
                gsum_issubtotal = Number(res[0].issubtotal);
                gsum_isgrandtotal = Number(res[0].isgrandtotal);
            }
        }
    })

    return _header;
}

function doGetHeader(_strHeader) {
    let _cart = [];

    _strHeader.split("||").forEach((item, i) => {
        let temp = [];
        temp.push(item.split(";;")[0]);
        temp.push(item.split(";;")[1]);
        temp.push(Number(item.split(";;")[2]));
        temp.push(item.split(";;")[3]);
        temp.push(Number(item.split(";;")[4]));
        temp.push(Number(item.split(";;")[5]));
        _cart.push(temp);
    });

    return _cart;
}

function doSimpanHeader(_href, _mode, _cart, _issubtotal, _isgrandtotal) {
    let _strHeader = "";

    _cart.forEach((item, i) => {
        if (i != 0) {
            _strHeader += '||';
        }
        _strHeader += item[0] + ';;' + item[1] + ';;' + item[2] + ';;' + item[3] + ';;' + item[4] + ';;' +
            item[5];
    });

    $.ajax({
        url: BANK_ROUTES.globalfunctions_doSimpanHeader,
        type: "get",
        async: false,
        data: {
            href: _href,
            mode: _mode,
            header: _strHeader,
            issubtotal: _issubtotal,
            isgrandtotal: _isgrandtotal
        },
        success: function(res) {
            // nothing to do
        }
    })
}

function doMoveHeader(_from, _to) {
    if (_from < 0 || _to < 0 || _from === _to) {
        return;
    }
    if (_from >= gcart_header.length || _to >= gcart_header.length) {
        return;
    }

    let _moved = gcart_header.splice(_from, 1)[0];
    gcart_header.splice(_to, 0, _moved);

    doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
}

function doButtonVisibility(_id) {
    gcart_header[_id][2] = (Number(gcart_header[_id][2]) === 1) ? 0 : 1;

    doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
}

function doSetDesimal(_index, _step) {
    let _next = Number(gcart_header[_index][5]) + _step;
    if (_next < 0 || _next > gct_desimal_max) {
        return;
    }

    gcart_header[_index][5] = _next;
    doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
}

function doButtonTotal(_index) {
    gcart_header[_index][4] = (Number(gcart_header[_index][4]) === 1) ? 0 : 1;

    doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
}

// Ambil field dari row tanpa peduli besar/kecil huruf.
function pickCI(r, key) {
    if (r[key] !== undefined) {
        return r[key];
    }
    let lk = String(key).toLowerCase();
    for (let k in r) {
        if (k.toLowerCase() === lk) {
            return r[k];
        }
    }
    return null;
}

function nullToEmpty(v) {
    return (v === null || v === undefined) ? '' : v;
}

function fmtYMD(v) {
    if (!v) {
        return '';
    }
    let date = new Date(v);
    if (isNaN(date)) {
        return '';
    }
    let day = ("0" + date.getDate()).slice(-2);
    let month = ("0" + (date.getMonth() + 1)).slice(-2);
    return date.getFullYear() + "/" + month + "/" + day;
}

// #modalOtorisasi: 2=Semua, 1=Sudah, 0=Belum — client-side saja.
function filterByOtorisasi(rows, filterVal) {
    if (filterVal === '1') {
        return rows.filter(r => Number(pickCI(r, 'IsOtorisasi1')) === 1);
    }
    if (filterVal === '0') {
        return rows.filter(r => Number(pickCI(r, 'IsOtorisasi1')) === 0);
    }
    return rows;
}

// Satu-satunya tempat yang menentukan tombol Aksi — dipakai renderTabel() untuk paint pertama
// MAUPUN tiap refresh loadAll(), jadi tidak bisa lagi berbeda seperti sebelumnya.
function aksiButtonsHtml(r) {
    const nobukti = r.NoBukti;
    const detailBtn =
        '<button type="button" class="btn-action-sm btn-action-warning" data-toggle="tooltip" title="Detail" onclick="buttonDetail(\'' +
        nobukti + '\', \'detail\')"><i class="bi bi-info"></i></button>';

    if (Number(pickCI(r, 'IsOtorisasi1')) === 1) {
        // Sudah otorisasi — Batal Otorisasi + Print
        return '<div class="action-buttons">' + detailBtn +
            '<button type="button" class="btn-action-sm btn-action-danger" data-toggle="tooltip" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' +
            nobukti + '\')"><i class="bi bi-key-fill"></i></button>' +
            '<button type="button" class="btn-action-sm btn-action-info" data-toggle="tooltip" title="Print" onclick="submitPrint(\'' +
            nobukti + '\')"><i class="bi bi-printer"></i></button>' +
            '</div>';
    }

    // Belum otorisasi — Koreksi + Otorisasi
    return '<div class="action-buttons">' + detailBtn +
        '<button type="button" class="btn-action-sm btn-action-primary" data-toggle="tooltip" title="Otorisasi" onclick="buttonDetail(\'' +
        nobukti + '\', \'otorisasi\')"><i class="bi bi-key"></i></button>' +
        '<button type="button" class="btn-action-sm btn-action-success" data-toggle="tooltip" title="Edit" onclick="buttonKoreksi(\'' +
        nobukti + '\')"><i class="bi bi-pencil-fill"></i></button>' +
        '</div>';
}

function renderTabel(resetPage) {
    if (resetPage !== false) {
        tabelPage2 = 1;
    }

    const cols = gcart_header.filter(c => c[2] === 1);
    const thead = document.querySelector('#mainTable thead');
    thead.innerHTML = ReportTable.headHtml(cols).replace('<tr>', '<tr><th class="rt-fixed-th">Actions</th>');

    const search = ($('#searchBox2').val() || '').trim().toLowerCase();
    let rows = lastRows;
    if (search) {
        rows = rows.filter(function(r) {
            return cols.some(function(c) {
                const v = pickCI(r, c[0]);
                return v != null && String(v).toLowerCase().indexOf(search) !== -1;
            });
        });
    }
    rows = filterByOtorisasi(rows, globalOtorisasi);

    const tbody = document.getElementById('tabel_data');
    $(tbody).find('[data-toggle="tooltip"]').tooltip('dispose');

    if (!rows.length) {
        tbody.innerHTML = '<tr class="empty-row"><td colspan="' + (cols.length + 1) + '">Tidak ada data</td></tr>';
        document.getElementById('footerLabel1').textContent = 'Tidak ada data';
        renderPager2(0, 0);
        return;
    }

    const totalRows = rows.length;
    const totalPages = tabelLen2 === -1 ? 1 : Math.max(1, Math.ceil(totalRows / tabelLen2));
    if (tabelPage2 > totalPages) {
        tabelPage2 = totalPages;
    }
    const pageRows = tabelLen2 === -1 ? rows : rows.slice((tabelPage2 - 1) * tabelLen2, tabelPage2 * tabelLen2);

    let html = '';
    pageRows.forEach(function(r) {
        html += '<tr class="data-row">';
        html += '<td class="text-center">' + aksiButtonsHtml(r) + '</td>';
        html += cols.map(function(c) {
            const v = pickCI(r, c[0]);
            if (c[0] === 'IsOtorisasi1') {
                return (Number(v) === 1) ?
                    '<td><span class="sp-badge is-active">Sudah</span></td>' :
                    '<td><span class="sp-badge is-inactive">Belum</span></td>';
            }
            if (c[3] === 'date') {
                return '<td>' + fmtYMD(v) + '</td>';
            }
            if (c[3] === 'float') {
                return '<td class="text-right">' + formatAngka(parseFloat(v || 0).toFixed(2)) +
                    '</td>';
            }
            return '<td>' + nullToEmpty(v) + '</td>';
        }).join('');
        html += '</tr>';
    });

    tbody.innerHTML = html;
    document.getElementById('footerLabel1').textContent = tabelLen2 === -1 ?
        'Menampilkan ' + totalRows + ' baris' :
        'Menampilkan ' + pageRows.length + ' dari ' + totalRows + ' baris';
    renderPager2(tabelPage2, totalPages);
    $('[data-toggle="tooltip"]').tooltip({
        container: 'body',
        boundary: 'window'
    });
}

function onLenChange2() {
    const v = Number(document.getElementById('tabelLen2').value);
    tabelLen2 = (v === -1 || v > 0) ? v : 10;
    renderTabel();
}

function gotoPage2(p) {
    tabelPage2 = p;
    renderTabel(false);
}

function renderPager2(page, totalPages) {
    const el = document.getElementById('pagerBtns1');
    if (!el) {
        return;
    }
    if (!totalPages || totalPages <= 1) {
        el.innerHTML = '';
        return;
    }

    function pgBtn(label, targetPage, active, disabled) {
        const cls = 'pg' + (active ? ' active' : '') + (disabled ? ' disabled' : '');
        const click = disabled ? '' : ' onclick="gotoPage2(' + targetPage + ')"';
        return '<div class="' + cls + '"' + click + '>' + label + '</div>';
    }

    let start = Math.max(1, page - 2);
    let end = Math.min(totalPages, start + 4);
    start = Math.max(1, end - 4);

    let html = pgBtn('&laquo;', page - 1, false, page <= 1);
    for (let p = start; p <= end; p++) {
        html += pgBtn(String(p), p, p === page, false);
    }
    html += pgBtn('&raquo;', page + 1, false, page >= totalPages);

    el.innerHTML = html;
}

/* -- FILTER MODAL (Otorisasi: Semua/Sudah Otorisasi/Belum) -- */
function updateFilterBadge() {
    let count = ($('#modalOtorisasi').val() !== '2') ? 1 : 0;
    $('#filterBadge').text(count + ' aktif');
}

function resetAllFilters() {
    $('#modalOtorisasi').val('2');
    updateFilterBadge();
}

$(document).on('show.bs.modal', '#modalFilter', function() {
    $('#modalOtorisasi').val(globalOtorisasi);
    updateFilterBadge();
});

$(document).on('change', '#modalFilter select.rt-native', updateFilterBadge);

function applyModalFilter() {
    globalOtorisasi = $('#modalOtorisasi').val();
    renderTabel();
    $('#modalFilter').modal('hide');
}

let listInvoice = []
// let tempNoBukti = ''

let tipemodalaktiva = 0

let flagtunai = 1
let tipeformdet = ''
let listtunai = []
let xislocalorexim = 0

let xaktiva = {}
let listAktiva = []
let dataLawanx = {}
let listData = []
let listCosting = []
let listSubCosting = []
let listPerkiraan = []
let listLawan = []
let listValas = []
let listDepartemen = []
let listDevisi = []

let xlawan = {}
let listBon = []
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
      doSetHeader(g_modeReport);
      ReportTable.init({
        table: '#mainTable',
        bar: '#rtBar',
        onChange: renderTabel
      });
      renderTabel();

        $("#tabel_add_list_akumulasibiaya").DataTable({
          "lengthChange": false,
            "paging": false ,
      });

        $("#tabel_add_list_lawan").DataTable({
        "lengthChange": false,
          "paging": false ,
          "columnDefs": [
        {
            type: 'string',
            targets: 0
        }
    ]
        //    "columnDefs": [
        // { "type": "date", "targets": [1] },
        // {  "className": "text-center", "targets": [3] },
      // ]
    });


        $("#tabel_add_list_bon").DataTable({
          "lengthChange": false,
            "paging": false ,
      });
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


function onclickPSKB () {

}


function buttonAddPickBiayaX (id , perkiraan) {

  // let _token = $("#_token").val();
  document.getElementById(`${id}`).value = perkiraan

  $('.showhidemodalbodyadd').hide();
  $('#modalAddListAktivaDetailX').show();
}



function refreshDataTableTunai () {
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: BANK_ROUTES.banklisttunaix,
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)

      if (!res.length) {

        alertify.warning("Tidak ada transaksi ditemukkan")
        return
      }
      listTunai = res

      // listLawan  = res
      let rowTable = ``
      let xdebet = 0
      let xsaldo = 0
      res.forEach((item, i) => {

        if (item.NoBukti == nobukti && item.StatusUID == 'I') {
          xdebet += Number(item.Debet)

        }

        console.log(xsaldo)
        if (Number(item.Debet) <= 0) {
          console.log("saldo")
          xsaldo = Number(item.Saldo)

        } else {
          console.log("minus")
          xsaldo += Number(item.Saldo)
        }
        console.log(item.Saldo)
        console.log(Number(item.Saldo))
        console.log(xsaldo ,'.')
        rowTable += `
        <tr>

        ${Number(item.Debet) <= 0 ?
          `<td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonTambahTunai(${i})" type="button" ><i class="bi bi-plus"></i></button></td>`

          :
          `<td class="text-center"><button class="btn btn-danger btn-sm" onclick="buttonDeleteTunai(${i})" type="button" ><i class="bi bi-dash"></i></button></td>`


        }

        <td>${item.NoFaktur}</td>
        <td>${item.NoRetur}</td>
        <td>${formatDate(item.Tanggal)}</td>
        <td>${item.NoBukti}</td>
        <td class="text-right">${formatAngka(parseFloat(item.Debet).toFixed(2))}</td>

        <td class="text-right">${formatAngka(parseFloat(item.Kredit).toFixed(2))}</td>
        <td class="text-right">${formatAngka(parseFloat(xsaldo).toFixed(2))}</td>

        <td>${item.Valas}</td>
        <td>${formatAngka(parseFloat(item.Kurs).toFixed(2))}</td>

        </tr>`
      });




      document.getElementById("AddAddJumlah").value = parseFloat(xdebet).toFixed(2)

      document.getElementById("tabel_data_add_list_tunai").innerHTML = rowTable


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}



function buttonTambahTunai (index) {
  let _token = $("#_token").val()
  let data = listTunai[index]
  let maxJumlah = $("#AddAddJumlahTunai").val()

  let choice = 'I'
  let tipetrans = 'L'
  let nobukti = $("#input_add_nobukti").val()
  let urut = 0



  let kredit = 0
  let debet = 0

  let sisasaldo = 0

  listTunai.forEach((item, i) => {
    if (data.NoFaktur == item.NoFaktur) {
      sisasaldo += Number(item.Saldo)
    }
  });



  if (Number(maxJumlah) > Number(sisasaldo)) {

    debet = sisasaldo

  } else {
    debet = maxJumlah

  }

  if (debet <= 0) {
    alertify.warning("Saldo habis")
    return
  }

  let noinvoice = 'LNS'

  $.ajax({
      url: BANK_ROUTES.kassptemphutpiut,
      type: "post",
      async: false,
      data: {
        choice,
        _token,
        data,
        tipetrans,
        nobukti,
        urut,
        kredit,
        debet,
        noinvoice

      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Pelunasan telah ditambah');
          let xjumlahtunai = $("#AddAddJumlahTunai").val()
          document.getElementById("AddAddJumlahTunai").value = Number(xjumlahtunai) -  Number(debet)
          refreshDataTableTunai()

        }
        if (res == 2) {
          alertify.warning('Sudah terdapat pelunasan');
        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}

function batalTunai () {

  $("#formTunai").modal('toggle')


}

function buttonAddPickCustSuppX (kodecustsupp, agent) {
  let _token = $("#_token").val();
  let lawan = $("#AddAddLawan").val();
  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();

  $.ajax({
    url: BANK_ROUTES.banklisttunai,
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: nobukti + nourut,
      lawan,
      kodecustsupp,
      agent
    },
    success: function(res) {
      console.log(res)

      if (!res.length) {

        alertify.warning("Tidak ada transaksi ditemukkan")
        return
      }
      listTunai = res
      flagtunai = 1


      document.getElementById("input_tunai_nobukti").value=nobukti
      document.getElementById("input_tunai_custsupp").value=kodecustsupp


      // listLawan  = res

      let xsaldo = 0
      let rowTable = ``
      res.forEach((item, i) => {
        if (Number(item.Debet) <= 0) {
          console.log("saldo")
          xsaldo = Number(item.Saldo)

        } else {
          console.log("minus")
          xsaldo += Number(item.Saldo)
        }
        rowTable += `
        <tr>

        ${Number(item.Debet) <= 0 ?
          `<td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonTambahTunai(${i})" type="button" ><i class="bi bi-plus"></i></button></td>`

          :
          `<td class="text-center"><button class="btn btn-danger btn-sm" onclick="buttonDeleteTunai(${i})" type="button" ><i class="bi bi-dash"></i></button></td>`


        }

        <td>${item.NoFaktur}</td>
        <td>${item.NoRetur}</td>
        <td>${formatDate(item.Tanggal)}</td>
        <td>${item.NoBukti}</td>
        <td class="text-right">${formatAngka(parseFloat(item.Debet).toFixed(2))}</td>

        <td class="text-right">${formatAngka(parseFloat(item.Kredit).toFixed(2))}</td>
        <td class="text-right">${formatAngka(parseFloat(xsaldo).toFixed(2))}</td>

        <td>${item.Valas}</td>
        <td class="text-right">${formatAngka(parseFloat(item.Kurs).toFixed(2))}</td>

        </tr>`
      });




      $("#form").modal("toggle");

      document.getElementById("tabel_data_add_list_tunai").innerHTML = rowTable

      $("#formTunai").modal("toggle");


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}



function buttonAddListXBiaya (id) {

  // let _token = $("#_token").val();
  $('#tabel_add_list_akumulasibiaya').DataTable().destroy();
  $.ajax({
    url: BANK_ROUTES.banklistbiayainput,
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.Perkiraan}</td>
        <td>${item.Keterangan}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickBiayaX('${id}','${item.Perkiraan}' )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });


      document.getElementById("tabel_data_add_list_akumulasibiaya").innerHTML = rowTable
      $("#tabel_add_list_akumulasibiaya").DataTable({
        "lengthChange": false,
          "paging": false ,
          "columnDefs": [
        {
            type: 'string',
            targets: 0 // Applies this definition to the first column (index 0)
        }
    ]
    });

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListAkumulasiBiaya').show();
      } else {
        alertify.warning("Biaya tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function buttonAddListXAkumulasi () {

  // let _token = $("#_token").val();
  $('#tabel_add_list_akumulasibiaya').DataTable().destroy();
  $.ajax({
    url: BANK_ROUTES.banklistakumulasiinput,
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.Perkiraan}</td>
        <td>${item.Keterangan}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickBiayaX('input_aktivax_akumulasi' ,'${item.Perkiraan}' )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });


      document.getElementById("tabel_data_add_list_akumulasibiaya").innerHTML = rowTable
      $("#tabel_add_list_akumulasibiaya").DataTable({
        "lengthChange": false,
          "paging": false ,
          "columnDefs": [
        {
            type: 'string',
            targets: 0 // Applies this definition to the first column (index 0)
        }
    ]
    });

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListAkumulasiBiaya').show();
      } else {
        alertify.warning("Biaya tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}


function submitAddAktivaX () {
  tipemodalaktiva = 0
    let noaktiva = $("#input_aktivax_noaktiva").val()
    let devisi = $("#input_aktivax_devisi").val()
    let keterangan = $("#input_aktivax_keterangan").val()
    let kuantum = $("#input_aktivax_kuantum").val()
    let persen = $("#input_aktivax_susut").val()
    let tglpemakaian = $("#input_aktivax_tglpemakaian").val()
    let metodepenyusutan = $("#input_aktivax_metodepenyusutan").val()
    let akumulasi = $("#input_aktivax_akumulasi").val()
    let groupaktiva = $("#input_aktivax_groupaktiva").val()
    let nobelakang = $("#input_aktivax_nobelakang").val()
    let biaya1 = $("#input_aktivax_biaya1").val()
    let biaya2 = $("#input_aktivax_biaya2").val()
    let biaya3 = $("#input_aktivax_biaya3").val()
    let persen1 = $("#input_aktivax_persen1").val()
    let persen2 = $("#input_aktivax_persen2").val()
    let persen3 = $("#input_aktivax_persen3").val()
    let tipeaktiva = $("#input_aktivax_tipeaktiva").val()
    let tglperolehan = $("#input_aktivax_tglperolehan").val()
    let choice = 'I'

    console.log({
      noaktiva ,
        devisi ,
        keterangan ,
        kuantum ,
        persen ,
        tglpemakaian ,
        metodepenyusutan ,
        akumulasi ,
        groupaktiva ,
        nobelakang ,
        biaya1 ,
        biaya2 ,
        biaya3 ,
        persen1 ,
        persen2 ,
        persen3 ,
        tipeaktiva ,
        tglperolehan ,
      })


      let _token = $("#_token").val()
      $.ajax({
        url: BANK_ROUTES.bankspaddnewaktiva,
        type: "post",
        async: false,
        data: {
          _token,
          noaktiva ,
            devisi ,
            keterangan ,
            kuantum ,
            persen ,
            tglpemakaian  ,
            metodepenyusutan ,
            akumulasi ,
            groupaktiva ,
            nobelakang ,
            biaya1 ,
            biaya2 ,
            biaya3 ,
            persen1 ,
            persen2 ,
            persen3 ,
            tipeaktiva ,
            tglperolehan , choice

        },
        success: function(res) {
          if (res == 2) {
            setNewNoAktiva(groupaktiva)

            alertify.warning("NoBelakang sudah direfresh, silahkan submit ulang")
          }
          if (res == 1) {
            document.getElementById("AddAddKodeLawan").value = dataLawanx.Kode
            document.getElementById("AddAddKeteranganLawan").value = dataLawanx.Keterangan

            document.getElementById("AddAddLawan").value = dataLawanx.Perkiraan

            $("#form").modal('toggle')
          }
          console.log(res)
          // document.getElementById("input_add_nobukti").value = res[0].Nobukti
          // document.getElementById("input_add_nourut").value = res[0].Nourut
          // document.getElementById("input_aktivax_namagroupaktiva").value = xlawan.Keterangan
          // document.getElementById("input_aktivax_groupaktiva").value = xlawan.Perkiraan
          // document.getElementById("input_aktivax_devisi").value = $('#AddAddKodeDevisi').val();
          // document.getElementById("input_aktivax_namadevisi").value = $('#AddAddNamaDevisi').val();
          // document.getElementById("input_aktivax_noaktiva").value = xlawan.Perkiraan + '.' + res[0].NoUrut
          // document.getElementById("input_aktivax_nobelakang").value = res[0].NoUrut
          // cari nourut

          // $('.showhidemodalbodyadd').hide();
          // $('#modalAddListAktivaDetailX').show();
        },
        error: function (err) {
          console.log(err)
          alertify.warning('Terjadi kesalahan silahkan refresh browser')
        }
      })



}

function buttonAddListBon () {
  listBon = []

  console.log('buttonAddListBon')


  let _token = $("#_token").val();
  let kodeperkiraan = $("#input_add_kodeperkiraan").val()

  if(!kodeperkiraan) {
      alertify.warning("Pilih Bank terlebih dahulu")
      return
  }

  $.ajax({
    url: BANK_ROUTES.kaslistbon,
    type: "post",
    async: false,
    data: {
      _token,
      kodeperkiraan
    },
    success: function(res) {
      console.log(res)
      listBon = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.NoBukti}</td>
        <td>${item.Penerima}</td>
        <td>${item.Keterangan}</td>
        <td>${item.Perkiraan}</td>
        <td class="text-right">${formatAngka(parseFloat(item.Debet).toFixed(2))}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickBon(${i},'${item.NoBukti}' , '${item.Debet}'  )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });

      $('#tabel_add_list_bon').DataTable().destroy();
      document.getElementById("tabel_data_add_list_bon").innerHTML = rowTable
      $("#tabel_add_list_bon").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListBon').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Bon tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function buttonAddPickBon (index, nobon, nilaibon) {
  console.log('buttonAddPickBon')
  document.getElementById("input_add_bon").value = nobon
  document.getElementById("input_add_nilaibon").value = parseFloat(nilaibon).toFixed(2)
  $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
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
  let simbol  = $("#input_add_simbol").val()
  let _token  = $("#_token").val()
  let kode  = $("#input_add_transaksi").val()
  $.ajax({
    url: BANK_ROUTES.spnobuktisimbol,
    type: "post",
    async: false,
    data: {
      _token,
      simbol,
      kode

    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
}


function cleanFormAddAdd () {

  if (tipeform == 'add') {
    document.getElementById("AddAddKodeDevisi").value = '01'
    document.getElementById("AddAddNamaDevisi").value = 'Accounting'
    document.getElementById("AddAddValas").value = 'IDR'
    document.getElementById("AddAddKurs").value = '1.00'

    document.getElementById("AddAddKodeDepartemen").value = ''
    document.getElementById("AddAddNamaDepartemen").value = ''


  }

  document.getElementById("AddAddKodeCosting").value = ''
  document.getElementById("AddAddNamaCosting").value = ''

  document.getElementById("AddAddKodeSubCosting").value = ''
  document.getElementById("AddAddNamaSubCosting").value = ''

  document.getElementById("AddAddKodeCustsupp").value = ''
  document.getElementById("AddAddNamaCustsupp").value = ''

  document.getElementById("AddAddLawan").value = ''
  document.getElementById("checkBoxSKB").checked = false
  document.getElementById("AddAddKeteranganLawan").value = ''
  document.getElementById("AddAddJumlah").value = '0.00'
  document.getElementById("AddAddKeterangan").value = ''
  document.getElementById("AddAddKeteranganDetail").value = ''





}


function closeShowHideAdd () {
  $('.showhide').hide();

}

function cleanFormAdd (tipe = 0) {

  if (tipe == 0) {
    document.getElementById("input_add_transaksi").value = 'BBK'
    onChangeTransaksi()
  }


  document.getElementById("input_add_kodeperkiraan").value = ''
  document.getElementById("input_add_keteranganperkiraan").value = ''
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_kepadaterima").value = ''

  document.getElementById("input_add_bon").value = ''
  document.getElementById("input_add_nilaibon").value = '0.00'



}


function submitAdd () {
  console.log("submitAdd")
  let _token  = $("#_token").val()
  let nobukti  = $("#input_add_nobukti").val()

  let tanggal  = $("#input_add_tanggal").val()
  let nopajak  = $("#input_add_nopajak").val()

  console.log(nobukti , tanggal , nopajak)

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

  if (Number(jumlah) <= 0) {
    alertify.warning("Jumlah <= 0")
    return
  }
  let nilaibon = 0

  // if (Number(jumlah) > Number(nilaibon)) {
  //   alertify.warning("Melebihi nilai bon")
  //   return
  // }

  let jumlahrp = Number(jumlah) * Number(kurs)


  let urut = 0

  let custsuppP = ''
  let custsuppL = ''
  let noaktivaP = ''
  let noaktivaL = ''
  let statusaktivaP = ''
  let statusaktivaL = ''

  let nobon = 0

  let kodebag = '-'

  let kodeP = ''
  let kodeL = ''
  let statusgiro = ''
  let simbol = $("#input_add_simbol").val()
  let flagsimbol = ''
  let kodecost = $("#AddAddKodeCosting").val()
  let kodesubcost = $("#AddAddKodeSubCosting").val()
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
  if (document.getElementById("checkBoxSKB").checked) {
    pSKB = 1
  }
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


  if (transaksi == 'BBK' && kodeFlag != 'AKV' && kodeFlag != 'AKM') {
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

  let xaktivagroupperkiraan = $("#input_aktiva_noaktiva").val();
  let xaktivatanggalperolehan = $("#input_aktiva_tglperolehan").val();
  let xaktivatanggalpemakaian= $("#input_aktiva_tglpemakaian").val();

  if (tipemodalaktiva == 0) {
     xaktivagroupperkiraan = $("#input_aktivax_noaktiva").val();
     xaktivatanggalperolehan = $("#input_aktivax_tglperolehan").val();
     xaktivatanggalpemakaian= $("#input_aktivax_tglpemakaian").val();



  }

  if (kodeFlag == 'AKV') {
    // kalo keluar dia masuk P
    // kalo masuk masuk ke L
    if (transaksi == "BBK") {
      custsuppP = $("#AddAddKodeCustsupp").val()
      noaktivaP = xaktivagroupperkiraan
      statusaktivaP = 'AKV+'
      kodeP = 'AKV'
      kodeL = ''
    } else {
      custsuppL = $("#AddAddKodeCustsupp").val()
      noaktivaL = xaktivagroupperkiraan
      statusaktivaL = 'AKV-'
      kodeL='AKV'
      kodeP = ''
    }
  }

  if (kodeFlag == 'AKM' ) {
    // kalo keluar dia masuk P
    // kalo masuk masuk ke L
    if (transaksi == "BBK") {
      custsuppP = $("#AddAddKodeCustsupp").val()
      noaktivaP = xaktivagroupperkiraan
      statusaktivaP = 'AKM+'
      kodeP = 'AKM'
      kodeL = ''
    } else {
      custsuppL = $("#AddAddKodeCustsupp").val()
      noaktivaL = xaktivagroupperkiraan
      statusaktivaL = 'AKM-'
      kodeL='AKM'
      kodeP = ''
    }



  }

    // tipeeform,
    // xaktivagroupperkiraan,
    // xaktivatanggalpemakaian,
    // xaktivatanggalperolehan,
    // choice,
    // _token,
    // nobukti ,
    // nourut,
    // transaksi,
    // note,
    // kodeperkiraan ,
    // tanggal,
    //
    // lampiran ,
    // keterangan2 ,
    // perkiraanx,
    // lawanx,
    //
    // kodedevisi ,
    // valas ,
    // kurs  ,
    // lawan ,
    // jumlah,
    // keterangan  ,
    // keterangandetail ,
    // kodedepartemen  ,
    //
    // kredit,
    // kreditrp,
    //
    // tphc,
    // jumlahrp ,
    //
    //
    // urut ,
    //
    // custsuppP ,
    // custsuppL,
    // noaktivaP ,
    // noaktivaL ,
    // statusaktivaP ,
    // statusaktivaL ,
    //
    // nobon ,
    // kodebag ,
    //
    // kodeP ,
    // kodeL ,
    // statusgiro ,
    // simbol,
    // flagsimbol ,
    // kodecost ,
    // kodesubcost ,
    // nodph ,
    // urutdph,
    // dppdph,
    // tp,
    // ppklx ,
    // nofaktur,
    // plok ,
    // nobons,
    // jmlrecord,
    // notitipan ,
    // uruttitipan,
    // pSKB,
    // custsupp})



    if (transaksi == 'BBK' && xislocalorexim == 1) {
      console.log("tunai add")
      $.ajax({
          url: BANK_ROUTES.bankspadd,
          type: "post",
          async: false,
          data: {
            xislocalorexim,
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
              alertify.success('Kas telah ditambah');
              lockFormAdd()
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

    } else if (transaksi == 'BBM' && kodeFlag == 'UHT') {
    $.ajax({
        url: BANK_ROUTES.bankspadd,
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
            lockFormAdd()
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
        url: BANK_ROUTES.bankspadddppdph,
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
        url: BANK_ROUTES.bankspadd,
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
            lockFormAdd()
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

  let nobon = ''
  let kodebag = '-'

  let kodeP = tempBarangAddEdit.KodeP
  let kodeL = tempBarangAddEdit.KodeL
  let statusgiro = tempBarangAddEdit.StatusGiro
  let simbol = $("#input_add_simbol").val()
  let flagsimbol = ''
  let kodecost = $("#AddAddKodeCosting").val()
  let kodesubcost = $("#AddAddKodeSubCosting").val()
  let nodph = tempBarangAddEdit.NODPH
  let urutdph = tempBarangAddEdit.urutDPH
  let dppdph = ''
  let tp = ''
  let ppklx = ''
  let nofaktur = ''
  let plok = 0
  let nobons = 0
  let jmlrecord = tipeform == 'add' ? 0 : 1
  let notitipan = tempBarangAddEdit.notitipan
  let uruttitipan = tempBarangAddEdit.URUTTITIPAN
  let pSKB = 0
  if (document.getElementById("checkBoxSKB").checked) {
    pSKB = 1
  }
  console.log(pSKB)


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
      url: BANK_ROUTES.bankspadd,
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
          lockFormAdd()
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



function buttonAddDelete (index) {


    tempBarangAddEdit = listData[index]






    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus Item ?',
        function() {

          let choice = "D"
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


          let jumlahrp = 0


          let urut = tempBarangAddEdit.Urut

          let custsuppP = tempBarangAddEdit.CustSuppP
          let custsuppL = tempBarangAddEdit.CustSuppL
          let noaktivaP = tempBarangAddEdit.NoAktivaP
          let noaktivaL = tempBarangAddEdit.NoAktivaL
          let statusaktivaP = tempBarangAddEdit.StatusAktivaP
          let statusaktivaL = tempBarangAddEdit.StatusAktivaL

          let nobon = ''
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
              url: BANK_ROUTES.bankspadd,
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
                  alertify.success('Bank telah dihapus');
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
        url: BANK_ROUTES.kreditnotespadd,
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


function buttonAddListCosting () {
  listCosting = []

  console.log('buttonAddListCosting')


  let _token = $("#_token").val();
  let lawan = $("#AddAddLawan").val();
  if(!lawan) {
    alertify.warning("Pilih lawan terlebih dahulu")
    return
  }
  console.log(lawan)

  $.ajax({
    url: BANK_ROUTES.banklistcosting,
    type: "post",
    async: false,
    data: {
      _token,
      kodelawan: lawan,
    },
    success: function(res) {
      console.log(res)
      listCosting  = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KodeCost}</td>
        <td>${item.NamaCost}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickCosting(${i},'${item.KodeCost}' , '${item.NamaCost}' )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });


      document.getElementById("tabel_data_add_list_costing").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListCosting').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Costing tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}



function buttonAddListSubCosting () {
  listSubCosting = []

  console.log('buttonAddListSubCosting')


  let _token = $("#_token").val();
  let costing = $("#AddAddKodeCosting").val();
  if(!costing) {
    alertify.warning("Pilih costing terlebih dahulu")
    return
  }

  $.ajax({
    url: BANK_ROUTES.banklistsubcosting,
    type: "post",
    async: false,
    data: {
      _token,
      kodecosting: costing,
    },
    success: function(res) {
      console.log(res)
      listSubCosting  = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KodeSubCost}</td>
        <td>${item.NamaSubCost}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSubCosting(${i},'${item.KodeSubCost}' , '${item.NamaSubCost}' )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });


      document.getElementById("tabel_data_add_list_subcosting").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListSubCosting').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Sub Costing tidak ditemukkan")
      }


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
    url: BANK_ROUTES.banklistlawan,
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
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickLawan(${i},'${item.Perkiraan}' , '${item.Keterangan}' , '${item.Simbol}', '${item.Kode}', '${item.iscost}', '${item.IsLokalOrExim}'  )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      $('#tabel_add_list_lawan').DataTable().destroy();

      document.getElementById("tabel_data_add_list_lawan").innerHTML = rowTable
      $("#tabel_add_list_lawan").DataTable({
      "lengthChange": false,
        "paging": false ,
        "columnDefs": [
      {
          type: 'string',
          targets: 0
      }
  ]
      //    "columnDefs": [
      // { "type": "date", "targets": [1] },
      // {  "className": "text-center", "targets": [3] },
    // ]
  });
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
    url: BANK_ROUTES.banklistdpp,
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
    url: BANK_ROUTES.banklistcustsuppumb,
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

function modalDPHUHT (dataLawan) {
  listDPH = []

  console.log('modalDPHUHT')

  console.log(dataLawan)


  let _token = $("#_token").val();
  let valas = $("#AddAddValas").val();

  $.ajax({
    url: BANK_ROUTES.banklistdphuht,
    type: "get",
    async: false,
    data: {
      _token,
      valas
    },
    success: function(res) {
      console.log(res)
      listDPH = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.Nobukti}</td>
        <td>${item.NOUM}</td>
        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td class="text-right">${item.DIBAYAR ? formatAngka(parseFloat(item.DIBAYAR).toFixed(2)) : '0.00'}</td>
        <td class="text-right">${item.KL ? formatAngka(parseFloat(item.KL).toFixed(2)) : '0.00'}</td>
        <td class="text-right">${item.LB ? formatAngka(parseFloat(item.LB).toFixed(2)) : '0.00'}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickDPH(${i} , '${dataLawan.Perkiraan}', '${dataLawan.Kode}' , '${dataLawan.Keterangan}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dphuht").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListDPHUHT').show();
        // $("#form").modal('toggle')
      } else {
        alertify.warning("DPH tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function modalDPH (dataLawan) {
  listDPH = []

  console.log('modalDPH')

  console.log(dataLawan)


  let _token = $("#_token").val();
  let valas = $("#AddAddValas").val();

  $.ajax({
    url: BANK_ROUTES.banklistdph,
    type: "get",
    async: false,
    data: {
      _token,
      valas
    },
    success: function(res) {
      console.log(res)
      listDPH = res
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
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickDPH(${i} , '${dataLawan.Perkiraan}', '${dataLawan.Kode}' , '${dataLawan.Keterangan}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dph").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListDPH').show();
        // $("#form").modal('toggle')
      } else {
        alertify.warning("DPH tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function buttonMinusUMB (index) {

  let _token = $("#_token").val();
  let umb = listUMB[index]
  let nobukti = $("#input_add_nobukti").val();
  $.ajax({
    url: BANK_ROUTES.bankspdeletetemprumjual,
    type: "post",
    async: false,
    data: {
      _token,
      noumb: umb.NOBUKTI,
      noretur: umb.NORETUR ,
      tanggal: umb.TANGGAL ,
      noso: umb.NOSO ,
      valas: umb.VALAS ,
      kurs: umb.KURS ,
      dpp: umb.DPP ,
      ppn: umb.PPN ,
      kodesupp: umb.KODESUPP ,
      subtotal: umb.SUBTOTAL ,
      nobukti: nobukti,
      urut: 0
    },
    success: function(res) {
      console.log(res)
      listUMB = res
      let rowTable = ``
      res.forEach((item, i) => {
        if (item.NORETUR == '') {
          rowTable += `
          <tr>
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>

          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>

          <td class="text-right">${parseFloat(item.PPN).toFixed(2)}</td>
          <td class="text-right">${parseFloat(item.SUBTOTAL).toFixed(2)}</td>

          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonPlusUMB(${i}  )" type="button" ><i class="bi bi-plus"></i></button></td>

          </tr>`


        } else {
          rowTable += `
          <tr style="background-color: #FF746C">
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>
          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>
          <td class="text-right">${parseFloat(item.PPN).toFixed(2)}</td>
          <td class="text-right">${parseFloat(item.SUBTOTAL).toFixed(2)}</td>

          <td class="text-center"><button class="btn btn-danger btn-sm" onclick="buttonMinusUMB(${i}  )" type="button" ><i class="bi bi-trash"></i></button></td>

          </tr>`

        }

      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = rowTable

      if (res.length) {
        document.getElementById("AddAddJumlah").value = res[0].totalqntx
      } else {
        document.getElementById("AddAddJumlah").value = '0.00'
        document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = `
          <tr>
            <td colspan=10 class="text-center">Data tidak ditemukkan</td>
          </tr>
        `
        alertify.warning("Data tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function buttonPlusUMB (index) {

  let _token = $("#_token").val();
  let umb = listUMB[index]
  let nobukti = $("#input_add_nobukti").val();
  $.ajax({
    url: BANK_ROUTES.bankspaddtemprumjual,
    type: "post",
    async: false,
    data: {
      _token,
      noumb: umb.NOBUKTI,
      noretur: "" ,
      tanggal: umb.TANGGAL ,
      noso: umb.NOSO ,
      valas: umb.VALAS ,
      kurs: umb.KURS ,
      dpp: umb.DPP ,
      ppn: umb.PPN ,
      kodesupp: umb.KODESUPP ,
      subtotal: umb.SUBTOTAL ,
      nobukti: nobukti,
      urut: 0
    },
    success: function(res) {
      console.log(res)
      listUMB = res
      let rowTable = ``
      res.forEach((item, i) => {
        if (item.NORETUR == '') {
          rowTable += `
          <tr>
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>
          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>

          <td class="text-center"><input id="add_inputPPNUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.PPN).toFixed(2)}' class="form-control text-right"  disabled></td>
          <td class="text-center"><input id="add_inputSUBTOTALUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.SUBTOTAL).toFixed(2)}' class="form-control text-right"  disabled></td>

          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonPlusUMB(${i}  )" type="button" ><i class="bi bi-plus"></i></button></td>

          </tr>`


        } else {
          rowTable += `
          <tr style="background-color: #FF746C">
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>
          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>

          <td class="text-center"><input id="add_inputPPNUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.PPN).toFixed(2)}' class="form-control text-right"  disabled></td>
          <td class="text-center"><input id="add_inputSUBTOTALUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.SUBTOTAL).toFixed(2)}' class="form-control text-right"  disabled></td>

          <td class="text-center"><button class="btn btn-danger btn-sm" onclick="buttonMinusUMB(${i}  )" type="button" ><i class="bi bi-trash"></i></button></td>

          </tr>`

        }

      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = rowTable

      if (res.length) {
        document.getElementById("AddAddJumlah").value = res[0].totalqntx
      } else {
        document.getElementById("AddAddJumlah").value = '0.00'
        document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = `
          <tr>
            <td colspan=10 class="text-center">Data tidak ditemukkan</td>
          </tr>
        `
        alertify.warning("Data tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}



function onChangeDPPUMB (index) {

  let qnt = $(`#add_inputDPPUMBQnt${index}`).val();

  if (Number(qnt) < 0) {
    document.getElementById(`add_inputDPPUMBQnt${index}`).value = '0.00'
    document.getElementById(`add_inputPPNUMBQnt${index}`).value = '0.00'
    document.getElementById(`add_inputSUBTOTALUMBQnt${index}`).value = '0.00'
    alertify.warning('Qnt < 0')
    return
  }

  let ppn = listUMB[index].ppnx

  let ppnx = Number(qnt) * Number(ppn)
  let subtotalx = Number(ppnx) + Number(qnt)





  let _token = $("#_token").val();
  let umb = listUMB[index]
  let nobukti = $("#input_add_nobukti").val();

  console.log(qnt, ppnx)
  $.ajax({
    url: BANK_ROUTES.bankspupdatetemprumjual,
    type: "post",
    async: false,
    data: {
      _token,
      noumb: umb.NOBUKTI,
      noretur: umb.NORETUR ,
      tanggal: umb.TANGGAL ,
      noso: umb.NOSO ,
      valas: umb.VALAS ,
      kurs: umb.KURS ,
      dpp: qnt ,
      ppn: ppnx ,
      kodesupp: umb.KODESUPP ,
      subtotal: subtotalx ,
      nobukti: nobukti,
      urut: 0
    },
    success: function(res) {
      console.log(res)
      listUMB = res
      let rowTable = ``
      res.forEach((item, i) => {
        if (item.NORETUR == '') {
          rowTable += `
          <tr>
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>
          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>

          <td class="text-center"><input id="add_inputPPNUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.PPN).toFixed(2)}' class="form-control text-right"  disabled></td>
          <td class="text-center"><input id="add_inputSUBTOTALUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.SUBTOTAL).toFixed(2)}' class="form-control text-right"  disabled></td>

          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonPlusUMB(${i}  )" type="button" ><i class="bi bi-plus"></i></button></td>

          </tr>`


        } else {
          rowTable += `
          <tr style="background-color: #FF746C">
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>
          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>

          <td class="text-center"><input id="add_inputPPNUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.PPN).toFixed(2)}' class="form-control text-right"  disabled></td>
          <td class="text-center"><input id="add_inputSUBTOTALUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.SUBTOTAL).toFixed(2)}' class="form-control text-right"  disabled></td>

          <td class="text-center"><button class="btn btn-danger btn-sm" onclick="buttonMinusUMB(${i}  )" type="button" ><i class="bi bi-trash"></i></button></td>

          </tr>`

        }

      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = rowTable

      if (res.length) {
        document.getElementById("AddAddJumlah").value = res[0].totalqntx
      } else {
        document.getElementById("AddAddJumlah").value = '0.00'
        document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = `
          <tr>
            <td colspan=10 class="text-center">Data tidak ditemukkan</td>
          </tr>
        `
        alertify.warning("Data tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })





}


function buttonAddPickCustDPHUHTBBM (kode, nama,perkiraanlawan, kodelawan , keteranganlawan) {




  document.getElementById("input_dphuhtbbm_namacustsupp").value = nama
  document.getElementById("input_dphuhtbbm_kodecustsupp").value = kode

  document.getElementById("AddAddLawan").value = perkiraanlawan
  document.getElementById("AddAddKodeLawan").value = kodelawan
  document.getElementById("AddAddKeteranganLawan").value = keteranganlawan



  let _token = $("#_token").val();

  $.ajax({
    url: BANK_ROUTES.bankprosesumb,
    type: "post",
    async: false,
    data: {
      _token,
      custsupp: kode
    },
    success: function(res) {
      console.log(res)
      listUMB = res
      let rowTable = ``
      res.forEach((item, i) => {
        if (item.NORETUR == '') {
          rowTable += `
          <tr>
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>
          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>

          <td class="text-center"><input id="add_inputPPNUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.PPN).toFixed(2)}' class="form-control text-right"  disabled></td>
          <td class="text-center"><input id="add_inputSUBTOTALUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.SUBTOTAL).toFixed(2)}' class="form-control text-right"  disabled></td>

          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonPlusUMB(${i}  )" type="button" ><i class="bi bi-plus"></i></button></td>

          </tr>`


        } else {
          rowTable += `
          <tr style="background-color: #FF746C">
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>
          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>

          <td class="text-center"><input id="add_inputPPNUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.PPN).toFixed(2)}' class="form-control text-right"  disabled></td>
          <td class="text-center"><input id="add_inputSUBTOTALUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.SUBTOTAL).toFixed(2)}' class="form-control text-right"  disabled></td>
<td class="text-center"><button class="btn btn-danger btn-sm" onclick="buttonMinusUMB(${i})" type="button" ><i class="bi bi-dash-lg"></i></button></td>

          </tr>`

        }

      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = rowTable



      if (res.length) {

        document.getElementById("AddAddJumlah").value = res[0].totalqntx
      } else {
        document.getElementById("AddAddJumlah").value = '0.00'
        document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = `
          <tr>
            <td colspan=10 class="text-center">Data tidak ditemukkan</td>
          </tr>
        `
        alertify.warning("Data tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })



}


function buttonAddPickDPH (indexDPH , perkiraanlawan, kodelawan , keteranganlawan) {


  tempDPPDPH = listDPH[indexDPH]

  document.getElementById("AddAddLawan").value = perkiraanlawan
  document.getElementById("AddAddKodeLawan").value = kodelawan
  document.getElementById("AddAddKeteranganLawan").value = keteranganlawan
  document.getElementById("AddAddJumlah").value = parseFloat(tempDPPDPH.DIBAYAR).toFixed(2)
  console.log(tempDPPDPH)

  // $('.showhideitem').hide();
  buttonAddListBatal()

}


function buttonAddPickDPP (indexDPP , perkiraanlawan, kodelawan , keteranganlawan) {


  tempDPPDPH = listDPP[indexDPP]

  document.getElementById("AddAddLawan").value = perkiraanlawan
  document.getElementById("AddAddKodeLawan").value = kodelawan
  document.getElementById("AddAddKeteranganLawan").value = keteranganlawan
  document.getElementById("AddAddJumlah").value = parseFloat(tempDPPDPH.DIBAYAR).toFixed(2)
  console.log(tempDPPDPH)

  // $('.showhideitem').hide();
  buttonAddListBatal()

}


function buttonAddListDepartemen () {
  listDepartemen = []

  console.log('buttonAddListDepartemen')


  let _token = $("#_token").val();

  $.ajax({
    url: BANK_ROUTES.banklistdepartemen,
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      listDepartemen  = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KDDEP}</td>
        <td>${item.NMDEP}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickDepartemen(${i},'${item.KDDEP}' , '${item.NMDEP}'  )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_departemen").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListDepartemen').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Departemen tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function buttonDeleteTunai (index) {
  console.log("buttonDeleteTunai")
  let _token = $("#_token").val()
  let data = listTunai[index]
  console.log(data)
  let maxJumlah = $("#AddAddJumlahTunai").val()

  let choice = 'D'
  let tipetrans = 'L'
  let nobukti = $("#input_add_nobukti").val()
  let urut = data.Urut

  let kredit = 0
  let debet = 0

  if(nobukti != data.NoBukti) {

    alertify.warning("Nobukti berbeda")
    return
  }
  if (Number(maxJumlah) > Number(data.Kredit)) {

    debet = data.Kredit

  } else {
    debet = maxJumlah

  }

  let noinvoice = 'LNS'

  $.ajax({
      url: BANK_ROUTES.kassptemphutpiut,
      type: "post",
      async: false,
      data: {
        choice,
        _token,
        data,
        tipetrans,
        nobukti,
        urut,
        kredit,
        debet,
        noinvoice

      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Pelunasan telah dihapus');
          let xjumlahtunai = $("#AddAddJumlahTunai").val()
          document.getElementById("AddAddJumlahTunai").value = Number(xjumlahtunai) +  Number(data.Debet)
          refreshDataTableTunai()

        }


      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}

function onChangeAddAddJumlah () {

    let trans =  $("#input_add_transaksi").val();
    if (trans == 'BBK' && xislocalorexim == 1 && tipeformdet == 'add') {
      let _token = $("#_token").val()
      let xnum =  $("#AddAddJumlah").val();
      document.getElementById("AddAddJumlahTunai").value= xnum
      let lawan = $("#AddAddLawan").val();
      if (flagtunai == 0) {
        $('.showhidemodalbodyadd').hide();

        $.ajax({
          url: BANK_ROUTES.kaslistcustsupptunai,
          type: "post",
          async: false,
          data: {
            _token,
            lawan
          },
          success: function(res) {
            console.log(res)




            // listLawan  = res

            let rowTable = ``
            res.forEach((item, i) => {

              rowTable += `
              <tr >

                <td>${item.KODECUSTSUPP}</td>
                <td>${item.NAMACUSTSUPP}</td>
                <td>${item.ALAMAT}</td>
                <td>${item.NAMAKOTA}</td>



                  <td class="text-center">

                    <button class="btn btn-primary btn-sm" onclick="buttonAddPickCustSuppX('${item.KODECUSTSUPP}', '${item.Agent}')" type="button" ><i class="bi bi-plus"></i></button>
                  </td>
            </tr>
              `
            });



            $('#tabel_add_list_customer').DataTable().destroy();

            document.getElementById("tabel_data_add_list_customer").innerHTML = rowTable
            $("#tabel_add_list_customer").DataTable({
              "lengthChange": false,
                "paging": false ,
          });

                    $('#modalAddListCustomer').show();
                    $("#form").modal('toggle')

          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })



      } else {
        $("#formTunai").modal('toggle')

      }

      // open modal kartu hutang


    }

}



function buttonAddListValas () {
  listValas= []

  console.log('buttonAddListValas')


  let _token = $("#_token").val();


  $.ajax({
    url: BANK_ROUTES.banklistvalas,
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



function buttonAddListDevisi () {
  listDevisi= []

  console.log('buttonAddListDevisi')


  let _token = $("#_token").val();


  $.ajax({
    url: BANK_ROUTES.banklistdevisi,
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      listDevisi = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.Devisi}</td>
        <td>${item.NamaDevisi}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickDevisi(${i},'${item.Devisi}' , '${item.NamaDevisi}' )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_devisi").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListDevisi').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Devisi tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListPerkiraan () {
  listPerkiraan= []

  console.log('buttonAddListPerkiraan')


  let _token = $("#_token").val();


  $.ajax({
    url: BANK_ROUTES.banklistkasheader,
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      listPerkiraan = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.Perkiraan}</td>
        <td>${item.Keterangan}</td>
        <td>${item.Simbol}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPerkiraan(${i},'${item.Perkiraan}' , '${item.Keterangan}' , '${item.Simbol}' )" type="button" ><i class="bi bi-plus"></i></button></td>

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


function buttonAddListInvoice () {
  listInvoice = []

  console.log('buttonAddListInvoice')


  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();

  if (!kodecustsupp  ) {
    alertify.warning("Pilih customer terlebih dahulu")
    return
  }

  $.ajax({
    url: BANK_ROUTES.kreditnotelistinvoice,
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp,
    },
    success: function(res) {
      console.log(res)
      listInvoice = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>
        <td>${item.NoFaktur}</td>
        <td>${formatDate(item.Tanggal,'/')}</td>
        <td>${formatDate(item.JatuhTempo,'/')}</td>
        <td>${item.KodeVls}</td>
        <td><input id="add_inputQnt${i}" style="height:30px; min-width: 130px" type="number" value='0.00' class="form-control text-right" onBlur="onChangeNilaiKurs(${i})"></td>

        <td><input id="add_inputKurs${i}" style="height:30px; min-width: 90px" type="number" value='1.00' class="form-control text-right" onBlur="onChangeNilaiKurs(${i})"></td>
        <td><input style="height:30px; min-width: 130px" id="add_inputQntRp${i}" type="number" value='0.00' class="form-control text-right"  disabled></td>

        <td class="text-right">${formatAngka(parseFloat(item.SaldoD).toFixed(2))}</td>
        <td class="text-right">${formatAngka(parseFloat(item.Saldo).toFixed(2))}</td>

        <td><input style="height:30px; min-width: 200px" id="add_inputKeterangan${i}" type="text" value='' class="form-control text-left" ></td>


        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_invoice").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListInvoice').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Tidak ada invoice untuk ditambah")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}



function buttonAddListNoBeli () {


  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();
  let noinvoice = $("#input_add_noinvoice").val();
  let noso = $("#input_add_noso").val();
  let kodebrg = $("#AddAddKodeBrg").val();

  if (!kodebrg ) {
    alertify.warning("Pilih barang terlebih dahulu")
    return
  }

  $('#tabel_add_list_nobeli').DataTable().destroy();
  $.ajax({
    url: BANK_ROUTES.perintahreturjuallistnobeli,
    type: "post",
    async: false,
    data: {
      _token,
      kodebrg,
      noso
    },
    success: function(res) {
      let rowTable = ``
      rowTable += `<tr>
      <td>-</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickNoBeli('-' , 0)" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.NOBUKTI}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickNoBeli('${item.NOBUKTI}' ,${item.urut} )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });





      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_nobeli").innerHTML = rowTable
      $("#tabel_add_list_nobeli").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListNoBeli').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function onChangeNilaiKursItem () {
  console.log('onChangeNilaiKursItem' )
  let onChangeQnt = $(`#AddEditNilai`).val();
  let onChangeKurs = $(`#AddEditKurs`).val();

  document.getElementById(`AddEditNilaiRp`).value = parseFloat(Number(onChangeQnt) * Number(onChangeKurs)).toFixed(2)

}

function onChangeNilaiKurs (index) {
  console.log('onChangeNilaiKurs' , index)
  let onChangeQnt = $(`#add_inputQnt${index}`).val();
  let onChangeKurs = $(`#add_inputKurs${index}`).val();

  document.getElementById(`add_inputQntRp${index}`).value = parseFloat(Number(onChangeQnt) * Number(onChangeKurs)).toFixed(2)

}

function buttonAddListNoInvoice () {


  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();

  if (!kodecustsupp) {
    alertify.warning("Pilih customer terlebih dahulu")
    return
  }

  $('#tabel_add_list_noinvoice').DataTable().destroy();
  $.ajax({
    url: BANK_ROUTES.perintahreturjuallistnoinvoice,
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.NOBUKTI}</td>
        <td>${item.TANGGAL}</td>
        <td>${item.NoSO}</td>
        <td>${item.NAMAGDG}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickNoInvoice('${item.NOBUKTI}' , '${item.NoSO}' , '${item.KODEGDG}', ${item.flagtipe}, ${item.ppn})" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });




      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_noinvoice").innerHTML = rowTable
      $("#tabel_add_list_noinvoice").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListNoInvoice').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListCustsupp () {
  console.log('buttonAddListCustsupp')
  $('#tabel_add_list_custsupp').DataTable().destroy();
  $.ajax({
    url: BANK_ROUTES.banklistcustsupp,
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.kodecustsupp}</td>
        <td>${item.namacustsupp}</td>
        <td>${item.alamat1}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickCustsupp('${item.kodecustsupp}' , '${item.namacustsupp}' , '${item.alamat1}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_custsupp").innerHTML = rowTable
      $("#tabel_add_list_custsupp").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListCustsupp').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddPickDevisi (index, kode, nama) {
  console.log('buttonAddPickDevisi')
  document.getElementById("AddAddKodeDevisi").value = kode
  document.getElementById("AddAddNamaDevisi").value = nama
  // document.getElementById("input_add_nobukti").value = simbol

  // $('.showhideitem').hide();
  // setNewNoBukti(simbol)
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickCustsupp (kode, nama) {
  console.log('buttonAddPickCustsupp')
  document.getElementById("AddAddKodeCustsupp").value = kode
  document.getElementById("AddAddNamaCustsupp").value = nama
  // document.getElementById("input_add_nobukti").value = simbol

  // $('.showhideitem').hide();
  // setNewNoBukti(simbol)
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickValas (index, kode, nama , kurs) {
  console.log('buttonAddPickValas')




  $('#rowCustsupp').hide();

  document.getElementById("AddAddValas").value = kode
  document.getElementById("AddAddKurs").value = parseFloat(kurs).toFixed(2)

  tempDPPDPH = {}

  document.getElementById("AddAddLawan").value = ''
  document.getElementById("AddAddKodeLawan").value = ''
  document.getElementById("AddAddKeteranganLawan").value = ''
  // document.getElementById("input_add_nobukti").value = simbol

  // $('.showhideitem').hide();
  // setNewNoBukti(simbol)
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function submitAddAktiva () {
  //  dataLawanx
  //

  console.log(dataLawanx)
  console.log(xaktiva)

  document.getElementById("AddAddKodeLawan").value = dataLawanx.Kode
  document.getElementById("AddAddKeteranganLawan").value = dataLawanx.Keterangan

  document.getElementById("AddAddLawan").value = dataLawanx.Perkiraan

  $("#form").modal('toggle')




}

function buttonAddPickAktiva (index) {
  tipemodalaktiva = 1
  document.getElementById("input_aktiva_keterangan").value = ''
  document.getElementById("input_aktiva_keterangan").disabled = false

  console.log(listAktiva[index])
  xaktiva = listAktiva[index]
  document.getElementById("input_aktiva_noaktiva").value = xaktiva.Perkiraan
  document.getElementById("input_aktiva_groupaktiva").value = xaktiva.NoMuka
  document.getElementById("input_aktiva_namagroupaktiva").value = xaktiva.NamaGroupAktiva
  // document.getElementById("input_aktiva_noaktiva").value = xaktiva.Perkiraan
  document.getElementById("input_aktiva_devisi").value = xaktiva.Devisi
  document.getElementById("input_aktiva_namadevisi").value = xaktiva.NamaDevisi

  document.getElementById("input_aktiva_keterangan").value = xaktiva.Keterangan
  document.getElementById("input_aktiva_tipeaktiva").value = Number(xaktiva.TipeAktiva)
  document.getElementById("input_aktiva_metodepenyusutan").value = xaktiva.Tipe

  document.getElementById("input_aktiva_kuantum").value = Number(xaktiva.Quantity) ? parseFloat(xaktiva.Quantity).toFixed(2) : '0.00'
  document.getElementById("input_aktiva_susut").value = Number(xaktiva.Persen) ? parseFloat(xaktiva.Persen).toFixed(2) : '0.00'
  document.getElementById("input_aktiva_akumulasi").value = xaktiva.Akumulasi

  document.getElementById("input_aktiva_biaya1").value = xaktiva.Biaya
  document.getElementById("input_aktiva_persen1").value = Number(xaktiva.PersenBiaya1) ? parseFloat(xaktiva.PersenBiaya1).toFixed(2) : '0.00'

  document.getElementById("input_aktiva_biaya2").value = xaktiva.Biaya2
  document.getElementById("input_aktiva_persen2").value = Number(xaktiva.PersenBiaya2) ? parseFloat(xaktiva.PersenBiaya2).toFixed(2) : '0.00'

  document.getElementById("input_aktiva_biaya3").value = xaktiva.biaya3
  document.getElementById("input_aktiva_persen3").value = Number(xaktiva.persenbiaya3) ? parseFloat(xaktiva.persenbiaya3).toFixed(2) : '0.00'
  document.getElementById("input_aktiva_tglperolehan").value = formatDate(new Date())
  document.getElementById("input_aktiva_tglpemakaian").value = xaktiva.Tanggal ? formatDate(xaktiva.Tanggal) : ''


  $('.showhidemodalbodyadd').hide();
  $('#modalAddListAktivaDetail').show();



}

function cleanFormAktivaDetailX () {
  document.getElementById("input_aktivax_keterangan").value = ''
  // document.getElementById("input_aktivax_keterangan").disabled = false

  // console.log(listAktiva[index])
  // xaktiva = listAktiva[index]
  // xkodelawan
  document.getElementById("input_aktivax_noaktiva").value = ''
  document.getElementById("input_aktivax_groupaktiva").value = ''
  document.getElementById("input_aktivax_namagroupaktiva").value = ''
  // document.getElementById("input_aktivax_noaktiva").value = xaktiva.Perkiraan
  document.getElementById("input_aktivax_devisi").value = ''
  document.getElementById("input_aktivax_namadevisi").value = ''

  document.getElementById("input_aktivax_keterangan").value = ''
  document.getElementById("input_aktivax_tipeaktiva").value = 0
  document.getElementById("input_aktivax_metodepenyusutan").value = 'L'

  document.getElementById("input_aktivax_kuantum").value =  '1.00'
  document.getElementById("input_aktivax_susut").value = '0.00'
  document.getElementById("input_aktivax_akumulasi").value = ''

  document.getElementById("input_aktivax_biaya1").value = '-'
  document.getElementById("input_aktivax_persen1").value = '0.00'

  document.getElementById("input_aktivax_biaya2").value = ''
  document.getElementById("input_aktivax_persen2").value = '0.00'

  document.getElementById("input_aktivax_biaya3").value = ''
  document.getElementById("input_aktivax_persen3").value = '0.00'
  document.getElementById("input_aktivax_tglperolehan").value = formatDate(new Date())
  document.getElementById("input_aktivax_tglpemakaian").value = formatDate(new Date())


}


function setNewNoAktiva (groupaktiva) {
  let _token = $("#_token").val()
  $.ajax({
    url: BANK_ROUTES.bankgetnourutaktiva,
    type: "post",
    async: false,
    data: {
      nomuka : xlawan.Perkiraan,
      _token

    },
    success: function(res) {

      console.log(res)
      // document.getElementById("input_add_nobukti").value = res[0].Nobukti
      // document.getElementById("input_add_nourut").value = res[0].Nourut
      document.getElementById("input_aktivax_noaktiva").value = xlawan.Perkiraan + '.' + res[0].NoUrut
      document.getElementById("input_aktivax_nobelakang").value = res[0].NoUrut
      // cari nourut

    }})
}

function buttonAddNewAktiva () {
  console.log('buttonAddNewAktiva')
  cleanFormAktivaDetailX()
  let _token = $("#_token").val()
  $.ajax({
    url: BANK_ROUTES.bankgetnourutaktiva,
    type: "post",
    async: false,
    data: {
      nomuka : xlawan.Perkiraan,
      _token

    },
    success: function(res) {

      console.log(res)
      // document.getElementById("input_add_nobukti").value = res[0].Nobukti
      // document.getElementById("input_add_nourut").value = res[0].Nourut
      document.getElementById("input_aktivax_namagroupaktiva").value = xlawan.Keterangan
      document.getElementById("input_aktivax_groupaktiva").value = xlawan.Perkiraan
      document.getElementById("input_aktivax_devisi").value = $('#AddAddKodeDevisi').val();
      document.getElementById("input_aktivax_namadevisi").value = $('#AddAddNamaDevisi').val();
      document.getElementById("input_aktivax_noaktiva").value = xlawan.Perkiraan + '.' + res[0].NoUrut
      document.getElementById("input_aktivax_nobelakang").value = res[0].NoUrut
      // cari nourut

      $('.showhidemodalbodyadd').hide();
      $('#modalAddListAktivaDetailX').show();
    }})


}


function modalAktiva (dataLawan) {
  listAktiva = []

  console.log('modalAktiva')
  dataLawanx = dataLawan
  console.log(dataLawan)
  $('#buttonAddNewAktiva').hide();


  let _token = $("#_token").val();
  let devisi = $("#AddAddKodeDevisi").val();
  let lawan = dataLawan.Perkiraan
  let xkodelawan = dataLawan.Kode

  if (xkodelawan == 'AKV') {
    $.ajax({
      url: BANK_ROUTES.banklistaktiva,
      type: "post",
      async: false,
      data: {
        _token,
        devisi,
        perkiraan: lawan
      },
      success: function(res) {
        console.log(res)
        listAktiva = res
        let rowTable = ``
        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td>${item.Perkiraan}</td>
          <td>${item.Keterangan}</td>
          <td>${formatDate(item.Tanggal)}</td>

          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickAktiva(${i} , '${dataLawan.Perkiraan}', '${dataLawan.Kode}' , '${dataLawan.Keterangan}')" type="button" ><i class="bi bi-plus"></i></button></td>

          </tr>`
        });






        // if(!res.length) {
        //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
        // }

        document.getElementById("tabel_data_add_list_aktiva").innerHTML = rowTable

        $('#buttonAddNewAktiva').show();
        if (res.length) {


          // $("#form").modal('toggle')
        } else {
          document.getElementById("tabel_data_add_list_aktiva").innerHTML = `
            <tr>
              <td colspan=4>Belum ada data</td>
            </tr>
          `
        }

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListAktiva').show();


      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })

  } else {
    $.ajax({
      url: BANK_ROUTES.banklistakumulasi,
      type: "post",
      async: false,
      data: {
        _token,
        perkiraan: lawan
      },
      success: function(res) {
        console.log(res)
        listAktiva = res
        let rowTable = ``
        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td>${item.Perkiraan}</td>
          <td>${item.Keterangan}</td>
          <td>${formatDate(item.Tanggal)}</td>

          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickAktiva(${i} , '${dataLawan.Perkiraan}', '${dataLawan.Kode}' , '${dataLawan.Keterangan}')" type="button" ><i class="bi bi-plus"></i></button></td>

          </tr>`
        });






        // if(!res.length) {
        //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
        // }

        document.getElementById("tabel_data_add_list_aktiva").innerHTML = rowTable


        if (res.length) {

          $('.showhidemodalbodyadd').hide();
          $('#modalAddListAktiva').show();
          // $("#form").modal('toggle')
        } else {
          alertify.warning("Akumulasi tidak ditemukkan")
        }


      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })

  }




}


function buttonAddPickPerkiraan (index, perkiraan, keterangan , simbol) {
  console.log('buttonAddPickPerkiraan')
  document.getElementById("input_add_kodeperkiraan").value = perkiraan
  document.getElementById("input_add_keteranganperkiraan").value = keterangan
  document.getElementById("input_add_simbol").value = simbol
  // document.getElementById("input_add_nobukti").value = simbol
  document.getElementById("input_add_bon").value = ''
  document.getElementById("input_add_nilaibon").value = '0.00'
  $('.showhideitem').hide();
  setNewNoBukti(simbol)
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickLawan (index, perkiraan, keterangan , simbol, kode, iscost, islocalorexim) {
  let trans =  $("#input_add_transaksi").val();
  console.log(trans)
  xlawan = listLawan[index]
  xislocalorexim = islocalorexim
  document.getElementById("AddAddKodeCosting").value = ''
  document.getElementById("AddAddNamaCosting").value = ''
  document.getElementById("AddAddNamaSubCosting").value = ''
  document.getElementById("AddAddKodeSubCosting").value = ''
  if (trans == 'BBK' && islocalorexim == 1) {

    flagtunai = 0
    listtunai = []
    console.log(index, perkiraan, keterangan , simbol, kode)
    document.getElementById("AddAddLawan").value = perkiraan
    document.getElementById("AddAddKodeLawan").value = kode
    document.getElementById("AddAddKeteranganLawan").value = keterangan
    buttonAddListBatal()
    return
  }
  if (kode == 'UHT' && trans == 'BBM') {

    modalDPHUHTBBM(listLawan[index])



    document.getElementById("AddAddJumlah").disabled = true
    document.getElementById("AddAddJumlah").value = '0.00'



    return
  }

  document.getElementById("AddAddJumlah").disabled = false
  document.getElementById("AddAddJumlah").value = '0.00'




  if ( kode == 'HT' ) {
    modalDPH(listLawan[index])

    return
  }

  if (kode == 'AKV' || kode == 'AKM' ) {
    modalAktiva(listLawan[index])

    return
  }



  if (kode == 'UHT' && trans == 'BBK') {
    modalDPHUHT(listLawan[index])

    return
  }






  if (perkiraan == '113400') {

    document.getElementById("AddAddKodeCustsupp").value = ''
    document.getElementById("AddAddNamaCustsupp").value = ''
    document.getElementById("AddAddLawan").value = perkiraan
    document.getElementById("AddAddKodeLawan").value = kode
    document.getElementById("AddAddKeteranganLawan").value = keterangan

    buttonAddListBatal()
    $('#rowCustsupp').show();

    return
  }

  $('#rowCustsupp').hide();

  $('#rowCosting').hide();
  $('#rowSubCosting').hide();


  if(Number(iscost) > 0 ) {
    $('#rowCosting').show();
    $('#rowSubCosting').show();
  }

  $('#rowCustsupp').hide();

  if (kode == 'PT' ) {
    modalDPP(listLawan[index])

    return
  }
  console.log('buttonAddPickLawan')
  console.log(index, perkiraan, keterangan , simbol, kode)
  document.getElementById("AddAddLawan").value = perkiraan
  document.getElementById("AddAddKodeLawan").value = kode
  document.getElementById("AddAddKeteranganLawan").value = keterangan

  // $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
}



function buttonAddPickCosting (index, kode, nama) {


  console.log('buttonAddPickCosting')
  document.getElementById("AddAddNamaCosting").value = nama
  document.getElementById("AddAddKodeCosting").value = kode
  document.getElementById("AddAddNamaSubCosting").value = ''
  document.getElementById("AddAddKodeSubCosting").value = ''

  // $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickSubCosting (index, kode, nama) {


  console.log('buttonAddPickSubCosting')
  document.getElementById("AddAddNamaSubCosting").value = nama
  document.getElementById("AddAddKodeSubCosting").value = kode

  // $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
}


function buttonAddPickDepartemen (index, kode, nama) {


  console.log('buttonAddPickDepartemen')
  document.getElementById("AddAddNamaDepartemen").value = nama
  document.getElementById("AddAddKodeDepartemen").value = kode

  // $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickCustomer (kode, nama , alamat) {
  console.log('buttonAddPickCustomer')
  console.log(kode,nama,alamat)
  document.getElementById("AddAddKodeCustsupp").value = kode
  document.getElementById("AddAddNamaCustsupp").value = nama

  $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
}
function onChangeQtyEdit () {

  console.log('onChangeQtyEdit')
  console.log('tempBarangAddEdit' , tempBarangAddEdit)

  let qty = $("#AddEditInputQty").val();
  let nosat = $("#AddEditInputNosat").val();
  if (jQuery.isEmptyObject(tempBarangAddEdit)) {
    console.log('g ada barang')
  } else {

    console.log('ada barang')
    let tempIsi = nosat == 1 ? tempBarangAddEdit.ISI1 : tempBarangAddEdit.ISI2
    console.log(tempIsi)
    let tempTotalQty = Number(tempIsi) * Number(qty)

    document.getElementById("AddEditInputQty1").value = tempTotalQty / tempBarangAddEdit.ISI1
    document.getElementById("AddEditInputQty2").value = tempTotalQty / tempBarangAddEdit.ISI2


  }
}

function onChangeQty () {

  console.log('onChangeQty')
  console.log('tempBarangAddAdd' , tempBarangAddAdd)
  let qty = $("#AddAddInputQty").val();
  let nosat = $("#AddAddInputNosat").val();
  console.log('qty' , qty)
  console.log('nosat' , nosat)

  if (jQuery.isEmptyObject(tempBarangAddAdd)) {
    console.log('g ada barang')
  } else {

    console.log('ada barang')
    let tempIsi = nosat == 1 ? tempBarangAddAdd.Isi1 : tempBarangAddAdd.Isi2
    console.log(tempIsi)
    let tempTotalQty = Number(tempIsi) * Number(qty)

    document.getElementById("AddAddInputQty1").value = tempTotalQty / tempBarangAddAdd.Isi1
    document.getElementById("AddAddInputQty2").value = tempTotalQty / tempBarangAddAdd.Isi2


  }

}

function buttonAddPickBarang (index) {
  console.log('buttonAddPickBarang')
  tempBarangAddAdd = listBarang[index]

  console.log('tempBarangAddAdd', tempBarangAddAdd)
  document.getElementById("AddAddKodeBrg").value = tempBarangAddAdd.KodeBrg
  document.getElementById("AddAddNamaBrg").value = tempBarangAddAdd.NamaBrg ? tempBarangAddAdd.NamaBrg : tempBarangAddAdd.NamaBrgx
  document.getElementById("AddAddInputQty").value = tempBarangAddAdd.QntSisa
  document.getElementById("AddAddInputQty1").value = tempBarangAddAdd.Qnt1Sisa
  document.getElementById("AddAddInputQty2").value = tempBarangAddAdd.Qnt2Sisa

  document.getElementById("AddAddInputSat1").value = tempBarangAddAdd.SAT1
  document.getElementById("AddAddInputSat2").value = tempBarangAddAdd.SAT2

  let selectOption = ''
  if (tempBarangAddAdd.SAT1) {
    selectOption += `<option value=1 ${tempBarangAddAdd.NoSat == 1 ? 'selected' : ''}>SAT1 - ${tempBarangAddAdd.SAT1}</option>`
  }
  if (tempBarangAddAdd.SAT2) {
    selectOption += `<option value=2 ${tempBarangAddAdd.NoSat == 2 ? 'selected' : ''}>SAT2 - ${tempBarangAddAdd.SAT2}</option>`
  }
  document.getElementById("AddAddInputNosat").innerHTML = selectOption






  buttonAddListBatal()
  // $("#form").modal('toggle')
}



function buttonAddPickNoInvoice (nobukti, noso , kodegdg, flagtipe, ppn) {
  console.log('buttonAddPickNoInvoice')
  document.getElementById("input_add_noinvoice").value = nobukti
  document.getElementById("input_add_noso").value = noso
  document.getElementById("input_add_gudang").value = kodegdg
  document.getElementById("input_add_flagtipe").value = flagtipe
  document.getElementById("input_add_ppn").value = ppn
  $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
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
  // document.getElementById("buttonAddListBon").disabled = true
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
  $.ajax({
    url: BANK_ROUTES.bankspdetail,
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
                  <td class="text-center">
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDelete(${i}  )"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>

              `
      });

      document.getElementById("addTableData").innerHTML = rowTable


        document.getElementById("input_add_transaksi").value = listData[0].TipeTransHD
        document.getElementById("input_add_kodeperkiraan").value = listData[0].PerkiraanHd
        document.getElementById("input_add_keteranganperkiraan").value = listData[0].NamaPerkiraanHd
        document.getElementById("input_add_kepadaterima").value = listData[0].Note
        document.getElementById("input_add_nobukti").value = listData[0].NoBukti
        document.getElementById("input_add_bon").value = listData[0].Nobon
        document.getElementById("input_add_nilaibon").value = listData[0].nilaibon ? parseFloat(listData[0].nilaibon).toFixed(2) : '0.00'
        // document.getElementById("input_add_transaksi").value = listData[0].NamaCustSupp
        // document.getElementById("input_add_alamatcustomer").value = listData[0].Alamat1
        // document.getElementById("input_add_nobukti").value = listData[0].NoBukti
        document.getElementById("input_add_tanggal").value = formatDate(listData[0].Tanggal)










    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
}


//
function formatAngkaParse (angka) {

        return parseFloat(angka).toFixed(2)
      }

      function formatAngkaVal (angka) {
        return Number(angka.split(',').join(''))
      }



function refreshDataTableDetail (nobukti) {
  console.log('refreshDataDetail' , nobukti)
  let _token = $("#_token").val();
  $.ajax({
    url: BANK_ROUTES.bankspdetail,
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
        document.getElementById("input_detail_bon").value = listData[0].Nobon
        document.getElementById("input_detail_nilaibon").value = listData[0].nilaibon ? parseFloat(listData[0].nilaibon).toFixed(2) : '0.00'

        // document.getElementById("input_detail_transaksi").value = listData[0].NamaCustSupp
        // document.getElementById("input_detail_alamatcustomer").value = listData[0].Alamat1
        // document.getElementById("input_detail_nobukti").value = listData[0].NoBukti
        document.getElementById("input_detail_tanggal").value = formatDate(listData[0].Tanggal)










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
    url: BANK_ROUTES.bankspotorisasi,
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

// function buttonDetail (nobukti) {
//   document.getElementById("divOto").style.display = "none";
//
//   let _token = $("#_token").val();
//   $.ajax({
//     url: BANK_ROUTES.kreditnotespdetail,
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



function buttonDetail (nobukti , tipe = 'detail') {
  console.log('buttonkoreksi' , nobukti )






  refreshDataTableDetail(nobukti)


  if (!listData.length) {
    alertify.warning("Data tidak ditemukkan")
    return
  }
  $('.showhidepage3').hide();

  if (tipe == 'otorisasi') {
    $('.page3otorisasi').show();
  } else {
    $('.page3detail').show();
  }


  // $('.showhideitem').hide();
  $('.showhidePart').hide();
  let value = $("#input_detail_transaksi").val()
  $(`.part${value}`).show();


  // $('#formAdd').show();
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
  cleanFormAdd()



  document.getElementById("input_add_tanggal").disabled = true
  // document.getElementById("buttonAddListBon").disabled = true
  document.getElementById("input_add_kepadaterima").disabled = true
  document.getElementById("buttonAddListPerkiraan").disabled = true
  document.getElementById("input_add_transaksi").disabled = true

  refreshDataTable(nobukti)


  if (!listData.length) {
    alertify.warning("Data tidak ditemukkan")
    return
  }
  console.log(listData)
  console.log(listData[0].isOtorisasi1)
  if (listData[0].IsOtorisasi1 == 1) {
    alertify.warning("Bank sudah diotorisasi")
    return
  }

  $('.showhideitem').hide();
  $('.showhidePart').hide();
  let value = $("#input_add_transaksi").val()
  $(`.part${value}`).show();


  $('#formAdd').show();
  $('.mainpage').hide();
  $('#page2').show();

  document.getElementById("AddAddKodeDevisi").value = '01'
  document.getElementById("AddAddNamaDevisi").value = 'Accounting'
  document.getElementById("AddAddValas").value = 'IDR'
  document.getElementById("AddAddKurs").value = '1.00'

  document.getElementById("AddAddKodeDepartemen").value = ''
  document.getElementById("AddAddNamaDepartemen").value = ''
}

function buttonAdd (nobukti) {
  console.log('buttonAdd' , nobukti)

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  document.getElementById("input_add_tanggal").disabled = false
  // document.getElementById("buttonAddListBon").disabled = false
  document.getElementById("input_add_kepadaterima").disabled = false
  document.getElementById("buttonAddListPerkiraan").disabled = false
  document.getElementById("input_add_transaksi").disabled = false




  document.getElementById("addTableData").innerHTML = `<td colspan=12 class="text-center">Belum ada data</td>`
  tipeform = 'add'
  // unlockFormAdd()
  $('.showhideitem').hide();
  // $('.showhideform').hide();
  $('#formAdd').show();
  // $("#form").modal('toggle')

  // input_add_nobukti
  // document.getElementById("input_add_nobukti").value = nobukti

  cleanFormAdd()
  // setNewNoBukti()
  document.getElementById("input_add_transaksi").value = 'BBK'
  onChangeTransaksi()

  $('.mainpage').hide();
  $('#page2').show();

}

function buttonAddAddItem () {
  // console.log(document.getElementById("checkBoxKembaliUang").checked)
    let value = $("#input_add_kodeperkiraan").val();
    if(!value) {
      alertify.warning("Pilih perkiraan terlebih dahulu")
      return
    }

    tipeformdet = 'add'
    flagtunai = 0

    $('#buttonSubmitAddAdd').show();
    $('#buttonSubmitAddEdit').hide();

    $('#labelAddAddItem').show();
    $('#labelAddEditItem').hide();
    $('#rowCustsupp').hide();
    $('#rowSubCosting').hide();
    $('#rowCosting').hide();
    // $('#rowCustsupp').show();
    // document.getElementById("buttonSubmitAddAdd").style.display = "block";
    // // document.getElementById("buttonSubmitAddEdit").style.display = "none";
    // document.getElementById("labelAddAddItem").style.display = "block";
    // document.getElementById("labelAddEditItem").style.display = "none";
    unlockFormAddAdd()
    $('.showhideitem').hide();
    document.getElementById("buttonAddListCosting").disabled = false
    document.getElementById("buttonAddListSubCosting").disabled = false
    document.getElementById("buttonAddListCustsupp").disabled = false

    cleanFormAddAdd()
    $('#formAddAdd').show();

}

function buttonAddEditItem (i) {

  tipeformdet = 'edit'
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

  document.getElementById("AddAddKodeCosting").value = tempBarangAddEdit.KODECOST ? tempBarangAddEdit.KODECOST : ''
  document.getElementById("AddAddNamaCosting").value = tempBarangAddEdit.NamaCost ? tempBarangAddEdit.NamaCost : ''

  document.getElementById("AddAddKodeSubCosting").value = tempBarangAddEdit.KODESUBCOST ? tempBarangAddEdit.KODESUBCOST : ''
  document.getElementById("AddAddNamaSubCosting").value = tempBarangAddEdit.NamaSubCost ? tempBarangAddEdit.NamaSubCost : ''

  if (Number(tempBarangAddEdit.pSKB) == 1) {
    document.getElementById("checkBoxSKB").checked = true
  } else {
    document.getElementById("checkBoxSKB").checked = false
  }

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

  $('#rowCustsupp').hide();
  $('#rowSubCosting').hide();
  $('#rowCosting').hide();

  document.getElementById("buttonAddListCosting").disabled = true
  document.getElementById("buttonAddListSubCosting").disabled = true
  document.getElementById("buttonAddListCustsupp").disabled = true


  if (tempBarangAddEdit.CustSuppP) {
    document.getElementById("AddAddKodeCustsupp").value = tempBarangAddEdit.CustSuppP
    document.getElementById("AddAddNamaCustsupp").value = tempBarangAddEdit.namacustsuppP
    $('#rowCustsupp').show();
  }

  if (tempBarangAddEdit.CustSuppL) {
    document.getElementById("AddAddKodeCustsupp").value = tempBarangAddEdit.CustSuppL
    document.getElementById("AddAddNamaCustsupp").value = tempBarangAddEdit.namacustsuppL

  }

  if (tempBarangAddEdit.KODECOST) {
    document.getElementById("AddAddKodeCosting").value = tempBarangAddEdit.KODECOST
    document.getElementById("AddAddNamaCosting").value = tempBarangAddEdit.NamaCost

    $('#rowCosting').show();
  }

  if (tempBarangAddEdit.KODESUBCOST) {
    document.getElementById("AddAddKodeSubCosting").value = tempBarangAddEdit.KODESUBCOST
    document.getElementById("AddAddNamaSubCosting").value = tempBarangAddEdit.NamaSubCost

    $('#rowSubCosting').show();
  }

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

  $.ajax({
    url: BANK_ROUTES.bankloadall,
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)
      lastRows = res.tempOutstanding || [];
      renderTabel();
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat data terbaru, menampilkan data terakhir')
    }})

}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: BANK_ROUTES.bankdetailCetak,
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
                  BUKTI KAS KELUAR
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
                  ${dataPrint[0].ketperk} (${dataPrint[0].lawan})
                </td>
              </tr>
                  <tr>
                    <td rowspan="2" class="text-center" style="width: 1%">No.</td>
                    <td rowspan="2" class="text-center" style="width: 10%">KODE</td>
                    <td rowspan="2" class="text-center" style="width: 20%">NAMA</td>
                    <td rowspan="2" class="text-center" style="width: 40%">KETERANGAN</td>
                    <td rowspan="2" class="text-center" style="width: 10%">SUB COST/SKB</td>
                    <td rowspan="2" class="text-center" style="width: 10%">JUMLAH</td>
                  </tr>
                </thead> `;

    let z = 0
    let maxRow = 8;
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalJumlah = 0;

    dataPrint.forEach(item => {

      if (item.JumlahRp) {
        grandTotalJumlah += Number(item.JumlahRp) || 0;
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
               style="width: 10%;  ">${itemSub.Perkiraan}</td>
         <td class="text-align: left"
               style="width: 20%;">${itemSub.ketlawan}</td>
         <td class="text-align: left"
               style="width: 40%;">${itemSub.keterangan}</td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.costSKB}</td>
         <td style="width: 10%; text-align: right;">
            ${itemSub.JumlahRp 
              ? Number(itemSub.JumlahRp).toLocaleString('id-ID', {
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
          <td colspan="4" style="border:1px solid; padding:5px; font-weight:bold;">
            Cetak ke : ${(item && item.length > 0) ? item[0].Cetakke : '-'}
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
              <td style="width: 20%; border: 1px solid; text-align: center;">Penerima</td>
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

  console.log(nobukti)



  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }





  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi Bank ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: BANK_ROUTES.bankspbatalotorisasi,
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
  console.log('formatAngka' , angkaString);
  let tempAngka = angkaString.split('.')
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


