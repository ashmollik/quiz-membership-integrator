<?php
/**
 * Admin Menu for Quiz Membership Integrator
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add admin menu
function qmi_add_admin_menu() {
    add_menu_page(
        'Quiz Membership Integrator',
        'Quiz Membership Integrator',
        'manage_options',
        'quiz-membership-integrator',
        'qmi_settings_page',
        'dashicons-controls-repeat',
        plugin_dir_url( __FILE__ ) . 'images/qmi-logo.png',
        100
    );
}

add_action( 'admin_menu', 'qmi_add_admin_menu' );

// Settings page
function qmi_settings_page() {
    $options = get_option( 'qmi_settings' );
    $active_conditions_count = 0;

    if ( isset( $options['qmi_conditions'] ) && is_array( $options['qmi_conditions'] ) ) {
        foreach ( $options['qmi_conditions'] as $condition ) {
            if ( ! empty( $condition['active'] ) && $condition['active'] == '1' ) {
                $active_conditions_count++;
            }
        }
    }
    ?>
    <div class="wrap qmi-settings-page">
        <div class="qmi-content-container">
            <div class="qmi-content">
                <h1><?php esc_html_e( 'Quiz Membership Integrator', 'quiz-membership-integrator' ); ?></h1>
                <p class="note"><?php echo __( 'QM integrator Connects quiz maker with membership plugins', 'quiz-membership-integrator' ); ?></p>
                
                <div class="developer">
                    <h3>
                        Developed by :
                            
                                <a href="https://www.facebook.com/AshiqurRahmanMollik/" target="_blank" rel="noopener noreferrer">
                                        Ashiqur Rahman
                                </a>
                                
                                <a href="https://www.facebook.com/AshiqurRahmanMollik/" target="_blank" class="icon-link">
                                    <i class="fas fa-external-link"></i>
                                </a>
                    </h3>
                    
                </div>

            </div>
            <div class="qmi-logo-container">
                <img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/qmi-logo.gif'; ?>" alt="QMI Logo" class="qmi-logo">
            </div>
        </div>
        <div class="qmi-form-container">
            <form id="qmi-settings-form" method="post" action="options.php">
                <?php
                settings_fields( 'qmi_settings_group' );
                do_settings_sections( 'quiz-membership-integrator' );
                submit_button();
                ?>
            </form>
        </div>
    </div>
    <?php
}

// Register settings
function qmi_register_settings() {
    register_setting( 'qmi_settings_group', 'qmi_settings' );

    add_settings_section(
        'qmi_settings_section',
        __( 'Membership Level Settings', 'quiz-membership-integrator' ),
        'qmi_settings_section_callback',
        'quiz-membership-integrator'
    );

    add_settings_field(
        'qmi_conditions',
        __( 'Conditions', 'quiz-membership-integrator' ),
        'qmi_conditions_render',
        'quiz-membership-integrator',
        'qmi_settings_section'
    );
}

add_action( 'admin_init', 'qmi_register_settings' );

// Callback function to render settings section
function qmi_settings_section_callback() {
    echo '<p class="note">' . __( 'Assign the membership level IDs based on the quiz score scores.', 'quiz-membership-integrator' ) . '</p>';
}

// Function to render conditions settings
function qmi_conditions_render() {
    $options = get_option( 'qmi_settings' );
    $conditions = isset( $options['qmi_conditions'] ) ? $options['qmi_conditions'] : array();
    $active_conditions_count = 0;

    if ( isset( $conditions ) && is_array( $conditions ) ) {
        foreach ( $conditions as $condition ) {
            if ( ! empty( $condition['active'] ) && $condition['active'] == '1' ) {
                $active_conditions_count++;
            }
        }
    }
    ?>
    <p class="total_active_conditions"><?php printf( __( 'Active Conditions: %d', 'quiz-membership-integrator' ), $active_conditions_count ); ?></p> <!-- Display Active Conditions on top -->
    <div id="qmi-conditions-container">
        <div class="qmi-conditions-header">
            <div class="qmi-column-header">#</div> <!-- New column header for row number -->
            <div class="qmi-column-header">Membership Level ID</div>
            <div class="qmi-column-header">Min Quiz Score</div>
            <div class="qmi-column-header">Max Quiz Score</div>
            <div class="qmi-column-header">on/off</div>
            <div class="qmi-column-header">Delete</div>
        </div>
        <?php if ( ! empty( $conditions ) ) : ?>
            <?php foreach ( $conditions as $index => $condition ) : ?>
                <div class="qmi-condition <?php echo ! empty( $condition['active'] ) ? 'active' : ''; ?>">
                    <div class="qmi-row-number"><?php echo $index + 1; ?></div> <!-- Display row number -->
                    <input type="number" name="qmi_settings[qmi_conditions][<?php echo $index; ?>][membership_level]" value="<?php echo esc_attr( $condition['membership_level'] ); ?>" placeholder="Membership Level ID">
                    <input type="number" name="qmi_settings[qmi_conditions][<?php echo $index; ?>][min_score]" value="<?php echo esc_attr( $condition['min_score'] ); ?>" placeholder="Min Quiz Score">
                    <input type="number" name="qmi_settings[qmi_conditions][<?php echo $index; ?>][max_score]" value="<?php echo esc_attr( $condition['max_score'] ); ?>" placeholder="Max Quiz Score">
                    
                    <!-- Toggle button -->
                    <label class="qmi-toggle">
                        <input type="checkbox" name="qmi_settings[qmi_conditions][<?php echo $index; ?>][active]" <?php checked( ! empty( $condition['active'] ) ); ?>>
                        <span class="qmi-slider round"></span>
                        <input type="hidden" name="qmi_settings[qmi_conditions][<?php echo $index; ?>][active]" value="<?php echo ! empty( $condition['active'] ) ? '1' : '0'; ?>"> <!-- Hidden field for toggle state -->
                    </label>
                    
                    <button class="qmi-remove-condition">Remove</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button id="qmi-add-condition" class="button"><?php _e( 'Add New Condition', 'quiz-membership-integrator' ); ?></button>
    <?php
}
?>
