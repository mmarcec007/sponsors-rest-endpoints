<?php


class SponsorsController extends WP_REST_Controller
{
    public function register_routes()
    {
        $version = '1';
        $namespace = 'sponsors-rest-endpoints/v' . $version;
        $base = 'sponsors';

        register_rest_route( $namespace, '/' . $base, array(
            array (
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_items' ),
                'args'                => array()
            )
        ));

        register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_item' ),
                'args'                => array(
                    'context' => array(
                        'default' => 'view',
                    ),
                ),
            )
        ) );
    }

    /**
     * Route URL https://example-wordpress.com/wp-json/sponsors-rest-endpoints/v1/sponsors
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function get_items($request)
    {
        return new WP_REST_Response( ["sponsors" => $this->getSponsorsOrSingleSponsor()], 200 );
    }

    /**
     * Route URL https://example-wordpress.com/wp-json/sponsors-rest-endpoints/v1/sponsors/1
     * @param WP_REST_Request $request
     * @return WP_Error|WP_REST_Response
     */
    public function get_item($request)
    {
        $params = $request->get_params();
        $id = $params['id'] ?? -1;
        $data = $this->getSponsorsOrSingleSponsor($id);

        if (!empty($data)) {
            return new WP_REST_Response( ["sponsor" => $data], 200 );
        }

        return new WP_Error( 'code', __( 'message', 'text-domain' ) );
    }

    private function getSponsorsOrSingleSponsor($id = null): array
    {
        $data = [];

        if ($id === null) {
            $args = array(
                'post_type' => 'sponsors',
                'post_status' => 'publish'
            );
            $the_query = new WP_Query( $args );

            if ( $the_query->have_posts() ) {
                foreach ( $the_query->posts as $post ) {
                    $postThumbnailId = intval(get_post_meta($post->ID, '_thumbnail_id', true));
                    $thumbnail = wp_get_attachment_url($postThumbnailId);

                    array_push($data, [
                        "id" => $post->ID,
                        "name" => $post->post_title,
                        "website" => get_post_meta($post->ID, '_website', true),
                        "email" => get_post_meta($post->ID, '_email', true),
                        "thumbnail" => $thumbnail
                    ]);
                }
            }
        } else {
            $post = get_post($id);

            $sponsorId = $post->ID;
            $name = $post->post_title;
            $website = get_post_meta($post->ID, '_website', true);
            $email = get_post_meta($post->ID, '_email', true);
            $postThumbnailId = intval(get_post_meta($post->ID, '_thumbnail_id', true));
            $thumbnail = wp_get_attachment_url($postThumbnailId);

            if ($post->post_status === 'publish' && $post->post_type === 'sponsors') {
                $data = [
                    "id" => $sponsorId,
                    "name" => $name,
                    "website" => $website,
                    "email" => $email,
                    "thumbnail" => $thumbnail
                ];
            }
        }

        /* Restore original Post Data */
        wp_reset_postdata();
        return $data;
    }
}