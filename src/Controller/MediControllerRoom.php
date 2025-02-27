<?php

namespace Medi\Controller;

use Medi\View\Admin\MediAdminRoom;
use Medi\View\Frontend\MediFrontendShortCode;
use Medi\Model\MediRoom;

class MediControllerRoom
{
    public MediAdminRoom $admin_view_room;

    public MediFrontendShortCode $shortcode_view_room;

    public MediRoom $model_room;

    public function __construct(MediRoom $model_room)
    {
        $this->model_room = $model_room;

        add_action('wp_ajax_room_insert_data_action', [$this, 'admin_room_insert_data']);
        add_action('wp_ajax_nopriv_room_insert_data_action', [$this, 'admin_room_insert_data']);
        add_action('wp_ajax_room_delete_data_action', [$this, 'admin_room_delete_data']);
        add_action('wp_ajax_nopriv_room_delete_data_action', [$this, 'admin_room_delete_data']);
    }

    /**
     * @param MediAdminRoom $admin_view_room
     * @return void
     */
    public function medi_set_room_view(MediAdminRoom $admin_view_room): void
    {
        $this->admin_view_room = $admin_view_room;
    }

    /**
     * @return array
     */
    public function medi_render_room_data() : array
    {
        return $this->model_room->render();
    }

    /**
     * @param $data_id
     * @return array
     */
    public function medi_render_room_data_by_id($data_id)
    {
        return $this->model_room->get_data($data_id);
    }

    /**
     * Set the page for the plugin inside the WordPress backend
     *
     * @return void
     */
    public function medi_add_room_subpage() : void
    {
        add_submenu_page(
        'admin-booking-overview',
        'Buchungs - Tool Einstellungen',
        'Einstellungen',
        'manage_options',
        'admin-settings',
            [$this->admin_view_room, 'medi_admin_room_view_render'],
        );
    }

    public function admin_room_insert_data() : void
    {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'medi_booking_nonce')) {
            wp_send_json_error(['message' => 'Ungültige Anfrage.']);
            return;
        }

        if (isset($_POST['data'])) {
            parse_str($_POST['data'], $parsed_data);

            if (empty($parsed_data['room_name'])) {
                wp_send_json_error(['message' => 'Pflichtfeld "Raumname" nicht ausgefüllt']);
                return;
            }

            $data = [
                'room_name' => sanitize_text_field($parsed_data['room_name']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Datensatz einfügen
            $return = $this->model_room->insert($data);

            if ($return === true) {
                wp_send_json_success(['message' => 'Account wurde erfolgreich angelegt.']);
            } else {
                wp_send_json_error(['message' => 'Fehler beim Einfügen des Accounts.']);
            }
        } else {
            wp_send_json_error(['message' => "Fehler aufgetreten."]);
        }
    }

    public function admin_room_delete_data() : void
    {
        if( isset($_POST['dataId']) ) {
            $room_id = sanitize_text_field($_POST['dataId']);

            $return = $this->model_room->delete_data($room_id);

            if ( $return === true ) {
                wp_send_json_success(['message' => "Account ID {$room_id} gelöscht."]);
            } else {
                wp_send_json_error(['message' => "Fehler aufgetreten."]);
            }
        }
    }
}