<?php
echo "<h2 class='projects-heading'>🚀 Projects  " . bp_displayed_user_fullname() . "</h2>";

// Get project count for the user
$user_id = bp_displayed_user_id();
$project_count = count_user_posts($user_id, 'project', true);
echo "<div class='project-count'>My Projects: <span class='count-number'>" . $project_count . "</span></div>";

// Add filter controls
?>
<div class="projects-filters">
    <form id="projects-filter-form" class="buddyx-form">
        <div class="buddyx-row">
            <div class="buddyx-col-md-4">
                <label for="project-status">Filter by Status:</label>
                <select name="project-status" id="project-status" class="buddyx-form-control">
                    <option value="">All Statuses</option>
                    <?php
                    $statuses = get_terms(array(
                        'taxonomy' => 'project_status',
                        'hide_empty' => true,
                    ));
                    foreach ($statuses as $status) {
                        echo '<option value="' . esc_attr($status->slug) . '">' . esc_html($status->name) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="buddyx-col-md-4">
                <label for="project-category">Filter by Category:</label>
                <select name="project-category" id="project-category" class="buddyx-form-control">
                    <option value="">All Categories</option>
                    <?php
                    $categories = get_terms(array(
                        'taxonomy' => 'publication_type',
                        'hide_empty' => true,
                    ));
                    foreach ($categories as $category) {
                        echo '<option value="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="buddyx-col-md-4">
                <label for="project-search">Search:</label>
                <div class="input-group">
                    <input type="text" name="project-search" id="project-search" class="buddyx-form-control" placeholder="Search projects...">
                    <button type="submit" class="buddyx-btn buddyx-btn-primary">Filter</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
$user_id = bp_displayed_user_id();

// Query projects by this user
$args = array(
    'post_type'      => 'project',
    'author'         => $user_id,
    'post_status'    => 'publish',
    'posts_per_page' => 12,
    'orderby'        => 'date',
    'order'          => 'DESC',
);
$projects = new WP_Query($args);
?>
<style>
.project-flat {
  border-radius: 12px;
  background: #f9fafb;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  border: 1px solid #e5e7eb;
  padding: 32px 28px 24px 28px;
  margin-bottom: 28px;
  transition: box-shadow 0.2s;
}
.project-flat:hover {
  box-shadow: 0 4px 16px rgba(37,99,235,0.08);
  border-color: #c7d2fe;
}
.project-title {
  font-size: 1.5em;
  margin-bottom: 6px;
  color: #2563eb;
  font-weight: 700;
  letter-spacing: -0.5px;
}
.project-meta {
  color: #666;
  font-size: 1em;
  margin-bottom: 10px;
  font-weight: 500;
}
.project-description {
  margin: 14px 0 14px 0;
  font-size: 1.12em;
  color: #222;
  line-height: 1.6;
}
.project-links {
  margin: 12px 0 0 0;
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}
.project-links a, .project-links button {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 1em;
  background: #fff;
  border: 1px solid #e5e7eb;
  color: #2563eb;
  cursor: pointer;
  text-decoration: none;
  padding: 7px 18px 7px 14px;
  border-radius: 999px;
  font-weight: 600;
  transition: background 0.15s, color 0.15s, border 0.15s;
  box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.project-links a:hover, .project-links button:hover {
  background: #2563eb;
  color: #fff;
  border-color: #2563eb;
}
.project-links .edit-project-btn {
  color: #2563eb;
  border-color: #c7d2fe;
  background: #eff6ff;
}
.project-links .edit-project-btn:hover {
  background: #2563eb;
  color: #fff;
  border-color: #2563eb;
}
.project-links .delete-project-btn {
  color: #e74c3c;
  border-color: #fbcaca;
  background: #fff0f0;
}
.project-links .delete-project-btn:hover {
  background: #e74c3c;
  color: #fff;
  border-color: #e74c3c;
}
.project-collaborators {
  color: #444;
  font-size: 0.99em;
  margin-top: 10px;
  font-style: italic;
}
.project-status-flat {
  display: inline-block;
  background: #f3f4f6;
  color: #16a34a;
  border-radius: 4px;
  padding: 2px 10px;
  font-size: 0.97em;
  margin-left: 8px;
  font-weight: 600;
}
@media (max-width: 600px) {
  .project-flat { padding: 18px 6px 16px 6px; }
  .project-title { font-size: 1.15em; }
}
</style>
<?php
if ($projects->have_posts()) : ?>
    <div class="project-list-flat">
        <?php while ($projects->have_posts()) : $projects->the_post();
            $project_id = get_the_ID();
            $status = get_post_meta($project_id, 'project_status', true);
            $start = get_post_meta($project_id, 'project_start', true);
            $end = get_post_meta($project_id, 'project_end', true);
            $funding = get_post_meta($project_id, 'project_funding', true);
            $doc_id = get_post_meta($project_id, 'project_document', true);
            $doc_url = $doc_id ? wp_get_attachment_url($doc_id) : '';
            $user_collabs = get_post_meta($project_id, 'project_collaborators_users', true);
            $collab_names = [];
            if (!empty($user_collabs) && is_array($user_collabs)) {
                foreach ($user_collabs as $uid) {
                    $user = get_userdata($uid);
                    if ($user) $collab_names[] = esc_html($user->display_name);
                }
            }
            $external = get_post_meta($project_id, 'project_collaborators_external', true);
            if ($external) {
                $collab_names[] = esc_html($external);
            }
            $collab = !empty($collab_names) ? implode(', ', $collab_names) : '';
            $working_area = get_post_meta($project_id, 'project_working_area', true);
        ?>
        <div class="project-flat">
            <div>
                <span class="project-title"><?php the_title(); ?></span>
                <?php if ($status): ?><span class="project-status-flat"><?php echo ucfirst($status); ?></span><?php endif; ?>
            </div>
            <div class="project-meta">
                <?php if ($start): ?><span>Start: <?php echo esc_html($start); ?></span><?php endif; ?>
                <?php if ($end): ?> | <span>End: <?php echo esc_html($end); ?></span><?php endif; ?>
                <?php if ($funding): ?> | <span>Funding: $<?php echo esc_html($funding); ?></span><?php endif; ?>
                <?php if ($working_area): ?> | <span>Working Area: <a href="<?php echo esc_url($working_area); ?>" target="_blank"><?php echo esc_html($working_area); ?></a></span><?php endif; ?>
            </div>
            <div class="project-description"><?php the_excerpt(); ?></div>
            <div class="project-links">
                <?php if ($doc_url): ?>
                  <a href="<?php echo esc_url($doc_url); ?>" target="_blank">
                    <span class="dashicons dashicons-media-document"></span> View Document
                  </a>
                <?php endif; ?>
                <?php if (bp_is_my_profile() || current_user_can('edit_others_posts')): ?>
                    <button class="edit-project-btn" data-project-id="<?php echo $project_id; ?>" title="Edit Project">
                      <span class="dashicons dashicons-edit"></span> Edit
                    </button>
                    <button class="delete-project-btn" data-project-id="<?php echo $project_id; ?>" title="Delete Project">
                      <span class="dashicons dashicons-trash"></span> Delete
                    </button>
                <?php endif; ?>
            </div>
            <?php if ($collab): ?><div class="project-collaborators">Collaborators: <?php echo $collab; ?></div><?php endif; ?>
        </div>
        <?php endwhile; ?> 
    </div>
<?php else: ?>
    <p>No projects found.</p>
<?php endif; wp_reset_postdata(); ?>

<div id="collaborator-modal" class="project-modal">
    <div class="project-modal-content">
        <span class="project-modal-close">&times;</span>
        <h2>Add Collaborator</h2>
        <form id="add-collaborator-form">
            <input type="hidden" name="project_id" id="collab_project_id">
            <label>Collaborators (Registered Users)</label>
            <select name="project_collaborators_users[]" id="collab_users" multiple style="width:100%;">
                <?php
                $users = get_users(array('fields' => array('ID', 'display_name', 'user_email')));
                foreach ($users as $user) {
                    echo '<option value="' . esc_attr($user->ID) . '">' . esc_html($user->display_name . ' (' . $user->user_email . ')') . '</option>';
                }
                ?>
            </select>
            <label>External Collaborators (Names/Emails, comma separated)</label>
            <input type="text" name="project_collaborators_external" id="collab_external" placeholder="e.g. John Doe, jane@email.com">
            <label>Common Working Area (Google Doc, GitHub, etc.)</label>
            <input type="url" name="project_working_area" id="collab_working_area" placeholder="https://...">
            <button type="submit">Save Collaborators</button>
        </form>
        <div id="collab-form-message"></div>
    </div>
</div>

<!-- Add Edit Project Modal -->
<div id="edit-project-modal" class="project-modal">
    <div class="project-modal-content">
        <span class="project-modal-close">&times;</span>
        <h2>Edit Project</h2>
        <form id="edit-project-form" enctype="multipart/form-data">
            <input type="hidden" name="project_id" id="edit_project_id">
            
            <label>Project Title*</label>
            <input type="text" name="project_title" id="edit_project_title" required>
            
            <label>Description*</label>
            <textarea name="project_description" id="edit_project_description" required></textarea>
            
            <label>Start Date</label>
            <input type="date" name="project_start" id="edit_project_start">
            
            <label>End Date</label>
            <input type="date" name="project_end" id="edit_project_end">
            
            <label>Funding Amount</label>
            <input type="number" name="project_funding" id="edit_project_funding" min="0" step="any">
            
            <label>Project Document (PDF)</label>
            <input type="file" name="project_document" accept="application/pdf">
            <div id="current_document"></div>
            
            <label>Collaborators</label>
            <input type="text" name="project_collaborators" id="edit_project_collaborators" placeholder="Names, comma separated">
            
            <button type="submit">Update Project</button>
        </form>
        <div id="edit-project-form-message"></div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#projects-filter-form').on('submit', function(e) {
        e.preventDefault();
        
        var status = $('#project-status').val();
        var category = $('#project-category').val();
        var search = $('#project-search').val();
        
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'filter_projects',
                status: status,
                category: category,
                search: search,
                nonce: '<?php echo wp_create_nonce('projects_nonce'); ?>'
            },
            success: function(response) {
                $('#projects-list').html(response);
            }
        });
    });

    // Delete project
    $('.delete-project-btn').on('click', function(e){
        e.preventDefault();
        if(!confirm('Are you sure you want to delete this project? This cannot be undone.')) return;
        var projectId = $(this).data('project-id');
        $.post(projectModalVars.ajaxurl, {
            action: 'delete_project',
            nonce: projectModalVars.nonce,
            project_id: projectId
        }, function(response){
            if(response.success){
                // Optionally show a message
                location.reload();
            } else {
                alert(response.data ? response.data : 'Could not delete project.');
            }
        });
    });

    // Handle Edit Project Click
    $('.edit-project-btn').click(function(e) {
        e.preventDefault();
        var projectId = $(this).data('project-id');
        
        // Fetch project data
        $.ajax({
            url: projectModalVars.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_project_data',
                nonce: projectModalVars.nonce,
                project_id: projectId
            },
            success: function(response) {
                if (response.success) {
                    var project = response.data;
                    $('#edit_project_id').val(project.ID);
                    $('#edit_project_title').val(project.title);
                    $('#edit_project_description').val(project.description);
                    $('#edit_project_start').val(project.start_date);
                    $('#edit_project_end').val(project.end_date);
                    $('#edit_project_funding').val(project.funding);
                    $('#edit_project_collaborators').val(project.collaborators);
                    
                    if (project.document_url) {
                        $('#current_document').html('Current document: <a href="' + project.document_url + '" target="_blank">View</a>');
                    }
                    
                    $('#edit-project-modal').show();
                }
            }
        });
    });

    // Handle Edit Project Form Submit
    $('#edit-project-form').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'update_project');
        formData.append('nonce', projectModalVars.nonce);

        $.ajax({
            url: projectModalVars.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#edit-project-form-message').html('<div class="success">' + response.data + '</div>');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $('#edit-project-form-message').html('<div class="error">' + response.data + '</div>');
                }
            }
        });
    });

    // Handle collaborator form submission
    $('#add-collaborator-form').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'add_project_collaborators');
        formData.append('nonce', projectModalVars.nonce);

        // Get current values
        var currentUsers = $('#collab_users').val() || [];
        var currentExternal = $('#collab_external').val() || '';
        var currentWorkingArea = $('#collab_working_area').val() || '';

        // Only include fields that were actually changed
        if (currentUsers.length > 0) {
            formData.set('project_collaborators_users', currentUsers);
        } else {
            formData.delete('project_collaborators_users');
        }

        if (currentExternal.trim() !== '') {
            formData.set('project_collaborators_external', currentExternal);
        } else {
            formData.delete('project_collaborators_external');
        }

        if (currentWorkingArea.trim() !== '') {
            formData.set('project_working_area', currentWorkingArea);
        } else {
            formData.delete('project_working_area');
        }

        $.ajax({
            url: projectModalVars.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#collab-form-message').html('<div class="success">' + response.data + '</div>');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $('#collab-form-message').html('<div class="error">' + response.data + '</div>');
                }
            }
        });
    });

    // Add click handler for the collaborator button
    $('.add-collaborator-btn').click(function(e) {
        e.preventDefault();
        var projectId = $(this).data('project');
        $('#collab_project_id').val(projectId);
        
        // Clear previous values
        $('#collab_users').val([]);
        $('#collab_external').val('');
        $('#collab_working_area').val('');
        
        // Show modal
        $('#collaborator-modal').show();
    });
});
</script>