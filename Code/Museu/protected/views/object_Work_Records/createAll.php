<?php
$this->breadcrumbs=array(
	'Object  Work  Records'=>array('index'),
	'Create Object',
);

$this->menu=array(
	array('label'=>'List Object_Work_Records', 'url'=>array('index')),
	array('label'=>'Manage Object_Work_Records', 'url'=>array('admin')),
);
?>

<h1>Create Object_Work_Record</h1>

<?php echo $this->renderPartial('_formAll', array('Object_Work_Records'=>$Object_Work_Records, 'Object_Work_Titles'=>$Object_Work_Titles)); ?>
