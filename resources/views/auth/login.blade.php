@extends('layouts.app')

@section('content')
<h2>Login</h2>
<form id="loginForm">
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('loginForm').addEventListener('submit', async e => {
    e.preventDefault();

    let formData = new FormData(e.target);
    let body = Object.fromEntries(formData.entries());
    const API_BASE = "{{ $API_BASE }}";
    let res = await fetch(`${API_BASE}/auth/login`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(body)
    });

    let data = await res.json();
    if (data.data.token) {
        localStorage.setItem('token', data.data.token);
        alert('Login successful');
        window.location.href = '/author/dashboard';
    } else {
        alert('Login failed');
    }
});
</script>
@endpush
