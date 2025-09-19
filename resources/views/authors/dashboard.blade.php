@extends('authors.layouts.app')

@section('title', 'Author Dashboard')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 id="greeting" class="fw-bold text-primary"></h1>
        <p class="text-muted">Here’s a quick overview of your activity.</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body text-center">
                    <i class="bi bi-pencil-square display-5 text-primary"></i>
                    <h5 class="mt-3 fw-semibold">Total Posts</h5>
                    <h2 id="total-posts" class="fw-bold">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body text-center">
                    <i class="bi bi-journal-check display-5 text-success"></i>
                    <h5 class="mt-3 fw-semibold">Published</h5>
                    <h2 id="published-posts" class="fw-bold">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body text-center">
                    <i class="bi bi-trash3 display-5 text-danger"></i>
                    <h5 class="mt-3 fw-semibold">Trashed</h5>
                    <h2 id="trashed-posts" class="fw-bold">0</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-light fw-semibold">Post Status Overview</div>
        <div class="card-body">
            <canvas id="postsChart" height="120"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const API_BASE = "{{ $API_BASE }}";
    const token = localStorage.getItem("token");

    if (!token) {
        window.location.href = "/login"; 
    }

    async function loadDashboard() {
        try {
            let userRes = await fetch(`${API_BASE}/auth/user`, {
                headers: {
                    "Authorization": `Bearer ${token}`,
                    "Accept": "application/json"
                }
            });
            let user = await userRes.json();

            document.getElementById('greeting').textContent = `Welcome, ${user.data?.name ?? 'Author'}`;

            // Fetch posts
            let postsRes = await fetch(`${API_BASE}/posts?per_page=10`, {
                headers: {
                    "Authorization": `Bearer ${token}`,
                    "Accept": "application/json"
                }
            });
            let posts = await postsRes.json();
            // Filter user’s posts
            let myPosts = posts.data?.data?.filter(p => p.author.id === user.data?.id) ?? [];

            let totalPosts = myPosts.length;
            let publishedPosts = myPosts.filter(p => p.status === "published").length;
            let trashedPosts = myPosts.filter(p => p.deleted_at !== null).length;

            document.getElementById('total-posts').textContent = totalPosts;
            document.getElementById('published-posts').textContent = publishedPosts;
            document.getElementById('trashed-posts').textContent = trashedPosts;

            const ctx = document.getElementById('postsChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Published', 'Drafts', 'Trashed'],
                    datasets: [{
                        data: [
                            publishedPosts,
                            myPosts.filter(p => p.status === "draft").length,
                            trashedPosts
                        ],
                        backgroundColor: ['#198754', '#6c757d', '#dc3545']
                    }]
                },
                options: {
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

        } catch (err) {
            console.error("Error loading dashboard:", err);
        }
    }

    loadDashboard();
</script>
@endsection
