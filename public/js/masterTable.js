
$("#tabel_filter_visual").on("keyup", function () {
  $("#tabel").DataTable().search(this.value).draw();
});

$(document).on("change", "#tabel_length_visual", function () {
  $("#tabel").DataTable().page.len(Number(this.value)).draw();
});
