<?php
/**
 * Page template.
 *
 * The whole page body comes from the editor, so every heading, paragraph,
 * image, list, FAQ and form on a page is client editable.
 *
 * @package Splendid
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	the_content();
endwhile;

get_footer();
