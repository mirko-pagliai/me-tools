/**
 * This file is part of MeTools.
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
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