<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPK AHP-SAW Mentor Magang</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="assets/css/pages/auth.css">
    <link rel="stylesheet" href="assets/css/modern-custom.css">
    <link rel="icon" href="assets/images/favicon.svg" type="image/svg+xml">
</head>

<body>
    <div id="auth">

        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <h1 class="auth-title">SPK AHP-SAW</h1>
                    <p class="auth-subtitle mb-4">Pemilihan Mentor Magang Internal</p>
                    <form action="login-act.php" method="post">
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" class="form-control form-control-xl" placeholder="Username" name="username">
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl" placeholder="Password" name="password">
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Log in</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right" class="d-flex align-items-center justify-content-center text-white position-relative">
                    <!-- Floating abstract bg circles -->
                    <div class="bg-bubble bubble-1"></div>
                    <div class="bg-bubble bubble-2"></div>
                    <div class="bg-bubble bubble-3"></div>
                    
                    <div class="text-center px-5 py-4" style="z-index: 5;">
                        <div class="login-right-logo mb-4">
                            <i class="bi bi-trophy-fill" style="font-size: 5.5rem; filter: drop-shadow(0 8px 16px rgba(0,0,0,0.15)); display: inline-block; animation: floatIcon 6s ease-in-out infinite;"></i>
                        </div>
                        <h2 class="text-white font-extrabold mb-3" style="font-size: 2.2rem; text-shadow: 0 4px 12px rgba(0,0,0,0.15);">SPK PEMILIHAN MENTOR</h2>
                        <p class="lead text-white-50 mx-auto" style="max-width: 480px; font-size: 1.1rem; line-height: 1.6; text-shadow: 0 2px 4px rgba(0,0,0,0.15);">
                            Sistem Pendukung Keputusan Pemilihan Mentor Program Magang Internal Menggunakan Integrasi Metode AHP & SAW.
                        </p>
                        <div class="mt-4">
                            <span class="badge bg-white text-primary px-3 py-2 font-bold" style="font-size: 0.85rem; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 30px !important;">Kelompok 5</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
