<?php
$error_id = uniqid('error', true);
$title = isset($title) ? esc($title) : 'Error';
$exception = isset($exception) ? $exception : (object) [];
$file = isset($file) ? $file : '';
$line = isset($line) ? $line : 0;
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
			<h1><?= $title, isset($exception->getCode()) ? ' #' . $exception->getCode() : '' ?></h1>
			<p>
				<?= isset($exception->getMessage()) ? nl2br(esc($exception->getMessage())) : '' ?>
				<a href="https://www.duckduckgo.com/?q=<?= urlencode($title . ' ' . preg_replace('#\'.*\'|".*"#Us', '', isset($exception->getMessage()) ? $exception->getMessage() : '')) ?>"
				   rel="noreferrer" target="_blank">search &rarr;</a>
			</p>
		</div>
	</div>

	<!-- Source -->
	<div class="container">
		<p><b><?= isset($file) ? esc(static::cleanPath($file, $line)) : '' ?></b> at line <b><?= esc($line) ?></b></p>

		<?php if (is_file($file)) : ?>
			<div class="source">
				<?= static::highlightFile($file, $line, 15); ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="container">

		<ul class="tabs" id="tabs">
			<li><a href="#backtrace">Backtrace</a></li>
			<li><a href="#server">Server</a></li>
			<li><a href="#request">Request</a></li>
			<li><a href="#response">Response</a></li>
			<li><a href="#files">Files</a></li>
			<li><a href="#memory">Memory</a></li>
		</ul>

		<div class="tab-content">

			<!-- Backtrace -->
			<div class="content" id="backtrace">

				<ol class="trace">
				<?php if (isset($trace) && is_array($trace)) : ?>
					<?php foreach ($trace as $index => $row) : ?>

						<li>
							<p>
								<!-- Trace info -->
								<?php if (isset($row['file']) && is_file($row['file'])) :?>
									<?php
									if (isset($row['function']) && in_array($row['function'], ['include', 'include_once', 'require', 'require_once'], true))
									{
										echo esc($row['function'] . ' ' . static::cleanPath($row['file']));
									}
									else
									{
										echo esc(static::cleanPath($row['file']) . ' : ' . $row['line']);
									}
									?>
								<?php else : ?>
									{PHP internal code}
			
