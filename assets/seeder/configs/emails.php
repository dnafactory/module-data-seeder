<?php

use \Magento\Framework\App\Config\ScopeConfigInterface;

/*
SCOPE => this is the scope of the config, use ScopeConfigInterface to set the value (ex: STORE)
SCOPE_ID => this is the scope id used to identify the selected scope (ex: 0)
PATH => this is the path to config tha should have an email template (ex: vendor/module/email_template)
NAME => this is the name of email template
CODE => this is the code of email template
*/

//If code is not set the software will take the first email that has the name inserted. Be careful to insert the right name and code!

return [
//  [
//    'scope' => SCOPE,
//    'scope_id' => SCOPE_ID,
//    'path' => CONFIG_PATH,
//    'name' => EMAIL_NAME,
//    'code' => EMAIL_CODE | NULL
//  ]
];
