<?php
use Ramona\CMS\Admin\Home;

/**
@var \Ramona\CMS\Admin\LayoutView $model
 */ ?>
<!DOCTYPE html>
<html>
<head>
    <?php /** TODO: this needs to use the vite manifest in prod */ ?>
    <script type="module" src="http://localhost:5173/@vite/client"></script>
    <script type="module" src="http://localhost:5173/src/main.ts"></script>

    <?php foreach($model->frontendModules() as $frontendModule): ?>
        <script type="module" src="http://localhost:5173/<?=$frontendModule->key?>"></script>
    <?php endforeach; ?>
</head>
<body>
    <section>
        <div>Logged in as: <?=$model->loggedInUser->username()?></div>
        <ul>
            <li><a href="<?=$model->uriGenerator->forRoute(Home::ROUTE_NAME)?>">Home</a></li>
        </ul>
    </section>

    <section>
        <?= $model->body->render() ?>
    </section>
</body>
</html>
