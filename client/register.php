<?php require_once 'core/dbConfig.php'; ?>
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
    <div class="container">
      <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-8 col-lg-6">
          <div class="card shadow">
            <div class="card-body p-5">
              <div class="text-center mb-4">
                <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                <h2 class="font-weight-bold">Client Registration</h2>
                <p class="text-muted">Create your account to get started.</p>
              </div>

              <form class="registerForm">
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="first_name">First Name</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="fas fa-user"></i>
                        </span>
                      </div>
                      <input type="text" class="first_name form-control" placeholder="Enter first name">
                    </div>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="last_name">Last Name</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="fas fa-user"></i>
                        </span>
                      </div>
                      <input type="text" class="last_name form-control" placeholder="Enter last name">
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="username">Username</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fas fa-at"></i>
                      </span>
                    </div>
                    <input type="text" class="username form-control" placeholder="Choose a username">
                  </div>
                </div>

                <div class="form-group">
                  <label for="password">Password</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                      </span>
                    </div>
                    <input type="password" class="password form-control" placeholder="Create a password">
                  </div>
                </div>

                <div class="form-group">
                  <label for="confirm_password">Confirm Password</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                      </span>
                    </div>
                    <input type="password" class="confirm_password form-control" placeholder="Confirm your password">
                  </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block mt-4">
                  <i class="fas fa-user-plus mr-2"></i>Register
                </button>
              </form>

              <div class="text-center mt-4">
                <p class="mb-0">Already have an account? <a href="login.php" class="text-primary">Login here!</a></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      $('.registerForm').on('submit', function(event) {
        event.preventDefault();

        var formData = {
          username: $(this).find('.username').val(),
          first_name: $(this).find('.first_name').val(),
          last_name: $(this).find('.last_name').val(),
          password: $(this).find('.password').val(),
          confirm_password: $(this).find('.confirm_password').val(),
          insertNewUserBtn: 1
        };

        if (formData.username && formData.first_name && formData.last_name && formData.password && formData.confirm_password) {
          $.ajax({
            type: "POST",
            url: "core/handleForms.php",
            data: formData,
            dataType: 'json',
            success: function(response) {
              if (response.status === '200') {
                alert(response.message);
                location.href = "login.php";
              } else {
                alert(response.message);
              }
            },
            error: function() {
              alert("An error occurred. Please try again.");
            }
          });
        } else {
          alert("Please fill in all fields!");
        }
      });
    </script>
  </body>
</html>
