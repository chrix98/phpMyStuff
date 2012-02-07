<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
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
         <div class="menu">
            <ul class="menu" style="float: left;">
             <?php
             $session = Session::instance();
             $controller_name = Request::current()->controller();
             $action_name = Request::current()->action();

             if (Auth::instance()->logged_in()){
                if (Auth::instance()->logged_in('translate')){
                  echo '<li>'.Html::anchor('translate/index', __('Translate'), array('class' => ($controller_name == 'translate' ? 'selected' : ''))).'</td>';
                }
                if (Auth::instance()->logged_in('admin')){
                  echo '<li>'.Html::anchor('translate/admin/index', __('Admin'), array('class' => ($controller_name == 'admin' ? 'selected' : ''))).'</td>';
                }
                echo '<li>'.Html::anchor('user/profile_edit', __('Profile'), array('class' => (($controller_name == 'user' && $action_name == 'change_password') ? 'selected' : ''))).'</td>';
                echo '<li>'.Html::anchor('user/logout', __('Log out')).'</li>';
             } else {
                echo '<li>'.Html::anchor('user/register', __('Register')).'</td>';
                echo '<li>'.Html::anchor('user/login', __('Log in')).'</li>';
             }
             // links to allow changing language
           ?>
            </ul>
            <div style="float: right">
               <ul class="menu">
            <?php
                echo '<li><span>'.__('Change language').':'.'</span></li>';
                echo '<li>'.Html::anchor('/translate/change_language/en-us', 'English').'</li>';
                echo '<li>'.Html::anchor('/translate/change_language/fi', 'Finnish').'</li>';
            ?>
                  </ul>
            </div>
         </div>
      </div>
   <div id="content">
    <?php
    // output messages
      $messages = Session::instance()->get('messages');
      Session::instance()->delete('messages');

      if(!empty($messages)) {
         foreach($messages as $type => $messages) {
            foreach($messages as $message) {
               echo '<div class="'.$type.'">'.$message.'</div>';
            }
         }
      }
     echo $content ?>
   </div>
</div>

<div id="kohana-profiler">
<?php // echo View::factory('profiler/stats');
?>
</div>
</body>
</html>
