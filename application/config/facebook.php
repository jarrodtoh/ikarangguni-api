<?php

if (isset($_SERVER['HTTP_ORIGIN']) && strpos($_SERVER['HTTP_ORIGIN'], 'localhost') != -1) {
  $config['facebook_config']['app_id'] = '155208588003403';
  $config['facebook_config']['app_secret'] = 'a447aca3a8349fae488ce76bbdaa6868';
} else {
  $config['facebook_config']['app_id'] = '497232760353519';
  $config['facebook_config']['app_secret'] = 'ebafbea5bf5afc2d6961b0767e5e97a9';
}

?>
