<?php

namespace lnpay\link;

use Yii;

/**
 * This is the model class for table "link_type".
 *
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string $metadata
 *
 * @property Link[] $links
 */
class Type extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'link_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['metadata','description'], 'string'],
            [['name', 'display_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'display_name' => 'Display Name',
            'description'=>'Description',
            'metadata' => 'Metadata',
        ];
    }


    public function fields()
    {
        $fields = parent::fields();

        unset($fields['id'], $fields['metadata']);
        return $fields;
    }
}