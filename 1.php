<?php

/*Реализовать функцию findSimple ($a, $b). $a и $b – целые положительные числа.
Результат ее выполнение: массив простых чисел от $a до $b.*/

class Simple
{
    public $a, $b;
    public $array = array();
    public function __construct()
    {
        echo "Введите а: ";
        $this->a = readline();
        echo "Введите b: ";
        $this->b = readline();
    }

    public function findSimple()
    {
        $counter = 0;
        $divider = 0;
        for ($i = $this->a; $i <= $this->b; $i++)
        {
            for ($j = 1; $j <= $i; $j++)
            {
                if($i%$j == 0)
                {
                    $divider++;
                }
            }
            if($divider == 2)
            {
                $this->array[$counter] = (int)$i;
                $counter++;
            }
            $divider = 0;
        }
    }
}

$object = new Simple();
$object->findSimple();
var_dump($object->array);

/*Реализовать функцию createTrapeze($a). $a – массив положительных чисел, количество элементов кратно 3.
Результат ее выполнение: двумерный массив (массив состоящий из ассоциативных массива с ключами a, b, c).
Пример для входных массива [1, 2, 3, 4, 5, 6] результат [[‘a’=>1,’b’=>2,’с’=>3],[‘a’=>4,’b’=>5 ,’c’=>6]].*/

$a = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
function createTrapeze($a)
{
    $c = [];
    $j = 0;
    for ($i = 0; $i < count($a); $i+=3)
    {
        $c[$j] = array(
            "a" => $a[$i],
            "b" => $a[$i+1],
            "c" => $a[$i+2]
        );
        $j++;
    }
    return $c;
}

$c = createTrapeze($a);
var_dump($c);

/*Реализовать функцию squareTrapeze($a). $a – массив результата выполнения функции createTrapeze().
Результат ее выполнение: в исходный массив для каждой тройки чисел добавляется дополнительный ключ s,
содержащий результат расчета площади трапеции со сторонами a и b, и высотой c.*/

$a = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
function createTrapeze($a)
{
    $c = [];
    $j = 0;
    for ($i = 0; $i < count($a); $i+=3)
    {
        $c[$j] = array(
            "a" => $a[$i],
            "b" => $a[$i+1],
            "c" => $a[$i+2]
        );
        $j++;
    }
    return $c;
}
function squareTrapeze($c)
{
    $square = [];
    $i = 0;
    foreach ($c as $item)
    {
        $s = 1/2 * $item["c"]*($item["b"]+$item["a"]);
        $item["s"] = $s;
        $square[$i] = $item;
        $i++;
    }
    return $square;
}

$c = createTrapeze($a);

$c = squareTrapeze($c);
print_r($c);


/*Реализовать функцию getSizeForLimit($a, $b). $a – массив результата выполнения функции squareTrapeze(),
$b – максимальная площадь. Результат ее выполнение: массив размеров трапеции с максимальной площадью, но меньше или равной $b.*/

$a = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
function createTrapeze($a)
{
    $c = [];
    $j = 0;
    for ($i = 0; $i < count($a); $i+=3)
    {
        $c[$j] = array(
            "a" => $a[$i],
            "b" => $a[$i+1],
            "c" => $a[$i+2]
        );
        $j++;
    }
    return $c;
}
function squareTrapeze($c)
{
    $square = [];
    $i = 0;
    foreach ($c as $item)
    {
        $s = 1/2 * $item["c"]*($item["b"]+$item["a"]);
        $item["s"] = $s;
        $square[$i] = $item;
        $i++;
    }
    return $square;
}

function getSizeForLimit($c, $b)
{
    $limitValid = [];
    $i = 0;
    $j = 0;
    $k = 0;
    foreach ($c as $item)
    {
        if($item["s"] <= $b)
        {
            $limitValid[$i] = $item;
            $i++;
        }
    }
    $max = $limitValid[0]["s"];
    foreach ($limitValid as $item)
    {
        if($item["s"] > $max)
        {
            $max = $item["s"];
            $k = $j;
        }
        $j++;
    }
    return $limitValid[$k];

}

$c = createTrapeze($a);

$c = squareTrapeze($c);
print_r($c);

$maxSizeForLimit = getSizeForLimit([['s' => 2], ['s' => 1], ['s' => 3]], 5);
print_r($maxSizeForLimit);

/*Реализовать функцию getMin($a). $a – массив чисел. Результат ее выполнения:
минимальное числа в массиве (не используя функцию min, ключи массив может быть ассоциативный).*/

$a = array(5, 4, 20, 50, 1, 11);

function getMin($a)
{
    $min = $a[0];
    foreach ($a as $item)
    {
        if($item < $min)
        {
            $min = $item;
        }
    }
    return $min;
}

$min = getMin($a);
echo $min;

/*Реализовать функцию printTrapeze($a). $a – массив результата выполнения функции squareTrapeze().
Результат ее выполнение: вывод таблицы с размерами трапеций, строки с нечетной площадью трапеции отметить любым способом.*/

$a = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
function createTrapeze($a)
{
    $c = [];
    $j = 0;
    for ($i = 0; $i < count($a); $i+=3)
    {
        $c[$j] = array(
            "a" => $a[$i],
            "b" => $a[$i+1],
            "c" => $a[$i+2]
        );
        $j++;
    }
    return $c;
}
function squareTrapeze($c)
{
    $square = [];
    $i = 0;
    foreach ($c as $item)
    {
        $s = 1/2 * $item["c"]*($item["b"]+$item["a"]);
        $item["s"] = $s;
        $square[$i] = $item;
        $i++;
    }
    return $square;
}
function printTrapeze($c)
{
    foreach ($c as $item)
    {
        if($item["s"]%2 == 0)
        {
            echo "сторона a: {$item["a"]}; сторона b: {$item["b"]}; высота c: {$item["c"]}; площадь s: {$item["s"]}; \n";
        }else{
            echo "---сторона a: {$item["a"]}; сторона b: {$item["b"]}; высота c: {$item["c"]}; площадь s: {$item["s"]};--- \n";
        }
    }
}

$c = createTrapeze($a);

$c = squareTrapeze($c);
printTrapeze($c);

/*Реализовать абстрактный класс BaseMath содержащий 3 метода: exp1($a, $b, $c) и exp2($a, $b, $c),getValue().
Метода exp1 реализует расчет по формуле a*(b^c). Метода exp2 реализует расчет по формуле (a/b)^c. Метод getValue() возвращает результат расчета класса наследника.*/

abstract class BaseMath
{
    /* Данный метод должен быть определён в дочернем классе */
    protected function getValue($a, $b, $c)
    {
        $f = ($a * pow($b, $c) + pow((pow($a/$c, $b)%3), min($a, $b, $c)));
        return $f;
    }

    /* Общие методы */
    public function exp1($a, $b, $c)
    {
        $result = $a * (pow($b,$c));
        return $result;
    }
    public function exp2($a, $b, $c)
    {
        $result = pow(($a/$b), $c);
        return $result;
    }
}

/*Реализовать класс F1 наследующий методы BaseMath, содержащий конструктор с параметрами ($a, $b, $c) и метод getValue().
Класс реализует расчет по формуле f=(a*(b^c)+(((a/c)^b)%3)^min(a,b,c)).*/

class F1 extends BaseMath
{
    private $a, $b, $c;
    public function __construct($a,$b,$c)
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }
    parent::getValue($this->a, $this->b, $this->c);

}