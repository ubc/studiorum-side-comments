<?php
	/*
	 * Plugin Name: Studiorum Side Comments
	 * Description: Provides additional options for the WP Side Comments Plugin
	 * Version:     0.1
	 * Plugin URI:  #
	 * Author:      UBC, CTLT, Richard Tape
	 * Author URI:  http://ubc.ca/
	 * Text Domain: studiorum-side-comments
	 * License:     GPL v2 or later
	 * Domain Path: languages
	 *
	 * studiorum-side-comments is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation, either version 2 of the License, or
	 * any later version.
	 *
	 * studiorum-side-comments is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with studiorum-side-comments. If not, see <http://www.gnu.org/licenses/>.
	 *
	 * @package Grade Book
	 * @category Core
	 * @author Richard Tape
	 * @version 0.1.0
	 */

	if( !defined( 'ABSPATH' ) ){
		die( '-1' );
	}

	if( !defined( 'STUDIORUM_SIDE_COMMENTS_DIR' ) ){
		define( 'STUDIORUM_SIDE_COMMENTS_DIR', plugin_dir_path( __FILE__ ) );
	}

	if( !defined( 'STUDIORUM_SIDE_COMMENTS_URL' ) ){
		define( 'STUDIORUM_SIDE_COMMENTS_URL', plugin_dir_url( __FILE__ ) );
	}

	class Studiorum_Side_Comments
	{

		/**
		 * Actions and filters
		 *
		 * @since 0.1
		 *
		 * @param null
		 * @return null
		 */

		public function __construct()
		{

			// add options to studiorum options panel
			add_action( 'studiorum_settings_setup_start', array( $this, 'studiorum_settings_setup_start__addFilters' ) );

			// Determine if we're loading our custom css
			add_action( 'after_setup_theme', array( $this, 'after_setup_theme__determineIfLoadingCustomCSS' ) );

		}/* __construct() */


		/**
		 * Add our filters to add our options to the main studiorum settings panel
		 *
		 * @since 0.1
		 *
		 * @param null
		 * @return null
		 */

		public function studiorum_settings_setup_start__addFilters()
		{

			// Add the tab
			add_filter( 'studiorum_settings_in_page_tabs', array( $this, 'studiorum_settings_in_page_tabs__addSideCommentsSettingsTab' ) );

			// Add the settings section
			add_filter( 'studiorum_settings_settings_sections', array( $this, 'studiorum_settings_settings_sections__addSideCommentsSettingsSection' ) );

			// Add the fields to the new section
			add_filter( 'studiorum_settings_settings_fields', array( $this, 'studiorum_settings_settings_fields__addSideCommentsSettingsFields' ) );

		}/* studiorum_settings_setup_start__addFilters() */


		/**
		 * Add the lectio tab to the Studiorum settings panel
		 *
		 * @since 0.1
		 *
		 * @param array $studiorumSettingsTabs Existing settings tabs
		 * @return array $studiorumSettingsTabs Modified settings tabs
		 */

		public function studiorum_settings_in_page_tabs__addSideCommentsSettingsTab( $studiorumSettingsTabs )
		{

			if( !$studiorumSettingsTabs || !is_array( $studiorumSettingsTabs ) ){
				$studiorumSettingsTabs = array();
			}

			$studiorumSettingsTabs[] = array(
				'tab_slug'	=>	'side_comments',
				'title'		=>	__( 'Side Comments', 'studiorum-side-comments' )
			);

			return $studiorumSettingsTabs;

		}/* studiorum_settings_in_page_tabs__addSideCommentsSettingsTab */

		/**
		 * description
		 *
		 *
		 * @since 0.1
		 *
		 * @param string $param description
		 * @return string|int returnDescription
		 */

		public function studiorum_settings_settings_sections__addSideCommentsSettingsSection( $settingsSections )
		{

			if( !$settingsSections || !is_array( $settingsSections ) ){
				$settingsSections = array();
			}

			$settingsSections[] = array(
				'section_id'	=>	'side_comments_options',
				'tab_slug'		=>	'side_comments',
				'order'			=> 	3,
				'title'			=>	__( 'Side Comments Settings', 'studiorum-side-comments' ),
			);

			return $settingsSections;

		}/* studiorum_settings_settings_sections__addSideCommentsSettingsSection() */


		/**
		 * description
		 *
		 *
		 * @since 0.1
		 *
		 * @param string $param description
		 * @return string|int returnDescription
		 */

		public function studiorum_settings_settings_fields__addSideCommentsSettingsFields( $settingsFields )
		{

			if( !$settingsFields || !is_array( $settingsFields ) ){
				$settingsFields = array();
			}

			$settingsFields[] = array(	// Single Drop-down List
				'field_id'	=>	'studiorum_side_comments_hide_standard_comments',
				'section_id'	=>	'side_comments_options',
				'title'	=>	__( 'Hide standard comments?', 'studiorum-side-comments' ) . '<span class="label-note">' . __( 'At the bottom of most posts and pages on WordPress sites there are the normal, linear comments. Would you like to hide these by default and just show the inline comments for studiorum submissions?', 'studiorum-side-comments' ) . '</span>',
				'type'	=>	'select',
				'default'	=>	'true',	// the index key of the label array below which yields 'Yellow'.
				'label'	=>	array( 
					'true'	=>	'True',		
					'false'	=>	'False'
				),
				'attributes'	=>	array(
					'select'	=>	array(
						'style'	=>	"width: 285px;",
					),
				)
			);

			return $settingsFields;

		}/* studiorum_settings_settings_fields__addSideCommentsSettingsFields() */


		/**
		 * Determine if we are loading the custom CSS by checking the options in the back-end
		 *
		 * @since 0.1
		 *
		 * @param null
		 * @return null
		 */

		public function after_setup_theme__determineIfLoadingCustomCSS()
		{

			$hideCommentsOption = get_studiorum_option( 'side_comments_options', 'studiorum_side_comments_hide_standard_comments' );

			if( !$hideCommentsOption || $hideCommentsOption != 'true' ){
				return;
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts__loadSideCommentsCSS' ) );

		}/* after_setup_theme__determineIfLoadingCustomCSS() */


		/**
		 * Load the side comments CSS (as determined by the option)
		 *
		 * @since 0.1
		 *
		 * @param null
		 * @return null
		 */

		public function wp_enqueue_scripts__loadSideCommentsCSS()
		{

			wp_enqueue_style( 'studiorum-side-comments', trailingslashit( STUDIORUM_SIDE_COMMENTS_URL ) . 'assets/css/studiorum-side-comments.css' );

		}/* wp_enqueue_scripts__loadSideCommentsCSS() */


	}/* class Studiorum_Side_Comments */


	/**
	 *
	 * Instantiate the gradebook
	 *
	 * @since 0.1.0
	 * @return null
	 */

	function Studiorum_Side_Comments()
	{

		$Studiorum_Side_Comments = new Studiorum_Side_Comments;

	}/* Studiorum_Side_Comments() */

	// Get Studiorum_Side_Comments Running
	add_action( 'plugins_loaded', 'Studiorum_Side_Comments', 5 );