<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username']) || $_SESSION['is_client'] != 1) {
  header("Location: login.php");
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <i class="fas fa-briefcase"></i>Client Dashboard
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
          <a class="nav-link" href="gigs_posted.php">
            <i class="fas fa-list"></i>Gigs Posted
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="proposals.php">
            <i class="fas fa-file-alt"></i>Proposals
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