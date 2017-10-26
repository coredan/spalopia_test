<?php

/**
 * This is the model class for table "{{reservations}}".
 *
 * The followings are the available columns in table '{{reservations}}':
 * @property integer $id
 * @property integer $room_id
 * @property string $reservation_key
 * @property string $client_name
 * @property string $comments
 * @property string $checkin
 * @property string $checkout
 * @property string $extras
 * @property string $total
 *
 * The followings are the available model relations:
 * @property Room $room
 */
class Reservations extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{reservations}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('room_id, reservation_key, client_name, checkin, checkout, total', 'required'),
			array('room_id', 'numerical', 'integerOnly'=>true),
			array('reservation_key, client_name, extras', 'length', 'max'=>255),
			array('total', 'length', 'max'=>10),
			array('comments', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, room_id, reservation_key, client_name, comments, checkin, checkout, extras, total', 'safe', 'on'=>'search'),
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
			'room' => array(self::BELONGS_TO, 'Room', 'room_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'room_id' => 'Room',
			'reservation_key' => 'Reservation Key',
			'client_name' => 'Client Name',
			'comments' => 'Comments',
			'checkin' => 'Checkin',
			'checkout' => 'Checkout',
			'extras' => 'Extras',
			'total' => 'Total',
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
		$criteria->compare('room_id',$this->room_id);
		$criteria->compare('reservation_key',$this->reservation_key,true);
		$criteria->compare('client_name',$this->client_name,true);
		$criteria->compare('comments',$this->comments,true);
		$criteria->compare('checkin',$this->checkin,true);
		$criteria->compare('checkout',$this->checkout,true);
		$criteria->compare('extras',$this->extras,true);
		$criteria->compare('total',$this->total,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Reservations the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
