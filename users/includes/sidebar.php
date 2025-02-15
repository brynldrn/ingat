<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.5.0/remixicon.css">
   <link rel="stylesheet" href="asset/css/hfs.css">
   <!-- App icon -->
   <link rel="shortcut icon" href="asset/images/ingat.ico">
</head>
<body>
   <!-- Sidebar for Desktop -->
   <div class="sidebar desktop">
      <!-- Profile -->
      <div class="profile">
         <?php
            $query = mysqli_query($conn, "SELECT firstname, middlename, lastname, username, user_image FROM users WHERE user_email='" . $_SESSION['login'] . "'");
            while ($row = mysqli_fetch_array($query)) {
         ?>
         <img src="<?php echo !empty($row['user_image']) ? $row['user_image'] : '../uploads/profile.jpg'; ?>" alt="Profile Image">
         <div>
            <div class="name">
               <?php echo htmlentities($row['firstname'] . ' ' . ($row['middlename'] ? $row['middlename'] . ' ' : '') . $row['lastname']); ?>
            </div>
            <div class="username">@<?php echo htmlentities($row['username']); ?></div>
         </div>
         <?php } ?>
      </div>

      <!-- Navigation Links -->
      <div class="nav-list">
         <a href="dashboard.php">
            <i class="ri-home-line"></i> Home
         </a>
         <a href="profile.php">
            <i class="ri-user-line"></i> Profile
         </a>
         <a href="register-complaint.php">
            <i class="ri-alert-line"></i> Report
         </a>
         <a href="status.php">
            <i class="ri-bar-chart-line"></i> Status
         </a>
         <a href="#">
            <i class="ri-information-line"></i> About Us
         </a>
         <a href="logout.php">
            <i class="ri-logout-box-r-line"></i> Logout
         </a>
      </div>
   </div>

   <!-- Bottom Navigation for Mobile -->
   <div class="bottom-nav mobile">
      <a href="dashboard.php">
         <i class="ri-home-line"></i>
         <span>Home</span>
      </a>
      <a href="status.php">
         <i class="ri-bar-chart-line"></i>
         <span>Status</span>
      </a>
      <a href="register-complaint.php" class="report">
         <i class="ri-alert-line"></i>
         <span>Report</span>
      </a>
      <a href="profile.php">
         <i class="ri-user-line"></i>
         <span>Profile</span>
      </a>
      <a href="logout.php">
         <i class="ri-logout-box-r-line"></i>
         <span>Logout</span>
      </a>
   </div>

   <style>
      /* Bottom Navigation Styling */
      .bottom-nav {
         display: none; /* Hidden on desktop */
         position: fixed;
         bottom: 0;
         left: 0;
         width: 100%;
         background-color: #021526;
         z-index: 10000;
         padding: 10px 0;
         box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
      }

      .bottom-nav a {
         flex: 1;
         text-align: center;
         color: rgba(255, 255, 255, 0.73);
         text-decoration: none;
         font-size: 14px;
         display: flex;
         flex-direction: column;
         align-items: center;
         justify-content: center;
      }

      .bottom-nav a i {
         font-size: 20px;
         margin-bottom: 5px;
      }

      .bottom-nav a.report i {
         color: #ff6b6b; /* Highlight the report icon */
      }

      .bottom-nav a:hover {
         color: #fff;
      }

      /* Show bottom navigation on mobile */
      @media (max-width: 768px) {
         .bottom-nav {
            display: flex;
            justify-content: space-around;
         }

         .sidebar.desktop {
            display: none;
         }
      }
   </style>

   <script>
      // JavaScript to toggle between desktop and mobile navigation
      function adjustNavigation() {
         if (window.innerWidth <= 768) {
            document.querySelector('.sidebar.desktop').style.display = 'none';
            document.querySelector('.bottom-nav.mobile').style.display = 'flex';
         } else {
            document.querySelector('.sidebar.desktop').style.display = 'flex';
            document.querySelector('.bottom-nav.mobile').style.display = 'none';
         }
      }

      window.addEventListener('resize', adjustNavigation);
      window.addEventListener('load', adjustNavigation);
   </script>
</body>
</html>