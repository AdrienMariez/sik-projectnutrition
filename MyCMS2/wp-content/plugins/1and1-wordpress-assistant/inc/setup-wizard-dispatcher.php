<?php
/** Do not allow direct access! */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

/**
 * Class One_And_One_Setup_Wizard_Dispatcher
 * Computes and shows to the corresponding view of the Assistant in the WP Admin
 */
class One_And_One_Setup_Wizard_Dispatcher {

	/**
	 * Start and configure the Wizard
	 */
	public static function admin_init() {

		/** Create and configure the wizard page in the admin area */
		add_action( 'admin_menu', array( 'One_And_One_Setup_Wizard_Dispatcher', 'add_admin_menu_wizard_page' ), 5 );
		add_action( 'admin_bar_menu', array ( 'One_And_One_Setup_Wizard_Dispatcher', 'add_admin_top_bar_wizard_menu' ), 70 );

		/** Configure wizard-related actions in the admin */
		add_action( 'admin_init', array( 'One_And_One_Setup_Wizard_Dispatcher', 'handle_wizard_params' ) );
	}

	/**
	 * Handle redirection to the 1&1 Wizard after login
	 *
	 * @param  string $redirect_to
	 * @return string
	 */
	public static function redirect_after_login( $redirect_to ) {

		if ( get_option( 'oneandone_assistant_completed' ) == false ) {
			return admin_url( 'admin.php?page=1and1-wordpress-wizard&setup_action=greeting' );
		} else {
			return $redirect_to;
		}
	}

	/**
	 * Create and configure the wizard page in the admin area
	 * WP Hook https://codex.wordpress.org/Plugin_API/Action_Reference/admin_menu
	 */
	public static function add_admin_menu_wizard_page() {
		global $menu;

		$pos   = 50;
		$posp1 = $pos + 1;

		while ( isset( $menu[ $pos ] ) || isset( $menu[ $posp1 ] ) ) {
			$pos ++;
			$posp1 = $pos + 1;

			/** check that there is no menu at our level neither at ourlevel+1 because that will make us disappear in some case */
			if ( ! isset( $menu[ $pos ] ) && isset( $menu[ $posp1 ] ) ) {
				$pos = $pos + 2;
			}
		}

		add_menu_page(
			__( '1&1 WP Assistant', '1and1-wordpress-wizard' ),
			__( '1&1 WP Assistant', '1and1-wordpress-wizard' ),
			'manage_options',
			'1and1-wordpress-wizard',
			array( 'One_And_One_Setup_Wizard_Dispatcher', 'dispatch_wizard_actions' ),
			'none',
			$pos
		);

	}

	/**
	 * Add an extra menu item in the top admin bar
	 * https://codex.wordpress.org/Class_Reference/WP_Admin_Bar/add_menu
	 */
	public static function add_admin_top_bar_wizard_menu() {
		global $wp_admin_bar;

		if ( get_current_screen()->id == get_plugin_page_hookname( '1and1-wordpress-wizard', '' ) ) {
			$class = 'current';
		} else {
			$class = '';
		}

		$title_element = sprintf(
			"<span class='ab-icon'></span>" .
			"<span class='ab-label'>%s</span>",
			__( '1&1 WP Assistant', '1and1-wordpress-wizard' )
		);

		$wp_admin_bar->add_menu(
			array(
				'parent' => null,
				'id'     => '1and1-wordpress-wizard',
				'title'  => $title_element,
				'href'   => admin_url(
					add_query_arg( 'page', '1and1-wordpress-wizard', 'admin.php' )
				),
				'meta'   => array(
					'class' => $class
				)
			)
		);
	}

	/**
	 * Handle status change of the wizard anywhere in the admin area (via GET parameters)
	 * WP Hook https://codex.wordpress.org/Plugin_API/Action_Reference/admin_init
	 */
	public static function handle_wizard_params() {

		/** reset the wizard (restart from the beginning) */
		if ( isset( $_GET['1and1-wordpress-wizard-reset'] ) ) {
			delete_option( 'oneandone_assistant_completed' );
			delete_option( 'oneandone_assistant_sitetype' );
		}

		/** skip the wizard completely (the user won't be bother by it anymore) */
		if ( isset( $_GET['1and1-wordpress-wizard-cancel'] ) ) {
			update_option( 'oneandone_assistant_completed', true );
		}
	}

	/**
	 * Get current action and load corresponding view
	 * If something is missing show the start of the wizard
	 */
	public static function dispatch_wizard_actions() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Sorry, you do not have permission to access the 1&1 WP Assistant.', '1and1-wordpress-wizard' ) );
		}

		/** Add Wizard CSS stylesheet */
		wp_enqueue_style( '1and1-wp-wizard' );

		/** Load parameter for the current requested action */
		$action = isset( $_GET['setup_action'] ) ? $_GET['setup_action'] : 'choose_site';

		/** Dispatch action */
		switch ( $action ) {

			/** First step after login */
			default:
			case 'greeting':
				self::_greeting();
				break;

			/** Choose use case */
			case 'choose_site':
				self::_choose_site_type();
				break;

			/** Choose and install a theme */
			case 'choose_appearance':
				self::_choose_appearance();
				break;

			/** Choose / confirm & install plugins */
			case 'choose_functionality':
				self::_choose_functionality();
				break;

			/** Installation and activation processes */
			case 'activate':
				self::_activate();
				break;
		}
	}

	/**
	 * First step after login
	 * (otherwise the Assistant opens directly the "Choose type" page)
	 */
	private static function _greeting() {

		include( One_And_One_Wizard::get_views_dir_path() . 'setup-wizard-greeting-step.php' );
	}

	/**
	 * Choose use case
	 */
	private static function _choose_site_type() {

		include( One_And_One_Wizard::get_views_dir_path() . 'setup-wizard-site-select-step.php' );
		One_And_One_Site_Selection_Step::render();
	}

	/**
	 * Choose and install a theme
	 */
	private static function _choose_appearance() {

		$sitetype_transient = 'oneandone_assistant_process_sitetype_user_' . get_current_user_id();

		if ( isset( $_POST['sitetype'] )
		     || get_option( 'oneandone_assistant_sitetype' )
		     || get_transient( $sitetype_transient )
		) {

			if ( isset( $_POST['sitetype'] ) ) {
				$site_type = sanitize_text_field( key( $_POST['sitetype'] ) );
				set_transient( $sitetype_transient, $site_type, 1200 );

			} else if ( get_transient( $sitetype_transient ) ) {
				$site_type = get_transient( $sitetype_transient );

			} else {
				$site_type = get_option( 'oneandone_assistant_sitetype', '' );
			}

			include_once( One_And_One_Wizard::get_inc_dir_path().'theme-manager.php' );
			$theme_manager = new One_And_One_Theme_Manager();
			$theme_manager->get_theme_manager( $site_type );
		}
	}

	/**
	 * Choose / confirm & install plugins
	 */
	private static function _choose_functionality() {

		$sitetype_transient = 'oneandone_assistant_process_sitetype_user_' . get_current_user_id();

		if ( isset( $_GET['site_type'] )
		     || get_option( 'oneandone_assistant_sitetype' )
		     || get_transient( $sitetype_transient )
		) {

			$sitetype_transient = 'oneandone_assistant_process_sitetype_user_' . get_current_user_id();
			$theme_transient = 'oneandone_assistant_process_theme_user_' . get_current_user_id();

			if ( isset( $_GET['site_type'] ) ) {
				$site_type = sanitize_text_field( $_GET['site_type'] );
				set_transient( $sitetype_transient, $site_type, 1200 );

			} else if ( get_transient( $sitetype_transient ) ) {
				$site_type = get_transient( $sitetype_transient );

			} else if ( get_option( 'oneandone_assistant_sitetype' ) ) {
				$site_type = get_option( 'oneandone_assistant_sitetype' );
			}

			if ( isset( $_POST['theme'] ) ) {
				/** Added bc of xss protection */
				$theme_id = sanitize_text_field( key( $_POST['theme'] ) );
				set_transient( $theme_transient, $theme_id, 1200 );

			} elseif ( get_transient( $theme_transient ) ) {
				$theme_id = get_transient( $theme_transient );
			} else {
				$theme_id = '';
			}

			include_once( One_And_One_Wizard::get_inc_dir_path().'plugin-manager.php' );
			$plugin_manager = new One_And_One_Plugin_Manager();
			$plugin_manager->get_plugin_manager( $site_type, $theme_id );
		}
	}

	/**
	 * Installation and activation processes
	 */
	private static function _activate() {

		$sitetype_transient = 'oneandone_assistant_process_sitetype_user_' . get_current_user_id();
		$theme_transient = 'oneandone_assistant_process_theme_user_' . get_current_user_id();

		if ( isset( $_POST['site_type'] ) && isset( $_POST['theme'] ) ) {

			/** Increase PHP limits to avoid timeouts and memory limits */
			@ini_set( 'error_reporting', 0 );
			@ini_set( 'memory_limit', '256M' );
			@set_time_limit( 300 );

			include_once( One_And_One_Wizard::get_inc_dir_path().'plugin-manager.php' );
			include_once( One_And_One_Wizard::get_inc_dir_path().'plugin-adapter.php' );
			$plugin_manager = new One_And_One_Plugin_Manager();
			$plugin_adapter = new One_And_One_Plugin_Adapter();

			$site_type = sanitize_text_field( $_POST['site_type'] );
			$theme_id  = sanitize_text_field( $_POST['theme'] );
			$messages  = array();

			/** Check nonce */
			check_admin_referer( 'activate' );

			/** Process Theme */
			if ( ! empty( $theme_id ) ) {
				$installed_themes = wp_get_themes();
				$themes_meta      = $plugin_manager->get_theme_meta( $site_type );

				if ( ! array_key_exists( $theme_id, $installed_themes ) ) {
					include_once( One_And_One_Wizard::get_inc_dir_path().'installer.php' );
					One_And_One_Installer::install_theme( $themes_meta[$theme_id] );
				}

				switch_theme( $theme_id );
				update_option( 'oneandone_assistant_theme', $theme_id );

				$theme_name = One_And_One_Sitetype_Filter::get_active_theme_name();

				if ( ! empty( $themes_meta[$theme_id]['name'] ) ) {
					$theme_name = ucwords( $themes_meta[$theme_id]['name'] );
				}

				$messages[] = sprintf( __( 'Theme activated: <strong>%s</strong>', '1and1-wordpress-wizard' ), $theme_name );
			} else {
				$messages[] = __( 'There was no theme selected, so the current theme is still active.', '1and1-wordpress-wizard' );
			}

			/** Process Plugins */
			$plugins_to_activate = array();

			if ( isset( $_POST['plugins'] ) ) {
				if ( is_array( $_POST['plugins'] ) ) {
					foreach ( $_POST['plugins'] as $item ) {
						$plugins_to_activate[] = sanitize_text_field( $item );
					}
				}
			}

			/** Get all installed plugins */
			$installed_plugins = $plugin_manager->get_installed_plugin_slugs();
			$plugins_meta      = $plugin_manager->get_plugin_meta( $site_type );

			/** Download and install missing plugins first */
			foreach ( $plugins_to_activate as $plugin_to_activate ) {
				if ( ! in_array( $plugin_to_activate, $installed_plugins ) ) {
					if ( ! empty( $plugins_meta[$plugin_to_activate] ) ) {
						include_once( One_And_One_Wizard::get_inc_dir_path().'installer.php' );
						if ( $plugin_installed = One_And_One_Installer::install_plugin( $plugins_meta[$plugin_to_activate] ) ) {
							$installed_plugins = array_merge( $installed_plugins, $plugin_installed );
						}
					}
				}
			}

			foreach ( $installed_plugins as $plugin_path => $plugin_slug ) {
				try {
					$is_active_plugin = $plugin_manager->is_active_plugin( $plugin_slug );

					if ( $is_active_plugin && ! in_array( $plugin_slug, $plugins_to_activate ) ) {
						deactivate_plugins( $plugin_path );
						$messages[] = sprintf( __( 'Plugin deactivated: <strong>%s</strong>', '1and1-wordpress-wizard' ), ucwords( $plugins_meta[$plugin_slug]->name ) );

					} else if ( ! $is_active_plugin && in_array( $plugin_slug, $plugins_to_activate ) ) {
						//fix for woocommerce stuff
						if (in_array('woocommerce-germanized', $plugins_to_activate)
							&& in_array('woocommerce', $plugins_to_activate) &&
							$plugin_slug == 'woocommerce-germanized') {
							WC()->init();
						}

						if (in_array('woocommerce', $plugins_to_activate)) {
							if (!function_exists('wc_get_screen_ids')) {
								function wc_get_screen_ids() {
									return array();
								}
							}
						}

						$result = activate_plugin( plugin_basename( $plugin_path ) );

						if ( is_wp_error( $result ) ) {
							if ( ! empty( $result->errors['plugin_not_found'][0] ) ) {
								error_log( $result->errors['plugin_not_found'][0] );
							}
						}
						$messages[] = sprintf( __( 'Plugin activated: <strong>%s</strong>', '1and1-wordpress-wizard' ), ucwords( $plugins_meta[$plugin_slug]->name ) );
                        $is_active_plugin = true;
					}

					if ( $is_active_plugin ) {
						if ( method_exists( $plugin_adapter, 'adapt_'.$plugin_slug ) ) {
							call_user_func( array( $plugin_adapter, 'adapt_'.$plugin_slug ) );
						}
					}
				}
				catch ( Exception $e ) {
					error_log( $e->getMessage() );
				}
			}

			/** Render the final step of the wizard */
			include( One_And_One_Wizard::get_views_dir_path() . 'setup-wizard-final-step.php' );
			One_And_One_Final_Step::render( $messages );

			/** store assistant is completed */
			update_option( 'oneandone_assistant_completed', true );

			/** store website type in db */
			update_option( 'oneandone_assistant_sitetype', $site_type );

			$pluginsImploded = implode( ',', $plugins_to_activate );
			/** store plugins */
			update_option( 'oneandone_assistant_plugins', $pluginsImploded );

			delete_transient( $sitetype_transient );
			delete_transient( $theme_transient );

			/** Log the installation process */
			self::log( array( 'website_type' => $site_type, 'method' => 'finished', 'theme_selected' => $theme_id, 'plugins_selected' => $pluginsImploded ) );
		}
	}

	/**
	 * Log errors
	 *
	 * @param  array $args
	 * @return bool
	 */
	private static function log( $args ) {
		if ( ! oneandone_is_logging_enabled() ) {
			return false;
		}

		include_once( One_And_One_Wizard::get_inc_dir_path().'stats-logger.php' );
		One_And_One_StatsLogger::logRemote( $args );

		return true;
	}

}
