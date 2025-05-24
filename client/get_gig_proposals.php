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
              <h1 class="text-center mb-4">Gig Proposals</h1>
              <p class="text-center text-muted mb-4">Review proposals and schedule interviews for your gig.</p>
            </div>
          </div>

          <?php $getGigById = getGigById($pdo, $_GET['gig_id']); ?>
          <div class="card shadow-sm mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h4 class="mb-0"><?php echo htmlspecialchars($getGigById['gig_title']); ?></h4>
              <span class="badge badge-primary"><?php echo date('F j, Y', strtotime($getGigById['date_added'])); ?></span>
            </div>
            <div class="card-body">
              <p class="mb-3"><?php echo nl2br(htmlspecialchars($getGigById['gig_description'])); ?></p>
              <small class="text-muted">
                <i class="fas fa-user mr-1"></i>Posted by <?php echo htmlspecialchars($_SESSION['username']); ?>
              </small>
            </div>
          </div>

          <?php $getProposalsByGigId = getProposalsByGigId($pdo, $_GET['gig_id']); ?>
          <?php if (!empty($getProposalsByGigId)) { ?>
            <?php foreach ($getProposalsByGigId as $row) { ?>
              <div class="card shadow-sm mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h4 class="mb-0"><?php echo htmlspecialchars($row['last_name'] . ", " . $row['first_name']); ?></h4>
                  <span class="badge badge-info"><?php echo date('F j, Y', strtotime($row['date_added'])); ?></span>
                </div>
                <div class="card-body">
                  <p class="mb-3"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                  <div class="d-flex justify-content-between align-items-center">
                    <button class="btn btn-primary scheduleInterviewBtn" data-toggle="modal" data-target="#interviewModal" 
                            data-freelancer-id="<?php echo $row['user_id']; ?>" 
                            data-gig-id="<?php echo $_GET['gig_id']; ?>">
                      <i class="fas fa-calendar-check mr-1"></i>Schedule Interview
                    </button>
                  </div>
                </div>
              </div>
            <?php } ?>
          <?php } else { ?>
            <div class="text-center mt-5">
              <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
              <h3>No proposals yet</h3>
              <p class="text-muted">Proposals for this gig will appear here.</p>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <!-- Interview Modal -->
    <div class="modal fade" id="interviewModal" tabindex="-1" role="dialog" aria-labelledby="interviewModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="interviewModalLabel">Schedule Interview</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="interviewForm">
              <input type="hidden" id="freelancerId" name="freelancer_id">
              <input type="hidden" id="gigId" name="gig_id">
              <div class="form-group">
                <label for="timeStart">Start Time</label>
                <input type="datetime-local" class="form-control" id="timeStart" required>
                <small class="text-muted">Please select a future date and time</small>
              </div>
              <div class="form-group">
                <label for="timeEnd">End Time</label>
                <input type="datetime-local" class="form-control" id="timeEnd" required>
                <small class="text-muted">End time must be after start time</small>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="scheduleInterview">Schedule</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

    <script>
      $(document).ready(function() {
        // Set minimum date to now for both date inputs
        var now = new Date();
        var year = now.getFullYear();
        var month = String(now.getMonth() + 1).padStart(2, '0');
        var day = String(now.getDate()).padStart(2, '0');
        var hours = String(now.getHours()).padStart(2, '0');
        var minutes = String(now.getMinutes()).padStart(2, '0');
        var minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
        
        $('input[type="datetime-local"]').attr('min', minDateTime);

        $('.scheduleInterviewBtn').on('click', function() {
          var freelancerId = $(this).data('freelancer-id');
          var gigId = $(this).data('gig-id');
          $('#freelancerId').val(freelancerId);
          $('#gigId').val(gigId);
          $('#timeStart').val('');
          $('#timeEnd').val('');
        });

        $('#scheduleInterview').on('click', function() {
          var formData = {
            gig_id: $('#gigId').val(),
            freelancer_id: $('#freelancerId').val(),
            time_start: $('#timeStart').val(),
            time_end: $('#timeEnd').val(),
            insertNewGigInterview: 1
          };

          if (!formData.time_start || !formData.time_end) {
            alert('Please fill in both start and end times');
            return;
          }

          // Validate dates
          var startTime = new Date(formData.time_start);
          var endTime = new Date(formData.time_end);
          var now = new Date();

          if (startTime < now) {
            alert('Start time cannot be in the past');
            return;
          }

          if (endTime <= startTime) {
            alert('End time must be after start time');
            return;
          }

          $.ajax({
            type: "POST",
            url: "core/handleForms.php",
            data: formData,
            success: function(response) {
              try {
                var result = JSON.parse(response);
                if (result.status === '200') {
                  alert(result.message);
                  location.reload();
                } else {
                  alert(result.message || 'Error scheduling interview');
                }
              } catch (e) {
                console.error('Error parsing response:', e);
                console.error('Response:', response);
                alert('Error scheduling interview. Please try again.');
              }
            },
            error: function(xhr, status, error) {
              console.error('AJAX error:', error);
              console.error('Status:', status);
              console.error('Response:', xhr.responseText);
              alert('Error scheduling interview: ' + error);
            }
          });
        });
      });
    </script>
  </body>
</html>
