<?php

class IOD_Multisite_API_Get_Sites extends WP_REST_Controller
{
    /**
     * Get a collection of items.
     *
     * @param WP_REST_Request $request full data about the request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function callback($request)
    {
        $args = [
            'public' => 1,
            'archived' => 0,
            'mature' => 0,
            'spam' => 0,
            'deleted' => 0,
            'site__not_in' => [1],
        ];

        $args = apply_filters('multisite_api/get_sites/args', $args);
        $sites = apply_filters('multisite_api/get_sites', get_sites($args));

        return rest_ensure_response($sites);
    }
}
