<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'owner-roles-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'ownerRole'); ?>
		<?php echo $form->textField($model,'ownerRole',array('size'=>60,'maxlength'=>63)); ?>
		<?php echo $form->error($model,'ownerRole'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'termsource'); ?>
		<?php echo $form->textField($model,'termsource',array('size'=>60,'maxlength'=>63)); ?>
		<?php echo $form->error($model,'termsource'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'termsourceID'); ?>
		<?php echo $form->textField($model,'termsourceID',array('size'=>60,'maxlength'=>63)); ?>
		<?php echo $form->error($model,'termsourceID'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->