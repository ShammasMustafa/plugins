<?php
namespace Cuo\Admin;

use Cuo\Data\DataManager;

class AdminPage {
    private $dataManager;
    private $base_dir;

    public function __construct( DataManager $dm, $base_dir ) {
        $this->dataManager = $dm;
        $this->base_dir = $base_dir;
        add_action( 'admin_menu', [ $this, 'add_menu' ] );
        add_action( 'admin_post_cuo_refresh', [ $this, 'handle_refresh' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
    }

    public function add_menu() {
        add_menu_page(
            __( 'User Table', 'custom-user-orchestrator' ),
            __( 'User Table', 'custom-user-orchestrator' ),
            'manage_options',
            'cuo-user-table',
            [ $this, 'render_page' ],
            'dashicons-database'
        );
    }

    public function enqueue( $hook ) {
        if ( 'toplevel_page_cuo-user-table' !== $hook ) {
            return;
        }
        wp_enqueue_style( 'cuo-admin', plugins_url( '../assets/admin.css', __FILE__ ) );
    }

    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Unauthorized', 'custom-user-orchestrator' ) );
        }

        $users = $this->dataManager->get_users();

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'User Table', 'custom-user-orchestrator' ); ?></h1>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( 'cuo_refresh_action', 'cuo_refresh_nonce' ); ?>
                <input type="hidden" name="action" value="cuo_refresh">
                <button class="button button-primary" type="submit"><?php esc_html_e( 'Refresh Data', 'custom-user-orchestrator' ); ?></button>
            </form>

            <hr/>

            <?php
            if ( is_wp_error( $users ) ) {
                echo '<div class="notice notice-error"><p>' . esc_html( $users->get_error_message() ) . '</p></div>';
            } elseif ( empty( $users ) ) {
                echo '<p>' . esc_html__( 'No users found.', 'custom-user-orchestrator' ) . '</p>';
            } else {
                echo '<table class="widefat fixed striped">';
                echo '<thead><tr>';
                echo '<th>' . esc_html__( 'ID', 'custom-user-orchestrator' ) . '</th>';
                echo '<th>' . esc_html__( 'Name', 'custom-user-orchestrator' ) . '</th>';
                echo '<th>' . esc_html__( 'Email', 'custom-user-orchestrator' ) . '</th>';
                echo '<th>' . esc_html__( 'Phone', 'custom-user-orchestrator' ) . '</th>';
                echo '<th>' . esc_html__( 'Company', 'custom-user-orchestrator' ) . '</th>';
                echo '</tr></thead><tbody>';
                foreach ( $users as $user ) {
                    echo '<tr>';
                    echo '<td>' . esc_html( $user['id'] ?? '' ) . '</td>';
                    echo '<td>' . esc_html( $user['name'] ?? '' ) . '</td>';
                    echo '<td>' . esc_html( $user['email'] ?? '' ) . '</td>';
                    echo '<td>' . esc_html( $user['phone'] ?? '' ) . '</td>';
                    echo '<td>' . esc_html( $user['company']['name'] ?? '' ) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            }
            ?>
        </div>
        <?php
    }

    public function handle_refresh() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Unauthorized', 'custom-user-orchestrator' ) );
        }

        if ( ! isset( $_POST['cuo_refresh_nonce'] ) || ! wp_verify_nonce( $_POST['cuo_refresh_nonce'], 'cuo_refresh_action' ) ) {
            wp_die( __( 'Invalid nonce', 'custom-user-orchestrator' ) );
        }

        // Clear and fetch
        $this->dataManager->clear_cache();
        $result = $this->dataManager->fetch_and_cache();

        if ( is_wp_error( $result ) ) {
            wp_redirect( add_query_arg( 'cuo_error', urlencode( $result->get_error_message() ), wp_get_referer() ) );
            exit;
        }

        wp_redirect( add_query_arg( 'cuo_success', '1', wp_get_referer() ) );
        exit;
    }
}
