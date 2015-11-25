$(document).ready(function() {
    /* Core JS Functions */

    /* Collapsible Panels */
    $(".mws-collapsible.mws-collapsed .mws-panel-body").css("display", "none");
    $(".mws-collapsible .mws-panel-header")
            .append("<div class=\"mws-collapse-button mws-inset\"><span></span></div>")
            .find(".mws-collapse-button span")
            .live("click", function(event) {



        $(this)
                .parents(".mws-collapsible")
                .toggleClass("mws-collapsed")
                .find(".mws-panel-body")
                .slideToggle("fast");

        //code for finding height of the step container


    });

    /* Side dropdown menu */
    $("div#mws-navigation ul li a, div#mws-navigation ul li span")
            .bind('click', function(event) {
        if ($(this).next('ul').size() !== 0) {
            $(this).next('ul').slideToggle('fast', function() {
                $(this).toggleClass('closed');
            });
            event.preventDefault();
        }
    });

    /* Responsive Layout Script */

    $("div#mws-navigation").live('click', function(event) {
        if (event.target === this) {
            $(this).toggleClass('toggled');
        }
    });

    /* Form Messages */

    $(".mws-form-message").live("click", function() {
        $(this).animate({opacity: 0}, function() {
            $(this).slideUp("medium", function() {
                $(this).css("opacity", '');
            });
        });
    });

    /* Message & Notifications Dropdown */
    $("div#mws-user-tools .mws-dropdown-menu a").click(function(event) {
        $(".mws-dropdown-menu.toggled").not($(this).parent()).removeClass("toggled");
        $(this).parent().toggleClass("toggled");
        event.preventDefault();
    });

    $('html').click(function(event) {
        if ($(event.target).parents('.mws-dropdown-menu').size() == 0) {
            $(".mws-dropdown-menu").removeClass("toggled");
        }
    });

    /* Side Menu Notification Class */
    $(".mws-nav-tooltip").addClass("mws-inset");

    /* Table Row CSS Class */
    $("table.mws-table tbody tr:even").addClass("even");
    $("table.mws-table tbody tr:odd").addClass("odd");

    /* Adding title attribute to table header, toolbar buttons and wizard navigation */
    $("table.mws-table thead tr th, .mws-panel-toolbar ul li a, .mws-panel-toolbar ul li a span, .mws-wizard ul li a, .mws-wizard ul li span").each(function() {
        $(this).attr('title', $(this).text());
    });

    /* File Input Styling */

    if ($.fn.customFileInput) {
        $("input[type='file']").not(":hidden").not('.no-custom').customFileInput();

    }


    if ($.fn.httpAdder) {
        $(".httpadder").httpAdder();
    }


    /* Chosen Select Box Plugin */

    if ($.fn.chosen) {
        $("select.chzn-select").chosen();
    }

    /* Tooltips */

    if ($.fn.tipsy) {
        var gravity = ['n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw'];
        for (var i in gravity)
            $(".mws-tooltip-" + gravity[i]).tipsy({gravity: gravity[i]});

        $('input[title], select[title], textarea[title]').tipsy({trigger: 'focus', gravity: 'w'});
    }

    /* Dual List Box */

    if ($.configureBoxes) {
        $.configureBoxes();
    }

    if ($.fn.placeholder) {
        $('[placeholder]').placeholder();
    }

    //mws wizard
    var v = $("#mws-wizard-form").validate({onsubmit: true,  onkeyup: false, onfocusout:false});
    if ($.fn.mwsWizard) {
        $("#mws-wizard-form").mwsWizard({
            forwardOnly: false,
            onLeaveStep: function(index, elem) {
                //if property extra page
                if (index === 1) {
                    var propertyId = $('#propertyId').val();
                    getExtras(propertyId);
                    $('#checkInDate').empty().text($('#date_from').val());
                    $('#nights').empty().text($('#date_to').val());
                    $('#departureDate').empty().text($('#departureDates').val());
                    $('#totalCost').empty().text("£ " + $('#totalAmount').val());
                }

                if (index === 2) {
                    console.log("inside");
                    var costAftrSpcl = $("#costAftrSpcl").text().trim();
                    var extraCost = $("#extraCost").val();
                    if (!costAftrSpcl) {
                        costAftrSpcl = $("#totalAmount").val();
                        $("#costAftrSpcl").text("£ " + $("#totalAmount").val());
                    }
                    $("#finalCost").empty().text("£ " +(parseInt(costAftrSpcl) + parseInt(extraCost)));
                    $("#finalAmount").val(parseInt(costAftrSpcl) + parseInt(extraCost));

                    //save user

                }
                console.log(index);
                return v.form();
            },
            onBeforeSubmit: function() {
                return v.form();
            }
        });
    }
});
