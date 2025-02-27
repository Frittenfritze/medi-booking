/**
 * Set save array´s
 *
 * @type {{}}
 */
let booking_data = {};
let account_data = {};

jQuery(document).ready(function($) {
    let body_tag = $('body');

    /**
     * Buttons
     *
     * @type {jQuery|HTMLElement|*}
     */
    let btn_step_two = $('#btn-step-two');
    let btn_step_three = $('#btn-step-three');
    let btn_step_booking = $('#btn-step-booking');

    /**
     * Time variables
     *
     * @type {Date}
     */
    let currentDate = new Date();
    let today = new Date();

    /**
     * Inputfields
     *
     * @type {jQuery|HTMLElement|*}
     */
    let username_input = $('input[name="username"]');
    let firstname_input = $('input[name="firstname"]');
    let lastname_input = $('input[name="lastname"]');
    let mail_input = $('input[name="mail"]');
    let notice_input = $('textarea[name="booking-notice"]');
    let account_id = $('input[name="account_id"]');
    let time_input = $('input[name="time"]');
    let time_input_ranche = $('select[name="time_ranche"]');

    /**
     * call init functions
     */
    updateWeekView("#week-days"); // Initiale Anzeige der aktuellen Woche
    updateWeekView("#list"); // Initiale Anzeige der aktuellen Woche

    /**
     * Next steps button events
     */
    btn_step_two.on('click', function () {
        let value_time = '';
        let value_duration = time_input_ranche.val();

        $('.time-selector').each(function () {
            if ($(this).hasClass('active')) {
                value_time = $(this).attr('value');
            }
        })

        booking_data['time'] = value_time;
        booking_data['duration'] = value_duration;
    })

    btn_step_three.on('click', function () {
        $(".day").each(function () {
            if($(this).hasClass('active')) {
                if (booking_data['time'] !== '') {
                    let day = $(this).attr('day');
                    let time = booking_data['time'];
                    let date_start = new Date(day);
                    let date_end = new Date(day);

                    let [hours, minutes] = time.split(':');

                    let hours_start = parseInt(hours) + 1;
                    let hours_end = hours_start + parseInt(booking_data['duration']);

                    date_start.setHours(hours_start, parseInt(minutes), 0, 0)
                    date_end.setHours(hours_end, parseInt(minutes), 0, 0)

                    booking_data['booking_date_start'] = date_start.toISOString();
                    booking_data['booking_date_end'] = date_end.toISOString();
                } else {
                    booking_data['booking_date_start'] = $(this).attr('day');
                    booking_data['booking_date_end'] = $(this).attr('day');
                }
            }
        });
    })

    btn_step_booking.on('click', function () {
        booking_data['account_username'] = username_input.val();
        booking_data['account_firstname'] = firstname_input.val();
        booking_data['account_lastname'] = lastname_input.val();
        booking_data['account_mail'] = mail_input.val();
        booking_data['booking_notice'] = notice_input.val();
        booking_data['room_id'] = 1;

        if (account_id.val() !== '') {
            booking_data['account_id'] = account_id.val();

            // Jetzt die zweite Funktion ausführen
            ajaxInsertDataFrontend(booking_data, 'booking');

            alert('Buchung erfolgreich abgeschlossen.');

            window.location.reload();
        } else {
            $.when(ajaxInsertDataFrontend(booking_data, 'account')).done(function(response) {
                // Nehmen wir an, die Funktion gibt die account_id zurück
                booking_data['account_id'] = response.data.account_id;

                // Jetzt die zweite Funktion ausführen
                ajaxInsertDataFrontend(booking_data, 'booking');

                alert('Buchung erfolgreich abgeschlossen.');

                window.location.reload();
            });
        }
    })

    /**
     * Input and Changes Events
     */
    body_tag.on('input', username_input, function () {
        account_data['username'] = username_input.val();

        if ( account_data['username'] !== '') {
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'account_get_data_by_username_action',
                    _ajax_nonce: ajax_object.nonce,
                    data: jQuery.param(account_data),
                },
                success: function (response) {
                    if (response.success) {
                        let account_data = response.data.account;

                        username_input.val(account_data.account_username);
                        firstname_input.val(account_data.account_firstname);
                        lastname_input.val(account_data.account_lastname);
                        mail_input.val(account_data.account_mail);
                        account_id.val(account_data.id);

                        btn_step_booking.removeClass('inactive');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('AJAX-Fehler: ', textStatus, errorThrown);
                }
            });
        }
    })

    body_tag.on('change', time_input,function () {
        let value_time = $('input[name="time"]:checked').val();

        if ( value_time === 'day' ) {
            btn_step_two.removeClass('inactive');

            $('#day').addClass('d-none');
            $("#time-box").removeClass('d-flex').addClass('d-none');
        } else if ( value_time === 'hour') {
            btn_step_two.addClass('inactive');

            $('#day').removeClass('d-none');

            $("#time-box").empty().removeClass('d-none').addClass('d-flex');

            body_tag.on('change', time_input_ranche, function () {
                let value_selected = $('select[name="time_ranche"]').val();

                let end_time = 18 - value_selected;
                let string_endtime = end_time.toString() + ":00";

                const timeArray = generateTimeArray("08:00", string_endtime, 30);

                $("#time-box").empty().addClass('show');

                timeArray.forEach((time) => {
                    $("#time-box").append(`<div class="w-50 time-selector text-center" value="${time}">${time}</div>`);
                });
            });
        }
    });

    body_tag.on('change', notice_input, function () {
        if(mail_input.val() !== '' && username_input.val() !== '') {
            btn_step_booking.removeClass('inactive');
        } else {
            btn_step_booking.addClass('inactive');
        }
    })

    body_tag.on('change', username_input, function () {
        if(mail_input.val() !== '' && notice_input.val() !== '') {
            btn_step_booking.removeClass('inactive');
        } else {
            btn_step_booking.addClass('inactive');
        }
    });

    body_tag.on('change', mail_input, function () {
        if(username_input.val() !== '' && notice_input.val() !== '') {
            btn_step_booking.removeClass('inactive');
        } else {
            btn_step_booking.addClass('inactive');
        }
    });

    /**
     * Body Click Events
     */
    body_tag.on('click', '.forward', function () {
        let switch_id = $(this).attr('id');

        if (switch_id === 'btn-step-two') {
            $('#step-one').hide();
            $('#step-two').show();
        }

        if (switch_id === 'btn-step-three') {
            $('#step-two').hide();
            $('#step-three').show();
        }
    })


    body_tag.on('click', '.back', function () {
        let switch_id = $(this).data('id');

        if (switch_id === 'step-one') {
            $('#step-one').show();
            $('#step-two').hide();
        }

        if (switch_id === 'step-two') {
            $('#step-two').show();
            $('#step-three').hide();
        }
    })

    body_tag.on('click', '.back', function () {
        let switch_id = $(this).data('id');

        if (switch_id === 'step-one') {
            $('#step-one').show();
            $('#step-two').hide();
        }

        if (switch_id === 'step-two') {
            $('#step-two').show();
            $('#step-three').hide();
        }
    })

    body_tag.on('click', '.time-selector', function () {
        $('.time-selector').each(function () {
            $(this).removeClass('active');
        })

        $(this).addClass('active');

        btn_step_two.removeClass('inactive');
    })

    body_tag.on('click', '.day', function () {
        $('.day').each(function () {
            $(this).removeClass('active');
        })

        $(this).addClass('active');

        btn_step_three.removeClass('inactive');
    })

    body_tag.on('click', '#week-prev', function () {
        let tempDate = new Date(currentDate);

        tempDate.setDate(tempDate.getDate() - 7);

        if (tempDate >= today) {
            currentDate = tempDate;

            updateWeekView("#week-days", currentDate);
        }
    });

    body_tag.on('click', '#week-next', function () {
        currentDate.setDate(currentDate.getDate() + 7);

        updateWeekView("#week-days", currentDate);
    });

    /**
     * Calender functions
     */
    $('#month').on('zabuto:calendar:day', function(e) {
        let now = new Date();
        $('.zabuto-calendar__event').each(function() {
            if($(this).hasClass('active')) {
                $(this).removeClass('active').attr('day', '');
            }
        });

        if (e.today) {
            if (!$(e.element).hasClass('fully-booked')) {
                $(e.element).addClass('active').attr('day', e.date.toDateString());
            }
        } else if (e.date.getTime() > now.getTime()) {
            if (!$(e.element).hasClass('fully-booked')) {
                $(e.element).addClass('active').attr('day', e.date.toDateString());
            }
        }

        btn_step_three.removeClass('inactive');
    });

    $("#month").zabuto_calendar({
        header_format: '[month] [year]',
        show_days: true,
        ajax: {
            url: ajax_object.ajax_url,
            success: function (response) {

            },
            error: function () {
                console.error('Ajax Anfrage fehlgeschlagen.');
            }
        },
        navigation_markup: {
            prev: '<div class="prev"></div>',
            next: '<div class="next"></div>'
        },
        translation: {
            "months" : {
                "1":"Januar",
                "2":"Februar",
                "3":"März",
                "4":"April",
                "5":"Mai",
                "6":"Juni",
                "7":"Juli",
                "8":"August",
                "9":"September",
                "10":"Oktober",
                "11":"November",
                "12":"Dezember"
            },
            "days" : {
                "0":"So",
                "1":"Mo",
                "2":"Di",
                "3":"Mi",
                "4":"Do",
                "5":"Fr",
                "6":"Sa"
            }
        }
    });

    $('.calender-nav .btn').each(function () {
        $(this).on('click', function () {
            let switch_id = $(this).data('id');

            $('.calender-nav .btn').each(function () {
                $(this).removeClass('active');
            });

            $(this).addClass('active');

            $('.display').each(function () {
                $(this).hide();
            });

            $('#' + switch_id).show();
        })
    })
});