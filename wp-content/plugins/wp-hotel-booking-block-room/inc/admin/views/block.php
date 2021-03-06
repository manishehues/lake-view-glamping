<?php
/**
 * Block date angular admin template.
 *
 * @author   ThimPress
 * @package  WP-Hotel-Booking/Block/View
 * @version  1.7.4
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

global $wpdb;
$rooms = $wpdb->get_results( $wpdb->prepare(
	"SELECT ID, post_title FROM {$wpdb->posts} WHERE `post_type` = %s AND `post_status` = %s", 'hb_room', 'publish'
), OBJECT );
?>

<div class="wrap" ng-app="hotel_booking_block">

    <form ng-submit="submit()" ng-controller="hotel_booking_calendar">

        <div class="hotel_booking_calendar">
            <div class="calendar_setup block-id-{{block.id}}" ng-repeat="block in calendars">
                <!--Remove button-->
                <a href="javascript:void(0)" class="remove-plan dashicons dashicons-trash"
                   ng-click="remove_calendar( block.id )"
                   title="<?php echo __( 'Delete', 'wp-hotel-booking-block' ); ?>"></a>
                <select class="select-block-rooms" ng-model="block.post_id" ng-multiple="true"
                        ng-options="room.post_title for room in rooms  track by room.ID" size="10" multiple></select>
                <hotel-calendar/>
            </div>
        </div>

        <a class="button" id="hotel_add_calendar"
           ng-click="add_calendar()"><?php _e( 'Add Specific Calendar', 'wp-hotel-booking-block' ); ?></a>
        <button class="button button-primary"><?php _e( 'Update', 'wp-hotel-booking-block' ) ?></button>
        <span class="block-message {{ status_ajax}}">{{ message}}</span>

    </form>

</div>

<script type="text/javascript">

    (function ($) {

        var app = angular.module('hotel_booking_block', ['multipleDatePicker', 'hotel_booking_directive']);

        app.controller('hotel_booking_calendar', ['$scope', '$http', function ($scope, $http) {

            $scope.rooms = <?php echo json_encode( $rooms ) ?>;
            // moment.tz.setDefault( '<?php echo date_default_timezone_get() ?>' );
            // $scope.today = moment().unix( <?php echo current_time( 'timestamp', 1 ) * 1000 ?> );
            $scope.today = moment().utc();
            // $scope.today = moment();

            $scope.calendar = [];
            $scope.message = '';
            $scope.status_ajax = '';

            $scope.temp = false;
            $scope.addSelected = false;
            $scope.removeSelected = false;

            $scope.calendars = <?php echo json_encode( Hotel_Booking_Block::instance()->get_blocked(), true ) ?>;

            // add new calendar
            $scope.add_calendar = function () {
                $scope.message = '';
                var unique_time = moment().valueOf(),
                    new_calendar = {
                        id: unique_time,
                        post_id: [],
                        selected: []
                    };
                $scope.calendars[unique_time] = new_calendar;
            };

            // save form
            $scope.submit = function () {
                $scope.message = '';

                $http.post(
                    Hotel_Booking_Block.ajaxurl + '&action=hotel_block_update', {
                        data: JSON.stringify($scope.calendars)
                    }
                ).success(function (data, status, headers, config) {
                    var status_ajax = 'error';
                    if (status === 200) {
                        if (typeof data.status === 'undefined' || data.status !== 'success') {
                            $scope.message = Hotel_Booking_Block.error_ajax;
                        }

                        if (typeof data.data !== 'undefined') {
                            status_ajax = 'success';
                            $scope.calendars = data.data;
                            if (typeof data.message !== 'undefined') {
                                $scope.message = data.message;
                            }
                        }
                    } else {
                        $scope.message = Hotel_Booking_Block.error_ajax;
                    }
                    $scope.status_ajax = status_ajax;
                });
            };

            // on select calendar
            $scope.logInfos = function (time, date) {
                var time = date.valueOf(); // time is timestamp utc
                $scope.temp = true;

                if (date.selected === false) {
                    $scope.removeSelected = false;
                    $scope.addSelected = time;
                } else {
                    $scope.addSelected = false;
                    $scope.removeSelected = time;
                }
            };

            // add remove scope calendar
            $scope.callback = function (id) {
                if ($scope.temp === false)
                    return;

                if (typeof $scope.calendars[id].selected === 'undefined') {
                    $scope.calendars[id].selected = [];
                }
                if ($scope.addSelected && $scope.removeSelected === false) {
                    $scope.calendars[id].selected.push($scope.addSelected)
                } else if ($scope.removeSelected && $scope.addSelected === false) {
                    var index = $scope.calendars[id].selected.indexOf($scope.removeSelected);
                    $scope.calendars[id].selected.splice(index, 1);
                }

                $scope.temp = false;
                $scope.addSelected = false;
            };

            $scope.remove_calendar = function (id) {
                $scope.message = '';
                var $block = $('.block-id-' + id);

                $block.css('opacity', '0.5');

                $http.post(
                    Hotel_Booking_Block.ajaxurl + '&action=hotel_block_delete_post_type', {calendar_id: id}, {async: false}
                ).success(function (data, status, headers, config) {
                    var status_ajax = 'error';
                    if (status === 200) {
                        if (typeof data.status !== 'undefined' && data.status === 'success') {
                            status_ajax = 'success';
                        }

                        if (typeof data.message !== 'undefined') {
                            $scope.message = data.message;
                        } else {
                            $scope.message = '';
                        }

                        if (typeof data.data !== 'undefined') {
                            $scope.calendars = data.data;
                        }
                    } else {
                        $scope.message = Hotel_Booking_Block.error_ajax;
                    }
                    $scope.status_ajax = status_ajax;
                });
            };
        }]);

        // directive
        var hotel_booking_directive = angular.module('hotel_booking_directive', []);
        // html element <hotel-calendar/>
        hotel_booking_directive.directive('hotelCalendar', function () {
            return {
                restrict: 'E',
                template: '<multiple-date-picker ng-model="block.selected" disable-days-before="today" calendar-id="block.id" day-click="logInfos" ng-click="callback( block.id )" days-selected="block.selected" data-id="{{ block.id }}"/>'
            }
        });
        // end directive

    })(jQuery);

</script>
