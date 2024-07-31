document.getElementById('remove-btn').addEventListener("click", event => {
    const delete_rows = document.querySelectorAll('.show-on-remove-btn-click');
    delete_rows.forEach(row => {
        row.style.display === "" ? row.style.display = "none" : row.style.display = "";
    });
});

document.getElementById('final_delete_btn').addEventListener('click', function () {
    const checkboxes = document.querySelectorAll('.check:checked');
    const ids = Array.from(checkboxes).map(checkbox => checkbox.value);
    document.getElementById('product-ids').value = ids.join(',');
    document.getElementById('delete-form').submit();
});
