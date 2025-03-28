<?php

session_start();

include_once("db_connect.php");

function debug($str) {
    print "<DIV class='debug'>$str</DIV>\n";
}

function genLoginForm() {
?>

<FORM name='fmLogin' method='POST' action='dashboard.php?menu=login'>
<INPUT type='text'   name='uid' size='5' placeholder='user id' />
<INPUT type='submit' value='login' />
</FORM>

<?php
}

function genLogoutForm() {
    print "<FORM name='fmLogout' method='POST' action='dashboard.php?menu=logout'>\n";
    print "<INPUT type='submit' value='logout' />\n";
    print "</FORM>\n";
}

function processLogin($db, $formData) {
    $uid = $formData['uid'];

    $query = "USE s25_tsymma01";
    $res = $db->query($query);

    $query = "SELECT name FROM titan1 WHERE id=$uid";

    $res = $db->query($query);

    if ($res == false || $res->rowCount() != 1) {
        header("refresh:2;url=dashboard.php");
        print "<P>Login as $uid failed</P>\n";    
    }
    else {
        header("refresh:2;url=dashboard.php");
        $row = $res->fetch();
        $_SESSION['uid'] = $uid;
        $_SESSION['uname'] = $row['name'];
        print "<P>Successfully logged in</P>\n";
    }

}

function processLogout() {
    header("refresh:2;url=dashboard.php");
    unset($_SESSION['uid']);
    unset($_SESSION['uname']);
}

function genInbox($db, $uid) {
// display messages for $uid (as recipient)

    $query = "SELECT mid, name, subject, content, msgDate
              FROM message JOIN titan1 ON sid=id
              WHERE rid=$uid";

    $res = $db->query($query);

	echo "<table class='message-table'>\n";
    echo "<tr><th>Date</th><th>Sender</th><th>Subject</th></tr>\n";

    if ($res === false) {
		debug("Error accessing messages for the user $uid");
		return;
	}

    while ($row = $res->fetch()) {
        $msgId = $row['mid'];
        $sender = $row['name'];
        $sub = $row['subject'];
        $con = $row['content'];
        $date = $row['msgDate'];

        echo "<tr>";
        echo "<td>$date</td><td>$sender</td>";
        echo "<td><span class='message-subject' onclick='toggleContent(\"msg-$msgId\")'>$sub</span></td>";
        echo "</tr>";
        echo "<tr><td colspan='3'><div id='msg-$msgId' class='message-content'>$con</div></td></tr>";
        }

    echo "</table>\n";
}



function genHistoryForm($db, $uid) {
    $query = "SELECT id, name FROM titan1";
    $res = $db->query($query);
    
    if ($res === false) {
        debug("database error");
        return;
    }
    // Create a form
    echo "<h3>Message History</h3>\n";
    echo "<form method='GET' action='dashboard.php'>\n";
    echo "<input type='hidden' name='menu' value='history'>\n";
        
    echo "Select User: <SELECT name='partner'>\n";
    echo "<OPTION value=''>-- Select a user --</OPTION>\n";
        
    // For each user, generate an option
    while ($row = $res->fetch()) {
        $name = $row['name'];
        $id = $row['id'];
            
        print "<OPTION value='" . $id . "'>$name</OPTION>\n";
    }

    echo "</SELECT>\n";
    echo "<input type='submit' value='View Messages'>\n";
    echo "</form>\n";

	genHistory($db, $uid);
}

function genHistory($db, $uid) {
	if (isset($_GET['partner'])) {
        $partnerId = $_GET['partner'];
        // echo $partnerId;
        $query = "SELECT *, titan1.name AS sender_name
                  FROM 
                    message JOIN titan1 ON sid=id
                  WHERE 
                    (sid=$uid AND rid=$partnerId) 
                    OR 
                    (sid=$partnerId AND rid=$uid)
                  ORDER BY
                    msgDate DESC"; // important* sort by date
        
        $res = $db->query($query);

        if ($res === false || $res->rowCount() === 0) {
            debug('No messages found or database error');
            return;
        }

        echo "<h4>Messages:</h4>\n";

		echo "<div class='message-container'>";
		while ($msg = $res->fetch()) {
			// if current sid is the same as the sender id from the table apply the according style
			$senderId = $msg['sid'];
			$messageStyle = ($senderId === $uid) ? 'sender' : 'recipient';

			$date = $msg['msgDate'] ?: 'No date';
			$subject = $msg['subject'];
			$content = $msg['content'];

			echo "<div class='message $messageStyle'>";
			echo "<strong>" . ($messageStyle == 'sender' ? 'You' : $msg['sender_name']) . "</strong>";
			echo "<p><strong>Subject:</strong> $subject</p>";
			echo "<p>$content</p>";
			echo "<div class='message-time'>$date</div>";
			echo "</div>";        
		}
		echo "</div>";
	}
}


function genComposeForm($db, $sid) {
?>

<FORM name="fmMsg" method="POST" action="dashboard.php?menu=send">

<?php
//RECIPIENT: <INPUT type="text" name="rid" size="5" /><BR /><BR />

    $query = "SELECT id, name FROM titan1";
    $res = $db->query($query);

    if ($res == false) {
        debug("database error");
    }
    else {
        print "Recipient: <SELECT name='rid'>\n";

        // for each user in titan1, generate <OPTION>... </OPTION> string
        while ($row = $res->fetch()) {

            $name = $row['name'];
            $id   = $row['id'];

            print "<OPTION value='" . $id . "'>$name</OPTION>\n";
        }

        print "</SELECT><BR /><BR />\n";
    }

?>

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
    debug($query);
    $res = $db->query($query);

/*
    // marked (unnamed) placeholders
    $stmt  = $db->prepare("INSERT INTO message(sid, rid, subject, content) VALUE(?, ?, ?)");

    // option 1
    //$stmt->execute(array($sid, $rid, $subject, $content));

    // option 2
    $stmt->bindParam(1, $sid);
    $stmt->bindParam(2, $rid);
    $stmt->bindParam(3, $subject);
    $stmt->bindParam(4, $content);

    $res = $stmt->execute();
*/

    if ($res != false) {
        print "<P>Message sent</P>\n";
    }
    else {
        debug("error sending a message to $rid");
    }

}


?>