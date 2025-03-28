<?php 

session_start();

print_r($_SESSION);
print_r($_POST);
print_r($_GET);

include("util.php"); 



$menu = "inbox"; // default menu

if (isset($_GET['menu'])) {
    $menu = $_GET['menu'];
}

//$uid = 10; // assume user is Robin (until login/logout is implemented)
//$uname = "Robin";

?>

<!DOCTYPE html>
<HTML>
<HEAD>
<TITLE> Message Dashboard </TITLE>

<?php include("bootstrap.php"); ?>
<script src='myScript.js'> </script>
</HEAD>

<BODY>

<DIV class="container">

<!-- banner -->
<DIV class="row">
<DIV class="col-8" style="font-size: 30px">Welcome <?php echo $_SESSION['uname']; ?>!</DIV>
<DIV class="col-4">
<?php
// show login if user isn't logged in or logout form if user is logged in
if (!isset($_SESSION['uid'])) {
    genLoginForm();
}
else {
    genLogoutForm();
}

?>
</DIV>
</DIV>

<!-- navbar: menu -->
<DIV class="row">

<!-- href="?menu=inbox" access current html/php file -->
<DIV class="col-4 menuItem"><A href="dashboard.php?menu=inbox">Inbox</A></DIV>
<DIV class="col-4 menuItem"><A href="?menu=compose">Compose</A></DIV>
<DIV class="col-4 menuItem"><A href="?menu=history">Message History</A></DIV>

</DIV>

<!-- actual content for each menu item -->
<DIV class="row main">

<DIV class="col-12">

<?php

// based on $menu, call appropriate function to display intended content

switch ($menu) {
case 'inbox': 
    genInbox($db, $_SESSION['uid']);
    break;
case 'compose':
    genComposeForm($db, $_SESSION['uid']);
    break;
case 'send':
    sendMessage($db, $_SESSION['uid'], $_POST);
    break;
case 'login':
    processLogin($db, $_POST);
    break;
case 'logout':
    processLogout();
    break;
case 'history':
    genHistoryForm($db, $_SESSION['uid']);
}

?>

</DIV> <!-- col-12 -->

</DIV> <!-- row -->

</DIV> <!-- container -->

</BODY>
</HTML>
