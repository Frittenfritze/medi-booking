<?php

namespace Medi\View\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Medi\Controller\MediControllerAccount;

class MediAdminAccount
{
    private MediControllerAccount $admin_controller_account;

    public function __construct(MediControllerAccount $admin_controller_account)
    {
        $this->admin_controller_account = $admin_controller_account;
    }

    /**
     * Render the admin view for accounts
     *
     * @return void
     */
    public function medi_admin_account_view_render() : void
    {
        $account_table = $this->medi_admin_account_view_table_render();
        $account_modal = $this->medi_admin_account_modal_render();

        $html = '
            <div class="wrap medi-booking-tool-admin">
                <h1 class="mb-2">Besuchervewaltung</h1>
                ' . $account_table . '
                <button id="show_account_modal" class="button button-primary mt">Neuer Besucher</button>
                ' . $account_modal . '
            </div>';

        echo $html;
    }

    /**
     * Render table view for accounts
     *
     * @return string
     */
    private function medi_admin_account_view_table_render() : string
    {
        $data = $this->admin_controller_account->medi_render_account_data();

        $html = '';
        $html_account = '';
        $html_modal = '';

        foreach ( $data as $account ) {
            $html_modal .= $this->medi_admin_account_modal_render_edit($account->id);

            $html_account .= '
                    <tr class="iedit author-self level-0 post-1 type-post status-publish format-standard hentry category-Dummy category">
			            <th scope="row" class="check-column">
			                <label class="screen-reader-text" for="cb-select-1">' . $account->id . '</label>
			                <input id="cb-select-1" type="checkbox" name="post[]" value="1">
			                <div class="locked-indicator">
				                <span class="locked-indicator-icon" aria-hidden="true"></span>
			                    <span class="screen-reader-text">
					                ' . $account->id . '
				                </span>
			                </div>
			            </th>
			            <th>
			                ' . $account->id . '
			            </th>
			            <th>
			                ' . $account->account_username . '
			            </th>
			            <th>
			                ' . $account->account_firstname . '
			            </th>
			            <th>
			                ' . $account->account_lastname . '        
			            </th>
			            <th>
			                ' . $account->account_mail . '        
			            </th>
			            <th>
			                ' . $account->created_at . '
                        </th>
                        <th>
			                ' . $account->updated_at . '
                        </th>
                        <th data-id="' . $account->id . '" data-action="account">
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
            <table id="medi-account-table" class="wp-list-table widefat fixed striped posts mb-2">
			    <thead>
			        <tr>
			            <td id="cb" class="manage-column column-cb check-column">
			                <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
			                <input id="cb-select-all-1" type="checkbox">
			            </td>
			            <th scope="col" class="manage-column column-title column-primary">
                            Besucher - ID
			            </th>
			            <th scope="col" class="manage-column column-title column-primary">
                            Besuchername
			            </th>
			            <th scope="col" class="manage-column column-title column-primary">
                            Besucher - Vorname
			            </th>
			            <th scope="col" class="manage-column column-title column-primary">
                            Besucher - Nachname
			            </th>
			            <th scope="col" class="manage-column column-title column-primary">
                            Besucher - Mail
			            </th>
			            <th scope="col" class="manage-column column-date">
				            Erstellt
			            </th>
			            <th scope="col" class="manage-column column-date">
			                Update
			            </th>
			            <th scope="col" class="manage-column column-settings">
			            </th>
			        </tr>
			    </thead>
                <tbody id="the-list">
                    ' . $html_account . '
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
    private function medi_admin_account_modal_render (int $id = 0) : string
    {
        return '
            <div id="add_account_modal" class="modal" style="display:none;">
                <div class="modal-content-box">
                    <span class="modal-close">&times;</span>
                    <h2 class="mb-2">Neuen Besucher anlegen</h2>
                    <form id="account" method="post" data-id="' . $id . '">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                            <label for="account_firstname">Besucher Username*</label>
                            <input type="text" name="account_username" class="form-control">
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">              
                            <label for="account_mail">Besucher Mail Adresse*</label>
                            <input type="text" name="account_mail" class="form-control">
                        </div> 
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                            <label for="account_firstname">Besucher Vorname</label>
                            <input type="text" name="account_firstname" class="form-control">
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">              
                            <label for="account_lastname">Besucher Nachname</label>
                            <input type="text" name="account_lastname" class="form-control">
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
     * Renders the modal for editing an existing account.
     *
     * @param int $id The unique identifier of the account to be edited.
     * @return string The HTML content for the edit account modal.
     */
    private function medi_admin_account_modal_render_edit (int $id) : string
    {
        $account = $this->admin_controller_account->medi_render_account_data_by_id($id);

        return '
            <div id="add_account_modal_' . $account->id . '" class="modal" style="display:none;">
                <div class="modal-content-box">
                    <span class="modal-close">&times;</span>
                    <h2 class="mb-2">Besucher ändern - #' . $account->id . '</h2>
                    <form id="account" method="post">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                            <label for="account_firstname">Besucher Username*</label>
                            <input type="text" name="account_username" class="form-control" placeholder="' . $account->account_username . '">
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">              
                            <label for="account_mail">Besucher Mail Adresse*</label>
                            <input type="text" name="account_mail" class="form-control" placeholder="' . $account->account_mail . '">
                        </div> 
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                            <label for="account_firstname">Besucher Vorname</label>
                            <input type="text" name="account_firstname" class="form-control" placeholder="' . $account->account_firstname . '">
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">              
                            <label for="account_lastname">Besucher Nachname</label>
                            <input type="text" name="account_lastname" class="form-control" placeholder="' . $account->account_lastname . '">
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