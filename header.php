<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="#">
            <img src="img/logo.png" alt="INGAT">
        </a>

        <!-- Toggler Button for Mobile View -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Updates</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="map.php">Map</a>
                </li>
                <!-- Login and Register buttons inside menu -->
                <li class="nav-item d-lg-none">
                    <a class="btn w-100 mt-2" href="users/">Login</a>
                </li>
                <li class="nav-item d-lg-none">
                    <a class="btn w-100 mt-2" href="users/registration.php">Register</a>
                </li>
            </ul>
        </div>

        <!-- Login/Register for larger screens -->
        <div class="d-none d-lg-flex">
            <a class="btn me-2" href="users/">Login</a>
            <a class="btn" href="users/registration.php">Register</a>
        </div>
    </div>
</nav>

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

    h1, h2 {
        font-family: 'Arial', sans-serif;
    }

    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 10;
        background: #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        padding: 10px 15px;
    }

    .navbar-brand img {
        height: 40px;
    }

    .navbar-nav {
        display: flex;
        align-items: center;
    }

    .navbar-nav .nav-link {
        font-size: 1rem;
        font-weight: 500;
        color: #333;
        margin: 0 15px;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .navbar-nav .nav-link:hover {
        color: #F7931E;
    }

    .navbar .btn {
        background-color: #F7931E;
        color: #fff;
        border: none;
        padding: 6px 15px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .navbar .btn:hover {
        background-color: #e07b1a;
    }

    /* Full-width buttons inside the menu */
    .navbar-nav .btn {
        width: 100%;
        text-align: center;
    }
</style>
