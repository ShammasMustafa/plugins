<?php
namespace Cuo;

use Cuo\Api\RemoteApiClient;
use Cuo\Data\DataManager;
use Cuo\Rest\Routes;
use Cuo\Admin\AdminPage;
use Cuo\Cli\Command;

class Plugin {
    private $file;
    private $base_dir;

    public function __construct( $file ) {
        $this->file = $file;
        $this->base_dir = dirname( $file );
    }

    public function run() {
        // init components
        $client = new RemoteApiClient();
        $dataManager = new DataManager( $client );
        new Routes( $dataManager );
        new AdminPage( $dataManager, $this->base_dir );
        // register CLI if WP_CLI exists
        if ( defined('WP_CLI') && WP_CLI ) {
            \WP_CLI::add_command( 'custom-plugin', new Command( $dataManager ) );
        }
        // enqueue block scripts (we assume build/block.js exists)
        $base_dir = $this->base_dir;
        $file = $this->file;

        add_action( 'init', function() use ( $base_dir, $file ) {

            // register script for block (if available)
            $asset_file = $this->base_dir . '/build/block.asset.php';
            if ( file_exists( $asset_file ) ) {
                $asset = require $asset_file;
                wp_register_script(
                    'cuo-user-table-block',
                    plugins_url( 'build/block.js', $this->file ),
                    $asset['dependencies'] ?? ['wp-blocks','wp-element','wp-editor','wp-components','wp-i18n'],
                    $asset['version'] ?? filemtime( $this->base_dir . '/build/block.js' )
                );
                register_block_type( 'cuo/user-table', [
                    'editor_script' => 'cuo-user-table-block'
                ] );
            }
        } );
    }
}