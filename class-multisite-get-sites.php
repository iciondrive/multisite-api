<?php

namespace IciOnDrive;

class Get_Sites extends \WP_REST_Controller
{
    /**
     * Get a collection of items.
     *
     * @param \WP_REST_Request $request full data about the request
     *
     * @return \WP_Error|\WP_REST_Response
     */
    public function callback($request)
    {
        $args = [
            'public' => 1,
            'archived' => 0,
            'mature' => 0,
            'spam' => 0,
            'deleted' => 0,
            'site__not_in' => [1, 91],
            'orderby' => 'path',
            'order' => $request['order'],
            'number' => $request['per_page'],
        ];

        $args = apply_filters('multisite_api/get_sites/args', $args);
        $sites = apply_filters('multisite_api/get_sites', get_sites($args));

        foreach ($sites as $key => $site) {
            switch_to_blog($site->blog_id);
            // Get ACF Fields
            if ($request['fields']) {
                $site = $this->get_fields($site);
            }
            restore_current_blog();
        }

        return rest_ensure_response($sites);
    }

    protected function get_fields($site)
    {
        $home_id = get_option('page_on_front');
        $fields = get_fields($home_id);
        $site->fields = $fields;

        return apply_filters('multisite_api/get_sites/fields', $site);
    }
}
