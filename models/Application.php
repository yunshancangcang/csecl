<?php

namespace csecl\models;

use Yii;
use csecl\models\Question;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;
/**
 * This is the model class for table "application".
 *
 * @property integer $id
 * @property string $name
 * @property string $number
 * @property integer $sex
 * @property string $address
 * @property string $email
 * @property string $phone
 * @property string $qq
 * @property string $college
 * @property string $major
 * @property string $grade
 * @property integer $english_grade
 * @property integer $math_grade
 * @property string $referrer
 * @property string $referrer_info
 * @property string $direct
 * @property integer $status
 * @property integer $created
 * @property integer $updated
 */
class Application extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'application';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'number', 'address', 'email','phone', 'qq', 'college', 'major', 'grade', 'english_grade', 'math_grade', 'direct', 'created', 'updated'], 'required'],
            [['sex', 'english_grade', 'math_grade', 'status', 'created', 'updated'], 'integer'],
            [['number'],'unique'],
            [['name', 'college', 'major'], 'string', 'max' => 20],
            [['number'], 'string', 'max' => 12],
            [['address','email'], 'string', 'max' => 50],
            [['phone', 'qq'], 'string', 'max' => 11],
            [['grade'], 'string', 'max' => 4],
            [['referrer', 'direct'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'number' => 'Number',
            'sex' => 'Sex',
            'address' => 'Address',
            'email' => 'Email',
            'phone' => 'Phone',
            'qq' => 'Qq',
            'college' => 'College',
            'major' => 'Major',
            'grade' => 'Grade',
            'english_grade' => 'English Grade',
            'math_grade' => 'Math Grade',
            'referrer' => 'Referrer',
            'direct' => 'Direct',
            'status' => 'Status',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }
}

