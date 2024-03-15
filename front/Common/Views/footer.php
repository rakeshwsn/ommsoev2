<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style type="text/css">
        /* Add your CSS here */
    </style>
</head>
<body>

<!-- FOOTER START -->
<?php if($header):?>
<footer class="site-footer mt-3">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 footer-col left-col">
                <img src="images/logos/sppif-header.svg" alt="SPPIF logo" class="img-fluid mb-3">
                <p>The SPPIF is for enhancing production, income and household level food security & reducing climate risks will be the focus.</p>
                <div class="footer-social">
                    <ul>
                        <li><a href="https://www.facebook.com/" target="blank" rel="noopener noreferrer"><i class="fab fa-facebook-f" aria-label="Facebook"></i></a></li>
                        <li><a href="https://www.twitter.com/" target="blank" rel="noopener noreferrer"><i class="fab fa-twitter" aria-label="Twitter"></i></a></li>
                        <li><a href="https://www.instagram.com/" target="blank" rel="noopener noreferrer"><i class="fab fa-instagram" aria-label="Instagram"></i></a></li>
                        <li><a href="https://www.youtube.com/" target="blank" rel="noopener noreferrer"><i class="fab fa-youtube" aria-label="YouTube"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 footer-col spacing-m-center">
                <h5>Quick Links</h5>
                <ul class="quick-links left-layer">
                    <li><a href="/">Home</a></li>
                    <li><a href="privacy.php">About</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
                <ul class="quick-links right-layer d-none">
                    <li><a href="faq.html">FAQ</a></li>
                    <li><a href="contact.html">Contact Us</a></li>
                    <li><a href="gallery-grid.html">Gallery</a></li>
                    <li><a href="blog-grid.html">Blogs</a></li>
                </ul>
            </div>
            <div class="col-lg-3 footer-col">
                <h5>NEWSLETTER</h5>
                <p>Subscribe to our newsletter to receive the latest news about our services.</p>
                <div class="newsletter">
                    <form action="/newsletter-signup" method="post" name="sign-up" novalidate>
                        <label for="newsletter-email">Email address:</label>
                        <input type="email" class="input" id="newsletter-email" name="email" placeholder="Your email address" required>
                        <input type="submit" class="button" id="newsletter-submit" value="Sign Up">
                    </form>
                </div
