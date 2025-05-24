<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_client'] == 1) {
  header("Location: ../client/index.php");
}

// Get proposal ID from URL
$proposal_id = isset($_GET['proposal_id']) ? $_GET['proposal_id'] : null;

if (!$proposal_id) {
  header("Location: index.php");
  exit;
}

// Get proposal details
$sql = "SELECT 
          gp.gig_proposal_id,
          gp.gig_proposal_description,
          gp.date_added,
          g.gig_title,
          g.gig_description,
          u.username as client_name
        FROM gig_proposals gp
        JOIN gigs g ON gp.gig_id = g.gig_id
        JOIN upwork_users u ON g.user_id = u.user_id
        WHERE gp.gig_proposal_id = ? AND gp.user_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$proposal_id, $_SESSION['user_id']]);
$proposal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$proposal) {
  header("Location: index.php");
  exit;
}
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="card shadow-sm mt-4">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">Proposal Details</h1>
                <a href="index.php" class="btn btn-outline-primary">
                  <i class="fas fa-arrow-left mr-1"></i>Back to Gigs
                </a>
              </div>
            </div>
          </div>

          <div class="card shadow-sm mt-4">
            <div class="card-header">
              <h4 class="mb-0"><?php echo htmlspecialchars($proposal['gig_title']); ?></h4>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <h5 class="text-muted mb-3">Gig Description</h5>
                <p><?php echo nl2br(htmlspecialchars($proposal['gig_description'])); ?></p>
              </div>

              <div class="mb-4">
                <h5 class="text-muted mb-3">Your Proposal</h5>
                <p><?php echo nl2br(htmlspecialchars($proposal['gig_proposal_description'])); ?></p>
              </div>

              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <small class="text-muted">
                    <i class="fas fa-user mr-1"></i>Client: <?php echo htmlspecialchars($proposal['client_name']); ?>
                  </small>
                  <br>
                  <small class="text-muted">
                    <i class="fas fa-clock mr-1"></i>Submitted on: <?php echo date('F j, Y', strtotime($proposal['date_added'])); ?>
                  </small>
                </div>
                <div>
                  <button class="btn btn-outline-danger deleteProposalBtn" data-proposal-id="<?php echo $proposal['gig_proposal_id']; ?>">
                    <i class="fas fa-trash-alt mr-1"></i>Delete Proposal
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      $('.deleteProposalBtn').on('click', function() {
        var proposalId = $(this).data('proposal-id');
        if (confirm('Are you sure you want to delete this proposal? This action cannot be undone.')) {
          $.ajax({
            type: 'POST',
            url: 'core/handleForms.php',
            data: {
              proposal_id: proposalId,
              deleteProposal: 1
            },
            success: function(response) {
              window.location.href = 'index.php';
            }
          });
        }
      });
    </script>
  </body>
</html> 