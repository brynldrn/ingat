<?php include "plugins-header.php";?>
<body>
	<div class="container px-1 py-4">
		<div class="row row-gap-3 column-gap-2 mx-0 flex-wrap-reverse">
			<div class="col-12 row row-gap-4 column-gap-2 mx-0" style="max-height: calc(100vh - 120px);">
				<a href="#" class="text-decoration-none text-dark mb-0"><i class="ri-arrow-left-double-line"></i> Back to Dashboard</a>
				<div class="col-12 col-lg-11 d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-between">
					<div>
						<h5 class="fw-bold">Complaint Management</h5>
						<p><i class="ri-map-pin-line"></i>123 Main Street</p>
					</div>
					<div>
						<span class="badge text-bg-primary">In Progress</span>
					</div>
				</div>

				<div class="col-12 col-lg-8">
					<div class="bg-white p-4 shadow-sm border rounded-2 mb-4">
						<div class="d-flex align-items-center justify-content-between">
							<div>
								<strong class="d-block m-0">Complaint Details</strong>
								<small><i class="ri-calendar-line me-2"></i>Reported on 2025-03-10</small>
							</div>
							<div>
								<span class="badge text-bg-warning">Pending</span>
							</div>
						</div>
						<div class="my-3">
							<p>Loud music playing after midnight. The noise has been consistent for the past week and is disrupting sleep for multiple residents in the area. Previous attempts to resolve the issue directly with the neighbor have been unsuccessful.</p>
						</div>

						<div class="mb-2 p-3" style="background-color: var(--light-gray);">
							<div><i class="ri-user-line me-2"></i><b>Reporter Information</b></div>
							<ul class="navbar-nav">
								<li class="nav-item">Name: <span>John Doe</span></li>
								<li class="nav-item">Phone: <span>555-123-4567</span></li>
								<li class="nav-item">Email: <span>john.doe@example.com</span></li>
							</ul>
						</div>
						<button class="btn btn-dark text-nowrap">Take Action</button>
					</div>
					<div class="bg-white p-4 shadow-sm border rounded-2 mb-4">
						<div class="d-flex align-items-center justify-content-between">
							<div>
								<strong class="d-block m-0">Action History</strong>
								<small><i class="ri-calendar-line me-2"></i>2025-03-10</small>
							</div>
							<div>
								<span class="badge text-bg-primary">In Progress</span>
							</div>
						</div>
						<div class="my-3">
							<p>Initial assessment completed. Visited the location and confirmed the noise complaint.</p>
						</div>
					</div>
					<div class="bg-white p-4 shadow-sm border rounded-2">
						<div class="d-flex align-items-center justify-content-between">
							<div>
								<strong class="d-block m-0">Officer Johnson</strong>
								<small><i class="ri-calendar-line me-2"></i>2025-03-11</small>
							</div>
							<div>
								<span class="badge text-bg-primary">In Progress</span>
							</div>
						</div>
						<div class="my-3">
							<p>Spoke with the property owner about noise regulations and issued a warning.</p>
							<img src="../img/pnp.jpeg" width="100%" height="auto">
						</div>
					</div>
				</div>

				<div class="col-12 col-lg-3 ">
					<div class="bg-white p-4 shadow-sm border rounded-2">
						<strong>Case Summary</strong>
						<div>
							<small class="text-secondary">Case ID</small>
							<p>COMP-1235</p>
						</div>
						<hr>
						<div>
							<small class="text-secondary d-block">Status</small>
							<span class="badge text-bg-primary">In Progress</span>
						</div>
						<hr>
						<div>
							<small class="text-secondary d-block">Date Reported</small>
							<span>2025-03-10</span>
						</div>
						<hr>
						<div>
							<small class="text-secondary d-block">Last Updated</small>
							<span>2025-03-11</span>
						</div>
						<hr>
						<div class="mb-3">
							<small class="text-secondary d-block">Assigned Officer</small>
							<span>Officer Johnson</span>
						</div>
						<div class="d-flex flex-column row-gap-2">
							<button class="btn btn-outline-dark border-secondary-subtle">Print Report</button>
							<button class="btn btn-dark">Take Action</button>
						</div>
					</div>
				</div>


			</div>
		</div>
	</div>
</body>

<?php include "plugins-footer.php";?>