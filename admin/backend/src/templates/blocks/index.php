<?php

use Ramona\CMS\Admin\Blocks\GetEditBlock;
use Ramona\CMS\Admin\Blocks\PostDeleteBlock;

/** @var \Ramona\CMS\Admin\Blocks\BlocksView $model */ ?>

<a href="<?=$model->uriGenerator->forRoute(GetEditBlock::ROUTE_NAME)?>">Add block</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($model->blocks as $block): ?>
            <tr>
                <td><?=$block->id()?></td>
                <td>
                    <a href="<?=$model->uriGenerator->forRoute(GetEditBlock::ROUTE_NAME, [
                        'id' => (string) $block->id(),
                    ])?>">Edit</a>

                    <form action="<?=$model->uriGenerator->forRoute(PostDeleteBlock::ROUTE_NAME, [
                        'id' => (string) $block->id(),
                    ])?>" method="POST">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
