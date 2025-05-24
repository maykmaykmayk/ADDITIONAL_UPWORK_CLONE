<?php  

require_once 'dbConfig.php';

function checkIfUserExists($pdo, $username) {
	$response = array();
	$sql = "SELECT * FROM upwork_users WHERE username = ?";
	$stmt = $pdo->prepare($sql);

	if ($stmt->execute([$username])) {

		$userInfoArray = $stmt->fetch();

		if ($stmt->rowCount() > 0) {
			$response = array(
				"result"=> true,
				"status" => "200",
				"userInfoArray" => $userInfoArray
			);
		}

		else {
			$response = array(
				"result"=> false,
				"status" => "400",
				"message"=> "User doesn't exist from the database"
			);
		}
	}

	return $response;

}

function insertNewUser($pdo, $username, $first_name, $last_name, $password) {
	$response = array();
	$checkIfUserExists = checkIfUserExists($pdo, $username); 

	if (!$checkIfUserExists['result']) {

		$sql = "INSERT INTO upwork_users (username, first_name, last_name, is_client, password) 
		VALUES (?,?,?,?,?)";

		$stmt = $pdo->prepare($sql);

		if ($stmt->execute([$username, $first_name, $last_name, true, $password])) {
			$response = array(
				"status" => "200",
				"message" => "User successfully inserted!"
			);
		}

		else {
			$response = array(
				"status" => "400",
				"message" => "An error occured with the query!"
			);
		}
	}

	else {
		$response = array(
			"status" => "400",
			"message" => "User already exists!"
		);
	}

	return $response;
}

// Gig entity

function getGigById($pdo, $gig_id) {
	$sql = "SELECT * FROM gigs WHERE gig_id = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$gig_id]);
	return $stmt->fetch();
}


function getAllGigs($pdo) {
	$sql = "SELECT
				upwork_users.username AS username, 
				gigs.gig_id AS gig_id, 
				gigs.gig_title AS title, 
				gigs.gig_description AS description, 
				gigs.date_added AS date_added 
			FROM upwork_users
			JOIN gigs ON upwork_users.user_id = gigs.user_id
			ORDER BY date_added DESC
			";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	return $stmt->fetchAll();
}

function getAllGigsByUserId($pdo, $user_id) {
	$sql = "SELECT
				upwork_users.username AS username, 
				gigs.gig_id AS gig_id, 
				gigs.gig_title AS title, 
				gigs.gig_description AS description, 
				gigs.date_added AS date_added 
			FROM upwork_users
			JOIN gigs ON upwork_users.user_id = gigs.user_id
			WHERE upwork_users.user_id = ?
			ORDER BY date_added DESC
			";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$user_id]);
	return $stmt->fetchAll();
}

function insertNewGig($pdo, $gig_title, $gig_description, $user_id) {
	$sql = "INSERT INTO gigs (gig_title, gig_description, user_id) 
			VALUES (?,?,?)";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$gig_title, $gig_description, $user_id]);
}

function updateGig($pdo, $gig_title, $gig_description, $gig_id) {
	$sql = "UPDATE gigs SET gig_title = ?, gig_description = ? WHERE gig_id = ?";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$gig_title, $gig_description, $gig_id]);
}

function deleteGig($pdo, $gig_id) {
	$sql = "DELETE FROM gigs WHERE gig_id = ?";
	$stmt = $pdo->prepare($sql);
	if (deleteAllProposalsByGig($pdo, $gig_id) && deleteAllInterviewsByGig($pdo, $gig_id)) {
		return $stmt->execute([$gig_id]);
	}
}

function deleteAllProposalsByGig($pdo, $gig_id) {
	$sql = "DELETE FROM gig_proposals WHERE gig_id = ?";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$gig_id]);
}

function deleteAllInterviewsByGig($pdo, $gig_id) {
	$sql = "DELETE FROM gig_interviews WHERE gig_id = ?";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$gig_id]);
}

// Gig proposals
function getProposalsByGigId($pdo, $gig_id) {
	$sql = "SELECT 
				upwork_users.user_id AS user_id,
				upwork_users.first_name AS first_name,
				upwork_users.last_name AS last_name,
				gig_proposals.gig_proposal_description AS description,
				gig_proposals.date_added AS date_added
			FROM upwork_users 
			JOIN gig_proposals ON upwork_users.user_id = gig_proposals.user_id
			WHERE gig_id = ?
			";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$gig_id]);
	return $stmt->fetchAll();	
}



// Gig Interview entity
function checkIfUserAlreadyScheduled($pdo, $freelancer_id, $gig_id) {
	$sql = "SELECT * FROM gig_interviews WHERE freelancer_id = ? AND gig_id = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$freelancer_id, $gig_id]);
	if ($stmt->rowCount() > 0) {
		return true;
	}
}

function getAllInterviewsByGig($pdo, $gig_id) {
	$sql = "SELECT 
				upwork_users.first_name AS first_name,
				upwork_users.last_name AS last_name,
				gig_interviews.time_start AS time_start,
				gig_interviews.time_end AS time_end,
				gig_interviews.status AS status
			FROM upwork_users JOIN gig_interviews 
			ON upwork_users.user_id = gig_interviews.freelancer_id
			WHERE gig_interviews.gig_id = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$gig_id]);
	return $stmt->fetchAll();
}

function validateInterviewDate($pdo, $time_start, $time_end, $gig_id) {
	// Convert to DateTime objects
	$start = new DateTime($time_start);
	$end = new DateTime($time_end);
	$now = new DateTime();

	// Check if dates are in the past
	if ($start < $now || $end < $now) {
		return [
			'valid' => false,
			'message' => 'Interview cannot be scheduled in the past'
		];
	}

	// Check if end time is after start time
	if ($end <= $start) {
		return [
			'valid' => false,
			'message' => 'End time must be after start time'
		];
	}

	// Get client_id from the gig
	$stmt = $pdo->prepare("SELECT user_id FROM gigs WHERE gig_id = ?");
	$stmt->execute([$gig_id]);
	$client_id = $stmt->fetchColumn();

	// Check for conflicts with existing interviews
	$stmt = $pdo->prepare("
		SELECT * FROM gig_interviews 
		WHERE gig_id = ? 
		AND (
			(time_start <= ? AND time_end > ?) OR
			(time_start < ? AND time_end >= ?) OR
			(time_start >= ? AND time_end <= ?)
		)
	");
	
	$stmt->execute([
		$gig_id,
		$time_start, $time_start,
		$time_end, $time_end,
		$time_start, $time_end
	]);

	if ($stmt->rowCount() > 0) {
		return [
			'valid' => false,
			'message' => 'This time slot conflicts with another scheduled interview'
		];
	}

	return [
		'valid' => true,
		'message' => 'Date is valid'
	];
}

function insertNewGigInterview($pdo, $gig_id, $freelancer_id, $time_start, $time_end) {
	try {
		// Get client_id from the gig
		$stmt = $pdo->prepare("SELECT user_id FROM gigs WHERE gig_id = ?");
		$stmt->execute([$gig_id]);
		$client_id = $stmt->fetchColumn();

		// Validate the date
		$validation = validateInterviewDate($pdo, $time_start, $time_end, $gig_id);
		if (!$validation['valid']) {
			return json_encode([
				'status' => '400',
				'message' => $validation['message']
			]);
		}

		// Check if interview already exists
		if (checkIfUserAlreadyScheduled($pdo, $freelancer_id, $gig_id)) {
			return json_encode([
				'status' => '400',
				'message' => 'An interview has already been scheduled for this proposal'
			]);
		}

		$stmt = $pdo->prepare("
			INSERT INTO gig_interviews (gig_id, freelancer_id, time_start, time_end, status) 
			VALUES (?, ?, ?, ?, 'pending')
		");
		
		if ($stmt->execute([$gig_id, $freelancer_id, $time_start, $time_end])) {
			return json_encode([
				'status' => '200',
				'message' => 'Interview scheduled successfully'
			]);
		} else {
			return json_encode([
				'status' => '400',
				'message' => 'Failed to schedule interview'
			]);
		}
	} catch (PDOException $e) {
		return json_encode([
			'status' => '400',
			'message' => 'Error scheduling interview: ' . $e->getMessage()
		]);
	}
}

function updateGigInterview($pdo, $gig_title, $gig_description, $gig_id) {
	$sql = "UPDATE gig_interviews SET time_start = ?, time_end = ? WHERE gig_interview_id = ?";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$gig_title, $gig_description, $gig_id]);
}

function deleteGigInterview($pdo, $gig_interview_id) {
	$sql = "DELETE FROM gig_interviews WHERE gig_interview_id = ?";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$gig_id]);	
}

function getAllProposalsForClient($pdo, $client_id) {
    $sql = "SELECT 
                gigs.gig_id,
                gigs.gig_title AS title,
                upwork_users.username,
                gig_proposals.gig_proposal_description AS description,
                gig_proposals.date_added AS date_added
            FROM gigs 
            JOIN gig_proposals ON gigs.gig_id = gig_proposals.gig_id
            JOIN upwork_users ON gig_proposals.user_id = upwork_users.user_id
            WHERE gigs.user_id = ?
            ORDER BY gig_proposals.date_added DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$client_id]);
    return $stmt->fetchAll();
}

function getAllProposals($pdo, $client_id) {
    $sql = "SELECT 
                gp.gig_proposal_id AS proposal_id,
                g.gig_id,
                g.gig_title AS title,
                g.gig_description AS description,
                u.username,
                gp.gig_proposal_description AS description,
                gp.date_added,
                CASE WHEN gi.gig_interview_id IS NOT NULL THEN 1 ELSE 0 END as interview_status,
                gi.time_start,
                gi.time_end
            FROM gig_proposals gp
            JOIN gigs g ON gp.gig_id = g.gig_id
            JOIN upwork_users u ON gp.user_id = u.user_id
            LEFT JOIN gig_interviews gi ON gp.gig_id = gi.gig_id AND gp.user_id = gi.freelancer_id
            WHERE g.user_id = ?
            ORDER BY gp.date_added DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$client_id]);
    return $stmt->fetchAll();
}

