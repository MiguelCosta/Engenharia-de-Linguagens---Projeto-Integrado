<div class="form">

	<?php 
	$form=$this->beginWidget(
			'CActiveForm',
			array(
					'id'=>'exhibitions-form',
					'enableAjaxValidation'=>false,
					'htmlOptions' => array('enctype' => 'multipart/form-data'),
			)
	);
	?>

	<p class="note">
		Fields with <span class="required">*</span> are required.
	</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>63)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php //echo $form->textField($model,'description',array('size'=>60,'maxlength'=>255)); ?>
		<?php $this->widget('application.extensions.eckeditor.ECKEditor', array(
				'model'=>$model,
				'attribute'=>'description',
				"config" => array(
                                  "height"=>"250px",
                                  "width"=>"900px",
                                  "toolbar"=>"Full",
								  "skin"=>'kama',
                                  ),
                )); 
		//http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html
		?>


		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row">
		<?php 
		echo $form->labelEx($model, 'image');
		echo $form->fileField($model, 'image');
		echo $form->error($model, 'image');
		?>
		<p class="hint">Uma imagem ilustrativa do conteúdo da exposição.</p>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>
<!-- form -->
