	<footer>
		Created by Michael &amp; Christina Stokes <?php echo date('Y') ?> &copy; <cite>This site is hosted using <a href='http://pagodabox.com/' target='_BLANK' tabindex="-1">Pagodabox!</a></cite>
	</footer>
	<?php if($live_refresh) 
		echo "<script>document.write('<script src=\"http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1\"></' + 'script>')</script>";
	?>
</body>
</html>