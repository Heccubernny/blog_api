@extends('layouts.app')

@section('content')
<div id="post"></div>
@endsection

@push('scripts')
<script>
async function loadPost() {
    let postId = window.location.pathname.split("/").pop();
    let res = await fetch(`${API_BASE}/posts/${postId}`);
    let data = await res.json();
    let postDiv = document.getElementById('post');
    postDiv.innerHTML = `
        <div class="card">
            <div class="card-body">
                <h2>${data.data.title}</h2>
                <p>${data.data.body}</p>
                <small>Author: ${data.data.author.name}</small>
            </div>
        </div>
    `;
}

loadPost();
</script>
@endpush
