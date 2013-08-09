<?php
/**
 * This is the model class for table "cron_jobs".
 *
 * The followings are the available columns in table 'cron_jobs':
 * @property integer $id
 * @property string $execute_after
 * @property string $executed_started
 * @property string $executed_finished
 * @property string $action
 * @property string $parameters
 * @property string $execution_result
 *
 * @package
 * @author     Nikolay Kondikov<nikolay.kondikov@sirma.bg>
 */
class CronJob extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'cron_jobs';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('execute_after, action', 'required'),
            array('succeeded', 'numerical', 'integerOnly' => true),
            array('action', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, execute_after, executed_started, executed_finished, succeeded, action, parameters, execution_result', 'safe', 'on' => 'search')
        );
    }

    public function relations()
    {
        return array();
    }

    public function attributeLabels()
    {
        return array(
            'action' => 'Action',
            'parameters' => 'Parameters',
            'execution_result' => 'Execution Result'
        );
    }

    public function beforeValidate()
    {
        return parent::beforeValidate();
    }

    public function afterFind()
    {
        return parent::afterFind();
    }

    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id);
        $criteria->compare('execute_after', $this->execute_after, true);
        $criteria->compare('executed_at', $this->executed_at, true);
        $criteria->compare('action', $this->action, true);
        $criteria->compare('parameters', $this->parameters, true);
        $criteria->compare('execution_result', $this->parameters, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}
