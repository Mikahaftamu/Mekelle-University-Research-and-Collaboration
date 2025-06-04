<?php
// Get project status
$status_terms = get_the_terms(get_the_ID(), 'project_status');
$status = $status_terms ? $status_terms[0]->slug : '';

// Get project URL
$project_url = get_post_meta(get_the_ID(), 'project_url', true);
?>

<div class="project-card">
    <?php if (has_post_thumbnail()) : ?>
        <div class="project-thumbnail">
            <?php the_post_thumbnail('medium'); ?>
        </div>
    <?php endif; ?>
    
    <div class="project-content">
        <h3 class="project-title"><?php the_title(); ?></h3>
        
        <?php if ($status) : ?>
            <span class="status-badge <?php echo esc_attr($status); ?>">
                <?php echo esc_html($status_terms[0]->name); ?>
            </span>
        <?php endif; ?>
        
        <div class="project-excerpt">
            <?php the_excerpt(); ?>
        </div>
        
        <div class="project-meta">
            <?php if ($project_url) : ?>
                <a href="<?php echo esc_url($project_url); ?>" class="project-link" target="_blank">
                    Visit Project
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>