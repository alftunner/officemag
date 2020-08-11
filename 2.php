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
