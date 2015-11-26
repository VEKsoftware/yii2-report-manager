<?php

namespace reportmanager\controllers;

use Yii;
use reportmanager\models\Reports;
use reportmanager\models\ReportsConditions;
use reportmanager\ReportManagerAccessInterface;

use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\base\ErrorException;

/**
 * ReportManagerController implements the CRUD actions for Reports model.
 */
class ReportManagerController extends Controller
{
    public $defaultAction = 'index';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'delete-condition' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Reports models.
     * @return mixed
     */
    public function actionIndex()
    {
//        $access = new $this->module->accessClass;
        if(isset($access) && ! $access->isAllowed('reportmanager.index')) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('reportmanager','Operation is not allowed!'));
        }

        if($this->module->reportModelClass) {
            $repClass = $this->module->reportModelClass;
        } else {
            throw new ErrorException('You need to initialize reportModelClass variable in $config["model"] by a child of \reportmanager\models\Reports');
        }

        $repDataProvider = new ActiveDataProvider([
            'query' => $repClass::findReports(),
            'sort' => ['defaultOrder' => ['name' => SORT_ASC]],
        ]);

        return $this->render('index', [
            'dataProvider' => $repDataProvider,
        ]);
    }

    /**
     * Displays a single Reports model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $report = $this->findModel($id);

        $dataProvider = $report->generateReport();

        return $this->render('view', [
            'model' => $report,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Reports model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = Reports::instantiate([]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Reports model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $report = $this->findModel($id);

        if($report->load(Yii::$app->request->post()) && $report->save()) {
            return $this->redirect(['view', 'id' => $report->id]);
        }

        $condDataProvider = new ActiveDataProvider([
            'query' => ReportsConditions::find()->where(['report_id' => $report->id])->with('report'),
            'sort' => ['defaultOrder' => ['order' => SORT_ASC]],
        ]);

        return $this->render('update', [
            'report' => $report,
            'condDataProvider' => $condDataProvider,
        ]);
    }

    /**
     * Work on conditions

     * @param integer $id
     * @return mixed
     */
    public function actionCondition($report_id, $id=NULL)
    {
        if($id) {
            $condition = $this->findCondition($id);
            $report = $condition->report;
        } else {
            $report = $this->findModel($report_id);
            $condition = new ReportsConditions(['report_id' => $report->id]);
        }

        if($condition->load(Yii::$app->request->post()) && $condition->save() && NULL !== Yii::$app->request->post('save')) {
            return $this->redirect(['update', 'id' => $report->id]);
        }

        $condDataProvider = new ActiveDataProvider([
            'query' => ReportsConditions::find()->where(['report_id' => $report->id])->with('report'),
            'sort' => ['defaultOrder' => ['order' => SORT_ASC]],
        ]);

        return $this->render('update', [
            'report' => $report,
            'condDataProvider' => $condDataProvider,
            'condition' => $condition,
        ]);
    }

    /**
     * Deletes an existing ReportsConditions model.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteCondition($id)
    {
        $condition = $this->findCondition($id);
        $report_id = $condition->report_id;
        $condition->delete();

        return $this->redirect(['update','id' => $report_id]);
    }

    /**
     * Deletes an existing Reports model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Reports model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Reports the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if($this->module->reportModelClass) {
            $repClass = $this->module->reportModelClass;
        } else {
            throw new ErrorException('You need to initialize reportModelClass variable in $config["model"] by a child of \reportmanager\models\Reports');
        }

        if (($model = $repClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findCondition($id)
    {
        if (($model = ReportsConditions::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
