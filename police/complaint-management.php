<?php include "plugins-header.php";?>
<body>
	<div class="container-lg px-1 py-4">
		<div class="row row-gap-3 column-gap-2 mx-0 flex-wrap-reverse">
			<div class="col row row-gap-4 mx-0" style="max-height: calc(100vh - 120px);">
				<div class="d-flex flex-column flex-lg-row align-items-center justify-content-between">
					<div>
						<h5 class="fw-bold">Complaint Management</h5>
						<p>Monitor and update citizen complaints</p>
					</div>
					<div class="d-flex align-items-center column-gap-2">

						<div class="input-group" style="width: 100%;">
						  <span class="input-group-text border-end-0" id="basic-addon1">
						  	<i class="ri-search-line"></i>
						  </span>
						  <input type="text" class="form-control border-start-0" placeholder="Search reports..." aria-label="Username" aria-describedby="basic-addon1">
						</div>

						<button class="btn btn-dark text-nowrap">New Complaint</button>
					</div>
				</div>

				<div class="bg-white p-4 shadow-sm border rounded-2">
					<div class="d-flex align-items-center justify-content-between">
						<div>
							<h5 class="m-0 fw-bold">Noise Complaint</h5>
							<small>COMP-1234 • 123 Main Street</small>
						</div>
						<div>
							<span class="badge text-bg-warning">Pending</span>
						</div>
					</div>
					<div class="my-3">
						<small class="fw-bold m-0 d-block">Loud music playing after midnightd</small>
						<small>Reported on: 2025-03-10</small>
					</div>
					<div class="d-flex align-items-center justify-content-end column-gap-2">
						<button class="btn btn-outline-dark text-nowrap">View Details</button>
						<button class="btn btn-dark text-nowrap">Take Action</button>
					</div>
				</div>

				<div class="bg-white p-4 shadow-sm border rounded-2">
					<div class="d-flex align-items-center justify-content-between">
						<div>
							<h5 class="m-0 fw-bold">Suspicious Activity</h5>
							<small>COMP-1235 • 456 Oak Avenue</small>
						</div>
						<div>
							<span class="badge text-bg-primary">In Progress</span>
						</div>
					</div>
					<div class="my-3">
						<small class="fw-bold m-0 d-block">Unknown individuals loitering around property</small>
						<small>Reported on: 2025-03-09</small>
					</div>
					<div class="d-flex align-items-center justify-content-end column-gap-2">
						<button class="btn btn-outline-dark text-nowrap">View Details</button>
						<button class="btn btn-dark text-nowrap">Take Action</button>
					</div>
				</div>

				<div class="bg-white p-4 shadow-sm border rounded-2">
					<div class="d-flex align-items-center justify-content-between">
						<div>
							<h5 class="m-0 fw-bold">Theft Report</h5>
							<small>COMP-1236 • 789 Pine Road</small>
						</div>
						<div>
							<span class="badge text-bg-success">Solved</span>
						</div>
					</div>
					<div class="my-3">
						<small class="fw-bold m-0 d-block">Bicycle stolen from front yard</small>
						<small>Reported on: 2025-03-08</small>
					</div>
					<div class="d-flex align-items-center justify-content-end column-gap-2">
						<button class="btn btn-outline-dark text-nowrap">View Details</button>
						<button class="btn btn-dark text-nowrap">Take Action</button>
					</div>
				</div>

				<div class="bg-white p-4 shadow-sm border rounded-2">
					<div class="d-flex align-items-center justify-content-between">
						<div>
							<h5 class="m-0 fw-bold">Traffic Violation</h5>
							<small>COMP-1237 • 101 Elm Boulevard</small>
						</div>
						<div>
							<span class="badge text-bg-primary">In Progress</span>
						</div>
					</div>
					<div class="my-3">
						<small class="fw-bold m-0 d-block">Vehicles consistently speeding in school zone</small>
						<small>Reported on: 2025-03-07</small>
					</div>
					<div class="d-flex align-items-center justify-content-end column-gap-2">
						<button class="btn btn-outline-dark text-nowrap">View Details</button>
						<button class="btn btn-dark text-nowrap">Take Action</button>
					</div>
				</div>

			</div>
		</div>
	</div>
</body>

<?php include "plugins-footer.php";?>