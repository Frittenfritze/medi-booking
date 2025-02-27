<?php

namespace Medi\Handler;

use Medi\View\Frontend\MediFrontendShortCode;
use Medi\View\Frontend\MediFrontendBlock;

use Medi\Controller\MediControllerBooking;
use Medi\Controller\MediControllerAccount;
use Medi\Controller\MediControllerRoom;
use Medi\View\Admin\MediAdminBooking;
use Medi\View\Admin\MediAdminAccount;
use Medi\View\Admin\MediAdminRoom;
use Medi\Model\MediBooking;
use Medi\Model\MediAccount;
use Medi\Model\MediRoom;

class MediController
{
    public MediFrontendShortCode $frontend_short_code;
    public MediFrontendBlock $frontend_block;
    public MediControllerBooking $admin_controller_booking;
    public MediControllerAccount $admin_controller_account;
    public MediControllerRoom $admin_controller_room;
    public MediAdminBooking $admin_view_booking;
    public MediAdminAccount $admin_view_account;
    public MediAdminRoom $admin_view_room;
    public MediBooking $model_booking;
    public MediAccount $model_account;
    public MediRoom $model_room;

    public function __construct()
    {
        global $wpdb;

        $this->model_booking = new MediBooking($wpdb);
        $this->model_account = new MediAccount($wpdb);
        $this->model_room = new MediRoom($wpdb);

        $this->admin_controller_booking = new MediControllerBooking($this->model_booking);
        $this->admin_controller_account = new MediControllerAccount($this->model_account);
        $this->admin_controller_room = new MediControllerRoom($this->model_room);

        $this->admin_view_booking = new MediAdminBooking($this->admin_controller_booking);
        $this->admin_view_account = new MediAdminAccount($this->admin_controller_account);
        $this->admin_view_room = new MediAdminRoom($this->admin_controller_room);

        $this->admin_controller_booking->medi_set_booking_view($this->admin_view_booking);
        $this->admin_controller_account->medi_set_account_view($this->admin_view_account);
        $this->admin_controller_room->medi_set_room_view($this->admin_view_room);

        $this->frontend_short_code = new MediFrontendShortCode($this->admin_controller_room);
        $this->frontend_block = new MediFrontendBlock();

        add_action('admin_menu', [$this->admin_controller_booking, 'medi_add_booking_page']);
        add_action('admin_menu', [$this->admin_controller_account, 'medi_add_account_subpages']);
        add_action('admin_menu', [$this->admin_controller_room, 'medi_add_room_subpage']);

        add_shortcode('booking_view', [$this->frontend_short_code, 'medi_shortcode_render']);
    }
}