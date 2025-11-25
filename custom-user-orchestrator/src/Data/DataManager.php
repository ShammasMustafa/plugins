<?php
namespace Cuo\Data;

use Cuo\Api\RemoteApiClient;

class DataManager {
    const TRANSIENT_KEY = 'cuo_users_cache';
    const TRANSIENT_TTL = HOUR_IN_SECONDS; // 1 hour

    private $client;

    public function __construct( RemoteApiClient $client ) {
        $this->client = $client;
    }

    /**
     * Get cached users or fetch fresh if missing.
     * @return array|WP_Error
     */
    public function get_users() {
        $cached = get_transient( self::TRANSIENT_KEY );

        if ( false !== $cached && is_array( $cached ) ) {
            return $cached;
        }

        return $this->fetch_and_cache();
    }

    /**
     * Force fetch and update cache.
     * @return array|WP_Error
     */
    public function fetch_and_cache() {
        $result = $this->client->fetch_users();

        if ( is_wp_error( $result ) ) {
            // if we have old cache, return it instead of failing outright
            $old = get_transient( self::TRANSIENT_KEY );
            if ( false !== $old ) {
                return $old;
            }
            return $result;
        }

        // store the raw array
        set_transient( self::TRANSIENT_KEY, $result, self::TRANSIENT_TTL );
        return $result;
    }

    public function clear_cache() {
        delete_transient( self::TRANSIENT_KEY );
        
    }
}
