<?php $depth = method_exists('General', 'getDepth') ? General::getDepth() : './'; ?>
<div class="navigation">
	<nav>
		<a href="<?php echo $depth; ?>" class="logo">
			<img src="<?php echo $depth; ?>img/logo.png" alt="H4H">
		</a>
		<input id="navigationDropdown" type="checkbox" class="navigation-checkbox">
		<label for="navigationDropdown" class="navigation-toogle">
			<span></span>
			<span></span>
			<span></span>
		</label>
		<ul>
			<li><a href="<?php echo $depth; ?>">Home</a></li>
			<li><span></span></li>
			<?php if (!empty($user) && is_a($user, 'User')): ?>
				<li><a href="<?php echo $depth; ?>profile.php">Profile</a></li>
				<li><span></span></li>
				<?php if ($user->getRole() == 1): ?>
					<li><a href="<?php echo $depth; ?>users/">Users</a></li>
					<li><span></span></li>
					<li><a href="<?php echo $depth; ?>roles/">Roles</a></li>
					<li><span></span></li>
				<?php endif; ?>
				<li>
					<form method="post" action="<?php echo $depth; ?>php/sign_out.php">
						<input type="submit" value="Sign Out" class="navigation-submit">
					</form>
				</li>
			<?php else: ?>
				<li><a href="<?php echo $depth; ?>register.php">Register</a></li>
				<li><span></span></li>
				<li><a href="<?php echo $depth; ?>sign_in.php">Sign In</a></li>
			<?php endif; ?>
		</ul>
	</nav>
</div>
