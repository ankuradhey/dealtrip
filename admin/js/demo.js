$(document).ready(function() {
    /* Demo Start */

    /* jQuery-UI Widgets */

    $(".mws-accordion").accordion();

    $(".mws-tabs").tabs();

    $(".mws-datepicker").datepicker({
        showOtherMonths: true,
        dateFormat: "dd/mm/yy"
    });

    $(".mws-datepicker-wk").datepicker({
        showOtherMonths: true,
        showWeek: true
    });

    $(".mws-datepicker-mm").datepicker({
        showOtherMonths: true,
        numberOfMonths: 3
    });

    $(".mws-dtpicker").datetimepicker();

    $(".mws-tpicker").timepicker({});

    $(".mws-slider").slider({
        range: "min"
    });

    $(".mws-progressbar").progressbar({
        value: 37
    });

    $(".mws-range-slider").slider({
        range: true,
        min: 0,
        max: 500,
        values: [75, 300]
    });

    var availableTags = [
        "ActionScript",
        "AppleScript",
        "Asp",
        "BASIC",
        "C",
        "C++",
        "Clojure",
        "COBOL",
        "ColdFusion",
        "Erlang",
        "Fortran",
        "Groovy",
        "Haskell",
        "Java",
        "JavaScript",
        "Lisp",
        "Perl",
        "PHP",
        "Python",
        "Ruby",
        "Scala",
        "Scheme"
    ];
    $(".mws-autocomplete").autocomplete({
        source: availableTags
    });

    $("#mws-jui-dialog").dialog({
        autoOpen: false,
        title: "jQuery-UI Dialog",
        modal: true,
        width: "640",
        buttons: [{
                text: "Close Dialog",
                click: function() {
                    $(this).dialog("close");
                }
            }]
    });
    $("#mws-form-dialog").dialog({
        autoOpen: false,
        title: "jQuery-UI Modal Form",
        modal: true,
        width: "640",
        buttons: [{
                text: "Submit",
                click: function() {
                    $(this).find('form#mws-validate').submit();
                }
            }]
    });
    $("#mws-jui-dialog-btn").bind("click", function(event) {
        $("#mws-jui-dialog").dialog("option", {
            modal: false
        }).dialog("open");
        event.preventDefault();
    });
    $("#mws-jui-dialog-mdl-btn").bind("click", function(event) {
        $("#mws-jui-dialog").dialog("option", {
            modal: true
        }).dialog("open");
        event.preventDefault();
    });
    $("#mws-form-dialog-mdl-btn").bind("click", function(event) {
        $("#mws-form-dialog").dialog("option", {
            modal: true
        }).dialog("open");
        event.preventDefault();
    });

    $(".mws-slider-vertical").slider({
        orientation: "vertical",
        range: "min",
        min: 0,
        max: 100,
        value: 60
    });

    $("#eq > span").each(function() {
        // read initial values from markup and remove that
        var value = parseInt($(this).text(), 10);
        $(this).empty().slider({
            value: value,
            range: "min",
            animate: true,
            orientation: "vertical"
        });
    });


    /* ColorPicker */

    $(".mws-colorpicker").ColorPicker({
        onSubmit: function(hsb, hex, rgb, el) {
            $(el).val(hex);
            $(el).ColorPickerHide();
        },
        onBeforeShow: function() {
            $(this).ColorPickerSetColor(this.value);
        }
    });

    /* Data Tables */

    $(".mws-datatable").dataTable();

    var oTable = $(".mws-datatable-fn").dataTable({
        sPaginationType: "full_numbers",
        "fnDrawCallback": function(oSettings) {
            $('table.mws-table input:checkbox').each(function(index, el) {
                //console.log(el);
                el.checked = false;
            })
        }
    });

        $.fn.dataTableExt.afnFiltering.push(
                function(oSettings, aData, iDataIndex) {
                    if($('#exludeitems').hasClass('inhouse'))
                        var html = aData[7];
                    else
                        var html = aData[6];
                    if (/selected="selected" value="4"/.test(html) && $('#exludeitems').prop('checked') == true) {
                        return false;
                    }
                    else {
                        return true;
                    }
                }
        );
            
        $('#exludeitems').change(function() {
            if ($('#exludeitems').prop('checked') == true) {
                localStorage.setItem('excludeSuspendedFlag', 'true');
            } else {
                localStorage.setItem('excludeSuspendedFlag', '');
            }
            oTable.fnDraw();
        });

    if (localStorage.getItem('excludeSuspendedFlag') == 'true' && $('.mws-datatable').length) {
        $('#exludeitems').prop('checked', 'checked');
        oTable.fnDraw();
    }

    $(".mws-datatable-fn-draggable").dataTable({
        sPaginationType: "full_numbers",
        "iDisplayLength": -1
    }).rowReordering({
        sURL: SITEURLADMIN + "slides/orderslides/lppty_type/0",
        sRequestType: "GET",
        fnAlert: function(message) {
            alert(message);
        }
    });

    $(".mws-datatable-fn-draggable-home").dataTable({
        sPaginationType: "full_numbers",
        "iDisplayLength": -1
    }).rowReordering({
        sURL: SITEURLADMIN + "photo/orderposition/",
        sRequestType: "GET",
        fnAlert: function(message) {
            alert(message);
        }
    });

    $(".mws-datatable-fn-draggable-popular").dataTable({
        sPaginationType: "full_numbers",
        "iDisplayLength": -1
    }).rowReordering({
        sURL: SITEURLADMIN + "slides/orderslides/lppty_type/1",
        sRequestType: "GET",
        fnAlert: function(message) {
            alert(message);
        }
    });

    $(".mws-datatable-fn-draggable-special").dataTable({
        sPaginationType: "full_numbers",
        "iDisplayLength": -1
    }).rowReordering({
        sURL: SITEURLADMIN + "slides/orderslides/lppty_type/2",
        sRequestType: "GET",
        fnAlert: function(message) {
            alert(message);
        }
    });

    $(".mws-datatable-fn-draggable-review").dataTable({
        sPaginationType: "full_numbers",
        "iDisplayLength": -1
    }).rowReordering({
        sURL: SITEURLADMIN + "slides/orderreviewslides",
        sRequestType: "GET",
        fnAlert: function(message) {
            alert(message);
        }
    });

    /* Server side datatable */
    $(".mws-datatable-fn-server").dataTable({
        processing:"true",
        serverSide:"true",
        ajax:SITEURLADMIN+"property/ajaxlist",
        "bPaginate":true, // Pagination True
        "lengthMenu": [[75, 100], [75, 100]],
        "fnDrawCallback": function(oSettings) {
        }
    })

    /* imgAreaSelect */
    /* Server side datatable */
    $(".mws-datatable-fn-server-ppty").dataTable({
        processing:"true",
        serverSide:"true",
        ajax:SITEURLADMIN+"property/ajaxpropertylist",
        "bPaginate":true, // Pagination True 
        "lengthMenu": [[75, 100], [75, 100]],
        "fnDrawCallback": function(oSettings) {
        }
    })

    /* imgAreaSelect */

    $(".mws-crop-target").imgAreaSelect({
        handles: true,
        x1: 32,
        y1: 32,
        x2: 132,
        y2: 132,
        onSelectChange: function(img, selection) {
            $("#crop_x1").val(selection.x1);
            $("#crop_y1").val(selection.y1);
            $("#crop_x2").val(selection.x2);
            $("#crop_y2").val(selection.y2);
        }
    });

    /* Full Calendar */

    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();


    $("#mws-calendar").fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        editable: true,
        events: [
            {
                title: 'All Day Event',
                start: new Date(y, m, 1)
            },
            {
                title: 'Long Event',
                start: new Date(y, m, d - 5),
                end: new Date(y, m, d - 2)
            },
            {
                id: 999,
                title: 'Repeating Event',
                start: new Date(y, m, d - 3, 16, 0),
                allDay: false
            },
            {
                id: 999,
                title: 'Repeating Event',
                start: new Date(y, m, d + 4, 16, 0),
                allDay: false
            },
            {
                title: 'Meeting',
                start: new Date(y, m, d, 10, 30),
                allDay: false
            },
            {
                title: 'Lunch',
                start: new Date(y, m, d, 12, 0),
                end: new Date(y, m, d, 14, 0),
                allDay: false
            },
            {
                title: 'Birthday Party',
                start: new Date(y, m, d + 1, 19, 0),
                end: new Date(y, m, d + 1, 22, 30),
                allDay: false
            },
            {
                title: 'Click for Google',
                start: new Date(y, m, 28),
                end: new Date(y, m, 29),
                url: 'http://google.com/'
            }
        ]
    });

    /* Sourcerer */

    $(".mws-code-html").sourcerer('html');

    /* Validation Plugin */
    $('#copyform').validate({
        rules: {
            spinner: {
                required: true,
                max: 5
            }
        },
        invalidHandler: function(form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                var message = errors == 1
                        ? 'You missed 1 field. It has been highlighted'
                        : 'You missed ' + errors + ' fields. They have been highlighted';
                $("#mws-validate-error").html(message).show();
            } else {
                $("#mws-validate-error").hide();
            }
        }

    })



    $("fieldset#step-5 form").each(function(index, element) {


        $(this).validate({
            rules: {
                spinner: {
                    required: true,
                    max: 5
                }
            },
            invalidHandler: function(form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    var message = errors == 1
                            ? 'You missed 1 field. It has been highlighted'
                            : 'You missed ' + errors + ' fields. They have been highlighted';
                    $("#mws-validate-error").html(message).show();
                } else {
                    $("#mws-validate-error").hide();
                }
            }
        });

    });










    $('#step-8 .mws-form').validate({
        rules: {
            spinner: {
                required: true,
                max: 5
            }
        },
        invalidHandler: function(form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                var message = errors == 1
                        ? 'You missed 1 field. It has been highlighted'
                        : 'You missed ' + errors + ' fields. They have been highlighted';
                $("#mws-validate-error").html(message).show();
            } else {
                $("#mws-validate-error").hide();
            }
        }
    });


    $('#step-1 .mws-form').validate({
        rules: {
            spinner: {
                required: true,
                max: 5
            }
        },
        invalidHandler: function(form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                var message = errors == 1
                        ? 'You missed 1 field. It has been highlighted'
                        : 'You missed ' + errors + ' fields. They have been highlighted';
                $("#mws-validate-error").html(message).show();
            } else {
                $("#mws-validate-error").hide();
            }
        }
    });


    $('#step-3 .mws-form').validate({
        rules: {
            spinner: {
                required: true,
                max: 5
            }
        },
        invalidHandler: function(form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                var message = errors == 1
                        ? 'You missed 1 field. It has been highlighted'
                        : 'You missed ' + errors + ' fields. They have been highlighted';
                $("#mws-validate-error").html(message).show();
            } else {
                $("#mws-validate-error").hide();
            }
        }
    });

    $('#step-10 .validate').validate({
        rules: {
            spinner: {
                required: true,
                max: 5
            }
        },
        invalidHandler: function(form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                var message = errors == 1
                        ? 'You missed 1 field. It has been highlighted'
                        : 'You missed ' + errors + ' fields. They have been highlighted';
                $("#mws-validate-error").html(message).show();
            } else {
                $("#mws-validate-error").hide();
            }
        }
    });





    /* jGrowl Notifications */

    $("#mws-growl-btn").bind("click", function(event) {
        $.jGrowl("Hello World!", {
            position: "bottom-right"
        });
    });

    $("#mws-growl-btn-1").bind("click", function(event) {
        $.jGrowl("A sticky message", {
            sticky: true,
            position: "bottom-right"
        });
    });

    $("#mws-growl-btn-2").bind("click", function(event) {
        $.jGrowl("Message with Header", {
            header: "Important!",
            position: "bottom-right"
        });
    });
});
