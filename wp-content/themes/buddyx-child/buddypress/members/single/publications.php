<?php
echo "<h2 class='publication-heading'>📚 My Publications  " . bp_displayed_user_fullname() . "</h2>";

$args = array(
    'post_type'      => 'publication',
    'posts_per_page' => -1,
    'author'         => bp_displayed_user_id()
);

$publications = new WP_Query($args);

if ($publications->have_posts()) :
    echo '<div class="publications-grid">';

    while ($publications->have_posts()) : $publications->the_post(); ?>

        <div class="publication-card">
            <div class="card-header">
                <h3 class="publication-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>
                <div class="publication-meta">
                    <?php if (get_field('journal_name')) : ?>
                        <span class="meta-item journal"><i class="fas fa-book"></i> <?php the_field('journal_name'); ?></span>
                    <?php endif; ?>
                    <?php if (get_field('publication_date')) : ?>
                        <span class="meta-item date"><i class="far fa-calendar-alt"></i> <?php the_field('publication_date'); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-content">
                <?php if (get_the_excerpt()) : ?>
                    <p class="publication-excerpt"><?php the_excerpt(); ?></p>
                <?php endif; ?>

                <div class="publication-details">
                    <?php if (get_field('orcid_id')) : ?>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-id-badge"></i> ORCID:</span>
                            <a href="https://orcid.org/<?php the_field('orcid_id'); ?>" target="_blank"><?php the_field('orcid_id'); ?></a>
                        </div>
                    <?php endif; ?>

                    <?php if (get_field('doi')) : ?>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-link"></i> DOI:</span>
                            <a href="https://doi.org/<?php the_field('doi'); ?>" target="_blank"><?php the_field('doi'); ?></a>
                        </div>
                    <?php endif; ?>

                    <?php if (get_field('review_status')) : ?>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-clipboard-check"></i> Review Status:</span>
                            <span class="status-badge <?php echo sanitize_title(get_field('review_status')); ?>"><?php the_field('review_status'); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (get_field('award_won')) : ?>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-trophy"></i> Award:</span>
                            <?php the_field('award_won'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-footer">
                <?php if (get_field('pdf')) : ?>
                    <a href="<?php the_field('pdf'); ?>" target="_blank" class="action-button pdf"><i class="fas fa-file-pdf"></i> PDF</a>
                <?php endif; ?>

                <?php if (get_field('publication_url')) : ?>
                    <a href="<?php the_field('publication_url'); ?>" target="_blank" class="action-button online"><i class="fas fa-globe"></i> Read Online</a>
                <?php endif; ?>

                <?php if (get_field('github_repo')) : ?>
                    <a href="<?php the_field('github_repo'); ?>" target="_blank" class="action-button github"><i class="fab fa-github"></i> Code</a>
                <?php endif; ?>

                <?php if (get_field('dataset_url')) : ?>
                    <a href="<?php the_field('dataset_url'); ?>" target="_blank" class="action-button dataset"><i class="fas fa-database"></i> Data</a>
                <?php endif; ?>
            </div>

            <?php if (get_field('graphical_abstract')) : ?>
                <div class="graphical-abstract">
                    <div class="expandable-image">
                        <img src="<?php the_field('graphical_abstract'); ?>" alt="Graphical Abstract" />
                        <span class="expand-icon"><i class="fas fa-expand"></i></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    <?php endwhile;

    echo '</div>';
    wp_reset_postdata();
else :
    echo '<p class="no-publications">No publications found for this user.</p>';
endif;
?>

<style>
/* Base Styles */
.publications-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    margin: 0 auto;
    max-width: 1200px;
}

.publication-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.publication-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.12);
}

.card-header {
    padding: 20px;
    background: linear-gradient(135deg, #0073aa 0%, #005f8d 100%);
    color: white;
}

.publication-title {
    margin: 0 0 10px 0;
    font-size: 1.4em;
    line-height: 1.3;
}

.publication-title a {
    color: white;
    text-decoration: none;
    transition: color 0.2s;
}

.publication-title a:hover {
    color: rgba(255, 255, 255, 0.9);
}

.publication-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 0.85em;
    opacity: 0.9;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.card-content {
    padding: 20px;
    flex-grow: 1;
}

.publication-excerpt {
    color: #555;
    margin-bottom: 15px;
    font-size: 0.95em;
    line-height: 1.5;
}

.publication-details {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
    margin-top: 15px;
}

.detail-item {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    font-size: 0.9em;
}

.detail-label {
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 5px;
}

.status-badge {
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: 500;
}

.status-badge.in_review {
    background: #fff3cd;
    color: #856404;
}

.status-badge.published {
    background: #d4edda;
    color: #155724;
}

.status-badge.rejected {
    background: #f8d7da;
    color: #721c24;
}

.card-footer {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 0 20px 20px 20px;
    border-top: 1px solid #eee;
    padding-top: 15px;
}

.action-button {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.85em;
    text-decoration: none;
    transition: all 0.2s;
}

.action-button.pdf {
    background: #f8f9fa;
    color: #d32f2f;
    border: 1px solid #d32f2f;
}

.action-button.pdf:hover {
    background: #d32f2f;
    color: white;
}

.action-button.online {
    background: #f8f9fa;
    color: #0073aa;
    border: 1px solid #0073aa;
}

.action-button.online:hover {
    background: #0073aa;
    color: white;
}

.action-button.github {
    background: #f8f9fa;
    color: #333;
    border: 1px solid #333;
}

.action-button.github:hover {
    background: #333;
    color: white;
}

.action-button.dataset {
    background: #f8f9fa;
    color: #28a745;
    border: 1px solid #28a745;
}

.action-button.dataset:hover {
    background: #28a745;
    color: white;
}

.graphical-abstract {
    padding: 0 20px 20px 20px;
}

.expandable-image {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s;
}

.expandable-image img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 8px;
    transition: transform 0.3s;
}

.expandable-image:hover img {
    transform: scale(1.02);
}

.expand-icon {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.expandable-image:hover .expand-icon {
    opacity: 1;
}

.publication-heading {
    font-size: 2em;
    margin-bottom: 30px;
    font-weight: 600;
    color: #333;
    text-align: center;
    position: relative;
    padding-bottom: 15px;
}

.publication-heading:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: linear-gradient(90deg, #0073aa, #00a0d2);
}

.no-publications {
    text-align: center;
    color: #666;
    font-size: 1.1em;
    padding: 40px;
    background: #f9f9f9;
    border-radius: 8px;
    max-width: 600px;
    margin: 0 auto;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .publications-grid {
        grid-template-columns: 1fr;
    }
    
    .publication-heading {
        font-size: 1.6em;
    }
}

/* Font Awesome Icons (if not already loaded) */
.fas, .far, .fab {
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
}

.fab {
    font-family: 'Font Awesome 5 Brands';
}
</style>