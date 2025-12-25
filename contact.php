<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - QuickNote</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/landing.css">
    <style>
        /* Contact Specific Styles */
        body {
            background-color: #000;
            background-image:
                radial-gradient(circle at 10% 20%, rgba(0, 210, 106, 0.08), transparent 25%),
                radial-gradient(circle at 90% 80%, rgba(0, 100, 255, 0.05), transparent 25%);
        }

        .contact-section {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 200px);
            /* Adjust for footer */
            padding: 140px 5% 80px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 60px;
            width: 100%;
            align-items: center;
        }

        /* Left Column: Info */
        .contact-info h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 4rem;
            line-height: 1.1;
            margin-bottom: 25px;
            background: linear-gradient(135deg, #fff 0%, #aaffd3 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .contact-info p {
            font-size: 1.15rem;
            color: #ccc;
            margin-bottom: 40px;
            line-height: 1.6;
            max-width: 450px;
        }

        .contact-details {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 15px;
            color: #fff;
            font-size: 1.1rem;
        }

        .contact-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--accent-primary);
            font-size: 1.2rem;
            transition: 0.3s;
        }

        .contact-item:hover .contact-icon {
            background: rgba(0, 210, 106, 0.15);
            border-color: var(--accent-primary);
            box-shadow: 0 0 15px var(--accent-glow);
        }

        /* Right Column: Form */
        .contact-form-wrapper {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 50px;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .contact-form-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--accent-primary), transparent);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            position: absolute;
            left: 20px;
            top: 16px;
            color: #666;
            pointer-events: none;
            transition: 0.3s ease;
            font-size: 1rem;
        }

        .form-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 16px 20px;
            color: #fff;
            font-size: 1rem;
            font-family: 'Outfit', sans-serif;
            outline: none;
            transition: 0.3s;
        }

        .form-input:focus {
            border-color: var(--accent-primary);
            background: rgba(0, 0, 0, 0.5);
            box-shadow: 0 0 0 4px rgba(0, 210, 106, 0.1);
        }

        .form-input:focus+label,
        .form-input:not(:placeholder-shown)+label {
            top: -12px;
            left: 15px;
            font-size: 0.8rem;
            color: var(--accent-primary);
            background: #000;
            padding: 0 5px;
        }

        textarea.form-input {
            resize: vertical;
            min-height: 120px;
        }

        .btn-send {
            width: 100%;
            padding: 18px;
            background: #fff;
            color: #000;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-send:hover {
            background: var(--accent-primary);
            box-shadow: 0 0 30px var(--accent-glow);
            transform: translateY(-2px);
        }

        @media (max-width: 900px) {
            .contact-container {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .contact-info h1 {
                font-size: 3rem;
            }

            .contact-section {
                padding-top: 120px;
            }
        }
    </style>
</head>

<body>
    <canvas id="glCanvas"></canvas>
    <?php include 'includes/navbar.php'; ?>

    <section class="contact-section">
        <div class="contact-container">
            <!-- Left Side -->
            <div class="contact-info">
                <h1>Let’s build something future-proof.</h1>
                <p>Have a question about our encryption, pricing, or just want to explore how QuickNote can fit your
                    workflow? We're ready to chat.</p>

                <div class="contact-details">
                    <div class="contact-item">
                        <div class="contact-icon">@</div>
                        <span>dianacastillon@gmail.com</span>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">#</div>
                        <span>#QuickNote</span>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">📍</div>
                        <span>Philippines, Zamboanga City</span>
                    </div>
                </div>
            </div>

            <!-- Right Side (Form)-->
            <div class="contact-form-wrapper">
                <form>
                    <div class="form-group">
                        <input type="text" class="form-input" id="name" placeholder=" " required>
                        <label for="name">Your Name</label>
                    </div>

                    <div class="form-group">
                        <input type="email" class="form-input" id="email" placeholder=" " required>
                        <label for="email">Email Address</label>
                    </div>

                    <div class="form-group">
                        <textarea class="form-input" id="message" placeholder=" " required></textarea>
                        <label for="message">How can we help?</label>
                    </div>

                    <button type="submit" class="btn-send">Send Message <span>→</span></button>
                </form>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>

</html>