<?php
/**
 * Custom Elementor widgets — all 8 homepage sections as drag-and-drop
 * components. Each widget renders the exact same HTML as the PHP templates
 * so every existing CSS rule and JS interaction works unchanged.
 *
 * Registered via glow_register_elementor_widgets() in functions.php.
 *
 * @package Glow_KBeauty
 */

defined( 'ABSPATH' ) || exit;

/* ==========================================================================
   1. Hero Stage Widget
   ========================================================================== */

class Glow_Hero_Stage_Widget extends \Elementor\Widget_Base {

	public function get_name() { return 'glow_hero_stage'; }
	public function get_title() { return __( 'Hero Stage', 'glow-kbeauty' ); }
	public function get_icon() { return 'eicon-slides'; }
	public function get_categories() { return array( 'glow-kbeauty' ); }

	protected function register_controls() {

		/* ---- Hero Copy ---- */
		$this->start_controls_section( 'section_copy', array( 'label' => __( 'Hero Copy', 'glow-kbeauty' ) ) );

		$this->add_control( 'eyebrow_text', array(
			'label'   => __( 'Eyebrow text', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => __( 'The K-beauty method', 'glow-kbeauty' ),
		) );
		$this->add_control( 'heading', array(
			'label'   => __( 'Heading (use <em> for yuja underline)', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::WYSIWYG,
			'default' => __( 'The glow is in the <em>method,</em> not the miracle.', 'glow-kbeauty' ),
		) );
		$this->add_control( 'lead', array(
			'label'   => __( 'Lead paragraph', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => __( 'Korean skincare works because of the order you apply it in, not the logo on the bottle. So we organised the whole store the way you\'ll actually use it — seven steps, cleanse to SPF.', 'glow-kbeauty' ),
		) );
		$this->add_control( 'cta1_text', array(
			'label'   => __( 'Primary CTA label', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => __( 'Shop all products', 'glow-kbeauty' ),
		) );
		$this->add_control( 'cta1_url', array(
			'label'   => __( 'Primary CTA URL', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::URL,
			'default' => array( 'url' => '' ),
		) );
		$this->add_control( 'cta2_text', array(
			'label'   => __( 'Secondary CTA label', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => __( 'Build my routine', 'glow-kbeauty' ),
		) );
		$this->add_control( 'cta2_anchor', array(
			'label'   => __( 'Secondary CTA anchor (e.g. #routine)', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '#routine',
		) );
		$this->add_control( 'trust_1', array(
			'label'   => __( 'Trust signal 1', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => get_theme_mod( 'glow_trust_1', __( 'Batch-verified imports', 'glow-kbeauty' ) ),
		) );
		$this->add_control( 'trust_2', array(
			'label'   => __( 'Trust signal 2', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => get_theme_mod( 'glow_trust_2', __( 'Free shipping over R500', 'glow-kbeauty' ) ),
		) );
		$this->add_control( 'trust_3', array(
			'label'   => __( 'Trust signal 3', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => get_theme_mod( 'glow_trust_3', __( 'Ships from Joburg', 'glow-kbeauty' ) ),
		) );

		$this->end_controls_section();

		/* ---- Stage Products ---- */
		$this->start_controls_section( 'section_stages', array( 'label' => __( 'Stage Products (one per routine step)', 'glow-kbeauty' ) ) );

		$stages_repeater = new \Elementor\Repeater();

		$stages_repeater->add_control( 'brand', array(
			'label'   => __( 'Brand name', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'COSRX',
		) );
		$stages_repeater->add_control( 'name', array(
			'label'   => __( 'Product name', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => __( 'Low pH Good Morning Gel Cleanser', 'glow-kbeauty' ),
		) );
		$stages_repeater->add_control( 'price', array(
			'label'   => __( 'Price (display only, e.g. R295)', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'R295',
		) );
		$stages_repeater->add_control( 'svg', array(
			'label'       => __( 'Image filename or full URL', 'glow-kbeauty' ),
			'description' => __( 'Filename from the theme\'s images/products/ folder (e.g. cosrx-low-ph-cleanser.svg), or a full https:// URL.', 'glow-kbeauty' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => 'cosrx-low-ph-cleanser.svg',
		) );
		$stages_repeater->add_control( 'tone', array(
			'label'   => __( 'Background tone', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'tone-seafoam',
			'options' => array(
				'tone-seafoam'   => __( 'Seafoam (green)', 'glow-kbeauty' ),
				'tone-petal'     => __( 'Petal (pink)', 'glow-kbeauty' ),
				'tone-rice-deep' => __( 'Rice deep (warm cream)', 'glow-kbeauty' ),
			),
		) );

		$this->add_control( 'stages', array(
			'label'       => __( 'Stage products', 'glow-kbeauty' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $stages_repeater->get_controls(),
			'default'     => array(
				array( 'brand' => 'COSRX',     'name' => 'Low pH Good Morning Gel Cleanser',      'price' => 'R295', 'svg' => 'product-step-01.jpg', 'tone' => 'tone-seafoam' ),
				array( 'brand' => 'COSRX',     'name' => 'BHA Blackhead Power Liquid',            'price' => 'R385', 'svg' => 'product-step-02.jpg', 'tone' => 'tone-petal' ),
				array( 'brand' => 'COSRX',     'name' => 'Advanced Snail 96 Mucin Power Essence', 'price' => 'R450', 'svg' => 'product-step-03.jpg', 'tone' => 'tone-rice-deep' ),
				array( 'brand' => 'Klairs',    'name' => 'Freshly Juiced Vitamin Drop',           'price' => 'R430', 'svg' => 'product-step-04.jpg', 'tone' => 'tone-seafoam' ),
				array( 'brand' => 'Laneige',   'name' => 'Water Bank Blue Hyaluronic Cream',      'price' => 'R750', 'svg' => 'product-step-05.jpg', 'tone' => 'tone-petal' ),
				array( 'brand' => 'AHC',       'name' => 'Ageless Real Eye Cream For Face',       'price' => 'R340', 'svg' => 'product-step-06.jpg', 'tone' => 'tone-rice-deep' ),
				array( 'brand' => 'Innisfree', 'name' => 'Daily UV Defense Sunscreen SPF36+',     'price' => 'R330', 'svg' => 'product-step-07.jpg', 'tone' => 'tone-seafoam' ),
			),
			'title_field' => '{{{ brand }}} — {{{ name }}}',
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$steps = glow_routine_steps();

		$shop_url = glow_wc_active() ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
		if ( ! empty( $s['cta1_url']['url'] ) ) {
			$shop_url = esc_url( $s['cta1_url']['url'] );
		}

		?>
		<section class="hero">
			<div class="container hero-grid">

				<div class="hero-copy">
					<p class="eyebrow"><?php echo esc_html( $s['eyebrow_text'] ); ?></p>
					<h1 class="t-hero"><?php echo wp_kses( $s['heading'], array( 'em' => array(), 'strong' => array(), 'br' => array() ) ); ?></h1>
					<p class="lead"><?php echo esc_html( $s['lead'] ); ?></p>

					<div class="hero-ctas">
						<a class="btn btn-solid" href="<?php echo esc_url( $shop_url ); ?>"><?php echo esc_html( $s['cta1_text'] ); ?></a>
						<a class="btn btn-outline" href="<?php echo esc_attr( $s['cta2_anchor'] ); ?>"><?php echo esc_html( $s['cta2_text'] ); ?></a>
					</div>

					<p class="hero-footnote">
						<span><?php echo esc_html( $s['trust_1'] ); ?></span>
						<span><?php echo esc_html( $s['trust_2'] ); ?></span>
						<span><?php echo esc_html( $s['trust_3'] ); ?></span>
					</p>
				</div>

				<div class="hero-stage tone-seafoam" data-hero-stage aria-live="polite">
					<?php foreach ( $s['stages'] as $i => $stage ) :
						$step_no = $i + 1;
						$step    = isset( $steps[ $i ] ) ? $steps[ $i ] : null;
						$is_active = ( 0 === $i ) ? ' is-active' : '';
						$tone    = ! empty( $stage['tone'] ) ? $stage['tone'] : 'tone-seafoam';

						$svg = trim( $stage['svg'] );
						if ( filter_var( $svg, FILTER_VALIDATE_URL ) ) {
							$img_src = $svg;
						} elseif ( $svg ) {
							$img_src = get_template_directory_uri() . '/images/products/' . sanitize_file_name( $svg );
						} else {
							$img_src = get_template_directory_uri() . '/images/products/_default.svg';
						}

						$step_url = $step ? glow_step_url( $step['slug'] ) : '#';
					?>
						<div class="stage-item<?php echo esc_attr( $is_active ); ?>" data-stage="<?php echo (int) $step_no; ?>" data-tone="<?php echo esc_attr( $tone ); ?>">
							<?php if ( $step ) : ?>
								<span class="stage-tag">STEP <?php echo esc_html( $step['no'] ); ?> · <?php echo esc_html( $step['name'] ); ?></span>
							<?php endif; ?>
							<div class="stage-media">
								<img src="<?php echo esc_url( $img_src ); ?>" alt="<?php echo esc_attr( $stage['brand'] . ' ' . $stage['name'] ); ?>" width="600" height="600" />
							</div>
							<a class="stage-meta" href="<?php echo esc_url( $step_url ); ?>">
								<span class="stage-name">
									<span class="stage-brand"><?php echo esc_html( $stage['brand'] ); ?></span>
									<?php echo esc_html( $stage['name'] ); ?>
								</span>
								<span class="stage-price mono"><?php echo esc_html( $stage['price'] ); ?></span>
							</a>
						</div>
					<?php endforeach; ?>
				</div>

			</div>
		</section>
		<?php
	}
}

/* ==========================================================================
   2. Routine Rail Widget
   ========================================================================== */

class Glow_Routine_Rail_Widget extends \Elementor\Widget_Base {

	public function get_name() { return 'glow_routine_rail'; }
	public function get_title() { return __( 'Routine Rail', 'glow-kbeauty' ); }
	public function get_icon() { return 'eicon-nav-menu'; }
	public function get_categories() { return array( 'glow-kbeauty' ); }

	protected function register_controls() {
		$this->start_controls_section( 'section_settings', array( 'label' => __( 'Settings', 'glow-kbeauty' ) ) );
		$this->add_control( 'linked', array(
			'label'        => __( 'Link steps to category archives', 'glow-kbeauty' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => __( 'Yes', 'glow-kbeauty' ),
			'label_off'    => __( 'No', 'glow-kbeauty' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		glow_routine_rail( 'yes' === $s['linked'] );
	}
}

/* ==========================================================================
   3. Concern Tiles Widget
   ========================================================================== */

class Glow_Concern_Tiles_Widget extends \Elementor\Widget_Base {

	public function get_name() { return 'glow_concern_tiles'; }
	public function get_title() { return __( 'Concern Tiles', 'glow-kbeauty' ); }
	public function get_icon() { return 'eicon-inner-section'; }
	public function get_categories() { return array( 'glow-kbeauty' ); }

	protected function register_controls() {
		$this->start_controls_section( 'section_header', array( 'label' => __( 'Section Header', 'glow-kbeauty' ) ) );
		$this->add_control( 'eyebrow_text',   array( 'label' => __( 'Eyebrow text', 'glow-kbeauty' ),    'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'Skip to the fix', 'glow-kbeauty' ) ) );
		$this->add_control( 'heading',        array( 'label' => __( 'Heading', 'glow-kbeauty' ),          'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'In a hurry? Shop by concern.', 'glow-kbeauty' ) ) );
		$this->add_control( 'description',    array( 'label' => __( 'Description', 'glow-kbeauty' ),      'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => __( 'No routine required — go straight to what your skin is asking for.', 'glow-kbeauty' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_tiles', array( 'label' => __( 'Tiles', 'glow-kbeauty' ) ) );

		$repeater = new \Elementor\Repeater();
		$repeater->add_control( 'title',        array( 'label' => __( 'Title', 'glow-kbeauty' ),           'type' => \Elementor\Controls_Manager::TEXT ) );
		$repeater->add_control( 'description',  array( 'label' => __( 'Description', 'glow-kbeauty' ),     'type' => \Elementor\Controls_Manager::TEXTAREA ) );
		$repeater->add_control( 'concern_slug', array( 'label' => __( 'Concern taxonomy slug', 'glow-kbeauty' ), 'type' => \Elementor\Controls_Manager::TEXT ) );
		$repeater->add_control( 'tone',         array(
			'label'   => __( 'Tile tone', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'tone-seafoam',
			'options' => array(
				'tone-seafoam'   => 'Seafoam',
				'tone-petal'     => 'Petal',
				'tone-moss'      => 'Moss',
				'tone-rice-deep' => 'Rice deep',
			),
		) );
		$repeater->add_control( 'link_label',   array( 'label' => __( 'Link label', 'glow-kbeauty' ),      'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'Shop', 'glow-kbeauty' ) ) );

		$this->add_control( 'tiles', array(
			'label'       => __( 'Concern tiles', 'glow-kbeauty' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => array(
				array( 'title' => __( 'Dehydrated & dull', 'glow-kbeauty' ),     'description' => __( 'Skin that drinks moisturiser and still looks tired by 3pm. Humectants and rice extract first.', 'glow-kbeauty' ),              'concern_slug' => 'dehydrated-dull',    'tone' => 'tone-seafoam',   'link_label' => __( 'Shop', 'glow-kbeauty' ) ),
				array( 'title' => __( 'Breakouts & texture', 'glow-kbeauty' ),   'description' => __( 'Congestion, bumps and marks that overstay. BHA, tea tree and patience — in that order.', 'glow-kbeauty' ),                   'concern_slug' => 'breakouts-texture',  'tone' => 'tone-petal',     'link_label' => __( 'Shop', 'glow-kbeauty' ) ),
				array( 'title' => __( 'Fine lines & firmness', 'glow-kbeauty' ), 'description' => __( 'Early lines and slow mornings. Peptides, snail mucin and daily SPF do the long game.', 'glow-kbeauty' ),                    'concern_slug' => 'fine-lines-firmness', 'tone' => 'tone-moss',     'link_label' => __( 'Shop', 'glow-kbeauty' ) ),
				array( 'title' => __( 'Sensitive & reactive', 'glow-kbeauty' ),  'description' => __( 'Burnt before? Everything here is fragrance-checked, with full ingredient lists up front.', 'glow-kbeauty' ),                 'concern_slug' => 'sensitive-reactive', 'tone' => 'tone-rice-deep', 'link_label' => __( 'Shop', 'glow-kbeauty' ) ),
			),
			'title_field' => '{{{ title }}}',
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="section">
			<div class="container">
				<div class="section-head" data-reveal>
					<div>
						<p class="eyebrow"><?php echo esc_html( $s['eyebrow_text'] ); ?></p>
						<h2 class="t-1"><?php echo esc_html( $s['heading'] ); ?></h2>
					</div>
					<p class="head-aside lead"><?php echo esc_html( $s['description'] ); ?></p>
				</div>
				<div class="grid-4" data-reveal>
					<?php foreach ( $s['tiles'] as $tile ) :
						$url = ! empty( $tile['concern_slug'] ) ? glow_tax_url( $tile['concern_slug'], 'skin_concern' ) : '#';
					?>
						<a class="concern-tile <?php echo esc_attr( $tile['tone'] ); ?>" href="<?php echo esc_url( $url ); ?>">
							<h3><?php echo esc_html( $tile['title'] ); ?></h3>
							<p><?php echo esc_html( $tile['description'] ); ?></p>
							<span class="link-arrow"><?php echo esc_html( $tile['link_label'] ); ?> <span class="arrow" aria-hidden="true">→</span></span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}

/* ==========================================================================
   4. Best Sellers Widget
   ========================================================================== */

class Glow_Best_Sellers_Widget extends \Elementor\Widget_Base {

	public function get_name() { return 'glow_best_sellers'; }
	public function get_title() { return __( 'Best Sellers', 'glow-kbeauty' ); }
	public function get_icon() { return 'eicon-products'; }
	public function get_categories() { return array( 'glow-kbeauty' ); }

	protected function register_controls() {
		$this->start_controls_section( 'section_settings', array( 'label' => __( 'Settings', 'glow-kbeauty' ) ) );
		$this->add_control( 'eyebrow_text',   array( 'label' => __( 'Eyebrow text', 'glow-kbeauty' ),    'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'What\'s flying off shelves', 'glow-kbeauty' ) ) );
		$this->add_control( 'heading',        array( 'label' => __( 'Heading', 'glow-kbeauty' ),          'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'Our best sellers', 'glow-kbeauty' ) ) );
		$this->add_control( 'limit', array(
			'label'   => __( 'Number of products', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 4,
			'min'     => 1,
			'max'     => 12,
		) );
		$this->add_control( 'use_featured', array(
			'label'        => __( 'Show featured products', 'glow-kbeauty' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => __( 'Yes', 'glow-kbeauty' ),
			'label_off'    => __( 'No', 'glow-kbeauty' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		) );
		$this->end_controls_section();
	}

	protected function render() {
		if ( ! glow_wc_active() ) {
			echo '<p>' . esc_html__( 'WooCommerce is required for this widget.', 'glow-kbeauty' ) . '</p>';
			return;
		}

		$s     = $this->get_settings_for_display();
		$limit = max( 1, (int) $s['limit'] );
		$args  = array( 'status' => 'publish', 'limit' => $limit );

		if ( 'yes' === $s['use_featured'] ) {
			$args['featured'] = true;
		}

		$products = wc_get_products( $args );

		if ( empty( $products ) && 'yes' === $s['use_featured'] ) {
			$products = wc_get_products( array( 'status' => 'publish', 'limit' => $limit, 'orderby' => 'date', 'order' => 'DESC' ) );
		}

		if ( empty( $products ) ) {
			echo '<p>' . esc_html__( 'No products found.', 'glow-kbeauty' ) . '</p>';
			return;
		}
		?>
		<section class="section section-tight">
			<div class="container">
				<div class="section-head" data-reveal>
					<div>
						<p class="eyebrow"><?php echo esc_html( $s['eyebrow_text'] ); ?></p>
						<h2 class="t-1"><?php echo esc_html( $s['heading'] ); ?></h2>
					</div>
				</div>
				<ul class="products grid-4" data-reveal>
					<?php
					global $product, $post;
					wc_setup_loop( array( 'columns' => 4 ) );
					foreach ( $products as $product ) {
						$post = get_post( $product->get_id() );
						if ( ! $post ) { continue; }
						setup_postdata( $post );
						wc_get_template_part( 'content', 'product' );
					}
					wp_reset_postdata();
					wc_reset_loop();
					?>
				</ul>
			</div>
		</section>
		<?php
	}
}

/* ==========================================================================
   5. Sourcing Split Widget
   ========================================================================== */

class Glow_Sourcing_Split_Widget extends \Elementor\Widget_Base {

	public function get_name() { return 'glow_sourcing_split'; }
	public function get_title() { return __( 'Sourcing Split', 'glow-kbeauty' ); }
	public function get_icon() { return 'eicon-column'; }
	public function get_categories() { return array( 'glow-kbeauty' ); }

	protected function register_controls() {
		$this->start_controls_section( 'section_content', array( 'label' => __( 'Content', 'glow-kbeauty' ) ) );
		$this->add_control( 'eyebrow_text',   array( 'label' => __( 'Eyebrow text', 'glow-kbeauty' ),      'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'The supply line', 'glow-kbeauty' ) ) );
		$this->add_control( 'heading',        array( 'label' => __( 'Heading', 'glow-kbeauty' ),           'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'Seoul to Joburg in under three weeks', 'glow-kbeauty' ) ) );
		$this->add_control( 'lead',           array( 'label' => __( 'Lead paragraph', 'glow-kbeauty' ),    'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => __( 'Freshness is most of the battle with actives like vitamin C and probiotics. Short, frequent supply runs mean what you buy was made recently — not discovered in a warehouse.', 'glow-kbeauty' ) ) );
		$this->add_control( 'cta_text',       array( 'label' => __( 'CTA label', 'glow-kbeauty' ),         'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'Shop the current batch', 'glow-kbeauty' ) ) );
		$this->add_control( 'cta_url',        array( 'label' => __( 'CTA URL', 'glow-kbeauty' ),           'type' => \Elementor\Controls_Manager::URL, 'default' => array( 'url' => '' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_supply', array( 'label' => __( 'Supply Line Steps', 'glow-kbeauty' ) ) );
		$repeater = new \Elementor\Repeater();
		$repeater->add_control( 'time_label',   array( 'label' => __( 'Time label', 'glow-kbeauty' ),   'type' => \Elementor\Controls_Manager::TEXT ) );
		$repeater->add_control( 'description',  array( 'label' => __( 'Description', 'glow-kbeauty' ), 'type' => \Elementor\Controls_Manager::TEXT ) );
		$this->add_control( 'supply_items', array(
			'label'       => __( 'Supply line steps', 'glow-kbeauty' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => array(
				array( 'time_label' => __( 'Week 0', 'glow-kbeauty' ),    'description' => __( 'Order placed with the brand in Seoul, in Korean', 'glow-kbeauty' ) ),
				array( 'time_label' => __( 'Week 1', 'glow-kbeauty' ),    'description' => __( 'Air freight to OR Tambo — small batches, no sea containers', 'glow-kbeauty' ) ),
				array( 'time_label' => __( 'Week 2', 'glow-kbeauty' ),    'description' => __( 'Batch numbers logged and cross-checked at our Joburg studio', 'glow-kbeauty' ) ),
				array( 'time_label' => __( 'Same week', 'glow-kbeauty' ), 'description' => __( 'On the site, in date, on its way to you', 'glow-kbeauty' ) ),
			),
			'title_field' => '{{{ time_label }}}',
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$cta_url = ! empty( $s['cta_url']['url'] ) ? $s['cta_url']['url'] : ( glow_wc_active() ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) );
		?>
		<section class="section section-tight">
			<div class="container">
				<div class="sourcing-split" data-reveal>
					<div class="sourcing-media">
						<ul class="supply-line">
							<?php foreach ( $s['supply_items'] as $item ) : ?>
								<li><span class="mono"><?php echo esc_html( $item['time_label'] ); ?></span> <?php echo esc_html( $item['description'] ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<div class="sourcing-body">
						<p class="eyebrow"><?php echo esc_html( $s['eyebrow_text'] ); ?></p>
						<h2 class="t-1"><?php echo esc_html( $s['heading'] ); ?></h2>
						<p class="lead"><?php echo esc_html( $s['lead'] ); ?></p>
						<a class="btn btn-solid" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( $s['cta_text'] ); ?></a>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}

/* ==========================================================================
   6. Ingredient Index Widget
   ========================================================================== */

class Glow_Ingredient_Index_Widget extends \Elementor\Widget_Base {

	public function get_name() { return 'glow_ingredient_index'; }
	public function get_title() { return __( 'Ingredient Index', 'glow-kbeauty' ); }
	public function get_icon() { return 'eicon-bullet-list'; }
	public function get_categories() { return array( 'glow-kbeauty' ); }

	protected function register_controls() {
		$this->start_controls_section( 'section_header', array( 'label' => __( 'Section Header', 'glow-kbeauty' ) ) );
		$this->add_control( 'eyebrow_text',   array( 'label' => __( 'Eyebrow text', 'glow-kbeauty' ),    'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'What\'s inside', 'glow-kbeauty' ) ) );
		$this->add_control( 'heading',        array( 'label' => __( 'Heading', 'glow-kbeauty' ),          'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'Find products by key active.', 'glow-kbeauty' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_items', array( 'label' => __( 'Ingredients', 'glow-kbeauty' ) ) );
		$repeater = new \Elementor\Repeater();
		$repeater->add_control( 'name',        array( 'label' => __( 'Ingredient name', 'glow-kbeauty' ),  'type' => \Elementor\Controls_Manager::TEXT ) );
		$repeater->add_control( 'description', array( 'label' => __( 'Description', 'glow-kbeauty' ),      'type' => \Elementor\Controls_Manager::TEXTAREA ) );
		$repeater->add_control( 'search_term', array( 'label' => __( 'Search term (exact)', 'glow-kbeauty' ), 'type' => \Elementor\Controls_Manager::TEXT ) );
		$this->add_control( 'items', array(
			'label'       => __( 'Ingredient rows', 'glow-kbeauty' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => array(
				array( 'name' => 'Snail Mucin',         'description' => __( 'Hydration, texture, repair. Works for almost all skin types.', 'glow-kbeauty' ),                          'search_term' => 'snail mucin' ),
				array( 'name' => 'Niacinamide',         'description' => __( 'Brightening, pore-minimising, oil-balancing. The workhorse.', 'glow-kbeauty' ),                           'search_term' => 'niacinamide' ),
				array( 'name' => 'Centella Asiatica',   'description' => __( 'Calms, strengthens barrier, speeds healing. Sensitive skin first pick.', 'glow-kbeauty' ),               'search_term' => 'centella' ),
				array( 'name' => 'Hyaluronic Acid',     'description' => __( 'Draws moisture into skin. Layered under moisturiser — not instead of it.', 'glow-kbeauty' ),             'search_term' => 'hyaluronic acid' ),
				array( 'name' => 'Vitamin C',           'description' => __( 'Brightening + antioxidant. Store cool, use morning only.', 'glow-kbeauty' ),                             'search_term' => 'vitamin c' ),
			),
			'title_field' => '{{{ name }}}',
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$s        = $this->get_settings_for_display();
		$base_url = glow_wc_active() ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
		?>
		<section class="section" id="ingredients">
			<div class="container">
				<div class="section-head" data-reveal>
					<div>
						<p class="eyebrow"><?php echo esc_html( $s['eyebrow_text'] ); ?></p>
						<h2 class="t-1"><?php echo esc_html( $s['heading'] ); ?></h2>
					</div>
				</div>
				<div class="ingredient-index" data-reveal>
					<?php foreach ( $s['items'] as $item ) :
						$url = add_query_arg( array( 's' => urlencode( $item['search_term'] ), 'post_type' => 'product' ), home_url( '/' ) );
					?>
						<a class="ingredient-row" href="<?php echo esc_url( $url ); ?>">
							<span class="ingredient-name"><?php echo esc_html( $item['name'] ); ?></span>
							<span class="ingredient-desc"><?php echo esc_html( $item['description'] ); ?></span>
							<span class="ingredient-arrow" aria-hidden="true">→</span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}

/* ==========================================================================
   7. Review Cards Widget
   ========================================================================== */

class Glow_Review_Cards_Widget extends \Elementor\Widget_Base {

	public function get_name() { return 'glow_review_cards'; }
	public function get_title() { return __( 'Review Cards', 'glow-kbeauty' ); }
	public function get_icon() { return 'eicon-testimonial'; }
	public function get_categories() { return array( 'glow-kbeauty' ); }

	protected function register_controls() {
		$this->start_controls_section( 'section_header', array( 'label' => __( 'Section Header', 'glow-kbeauty' ) ) );
		$this->add_control( 'eyebrow_text',   array( 'label' => __( 'Eyebrow text', 'glow-kbeauty' ),    'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'Real customers', 'glow-kbeauty' ) ) );
		$this->add_control( 'heading',        array( 'label' => __( 'Heading', 'glow-kbeauty' ),          'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'What your skin said after 30 days.', 'glow-kbeauty' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_reviews', array( 'label' => __( 'Reviews', 'glow-kbeauty' ) ) );
		$repeater = new \Elementor\Repeater();
		$repeater->add_control( 'author',       array( 'label' => __( 'Name', 'glow-kbeauty' ),         'type' => \Elementor\Controls_Manager::TEXT ) );
		$repeater->add_control( 'location',     array( 'label' => __( 'Location', 'glow-kbeauty' ),     'type' => \Elementor\Controls_Manager::TEXT ) );
		$repeater->add_control( 'text',         array( 'label' => __( 'Review text', 'glow-kbeauty' ),  'type' => \Elementor\Controls_Manager::TEXTAREA ) );
		$repeater->add_control( 'product_name', array( 'label' => __( 'Product name', 'glow-kbeauty' ), 'type' => \Elementor\Controls_Manager::TEXT ) );
		$repeater->add_control( 'step_no',      array( 'label' => __( 'Routine step (e.g. 03)', 'glow-kbeauty' ), 'type' => \Elementor\Controls_Manager::TEXT ) );
		$repeater->add_control( 'rating',       array(
			'label'   => __( 'Star rating', 'glow-kbeauty' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '5',
			'options' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5' ),
		) );
		$this->add_control( 'reviews', array(
			'label'       => __( 'Reviews', 'glow-kbeauty' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => array(
				array( 'author' => 'Thandi M.', 'location' => 'Cape Town',     'text' => __( "I\'ve been using the COSRX snail essence for three months. My texture has genuinely improved — I\'m not just saying that.", 'glow-kbeauty' ), 'product_name' => 'COSRX Snail 96 Essence',        'step_no' => '03', 'rating' => '5' ),
				array( 'author' => 'Priya N.',  'location' => 'Durban',        'text' => __( 'Ordered the Laneige cream after comparing three options with the team on WhatsApp. Arrived in two days. Incredible after the first week.', 'glow-kbeauty' ), 'product_name' => 'Laneige Water Bank Cream', 'step_no' => '05', 'rating' => '5' ),
				array( 'author' => 'Jenna F.',  'location' => 'Johannesburg',  'text' => __( "Finally a place that actually stocks the real thing. Checked my Innisfree sunscreen batch code — it\'s legitimate and fresh.", 'glow-kbeauty' ), 'product_name' => 'Innisfree UV Defense SPF36+', 'step_no' => '07', 'rating' => '4' ),
			),
			'title_field' => '{{{ author }}} — {{{ location }}}',
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="section section-tight">
			<div class="container">
				<div class="section-head" data-reveal>
					<div>
						<p class="eyebrow"><?php echo esc_html( $s['eyebrow_text'] ); ?></p>
						<h2 class="t-1"><?php echo esc_html( $s['heading'] ); ?></h2>
					</div>
				</div>
				<div class="reviews-grid" data-reveal>
					<?php foreach ( $s['reviews'] as $review ) : ?>
						<div class="review-card">
							<div class="review-head">
								<?php glow_stars( (float) $review['rating'] ); ?>
								<?php if ( ! empty( $review['product_name'] ) && ! empty( $review['step_no'] ) ) : ?>
									<span class="review-product"><span class="mono">STEP <?php echo esc_html( $review['step_no'] ); ?></span> <?php echo esc_html( $review['product_name'] ); ?></span>
								<?php endif; ?>
							</div>
							<blockquote class="review-body">
								<p><?php echo esc_html( $review['text'] ); ?></p>
							</blockquote>
							<footer class="review-foot">
								<strong><?php echo esc_html( $review['author'] ); ?></strong>
								<?php if ( ! empty( $review['location'] ) ) : ?>
									<span class="review-location"><?php echo esc_html( $review['location'] ); ?></span>
								<?php endif; ?>
							</footer>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}

/* ==========================================================================
   8. Newsletter Widget
   ========================================================================== */

class Glow_Newsletter_Widget extends \Elementor\Widget_Base {

	public function get_name() { return 'glow_newsletter'; }
	public function get_title() { return __( 'Newsletter Panel', 'glow-kbeauty' ); }
	public function get_icon() { return 'eicon-envelope'; }
	public function get_categories() { return array( 'glow-kbeauty' ); }

	protected function register_controls() {
		$this->start_controls_section( 'section_content', array( 'label' => __( 'Content', 'glow-kbeauty' ) ) );
		$this->add_control( 'eyebrow_text',   array( 'label' => __( 'Eyebrow text', 'glow-kbeauty' ),    'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'Stay in the know', 'glow-kbeauty' ) ) );
		$this->add_control( 'heading',        array( 'label' => __( 'Heading', 'glow-kbeauty' ),          'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'One email a month.', 'glow-kbeauty' ) ) );
		$this->add_control( 'description',    array( 'label' => __( 'Description', 'glow-kbeauty' ),      'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => __( 'New arrivals, ingredient deep-dives, and the occasional honest opinion on what isn\'t worth your money. No sequences, no drip campaigns.', 'glow-kbeauty' ) ) );
		$this->add_control( 'button_label',   array( 'label' => __( 'Button label', 'glow-kbeauty' ),     'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'Join the list', 'glow-kbeauty' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="section newsletter-panel">
			<div class="container">
				<div class="newsletter-inner" data-reveal>
					<div class="newsletter-copy">
						<p class="eyebrow"><?php echo esc_html( $s['eyebrow_text'] ); ?></p>
						<h2 class="t-1"><?php echo esc_html( $s['heading'] ); ?></h2>
						<p class="lead"><?php echo esc_html( $s['description'] ); ?></p>
					</div>
					<form class="newsletter-form" data-ajax-form="glow_newsletter">
						<label class="screen-reader-text" for="nl-email-<?php echo esc_attr( $this->get_id() ); ?>"><?php esc_html_e( 'Email address', 'glow-kbeauty' ); ?></label>
						<input type="email" id="nl-email-<?php echo esc_attr( $this->get_id() ); ?>" name="email" placeholder="<?php esc_attr_e( 'your@email.com', 'glow-kbeauty' ); ?>" required autocomplete="email" />
						<button class="btn btn-yuja" type="submit"><?php echo esc_html( $s['button_label'] ); ?></button>
					</form>
				</div>
			</div>
		</section>
		<?php
	}
}
