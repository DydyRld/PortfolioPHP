<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css">
    <style>
        @media only screen and (max-width: 768px) {
    body {
        margin: 0;
    }

    body.menu-open {
        padding-top: 30%;
        overflow-x: hidden;
    }

    #desktop-nav {
        display: none;
    }

    #mobile-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        color: #fff;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
    }

    .logo,
    .logo-mobile {
        font-size: 1.5em;
        display: none;
    }

    .burger-menu {
        display: block;
        cursor: pointer;
        z-index: 1001;
    }

    .bar {
        width: 25px;
        height: 3px;
        background-color: black;
        margin: 6px 0;
    }

    .nav-links-mobile {
        list-style: none;
        padding: 0;
        margin: 0;
        display: none;
        flex-direction: column;
        position: relative;
        left: 40%;
        transform: translateX(-50%);
        width: 100%;
        border-radius: 8px;
    }

    .nav-links-mobile.visible {
        display: flex;
    }

    .nav-links-mobile li {
        margin-bottom: 10px;
        text-align: center;
    }

    .nav-links-mobile a {
        color: black;
        text-decoration: none;
    }
}

        @media only screen and (min-width: 769px) {
            #mobile-nav {
                display: none;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar pour desktop -->
    <nav id="desktop-nav">
        <div class="logo">Dylan Rolland</div>
        <div>
            <ul class="nav-links">
                <?php
                // Vérifie si l'utilisateur est admin
                if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
                    echo '<li><a href="admin.php">Admin</a></li>';
                } else {
                    echo '<li><a href="index.php">Portfolio</a></li>';
                }
                ?>
                <li><a href="blog.php">Blog</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
    </nav>

    <!-- Navbar pour mobile -->
    <nav id="mobile-nav">
        <div class="logo-mobile">Dylan Rolland</div>
        <div class="burger-menu" onclick="toggleMobileMenu()">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
        <ul class="nav-links-mobile">
            <?php
            if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
                echo '<li><a href="admin.php">Admin</a></li>';
            } else {
                echo '<li><a href="index.php">Portfolio</a></li>';
            }
            ?>
            <li><a href="blog.php">Blog</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>

    <script>
        function toggleMobileMenu() {
            var navLinksMobile = document.querySelector(".nav-links-mobile");
            var body = document.querySelector("body");

            if (navLinksMobile.classList.contains("visible")) {
                navLinksMobile.classList.remove("visible");
                body.classList.remove("menu-open");
            } else {
                navLinksMobile.classList.add("visible");
                body.classList.add("menu-open");
            }
        }
    </script>

</body>

</html>