@extends('layouts.app')

@section('content')
<h2>Register</h2>
<form id="registerForm">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Register</button>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('registerForm').addEventListener('submit', async e => {
    e.preventDefault();

    let formData = new FormData(e.target);
    let body = Object.fromEntries(formData.entries());
    const API_BASE = "{{ $API_BASE }}";
    let res = await fetch(`${API_BASE}/auth/register`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(body)
    });

    let data = await res.json();
    if (data.data.token) {
        localStorage.setItem('token', data.data.token);
        alert('Registration  successful');
        window.location.href = '/login';
    } else {
        alert('Registration failed');
    }
});
</script>
@endpush
