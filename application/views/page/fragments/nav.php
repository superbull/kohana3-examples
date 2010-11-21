<nav>
	<a href="<?php echo URL::site()?>">Home</a>
	<a href="<?php echo URL::site('links')?>">Links</a>
	<a href="<?php echo URL::site('contact')?>">Contact</a>
	
	<?php if (Auth::instance()->logged_in()) {?>
		
		<a href="<?php echo URL::site('auth/profile')?>">Profile</a>
		<a href="<?php echo URL::site('auth/signout')?>">Sign out</a>

	<?php } else {?>

		<a href="<?php echo URL::site('auth/signin')?>">Sign in</a>
		<a href="<?php echo URL::site('auth/signup')?>">Sign up</a>
	<?php } ?>

	<?php if (Kohana::$environment === Kohana::DEVELOPMENT){?>

		<a class="helper-right" href="#profiler">
			Profiler 
			<span class="plus">+</span>
			<span class="minus helper-hidden">&ndash;</span>
		</a>
	<?php }?>
</nav>
