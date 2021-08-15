function loadData(task, tableId) {
    var id_paket_ob = $('#id_paket_obat').val();
    const request = new XMLHttpRequest();
    request.open("get", "ajax_data/get_data_detail_paket.php?id=" + id_paket_ob + "&task=" + task);
    request.onload = () => {
        try {
            const json = JSON.parse(request.responseText);
            populateData(json, tableId);
        } catch (e) {
            console.warn("cannot load table " + tableId + " data :" + e);
        }
    };
    request.send();
}
function populateData(json, tableId) {
    const tableBody = document.querySelector("#" + tableId + " > tbody");
    while (tableBody.firstChild) {
        tableBody.removeChild(tableBody.firstChild);
    }
    json.forEach((row) => {
        const tr = document.createElement("tr");
        row.forEach((cell) => {
            const td = document.createElement("td");
            td.innerHTML = cell;
            tr.appendChild(td);
        });
        tableBody.appendChild(tr);
    });
}
function hapusData(ele) {
    var idBtn = ele.id;
    var id_detail = $('#' + idBtn).data('id');
    swal({
        title: "Hapus Data ini?",
        text: "Data yang sudah dihapus tidak dapat dikembalikan",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: "POST",
                    url: "ajax_data/del_detail_paket.php",
                    data: {
                        "id_detail": id_detail
                    },
                    success: function (response) {
                        var r = JSON.parse(response)
                        swal({
                            title: r.title,
                            text: r.msg,
                            icon: r.icon
                        });
                        resetForm();
                        loadInitData();
                    }
                });
            }
        });
}
function loadInitData() {
    loadData('umum', 'table-umum');
    loadData('bedah', 'table-bedah');
    loadData('anestesi', 'table-anestesi');
}
function resetForm(){
    $('#nama_obat').val('').trigger('change');
    $('#jenis').val('').trigger('change');
    $('#jumlah').val('');
}
// function loadListObat() {
//     const req = new XMLHttpRequest();
//     req.open("get", "ajax_data/getListObat.php");
//     req.onload = () => {
//         try {
//             const json = JSON.parse(request.responseText);
//             populateData(json, tableId);
//         } catch (e) {
//             console.warn("cannot load select data " + e);
//         }
//     };
//     request.send();
// }
$(document).ready(function () {
    loadInitData();
    $('#nama_obat').select2({
        placeholder: "Pilih Obat",
        allowClear: true,
        width: 'resolve'
    });
    $('#jenis').select2({
        placeholder: "Pilih Jenis Obat",
        allowClear: true,
        width: 'resolve'
    });
    $('#submitObat').on('click', function () {
        var id_paket_obat = $('#id_paket_obat').val();
        var id_obat = $('#nama_obat').val();
        var jenis = $('#jenis').val();
        var jumlah = $('#jumlah').val();
        if (id_obat == '') {
            swal({
                title: 'Peringatan!',
                text: 'Nama Obat Belum dipilih',
                icon: 'warning'
            });
            return;
        }
        if (jenis == '') {
            swal({
                title: 'Peringatan!',
                text: 'Jenis Belum dipilih',
                icon: 'warning'
            });
            return;
        }
        if (jumlah == '') {
            swal({
                title: 'Peringatan!',
                text: 'Jumlah Belum diisi',
                icon: 'warning'
            });
            return;
        }
        $.ajax({
            type: "POST",
            url: "ajax_data/ins_data_paket.php",
            data: {
                "id_paket_obat": id_paket_obat,
                "id_obat": id_obat,
                "jenis": jenis,
                "jumlah": jumlah
            },
            success: function (response) {
                var res = JSON.parse(response);
                console.log(res);
                swal({
                    title: res.title,
                    text: res.msg,
                    icon: res.icon
                }).then((val) => {
                    loadInitData();
                    resetForm();
                });
            }, error: function (err) {
                console.warn(err)
            }
        });
    });
    $("#nama_obat").select2({
        ajax: {
            url: "ajax_data/getDataObat.php",
            dataType: 'json',
            delay: 100,
            data: function (params) {
                return {
                    q: params.term, // search term
                };
            },
            processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                // params.page = params.page || 1;

                return {
                    results: data.items,
                };
            },
            cache: true
        },
        placeholder: 'Masukan Nama Obat',
        minimumInputLength: 3,
        minimumResultsForSearch: Infinity,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection
    });

    function formatRepo(repo) {
        if (repo.loading) {
            return "Sedang Melakukan Penarikan Data...";
        }
        var $state = repo.text;
        return $state;
    }

    function formatRepoSelection(repo) {
        return repo.text;
    }
})