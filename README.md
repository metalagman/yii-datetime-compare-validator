# DateTime comparison validator for Yii #

## Usage example ##

```php
public function rules()
{
    return [
        ['endTime', 'vendor.lagman.yii-datetime-compare-validator.DateTimeCompareValidator',
            'compareAttribute' => 'beginTime', 'format'=>'H:i:s', 'operator' => '>='],
    ];
}
```
