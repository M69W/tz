<?php
/* ---------------------------------------------------- */
/* 程序名称: PHP探针-Yahei
/* 程序功能: 探测系统的Web服务器运行环境
/* 程序开发: Yahei.Net
/* 联系方式: info@Yahei.net
/* Date: 1970-01-01 / 2012-07-08
/* ---------------------------------------------------- */
/* 使用条款:
/* 1.该软件免费使用.
/* 2.禁止任何衍生版本.
/* ---------------------------------------------------- */
/* 感谢以下朋友为探针做出的贡献:
/* zyypp,酷を龙卷风,龙智超,菊花肿了,闲人,Clare Lou,hotsnow
/* 二戒,yexinzhu,wangyu1314,Kokgog,gibyasus,黃子珅,A大,huli
/* 小松,charwin,华景网络
/* 您可能是下一个?
/* ---------------------------------------------------- */
error_reporting(0); //抑制所有错误信息
@header("content-Type: text/html; charset=utf-8"); //语言强制
ob_start();
date_default_timezone_set('Asia/Shanghai');//此句用于消除时间差

$title = '雅黑PHP探针[精简版]';
$version = "v0.4.7"; //版本号

define('HTTP_HOST', preg_replace('~^www\.~i', '', $_SERVER['HTTP_HOST']));

$time_start = microtime_float();

function memory_usage() 
{
	$memory	 = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB';
	return $memory;
}

// 计时
function microtime_float() 
{
	$mtime = microtime();
	$mtime = explode(' ', $mtime);
	return $mtime[1] + $mtime[0];
}

//单位转换
function formatsize($size) 
{
	$danwei=array(' B ',' K ',' M ',' G ',' T ');
	$allsize=array();
	$i=0;

	for($i = 0; $i <5; $i++) 
	{
		if(floor($size/pow(1024,$i))==0){break;}
	}

	for($l = $i-1; $l >=0; $l--) 
	{
		$allsize1[$l]=floor($size/pow(1024,$l));
		$allsize[$l]=$allsize1[$l]-$allsize1[$l+1]*1024;
	}

	$len=count($allsize);

	for($j = $len-1; $j >=0; $j--) 
	{
		$fsize=$fsize.$allsize[$j].$danwei[$j];
	}	
	return $fsize;
}

function valid_email($str) 
{
	return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
}

//检测PHP设置参数
function show($varName)
{
	switch($result = get_cfg_var($varName))
	{
		case 0:
			return '<font color="red">×</font>';
		break;
		
		case 1:
			return '<font color="green">√</font>';
		break;
		
		default:
			return $result;
		break;
	}
}

//保留服务器性能测试结果
$valInt = isset($_POST['pInt']) ? $_POST['pInt'] : "未测试";
$valFloat = isset($_POST['pFloat']) ? $_POST['pFloat'] : "未测试";
$valIo = isset($_POST['pIo']) ? $_POST['pIo'] : "未测试";

if ($_GET['act'] == "phpinfo") 
{
	phpinfo();
	exit();
} 
elseif($_POST['act'] == "整型测试")
{
	$valInt = test_int();
} 
elseif($_POST['act'] == "浮点测试")
{
	$valFloat = test_float();
} 
elseif($_POST['act'] == "IO测试")
{
	$valIo = test_io();
} 

elseif($_GET['act'] == "Function")
{
	$arr = get_defined_functions();
	Function php()
	{
	}
	echo "<pre>";
	Echo "这里显示系统所支持的所有函数,和自定义函数\n";
	print_r($arr);
	echo "</pre>";
	exit();
}elseif($_GET['act'] == "disable_functions")
{
	$disFuns=get_cfg_var("disable_functions");
	if(empty($disFuns))
	{
		$arr = '<font color=red>×</font>';
	}
	else
	{ 
		$arr = $disFuns;
	}
	Function php()
	{
	}
	echo "<pre>";
	Echo "这里显示系统被禁用的函数\n";
	print_r($arr);
	echo "</pre>";
	exit();
}

	
// 检测函数支持
function isfun($funName = '')
{
    if (!$funName || trim($funName) == '' || preg_match('~[^a-z0-9\_]+~i', $funName, $tmp)) return '错误';
	return (false !== function_exists($funName)) ? '<font color="green">√</font>' : '<font color="red">×</font>';
}
function isfun1($funName = '')
{
    if (!$funName || trim($funName) == '' || preg_match('~[^a-z0-9\_]+~i', $funName, $tmp)) return '错误';
	return (false !== function_exists($funName)) ? '√' : '×';
}

function GetCoreInformation() {$data = file('/proc/stat');$cores = array();foreach( $data as $line ) {if( preg_match('/^cpu[0-9]/', $line) ){$info = explode(' ', $line);$cores[]=array('user'=>$info[1],'nice'=>$info[2],'sys' => $info[3],'idle'=>$info[4],'iowait'=>$info[5],'irq' => $info[6],'softirq' => $info[7]);}}return $cores;}
function GetCpuPercentages($stat1, $stat2) {if(count($stat1)!==count($stat2)){return;}$cpus=array();for( $i = 0, $l = count($stat1); $i < $l; $i++) {	$dif = array();	$dif['user'] = $stat2[$i]['user'] - $stat1[$i]['user'];$dif['nice'] = $stat2[$i]['nice'] - $stat1[$i]['nice'];	$dif['sys'] = $stat2[$i]['sys'] - $stat1[$i]['sys'];$dif['idle'] = $stat2[$i]['idle'] - $stat1[$i]['idle'];$dif['iowait'] = $stat2[$i]['iowait'] - $stat1[$i]['iowait'];$dif['irq'] = $stat2[$i]['irq'] - $stat1[$i]['irq'];$dif['softirq'] = $stat2[$i]['softirq'] - $stat1[$i]['softirq'];$total = array_sum($dif);$cpu = array();foreach($dif as $x=>$y) $cpu[$x] = round($y / $total * 100, 2);$cpus['cpu' . $i] = $cpu;}return $cpus;}
$stat1 = GetCoreInformation();sleep(1);$stat2 = GetCoreInformation();$data = GetCpuPercentages($stat1, $stat2);
$cpu_show = $data['cpu0']['user']."%us,  ".$data['cpu0']['sys']."%sy,  ".$data['cpu0']['nice']."%ni, ".$data['cpu0']['idle']."%id,  ".$data['cpu0']['iowait']."%wa,  ".$data['cpu0']['irq']."%irq,  ".$data['cpu0']['softirq']."%softirq";

// 根据不同系统取得CPU相关信息
switch(PHP_OS)
{
	case "Linux":
		$sysReShow = (false !== ($sysInfo = sys_linux()))?"show":"none";
	break;
	
	case "FreeBSD":
		$sysReShow = (false !== ($sysInfo = sys_freebsd()))?"show":"none";
	break;
/*	
	case "WINNT":
		$sysReShow = (false !== ($sysInfo = sys_windows()))?"show":"none";
	break;
*/	
	default:
	break;
}

//linux系统探测
function sys_linux()
{
    // CPU
    if (false === ($str = @file("/proc/cpuinfo"))) return false;
    $str = implode("", $str);
    @preg_match_all("/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(\@.-]+)([\r\n]+)/s", $str, $model);
    @preg_match_all("/cpu\s+MHz\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $mhz);
    @preg_match_all("/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/", $str, $cache);
    @preg_match_all("/bogomips\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $bogomips);
    if (false !== is_array($model[1]))
	{
        $res['cpu']['num'] = sizeof($model[1]);
		/*
        for($i = 0; $i < $res['cpu']['num']; $i++)
        {
            $res['cpu']['model'][] = $model[1][$i].'&nbsp;('.$mhz[1][$i].')';
            $res['cpu']['mhz'][] = $mhz[1][$i];
            $res['cpu']['cache'][] = $cache[1][$i];
            $res['cpu']['bogomips'][] = $bogomips[1][$i];
        }*/
		if($res['cpu']['num']==1)
			$x1 = '';
		else
			$x1 = ' ×'.$res['cpu']['num'];
		$mhz[1][0] = ' | 频率:'.$mhz[1][0];
		$cache[1][0] = ' | 二级缓存:'.$cache[1][0];
		$bogomips[1][0] = ' | Bogomips:'.$bogomips[1][0];
		$res['cpu']['model'][] = $model[1][0].$mhz[1][0].$cache[1][0].$bogomips[1][0].$x1;
        if (false !== is_array($res['cpu']['model'])) $res['cpu']['model'] = implode("<br />", $res['cpu']['model']);
        if (false !== is_array($res['cpu']['mhz'])) $res['cpu']['mhz'] = implode("<br />", $res['cpu']['mhz']);
        if (false !== is_array($res['cpu']['cache'])) $res['cpu']['cache'] = implode("<br />", $res['cpu']['cache']);
        if (false !== is_array($res['cpu']['bogomips'])) $res['cpu']['bogomips'] = implode("<br />", $res['cpu']['bogomips']);
	}

    // NETWORK

    // UPTIME
    if (false === ($str = @file("/proc/uptime"))) return false;
    $str = explode(" ", implode("", $str));
    $str = trim($str[0]);
    $min = $str / 60;
    $hours = $min / 60;
    $days = floor($hours / 24);
    $hours = floor($hours - ($days * 24));
    $min = floor($min - ($days * 60 * 24) - ($hours * 60));
    if ($days !== 0) $res['uptime'] = $days."天";
    if ($hours !== 0) $res['uptime'] .= $hours."小时";
    $res['uptime'] .= $min."分钟";

    // MEMORY
    if (false === ($str = @file("/proc/meminfo"))) return false;
    $str = implode("", $str);
    preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buf);
	preg_match_all("/Buffers\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buffers);

    $res['memTotal'] = round($buf[1][0]/1024, 2);
    $res['memFree'] = round($buf[2][0]/1024, 2);
    $res['memBuffers'] = round($buffers[1][0]/1024, 2);
	$res['memCached'] = round($buf[3][0]/1024, 2);
    $res['memUsed'] = $res['memTotal']-$res['memFree'];
    $res['memPercent'] = (floatval($res['memTotal'])!=0)?round($res['memUsed']/$res['memTotal']*100,2):0;

    $res['memRealUsed'] = $res['memTotal'] - $res['memFree'] - $res['memCached'] - $res['memBuffers']; //真实内存使用
	$res['memRealFree'] = $res['memTotal'] - $res['memRealUsed']; //真实空闲
    $res['memRealPercent'] = (floatval($res['memTotal'])!=0)?round($res['memRealUsed']/$res['memTotal']*100,2):0; //真实内存使用率

	$res['memCachedPercent'] = (floatval($res['memCached'])!=0)?round($res['memCached']/$res['memTotal']*100,2):0; //Cached内存使用率

    $res['swapTotal'] = round($buf[4][0]/1024, 2);
    $res['swapFree'] = round($buf[5][0]/1024, 2);
    $res['swapUsed'] = round($res['swapTotal']-$res['swapFree'], 2);
    $res['swapPercent'] = (floatval($res['swapTotal'])!=0)?round($res['swapUsed']/$res['swapTotal']*100,2):0;

    // LOAD AVG
    if (false === ($str = @file("/proc/loadavg"))) return false;
    $str = explode(" ", implode("", $str));
    $str = array_chunk($str, 4);
    $res['loadAvg'] = implode(" ", $str[0]);

    return $res;
}

//FreeBSD系统探测
function sys_freebsd()
{
	//CPU
	if (false === ($res['cpu']['num'] = get_key("hw.ncpu"))) return false;
	$res['cpu']['model'] = get_key("hw.model");
	//LOAD AVG
	if (false === ($res['loadAvg'] = get_key("vm.loadavg"))) return false;
	//UPTIME
	if (false === ($buf = get_key("kern.boottime"))) return false;
	$buf = explode(' ', $buf);
	$sys_ticks = time() - intval($buf[3]);
	$min = $sys_ticks / 60;
	$hours = $min / 60;
	$days = floor($hours / 24);
	$hours = floor($hours - ($days * 24));
	$min = floor($min - ($days * 60 * 24) - ($hours * 60));
	if ($days !== 0) $res['uptime'] = $days."天";
	if ($hours !== 0) $res['uptime'] .= $hours."小时";
	$res['uptime'] .= $min."分钟";
	//MEMORY
	if (false === ($buf = get_key("hw.physmem"))) return false;
	$res['memTotal'] = round($buf/1024/1024, 2);

	$str = get_key("vm.vmtotal");
	preg_match_all("/\nVirtual Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i", $str, $buff, PREG_SET_ORDER);
	preg_match_all("/\nReal Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i", $str, $buf, PREG_SET_ORDER);

	$res['memRealUsed'] = round($buf[0][2]/1024, 2);
	$res['memCached'] = round($buff[0][2]/1024, 2);
	$res['memUsed'] = round($buf[0][1]/1024, 2) + $res['memCached'];
	$res['memFree'] = $res['memTotal'] - $res['memUsed'];
	$res['memPercent'] = (floatval($res['memTotal'])!=0)?round($res['memUsed']/$res['memTotal']*100,2):0;

	$res['memRealPercent'] = (floatval($res['memTotal'])!=0)?round($res['memRealUsed']/$res['memTotal']*100,2):0;

	return $res;
}

//取得参数值 FreeBSD
function get_key($keyName)
{
	return do_command('sysctl', "-n $keyName");
}

//确定执行文件位置 FreeBSD
function find_command($commandName)
{
	$path = array('/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');
	foreach($path as $p) 
	{
		if (@is_executable("$p/$commandName")) return "$p/$commandName";
	}
	return false;
}

//执行系统命令 FreeBSD
function do_command($commandName, $args)
{
	$buffer = "";
	if (false === ($command = find_command($commandName))) return false;
	if ($fp = @popen("$command $args", 'r')) 
	{
		while (!@feof($fp))
		{
			$buffer .= @fgets($fp, 4096);
		}
		return trim($buffer);
	}
	return false;
}

//windows系统探测
function sys_windows()
{
	if (PHP_VERSION >= 5)
	{
		$objLocator = new COM("WbemScripting.SWbemLocator");
		$wmi = $objLocator->ConnectServer();
		$prop = $wmi->get("Win32_PnPEntity");
	}
	else
	{
		return false;
	}

	//CPU
	$cpuinfo = GetWMI($wmi,"Win32_Processor", array("Name","L2CacheSize","NumberOfCores"));
	$res['cpu']['num'] = $cpuinfo[0]['NumberOfCores'];
	if (null == $res['cpu']['num']) 
	{
		$res['cpu']['num'] = 1;
	}/*
	for ($i=0;$i<$res['cpu']['num'];$i++)
	{
		$res['cpu']['model'] .= $cpuinfo[0]['Name']."<br />";
		$res['cpu']['cache'] .= $cpuinfo[0]['L2CacheSize']."<br />";
	}*/
	$cpuinfo[0]['L2CacheSize'] = ' ('.$cpuinfo[0]['L2CacheSize'].')';
	if($res['cpu']['num']==1)
		$x1 = '';
	else
		$x1 = ' ×'.$res['cpu']['num'];
	$res['cpu']['model'] = $cpuinfo[0]['Name'].$cpuinfo[0]['L2CacheSize'].$x1;
	// SYSINFO
	$sysinfo = GetWMI($wmi,"Win32_OperatingSystem", array('LastBootUpTime','TotalVisibleMemorySize','FreePhysicalMemory','Caption','CSDVersion','SerialNumber','InstallDate'));
	$sysinfo[0]['Caption']=iconv('GBK', 'UTF-8',$sysinfo[0]['Caption']);
	$sysinfo[0]['CSDVersion']=iconv('GBK', 'UTF-8',$sysinfo[0]['CSDVersion']);
	$res['win_n'] = $sysinfo[0]['Caption']." ".$sysinfo[0]['CSDVersion']." 序列号:{$sysinfo[0]['SerialNumber']} 于".date('Y年m月d日H:i:s',strtotime(substr($sysinfo[0]['InstallDate'],0,14)))."安装";
	//UPTIME
	$res['uptime'] = $sysinfo[0]['LastBootUpTime'];

	$sys_ticks = 3600*8 + time() - strtotime(substr($res['uptime'],0,14));
	$min = $sys_ticks / 60;
	$hours = $min / 60;
	$days = floor($hours / 24);
	$hours = floor($hours - ($days * 24));
	$min = floor($min - ($days * 60 * 24) - ($hours * 60));
	if ($days !== 0) $res['uptime'] = $days."天";
	if ($hours !== 0) $res['uptime'] .= $hours."小时";
	$res['uptime'] .= $min."分钟";

	//MEMORY
	$res['memTotal'] = round($sysinfo[0]['TotalVisibleMemorySize']/1024,2);
	$res['memFree'] = round($sysinfo[0]['FreePhysicalMemory']/1024,2);
	$res['memUsed'] = $res['memTotal']-$res['memFree'];	//上面两行已经除以1024,这行不用再除了
	$res['memPercent'] = round($res['memUsed'] / $res['memTotal']*100,2);

	$swapinfo = GetWMI($wmi,"Win32_PageFileUsage", array('AllocatedBaseSize','CurrentUsage'));

	// LoadPercentage
	$loadinfo = GetWMI($wmi,"Win32_Processor", array("LoadPercentage"));
	$res['loadAvg'] = $loadinfo[0]['LoadPercentage'];

	return $res;
}

function GetWMI($wmi,$strClass, $strValue = array())
{
	$arrData = array();

	$objWEBM = $wmi->Get($strClass);
	$arrProp = $objWEBM->Properties_;
	$arrWEBMCol = $objWEBM->Instances_();
	foreach($arrWEBMCol as $objItem) 
	{
		@reset($arrProp);
		$arrInstance = array();
		foreach($arrProp as $propItem) 
		{
			eval("\$value = \$objItem->" . $propItem->Name . ";");
			if (empty($strValue)) 
			{
				$arrInstance[$propItem->Name] = trim($value);
			} 
			else
			{
				if (in_array($propItem->Name, $strValue)) 
				{
					$arrInstance[$propItem->Name] = trim($value);
				}
			}
		}
		$arrData[] = $arrInstance;
	}
	return $arrData;
}

//比例条
function bar($percent)
{
?>
	<div class="bar"><div class="barli" style="width:<?php echo $percent?>%">&nbsp;</div></div>
<?php
}

$uptime = $sysInfo['uptime']; //在线时间
$stime = date('Y-m-d H:i:s'); //系统当前时间

//硬盘
$dt = round(@disk_total_space(".")/(1024*1024*1024),3); //总
$df = round(@disk_free_space(".")/(1024*1024*1024),3); //可用
$du = $dt-$df; //已用
$hdPercent = (floatval($dt)!=0)?round($du/$dt*100,2):0;

$load = $sysInfo['loadAvg'];	//系统负载


//判断内存如果小于1G，就显示M，否则显示G单位
if($sysInfo['memTotal']<1024)
{
	$memTotal = $sysInfo['memTotal']." M";
	$mt = $sysInfo['memTotal']." M";
	$mu = $sysInfo['memUsed']." M";
	$mf = $sysInfo['memFree']." M";
	$mc = $sysInfo['memCached']." M";	//cache化内存
	$mb = $sysInfo['memBuffers']." M";	//缓冲
	$st = $sysInfo['swapTotal']." M";
	$su = $sysInfo['swapUsed']." M";
	$sf = $sysInfo['swapFree']." M";
	$swapPercent = $sysInfo['swapPercent'];
	$memRealUsed = $sysInfo['memRealUsed']." M"; //真实内存使用
	$memRealFree = $sysInfo['memRealFree']." M"; //真实内存空闲
	$memRealPercent = $sysInfo['memRealPercent']; //真实内存使用比率
	$memPercent = $sysInfo['memPercent']; //内存总使用率
	$memCachedPercent = $sysInfo['memCachedPercent']; //cache内存使用率
}
else
{
	$memTotal = round($sysInfo['memTotal']/1024,3)." G";
	$mt = round($sysInfo['memTotal']/1024,3)." G";
	$mu = round($sysInfo['memUsed']/1024,3)." G";
	$mf = round($sysInfo['memFree']/1024,3)." G";
	$mc = round($sysInfo['memCached']/1024,3)." G";
	$mb = round($sysInfo['memBuffers']/1024,3)." G";
	$st = round($sysInfo['swapTotal']/1024,3)." G";
	$su = round($sysInfo['swapUsed']/1024,3)." G";
	$sf = round($sysInfo['swapFree']/1024,3)." G";
	$swapPercent = $sysInfo['swapPercent'];
	$memRealUsed = round($sysInfo['memRealUsed']/1024,3)." G"; //真实内存使用
	$memRealFree = round($sysInfo['memRealFree']/1024,3)." G"; //真实内存空闲
	$memRealPercent = $sysInfo['memRealPercent']; //真实内存使用比率
	$memPercent = $sysInfo['memPercent']; //内存总使用率
	$memCachedPercent = $sysInfo['memCachedPercent']; //cache内存使用率
}

//网卡流量
$strs = @file("/proc/net/dev"); 

for ($i = 2; $i < count($strs); $i++ )
{
	preg_match_all( "/([^\s]+):[\s]{0,}(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/", $strs[$i], $info );
	$NetOutSpeed[$i] = $info[10][0];
	$NetInputSpeed[$i] = $info[2][0];
	$NetInput[$i] = formatsize($info[2][0]);
	$NetOut[$i]  = formatsize($info[10][0]);
}

//ajax调用实时刷新
if ($_GET['act'] == "rt")
{
	$arr=array('useSpace'=>"$du",'freeSpace'=>"$df",'hdPercent'=>"$hdPercent",'barhdPercent'=>"$hdPercent%",'TotalMemory'=>"$mt",'UsedMemory'=>"$mu",'FreeMemory'=>"$mf",'CachedMemory'=>"$mc",'Buffers'=>"$mb",'TotalSwap'=>"$st",'swapUsed'=>"$su",'swapFree'=>"$sf",'loadAvg'=>"$load",'uptime'=>"$uptime",'freetime'=>"$freetime",'bjtime'=>"$bjtime",'stime'=>"$stime",'memRealPercent'=>"$memRealPercent",'memRealUsed'=>"$memRealUsed",'memRealFree'=>"$memRealFree",'memPercent'=>"$memPercent%",'memCachedPercent'=>"$memCachedPercent",'barmemCachedPercent'=>"$memCachedPercent%",'swapPercent'=>"$swapPercent",'barmemRealPercent'=>"$memRealPercent%",'barswapPercent'=>"$swapPercent%",'NetOut2'=>"$NetOut[2]",'NetOut3'=>"$NetOut[3]",'NetOut4'=>"$NetOut[4]",'NetOut5'=>"$NetOut[5]",'NetOut6'=>"$NetOut[6]",'NetOut7'=>"$NetOut[7]",'NetOut8'=>"$NetOut[8]",'NetOut9'=>"$NetOut[9]",'NetOut10'=>"$NetOut[10]",'NetInput2'=>"$NetInput[2]",'NetInput3'=>"$NetInput[3]",'NetInput4'=>"$NetInput[4]",'NetInput5'=>"$NetInput[5]",'NetInput6'=>"$NetInput[6]",'NetInput7'=>"$NetInput[7]",'NetInput8'=>"$NetInput[8]",'NetInput9'=>"$NetInput[9]",'NetInput10'=>"$NetInput[10]",'NetOutSpeed2'=>"$NetOutSpeed[2]",'NetOutSpeed3'=>"$NetOutSpeed[3]",'NetOutSpeed4'=>"$NetOutSpeed[4]",'NetOutSpeed5'=>"$NetOutSpeed[5]",'NetInputSpeed2'=>"$NetInputSpeed[2]",'NetInputSpeed3'=>"$NetInputSpeed[3]",'NetInputSpeed4'=>"$NetInputSpeed[4]",'NetInputSpeed5'=>"$NetInputSpeed[5]");
	$jarr=json_encode($arr); 
	$_GET['callback'] = htmlspecialchars($_GET['callback']);
	echo $_GET['callback'],'(',$jarr,')';
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $title.$version; ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script type="text/javascript" src="mjs/jquery-1.8.0.js"></script> 
<script src="mjs/echarts.js" charset="UTF-8"></script>
<script type="text/javascript">
	$('#document').ready(function(){
		 getEcharts();
	 });
</script>

<script type="text/javascript">
function getEcharts(){
    // Step:3 conifg ECharts's path, link to echarts.js from current page.
    // Step:3 为模块加载器配置echarts的路径，从当前页面链接到echarts.js，定义所需图表路径
    require.config({
        paths: {
            echarts: './mjs'
        }
    });
    
    // Step:4 require echarts and use it in the callback.
    // Step:4 动态加载echarts然后在回调函数中开始使用，注意保持按需加载结构定义图表路径
    require(
        [
            'echarts',
            'echarts/chart/map'
        ],
        function (ec) {
            // --- 地图 ---
            var myChart2 = ec.init(document.getElementById('mainMap'));
            myChart2.setOption({
				dataRange: {
					min : 0,
					max : 100,
					calculable : true,
					color: ['#ff3333', 'orange', 'yellow','lime','aqua'],
					textStyle:{
						color:'#fff'
					}
				},
				series : [
					{
						name: '全国',
						type: 'map',
						roam: true,
						hoverable: false,
						mapType: 'china',
						itemStyle:{
							normal:{
								borderColor:'rgba(100,149,237,1)',
								borderWidth:0.5,
								areaStyle:{
									color: '#1b1b1b'
								}
							}
						},
						data:[],
						markLine : {
							smooth:true,
							symbol: ['none', 'circle'],  
							symbolSize : 1,
							itemStyle : {
								normal: {
									color:'#fff',
									borderWidth:1,
									borderColor:'rgba(30,144,255,0.5)'
								}
							},
							data : [
							],
						},
						geoCoord: {
							'上海': [121.4648,31.2891],
							'东莞': [113.8953,22.901],
							'东营': [118.7073,37.5513],
							'中山': [113.4229,22.478],
							'临汾': [111.4783,36.1615],
							'临沂': [118.3118,35.2936],
							'丹东': [124.541,40.4242],
							'丽水': [119.5642,28.1854],
							'乌鲁木齐': [87.9236,43.5883],
							'佛山': [112.8955,23.1097],
							'保定': [115.0488,39.0948],
							'兰州': [103.5901,36.3043],
							'包头': [110.3467,41.4899],
							'北京': [116.4551,40.2539],
							'北海': [109.314,21.6211],
							'南京': [118.8062,31.9208],
							'南宁': [108.479,23.1152],
							'南昌': [116.0046,28.6633],
							'南通': [121.1023,32.1625],
							'厦门': [118.1689,24.6478],
							'台州': [121.1353,28.6688],
							'合肥': [117.29,32.0581],
							'呼和浩特': [111.4124,40.4901],
							'咸阳': [108.4131,34.8706],
							'哈尔滨': [127.9688,45.368],
							'唐山': [118.4766,39.6826],
							'嘉兴': [120.9155,30.6354],
							'大同': [113.7854,39.8035],
							'大连': [122.2229,39.4409],
							'天津': [117.4219,39.4189],
							'太原': [112.3352,37.9413],
							'威海': [121.9482,37.1393],
							'宁波': [121.5967,29.6466],
							'宝鸡': [107.1826,34.3433],
							'宿迁': [118.5535,33.7775],
							'常州': [119.4543,31.5582],
							'广州': [113.5107,23.2196],
							'廊坊': [116.521,39.0509],
							'延安': [109.1052,36.4252],
							'张家口': [115.1477,40.8527],
							'徐州': [117.5208,34.3268],
							'德州': [116.6858,37.2107],
							'惠州': [114.6204,23.1647],
							'成都': [103.9526,30.7617],
							'扬州': [119.4653,32.8162],
							'承德': [117.5757,41.4075],
							'拉萨': [91.1865,30.1465],
							'无锡': [120.3442,31.5527],
							'日照': [119.2786,35.5023],
							'昆明': [102.9199,25.4663],
							'杭州': [119.5313,29.8773],
							'枣庄': [117.323,34.8926],
							'柳州': [109.3799,24.9774],
							'株洲': [113.5327,27.0319],
							'武汉': [114.3896,30.6628],
							'汕头': [117.1692,23.3405],
							'江门': [112.6318,22.1484],
							'沈阳': [123.1238,42.1216],
							'沧州': [116.8286,38.2104],
							'河源': [114.917,23.9722],
							'泉州': [118.3228,25.1147],
							'泰安': [117.0264,36.0516],
							'泰州': [120.0586,32.5525],
							'济南': [117.1582,36.8701],
							'济宁': [116.8286,35.3375],
							'海口': [110.3893,19.8516],
							'淄博': [118.0371,36.6064],
							'淮安': [118.927,33.4039],
							'深圳': [114.5435,22.5439],
							'清远': [112.9175,24.3292],
							'温州': [120.498,27.8119],
							'渭南': [109.7864,35.0299],
							'湖州': [119.8608,30.7782],
							'湘潭': [112.5439,27.7075],
							'滨州': [117.8174,37.4963],
							'潍坊': [119.0918,36.524],
							'烟台': [120.7397,37.5128],
							'玉溪': [101.9312,23.8898],
							'珠海': [113.7305,22.1155],
							'盐城': [120.2234,33.5577],
							'盘锦': [121.9482,41.0449],
							'石家庄': [114.4995,38.1006],
							'福州': [119.4543,25.9222],
							'秦皇岛': [119.2126,40.0232],
							'绍兴': [120.564,29.7565],
							'聊城': [115.9167,36.4032],
							'肇庆': [112.1265,23.5822],
							'舟山': [122.2559,30.2234],
							'苏州': [120.6519,31.3989],
							'莱芜': [117.6526,36.2714],
							'菏泽': [115.6201,35.2057],
							'营口': [122.4316,40.4297],
							'葫芦岛': [120.1575,40.578],
							'衡水': [115.8838,37.7161],
							'衢州': [118.6853,28.8666],
							'西宁': [101.4038,36.8207],
							'西安': [109.1162,34.2004],
							'贵阳': [106.6992,26.7682],
							'连云港': [119.1248,34.552],
							'邢台': [114.8071,37.2821],
							'邯郸': [114.4775,36.535],
							'郑州': [113.4668,34.6234],
							'鄂尔多斯': [108.9734,39.2487],
							'重庆': [107.7539,30.1904],
							'金华': [120.0037,29.1028],
							'铜川': [109.0393,35.1947],
							'银川': [106.3586,38.1775],
							'镇江': [119.4763,31.9702],
							'长春': [125.8154,44.2584],
							'长沙': [113.0823,28.2568],
							'长治': [112.8625,36.4746],
							'阳泉': [113.4778,38.0951],
							'青岛': [120.4651,36.3373],
							'韶关': [113.7964,24.7028]
						},
						markPoint : {
							symbol:'emptyCircle',
							symbolSize : function (v){
								return 10 + v/10
							},
							effect : {
								show: true,
								shadowBlur : 0
							},
							itemStyle:{
								normal:{
									label:{show:false}
								},
								emphasis: {
									label:{position:'top'}
								}
							},
							data : [
								{name:'上海',value:95},
								{name:'广州',value:90},
								{name:'大连',value:80},
								{name:'南宁',value:70},
								{name:'南昌',value:60},
								{name:'拉萨',value:50},
								{name:'长春',value:40},
								{name:'包头',value:30},
								{name:'重庆',value:20},
								{name:'常州',value:10}
							]
						}
					},
					{
						name: '北京 Top10',
						type: 'map',
						mapType: 'china',
						data:[],
						markLine : {
							smooth:true,
							effect : {
								show: true,
								scaleSize: 1,
								period: 30,
								color: '#fff',
								shadowBlur: 10
							},
							itemStyle : {
								normal: {
									label:{show:false},
									borderWidth:1,
									lineStyle: {
										type: 'solid',
										shadowBlur: 10
									}
								}
							},
							data : [
								[{name:'上海'}, {name:'北京',value:95}],
								[{name:'广州'}, {name:'北京',value:90}],
								[{name:'大连'}, {name:'北京',value:80}],
								[{name:'南宁'}, {name:'北京',value:70}],
								[{name:'南昌'}, {name:'北京',value:60}],
								[{name:'拉萨'}, {name:'北京',value:50}],
								[{name:'长春'}, {name:'北京',value:40}],
								[{name:'包头'}, {name:'北京',value:30}],
								[{name:'重庆'}, {name:'北京',value:20}],
								[{name:'常州'}, {name:'北京',value:10}]
							]
						},
						markPoint : {
							symbol:'emptyCircle',
							symbolSize : function (v){
								return 0.1
							},
							effect : {
								show: false,
								shadowBlur : 0
							},
							itemStyle:{
								normal:{
									label:{show:true,
										  position:'top',
										  textStyle: {
													fontSize: 14
												}
										  }
								},
								emphasis: {
									label:{show:false}
								}
							},
							data : [
								{name:'上海',value:95},
								{name:'广州',value:90},
								{name:'大连',value:80},
								{name:'南宁',value:70},
								{name:'南昌',value:60},
								{name:'拉萨',value:50},
								{name:'长春',value:40},
								{name:'包头',value:30},
								{name:'重庆',value:20},
								{name:'常州',value:10}
							]
						}
					}
				]
        });
	});
}
    </script>

<style type="text/css">
<!--
* {font-family: "Microsoft Yahei",Tahoma, Arial; }
body{text-align: center; margin: 0 auto; padding: 0; background-color:#fafafa;font-size:12px;font-family:Tahoma, Arial}
h1 {font-size: 26px; padding: 0; margin: 0; color: #333333; font-family: "Lucida Sans Unicode","Lucida Grande",sans-serif;}
h1 small {font-size: 11px; font-family: Tahoma; font-weight: bold; }
a{color: #666; text-decoration:none;}
a.black{color: #000000; text-decoration:none;}
table{width:100%;clear:both;padding: 0; margin: 0 0 10px;border-collapse:collapse; border-spacing: 0;
box-shadow: 1px 1px 1px #CCC;
-moz-box-shadow: 1px 1px 1px #CCC;
-webkit-box-shadow: 1px 1px 1px #CCC;
-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=2, Direction=135, Color='#CCCCCC')";}
th{padding: 3px 6px; font-weight:bold;background:#dedede;color:#626262;border:1px solid #cccccc; text-align:left;}
tr{padding: 0; background:#FFFFFF;}
td{padding: 3px 6px; border:1px solid #CCCCCC;}
.w_logo{height:25px;text-align:center;color:#333;FONT-SIZE: 15px; width:13%; }
.w_top{height:25px;text-align:center; width:8.7%;}
.w_top:hover{background:#dadada;}
.w_foot{height:25px;text-align:center; background:#dedede;}
input{padding: 2px; background: #FFFFFF; border-top:1px solid #666666; border-left:1px solid #666666; border-right:1px solid #CCCCCC; border-bottom:1px solid #CCCCCC; font-size:12px}
input.btn{font-weight: bold; height: 20px; line-height: 20px; padding: 0 6px; color:#666666; background: #f2f2f2; border:1px solid #999;font-size:12px}
.bar {border:1px solid #999999; background:#FFFFFF; height:5px; font-size:2px; width:89%; margin:2px 0 5px 0;padding:1px; overflow: hidden;}
.bar_1 {border:1px dotted #999999; background:#FFFFFF; height:5px; font-size:2px; width:89%; margin:2px 0 5px 0;padding:1px; overflow: hidden;}
.barli_red{background:#ff6600; height:5px; margin:0px; padding:0;}
.barli_blue{background:#0099FF; height:5px; margin:0px; padding:0;}
.barli_green{background:#36b52a; height:5px; margin:0px; padding:0;}
.barli_black{background:#333; height:5px; margin:0px; padding:0;}
.barli_1{background:#999999; height:5px; margin:0px; padding:0;}
.barli{background:#36b52a; height:5px; margin:0px; padding:0;}
#page {width: 960px; padding: 0 auto; margin: 0 auto; text-align: left;}
#header{position:relative; padding:5px;}
.w_small{font-family: Courier New;}
.w_number{color: #f800fe;}
.sudu {padding: 0; background:#5dafd1; }
.suduk { margin:0px; padding:0;}
.resYes{}
.resNo{color: #FF0000;}
.word{word-break:break-all;}
* {
  margin: 0;
  padding: 0;
}


h1 {
  font-size: 20px;
  color: #424242;
}

p {
  line-height:200%;
}

svg {
  width: 100%;
  height: 100%;
}

.napolin {
  max-width:550px;
  text-align:center;
  margin:120px auto auto auto;
}

.napolin a {
  color: #535353;
  text-decoration: none;
}

.napolin a:link,
.napolin a:visited {
  color: #535353;
  text-decoration: none;
}

.avatar {
  margin-bottom: 35px;
}

.colortext{
  font-size: 6em;
}

.text-copy {
  fill: none;
  stroke: white;
  stroke-dasharray: 6% 29%;
  stroke-width: 5;
  stroke-dashoffset: 0%;
  -webkit-animation: stroke-offset 5s infinite linear;
          animation: stroke-offset 5s infinite linear;
}
.text-copy:nth-child(1) {
  stroke: #4CAF50;
  -webkit-animation-delay: -1s;
          animation-delay: -1s;
}
.text-copy:nth-child(2) {
  stroke: #F44336;
  -webkit-animation-delay: -2s;
          animation-delay: -2s;
}
.text-copy:nth-child(3) {
  stroke: #03A9F4;
  -webkit-animation-delay: -3s;
          animation-delay: -3s;
}
.text-copy:nth-child(4) {
  stroke: #FF9800;
  -webkit-animation-delay: -4s;
          animation-delay: -4s;
}
.text-copy:nth-child(5) {
  stroke: #9C27B0;
  -webkit-animation-delay: -5s;
          animation-delay: -5s;
}

@media screen and (max-width: 800px){
    .napolin{
        margin:auto;
    }
}

@-webkit-keyframes stroke-offset {
  100% {
    stroke-dashoffset: -35%;
  }
}

@keyframes stroke-offset {
  100% {
    stroke-dashoffset: -35%;
  }
}

.linkEffect a::before,
.linkEffect a::after {
  display: inline-block;
  opacity: 0;
  -webkit-transition: -webkit-transform 0.3s, opacity 0.2s;
  -moz-transition: -moz-transform 0.3s, opacity 0.2s;
  transition: transform 0.3s, opacity 0.2s;
}

.linkEffect a::before {
  margin-right: 10px;
  content: '[';
  -webkit-transform: translateX(20px);
  -moz-transform: translateX(20px);
  transform: translateX(20px);
}

.linkEffect a::after {
  margin-left: 10px;
  content: ']';
  -webkit-transform: translateX(-20px);
  -moz-transform: translateX(-20px);
  transform: translateX(-20px);
}

.linkEffect a:hover::before,
.linkEffect a:hover::after,
.linkEffect a:focus::before,
.linkEffect a:focus::after {
  opacity: 1;
  -webkit-transform: translateX(0px);
  -moz-transform: translateX(0px);
  transform: translateX(0px);
}


.fadein {
      animation: fade-in;/*动画名称*/  
      animation-duration: 5s;/*动画持续时间*/  
      -webkit-animation:fade-in 5s;/*针对webkit内核*/  
    } 

-->
</style>
<script language="JavaScript" type="text/javascript" src="http://lib.sinaapp.com/js/jquery/1.7/jquery.min.js"></script>
<script type="text/javascript"> 
<!--
$(document).ready(function(){getJSONData();});
var OutSpeed2=<?php echo floor($NetOutSpeed[2]) ?>;
var OutSpeed3=<?php echo floor($NetOutSpeed[3]) ?>;
var OutSpeed4=<?php echo floor($NetOutSpeed[4]) ?>;
var OutSpeed5=<?php echo floor($NetOutSpeed[5]) ?>;
var InputSpeed2=<?php echo floor($NetInputSpeed[2]) ?>;
var InputSpeed3=<?php echo floor($NetInputSpeed[3]) ?>;
var InputSpeed4=<?php echo floor($NetInputSpeed[4]) ?>;
var InputSpeed5=<?php echo floor($NetInputSpeed[5]) ?>;
function getJSONData()
{
	setTimeout("getJSONData()", 1000);
	$.getJSON('?act=rt&callback=?', displayData);
}
function ForDight(Dight,How)
{ 
  if (Dight<0){
  	var Last=0+"B/s";
  }else if (Dight<1024){
  	var Last=Math.round(Dight*Math.pow(10,How))/Math.pow(10,How)+"B/s";
  }else if (Dight<1048576){
  	Dight=Dight/1024;
  	var Last=Math.round(Dight*Math.pow(10,How))/Math.pow(10,How)+"K/s";
  }else{
  	Dight=Dight/1048576;
  	var Last=Math.round(Dight*Math.pow(10,How))/Math.pow(10,How)+"M/s";
  }
	return Last; 
}
function displayData(dataJSON)
{
	$("#useSpace").html(dataJSON.useSpace);
	$("#freeSpace").html(dataJSON.freeSpace);
	$("#hdPercent").html(dataJSON.hdPercent);
	$("#barhdPercent").width(dataJSON.barhdPercent);
	$("#TotalMemory").html(dataJSON.TotalMemory);
	$("#UsedMemory").html(dataJSON.UsedMemory);
	$("#FreeMemory").html(dataJSON.FreeMemory);
	$("#CachedMemory").html(dataJSON.CachedMemory);
	$("#Buffers").html(dataJSON.Buffers);
	$("#TotalSwap").html(dataJSON.TotalSwap);
	$("#swapUsed").html(dataJSON.swapUsed);
	$("#swapFree").html(dataJSON.swapFree);
	$("#swapPercent").html(dataJSON.swapPercent);
	$("#loadAvg").html(dataJSON.loadAvg);
	$("#uptime").html(dataJSON.uptime);
	$("#freetime").html(dataJSON.freetime);
	$("#stime").html(dataJSON.stime);
	$("#bjtime").html(dataJSON.bjtime);
	$("#memRealUsed").html(dataJSON.memRealUsed);
	$("#memRealFree").html(dataJSON.memRealFree);
	$("#memRealPercent").html(dataJSON.memRealPercent);
	$("#memPercent").html(dataJSON.memPercent);
	$("#barmemPercent").width(dataJSON.memPercent);
	$("#barmemRealPercent").width(dataJSON.barmemRealPercent);
	$("#memCachedPercent").html(dataJSON.memCachedPercent);
	$("#barmemCachedPercent").width(dataJSON.barmemCachedPercent);
	$("#barswapPercent").width(dataJSON.barswapPercent);
	$("#NetOut2").html(dataJSON.NetOut2);
	$("#NetOut3").html(dataJSON.NetOut3);
	$("#NetOut4").html(dataJSON.NetOut4);
	$("#NetOut5").html(dataJSON.NetOut5);
	$("#NetOut6").html(dataJSON.NetOut6);
	$("#NetOut7").html(dataJSON.NetOut7);
	$("#NetOut8").html(dataJSON.NetOut8);
	$("#NetOut9").html(dataJSON.NetOut9);
	$("#NetOut10").html(dataJSON.NetOut10);
	$("#NetInput2").html(dataJSON.NetInput2);
	$("#NetInput3").html(dataJSON.NetInput3);
	$("#NetInput4").html(dataJSON.NetInput4);
	$("#NetInput5").html(dataJSON.NetInput5);
	$("#NetInput6").html(dataJSON.NetInput6);
	$("#NetInput7").html(dataJSON.NetInput7);
	$("#NetInput8").html(dataJSON.NetInput8);
	$("#NetInput9").html(dataJSON.NetInput9);
	$("#NetInput10").html(dataJSON.NetInput10);	
	$("#NetOutSpeed2").html(ForDight((dataJSON.NetOutSpeed2-OutSpeed2),3));	OutSpeed2=dataJSON.NetOutSpeed2;
	$("#NetOutSpeed3").html(ForDight((dataJSON.NetOutSpeed3-OutSpeed3),3));	OutSpeed3=dataJSON.NetOutSpeed3;
	$("#NetOutSpeed4").html(ForDight((dataJSON.NetOutSpeed4-OutSpeed4),3));	OutSpeed4=dataJSON.NetOutSpeed4;
	$("#NetOutSpeed5").html(ForDight((dataJSON.NetOutSpeed5-OutSpeed5),3));	OutSpeed5=dataJSON.NetOutSpeed5;
	$("#NetInputSpeed2").html(ForDight((dataJSON.NetInputSpeed2-InputSpeed2),3));	InputSpeed2=dataJSON.NetInputSpeed2;
	$("#NetInputSpeed3").html(ForDight((dataJSON.NetInputSpeed3-InputSpeed3),3));	InputSpeed3=dataJSON.NetInputSpeed3;
	$("#NetInputSpeed4").html(ForDight((dataJSON.NetInputSpeed4-InputSpeed4),3));	InputSpeed4=dataJSON.NetInputSpeed4;
	$("#NetInputSpeed5").html(ForDight((dataJSON.NetInputSpeed5-InputSpeed5),3));	InputSpeed5=dataJSON.NetInputSpeed5;
}
-->
</script>
</head>
<body>
<a name="w_top"></a>
<div id="page">
	



<!--服务器相关参数-->
<table>
  <tr><th colspan="4">服务器参数</th></tr>
  <tr>
    <td>服务器域名/IP地址</td>
    <td colspan="3"><?php echo @get_current_user();?> - <?php echo $_SERVER['SERVER_NAME'];?>(<?php if('/'==DIRECTORY_SEPARATOR){echo $_SERVER['SERVER_ADDR'];}else{echo @gethostbyname($_SERVER['SERVER_NAME']);} ?>)&nbsp;&nbsp;你的IP地址是：<?php echo @$_SERVER['REMOTE_ADDR'];?></td>
  </tr>
  <tr>
    <td>服务器标识</td>
    <td colspan="3"><?php if($sysInfo['win_n'] != ''){echo $sysInfo['win_n'];}else{echo @php_uname();};?></td>
  </tr>
  <tr>
    <td width="13%">服务器操作系统</td>
    <td width="37%"><?php $os = explode(" ", php_uname()); echo $os[0];?> &nbsp;内核版本：<?php if('/'==DIRECTORY_SEPARATOR){echo $os[2];}else{echo $os[1];} ?></td>
    <td width="13%">服务器解译引擎</td>
    <td width="37%"><?php echo $_SERVER['SERVER_SOFTWARE'];?></td>
  </tr>
  <tr>
    <td>服务器语言</td>
    <td><?php echo getenv("HTTP_ACCEPT_LANGUAGE");?></td>
    <td>服务器端口</td>
    <td><?php echo $_SERVER['SERVER_PORT'];?></td>
  </tr>
  <tr>
	  <td>服务器主机名</td>
	  <td><?php if('/'==DIRECTORY_SEPARATOR ){echo $os[1];}else{echo $os[2];} ?></td>
	  <td>绝对路径</td>
	  <td><?php echo $_SERVER['DOCUMENT_ROOT']?str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']):str_replace('\\','/',dirname(__FILE__));?></td>
	</tr>
  <tr>
	  <td>管理员邮箱</td>
	  <td><?php echo $_SERVER['SERVER_ADMIN'];?></td>
		<td>探针路径</td>
		<td><?php echo str_replace('\\','/',__FILE__)?str_replace('\\','/',__FILE__):$_SERVER['SCRIPT_FILENAME'];?></td>
	</tr>	
</table>

<?if("show"==$sysReShow){?>
<table>
  <tr><th colspan="6">服务器实时数据</th></tr>
  <tr>
    <td width="13%" >服务器当前时间</td>
    <td width="37%" ><span id="stime"><?php echo $stime;?></span></td>
    <td width="13%" >服务器已运行时间</td>
    <td width="37%" colspan="3"><span id="uptime"><?php echo $uptime;?></span></td>
  </tr>
  <tr>
    <td width="13%">CPU型号 [<?php echo $sysInfo['cpu']['num'];?>核]</td>
    <td width="87%" colspan="5"><?php echo $sysInfo['cpu']['model'];?></td>
  </tr>
  <tr>
    <td>CPU使用状况</td>
    <td colspan="5"><?php if('/'==DIRECTORY_SEPARATOR){echo $cpu_show." | ";}else{echo "暂时只支持Linux系统";}?>
	</td>
  </tr>
  <tr>
    <td>硬盘使用状况</td>
    <td colspan="5">
		总空间 <?php echo $dt;?>&nbsp;G，
		已用 <font color='#333333'><span id="useSpace"><?php echo $du;?></span></font>&nbsp;G，
		空闲 <font color='#333333'><span id="freeSpace"><?php echo $df;?></span></font>&nbsp;G，
		使用率 <span id="hdPercent"><?php echo $hdPercent;?></span>%
		<div class="bar"><div id="barhdPercent" class="barli_black" style="width:<?php echo $hdPercent;?>%" >&nbsp;</div> </div>
	</td>
  </tr>
  <tr>
		<td>内存使用状况</td>
		<td colspan="5">
<?php
$tmp = array(
    'memTotal', 'memUsed', 'memFree', 'memPercent',
    'memCached', 'memRealPercent',
    'swapTotal', 'swapUsed', 'swapFree', 'swapPercent'
);
foreach ($tmp AS $v) {
    $sysInfo[$v] = $sysInfo[$v] ? $sysInfo[$v] : 0;
}
?>
          物理内存：共
          <font color='#CC0000'><?php echo $memTotal;?> </font>
           , 已用
          <font color='#CC0000'><span id="UsedMemory"><?php echo $mu;?></span></font>
          , 空闲
          <font color='#CC0000'><span id="FreeMemory"><?php echo $mf;?></span></font>
          , 使用率
		  <span id="memPercent"><?php echo $memPercent;?></span>
          <div class="bar"><div id="barmemPercent" class="barli_green" style="width:<?php echo $memPercent?>%" >&nbsp;</div> </div>
<?php
//判断如果cache为0，不显示
if($sysInfo['memCached']>0)
{
?>		
		  Cache化内存为 <span id="CachedMemory"><?php echo $mc;?></span>
		  , 使用率 
          <span id="memCachedPercent"><?php echo $memCachedPercent;?></span>
		  %	| Buffers缓冲为  <span id="Buffers"><?php echo $mb;?></span>
          <div class="bar"><div id="barmemCachedPercent" class="barli_blue" style="width:<?php echo $memCachedPercent?>%" >&nbsp;</div></div>

          真实内存使用
          <span id="memRealUsed"><?php echo $memRealUsed;?></span>
		  , 真实内存空闲
          <span id="memRealFree"><?php echo $memRealFree;?></span>
		  , 使用率
          <span id="memRealPercent"><?php echo $memRealPercent;?></span>
          %
          <div class="bar_1"><div id="barmemRealPercent" class="barli_1" style="width:<?php echo $memRealPercent?>%" >&nbsp;</div></div> 
<?php
}
//判断如果SWAP区为0，不显示
if($sysInfo['swapTotal']>0)
{
?>	
          SWAP区：共
          <?php echo $st;?>
          , 已使用
          <span id="swapUsed"><?php echo $su;?></span>
          , 空闲
          <span id="swapFree"><?php echo $sf;?></span>
          , 使用率
          <span id="swapPercent"><?php echo $swapPercent;?></span>
          %
          <div class="bar"><div id="barswapPercent" class="barli_red" style="width:<?php echo $swapPercent?>%" >&nbsp;</div> </div>

<?php
}	
?>		  
	  </td>
	</tr>
	  <tr>
		<td>系统平均负载</td>
		<td colspan="5" class="w_number"><span id="loadAvg"><?php echo $load;?></span></td>
	</tr>
</table>
<?}?>

<?php if (false !== ($strs = @file("/proc/net/dev"))) : ?>
<table>
    <tr><th colspan="5">网络使用状况</th></tr>
<?php for ($i = 2; $i < count($strs); $i++ ) : ?>
<?php preg_match_all( "/([^\s]+):[\s]{0,}(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/", $strs[$i], $info );?>
     <tr>
        <td width="13%"><?php echo $info[1][0]?> : </td>
        <td width="29%">入网: <font color='#CC0000'><span id="NetInput<?php echo $i?>"><?php echo $NetInput[$i]?></span></font></td>
		<td width="14%">实时: <font color='#CC0000'><span id="NetInputSpeed<?php echo $i?>">0B/s</span></font></td>
        <td width="29%">出网: <font color='#CC0000'><span id="NetOut<?php echo $i?>"><?php echo $NetOut[$i]?></span></font></td>
		<td width="14%">实时: <font color='#CC0000'><span id="NetOutSpeed<?php echo $i?>">0B/s</span></font></td>
    </tr>
<?php endfor; ?>
</table>
<?php endif; ?>

</table>
</form>
<div align="center"><div id="mainMap" style="height:400px;width: 700px;padding:10px;background:#1B1B1B">
</div></div>

  <h1>

    <div class="colortext"> 
      <svg viewBox="0 0 2000 300">
        <symbol id="s-text">
          <text text-anchor="middle" x="50%" y="60%" class="text--line">
hostloc.com          </text>
        </symbol>
        
        <g class="g-ants">
          <use xlink:href="#s-text" class="text-copy"></use>     
          <use xlink:href="#s-text" class="text-copy"></use>     
          <use xlink:href="#s-text" class="text-copy"></use>     
          <use xlink:href="#s-text" class="text-copy"></use>     
          <use xlink:href="#s-text" class="text-copy"></use>     
        </g>  
        </svg>
    </div>
  </h1>
	

</div>
</body>
</html>
