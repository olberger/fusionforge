<?php
/**
 * FusionForge Installation
 *
 * Copyright 2010, Roland Mas
 * Copyright 2011, Alain Peyrat
 * http://fusionforge.org/
 */

if (posix_isatty(STDOUT) ) {
	define ('GREEN', "\033[01;32m");
	define ('NORMAL', "\033[00m");
	define ('RED', "\033[01;31m");
} else {
	define ('GREEN', '' );
	define ('NORMAL', '');
	define ('RED', '');
}

$fusionforge_etc_dir = getenv('FUSIONFORGE_ETC_DIR');
if (empty($fusionforge_etc_dir))
{
	$fusionforge_etc_dir = '/etc/gforge';
}

$fusionforge_src_dir = getenv('FUSIONFORGE_SRC_DIR');
if (empty($fusionforge_src_dir))
{
	$fusionforge_src_dir = '/opt/gforge';
}

$fusionforge_data_dir = getenv('FUSIONFORGE_DATA_DIR');
if (empty($fusionforge_data_dir))
{
	$fusionforge_data_dir = '/var/lib/gforge';
}

$fusionforge_log = getenv('FUSIONFORGE_LOG');

function show($text, $newLine = true) {
	global $STDOUT, $fusionforge_log;

	if ($fusionforge_log) {
		$hd = fopen ($fusionforge_log, 'a+');
		fwrite($hd, "*** $text\n");
		fclose($hd);
	}

	if ($newLine) {
		$text = GREEN.$text .NORMAL."\n";
	}
	fwrite($STDOUT, $text);
}

function mkdir_safe($dir, $mode=0777) {
	if (!mkdir ($dir, $mode, true)) {
		echo "ERROR: mkdir $dir failed.\n";
		exit(2);
	}
}

function symlink_safe($dest, $source) {
	if (!is_dir($source)) {
		if (!symlink($dest, $source)) {
			echo "ERROR: symlink $source => $dest failed.\n";
			exit(2);
		}
	}
}

function run_safe($command, $ignore = false) {
	global $fusionforge_log;

	$cmd = $command;
	if ($fusionforge_log) {
		$hd = fopen ($fusionforge_log, 'a+');
		fwrite($hd, "CMD $command\n");
		if ($ignore) fwrite($hd, "# Command may fail without impact on install procedure.\n");
		fclose($hd);
		$cmd .= " >>$fusionforge_log 2>&1";
	}

	system($cmd, $ret);
	if ($ignore) {
		if ($ret != 0) {
			return false;
		} else {
			return true;
		}
	} else {
		if ($ret != 0) {
			echo "ERROR: $command failed.\n";
			exit(2);
		}
	}
}
