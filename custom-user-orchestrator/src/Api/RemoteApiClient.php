<?php
namespace Cuo\Api;

class RemoteApiClient {
    private $endpoint = 'https://jsonplaceholder.typicode.com/users';
    private $timeout = 15;

    public function fetch_users() {
        $response = wp_remote_get( $this->endpoint, [
            'timeout' => $this->timeout,
            'headers' => [
                'Accept' => 'application/json'
            ]
        ] );

        if ( is_wp_error( $response ) ) {
            return new \WP_Error( 'http_error', $response->get_error_message() );
        }

        $code = wp_remote_retrieve_response_code( $response );
        if ( 200 !== (int) $code ) {
            return new \WP_Error( 'http_status', "Unexpected status: {$code}" );
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( null === $data ) {
            return new \WP_Error( 'json_parse', 'Failed to parse JSON' );
        }

        return $data;
    }
}
