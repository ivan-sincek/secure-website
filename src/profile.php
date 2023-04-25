<?php
require_once './php/user.class.php';
require_once './php/session.class.php';
require_once './php/general.class.php';
$user = Session::getUser();
if (!$user) {
    header('Location: ./');
    exit();
}
$userData = General::outputArray($user->getAll());
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title><?php echo $userData['username']; ?> | Profile | Secure Website</title>
		<meta name="description" content="User profile.">
		<meta name="keywords" content="HTML, CSS, PHP, JavaScript, jQuery, secure, website">
		<meta name="author" content="Ivan Šincek">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="icon" href="./img/favicon.ico">
		<link rel="stylesheet" href="./css/main.css" hreflang="en" type="text/css" media="all">
		<script src="./js/jquery.js"></script>
		<script src="./js/main.js" defer></script>
	</head>
	<body>
		<?php require_once './components/navigation.php'; ?>
		<div class="profile">
			<div class="layout">
				<p>User ID: <span><?php echo $userData['id']; ?></span></p>
				<p>Username: <span><?php echo $userData['username']; ?></span></p>
				<p>Role ID: <span><?php echo $userData['role']; ?></span></p>
			</div>
		</div>
		<?php require_once './components/footer.php'; ?>
	</body>
</html>
