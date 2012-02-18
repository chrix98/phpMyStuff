<?php
/**
 * Filename:	log.php
 * Filetype:	class/controller/model/view/
 * Date:	$Date:  $
 * @author:	$Author:  $
 * Revision:	$Rev:  $
 * SVN URL:	$Url:  $
 *	@author	Chris Petermann	chris@petermannlive.com
 *	@purpose	display the log file of the current day for debugging purpose.
 *	@param	boolean	truncate	whether or not to truncate todays file
 *	@param	string	filter	filters file for lines containing <string>
 * ==============================================
 * Description:
 * allows to view the log files
 **/


//	the output array containing lines of output per item
$fileoutput = array();
//define("LOG_FILE_REGEX", "/(?<year>[0-9]{4})-(?<month>[0-9]{2})-(?<day>[0-9]{2})\.[0-9]+/");	//	this one is for when the posix pid is appended
//define("LOG_FILE_REGEX", "/(?<year>[0-9]{4})\/(?<month>[0-9]{2})\/(?<day>[0-9]{2})/");
define("LOG_FILE_REGEX", "/.php/");



class Controller_Log extends Controller {

	function __construct()
	{
		$this->debug	=	"style=\"color: green\"";
		$this->info		=	"style=\"color: gray\"";
		$this->error	=	"style=\"color: red\"";
		$this->lnno		=	"style=\"background-color: lightgray\"";
		$this->hghlght	=	"style=\"background-color:	yellow;font-weight:bold;text-transform: uppercase;\"";

		$this->g_filter 	= isset($_GET['filter']) 	? trim(urldecode($_GET['filter'])) : '';
		$this->g_truncate 	= isset($_GET['truncate']) 	? trim(urldecode($_GET['truncate'])) : '';
		$this->g_goto		= isset($_GET['goto']) 		? trim(urldecode($_GET['goto'])) : '';
		$this->g_delete		= isset($_GET['delete']) 	? trim(urldecode($_GET['delete'])) : '';

		//$this->logfilename	= APPPATH."logs/".date("Y")."/".date("m")."/".date("d").".php";
		if(isset($_GET['logfile']))	{
			$this->logfilename = APPPATH.$_GET['logfile'];
		}else{
			$this->logfilename = '';
		}

		$this->logdir		= APPPATH."logs/";

		$this->disallowed_files = array(".", "..", "index.php");

		$this->log = LOG::instance();

//		$this->log->add(LOG::INFO, " -- Starting");

		if($this->g_truncate=='1')
		{
			if($fh = fopen($this->logfilename, 'w+'))	{
				fwrite($fh, "");
				fclose($fh);
			}
			exit("<script>window.location.href = document.referrer</script>");
		}

		if($this->g_delete=='1')
		{
			if(file_exists($this->logfilename) && is_writable($this->logfilename))	{
				unlink($this->logfilename);
			}
			header("Location: ?");
		}
		elseif($this->g_delete=='2')
		{
			$logfiles = _read_log_files();
			if(is_array($logfiles) && count($logfiles))
			{
				foreach($logfiles as $file)
				{
					unlink($file);
				}

			}
			header("Location: ?");
		}
	}

	function action_index()
	{
		$this->log->add(LOG::INFO, __METHOD__.": entered");

		LOG::instance()->add(LOG::DEBUG, __METHOD__.": logfile: ".var_export($this->logfilename,1));

		if(file_exists($this->logfilename) && is_readable($this->logfilename))	{
			$this->log->add(LOG::INFO, __METHOD__.": we use log file" );
			$file = file_get_contents($this->logfilename);
			$fileoutput = $this->_parse_file($file);
		}else{
			$this->log->add(LOG::DEBUG, __METHOD__.": listing files" );
			$fileoutput = $this->_list_files();
			$this->log->add(LOG::DEBUG, __METHOD__.": ...".var_export($fileoutput,1) );
		}

		$this->show($fileoutput);
	}

	function _parse_file($file)
	{
		$filelines = explode("\n", $file);
		$fileoutput = array();
		//var_dump($filelines);

		foreach($filelines as $linenumber=>$line)	{
			if(!empty($this->g_filter)) {
				if(stripos(" ".$line, urldecode($this->g_filter))!==false	)	{

					switch(substr($line,0,strpos($line," - "))) {
						case 'DEBUG':
							$fileoutput[] = "<span $this->lnno>".lpad($linenumber)."</span> <font $this->debug>".str_ireplace($this->g_filter, "<span $this->hghlght>".$this->g_filter."</span>", $line)."</font>";
						break;
						case 'ERROR':
							$fileoutput[] = "<span $this->lnno>".lpad($linenumber)."</span> <font $this->error>".str_ireplace($this->g_filter, "<span $this->hghlght>".$this->g_filter."</span>", $line)."</font>";
						break;
						case 'INFO ':
						default:
							$fileoutput[] = "<span $this->lnno>".lpad($linenumber)."</span> <font $this->info>".str_ireplace($this->g_filter, "<span $this->hghlght>".$this->g_filter."</span>", $line)."</font>";
						break;
					}
				}
			}else{
				switch(substr($line,0,strpos($line," - "))) {
					case 'DEBUG':
						$fileoutput[] = "<span $this->lnno>".lpad($linenumber)."</span> <font $this->debug>".$line."</font>";
					break;
					case 'ERROR':
						$fileoutput[] = "<span $this->lnno>".lpad($linenumber)."</span> <font $this->error>".$line."</font>";
					break;
					case 'INFO ':
					default:
						$fileoutput[] = "<span $this->lnno>".lpad($linenumber)."</span> <font $this->info>".$line."</font>";
					break;
				}
			}
		}

		return $fileoutput;
	}

	function _read_log_files($dir)
	{
		$this->log = LOG::instance();
		$this->log->add(LOG::INFO, __METHOD__.": entered with dir: :dir", array(':dir'=>$dir) );

		$ret = array();
		if( "/" != substr($dir, -1))
			$dir .= "/";

		if (is_dir($dir))	{
			if ($dh = opendir($dir))	{
				while ((($file = readdir($dh)) !== false)	) {
					LOG::instance()->add(LOG::DEBUG, __METHOD__.": found file: ".var_export($file,1));

					if(in_array($file, $this->disallowed_files))
						continue;

					if(preg_match(LOG_FILE_REGEX, $file) )	{
						LOG::instance()->add(LOG::DEBUG, __METHOD__.": file matches LOG_FILE_REGEX: ".var_export(LOG_FILE_REGEX,1));
						$ret[] = $dir.$file;

					}elseif(is_dir($dir.$file))	{
						LOG::instance()->add(LOG::DEBUG, __METHOD__.": file is a dir - reading contents ...");

						if($tmp = $this->_read_log_files($dir.$file))	{
							LOG::instance()->add(LOG::DEBUG, __METHOD__.": dirs subcontent: ".var_export($tmp,1));
							$ret = array_merge($ret, $tmp);
							unset($tmp);
						}
					}else
					{
						LOG::instance()->add(LOG::DEBUG, __METHOD__.": file not a dir and not matching regex ".$file);
					}
				}
				closedir($dh);

			}else{
				LOG::instance()->add(LOG::DEBUG, __METHOD__.": can not read directory: ".var_export($dir,1));
			}
		}else{
			//echo "no dir?";
			LOG::instance()->add(LOG::DEBUG, __METHOD__.": not a directory: ".var_export($dir,1));
		}

		if(count($ret)<1)
			$ret = false;

		LOG::instance()->add(LOG::DEBUG, __METHOD__.": finished, returning: ".var_export($ret,1));
		return $ret;
	}

	function _list_files()
	{
		$this->log->add(LOG::INFO, __METHOD__.": entered" );

		$logfiles = $this->_read_log_files($this->logdir);
		if(!$logfiles || !is_array($logfiles) || !count($logfiles))
		{
			LOG::instance()->add(LOG::DEBUG, __METHOD__.": No log files ? ".var_export($logfiles,1));
			return false;
		}

		foreach($logfiles as $file)
		{
			if(!preg_match("/".$this->g_filter."/", $file))
			{
				continue;
			}
			$ext = substr($file, strrpos($file,".")+1);
			preg_match(LOG_FILE_REGEX, $file, $matches);

//			$link 		= (($matches[0] && filesize($file)>0) ? ("<a href='?logfile=".$matches[0]."'>".$file."</a>") : ($file));
//			$view_link 	= (($matches[0] && filesize($file)>0) ? ("<a href='?logfile=".$matches[0]."'>View</a>") : ('View'));
//			$trunc_link	= (($matches[0] && filesize($file)>0) ? ("<a href='?truncate=1&logfile=".$matches[0]."'>Truncate</a>") : ('Truncate'));
//			$delet_link	= (($matches[0] && is_writable($file)) ? ("<a href='?delete=1&logfile=".$matches[0]."'>Delete</a>") : ('Delete'));

			$fileParm = str_replace(APPPATH, "", $file);

			$link 		= (($file && filesize($file)>0) ? ("<a href='?logfile=".$fileParm."'>".$fileParm."</a>") : ($file));
			$view_link 	= (($file && filesize($file)>0) ? ("<a href='?logfile=".$fileParm."'>View</a>") : ('View'));
			$trunc_link	= (($file && filesize($file)>0) ? ("<a href='?truncate=1&logfile=".$fileParm."'>Truncate</a>") : ('Truncate'));
			$delet_link	= (($file && is_writable($file)) ? ("<a href='?delete=1&logfile=".$fileParm."'>Delete</a>") : ('Delete'));

			$fileoutput[] = "<img src='/icons/".$ext.".gif' width='20' height='20' >&nbsp;".
				$link . rpad(" "," ", 50-strlen($file)).
				rpad(formatBytes(filesize($file),0)," ", 24).
				rpad(date('Y-m-d H:i:s', filemtime($file)), " ", 32).
				$trunc_link."&nbsp;".$delet_link."&nbsp;".$view_link;
		}

		$header = "<img src='/icons/portal.png' width=20 height=20>&nbsp;";
		$header.= "<b>".rpad("Filename", " ", 50)."</b>";
		$header.= "<b>".rpad("Filesize", " ", 24)."</b>";
		$header.= "<b>".rpad("last modified", " ", 32)."</b>";
		$header.= "<b>".rpad("Action", " ", 60)."</b>";

		$toprow[] = $header;

		return array_merge($toprow,$fileoutput);
	}

	function show($fileoutput)
	{
	?>
	<html><head><TITLE>Log File viewer:</TITLE>
	<style>
	#filter	{
		border:0px solid darkgray;
		margin: 2px;
		position: fixed;
		width: 98%;
		height: 50px;
		background-color: ghostwhite;
		filter:			alpha(opacity=80);
		-moz-opacity:	0.8;
		-khtml-opacity:	0.8;
		opacity: 0.8;

	}
	fieldset, input	{
		font-family:	courier,system,terminal;
		font-size:	11px;
		color:	black;
	}
	fieldset	{
		background-color: white;
	/*	filter:			alpha(opacity=70);
		-moz-opacity:	0.7;
		-khtml-opacity:	0.7;
		opacity: 0.7;*/

	}
	</style>
	</head>
	<body>
	<table id="filter"><form id="formfilter" action="" method="GET">
				<? echo ($this->logfilename) ? ("<input type=\"hidden\" name=\"logfile\" value=\"".$this->logfilename."\" >") : ('') ?>
		<TR><TD>
				<fieldset><legend><a href="?">All File(s)</a></legend>
				<?php
				if($this->logfilename && file_exists($this->logfilename))	{
				?>
					Current File <input type=text disabled=true value="<?= $this->logfilename ?>" ><b>size:</b><?= formatBytes((int)@filesize($this->logfilename)) ?>	<b>modified:</b
					><input type="button" accesskey="d" type="button" value="<?= date("H:i:s",@filemtime($this->logfilename)) ?>" onclick="window.location.href=('?filter='+this.value)"
					><input accesskey="t" type="button" name="truncate" value="Truncate" onclick="window.location.href=('?truncate=1&logfile=<?= $this->logfilename ?>');"
					><input accesskey="e" type="button" name="delete" value="Delete" onclick="window.location.href=('?delete=1&logfile=<?= $this->logfilename ?>');"
					>
				<?php
				}else{
					//echo "<span style=\"line-height: 23px; width: 800px;\">select a file from below (if any) by clicking the file name</span>";
				?>
					<span style="line-height: 23px; width: 800px;">
					<input type="button" accesskey="d" value="Delete" onclick="window.location.href=('?delete=2&logfile=')" />
					<input type="button" accesskey="z" value="Zip" onclick="window.location.href=('?zip=2&logfile=')" />
					</span>
				<?php
					}
				?>
				</fieldset>
			</TD>
			<TD>
				<fieldset><legend>Filter</legend>
				<b>Search:</b
				><input accesskey="f" type="text" name="filter" value="<?= $this->g_filter ?>"
				><input type="submit" value="Apply"
				><input accesskey="c" type="button" value="Clear" onclick="window.location.href=('?logfile=<?= $this->logfilename ?>');"
				>
				</fieldset>
			</TD>
			<TD>
	<!--			<fieldset><legend>Navigation</legend>
				<b>Go to:</b
				><input accesskey="g" type="text" name="goto" value="<?= $this->g_goto ?>"
				><input type="submit" value="Go"
				>&nbsp;<input type="button" name="gototop" onclick="document.body.scrollTop" value="Top" disabled="true"
				><input type="button" name="gotobottom" onclick="document.body.scrollTop" value="Bottom" disabled=true>
				</fieldset>
	//-->
			</TD></form>
		</TR>
	</table>

	<?
	if(is_array($fileoutput) && count($fileoutput)>1)	{
		echo "<br><br><br><br><pre>".implode("\n", $fileoutput)."</pre>";
		echo "<hr>";
	}else{
		echo "<br><br><br><br><hr>";
		echo "<pre>There are currently no accessible files in the log directory.</pre>";
		echo "<hr>";
	}

	?>
	</body>
	</html>

	<?
	}

}

//	LITTLE HANDLER TO DO LEFT PADDING FOR THE LINE NUMBER
function	lpad($subject, $char=" ", $maxsize=4)	{
	while(strlen($subject)<$maxsize)
		$subject = $char.$subject;

	return $subject;
}

function	rpad($subject, $char=" ", $maxsize=4)	{
	while(strlen($subject)<$maxsize)
		$subject = $subject.$char;

	return $subject;
}

function formatBytes($bytes, $precision = 2) {
	$units = array('B', 'KB', 'MB', 'GB', 'TB');

	$bytes = max($bytes, 0);
	$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	$pow = min($pow, count($units) - 1);

	$bytes /= pow(1024, $pow);

	return round($bytes, $precision) . ' ' . $units[$pow];
}






