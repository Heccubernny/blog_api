<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Blog</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">My Blog</a>
        <div id="nav-links">
        </div>
    </div>
</nav>

<div class="container">
    @yield('content')
</div>

<script>
    const API_BASE = "{{ url('/api') }}";
    const TOKEN = localStorage.getItem('token');

    const navLinks = document.getElementById("nav-links");

    if (TOKEN) {
        navLinks.innerHTML = `
            <a class="btn btn-sm btn-outline-light me-2" href="{{ url('/author/dashboard') }}">Dashboard</a>
            <button class="btn btn-sm btn-danger" onclick="logout()">Logout</button>
        `;
    } else {
        navLinks.innerHTML = `
            <a class="btn btn-sm btn-outline-light me-2" href="{{ url('/login') }}">Login</a>
            <a class="btn btn-sm btn-warning" href="{{ url('/register') }}">Register</a>
        `;
    }

    async function logout() {
        try {
            await fetch(`${API_BASE}/auth/logout`, {
                method: "POST",
                headers: {
                    "Authorization": `Bearer ${TOKEN}`,
                    "Accept": "application/json"
                }
            });
        } catch (err) {
            console.error("Logout error:", err);
        }
        localStorage.removeItem("token");
        window.location.href = "/";
    }
</script>

@stack('scripts')
</body>
</html>
