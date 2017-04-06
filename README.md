## Screenshots and video
**This extension is evolving** and the screenshots and the video below (made in **2015**) are outdated.  
The actual screenshots and the backend settings you can see on the «**[Login with Amazon]( https://mage2.pro/t/topic/1763)**» and «**[Blackbaud NetCommunity](https://mage2.pro/t/topic/2173)**» extensions pages.  
The «Facebook Login» now looks and behaves very closely to these extensions.

### An outdated screenshot (2015)
![](https://mage2.pro/uploads/default/original/1X/9d55c5338c7bf62bd8bb7196469bc4239cb09591.png)

### An outdated video (2015)
https://www.youtube.com/watch?v=BZ-W8rpM_mc

## How to buy
The extension is free, but if you need my installation and support service, you can [buy it](https://mage2.pro/t/136).

## How to install
### 1. Free installation service (if you have bought the extension)
Just order my [free installation service](https://mage2.pro/t/3585).

### 2. Self-installation
```
composer require mage2pro/facebook-login:*
bin/magento setup:upgrade
rm -rf pub/static/* && bin/magento setup:static-content:deploy
rm -rf var/di var/generation && bin/magento setup:di:compile
```
If you have some problems while executing these commands, then check the [detailed instruction](https://mage2.pro/t/263).


## Support
- [Ask a question](https://mage2.pro/c/extensions/facebook-login)
- [Report an issue](https://github.com/mage2pro/facebook-login/issues)



