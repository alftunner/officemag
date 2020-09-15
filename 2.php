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

/*IMPORT XML*/

function dataBaseConnect() //функция для соединения с БД
{
    $link = mysqli_connect("127.0.0.1", "alftunner", "", "test_samson");

    if (!$link) {
        echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
        echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
        echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
        exit;
    }

    echo "Соединение с MySQL установлено!" . PHP_EOL;
    echo "Информация о сервере: " . mysqli_get_host_info($link) . PHP_EOL;
    return $link;
}

function getProductId($conn, $code, $name) //функция для получения id текущего товара
{
    $code = mysqli_real_escape_string($conn, $code);
    $name = mysqli_real_escape_string($conn, $name);
    $sql_select = "SELECT id from a_product WHERE code = '{$code}' and name = '{$name}' ORDER BY id DESC";
    $res = mysqli_query($conn, $sql_select);
    $row = $res->fetch_assoc();
    return $row['id'];
}

function insertToDB($conn, $sql) //функция для получения информации о успешном или неуспешном добавлении данных в БД
{
    if (mysqli_query($conn, $sql)) {
        echo "\n" . "New record created successfully";
    } else {
        echo "\n" . "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

function insertPrice($conn, $product, $product_id) //функция для добаления цены в БД
{
    if(isset($product->Цена))
    {
        foreach ($product->Цена as $price)
        {
            $price_type = $price["Тип"];
            $price_value = (double)$price["Значение"];

            $price_type = mysqli_real_escape_string($conn, $price_type);
            $price_value = mysqli_real_escape_string($conn, $price_value);

            $sql = "SELECT id from a_price WHERE product_id='{$product_id}' and type='{$price_type}' and price='{$price_value}'";
            $res = mysqli_query($conn, $sql);
            $row = $res->fetch_assoc();
            if(!empty($row['id']))
            {
                echo "\n" . "Внимание: запись где id = {$product_id}, type = {$price_type}, price = {$price_value} уже существует!";
            }
            else{
                $sql = "INSERT INTO a_price (product_id, type, price) VALUES ('{$product_id}', '{$price_type}', '{$price_value}')";
                insertToDB($conn, $sql);
            }
        }
    }
    else{
        echo "\n Узла ЦЕНА не существует в xml";
    }
}

function insertProperty($conn, $product, $product_id) //функция для добавления свойств товара в БД
{
    if (isset($product->Свойства))
    {
        foreach ($product->Свойства as $property)
        {
            foreach ($property as $key=>$value)
            {
                $property_type = $key;
                $property_value = $value;

                $property_type = mysqli_real_escape_string($conn, $property_type);
                $property_value = mysqli_real_escape_string($conn, $property_value);

                $sql = "SELECT id from a_price WHERE product_id='{$product_id}' and type='{$property_type}' and price='{$property_value}'";
                $res = mysqli_query($conn, $sql);
                $row = $res->fetch_assoc();
                if(!empty($row['id']))
                {
                    echo "\n" . "Внимание: запись где id = {$product_id}, type = {$property_type}, price = {$property_value} уже существует!";
                }
                else{
                    $sql = "INSERT INTO a_property (product_id, type, value) VALUES ('{$product_id}', '{$property_type}', '{$property_value}')";
                    insertToDB($conn, $sql);
                }
            }
        }
    }
    else{
        echo "\n Узла СВОЙСТВА не существует в xml";
    }

}

function insertCategory($conn, $product, $product_id) //функция для добавления данных о категориях (в xml файле "Разделы"), если такой раздел существует, то записывает данные о связи продукта с категорией, если не существует, сначала создает категорию, затем устанавливает связь
{
    if(isset($product->Разделы))
    {
        foreach ($product->Разделы as $category)
        {
            foreach ($category as $key=>$value)
            {
                $value = mysqli_real_escape_string($conn, $value);
                $sql_select = "SELECT id from a_category WHERE name = '{$value}'";
                $res = mysqli_query($conn, $sql_select);
                $row = $res->fetch_assoc();
                $category_id = $row['id'];

                if (!empty($category_id)) {
                    $sql = "INSERT INTO a_relation_product_category (product_id, category_id) VALUES ('{$product_id}', '{$category_id}')";
                    insertToDB($conn, $sql);
                } else {
                    $sql = "INSERT INTO a_category (name) VALUES ('{$value}')";
                    insertToDB($conn, $sql);

                    $sql_select = "SELECT id from a_category WHERE name = '{$value}'";
                    $res = mysqli_query($conn, $sql_select);
                    $row = $res->fetch_assoc();
                    $category_id = $row['id'];

                    $sql = "INSERT INTO a_relation_product_category (product_id, category_id) VALUES ('{$product_id}', '{$category_id}')";
                    insertToDB($conn, $sql);
                }
            }
        }
    }
    else{
        echo "\n Узла РАЗДЕЛЫ не существует в xml";
    }

}

function parseXml($xml) // Парсит xml и раскладывает по таблицам БД
{
    $conn = dataBaseConnect();

    if(isset($xml->Товар))
    {
        foreach ($xml->Товар as $product)
        {
            $code = $product["Код"];
            $name = $product["Название"];

            $code = mysqli_real_escape_string($conn, $code);
            $name = mysqli_real_escape_string($conn, $name);

            $sql = "SELECT id from a_product WHERE code='{$code}' and name='{$name}'";
            $res = mysqli_query($conn, $sql);
            $row = $res->fetch_assoc();
            if(!empty($row['id']))
            {
                echo "\n" . "Внимание: запись где code = {$code}, name = {$name} уже существует!";
            }
            else{
                $sql = "INSERT INTO a_product (code, name) VALUES ('{$code}', '{$name}')";
                if (mysqli_query($conn, $sql)) {
                    echo "\n" ."New record created successfully";
                    $product_id = getProductId($conn, $code, $name);
                    insertPrice($conn, $product, $product_id);
                    insertProperty($conn, $product, $product_id);
                    insertCategory($conn, $product, $product_id);

                } else {
                    echo "\n" ."Error: " . $sql . "<br>" . mysqli_error($conn);
                }
            }
        }
    }
    else{
        echo "\n Узла ТОВАР не существует в xml";
    }

}
function importXml($a) // Финальная функция, если фаил доступен, то парсит и кладет в БД
{
    if (file_exists($a)) {
        $xml = simplexml_load_file($a);
        parseXml($xml);
    } else {
        exit('Не удалось открыть файл ' . $a);
    }
}

importXml('file.xml');

/*EXPORT XML*/

function dataBaseConnect() //функция для соединения с БД
{
    $link = mysqli_connect("127.0.0.1", "alftunner", "", "test_samson");

    if (!$link) {
        echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
        echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
        echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
        exit;
    }

    echo "Соединение с MySQL установлено!" . PHP_EOL;
    echo "Информация о сервере: " . mysqli_get_host_info($link) . PHP_EOL;
    return $link;
}
function exportXml($a, $b)
{
    $conn = dataBaseConnect();
    $dom = new DOMDocument( '1.0', 'utf-8' ); // Класс для работы с DOM
    $dom -> formatOutput = True;
    $root  = $dom->createElement( 'Товары' );
    $dom   ->appendChild( $root );
    $query = "SELECT product_id FROM a_relation_product_category WHERE category_id = '$b'";
    $res = mysqli_query($conn, $query);
    $res_test = mysqli_query($conn, $query);
    $row_test = $res_test->fetch_assoc();
    if(isset($row_test)) // Проверяем есть ли нужная нам выборка в БД
    {
        while ($row = $res->fetch_assoc()) {
            $query_product = "SELECT code, name FROM a_product WHERE id='{$row['product_id']}'";
            $res_product = mysqli_query($conn, $query_product);
            $products = $dom->createElement( 'Товар' );
            while ($row_product = $res_product->fetch_assoc())
            {

                foreach( $row_product as $key => $val )
                {
                    $child = $dom->createElement( $key );
                    $child ->appendChild( $dom->createTextNode($val) );
                    $products  ->appendChild( $child );
                }
                $root->appendChild( $products );
            }

            $query_price = "SELECT type, price FROM a_price WHERE product_id='{$row['product_id']}'";
            $res_price = mysqli_query($conn, $query_price);
            $price = $dom->createElement( 'Цена' );
            while ($row_price = $res_price->fetch_assoc())
            {
                $dom -> appendChild($price);
                $child = $dom->createElement( $row_price['type'] );
                $child ->appendChild( $dom->createTextNode($row_price['price']) );
                $price  ->appendChild( $child );
            }
            $products->appendChild($price);

            $query_property = "SELECT type, value FROM a_property WHERE product_id='{$row['product_id']}'";
            $res_property = mysqli_query($conn, $query_property);
            $property = $dom->createElement( 'Свойства' );
            while ($row_property = $res_property->fetch_assoc())
            {
                $dom -> appendChild( $property );
                $child = $dom->createElement( $row_property['type'] );
                $child ->appendChild( $dom->createTextNode($row_property['value']) );
                $property  ->appendChild( $child );
            }
            $products->appendChild( $property );

            $query_category = "SELECT name FROM a_category WHERE id='$b'"; //Attantion
            $res_category = mysqli_query($conn, $query_category);
            $category = $dom->createElement( 'Разделы' );
            while ($row_category = $res_category->fetch_assoc())
            {
                $dom -> appendChild( $category );
                $child = $dom->createElement( 'Раздел' );
                $child ->appendChild( $dom->createTextNode($row_category['name']) );
                $category  ->appendChild( $child );
            }
            $products->appendChild( $category );
        }
        $dom->save($a);
    }else{
        echo "В базе данных нет категории с id = $b";
    }
}

exportXml('file2.xml', 15);