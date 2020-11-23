<?php
declare(strict_types=1);

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
if (!$this->Paginator->hasPage(2, null)) {
    return;
}
?>

<nav>
    <ul class="pagination d-none d-lg-flex justify-content-center">
        <?= $this->Paginator->prev('', ['icon' => 'caret-left']) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next('', ['icon' => 'caret-right']) ?>
    </ul>
    <ul class="pagination d-lg-none justify-content-center">
        <?php
        if ($this->Paginator->hasPrev() && $this->Paginator->hasNext()) {
            echo $this->Paginator->prev('', ['icon' => 'caret-left']);
            echo $this->Html->li(
                $this->Html->span(__d('me_tools', 'Page {0}', $this->Paginator->current()), ['class' => 'page-link']),
                ['class' => 'page-item']
            );
            echo $this->Paginator->next('', ['icon' => 'caret-right']);
        } elseif (!$this->Paginator->hasPrev()) {
            echo $this->Paginator->next(__d('me_tools', 'Next'), ['icon' => 'caret-right', 'icon-align' => 'right']);
        } else {
            echo $this->Paginator->prev(__d('me_tools', 'Previous'), ['icon' => 'caret-left']);
        }
        ?>
    </ul>
</nav>
