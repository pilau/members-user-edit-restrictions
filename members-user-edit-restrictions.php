<?php

/**
 * Members User Edit Restrictions
 *
 * @package   Pilau_Members_User_Edit_Restrictions
 * @author    Steve Taylor
 * @license   GPL-2.0+
 * @copyright 2015 Steve Taylor
 *
 * @wordpress-plugin
 * Plugin Name:			Members User Edit Restrictions
 * Description:			Essential restrictions for users editing users when custom roles created with the Members plugin are present.
 * Version:				0.1
 * Author:					Steve Taylor
 * License:				GPL-2.0+
 * License URI:			http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI:	https://github.com/pilau/members-user-edit-restrictions
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


if ( ! class_exists( 'Pilau_MembersUserEditRestrictions' ) ) {

	class Pilau_MembersUserEditRestrictions {

		/**
		 * @var    $instance
		 * @since  0.1
		 */
		public static $instance;

		/**
		 * @var    $role_hierarchy
		 * @since  0.1
		 */
		private static $role_hierarchy;

		/**
		 * @var    $current_user_role
		 * @since  0.1
		 */
		private static $current_user_role;

		/**
		 * Init
		 *
		 * @since	0.1
		 */
		public static function init() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new Pilau_MembersUserEditRestrictions();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since	0.1
		 */
		private function __construct() {

			add_action( 'admin_init', array( &$this, 'init_vars' ) );
			add_action( 'admin_init', array( &$this, 'add_hooks' ) );

		}

		/**
		 * Initialise variables
		 *
		 * @since	0.1
		 */
		public function init_vars() {

			// Role hierarchy
			self::$role_hierarchy = apply_filters( 'pilau_muer_role_hierarchy', array(
				'administrator',
				'editor',
				'author',
				'contributor',
				'subscriber'
			));

			// Current user role
			self::$current_user_role = self::get_user_role();

		}

		/**
		 * Add hooks
		 *
		 * @since	0.1
		 */
		public function add_hooks() {

			add_filter( 'user_has_cap', array( &$this, 'user_has_cap' ), 10, 3 );

		}

		/**
		 * Does the first role have priority over the second?
		 *
		 * @since	0.1
		 * @param	string	$role1
		 * @param	string	$role2
		 * @return	float				1 = has priority; 0 = equal roles; -1 doesn't have priority
		 */
		public function role_has_priority( $role1, $role2 ) {
			$role_has_priority = -1; // cautious by default

			if ( $role1 == $role2 ) {

				// Same roles
				$role_has_priority = 0;

			} else {

				// Check positions in hierarchy
				$role1_pos = array_search( $role1, self::$role_hierarchy );
				$role2_pos = array_search( $role2, self::$role_hierarchy );

				// Only give priority if both are present in hierarchy, and first is before second
				if ( $role1_pos && $role2_pos && $role1_pos < $role2_pos ) {
					$role_has_priority = 1;
				}

			}

			return $role_has_priority;
		}

		/**
		 * Get a user's role
		 *
		 * @since	0.1
		 * @param	int		$user_id
		 * @return	string
		 */
		public function get_user_role( $user_id = null ) {
			$role = null;

			// Default to current user
			if ( is_null( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			// Basic role retrieval
			$userdata = get_userdata( $user_id );
			if ( isset( $userdata->roles[0] ) ) {
				$role = $userdata->roles[0];
			}

			return $role;
		}

		/**
		 * Hooked to user_has_cap
		 *
		 * @since	0.1
		 * @param	array	$allcaps	All the capabilities of the user
		 * @param	array	$cap		[0] Requested capability
		 * @param	array	$args		[0] Requested capability
		 *								[1] User ID
		 *								[2] Associated object ID
		 * @return	array
		 */
		public function user_has_cap( $allcaps, $cap, $args ) {

			if ( isset( $cap[0] ) ) {

				switch ( $cap[0] ) {

					case 'delete_user': {
						if ( ! self::role_has_priority( self::get_user_role( $args[1] ), self::get_user_role( $args[2] ) ) ) {
							unset( $allcaps['delete_user'] );
						}
						break;
					}

				}

			}

			return $allcaps;
		}

	} // End Class

} // End if class exists statement


// Instantiate the class
if ( class_exists( 'Pilau_MembersUserEditRestrictions' ) ) {
	Pilau_MembersUserEditRestrictions::init();
}
