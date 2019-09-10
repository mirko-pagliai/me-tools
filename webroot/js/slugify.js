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

(function ($) {
    $.fn.extend({
        slugify(source, target) {
            //Sets the default values, if necessary
            source = typeof source !== "undefined" ? source : "form #title";
            target = typeof target !== "undefined" ? target : "form #slug";

            /**
             * Gets a slug from a string
             */
            function getSlug(str)
            {
                str = str.toLowerCase(); //Lowercase
                //Removes accents, swap Ã± for n, etc
                var from = "àáäâèéëêìíïîıòóöôùúüûñç·/_,:;";
                var to = "aaaaeeeeiiiiioooouuuunc------";

                for (var i = 0, l = from.length; i < l; i++) {
                    str = str.replace(new RegExp(from.charAt(i), "g"), to.charAt(i));
                }

                return str.replace(/[^a-z0-9 -]/g, "") //Removes invalid chars
                .replace(/^\s+|\s+$/g, "") //Trim
                .replace(/\s+/g, "-") //Collapses whitespace and replace by -
                .replace(/-+/g, "-"); //Collapses dashes
            }

            //When changing the source or the target (the slug field)
            $(source).add(target).change(function () {
                //If the target (the slug field) is empty and the source is set, sets the target from the source
                if (!$(target).val().length && $(source).val().length) {
                    $(target).val(getSlug($(source).val()));
                //Else, if the target isn't empty, sets the target from himself
                } else {
                    $(target).val(getSlug($(target).val()));
                }
            });
        }
    });
})(jQuery);