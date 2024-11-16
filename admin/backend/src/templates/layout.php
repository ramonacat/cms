<?php /**
@var \Ramona\CMS\Admin\LayoutView $model
 */ ?>
<!DOCTYPE html>
<html>
<head>
    <?php /** TODO: this needs to use the vite manifest in prod */ ?>
    <script type="module" src="http://localhost:5173/@vite/client"></script>
    <script type="module" src="http://localhost:5173/src/main.ts"></script>
</head>
<body>
    <?= $model->body->render() ?>
</body>
</html>
