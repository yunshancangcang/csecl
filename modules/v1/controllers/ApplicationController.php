<?php
namespace csecl\modules\v1\controllers;
use csecl\models\Application;
use csecl\models\Question;
use csecl\models\Open;
use yii\helpers\ArrayHelper;
use csecl\modules\v1\controllers\common\BaseController;
use Yii;
class ApplicationController extends BaseController
{
    public $modelClass = 'csecl\models\Application';

    public function actions(){
        $actions = parent::actions();
        unset($actions['delete'], $actions['create'],$actions['updata']);
        return $actions;
    }

    //需不需要返回 总共有多少页

    //展示所有报名表  
    // public function actionShow(){
    // 	//一页展示的数  $limit 
    //     // if(!isset($page))  
    //     //     return $this->renderJson([] , 0 , 201 , "page参数没找到");
    //     // if($page == 0)  return $this->renderJson([] , 0 , 404 , "资源不存在");
    //     // $page = $page -1;
    //     // $limit = 10;
    //     // $offset = $limit * $page;
    //     // $count=Application::find()->count();
    //     // if($count<10) $offset = 0;
    //     $i=0;
    //     $personers = (new \yii\db\Query())
    //             ->from('application')
    //             //->limit($limit)
    //             ->orderBy('id')
    //             //->offset($offset)
    //             ->all();
    //     if(!$personers)  return $this->renderJson([] , 0 , 404 , "资源不存在");
    //     foreach ($personers as $personer) {
    //         $question = Question::findOne(['appid'=>$personer['id']]);
    //         $question = ArrayHelper::toArray($question, ['frontend\models\Question' => ['answer1','answer2','answer3','answer4','answer5','answer6'],]);
    //         $personers[$i] = ArrayHelper::merge($personers[$i],$question);
    //         $i++;
    //     }
    //     //$personers['totalpage'] = ceil($count/$limit);
    //     return $this->renderJson($personers , 1 , 200 , []);
    // }

    //展示单个报名表 by id
    public function actionGet($id){
        if(!isset($id))  
            return $this->renderJson([] , 0 , 201 , "id参数没找到");
    	$personer = Application::findOne(['id'=>$id]);
        if(!$personer)  return $this->renderJson([] , 0 , 404 , "资源不存在");
        $personer = ArrayHelper::toArray($personer, ['frontend\models\Application' => [],]);
        $question = Question::findOne(['appid'=>$personer['id']]);
        $question = ArrayHelper::toArray($question, ['frontend\models\Question' => ['answer1','answer2','answer3','answer4','answer5','answer6'],]);
        $personer = ArrayHelper::merge($personer,$question);
        return $this->renderJson($personer , 1 , 200 , []);
	}

    // //展示单个报名表 by id
    // public function actionGet($id){
    //     $personer = Application::findOne(['id'=>$id]);
    //     if(!$personer)  return $this->renderJson([] , 0 , 404 , "资源不存在");
    //     $personer = ArrayHelper::toArray($personer, ['frontend\models\Application' => [],]);
    //     $question = Question::findOne(['appid'=>$personer['id']]);
    //     $question = ArrayHelper::toArray($question, ['frontend\models\Question' => ['answer1','answer2','answer3','answer4','answer5','answer6'],]);
    //     $personer = ArrayHelper::merge($personer,$question);
    //     return $this->renderJson($personer , 1 , 200 , []);
    // }

    //修改报名数据 报名->见习
    public function actionUpda(){
        $data = Yii::$app->request->post();
        if(!isset($data['id'])||!isset($data['status']))  
            return $this->renderJson([] , 0 , 201 , "参数没找到");
        $model = Application::findOne(['id'=>$data['id']]);
        if(!$model) return $this->renderJson([],0,404,'资源不存在修改失败！');
        $model->setAttributes($data);
        if(!$model->save()) return $this->renderJson([],0,201,'修改失败！');
        return $this->renderJson([],1,200,'修改成功！');
    }

    //展示报名表简要信息
	public function actionSimple($page){
        //一页展示的数  $limit 
        if(!isset($page))  
            return $this->renderJson([] , 0 , 201 , "page参数没找到");
        if($page == 0)  return $this->renderJson([] , 0 , 404 , "资源不存在");
        $page = $page - 1;
        $limit = 10;
        $offset = $limit * $page;
        $count=Application::find()->count();
        if($count<10) $offset = 0;
        $personers = (new \yii\db\Query())
                ->select(['id','sex','name','address','grade','college','major','direct','english_grade','math_grade','referrer'])
                ->from('application')
                ->limit($limit)
                ->orderBy('id')
                ->offset($offset)
                ->all();
        if(!$personers)  return $this->renderJson([] , 0 , 404 , "资源不存在");
        $personers['totalpage'] = ceil($count/$limit);
        return $this->renderJson($personers , 1 , 200 , []);
	}

    //填写报名
    public function actionCreateapp(){
        //检测报名状态是否开启
        $model = Open::findOne('1');
        if(!$model->status)
            return $this->renderJson([] , 0 , 200 , "当前时间未开启报名状态");
        
        $data = Yii::$app->request->post();
        if(!isset($data['application']))  
            return $this->renderJson([] , 0 , 201 , "application没找到");
        if(!isset($data['question']))  
            return $this->renderJson([] , 0 , 201 , "question没找到");
        //检查是否重复报名 by number
        $status = Application::find()->where(['number'=>$data['application']['number']])->Count();
        if($status) return $this->renderJson([] , 0 , 200 , "学号已存在请勿重复提交");

        $model = new Application();
        $model->setAttributes($data['application']);
        $model->created = time();
        $model->updated = time();
        if(!$model->save())  return $this->renderJson([] , 0 , 200 , "提交失败！");
        $question = new Question();
        $question->setAttributes($data['question']);
        $question->appid = $model->id;
        $question->created = time();
        $question->updated = time();
        $question->save();
        return $this->renderJson([] , 1 , 200 , "提交成功！");
    }

    //检查是否已提交过申请表
    public function actionChk(){
       $data = Yii::$app->request->post();
       if(!isset($data['number']))  
            return $this->renderJson([] , 0 , 200 , "number参数没找到");
       $status = Application::find()->where(['number'=>$data['number']])->Count();
       if($status) return $this->renderJson([] , 1 , 200 , "请勿重复提交");
       return $this->renderJson([] , 0 , 200, "学号未进行过提交"); 
    }

    //按方向分组 orderby id
    public function actionShow(){
        $personers[1]= Application::getdate("程序","A");
        $personers[2]= Application::getdate("前端","B");
        $personers[3]= Application::getdate("产品","C");
        return $this->renderJson($personers, 1 , 200, "");
    }

    //设置报名开启状态
    public function actionSet(){
        $status = Yii::$app->request->post('status');
        if(!isset($status))  
            return $this->renderJson([] , 0 , 200 , "status参数没找到");
        $model = Open::findOne('1');
        $model->status = ($status==1 ? 1 : 0 );
        if($model->save())
            return $this->renderJson([] , 1 , 200 , "报名开启状态修改成功");
        else
            return $this->renderJson([] , 0 , 200 , "报名开启状态修改失败");
    }

    //返回报名开启状态 1为已开启 0为未开启
    public function actionStatus(){
        $model = Open::findOne('1');
        $status = $model->status;
        if($status == 1)
            return $this->renderJson([],1,200,'报名状态已开启');
        if($status == 0)
            return $this->renderJson([],0,200,'报名状态已关闭');
    }
}