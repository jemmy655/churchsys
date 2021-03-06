<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'member-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); ?>

	<p class="note"><span class="required">*</span>必須填寫.</p>
	
	<?php echo $form->errorSummary($model); ?>
	
	<img src="<?php echo (file_exists("images/member/" . $model->code . ".jpg") ? "images/member/" . $model->code . ".jpg" : "images/anonymous.gif"); ?>" width="200"/>
<?php echo $form->fileField($model, 'photo'); ?>
	
<?php if (isset($_REQUEST["toMember"])) : ?>
	<div class="row">
		<h2>請製作新名牌!!!</h2>
		<?php echo $form->hiddenField($model, 'new_card', array('value'=>Member::NEW_CARD_WAITING_CARD)); ?>
	</div>
<?php else: ?>
	<?php echo $form->hiddenField($model, 'new_card', array('value'=>$model->new_card)); ?>
<?php endif; ?>
	<fieldset>
		<legend>個人資料</legend>

	<div class="row">
		<?php echo $form->labelEx($model,'account_type'); ?>
<?php if (isset($_REQUEST["toMember"])) : ?>
		<?php echo $form->hiddenField($model, 'account_type', array("value" => Member::ACCOUNT_TYPE_MEMBER)); ?>
		<?php echo $model->getAccountTypeList(Member::ACCOUNT_TYPE_NEW_MEMBER) . " --> " . $model->getAccountTypeList(Member::ACCOUNT_TYPE_MEMBER); ?>
<?php else :?>		
		<?php echo $form->dropDownList($model, 'account_type', $model->getAccountTypeList()); ?>
<?php endif; ?>
		<?php echo $form->error($model,'account_type'); ?>
		<?php echo $form->hiddenField($model, 'state', array("value" => $model->state)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'code'); ?>
<?php if (isset($_REQUEST["toMember"])) : ?>
		<?php echo $form->hiddenField($model, 'code', array("value" => $nextCode)); ?>
		<?php echo $model->code . " --> " . $nextCode; ?>
<?php else :?>		
		<?php echo $form->textField($model,'code',array('size'=>10,'maxlength'=>10)); ?>
<?php endif; ?>
		<?php echo $form->error($model,'code'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'english_name'); ?>
		<?php echo $form->textField($model,'english_name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'english_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'remarks'); ?>
		<?php echo $form->textArea($model,'remarks',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'remarks'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'gender'); ?>
		<?php echo $form->dropDownList($model, 'gender', $model->getGenderList()); ?>
		<?php echo $form->error($model,'gender'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'birthday'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'Member[birthday]',
    // additional javascript options for the date picker plugin
    'options'=>array(
        'showAnim'=>'fold',
    	'dateFormat' => 'yy-mm-dd',
    ),
    'htmlOptions'=>array(
        'style'=>'height:15px;'
    ),
    'value'=>$model->birthday
));?>
		<?php echo $form->error($model,'birthday'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>
</fieldset>
<fieldset>
	<legend>信仰狀況</legend>
	<div class="row">
		<?php echo $form->labelEx($model,'believe'); ?>
		<?php echo $form->textField($model,'believe',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'believe'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'believe_date'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'Member[believe_date]',
    // additional javascript options for the date picker plugin
    'options'=>array(
        'showAnim'=>'fold',
    	'dateFormat' => 'yy-mm-dd',
    ),
    'htmlOptions'=>array(
        'style'=>'height:15px;'
    ),
    'value'=>$model->believe_date
));?>
		<?php echo $form->error($model,'believe_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'baptized'); ?>
		<?php echo $form->textField($model,'baptized',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'baptized'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'baptized_date'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'Member[baptized_date]',
    // additional javascript options for the date picker plugin
    'options'=>array(
        'showAnim'=>'fold',
    	'dateFormat' => 'yy-mm-dd',
    ),
    'htmlOptions'=>array(
        'style'=>'height:15px;'
    ),
    'value'=>$model->baptized_date
));?>
		<?php echo $form->error($model,'baptized_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'arrived_date'); ?>
		<?php echo $form->textField($model,'arrived_date'); ?>
		<?php echo $form->error($model,'arrived_date'); ?>
	</div>
</fieldset>
<fieldset>
	<legend>聯絡資料</legend>

	<div class="row">
		<?php echo $form->labelEx($model,'address_district'); ?>
		<?php echo $form->textField($model,'address_district',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'address_district'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'address_estate'); ?>
		<?php echo $form->textField($model,'address_estate',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'address_estate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'address_house'); ?>
		<?php echo $form->textField($model,'address_house',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'address_house'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'address_flat'); ?>
		<?php echo $form->textField($model,'address_flat',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'address_flat'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'contact_home'); ?>
		<?php echo $form->textField($model,'contact_home',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'contact_home'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'contact_mobile'); ?>
		<?php echo $form->textField($model,'contact_mobile',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'contact_mobile'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'contact_office'); ?>
		<?php echo $form->textField($model,'contact_office',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'contact_office'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'contact_others'); ?>
		<?php echo $form->textField($model,'contact_others',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'contact_others'); ?>
	</div>
</fieldset>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
