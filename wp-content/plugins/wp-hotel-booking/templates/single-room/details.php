<?php
/**
 * The template for displaying single room details.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/single-room/details.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$room = WPHB_Room::instance( get_the_ID() );

$checkout_date = '';
$checkin_date = '';
$capasicity = '';
if(isset($_REQUEST['check_in_date']) && $_REQUEST['check_in_date'] !=''){
	$checkin_date = $_REQUEST['check_in_date'];

}
if(isset($_REQUEST['check_out_date']) && $_REQUEST['check_out_date'] !=''){
	$checkout_date = $_REQUEST['check_out_date'];

}

if(isset($_REQUEST['capasicity']) && $_REQUEST['capasicity'] !=''){
	$capasicity = $_REQUEST['capasicity'];

}


ob_start();
the_content();
$content = ob_get_clean();

$tabsInfo   = array();

$tabsInfo[] = array(
	'id'      => 'hb_room_additinal',
	'title'   => __( 'Additional Information', 'wp-hotel-booking' ),
	'content' => $room->addition_information
);
$tabsInfo[] = array(
	'id'      => 'hb_room_description',
	'title'   => __( 'Description', 'wp-hotel-booking' ),
	'content' => $content
);
$tabs       = apply_filters( 'hotel_booking_single_room_infomation_tabs', $tabsInfo );

// prepend after li tabs single
do_action( 'hotel_booking_before_single_room_infomation' );
?>



<div class="hb_single_room_details">

    <ul class="hb_single_room_tabs">
		<?php /* foreach ( $tabs as $key => $tab ) { ?>
            <li>
                <a href="#<?php echo esc_attr( $tab['id'] ) ?>">
					<?php do_action( 'hotel_booking_single_room_before_tabs_' . $tab['id'] ); ?>
					<?php printf( '%s', $tab['title'] ) ?>
					<?php do_action( 'hotel_booking_single_room_after_tabs_' . $tab['id'] ); ?>
                </a>
            </li>
		<?php }  */ ?>
    </ul>

	<?php
	// append after li tabs single
	//do_action( 'hotel_booking_after_single_room_infomation' ); ?>

    <div class="hb_single_room_tabs_content">

	<?php 
		unset($tabs[2]); 
		unset($tabs[3]); 
	?>

		<?php foreach ( $tabs as $key => $tab ) { ?>
            <div id="<?php echo esc_attr( $tab['id'] ) ?>" class="hb_single_room_tab_details1">

				<?php do_action( 'hotel_booking_single_room_before_tabs_content_' . $tab['id'] ); ?>

				<?php printf( '%s', $tab['content'] ); ?>

				<?php do_action( 'hotel_booking_single_room_after_tabs_content_' . $tab['id'] ); ?>
            </div>
		<?php } ?>
    </div>

</div>
