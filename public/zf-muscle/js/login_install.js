$(function ()
{
    $.validator.setDefaults({
        debug: true,
        success: "valid"
    });
    
    var $target; // to hold element to display ajax response
    
    var $action; // to hold our type of effect
    var $actionOn; // to hold the element to perform effect on
    
    var $loading = $('#loading'); // to hold our loading gif
    
    var $form; // to hold our form
    
    var position = 'top'; // to hold our notification position, default to 'top-right'
    var style = 'bar'; // to hold our notification style, default to 'bar'
    var type = 'info';
    var title;
    var timeout = 4000; // timeout for notification, default to 4sec
    
    var $url; // to hold redirect urls
    
    
    // Binding Role Resources to an event
    $("#role-resource li div input[type='checkbox']").change(function ()
    {
        $(this).parent().siblings('ul').find("input[type='checkbox']").prop('checked', this.checked);
    });
    
    /**
     * Display flashmessanger if not empty
     */
    type = $('#flash-type').html();
    var $msg = $('#flash-message').html();
    if ($msg)
    {
        var $flashObj = {
            'message' : $msg
        };
        showNotify($flashObj);
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
            $target = $('#result');
            $action = 'fadeOut';
            $actionOn = $('#login');
            $form = $('#form-login');
            
            $.ajax({
                type: "POST",
                url: form.action,
                data: $form.serialize(),
                beforeSend: function()
                {
                    $loading.show("slow", function()
                    {
                        $('.login-container').fadeTo("slow", 0.3, function()
                        {
                        }).delay(5000);
                    }).delay(3000);
                }
            })
            .done(function(data)
            {
                position = 'top-right';
                
                if (data.status === "ERROR")
                {
                    type = 'danger';
                    style = 'flip';
                }
                else if (data.status === "SUCCESS")
                {
                    type = 'info';
                    title = data.name;
                    style = 'circle';
                }
        
                $('.login-container').fadeTo("slow", 1, function()
                {
                    $loading.hide("slow", function()
                    {
                        showNotify(data);
                    }).delay(3000);
                });
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
                    $loading.show("slow", function()
                    {
                        $('.page-content-wrapper').fadeTo("slow", 0.3, function()
                        {
                        }).delay(5000);
                    }).delay(3000);
                }
            })
            .done(function(data)
            {
                timeout = 6000;
                if (data.status === "ERROR")
                {
                    type = 'danger';
                }
                else if (data.status === "SUCCESS")
                {
                    type = 'success';
                }
                
                $('.page-content-wrapper').fadeTo("slow", 1, function()
                {
                    $loading.hide("slow", function()
                    {
                        showNotify(data);
                    }).delay(3000);
                });
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
                    $loading.show("slow", function()
                    {
                        $('.page-content-wrapper').fadeTo("slow", 0.3, function()
                        {
                        }).delay(5000);
                    }).delay(3000);
                }
            })
            .done(function(data)
            {
                timeout = 6000;
                if (data.status === "ERROR")
                {
                    type = 'danger';
                }
                else if (data.status === "SUCCESS")
                {
                    type = 'success';
                }
                
                $('.page-content-wrapper').fadeTo("slow", 1, function()
                {
                    $loading.hide("slow", function()
                    {
                        showNotify(data);
                    }).delay(3000);
                });
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
        if (style === 'flip')
        {
            $('body').pgNotification({
                style: style,
                message: response.message,
                position: position,
                timeout: timeout,
                type: type,
                onClose: is_redirect(response.redirect)
            }).show();
        }
        else if (style === 'circle')
        {
            $('body').pgNotification({
                style: style,
                title: title,
                message: response.message,
                position: position,
                timeout: timeout,
                type: type,
                onClose: is_redirect(response.redirect)
            }).show();
        }
        else if (style === 'bar')
        {
            $('body').pgNotification({
                style: style,
                message: response.message,
                position: position,
                timeout: timeout,
                type: type,
                onClose: is_redirect(response.redirect)
            }).show();
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
