<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.5.0/remixicon.css">
   <link rel="shortcut icon" href="asset/images/ingat.ico">
   <link rel="stylesheet" href="asset/css/hfs.css">

</head>
<body>
   <!-- Header -->
   <header class="header">
      <div class="logo">
         <img src="../img/logo.png" alt="INGAT Logo">
         <span>INGAT</span>
      </div>
      <div class="settings" onclick="toggleDropdown()">
         <a href="#">
            <i class="ri-settings-2-line"></i>
         </a>
         <!-- Dropdown Menu -->
         <div class="dropdown-menu" id="dropdownMenu">
            <a href="change-password.php">
                <i class="ri-lock-password-line"></i> Change Password
            </a>
            <a href="logout.php">
                <i class="ri-logout-box-line"></i> Logout
            </a>
         </div>
      </div>
   </header>

   <script>
      // Toggle dropdown visibility
      function toggleDropdown() {
         const dropdown = document.getElementById('dropdownMenu');
         dropdown.classList.toggle('show');
      }

      // Close the dropdown if clicked outside of it
      window.onclick = function(event) {
         const dropdown = document.getElementById('dropdownMenu');
         if (!event.target.matches('.settings, .settings *')) {
            dropdown.classList.remove('show');
         }
      }
   </script>
</body>
</html>
