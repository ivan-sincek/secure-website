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
$inputValues = array(
    'token' => '',
    'name' => '',
    'description' => ''
);
$errorMessages = array(
    'global' => '',
    'token' => '',
    'name' => '',
    'description' => ''
);
if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
    if (isset($_POST['token']) && isset($_POST['name']) && isset($_POST['description'])) {
        $inputValues['name'] = General::output($_POST['name']);
        $inputValues['description'] = General::output($_POST['description']);
        $parameters = array(
            'token' => trim($_POST['token']),
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description'])
        );
        mb_internal_encoding('UTF-8');
        $error = false;
        if (mb_strlen($parameters['token']) < 1) {
            $error = true;
            $errorMessages['token'] = 'Form token was not supplied';
        } else if (!Session::verifyToken('add_role', $parameters['token'])) {
            $error = true;
            $errorMessages['token'] = 'Form token is invalid or has expired';
        }
        if (mb_strlen($parameters['name']) < 1) {
            $error = true;
            $errorMessages['name'] = 'Please enter name';
        } else if (mb_strlen($parameters['name']) > 20) {
            $error = true;
            $errorMessages['name'] = 'Name is exceeding 20 characters';
        } else {
            $params = array(
                'name' => strtolower($parameters['name'])
            );
            $count = Query::count('SELECT `name` FROM `roles` WHERE LOWER(`name`) = :name', $params);
            if ($count === false) {
                $error = true;
                $errorMessages['global'] = 'Database error';
                $errorMessages['name'] = 'Cannot verify name';
            } else if ($count > 0) {
                $error = true;
                $errorMessages['name'] = 'Name already exists';
            }
        }
        if (mb_strlen($parameters['description']) < 1) {
            $error = true;
            $errorMessages['description'] = 'Please enter description';
        } else if (mb_strlen($parameters['description']) > 300) {
            $error = true;
            $errorMessages['description'] = 'Description is exceeding 300 characters';
        }
        if (!$error) {
            $params = array(
                'name' => $parameters['name'],
                'description' => $parameters['description']
            );
            if (Query::insert('INSERT INTO `roles` (`name`, `description`) VALUES (:name, :description)', $params)) {
                header('Location: ./');
                exit();
            } else {
                $errorMessages['global'] = 'Database error';
            }
        }
    } else {
        $errorMessages['global'] = 'Required data is missing';
    }
}
$inputValues['token'] = Session::generateToken('add_role');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Add Role | Secure Website</title>
		<meta name="description" content="Add new role.">
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
		<div class="crud-add-edit">
			<p class="error-global"><?php echo $errorMessages['global']; ?></p>
			<p class="error-global"><?php echo $errorMessages['token']; ?></p>
			<form id="add-form" method="post" action="./add.php">
				<input name="token" id="token" type="hidden" value="<?php echo $inputValues['token']; ?>">
				<div class="data-row">
					<label for="name">Name</label>
					<input name="name" id="name" type="text" spellcheck="false" maxlength="20" required="required" autofocus="autofocus" value="<?php echo $inputValues['name']; ?>">
					<p class="error"><?php echo $errorMessages['name']; ?></p>
				</div>
				<div class="data-row">
					<label for="description">Description</label>
					<textarea name="description" id="description" form="add-form" rows="6" required="required"><?php echo $inputValues['description']; ?></textarea>
					<p class="error"><?php echo $errorMessages['description']; ?></p>
				</div>
				<div class="btn">
					<input type="submit" value="Add">
				</div>
			</form>
		</div>
		<?php require_once '../components/footer.php'; ?>
	</body>
</html>
