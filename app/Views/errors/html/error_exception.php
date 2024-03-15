<?php
declare(strict_types=1);

$errorId = uniqid('error', true);
$title = $title ?? 'Error';
$title = esc($title);
$exception = $exception ?? (object) [];
$file = $file ?? '';
$line = $line ?? 0;
?>
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="robots" content="noindex">

	<title><?= $title ?></title>
	<style type="text/css">
		<?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
	</style>

	<script type="text/javascript">
		<?= file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.js') ?>
	</script>
</head>
<body onload="init()">

	<!-- Header -->
	<div class="header">
		<div class="container">
			<h1><?= "{$title} #" . ($exception->getCode() ?? '') ?></h1>
			<p>
				<?= nl2br(esc($exception->getMessage() ?? '')) ?>
				<a href="https://www.duckduckgo.com/?q=<?= urlencode("{$title} " . preg_replace('#\'.*\'|".*"#Us', '', $exception->getMessage() ?? '')) ?>"
				   rel="noreferrer" target="_blank">search &rarr;</a>
			</p>
	
