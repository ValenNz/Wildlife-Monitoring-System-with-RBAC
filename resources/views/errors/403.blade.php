<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Unauthorized Access</title>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #16a34a, #15803d);
            --bg-dark: #f9fafb;
            --text-dark: #111827;
            --text-gray: #6b7280;
            --card-bg: #ffffff;
            --border-gray: #e5e7eb;
            --success-bg: #ecfdf5;
            --success-text: #059669;
        }

        body {
            background: var(--bg-dark);
            color: var(--text-dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            line-height: 1.6;
        }

        .error-container {
            text-align: center;
            max-width: 500px;
            padding: 2rem;
            background: var(--card-bg);
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-gray);
        }

        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #16a34a;
        }

        .error-code {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0.5rem 0;
            color: #16a34a;
        }

        .error-title {
            font-size: 1.5rem;
            margin: 0.5rem 0 1rem;
            color: var(--text-dark);
        }

        .error-message {
            color: var(--text-gray);
            margin-bottom: 1.5rem;
            font-size: 1rem;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            margin: 0.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(22, 163, 74, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-gray);
            border: 1px solid var(--border-gray);
        }

        .btn-secondary:hover {
            background: rgba(249, 250, 251, 0.8);
            color: var(--text-dark);
        }

        .footer {
            margin-top: 2rem;
            font-size: 0.875rem;
            color: var(--text-gray);
        }

        @media (max-width: 600px) {
            .error-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            .error-code {
                font-size: 2rem;
            }
            .error-title {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">🔒</div>
        <div class="error-code">403</div>
        <h1 class="error-title">Unauthorized Access</h1>
        <p class="error-message">
            You don't have permission to access this resource.
            Please contact your administrator or log in with an account that has the required permissions.
        </p>

        <div class="button-group">
            <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">← Back to Dashboard</a>
            <a href="{{ route('login') }}" class="btn btn-primary">Login as Another User</a>
        </div>

        <div class="footer">
            If you believe this is an error, please contact support.
        </div>
    </div>
</body>
</html>
