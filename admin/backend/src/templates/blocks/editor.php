<?php /** @var \Ramona\CMS\Admin\Blocks\EditorView $model */ ?>
<form action="#" method="POST">
    <textarea id="block-editor"><?= htmlentities($model->initialContent, ENT_HTML5 | ENT_SUBSTITUTE)?></textarea>

    <button type="submit">Save</button>
</form>
