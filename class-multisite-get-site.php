<?php

namespace IciOnDrive;

class Get_Site extends \WP_REST_Controller
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
        $site = apply_filters('multisite_api/get_site', $this->get_site($request['id']));

        if (is_wp_error($site)) {
            return $site;
        }

        // Get ACF Fields
        switch_to_blog($request['id']);
        if ($request['fields']) {
            $site = $this->get_fields($site);
        }
        restore_current_blog();

        return $site;
    }

    protected function get_site($id)
    {
        $error = new \WP_Error(
            'rest_site_invalid_id',
            __('Invalid site ID.'),
            ['status' => 404]
        );

        if ((int) $id <= 0) {
            return $error;
        }

        $site = get_site((int) $id);

        if (empty($site) || empty($site->blog_id)) {
            return $error;
        }

        return $site;
    }

    protected function get_fields($site)
    {
        $home_id = get_option('page_on_front');
        $fields = get_fields($home_id);
        $site->fields = $fields;

        return apply_filters('multisite_api/get_site/fields', $site);
    }
}
