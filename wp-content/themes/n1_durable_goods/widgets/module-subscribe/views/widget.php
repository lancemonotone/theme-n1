<?php namespace N1_Durable_Goods;

$context_issue = N1_Magazine::get_context_issue();?>

<a id="issue-display-trigger" class="issue meta pubinfo wrapper" href="javascript:void(0)">
	<ul class="issue meta postinfo pubinfo trigger">
		<li class="issue meta pubinfo issuenumber"><span><?php echo $context_issue->post_title?></span></li>
		<li class="issue meta pubinfo issuetitle"><?php echo get_field('issue_name', $context_issue->ID)?></li>
	</ul>
</a>
<?php if(N1_Magazine::is_paywalled()){?>
<section class="subscribe">
	<div class="subscribe category"><span class="module-hed"><?php _e('Available Now')?></span></div>
	<h3 class="subscribe issuetitle"><?php echo N1_Magazine::get_current_issue()->post_title?>: <?php echo get_field('issue_name', $context_issue->ID)?></h3>
	<p class="subscribe prompt"><?php echo get_field('options_subscribe_prompt','options')?></p>
	<div class="wrapper">
		<a class="button" href="<?php echo home_url()?>/subscribe"><?php _e('Subscribe')?></a>
	</div>
	<?php if(!is_user_logged_in()){?>
	<div class="subscribe signin">
		<p class="subscribe action"><?php echo get_field('options_subscribe_action','options')?></p>
		<form class="subscribe cf" action="<?php echo home_url()?>/wp-login.php?redirect_to=<?php echo urlencode(site_url( $_SERVER['REQUEST_URI'] ))?>" method="POST">
			<fieldset class="subscribe username">
				<label class="subscribe username" for="username"><?php _e('Email')?></label>
				<input class="subscribe text username form-text" type="text" id="log" name="log" placeholder="email" />
			</fieldset>

			<fieldset class="subscribe password">
				<label class="subscribe password" for="password"><?php _e('Password')?></label>
				<input class="form-text home subscribe text password" type="password" id="pwd" name="pwd" placeholder="password" />
			</fieldset>

			<fieldset class="form-actions subscribe submit">
				<input class="subscribe submit button" type="submit" value="<?php _e('Sign In')?>" /><br/>
				<a href="<?php echo home_url()?>/forgot-password/">Forgot Password</a>
			</fieldset>
		</form>
	</div>
	<?php } ?>
</section>
<?php }?>
