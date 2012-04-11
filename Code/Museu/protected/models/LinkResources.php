<?php

/**
 * This is the model class for table "LinkResources".
 *
 * The followings are the available columns in table 'LinkResources':
 * @property integer $id_linkResources
 * @property string $linkResource
 * @property string $type
 * @property string $formatResource
 */
class LinkResources extends CActiveRecord
{
	
	public $image;
	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return LinkResources the static model class
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
		return 'LinkResources';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('linkResource', 'required'),
			array('linkResource', 'length', 'max'=>2000),
			array('type, formatResource', 'length', 'max'=>15),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_linkResources, linkResource, type, formatResource', 'safe', 'on'=>'search'),
			array('image', 'file', 'types'=>'JPG, gif, png'),
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
			'resources' => array(self::HAS_MANY, 'Resources', 'LinkResource'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_linkResources' => 'Id Link Resources',
			'linkResource' => 'Link Resource',
			'type' => 'Type',
			'formatResource' => 'Format Resource',
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

		$criteria->compare('id_linkResources',$this->id_linkResources);

		$criteria->compare('linkResource',$this->linkResource,true);

		$criteria->compare('type',$this->type,true);

		$criteria->compare('formatResource',$this->formatResource,true);

		return new CActiveDataProvider('LinkResources', array(
			'criteria'=>$criteria,
		));
	}
}