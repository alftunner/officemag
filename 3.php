<?php

namespace Test3;

class newBase


{
    static private $count = 0;
    static private $arSetName = [];
    /**
     * @param string $name
     */
    function __construct(int $name = null) //TODO не может быть нулём, только null
    {
        if (empty($name)) {
            while (array_search(self::$count, self::$arSetName) !== false) { //TODO добавил строгое сравнение
                ++self::$count;
            }
            $name = self::$count;
        }
        $this->name = $name;
        self::$arSetName[] = $this->name;
    }
    protected $name; //TODO ? изменение на protected
    /**
     * @return string
     */
    public function getName(): string
    {
        return '*' . $this->name  . '*';
    }
    protected $value;

    /**
     * @param mixed $value
     * @return newBase
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this; //TODO добавил
    }
    /**
     * @return string
     */
    public function getSize()
    {
        $size = strlen(serialize($this->value));
        return strlen($size) + $size;
    }
    public function __sleep()
    {
        return ['value'];
    }
    /**
     * @return string
     */
    public function getSave(): string
    {
        $value = serialize($this->value); //TODO добавление this перед value в serialize()
        return $this->name . ':' . sizeof($value) . ':' . $value;
    }

    /**
     * @param string $value
     * @return newBase
     */
    static public function load(string $value): newBase
    {
        $arValue = explode(':', $value);
        return (new newBase($arValue[0]))
            ->setValue(unserialize(substr($value, strlen($arValue[0]) + 1
                + strlen($arValue[1]) + 1), $arValue[1])); //TODO ?
    }
}
class newView extends newBase
{
    private $type = null;
    private $size = 0;
    private $property = null;
    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        parent::setValue($value);
        $this->setType();
        $this->setSize();
        return $this;
    }
    public function setProperty($value)
    {
        $this->property = $value;
        //echo '$this->property'.$this->property;
        //echo '$value'.$value;
        return $this; //TODO ?
    }
    private function setType()
    {
        $this->type = gettype($this->value);
    }
    private function setSize()
    {
        if (is_subclass_of($this->value, "Test3\\newView")) { //TODO ? добавил экранирование слэша
            $this->size = parent::getSize() + 1 + strlen($this->property);
        } elseif ($this->type == 'test') {
            $this->size = parent::getSize();
        } else {
            $this->size = strlen($this->value);
        }
    }
    /**
     * @return string
     */
    public function __sleep()
    {
        return ['property'];
    }
    /**
     * @return string
     */
    public function getName(): string
    {
        if (empty($this->name)) {
            throw new Exception('The object doesn\'t have name');
        }
        return '"' . $this->name  . '": ';
    }
    /**
     * @return string
     */
    public function getType(): string
    {
        return ' type ' . $this->type  . ';';
    }
    /**
     * @return string
     */
    public function getSize(): string
    {
        return ' size ' . $this->size . ';';
    }
    public function getInfo()
    {
        try {
            echo $this->getName()
                . $this->getType()
                . $this->getSize()
                . "\r\n";
        } catch (Exception $exc) {
            echo 'Error: ' . $exc->getMessage();
        }
    }
    /**
     * @return string
     */
    public function getSave(): string
    {
        if ($this->type == 'test') {
            $this->value = parent::getSave() . serialize($this->property); // TODO заменил $this->$value->getSave()
        }
        return parent::getSave() . serialize($this->property);
    }

    /**
     * @param string $value
     * @return newView
     */
    static public function load(string $value): newBase
    {
        $arValue = explode(':', $value);
        var_dump($arValue);
        return (new newView($arValue[0])) //TODO Поменял newBase на newView
            ->setValue(unserialize(substr($value, strlen($arValue[0]) + 1
                + strlen($arValue[1]) + 1 + strlen($arValue[1]))))
            ->setProperty(unserialize(substr($value, strlen($arValue[0]) + 1 + strlen($arValue[1]) + 1 + strlen($arValue[1])))); //TODO менял расстановку скобок и добавлял strlen()
    }
}
function gettype($value): string
{
    if (is_object($value)) {
        $type = get_class($value);
        do {
            if (strpos($type, "Test3\\newBase") !== false) { //TODO ? экранирование слеша
                return 'test';
            }
        } while ($type = get_parent_class($type));
    }
    return 'no test'; //TODO Убрал бесконечную рекурсию
}


/*$obj1 = new newBase();
echo $obj1->getName();

$obj2 = new newBase();
echo $obj2->getName();*/

$obj = new newBase('12345');
$obj->setValue('text');

$obj2 = new newView('9876'); //TODO убрали 0, ибо было не число
$obj2->setValue($obj);
$obj2->setProperty('field');
$obj2->getInfo();

$save = $obj2->getSave();

$obj3 = newView::load($save);

var_dump($obj2->getSave() == $obj3->getSave());

