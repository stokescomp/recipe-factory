<?php
$title = "Manager Categories";
$page = "manage_categories";
$header_message = "Manage Categories for The Recipe Factory";
require('../includes/init.php');
require($_SERVER['DOCUMENT_ROOT'].'/page_includes/header.php');
?>
<article id="main">
   <p>Manage Categories information</p>
   <p><a href="<?php echo $root?>edit_categories">Edit Categories</a></p>
</article>
<?php require($_SERVER['DOCUMENT_ROOT'].'/page_includes/footer.php');