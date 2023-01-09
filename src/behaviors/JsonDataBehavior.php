<?php

namespace lnpay\behaviors;

use lnpay\components\HelperComponent;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\base\Behavior;

/**
 * JsonDataBehavior adds functions for storing, retrieving, and deleting JSON data from a column in a table
 *
 * To use JsonDataBehavior, insert the following code to your ActiveRecord class:
 *
 * ```php
 * use tkijewski\JsonDataBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         JsonDataBehavior::className(),
 *     ];
 * }
 * ```
 *
 * This behavior takes arrays and manipulates a JSON data field in a table. You can append/add keys, delete keys
 * and get by key
 *
 * If the model exists in the DB it is saved by this behavior. If it doesn't exist, the JSON string is loaded in the
 * attribute. It will be saved when user calls save.
 *
 * Because attribute values will be set automatically by this behavior, they are usually not user input and should therefore
 * not be validated, i.e. `json_data` should not appear in the [[\yii\base\Model::rules()|rules()]] method of the model.
 *
 * For the above implementation to work with MySQL database, please declare the columns(`json_data`) as JSON or LONGTEXT
 *
 * If your attribute name is different you may configure the [[attribute]] property like the following:
 *
 * ```php
 * use yii\db\Expression;
 *
 * public function behaviors()
 * {
 *     return [
 *         [
 *             'class' => JsonDataBehavior::className(),
 *             'attribute' => 'json_data'
 *         ],
 *     ];
 * }
 * ```
 *
 *
 * USAGE:
 *
 * $model = Model::findOne($id);
 * $model->appendJsonData(['likes_dogs'=>1]);
 *
 * //$model->json_data -> {"likes_dogs":1}
 *
 * WARNING ----------------------------------------------------------------
 *
 * USING NUMBERS AS KEYS DOES NOT WORK RIGHT! THEY KEY VALUE IS IGNORED
 *
 * @author Tim Kijewski <bootstrapbandit7@gmail.com>
 */
class JsonDataBehavior extends Behavior
{
    /**
     * @var string the attribute of the JSON data
     */
    public $attribute = 'json_data';

    private $inMemoryJsonArray = [];

    /**
     * @var array attributes that don't need a column in db but are stored in json
     */
    public $extraAttributes = [];


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_BEFORE_INSERT => 'updateAttributesBeforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'updateAttributesBeforeSave'
        ];
    }

    /**
     * Pull the extra attributes from the json and assign to model vars
     */
    public function afterFind()
    {
        $this->inMemoryJsonArray = $this->getJsonData();
        foreach ($this->extraAttributes as $ea) {
            $this->owner->{$ea} = @$this->inMemoryJsonArray['extraAttributes'][$ea];
        }
    }

    public function updateAttributesBeforeSave()
    {
        if (!empty($this->extraAttributes)) {
            $array = [];
            foreach ($this->extraAttributes as $ea) {
                $array[$ea] = $this->owner->{$ea};
            }
            $data = ArrayHelper::merge($this->inMemoryJsonArray,['extraAttributes'=>$array]);
            $this->owner->{$this->attribute} = new \yii\db\JsonExpression($data);
        }

    }



    /**
     * Return all of the json data as array if $key is null. Otherwise return key specific value
     * @param null $key
     * @return mixed|null
     */
    public function getJsonData($key=NULL)
    {
        $data = $this->owner->{$this->attribute};

        if ($data instanceof \yii\db\JsonExpression)
            $data = $data->jsonSerialize();
        else if (!is_array($data) && !empty($data))
            $data = json_decode($data,TRUE);

        if (!$data)
            return null;

        if (!$key)
            return $data;

        return @$data[$key];
    }

    /**
     * Append custom user json data
     * @param $array
     * @param bool $truncate clear existing json_data
     * @return mixed
     * @throws \Exception
     */
    public function appendJsonData($array,$truncate=FALSE)
    {
        if (!is_array($array))
            throw new \Exception('Array is required!');

        $attribute = $this->attribute;

        if ($truncate)
            $data = NULL;
        else
            $data = $this->inMemoryJsonArray;

        //Append to existing json data
        if ($data) {
            $data = ArrayHelper::merge($data,$array);
        } else { // start fresh
            $data = $array;
        }

        $this->owner->updateAttributes([$attribute=>new \yii\db\JsonExpression($data)]);

        $this->inMemoryJsonArray = $this->getJsonData();
        if ($this->owner->getIsNewRecord()) {
            return $this->getJsonData();
        } else {
            if ($this->owner->save(false)) {
                return $this->inMemoryJsonArray;
            }
            else
                throw new \Exception('Unable to save json data: '.print_r($this->owner->errors,TRUE));
        }
    }

    /**
     * delete all keys in array. if empty delete all
     * @param null $arrayOfKeys
     * @return mixed
     * @throws \Exception
     */
    public function deleteJsonData($arrayOfKeys=null)
    {
        $data = $this->getJsonData();
        $attribute = $this->attribute;

        if (!$arrayOfKeys)
            $data = NULL;


        if (!empty($arrayOfKeys) && is_array($arrayOfKeys)) {
            foreach ($arrayOfKeys as $key) {
                self::recursive_unset($data,$key);
            }
        }

        $data = ($data?$data:null);
        $this->owner->updateAttributes([$attribute=>new \yii\db\JsonExpression($data)]);
        if ($this->owner->getIsNewRecord()) {
            return true;
        } else {
            if ($this->owner->save())
                return true;
            else
                throw new \Exception(HelperComponent::getFirstErrorFromFailedValidation($this->owner));
        }
    }

    public static function recursive_unset(&$array, $unwanted_key) {
        unset($array[$unwanted_key]);
        if (!empty($array)) {
            foreach ($array as &$value) {
                if (is_array($value)) {
                    self::recursive_unset($value, $unwanted_key);
                }
            }
        }

    }
}
