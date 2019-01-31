<?php
$title = "Manager Users";
$page = "manage_users";
$header_message = "Manage Users for The Recipe Factory";
require('../includes/init.php');
require($_SERVER['DOCUMENT_ROOT'].'/page_includes/header.php');
?>
<article id="main">
   <p>Manage Users information</p>
   <p><a href="<?php echo $root?>edit_users">Edit Users</a></p>
</article>
<?php require($_SERVER['DOCUMENT_ROOT'].'/page_includes/footer.php');