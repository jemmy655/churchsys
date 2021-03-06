<?php

class CourseController extends Controller
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
				'actions'=>array('index','addNewLesson','updateAttendance','view','viewOwn','create','update','removeMember', 'admin','delete','ajaxAddNewMember','ajaxAddNewAttendance'),
				'roles'=>array('courseManager', 'pastor', 'preacher', 'deacon', 'staff', 'itadmin'),
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
		$criteria=new CDbCriteria(array(
	        'condition'=>'course_id='.$id,
	        'order'=>'member_id',
	    ));
	    $dataProvider=new CActiveDataProvider('CourseMember', array(
	        'pagination'=>array(
	            'pageSize'=>20,
	        ),
	        'criteria'=>$criteria,
	    ));
		
		$this->render('view',array(
			'model'=>$this->loadModel($id),
	        'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Course;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Course']))
		{
			$model->attributes=$_POST['Course'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
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

		if(isset($_POST['Course']))
		{
			$model->attributes=$_POST['Course'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}
	
	public function actionUpdateAttendance()
	{
		
	}
	
	public function actionAddNewLesson()
	{
		if (!empty($_REQUEST["newLessonDate"]) && !empty($_REQUEST["course_id"]))
		{
			$course_id = (int) $_REQUEST["course_id"];
			$criteria=new CDbCriteria(array(
		        'condition'=>'course_id='.$course_id,
				'order'=>'member_id',
		    ));
		    $coursemember_list = CourseMember::model()->findAll($criteria);
		    foreach ($coursemember_list as $coursemember)
		    {
		    	$model = new CourseAttendance();
		    	$model->course_id = $coursemember->course_id;
		    	$model->member_id = $coursemember->member_id;
		    	$model->attendance_date = $_REQUEST["newLessonDate"];
		    	$model->lesson_number = 0; // TODO: find lesson number
		    	$model->state = 0; // Set to absent defaultly
		    	$model->save();
		    }
		}
		
		$this->redirect($this->createUrl('ajaxAddNewAttendance', array('id'=>(int) $_REQUEST["course_id"])));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	public function actionRemoveMember()
	{
		if(!empty($_REQUEST["member_id"]) && !empty($_REQUEST["course_id"]))
		{
			CourseMember::model()->deleteAllByAttributes(array("member_id"=>$_REQUEST["member_id"], "course_id"=>$_REQUEST["course_id"]));
			$this->redirect($this->createUrl('ajaxAddNewMember', array('id'=>$_REQUEST["course_id"])));
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
		$model=new Course('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Course']))
			$model->attributes=$_GET['Course'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	
	
	public function actionAjaxAddNewMember()
	{
		$model=new CourseMember;
		if (isset($_POST["CourseMember"])) {
			$model->attributes=$_POST['CourseMember'];
			if (CourseMember::model()->countByAttributes(array("member_id"=>$model->member_id, "course_id"=>$model->course_id)) <= 0)
			{
				$model->save();
			}
		} elseif (isset($_REQUEST["id"])) {
			$model->course_id = $_REQUEST["id"];
		}
		$criteria=new CDbCriteria(array(
	        'condition'=>'course_id='.$model->course_id,
	    ));
		$data = $model->findAll($criteria);
//		$dataProvider=new CActiveDataProvider('CourseMember', array(
//	        'pagination'=>array(
//	            'pageSize'=>20,
//	        ),
//	        'criteria'=>$criteria,
//	    ));
		$this->renderPartial('ajaxAddNewMember', array(
			"model" => $model,
			"data_list" => $data
		), false, true);
		Yii::app()->end();
	}
	
	public function actionAjaxAddNewAttendance()
	{
		$model=new CourseAttendance;
		
		if (isset($_POST["CourseAttendance"])) {
//			$model->attributes=$_POST['CourseAttendance'];
//			if (CourseMember::model()->countByAttributes(array("member_id"=>$model->member_id, "course_id"=>$model->course_id)) <= 0)
//			{
//				$model->save();
//			}
		} elseif (isset($_REQUEST["id"])) {
			$model->course_id = $_REQUEST["id"];
		}
		
		$query = "SELECT DISTINCT attendance_date " . 
				"FROM tbl_course_attendance " . 
				"WHERE course_id=" . ((int) $_REQUEST["id"]) . " " . 
				"ORDER BY attendance_date ASC";
		$date_list = Yii::app()->db->createCommand($query)->queryAll();
		
		$criteria=new CDbCriteria(array(
	        'condition'=>'course_id='.$model->course_id,
			'order'=>'member_id',
	    ));
	    $coursemember_list = CourseMember::model()->findAll($criteria);
	    
	    $data_list = array();
	    foreach ($coursemember_list as $coursemember) {
	    	$data_list[$coursemember->member_id] = array();
	    	foreach ($date_list as $date) {
				$ca = CourseAttendance::model()->findByAttributes(array("course_id"=>$model->course_id, "member_id"=>$member->id, "attendance_date"=>$date));
				if (empty($ca))
	    			$data_list[$member->id][$date] = new CourseAttendance;
	    		else
	    			$data_list[$member->id][$date] = $ca;
	    	}
	    }
		$this->renderPartial('ajaxAddNewAttendance', array(
			"model" => $model,
			"date_list" => $date_list,
			"coursemember_list" => $coursemember_list,
			"data_list" => $data_list
		), false, true);
		Yii::app()->end();
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Course::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='course-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
