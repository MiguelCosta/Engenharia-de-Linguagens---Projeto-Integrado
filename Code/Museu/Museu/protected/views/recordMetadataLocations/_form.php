<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'record-metadata-locations-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'recordMetadataLocation'); ?>
		<?php echo $form->textField($model,'recordMetadataLocation',array('size'=>60,'maxlength'=>511)); ?>
		<?php echo $form->error($model,'recordMetadataLocation'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->textField($model,'type',array('size'=>15,'maxlength'=>15)); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'RecordInfo'); ?>
		<?php echo $form->textField($model,'RecordInfo'); ?>
		<?php echo $form->error($model,'RecordInfo'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->