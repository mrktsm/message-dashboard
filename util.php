<?php

include_once("db_connect.php");

function debug($str) {
	print "<DIV class='debug'>$str</DIV>\n";
}

function genLoginForm()
{
    ?>
    <form name="fmLogin" method="POST" action="dashboard.php?menu=login">
        <input type="text" name='uid' size="5" placeholder='user id' />
        <input type="submit" value="login" />
    </form>
    <?php
}

function processLogin($formData)
{
    global $db;
    $uid = $formData['uid'];
    $query = "SELECT * FROM titan1 WHERE id=:id1";
    $result = $db->prepare($query);

    $result->bindParam(":id1", $uid);

    try {
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $row = $result->fetch();

        if (count($row) == 1) {
            header("refresh:2;url=dashboard.php");
            print ("<h1>The provided  userid does not exist!</h1>");
        } else {
            header("refresh:2;url=dashboard.php");

            $_SESSION['uid'] = $row['id'];
            $_SESSION['uname'] = $row['name'];
            print "<p>Successfully logged in</p>\n";
        }




    } catch (Exception $ex) {
        echo "There is error executing the SQL query: ", $ex;
    }
}


function genLogoutForm()
{
    print "<form name='logout' method = 'POST' action='dashboard.php?menu=logout'>\n";
    print "<input type ='submit' value='logout'/>\n";
    print "</form>\n";
}

function genInbox($db, $uid) {
// display messages for $uid (as recipient)

	$query = "SELECT * FROM message WHERE rid=$uid";
	$res = $db->query($query);

	if ($res != false) {
		
		while ($row = $res->fetch()) {

			$sid = $row['sid'];
			$sub = $row['subject'];
			$con = $row['content'];
			$date = $row['msgDate'];

			print "<PRE>$date, $sid, $sub, $con</PRE>\n";

		}

	}
	else {
		debug("Error accessing messages for the user $uid");
	}

}

function genComposeForm($db, $sid) {

?>

<FORM name="fmMsg" method="POST" action="dashboard.php?menu=send">

RECIPIENT: <INPUT type="text" name="rid" size="5" /><BR /><BR />

<INPUT type=text" name="subject" size="30"
       placeholder="type message subject" /><BR />

<TEXTAREA name="content" rows="5" cols="30"> 
</TEXTAREA>
<BR />
<INPUT type="submit" value="Send message" />

</FORM>

<?php
} // closes genComposeForm

function sendMessage($db, $sid, $msgData) {
	$rid = $msgData['rid'];
	$subject = $msgData['subject'];
	$content = $msgData['content'];

	/*
	INSERT INTO message(sid, rid, subject, content)
	       VALUE($sid, $rid, "$subject", "$content");
	*/

	$query = "INSERT INTO message(sid, rid, subject, content) "
	       . "VALUE($sid, $rid, '" . $subject .  "', '" . $content . "')";

	//debug($query);

	$res = $db->query($query);

	if ($res != false) {
		print "<P>Message sent</P>\n";
	}
	else {
		debug("error sending a message to $rid");
	}

}


?>