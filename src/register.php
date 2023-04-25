<?php
require_once './php/user.class.php';
require_once './php/session.class.php';
require_once './php/database.class.php';
require_once './php/query.class.php';
require_once './php/general.class.php';
$user = Session::getUser();
if ($user) {
    header('Location: ./');
    exit();
}
$inputValues = array(
    'token' => '',
    'username' => '',
    'email' => ''
);
$errorMessages = array(
    'global' => '',
    'token' => '',
    'username' => '',
    'email' => '',
    'password' => '',
    'confirmPassword' => ''
);
if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
    if (isset($_POST['token']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirmPassword'])) {
        $inputValues['username'] = General::output($_POST['username']);
        $inputValues['email'] = General::output($_POST['email']);
        $parameters = array(
            'token' => trim($_POST['token']),
            'username' => trim($_POST['username']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'],
            'confirmPassword' => $_POST['confirmPassword']
        );
        mb_internal_encoding('UTF-8');
        $error = false;
        if (mb_strlen($parameters['token']) < 1) {
            $error = true;
            $errorMessages['token'] = 'Form token was not supplied';
        } else if (!Session::verifyToken('register', $parameters['token'])) {
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
        // implement a CAPTCHA
        if (!$error) {
            $params = array(
                'username' => $parameters['username'],
                'email' => strtolower($parameters['email']),
                'password' => password_hash($parameters['password'], PASSWORD_BCRYPT, array('cost' => 12)),
                'date_created' => General::getDate()
            );
            if (Query::insert('INSERT INTO `users` (`username`, `email`, `password`, `date_created`) VALUES (:username, :email, :password, :date_created)', $params)) {
                // implement email verification
                header('Location: ./sign_in.php');
                exit();
            } else {
                $errorMessages['global'] = 'Database error';
            }
        }
    }
}
$inputValues['token'] = Session::generateToken('register');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Register | Secure Website</title>
		<meta name="description" content="Become a member.">
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
		<div class="front-form">
			<div class="layout">
				<header>
					<h1 class="title">Register</h1>
				</header>
				<p class="error-global"><?php echo $errorMessages['global']; ?></p>
				<p class="error-global"><?php echo $errorMessages['token']; ?></p>
				<form method="post" action="./register.php">
					<input name="token" id="token" type="hidden" value="<?php echo $inputValues['token']; ?>">
					<div class="label-info">
						<label for="username">Username</label>
						<div class="info">
							<input id="usernameDropdown" type="checkbox" class="info-checkbox">
							<label for="usernameDropdown" class="info-toogle">
								<img src="./img/info.png" alt="Info">
							</label>
							<ul>
								<li><p>Required</p></li>
								<li><p>Max. 30 characters</p></li>
								<li><p>Allowed ! # % ? * _</p></li>
							</ul>
						</div>
					</div>
					<input name="username" id="username" type="text" spellcheck="false" maxlength="30" pattern="^[a-zA-Z0-9!#%?*_]+$" required="required" autofocus="autofocus" value="<?php echo $inputValues['username']; ?>">
					<p class="error"><?php echo $errorMessages['username']; ?></p>
					<div class="label-info">
						<label for="email">Email</label>
						<div class="info">
							<input id="emailDropdown" type="checkbox" class="info-checkbox">
							<label for="emailDropdown" class="info-toogle">
								<img src="./img/info.png" alt="Info">
							</label>
							<ul>
								<li><p>Required</p></li>
								<li><p>Max. 254 characters</p></li>
								<li><p>Valid format</p></li>
							</ul>
						</div>
					</div>
					<input name="email" id="email" type="text" spellcheck="false" maxlength="254" pattern="^(([^<>()\[\]\\.,;:\s@\u0022]+(\.[^<>()\[\]\\.,;:\s@\u0022]+)*)|(\u0022.+\u0022))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$" required="required" value="<?php echo $inputValues['email']; ?>">
					<p class="error"><?php echo $errorMessages['email']; ?></p>
					<div class="label-info">
						<label for="password">Password</label>
						<div class="info">
							<input id="passwordDropdown" type="checkbox" class="info-checkbox">
							<label for="passwordDropdown" class="info-toogle">
								<img src="./img/info.png" alt="Info">
							</label>
							<ul>
								<li><p>Required</p></li>
								<li><p>Min. 10 characters</p></li>
								<li><p>Max. 72 characters</p></li>
							</ul>
						</div>
					</div>
					<input name="password" id="password" type="password" autocomplete="off" maxlength="72" required="required">
					<p class="error"><?php echo $errorMessages['password']; ?></p>
					<div class="label-info">
						<label for="confirmPassword">Confirm password</label>
						<div class="info">
							<input id="confirmPasswordDropdown" type="checkbox" class="info-checkbox">
							<label for="confirmPasswordDropdown" class="info-toogle">
								<img src="./img/info.png" alt="Info">
							</label>
							<ul>
								<li><p>Required</p></li>
								<li><p>Max. 72 characters</p></li>
								<li><p>Match password</p></li>
							</ul>
						</div>
					</div>
					<input name="confirmPassword" id="confirmPassword" type="password" autocomplete="off" maxlength="72" required="required">
					<p class="error"><?php echo $errorMessages['confirmPassword']; ?></p>
					<input type="submit" value="Register">
				</form>
				<ul class="links">
					<li><a href="./sign_in.php">Already have an account?</a></li>
				</ul>
			</div>
		</div>
		<?php require_once './components/footer.php'; ?>
	</body>
</html>
