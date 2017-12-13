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
?>

<div class="alert <?= empty($params['class']) ? null : $params['class'] ?> alert-dismissible border-0 fade show" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="<?= __('me_tools', 'Close') ?>">
        <span aria-hidden="true">&times;</span>
    </button>
    <?= $message ?>
</div>
