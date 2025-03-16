<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("includes/config.php");
?>
<div class="shadow-sm d-flex align-items-center bg-white" style="min-height: 65px;">
	<div class="container-fluid px-1">
		<div class="row mx-0 w-100">
			<div class="col-12 col-lg-auto d-flex align-items-center justify-content-between pt-2 pt-lg-0">
				<div>
					<img src="../img/logo3.png" width="44px" height="auto">
				</div>
				<div  class="d-flex d-lg-none align-items-center column-gap-4">
					<div>
						<div>
							<div class="position-relative" style="height: 25px; width: 25px;">
								<a href="#" class="text-dark text-decoration-none">
									<span class="badge text-bg-danger rounded-circle p-1 position-absolute" style="top: -4;right: 0; font-size: 11px;">1</span>
									<i class="ri-notification-line fs-5" style="height: 100%; width: 100%;"></i>
								</a>
							</div>
						</div>
					</div>
					<div class="dropdown">
						<div id="header-profile" class="btn btn-outline-secondary d-flex align-items-center border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
							<span class="me-2">
								<i class="ri-user-line text-dark"></i>
							</span>
							<div class="d-flex align-items-center">
								<span class="text-dark text-nowrap">
									<?= htmlentities($_SESSION['displayName']); ?>
								</span>
								<i class="ri-arrow-drop-down-line text-dark fs-4"></i>
							</div>
						</div>
						<ul class="dropdown-menu dropdown-menu-end" style="width: 14rem;">
							<li>
								<a class="dropdown-item" href="profile.php">
									<i class="ri-user-3-line"></i> 
									Profile
								</a>
							</li>
							<li><hr class="dropdown-divider"></li>
							<li>
								<a class="dropdown-item text-danger" href="logout.php">
									<i class="ri-logout-circle-r-line"></i>
									Logout
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<hr class="m-0 mt-2 text-secondary d-lg-none">
			<hr class="m-0 mt-3 text-secondary order-3 d-lg-none">
			<div class="col-12 col-lg order-3 order-lg-2">
				<ul class="navbar-nav navbar-text-color flex-row align-items-center justify-content-between justify-content-lg-center column-gap-4">
					<li class="nav-item">
						<a href="dashboard.php" class="nav-link d-flex flex-column flex-lg-row text-center column-gap-1 px-2 rounded-2">
							<div class="d-lg-none d-flex flex-column">
								<i class="ri-home-4-line fs-5"></i>
								<small>Home</small>
							</div>
							<div class="d-lg-block d-none">
								<i class="ri-home-4-line"></i>
								<span>Home</span>
							</div>
						</a>
					</li>
					<li class="nav-item">
						<a href="register-complaint.php" class="nav-link d-flex flex-column flex-lg-row text-center column-gap-1 px-2 rounded-2">
							<div class="d-lg-none d-flex flex-column">
								<i class="ri-shield-line fs-5"></i>
								<small>Report</small>
							</div>
							<div class="d-lg-block d-none">
								<i class="ri-shield-line"></i>
								<span>Report</span>
							</div>
						</a>
					</li>
					<li class="nav-item">
						<a href="status.php" class="nav-link d-flex flex-column flex-lg-row text-center column-gap-1 px-2 rounded-2">
							<div class="d-lg-none d-flex flex-column">
								<i class="ri-pie-chart-line fs-5"></i>
								<small>Status</small>
							</div>
							<div class="d-lg-block d-none">
								<i class="ri-pie-chart-line"></i>
								<span>Status</span>
							</div>
						</a>
					</li>
				</ul>
			</div>
			<div class="col-12 col-lg-auto order-2 order-lg-3 d-flex align-items-center column-gap-5 mt-3 mt-lg-0">
				<div class="w-100">
					<div class="input-group" style="width: 100%;">
					  <span class="input-group-text border-end-0" id="basic-addon1">
					  	<i class="ri-search-line"></i>
					  </span>
					  <input type="text" class="form-control border-start-0" placeholder="Search reports..." aria-label="Username" aria-describedby="basic-addon1">
					</div>
				</div>
				<div class="d-none d-lg-flex">
					<div>
						<div class="position-relative" style="height: 25px; width: 25px;">
							<a href="#" class="text-dark text-decoration-none">
								<span class="badge text-bg-danger rounded-circle p-1 position-absolute" style="top: -4;right: 0; font-size: 11px;">1</span>
								<i class="ri-notification-line fs-5" style="height: 100%; width: 100%;"></i>
							</a>
						</div>
					</div>
				</div>
				<div class="dropdown d-none d-lg-flex">
					<div id="header-profile" class="btn btn-outline-secondary d-flex align-items-center border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
						<span class="me-2">
							<i class="ri-user-line text-dark"></i>
						</span>
						<div class="d-flex align-items-center">
							<span class="text-dark text-nowrap"> <?= htmlentities($_SESSION['displayName']); ?></span>
							<i class="ri-arrow-drop-down-line text-dark fs-4"></i>
						</div>

					</div>
					  <ul class="dropdown-menu dropdown-menu-end" style="width: 14rem;">
					    <li>
					    	<a class="dropdown-item" href="profile.php">
						    	<i class="ri-user-3-line"></i> 
						    	Profile
						    </a>
					    </li>
					    <li><hr class="dropdown-divider"></li>
					    <li>
					    	<a class="dropdown-item text-danger" href="logout.php">
						    	<i class="ri-logout-circle-r-line"></i>
						    	Logout
						    </a>
					    </li>
					  </ul>
				</div>
			</div>
		</div>
	</div>
</div>