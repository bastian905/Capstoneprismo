<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Prismo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f6fa;
        }

        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            color: #667eea;
            font-size: 24px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .avatar-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: #333;
        }

        .user-email {
            font-size: 12px;
            color: #666;
        }

        .logout-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 40px;
            color: white;
            margin-bottom: 30px;
        }

        .welcome-card h2 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .welcome-card p {
            font-size: 16px;
            opacity: 0.9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card h3 {
            color: #999;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
        }

        .info-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .info-card h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-weight: 600;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-primary {
            background: #e7e9ff;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <h1>Prismo</h1>
        </div>
        <div class="user-info">
            @if(auth()->user()->avatar)
                <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="avatar">
            @else
                <div class="avatar-placeholder">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            @endif
            <div class="user-details">
                <span class="user-name">{{ auth()->user()->name }}</span>
                <span class="user-email">{{ auth()->user()->email }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="welcome-card">
            <h2>Welcome back, {{ auth()->user()->name }}! 👋</h2>
            <p>You're successfully logged in to your Prismo dashboard.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="stat-value">{{ \App\Models\User::count() }}</div>
            </div>
            <div class="stat-card">
                <h3>Your Account</h3>
                <div class="stat-value">
                    @if(auth()->user()->google_id)
                        <span class="badge badge-primary">Google</span>
                    @else
                        <span class="badge badge-success">Email</span>
                    @endif
                </div>
            </div>
            <div class="stat-card">
                <h3>Session Status</h3>
                <div class="stat-value">
                    <span class="badge badge-success">Active</span>
                </div>
            </div>
        </div>

        <div class="info-card">
            <h3>Account Information</h3>
            <div class="info-item">
                <span class="info-label">Name</span>
                <span class="info-value">{{ auth()->user()->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Email</span>
                <span class="info-value">{{ auth()->user()->email }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Login Method</span>
                <span class="info-value">
                    @if(auth()->user()->google_id)
                        Google OAuth
                    @else
                        Magic Link (Email)
                    @endif
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Account Created</span>
                <span class="info-value">{{ auth()->user()->created_at->format('M d, Y') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Last Updated</span>
                <span class="info-value">{{ auth()->user()->updated_at->format('M d, Y H:i') }}</span>
            </div>
        </div>
    </div>
</body>
</html>
