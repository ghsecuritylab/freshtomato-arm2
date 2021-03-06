--TEST--
Phar: fopen a .phar for writing (new file)
--SKIPIF--
<?php
if (!extension_loaded("phar")) die("skip");
?>
--INI--
phar.readonly=1
phar.require_hash=0
--FILE--
<?php
$fname = dirname(__FILE__) . '/' . basename(__FILE__, '.php') . '.phar.php';
$pname = 'phar://' . $fname;
$file = "<?php __HALT_COMPILER(); ?>";

$files = array();
$files['a.php'] = '<?php echo "This is a\n"; ?>';
$files['b.php'] = '<?php echo "This is b\n"; ?>';
$files['b/c.php'] = '<?php echo "This is b/c\n"; ?>';
include 'files/phar_test.inc';

function err_handler($errno, $errstr, $errfile, $errline) {
  echo "Recoverable fatal error: $errstr in $errfile on line $errline\n";
}

set_error_handler("err_handler", E_RECOVERABLE_ERROR);

$fp = fopen($pname . '/b/new.php', 'wb');
fwrite($fp, 'extra');
fclose($fp);
include $pname . '/b/c.php';
include $pname . '/b/new.php';
?>

===DONE===
--CLEAN--
<?php unlink(dirname(__FILE__) . '/' . basename(__FILE__, '.clean.php') . '.phar.php'); ?>
--EXPECTF--
Warning: fopen(phar://%sopen_for_write_newfile_b.phar.php/b/new.php): failed to open stream: phar error: write operations disabled by the php.ini setting phar.readonly in %sopen_for_write_newfile_b.php on line %d

Warning: fwrite() expects parameter 1 to be resource, boolean given in %sopen_for_write_newfile_b.php on line %d

Warning: fclose() expects parameter 1 to be resource, boolean given in %sopen_for_write_newfile_b.php on line %d
This is b/c

Warning: include(phar://%sopen_for_write_newfile_b.phar.php/b/new.php): failed to open stream: phar error: "b/new.php" is not a file in phar "%sopen_for_write_newfile_b.phar.php" in %sopen_for_write_newfile_b.php on line 22

Warning: include(): Failed opening 'phar://%sopen_for_write_newfile_b.phar.php/b/new.php' for inclusion (include_path='%s') in %sopen_for_write_newfile_b.php on line %d

===DONE===
