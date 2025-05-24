<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_client'] == 1) {
  header("Location: ../client/index.php");
}

// Get pending interviews count
$pendingInterviews = getPendingInterviewsCount($pdo, $_SESSION['user_id']);
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
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="card shadow-sm mt-4">
            <div class="card-body">
              <h1 class="text-center mb-4">Interviews</h1>
              <p class="text-center text-muted mb-4">Manage your scheduled interviews and proposals.</p>
            </div>
          </div>

          <?php $getAllInterviews = getAllInterviews($pdo, $_SESSION['user_id']); ?>
          <?php if (!empty($getAllInterviews)) { ?>
            <?php foreach ($getAllInterviews as $row) { ?>
              <div class="card shadow-sm mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h4 class="mb-0"><?php echo $row['title']; ?></h4>
                  <?php if ($row['interview_status'] == 0) { ?>
                    <span class="badge badge-warning">Pending</span>
                  <?php } else { ?>
                    <span class="badge badge-success">Scheduled</span>
                  <?php } ?>
                </div>
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                      <h5 class="mb-2">Client: <?php echo $row['username']; ?></h5>
                      <p class="mb-0"><?php echo $row['description']; ?></p>
                      <?php if ($row['time_start'] && $row['time_end']) { ?>
                        <div class="mt-2">
                          <small class="text-muted">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            <?php 
                              $start = new DateTime($row['time_start']);
                              $end = new DateTime($row['time_end']);
                              echo $start->format('F j, Y g:i A') . ' - ' . $end->format('g:i A');
                            ?>
                          </small>
                        </div>
                      <?php } ?>
                    </div>
                    <div class="text-right">
                      <?php if ($row['interview_status'] == 0) { ?>
                        <button class="btn btn-success acceptInterviewBtn" data-proposal-id="<?php echo $row['proposal_id']; ?>">
                          <i class="fas fa-check mr-1"></i>Accept
                        </button>
                        <button class="btn btn-danger rejectInterviewBtn" data-proposal-id="<?php echo $row['proposal_id']; ?>">
                          <i class="fas fa-times mr-1"></i>Reject
                        </button>
                      <?php } else { ?>
                        <span class="badge badge-info">
                          <i class="fas fa-calendar-check mr-1"></i>Interview Scheduled
                        </span>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                      <i class="fas fa-clock mr-1"></i>Posted on <?php echo $row['date_added']; ?>
                    </small>
                    <a href="view_proposal.php?proposal_id=<?php echo $row['proposal_id']; ?>" class="btn btn-outline-primary">
                      <i class="fas fa-eye mr-1"></i>View Details
                    </a>
                  </div>
                </div>
              </div>
            <?php } ?>
          <?php } else { ?>
            <div class="text-center mt-5">
              <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
              <h3>No interviews scheduled</h3>
              <p class="text-muted">Your scheduled interviews will appear here.</p>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <script>
      $('.acceptInterviewBtn').on('click', function() {
        var proposalId = $(this).data('proposal-id');
        if (confirm('Are you sure you want to accept this interview?')) {
          $.ajax({
            type: 'POST',
            url: 'core/handleForms.php',
            data: {
              proposal_id: proposalId,
              acceptInterview: 1
            },
            success: function(response) {
              location.reload();
            }
          });
        }
      });

      $('.rejectInterviewBtn').on('click', function() {
        var proposalId = $(this).data('proposal-id');
        if (confirm('Are you sure you want to reject this interview?')) {
          $.ajax({
            type: 'POST',
            url: 'core/handleForms.php',
            data: {
              proposal_id: proposalId,
              rejectInterview: 1
            },
            success: function(response) {
              location.reload();
            }
          });
        }
      });
    </script>
  </body>
</html>
