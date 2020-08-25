<?php

/*- функцию convertString($a, $b). Результат ее выполнение: если в строке $a содержится 2 и более подстроки $b,
то во втором месте заменить подстроку $b на инвертированную подстроку.*/

function convertString($a, $b)
{
    $count = substr_count($a, $b);
    if($count > 1)
    {
        $posTemp = strpos($a, $b);
        $posFinal = strpos($a, $b, $posTemp+1);
        $a = substr_replace($a, strrev($b), $posFinal, strlen($b));
        echo $a;
    }else{
        echo "Число вхождений подстроки $b в строку $a меньше или равно 1";
    }
}

convertString("isalexis", "is");

/*функию mySortForKey($a, $b). $a – двумерный массив вида [['a'=>2,'b'=>1],['a'=>1,'b'=>3]], $b – ключ вложенного массива.
Результат ее выполнения: двумерном массива $a отсортированный по возрастанию значений для ключа $b.
В случае отсутствия ключа $b в одном из вложенных массивов, выбросить ошибку класса Exception с индексом неправильного массива*/

$a = [['a'=>2,'b'=>1],['a'=>1,'b'=>3], ['a'=>8,'b'=>2]];

function mySortForKey($a, $b)
{
    foreach ($a as $key => $row) {
        if (!isset($row[$b])) {
            throw new Exception('В одном из вложенных массивов отсутствует указанный ключ');
        }else{
            $b_arr[$key]  = $row[$b];
        }
    }
    array_multisort($b_arr, SORT_ASC, $a);
    return $a;
}

try {
    $a = mySortForKey($a, 'b');
    var_dump($a);
} catch (Exception $e) {
    echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
}