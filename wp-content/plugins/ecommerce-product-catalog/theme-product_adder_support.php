<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility checker
 *
 * Here current theme is checked for compatibility with WP PRODUCT ADDER.
 *
 * @version		1.1.2
 * @package		ecommerce-product-catalog/functions
 * @author 		Norbert Dreszer
 */
class ic_catalog_notices {

	function __construct( $run = false ) {
		if ( $run ) {
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( AL_PLUGIN_MAIN_FILE ), array( $this, 'catalog_links' ) );
			add_action( 'wp_ajax_hide_review_notice', array( $this, 'ajax_hide_review_notice' ) );
			add_action( 'wp_ajax_hide_translate_notice', array( $this, 'ajax_hide_translation_notice' ) );
			add_action( 'wp_ajax_ic_add_catalog_shortcode', array( $this, 'add_catalog_shortcode' ) );
			add_action( 'wp', array( $this, 'remove_catalog_shortcode' ) );
		}
	}

	function admin_notices() {
		if ( current_user_can( 'activate_plugins' ) ) {
			if ( !is_advanced_mode_forced() || (ic_get_product_listing_status() && ic_get_product_listing_status() === 'private') ) {
				$template		 = get_option( 'template' );
//$integration_type	 = get_integration_type();
				$current_check	 = $this->theme_support_check();
				if ( !empty( $_GET[ 'hide_al_product_adder_support_check' ] ) ) {
					$current_check[ $template ] = $template;
					update_option( 'product_adder_theme_support_check', $current_check );
					return;
				}
				if ( empty( $current_check[ $template ] ) && current_user_can( 'manage_product_settings' ) ) {
					$this->theme_check_notice();
				}
			}
			if ( is_ic_catalog_admin_page() ) {
				$product_count = ic_products_count();
				if ( $product_count > 5 ) {
					if ( false === get_site_transient( 'implecode_hide_plugin_review_info' ) ) {
						$this->review_notice();
						set_site_transient( 'implecode_hide_plugin_translation_info', 1, WEEK_IN_SECONDS );
					} else if ( false === get_site_transient( 'implecode_hide_plugin_translation_info' ) && !is_english_catalog_active() ) {
						$this->translation_notice();
					}
				} else if ( false === get_site_transient( 'implecode_hide_plugin_review_info' ) ) {
					set_site_transient( 'implecode_hide_plugin_review_info', 1, WEEK_IN_SECONDS );
				}
			}
		}
	}

	function theme_check_notice() {
		if ( is_integration_mode_selected() && get_integration_type() == 'simple' ) {
			?>
			<div id="implecode_message" class="updated product-adder-message messages-connect">
				<div class="squeezer">
					<h4><?php echo sprintf( __( 'You are currently using %s in Simple Mode. It is perfectly fine to use it this way, however some features are limited.', 'ecommerce-product-catalog' ), IC_CATALOG_PLUGIN_NAME ); ?></h4>
					<h4><?php echo __( 'To switch to Advanced Mode please add the [show_product_catalog] shortcode to your product listing page.', 'ecommerce-product-catalog' ); ?></h4>
					<h4><?php echo sprintf( __( 'You can also use awesome %sCatalog Me! theme%s.', 'ecommerce-product-catalog' ), '<a href="' . admin_url( 'theme-install.php?search=Catalog%20me' ) . '">', '</a>' ) ?></h4>
					<p class="submit">
						<?php /* <a href="https://implecode.com/wordpress/product-catalog/theme-integration-guide/#cam=simple-mode&key=top-message" target="_blank" class="button-primary"><?php _e( 'Theme Integration Guide', 'ecommerce-product-catalog' ); ?></a> */ ?>
						<a class="button-primary add-catalog-shortcode"><?php _e( 'Add Shortcode Now', 'ecommerce-product-catalog' ); ?></a>
						<script>jQuery( ".add-catalog-shortcode" ).click( function ( event ) {
			                    event.preventDefault();
			                    var data = {
			                        'action': 'ic_add_catalog_shortcode'
			                    };
			                    jQuery( this ).prop( "disabled", true );
			                    jQuery.post( ajaxurl, data, function ( response ) {
			                        jQuery( '<a style="margin-left: 5px;" href="' + response + '" class="button-primary"><?php _e( 'See Your Product Listing', 'ecommerce-product-catalog' ); ?></a>' ).insertAfter( ".add-catalog-shortcode" );
			                        jQuery( ".add-catalog-shortcode" ).replaceWith( "<?php _e( 'The shortcode has been added successfully!', 'ecommerce-product-catalog' ) ?>" );
			                        jQuery( ".skip.button" ).remove();
			                    } );
			                } );
						</script>
						<a class="skip button" href="<?php echo admin_url( 'edit.php?post_type=al_product&page=product-settings.php&tab=product-settings&submenu=support' ) ?>"><?php _e( 'Plugin Support', 'ecommerce-product-catalog' ); ?></a>
						<a class="skip button" href="<?php echo esc_url( add_query_arg( 'hide_al_product_adder_support_check', 'true' ) ); ?>"><?php _e( 'I know, don\'t bug me', 'ecommerce-product-catalog' ); ?></a>
					</p>
				</div>
			</div><div class="clear"></div><?php
		} else if ( is_integration_mode_selected() && get_integration_type() == 'advanced' && !is_ic_shortcode_integration() ) {

			/* ?>
			  <div id="implecode_message" class="updated product-adder-message messages-connect">
			  <div class="squeezer">
			  <h4><?php _e( 'You are currently using eCommerce Product Catalog in Advanced Mode without the integration file. It is perfectly fine to use it this way, however the file may be very handy if you need more control over product pages. See the guide for quick integration file creation.', 'ecommerce-product-catalog' ); ?></h4>
			  <p class="submit"><a href="https://implecode.com/wordpress/product-catalog/theme-integration-guide/#cam=advanced-mode&key=top-message" target="_blank" class="button-primary"><?php _e( 'Theme Integration Guide', 'ecommerce-product-catalog' ); ?></a> <a class="skip button" href="<?php echo admin_url( 'edit.php?post_type=al_product&page=product-settings.php&tab=product-settings&submenu=support' ) ?>"><?php _e( 'Plugin Support', 'ecommerce-product-catalog' ); ?></a> <a class="skip button" href="<?php echo esc_url( add_query_arg( 'hide_al_product_adder_support_check', 'true' ) ); ?>"><?php _e( 'I know, don\'t bug me', 'ecommerce-product-catalog' ); ?></a></p>
			  </div>
			  </div>

			  <div id="implecode_message" class="updated product-adder-message messages-connect">
			  <div class="squeezer">
			  <h4><?php echo sprintf( __( 'Congratulations! Now your theme is fully integrated with %s.', 'ecommerce-product-catalog' ), IC_CATALOG_PLUGIN_NAME ); ?></h4>
			  <p class="submit"><a href="<?php echo admin_url( 'post-new.php?post_type=al_product' ) ?>" class="button-primary"><?php _e( 'Add Product', 'ecommerce-product-catalog' ); ?></a> <a class="skip button" href="<?php echo admin_url( 'edit.php?post_type=al_product&page=product-settings.php' ) ?>"><?php _e( 'Product Settings', 'ecommerce-product-catalog' ); ?></a> <a href="https://implecode.com/docs/ecommerce-product-catalog/#cam=advanced-mode&key=top-message-docs" class="button"><?php _e( 'Help & Documentation', 'ecommerce-product-catalog' ); ?></a>
			  </p>
			  </div>
			  </div>
			 * */

			$template					 = get_option( 'template' );
			$current_check				 = $this->theme_support_check();
			$current_check[ $template ]	 = $template;
			update_option( 'product_adder_theme_support_check', $current_check );
		} else {
			$product_id			 = sample_product_id();
			$sample_product_url	 = get_permalink( $product_id );
			if ( !$sample_product_url || get_post_status( $product_id ) != 'publish' ) {
				$sample_product_url = esc_url( add_query_arg( 'create_sample_product_page', 'true' ) );
			}
			?>
			<div id="implecode_message" class="updated product-adder-message messages-connect">
				<div class="squeezer">
					<h4><?php echo sprintf( __( 'Thank you for choosing %1$s! If you have any questions or issues feel free to %2$spost a support ticket%3$s.', 'ecommerce-product-catalog' ), IC_CATALOG_PLUGIN_NAME, '<a href="https://implecode.com/support/?cam=simple-mode&key=support-top">', '</a>' ) ?></h4>
					<?php sprintf( __( '%s requires initial configuration in order to work properly &#8211; please click the Initial Configuration button to proceed.', 'ecommerce-product-catalog' ), IC_CATALOG_PLUGIN_NAME ) ?>
					<?php sprintf( __( '<strong>Your theme does not declare %s support</strong> &#8211; please proceed to sample product page where automatic layout adjustment can be done.', 'ecommerce-product-catalog' ), IC_CATALOG_PLUGIN_NAME ) ?>
					<h4><?php echo __( 'You can create your product listing page automatically or insert [show_product_catalog] shortcode on any of your existing pages.', 'ecommerce-product-catalog' ) ?></h4>
					<h4><?php echo sprintf( __( 'If you are looking for a great customizable product theme we recommend free %sCatalog Me! theme%s.', 'ecommerce-product-catalog' ), '<a href="' . admin_url( 'theme-install.php?search=Catalog+me%21' ) . '">', '</a>' ) ?></h4>
					<p class="submit">
						<?php
						$listing_status	 = ic_get_product_listing_status();
						$label			 = '';
						if ( $listing_status === 'private' ) {
							?>
						<h4><?php echo __( 'Your main catalog listing page has been created automatically with private status (it is not visible for visitors).', 'ecommerce-product-catalog' ) ?></h4>
						<?php
						$label = __( 'Publish Main Catalog Listing', 'ecommerce-product-catalog' );
					}
					?>
					<a class="button-primary" href="https://implecode.com/docs/ecommerce-product-catalog/getting-started/#cam=default-mode&key=getting-started"><?php _e( 'Getting Started Guide', 'ecommerce-product-catalog' ) ?></a>
					<?php
					echo $this->create_listing_page_button( $label, true, 'button-secondary' );
					?>

					<?php // echo sample_product_button()   ?>
					<?php /* <a href="https://implecode.com/docs/ecommerce-product-catalog/theme-integration-wizard/#cam=default-mode&key=top-message-video" class="button"><?php _e( 'Configuration Video', 'ecommerce-product-catalog' ); ?></a> */ ?>
					<?php /* <a href="https://implecode.com/wordpress/product-catalog/theme-integration-guide/#cam=default-mode&key=top-message" target="_blank" class="button"><?php _e( 'Theme Integration Guide', 'ecommerce-product-catalog' ); ?></a> */ ?>
					<?php /* <a class="skip button" href="<?php echo admin_url( 'edit.php?post_type=al_product&page=product-settings.php&tab=product-settings&submenu=support' ) ?>"><?php _e( 'Plugin Support', 'ecommerce-product-catalog' ); ?></a> */ ?>
					<a class="skip button" href="<?php echo esc_url( add_query_arg( 'hide_al_product_adder_support_check', 'true' ) ); ?>"><?php _e( 'Hide Forever', 'ecommerce-product-catalog' ); ?></a>
					</p>
				</div>
			</div><?php
		}
	}

	static function create_listing_page_button( $label = null, $listing_button = false, $primary_class = 'button-primary' ) {
		ob_start();
		if ( $listing_button && $primary_class !== 'button-primary' ) {
			self::main_listing_button();
			$listing_button = false;
		}
		if ( empty( $label ) ) {
			$label = __( 'Create Main Catalog Listing', 'ecommerce-product-catalog' );
		}
		?>
		<a class="<?php echo $primary_class ?> add-catalog-shortcode"><?php echo $label ?></a>
		<?php
		if ( $listing_button ) {
			self::main_listing_button();
		}
		?>
		<script>jQuery( ".add-catalog-shortcode" ).click( function ( event ) {
		        event.preventDefault();
		        var data = {
		            'action': 'ic_add_catalog_shortcode'
		        };
		        jQuery( this ).prop( "disabled", true );
		        jQuery.post( ajaxurl, data, function ( response ) {
		            jQuery( '<a style="margin-left: 5px;" href="' + response + '" class="button-primary"><?php _e( 'See Your Product Listing', 'ecommerce-product-catalog' ); ?></a>' ).insertAfter( ".add-catalog-shortcode" );
		            jQuery( ".add-catalog-shortcode" ).replaceWith( "<?php _e( 'The listing has been added successfully!', 'ecommerce-product-catalog' ) ?>" );
		            jQuery( ".skip.button" ).remove();
		        } );
		    } );
		</script><?php
		return ob_get_clean();
	}

	static function main_listing_button() {
		$listing_url = product_listing_url();
		if ( !empty( $listing_url ) ) {
			if ( !is_integration_mode_selected() && !is_ic_shortcode_integration() ) {
				return;
			}
			?>
			<a href="<?php echo $listing_url ?>" class="button-secondary"><?php _e( 'See Main Catalog Listing', 'ecommerce-product-catalog' ); ?></a>
			<?php
		}
	}

	function catalog_links( $links ) {
		$links[ 'settings' ] = '<a href="' . get_admin_url( null, 'edit.php?post_type=al_product&page=product-settings.php' ) . '">' . __( 'Settings', 'ecommerce-product-catalog' ) . '</a>';
//$links[] = '<a href="https://implecode.com/wordpress/plugins/premium-support/#cam=catalog-settings-link&key=support-link" target="_blank">Premium Support</a>';
		return apply_filters( 'ic_epc_links', array_reverse( $links ) );
	}

	function review_notice() {
		$hidden = get_user_meta( get_current_user_id(), 'ic_review_hidden', true );
		if ( $hidden ) {
			return;
		}
		/* ?>
		  <div class="update-nag implecode-review"><strong><?php _e( 'Rate this Plugin!', 'ecommerce-product-catalog' ) ?></strong> <?php echo sprintf( __( 'Please <a target="_blank" href="%s">rate</a> %s and tell me if it works for you or not. It really helps development.', 'ecommerce-product-catalog' ), 'https://wordpress.org/support/view/plugin-reviews/ecommerce-product-catalog#postform', 'eCommerce Product Catalog' ) ?> <span class="dashicons dashicons-no"></span></div> */
		$text = apply_filters( 'ic_review_notice_text', sprintf( __( '%s is a free software. Would you mind taking <strong>5 seconds</strong> to <a target="_blank" href="%s">rate the plugin</a> for us please? Your comments <strong>help others know what to expect</strong> when they install %s.', 'ecommerce-product-catalog' ), IC_CATALOG_PLUGIN_NAME, 'https://wordpress.org/support/plugin/' . IC_CATALOG_PLUGIN_SLUG . '/reviews/#bbp-forum-0', IC_CATALOG_PLUGIN_NAME ) )
		?>
		<div class="notice notice-warning implecode-review is-dismissible">
			<p><?php echo $text . ' ' . __( 'A <strong>huge thank you</strong> from impleCode and WordPress community in advance!', 'ecommerce-product-catalog' ) ?></p>
			<p><a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/ecommerce-product-catalog#postform" class="button-primary"><?php _e( 'Rate Now & Hide Forever', 'ecommerce-product-catalog' ); ?></a>
				<?php /* <a href="" class="button"><?php _e( 'Hide Forever', 'ecommerce-product-catalog' ); ?></a> */ ?>
		</div>
		<div class="update-nag implecode-review-thanks" style="display: none"><?php echo sprintf( __( 'Thank you for <a target="_blank" href="%s">your rating</a>! We appreciate your time and input.', 'ecommerce-product-catalog' ), 'https://wordpress.org/support/view/plugin-reviews/ecommerce-product-catalog#postform' ) ?> <span class="dashicons dashicons-yes"></span></div><?php
	}

	function translation_notice() {
		?>
		<div class="update-nag implecode-translate"><?php echo sprintf( __( "<strong>Psst, it's less than 1 minute</strong> to add some translations to %s collaborative <a target='_blank' href='%s'>translation project</a>", 'ecommerce-product-catalog' ), IC_CATALOG_PLUGIN_NAME, 'https://translate.wordpress.org/projects/wp-plugins/ecommerce-product-catalog', IC_CATALOG_PLUGIN_NAME ) ?> <span class="dashicons dashicons-no"></span></div><?php
	}

	public static function review_notice_hide( $forever = false ) {
		if ( $forever ) {
//set_site_transient( 'implecode_hide_plugin_review_info', 1, 0 );
			update_user_meta( get_current_user_id(), 'ic_review_hidden', true );
		} else {
			$count	 = get_option( 'implecode_hide_plugin_review_info_count', 1 );
			$count	 = ($count < 6) ? $count : 0;
			set_site_transient( 'implecode_hide_plugin_review_info', 1, WEEK_IN_SECONDS * $count );
			$count	 += 1;
			update_option( 'implecode_hide_plugin_review_info_count', $count );
		}
	}

	function ajax_hide_review_notice() {
		$forever = isset( $_POST[ 'forever' ] ) ? true : false;
		$this->review_notice_hide( $forever );
		wp_die();
	}

	function translation_notice_hide() {
		set_site_transient( 'implecode_hide_plugin_translation_info', 1 );
	}

	function ajax_hide_translation_notice() {
		$this->translation_notice_hide();
		wp_die();
	}

	public static function theme_support_check() {
		$current_check = get_option( 'product_adder_theme_support_check', array() );
		if ( !is_array( $current_check ) ) {
			$old_current_check					 = $current_check;
			$current_check						 = array();
			$current_check[ $old_current_check ] = $old_current_check;
		}
		return $current_check;
	}

	function add_catalog_shortcode() {
		$page_id = get_product_listing_id();
		if ( empty( $page_id ) || $page_id == 'noid' ) {
			$page_id = create_products_page();
		}
		if ( !empty( $page_id ) ) {
			$post				 = get_post( $page_id );
			$post->post_status	 = 'publish';
			if ( !is_ic_shortcode_integration( $page_id ) ) {
				$post->post_content .= '[show_product_catalog]';
			}
			wp_update_post( $post );
		}
		permalink_options_update();
		create_sample_product();
		echo product_listing_url();
		wp_die();
	}

	function remove_catalog_shortcode() {
		if ( !empty( $_GET[ 'remove_shortcode_integration' ] ) && current_user_can( 'manage_product_settings' ) ) {
			$page_id = get_product_listing_id();
			if ( !empty( $page_id ) && is_ic_shortcode_integration( $page_id ) ) {
				$post				 = get_post( $page_id );
				$post->post_content	 = str_replace( '[show_product_catalog]', '', $post->post_content );
				wp_update_post( $post );
				permalink_options_update();
				wp_redirect( remove_query_arg( 'remove_shortcode_integration' ) );
			}
		}
	}

}

$ic_notices = new ic_catalog_notices( true );
