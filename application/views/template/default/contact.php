<?php
$form = new Appform();
if(isset($errors)) {
   $form->errors = $errors;
}
if(isset($username)) {
   $form->values['username'] = $username;
}
// set custom classes to get labels moved to bottom:
$form->error_class = 'error block';
$form->info_class = 'info block';

?>
<style>
#address {
    font-size: smaller;
    border: 1px solid silver;
    margin: 2px 0 0 2px;
    padding: 8px 0 8px 12px;
}
#address .name {
    font-weight: bold;
    margin-bottom: 4px;
    padding-bottom: 4px;
}
#address .line1 {
    margin-bottom: 0px;
    padding-bottom: 0px;
}
#address .line2 {
    margin-bottom: 4px;
    padding-bottom: 4px;
}
#address .city {
    font-weight: normal;
    margin-bottom: 4px;
    padding-bottom: 4px;
}
#address .postcode {
    font-weight: bold;
    text-transform: uppercase;
}

#address .country {
    font-weight: bold;
    text-decoration: underline;
    text-transform: uppercase;
}

#phone {
    font-size: smaller;
    border: 1px solid silver;
    margin: 2px 0 0 2px;
    padding: 8px 0 8px 12px;
}
#phone li {
    font-weight: bold;
}
</style>
<div id="box">
   <div class="block">
      <h1><?php echo __('Contact Us'); ?></h1>
      <div class="content">
<?php

if(!empty($showform)) {
    echo $form->open('contact');
    echo '<table><tr><td style="vertical-align: top;">';
    echo '<ul>';

    echo '<li>'.$form->label('contactname', __('Your name')).'</li>';
    echo $form->input('contactname', null, array('class' => 'text twothirds'));

    echo '<li>'.$form->label('emailaddress', __('Email Address')).'</li>';
    echo $form->input('emailaddress', null, array('class' => 'text twothirds'));

    echo '<li>'.$form->label('message', __('Your message or question')).'</li>';
    echo $form->textarea('message', null, array('class' => 'text onethird', 'style' => 'width:90%;'));

    echo '</ul>';
    echo $form->submit(NULL, __('Submit'));

    echo $form->close();
}elseif(!empty($msg)) {
    echo $msg;
}


if(!empty($showContactDetailsPost) || !empty($showContactDetailsPhone) ) {
    echo '</td><td width="5" style="border-right: 1px solid #DDD;">&nbsp;</td><td><td style="padding-left: 2px; vertical-align: top; width: 45%;">';

}else{
    echo '</td><td width="5" >&nbsp;</td><td><td style="padding-left: 2px; vertical-align: top;">';
}


if(!empty($showContactDetailsPost)) {
    echo '<ul>';
    echo '<li style="padding-bottom: 8px;font-weight: bold;">'.__('Contact us by Post:').'</li>';
    echo "<div id='address'>";
    echo "<p class='name'>Chris Petermann</p>";
    echo "<p class='line1'>4, Etruria Court</p>";
    echo "<p class='line2'>Grenfell Road</p>";
    echo "<p class='city'>Maidenhead, Berks</p>";
    echo "<p class='postcode'>SL6 1EJ</p>";
    echo "<p class='country'>United Kingdom</p>";
    echo "</div>";
}

if(!empty($showContactDetailsPhone)) {
    echo '<li style="padding-top: 21px; padding-bottom: 8px;font-weight: bold;">'.__('Contact us by Phone:').'</li>';
    echo "<div id='phone'>";
    echo "<li style=''>Home:</li>";
    echo "<p class='home'>+44 (0) 1628 673908</p>";
    echo "<li style=''>Chris mobile:</li>";
    echo "<p class='mobile'>+44 (0) 7921 070500</p>";
    echo "<li style=''>Monika mobile:</li>";
    echo "<p class='mobile'>+44 (0) 1234 567890</p>";
    echo "</div>";
}

echo '</ul>';
echo '</td></tr></table>';
?>
      </div>
   </div>
</div>


