<?php

namespace Medi\Controller;

use Exception;
use Medi\View\Admin\MediAdminBooking;
use Medi\Model\MediBooking;
use Medi\Model\MediAccount;
use Medi\Controller\MediControllerAccount;

class MediControllerBooking
{
    public MediAdminBooking $admin_view_booking;
    public MediBooking $model_booking;
    public MediAccount $model_account;
    public MediControllerAccount $controller_account;

    public function __construct(MediBooking $model_booking)
    {
        global $wpdb;

        $this->model_booking = $model_booking;
        $this->model_account = new MediAccount($wpdb);
        $this->controller_account = new MediControllerAccount($this->model_account);

        add_action('wp_ajax_booking_insert_data_action', [$this, 'admin_booking_insert_data']);
        add_action('wp_ajax_nopriv_booking_insert_data_action', [$this, 'admin_booking_insert_data']);
        add_action('wp_ajax_booking_delete_data_action', [$this, 'admin_booking_delete_data']);
        add_action('wp_ajax_nopriv_booking_delete_data_action', [$this, 'admin_booking_delete_data']);
        add_action('wp_ajax_booking_calender_render', [$this, 'medi_booking_calender_render']);
        add_action('wp_ajax_nopriv_booking_calender_render', [$this, 'medi_booking_calender_render']);
        add_action('wp_ajax_booking_calender_render_week', [$this, 'medi_booking_calender_render_week']);
        add_action('wp_ajax_nopriv_booking_calender_render_week', [$this, 'medi_booking_calender_render_week']);
    }

    /**
     * @param MediAdminBooking $admin_view_booking
     * @return void
     */
    public function medi_set_booking_view(MediAdminBooking $admin_view_booking): void
    {
        $this->admin_view_booking = $admin_view_booking;
    }

    /**
     * @return array
     */
    public function medi_render_booking_data() : array
    {
        return $this->model_booking->render();
    }

    /**
     * @param $data_id
     * @return array
     */
    public function medi_render_booking_data_by_id($data_id)
    {
        return $this->model_booking->get_data($data_id);
    }

    /**
     * Set the page for the plugin inside the WordPress backend
     *
     * @return void
     * @throws Exception
     */
    public function medi_add_booking_page() : void
    {
        if (!isset($this->admin_view_booking)) {
            throw new Exception("admin_view_account wurde nicht gesetzt!");
        }

        add_menu_page (
            'Buchungs - Tool',
            'Buchungen',
            'manage_options',
            'admin-booking-overview',
            [$this->admin_view_booking, 'medi_admin_booking_view_render'],
            'dashicons-calendar-alt',
            89
        );
    }

    /**
     * @return void
     */
    public function admin_booking_insert_data() : void
    {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'medi_booking_nonce')) {
            wp_send_json_error(['message' => 'Ungültige Anfrage.']);
            return;
        }

        if (isset($_POST['data'])) {
            parse_str($_POST['data'], $parsed_data);

            if (empty($parsed_data['booking_date_start']) || empty($parsed_data['booking_date_end']) || empty($parsed_data['account_id']) || empty($parsed_data['room_id'])) {
                wp_send_json_error(['message' => 'Pflichtfelder fehlen für die Buchung']);
                return;
            }

            $data = [
                'booking_notice' => sanitize_text_field($parsed_data['booking_notice']),
                'booking_date_start' => sanitize_text_field($parsed_data['booking_date_start']),
                'booking_date_end' => sanitize_text_field($parsed_data['booking_date_end']),
                'account_id' => sanitize_text_field($parsed_data['account_id']),
                'room_id' => sanitize_text_field($parsed_data['room_id']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($parsed_data['booking_id']) {
                $return = $this->model_booking->update($data, $parsed_data['booking_id']);
            } else {
                $return = $this->model_booking->insert($data);
            }

            if ($return === true) {
                wp_send_json_success(['message' => 'Buchung wurde erfolgreich angelegt.']);
            } else {
                wp_send_json_error(['message' => 'Fehler beim Einfügen der Buchung.']);
            }
        } else {
            wp_send_json_error(['message' => "Fehler aufgetreten."]);
        }
    }

    /**
     * @return void
     */
    public function admin_booking_delete_data() : void
    {
        if( isset($_POST['dataId']) ) {
            $booking_id = sanitize_text_field($_POST['dataId']);

            $return = $this->model_booking->delete_data($booking_id);

            if ( $return === true ) {
                wp_send_json_success(['message' => "Account ID {$booking_id} gelöscht."]);
            } else {
                wp_send_json_error(['message' => "Fehler aufgetreten."]);
            }
        }
    }

    /**
     * @return void
     */
    public function medi_booking_calender_render() : void
    {
        $year = isset($_POST['year']) ? intval(sanitize_text_field($_POST['year'])) : date('Y');
        $month = isset($_POST['month']) ? intval(sanitize_text_field($_POST['month'])) : date('m');

        $today = date('Y-m-d');

        $events = [];

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf("%04d-%02d-%02d", $year, $month, $day);
            $account_id = $this->medi_booking_room_available($date);

            if ($date < $today) {
                $classname = "day inactive";
                $booking_data = [];
            } elseif ($account_id !== 0) {
                $classname = "day inactive fully-booked";
                $booking_data = $this->controller_account->medi_render_account_data_by_id($account_id);
            } else {
                $classname = "day";
                $booking_data = [];
            }

            $events[] = [
                "date" => $date,
                "classname" => $classname,
                "booking_data" => $booking_data,
            ];
        }

        wp_send_json_success($events);
    }

    /**
     * @return void
     */
    public function medi_booking_calender_render_week() : void
    {
        $weekDays = $_POST['week_days'];

        $events = [];
        $today = date('Y-m-d');

        foreach ($weekDays as $date) {
            $account_id = $this->medi_booking_room_available($date);

            if ($date < $today) {
                $classname = "day inactive";
                $booking_data = [];
            } elseif ($account_id !== 0) {
                $classname = "day inactive fully-booked";
                $booking_data = $this->controller_account->medi_render_account_data_by_id($account_id);
            } else {
                $classname = "day";
                $booking_data = [];
            }

            $events[] = [
                "date" => $date,
                "classname" => $classname,
                "booking_data" => $booking_data,
            ];
        }

        wp_send_json_success($events);
    }

    /**
     * @param $date
     * @return int
     */
    private function medi_booking_room_available($date) : int
    {
        $booked_data = $this->model_booking->render();

        $requested_date = date('Y-m-d', strtotime($date));

        foreach ($booked_data as $booking) {
            $booking_start_date = date('Y-m-d', strtotime($booking->booking_date_start));
            $booking_end_date = date('Y-m-d', strtotime($booking->booking_date_end));

            if ( $requested_date >= $booking_start_date && $requested_date <= $booking_end_date) {
                return $booking->account_id;
            }
        }

        return 0;
    }
}