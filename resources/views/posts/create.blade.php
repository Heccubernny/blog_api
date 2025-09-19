@extends('layouts.app')

@section('content')
    <h1>Create Post</h1>

    <form id="createPostForm">
        @csrf
        <div>
            <label>Title</label>
            <input type="text" name="title" required>
        </div>

        <div>
            <label>Body</label>
            <textarea name="body" required></textarea>
        </div>

        <button type="submit">Submit</button>
    </form>

    <div id="message"></div>

    <script>
        document.getElementById('createPostForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            try {
                let res = await fetch("{{ url('/api/posts') }}", {
                    method: "POST",
                    headers: {
                        "Authorization": "Bearer {{ auth()->user()?->api_token ?? '' }}",
                        "Accept": "application/json"
                    },
                    body: formData
                });

                let json = await res.json();

                if (res.ok) {
                    document.getElementById('message').innerHTML = "<p style='color:green'>Post created successfully!</p>";
                    this.reset();
                } else {
                    document.getElementById('message').innerHTML = "<p style='color:red'>" + JSON.stringify(json.message) + "</p>";
                }
            } catch (err) {
                document.getElementById('message').innerHTML = "<p style='color:red'>Something went wrong.</p>";
            }
        });
    </script>
@endsection
