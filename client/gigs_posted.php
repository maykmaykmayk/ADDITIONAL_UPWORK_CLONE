<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_client'] == 0) {
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
              <h1 class="text-center mb-4">Gigs Posted</h1>
              <p class="text-center text-muted mb-4">Double click on a gig to edit its details.</p>
            </div>
          </div>

          <?php $getAllGigsByUserId = getAllGigsByUserId($pdo, $_SESSION['user_id']); ?>
          <?php if (!empty($getAllGigsByUserId)) { ?>
            <?php foreach ($getAllGigsByUserId as $row) { ?>
              <div class="gigContainer card shadow-sm mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h4 class="mb-0"><?php echo $row['title']; ?></h4>
                  <button class="deleteGigBtn btn btn-danger">
                    <i class="fas fa-trash-alt mr-1"></i>Delete
                  </button>
                </div>
                <div class="card-body">
                  <p class="mb-3"><?php echo $row['description']; ?></p>
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <small class="text-muted">
                        <i class="fas fa-calendar-alt mr-1"></i><?php echo $row['date_added']; ?>
                      </small>
                      <small class="text-muted ml-3">
                        <i class="fas fa-user mr-1"></i><?php echo $row['username']; ?>
                      </small>
                    </div>
                    <a href="get_gig_proposals.php?gig_id=<?php echo $row['gig_id']; ?>" class="btn btn-outline-primary">
                      <i class="fas fa-eye mr-1"></i>View Proposals
                    </a>
                  </div>
                  <form class="editGigForm mt-4 d-none">
                    <div class="form-group">
                      <input type="hidden" value="<?php echo $row['gig_id']; ?>" class="form-control gig_id" required>
                      <label>Title</label>
                      <input type="text" value="<?php echo $row['title']; ?>" class="form-control title" required>
                    </div>
                    <div class="form-group">
                      <label>Description</label>
                      <textarea class="form-control description" rows="4" required><?php echo $row['description']; ?></textarea>
                      <button type="submit" class="btn btn-primary float-right mt-3">
                        <i class="fas fa-save mr-1"></i>Save Changes
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            <?php } ?>
          <?php } else { ?>
            <div class="text-center mt-5">
              <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
              <h3>No gigs posted yet</h3>
              <p class="text-muted">Create your first gig to get started!</p>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <script>
      $('.gigContainer').on('dblclick', function (event) {
        var editForm = $(this).find('.editGigForm');
        editForm.toggleClass('d-none');
      });

      $('.deleteGigBtn').on('click', function (event) {
        var formData = {
          gig_id: $(this).closest('.gigContainer').attr('gig_id'),
          deleteGig: 1 
        };
        if (formData.gig_id != "") {
          if (confirm("Are you sure you want to delete this gig?")) { 
            $.ajax({
              type: "POST",
              url: "core/handleForms.php",
              data: formData,
              success: function (data) {
                location.reload();              
              }
            });
          }
        } else {
          alert("An error occurred with your input");
        }
      });

      $('.editGigForm').on('submit', function (event) {
        event.preventDefault();
        var formData = {
          gig_id: $(this).find('.gig_id').val(),
          title: $(this).find('.title').val(),
          description: $(this).find('.description').val(),
          updateGig: 1 
        };
        if (formData.gig_id != "" && formData.title != "" && formData.description != "") {
          $.ajax({
            type: "POST",
            url: "core/handleForms.php",
            data: formData,
            success: function (data) {
              location.reload();              
            }
          });            
        } else {
          alert("Make sure the input fields are not empty!");
        }
      });
    </script>
  </body>
</html>
