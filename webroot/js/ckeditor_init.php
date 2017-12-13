<?php
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

$baseDir = dirname(dirname(getenv('SCRIPT_NAME')));
?>
$(function () {
    $('.editor.wysiwyg').each(function () {
        CKEDITOR.on('dialogDefinition', function (ev) {
            var dialogName = ev.data.name;
            var dialogDefinition = ev.data.definition;

            if (dialogName == 'table') {
                var advanced = dialogDefinition.getContents('advanced');
                var info = dialogDefinition.getContents('info');

                advanced.get('advCSSClasses')['default'] = 'table'; //Default cell classes
                info.get('txtWidth')['default'] = '100%'; //Default width
                info.get('txtBorder')['default'] = '0'; //Default border
                info.get('txtCellSpace')['default'] = '0'; //Default cell spacing
                info.get('txtCellPad')['default'] = '0'; //Default cell padding
            }
        });
        
        CKEDITOR.replace(this.id, {
            bodyClass: 'article p-3',
            contentsCss: [
                /**
                 * You can add several css files so that the editor style is the same as the article preview
                 */
                '<?= $baseDir ?>/vendor/bootstrap/css/bootstrap.min.css',
                '<?= $baseDir ?>/me_cms/css/layout.css',
                '<?= $baseDir ?>/me_cms/css/contents.css',
                //'<?= $baseDir ?>/css/layout.css',
                //'<?= $baseDir ?>/css/contents.css',
            ],
            disableNativeSpellChecker: false,
            fontSize_sizes: '10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;30/30px;',
            height: 375,
            image2_alignClasses: [ 'float-left', 'text-center', 'float-right' ],
            insertpre_class: false,
            removeButtons: 'Font',
            removeDialogTabs: false,
            removePlugins: 'divarea',
            tabSpaces: 4,
            toolbarCanCollapse: true,
            toolbarGroups: [
                { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
                { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
                { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },
                { name: 'links' },
                { name: 'insert' },
                { name: 'forms' },
                { name: 'tools' },
                { name: 'others' },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
                { name: 'styles' },
                { name: 'colors' },
                { name: 'about' }
            ],

            /**
             * To use KCFinder, you have to comment out these lines and indicate the position of KCFinder
             */
            //filebrowserBrowseUrl: '<?= $baseDir ?>/vendor/kcfinder/browse.php?type=files',
            //filebrowserImageBrowseUrl: '<?= $baseDir ?>/vendor/kcfinder/browse.php?type=images',
            //filebrowserUploadUrl: '<?= $baseDir ?>/vendor/kcfinder/upload.php?type=files',
            //filebrowserImageUploadUrl: '<?= $baseDir ?>/vendor/kcfinder/upload.php?type=images',
        });
    });
});
