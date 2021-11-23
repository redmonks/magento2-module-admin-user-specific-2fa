# User Specific 2FA - Magento 2 Extension 

In Magento 2, Admin can't set specific 2FA provider for any user. If you enable more than one 2FA providers from the admin. Then Magento will send 2FA configuration email to admin user during the admin login if he/she has not configured all enabled 2FA provider.

Using this extension you can set any specific enabled 2FA provider for any user. 

> System > All Users  

Select any specific user, For whom you want to set the default 2FA. Now from the left tab you can select 2FA and set default 2FA provider for respective user.

![2FA Default Provider Option](https://raw.githubusercontent.com/sanchit-redmonks/repo-images/master/magento2-module-admin-user-specific-2fa/2FA-Default.png)

## Installation

> composer require redmonks/module-user-specific-two-factor-auth
