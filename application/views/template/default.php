<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" dir="ltr" lang="en-US">
<head>
   <title><?php echo $title ?></title>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
   <?php foreach ($styles as $file => $type) echo HTML::style($file, array('media' => $type)), "\n" ?>
   <?php foreach ($scripts as $file) echo HTML::script($file), "\n" ?>
   <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
</head>
<body>
   <div id="page">
      <div id="header"></div>
      <div id="navigation">
         <ul class="menu">

             <?php
             $session = Session::instance();
						echo '<li class="left">'.Html::anchor('/', __('Home')).'</li>';

             if (Auth::instance()->logged_in()){
			 	$avatarUrl = Orm::factory('avatar', Auth::instance()->get_user()->avatar_id )->get_avatar_url();
			 	$sUserFirstName	= Auth::instance()->get_user()->first_name;
			 	$sUserLastName 	= Auth::instance()->get_user()->last_name;

				if(Auth::instance()->logged_in('admin')){
					echo '<li class="right">'.Html::anchor('admin_user', __('User Admin')).'</li>';
				}
				echo '<li class="right">'.Html::anchor('user/logout', __('Log out')).'</li>';

				$img = sprintf(
					"<img src='%s' border=0 width=30 height=30 style='float:left; margin: 5px; 5px;'>",
					$avatarUrl
				);

				echo '<li class="right">'.Html::anchor('user/profile', $img." ".$sUserFirstName." ".$sUserLastName).'</li>';
             } else {
				echo '<li class="right">'.Html::anchor('user/register', __('Register')).'</li>';
				echo '<li class="right">'.Html::anchor('user/login', __('Log in')).'</li>';
             }
           ?>
         </ul>
      </div>
   <div id="content">
    <?php
    // output messages
     echo Message::output();
     echo $content ?>
   </div>
   <div id="footer">
   	<ul class="menu">
		<li class="left"><?= HTML::anchor('copyright', '&copy; 2011 Chris Petermann'); ?></li>
		<li class="left"><?= HTML::anchor('about', __('About')); ?></li>
		<li class="left"><?= HTML::anchor('contact', __('Contact')); ?></li>
		<li class="right"><?= LANGUAGES::lang_changer(); ?></li>
   	</ul>
   </div>
</div>

<?php if(KOHANA::$profiling === TRUE)
	echo View::factory('profiler/stats')
?>

</body>
</html>
