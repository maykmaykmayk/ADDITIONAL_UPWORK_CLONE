<?php  

require_once 'dbConfig.php';


// User entity

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

		if ($stmt->execute([$username, $first_name, $last_name, false, $password])) {
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

function getAllUsers($pdo) {
	$sql = "SELECT * FROM upwork_users";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}


// Gig entity

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

function getAllInterviewsByUserId($pdo, $user_id) {
	$sql = "SELECT 
				gigs.gig_title AS title,
				gigs.gig_description AS description,
				upwork_users.username AS client_name,
				gig_interviews.gig_interview_id AS gig_interview_id,
				gig_interviews.time_start AS time_start,
				gig_interviews.time_end AS time_end,
				gig_interviews.status AS status
			
			FROM gig_interviews 

			JOIN gigs ON 
				gig_interviews.gig_id = gigs.gig_id 

			JOIN upwork_users upwork_freelancers ON 
				gig_interviews.freelancer_id = upwork_freelancers.user_id  

			JOIN upwork_users ON 
				gigs.user_id = upwork_users.user_id 

			WHERE gig_interviews.freelancer_id = ?";

	$stmt = $pdo->prepare($sql);
	$stmt->execute([$user_id]);
	return $stmt->fetchAll();
}

function updateInterviewStatus($pdo, $status, $gig_interview_id) {
	$sql = "UPDATE gig_interviews SET status = ? WHERE gig_interview_id = ?";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$status, $gig_interview_id]);
}


// Gig proposal

function getProposalByGig($pdo, $gig_id, $user_id) {
	$sql = "SELECT * FROM gig_proposals WHERE gig_id = ? AND user_id = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$gig_id, $user_id]);
	return $stmt->fetch();
}

function checkIfGigProposalAlreadyExists($pdo, $gig_id, $user_id) {
	$sql = "SELECT * FROM gig_proposals WHERE gig_id = ? AND user_id = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$gig_id, $user_id]);
	return $stmt->rowCount() > 0;
}

function insertNewGigProposal($pdo, $gig_proposal_description, $gig_id, $user_id) {
    try {
        if (!checkIfGigProposalAlreadyExists($pdo, $gig_id, $user_id)) {
            $sql = "INSERT INTO gig_proposals (gig_proposal_description, gig_id, user_id) 
                    VALUES (?,?,?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$gig_proposal_description, $gig_id, $user_id])) {
                return json_encode([
                    'status' => '200',
                    'message' => 'Proposal submitted successfully'
                ]);
            } else {
                return json_encode([
                    'status' => '400',
                    'message' => 'Failed to submit proposal'
                ]);
            }
        } else {
            return json_encode([
                'status' => '400',
                'message' => 'You have already submitted a proposal for this gig'
            ]);
        }
    } catch (PDOException $e) {
        return json_encode([
            'status' => '400',
            'message' => 'Error submitting proposal: ' . $e->getMessage()
        ]);
    }
}

function updateGigProposal($pdo, $gig_proposal_description, $gig_proposal_id) {
	$sql = "UPDATE gig_proposals SET gig_proposal_description = ? WHERE gig_proposal_id = ?";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$gig_proposal_description, $gig_proposal_id]);
}

function deleteGigProposal($pdo, $gig_id) {
	$sql = "DELETE FROM gig_proposals WHERE gig_proposal_id = ?";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$gig_id]);	
}

function getNumOfPendingInterviews($pdo, $user_id) {
	$sql = "SELECT 
				COUNT(gig_interview_id) AS pendingCount 
			FROM gig_interviews 
			WHERE status = 'Pending' AND freelancer_id = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$user_id]);	
	return $stmt->fetch();
}

function getPendingInterviewsCount($pdo, $freelancer_id) {
    $sql = "SELECT COUNT(*) as count 
            FROM gig_interviews 
            WHERE freelancer_id = ? 
            AND status = 'pending'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$freelancer_id]);
    $result = $stmt->fetch();
    return $result['count'];
}

function getAllInterviews($pdo, $freelancer_id) {
  $sql = "SELECT 
            gi.gig_interview_id,
            g.gig_title as title,
            g.gig_description as description,
            u.username,
            gp.gig_proposal_description as proposal_description,
            gp.gig_proposal_id as proposal_id,
            gi.time_start,
            gi.time_end,
            gi.status as interview_status,
            gp.date_added
          FROM gig_interviews gi
          JOIN gigs g ON gi.gig_id = g.gig_id
          JOIN upwork_users u ON g.user_id = u.user_id
          JOIN gig_proposals gp ON gi.gig_id = gp.gig_id AND gi.freelancer_id = gp.user_id
          WHERE gi.freelancer_id = :freelancer_id
          ORDER BY gi.time_start DESC";
  
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['freelancer_id' => $freelancer_id]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}