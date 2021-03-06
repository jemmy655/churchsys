<?php

/**
 * This is the model class for table "tbl_hymn_tags".
 *
 * The followings are the available columns in table 'tbl_hymn_tags':
 * @property integer $id
 * @property string $tag
 * @property integer $hymn_id
 *
 * The followings are the available model relations:
 * @property Hymn $hymn
 */
class HymnTags extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HymnTags the static model class
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
		return 'tbl_hymn_tags';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tag, hymn_id', 'required'),
			array('hymn_id', 'numerical', 'integerOnly'=>true),
			array('tag', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, tag, hymn_id', 'safe', 'on'=>'search'),
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
			'hymn' => array(self::BELONGS_TO, 'Hymn', 'hymn_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'tag' => 'Tag',
			'hymn_id' => 'Hymn',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('tag',$this->tag,true);
		$criteria->compare('hymn_id',$this->hymn_id);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}