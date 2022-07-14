var amountPerPage = 12;
var currentPage = 0;

redraw(currentPage, amountPerPage);

var pageCount = Math.ceil($(" tr:has(td):not(.hidden)").length / amountPerPage);

$("table").append(
  '<div class="pagination-div"><ul class="pagination"><li id="table-vorige" class="disabled"><a href="#" aria-label="Vorige"><span aria-hidden="true">&laquo;</span></a></li><li id="table-volgende"><a href="#" aria-label="Volgende"><span aria-hidden="true">&raquo;</span></a></li></ul></div>'
);

for (var i = pageCount; i >= 1; i--) {
  var pagenum = $("<li><a href='#'>" + i + "</a></li>");
  $("table.table .pagination #table-vorige").after(pagenum);

  if (i == 1) {
    $(pagenum).addClass("active");
  }
}

$(".pagination li a").click(function () {
  var p = $(this).text();

  if (isNaN(parseInt(p))) {
    if ($(this).parent().is(".disabled")) {
      return;
    }

    if ($(this).parent().is("#table-volgende")) {
      var pageNum = currentPage + 1;
    } else {
      var pageNum = currentPage - 1;
    }
  } else {
    var pageNum = parseInt(p) - 1;
  }

  currentPage = pageNum;

  redraw(pageNum, amountPerPage);
});

function redraw(currentPage, amountPerPage) {
  $("#table-vorige").removeClass("disabled");
  $("#table-volgende").removeClass("disabled");

  if (currentPage === 0) {
    $("#table-vorige").addClass("disabled");
  }

  if (currentPage === pageCount - 1) {
    $("#table-volgende").addClass("disabled");
  }

  $(".pagination li.active").removeClass("active");
  $('.pagination li:contains("' + (currentPage + 1) + '")').addClass("active");

  var totalCounter = 0;
  $("table.table tr:has(td):not(.hidden)").each(function (cnt, tr) {
    var start = currentPage * amountPerPage;
    var end = start + amountPerPage;

    if (!(totalCounter >= start && totalCounter < end)) {
      $(this).addClass("row-hidden");
    } else {
      $(this).removeClass("row-hidden");
    }

    totalCounter++;
  });
}
