<?php

class MemberController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			//'accessControl', // perform access control for CRUD operations
			'rights',
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	public function accessRules()
	{
		return array(
		array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','create','update','admin','delete','duplicate','merge'),
				'roles'=>array('pastor', 'preacher', 'deacon', 'periodTutor', 'staff', 'itadmin'),
		),
		array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('view','update'),
				'roles'=>array('groupTutor'),
		),
			
		array('allow',  // deny all users
				'actions' => array('autoComplete','memberIdAutoComplete'),
				'users'=>array('*'),
		),
		array('deny',  // deny all users
				'users'=>array('*'),
		),
		);
	}
	 */

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Member;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Member']))
		{
			$model->attributes=$_POST['Member'];
			if($model->save())
			{
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$query = 'SELECT MAX(code)+1 AS code FROM
			 tbl_member WHERE code<9000';	
		$nextCode=Yii::app()->db->createCommand($query)->queryScalar();

		$this->render('create',array(
			'model'=>$model,
			'nextCode' => $nextCode
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Member']))
		{
			$model->attributes=$_POST['Member'];
			if($model->save())
			$this->redirect(array('view','id'=>$model->id));
		}

		$query = 'SELECT MAX(code)+1 AS code FROM
			 tbl_member WHERE code<9000';		
		$nextCode=Yii::app()->db->createCommand($query)->queryScalar();

		$this->render('update',array(
			'model'=>$model,
			'nextCode' => $nextCode
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
        // we only allow deletion via POST request
		if(Yii::app()->request->isPostRequest)
		{
            // remove the member from the groups which he/she has joined
            $rowsDeleted = GroupMember::model()->deleteAll("member_id=:mid", array(":mid" => $id));

            $model=$this->loadModel($id);
            // state = 0 means account deleted
            $isUpdated = $model->saveAttributes(array("state" => '0', "code" => '', "account_type" => '-1'));
			if($isUpdated)
            {
                // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                if(!isset($_GET['ajax']))
                    $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
            }
		}

		else
		throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->redirect(array("admin"));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Member('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Member']))
		$model->attributes=$_GET['Member'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Member::model()->findByPk((int)$id);
		if($model===null)
		throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='member-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}


	//Action
	public function actionAutoComplete()
	{
		if(Yii::app()->request->isAjaxRequest && isset($_GET['q']))
		{
			/* q is the default GET variable name that is used by
			 / the autocomplete widget to pass in user input
			 */
			$name_code = $_GET['q'];
			// this was set with the "max" attribute of the CAutoComplete widget
			$limit = min($_GET['limit'], 50);
			$criteria = new CDbCriteria;
			$criteria->condition = "name LIKE :sterm OR code LIKE :sterm";
			$criteria->params = array(":sterm"=>"%$name_code%");
			$criteria->with = "groups";
			$criteria->limit = $limit;
			$memberArray = Member::model()->findAll($criteria);
			$returnVal = '';
			foreach($memberArray as $member)
			{
				if (count($member->groups) > 0)
				{
					$returnVal .= $member->name.' ' . $member->code.' ('. $member->groups[0]->period->name . '-'.$member->groups[0]->name.')|'.$member->code."\n";
				} else {
					$returnVal .= $member->name.' ' . $member->code.' (未入組)|'.$member->code."\n";
				}
			}
			echo $returnVal;
		}
	}
	public function actionMemberIdAutoComplete()
	{
		if(Yii::app()->request->isAjaxRequest && isset($_GET['q']))
		{
			/* q is the default GET variable name that is used by
			 / the autocomplete widget to pass in user input
			 */
			$name_code = $_GET['q'];
			// this was set with the "max" attribute of the CAutoComplete widget
			$limit = min($_GET['limit'], 50);
			$criteria = new CDbCriteria;
			$criteria->condition = "name LIKE :sterm OR code LIKE :sterm";
			$criteria->params = array(":sterm"=>"%$name_code%");
			$criteria->limit = $limit;
			$criteria->with = "groups";
			$memberArray = Member::model()->findAll($criteria);
			$returnVal = '';
			foreach($memberArray as $member)
			{
				if (count($member->groups) > 0)
				{
					$returnVal .= $member->name.' ' . $member->code.' ('. $member->groups[0]->period->name . '-'.$member->groups[0]->name.')|'.$member->id."\n";
				} else {
					$returnVal .= $member->name.' ' . $member->code.' (未入組)|'.$member->id."\n";
				}
			}
			echo $returnVal;
		}
	}
	
	public function actionDuplicate()
	{
		$criteria = new CDbCriteria;
		
		$criteria->group = "name";
		$criteria->having = "COUNT(name) > 1";
		$criteria->condition = "account_type=:account_type";
		$criteria->params = array(":account_type" => Member::ACCOUNT_TYPE_NEW_MEMBER);
		$duplicate_names = Member::model()->findAll($criteria);
		
		$duplicate_list = array();
		foreach ($duplicate_names as $i => $member)
		{
			$criteria=new CDbCriteria;
			$criteria->condition = "name=:name AND account_type=:account_type";
			$criteria->order = "code";
			$criteria->params = array(":name"=>$member->name, ":account_type" => Member::ACCOUNT_TYPE_NEW_MEMBER);
			$duplicate_list[] = Member::model()->findAll($criteria);
		}
		
		$this->render('duplicate',array(
			'duplicate_members'=>$duplicate_list,
		));
	}
	
	public function actionMerge($id)
	{
		$model=$this->loadModel($id);
		
		$criteria=new CDbCriteria;
		$criteria->condition = "name=:name AND account_type=:account_type";
		$criteria->order = "code";
		$criteria->params = array(":name"=>$model->name, ":account_type" => Member::ACCOUNT_TYPE_NEW_MEMBER);
		$duplicate_list = Member::model()->findAll($criteria);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Member']))
		{
			$model->attributes=$_POST['Member'];
			if($model->save()) {
				foreach ($duplicate_list as $member) {
					if ($member->id == $model->id)
						continue;
					CourseMember::model()->updateAll(array("member_id"=>$model->id), "member_id=:member_id", array(":member_id"=>$member->id));
					CourseAttendance::model()->updateAll(array("member_id"=>$model->id), "member_id=:member_id", array(":member_id"=>$member->id));
					GroupAttendance::model()->updateAll(array("member_id"=>$model->id), "member_id=:member_id", array(":member_id"=>$member->id));
					GroupMember::model()->updateAll(array("member_id"=>$model->id), "member_id=:member_id", array(":member_id"=>$member->id));
					MemberRelationship::model()->updateAll(array("related1_id"=>$model->id), "related1_id=:member_id", array(":member_id"=>$member->id));
					MemberRelationship::model()->updateAll(array("related2_id"=>$model->id), "related2_id=:member_id", array(":member_id"=>$member->id));
					PledgeMember::model()->updateAll(array("member_id"=>$model->id), "member_id=:member_id", array(":member_id"=>$member->id));
					WorshipAttendance::model()->updateAll(array("member_id"=>$model->id), "member_id=:member_id", array(":member_id"=>$member->id));
					WorshipGreeting::model()->updateAll(array("member_id"=>$model->id), "member_id=:member_id", array(":member_id"=>$member->id));
				}
				Member::model()->deleteAll("name=:name AND id<>:id",array(":name"=>$model->name, ":id"=>$model->id));
				$this->redirect(array('duplicate','id'=>$model->id));
			}
		}
		
		$merged_member = $this->loadModel($id);
		foreach ($duplicate_list as $member) {
			if ($member->id == $model->id)
				continue;
				
			if (!empty($member->remarks))
				$merged_member->remarks .= $member->remarks . "\n\n";
			if ($member->english_name != $merged_member->english_name && empty($merged_member->english_name))
				$merged_member->english_name = $member->english_name;
				
			if ($member->photo != $merged_member->photo && empty($merged_member->photo))
				$merged_member->photo = $member->photo;
			if ($member->gender != $merged_member->gender && $merged_member->gender == Member::GENDER_UNKNOWN)
				$merged_member->gender = $member->gender;
			if ($member->birthday != $merged_member->birthday && ($merged_member->birthday == "1970-01-01" || $merged_member->birthday == "0000-00-00"))
				$merged_member->birthday = $member->birthday;
			if ($member->email != $merged_member->email && empty($merged_member->email))
				$merged_member->email = $member->email;
				
			if ($member->believe != $merged_member->believe && empty($merged_member->believe))
				$merged_member->believe = $member->believe;
			if ($member->believe_date != $merged_member->believe_date && ($merged_member->believe_date == "1970-01-01" || $merged_member->believe_date == "0000-00-00"))
				$merged_member->believe_date = $member->believe_date;
			if ($member->baptized != $merged_member->baptized && empty($merged_member->baptized))
				$merged_member->baptized = $member->baptized;
			if ($member->baptized_date != $merged_member->baptized_date && ($merged_member->baptized_date == "1970-01-01" || $merged_member->baptized_date == "0000-00-00"))
				$merged_member->baptized_date = $member->baptized_date;
				
			if ($member->account_type != $merged_member->account_type && $merged_member->account_type == Member::ACCOUNT_TYPE_NEW_MEMBER)
				$merged_member->account_type = $member->account_type;
			if ($member->arrived_date < $merged_member->arrived_date && ($merged_member->arrived_date == "1970-01-01" || $merged_member->arrived_date == "0000-00-00"))
				$merged_member->arrived_date = $member->arrived_date;
			if ($member->create_date != $merged_member->create_date && ($merged_member->create_date == "1970-01-01" || $merged_member->create_date == "0000-00-00"))
				$merged_member->create_date = $member->create_date;
			if ($member->modify_date != $merged_member->modify_date && ($merged_member->modify_date == "1970-01-01" || $merged_member->modify_date == "0000-00-00"))
				$merged_member->modify_date = $member->modify_date;
			if ($member->creator_id != $merged_member->creator_id && empty($merged_member->creator_id))
				$merged_member->creator_id = $member->creator_id;
			if ($member->modifier_id != $merged_member->modifier_id && empty($merged_member->modifier_id))
				$merged_member->modifier_id = $member->modifier_id;
				
			if ($member->address_district != $merged_member->address_district && empty($merged_member->address_district))
				$merged_member->address_district = $member->address_district;
			if ($member->address_estate != $merged_member->address_estate && empty($merged_member->address_estate))
				$merged_member->address_estate = $member->address_estate;
			if ($member->address_house != $merged_member->address_house && empty($merged_member->address_house))
				$merged_member->address_house = $member->address_house;
			if ($member->address_flat != $merged_member->address_flat && empty($merged_member->address_flat))
				$merged_member->address_flat = $member->address_flat;
			if ($member->contact_home != $merged_member->contact_home && empty($merged_member->contact_home))
				$merged_member->contact_home = $member->contact_home;
			if ($member->contact_mobile != $merged_member->contact_mobile && empty($merged_member->contact_mobile))
				$merged_member->contact_mobile = $member->contact_mobile;
			if ($member->contact_office != $merged_member->contact_office && empty($merged_member->contact_office))
				$merged_member->contact_office = $member->contact_office;
			if ($member->contact_others != $merged_member->contact_others && empty($merged_member->contact_others))
				$merged_member->contact_others = $member->contact_others;
		}
		
		$this->render('merge',array(
			'duplicate_members' => $duplicate_list,
			'merged_member' => $merged_member,
		));
	}
}
