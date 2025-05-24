<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username']) || $_SESSION['is_client'] == 1) {
  header("Location: login.php");
}

// Get pending interviews count
$pendingInterviews = getPendingInterviewsCount($pdo, $_SESSION['user_id']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <i class="fas fa-user-tie"></i>Freelancer Dashboard
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <i class="fas fa-home"></i>Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="interviews.php">
            <i class="fas fa-calendar-alt"></i>Interviews
            <?php if ($pendingInterviews > 0): ?>
              <span class="badge badge-light"><?php echo $pendingInterviews; ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="core/handleForms.php?logoutUserBtn=1">
            <i class="fas fa-sign-out-alt"></i>Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
.navbar {
  box-shadow: 0 2px 4px rgba(0,0,0,.1);
}

.navbar-brand {
  font-weight: 600;
  font-size: 1.2rem;
}

.navbar-brand i {
  margin-right: 8px;
}

.nav-link {
  font-weight: 500;
  padding: 0.5rem 1rem !important;
  transition: all 0.3s ease;
}

.nav-link i {
  margin-right: 6px;
}

.nav-link:hover {
  background-color: rgba(255,255,255,.1);
  border-radius: 4px;
}

.nav-item.active .nav-link {
  background-color: rgba(255,255,255,.1);
  border-radius: 4px;
}

.badge {
  margin-left: 4px;
  font-size: 0.75rem;
  padding: 0.25em 0.6em;
}

@media (max-width: 991.98px) {
  .navbar-collapse {
    background-color: #0056b3;
    padding: 1rem;
    border-radius: 0 0 4px 4px;
    margin-top: 0.5rem;
  }
  
  .nav-link {
    padding: 0.75rem 1rem !important;
  }
  
  .nav-link:hover {
    background-color: rgba(255,255,255,.1);
  }
}
</style>

<script>
$(document).ready(function() {
    // Function to handle navbar state
    function handleNavbar() {
        var width = $(window).width();
        var $navbar = $('#navbarNav');
        
        if (width >= 992) {
            $navbar.addClass('show');
            $navbar.removeClass('collapsing');
        }
    }

    // Initial call
    handleNavbar();

    // Handle window resize
    $(window).resize(function() {
        handleNavbar();
    });

    // Handle navbar toggle click
    $('.navbar-toggler').on('click', function() {
        if ($(window).width() < 992) {
            $('#navbarNav').collapse('toggle');
        }
    });

    // Add active class to current page link
    var currentPage = window.location.pathname.split('/').pop();
    $('.nav-link').each(function() {
        var href = $(this).attr('href');
        if (href === currentPage) {
            $(this).addClass('active');
            $(this).closest('.nav-item').addClass('active');
        }
    });

    // Prevent navbar collapse on link click for desktop
    $('.nav-link').on('click', function() {
        if ($(window).width() >= 992) {
            return true;
        }
    });
});
</script>