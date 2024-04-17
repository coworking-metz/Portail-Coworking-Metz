<?php
// 1 - auto-login-reservation


//http://codex.wordpress.org/Function_Reference/wp_get_current_user
$current_user = wp_get_current_user();
/**
* @example Safe usage: $current_user = wp_get_current_user();
* if ( !($current_user instanceof WP_User) )
* return;
*/

/*

echo 'User first name: ' . $current_user->user_firstname . '<br />';
echo 'User last name: ' . $current_user->user_lastname . '<br />';
*/

?>

<script type="text/javascript">
//function setValuesOfUser()
var interval = setTimeout(function(){triggerCheck();}, 3000);
var loopCnt = 0;

function triggerCheck(){
var fieldForeName = document.getElementById("DOPBSPCalendar-form-field2_6");
var fieldLastName = document.getElementById("DOPBSPCalendar-form-field2_7");
var fieldMail = document.getElementById("DOPBSPCalendar-form-field2_8");

loopCnt++;
if(fieldForeName != null
&& fieldLastName != null
&& fieldMail != null
){
clearInterval(interval);

fieldForeName.value = '<?php print($current_user->user_firstname); ?>';
fieldLastName.value = '<?php print($current_user->user_lastname); ?>';
fieldMail.value = '<?php print($current_user->user_email); ?>';

}else if(loopCnt > 60){
clearInterval(interval);
}else{
setTimeout(function(){triggerCheck();}, 3000);
}
}

</script>