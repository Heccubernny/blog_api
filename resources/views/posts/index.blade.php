@extends('layouts.app')

@section('content')
<h1 class="mb-3">All Posts</h1>
<div id="posts" class="row"></div>
@endsection

@push('scripts')
<script>
async function loadPosts() {
    let res = await fetch(`${API_BASE}/posts`);
    let data = await res.json();
    let postsDiv = document.getElementById('posts');
    postsDiv.innerHTML = '';
        console.log(data.data.data);

    data.data.data.forEach(post => {
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

loadPosts();
</script>
@endpush
