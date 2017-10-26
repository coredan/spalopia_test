<?php
class WH_sidebar extends CWidget
{
	public $extras;
	//public $totalMessages;
	public function init() { 
		if(!Yii::app()->user->isGuest) {			
		}	
    	$this->extras = Roomextras::model()->findAll();
	}

    public function run()
    {    	
        $this->render('WH_sidebar_view', array(/*'model'=>User::model()->findbyPk(Yii::app()->user->id)*/));
    }
}