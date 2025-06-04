jQuery(document).ready(function($){
    // Open modal
    $('.dashicons-plus').on('click', function(e){
        e.preventDefault();
        $('#project-modal').fadeIn();
    });
    // Close modal
    $('.project-modal-close').on('click', function(){
        $(this).closest('.project-modal').fadeOut();
    });
    // Close modal when clicking outside
    $(window).on('click', function(e){
        if($(e.target).hasClass('project-modal')){
            $(e.target).fadeOut();
        }
    });
    // Submit form via AJAX
    $('#add-project-form').on('submit', function(e){
        e.preventDefault();
        var $form = $(this);
        var $message = $('#project-form-message');
        var formData = new FormData(this);
        formData.append('action', 'add_new_project');
        formData.append('nonce', projectModalVars.nonce);

        // Show loading state
        $message.html('<div class="loading">Submitting project...</div>');
        $form.find('button[type="submit"]').prop('disabled', true);

        $.ajax({
            url: projectModalVars.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
                if(response.success){
                    $message.html('<div class="success">' + response.data + '</div>');
                    $form[0].reset();
                    setTimeout(function(){ 
                        $('#project-modal').fadeOut();
                        location.reload();
                    }, 1500);
                } else {
                    $message.html('<div class="error">' + (response.data || 'Error creating project. Please try again.') + '</div>');
                }
            },
            error: function(){
                $message.html('<div class="error">Server error. Please try again.</div>');
            },
            complete: function(){
                $form.find('button[type="submit"]').prop('disabled', false);
            }
        });
    });
    // Delete project
    $(document).on('click', '.delete-project-btn', function(e) {
        e.preventDefault();
        const projectId = $(this).data('project-id');
        const button = $(this);
        
        console.log('Delete button clicked for project ID:', projectId);
        
        if (confirm('Are you sure you want to delete this project?')) {
            console.log('Delete confirmed. Preparing AJAX request with:', {
                projectId: projectId,
                nonce: projectModalVars.nonce,
                ajaxurl: projectModalVars.ajaxurl
            });
            
            button.prop('disabled', true);
            
            $.ajax({
                url: projectModalVars.ajaxurl,
                type: 'POST',
                data: {
                    action: 'delete_project',
                    project_id: projectId,
                    nonce: projectModalVars.nonce
                },
                beforeSend: function() {
                    console.log('Sending delete request...');
                },
                success: function(response) {
                    console.log('Delete response received:', response);
                    if (response.success) {
                        console.log('Delete successful, removing project card');
                        const projectCard = button.closest('.project-card');
                        projectCard.fadeOut(400, function() {
                            $(this).remove();
                            console.log('Project card removed');
                            if ($('.project-card').length === 0) {
                                console.log('No projects left, reloading page');
                                location.reload();
                            }
                        });
                    } else {
                        console.error('Delete failed:', response.data);
                        alert(response.data || 'Error deleting project');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete AJAX error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusText: xhr.statusText,
                        readyState: xhr.readyState,
                        xhr: xhr
                    });
                    alert('Server error occurred while deleting project');
                },
                complete: function() {
                    console.log('Delete request completed');
                    button.prop('disabled', false);
                }
            });
        } else {
            console.log('Delete cancelled by user');
        }
    });
    // Edit project
    $(document).on('click', '.edit-project-btn', function(e){
        e.preventDefault();
        var projectId = $(this).data('project-id');
        console.log('Edit button clicked for project ID:', projectId);
        
        // Fetch project data
        $.ajax({
            url: projectModalVars.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_project_data',
                nonce: projectModalVars.nonce,
                project_id: projectId
            },
            beforeSend: function(){
                console.log('Fetching project data...');
                $('#edit-project-form-message').html('<div class="loading">Loading project data...</div>');
            },
            success: function(response){
                console.log('Get project data response:', response);
                if(response.success){
                    console.log('Project data received:', response.data);
                    var project = response.data;
                    $('#edit_project_id').val(project.ID);
                    $('#edit_project_title').val(project.title);
                    $('#edit_project_description').val(project.description);
                    $('#edit_project_start').val(project.start_date);
                    $('#edit_project_end').val(project.end_date);
                    $('#edit_project_funding').val(project.funding);
                    $('#edit_project_collaborators').val(project.collaborators);
                    
                    if(project.document_url){
                        console.log('Current document URL:', project.document_url);
                        $('#current_document').html('Current document: <a href="' + project.document_url + '" target="_blank">View</a>');
                    } else {
                        console.log('No document URL found');
                        $('#current_document').empty();
                    }
                    
                    $('#edit-project-modal').fadeIn();
                    $('#edit-project-form-message').empty();
                } else {
                    console.error('Error loading project data:', response.data);
                    $('#edit-project-form-message').html('<div class="error">' + (response.data || 'Error loading project data.') + '</div>');
                }
            },
            error: function(xhr, status, error){
                console.error('Get project data error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusText: xhr.statusText,
                    readyState: xhr.readyState,
                    xhr: xhr
                });
                $('#edit-project-form-message').html('<div class="error">Server error. Please try again.</div>');
            }
        });
    });
    // Handle edit form submission
    $('#edit-project-form').on('submit', function(e){
        e.preventDefault();
        var $form = $(this);
        var $message = $('#edit-project-form-message');
        var formData = new FormData(this);
        formData.append('action', 'update_project');
        formData.append('nonce', projectModalVars.nonce);

        console.log('Edit form submitted. Form data:', {
            project_id: formData.get('project_id'),
            project_title: formData.get('project_title'),
            project_description: formData.get('project_description'),
            project_start: formData.get('project_start'),
            project_end: formData.get('project_end'),
            project_funding: formData.get('project_funding'),
            project_collaborators: formData.get('project_collaborators'),
            has_document: formData.get('project_document') ? 'Yes' : 'No'
        });

        // Show loading state
        $message.html('<div class="loading">Updating project...</div>');
        $form.find('button[type="submit"]').prop('disabled', true);

        $.ajax({
            url: projectModalVars.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                console.log('Sending update request...');
            },
            success: function(response){
                console.log('Update response received:', response);
                if(response.success){
                    console.log('Update successful');
                    $message.html('<div class="success">' + response.data + '</div>');
                    setTimeout(function(){
                        console.log('Reloading page after successful update');
                        $('#edit-project-modal').fadeOut();
                        location.reload();
                    }, 1500);
                } else {
                    console.error('Update failed:', response.data);
                    $message.html('<div class="error">' + (response.data || 'Error updating project.') + '</div>');
                }
            },
            error: function(xhr, status, error){
                console.error('Update AJAX error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusText: xhr.statusText,
                    readyState: xhr.readyState,
                    xhr: xhr
                });
                $message.html('<div class="error">Server error. Please try again.</div>');
            },
            complete: function(){
                console.log('Update request completed');
                $form.find('button[type="submit"]').prop('disabled', false);
            }
        });
    });
    // Open collaborator modal
    $('.add-collaborator-btn').on('click', function(e){
        e.preventDefault();
        var projectId = $(this).data('project');
        $('#collab_project_id').val(projectId);
        $('#collaborator-modal').fadeIn();
    });
    // Handle collaborator form submission
    $('#add-collaborator-form').on('submit', function(e){
        e.preventDefault();
        var $form = $(this);
        var $message = $('#collab-form-message');
        var formData = new FormData(this);
        formData.append('action', 'add_project_collaborators');
        formData.append('nonce', projectModalVars.nonce);

        // Show loading state
        $message.html('<div class="loading">Saving collaborators...</div>');
        $form.find('button[type="submit"]').prop('disabled', true);

        $.ajax({
            url: projectModalVars.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
            if(response.success){
                    $message.html('<div class="success">' + response.data + '</div>');
                    setTimeout(function(){
                        $('#collaborator-modal').fadeOut();
                        location.reload();
                    }, 1500);
                } else {
                    $message.html('<div class="error">' + (response.data || 'Error saving collaborators.') + '</div>');
                }
            },
            error: function(){
                $message.html('<div class="error">Server error. Please try again.</div>');
            },
            complete: function(){
                $form.find('button[type="submit"]').prop('disabled', false);
            }
        });
    });
});