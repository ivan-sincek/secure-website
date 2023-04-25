<?php
require_once '../php/user.class.php';
require_once '../php/session.class.php';
require_once '../php/database.class.php';
require_once '../php/query.class.php';
require_once '../php/general.class.php';
$user = Session::getUser();
if (!$user || $user->getRole() != 1) {
    header('Location: ../');
    exit();
}
$errorMessages = array(
    'global' => ''
);
if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'get') {
    if (isset($_GET['id'])) {
        if (is_numeric($_GET['id'])) {
            $params = array(
                'id' => $_GET['id']
            );
            $data = Query::select('SELECT `users`.`id`, `users`.`username`, `users`.`password`, `users`.`email`, `users`.`date_created`, `users`.`activated`, `users`.`sign_in_count`, `users`.`locked_until`, `users`.`banned`, `roles`.`id` AS `role_id`, `roles`.`name` AS `role` FROM `users` LEFT JOIN `roles` ON `users`.`role_id` = `roles`.`id` WHERE `users`.`id` = :id', $params, 'single');
            if ($data === false) {
                $errorMessages['global'] = 'Database error';
            } else if (sizeof($data) === 0) {
                $errorMessages['global'] = 'ID does not exists';
            } else {
                $data = General::outputArray($data);
            }
        } else {
            $errorMessages['global'] = 'ID does not match a numeric value';
        }
    } else {
        $errorMessages['global'] = 'ID is missing';
    }
} else {
    $errorMessages['global'] = 'Bad request';
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>View User | Secure Website</title>
		<meta name="description" content="View user data.">
		<meta name="keywords" content="HTML, CSS, PHP, JavaScript, jQuery, secure, website">
		<meta name="author" content="Ivan Šincek">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="icon" href="../img/favicon.ico">
		<link rel="stylesheet" href="../css/main.css" hreflang="en" type="text/css" media="all">
		<script src="../js/jquery.js"></script>
		<script src="../js/main.js" defer></script>
	</head>
	<body>
		<?php require_once '../components/navigation.php'; ?>
		<div class="crud-view">
			<p class="error-global"><?php echo $errorMessages['global']; ?></p>
			<div class="data-row">
				<p class="label">ID</p>
				<p class="value"><?php echo $data['id']; ?></p>
			</div>
			<div class="data-row">
				<p class="label">Username</p>
				<p class="value"><?php echo $data['username']; ?></p>
			</div>
			<div class="data-row">
				<p class="label">Password</p>
				<p class="value long"><?php echo $data['password']; ?></p>
			</div>
			<div class="data-row">
				<p class="label">Email</p>
				<p class="value long"><?php echo $data['email']; ?></p>
			</div>
			<div class="data-row">
				<p class="label">Date created</p>
				<p class="value"><?php echo $data['date_created']; ?></p>
			</div>
			<div class="data-row">
				<p class="label">Activated</p>
				<p class="value"><?php echo $data['activated']; ?></p>
			</div>
			<div class="data-row">
				<p class="label">Sign in count</p>
				<p class="value"><?php echo $data['sign_in_count']; ?></p>
			</div>
			<div class="data-row">
				<p class="label">Locked until</p>
				<p class="value"><?php echo $data['locked_until']; ?></p>
			</div>
			<div class="data-row">
				<p class="label">Banned</p>
				<p class="value"><?php echo $data['banned']; ?></p>
			</div>
			<div class="data-row">
				<p class="label">Role</p>
				<p class="value"><a href="../roles/view.php?id=<?php echo $data['role_id']; ?>" class="foreign-key"><?php echo $data['role']; ?></a></p>
			</div>
		</div>
		<?php require_once '../components/footer.php'; ?>
	</body>
</html>
