<div class="block">
   <h1><?= __('Confirm associating your user account') ?></h1>
   <div class="content">
<?php

echo Form::open('user/associate/'.$provider_name, array('style' => 'display: inline;'));

echo '<p>'.__('You are about to associate your user account with your :provider_name account. After this, you can log in using that account. Are you sure?', array(':provider_name'=>ucfirst($provider_name))) .'</p>';

echo Form::hidden('confirmation', 'Y');

echo Form::submit(NULL, __('Yes'));
echo Form::close();

echo Form::open('user/profile', array('style' => 'display: inline; padding-left: 10px;'));
echo Form::submit(NULL, __('Cancel'));
echo Form::close();
?>
   </div>
</div>
