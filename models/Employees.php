<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "employees".
 *
 * @property integer $id
 * @property string $name
 * @property string $date_prev
 * @property string $date_next
 * @property integer $postponement
 * @property integer $in_office
 */
class Employees extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employees';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'in_office'], 'required'],
            [['date_prev', 'date_next', 'postponement'], 'safe'],
            [['in_office', 'postponement'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t( 'custom', 'Name'),
            'date_prev' => Yii::t('custom', 'Date prev'),
            'date_next' => Yii::t('custom', 'Date next'),
            'in_office' => Yii::t('custom', 'In office'),
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            return true;
        }
        return false;
    }
}
