## Screenshots and video
**This module is evolving** and the screenshots and the video below (made in **2015**) are outdated.  
The actual screenshots and the backend settings you can see on the «**[Login with Amazon]( https://mage2.pro/t/1763)**» and «**[Blackbaud NetCommunity](https://mage2.pro/t/2173)**» modules pages.  
The «Facebook Login» now looks and behaves very closely to these modules.  
The module is **free** and **open source**.

### An outdated screenshot (2015)
![](https://mage2.pro/uploads/default/original/1X/9d55c5338c7bf62bd8bb7196469bc4239cb09591.png)

### An outdated video (2015)
https://www.youtube.com/watch?v=BZ-W8rpM_mc

## How to install
[Hire me in Upwork](https://upwork.com/fl/mage2pro), and I will: 
- install and configure the module properly on your website
- answer your questions
- solve compatiblity problems with third-party checkout, shipping, marketing modules
- implement new features you need 

### 2. Self-installation
```
bin/magento maintenance:enable
rm -f composer.lock
composer clear-cache
composer require mage2pro/facebook-login:*
bin/magento setup:upgrade
bin/magento cache:enable
rm -rf var/di var/generation generated/code
bin/magento setup:di:compile
rm -rf pub/static/*
bin/magento setup:static-content:deploy -f en_US <additional locales>
bin/magento maintenance:disable
```

## How to update
```
bin/magento maintenance:enable
composer remove mage2pro/facebook-login
rm -f composer.lock
composer clear-cache
composer require mage2pro/facebook-login:*
bin/magento setup:upgrade
bin/magento cache:enable
rm -rf var/di var/generation generated/code
bin/magento setup:di:compile
rm -rf pub/static/*
bin/magento setup:static-content:deploy -f en_US <additional locales>
bin/magento maintenance:disable
```

