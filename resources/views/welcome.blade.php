<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YES Higher Education · welcome</title>
    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;1,14..32,300&display=swap"
        rel="stylesheet">
    <!-- Font Awesome 6 (free) for subtle icons, optional but nice -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #fafafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* refined background – subtle light gradient + soft pattern */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 10% 20%, rgba(220, 38, 38, 0.02) 0%, transparent 30%),
                radial-gradient(circle at 90% 70%, rgba(220, 38, 38, 0.02) 0%, transparent 35%),
                linear-gradient(145deg, #ffffff 0%, #f6f5fa 100%);
            z-index: 0;
        }

        .container {
            text-align: center;
            z-index: 10;
            max-width: 900px;
            padding: 2.5rem 2rem;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 48px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(220, 38, 38, 0.08);
            margin: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        /* logo container – subtle red glow matching your red logo */
        .logo-container {
            margin-bottom: 1.75rem;
            filter: drop-shadow(0 8px 16px rgba(220, 38, 38, 0.18));
            transition: transform 0.2s ease;
        }

        .logo-container:hover {
            transform: scale(1.01);
        }

        .logo {
            max-width: 320px;
            width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
            /* your logo is red – we add a very faint red glow to harmonize */
            filter: drop-shadow(0 4px 8px rgba(185, 28, 28, 0.25));
        }

        h1 {
            font-size: 2.9rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
            color: #0f172a;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
        }

        .accent {
            color: #b91c1c;
            /* deep red – matches typical red logo */
            background: linear-gradient(145deg, #b91c1c, #dc2626);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            position: relative;
        }

        /* subtle underline accent */
        .accent::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: #dc2626;
            border-radius: 2px;
            opacity: 0.25;
            bottom: -2px;
        }

        .tagline {
            font-size: 1.4rem;
            font-weight: 300;
            color: #334155;
            max-width: 600px;
            margin: 0 auto 2rem auto;
            line-height: 1.5;
            font-style: italic;
            border-left: 4px solid #dc2626;
            padding-left: 1.5rem;
            background: rgba(220, 38, 38, 0.02);
            border-radius: 0 20px 20px 0;
        }

        .redirect-card {
            margin: 2.2rem auto 0;
            padding: 1.5rem 2rem;
            background: white;
            border-radius: 100px;
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.03), 0 0 0 1px rgba(220, 38, 38, 0.15);
            display: inline-flex;
            align-items: center;
            gap: 1.25rem;
            backdrop-filter: blur(4px);
            transition: all 0.2s;
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .redirect-card i {
            font-size: 1.8rem;
            color: #b91c1c;
            opacity: 0.8;
        }

        .redirect-text {
            font-size: 1.15rem;
            font-weight: 500;
            color: #1e293b;
        }

        .countdown-badge {
            background: #b91c1c;
            color: white;
            font-weight: 700;
            font-size: 1.6rem;
            padding: 0.2rem 1rem;
            border-radius: 60px;
            line-height: 1;
            box-shadow: 0 4px 10px rgba(185, 28, 28, 0.3);
            min-width: 70px;
            display: inline-block;
            text-align: center;
            letter-spacing: 2px;
        }

        .countdown-label {
            font-size: 0.95rem;
            font-weight: 400;
            color: #64748b;
            margin-top: 0.25rem;
        }

        /* floating shapes – redefined with red accent */
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            background: rgba(220, 38, 38, 0.06);
            border-radius: 50%;
            animation: float 10s infinite ease-in-out;
            backdrop-filter: blur(2px);
            border: 1px solid rgba(220, 38, 38, 0.15);
        }

        .shape:nth-child(1) {
            width: 180px;
            height: 180px;
            top: 10%;
            left: -3%;
            animation-delay: 0s;
            background: radial-gradient(circle, rgba(220, 38, 38, 0.04) 0%, transparent 70%);
        }

        .shape:nth-child(2) {
            width: 250px;
            height: 250px;
            bottom: 5%;
            right: -3%;
            animation-delay: 2s;
            background: radial-gradient(circle, rgba(185, 28, 28, 0.03) 0%, transparent 70%);
        }

        .shape:nth-child(3) {
            width: 120px;
            height: 120px;
            top: 60%;
            left: 8%;
            animation-delay: 4s;
            background: rgba(220, 38, 38, 0.04);
        }

        .shape:nth-child(4) {
            width: 200px;
            height: 200px;
            top: 20%;
            right: 8%;
            animation-delay: 1s;
            background: rgba(185, 28, 28, 0.02);
        }

        .shape:nth-child(5) {
            width: 80px;
            height: 80px;
            bottom: 15%;
            left: 20%;
            animation-delay: 5s;
            background: rgba(220, 38, 38, 0.05);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg) scale(1);
            }

            50% {
                transform: translateY(-30px) rotate(4deg) scale(1.02);
            }
        }

        /* small footer / meta */
        .meta-note {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #64748b;
            z-index: 10;
            background: rgba(255, 255, 255, 0.5);
            padding: 0.5rem 1.5rem;
            border-radius: 40px;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(220, 38, 38, 0.1);
            display: inline-block;
        }

        .meta-note i {
            color: #b91c1c;
            margin: 0 4px;
        }

        /* responsive finesse */
        @media (max-width: 600px) {
            .container {
                padding: 2rem 1.25rem;
                border-radius: 32px;
            }

            h1 {
                font-size: 2.2rem;
                flex-direction: column;
                gap: 0.2rem;
            }

            .accent::after {
                bottom: 2px;
            }

            .tagline {
                font-size: 1.2rem;
                padding-left: 1rem;
            }

            .redirect-card {
                flex-direction: column;
                gap: 0.7rem;
                padding: 1.5rem 1.5rem;
                border-radius: 60px;
            }

            .countdown-badge {
                font-size: 2rem;
                min-width: 90px;
            }
        }
    </style>
</head>

<body>
    <!-- floating abstract shapes with red undertone -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="container">
        <!-- logo container – your red logo will pop against the clean background -->
        <div class="logo-container">
            <!-- using asset helper – logo.png is assumed to be red -->
            <img src="{{ asset('uploads/logo/logo.png') }}" alt="YES Higher Education red logo" class="logo"
                onerror="this.src='https://placehold.co/320x120/b91c1c/white?text=YES+Logo&font=inter';">
        </div>

        <h1>
            <span>Welcome to</span>
            <span class="accent">YES Higher Education</span>
        </h1>

        <p class="tagline">
            <i class="fas fa-quote-left" style="font-size:0.9rem; opacity:0.6; margin-right:6px;"></i>
            Empowering minds, shaping futures through excellence in education
            <i class="fas fa-quote-right" style="font-size:0.9rem; opacity:0.6; margin-left:6px;"></i>
        </p>

        <!-- redirect card with red countdown -->
        <div class="redirect-card">
            <i class="fas fa-arrow-right-to-bracket"></i>
            <span class="redirect-text">Redirecting to login</span>
            <div style="display: flex; flex-direction: column; align-items: center;">
                <span class="countdown-badge" id="countdown">5</span>
                <span class="countdown-label">seconds</span>
            </div>
        </div>

        <!-- subtle meta note (only if you want extra polish) -->
        <div class="meta-note">
            <i class="fas fa-graduation-cap"></i> secure portal · <i class="fas fa-lock" style="font-size:0.8rem;"></i>
            encrypted
        </div>
    </div>

    <script>
        (function () {
            // countdown starting at 5, respecting original behaviour
            let secondsLeft = 5;
            const countdownSpan = document.getElementById('countdown');

            // initial render
            countdownSpan.textContent = secondsLeft;

            const timer = setInterval(() => {
                secondsLeft -= 1;
                countdownSpan.textContent = secondsLeft;

                if (secondsLeft <= 0) {
                    clearInterval(timer);
                    // redirect using laravel route helper (same as original)
                    window.location.href = "{{ route('login') }}";
                }
            }, 1000);
        })();
    </script>

    <!-- small extra: if route helper isn't available in design preview, 
         we show a fallback for the snippet (for standalone viewing). 
         but keep blade syntax -> it will be replaced by Laravel. 
    -->
</body>

</html>