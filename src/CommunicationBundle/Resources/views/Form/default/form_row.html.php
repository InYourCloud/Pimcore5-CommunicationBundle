<?php
$isValid = $form->vars['valid'];
?>

<div class="form-group <?= $isValid ? '' : 'error' ?>">
    <?= $this->form()->label($form); ?>

    <div>
        <?= $this->form()->widget($form); ?>
        <span class="form-error"><?= $this->t('form.error.required_field'); ?></span>

        <?= $this->form()->errors($form); ?>
    </div>
</div>