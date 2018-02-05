<?php
	$this->extend($this->layout);

	$this->form()->setTheme($form, 'CommunicationBundle:Form/default');
?>

<div>
    <?php if($this->successful || $this->editmode): ?>
	<div class="callout success">
		<?= $this->wysiwyg('formSuccess'); ?>
        <?php if(!$this->editmode): ?>
        <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
            <span aria-hidden="true">&times;</span>
        </button>
        <?php endif; ?>
	</div>
    <?php endif; ?>
    <?php if(($this->submitted && !$this->successful) || $this->editmode): ?>
    <div class="callout alert">
	    <?= $this->wysiwyg('formError'); ?>
	    <?php if(!$this->editmode): ?>
        <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
            <span aria-hidden="true">&times;</span>
        </button>
        <?php endif; ?>
    </div>
    <?php endif; ?>

	<?= $this->form()->start($form, [
		'attr' => [
			'class' => 'form-horizontal',
			'role'  => 'form',
			'data-abide' => true,
			'novalidate' => true
		]
	]); ?>

	<?= $this->form()->end($form) ?>
</div>
