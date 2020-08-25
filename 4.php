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





