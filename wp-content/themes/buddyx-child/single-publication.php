<?php get_header(); ?>

<div id="primary" class="content-area single-publication">
  <main id="main" class="site-main">

    <?php while ( have_posts() ) : the_post(); ?>

      <article <?php post_class('publication-article'); ?> style="padding: 40px; max-width: 1000px; margin: auto;">

        <header class="entry-header">
          <h1 class="entry-title" style="font-size: 2.2em; color: #333;"><?php the_title(); ?></h1>
        </header>

        <div class="publication-grid" style="display: flex; flex-wrap: wrap; gap: 30px; margin-top: 30px;">
          
          <!-- Left Column: Metadata -->
<div class="publication-meta" style="flex: 1 1 300px; background: #f9f9f9; padding: 20px; border-radius: 8px;">
  <h3 style="margin-top: 0;">Details</h3>

  <p><strong>👤 Author:</strong> <?php the_author(); ?></p>

  <p><strong>📘 Journal:</strong> <?php echo esc_html(get_field('journal_name')); ?></p>

  <?php if ($orcid = get_field('orcid_id')) : ?>
    <p><strong>🧬 ORCID:</strong> 
      <a href="https://orcid.org/<?php echo esc_attr($orcid); ?>" target="_blank">
        <?php echo esc_html($orcid); ?>
      </a>
    </p>
  <?php endif; ?>

  <?php if ($doi = get_field('doi')) : ?>
    <p><strong>🔗 DOI:</strong> 
      <a href="https://doi.org/<?php echo esc_attr($doi); ?>" target="_blank">
        <?php echo esc_html($doi); ?>
      </a>
    </p>
  <?php endif; ?>

  <?php if ($pdf = get_field('pdf')) : ?>
    <p><strong>📄 PDF:</strong> 
      <a href="<?php echo esc_url($pdf); ?>" target="_blank">Download PDF</a>
    </p>
  <?php endif; ?>
</div>


          <!-- Right Column: Main Content -->
          <div class="publication-content" style="flex: 2 1 600px;">
            <div class="entry-content">
              <?php the_content(); ?>
            </div>
          </div>

        </div>

        <!-- Navigation & View All Button -->
        <div class="publication-footer" style="margin-top: 40px;">
          <div class="publication-nav" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="nav-previous">
              <?php previous_post_link('%link', '← Previous'); ?>
            </div>
            <div class="nav-next">
              <?php next_post_link('%link', 'Next →'); ?>
            </div>
          </div>

          <div class="view-all" style="text-align: center; margin-top: 30px;">
            <a href="<?php echo site_url('/publications'); ?>" class="button" style="background: #0073aa; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none;">
              📚 View All Publications
            </a>
          </div>
        </div>

      </article>

    <?php endwhile; ?>

  </main>
</div>

<?php get_footer(); ?>
