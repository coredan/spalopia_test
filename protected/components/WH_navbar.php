<?php
class WH_navbar extends CWidget
{
	//public $totalMessages;
	public function init() { 
		if(!Yii::app()->user->isGuest) {			
		}	
    	
	}

    public function run()
    {    	
        $this->render('WH_navbar_view', array(/*'model'=>User::model()->findbyPk(Yii::app()->user->id)*/));
    }
}