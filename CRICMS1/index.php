<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>City Resident ID Card Management System</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php';?>
  <main>
    <section class="hero py-5">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h1>Welcome to the City Resident ID Card Management System</h1>
            <p class="lead">Manage your city resident ID cards with ease.</p>
            <a href="resident/resident_register.php" class="btn btn-primary btn-lg">Register for ID Card</a>
            <a href="login.php" class="btn btn-primary btn-lg">Login</a>
          </div>
          <div class="col-md-6">
            <img src="images/id-card.jpg" alt="ID Card" class="img-fluid">
          </div>
        </div>
      </div>
    </section>

    <section class="features py-5 bg-light">
      <div class="container">
        <h2 class="text-center mb-5">Key Features</h2>
        <div class="row">
          <div class="col-md-4">
            <div class="feature-box text-center">
              <i class="fas fa-user-plus fa-3x mb-3"></i>
              <h4>Easy Registration</h4>
              <p>Residents can easily register for their ID cards online.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="feature-box text-center">
              <i class="fas fa-id-card fa-3x mb-3"></i>
              <h4>ID Card Management</h4>
              <p>Admins and moderators can manage resident ID cards.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="feature-box text-center">
              <i class="fas fa-city fa-3x mb-3"></i>
              <h4>City Component Management</h4>
              <p>Admins can manage city components like regions, zones, and kebeles.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include 'includes/footer.php';?>


  <!-- Bootstrap JavaScript -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <!-- Custom JavaScript -->
  <script src="js/script.js"></script>
</body>
</html>