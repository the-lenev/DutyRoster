<?php
use yii\grid\GridView;

$this->title = Yii::t('custom', 'Duty Roster');
?>
<div class="site-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            'date_prev:date',
            'date_next:date',
            //'in_office',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
