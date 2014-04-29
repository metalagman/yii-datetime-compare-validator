<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */

/**
 * Class DateTimeCompareValidator
 *
 * $perofrms
 */
class DateTimeCompareValidator extends CValidator
{
    public $format = 'd.m.Y';
    /**
     * @var string the name of the attribute to be compared with
     */
    public $compareAttribute;
    /**
     * @var string the constant value to be compared with
     */
    public $compareValue;
    /**
     * @var boolean whether the attribute value can be null or empty. Defaults to false.
     * If this is true, it means the attribute is considered valid when it is empty.
     */
    public $allowEmpty = false;
    /**
     * @var string the operator for comparison. Defaults to '='.
     * The followings are valid operators:
     * <ul>
     * <li>'=' or '==': validates to see if the two values are equal.</li>
     * <li>'!=': validates to see if the two values are NOT equal..</li>
     * <li>'>': validates to see if the value being validated is greater than the value being compared with.</li>
     * <li>'>=': validates to see if the value being validated is greater than or equal to the value being compared with.</li>
     * <li>'<': validates to see if the value being validated is less than the value being compared with.</li>
     * <li>'<=': validates to see if the value being validated is less than or equal to the value being compared with.</li>
     * </ul>
     */
    public $operator = '=';

    public function init()
    {
        if ($this->isEmpty($this->compareAttribute) && $this->isEmpty($this->compareValue))
            throw new LogicException(Yii::t('app', 'You must specify compareAttribute or compareValue'));
    }

    /**
     * Validates the attribute of the object.
     * If there is any error, the error message is added to the object.
     * @param CModel $object the object being validated
     * @param string $attribute the attribute being validated
     * @throws CException if invalid operator is used
     */
    protected function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;
        if ($this->allowEmpty && $this->isEmpty($value))
            return;

        if ($this->compareValue !== null)
            $compareTo = $compareValue = $this->compareValue;
        else {
            $compareAttribute = $this->compareAttribute;
            $compareValue = $object->$compareAttribute;
            $compareTo = $object->getAttributeLabel($compareAttribute);
        }

        $valueDT = DateTime::createFromFormat($this->format, $value);
        $compareValueDT = DateTime::createFromFormat($this->format, $compareValue);

        if (!$valueDT instanceof DateTime) {
            $this->addError($object, $attribute, Yii::t('yii', 'Invalid value date format: {value}'), array('{value}' => $value));
            return;
        }

        if (!$compareValueDT instanceof DateTime) {
            $this->addError($object,
                isset($compareAttribute) ? $attribute : $compareAttribute,
                Yii::t('yii', 'Invalid compare value date format: {value}'), array('{value}' => $compareValue)
            );
            return;
        }

        switch ($this->operator) {
            case '=':
                if ($valueDT != $compareValueDT)
                    $message = $this->message !== null ? $this->message : Yii::t('yii', '{attribute} must be repeated exactly.');
                break;
            case '!=':
                if ($valueDT = $compareValueDT)
                    $message = $this->message !== null ? $this->message : Yii::t('yii', '{attribute} must not be equal to "{compareValue}".');
                break;
            case '>':
                if ($valueDT <= $compareValueDT)
                    $message = $this->message !== null ? $this->message : Yii::t('yii', '{attribute} must be greater than "{compareValue}".');
                break;
            case '>=':
                if ($valueDT < $compareValueDT)
                    $message = $this->message !== null ? $this->message : Yii::t('yii', '{attribute} must be greater than or equal to "{compareValue}".');
                break;
            case '<':
                if ($valueDT >= $compareValueDT)
                    $message = $this->message !== null ? $this->message : Yii::t('yii', '{attribute} must be less than "{compareValue}".');
                break;
            case '<=':
                if ($valueDT > $compareValueDT)
                    $message = $this->message !== null ? $this->message : Yii::t('yii', '{attribute} must be less than or equal to "{compareValue}".');
                break;
            default:
                throw new CException(Yii::t('yii', 'Invalid operator "{operator}".', array('{operator}' => $this->operator)));
        }

        if (!empty($message))
            $this->addError($object, $attribute, $message, array('{compareAttribute}' => $compareTo, '{compareValue}' => $compareValue));
    }

}
