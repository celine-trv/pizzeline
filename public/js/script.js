
// BOOTSTRAP disabling form submissions if there are invalid fields (!! just html verif !!)
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        
        var validation = Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
        });
    }, false);
})();


// CAROUSEL TABS_HOME
if(document.querySelector("#tabs_home")) {

    function click_tab() {
        var tabs = document.querySelectorAll("#tabs_home a");
        var contents = document.querySelectorAll("#block_tabs_home .tab-pane");

        if(tabs[currentTab].getAttribute("aria-selected") == "true") {

            tabs[currentTab].setAttribute("aria-selected", "false");
            tabs[currentTab].classList.remove("active");
            contents[currentTab].classList.remove("active", "show");

            currentTab++;

            if (currentTab >= tabs.length)
                currentTab = 0;
    
            tabs[currentTab].setAttribute("aria-selected", "true");
            tabs[currentTab].classList.add("active");
            contents[currentTab].classList.add("active", "show");
        }
    }
    let currentTab = 0;
    let x = setInterval(click_tab, 8000);

    document.querySelectorAll("#tabs_home a").forEach(function(tab) {
        tab.addEventListener("click", function() {
            clearInterval(x);
        });
    });
}


// FORM CONTACT : characters counter
if(document.getElementById("message_contact")) {
    document.getElementById("message_contact").addEventListener("keyup", function countWords() {
        document.getElementById("char_counter_contact").innerText = this.value.length;
    });
}



// ********************  JQUERY  ********************
$(document).ready(function() {

    // BURGER
    $("#burger").on("click", function() {
        $(".cross_line").fadeToggle(100);
        $(".cross_off").fadeToggle(100);
        $("#cross_line1").toggleClass("rotate1");
        $("#cross_line2").toggleClass("rotate2");
    });
    $("#burger").on("mouseover", function() {
        $(".burger_line").css({"background-color" : "#f5cd4b"});
    });
    $("#burger").on("mouseleave", function() {
        $(".burger_line").css({"background-color" : "#fff0d7"});
    });
    

    // PASSWORD : toggle visibility
    $(".icon_eye").on("mousedown", function() {
        $(this).parent("div").find("input").attr("type", "texte");
    });
    $(".icon_eye").on("mouseup", function() {
        $(this).parent("div").find("input").attr("type", "password");
    });
    $(".icon_eye").on("touchstart", function() {
        $(this).parent("div").find("input").attr("type", "texte");
    });
    $(".icon_eye").on("touchend", function() {
        $(this).parent("div").find("input").attr("type", "password");
    });


    // CURSOR WAIT
    if($(".cursor_wait").html() && $(".cursor_wait").html().length > 0) {
        $("*").css({"cursor" : "wait"});
    };


    // FORM ACCOUNT : toggle class (to disable / enable inputs to edit)
    if($(".form-control-plaintext").attr("class") && $(".form-control-plaintext").attr("class").indexOf("form-control") >= 0) {
        $(".form-control-plaintext").removeClass("form-control");
    };
    function enable_form() {
        $("#msg_account").html("");
        $(".form-control-plaintext").prop("readonly", false);
        $(".form-control-plaintext").addClass("form-control");
        $(".form-control-plaintext").removeClass("form-control-plaintext");
        $(".col-form-label").css({"text-decoration" : "none"});
        $("#phone_account").val($("#phone_account").val().replace(/[ .,-]/g, ""));
        $("#btn_edit_account").parent().css({"display" : "none"});
        $("#btn_reset_account").parent().css({"display" : "flex"});
    }
    function disable_form() {
        remove_errors();
        $("#form_account .form-control").prop("readonly", true);
        $("#form_account .form-control").addClass("form-control-plaintext");
        $("#form_account .form-control").removeClass("form-control");
        $(".col-form-label").css({"text-decoration" : "underline"});
        let phone = $("#phone_account").val().replace(/[ .,-]/g, "");
        $("#phone_account").val(phone.substr(0, 2) + " " + phone.substr(2, 2) + " " + phone.substr(4, 2) + " " + phone.substr(6, 2) + " " + phone.substr(8, 2));
        $("#btn_reset_account").parent().css({"display" : "none"});
        $("#btn_edit_account").parent().css({"display" : "flex"});
    }
    function remove_errors() {                              // also in Ajax for form account below
        $(".border_error").removeClass("border_error");
        $("#error_first_name_account").remove();
        $("#error_last_name_account").remove();
        $("#error_email_account").remove();
        $("#error_phone_account").remove();
        $("#error_address_account").remove();
        $("#error_zipcode_account").remove();
        $("#error_town_account").remove();
        // doesn't work with $(".error_form").remove()... (no msg displayed)
    }
    $("#btn_edit_account").on("click", enable_form);
    $("#btn_reset_account").on("click", disable_form);      // also in Ajax for form account below


    // FORM ORDER MODAL
    const MAX_PRODUCT_QTY = $(".orderModal input[name='quantity']").data("max_product_qty");    // currently = 20
    const MAX_ORDER_QTY = $(".orderModal form").data("max_order_qty");                          // currently = 60
    $(".orderModal input[name='quantity'], .orderModal .invalid-feedback").removeAttr("id");

    // product total price in btn
    let product_total_price = document.createElement('small');
    $(".orderModal .modal-body button").append(product_total_price);

    function calc_product_total_price() {
        $(".orderModal .modal-body button small").each(function() {
            let product_price = $(this).closest(".modal-body").find(".product_price");
            let qty = $(this).closest("form").find("input[name='quantity']");
            let product_total_price_val = parseInt(qty.val()) * parseFloat(product_price.children().html());
            $(this).html(" (" + product_total_price_val.toFixed(2) + " €)");
        })
    }
    calc_product_total_price();     // also in Ajax for form order modal below

    // actions when click on qty -
    $(".minus_symbol:not(.disabled)").on("click", function() {
        let qty = parseInt($(this).closest("div").find("input[name='quantity']").val());

        if(qty > 1 && qty <= MAX_PRODUCT_QTY) {
            $(this).closest("div").find("input[name='quantity']").val(qty-1);
            $(this).closest("div").find(".plus_symbol").removeClass("disabled");
        }
        else if(qty > MAX_PRODUCT_QTY) {
            $(this).closest("div").find("input[name='quantity']").val(MAX_PRODUCT_QTY);
            $(this).closest("div").find(".border_error").removeClass("border_error");
            $(this).closest("div").find(".plus_symbol").addClass("disabled");
            $(this).closest(".orderModal .modal-body").find(".badge").html("Quantité maximale atteinte");
        }
        if(qty >= 1 && qty <= MAX_PRODUCT_QTY) {
            $(this).closest("div").find(".border_error").removeClass("border_error");
            $(this).closest(".orderModal .modal-body").find(".badge").html("");
        }
        if(qty <= 2) {
            $(this).addClass("disabled");
        }
        calc_product_total_price();
    })

    // actions when click on qty +
    $(".plus_symbol:not(.disabled)").on("click", function() {
        let qty = parseInt($(this).closest("div").find("input[name='quantity']").val());
        let counter_order = $(".counter_order").html() ? parseInt($(".counter_order").html()) : 0;
        let max_qty1 = MAX_ORDER_QTY - counter_order;
        let max_qty2 = MAX_ORDER_QTY - counter_order + parseInt($(this).closest("div").find("input[name='quantity']").data("qty"));

        if(qty >= 1 && qty < MAX_PRODUCT_QTY && (qty < max_qty1 || qty < max_qty2)) {
            $(this).closest("div").find("input[name='quantity']").val(qty+1);
        }
        else if(qty >= MAX_PRODUCT_QTY) {
            $(this).closest("div").find("input[name='quantity']").val(MAX_PRODUCT_QTY);
            $(this).closest("div").find(".border_error").removeClass("border_error");
        }
        if(qty >= 1 && qty <= MAX_PRODUCT_QTY) {
            $(this).closest("div").find(".border_error").removeClass("border_error");
        }
        if(qty >= MAX_PRODUCT_QTY - 1 || isNaN(max_qty2) && qty >= max_qty1 - 1 || qty >= max_qty2 - 1) {
            $(this).addClass("disabled");
            $(this).closest(".orderModal .modal-body").find(".badge").html("Quantité maximale atteinte");
        }
        if(qty >= 1) {
            $(this).closest("div").find(".minus_symbol").removeClass("disabled");
        }
        calc_product_total_price();
    })
    
    // disables in forms orderModal
    function disable_add_order() {
        let counter_order = $(".counter_order").html() ? parseInt($(".counter_order").html()) : 0;

        // disable all in menu page (no add) if max_order_qty
        if(counter_order >= MAX_ORDER_QTY) {
            $(".form_menu button[type='submit']").prop("disabled", true);
            $(".form_menu input[name='quantity']").prop("disabled", true);
            $(".form_menu button[type='submit']").html("Commande max atteinte");
            $(".form_menu .plus_symbol, .form_menu .minus_symbol").addClass("disabled");
        };
        // disable plus_symbol if max_order_qty or max_product_qty
        $(".plus_symbol").each(function() {
            let qty = parseInt($(this).closest("div").find("input[name='quantity']").val());
            if(qty >= MAX_PRODUCT_QTY || counter_order >= MAX_ORDER_QTY) {
                $(this).addClass("disabled");
            }
        })
        // disable minus_symbol if qty = 1
        $(".minus_symbol").each(function() {
            let qty = parseInt($(this).closest("div").find("input[name='quantity']").val());
            if(qty <= 1) {
                $(this).addClass("disabled");
            }
        })
    }
    disable_add_order();            // also in Ajax for form order modal below

    $("#en-cours .orderModal").on('hide.bs.modal', function() {
        window.location.reload();
    })


    // CREATE ORDER : max attribute to limit reception date
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;    // January is 0!
    var mm_max = today.getMonth() + 2;
    var yyyy = today.getFullYear();
    if(dd < 10)
        dd = '0' + dd;
    if(mm < 10)
        mm = '0' + mm;
    if(mm_max < 10)
        mm_max = '0' + mm_max;
    
    today = yyyy + '-' + mm + '-' + dd;
    let max_date = yyyy + '-' + mm_max + '-' + dd;
    $("#reception_date_order").attr("min", today);
    $("#reception_date_order").attr("max", max_date);

    // displaying elements depending on type
    $("#type_order").on("input", function() {
        if($(this).val() == "livraison")
            $(".if_delivery").css({"display" : "block"});
        if($(this).val() != "livraison")
            $(".if_delivery").css({"display" : "none"});
        if($(this).val() == "emporte")
            $(".if_take-away").css({"display" : "inline"});
        if($(this).val() != "emporte")
            $(".if_take-away").css({"display" : "none"});
    })


    // ********************  FORMs ADMIN  ********************
    // FORM ADMIN_USERS (to disable / enable input fidelity & status)
    $(".fidelity_admin_users, .status_admin_users").removeAttr("id");
    $(".fidelity_admin_users, .status_admin_users").prop("disabled", true);

    function color_status() {
        $(".status_admin_users").each(function() {
            if($(this).children("option:selected").text() == "Admin")
                $(this).css({"color" : "#8c051e"});
            else
                $(this).css({"color" : "inherit"});
        })
    }
    color_status();
    
    $("#table_admin_users .btn_img_edit").each(function() {
        $(this).on("click", function() {
            $(".border_error").removeClass("border_error");
            $(".error_form").remove();
            $("#msg_admin_users").html("");
            $(".fidelity_admin_users, .status_admin_users").prop("disabled", true);
            $(".fidelity_admin_users, .status_admin_users").addClass("form-control-plaintext");
            $(".fidelity_admin_users, .status_admin_users").removeClass("form-control");
            $(".status_admin_users").css({"-webkit-appearance" : "none", "-moz-appearance" : "none"});
            $("#table_admin_users .btn_img_edit").css({"display" : "initial"});
            $("#table_admin_users .btn_img_save").css({"display" : "none"});
            color_status();

            let this_fidelity = $(this).closest("tr").find(".fidelity_admin_users");
            let this_select = $(this).closest("tr").find(".status_admin_users");

            this_fidelity.removeAttr("disabled");
            this_select.removeAttr("disabled");
            this_fidelity.addClass("form-control");
            this_select.addClass("form-control");
            this_fidelity.removeClass("form-control-plaintext");
            this_select.removeClass("form-control-plaintext");
            this_select.css({"-webkit-appearance" : "menulist", "-moz-appearance" : "menulist", "color" : "inherit"});
            $(this).css({"display" : "none"});
            $(this).closest("td").children("#table_admin_users .btn_img_save").css({"display" : "initial"});
        });
    })


    // FORM ADMIN_PRODUCTS (to add calculated price_ht value)
    $("#price_ttc_admin_products, #tva_admin_products").on("input", function() {
        let prix_ht = parseFloat($("#price_ttc_admin_products").val().replace(",",".")) / (1 + parseFloat($("#tva_admin_products").val()));
        let prix_ht_round = Math.round(prix_ht * 10000) / 10000;

        if(prix_ht && typeof prix_ht == "number") {
            $("#price_ht_admin_products").val(prix_ht_round);
            // prix_ht_round.length will be treated in php but it's for good precision...
        }
    })
    // SHOW / HIDE FORM
    $("#btn_new_admin_products").on("click", function() {
        $("#msg_admin_products").html("");
        $(".form_admin_section").show("slow");
        $(this).hide();
    })
    $(".form_admin_section .close").on("click", function() {
        $(".form_admin_section").hide("slow");
        $("#btn_new_admin_products").show();
        window.location.href = "admin-carte";
    })
    if(window.location.search.substr(1,4) == "edit" && !$("#msg_admin_products").html() && !$("#form_admin_products input, #form_admin_products textarea, #form_admin_products select, #form_admin_products .form-radio").hasClass("border_error")) {
        $(".form_admin_section").show("slow");
        $("#btn_new_admin_products").hide();
    }
    if(window.location.search.indexOf("delete") == -1 && $("#msg_admin_products").html() && $("#msg_admin_products").html().indexOf("a bien été") == -1 || $("#form_admin_products input, #form_admin_products textarea, #form_admin_products select, #form_admin_products .form-radio").hasClass("border_error")) {
        $(".form_admin_section").css({"display" : "block"});
        $("#btn_new_admin_products").hide();
    }



    // ********************  AJAX  ********************
    // FORM LOGIN
    $("#form_login").on("submit", function(e) {
        e.preventDefault();
        
        $.ajax({
            data: $(this).serialize(),
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            cache: false,
            dataType: "json",

            success: function(json) {
                $(".border_error").removeClass("border_error");
                $("#error_email_login").remove();
                $("#error_password_login").remove();
                $("#msg_login").html("");
                
                if(json.length == 0) {
                    $("#loginModal").modal("hide");
                    $("#form_login").removeClass("was-validated");
                    $("#form_login input:not([name='token'])").val("");
                    window.location.reload();
                }
                else {
                    if(json.error) {
                        $("#msg_login").html(json.error);
                    }
                    if(json.email || json.password) {
                        for (const key in json) {
                            if(json[key].length != 0) {
                                let div = $("#"+key+"_login").closest(".form-group");
                                let small = document.createElement('small');
                                small.setAttribute("class", "error_form");
                                small.setAttribute("id", "error_"+key+"_login");
                                div.append(small);
                                $("#error_"+key+"_login").html(json[key]);
                                $("#"+key+"_login").addClass("border_error");
                            }
                        }
                    }
                }
            },
            error: function(jqXHR, status, error) {
                console.log(status + ": " + error);
                alert("Une erreur est survenue, merci de réessayer.");
            }
        });
    });


    // FORM ACCOUNT
    $("#form_account").on("submit", function(e) {
        e.preventDefault();
        
        $.ajax({
            data: $(this).serialize(),
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            cache: false,
            dataType: "json",

            success: function(json) {
                remove_errors();                // function above
                
                if(json.validate) {
                    $("#msg_account").html(json.validate);
                    disable_form();             // function above
                    // window.location.href = "mon-compte";
                }
                else {
                    if(json.error) {
                        $("#msg_account").html(json.error);
                    }

                    if(json.first_name || json.last_name || json.email || json.phone || json.address || json.zipcode || json.town) {
                        for (const key in json) {
                            if(json[key].length != 0) {
                                let div = $("#"+key+"_account").closest(".form-group");
                                let small = document.createElement('small');
                                small.setAttribute("class", "error_form");
                                small.setAttribute("id", "error_"+key+"_account");
                                div.append(small);
                                $("#error_"+key+"_account").html(json[key]);
                                $("#"+key+"_account").addClass("border_error");
                            }
                        }
                    }
                }
            },
            error: function(jqXHR, status, error) {
                console.log(status + ": " + error);
                alert("Une erreur est survenue, merci de réessayer.");
            }
        });
    });


    // FORM PASSWORD
    $("#form_password").on("submit", function(e) {
        e.preventDefault();
        
        $.ajax({
            data: $(this).serialize(),
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            cache: false,
            dataType: "json",

            success: function(json) {
                $(".border_error").removeClass("border_error");
                $("#error_old_password_password").remove();
                $("#error_new_password_password").remove();
                $("#error_conf_new_password_password").remove();
                $("#msg_password").html("");
                
                if(json.validate) {
                    $("#passwordModal").modal("hide");
                    $("#form_password").removeClass("was-validated");
                    $("#form_password input:not([name='token'])").val("");
                    $("#msg_account").html(json.validate);
                }
                else {
                    if(json.error) {
                        $("#msg_password").html(json.error);
                    }
                    if(json.old_password || json.new_password || json.conf_new_password) {
                        for (const key in json) {
                            if(json[key].length != 0) {
                                let div = $("#"+key+"_password").closest(".form-group");
                                let small = document.createElement('small');
                                small.setAttribute("class", "error_form");
                                small.setAttribute("id", "error_"+key+"_password");
                                div.append(small);
                                $("#error_"+key+"_password").html(json[key]);
                                $("#"+key+"_password").addClass("border_error");
                            }
                        }
                    }
                }
            },
            error: function(jqXHR, status, error) {
                console.log(status + ": " + error);
                alert("Une erreur est survenue, merci de réessayer.");
            }
        });
    });


    // FORM ORDER MODAL
    $(".form_menu").each(function() {
        $(this).on("submit", function(e) {
            e.preventDefault();
            let form_menu = $(this);
            
            $.ajax({
                data: $(this).serialize(),
                type: $(this).attr("method"),
                url: $(this).attr("action"),
                cache: false,
                dataType: "json",

                success: function(json) {
                    form_menu.find(".border_error").removeClass("border_error");
                    form_menu.closest(".modal-body").find(".msg_menu").html("");
                    
                    if(json.validate) {
                        if(!$(".counter_order").html()) {
                            let counter_order = document.createElement("small");
                            counter_order.setAttribute("class", "badge-pill counter_order");
                            $("#li_order").append(counter_order);
                            $(".counter_order").html(0);
                        }
                        let new_counter_val = parseInt($(".counter_order").html()) + parseInt(json.quantity);
                        $(".counter_order").html(new_counter_val);
                        disable_add_order();                // function above

                        $("input[name='quantity']").val(1);
                        calc_product_total_price();         // function above

                        form_menu.closest(".modal-body").find(".msg_menu").html(json.validate);
                        setTimeout(function() { $(".orderModal").modal("hide"); }, 1500);
                        setTimeout(function() { form_menu.closest(".modal-body").find(".msg_menu").html(""); }, 2000);
                    }
                    else {
                        if(json.error) {
                            form_menu.closest(".modal-body").find(".msg_menu").html(json.error);
                        }
                        if(json.quantity) {
                            form_menu.closest(".modal-body").find(".msg_menu").html(json.quantity);
                            form_menu.find("input[name='quantity']").addClass("border_error");
                        }
                    }
                },
                error: function(jqXHR, status, error) {
                    console.log(status + ": " + error);
                    alert("Une erreur est survenue, merci de réessayer.");
                }
            });
        });
    });
});     // ----- FERMETURE jQuery ----- 
