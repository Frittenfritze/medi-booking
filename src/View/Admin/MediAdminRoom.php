<?php

namespace Medi\View\Admin;

use Medi\Controller\MediControllerRoom;

class MediAdminRoom
{
    private MediControllerRoom $admin_controller_room;

    public function __construct(MediControllerRoom $admin_controller_room)
    {
        $this->admin_controller_room = $admin_controller_room;
    }

    /**
     * Render the admin view for settings and rooms
     *
     * @return void
     */
    public function medi_admin_room_view_render() : void
    {
        $room_table = $this->admin_room_view_table_render();
        $room_modal = $this->medi_admin_room_modal_render();

        $html = '
            <div class="wrap medi-booking-tool-admin">
                <h1 class="mb-2">Einstellungen</h1>
                ' . $room_table . '
                <button id="save_settings" class="button button-primary mt">Einstellungen speichern</button>
                <button id="show_room_modal" class="button button-primary mt">Raum anlegen</button>
                ' . $room_modal . '
            </div>';

        echo $html;
    }

    /**
     * Render table view for settings and rooms
     *
     * @return string
     */
    private function admin_room_view_table_render() : string
    {
        $data = $this->admin_controller_room->medi_render_room_data();

        $html = '';
        $html_room = '';
        $html_modal = '';

        foreach ( $data as $room ) {
            $html_modal .= $this->medi_admin_room_modal_render_edit($room->id);

            $html_room .= '
                    <tr class="iedit author-self level-0 post-1 type-post status-publish format-standard hentry category-Dummy category">
			            <th scope="row" class="check-column">
			                <label class="screen-reader-text" for="cb-select-1">' . $room->id . '</label>
			                <input id="cb-select-1" type="checkbox" name="post[]" value="1">
			                <div class="locked-indicator">
				                <span class="locked-indicator-icon" aria-hidden="true"></span>
			                    <span class="screen-reader-text">
					                ' . $room->room_name . '
				                </span>
			                </div>
			            </th>
			            <th>
			                ' . $room->id  . '
			            </th>
			            <th>
			                ' . $room->room_name . '
			            </th>
			            <th>
			                ' . $room->created_at . '
                        </th>
                        <th>
			                ' . $room->updated_at . '
                        </th>
			            <th data-id="' . $room->id . '" data-action="room">
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
			                Raum - ID
			            </th>
			            <th scope="col" class="manage-column column-title column-primary">
			                Raum - Name
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
                    ' . $html_room . '
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
    private function medi_admin_room_modal_render (int $id = 0) : string
    {
        return '
            <div id="add_room_modal" class="modal" style="display:none;">
                <div class="modal-content-box">
                    <span class="modal-close">&times;</span>
                    <h2 class="mb-2">Neuen Raum anlegen</h2>
                    <form id="room" method="post">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                            <label for="room_name">Raumname*</label>
                            <input type="text" name="room_name" class="form-control">
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
     * Renders booking accounts modal to add a new account
     *
     * @param int $id
     * @return string
     */
    private function medi_admin_room_modal_render_edit (int $id = 0) : string
    {
        $room = $this->admin_controller_room->medi_render_room_data_by_id($id);

        return '
            <div id="add_room_modal_' . $room->id . '" class="modal" style="display:none;">
                <div class="modal-content-box">
                    <span class="modal-close">&times;</span>
                    <h2 class="mb-2">Buchung ändern - #' . $room->id . '</h2>
                    <form id="room" method="post" data-id="' . $room->id . '">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                            <label for="room_name">Raumname*</label>
                            <input type="text" name="room_name" class="form-control" placeholder="' . $room->room_name . '">
                        </div>                                
                        <div class="d-flex justify-content-center mb-1">
                            <input type="submit" value="Speichern" class="button button-primary">
                        </div>
                        * Pflichtfelder
                    </form>
                </div>
            </div>';
    }
}