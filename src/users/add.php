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
    'username' => '',
    'email' => '',
    'role_id' => ''
);
$errorMessages = array(
    'global' => '',
    'token' => '',
    'username' => '',
    'email' => '',
    'password' => '',
    'confirmPassword' => '',
    'role_id' => ''
);
if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
    if (isset($_POST['token']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirmPassword']) && isset($_POST['role_id'])) {
        $inputValues['username'] = General::output($_POST['username']);
        $inputValues['email'] = General::output($_POST['email']);
        $inputValues['role_id'] = General::output($_POST['role_id']);
        $parameters = array(
            'token' => trim($_POST['token']),
            'username' => trim($_POST['username']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'],
            'confirmPassword' => $_POST['confirmPassword'],
            'role_id' => trim($_POST['role_id'])
        );
        mb_internal_encoding('UTF-8');
        $error = false;
        if (mb_strlen($parameters['token']) < 1) {
            $error = true;
            $errorMessages['token'] = 'Form token was not supplied';
        } else if (!Session::verifyToken('add_user', $parameters['token'])) {
            $error = true;
            $errorMessages['token'] = 'Form token is invalid or has expired';
        }
        if (mb_strlen($parameters['username']) < 1) {
            $error = true;
            $errorMessages['username'] = 'Please enter username';
        } else if (mb_strlen($parameters['username']) > 30) {
            $error = true;
            $errorMessages['username'] = 'Username is exceeding 30 characters';
        } else {
            $exp = '/^[a-zA-Z0-9!#%?*_]+$/';
            if (!preg_match($exp, $parameters['username'])) {
                $error = true;
                $errorMessages['username'] = 'Username contains forbidden characters';
            } else {
                $params = array(
                    'username' => strtolower($parameters['username'])
                );
                $count = Query::count('SELECT `username` FROM `users` WHERE LOWER(`username`) = :username', $params);
                if ($count === false) {
                    $error = true;
                    $errorMessages['global'] = 'Database error';
                    $errorMessages['username'] = 'Cannot verify username ';
                } else if ($count > 0) {
                    $error = true;
                    $errorMessages['username'] = 'Username already exists';
                }
            }
        }
        if (mb_strlen($parameters['email']) < 1) {
            $error = true;
            $errorMessages['email'] = 'Please enter email';
        } else if (mb_strlen($parameters['email']) > 254) {
            $error = true;
            $errorMessages['email'] = 'Email is exceeding 254 characters';
        } else {
            $exp = '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
            if (!preg_match($exp, $parameters['email'])) {
                $error = true;
                $errorMessages['email'] = 'Email format is not supported';
            } else {
                $params = array(
                    'email' => strtolower($parameters['email'])
                );
                $count = Query::count('SELECT `email` FROM `users` WHERE LOWER(`email`) = :email', $params);
                if ($count === false) {
                    $error = true;
                    $errorMessages['global'] = 'Database error';
                    $errorMessages['email'] = 'Cannot verify email';
                } else if ($count > 0) {
                    $error = true;
                    $errorMessages['email'] = 'Email already exists';
                }
            }
        }
        if (mb_strlen($parameters['password']) < 1) {
            $error = true;
            $errorMessages['password'] = 'Please enter password';
        } else if (mb_strlen($parameters['password']) < 10) {
            $error = true;
            $errorMessages['password'] = 'Password must be at least 10 characters long';
        } else if (mb_strlen($parameters['password']) > 72) {
            $error = true;
            $errorMessages['password'] = 'Password is exceeding 72 characters';
        }
        if (mb_strlen($parameters['confirmPassword']) < 1) {
            $error = true;
            $errorMessages['confirmPassword'] = 'Please confirm password';
        } else if (mb_strlen($parameters['confirmPassword']) > 72) {
            $error = true;
            $errorMessages['confirmPassword'] = 'Confirmed password is exceeding 72 characters';
        } else if ($parameters['confirmPassword'] !== $parameters['password']) {
            $error = true;
            $errorMessages['confirmPassword'] = 'Password and confirmed password do not match';
        }
        if (mb_strlen($parameters['role_id']) < 1) {
            $error = true;
            $errorMessages['role_id'] = 'Please select role';
        } else if (!is_numeric($parameters['role_id'])) {
            $error = true;
            $errorMessages['role_id'] = 'Role does not match a numeric value';
        } else {
            $params = array(
                'id' => strtolower($parameters['role_id'])
            );
            $count = Query::count('SELECT `name` FROM `roles` WHERE LOWER(`id`) = :id', $params);
            if ($count === false) {
                $error = true;
                $errorMessages['global'] = 'Database error';
                $errorMessages['role_id'] = 'Cannot verify role';
            } else if ($count === 0) {
                $error = true;
                $errorMessages['role_id'] = 'Role does not exists';
            }
        }
        if (!$error) {
            $params = array(
                'username' => $parameters['username'],
                'email' => strtolower($parameters['email']),
                'password' => password_hash($parameters['password'], PASSWORD_BCRYPT, array('cost' => 12)),
                'date_created' => General::getDate(),
                'role_id' => $parameters['role_id']
            );
            if (Query::insert('INSERT INTO `users` (`username`, `email`, `password`, `date_created`, `role_id`) VALUES (:username, :email, :password, :date_created, :role_id)', $params)) {
                header('Location: ./');
                exit();
            } else {
                $errorMessages['global'] = 'Database error';
            }
        }
    }
}
$roles = Query::select('SELECT `id`, `name` FROM `roles`');
if ($roles === false) {
    $errorMessages['global'] = 'Database error';
    $errorMessages['role_id'] = 'Cannot fetch user roles';
} else {
    foreach ($roles as $row => $values) {
        $roles[$row] = General::outputArray($values);
    }
}
$inputValues['token'] = Session::generateToken('add_user');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Add User | Secure Website</title>
		<meta name="description" content="Add new user.">
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
					<label for="username">Username</label>
					<input name="username" id="username" type="text" spellcheck="false" maxlength="30" pattern="^[a-zA-Z0-9!#%?*_]+$" required="required" autofocus="autofocus" value="<?php echo $inputValues['username']; ?>">
					<p class="error"><?php echo $errorMessages['username']; ?></p>
				</div>
				<div class="data-row">
					<label for="email">Email</label>
					<input name="email" id="email" type="text" spellcheck="false" maxlength="254" pattern="^(([^<>()\[\]\\.,;:\s@\u0022]+(\.[^<>()\[\]\\.,;:\s@\u0022]+)*)|(\u0022.+\u0022))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$" required="required" value="<?php echo $inputValues['email']; ?>">
					<p class="error"><?php echo $errorMessages['email']; ?></p>
				</div>
				<div class="data-row">
					<label for="password">Password</label>
					<input name="password" id="password" type="password" maxlength="72" autocomplete="off" required="required">
					<p class="error"><?php echo $errorMessages['password']; ?></p>
				</div>
				<div class="data-row">
					<label for="confirmPassword">Confirm password</label>
					<input name="confirmPassword" id="confirmPassword" type="password" maxlength="72" autocomplete="off" required="required">
					<p class="error"><?php echo $errorMessages['confirmPassword']; ?></p>
				</div>
				<div class="data-row">
					<label for="role_id">Role</label>
					<select name="role_id" id="role_id" required="required">
						<option value="">none</option>
						<?php foreach ($roles as $role): ?>
							<option value="<?php echo $role['id']; ?>"<?php echo $role['id'] === $inputValues['role_id'] ? ' selected="selected"' : ''; ?>><?php echo $role['name']; ?></option>
						<?php endforeach ?>
					</select>
					<p class="error"><?php echo $errorMessages['role_id']; ?></p>
				</div>
				<div class="btn">
					<input type="submit" value="Add">
				</div>
			</form>
		</div>
		<?php require_once '../components/footer.php'; ?>
	</body>
</html>
