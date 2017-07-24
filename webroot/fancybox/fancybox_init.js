/**
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
$(document).ready(function () {
    $(".fancybox").fancybox({
        closeBtn: false,
        prevEffect: 'none',
        nextEffect: 'none',
        margin: [ 10, 10, 0, 10 ],
        padding: 0,
        type : 'image',
        beforeShow: function () {
            /* Right click disabled by default */
            $.fancybox.wrap.bind("contextmenu", function () {
                return false;
            });
        },
        helpers: {
            thumbs: {
                width: 50,
                height: 50
            },
            title: {
                type: 'over'
            }
        }
    });
});