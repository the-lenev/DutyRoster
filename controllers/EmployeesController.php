<?php

namespace app\controllers;

use Yii;
use app\models\Employees;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EmployeesController implements the CRUD actions for Employees model.
 */
class EmployeesController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Employees models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Employees::find(),
            'pagination' => [
                'pageSize' => isset(Yii::$app->params['pageSize']) ? Yii::$app->params['pageSize'] : 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Employees model.
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
     * Creates a new Employees model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $employee = new Employees();

        if ($employee->load(Yii::$app->request->post())) {

            // Получаем максимальную(последнюю) дату из всех записей
            $max_date = Employees::find()->max('date_next');

            $employee->date_next = $this->nextDate(false, $max_date);

            if ($employee->save()) {
                // return $this->redirect(['view', 'id' => $model->id]);
                return $this->redirect(['index']);
            }
        } else {
            return $this->render('create', [
                'model' => $employee,
            ]);
        }
    }

    /**
     * Updates an existing Employees model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $request = Yii::$app->request;
        if ($model->load($request->post())) {
            if ($model->save()) {
                // return $this->redirect(['view', 'id' => $model->id]);
                return $this->redirect(['index']);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Employees model.
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
     * Finds the Employees model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Employees the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Employees::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdate_schedule() {
        // Получаем всех сотрудников, без учета присутствия в офисе
        $employees = Employees::find()->all();
        // Количество людей, в офисе
        $num_empl = 0;
        // Текущий дежурный
        $curr_attendant = false;
        // Индекс текущего дежурного
        $curr_index = 0;
        // Последний дежурный
        $last_attendant = false;
        // Индекс последнего дежурного
        $last_index = 0;
        // Следующие дежурные (с отсрочкой)
        $next_attendants = [];
        // Изменение графика
        $change_date = false;
        // Переменная для хранения предыдущей даты дежурств, изначально равна текущему дню
        $date_next = date('Y-m-d');

        // Определяем исходные параметры для сортировки
        foreach ($employees as $key => $employee) {
            // Если предыдущий сотрудник, который должен дежурить, не в офисе, то устанавливаем дежурство, на следующего по списку
            if ($change_date) {
                $employee->date_next = date('Y-m-d');
                $change_date = false;
            }
            // Из сотрудников, находящихся в офисе
            if ($employee->in_office == 1) {
                // Находим текущего дежурного
                if ($employee->date_next == date('Y-m-d') || $employee->date_prev == date('Y-m-d')) {
                    // Запоминаем текущего дежурного
                    $curr_attendant = $employee;
                    // Запоминаем его индекс
                    $curr_index = $key;
                    // На данный этапе текущий дежурный так же будет являться и последним
                    $last_attendant = $employee;
                    $last_index = $key;
                }
                // Если у сотрудника была отсрочка и он не текущий дежуный, то сохраняем его в отдельный массив
                elseif ($employee->postponement == 1) {
                    // Запоминаем индекс
                    $next_attendants[$key] = $employee;
                }
            }
            // Если сотрудник не в офисе
            // Проверяем, установлено ли текущее дежурство на отсутствующего сотрудника
            elseif ($employee->date_next == date('Y-m-d') || $employee->date_prev == date('Y-m-d')) {
                    // Обнуляем дату следующего дежурства
                    $employee->date_next = null;
                    // Устанавливаем отсрочку
                    $employee->postponement = 1;
                    // Устанавливаем изменение даты для следующего сотрудника, так как дежурство переходит на него
                    $change_date = true;
            }
            // Считаем сколько людей в офисе или не имеет отсрочки
            // По ним будем рассчитывать следующую дату дежурства
            if ($employee->in_office == 1 || $employee->postponement == 0) {
                $num_empl++;
            }
            // Если установлена дата предыдущего дежурства и она меньше, чем дата последнего дежурного (на данной итерации), то
            // переопределяем последнего дежурного
            if ($employee->date_prev != null && $last_attendant && ($employee->date_prev < $last_attendant->date_next)) {
                $last_attendant = $employee;
                $last_index = $key;
            }
        }
        unset($employee);

        // Если сотрудника с текущей датой дежурства нет, то устанавливаем дежурным первого сотрудника
        if (!$curr_attendant) {
            $curr_attendant = $employees[0];
        }
        // Если последнего дежурного не установлено, то устанавливаем последним дежурным текущего сотрудника
        if (!$last_attendant) {
            $last_attendant = $curr_attendant;
        }
        // $employees[$last_index]->last_duty = 1;

        /* Даты для текущего дежурного */
        // Устанавливаем дату следующего дежурства (если не установлена вручную)
        // и сдвигаем дату дежурства на +1 для следующих сотрудников
        $date_next = $this->nextDate($curr_attendant, $date_next);
        // Устанавливаем дату предыдущего дежурства (сегодняшнюю)
        $curr_attendant->date_prev = $curr_attendant->date_next;
        // Сбрасываем отсрочку (если она была)
        $curr_attendant->postponement = 0;
        $curr_attendant->save();

        // Если предопределены следующие дежурные, устанавливаем дежурства для них
        if (!empty($next_attendants)) {
            foreach ($next_attendants as $next) {
                $date_next = $this->nextDate($next, $date_next);
                $next->save();
            }
        }
        // Открываем транзакцию
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $before = true;
            // Устанавливаем дежурства для всех остальных сотрудников
            foreach ($employees as $key => $employee) {
                // Если первая итерация в цикле...
                if ($key == 0) {
                    // Сохраняем текущую дату дежурства в промежуточную переменную
                    $date_next_save = $date_next;
                    // Устанавливаем дату дежурств, исходя из количества сотрудников после последнего дежурного и сотрудников с отсрочкой
                    // чтобы сотрудники до последнего дежурного, не дежурили раньше сотрудников, расположенных после него (исключая сотрудников с отсрочкой)
                    $index = $last_index;
                    foreach ($next_attendants as $key => $next) {
                        if ($last_index > $key) {
                            $index--;
                        }
                    }
                    $date_next = $this->lastDate(date('Y-m-d'), $num_empl - $index);
                }
                // Если прошли последнего дежурного, то
                elseif (($last_index == 0 || $last_index == $key - 1) && $before) {
                    // Восстанавливаем предустановленную (рассчитанную до цикла) дату дежурств
                    $date_next = $date_next_save;
                    $before = false;
                }
                // Если сотрудник не имел и не имеет отсрочки, то ...
                if (!in_array($employee, $next_attendants) && $employee->postponement == 0) {
                    $date_next = $this->nextDate($employee, $date_next);
                }
                $employee->save();
            }
            // Если все впорядке, закрываем транзакцию
            $transaction->commit();
        } catch (\Exception $e) {
            // Иначе откатываем транзакцию
            $transaction->rollBack();
            // И выбрасываем исключение
            throw $e;
        }

        return $this->redirect(['index']);
    }

    public function actionReset()
    {
        $models = Employees::find()->all();

        // Открываем транзакцию
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Перебираем все записи сотрудников в БД
            foreach ($models as $model) {
                $model->date_prev = null;
                $model->date_next = null;
                // $model->last_duty = 0;
                $model->save();
            }
            // Если все впорядке, закрываем транзакцию
            $transaction->commit();
        } catch (\Exception $e) {
            // Иначе откатываем транзакцию
            $transaction->rollBack();
            // И выбрасываем исключение
            throw $e;
        }

        return $this->redirect(['index']);
    }

    /**
    * Функция устанавливает дату следующего дежурства у сотрудника, если он передан
    * и возвращает дату смещенную на указанное количество дней (по умолчанию на 1)
    */
    protected function nextDate($employee, $date_next, $num_days = 1)
    {
        if ($employee) {
            $employee->date_next = $date_next;
        }
        $date_time = new \DateTime($date_next);
        // Устанавливаем следующее дежурство
        $date_time->modify('+'.$num_days.' day');
        // Если дежурство попадает на выходной или праздник, то сдвигаем его
        while ($date_time->format('w') == 6 || $date_time->format('w') == 0 || in_array($date_time->format('Y-m-d'), Yii::$app->params['holidays'])) {
            $date_time->modify('+1 day');
        }
        return $date_time->format('Y-m-d');
    }

    // Функция возвращает дату последнего дежурства
    protected function lastDate($date, $num_employees)
    {
        $date_time = new \DateTime($date);
        for ($i = 0; $i < $num_employees; $i++) {
            $date_time->modify('+1 day');
            while ($date_time->format('w') == 6 || $date_time->format('w') == 0 || in_array($date_time->format('Y-m-d'), Yii::$app->params['holidays'])) {
                $date_time->modify('+1 day');
            }
        }
        return $date_time->format('Y-m-d');
    }
}
