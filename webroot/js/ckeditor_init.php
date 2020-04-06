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
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('WEBROOT')) {
    $scriptFilename = getenv('SCRIPT_FILENAME');
    define('WEBROOT', substr($scriptFilename, 0, strrpos($scriptFilename, DS . 'webroot' . DS)) . DS . 'webroot' . DS);
}
if (!defined('BASEDIR')) {
    $scriptName = getenv('SCRIPT_NAME');
    define('BASEDIR', substr($scriptName, 0, strrpos($scriptName, '/webroot/')));
}
?>
$(function () {
    $('.editor.wysiwyg').each(function () {
        CKEDITOR.on('dialogDefinition', function (ev) {
            var dialogName = ev.data.name;
            var dialogDefinition = ev.data.definition;

            if (dialogName == 'iframe') {
                var advanced = dialogDefinition.getContents('advanced');

                advanced.get('advCSSClasses')['default'] = 'embed-responsive embed-responsive-item'; //Default iframe classes
            }

            if (dialogName == 'table') {
                var advanced = dialogDefinition.getContents('advanced');
                var info = dialogDefinition.getContents('info');

                advanced.get('advCSSClasses')['default'] = 'table table-responsive'; //Default table classes
                info.get('txtWidth')['default'] = '100%'; //Default width
                info.get('txtBorder')['default'] = '0'; //Default border
                info.get('txtCellSpace')['default'] = '0'; //Default cell spacing
                info.get('txtCellPad')['default'] = '0'; //Default cell padding
            }
        });

        CKEDITOR.replace(this.id, {
            autoGrow_bottomSpace: 0,
            autoGrow_maxHeight: 550,
            autoGrow_onStartup: true,
            bodyClass: 'article p-3',
            contentsCss: [
                /**
                 * These are the css files that will be loaded into the editor.
                 * In this way, the style applied within the editor will be the
                 * style actually used by the post when it will be published
                 */
                '<?= BASEDIR ?>/vendor/bootstrap/css/bootstrap.min.css',
                '<?= BASEDIR ?>/me_cms/css/layout.css',
                '<?= BASEDIR ?>/me_cms/css/contents.css',
                <?php
                //If `layout.css` and `contents.css` files exists in the
                //  `webroot/css` directory, they will also be loaded
                foreach (['layout.css', 'contents.css'] as $file) {
                    if (is_readable(WEBROOT . 'css' . DS . $file)) {
                        echo '\'' . BASEDIR . '/css/' . $file . '\',';
                    }
                }
                ?>
            ],
            disableNativeSpellChecker: false,
            filebrowserUploadMethod: 'form',
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

            <?php
            //Checks if ElFinder/KCFinder exists
            if (is_readable(WEBROOT . 'vendor' . DS . 'elfinder' . DS . 'elfinder-cke.html')) {
                echo 'filebrowserBrowseUrl: \'' . BASEDIR . '/vendor/elfinder/elfinder-cke.html\',';
            } elseif (is_readable(WEBROOT . 'vendor' . DS . 'kcfinder' . DS . 'browse.php')) {
                echo 'filebrowserBrowseUrl: \'' . BASEDIR . '/vendor/kcfinder/browse.php?type=files\',';
                echo 'filebrowserImageBrowseUrl: \'' . BASEDIR . '/vendor/kcfinder/browse.php?type=images\',';
                echo 'filebrowserUploadUrl: \'' . BASEDIR . '/vendor/kcfinder/upload.php?type=files\',';
                echo 'filebrowserImageUploadUrl: \'' . BASEDIR . '/vendor/kcfinder/upload.php?type=images\',';
            }
            ?>
        });
    });
});
