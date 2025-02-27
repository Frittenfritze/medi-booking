jQuery(document).ready( function($) {
    modalShow('booking_modal');
    modalClose('booking_modal');

    modalShow('account_modal');
    modalClose('account_modal');

    modalShow('room_modal');
    modalClose('room_modal');

    ajaxInsertDataForm('#booking' , 'booking')
    ajaxInsertDataForm('#account' , 'account')
    ajaxInsertDataForm('#room' , 'room')

    ajaxDeleteData()

    $('.modal').each(function () {
        if ( $(this).attr("data-id") ) {
            ajaxInsertDataForm('#' + $(this).attr("data-id"), 'booking')
        }
    })

    $('.button-edit').each(function () {
        $(this).on('click', function() {
            let modal_id = $(this).parent().data('id');

            modalShowEdit('booking_modal_' + modal_id);
            modalClose('booking_modal_' + modal_id);

            modalShowEdit('account_modal_' + modal_id);
            modalClose('account_modal_' + modal_id);

            modalShowEdit('room_modal_' + modal_id);
            modalClose('room_modal_' + modal_id);
        })
    })
})

function modalShow (modalName)
{
    jQuery('#show_' + modalName).on('click', function() {
        jQuery('#add_' + modalName).show();
    });
}

function modalShowEdit (modalName)
{
    jQuery('#add_' + modalName).show();
}

function modalClose (modalName)
{
    jQuery('.modal-close').on('click', function() {
        jQuery('#add_' + modalName).hide();
    });
}