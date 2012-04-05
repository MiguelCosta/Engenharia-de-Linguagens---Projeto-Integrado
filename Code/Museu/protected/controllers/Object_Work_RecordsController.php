<?php

class Object_Work_RecordsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','createAll'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 */
	public function actionView()
	{
		$this->render('view',array(
			'model'=>$this->loadModel(),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Object_Work_Records;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Object_Work_Records']))
		{
			$model->attributes=$_POST['Object_Work_Records'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id_object_Work_Records));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}
	
	public function actionCreateAll()
	{
// 		Yii::import('ext.multimodelform.MultiModelForm');
		
// 		$owr=new Object_Work_Records;
// 		$owt=new Object_Work_Titles;
// 		$validatedMembers = array();  //ensure an empty array
		
// 		if(isset($_POST['Object_Work_Records']))
// 		{
// 			$owr->attributes=$_POST['Object_Work_Records'];
			
// 			//validate detail before saving the master
// 			$detailOK = MultiModelForm::validate($owt,$validatedMembers,$deleteItems);
			
// 			if ($detailOK && empty($validatedMembers))
// 			{
// 				Yii::app()->user->setFlash('error','No detail submitted');
// 				$detailOK = false;
// 			}
			
// 			if( $detailOK && $owr->save() )
// 			{
// 				// vai buscar o ultimo id
// 				$maxRecordNumber = Yii::app()->db->createCommand()
// 					->select('max(id_object_Work_Records) as max')
// 					->from('Object_Work_Records')
// 					->queryScalar();
// 				//the value for the foreign key 'groupid'
// 				$masterValues = array ('Object_Work_Record'=>$maxRecordNumber + 1);
				
// 				if (MultiModelForm::save($owt,$validatedMembers,$deleteMembers,$masterValues))
// 					$this->redirect(array('view','id'=>$owr->id_object_Work_Records));
// 			}
// 		}
		
// 		$this->render('createAll', array(
// 				'Object_Work_Records'=>$owr,
// 				'Object_Work_Titles'=>$owt,
// 				'validatedMembers' => $validatedMembers
// 		));

		$validMeasureI 	= 	false; // Temporario enquanto nao se tratar os casos 1:N
		$validMeasureII = 	false; // Temporario enquanto nao se tratar os casos 1:N
		$validEMaterial	= 	false;
		$owr 	=	new Object_Work_Records;
		$owt 	=	new Object_Work_Titles;
		$owtp 	=	new Object_Work_Types;
		$im		=	new IndexingMeasurements;
		$m 		=	new Measurements;
		$mII 	=	new Measurements;
		$imt	=	new IndexingMaterialsTech;
		$emt 	=	new ExtentMaterialsTech;
		
		
 		if(isset($_POST['Object_Work_Records'], $_POST['Object_Work_Titles'], $_POST['Object_Work_Types']))
 		{
 			// vai buscar o ultimo id de Object_Work_Records
 			$maxRecordNumber = Yii::app()->db->createCommand()
	 			->select('max(id_object_Work_Records) as max')
	 			->from('Object_Work_Records')
	 			->queryScalar();
 			
 			// obtem os dados do form relativos ao Object_Work_Records
 			$owr->attributes=$_POST['Object_Work_Records'];
 			
 			// obtem os dados do form relativos ao Object_Work_Types
 			//$owr->object_Work_Types = array($_POST['ddlWType']); // TODO 1:N
 			$owtp->attributes=$_POST['Object_Work_Types'];
 			$id_type = Yii::app()->db->createCommand()
	 			->select('id_type')
	 			->from('Object_Work_Types')
	 			->where('type=:id', array(':id'=>$owtp->type))
	 			->queryScalar();
 			$owr->object_Work_Types = array($id_type);
 			
 			// obtem os dados do form relativos ao Object_Work_Titles
 			$owt->attributes=$_POST['Object_Work_Titles']; // TODO 1:N
 			$owt->Object_Work_Record = $maxRecordNumber + 1;
 			
 			
 			// obtem os dados do form relativos as Medidas/Dimensoes
 			//if (isset($_POST['Measurements']) || isset($_POST['MeasurementII'])) {
 			if (isset($_POST['Measurements'])) {
 				// chave estrangeira da IndexingMeasurements que referencia Object_Work_Records
 				$im->Object_Work_Record = $maxRecordNumber + 1;
 				
 				// vai buscar o ultimo id de IndexingMeasurements
 				$maxIMNumber = Yii::app()->db->createCommand()
	 				->select('max(id_indexingMeasurements) as max')
	 				->from('IndexingMeasurements')
	 				->queryScalar();
 				
 				// Temporario: Se o primeiro grupo de dados relativos a Measurements tiver sido preenchido
 				//CVarDumper::dump($_POST['Measurements'],12,true);
 				if (isset($_POST['Measurements'][1])) {
 					// obtem os dados do form relativos a Measurements
 					$m->attributes=$_POST['Measurements'][1]; // TODO 1:N
 					
 					if ($m->value!=NULL || $m->unit!=NULL || $m->type!=NULL) {
	 					// chave estrangeira da Measurements que referencia IndexingMeasurements
	 					$m->IndexingMeasurement=$maxIMNumber + 1;
	 						
	 					// valida os models antes de guardar
	 					$validMeasureI=$m->validate();
 					}
 				}
 				
 				// Temporario: Se o segundo grupo de dados relativos a Measurements tiver sido preenchido
 				if (isset($_POST['MeasurementII'][2])) { // TODO nao funciona isto. tem a ver com tabular input
 					// obtem os dados do form relativos a Measurements
 					$mII->attributes=$_POST['MeasurementII'][2]; // TODO 1:N
 					
 					if ($mII->value!=NULL || $mII->unit!=NULL || $mII->type!=NULL) {
	 					// chave estrangeira da Measurements que referencia IndexingMeasurements
	 					$mII->IndexingMeasurement=$maxIMNumber + 1;
	 				
	 					// valida os models antes de guardar
	 					$validMeasureII=$mII->validate();
 					}
 				}

 				// Temporario: Se os dados relativos a QualifierMeasurements tiverem sido preenchidos
 				if (isset($_POST['ddlQualifierM']) && $_POST['ddlQualifierM']!='') {
 					// obtem os dados do form relativos ao QualifierMeasurements
 					$im->qualifierMeasurements=array($_POST['ddlQualifierM']); // TODO 1:N
 				}
 			}

 			
 			// obtem os dados do form relativos aos Materiais Se os dados relativos a termMaterialsTech tiverem sido preenchidos
 			if (isset($_POST['ddlTermMT']) && $_POST['ddlTermMT']!='') { // TODO 1:N
 				// chave estrangeira da IndexingMaterialsTech que referencia Object_Work_Records
 				$imt->Object_Work_Record = $maxRecordNumber + 1;
 					
 				// vai buscar o ultimo id de IndexingMaterialsTech
 				$maxIMTNumber = Yii::app()->db->createCommand()
	 				->select('max(id_indexingMaterialsTech) as max')
	 				->from('IndexingMaterialsTech')
	 				->queryScalar();
 				
 				// obtem os dados do form relativos ao IndexingMaterialsTech
 				if (isset($_POST['ddlTypeMT']) && $_POST['ddlTypeMT']!='')
 					$imt->type=$_POST['ddlTypeMT'];
 				
 				// obtem os dados do form relativos ao TermMaterialsTech
 				$imt->termMaterialsTeches=array($_POST['ddlTermMT']); 
 					
 				// obtem os dados do form relativos a ExtentMaterialsTech
 				$emt->attributes=$_POST['ExtentMaterialsTech']; 
 			
 				if ($emt->extentMaterialsTech!=NULL) {
					// chave estrangeira da ExtentMaterialsTech que referencia IndexingMaterialsTech
					$emt->IndexingMaterialsTech=$maxIMTNumber + 1;
		
					// valida os models antes de guardar
					$validEMaterial=$emt->validate();
				}
 			}
			
 			// valida os models antes de guardar
 			$valid=$owr->validate();
 			$valid=$owt->validate() && $valid;
	
 			if($valid)
 			{
 				// use false parameter to disable validation
 				$owr->save(false);
 				$owt->save(false);
 				if ($validMeasureI || $validMeasureII) {$im->save();}
 				if ($validMeasureI) {$m->save(false);}
 				if ($validMeasureII) {$mII->save(false);}
 				if (isset($_POST['ddlTermMT']) && $_POST['ddlTermMT']!='') {
 					$imt->save();
 					if ($validEMaterial) { $emt->save(false); }
 				}
 				// redirect to another page
 				$this->redirect(array('view','id'=>$owr->id_object_Work_Records));
 			}
 		}
	
 		$this->render('createAll', array(
 				'Object_Work_Records'=>$owr,
 				'Object_Work_Titles'=>$owt,
 				'Object_Work_Types'=>$owtp,
 				'Measurements'=>$m,
 				'MeasurementsII'=>$mII,
 				'ExtentMaterialsTech'=>$emt,
 		));
	}

	
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionUpdate()
	{
		$model=$this->loadModel();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Object_Work_Records']))
		{
			$model->attributes=$_POST['Object_Work_Records'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id_object_Work_Records));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 */
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel()->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Object_Work_Records');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Object_Work_Records('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Object_Work_Records']))
			$model->attributes=$_GET['Object_Work_Records'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id']))
                            // with('object_Work_Titles') adicionado para carregar mais informação de
                            // uma vez da base de dados
				$this->_model=Object_Work_Records::model()
                                                ->with('object_Work_Titles','recordType', 'object_Work_Types', 'inscriptions')
                                                ->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='object--work--records-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
