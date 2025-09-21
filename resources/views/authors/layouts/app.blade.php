<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Author Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 240px;
            height: 100vh;
            background: #212529;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
        }
        .sidebar h2 {
            font-size: 1.25rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1rem;
        }
        .sidebar .dashboard-menu {
            display: block;
            color: #adb5bd;
            padding: 10px 20px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .sidebar .dashboard-menu:hover,
        .sidebar .dashboard-menu.active {
            color: #fff;
            background: #0d6efd;
        }
        .main {
            margin-left: 240px;
            padding: 20px;
        }
        .navbar {
            margin-left: 240px;
        }
    </style>
</head>
<body>
     <script>
        // get user info
        async function fetchUserInfo() {
        const API_BASE = "{{ url('/api') }}";
        const token = localStorage.getItem('token');
            try {
                let userRes = await fetch(`${API_BASE}/auth/user`, {
                headers: {
                    "Authorization": `Bearer ${token}`,
                    "Accept": "application/json"
                }
            });
            let user = await userRes.json();
            document.getElementById('author_name').innerHTML = `<a href="{{ url('/') }}" class="text-white text-decoration-none">
    <i class="bi bi-person-circle me-2"></i>${user.data?.name ?? 'Author'}
</a>`;
            } catch (err) {
                console.error("Fetch user error:", err);
            }
        }

   
    fetchUserInfo();
</script>

    <div class="sidebar d-flex flex-column vh-100 p-3 bg-dark text-white">
    <div class="mb-4">
        <h2 class="d-flex align-items-center" id="author_name">
            <a href="{{ url('/') }}" class="text-white text-decoration-none"><i class="bi bi-person-circle me-2"></i> Author</a>
        </h2>
    </div>

    <nav class="flex-grow-1">
        <a href="{{ url('/author/dashboard') }}" 
           class="d-flex dashboard-menu align-items-center mb-2 px-2 py-1 text-white {{ request()->is('author/dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        <a href="{{ url('/author/posts') }}" 
           class="d-flex dashboard-menu align-items-center mb-2 px-2 py-1 text-white {{ request()->is('author/posts*') ? 'active' : '' }}">
            <i class="bi bi-journal-text me-2"></i> Manage Posts
        </a>
    </nav>

    <script>
         async function logout() {
        try {
            await fetch(`${API_BASE}/auth/logout`, {
                method: "POST",
                headers: {
                    "Authorization": `Bearer ${token}`,
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

    <div class="mt-auto">
        <button class="btn btn-danger w-100" onclick="logout()">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
        </button>
    </div>
</div>


    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold text-primary">Author Dashboard</span>
        </div>
    </nav>

    <main class="main">
        @yield('content')
    </main>
@stack('scripts')

   
  

</body>
</html>
