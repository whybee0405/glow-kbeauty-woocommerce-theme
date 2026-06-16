<?php
/**
 * WordPress Customizer controls — branding, notice bar, contact details,
 * and hero trust signals. All values surface in the live preview.
 *
 * @package Glow_KBeauty
 */

defined( 'ABSPATH' ) || exit;

function glow_customizer_register( $wp_customize ) {

	/* ------------------------------------------------------------------
	 * Panel
	 * ------------------------------------------------------------------ */
	$wp_customize->add_panel(
		'glow_panel',
		array(
			'title'    => __( 'Glow K-Beauty', 'glow-kbeauty' ),
			'priority' => 30,
		)
	);

	/* ------------------------------------------------------------------
	 * Section: Homepage
	 * ------------------------------------------------------------------ */
	$wp_customize->add_section(
		'glow_homepage',
		array(
			'title'    => __( 'Homepage', 'glow-kbeauty' ),
			'panel'    => 'glow_panel',
			'priority' => 5,
		)
	);

	$wp_customize->add_setting(
		'glow_hero_shortcode',
		array(
			'default'           => '[helix_search]',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'glow_hero_shortcode',
		array(
			'label'       => __( 'Shortcode above hero', 'glow-kbeauty' ),
			'description' => __( 'Leave blank to hide. Example: [helix_search]', 'glow-kbeauty' ),
			'section'     => 'glow_homepage',
			'type'        => 'text',
		)
	);

	/* ------------------------------------------------------------------
	 * Section: Notice Bar
	 * ------------------------------------------------------------------ */
	$wp_customize->add_section(
		'glow_notice_bar',
		array(
			'title'    => __( 'Notice Bar', 'glow-kbeauty' ),
			'panel'    => 'glow_panel',
			'priority' => 10,
		)
	);

	$notice_defaults = array(
		'glow_notice_1' => __( 'Free shipping over R500', 'glow-kbeauty' ),
		'glow_notice_2' => __( 'Every batch verified', 'glow-kbeauty' ),
		'glow_notice_3' => __( 'Authentic K-beauty', 'glow-kbeauty' ),
	);

	foreach ( $notice_defaults as $key => $default ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $default,
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => sprintf( __( 'Notice item %d', 'glow-kbeauty' ), (int) substr( $key, -1 ) ),
				'section' => 'glow_notice_bar',
				'type'    => 'text',
			)
		);
	}

	/* ------------------------------------------------------------------
	 * Section: Trust Signals (hero footnote)
	 * ------------------------------------------------------------------ */
	$wp_customize->add_section(
		'glow_trust',
		array(
			'title'    => __( 'Trust Signals', 'glow-kbeauty' ),
			'panel'    => 'glow_panel',
			'priority' => 20,
		)
	);

	$trust_defaults = array(
		'glow_trust_1' => __( 'Batch-verified imports', 'glow-kbeauty' ),
		'glow_trust_2' => __( 'Free shipping over R500', 'glow-kbeauty' ),
		'glow_trust_3' => __( 'Ships from Joburg', 'glow-kbeauty' ),
	);

	foreach ( $trust_defaults as $key => $default ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $default,
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => sprintf( __( 'Trust signal %d', 'glow-kbeauty' ), (int) substr( $key, -1 ) ),
				'section' => 'glow_trust',
				'type'    => 'text',
			)
		);
	}

	/* ------------------------------------------------------------------
	 * Section: Contact Details
	 * ------------------------------------------------------------------ */
	$wp_customize->add_section(
		'glow_contact',
		array(
			'title'    => __( 'Contact Details', 'glow-kbeauty' ),
			'panel'    => 'glow_panel',
			'priority' => 30,
		)
	);

	$contact_fields = array(
		'glow_whatsapp_number' => array(
			'label'   => __( 'WhatsApp number (display)', 'glow-kbeauty' ),
			'default' => '+27 11 000 0000',
		),
		'glow_whatsapp_url' => array(
			'label'   => __( 'WhatsApp URL (wa.me link)', 'glow-kbeauty' ),
			'default' => 'https://wa.me/27110000000',
		),
		'glow_email' => array(
			'label'   => __( 'Main email address', 'glow-kbeauty' ),
			'default' => 'hello@glowkbeauty.co.za',
		),
		'glow_email_korean' => array(
			'label'   => __( 'Korean enquiries email', 'glow-kbeauty' ),
			'default' => 'korean@glowkbeauty.co.za',
		),
		'glow_hours' => array(
			'label'   => __( 'Business hours', 'glow-kbeauty' ),
			'default' => 'Mon–Fri, 08:30–17:00 SAST',
		),
	);

	foreach ( $contact_fields as $key => $cfg ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $cfg['default'],
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $cfg['label'],
				'section' => 'glow_contact',
				'type'    => 'text',
			)
		);
	}
}
add_action( 'customize_register', 'glow_customizer_register' );

/**
 * Postmessage JS for live-preview updates on notice bar and trust signals.
 */
function glow_customizer_preview_js() {
	wp_enqueue_script(
		'glow-customizer-preview',
		get_template_directory_uri() . '/js/customizer-preview.js',
		array( 'customize-preview' ),
		GLOW_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'glow_customizer_preview_js' );
