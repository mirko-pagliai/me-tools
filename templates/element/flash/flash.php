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
 * @var string $message
 */
?>

<div class="alert alert-dismissible border-0 <?= $params['class'] ?? null ?> p-3 fade show" role="alert">
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= __d('me_tools', 'Close') ?>"></button>

    <?= $message ?>
</div>
