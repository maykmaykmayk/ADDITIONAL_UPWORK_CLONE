<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_client'] == 1) {
  header("Location: ../client/index.php");
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
              <h1 class="text-center mb-4">Welcome, <span class="text-primary"><?php echo $_SESSION['username']; ?></span>!</h1>
              <p class="text-center text-muted mb-4">Browse available gigs and submit your proposals.</p>
            </div>
          </div>

          <?php $getAllGigs = getAllGigs($pdo); ?>
          <?php if (!empty($getAllGigs)) { ?>
            <?php foreach ($getAllGigs as $row) { ?>
              <div class="card shadow-sm mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h4 class="mb-0"><?php echo $row['title']; ?></h4>
                  <span class="badge badge-primary"><?php echo $row['date_added']; ?></span>
                </div>
                <div class="card-body">
                  <p class="mb-3"><?php echo $row['description']; ?></p>
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                      <i class="fas fa-user mr-1"></i>Posted by <?php echo $row['username']; ?>
                    </small>
                    <button class="btn btn-primary submitProposalBtn" data-gig-id="<?php echo $row['gig_id']; ?>">
                      <i class="fas fa-paper-plane mr-1"></i>Submit Proposal
                    </button>
                  </div>
                </div>
              </div>
            <?php } ?>
          <?php } else { ?>
            <div class="text-center mt-5">
              <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
              <h3>No gigs available</h3>
              <p class="text-muted">Check back later for new opportunities!</p>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <!-- Proposal Modal -->
    <div class="modal fade" id="proposalModal" tabindex="-1" role="dialog" aria-labelledby="proposalModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="proposalModalLabel">Submit Proposal</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="proposalForm">
              <input type="hidden" id="gigId" name="gig_id">
              <div class="form-group">
                <label for="proposalDescription">Your Proposal</label>
                <textarea class="form-control" id="proposalDescription" rows="4" required></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="submitProposal">Submit</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

    <script>
      $(document).ready(function() {
        $('.submitProposalBtn').on('click', function() {
          var gigId = $(this).data('gig-id');
          $('#gigId').val(gigId);
          $('#proposalDescription').val(''); // Clear previous input
          $('#proposalModal').modal('show');
        });

        $('#submitProposal').on('click', function() {
          var formData = {
            gig_id: $('#gigId').val(),
            gig_proposal_description: $('#proposalDescription').val(),
            newGigProposal: 1
          };

          if (formData.gig_proposal_description.trim() === '') {
            alert('Please enter your proposal description');
            return;
          }

          $.ajax({
            type: 'POST',
            url: 'core/handleForms.php',
            data: formData,
            success: function(response) {
              try {
                var result = JSON.parse(response);
                if (result.status === '200') {
                  alert('Proposal submitted successfully!');
                  $('#proposalModal').modal('hide');
                  location.reload();
                } else {
                  alert(result.message || 'Error submitting proposal');
                }
              } catch (e) {
                alert('Error submitting proposal. Please try again.');
              }
            },
            error: function(xhr, status, error) {
              alert('Error submitting proposal: ' + error);
            }
          });
        });
      });
    </script>
  </body>
</html>
