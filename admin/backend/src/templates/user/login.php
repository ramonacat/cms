<?php /** @var \Ramona\CMS\Admin\Authentication\LoginView $model */ ?>
<div class="<?= $model->cssModule->classFor('login-form')?>">
    <form action="" method="POST">
        <div>
            <label for="<?= ($usernameId = uniqid('username'))?>">
                username
            </label>
            <input type="text" name="username" id="<?= $usernameId?>"/>
        </div>

        <div>
            <label for="<?= ($passwordId = uniqid('password'))?>">
                password
            </label>
            <input type="password" name="password" id="<?= $passwordId?>" />
        </div>

        <div>
            <button type="submit">login</button>
        </div>
    </form>
</div>
