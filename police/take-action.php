<?php include "plugins-header.php";?>
<style>
        .upload-box {
            width: 100%;
            height: 150px;
            border: 2px dashed #ccc;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            background-color: #f9f9f9;
            cursor: pointer;
            transition: 0.3s;
        }

        .upload-box:hover {
            background-color: #f0f0f0;
        }

        .upload-box i {
            font-size: 32px;
            color: #666;
            margin-bottom: 8px;
        }

        .upload-box p {
            margin: 0;
            font-size: 14px;
            color: #555;
        }

        .upload-box small {
            font-size: 12px;
            color: #888;
        }

        /* Hide default file input */
        .upload-box input {
            display: none;
        }
    </style>
<body>
	<div class="container px-1 py-4">
		<div class="row row-gap-3 column-gap-2 mx-0 flex-wrap-reverse">
			<div class="col-12 row row-gap-4 column-gap-2 mx-0" style="max-height: calc(100vh - 120px);">
				<a href="#" class="text-decoration-none text-dark mb-0"><i class="ri-arrow-left-double-line"></i> Back to Complaint</a>
				<div class="col-12 col-lg-11">
					<div>
						<h4 class="fw-bold">Take Action</h4>
						<p class="text-secondary">Update status and add details for complaint COMP-1235</p>
					</div>
				</div>

				<div class="col-12 row mx-0 justify-content-center">
					<div class="col-12 col-lg-8">
						<div class="bg-white p-4 shadow-sm border rounded-2 mb-4">
							<form class="row mx-0 row-gap-3">
								<div class="col-12">
									<strong class="d-block m-0">Action Report</strong>
									<small class="text-secondary">Document the actions taken and update the complaint status</small>
								</div>

								<div>
									<label for="officername" class="form-label">Officer Name</label>
									<div class="form-floating">
					                  <input required type="text" name="officername" id="officername" class="form-control rounded-1" placeholder="input officername">
					                  <label for="officername">Enter your name</label>
					                </div>
					            </div>

					            <div>
									<label for="notes" class="form-label">Action Notes</label>
									<div class="form-floating">
										<textarea required type="text" name="notes" id="notes" class="form-control rounded-1" placeholder="input notes" style="height: 100px;"></textarea>
					                  	<label for="notes" class="text-wrap">Describe the actions taken...</label>
					                </div>
					            </div>

					            <div>
									<label for="officername" class="form-label">Case Status</label>
									<div class="form-check">
									  <input class="form-check-input border-dark" type="radio" value="In Progress" name="In_Progress" id="In_Progress">
									  <label class="form-check-label" for="In_Progress">
									    In Progress
									  </label>
									</div>
									<div class="form-check">
									  <input class="form-check-input border-dark" type="radio" value="Solved" name="Solved" id="Solved">
									  <label class="form-check-label" for="Solved">
									    Solved
									  </label>
									</div>
					            </div>

					            <div>
					            	<div class="d-flex justify-content-center">
	                                    <label class="upload-box">
	                                    	<i class="ri-camera-line"></i>
	                                        <p>Drag photos here or click to upload</p>
	                                        <input type="file" name="photos_actions">
	                                    </label>
	                                </div>
	                            </div>
	                            <div class="d-flex align-items-center justify-content-between">
	                            	<button class="btn btn-outline-dark">Cancel</button>
	                            	<button class="btn btn-dark">Submit Report</button>
	                            </div>
							</form>
						</div>
					</div>
				</div>


			</div>
		</div>
	</div>
</body>

<?php include "plugins-footer.php";?>