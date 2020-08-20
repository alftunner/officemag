<?php
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
    foreach ($product->Цена as $price)
    {
        $price_type = $price["Тип"];
        $price_value = (double)$price["Значение"];

        $sql = "INSERT INTO a_price (product_id, type, price) VALUES ('{$product_id}', '{$price_type}', '{$price_value}')";
        insertToDB($conn, $sql);
    }
}

function insertProperty($conn, $product, $product_id) //функция для добавления свойств товара в БД
{
    foreach ($product->Свойства as $property)
    {
        foreach ($property as $key=>$value)
        {
            $property_type = $key;
            $property_value = $value;

            $sql = "INSERT INTO a_property (product_id, type, value) VALUES ('{$product_id}', '{$property_type}', '{$property_value}')";
            insertToDB($conn, $sql);
        }
    }
}

function insertCategory($conn, $product, $product_id) //функция для добавления данных о категориях (в xml файле "Разделы"), если такой раздел существует, то записывает данные о связи продукта с категорией, если не существует, сначала создает категорию, затем устанавливает связь
{
    foreach ($product->Разделы as $category)
    {
        foreach ($category as $key=>$value)
        {
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

function parseXml($xml) // Парсит xml и раскладывает по таблицам БД
{
    $conn = dataBaseConnect();

    foreach ($xml->Товар as $product)
    {
        $code = $product["Код"];
        $name = $product["Название"];

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
function importXml($a) // Финальная функция, если фаил доступет, то парсит и кладет в БД
{
    if (file_exists($a)) {
        $xml = simplexml_load_file($a);
        parseXml($xml);
    } else {
        exit('Не удалось открыть файл ' . $a);
    }
}

importXml('file.xml');
