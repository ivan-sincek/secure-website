<?php
require_once './php/user.class.php';
require_once './php/session.class.php';
$user = Session::getUser();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Home | Secure Website</title>
		<meta name="description" content="Welcome.">
		<meta name="keywords" content="HTML, CSS, PHP, JavaScript, jQuery, secure, website">
		<meta name="author" content="Ivan Šincek">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="icon" href="./img/favicon.ico">
		<link rel="stylesheet" href="./css/main.css" hreflang="en" type="text/css" media="all">
		<script src="./js/jquery.js"></script>
		<script src="./js/main.js" defer></script>
	</head>
	<body class="background-img">
		<?php require_once './components/navigation.php'; ?>
		<div class="home">
			<header>
				<h1 class="title">Secure Website</h1>
				<p>The Backdoor to the Internet</p>
			</header>
		</div>
		<?php require_once './components/footer.php'; ?>
	</body>
</html>
