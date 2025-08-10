document.querySelectorAll('.open-modal').forEach(button => {
    button.addEventListener('click', function () {
        const mode = this.getAttribute('mode');

        const clientid = this.getAttribute('clientid');
        const meterid = this.getAttribute('meterid');
        if (mode == "edit") {
            var id = document.getElementById("id_" + meterid).value;
        } else {
            var id = "0";
        }
        
        fetch(`/html/elmeters/form_measured.php?id=${id}&clientid=${clientid}&meterid=${meterid}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('measuredForm').innerHTML = data;
                const modal = new bootstrap.Modal(document.getElementById('modalMeasured'));
                modal.show();
            })
            .catch(error => console.error('Error fetching modal content:', error));
    });
});

function toggleChildren(className) {
    const rows = document.querySelectorAll("." + className);
    var display = false;
    rows.forEach(row => {
        if(!display){
            display = rows[0].style.display === "none" ? "table-row" : "none";
        }
        row.style.display = display;

        const childRows = row.querySelectorAll("tr");
        childRows.forEach(childRow => {
            childRow.style.display = display;
        });

    });
}
