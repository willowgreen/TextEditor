<?php
define('_VERSION', 0x00000002);
define('FILE_ACCESS', $_GET['FILE_ACCESS'] ?? false);
define('FILE_CUSTOM', $_POST['FILE_CUSTOM'] ?? false);
define('FILE_DELETE', isset($_GET['FILE_DELETE']));
define('FILE_FINISH', isset($_GET['FILE_FINISH']));
define('FILE_VALID', array_diff(scandir('.') , array(
	'.',
	'..',
	basename(__FILE__),
	'.htaccess',
	'Default.aspx',
	'index.html',
	'index.php'
)));
define('FILE_WRITE', $_GET['FILE_WRITE'] ?? false);
define('PASSWORD', 'DEFINE_PASSWORD_HERE');
define('SIGNED_IN', isset($_COOKIE['PASSWORD']) && $_COOKIE['PASSWORD'] == PASSWORD);
if (isset($_POST['PASSWORD'])) {
    setcookie('PASSWORD', $_POST['PASSWORD'], strtotime('+30 days'));
    header('LOCATION: ./' . basename(__FILE__));
    die();
}
if (SIGNED_IN) {
	if (FILE_CUSTOM) {
		header('LOCATION: ./' . basename(__FILE__) . '?FILE_ACCESS=' . htmlspecialchars(FILE_CUSTOM));
		die();
	}
	if (FILE_ACCESS) {
		if (FILE_FINISH) {
			$handle = @fopen(dirname(__FILE__) . DIRECTORY_SEPARATOR . htmlspecialchars_decode(FILE_ACCESS) , 'w');
			fwrite($handle, $_POST['CONTENTS']);
			fclose($handle);
			header('LOCATION: ./' . basename(__FILE__));
			die();
		}
		if (FILE_DELETE) {
			@unlink(dirname(__FILE__) . DIRECTORY_SEPARATOR . htmlspecialchars_decode(FILE_ACCESS));
			header('LOCATION: ./' . basename(__FILE__));
			die();
		}
		@fopen(dirname(__FILE__) . DIRECTORY_SEPARATOR . htmlspecialchars_decode(FILE_ACCESS) , 'x');
	}
}
?>
<html lang="en">
<head>
    <title>Textr<?php
if (!SIGNED_IN) echo (' | sign in'); ?></title>
</head>
<div align="center">
    <a href="<?= basename(__FILE__); ?>">
        <h3>Textr</h3>
    </a>
</div>
<br />
<body>
    <div align="center">
        <?php
if (SIGNED_IN) {
		echo ('<form action="' . basename(__FILE__) . '?FILE_ACCESS=' . FILE_ACCESS . '&FILE_FINISH" method="post">');
?>
        <textarea style="height: 75%; width: 100%;" name="CONTENTS" wrap="soft"><?= file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . htmlspecialchars_decode(FILE_ACCESS)); ?></textarea>
        <br />
        <br />
        <input type="submit" value="save file" />
        <a href="<?= basename(__FILE__) . '?FILE_ACCESS=' . FILE_ACCESS . '&FILE_DELETE'; ?>">
            <input type="button" value="delete file" /></a>
        </form>
        <?php
	}
	else {
		foreach(FILE_VALID as $file) {
?>
        <a href="<?= basename(__FILE__) . '?FILE_ACCESS=' . htmlspecialchars($file); ?>">
            <pre><?= htmlspecialchars_decode($file); ?></pre>
        </a>
        <?php
		}
?>
        <br />
        <br />
        <form action="<?= basename(__FILE__); ?>" method="post">
            <input name="FILE_CUSTOM" type="text" />
            <input type="submit" value="create" />
        </form>
        <?php
}
if (!SIGNED_IN) {
?>
        <br />
        <br />
        <br />
        <form action="<?= basename(__FILE__); ?>" method="post">
            <div align="center">
                <input type="password" name="PASSWORD" class="form-control" placeholder="password" />
                <br />
                <br />
                <br />
                <input type="submit" value="access" />
            </div>
        </form>
        <?php
}
?>
    </div>
</body>
<br />
<br />
<footer>
    <div align="center">
        TextEditor <?= _VERSION; ?> &mdash; Made with ❤️ by <a href="https://willowgreen.io/opensource">Willowgreen</a>
    </div>
</footer>
</html>
