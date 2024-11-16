function queryParams(p) {
    return {
        page: p.offset / p.limit + 1,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}
window.icons = {
    refresh: 'bx-refresh',
    toggleOff: 'bx-toggle-left',
    toggleOn: 'bx-toggle-right'
}
function loadingTemplate(message) {
    return '<i class="bx bx-loader-alt bx-spin bx-flip-vertical" ></i>'
}

function actionFormatter(value, row, index) {
    return [
        '<a href="/superadmin/customers/edit/' + row.id + '" title=' + label_update + '>' +
        '<i class="bx bx-edit mx-1">' +
        '</i>' +
        '</a>' +
        '<button title=' + label_delete + ' type="button" class="btn delete" data-id=' + row.id + ' data-type="customers">' +
        '<i class="bx bx-trash text-danger mx-1"></i>' +
        '</button>'
    ]
}
$('.delete-customer').on('click', function () {

    var id = $(this).data('id');
    var type = $(this).data('type');
    var routePrefix = $('#table').data('routePrefix');
})
$(document).ready(function () {
    // Handle form submission
    $('#registerCustomerForm').on('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        const phoneInputElement = document.getElementById('phone-input');
        if (phoneInputElement && phoneInputElement.phoneInputMethods) {
            const countryCode = phoneInputElement.phoneInputMethods.getCountryCode();
            const number = phoneInputElement.phoneInputMethods.getNumber();
            const countryISOCode = phoneInputElement.phoneInputMethods.getISOCode();
            $('#phone_number').val(number);
            $('#country_code').val(countryCode);
            $('#country_iso_code').val(countryISOCode);
            console.log(countryCode, number);
        } else {
            console.error('Phone input methods not found');
        }


        // Perform client-side validation
        var firstName = $('#first_name').val();
        var lastName = $('#last_name').val();
        var email = $('#email').val();
        var phone = $('#phone_number').val();
        var country_code = $('#country_code').val();
        var password = $('#password').val();
        var confirmPassword = $('#password_confirmation').val();
        if (firstName == '' || lastName == '' || email == '' || phone == '' || password == '' || confirmPassword == '') {
            toastr.error('All fields are required');
            return false;
        }
        var nameRegex = /^[^\d]+$/;
        if (!nameRegex.test(firstName) || !nameRegex.test(lastName)) {
            toastr.error('Name fields cannot contain integers');
            return false;
        }

        var phoneRegex = /^\d+$/;
        if (!phoneRegex.test(phone)) {
            toastr.error('Please enter a valid phone number without alphabets');
            return false;
        }
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            toastr.error('Please enter a valid email address');
            return false;
        }
        if (password.length < 6) {
            toastr.error('Password must be at least 6 characters long');
            return false;
        }
        if (password != confirmPassword) {
            toastr.error('Password and Confirm Password do not match');
            return false;
        }
        // If validation passes, proceed with AJAX request
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: $(this).serialize(), // Serialize form data
            success: function (response) {
                // Handle success response
                console.log(response);
                toastr.success('Customer registered successfully');
                // Optionally, you can redirect the user to another page after successful registration
                setTimeout(function () {
                    window.location = response.redirect_url;
                }, 2000);
            },
            error: function (xhr, status, error) {
                // Handle error response
                var errors = xhr.responseJSON.errors;
                console.log(errors);
                // Check if there are any validation errors
                if (errors) {
                    // Loop through each error and display it using toastr
                    $.each(errors, function (key, value) {
                        toastr.error(value);
                    });
                }
                else {
                    if (xhr.responseJSON.error) {
                        console.log(xhr.responseJSON);
                        $.each(xhr.responseJSON.message, function (key, value) {
                            toastr.error(value);
                        })
                    }
                    else {
                        // If there are no validation errors, display a generic error message
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            }
        });
    });
});
$(document).ready(function () {
    if ($('#phone-input').length) {
        const phoneInput = initPhoneInput('phone-input');

    }
});
$(document).ready(function () {
    if ($('#phone-input-edit').length) {

        const phoneInput = initPhoneInput('phone-input-edit', $('#country_code').val(), $('#country_iso_code').val());
        $('form').on('submit', function (e) {
            $('#country_code').val(phoneInput.getCountryCode());
            $('#phone_number').val(phoneInput.getNumber().replace(/\s+/g, ''));
            $('#country_iso_code').val(phoneInput.getISOCode());

        });
    }
});
