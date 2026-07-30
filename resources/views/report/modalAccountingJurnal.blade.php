<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>

    <!-- Modal -->
<div class="modal fade"  id="formSelect" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body">
            <table id="tabelSelect" class="table table-bordered table-striped">
            <thead class="text-center" id="tabelHeader">
                <tr>
                <th scope="col"></th>
                </tr>
            </thead>

            <tbody id="tabel_dataSelect" class="text-left" >
                <tr>
                <td></td>
            </tr>
            </tbody>

            </table>

        </div>
            <div class="modal-footer">
                 
            </div>
    </div>
    </div>
</div>

  </body>
</html>


<script>

function buttonPilih(idPart, selectedData)
  {
      console.log(idPart, 'asdqwdqwdqwd', selectedData)
      if (idPart == 1)
      {
        $("#inputPerkiraan1").val(selectedData);
        $("#formSelect").modal("hide");
      }
      else if (idPart == 2)
      {
        $("#inputDivisi").val(selectedData);
        $("#formSelect").modal("hide");
      }
      else if (idPart == 3)
      {
        $("#inputPerkiraan2").val(selectedData);
        $("#formSelect").modal("hide");
      }
      else if (idPart == 4)
      {
        $("#inputKategori").val(selectedData);
        $("#formSelect").modal("hide");
      }
      else if (idPart == 5)
      {
        $("#inputSubKategori").val(selectedData);
        $("#formSelect").modal("hide");
      }
      else if (idPart == 6)
      {
        $("#inputMerk").val(selectedData);
        $("#formSelect").modal("hide");
      }

  }

function buttonSelect (idModal)
{
  $("#formSelect").modal('toggle')

  if (idModal == "selectPerkiraan1")
  {
    loadSelectPerkiraan1();
  }
  else if (idModal == "selectDivisi")
  {
    loadSelectDivisi();
  }
  else if (idModal == "selectPerkiraan2")
  {
    loadSelectPerkiraan2();
  }
  else if (idModal == "selectKategori")
  {
    loadSelectKategori();
  }
  else if (idModal == "selectSubKategori")
  {
    loadSelectSubKategori();
  }
  else if (idModal == "selectMerk")
  {
    loadSelectMerk();
  }

}

function loadSelectPerkiraan1() {
  let _token = $("#_token").val();

  $('#tabelSelect').DataTable().destroy();

  $.ajax({
    url: "{!! url('laporanaccounting_loadperkiraan1') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });
  let namaSelect = "Select Perkiraan"
  document.getElementById('exampleModalLabel').innerHTML = namaSelect;

  let headerTable =
        `<tr>
        <th>Actions</th>
        <th>Perkiraan</th>
        <th>Keterangan</th>
        </tr>
        `;
  document.getElementById("tabelHeader").innerHTML = headerTable;

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilih('1','${item.Perkiraan}')">+</button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelect").innerHTML = rowTable;
  $("#tabelSelect").DataTable({
    "lengthChange": false,
    "paging": true,
  });
}

function loadSelectDivisi() {
  let _token = $("#_token").val();

  $('#tabelSelect').DataTable().destroy();

  $.ajax({
    url: "{!! url('laporanaccountingjurnal_loaddivisi') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let namaSelect = "Select Divisi"
  document.getElementById('exampleModalLabel').innerHTML = namaSelect;

  let headerTable =
        `<tr>
        <th>Actions</th>
        <th>Devisi</th>
        <th>Nama Devisi</th>
        </tr>
        `;
  document.getElementById("tabelHeader").innerHTML = headerTable;

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilih('2', '${item.Devisi}')">+</button>
      </td>
      <td>${item.Devisi}</td>
      <td>${item.NamaDevisi}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelect").innerHTML = rowTable;
  $("#tabelSelect").DataTable({
    "lengthChange": false,
    "paging": true,
  });
}

function loadSelectPerkiraan2() {
  let _token = $("#_token").val();

  $('#tabelSelect').DataTable().destroy();

  $.ajax({
    url: "{!! url('laporanaccounting_loadperkiraan1') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });
  let namaSelect = "Select Perkiraan"
  document.getElementById('exampleModalLabel').innerHTML = namaSelect;

  let headerTable =
        `<tr>
        <th>Actions</th>
        <th>Perkiraan</th>
        <th>Keterangan</th>
        </tr>
        `;
  document.getElementById("tabelHeader").innerHTML = headerTable;

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilih('3','${item.Perkiraan}')">+</button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelect").innerHTML = rowTable;
  $("#tabelSelect").DataTable({
    "lengthChange": false,
    "paging": true,
  });
}

function loadSelectKategori() {
  let _token = $("#_token").val();

  $('#tabelSelect').DataTable().destroy();

  $.ajax({
    url: "{!! url('laporanmarketingso_loadkategori') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let namaSelect = "Select Kategori"
  document.getElementById('exampleModalLabel').innerHTML = namaSelect;

  let headerTable =
        `<tr>
        <th>Kode Sub Group</th>
        <th>Nama Sub Group</th>
        <th>Actions</th>
        </tr>
        `;
  document.getElementById("tabelHeader").innerHTML = headerTable;

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td>${item.KOdeSubGrp}</td>
      <td>${item.NamaSubGrp}</td>
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilih('4', '${item.KOdeSubGrp}')">+</button>
      </td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelect").innerHTML = rowTable;
  $("#tabelSelect").DataTable({
    "lengthChange": false,
    "paging": true,
  });
}

function loadSelectSubKategori() {
  let _token = $("#_token").val();

  $('#tabelSelect').DataTable().destroy();

  $.ajax({
    url: "{!! url('laporanmarketingso_loadsubkategori') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let namaSelect = "Select Sub-Kategori"
  document.getElementById('exampleModalLabel').innerHTML = namaSelect;

  let headerTable =
        `<tr>
        <th>Kode Jenis</th>
        <th>Nama Jenis</th>
        <th>Actions</th>
        </tr>
        `;
  document.getElementById("tabelHeader").innerHTML = headerTable;

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td>${item.Urut}</td>
      <td>${item.Keterangan}</td>
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilih('5', '${item.Urut}')">+</button>
      </td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelect").innerHTML = rowTable;
  $("#tabelSelect").DataTable({
    "lengthChange": false,
    "paging": true,
  });
}

function loadSelectMerk() {
  let _token = $("#_token").val();

  $('#tabelSelect').DataTable().destroy();

  $.ajax({
    url: "{!! url('laporanmarketingso_loadmerk') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let namaSelect = "Select Merk"
  document.getElementById('exampleModalLabel').innerHTML = namaSelect;

  let headerTable =
        `<tr>
        <th>Nama</th>
        <th>Kode Merk</th>
        <th>Actions</th>
        </tr>
        `;
  document.getElementById("tabelHeader").innerHTML = headerTable;

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td>${item.KodeMerk}</td>
      <td>${item.NamaMerk}</td>
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilih('6', '${item.KodeMerk}')">+</button>
      </td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelect").innerHTML = rowTable;
  $("#tabelSelect").DataTable({
    "lengthChange": false,
    "paging": true,
  });
}

</script>
