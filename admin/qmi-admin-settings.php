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
                <p class="note"><?php esc_html_e( 'QM integrator Connects quiz maker with membership plugins', 'quiz-membership-integrator' ); ?></p>
                
                <div class="developer">
                    <h3>
                        <?php esc_html_e( 'Developed by:', 'quiz-membership-integrator' ); ?>
                            	
                        <a href="https://www.facebook.com/AshiqurRahmanMollik/" target="_blank" rel="noopener noreferrer">
                            <?php esc_html_e( 'Ashiqur Rahman', 'quiz-membership-integrator' ); ?>
                        </a>
                                                
                        <a href="https://www.facebook.com/AshiqurRahmanMollik/" target="_blank" class="icon-link">
                            <i class="fas fa-external-link"></i>
                        </a>
                    </h3>
                </div>
            </div>
            <div class="qmi-logo-container">
    				<?php 
        				// Get the logo image ID from the media library (if the image is uploaded to the media library)
        				$logo_url = plugin_dir_url( __FILE__ ) . 'images/qmi-logo.gif';
        
        				// If you have uploaded the image to the media library, you should use the image ID (attachment ID)
        				$logo_image_id = attachment_url_to_postid( $logo_url );

        				// Use wp_get_attachment_image() if the logo is an attachment in the media library
        				if ( $logo_image_id ) {
           	 			echo wp_get_attachment_image( $logo_image_id, 'full', false, array( 'alt' => esc_attr__( 'QMI Logo', 'quiz-membership-integrator' ) ) );
        				} else {
            			// Fallback if not found in media library, use img tag with URL
//             			echo '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr__( 'QMI Logo', 'quiz-membership-integrator' ) . '" class="qmi-logo">';
       					 }
    				?>
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
    register_setting( 'qmi_settings_group', 'qmi_settings', 'qmi_settings_sanitize' ); // Added sanitization function

    add_settings_section(
        'qmi_settings_section',
        esc_html__( 'Membership Level Settings', 'quiz-membership-integrator' ),
        'qmi_settings_section_callback',
        'quiz-membership-integrator'
    );

    add_settings_field(
        'qmi_conditions',
        esc_html__( 'Conditions', 'quiz-membership-integrator' ),
        'qmi_conditions_render',
        'quiz-membership-integrator',
        'qmi_settings_section'
    );
}

add_action( 'admin_init', 'qmi_register_settings' );

// Callback function to render settings section
function qmi_settings_section_callback() {
    echo wp_kses_post( '<p class="note">' . esc_html__( 'Assign the membership level IDs based on the quiz score scores.', 'quiz-membership-integrator' ) . '</p>' );
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
    <p class="total_active_conditions">
        <?php 
        /* translators: %d: number of active conditions */
        echo esc_html( sprintf( 
            /* translators: %d: number of active conditions */
            __( 'Active Conditions: %d', 'quiz-membership-integrator' ), 
            $active_conditions_count 
        ) ); 
        ?>
    </p>
    <div id="qmi-conditions-container">
        <div class="qmi-conditions-header">
            <div class="qmi-column-header"><?php esc_html_e( '#', 'quiz-membership-integrator' ); ?></div>
            <div class="qmi-column-header"><?php esc_html_e( 'Membership Level ID', 'quiz-membership-integrator' ); ?></div>
            <div class="qmi-column-header"><?php esc_html_e( 'Min Quiz Score', 'quiz-membership-integrator' ); ?></div>
            <div class="qmi-column-header"><?php esc_html_e( 'Max Quiz Score', 'quiz-membership-integrator' ); ?></div>
            <div class="qmi-column-header"><?php esc_html_e( 'on/off', 'quiz-membership-integrator' ); ?></div>
            <div class="qmi-column-header"><?php esc_html_e( 'Delete', 'quiz-membership-integrator' ); ?></div>
        </div>
        <?php if ( ! empty( $conditions ) ) : ?>
            <?php foreach ( $conditions as $index => $condition ) : ?>
                <div class="qmi-condition <?php echo esc_attr( ! empty( $condition['active'] ) ? 'active' : '' ); ?>">
                    <div class="qmi-row-number"><?php echo esc_html( $index + 1 ); ?></div>
                    <input type="number" 
                           name="<?php echo esc_attr( sprintf( 'qmi_settings[qmi_conditions][%d][membership_level]', $index ) ); ?>" 
                           value="<?php echo esc_attr( $condition['membership_level'] ); ?>" 
                           placeholder="<?php esc_attr_e( 'Membership Level ID', 'quiz-membership-integrator' ); ?>">
                    <input type="number" 
                           name="<?php echo esc_attr( sprintf( 'qmi_settings[qmi_conditions][%d][min_score]', $index ) ); ?>" 
                           value="<?php echo esc_attr( $condition['min_score'] ); ?>" 
                           placeholder="<?php esc_attr_e( 'Min Quiz Score', 'quiz-membership-integrator' ); ?>">
                    <input type="number" 
                           name="<?php echo esc_attr( sprintf( 'qmi_settings[qmi_conditions][%d][max_score]', $index ) ); ?>" 
                           value="<?php echo esc_attr( $condition['max_score'] ); ?>" 
                           placeholder="<?php esc_attr_e( 'Max Quiz Score', 'quiz-membership-integrator' ); ?>">
                    
                    <label class="qmi-toggle">
                        <input type="checkbox" 
                               name="<?php echo esc_attr( sprintf( 'qmi_settings[qmi_conditions][%d][active]', $index ) ); ?>" 
                               <?php checked( ! empty( $condition['active'] ) ); ?>>
                        <span class="qmi-slider round"></span>
                        <input type="hidden" 
                               name="<?php echo esc_attr( sprintf( 'qmi_settings[qmi_conditions][%d][active]', $index ) ); ?>" 
                               value="<?php echo esc_attr( ! empty( $condition['active'] ) ? '1' : '0' ); ?>">
                    </label>
                    
                    <button class="qmi-remove-condition"><?php esc_html_e( 'Remove', 'quiz-membership-integrator' ); ?></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button id="qmi-add-condition" class="button"><?php esc_html_e( 'Add New Condition', 'quiz-membership-integrator' ); ?></button>
    <?php
}

// Sanitization function
function qmi_settings_sanitize( $options ) {
    // Ensure the qmi_conditions field is an array
    if ( isset( $options['qmi_conditions'] ) && is_array( $options['qmi_conditions'] ) ) {
        foreach ( $options['qmi_conditions'] as $index => $condition ) {
            // Sanitize individual fields
            $options['qmi_conditions'][$index]['membership_level'] = intval( $condition['membership_level'] );
            $options['qmi_conditions'][$index]['min_score'] = intval( $condition['min_score'] );
            $options['qmi_conditions'][$index]['max_score'] = intval( $condition['max_score'] );
            $options['qmi_conditions'][$index]['active'] = isset( $condition['active'] ) ? '1' : '0';
        }
    }
    return $options;
}
?>
