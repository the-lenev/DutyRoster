<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Employees';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employees-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Employees', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <p>
        <?= Html::a('Update schedule', ['update_schedule'], ['class' => 'btn btn-info']) ?>
    </p>
    <p>
        <?= Html::a('Reset', ['reset'], ['class' => 'btn btn-danger']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // Класс таблицы
        'tableOptions' => [
            'class' => 'table table-striped table-bordered'
        ],
        // Класс строк
        'rowOptions' => function ($model, $key, $index, $grid) {
            $class = $index%2 ? 'odd' : 'even';
            return [
                'key'=>$key,
                'index'=>$index,
                'class'=>$class
            ];
        },
        // Шаблон отображения
        // sorter - отдельная сортировка
        // pager - блок постраничной навигации (при превышении количества)
        // summary - общее количество записей
        // items - сама таблица
        //'layout'=>"{sorter}\n{pager}\n{summary}\n{items}",
        'layout'=>"{summary}\n{items}\n{pager}",
        // Отключаем отображение общего количества записей
        'summary' => false,
        // Отображение заголовка
        'showHeader' => true,
        // Отображение футера
        'showFooter' => false,
        // Отображение пустой таблицы
        'showOnEmpty' => true,
        // Значения в пустых ячейках
        'emptyCell' => '_',

        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id:text:ID',

            // поле/формат данных/метка
            'name:text:Last name',

            //['class' => yii\grid\CheckboxColumn::className()],

            // // Один вариант
            // [
            //     'attribute' => 'parent_id',
            //     'label' => 'Родительская категория',
            //     'contentOptions' =>function ($model, $key, $index, $column){
            //         return ['class' => 'name'];
            //     },
            //     'content'=>function($data){
            //         return "value";
            //     }
            // ],
            // // Другой вариант
            // [
            //    'attribute' => 'category_image',
            //    'label' => 'Изображение категории',
            //    'contentOptions' => [
            //        'class' => 'table_class',
            //        'style' => 'display:block;'
            //    ],
            //    'content' => function($data){
            //        return "value";
            //    }
            // ],

            //'date_prev:date:Date prev',
            // Вариант с явным указанием формата вывода даты/времени
            [
                'attribute' => 'date_prev',
                'format' =>  ['date', 'php:Y-m-d'],
                //'options' => ['width' => '200'],
                'label' => 'Date prev',
            ],

            //'date_next:date:Date next',
            [
                'attribute' => 'date_next',
                'format' =>  ['date', 'php:Y-m-d'],
                //'options' => ['width' => '200'],
                'label' => 'Date next',
            ],

            //'in_office:boolean:In office',
            // [
            //     'attribute' => 'in_office',
            //     'format' =>  'boolean',
            //     'label' => 'In office',
            //     //'options' => ['width' => '200']
            // ],
            [
                // Название поля модели
                'attribute' => 'in_office',
                'label' => 'In office',
                /**
                 * Формат вывода.
                 * В этом случае мы отображает данные, как передали.
                 * По умолчанию все данные прогоняются через Html::encode()
                 */
                'format' => 'raw',
                /**
                 * Переопределяем отображение фильтра.
                 * Задаем выпадающий список с заданными значениями вместо поля для ввода
                 */
                'filter' => [
                    0 => 'No',
                    1 => 'Yes',
                ],
                /**
                 * Переопределяем отображение самих данных.
                 * Вместо 1 или 0 выводим Yes или No соответственно.
                 * Попутно оборачиваем результат в span с нужным классом
                 */
                'value' => function ($model, $key, $index, $column) {
                    $active = $model->{$column->attribute} === 1;
                    return \yii\helpers\Html::tag(
                        'span',
                        $active ? 'Yes' : 'No',
                        [
                            'class' => 'label label-' . ($active ? 'success' : 'danger'),
                        ]
                    );
                },
            ],
            [
                /**
                 * Название поля модели
                 */
                'attribute' => 'postponement',
                /**
                 * Формат вывода.
                 * В этом случае мы отображает данные, как передали.
                 * По умолчанию все данные прогоняются через Html::encode()
                 */
                'format' => 'raw',
                /**
                 * Переопределяем отображение фильтра.
                 * Задаем выпадающий список с заданными значениями вместо поля для ввода
                 */
                'filter' => [
                    0 => 'No',
                    1 => 'Yes',
                ],
                /**
                 * Переопределяем отображение самих данных.
                 * Вместо 1 или 0 выводим Yes или No соответственно.
                 * Попутно оборачиваем результат в span с нужным классом
                 */
                'value' => function ($model, $key, $index, $column) {
                    $active = $model->{$column->attribute} === 1;
                    return \yii\helpers\Html::tag(
                        'span',
                        $active ? 'Yes' : 'No',
                        [
                            'class' => 'label label-' . ($active ? 'success' : 'danger'),
                        ]
                    );
                },
            ],
            [
                // Класс действий со строками
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Действия',
                'headerOptions' => ['width' => '80'],
                'template' => '{view} {update} {delete} {link}',
                // Изменение базовых отображений картинок
                'buttons' => [
                    // 'update' => function ($url,$model) {
                    //     return Html::a(
                    //     '<span class="glyphicon glyphicon-screenshot"></span>',
                    //     $url);
                    // },
                    // 'link' => function ($url,$model,$key) {
                    //     return Html::a('Действие', $url);
                    // },
                ],
            ],
        ],
    ]); ?>
</div>
