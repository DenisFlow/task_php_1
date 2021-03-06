<!DOCTYPE html>
<html>
<head>
    <title>Test page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<table width="795" align="center" bgcolor="#ffc0cb">
<tr align="center">
    <td colspan="6"><h2>вывести данные из таблицы data в виде строк со столбцами name, email, value</h2></td>
</tr>
<tr align="center" bgcolor="#87ceeb">
    <th>name</th>
    <th>email</th>
    <th>value</th>
</tr>

<?php
$con = mysqli_connect("localhost", "root", "", "celina_heats");

if(mysqli_connect_errno()){
    echo "Faild to connect to MySQL:" . mysqli_connect_error();
}

$get = "select * from data";

$run = mysqli_query($con, $get);


while ($row = mysqli_fetch_array($run)) {

    $name = $row['name'];
    $email = $row['email'];
    $value = $row['value'];


    ?>
    <tr align="center">
        <td><?php echo $name; ?></td>
        <td><?php echo $email; ?></td>
        <td><?php echo $value; ?></td>
    </tr>
<?php } ?>



</table>

<br>
<h2>Построить дерево по parent.</h2>

<?php


$run = mysqli_query($con, $get);
$arr = [];
$arrSum = [];

while ($row = mysqli_fetch_assoc($run)) {

    $arr[$row['id']]['name'] = $row['name'];
    $arr[$row['id']]['email'] = $row['email'];
    $arr[$row['id']]['value'] = $row['value'];
    $arr[$row['id']]['parent'] = $row['parent'];
//    for sum {
    $arr[$row['id']]['id'] = $row['id'];
// }


}


buildTreeView($arr, 0, 0, -1, 0, $arrSum);



function buildTreeView($arr, $parent, $level = 0, $prelevel = -1, $req = 0, &$arrSum) {
    foreach ($arr as $id=>$data) {
        if ($parent == $data['parent']) {
            if ($level > $prelevel) {
                echo "<ol>";
            }
            if ($level ==  $prelevel) {
                echo "</li>";
            }

            $arrSum[$data['id']]['name'] = $data['name'];
            $arrSum[$data['id']]['id'] = $data['id'];
            $arrSum[$data['id']]['value'] = $data['value'];
            $arrSum[$data['id']]['level'] = $level;

            $req ++;




            echo "<li>".$data['name']."\t". $data['email']."\t". $data['value'];

            if ($level > $prelevel) {
                $prelevel = $level;
            }
            $level++;

            buildTreeView($arr, $id, $level, $prelevel, $req, $arrSum);

            $level--;


        }

    }

    if ($level == $prelevel) {
        echo "</li></ol>";
    }
}

function validMail($value) {
    if (preg_match("/.+@.+\..+/", $value)) {
        return "yes";
    }
    return "no";

}



?>

<table width="795" align="center" bgcolor="#ffc0cb">
    <tr align="center">
        <td colspan="6"><h2>Проверить email на валидность.</h2></td>
    </tr>
    <tr align="center" bgcolor="#87ceeb">
        <th>name</th>
        <th>email</th>
        <th>valid</th>
    </tr>

    <?php


    $run = mysqli_query($con, $get);


    while ($row = mysqli_fetch_array($run)) {

        $name = $row['name'];
        $email = $row['email'];
        $value = $row['value'];


        ?>
        <tr align="center">
            <td><?php echo $name; ?></td>
            <td><?php echo $email; ?></td>
            <td><?php echo validMail($email); ?></td>
        </tr>
    <?php } ?>



</table>

<br>

<table width="795" align="center" bgcolor="#ffc0cb">
    <tr align="center">
        <td colspan="6"><h2>Добавить столбец sum = собственное значение value + value всех подчинённых строк.</h2></td>
    </tr>
    <tr align="center" bgcolor="#87ceeb">
        <th>name</th>
        <th>email</th>
        <th>value</th>
        <th>sum</th>
    </tr>

    <?php


    function countSum($arr, $parent, $level = 0, $prelevel = -1) {
        foreach ($arr as $id=>$data) {
            if ($parent == $data['parent']) {

                if ($level > $prelevel) {
                    echo "<ol>";
                }

                if ($level == $prelevel) {
                    echo "</li>";
                }

                echo "<li>".$data['name']."\t". $data['email']."\t". $data['value']."\t";

                if ($level > $prelevel) {
                    $prelevel = $level;
                }
                $level++;

                buildTreeView($arr, $id, $level, $prelevel);

                $level--;
            }

        }

        if ($level == $prelevel) {
            echo "</li></ol>";
        }
    }



    $run = mysqli_query($con, $get);


    while ($row = mysqli_fetch_array($run)) {

        $getId = $row['id'];
        $name = $row['name'];
        $email = $row['email'];
        $value = $row['value'];

        ?>
        <tr align="center">
            <td><?php echo $name; ?></td>
            <td><?php echo $email; ?></td>
            <td><?php echo $value; ?></td>
            <td><?php

                $sum = 0;
                $last = null;

                foreach ($arrSum as $id=>$data) {

                    if ($data['id'] != $getId and $last === null) {
                        continue;
                    }

                    if ($last === null) {

                        $sum += $data['value'];
                    } else if ($data['level'] > $last) {

                        $sum += $data['value'];

                    } else {
                        break;
                    }

                    if ($last === null) {
                        $last = $data['level'];
                    }

                }

                echo $sum;

                unset ($data)
                ?></td>
        </tr>
    <?php }



    ?>



</table>

    <br>

    <h1>При наведении курсора мыши строка подсвечивается с задержкой 0.3 секунды.</h1>
    <br>
    <input type="radio" name="jobtype" id="jt-webapp" />
    <label class="lbl-jobtype" for="jt-webapp">При клике на строку появляется поле text, которое изначально скрыто</label>



    <p class="text-for-jobtype" id="t-webapp">Some text</p>
    <br>
</body>



</html>

<!--Задача: вывести данные из таблицы data в виде строк со столбцами name, email, value.
Построить дерево по parent.
Проверить email на валидность.
Добавить столбец sum = собственное значение value + value всех подчинённых строк.
При наведении курсора мыши строка подсвечивается с задержкой 0.3 секунды.
При клике на строку появляется поле text, которое изначально скрыто.
-->