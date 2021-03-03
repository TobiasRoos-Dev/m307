$(document).ready(function () {

    loadTable();


    $('#new_entry .modal-content .content').load('sites/formular.html', function () {
        M.AutoInit();
        $('#row_form').submit(function (e) {
            e.preventDefault();
            let type = $('#row_form select > option:selected').val();
            let method = (parseInt($('#id').val()) === -1) ? 'insert' : 'update';
            let data = $('#row_form').serialize() + `&method=${method}`

            $.ajax({
                dataType: 'json',
                'data': data,
                type: "POST",
                url: "api.php",
                success: function (response) {
                    if(response.success === true) {
                        loadTable();
                        if(response.method === 'insert') {
                            let id = response.id;
                            Toast.fire({
                                title: `Gerät mit der ID ${id} wurde eingefügt.`,
                                icon: 'success',
                            });
                        }
                        if(response.method === 'update') {
                            let id = response.id;
                            Toast.fire({
                                title: `Gerät mit der ID ${id} wurde gespeichert.`,
                                icon: 'success',
                            });
                        }
                        M.Modal.getInstance($('.modal')).close();
                    } else {
                        let fehlermeldungen = '';
                        response.message.forEach(function (msg) {
                            fehlermeldungen += msg + "<br>";
                        })
                        Swal.fire(
                            'Fehler!',
                            fehlermeldungen,
                            'error'
                        )
                    }

                }
            })

        })
    });


    $('.new_entry a').click(function () {
        let title = $(this).data('title');
        let id = -1;
        $('#new_entry .modal-content h4').text(title);
        fillEdit(id);
        M.AutoInit();
        M.Modal.getInstance($('.modal')).open();
        setTimeout(function() {
            M.updateTextFields();
            initDatePicker(id);
        },100)
    })



    function fillEdit(id) {
        if(id >= 0) {
            $.ajax(`api.php?method=get&id=${id}`, {
                dataType: 'json',
                success: function (response) {
                    let template = $('#form-template').html();
                    let html = Mustache.render(template, response);
                    $('#form_content').html(html);
                    let type = response.data[0].bauart;
                    $(`#form_content select option[value="${type}"]`).prop('selected', true)
                }
            });
        } else {
            let response = {data: {
                    id: -1
                }};
            let template = $('#form-template').html();
            let html = Mustache.render(template, response);
            $('#form_content').html(html);

        }
    }

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })
    let options = {

    };
    function initDatePicker(id = -1) {
        let options = {
            container: 'body',
            format: 'dd.mm.yyyy',
        };
        $('.datepicker').datepicker(options);
        if(id >= 0) {
            $.ajax(`api.php?method=get&col=date&id=${id}`, {
                dataType: 'json',
                success: function (response) {
                    let date = response.data[0].date.split('.');
                    let year = parseInt(date[2]);
                    let month = parseInt(date[1])-1;
                    let day = parseInt(date[0]);
                    let new_options = {
                        container: 'body',
                        setDefaultDate: true,
                        defaultDate: new Date(year, month, day),
                        format: 'dd.mm.yyyy',
                    }
                    $('.datepicker').datepicker(new_options);
                }
            })
        }

        $('.datepicker').click(function() {
            // $('.datepicker-modal').appendTo('body');
        })
    }

    function loadTable() {
        $.ajax('api.php?method=get', {
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    let table = $('.table table');
                    let template = $('#template').html();
                    let content = Mustache.render(template, response);
                    let settings = {
                        retrieve: true,
                        paging: false,
                        lengthMenu: false,
                    }
                    $(table).DataTable(settings)
                    $(table).DataTable().clear().destroy();

                    $(table).find('tbody').html(content);

                    $(table).DataTable(settings)

                    $('.edit').off().click(function () {
                        let title = $(this).data('title');
                        let id = $(this).closest('td').data('id');
                        $('#new_entry .modal-content h4').text(title);
                        fillEdit(id);
                        M.Modal.getInstance($('.modal')).open();
                        setTimeout(function () {
                            M.updateTextFields();
                            initDatePicker(id);
                        }, 100)
                    });
                    $('a.delete').off().click(function () {
                        let id = $(this).closest('td').data('id');
                        Swal.fire({
                            title: 'Sind Sie sich sicher?',
                            text: "Der Datensatz kann nicht mehr wiederhergestellt werden!",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ja',
                            cancelButtonText: 'Nein',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: `api.php?method=delete&id=${id}`,
                                    dataType: 'json',
                                    success: function (response) {
                                        loadTable();
                                        if (response.success === true) {
                                            Toast.fire({
                                                title: `Gerät mit der ID ${id} wurde gelöscht.`,
                                                icon: 'success',
                                            });
                                        }
                                    }
                                })
                            }
                        });
                    });
                    $('a.tank').off().click(function () {
                        let id = $(this).closest('td').data('id');
                        $.ajax({
                            url: `api.php?method=tanken&id=${id}`,
                            dataType: 'json',
                            success: function (response) {
                                if (response.success === true) {
                                    loadTable();
                                    Toast.fire({
                                        title: `Gerät mit der ID ${id} wurde getankt.`,
                                        icon: 'success',
                                    });
                                }
                            }
                        })
                    })
                } else {
                    let fehlermeldungen = '';
                    response.message.forEach(function (msg) {
                        fehlermeldungen += msg + "<br>";
                    })
                    Swal.fire(
                        'Fehler!',
                        fehlermeldungen,
                        'error'
                    )
                }

            }
        });
    }

})