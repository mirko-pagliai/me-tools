/*!
 * This file is part of MeTools.
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 */

/**
 * Function to auto-submit a form.
 *
 * Example:
 * <select onchange="send_form(this)"></select>
 * @param {Object} element
 */
function send_form(element)
{
    $(element).closest('form').submit();
}

/**
 * Extended disable function.
 *
 * Example:
 * $(this).disable(true);
 * @see http://stackoverflow.com/a/16788240/1480263
 */
jQuery.fn.extend({
    disable: function (state) {
        return this.each(function () {
            if ($(this).is('input, button')) {
                $(this).prop('disabled', state);
            } else {
                $(this).toggleClass('disabled', state);
            }
        });
    }
});

/**
 * Closes a flash messages with an animation
 */
function close_flashMessage()
{
    $('.alert').animate({opacity: '0'}, '800', function () {
        $(this).slideUp('200');
    });
}

$(function () {
    /**
     * Enables tooltips, if there are elements that require them
     */
    if ($('[data-toggle~="tooltip"]').length) {
        $('[data-toggle~="tooltip"]').tooltip();
    }
    
    /**
     * Closes a flash message when clicking the close button
     */
    $('.alert .close').click(function () {
        close_flashMessage();
        //It requires "return false" to prevent the default behavior of jQuery
        return false;
    });

    /**
     * Submits button will be disabled when the form is submitted.
     */
    $('form').submit(function () {
        $(':submit', this).disable(true);
    });

    /**
     * Change the visibility of some elements.
     * Elements with "to-be-shown" class will be shown, while elements with "to-be-hidden" class will be hidden
     */
    $('.hidden.to-be-shown:hidden').removeClass('hidden to-be-shown');
    $('.to-be-hidden:visible').addClass('hidden').removeClass('to-be-hidden');

    /**
     * Recapcha mail links.
     * It opens the popup
     */
    $('.recaptcha-mail').click(function (event) {
        event.preventDefault();

        window.open($(this).attr('href'), '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300');

        return false;
    });

    /**
     * Closes automatically the flash messages after a preset time
     */
    setTimeout(close_flashMessage, 3500);
});