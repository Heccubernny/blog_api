@extends('layouts.app')

@section('content')
<h1 class="mb-3">All Posts</h1>

<div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search posts...">
</div>

<div id="posts" class="row"></div>
<nav>
    <ul id="pagination" class="pagination"></ul>
</nav>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let currentSearch = '';

async function loadPosts(page = 1, search = '') {
    const token = localStorage.getItem("token");
    let url = `${API_BASE}/posts?per_page=10&page=${page}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;

    let res = await fetch(url, {
        headers: {
            "Authorization": `Bearer ${token}`,
            "Accept": "application/json"
        }
    });
    let data = await res.json();

    let postsDiv = document.getElementById('posts');
    postsDiv.innerHTML = '';

    let result = data.data?.has_posts ? data.data?.data?.data ?? [] : data.data?.data?.data ?? [];

    if (result.length === 0) {
        postsDiv.innerHTML = `<p class="text-center">No posts found.</p>`;
    } else {
        result.forEach(post => {
            postsDiv.innerHTML += `
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5>${post.title}</h5>
                            <p>${post.body.substring(0, 100)}...</p>
                            <a href="/posts/${post.id}" class="btn btn-sm btn-primary">Read More</a>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    // Pagination
    const paginationUl = document.getElementById('pagination');
    paginationUl.innerHTML = '';
    let totalPages = data.data?.data?.last_page ?? 1;

    for (let i = 1; i <= totalPages; i++) {
        let activeClass = i === page ? 'active' : '';
        paginationUl.innerHTML += `
            <li class="page-item ${activeClass}">
                <button class="page-link" onclick="changePage(${i})">${i}</button>
            </li>
        `;
    }
}

function changePage(page) {
    currentPage = page;
    loadPosts(currentPage, currentSearch);
}

document.getElementById('searchInput').addEventListener('input', function(e) {
    currentSearch = e.target.value;
    currentPage = 1;
    loadPosts(currentPage, currentSearch);
});

loadPosts();
</script>
@endpush
