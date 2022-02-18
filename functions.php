<?php
/**
 * Functions and definitions
 *
 * @package Yoast\YoastBlockTheme
 */

/**
 * Add theme support for block styles and editor style.
 *
 * @since 0.1.0
 *
 * @return void
 */
function yoast_block_theme_setup() {
	add_theme_support( 'wp-block-styles' );
	add_editor_style( './assets/css/tailwind-build.css' );
}
add_action( 'after_setup_theme', 'yoast_block_theme_setup' );

/**
 * Enqueue scripts & styles.
 */
function yoast_block_theme_enqueue_scripts_and_styles() {
	// Add the default stylesheet.
	wp_enqueue_style(
		'yoast-block-theme-style',
		get_template_directory_uri() . '/style.css',
		[],
		filemtime( get_template_directory() . '/style.css' ) // DEV only.
	);

	// Add tailwind CSS.
	wp_enqueue_style(
		'yoast-block-theme-tailwind',
		get_template_directory_uri() . '/assets/css/tailwind-build.css',
		[],
		filemtime( get_template_directory() . '/assets/css/tailwind-build.css' ) // DEV only.
	);
}
add_action( 'wp_enqueue_scripts', 'yoast_block_theme_enqueue_scripts_and_styles' );

// Block styles
require_once get_theme_file_path( 'inc/register-block-styles.php' );

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
				'yst-w-full yst-flex yst-items-center yst-justify-center yst-px-8 yst-py-3 yst-border yst-border-transparent yst-text-base yst-font-medium yst-rounded-md md:yst-py-4 md:yst-text-lg md:yst-px-10',
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

	/**
	 * Replace presets classes.
	 */
	// if ( strpos( $html, 'yst-large-gray-p' ) ) {
	// 	$html = yoast_block_theme_replace_class(
	// 		[ 'yst-large-gray-p' ],
	// 		[ 'yst-mt-3', 'yst-text-base', 'yst-text-gray-500', 'sm:yst-mt-5', 'sm:yst-text-xl', 'lg:yst-text-lg', 'xl:yst-text-xl' ],
	// 		$html
	// 	);
	// }

	return $html;
}, 10, 2 );
