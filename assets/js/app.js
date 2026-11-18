// Small client-side helpers: confirm deletes, live table search, print reports.
document.addEventListener('click', function (ev) {
  var t = ev.target.closest('[data-confirm]');
  if (t && !confirm(t.getAttribute('data-confirm'))) ev.preventDefault();
});
function filterTable(inputId, tableId) {
  var q = document.getElementById(inputId).value.toLowerCase();
  document.querySelectorAll('#' + tableId + ' tbody tr').forEach(function (r) {
    r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}
