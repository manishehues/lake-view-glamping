<?php
/**
 * The template for displaying search room results.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/search/results.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

do_action( 'hb_before_search_result' ); ?>
<?php the_field('header_part'); ?>

<?php global $hb_search_rooms; ?>

<div id="booking-results" class="section">
	<div class="fusion-row">
		<div id="hotel-booking-results">
			<?php if ( $results && ! empty( $hb_search_rooms['data'] ) ) { ?>        

				<?php hb_get_template( 'search/list.php', array( 'results' => $hb_search_rooms['data'], 'atts' => $atts ) ); ?>

				<nav class="rooms-pagination">
					<?php echo paginate_links( apply_filters( 'hb_pagination_args', array(
						'base'      => add_query_arg( 'hb_page', '%#%' ),
						'format'    => '',
						'prev_text' => __( 'Previous', 'wp-hotel-booking' ),
						'next_text' => __( 'Next', 'wp-hotel-booking' ),
						'total'     => $hb_search_rooms['max_num_pages'],
						'current'   => $hb_search_rooms['page'],
						'type'      => 'list',
						'end_size'  => 3,
						'mid_size'  => 3
					) ) );
					?>
				</nav>
			<?php } else { ?>
				<div class="notFound">
					<h4><?php _e( 'No room found.', 'wp-hotel-booking' ); ?></h4>				
					<div class="btnArea">
						<a class="btn btn-primary small" href="<?php echo site_url('/booking'); ?>"><?php _e( 'Search again!', 'wp-hotel-booking' ); ?></a>
					</div>
				</div>				
			<?php } ?>
		</div>
	</div>
</div>

