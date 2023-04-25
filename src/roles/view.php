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
            $data = Query::select('SELECT `id`, `name`, `description` FROM `roles` WHERE `id` = :id', $params, 'single');
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
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>View Role | Secure Website</title>
		<meta name="description" content="View role data.">
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
				<p class="label">Name</p>
				<p class="value"><?php echo $data['name']; ?></p>
			</div>
			<div class="data-row">
				<p class="label">Description</p>
				<textarea rows="6" disabled="disabled"><?php echo $data['description']; ?></textarea>
			</div>
		</div>
		<?php require_once '../components/footer.php'; ?>
	</body>
</html>
