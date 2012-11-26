<?php

/**
 * This is the model class for table "RecordsID".
 *
 * The followings are the available columns in table 'RecordsID':
 * @property integer $id_recordsID
 * @property string $recordID
 * @property string $type
 * @property integer $Object_Work_Record
 */
class RecordsID extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return RecordsID the static model class
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
		return 'RecordsID';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('recordID, Object_Work_Record', 'required'),
			array('Object_Work_Record', 'numerical', 'integerOnly'=>true),
			array('recordID', 'length', 'max'=>15),
			array('type', 'length', 'max'=>31),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_recordsID, recordID, type, Object_Work_Record', 'safe', 'on'=>'search'),
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
			'object_Work_Record' => array(self::BELONGS_TO, 'ObjectWorkRecords', 'Object_Work_Record'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_recordsID' => 'Id Records',
			'recordID' => 'Número de inventário',
			'type' => 'Type',
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

		$criteria->compare('id_recordsID',$this->id_recordsID);

		$criteria->compare('recordID',$this->recordID,true);

		$criteria->compare('type',$this->type,true);

		$criteria->compare('Object_Work_Record',$this->Object_Work_Record);

		return new CActiveDataProvider('RecordsID', array(
			'criteria'=>$criteria,
		));
	}
}