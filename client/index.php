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
              <h1 class="text-center mb-4">Welcome, <span class="text-primary"><?php echo $_SESSION['username']; ?></span>!</h1>
              <p class="text-center text-muted mb-4">Here are all the available gigs. Create a new one or browse existing ones.</p>
              <div class="text-center mb-4">
                <button class="btn btn-primary showCreateGigForm">
                  <i class="fas fa-plus mr-2"></i>Create New Gig
                </button>
              </div>
            </div>
          </div>

          <form class="createNewGig d-none card shadow-sm mt-4">
            <div class="card-body">
              <h4 class="mb-4">Create New Gig</h4>
              <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="title form-control" placeholder="Enter gig title">
              </div>
              <div class="form-group">
                <label for="description">Description</label>
                <textarea class="description form-control" rows="4" placeholder="Enter gig description"></textarea>
                <button type="submit" class="btn btn-primary float-right mt-4">
                  <i class="fas fa-paper-plane mr-2"></i>Submit
                </button>
              </div>
            </div>
          </form>

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
                      <i class="fas fa-user mr-1"></i><?php echo $row['username']; ?>
                    </small>
                    <a href="get_gig_proposals.php?gig_id=<?php echo $row['gig_id']; ?>" class="btn btn-outline-primary btn-sm">
                      <i class="fas fa-eye mr-1"></i>View Proposals
                    </a>
                  </div>
                </div>
              </div>
            <?php } ?>
          <?php } else { ?>
            <div class="text-center mt-5">
              <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
              <h3>No gigs available</h3>
              <p class="text-muted">Be the first to create a gig!</p>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <script>
      $('.showCreateGigForm').on('click', function() {
        $('.createNewGig').toggleClass('d-none');
      });

      $('.createNewGig').on('submit', function(event) {
        event.preventDefault();

        var formData = {
          title: $(this).find('.title').val(),
          description: $(this).find('.description').val(),
          createNewGig: 1
        };

        if (formData.title && formData.description) {
          $.ajax({
            type: "POST",
            url: "core/handleForms.php",
            data: formData,
            success: function(data) {
              location.reload();
            }
          });
        } else {
          alert("Please fill in all fields!");
        }
      });
    </script>
  </body>
</html>
