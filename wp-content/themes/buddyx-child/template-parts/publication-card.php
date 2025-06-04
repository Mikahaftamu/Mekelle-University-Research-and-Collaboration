<div class="publication-card buddyx-card">
    <div class="buddyx-card-body">
        <?php if (has_post_thumbnail()) : ?>
            <div class="publication-thumbnail">
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('medium', array('class' => 'buddyx-card-img-top')); ?>
                </a>
            </div>
        <?php endif; ?>
        
        <div class="publication-content">
            <h3 class="publication-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            
            <div class="publication-meta">
                <?php
                $authors = get_field('authors');
                if ($authors) {
                    echo '<div class="publication-authors">';
                    $author_links = array();
                    foreach ($authors as $author) {
                        $author_links[] = '<a href="' . bp_core_get_user_domain($author['ID']) . '">' . esc_html($author['display_name']) . '</a>';
                    }
                    echo implode(', ', $author_links);
                    echo '</div>';
                }
                
                $date = get_field('publication_date');
                if ($date) {
                    echo '<div class="publication-date">' . date('F Y', strtotime($date)) . '</div>';
                }
                
                $types = get_the_terms(get_the_ID(), 'publication_type');
                if ($types && !is_wp_error($types)) {
                    echo '<div class="publication-types">';
                    $type_links = array();
                    foreach ($types as $type) {
                        $type_links[] = '<a href="' . get_term_link($type) . '">' . $type->name . '</a>';
                    }
                    echo implode(', ', $type_links);
                    echo '</div>';
                }
                ?>
            </div>
            
            <div class="publication-excerpt">
                <?php 
                $abstract = get_field('abstract');
                if ($abstract) {
                    echo '<p>' . wp_trim_words($abstract, 20) . '</p>';
                } else {
                    the_excerpt();
                }
                ?>
            </div>
            
            <div class="publication-actions">
                <a href="<?php the_permalink(); ?>" class="buddyx-btn buddyx-btn-sm buddyx-btn-primary">View Details</a>
                
                <?php if (get_field('pdf')) : ?>
                    <a href="<?php echo esc_url(get_field('pdf')); ?>" class="buddyx-btn buddyx-btn-sm buddyx-btn-secondary" download>Download PDF</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>