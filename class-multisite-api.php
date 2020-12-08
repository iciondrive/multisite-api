<?php
/**
 * Plugin Name: Ici On Drive - Multisite API
 * Description: Create endpoint to the WordPress REST API for multisite. Plugin developed for the "Ici On Drive" platform.
 * Plugin URI: https://github.com/iciondrive/multisite-api
 * Author: Thomas Navarro
 * Version: 1.0.0
 * Network: true
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI: http://github.com/thomasnavarro.
 */

namespace IciOnDrive;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Multisite_API')) {
    class Multisite_API
    {
        /**
         * @var string namespace
         */
        private const NAMESPACE = 'iciondrive/v1';

        /**
         * @var string rest_base
         */
        private $rest_base = 'sites';

        public function __construct()
        {
            // WP hooks
            add_action('init', [$this, 'init_hook']);

            $this->get_sites = new Get_Sites();
            $this->get_site = new Get_Site();
        }

        public function init_hook()
        {
            add_action('rest_api_init', [$this, 'register_rest_route']);
        }

        public function register_rest_route()
        {
            // Get all sites
            register_rest_route(
                self::NAMESPACE, '/sites',
                [
                    'methods' => \WP_REST_Server::READABLE,
                    'callback' => [$this->get_sites, 'callback'],
                    // 'permission_callback' => [$this, 'get_item_permissions_check'],
                    'args' => [
                        'page' => [
                            'description' => __('Current page of the result.'),
                            'type' => 'integer',
                            'default' => 1,
                        ],
                        'per_page' => [
                            'description' => __('Maximum number of items to be returned in result set.'),
                            'type' => 'integer',
                            'default' => 10,
                        ],
                        'fields' => [
                            'default' => true,
                            'type' => 'boolean',
                            'description' => __('Retrieves the ACF fields from the site.'),
                        ],
                        'gps' => [
                            'default' => false,
                            'type' => 'boolean',
                            'description' => __('Retrieves only the GPS coordinates of the site.'),
                        ],
                    ],
                    // 'schema' => [$this, 'get_public_item_schema'],
                ]
            );

            // Get site by blog ID
            register_rest_route(
                self::NAMESPACE, '/'.$this->rest_base.'/(?P<id>[\d]+)',
                [
                    'args' => [
                        'id' => [
                            'description' => __('Unique identifier for the object.'),
                            'type' => 'integer',
                        ],
                    ],
                    [
                        'methods' => \WP_REST_Server::READABLE,
                        'callback' => [$this->get_site, 'callback'],
                        // 'permission_callback' => [$this, 'get_item_permissions_check'],
                        'args' => [
                            'fields' => [
                                'default' => true,
                                'type' => 'boolean',
                                'description' => __('Retrieves the ACF fields from the site.'),
                            ],
                            'gps' => [
                                'default' => false,
                                'type' => 'boolean',
                                'description' => __('Retrieves only the GPS coordinates of the site.'),
                            ],
                        ],
                    ],
                    'schema' => [$this, 'get_public_item_schema'],
                ]
            );
        }

        private function get_item_permissions_check()
        {
            global $wp_version;

            if (version_compare($wp_version, '4.8', '>=')) {
                return current_user_can('setup_network');
            }

            return is_super_admin();
        }
    }

    // Includes
    require_once dirname(__FILE__).'/class-multisite-get-sites.php';
    require_once dirname(__FILE__).'/class-multisite-get-site.php';

    // Instantiate
    new Multisite_API();
}
