这里收集一些探针

官网：[http://www.yahei.net/](http://www.yahei.net/)


### PHP探针不显示cpu、内存、硬盘信息的[原因](http://www.yahei.net/notice/2.php)   

```
1.proc目录权限 
看看proc目录的权限是多少？可以尝试将proc目录的权限设置为默认的0555 

2.apache的open_basedir设置 
如果你开启open_basedir安全设置，会导致探针无法显示内存等信息 

3.禁止了相应的php函数 
比如file或其他函数 

4.虚拟主机的面板自动设置 
有些面板，比如DirectAdmin或Kloxo等也会无法查看探针信息
```

官网下载备用 && 官网演示：  
tz.zip     简体版    [演示](http://www.yahei.net/tz/tz.php)  
tz_tw.zip  繁體版    [演示](http://www.yahei.net/tz/tz_tw.php)  
tz_e.zip   EngLish  [演示](http://www.yahei.net/tz/tz_e.php)  
top.zip    TOP版    [演示](http://www.yahei.net/tz/top.php)  
m.zip      手机版    [演示](http://www.yahei.net/tz/m.php)  

### Other
支持 PHP 7 来自[此](https://github.com/jakehu/phpinfo-by-yahei)


支持 PHP 7 来自[此](https://github.com/WuSiYu/PHP-Probe) [作者介绍](https://wusiyu.me/%e9%9b%85%e9%bb%91php%e6%8e%a2%e9%92%88%e9%ad%94%e6%94%b9%e7%89%88-%e6%94%af%e6%8c%81php7%ef%bc%8c%e5%a4%a7%e5%b9%85%e6%94%b9%e8%bf%9b%e7%95%8c%e9%9d%a2/)  



### [雅黑PHP探针PHP7/HTTPS/NoIP修改版](https://luotianyi.vc/1830.html)  
PHP7和SSL修正版  
修改了探针里不被PHP7支持的函数，并且调用资源全部https化  [演示](https://luotianyi.date/tz.php)  [下载](https://mirror.luotianyi.vc/code/tz/yhtz7-https.zip)  

无本机IP显示（NoIP）版  
有些套了CDN的服务器挂探针会暴露源站IP，于是去掉IP显示  [下载](https://mirror.luotianyi.vc/code/tz/yhtz7-https-NoIP.zip)  

### X 探针（刘海探针）——开源 PHP 探针  
[https://inn-studio.com/prober](https://inn-studio.com/prober)  
[https://github.com/kmvan/x-prober](https://github.com/kmvan/x-prober)  

[分享几个PHP探针](https://zkk.me/0x0012.html)  
[基于workerman的雅黑探针](https://github.com/wwng2333/tz)  
[PHP 雅黑探针---Docker启动](https://github.com/malaohu/php-yahei-tz)  
[yahei-php-youqu/雅黑PHP探针有趣版](https://github.com/hdoodle/yahei-php-youqu)  
[逼格 更高的逼格，雅黑探针精简装逼版 来自此](https://www.hostloc.com/thread-357633-1-1.html) [网站1](https://www.hostloc.com/thread-356945-1-1.html)  
