<?php
/*
Plugin Name: Custom Smilies Directory
Plugin URI: http://plugins.josepardilla.com/custom-smilies-directory/
Description: Light plugin that tells WordPress to load Smilies from your theme's directory. This allows you to use custom Smilies without loosing them when you update WordPress.
Version: 1.0
Author: Jos&eacute; Pardilla
Author URI: http://josepardilla.com/

*/


/**
 * Convert one smiley code to the icon graphic file equivalent.
 *
 * Looks up one smiley code in the $wpsmiliestrans global array and returns an
 * <img> string for that smiley.
 *
 * @global array $wpsmiliestrans
 * @since 2.8.0
 *
 * @param string $smiley Smiley code to convert to image.
 * @return string Image string for smiley.
 */
function jpm_translate_smiley($smiley) {
	global $wpsmiliestrans;

	if ( count($smiley) == 0 ) {
		return '';
	}

	$smiley = trim( reset($smiley) );
	$img = $wpsmiliestrans[$smiley];
	$smiley_masked = esc_attr( $smiley );

	$srcurl = apply_filters( 'smilies_src', get_stylesheet_directory_uri() . "/smilies/$img", $img, site_url() );

	return " <img src='$srcurl' alt='$smiley_masked' class='wp-smiley' /> ";
}

/**
 * Convert text equivalent of smilies to images.
 *
 * Will only convert smilies if the option 'use_smilies' is true and the global
 * used in the function isn't empty.
 *
 * @since 0.71
 * @uses $wp_smiliessearch
 *
 * @param string $text Content to convert smilies from text.
 * @return string Converted content with text smilies replaced with images.
 */
function jpm_convert_smilies($text) {
	global $wp_smiliessearch;
	$output = '';
	if ( get_option('use_smilies') && !empty($wp_smiliessearch) ) {
		// HTML loop taken from texturize function, could possible be consolidated
		$textarr = preg_split("/(<.*>)/U", $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between
		$stop = count($textarr);// loop stuff
		for ($i = 0; $i < $stop; $i++) {
			$content = $textarr[$i];
			if ((strlen($content) > 0) && ('<' != $content[0])) { // If it's not a tag
				$content = preg_replace_callback($wp_smiliessearch, 'jpm_translate_smiley', $content);
			}
			$output .= $content;
		}
	} else {
		// return default text.
		$output = $text;
	}
	return $output;
}


/**
 * Hook
 */
function jpm_custom_smilies_init() {
	remove_filter( 'the_content', 'convert_smilies' );
	remove_filter( 'the_excerpt', 'convert_smilies' );
	remove_filter( 'comment_text', 'convert_smilies' );
	add_filter( 'the_content', 'jpm_convert_smilies' );
	add_filter( 'the_excerpt', 'jpm_convert_smilies' );
	add_filter( 'comment_text', 'jpm_convert_smilies' );
}

add_action( 'init', 'jpm_custom_smilies_init' );
?>