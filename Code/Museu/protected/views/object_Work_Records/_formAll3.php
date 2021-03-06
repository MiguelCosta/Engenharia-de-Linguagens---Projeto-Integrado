<?php
$this->widget('ext.slidetoggle.ESlidetoggle',
  array(
   'itemSelector' => 'div.form .group',
   'titleSelector' => 'div.form .group .title',
   'collapsed' => 'div.form .group',
  ));
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'object--work--records-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>
 
    <?php //echo CHtml::errorSummary(array($Object_Work_Records,$Object_Work_Titles)); 
		echo $form->errorSummary(array_merge(array($Object_Work_Records),$validatedMembers));?>
    
    
    <!-- Object_Work_Titles -->
 	
	<?php

 
$memberFormConfig = array(
      'elements'=>array(
        'title'=>array(
            'type'=>'text',
        	'size'=>60,
            'maxlength'=>255,
        ),
        'lastname'=>array(
            'type'=>'hidden',
        	'value'=>1
        )
    ));
 
$this->widget('ext.multimodelform.MultiModelForm',array(
        'id' => 'id_title', //the unique widget id
        'formConfig' => $memberFormConfig, //the form configuration array
        'model' => $Object_Work_Titles, //instance of the form model
        //if submitted not empty from the controller,
        //the form will be rendered with validation errors
        'validatedItems' => $validatedMembers,
 
        //array of member instances loaded from db
        'data' => $Object_Work_Titles->findAll('Object_Work_Record=:Object_Work_Record', array(':Object_Work_Record'=>$Object_Work_Records->id_object_Work_Records)),
    ));
?>
	
	
 
 
	<!-- Object_Work_Records -->
    <div class="row">
		<?php echo $form->labelEx($Object_Work_Records,'displayCreator'); ?>
		<?php echo $form->textField($Object_Work_Records,'displayCreator',array('size'=>60,'maxlength'=>511)); ?>
		<?php echo $form->error($Object_Work_Records,'displayCreator'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($Object_Work_Records,'displayMaterialsTech'); ?>
		<?php echo $form->textField($Object_Work_Records,'displayMaterialsTech',array('size'=>60,'maxlength'=>511)); ?>
		<?php echo $form->error($Object_Work_Records,'displayMaterialsTech'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($Object_Work_Records,'displayCreationDate'); ?>
		<?php echo $form->textField($Object_Work_Records,'displayCreationDate',array('size'=>60,'maxlength'=>511)); ?>
		<?php echo $form->error($Object_Work_Records,'displayCreationDate'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($Object_Work_Records,'RecordType'); ?>
		<?php echo $form->textField($Object_Work_Records,'RecordType'); ?>
		<?php echo $form->error($Object_Work_Records,'RecordType'); ?>
	</div>
	
 	<div class="group"> 
		<div class="title">
			<?php echo "Medidas" ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($Object_Work_Records,'displayMeasurements'); ?>
			<?php echo $form->textField($Object_Work_Records,'displayMeasurements',array('size'=>60,'maxlength'=>511)); ?>
			<?php echo $form->error($Object_Work_Records,'displayMeasurements'); ?>
		</div>
	</div>
		
	<div class="row buttons">
		<?php echo CHtml::submitButton($Object_Work_Records->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->