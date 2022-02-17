<?php

/**
 * Enqueue scripts & styles.
 */
function yoast_block_theme_enqueue_scripts_and_styles() {
	/**
	 * Add Tailwind from the play CDN.
	 *
	 * This is not the way to do things in a production environment.
	 * However, for this POC it's enough to get the ball rolling.
	 */
	wp_enqueue_script( 'tailwind-play-cdn', 'https://cdn.tailwindcss.com' );

	// Add tailwind config.
	wp_add_inline_script( 'tailwind-play-cdn', file_get_contents( get_template_directory() . '/tailwind.config.js' ) );

	// Add the default stylesheet.
	wp_enqueue_style(
		'yoast-block-theme-style',
		get_template_directory_uri() . '/style.css',
		[],
		filemtime( get_template_directory() . '/style.css' ) // DEV only.
	);
}
add_action( 'wp_enqueue_scripts', 'yoast_block_theme_enqueue_scripts_and_styles' );
add_action( 'admin_enqueue_scripts', 'yoast_block_theme_enqueue_scripts_and_styles' );

/**
 * Inject tailwind custom styles in <head>.
 */
function yoast_block_theme_inject_tailwind_modifications() {
	echo '<style type="text/tailwindcss">' . file_get_contents( get_template_directory() . '/tailwind-styles.css' ) . '</style>';
}
add_action( 'wp_head', 'yoast_block_theme_inject_tailwind_modifications' );
add_action( 'admin_head', 'yoast_block_theme_inject_tailwind_modifications' );

/**
 * A simple function to add/replace classes in elements.
 *
 * @param string $remove  The class to remove.
 * @param string $add     The classes to add.
 * @param string $html    The HTML to modify.
 */
function yoast_block_theme_replace_class( $remove = [], $add = [], $html = '' ) {
	if ( strpos( $html, 'class=' ) !== false ) {
		$html = str_replace(
			[ 'class="', 'class=\'' ],
			[ 'class="' . implode( ' ', $add ) . ' ',  'class=\'' . implode( ' ', $add ) . ' ' ],
			$html
		);
	} else {
		$html = str_replace(
			[ '>', '/>' ],
			[ ' class="' . implode( ' ', $add ) . '">', ' class="' . implode( ' ', $add ) . '" />' ],
			$html
		);
	}

	foreach ( $remove as $class_name ) {
		$html = str_replace( [ $class_name . ' ', $class_name ], [ ' ', '' ], $html );
	}

	return $html;
}

/**
 * Filter block classes.
 *
 * @param string $html The block HTML.
 * @param array  $block The block data.
 */
add_filter( 'render_block', function( $html, $block ) {

	// Skip if not a block.
	if ( ! isset( $block['blockName'] ) ) {
		return $html;
	}

	switch ( $block['blockName'] ) {

		// Buttons.
		case 'core/buttons':
			$html = str_replace(
				'wp-block-buttons',
				'wp-block-buttons sm:mt-8 sm:flex',
				$html
			);

		// Button.
		case 'core/button':
			$html = str_replace(
				'wp-block-button__link',
				'yst-w-full yst-flex yst-items-center yst-justify-center yst-px-8 yst-py-3 yst-border yst-border-transparent yst-text-base yst-font-medium yst-rounded-md yst-md:py-4 yst-md:text-lg yst-md:px-10',
				$html
			);
			break;

		// Headings.
		case 'core/heading':

			// If `level` is not set, it's `2`.
			$block['attrs']          = empty( $block['attrs'] ) ? [] : $block['attrs'];
			$block['attrs']['level'] = empty( $block['attrs']['level'] ) ? 2 : $block['attrs']['level'];

			// Heading 1.
			if ( 1 === $block['attrs']['level'] ) {
				$html = yoast_block_theme_replace_class(
					[],
					[ 'yst-mt-1', 'yst-block', 'yst-text-4xl', 'yst-tracking-tight', 'yst-font-extrabold', 'sm:yst-text-5xl', 'xl:yst-text-6xl' ],
					$html
				);
			}

			// Heading 2.
			if ( 2 === $block['attrs']['level'] ) {
				$html = yoast_block_theme_replace_class(
					[],
					[ 'yst-text-3xl', 'yst-leading-8', 'yst-font-extrabold', 'yst-tracking-tight', 'sm:yst-text-5xl' ],
					$html
				);
			}

			// Heading 3.
			if ( 3 === $block['attrs']['level'] ) {
				$html = yoast_block_theme_replace_class(
					[],
					[ 'yst-text-2xl', 'yst-font-extrabold', 'yst-text-gray-900', 'yst-tracking-tight', 'sm:yst-text-3xl', 'yst-mt-2' ],
					$html
				);
			}

			break;
	}

	// Text-align: center.
	$html = str_replace( 'has-text-align-center', 'yst-text-center', $html );

	// Background-colors.
	$html = str_replace( 'has-background', '', $html );
	$html = preg_replace(
		'/has-([a-z_]+-[0-9]{1,3})-background-color/i',
		'yst-bg-$1',
		$html
	);

	// Text-colors.
	$html = str_replace( 'has-text-color', '', $html );
	$html = preg_replace(
		'/has-([a-z_]+-[0-9]{1,3})-color/i',
		'yst-text-$1',
		$html
	);

	/**
	 * Replace presets classes.
	 */
	// if ( strpos( $html, 'yst-large-gray-p' ) ) {
	// 	$html = yoast_block_theme_replace_class(
	// 		[ 'yst-large-gray-p' ],
	// 		[ 'yst-mt-3', 'yst-text-base', 'yst-text-gray-500', 'sm:yst-mt-5', 'sm:yst-text-xl', 'yst-lg:text-lg', 'xl:yst-text-xl' ],
	// 		$html
	// 	);
	// }

	return $html;
}, 10, 2 );

/**
 * Register block styles.
 */
register_block_style( 'core/paragraph', [
	'name'         => 'yst-large-gray-p',
	'label'        => __( 'Large gray', 'yoast-block-theme' ),
] );
register_block_style( 'core/paragraph', [
	'name'         => 'yst-purple-rain-p',
	'label'        => __( 'Purple rain caps', 'yoast-block-theme' ),
] );
register_block_style( 'core/paragraph', [
	'name'         => 'yst-medium-p',
	'label'        => __( 'Medium', 'yoast-block-theme' ),
] );
register_block_style( 'core/paragraph', [
	'name'         => 'yst-smallish-light-p',
	'label'        => __( 'Small-light', 'yoast-block-theme' ),
] );

register_block_style( 'core/group', [
	'name'         => 'yst-rounded-border-group',
	'label'        => __( 'Rounded-bordered', 'yoast-block-theme' ),
] );
register_block_style( 'core/group', [
	'name'         => 'yst-padded-round-group',
	'label'        => __( 'Round & padded', 'yoast-block-theme' ),
] );
