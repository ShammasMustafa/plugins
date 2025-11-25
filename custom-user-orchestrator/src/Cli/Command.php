<?php
namespace Cuo\Cli;

use Cuo\Data\DataManager;

class Command {
    private $dataManager;

    public function __construct( DataManager $dm ) {
        $this->dataManager = $dm;
    }

    /**
     * Clear cache and fetch fresh data
     *
     * ## EXAMPLES
     *
     *     wp custom-plugin refresh-data
     *
     * @when after_wp_load
     */
    public function refresh_data( $args, $assoc_args ) {
        \WP_CLI::log( 'Clearing cache...' );
        $this->dataManager->clear_cache();
        \WP_CLI::log( 'Fetching fresh data...' );
        $res = $this->dataManager->fetch_and_cache();

        if ( is_wp_error( $res ) ) {
            \WP_CLI::error( $res->get_error_message() );
        } else {
            \WP_CLI::success( 'Data refreshed. ' . count( $res ) . ' users cached.' );
        }
    }
}
