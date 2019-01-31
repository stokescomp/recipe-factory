<?php
$title = "Home";
$page = "home";
$header_message = "The Recipe Factory";
require('includes/init.php');
if(isset($_GET['logout'])){
	logout();
}
require('page_includes/header.php');
?>
<article id="main">
   <a class="btn btn-default" href="<?php echo $root?>search">Search for a Recipe</a><br /><br />
   <img src="images/homepage-food.jpg" alt="food" />
</article>
<?php require('page_includes/footer.php');