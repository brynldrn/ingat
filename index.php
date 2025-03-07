<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INGAT - Community Safety Portal</title>
    <link rel="shortcut icon" href="users/asset/images/ingat.ico">

    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* General Styles */
        body {
            font-family: 'Roboto', sans-serif; /* Change to a more readable font */
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #F9FAFB;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-size: 18px;
            scroll-behavior: smooth;
        }

        h1, h2 {
            font-family: 'Arial', sans-serif; /* Keep Arial for headings */
        }

        /* Header Styles */
        .header {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 30px;
            background: transparent;
        }

        .header img {
            height: 70px;
        }

        .header nav a {
            font-size: 1.2rem;
            font-weight: 600;
            color: #a9b4cd;
            margin-left: 15px;
        }

        .header nav a:hover {
            color: #98DED9;
            text-decoration: none;
        }

        /* Hero Section Styles */
.hero {
    position: relative;
    color: white;
    padding: 120px 20px;
    text-align: center;
    flex-grow: 1;
    background-color: #060270;
    animation: fadeIn 2s ease-in-out;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Initial soft shadow */
    overflow: hidden;
}

/* Glowing Ripple Animation */
@keyframes glowingRipple {
    0% {
        box-shadow: 0 0 20px rgba(152, 222, 217, 0.7), 0 0 50px rgba(152, 222, 217, 0.4), 0 0 80px rgba(152, 222, 217, 0.2);
    }
    50% {
        box-shadow: 0 0 40px rgba(152, 222, 217, 1), 0 0 100px rgba(152, 222, 217, 0.6), 0 0 150px rgba(152, 222, 217, 0.3);
    }
    100% {
        box-shadow: 0 0 20px rgba(152, 222, 217, 0.7), 0 0 50px rgba(152, 222, 217, 0.4), 0 0 80px rgba(152, 222, 217, 0.2);
    }
}

/* Apply glowing ripple effect */
.hero {
    animation: glowingRipple 2s infinite ease-in-out;
}


        .hero h1 {
            font-size: 4rem;
            font-weight: normal;
            margin-bottom: 30px;
            font-family: 'Arial', sans-serif;
            text-shadow:
                -2px -2px 3px rgba(255, 255, 255, 0.2),
                2px 2px 3px rgba(255, 255, 255, 0.2),
                0 0 6px rgba(255, 255, 255, 0.2);
            opacity: 0;
            animation: fadeInText 2s forwards;
        }

        .hero p {
            font-size: 1.5rem;
            margin-bottom: 40px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            opacity: 0;
            animation: fadeInText 2s 0.5s forwards;
        }

        .hero .btn {
            background-color: #98DED9;
            color: #021D58;
            font-size: 1.3rem;
            padding: 12px 30px;
            border-radius: 5px;
            text-transform: uppercase;
            text-decoration: none;
            font-weight: bold;
            margin: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .hero .btn:hover {
            background-color: #004A99;
            color: white;
            transform: translateY(-5px);
        }

        /* About Section */
        .about-section {
            padding: 40px 20px;
            text-align: center;
            background-color: #FFFFFF;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
        }

        .about-section h2 {
            font-size: 2.5rem;
            color: #021D58;
            margin-bottom: 30px;
        }

        .about-section p {
            font-size: 1.3rem;
            color: #666666;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Flexbox for centering content inside rows */
        .row {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .row .col-12 {
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        /* Image Styling */
        .img-fluid {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 0 auto;
            display: block;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem; /* Smaller heading on mobile */
                margin-bottom: 20px;
            }

            .hero p {
                font-size: 1.2rem; /* Adjust text size */
                margin-bottom: 30px;
            }

            .about-section h2 {
                font-size: 2rem;
            }

            .about-section p {
                font-size: 1.1rem;
            }

            .img-fluid {
                max-width: 100%;  
            }

            footer {
                font-size: 1rem;
            }
            .download-section h2 {
                font-size: 1.8rem;
            }

            .download-section p {
                font-size: 1rem;
            }

            .download-section .download-btn {
                font-size: 1.1rem;
                padding: 10px 20px;
            }
        }

        /* Footer */
        footer {
            background-color: #021D58;
            color: white;
            padding: 20px 10px;
            text-align: center;
            font-size: 1.2rem;
            margin-top: auto;
        }

        footer p {
            margin: 0;
        }

        footer a {
            color: #98DED9;
            text-decoration: none;
            font-size: 1.2rem;
        }

        footer a:hover {
            color: #ffffff;
        }

        /* Keyframe Animation */
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        @keyframes fadeInText {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .download-section {
            padding: 50px 20px;
            background-color: #f1f3f5;
            text-align: center;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }

        .download-section h2 {
            color: #006400;
            font-size: 2.2rem;
            margin-bottom: 20px;
        }

        .download-section p {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .download-section .download-btn {
            display: inline-flex;
            align-items: center;
            background-color: #006400;
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .download-section .download-btn:hover {
            background-color: #004d00;
            transform: translateY(-3px);
        }

        .download-section .download-btn::before {
            content: "📥";
            margin-right: 10px;
        }

        .download-section .requirements {
            font-size: 0.9rem;
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <div class="header">
        <img src="img/logo.png" alt="INGAT">
        <nav>
            <a href="/inform/map.php">Map</a>
        </nav>
    </div>

    <!-- Hero Section -->
    <div class="hero">
        <h1>Inform. Navigate. Guard. Act. Together.</h1>
        <p>
            Together, we create safer communities by reporting incidents, staying informed, and navigating real-time situations. INGAT empowers citizens to take action.
        </p>
        <div class="buttons">
            <a href="/inform/users/registration.php" class="btn">Register</a>
            <a href="/inform/users/" class="btn">Login</a>
        </div>
    </div>

   <!-- About Section --> <section class="py-3 py-md-5"> 
    <div class="container"> 
        <div class="row gy-3 gy-md-4 gy-lg-0"> 
            <div class="col-12 col-lg-6"> 
                <p>Ang INGAT ay isang platform para sa kaligtasan ng komunidad na naglalayong tulungan ang mga mamamayan na mag-ulat ng krimen, manatiling updated, at gawing mas ligtas ang kanilang mga paligid. Sa pamamagitan ng Ingat app, madali ang pag-ulat ng insidente at pagbibigay ng abiso sa mga lokal na awtoridad tungkol sa mga emerhensiya o kahina-hinalang aktibidad.</p> 
            </div> 
            <div class="col-12 col-lg-6">
                 <img src="img/hands.png" alt="Community Safety" class="img-fluid rounded" loading="lazy">
 </div> </div> 

               <div class="row gy-3 gy-md-4 gy-lg-0">  
        <div class="col-12 col-lg-6 order-lg-2">  
            <img src="img/2.png" alt="Stay Connected" class="img-fluid rounded" loading="lazy">  
        </div>  
        <div class="col-12 col-lg-6 order-lg-1">  
            <p class="lead fs-4 mb-3"><strong>Manatiling Konektado!</strong> Sa pamamagitan ng pagiging konektado, makakakuha ng mahahalagang impormasyon tungkol sa kaligtasan, mga alerto ng krimen, at mga lokal na update. Pinadali ng INGAT ang pagtutulungan ng mga kapitbahay para mapanatili ang kaligtasan ng komunidad.</p>  
        </div>  
    </div>  

    <div class="row gy-3 gy-md-4 gy-lg-0">  
        <div class="col-12 col-lg-6">  
            <p>Sa INGAT, maaari kang maging bahagi ng mas ligtas at mas konektadong komunidad. Anuman ang papel mo—pag-ulat man ng isyu o pag-monitor ng lokal na kaligtasan—lahat ng aksyon ay may halaga.</p>  
        </div>  
        <div class="col-12 col-lg-6">  
            <img src="img/3.png" alt="Report Incidents" class="img-fluid rounded" loading="lazy">  
        </div>  
    </div>  
</div>  

    </section>
    <section class="download-section" id="download">
        <h2>Download INGAT Mobile App</h2>
        <p>Access community safety features anytime, anywhere with our mobile application. Stay connected with real-time incident reports and alerts on the go.</p>
        <a href="https://github.com/elaizakriselle/ingat/releases/download/alpha-1.1.0/build-1741099535010.apk" 
           class="download-btn" 
           download="INGAT-App.apk">Download APK</a>
        <p class="requirements">For Android devices running Android 7.0 and above</p>
    </section>

    <!-- Footer -->
    <footer>
        <p>@2024 TeamIngat. All Rights Reserved.</p>
        <p><a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
