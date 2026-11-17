<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Connexion') — BinB Group</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ------------------ Variables de secours ------------------ */
        :root {
            --c-primary: #2563eb;
            --c-primary-dark: #1d4ed8;
            --c-bg: #f3f4f6;
            --c-surface: #fff;
            --c-text: #111827;
            --c-muted: #6b7280;
            --c-border: #e5e7eb;
            --c-danger: #dc2626;
            --radius: 8px;
        }

        /* ------------------ Layout Auth ------------------ */
        .auth-shell {
            display: flex;
            min-height: 100vh;
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--c-bg);
        }

        /* Panneau gauche (image / branding) */
        .auth-visual {
            display: none;
            flex: 1 1 45%;
            padding: 3rem;
            flex-direction: column;
            justify-content: space-between;
            color: #fff;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(17, 24, 39, 0.95), rgba(37, 99, 235, 0.85)), url('{{ asset("images/auth-bg.jpg") }}') center/cover no-repeat;
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -.01em;
            z-index: 1;
        }

        .auth-brand img {
            height: 44px;
            width: auto;
            object-fit: contain;
        }

        .auth-quote {
            max-width: 440px;
            z-index: 1;
            margin-top: auto;
            margin-bottom: auto;
        }

        .auth-quote h2 {
            font-size: 2.2rem;
            line-height: 1.15;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .auth-quote p {
            color: #d1d5db;
            font-size: 1.05rem;
            line-height: 1.5;
        }

        .auth-footer-visual {
            font-size: .875rem;
            color: #9ca3af;
            z-index: 1;
        }

        /* Panneau droit (formulaire) */
        .auth-form-side {
            flex: 1 1 55%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
            background: var(--c-surface);
        }

        .auth-card {
            width: 100%;
            max-width: 560px; /* <-- Élargi de 420px à 560px */
        }

        .auth-back-link {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            color: var(--c-muted);
            font-size: .875rem;
            text-decoration: none;
            margin-bottom: 2rem;
            transition: color .15s ease;
        }

        .auth-back-link:hover {
            color: var(--c-primary);
        }

        .auth-card-header {
            margin-bottom: 2rem;
        }

        .auth-card-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--c-text);
            margin-bottom: .35rem;
        }

        .auth-card-header p {
            font-size: .95rem;
            color: var(--c-muted);
        }

        /* --- Gestion des erreurs / statut --- */
        .auth-status {
            padding: .75rem 1rem;
            margin-bottom: 1.25rem;
            border-radius: var(--radius);
            background: #dcfce7;
            color: #166534;
            font-size: .9rem;
        }

        .auth-errors {
            padding: .75rem 1rem;
            margin-bottom: 1.25rem;
            border-radius: var(--radius);
            background: #fee2e2;
            color: #991b1b;
            font-size: .9rem;
        }

        .auth-errors ul {
            margin: 0;
            padding-left: 1.1rem;
        }

        /* --- Formulaires --- */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: .4rem;
            font-size: .9rem;
            color: var(--c-text);
        }

        /* ✅ AJOUT DE input[type="tel"] ET select */
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="tel"],
        .form-group select {
            width: 100%;
            padding: .65rem .85rem;
            border: 1px solid var(--c-border);
            border-radius: var(--radius);
            font: inherit;
            font-size: .95rem;
            background: #fff;
            transition: border-color .15s, box-shadow .15s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--c-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        .error {
            color: var(--c-danger);
            font-size: .8rem;
            margin-top: .35rem;
            display: block;
        }

        /* Checkbox "Se souvenir de moi" */
        .check {
            display: inline-flex;
            gap: .5rem;
            align-items: center;
            color: var(--c-muted);
            font-size: .875rem;
            cursor: pointer;
        }
        
        .check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: var(--c-primary);
        }

        /* ✅ AJOUT : Honeypot invisible */
        .hp {
            position: absolute;
            left: -9999px;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }

        /* --- Bouton submit --- */
        .btn {
            display: inline-block;
            border: none;
            border-radius: var(--radius);
            padding: .55rem 1rem;
            font: inherit;
            cursor: pointer;
            text-decoration: none;
            transition: background-color .15s;
        }

        .btn-primary {
            background: var(--c-primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--c-primary-dark);
        }

        .auth-card .btn-primary {
            width: 100%;
            padding: .75rem 1rem;
            font-weight: 600;
            font-size: .95rem;
            margin-top: 1.5rem;
        }

        .auth-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        .auth-links a {
            color: var(--c-primary);
            font-weight: 500;
            font-size: .875rem;
            text-decoration: none;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        .auth-signup {
            text-align: center;
            margin-top: 2rem;
            font-size: .875rem;
            color: var(--c-muted);
        }

        .auth-signup a {
            color: var(--c-primary);
            font-weight: 600;
            text-decoration: none;
        }

        /* --- Responsive --- */
        @media (min-width: 992px) {
            .auth-visual {
                display: flex;
            }
            .auth-form-side {
                background: var(--c-bg);
            }
            .auth-card {
                background: var(--c-surface);
                border: 1px solid var(--c-border);
                border-radius: 12px;
                padding: 3rem 2.5rem;
                box-shadow: 0 10px 30px -10px rgba(17, 24, 39, .08);
            }
        }
    </style>
</head>
<body>

    <div class="auth-shell">
        <!-- ============ PANNEAU GAUCHE (VISUEL) ============ -->
        <aside class="auth-visual">
            <div class="auth-brand">
                <img src="{{ asset('assets/icons/apple-touch-icon.png') }}" alt="Logo BinB Group">
                <span>BinB <span style="color:#93c5fd">group</span></span>
            </div>

            <div class="auth-quote">
                <h2>Votre partenaire pour vos projets immobiliers en Côte d'Ivoire.</h2>
                <p>Connectez-vous pour accéder à votre espace sécurisé et suivre vos projets.</p>
            </div>

            <div class="auth-footer-visual">
                &copy; {{ date('Y') }} BinB Group. Tous droits réservés.
            </div>
        </aside>

        <!-- ============ PANNEAU DROIT (FORMULAIRE) ============ -->
        <main class="auth-form-side">
            <div class="auth-card">

                <a href="{{ url('/') }}" class="auth-back-link">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour à l'accueil
                </a>

                <div class="auth-card-header">
                    <h1>@yield('auth-title', 'Connexion')</h1>
                    <p>@yield('auth-subtitle', 'Entrez vos identifiants pour continuer.')</p>
                </div>

                {{-- Statut de session (ex: mot de passe réinitialisé) --}}
                @if (session('status'))
                    <div class="auth-status">
                        {{ session('status') }}
                    </div>
                @endif
                
                {{-- Contenu injecté par la page login/register/etc. --}}
                @yield('content')

            </div>
        </main>

    </div>

</body>
</html>