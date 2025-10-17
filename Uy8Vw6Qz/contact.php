<?php
    session_start();

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'library/PHPMailer/src/Exception.php';
    require 'library/PHPMailer/src/PHPMailer.php';
    require 'library/PHPMailer/src/SMTP.php';

    $sentMessage = $_SESSION['sentMessage'] ?? '';
    $errorMessage = $_SESSION['errorMessage'] ?? '';

    $form_data = $_SESSION['form_data'] ?? [];
    $firstName = htmlspecialchars($form_data['firstName'] ?? '');
    $lastName  = htmlspecialchars($form_data['lastName'] ?? '');
    $email     = htmlspecialchars($form_data['email'] ?? '');
    $phone     = htmlspecialchars($form_data['phone'] ?? '');
    $message   = htmlspecialchars($form_data['message'] ?? '');

    unset($_SESSION['sentMessage'], $_SESSION['errorMessage'], $_SESSION['form_data']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $post_firstName = trim($_POST['firstName'] ?? '');
        $post_lastName  = trim($_POST['lastName'] ?? '');
        $post_email     = trim($_POST['email'] ?? '');
        $post_phone     = trim($_POST['phone'] ?? '');
        $post_message   = trim($_POST['message'] ?? '');

        $_SESSION['form_data'] = $_POST;

        if (empty($post_firstName) || empty($post_lastName) || empty($post_message) || !filter_var($post_email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['errorMessage'] = 'Lütfen gerekli alanları doğru bir şekilde doldurunuz.';
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'osmannboz06@gmail.com';
            $mail->Password   = 'divl zvbm fwvr yzdp'; // Uygulama Şifreniz
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($mail->Username, 'Yalvaç Hukuk Web Sitesi');
            $mail->addAddress('osmannboz06@gmail.com', 'Osman Boz');
            $mail->addReplyTo($post_email, "$post_firstName $post_lastName");

            $mail->isHTML(true);
            $mail->Subject = "Web Sitenizden Yeni Mesaj: $post_firstName $post_lastName";

            $safe_firstName = htmlspecialchars($post_firstName);
            $safe_lastName  = htmlspecialchars($post_lastName);
            $safe_email     = htmlspecialchars($post_email);
            $safe_phone     = htmlspecialchars($post_phone);
            $safe_message   = nl2br(htmlspecialchars($post_message));

            $currentYear = date('Y');
            
            $mail->Body = <<<HTML
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>New Contact Form Message</title>
            </head>
            <body style='margin:0; padding:0; background-color:#f5f7fa; font-family:-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;'>
                <center style='width:100%; table-layout:fixed; padding:40px 0;'>
                    <div style='max-width:600px; background-color:#ffffff; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.07);'>
                        <table align='center' style='width:100%; margin:0 auto; border-spacing:0; color:#333;'>
                            <tr>
                                <td style='padding:0;'>
                                    <table width='100%' style='border-radius:12px 12px 0 0; background-color:#0a2e5c;'>
                                        <tr>
                                            <td style='padding:30px; text-align:center;'>
                                                <h1 style='margin:0; font-size:24px; color:#ffffff; font-weight:600;'>Yalvaç Law Firm - New Form Message</h1>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding:35px;'>
                                    <p style='margin:0 0 20px; font-size:18px; font-weight:500; color:#111;'>Hello,</p>
                                    <p style='margin:0 0 25px; line-height:1.6; color:#555;'>You have received a new message through the contact form on your website. Details are below:</p>
                                    <table width='100%' style='background-color:#f8f9fc; border:1px solid #e9ecef; border-radius:8px; margin-bottom:25px;'>
                                        <tr>
                                            <td style='padding:20px;'>
                                                <table width='100%'>
                                                    <tr style='margin-bottom:15px; display:block;'>
                                                        <td style='font-weight:600; color:#0a2e5c; padding-right:15px; width:100px; display:inline-block;'>Name:</td>
                                                        <td style='color:#444;'>{$safe_firstName} {$safe_lastName}</td>
                                                    </tr>
                                                    <tr style='margin-bottom:15px; display:block;'>
                                                        <td style='font-weight:600; color:#0a2e5c; padding-right:15px; width:100px; display:inline-block;'>Email:</td>
                                                        <td style='color:#444;'><a href='mailto:{$safe_email}' style='color:#007bff; text-decoration:none;'>{$safe_email}</a></td>
                                                    </tr>
                                                    <tr style='margin-bottom:0; display:block;'>
                                                        <td style='font-weight:600; color:#0a2e5c; padding-right:15px; width:100px; display:inline-block;'>Phone:</td>
                                                        <td style='color:#444;'>{$safe_phone}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style='margin:0 0 10px; font-size:16px; font-weight:600; color:#0a2e5c;'>Message</p>
                                    <div style='background-color:#ffffff; border:1px solid #e9ecef; border-radius:8px; padding:20px; line-height:1.7; color:#333;'>{$safe_message}</div>
                                    <table width='100%' style='margin-top:30px;'>
                                        <tr>
                                            <td align='center'>
                                                <a href='mailto:{$safe_email}' style='background-color:#0a2e5c; color:#ffffff; padding:14px 28px; border-radius:8px; text-decoration:none; font-weight:600;'>Reply by Email</a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding:0;'>
                                    <table width='100%' style='border-top:1px solid #e9ecef;'>
                                        <tr>
                                            <td style='padding:25px; text-align:center; font-size:12px; color:#999;'>
                                                This e-mail was sent via the Yalvaç Law Firm website contact form.<br>&copy; {$currentYear} Yalvaç Law Firm. All rights reserved.
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </center>
            </body>
            </html>
            HTML;

        // The rest of your code ($mail->AltBody, $mail->send(), etc.) remains the same.
            
            $mail->AltBody = "Yeni İletişim Formu Mesajı\n\nAd Soyad: $safe_firstName $safe_lastName\nE-posta: $safe_email\nTelefon: $safe_phone\n\nMesaj:\n$post_message";

            $mail->send();
            
            unset($_SESSION['form_data']);

            $_SESSION['sentMessage'] = 'Mesajınız başarıyla gönderildi. En kısa sürede size geri dönüş yapacağız.';
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();

        } catch (Exception $e) {
            $_SESSION['errorMessage'] = 'Mesajınız gönderilemedi. Lütfen daha sonra tekrar deneyin.';
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
    }
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Temel Meta -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta -->
    <title>Yalvaç Law Firm - Contact</title>
    <meta name="description" content="Bu sayfanın kısa ve açıklayıcı SEO açıklaması. 150-160 karakter arası ideal.">
    <meta name="keywords" content="anahtar kelime1, anahtar kelime2, anahtar kelime3">
    <meta name="author" content="Osman Nuri Boz">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph (Sosyal Medya için) -->
    <meta property="og:title" content="Yalvaç Law Firm - Contact">
    <meta property="og:description" content="Sayfa açıklaması, sosyal medyada görünecek kısa özet.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.yalvac.av.tr/contact">
    <meta property="og:image" content="uploads/images/company/favicon.png">
    <meta property="og:site_name" content="Yalvaç Law Firm - Contact">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Yalvaç Law Firm - Contact">
    <meta name="twitter:description" content="Sayfa açıklaması Twitter’da görünecek.">
    <meta name="twitter:image" content="uploads/images/company/favicon.png">
    <meta name="twitter:site" content="@kullaniciadi">

    <!-- Favicon -->
    <link rel="icon" sizes="192x192" href="uploads/images/company/favicon.png" type="image/png"/>
    <link rel="shortcut icon" href="uploads/images/company/favicon.png" type="image/png"/>
    <link rel="apple-touch-icon" href="uploads/images/company/favicon.png" type="image/png"/>

    <!-- CSS -->
    <link rel="stylesheet" href="library/css/core.css">
    <link rel="stylesheet" href="library/css/contact.css">
    <link rel="stylesheet" href="library/css/static.css">
</head>
<body>
    <header>
        <a href="/" title="Yalvaç Law Firm"><img src="uploads/images/company/yalvac-law-firm-logo.png" loading="lazy" alt="Yalvaç Law Firm"></a>
        
        <button>
            <i class="bi bi-list" aria-label="Toggle navigation"></i>
        </button>
        
        <nav>
            <ul>
                <li><a href="/home">Home</a></li>
                <li><a href="/about">About</a></li>
                <li><a href="/services">Services</a></li>
                <li><a href="/blog">Blog</a></li>
                <li><a href="/career">Career</a></li>
                <li><a href="/contact">Contact</a></li>
            </ul>
        </nav>
    </header>
    <main>
         <section class="hero-section">
            <img src="https://www.bicklawllp.com/wp-content/uploads/2016/07/who-we-are.jpg" loading="lazy" alt="Communication visual">
        </section>
        <section class="contact-section">
            <article class="contact-card">
                <div class="contact-item">
                    <i class="bi bi-envelope"></i>
                    <p>info@yalvac.av.tr</p>
                </div>
                
                <div class="contact-item">
                    <i class="bi bi-headset"></i>
                    <p>+90 312 472 06 72</p>
                </div>

                <div class="contact-item">
                    <i class="bi bi-whatsapp"></i>
                    <p>+90 532 699 06 32</p>
                </div>

                <div class="contact-item">
                    <i class="bi bi-geo-alt"></i>
                    <p>YDA Center, Kızılırmak Mah. Dumlupınar Bulvarı, 1443. Cd. No:9 A-3 Blok, Kat:8 No: 279, 06510 Çankaya/Ankara</p>
                </div>
            </article>

        <form action="contact.php" method="post" novalidate>

            <?php if (!empty($sentMessage)): ?>
                <p class="form-success"><?= htmlspecialchars($sentMessage) ?></p>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
                <p class="form-error"><?= htmlspecialchars($errorMessage) ?></p>
            <?php endif; ?>


            <article>
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName" required>
            </article>

            <article>
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName" required>
            </article>

            <article>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </article>

            <article>
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" required>
            </article>

            <article>
                <label for="message">Message</label>
                <textarea id="message" name="message" required></textarea>
            </article>

            <button type="submit">Send</button>
        </form>

        <section class="google-maps-section">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3060.394022339565!2d32.806328076488526!3d39.91019768631217!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14d345d8eda8107f%3A0x1f2bcc8041771292!2sYalva%C3%A7%20Hukuk!5e0!3m2!1str!2str!4v1760219399345!5m2!1str!2str"
                width="100%"
                height="500"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="Yalvac Law Firm Location Map">
            </iframe>
        </section>
    </main>
    <footer>
        <nav class="legal-link">
            <ul>
                <li><a href="uploads/documents/cookie-policy.pdf" title="Cookie Policy">Cookie Policy</a></li>
                <li><a href="uploads/documents/privacy-policy.pdf" title="Privacy Policy">Privacy Policy</a></li>
            </ul>
        </nav>
        <nav class="browsing-social-media">
            <ul>
                <li>
                    <a href="https://linkedin.com/company/yalvachukuk" title="Linkedin" target="_blank" rel="noopener noreferrer" aria-label="Visit our LinkedIn page">
                        <i class="bi bi-linkedin"></i>
                    </a>
                </li>
                <li>
                    <a href="mailto:info@yalchukuk.com" title="Email" target="_blank" rel="noopener noreferrer" aria-label="Send us an email">
                        <i class="bi bi-envelope-at-fill"></i>
                    </a>
                </li>
                <li>
                    <a href="tel:+905326990632" title="Phone" target="_blank" rel="noopener noreferrer" aria-label="Call us">
                        <i class="bi bi-telephone"></i>
                    </a>
                </li>
                <li>
                    <a href="https://wa.me/+905326990632" title="WhatsApp" target="_blank" rel="noopener noreferrer" aria-label="Contact us on WhatsApp">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <img src="uploads/images/company/yalvac-law-firm-logo.png" loading="lazy" alt="Yalvac Law Firm">
        <address>YDA Center, No: 279 Kızılırmak Mah. Dumlupınar Boulevard, 1443. St. No:9 A-3 Block, Floor:8 No: 279, 06510 Çankaya/Ankara</address>
        <p>
            This website, including its content and graphics, is the property of Yalvaç Law Firm.
            Unauthorized reproduction, distribution, or use of this content is strictly prohibited.
            Legal action may be taken in case of any infringement.
        </p>
        <hr>
        <small>© 2025 Yalvaç Law Firm All rights reserved.</small>
    </footer>

    <script src="library/scripts/core.js"></script>
</body>
</html>