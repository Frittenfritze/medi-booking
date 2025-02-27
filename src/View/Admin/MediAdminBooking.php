<?php

namespace Medi\View\Admin;

use Medi\Controller\MediControllerBooking;

class MediAdminBooking
{
    private MediControllerBooking $admin_controller_booking;

    public function __construct(MediControllerBooking $admin_controller_booking)
    {
        $this->admin_controller_booking = $admin_controller_booking;
    }

    /**
     * Render the admin view for bookings
     *
     * @return void
     */
    public function medi_admin_booking_view_render() : void
    {
        $booking_table = $this->medi_admin_booking_view_table_render();
        $booking_modal = $this->medi_admin_booking_modal_render();

        $html = '
            <div class="wrap medi-booking-tool-admin">
                <h1>Buchungstool</h1>
                <p class="mb-2">Das Buchungs - TOOL hilft dir dabei, deine Buchungen und Räume zu verwalten und auch anzulegen</p>
                ' . $booking_table . '
                <button id="show_booking_modal" class="button button-primary mt">Neue Buchung</button>
                ' . $booking_modal . '
            </div>';

        echo $html;
    }

    /**
     * Render table view for bookings
     *
     * @return string
     */
    private function medi_admin_booking_view_table_render() : string
    {
        $data = $this->admin_controller_booking->medi_render_booking_data();

        $html = '';
        $html_booking = '';
        $html_modal = '';

        foreach ( $data as $booking ) {
            $html_modal .= $this->medi_admin_booking_modal_render_edit($booking->id);

            $html_booking .= '
                    <tr class="iedit author-self level-0 post-1 type-post status-publish format-standard hentry category-Dummy category">
			            <th scope="row" class="check-column">
			                <label class="screen-reader-text" for="cb-select-1">' . $booking->booking_date_end . '</label>
			                <input id="cb-select-1" type="checkbox" name="post[]" value="1">
			                <div class="locked-indicator">
				                <span class="locked-indicator-icon" aria-hidden="true"></span>
			                    <span class="screen-reader-text">
					                ' . $booking->booking_notice . '
				                </span>
			                </div>
			            </th>
			            <th>
			                ' . $booking->id . '
			            </th>
			            <th>
			                ' . $booking->booking_notice . '
			            </th>
			            <th>
			                ' . date( 'd.m.Y - H:i', strtotime($booking->booking_date_start)) . '
			            </th>
			            <th>
			                ' . date( 'd.m.Y - H:i', strtotime($booking->booking_date_end)) . '        
			            </th>
			            <th>
			                ' . $booking->account_id . '        
			            </th>
			            <th>
			                ' . $booking->room_id . '        
			            </th>
			            <th>
			                ' . $booking->created_at . '
                        </th>
                        <th>
			                ' . $booking->updated_at . '
                        </th>
                        <th data-id="' . $booking->id . '" data-action="booking">
                            <button class="button-edit">
                                <span class="dashicons dashicons-edit"></span>
                            </button>
                            <button class="button-delete">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </th>
			        </tr>';
        }

        $html .= '
            <div class="alert-success"></div>
			<div class="alert-danger"></div>
            <table id="medi-api-tool-table" class="wp-list-table widefat fixed striped posts mb-2">
			    <thead>
			        <tr>
			            <td id="cb" class="manage-column column-cb check-column">
			                <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
			                <input id="cb-select-all-1" type="checkbox">
			            </td>
			            <th scope="col" class="manage-column column-title column-primary">
			                Buchungs - ID
			            </th>
			            <th scope="col" class="manage-column column-title column-primary">
                            Buchungs - Notiz
			            </th>
			            <th scope="col" class="manage-column column-title column-primary">
                            Buchungs - Startzeit
			            </th>
			            <th scope="col" class="manage-column column-title column-primary">
                            Buchungs - Endzeit
			            </th>
			            <th scope="col" class="manage-column column-title column-primary">
                            Besucher - ID
			            </th>
			            <th scope="col" class="manage-column column-title column-primary">
                            Raum - ID
			            </th>
			            <th scope="col" class="manage-column column-date">
				            Erstellt
			            </th>
			            <th scope="col" class="manage-column column-date">
			                Update
			            </th>
			            <th scope="col" class="manage-column column-date">
			            </th>
			        </tr>
			    </thead>
                <tbody id="the-list">
                    ' . $html_booking . '
                </tbody>
            </table>' . $html_modal ;

        return $html;
    }

    /**
     * Renders booking accounts modal to add a new account
     *
     * @param int $id
     * @return string
     */
    private function medi_admin_booking_modal_render () : string
    {
        return '
            <div id="add_booking_modal" class="modal" style="display:none;">
                <div class="modal-content-box">
                    <span class="modal-close">&times;</span>
                    <h2 class="mb-2">Neuen Besucher anlegen</h2>
                    <form id="booking" method="post">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                            <label for="booking_notice">Buchungsnotiz</label>
                            <input type="text" name="booking_notice" class="form-control">
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">              
                            <label for="booking_date_start">Buchungsstart*</label>
                            <input type="text" name="booking_date_start" class="form-control">
                        </div> 
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                            <label for="booking_date_end">Buchungsende</label>
                            <input type="text" name="booking_date_end" class="form-control">
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">              
                            <label for="account_id">Besucher ID</label>
                            <input type="text" name="account_id" class="form-control">
                        </div>    
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">              
                            <label for="room_id">Raum ID</label>
                            <input type="text" name="room_id" class="form-control">
                        </div>           
                        <div class="d-flex justify-content-center mb-1">
                            <input type="submit" value="Speichern" class="button button-primary">
                        </div>
                        * Pflichtfelder
                    </form>
                </div>
            </div>';
    }

    /**
     * Render edit modal for bookings
     *
     * @param int $id The ID of the booking to be edited.
     * @return string Rendered HTML content for the booking edit modal.
     */
    private function medi_admin_booking_modal_render_edit (int $id) : string
    {
        $booking = $this->admin_controller_booking->medi_render_booking_data_by_id($id);

        return '
            <div id="add_booking_modal_' . $booking->id . '" class="modal" style="display:none;"  data-id="' . $booking->id . '">
                <div class="modal-content-box">
                    <span class="modal-close">&times;</span>
                    <h2 class="mb-2">Buchung ändern - #' . $booking->id . '</h2>
                    <form id="' . $booking->id . '" method="post">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                            <label for="booking_notice">Buchungsnotiz</label>
                            <input type="text" name="booking_notice" class="form-control" value="' . $booking->booking_notice . '">
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">              
                            <label for="booking_date_start">Buchungsstart*</label>
                            <input type="text" name="booking_date_start" class="form-control" value="' . date( 'd.m.Y - H:i', strtotime($booking->booking_date_start)) . '">
                        </div> 
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                            <label for="booking_date_end">Buchungsende</label>
                            <input type="text" name="booking_date_end" class="form-control" value="' . date( 'd.m.Y - H:i', strtotime($booking->booking_date_end)) . '">
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">              
                            <label for="account_id">Besucher ID</label>
                            <input type="text" name="account_id" class="form-control" value="' . $booking->account_id . '">
                        </div>    
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">              
                            <label for="room_id">Raum ID</label>
                            <input type="text" name="room_id" class="form-control" value="' . $booking->room_id . '">
                        </div>           
                        <div class="d-flex justify-content-center mb-1">
                            <input type="hidden" name="booking_id" value="' . $booking->id . '">
                            <input type="submit" value="Speichern" class="button button-primary">
                        </div>
                        * Pflichtfelder
                    </form>
                </div>
            </div>';
    }
}