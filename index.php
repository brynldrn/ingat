<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INGAT - Community Safety Portal</title>
    <link rel="shortcut icon" href="users/asset/images/ingat.ico">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-size: 18px;
            scroll-behavior: smooth;
        }

        .hero {
            position: relative;
            color: #fff;
            padding: 100px 20px 50px;
            background-color: #1A2A6C; 
            background-image: url('img/pnp.jpeg');
            background-size: cover; 
            background-position: center; 
            min-height: 400px;
            display: flex;
            align-items: center;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(26, 42, 108, 0.86); 
            z-index: 1;
        }

        .hero-content {
            max-width: 600px;
            position: relative;
            z-index: 2; 
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #fff;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            max-width: 600px;
            color: #ddd;
        }

        .hero .btn {
            background-color: #F7931E;
            color: #fff;
            font-size: 1.1rem;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            margin: 5px;
            transition: background-color 0.3s ease;
        }

        .hero .btn:hover {
            background-color: #e07b1a;
        }

        .hero img {
            max-width: 500px;
            margin-left: auto;
            position: relative;
            z-index: 2;
        }

        .about-section {
            padding: 40px 20px;
            text-align: center;
            background-color: #fff;
        }

        .about-section h2 {
            font-size: 2rem;
            color: #1A2A6C;
            margin-bottom: 20px;
        }

        .about-section p {
            font-size: 1rem;
            color: #666;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto 20px;
        }

        .feature-box {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .feature-box i {
            font-size: 40px;
            margin-bottom: 10px;
            color: #F7931E;
        }

        .download-section {
            padding: 40px 20px;
            background-color: #FFF3E6;
            text-align: center;
        }

        .download-section h2 {
            color: #1A2A6C;
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .download-section p {
            font-size: 1rem;
            color: #666;
            margin-bottom: 20px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .download-section .download-btn {
            background-color: #F7931E;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 600;
            margin: 5px;
            transition: background-color 0.3s ease;
        }

        .download-section .download-btn:hover {
            background-color: #e07b1a;
        }

        .download-section .requirements {
            font-size: 0.9rem;
            color: #666;
            margin-top: 10px;
        }


        .scroll-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #F7931E;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease, background-color 0.3s ease;
        }

        .scroll-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .scroll-to-top:hover {
            background-color: #e07b1a;
        }

        @media (max-width: 768px) {
            .hero {
                padding: 80px 20px 30px;
                flex-direction: column;
                text-align: center;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .hero img {
                max-width: 100%;
                margin: 20px auto 0;
            }

            .about-section h2 {
                font-size: 1.5rem;
            }

            .download-section h2 {
                font-size: 1.5rem;
            }

            .feature-box {
                margin-bottom: 15px;
            }
        }

        .modal-dialog {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }

        .modal-content {
            margin: auto;
        }
        .ms-2 {
         margin-left: .0 !important;
        }
    </style>
</head>

<body>

   <?php include 'header.php'; ?>

    <div class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="hero-content">
                        <h1>Inform. Navigate. Guard. Act. Together.</h1>
                        <p>Together, we create safer communities by reporting incidents, staying informed, and navigating real-time situations. INGAT empowers citizens to take action.</p>
                        <div>
                            <a href="users/registration.php" class="btn">Register Now</a>
                            <a href="about.php" class="btn">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <img src="img/landp.png" alt="Mobile App" class="img-fluid">
                </div>
            </div>
        </div>
    </div>


    <section class="about-section">
        <h2>Community Safety Features</h2>
        <p>Sa INGAT, maaari kang maging bahagi ng ligtas at mas konektadong komunidad. Anuman ang papel mo—pag-ulat man ng isyu o pag-monitor ng lokal na kaligtasan—lahat ng aksyon ay may halaga.</p>
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="feature-box">
                        <i class="fas fa-bell"></i>
                        <h3>Real-time Alerts</h3>
                        <p>Receive instant notifications about safety incidents in your area, keeping you informed and prepared.</p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="feature-box">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>Location Tracking</h3>
                        <p>Monitor safety situations in your neighborhood with precise location-based information and updates.</p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="feature-box">
                        <i class="fas fa-users"></i>
                        <h3>Community Network</h3>
                        <p>Connect with neighbors and local authorities to build a stronger, more responsive safety network.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="download-section">
    <h2>Download INGAT Mobile App</h2>
    <p>Access community safety features anytime, anywhere with our mobile application. Stay connected with real-time incident reports and alerts on the go.</p>
    <a href="https://github.com/elaizakriselle/ingat/releases/download/alpha-1.1.0/build-1741099535010.apk" class="download-btn" download="INGAT-App.apk">
        <i class="fas fa-download"></i> Download APK
    </a>
    <a href="#" class="download-btn" data-bs-toggle="modal" data-bs-target="#demoModal">
        <i class="fas fa-play"></i> Demo
    </a>
    <p class="requirements">Requires Android 7.0 or higher</p>
</section>

    <div class="modal fade" id="demoModal" tabindex="-1" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demoModalLabel">Inform Navigate Guard and Act Together</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/yZuiQz8wObw" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <?php include 'footer.php'; ?>

    <button class="scroll-to-top" id="scrollToTopBtn">
        <i class="fas fa-arrow-up"></i>
    </button>

 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });

        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>

</html>