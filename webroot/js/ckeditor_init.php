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

$scriptFilename = getenv('SCRIPT_FILENAME');
$scriptName = getenv('SCRIPT_NAME');
$webroot = substr($scriptFilename, 0, strrpos($scriptFilename, DS . 'webroot' . DS)) . DS . 'webroot' . DS;
$baseDir = substr($scriptName, 0, strrpos($scriptName, '/webroot/'));
?>
$(function () {
    $('.editor.wysiwyg').each(function () {
        CKEDITOR.on('dialogDefinition', function (ev) {
            var dialogName = ev.data.name;
            var dialogDefinition = ev.data.definition;

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
                '<?= $baseDir ?>/vendor/bootstrap/css/bootstrap.min.css',
                '<?= $baseDir ?>/me_cms/css/layout.css',
                '<?= $baseDir ?>/me_cms/css/contents.css',
                <?php
                    //If `layout.css` and `contents.css` files exists in the
                    //  `webroot/css` directory, they will also be loaded
                    foreach (['layout.css', 'contents.css'] as $file) {
                        if (is_readable($webroot . 'css' . DS . $file)) {
                            echo '\'' . $baseDir . '/css/' . $file . '\',';
                        }
                    }
                ?>
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

            <?php
                //Checks if the KCFinder files exist
                if (is_readable($webroot . 'vendor' . DS . 'kcfinder' . DS . 'browse.php')) {
                    echo 'filebrowserBrowseUrl: \'' . $baseDir . '/vendor/kcfinder/browse.php?type=files\',';
                    echo 'filebrowserImageBrowseUrl: \'' . $baseDir . '/vendor/kcfinder/browse.php?type=images\',';
                    echo 'filebrowserUploadUrl: \'' . $baseDir . '/vendor/kcfinder/upload.php?type=files\',';
                    echo 'filebrowserImageUploadUrl: \'' . $baseDir . '/vendor/kcfinder/upload.php?type=images\',';
                }
            ?>
        });
    });
});
