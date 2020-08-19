<?php
function dataBaseConnect()
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

function getProductId($conn, $code, $name)
{
    $sql_select = "SELECT id from a_product WHERE code = '{$code}' and name = '{$name}' ORDER BY id DESC";
    $res = mysqli_query($conn, $sql_select);
    $row = $res->fetch_assoc();
    return $row['id'];
}

function insertToDB($conn, $sql)
{
    if (mysqli_query($conn, $sql)) {
        echo "\n" . "New record created successfully";
    } else {
        echo "\n" . "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

function insertPrice($conn, $product, $product_id)
{
    foreach ($product->Цена as $price)
    {
        $price_type = $price["Тип"];
        $price_value = (double)$price["Значение"];

        echo $price_type . "\n";
        echo $price_value . "\n";

        $sql = "INSERT INTO a_price (product_id, type, price) VALUES ('{$product_id}', '{$price_type}', '{$price_value}')";
        insertToDB($conn, $sql);
    }
}

function insertProperty($conn, $product, $product_id)
{
    foreach ($product->Свойства as $property)
    {
        foreach ($property as $key=>$value)
        {
            $property_type = $key;
            $property_value = $value;

            echo "\n" . "Свойства key = " . $key . "\n";
            echo "\n" . "Свойства value = " . $value . "\n";

            $sql = "INSERT INTO a_property (product_id, type, value) VALUES ('{$product_id}', '{$property_type}', '{$property_value}')";
            insertToDB($conn, $sql);
        }
    }
}

function insertCategory($conn, $product, $product_id)
{
    foreach ($product->Разделы as $category)
    {
        foreach ($category as $key=>$value)
        {
            echo "\n" . "category value = " . $value . "\n";

            $sql_select = "SELECT id from a_category WHERE name = '{$value}'";
            $res = mysqli_query($conn, $sql_select);
            $row = $res->fetch_assoc();
            $category_id = $row['id'];

            if (!empty($category_id)) {
                $sql = "INSERT INTO a_relation_product_category (product_id, category_id) VALUES ('{$product_id}', '{$category_id}')";
                insertToDB($conn, $sql);
            } else {
                echo "\n" . "Нет такого раздела" . "\n";

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

function parseXml($xml)
{
    $conn = dataBaseConnect();

    foreach ($xml->Товар as $product)
    {
        $code = $product["Код"];
        $name = $product["Название"];

        echo $code . "\n";
        echo $name . "\n";

        $sql = "INSERT INTO a_product (code, name) VALUES ('{$code}', '{$name}')";
        if (mysqli_query($conn, $sql)) {
            echo "\n" ."New record created successfully";

            $product_id = getProductId($conn, $code, $name);
            echo "\n" . "product_id = " .$product_id . "\n";

            insertPrice($conn, $product, $product_id);

            insertProperty($conn, $product, $product_id);
            insertCategory($conn, $product, $product_id);

        } else {
            echo "\n" ."Error: " . $sql . "<br>" . mysqli_error($conn);
        }


    }
}
function importXml($a)
{
    if (file_exists($a)) {
        $xml = simplexml_load_file($a);
        parseXml($xml);
    } else {
        exit('Не удалось открыть файл ' . $a);
    }
}

importXml('file.xml');
