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

/**
 * @var \App\View\AppView $this
 */

use function Cake\I18n\__d;

//Returns, if there's only one page
if (!$this->Paginator->hasPaginated() || !$this->Paginator->hasPage(2)) {
    return;
}
?>

<nav class="d-print-none mt-4">
    <ul class="pagination d-none d-lg-flex justify-content-center m-0">
        <?= $this->Paginator->prev() ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next() ?>
    </ul>
    <ul class="pagination d-lg-none justify-content-center m-0">
        <?php
        if ($this->Paginator->hasPrev() && $this->Paginator->hasNext()) {
            echo $this->Paginator->prev();
            echo $this->Html->li(
                $this->Html->link(__d('me_tools', 'Page {0}', $this->Paginator->current()), '#', ['class' => 'page-link']),
                ['class' => 'page-item']
            );
            echo $this->Paginator->next();
        } elseif (!$this->Paginator->hasPrev()) {
            echo $this->Paginator->next(__d('me_tools', 'Next'));
        } else {
            echo $this->Paginator->prev(__d('me_tools', 'Previous'));
        }
        ?>
    </ul>
</nav>
