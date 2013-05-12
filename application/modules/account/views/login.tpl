{extends 'layout.tpl'}

{block 'main'}
	<div id="login">
		<h1 class="nocenter">Login</h1>
		<form action="<?php echo site_url('account/login'); ?>" name="login-form" method="POST" class="styled-form">
		    <p><label for="username">Username:</label><br><input class="text" type="text" name="username"></p>
		    <p><label for="password">Password:</label><br><input class="text" type="password" name="password"></p>
		    <p><input type="submit" value="Login"></p>
		</form>
		<?php if (isset($errors)): ?>
		<?php foreach ($errors AS $error): ?>
		<p style="color: #FF0000; font-size: 14px; font-weight: bold;"><?php echo $error; ?></p>
		<?php endforeach; ?>
		<?php endif; ?>
	</div>
{/block}