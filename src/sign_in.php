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
    'username' => ''
);
$errorMessages = array(
    'global' => '',
    'token' => '',
    'username' => '',
    'password' => ''
);
if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
    if (isset($_POST['token']) && isset($_POST['username']) && isset($_POST['password'])) {
        $inputValues['username'] = General::output($_POST['username']);
        $parameters = array(
            'token' => trim($_POST['token']),
            'username' => trim($_POST['username']),
            'password' => $_POST['password']
        );
        mb_internal_encoding('UTF-8');
        $error = false;
        if (mb_strlen($parameters['token']) < 1) {
            $error = true;
            $errorMessages['token'] = 'Form token was not supplied';
        } else if (!Session::verifyToken('sign_in', $parameters['token'])) {
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
            }
        }
        if (mb_strlen($parameters['password']) < 1) {
            $error = true;
            $errorMessages['password'] = 'Please enter password';
        } else if (mb_strlen($parameters['password']) > 72) {
            $error = true;
            $errorMessages['password'] = 'Password is exceeding 72 characters';
        }
        if (!$error) {
            $params = array(
                'username' => strtolower($parameters['username'])
            );
            $data = Query::select('SELECT `id`, `username`, `password`, `sign_in_count`, `locked_until`, `banned`, `role_id` FROM `users` WHERE LOWER(`username`) = :username', $params, 'single');
            if ($data === false) {
                $errorMessages['global'] = 'Database error';
            } else if (sizeof($data) === 0) {
                $errorMessages['username'] = 'User does not exists';
            } else {
                $datetime = strtotime($data['locked_until']);
                if (time() > $datetime) {
                    if (@password_verify($parameters['password'], $data['password'])) {
                        // uncomment the following if email verification is implemented
                        // if ($data['activated']) {
                            if (!$data['banned']) {
                                if ($data['sign_in_count'] || $datetime) {
                                    Query::update('UPDATE `users` SET `sign_in_count` = 0, `locked_until` = null WHERE LOWER(`username`) = :username', $params);
                                }
                                $user = new User($data['id'], $data['username'], $data['role_id']);
                                if (Session::setUser($user)) {
                                    header('Location: ./profile.php');
                                    exit();
                                } else {
                                    $errorMessages['global'] = 'Cannot create user session';
                                }
                            } else {
                                $errorMessages['global'] = 'Account is banned';
                            }
                        // } else {
                            // $errorMessages['global'] = 'Account is not activated';
                        // }
                    } else {
                        $errorMessages['password'] = 'Invalid password';
                        $params['sign_in_count'] = ++$data['sign_in_count'];
                        $query = '';
                        if ($data['sign_in_count'] % 20) {
                            $query = 'UPDATE `users` SET `sign_in_count` = :sign_in_count WHERE LOWER(`username`) = :username';
                        } else {
                            $multiplicator = $data['sign_in_count'] / 20;
                            $multiplicator = $multiplicator > 3 ? 3 : $multiplicator;
                            $params['locked_until'] = General::getDatetime(time() + 60 * 3 * $multiplicator);
                            $query = 'UPDATE `users` SET `sign_in_count` = :sign_in_count, `locked_until` = :locked_until WHERE LOWER(`username`) = :username';
                        }
                        Query::update($query, $params);
                    }
                } else {
                    $datetime = General::getDatetime($datetime);
                    $errorMessages['global'] = "Account is locked until {$datetime}";
                }
            }
        }
    }
}
$inputValues['token'] = Session::generateToken('sign_in');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Sign In | Secure Website</title>
		<meta name="description" content="The backdoor to the internet.">
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
					<h1 class="title">Sign In</h1>
				</header>
				<p class="error-global"><?php echo $errorMessages['global']; ?></p>
				<p class="error-global"><?php echo $errorMessages['token']; ?></p>
				<form method="post" action="./sign_in.php">
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
					<input name="username" id="username" type="text" maxlength="30" spellcheck="false" pattern="^[a-zA-Z0-9!#%?*_]+$" required="required" autofocus="autofocus" value="<?php echo $inputValues['username']; ?>">
					<p class="error"><?php echo $errorMessages['username']; ?></p>
					<div class="label-info">
						<label for="password">Password</label>
						<div class="info">
							<input id="passwordDropdown" type="checkbox" class="info-checkbox">
							<label for="passwordDropdown" class="info-toogle">
								<img src="./img/info.png" alt="Info">
							</label>
							<ul>
								<li><p>Required</p></li>
								<li><p>Max. 72 characters</p></li>
							</ul>
						</div>
					</div>
					<input name="password" id="password" type="password" maxlength="72" autocomplete="off" required="required">
					<p class="error"><?php echo $errorMessages['password']; ?></p>
					<input type="submit" value="Sign In">
				</form>
				<ul class="links">
					<li><a href="">Forgot your password?</a></li>
					<li><a href="./register.php">Don't have an account?</a></li>
				</ul>
			</div>
		</div>
		<?php require_once './components/footer.php'; ?>
	</body>
</html>
