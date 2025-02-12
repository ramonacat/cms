<?php /** @var \Ramona\CMS\Admin\Authentication\LoginView $model */ ?>
<div class="<?= $model->cssModule->classFor('login-form')?>">
    <form action="" method="POST">
        <?php foreach($model->form->globalErrors() as $error): ?>
            <div class="<?=$model->cssModule->classFor('form-error')?>">
                <?=$error?>
            </div>
        <?php endforeach; ?>

        <div class="<?=$model->cssModule->classFor('row')?>">
            <label for="<?= ($usernameId = uniqid('username'))?>">
                username
            </label>
            <input type="text" name="username" id="<?= $usernameId?>"/>
        </div>

        <div class="<?=$model->cssModule->classFor('row')?>">
            <label for="<?= ($passwordId = uniqid('password'))?>">
                password
            </label>
            <input type="password" name="password" id="<?= $passwordId?>" />
        </div>

        <div class="<?=$model->cssModule->classFor('row')?>">
            <button type="submit">login</button>
        </div>
    </form>
</div>
