<?php

namespace reportmanager\controllers;

use Yii;
use reportmanager\models\Reports;
use reportmanager\models\ReportsConditions;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReportManagerController implements the CRUD actions for Reports model.
 */
class ReportManagerController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function getViewPath()
    {
        return Yii::getAlias('@reportmanager/views');
    }

/*
    public function beforeAction($action)
    {
//        $this->module->registerTranslation();
        parent::beforeAction($action);
    }
*/

    /**
     * Lists all Reports models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Reports();

        $model->load(Yii::$app->request->post()) && $model->save();

        $dataProvider = new ActiveDataProvider([
            'query' => Reports::find(),
            'sort' => ['defaultOrder' => ['name' => SORT_ASC]],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Lists all Conditions for the Reports models.
     * @return mixed
     */
    public function actionConditions($report_id)
    {
        $report = $this->findModel($report_id);
        $model = new ReportsConditions(['report_id' => $report->id]);

        $model->load(Yii::$app->request->post()) && $model->save();

        $dataProvider = new ActiveDataProvider([
            'query' => $report->getReportsConditions(),
            'sort' => ['defaultOrder' => ['attribute_name' => SORT_ASC]],
        ]);

        return $this->render('conditions', [
            'dataProvider' => $dataProvider,
            'report' => $report,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Reports model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Reports model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Reports();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
//                'classes_list' => $this->module->reportClasses,
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
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save() && !Yii::$app->request->isPjax) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
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
        if (($model = Reports::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
