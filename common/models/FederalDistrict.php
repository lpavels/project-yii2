<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "federal_district".
 *
 * @property int $id
 * @property string $name
 */
class FederalDistrict extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'federal_district';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Федеральный округ',
        ];
    }
}
