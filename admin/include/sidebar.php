<?php include('include/config.php');?>
 
 <!-- App Menu Start -->
 <div class="app-sidebar">
             <!-- Sidebar Logo -->
             <div class="logo-box">
    <a href="dashboard.php" class="logo-dark">
        <img src="assets/images/logo(1).png" class="logo-sm" alt="logo sm">
        <img src="assets/images/logodark.png" class="logo-lg" alt="logo dark" style="width: 150px; height: auto;">
    </a>

    <a href="dashboard.php" class="logo-light">
        <img src="assets/images/logo(1).png" class="logo-sm" alt="logo sm">
        <img src="assets/images/logolight.png" class="logo-lg" alt="logo light" style="width: 150px; height: auto;">
    </a>
</div>


             <div class="scrollbar" data-simplebar>

                  <ul class="navbar-nav" id="navbar-nav">

                       <li class="menu-title">Menu</li>

                       <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                 <span class="nav-icon">
                                      <iconify-icon icon="mingcute:home-3-line"></iconify-icon>
                                 </span>
                                 <span class="nav-text"> Dashboard </span>
                                 <span class="badge bg-primary badge-pill text-end"></span>
                            </a>
                       </li>

                       


                       <li class="nav-item">
    <a class="nav-link menu-arrow" href="#sidebarComplaints" data-bs-toggle="collapse" role="button"
        aria-expanded="false" aria-controls="sidebarComplaints">
        <span class="nav-icon">
            <iconify-icon icon="bx:bell"></iconify-icon>
        </span>
        <span class="nav-text">Complaints</span>
    </a>
    <div class="collapse" id="sidebarComplaints">
        <ul class="nav sub-navbar-nav">
            <?php
            // Define statuses
            $statuses = [
                'New' => ["status IS NULL", "bx:time"],
                'In Progress' => ["status = 'In Progress'", "bx:loader"],
                'Solved' => ["status = 'Solved'", "bx:check-circle"]
            ];

            // Loop through statuses and generate menu items
            foreach ($statuses as $statusLabel => $statusDetails) {
                $statusCondition = $statusDetails[0];
                $icon = $statusDetails[1];

                // Query for complaint count
                $query = "SELECT COUNT(*) AS count FROM tblcomplaints WHERE $statusCondition";
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_assoc($result);
                $count = $row['count'] ?? 0;

                // Map status to corresponding links
                $links = [
                    'New' => 'notprocess-complaint.php',
                    'In Progress' => 'inprocess-complaint.php',
                    'Solved' => 'closed-complaint.php'
                ];
                $link = $links[$statusLabel];

                // Map status to badge colors
                $badgeColors = [
                    'New' => 'orange',
                    'In Progress' => 'blue',
                    'Solved' => 'green'
                ];
                $badgeColor = $badgeColors[$statusLabel];
                ?>
                <li class="sub-nav-item">
                    <a class="sub-nav-link" href="<?php echo $link; ?>">
                        <i class="iconify <?php echo $icon; ?>"></i>
                        <?php echo $statusLabel; ?>
                        <b class="label <?php echo $badgeColor; ?> pull-right" style="position: absolute; right: 10px;">
                            &nbsp;<?php echo htmlentities($count); ?>&nbsp;
                        </b>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
    </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage-users.php">
                                <span class="nav-icon">
                                    <iconify-icon icon="bi:people-fill"></iconify-icon>
                                </span>
                                <span class="nav-text">Users</span>
                                <span class="badge bg-primary badge-pill text-end"></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="police.php">
                                <span class="nav-icon">
                                    <iconify-icon icon="mdi:police-badge"></iconify-icon>
                                </span>
                                <span class="nav-text">Officers</span>
                                <span class="badge bg-primary badge-pill text-end"></span>
                            </a>
                        </li>

                        <li class="menu-title">Other</li>
                        <li class="nav-item">
                        <li class="nav-item">
                    <a class="nav-link" href="updates.php">
                        <span class="nav-icon">
                            <iconify-icon icon="bi:bell-fill"></iconify-icon>
                        </span>
                        <span class="nav-text">Create/Manage Updates</span>
                        <span class="badge bg-primary badge-pill text-end"></span>
                    </a>
                </li>
                    <li class="nav-item">
                        <a class="nav-link" href="category.php">
                            <span class="nav-icon">
                                <iconify-icon icon="bi:card-text"></iconify-icon>
                            </span>
                            <span class="nav-text">Manage Category</span>
                            <span class="badge bg-primary badge-pill text-end"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="weapons.php">
                            <span class="nav-icon">
                                <iconify-icon icon="bi:shield-lock"></iconify-icon>
                            </span>
                            <span class="nav-text">Types of Weapon</span>
                            <span class="badge bg-primary badge-pill text-end"></span>
                        </a>
                    </li>
                  </ul>
             </div>
        </div>