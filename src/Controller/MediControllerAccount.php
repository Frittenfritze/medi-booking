<?php

namespace Medi\Controller;

use Exception;
use Medi\View\Admin\MediAdminAccount;
use Medi\Model\MediAccount;

class MediControllerAccount
{
    public MediAdminAccount $admin_view_account;
    public MediAccount $model_account;

    public function __construct(MediAccount $model_account)
    {
        $this->model_account = $model_account;

        add_action('wp_ajax_account_insert_data_action', [$this, 'admin_account_insert_data']);
        add_action('wp_ajax_nopriv_account_insert_data_action', [$this, 'admin_account_insert_data']);
        add_action('wp_ajax_account_delete_data_action', [$this, 'admin_account_delete_data']);
        add_action('wp_ajax_nopriv_account_delete_data_action', [$this, 'admin_account_delete_data']);
        add_action('wp_ajax_account_get_data_action', [$this, 'medi_render_account_data_by_id_ajax']);
        add_action('wp_ajax_nopriv_account_get_data_action', [$this, 'medi_render_account_data_by_id_ajax']);
        add_action('wp_ajax_account_get_data_by_mail_action', [$this, 'medi_render_account_data_by_mail_ajax']);
        add_action('wp_ajax_nopriv_account_get_data_by_mail_action', [$this, 'medi_render_account_data_by_mail_ajax']);
        add_action('wp_ajax_account_get_data_by_username_action', [$this, 'medi_render_account_data_by_username_ajax']);
        add_action('wp_ajax_nopriv_account_get_data_by_username_action', [$this, 'medi_render_account_data_by_username_ajax']);
    }

    /**
     * @param MediAdminAccount $admin_view_account
     * @return void
     */
    public function medi_set_account_view(MediAdminAccount $admin_view_account): void
    {
        $this->admin_view_account = $admin_view_account;
    }

    /**
     * @return array
     */
    public function medi_render_account_data() : array
    {
        return $this->model_account->render();
    }

    /**
     * @param $data_id
     * @return array
     */
    public function medi_render_account_data_by_id($data_id)
    {
        return $this->model_account->get_data($data_id);
    }

    /**
     * @param $data_id
     * @return array
     */
    public function medi_render_account_data_by_id_ajax() : void
    {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'medi_booking_nonce')) {
            wp_send_json_error(['message' => 'Ungültige Anfrage.']);
            return;
        }

        if ($_POST['data']) {
            parse_str($_POST['data'], $parsed_data);

            $data = $this->model_account->get_data($parsed_data['user-id']);

            if ($data) {
                wp_send_json_success([
                    'message' => 'Account wurde gefunden.',
                    'account' => $data,
                ]);
            } else {
                wp_send_json_error(['message' => 'Kein Account mit der angegebenen Kundennummer gefunden.']);
            }
        } else {
            wp_send_json_error(['message' => "Fehler aufgetreten."]);
        }
    }

    /**
     * @param $data_id
     * @return array
     */
    public function medi_render_account_data_by_mail_ajax() : void
    {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'medi_booking_nonce')) {
            wp_send_json_error(['message' => 'Ungültige Anfrage.']);
            return;
        }

        if ($_POST['data']) {
            parse_str($_POST['data'], $parsed_data);

            $data = $this->model_account->get_data_by_mail($parsed_data['mail']);

            if ($data) {
                wp_send_json_success([
                    'message' => 'Account wurde gefunden.',
                    'account' => $data,
                ]);
            } else {
                wp_send_json_error(['message' => 'Kein Account mit der angegebenen Mail - Adresse gefunden.']);
            }
        } else {
            wp_send_json_error(['message' => "Fehler aufgetreten."]);
        }
    }

    /**
     * @param $data_id
     * @return array
     */
    public function medi_render_account_data_by_username_ajax() : void
    {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'medi_booking_nonce')) {
            wp_send_json_error(['message' => 'Ungültige Anfrage.']);
            return;
        }

        if ($_POST['data']) {
            parse_str($_POST['data'], $parsed_data);

            $data = $this->model_account->get_data_by_username($parsed_data['username']);

            if ($data) {
                wp_send_json_success([
                    'message' => 'Account wurde gefunden.',
                    'account' => $data,
                ]);
            } else {
                wp_send_json_error(['message' => 'Kein Account mit dem angegebenen Benutzernamen gefunden.']);
            }
        } else {
            wp_send_json_error(['message' => "Fehler aufgetreten."]);
        }
    }

    /**
     * Set the subpages (Buchungen) for the plugin inside the WordPress backend
     *
     * @return void
     * @throws Exception
     */
    public function medi_add_account_subpages() : void
    {
        if (!isset($this->admin_view_account)) {
            throw new Exception("admin_view_account wurde nicht gesetzt!");
        }

        add_submenu_page(
            'admin-booking-overview',
            'Buchungs - Tool Besucher',
            'Besucher',
            'manage_options',
            'admin-account',
            [$this->admin_view_account, 'medi_admin_account_view_render']
        );
    }

    public function admin_account_insert_data() : void
    {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'medi_booking_nonce')) {
            wp_send_json_error(['message' => 'Ungültige Anfrage.']);
            return;
        }

        if (isset($_POST['data'])) {
            parse_str($_POST['data'], $parsed_data);

            if (empty($parsed_data['account_username']) || empty($parsed_data['account_mail'])) {
                wp_send_json_error(['message' => 'Pflichtfelder fehlen für den Account']);
                return;
            }

            $data = [
                'account_username' => sanitize_text_field($parsed_data['account_username']),
                'account_firstname' => sanitize_text_field($parsed_data['account_firstname']),
                'account_lastname' => sanitize_text_field($parsed_data['account_lastname']),
                'account_mail' => sanitize_email($parsed_data['account_mail']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Datensatz einfügen
            $return_id = $this->model_account->insert($data);

            if ($return_id !== false) {
                wp_send_json_success([
                    'message' => 'Account wurde erfolgreich angelegt.',
                    'account_id' => $return_id,
                ]);
            } else {
                wp_send_json_error(['message' => 'Fehler beim Einfügen des Accounts.']);
            }
        } else {
            wp_send_json_error(['message' => "Fehler aufgetreten."]);
        }
    }

    public function admin_account_delete_data() : void
    {
        if( isset($_POST['dataId']) ) {
            $account_id = sanitize_text_field($_POST['dataId']);

            $return = $this->model_account->delete_data($account_id);

            if ( $return === true ) {
                wp_send_json_success(['message' => "Account ID {$account_id} gelöscht."]);
            } else {
                wp_send_json_error(['message' => "Fehler aufgetreten."]);
            }
        }
    }

    public function admin_account_update_data() : void
    {
        if ( isset($_POST['account_username']) && isset($_POST['account_mail']) && isset($_POST['account_id']) ) {
            $account_id = sanitize_text_field($_POST['dataId']);

            $data = array(
                'account_username' => sanitize_text_field($_POST['account_username']),
                'account_firstname' => sanitize_text_field($_POST['account_firstname']),
                'account_lastname' => sanitize_text_field($_POST['account_lastname']),
                'account_mail' => sanitize_text_field($_POST['account_mail']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

           $return = $this->model_account->update($data, $account_id);

            if ( $return === true ) {
                wp_send_json_success(['message' => "Account ID {$account_id} geändert."]);
            } else {
                wp_send_json_success(['message' => "Fehler aufgetreten."]);
            }
        }
    }
}