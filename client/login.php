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
        <div class="col-md-6 col-lg-5">
          <div class="card shadow">
            <div class="card-body p-5">
              <div class="text-center mb-4">
                <i class="fas fa-briefcase fa-3x text-primary mb-3"></i>
                <h2 class="font-weight-bold">Client Login</h2>
                <p class="text-muted">Welcome back! Please login to your account.</p>
              </div>

              <form class="loginForm">
                <div class="form-group">
                  <label for="username">Username</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fas fa-user"></i>
                      </span>
                    </div>
                    <input type="text" class="username form-control" placeholder="Enter your username">
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
                    <input type="password" class="password form-control" placeholder="Enter your password">
                  </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block mt-4">
                  <i class="fas fa-sign-in-alt mr-2"></i>Login
                </button>
              </form>

              <div class="text-center mt-4">
                <p class="mb-0">Don't have an account yet? <a href="register.php" class="text-primary">Register here!</a></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      $('.loginForm').on('submit', function(event) {
        event.preventDefault();

        var formData = {
          username: $(this).find('.username').val(),
          password: $(this).find('.password').val(),
          loginUserBtn: 1
        };

        if (formData.username && formData.password) {
          $.ajax({
            type: "POST",
            url: "core/handleForms.php",
            data: formData,
            success: function(data) {
              if (data == 1) {
                location.href = "index.php";
              } else {
                alert("Invalid username or password!");
              }
            }
          });
        } else {
          alert("Please fill in all fields!");
        }
      });
    </script>
  </body>
</html>
