<?php  
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_POST['insertNewUserBtn'])) {
	$username = trim($_POST['username']);
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	if (!empty($username) && !empty($first_name) && !empty($last_name) && !empty($password) && !empty($confirm_password)) {
		if ($password == $confirm_password) {
			$insertQuery = insertNewUser($pdo, $username, $first_name, $last_name, password_hash($password, PASSWORD_DEFAULT));
			echo json_encode($insertQuery);
		} else {
			echo json_encode([
				'status' => '400',
				'message' => 'Please make sure both passwords are equal'
			]);
		}
	} else {
		echo json_encode([
			'status' => '400',
			'message' => 'Please make sure there are no empty input fields'
		]);
	}
}

if (isset($_POST['loginUserBtn'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (!empty($username) && !empty($password)) {
		$loginQuery = checkIfUserExists($pdo, $username);
		
		if ($loginQuery['result']) {
			$userIDFromDB = $loginQuery['userInfoArray']['user_id'];
			$usernameFromDB = $loginQuery['userInfoArray']['username'];
			$passwordFromDB = $loginQuery['userInfoArray']['password'];
			$isAdminStatusFromDB = $loginQuery['userInfoArray']['is_client'];

			if (password_verify($password, $passwordFromDB)) {
				$_SESSION['user_id'] = $userIDFromDB;
				$_SESSION['username'] = $usernameFromDB;
				$_SESSION['is_client'] = $isAdminStatusFromDB;
				echo "1";
			} else {
				echo "0";
			}
		} else {
			echo "0";
		}
	} else {
		echo "0";
	}
}

if (isset($_GET['logoutUserBtn'])) {
	unset($_SESSION['username']);
	header("Location: ../login.php");
}

if (isset($_POST['createNewGig'])) {
	echo insertNewGig(
						$pdo, 
						$_POST['title'], 
						$_POST['description'], 
						$_SESSION['user_id']
					);
}

if (isset($_POST['insertNewGigInterview'])) {
	echo insertNewGigInterview(
								$pdo, 
								$_POST['gig_id'], 
								$_POST['freelancer_id'], 
								$_POST['time_start'], 
								$_POST['time_end']
							);
}

if (isset($_POST['deleteGig'])) {
	if (deleteGig($pdo,$_POST['gig_id'])) {
		return true;
	}
}

if (isset($_POST['updateGig'])) {
	echo updateGig(
					$pdo, 
					$_POST['title'], 
					$_POST['description'], 
					$_POST['gig_id']
				);
}
