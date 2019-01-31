<?php
$title = "Page not found";
$page = "pagenotfound";
$header_message = "The Recipe Factory";
require('../includes/init.php');
require($_SERVER['DOCUMENT_ROOT'].'/page_includes/header.php');
?>
<article id="main">
   <p>The page you wanted to go to doesn't exist.</p>
</article>
<?php require($_SERVER['DOCUMENT_ROOT'].'/page_includes/footer.php');