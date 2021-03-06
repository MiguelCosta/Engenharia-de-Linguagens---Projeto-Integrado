<?php

/**
 * This is the model class for table "RelatedWorks".
 *
 * The followings are the available columns in table 'RelatedWorks':
 * @property integer $id_relatedWorks
 * @property string $relatedWorkRelType
 * @property integer $Object_Work_Record
 */
class RelatedWorks extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return RelatedWorks the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'RelatedWorks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('Object_Work_Record', 'required'),
				array('Object_Work_Record', 'numerical', 'integerOnly'=>true),
				array('relatedWorkRelType', 'length', 'max'=>31),
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array('id_relatedWorks, relatedWorkRelType, Object_Work_Record', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
				'labelRelatedWorks' => array(self::HAS_MANY, 'LabelRelatedWork', 'RelatedWork'),
				'linksRelatedWorks' => array(self::HAS_MANY, 'LinksRelatedWork', 'RelatedWork'),
				'locationsRelatedWorks' => array(self::HAS_MANY, 'LocationsRelatedWork', 'RelatedWork'),
				'object_Work_Record' => array(self::BELONGS_TO, 'ObjectWorkRecords', 'Object_Work_Record'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
				'id_relatedWorks' => 'Id Related Works',
				'relatedWorkRelType' => 'Related Work Rel Type',
				'Object_Work_Record' => 'Object Work Record',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id_relatedWorks',$this->id_relatedWorks);

		$criteria->compare('relatedWorkRelType',$this->relatedWorkRelType,true);

		$criteria->compare('Object_Work_Record',$this->Object_Work_Record);

		return new CActiveDataProvider('RelatedWorks', array(
				'criteria'=>$criteria,
		));
	}

	/*********************************************************************************/
	/********************************** My Functions *********************************/
	/*********************************************************************************/

	public function getLabelRelatedWorks(){
		$result = array();
		foreach ($this->labelRelatedWorks as $labelRelatedWorks){
			$tmp = array(
					'id_labelRelatedWork'=>$labelRelatedWorks->id_labelRelatedWork,
					'labelRelatedWorks'=>$labelRelatedWorks->labelRelatedWork,
			);
			array_push($result, $tmp);
			//CVarDumper::dump($result, 3, true);
		}
		return $result;
	}

}