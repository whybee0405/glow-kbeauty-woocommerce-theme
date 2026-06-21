<?php
/**
 * Template Name: FAQ
 *
 * Comprehensive frequently asked questions page. Sections: Buying, Finance,
 * Trade-in & Selling, Delivery & Collection, After-sales, About Digicars.
 * Each section uses a native <details>/<summary> accordion — zero JS required.
 *
 * @package Digicars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$digicars_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '';
if ( ! $digicars_shop_url ) {
	$digicars_shop_url = home_url( '/shop' );
}

$digicars_faq_sections = array(
	array(
		'id'    => 'buying',
		'title' => __( 'Buying a car', 'digicars' ),
		'faqs'  => array(
			array(
				'q' => __( 'How does buying a car on Digicars work?', 'digicars' ),
				'a' => __( 'Browse verified stock on our shop page, compare cars, use the affordability calculator to see realistic monthly instalments, then submit an enquiry. A consultant reaches out within one working day to confirm the vehicle, discuss trade-ins, and walk you through finance. Once everything is agreed, you collect from our Gauteng branch or arrange delivery.', 'digicars' ),
			),
			array(
				'q' => __( 'Are the cars on your site available immediately?', 'digicars' ),
				'a' => __( 'Every listing is live and sourced from our in-stock inventory. If a car is listed, it is available. Occasionally a vehicle may sell while you are completing an enquiry — your consultant will let you know and offer alternatives.', 'digicars' ),
			),
			array(
				'q' => __( 'Can I reserve a car before completing the paperwork?', 'digicars' ),
				'a' => __( 'Yes. Once your enquiry is confirmed and deposit arrangements are underway, we can hold the vehicle for a reasonable period. Ask your consultant about the holding deposit amount and time frame.', 'digicars' ),
			),
			array(
				'q' => __( 'Do I need to visit a showroom to buy?', 'digicars' ),
				'a' => __( 'No. Most of the process happens online — vehicle selection, affordability check, and finance application. You will need to physically collect the car or arrange delivery, and a consultant may request that you sign documents in person, depending on finance approval.', 'digicars' ),
			),
			array(
				'q' => __( 'What makes and models do you stock?', 'digicars' ),
				'a' => __( 'We are an authorised Chery, Omoda, and Jaecoo dealer for new and demo vehicles. We also carry a curated selection of quality multi-brand pre-owned vehicles. Browse the shop and use the make filter to see what is currently available.', 'digicars' ),
			),
			array(
				'q' => __( 'Are the vehicle prices negotiable?', 'digicars' ),
				'a' => __( 'Listed prices are market-priced and generally firm. On pre-owned vehicles, there may be some flexibility depending on the vehicle age and condition. Your consultant can advise during the enquiry process.', 'digicars' ),
			),
			array(
				'q' => __( 'What does "demo" mean?', 'digicars' ),
				'a' => __( 'A demo vehicle has been used as a demonstration or courtesy car by a dealership. It typically has low mileage, is in excellent condition, carries the remainder of the factory warranty, and is priced below a brand-new equivalent.', 'digicars' ),
			),
		),
	),
	array(
		'id'    => 'finance',
		'title' => __( 'Finance', 'digicars' ),
		'faqs'  => array(
			array(
				'q' => __( 'How does car finance work?', 'digicars' ),
				'a' => __( 'We submit one application to multiple banks (ABSA, Standard Bank, FNB, Nedbank, WesBank). They come back with indicative approvals, interest rates, and monthly instalments. You choose the offer that works for you, sign the agreement, and the bank settles the seller directly.', 'digicars' ),
			),
			array(
				'q' => __( 'What is a deposit, and how much do I need?', 'digicars' ),
				'a' => __( 'Most banks require a deposit of 10% to 20% of the vehicle purchase price. A larger deposit reduces your monthly instalment and total interest paid. Some credit profiles may qualify for zero-deposit finance — your consultant will advise based on the bank responses.', 'digicars' ),
			),
			array(
				'q' => __( 'How long does finance approval take?', 'digicars' ),
				'a' => __( 'Most applications receive indicative responses within 24 to 48 business hours. Formal written approval may take a few additional days once all supporting documents are in order.', 'digicars' ),
			),
			array(
				'q' => __( 'What documents do I need for a finance application?', 'digicars' ),
				'a' => __( 'Typically: a clear copy of your South African ID or valid passport, recent 3-month payslips or bank statements (6 months if self-employed), proof of residence less than 3 months old, and a valid South African driver\'s licence. Your consultant will confirm the exact requirements based on your situation.', 'digicars' ),
			),
			array(
				'q' => __( 'What is the affordability calculator on the listing?', 'digicars' ),
				'a' => __( 'It gives you a rough monthly estimate based on purchase price, deposit, term, and an illustrative interest rate. It is not a finance offer — it is a planning tool to help you shortlist vehicles before submitting a formal application.', 'digicars' ),
			),
			array(
				'q' => __( 'Can I get finance with a bad credit history?', 'digicars' ),
				'a' => __( 'It is more difficult but not impossible. Banks assess applications case by case. A larger deposit, a shorter finance term, or a co-applicant can improve approval chances. We recommend submitting an application and seeing what the banks respond with.', 'digicars' ),
			),
			array(
				'q' => __( 'Can I pay cash?', 'digicars' ),
				'a' => __( 'Yes. Cash purchases are welcome and typically the fastest way to complete a sale. EFT is the preferred payment method — we do not accept physical cash for vehicle transactions. Your consultant will provide banking details and a pro-forma invoice.', 'digicars' ),
			),
		),
	),
	array(
		'id'    => 'trade-in',
		'title' => __( 'Trade-in & selling', 'digicars' ),
		'faqs'  => array(
			array(
				'q' => __( 'Can I trade in my current car?', 'digicars' ),
				'a' => __( 'Yes. Let your consultant know about your trade-in during the enquiry process. We will assess the vehicle and provide a trade-in valuation. The trade-in value is deducted from the purchase price of your new car.', 'digicars' ),
			),
			array(
				'q' => __( 'How is the trade-in value calculated?', 'digicars' ),
				'a' => __( 'We assess make, model, year, mileage, condition, and current market demand. We use live trade pricing data to give you a fair market-related offer. The physical inspection may adjust the initial online estimate.', 'digicars' ),
			),
			array(
				'q' => __( 'Can I sell my car to Digicars without buying another one?', 'digicars' ),
				'a' => __( 'Yes. Visit our Sell page, describe your vehicle, and request a valuation. If it matches our stock requirements, we will make an offer and proceed with the purchase — no obligation to buy from us.', 'digicars' ),
			),
			array(
				'q' => __( 'Do I need to settle my existing car finance before trading in?', 'digicars' ),
				'a' => __( 'Not necessarily. If you have outstanding finance, we can handle the settlement as part of the deal — the settlement amount is deducted from the trade-in value, and any surplus goes toward your new vehicle. Your consultant will explain the numbers clearly.', 'digicars' ),
			),
		),
	),
	array(
		'id'    => 'delivery',
		'title' => __( 'Delivery & collection', 'digicars' ),
		'faqs'  => array(
			array(
				'q' => __( 'Can you deliver a car to me outside Gauteng?', 'digicars' ),
				'a' => __( 'Yes. We deliver nationwide. Delivery costs and lead times vary by province and distance. Ask your consultant for a quote. Vehicles are transported by reputable car carriers with transit insurance.', 'digicars' ),
			),
			array(
				'q' => __( 'How does collection from a branch work?', 'digicars' ),
				'a' => __( 'Your consultant will schedule a handover appointment at the relevant Gauteng branch. You bring your ID and driver\'s licence, inspect the vehicle, sign handover documents, and drive away.', 'digicars' ),
			),
			array(
				'q' => __( 'How long before I can collect after deal completion?', 'digicars' ),
				'a' => __( 'For cash purchases, typically 2 to 5 business days for the vehicle to be prepared, cleaned, and roadworthy-checked. Finance purchases depend on bank payout timelines, usually 3 to 7 business days after approval.', 'digicars' ),
			),
			array(
				'q' => __( 'Will the car be roadworthy when I collect?', 'digicars' ),
				'a' => __( 'Yes. Every vehicle goes through a pre-delivery inspection and a roadworthy check before handover. New and demo vehicles come with factory warranty; pre-owned vehicles are confirmed roadworthy and any noted defects disclosed.', 'digicars' ),
			),
		),
	),
	array(
		'id'    => 'aftersales',
		'title' => __( 'After-sales & service', 'digicars' ),
		'faqs'  => array(
			array(
				'q' => __( 'Does my new or demo car come with a warranty?', 'digicars' ),
				'a' => __( 'Yes. New Chery, Omoda, and Jaecoo vehicles come with the manufacturer\'s warranty (currently a 5-year/100 000 km warranty on most Chery models). Demo vehicles carry the remaining factory warranty from the first registration date.', 'digicars' ),
			),
			array(
				'q' => __( 'Do pre-owned vehicles carry a warranty?', 'digicars' ),
				'a' => __( 'Most pre-owned vehicles carry the remainder of any factory warranty if still active. Where the factory warranty has lapsed, we can advise on after-market warranty options. Ask your consultant.', 'digicars' ),
			),
			array(
				'q' => __( 'Can you service my Chery, Omoda or Jaecoo?', 'digicars' ),
				'a' => __( 'Yes. Our Sandton and Northcliff branches have Chery-authorised service centres. Our Sandton branch services Omoda and Jaecoo vehicles. Book online via the Book a Service page or call 010 595 1180.', 'digicars' ),
			),
			array(
				'q' => __( 'What happens if something goes wrong after I buy?', 'digicars' ),
				'a' => __( 'Contact your consultant or call 010 595 1180 as soon as possible. Warranty claims are handled through the relevant manufacturer\'s process. For non-warranty issues, we will advise on the best course of action.', 'digicars' ),
			),
		),
	),
	array(
		'id'    => 'about',
		'title' => __( 'About Digicars', 'digicars' ),
		'faqs'  => array(
			array(
				'q' => __( 'What is Digicars?', 'digicars' ),
				'a' => __( 'Digicars is a digital-first, multi-brand car marketplace and authorised Chery, Omoda, and Jaecoo dealer based in Gauteng, South Africa. We combine online discovery with physical showrooms so you can do as much or as little online as you like.', 'digicars' ),
			),
			array(
				'q' => __( 'Where are you located?', 'digicars' ),
				'a' => __( 'We have four Gauteng branches: Chery Sandton (24 Rivonia Road), Chery Northcliff (453 Beyers Naudé Drive), Omoda & Jaecoo Sandton (30 Rivonia Road), Omoda & Jaecoo Melrose Arch (1 Melrose Boulevard), and our group head office at 168 Grayston Drive, Sandown.', 'digicars' ),
			),
			array(
				'q' => __( 'What is the Concierge?', 'digicars' ),
				'a' => __( 'The Concierge is our AI-powered vehicle discovery tool. Describe what you are looking for in plain language — lifestyle, budget, family size, brand preference — and it shortlists verified cars from our live catalogue that actually match. Access it at digicars.co.za/concierge.', 'digicars' ),
			),
			array(
				'q' => __( 'How do I contact Digicars?', 'digicars' ),
				'a' => __( 'Call us on 010 595 1180 (Mon–Fri 08:00–17:30, Sat 08:00–13:00), email info@digicars.co.za, or complete the contact form on our Contact page. You can also WhatsApp us.', 'digicars' ),
			),
			array(
				'q' => __( 'Is Digicars an authorised dealer?', 'digicars' ),
				'a' => __( 'Yes. We are an authorised Chery, Omoda, and Jaecoo dealer, which means we sell genuine, manufacturer-backed new and demo vehicles with full factory warranties and access to authorised service centres.', 'digicars' ),
			),
		),
	),
);
?>

<?php /* Hero --------------------------------------------------------------- */ ?>
<section class="section section--tight">
	<div class="container container--narrow">
		<div class="stack">
			<p class="eyebrow eyebrow--signal"><?php esc_html_e( 'FAQ', 'digicars' ); ?></p>
			<h1 class="t-hero"><?php esc_html_e( 'Questions, answered.', 'digicars' ); ?></h1>
			<p class="t-lead">
				<?php esc_html_e( 'Everything you need to know about buying, finance, trade-ins, delivery, and what happens after the sale. If your question is not here, call 010 595 1180 or ask the Concierge.', 'digicars' ); ?>
			</p>
		</div>
	</div>
</section>

<?php /* FAQ content --------------------------------------------------------- */ ?>
<section class="section section--flush-top">
	<div class="container">
		<div class="faq-grid">

			<?php /* Section navigation sidebar */ ?>
			<nav class="faq-nav" aria-label="<?php esc_attr_e( 'FAQ sections', 'digicars' ); ?>">
				<?php foreach ( $digicars_faq_sections as $digicars_section ) : ?>
					<a class="faq-nav__link" href="#faq-<?php echo esc_attr( $digicars_section['id'] ); ?>">
						<?php echo esc_html( $digicars_section['title'] ); ?>
					</a>
				<?php endforeach; ?>
				<hr style="border:none;border-top:1px solid var(--border);margin:var(--s-3) 0;">
				<a class="faq-nav__link faq-nav__link--cta" href="<?php echo esc_url( home_url( '/contact' ) ); ?>">
					<?php esc_html_e( 'Still have questions?', 'digicars' ); ?>
				</a>
			</nav>

			<?php /* FAQ sections */ ?>
			<div class="faq-sections">
				<?php foreach ( $digicars_faq_sections as $digicars_section ) : ?>
					<div class="faq-section" id="faq-<?php echo esc_attr( $digicars_section['id'] ); ?>">
						<h2 class="faq-section__title"><?php echo esc_html( $digicars_section['title'] ); ?></h2>

						<?php foreach ( $digicars_section['faqs'] as $digicars_faq ) : ?>
							<details class="faq-item">
								<summary class="faq-item__question">
									<?php echo esc_html( $digicars_faq['q'] ); ?>
									<span class="faq-item__icon" aria-hidden="true"></span>
								</summary>
								<div class="faq-item__answer">
									<p><?php echo esc_html( $digicars_faq['a'] ); ?></p>
								</div>
							</details>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>

		</div>
	</div>
</section>

<?php /* Still need help CTA ------------------------------------------------ */ ?>
<section class="section section--tight surface-soft" data-reveal>
	<div class="container container--narrow">
		<div class="stack" style="text-align:center;align-items:center;">
			<p class="eyebrow"><?php esc_html_e( 'Still not sure?', 'digicars' ); ?></p>
			<h2 class="t-2"><?php esc_html_e( 'We are happy to help.', 'digicars' ); ?></h2>
			<p class="t-lead">
				<?php esc_html_e( 'Call 010 595 1180, email info@digicars.co.za, or describe your ideal car to the Concierge and let it shortlist the right options.', 'digicars' ); ?>
			</p>
			<div class="cluster" style="justify-content:center;">
				<a class="btn btn--signal btn--lg" href="<?php echo esc_url( home_url( '/concierge' ) ); ?>">
					<?php esc_html_e( 'Ask the Concierge', 'digicars' ); ?>
				</a>
				<a class="btn btn--outline btn--lg" href="<?php echo esc_url( home_url( '/contact' ) ); ?>">
					<?php esc_html_e( 'Contact us', 'digicars' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
