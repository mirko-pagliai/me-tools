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

//Returns, if there's only one page
if (!$this->Paginator->hasPage(null, 2)) {
    return;
}
?>

<div class="text-center">
    <div class="hidden-xs">
        <ul class="pagination">
            <?= $this->Paginator->prev(null, ['icon' => 'caret-left']) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(null, ['icon' => 'caret-right']) ?>
        </ul>
    </div>
    <div class="visible-xs">
        <ul class="pagination">
            <?php
            if ($this->Paginator->hasPrev() && $this->Paginator->hasNext()) {
                echo $this->Paginator->prev(null, ['icon' => 'caret-left']);
                echo $this->Html->li($this->Html->span(__d('me_tools', 'Page {0}', $this->Paginator->current())));
                echo $this->Paginator->next(null, ['icon' => 'caret-right']);
            } elseif (!$this->Paginator->hasPrev()) {
                echo $this->Paginator->next(__d('me_tools', 'Next'), ['icon' => 'caret-right', 'icon-align' => 'right']);
            } else {
                echo $this->Paginator->prev(__d('me_tools', 'Previous'), ['icon' => 'caret-left']);
            }
            ?>
        </ul>
    </div>
</div>
