
<div class="block">
   <div class="submenu">
      <ul>
         <li><?php echo Html::anchor('user/profile_edit', __('Edit profile')); ?></li>
         <li><?php echo Html::anchor('user/unregister', __('Delete account')); ?></li>
      </ul>
      <br style="clear:both;">
   </div>
   <?
   //print("<pre>"); var_dump($avatar); exit();
   ?>
   <h1>
   	  <div class="avatar" style="float: left; margin: 5px 7px 0 0px;">
  		<img src="<?php echo $avatar ?>" border="1" width="62" height="62" >
      </div>
   	  <?php echo __('User profile') ?>
   </h1>
   <div class="content">
      <p class="intro">
      <?= __('This is your user information, :username .', array(':username'=> $user->username )) ?></p>

      <h2><?= __('Username & Email Address'); ?></h2>
      <p><?php echo $user->username ?> &mdash; <?php echo $user->email ?></p>

      <h2><?= __('First & Last name'); ?></h2>
      <p><?php echo $user->first_name ?> &mdash; <?php echo $user->last_name ?></p>

      <h2><?= __('Login Activity'); ?></h2>
      <p><?= __('Last login was :date, at :time.', array(':date'=> date('F jS, Y', $user->last_login) , ':time'=> date('h:i:s a', $user->last_login))) ?><br/><?= __('Total logins: :logins', array(':logins'=>$user->logins)) ?></p>

      <?php
      $providers = array_filter(Kohana::$config->load('useradmin.providers'));
      $identities = $user->user_identity->find_all();
      if($identities->count() > 0) {
         echo '<h2>'.__('Accounts associated with your user profile').'</h2><p>';
         foreach($identities as $identity) {
            echo '<a class="associated_account" style="background: #FFF url(/img/small/'.$identity->provider.'.png) no-repeat center center"></a>';
            unset($providers[$identity->provider]);
         }
         echo '<br style="clear: both;"></p>';
      }
      if(!empty($providers)) {
         echo '<h2>'.__('Additional account providers').'</h2><p>'.__('Click the provider icon to associate it with your existing account.').'</p><p>';
         foreach($providers as $provider => $enabled) {
            echo '<a class="associated_account '.$provider.'" href="'.URL::site('/user/associate/'.$provider).'"></a>';
         }
         echo '<br style="clear: both;"></p>';
      }
      ?>
   </div>
</div>
