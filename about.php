<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - INGAT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="users/asset/images/ingat.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header and Navigation */
        .header-top {
            background-color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
        }
        .header-top img {
            height: 50px;
        }
        .header-top nav {
            display: flex;
            gap: 20px;
        }
        .header-top nav a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        .header-top nav a:hover {
            color: #F7931E;
        }
        .header-top nav a.active {
            border-bottom: 2px solid #28a745;
            padding-bottom: 5px;
        }

        /* Hero Section */
        .hero-section {
            background-image: url('img/sta.jpg');
            background-size: cover;
            background-position: center;
            height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            color: #fff;
            text-align: center;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Dark overlay for readability */
        }
        .hero-section h1 {
            position: relative;
            z-index: 1;
            font-size: 2.5rem;
            color: #F7931E; /* Match INGAT's accent color */
            text-transform: uppercase;
        }

        /* Content Section */
        .content {
            flex: 1 0 auto;
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .content h2 {
            text-align: center;
            text-transform: uppercase;
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #28a745; /* Green underline like the Court of Appeals */
            display: inline-block;
        }
        .mission-item, .law-item {
            margin-bottom: 20px;
        }
        .mission-item .number, .law-item .number {
            display: block;
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 10px;
        }
        .mission-item p, .law-item p {
            line-height: 1.6;
            margin: 0;
        }
        .law-item strong {
            color: #1A2A6C; /* Match the section heading color */
        }

        /* Existing Sections */
        .section {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .section h2 {
            color: #1A2A6C;
            border-bottom: 2px solid #F7931E;
            padding-bottom: 5px;
            text-align: left;
        }
        .section p {
            line-height: 1.6;
        }
        .contact-info {
            font-style: italic;
            color: #555;
        }
    </style>
</head>
<body>
    <!-- Assuming header.php contains the navigation -->
    <?php include 'header.php'; ?>

    <!-- Add a header-top section for navigation if header.php doesn't include it -->
    <div class="header-top">
        <img src="users/asset/images/ingat.ico" alt="INGAT Logo">
        <nav>
            <a href="#">Home</a>
            <a href="#" class="active">About Us</a>
            <a href="#">Services</a>
            <a href="#">FAQ</a>
            <a href="#">Contact Us</a>
        </nav>
    </div>

    <!-- Hero Section -->
    <div class="hero-section">
        <h1>Mission, Vision, and Philippine Laws</h1>
    </div>

    <!-- Content Section -->
    <div class="content">
        <h2>Mission</h2>
        <div class="mission-item">
            <div class="number">01</div>
            <p>
                To create a safer society by enabling citizens to report crimes and suspicious activities securely and efficiently, fostering a proactive partnership between the community and law enforcement agencies.
            </p>
        </div>

        <h2>Vision</h2>
        <div class="mission-item">
            <div class="number">02</div>
            <p>
                To establish INGAT as the leading platform for crime prevention and community safety in the Philippines, promoting transparency, trust, and collective action to build a crime-free future.
            </p>
        </div>

        <h2>Relevant Philippine Laws</h2>
        <div class="law-item">
            <div class="number">01</div>
            <p>
                <strong>Republic Act No. 10175 - Cybercrime Prevention Act of 2012:</strong> To address crimes committed through information and communications technologies, mandating the Philippine National Police (PNP) and National Bureau of Investigation (NBI) to handle cybercrimes.
            </p>
        </div>
        <div class="law-item">
            <div class="number">02</div>
            <p>
                <strong>Republic Act No. 10173 - Data Privacy Act of 2012:</strong> To protect personal data in information systems, requiring compliance with the National Privacy Commission’s standards.
            </p>
        </div>
        <div class="law-item">
            <div class="number">03</div>
            <p>
                <strong>Republic Act No. 6975 - Department of the Interior and Local Government Act of 1990 (as amended by RA 8551):</strong> To establish the PNP and promote community policing through programs like KASIMBAYANAN.
            </p>
        </div>
        <div class="law-item">
            <div class="number">04</div>
            <p>
                <strong>Republic Act No. 7160 - Local Government Code of 1991:</strong> To empower barangays to maintain public order and mediate disputes.
            </p>
        </div>
        <div class="law-item">
            <div class="number">05</div>
            <p>
                <strong>Republic Act No. 11313 - Safe Spaces Act (Bawal Bastos Law):</strong> To address gender-based harassment, including online incidents. 
            </p>
        </div>

        <!-- Existing Sections -->
        <div class="law-item">
            <h2>Who We Are</h2>
            <p>
                INGAT (Inform, Navigate, Guard, Act, Together) is a pioneering crime reporting system designed to empower citizens of the Philippines to contribute to a safer community. Launched with a vision to bridge the gap between the public and law enforcement, INGAT provides a secure, user-friendly platform for reporting incidents, ensuring that every voice is heard and every concern is addressed.
            </p>
            <p>
        We are student from the University of Caloocan City, and INGAT (Inform, Navigate, Guard, Act, Together) is a school project created as part of our academic requirements. This system is designed to explore innovative solutions for crime reporting and community safety.
    </p>
    <p>
        Our goal is to apply our technical skills and knowledge to develop a functional prototype that demonstrates how technology can assist in public safety efforts. While INGAT is not an official crime reporting system, it serves as a learning experience for us as aspiring developers.
    </p>
        </div>

        <div class="law-item">
            <h2>Contact Us</h2>
            <p class="contact-info">
                For inquiries and concerns, please reach out to us at: <br>
                Email: <a href="mailto:ingat.system@gmail.com" style="color: #F7931E;">ingat.system@gmail.com</a>
            </p>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>