<?php
$this->breadcrumbs=array(
		'Peças'=>array('index'),
		'Criar Ficha Completa',
);

$this->menu=array(
		array('label'=>'Índice', 'url'=>array('index')),
		array('label'=>'Criar Ficha Completa', 'url'=>array('createAll')),
		array('label'=>'Administração', 'url'=>array('admin')),
);
?>

<h1>Create Object_Work_Record</h1>

<?php echo $this->renderPartial('_formAll', array('Object_Work_Records'=>$Object_Work_Records, 'Object_Work_Titles'=>$Object_Work_Titles, 'Object_Work_Types'=>$Object_Work_Types)); ?>
