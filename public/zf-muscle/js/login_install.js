$(function ()
{
    $.validator.setDefaults({
        debug: true,
        success: "valid"
    });
    
    var $target = $('#flash-message'); // to hold element to display ajax response
    var $loading = $('#loading'); // to hold our loading gif
    var $form; // to hold our form
    var $url; // to hold redirect urls
    var obj;
    var bgColor = "bg-danger";
    
    
    // Binding Role Resources to an event
    $("#role-resource li div input[type='checkbox']").change(function ()
    {
        $(this).parent().siblings('ul').find("input[type='checkbox']").prop('checked', this.checked);
    });
    
    /**
     * Display flashmessanger if not empty
     */
    var message = $('#flash-message').html();
    if (message)
    {
        obj = {
            'message': message
        };
        showNotify(obj);
    }
    
//    $("#flashmessage-main").slideDown("slow", function () {
//        $("#flashmessage-main").slideUp("slow");
//    }).delay(3000);
    
    
    /**
     * Validate Install | Login | Registration forms
     */
    $('#form-install').validate({
        rules: {
            firstname: 'required',
            lastname: 'required',
            username: {
                required: true,
                cMinLength: '3',
                nowhitespace: true
            },
            email: {
                required: true,
                email: true,
                nowhitespace: true
            },
            password: {
                required: true,
                cMinLength: 6,
                nowhitespace: true
            },
            passwordVerify: {
                equalTo: "#password",
                nowhitespace: true
            }
        },
        messages: {
            firstname: 'First Name required',
            lastname: 'Last Name required',
            username: {
                required: 'Username required',
                cMinLength: 'Username must at least be 3 characters long'
            },
            email: {
                required: 'Email required',
                email: 'Enter valid email'
            },
            password: {
                required: 'Password required',
                cMinLength: 'Password must at least be 6 characters long'
            },
            passwordVerify: {
                equalTo: 'Passwords do not match'
            }
        },
        submitHandler: function(form) {
//            $(form).ajaxSubmit();
            form.submit();
        }
    });
    $('#form-login').validate({
        rules: {
            identity: {
                required: true,
                email: true,
                nowhitespace: true
            },
            credential: {
                required: true,
            }
        },
        messages: {
            identity: {
                required: 'Email required',
                email: 'Enter valid email'
            },
            credential: {
                required: 'Credential required'
            }
        },
        submitHandler: function(form) {
            $form = $('#form-login');
            $.ajax({
                type: "POST",
                url: form.action,
                data: $form.serialize(),
                beforeSend: function()
                {
                    $('#login').attr('disabled', 'disabled');
                    toggleAjaxLoader();
                }
            })
            .done(function(data)
            {
                toggleAjaxLoader(data);
                $('#login').removeAttr('disabled');
            });
        }
    });
    $('#form-user-add').validate({
        rules: {
            firstname: 'required',
            lastname: 'required',
            username: {
                required: true,
                cMinLength: '3',
                nowhitespace: true
            },
            email: {
                required: true,
                email: true,
                nowhitespace: true
            },
            password: {
                required: true,
                cMinLength: 6,
                nowhitespace: true
            },
            passwordVerify: {
                equalTo: "#password",
                nowhitespace: true
            },
            role: 'required'
        },
        messages: {
            firstname: 'First Name required',
            lastname: 'Last Name required',
            username: {
                required: 'Username required',
                cMinLength: 'Username must at least be 3 characters long'
            },
            email: {
                required: 'Email required',
                email: 'Enter valid email'
            },
            password: {
                required: 'Password required',
                cMinLength: 'Password must at least be 6 characters long'
            },
            passwordVerify: {
                equalTo: 'Passwords do not match'
            },
            role: 'Role is required'
        },
        submitHandler: function(form) {
//            form.submit();
            $form = $('#form-user-add');
            
            $.ajax({
                type: "POST",
                url: form.action,
                data: $form.serialize(),
                beforeSend: function()
                {
                    $('#install').attr('disabled', 'disabled');
                    toggleAjaxLoader();
                }
            })
            .done(function(data)
            {
                toggleAjaxLoader(data);
                $('#install').removeAttr('disabled');
            });
        }
    });
    
    $('#form-role-add').validate({
        rules: {
            name: {
                required: true,
                cMinLength: '3'
            }
        },
        messages: {
            name: {
                required: 'Role name required',
                cMinLength: 'Role name must at least be 3 characters long'
            }
        },
        submitHandler: function(form) {
            $form = $('#form-role-add');
            
            $.ajax({
                type: "POST",
                url: form.action,
                data: $form.serialize(),
                beforeSend: function()
                {
                    $('#addrole').attr('disabled', 'disabled');
                    toggleAjaxLoader();
                }
            })
            .done(function(data)
            {
                toggleAjaxLoader(data);
                $('#addrole').removeAttr('disabled');
            });
        }
    });
    
    /**
     * Checkbox controls
     */
    $('#remember_me').change(function()
    {
        if($(this).prop("checked") === true)
        {
            $(this).val("1");
        }
        else if($(this).prop("checked") === false)
        {
            $(this).val("0");
        }
    });
    
    function showNotify(response)
    {
        if ($target.is(':hidden') === false)
        {
            if ($target.hasClass(bgColor))
            {
                $target.removeClass(bgColor);
            }
            $target.slideUp("fast");
        }

        if (response.status === "ERROR")
        {
            bgColor = "bg-danger";
        }
        else if (response.status === "SUCCESS")
        {
            bgColor = "bg-success";
        }

        $target.addClass(bgColor);

        $target.html(response.message).slideDown("slow");
        if (response.redirect)
        {
            is_redirect(response.redirect);
        }
    }
    
    function is_redirect(redirect)
    {
        if (redirect === '1')
        {
            $url = $('#redirect_on_success_url').val();
            window.setTimeout(function()
            {
                $(location).attr('href', $url);
            }, 5000);
        }
        return false;
    }

    function toggleAjaxLoader(response)
    {
        if (($loading).is(":hidden") === true)
        {
            $loading.show("slow", function ()
            {
                $('body').addClass("body-overlay").delay(5000);
            }).delay(3000);
        }
        else
        {
            $loading.hide("slow", function()
            {
                if (response.message !== null)
                {
                    showNotify(response);
                }
                $('body').removeClass("body-overlay");
            }).delay(3000);
        }
    }
    
    $("#username").focus(function() {
        var firstname = $("#firstname").val().toLowerCase();
        var lastname = $("#lastname").val().toLowerCase();
        
        if (firstname && lastname && !this.value)
        {
            this.value = firstname + lastname;
        }
        
        $("#username").trigger('change');
    });
    
    /**
     * Custom validation rules begins
     */
    
    $.validator.addMethod("cMinLength", function(value, element, param) {
        return this.optional( element ) || value.length >= param ;
    }, 'Field must at least be {0} characters long');
    $.validator.addMethod("nowhitespace", function(value, element) {
	return this.optional(element) || /^\S+$/i.test(value);
    }, "No white spaces please");
    /**
     * Custom validation rules ends
     */
});
