<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_client'] == 0) {
  header("Location: ../freelancer/index.php");
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
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="card shadow-sm mt-4">
            <div class="card-body">
              <h1 class="text-center mb-4">Proposals</h1>
              <p class="text-center text-muted mb-4">Review and manage proposals for your gigs.</p>
            </div>
          </div>

          <?php $getAllProposals = getAllProposals($pdo, $_SESSION['user_id']); ?>
          <?php if (!empty($getAllProposals)) { ?>
            <?php foreach ($getAllProposals as $row) { ?>
              <div class="card shadow-sm mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h4 class="mb-0">
                    <a href="get_gig_proposals.php?gig_id=<?php echo $row['gig_id']; ?>" class="text-decoration-none">
                      <?php echo htmlspecialchars($row['title']); ?>
                    </a>
                  </h4>
                  <span class="badge badge-primary"><?php echo date('F j, Y', strtotime($row['date_added'])); ?></span>
                </div>
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                      <h5 class="mb-2">Proposal from: <?php echo htmlspecialchars($row['username']); ?></h5>
                      <p class="mb-0"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                      <?php if ($row['interview_status'] == 1 && $row['time_start'] && $row['time_end']) { ?>
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
                        <a href="get_gig_proposals.php?gig_id=<?php echo $row['gig_id']; ?>" class="btn btn-primary">
                          <i class="fas fa-calendar-check mr-1"></i>Schedule Interview
                        </a>
                      <?php } else { ?>
                        <span class="badge badge-info">
                          <i class="fas fa-calendar-check mr-1"></i>Interview Scheduled
                        </span>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                      <i class="fas fa-clock mr-1"></i>Posted on <?php echo date('F j, Y g:i A', strtotime($row['date_added'])); ?>
                    </small>
                    <a href="get_gig_proposals.php?gig_id=<?php echo $row['gig_id']; ?>" class="btn btn-outline-primary">
                      <i class="fas fa-eye mr-1"></i>View Details
                    </a>
                  </div>
                </div>
              </div>
            <?php } ?>
          <?php } else { ?>
            <div class="text-center mt-5">
              <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
              <h3>No proposals yet</h3>
              <p class="text-muted">Proposals for your gigs will appear here.</p>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
  </body>
</html> 