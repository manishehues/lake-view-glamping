<?php
/**
 * The template for displaying archive room.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/archive-room.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

get_header(); ?>

<div id="booking-results" class="section">
	<div class="fusion-row">

<?php
/**
 * hotel_booking_before_main_content hook
 */
do_action( 'hotel_booking_before_main_content' ); ?>

<?php
/**
 * hotel_booking_archive_description hook
 */
do_action( 'hotel_booking_archive_description' ); ?>

<?php if ( have_posts() ) : ?>

	<?php
	/**
	 * hotel_booking_before_room_loop hook
	 */
	do_action( 'hotel_booking_before_room_loop' ); ?>

	<?php hotel_booking_room_loop_start(); ?>

	<?php hotel_booking_room_subcategories(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php hb_get_template_part( 'content', 'room' ); ?>

	<?php endwhile; ?>

	<?php hotel_booking_room_loop_end(); ?>

	<?php
	/**
	 * hotel_booking_after_room_loop hook
	 */
	do_action( 'hotel_booking_after_room_loop' ); ?>

<?php endif; ?>

<?php
/**
 * hotel_booking_after_main_content hook
 */
do_action( 'hotel_booking_after_main_content' ); ?>

</div>
</div>

<?php
/**
 * hotel_booking_sidebar hook
 */
//do_action( 'hotel_booking_sidebar' ); ?>

<?php get_footer(); ?>