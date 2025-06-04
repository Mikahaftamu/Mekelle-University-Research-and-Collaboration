<?php
/**
 * Publications Archive Template
 */

get_header(); ?>

<div class="buddyx-container buddyx-publications-archive">
    <div class="buddyx-row">
        <div class="buddyx-col-12">
            <header class="page-header">
                <h1 class="page-title">Research Publications</h1>
                <div class="archive-description">
                    <p>Browse all publications from our research community</p>
                </div>
            </header>
            
            <div class="publications-filters">
                <form id="publications-filter-form" class="buddyx-form">
                    <div class="buddyx-row">
                        <div class="buddyx-col-md-3">
                            <label for="publication-type">Filter by Type:</label>
                            <select name="publication-type" id="publication-type" class="buddyx-form-control">
                                <option value="">All Types</option>
                                <?php
                                $types = get_terms(array(
                                    'taxonomy' => 'publication_type',
                                    'hide_empty' => true,
                                ));
                                foreach ($types as $type) {
                                    echo '<option value="' . esc_attr($type->slug) . '">' . esc_html($type->name) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="buddyx-col-md-3">
                            <label for="publication-year">Filter by Year:</label>
                            <select name="publication-year" id="publication-year" class="buddyx-form-control">
                                <option value="">All Years</option>
                                <?php
                                $years = get_terms(array(
                                    'taxonomy' => 'publication_year',
                                    'hide_empty' => true,
                                    'orderby' => 'name',
                                    'order' => 'DESC'
                                ));
                                foreach ($years as $year) {
                                    echo '<option value="' . esc_attr($year->slug) . '">' . esc_html($year->name) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="buddyx-col-md-3">
                            <label for="publication-author">Filter by Author:</label>
                            <select name="publication-author" id="publication-author" class="buddyx-form-control">
                                <option value="">All Authors</option>
                                <?php
                                $authors = get_users(array(
                                    'role__in' => array('administrator', 'editor', 'author', 'contributor', 'subscriber'),
                                    'orderby' => 'display_name'
                                ));
                                foreach ($authors as $author) {
                                    echo '<option value="' . esc_attr($author->ID) . '">' . esc_html($author->display_name) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="buddyx-col-md-3">
                            <label for="publication-search">Search:</label>
                            <div class="input-group">
                                <input type="text" name="publication-search" id="publication-search" class="buddyx-form-control" placeholder="Search publications...">
                                <button type="submit" class="buddyx-btn buddyx-btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div id="publications-list" class="publications-list">
                <?php if (have_posts()) : ?>
                    <div class="buddyx-row">
                        <?php while (have_posts()) : the_post(); ?>
                            <div class="buddyx-col-md-6 buddyx-col-lg-4">
                                <?php get_template_part('template-parts/publication', 'card'); ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="publications-pagination">
                        <?php
                        echo paginate_links(array(
                            'prev_text' => __('« Previous'),
                            'next_text' => __('Next »'),
                        ));
                        ?>
                    </div>
                <?php else : ?>
                    <p>No publications found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>