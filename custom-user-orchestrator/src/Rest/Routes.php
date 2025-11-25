<?php
namespace Cuo\Rest;

use Cuo\Data\DataManager;

class Routes {
    private $dataManager;

    public function __construct( DataManager $dataManager ) {
        $this->dataManager = $dataManager;
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        register_rest_route( 'custom-plugin/v1', '/data', [
            'methods' => 'GET',
            'callback' => [ $this, 'get_data' ],
            'permission_callback' => '__return_true', // public, but can be tightened if needed
        ] );
    }

    public function get_data( $request ) {
        $data = $this->dataManager->get_users();

        if ( is_wp_error( $data ) ) {
            return new \WP_REST_Response( [
                'error' => $data->get_error_message()
            ], 500 );
        }

        return rest_ensure_response( $data );
    }
}
