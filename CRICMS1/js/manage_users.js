$(document).ready(function() {
    // Show/hide fields based on user type
    $('#user_type').on('change', function() {
        var userType = $(this).val();
        if (userType === 'resident') {
            $('#resident_fields').show();
            $('#moderator_fields').hide();
        } else {
            $('#resident_fields').hide();
            $('#moderator_fields').show();
        }
    });

    // Populate edit form with user data
    $('.edit-user').on('click', function() {
        var userId = $(this).data('id');
        var userType = $(this).data('type');
        var modal = $('#editUserModal');

        // AJAX call to fetch user data by user ID
        $.ajax({
            url: 'get_user.php',
            type: 'POST',
            data: { userId: userId, userType: userType },
            dataType: 'json',
            success: function(response) {
                if (userType === 'resident') {
                    // Populate edit form fields for resident
                    modal.find('#edit_user_id').val(response.resident_id);
                    modal.find('#edit_user_type').val('resident');
                    modal.find('#edit_username').val(response.username);
                    modal.find('#edit_full_name').val(response.full_name);
                    modal.find('#edit_region_id').val(response.region_id);
                    modal.find('#edit_zone_id').val(response.zone_id);
                    modal.find('#edit_woreda_id').val(response.woreda_id);
                    modal.find('#edit_city_id').val(response.city_id);
                    modal.find('#edit_kebele_id').val(response.kebele_id);
                    modal.find('#edit_resident_fields').show();
                    modal.find('#edit_moderator_fields').hide();
                } else {
                    // Populate edit form fields for moderator
                    modal.find('#edit_user_id').val(response.moderator_id);
                    modal.find('#edit_user_type').val('moderator');
                    modal.find('#edit_username').val(response.username);
                    modal.find('#edit_kebele_id').val(response.kebele_id);
                    modal.find('#edit_city_id').val(response.city_id);
                    modal.find('#edit_resident_fields').hide();
                    modal.find('#edit_moderator_fields').show();
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });

        modal.modal('show');
    });

    // Populate delete form with user data
    $('.delete-user').on('click', function() {
        var userId = $(this).data('id');
        var userType = $(this).data('type');
        var modal = $('#deleteUserModal');

        modal.find('#delete_user_id').val(userId);
        modal.find('#delete_user_type').val(userType);
        modal.modal('show');
    });
});
