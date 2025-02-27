<?php
namespace Medi\View\Frontend;

use Medi\Controller\MediControllerRoom;
use Medi\Model\MediRoom;

class MediFrontendShortCode
{
    private MediControllerRoom $admin_controller_room;

    public function __construct(?MediControllerRoom $admin_controller_room = null)
    {
        global $wpdb;

        $this->admin_controller_room = $admin_controller_room ?? new MediControllerRoom(new MediRoom($wpdb));
    }

    public function medi_shortcode_render() : string
    {
        $data = $this->admin_controller_room->medi_render_room_data();

        $step_one = $this->medi_shortcode_render_step_one();
        $step_two = $this->medi_shortcode_render_step_two();
        $step_three = $this->medi_shortcode_render_step_three();

        if (is_array($data) && !empty($data)) {
            return '
                <section class="medi-booking-tool">
                    ' . $step_one . '
                    ' . $step_two . '
                    ' . $step_three . '
                </section> 
            ';
        } else {
            if (is_user_logged_in()) {
                return '<section class="medi-booking-tool">Bitte legen Sie zuerst Ihre Raumdaten im Admin Bereich an.</section>';
            } else {
                return '';
            }
        }
    }

    private function medi_shortcode_render_step_one() : string
    {
        return '
            <div id="step-one">
                <div class="h5 mb-1">Wie lange brauchen Sie ihren Raum?</div>
                <div class="d-flex gap-2">
                    <div class="d-flex">
                        <input type="radio" class="form-radio" name="time" id="radio_day" value="day" />
                        <label for="radio_day">Für einen Tag</label>
                    </div>
                    <div class="d-flex">
                        <input type="radio" class="form-radio" name="time" id="radio_hour" value="hour" />
                        <label for="radio_hour">Für einige Stunden</label>
                    </div>
                </div>
                <div id="day" class="d-none mt-2">
                    <div class="h5 mb-1">Wie viele Stunden brauchen Sie den Raum?</div>
                    <div class="form-select-container w-50">
                        <select class="form-select w-100" name="time_ranche">
                            <option value="0">Wähle Sie ihre Dauer aus.</option>
                            <option value="1">1 Stunde</option>
                            <option value="2">2 Stunde</option>
                            <option value="3">3 Stunde</option>
                            <option value="4">4 Stunde</option>
                            <option value="5">5 Stunde</option>
                            <option value="6">6 Stunde</option>
                            <option value="7">7 Stunde</option>
                            <option value="8">8 Stunde</option>
                        </select>     
                    </div>
                </div>
                <div id="time-box" class="w-50 flex-wrap mt-2 d-none"></div>
                <div class="d-flex mt-2 justify-content-end gap-1">
                    <div id="btn-step-two" class="btn btn-secondary forward inactive">
                        weiter           
                    </div>
                </div>
            </div>
        ';
    }

    private function medi_shortcode_render_step_two() : string
    {
        return '
            <div id="step-two">
                <div class="d-flex justify-content-end"> 
                    <div class="d-flex calender-nav gap-1">
                        <div class="btn btn-primary active" data-id="month">Monat</div>
                        <div class="btn btn-primary" data-id="week">Woche</div> 
                        <div class="btn btn-primary" data-id="list">Liste</div>
                    </div>
                </div>
                <div class="medi-booking-tool-content">    
                    <div id="month" class="display"></div>   
                    <div id="week" class="display">
                        <div class="d-flex justify-content-between">
                            <div id="week-prev"><</div>
                            <div id="week-title"></div>
                            <div id="week-next">></div>
                        </div>
                        <div class="d-flex justify-content-between weekdays">
                            <div class="text-center text-bolder">Mo</div>
                            <div class="text-center text-bolder">Di</div>
                            <div class="text-center text-bolder">Mi</div>
                            <div class="text-center text-bolder">Do</div>
                            <div class="text-center text-bolder">Fr</div>
                            <div class="text-center text-bolder">Sa</div>
                            <div class="text-center text-bolder">So</div>
                        </div>
                        <div id="week-days" class="d-flex justify-content-between"></div>
                    </div>
                    <div id="list" class="display">
                    </div>
                </div>
                <div class="d-flex mt-2 justify-content-end gap-1">
                    <div class="btn btn-secondary back" data-id="step-one">
                        zurück
                    </div>
                    <div id="btn-step-three" class="btn btn-secondary forward inactive">
                        weiter           
                    </div>
                </div>
            </div>
        ';
    }

    private function medi_shortcode_render_step_three() : string
    {
        return '
            <div id="step-three">
                <div class="h5 mb-1">Eingabe Ihrer Benutzerdaten</div>
                <p>Wenn Sie bereits ein Nutzerkonto haben, können Sie einfach ihren Benutzernamen eingeben und das Formular fühlt sich automatisch.</p>
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex flex-column">
                        <label for="booking-notice" class="w-100">Buchungsgrund / -notiz*</label>
                        <textarea class="form-text w-100 mt-1" rows="5" name="booking-notice" placeholder="Buchungsgrund / -notiz*" required></textarea>
                    </div>
                    <div class="d-flex justify-content-between">
                        <label for="username">Benutzername*</label>
                        <input type="text" class="form-text w-50" name="username" placeholder="Benutzername*" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <label for="mail">Mail - Adresse*</label>
                        <input type="email" class="form-text w-50" name="mail" placeholder="Mail*" required>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <label for="firstname">Vorname</label>
                        <input type="text" class="form-text w-50" name="firstname" placeholder="Vorname">
                    </div>
                    <div class="d-flex justify-content-between">
                        <label for="lastname">Nachname</label>
                        <input type="text" class="form-text w-50" name="lastname" placeholder="Nachname">
                    </div>
                    <input type="hidden" name="account_id">
                </div>
                <div class="d-flex mt-2 justify-content-end gap-1">
                    <div class="btn btn-secondary back" data-id="step-two">
                        zurück
                    </div>
                    <div id="btn-step-booking" class="btn btn-secondary forward inactive">
                        buchen   
                    </div>
                </div>
            </div>
        ';
    }
}