<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class BB_Activity_Feed_Anywhere
 */
class BB_Activity_Feed_Anywhere {

	/** Properties ************************************************************/

	/**
	 * If the feed is enabled.
	 *
	 * @var bool
	 */
	public $feed_enabled = false;

	/**
	 * If BuddyBoss is active
	 *
	 * @var bool
	 */
	public $is_buddyboss_active = false;

	public $bp_nouveau;

	/**
	 * Initialize the class and set its properties.
	 *
	 * We register all our common hooks here.
	 *
	 * @return void
	 */
	public function __construct() {
		//If BuddyBoss is active we need to add different code
		$this->is_buddyboss_active = BB_Activity_Feed_Anywhere_Dependency_Checker::check_buddyboss();

		// See if our shortcode exists on the current queried object
		add_action( 'parse_query', array( $this, 'check_for_shortcode' ) );

		// Add the main shortcode
		add_shortcode( 'activity_feed_anywhere_for_buddyboss', array( $this, 'add_shortcode' ) );

		// Hook into the single activity template
		add_action( 'bp_before_activity_entry', array( $this, 'hook_into_activity' ) );

		// Declare pages containing our shortcode to be an activity component
		// add_filter( 'bp_is_current_component', array( $this, 'enable_component' ), 10, 2 );

		// Enable the Heartbeat to refresh activities
		add_filter( 'bp_activity_do_heartbeat', array( $this, 'enable_heartbeat' ) );

		// Template stack modification (later version)
		//add_action( 'bp_init', array( $this, 'modify_template_stack' ) );

		// Template inclusion (later version)
		//add_filter( 'bp_get_template_part', array( $this, 'entry_template' ), 999, 3 );

		// Add required body classes
		add_filter( 'body_class', array( $this, 'add_body_classes' ) );

		//load scripts
		if($this->is_buddyboss_active){
			add_action( 'bp_nouveau_includes', array( $this, 'bp_nouveau_activity'), 10, 1 );
		}
	}

	/**
	 * Tell each activity they are part of the activity component.
	 *
	 * @return void
	 */
	public function hook_into_activity() {
		buddypress()->current_component = 'activity';
	}

	/**
	 * Modify the template stack. Currently unused.
	 *
	 * @return void
	 */
	public function modify_template_stack() {
		if ( function_exists( 'bp_register_template_stack' ) ) {
			bp_register_template_stack( 'bp_tol_register_template_location', 5 );
		}
	}

	/**
	 * See if our shortcode exists on the current queried object.
	 *
	 * @return void
	 */
	public function check_for_shortcode() {
		if ( true === $this->feed_enabled ) {
			return;
		}

		$queried_object = get_queried_object();

		if ( ! $queried_object ) {
			return;
		}

		if ( 'page' !== $queried_object->post_type ) {
			return;
		}
		
		// If shortcode is added in PHP with do_shortcode(), this needs to be set before that shortcode executes. That´s why we use page template name
		// Filename need to have {BB_AFA_PAGE_TEMPLATE_FILENAME} somewhere in the filename
		$page_template_filename_without_suffix = pathinfo(get_page_template_slug( $queried_object->ID ), PATHINFO_FILENAME);
		if ( has_shortcode( $queried_object->post_content, 'activity_feed_anywhere_for_buddyboss' ) || strpos($page_template_filename_without_suffix, BB_AFA_PAGE_TEMPLATE_FILENAME) !== false) {
			buddypress()->current_component = 'activity';
			buddypress()->is_directory 		= true;
			$this->feed_enabled             = true;
		}
	}

	/**
	 * We need to tell BuddyPress that a page/post with our shortcode on, should have the activity component active.
	 *
	 * @param $is_current_component
	 * @param $component
	 *
	 * @return bool|mixed
	 */
	public function enable_component( $is_current_component, $component ) {
		if ( function_exists( 'bp_duplicate_notice' ) && $this->feed_enabled ) {
			return true;
		} else {
			return $is_current_component;
		}
	}

	/**
	 * Tell our shortcode-inclusive pages to spin up the Heartbeat.
	 *
	 * @param $retval
	 *
	 * @return bool|mixed
	 */
	public function enable_heartbeat( $retval ) {
		if ( $this->feed_enabled ) {
			return true;
		} else {
			return $retval;
		}
	}

	/**
	 * Add classes to the body of Space pages, to assist with styling.
	 *
	 * @param $classes
	 *
	 * @return mixed
	 */
	public function add_body_classes( $classes ) {
		if ( $this->feed_enabled ) {
			if($this->is_buddyboss_active){
				$classes[] = 'directory activity buddypress activity-feed-anywhere-for-buddyboss bb-page-loaded';

				//if postbox we need these also
				$classes[] = 'js dialog-body dialog-buttons-body dialog-container dialog-buttons-container initial-post-form-open';
			} else {
				$classes[] = 'activity buddypress activity-feed-anywhere-for-buddyboss';
			}
		}

		return $classes;
	}

	/**
	 * Our shortcode.
	 *
	 * @param $atts
	 */
	public function add_shortcode( $atts ) {
		$a = shortcode_atts(
			array(
				'feed'    => true,
				'postbox' => true,
			),
			$atts
		);

		if ( ! $this->feed_enabled ) {
			//return;
		}

		// We need our feed to be part of the activity component
		buddypress()->current_component = 'activity';

		$classes = "buddypress-wrap";

		if ( $this->is_buddyboss_active ) {
			$classes = "buddypress-wrap bp-single-plain-nav bp-dir-hori-nav";

			$is_send_ajax_request = ! function_exists( 'bb_is_send_ajax_request' ) || bb_is_send_ajax_request();
		}
		
		?>

		<div id="buddypress" class="<?php echo esc_html($classes); ?>">

			<?php bp_nouveau_before_activity_directory_content(); ?>

			<?php if ( true === $a['postbox'] ) : ?>

				<?php if ( is_user_logged_in() ) : ?>

					<?php bp_get_template_part( 'activity/post-form' ); ?>

				<?php endif; ?>

			<?php endif; ?>

			<?php if ( true === $a['feed'] ) : ?>

				<?php bp_nouveau_template_notices(); ?>

				<?php if ( ! bp_nouveau_is_object_nav_in_sidebar() ) : ?>

					<?php
					if ( $this->is_buddyboss_active ) {
						echo '<div class="flex actvity-head-bar">';
						bp_get_template_part( 'common/nav/directory-nav' );
						bp_get_template_part( 'common/search-and-filters-bar' );
						echo '</div>';
					} else {
						bp_get_template_part( 'common/nav/directory-nav' );
					}
					?>

				<?php endif; ?>

				<div class="screen-content">

					<?php //if ( ! $this->is_buddyboss_active ) bp_get_template_part( 'common/search-and-filters-bar' ); ?>
					<?php bp_nouveau_activity_hook( 'before_directory', 'list' ); ?>

					<?php if ( $this->is_buddyboss_active ) { ?>
						<div id="activity-stream" class="activity" data-bp-list="activity" data-ajax="<?php echo esc_attr( $is_send_ajax_request ? 'true' : 'false' ); ?>">
						<?php
							if ( $is_send_ajax_request ) {
								echo '<div id="bp-ajax-loader">';
								?>
								<div class="bb-activity-placeholder">
									<div class="bb-activity-placeholder_head">
										<div class="bb-activity-placeholder_avatar bb-bg-animation bb-loading-bg"></div>
										<div class="bb-activity-placeholder_details">
											<div class="bb-activity-placeholder_title bb-bg-animation bb-loading-bg"></div>
											<div class="bb-activity-placeholder_description bb-bg-animation bb-loading-bg"></div>
										</div>
									</div>
									<div class="bb-activity-placeholder_content">
										<div class="bb-activity-placeholder_title bb-bg-animation bb-loading-bg"></div>
										<div class="bb-activity-placeholder_title bb-bg-animation bb-loading-bg"></div>
									</div>
									<div class="bb-activity-placeholder_actions">
										<div class="bb-activity-placeholder_description bb-bg-animation bb-loading-bg"></div>
										<div class="bb-activity-placeholder_description bb-bg-animation bb-loading-bg"></div>
										<div class="bb-activity-placeholder_description bb-bg-animation bb-loading-bg"></div>
									</div>
								</div>
								<div class="bb-activity-placeholder">
									<div class="bb-activity-placeholder_head">
										<div class="bb-activity-placeholder_avatar bb-bg-animation bb-loading-bg"></div>
										<div class="bb-activity-placeholder_details">
											<div class="bb-activity-placeholder_title bb-bg-animation bb-loading-bg"></div>
											<div class="bb-activity-placeholder_description bb-bg-animation bb-loading-bg"></div>
										</div>
									</div>
									<div class="bb-activity-placeholder_content">
										<div class="bb-activity-placeholder_title bb-bg-animation bb-loading-bg"></div>
										<div class="bb-activity-placeholder_title bb-bg-animation bb-loading-bg"></div>
									</div>
									<div class="bb-activity-placeholder_actions">
										<div class="bb-activity-placeholder_description bb-bg-animation bb-loading-bg"></div>
										<div class="bb-activity-placeholder_description bb-bg-animation bb-loading-bg"></div>
										<div class="bb-activity-placeholder_description bb-bg-animation bb-loading-bg"></div>
									</div>
								</div>
								<?php
								echo '</div>';
							} else {
								bp_get_template_part( 'activity/activity-loop' );
							}
							?>
					<?php } else { ?>
						<div id="activity-stream" class="activity" data-bp-list="activity" data-ajax="false">
							<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'directory-activity-loading' ); ?></div>
						
					<?php } ?>

					</div><!-- .activity -->

					<?php bp_nouveau_after_activity_directory_content(); ?>

				</div><!-- // .screen-content -->
			<?php endif; ?>


			<?php 
			if ( ! $this->is_buddyboss_active ) {
				bp_nouveau_after_directory_page();
			}
			?>

		</div> <!-- // .buddypress -->

		<?php
	}

	/**
	 * Include a template for activity/entry. Currently unused.
	 *
	 * @param $templates
	 * @param $slug
	 * @param $name
	 *
	 * @return mixed|string[]
	 */
	public function entry_template( $templates, $slug, $name ) {
		if ( 'activity/entry' !== $slug ) {
			return $templates;
		}

		return array( 'activity/entry.php' );
	}

	/**
	 * Launch the Activity loader class.
	 *
	 * @since BuddyPress 3.0.0
	 */
	public function bp_nouveau_activity( $bp_nouveau = null ) {
		if ( is_null( $bp_nouveau ) ) {
			return;
		}

		$bp_nouveau->activity = new BP_Nouveau_Activity();
	}
	
}
