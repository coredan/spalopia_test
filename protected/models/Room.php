<?php

/**
 * This is the model class for table "{{room}}".
 *
 * The followings are the available columns in table '{{room}}':
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $capacity
 *
 * The followings are the available model relations:
 * @property RoomRoomextras[] $roomRoomextrases
 */
class Room extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{room}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, capacity , price', 'required'),
			array('capacity, price', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, description, capacity, price', 'safe', 'on'=>'search'),
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
			'room_roomextras' => array(self::HAS_MANY, 'RoomRoomextras', 'room_id'),
			'seasonprices' => array(self::HAS_MANY, 'Seasonprices', 'room_id'),
			'reservations' => array(self::HAS_MANY, 'Reservations', 'room_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'description' => 'Description',
			'capacity' => 'Capacity',
			'price' => 'Price',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('capacity',$this->capacity);
		$criteria->compare('price',$this->price);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Room the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
