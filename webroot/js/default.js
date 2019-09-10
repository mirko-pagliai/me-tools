/*!
 * This file is part of me-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Function to auto-submit a form.
 *
 * Example:
 * <select onchange="sendForm(this)"></select>
 * @param {Object} element
 */
function sendForm(element)
{
    $(element).closest("form").submit();
}

/**
 * Extended disable function.
 *
 * Example:
 * $(this).disable(true);
 * @see http://stackoverflow.com/a/16788240/1480263
 */
jQuery.fn.extend({
    disable(state) {

        return this.each(function () {
            if ($(this).is("input, button")) {
                $(this).prop("disabled", state);
            } else {
                $(this).toggleClass("disabled", state);
            }
        });
    }
});

/**
 * Closes a flash messages with an animation
 */
function closeFlashMessage()
{
    $(".alert").animate({opacity: "0"}, "800", function () {
        $(this).slideUp("200");
    });
}

$(function () {
    /**
     * Enables tooltips, if there are elements that require them
     */
    if ($("[data-toggle~=\"tooltip\"]").length) {
        $("[data-toggle~=\"tooltip\"]").tooltip();
    }

    /**
     * Submits button will be disabled when the form is submitted.
     */
    $("form").submit(function () {
        $(":submit", this).disable(true);
    });

    /**
     * Change the visibility of some elements.
     * Elements with "to-be-shown" class will be shown, while elements with "to-be-hidden" class will be hidden
     */
    $(".hidden.to-be-shown:hidden").removeClass("d-none to-be-shown");
    $(".to-be-hidden:visible").addClass("d-none").removeClass("to-be-hidden");
});