<?php
/*
 * Template name: Custom Template With Activity Feed
 * 
 * This template can be overridden by copying it to yourtheme/page-custom-template-with-activity-feed.php
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header();
?>

<div id="primary" class="content-area bs-bp-container custom-template-with-activity-feed">
	<main id="main" class="site-main">
		<?php if ( have_posts() ) :

			do_action( THEME_HOOK_PREFIX . '_template_parts_content_top' );

			while ( have_posts() ) : the_post();

				do_action( THEME_HOOK_PREFIX . '_single_template_part_content', 'page' );

				do_shortcode('[activity_feed_anywhere_for_buddyboss]');

			endwhile; // End of the loop.
		else :
			get_template_part( 'template-parts/content', 'none' );
			?>

		<?php endif; ?>
	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
