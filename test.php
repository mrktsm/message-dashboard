<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Test</title>
</head>

<body>
    <h2>PHP Test</h2>


    <?php
    // phpinfo();
    
    include_once("db_connect.php");
    print "<H2>Hello World</H2>\n";

    $str = "USE s25_tsymma01";

    $res = $db->query($str);

    if ($res == FALSE) {
        print "<p>ERROR: could not change t s25_cs36</p>\n";
        exit();
    }



    //process the forms
    //add form
    $op = $_GET['op']; //comes from the link address
    
    $id = $_POST['id'];
    $name = $_POST['name'];
    $planet = $_POST['planet'];
    $power = $_POST["power"];






    if ($op == "add") { // add a new Titan
        print "<P>Attemping to add a new titan, $name</P>\n";

        $str1 = "INSERT INTO titan1 VALUE($id, '$name')\n";
        $str2 = "INSERT INTO titan2 VALUE($id, '$planet', '$power')\n";


        $res1 = $db->query($str1);
        $res2 = $db->query($str2);

        if ($res1 == FALSE || $res2 == FALSE) {
            print "<P>ERROR: could not add new titan</P>\n";
        } else {
            print "<P>SUCCESS: added $name</P>\n";
        }

    } else if ($op == "delete") {
        print "<P>Attemping to delete titan id = , $id</P>\n";

        $str1 = "DELETE FROM titan1 WHERE id=$id\n";
        $str2 = "DELETE FROM titan2 WHERE id=$id\n";

        $res1 = $db->query($str1);
        $res2 = $db->query($str2);

        if ($res1 == FALSE || $res2 == FALSE) {
            print "<P>ERROR: could not delete titan with id = $id</P>\n";
        } else {
            print "<P>SUCCESS: deleted $id</P>\n";
        }



    } else if ($op == 'send') {
        print "<P>Sending a message</P>\n";

        $sid = $_POST['sender'];
        $rid = $_POST['recipient'];
        $subject = $_POST['subject'];
        $content = $_POST['content'];
        //$date = date(); // will handle this on Database side, need to modify table
    
        //print "<P>send: $date</P>\n";
    }


    ?>

    <form name="fmMsg" method="POST" action="test.php?op=send">
        SENDER: <input type="text" name="sender" size="5" /> <br />
        RECIPIENT: <input type="text" name="recipient" size="5" /> <br /><br />


        <input type="text" name="subject" placeholder="type message subject" size="30" /><br />
        <textarea name="content" rows="5" cols="30">
        </textarea><br />


        <input type="submit" value="Send message" />

    </form>


    <?php

    $str = "SELECT titan1.id AS id,name,planet,power FROM titan1 LEFT JOIN titan2 ON titan1.id = titan2.id";

    $res = $db->query($str);

    if ($res == FALSE) {
        print "<p>ERROR: could not execute $str</p>\n";
        exit();
    }

    $nRows = $res->rowCount();
    $nCol = $res->columnCount();

    print ("<p> nRows =  $nRows, nCols = $nCol</P>\n");


    ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Id</th>
            <th>name</th>
            <th>planet</th>
            <th>power</th>
            <th>action</th>
        </tr>

        <form name="fmAdd" action="test.php?op=add" method="POST">
            <tr>
                <TD><INPUT type="text" name="id" /> </TD>
                <TD><INPUT type="text" name="name" /> </TD>
                <TD><INPUT type="text" name="planet" /> </TD>
                <TD><INPUT type="text" name="power" /> </TD>
                <TD><INPUT type="submit" value="add" /> </TD>

            </tr>
        </form>



        <?php

        while ($row = $res->fetch()) {

            $id = $row['id'];
            $name = $row['name'];
            $planet = $row['planet'];
            $power = $row['power'];
            $action = "<form name='famDel' method='POST' action='test.php?op=delete'>\n" . "<input type='hidden' name='id' value='" . $id . "'/>\n"
                . "<input type='submit' value='delete'/>\n" . "</form>\n";

            $tRow = "<tr><td>$id</td><td>$name</td><td>$planet</td><td>$power</td>\n"
                . "<td>$action</td></tr> ";

            print $tRow;
        }

        ?>

    </table>

</body>

</html>