<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 Page Not Found</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #fafafa;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #777;
            font-weight: 300;
            margin: 0;
        }

        .container {
            text-align: center;
            max-width: 500px;
        }

        h1 {
            font-weight: lighter;
            letter-spacing: 0.8;
            font-size: 3rem;
            margin-top: 0;
            margin-bottom: 0;
            color: #222;
        }

        p {
            margin-top: 1.5rem;
        }

        pre {
            white-space: normal;
            margin-top: 1.5rem;
        }

        code {
            background: #fafafa;
            border: 1px solid #efefef;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            display: block;
        }

        .footer {
            margin-top: 2rem;
            border-top: 1px solid #efefef;
            padding: 1em 2em 0 2em;
            font-size: 85%;
            color: #999;
        }

        a:active,
        a:link,
        a:visited {
            color: #dd4814;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>404 - File Not Found</h1>

    <?php
    $message = isset($_GET['message']) ? $_GET['message'] : null;
    ?>

    <?php if ($message !== null && $message !== '(null)') : ?>
        <p><?= htmlspecialchars(trim($message)); ?></p>
    <?php else : ?>
        <p>Sorry! Cannot seem
