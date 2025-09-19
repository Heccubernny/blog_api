@extends('authors.layouts.app')

@section('title', 'Manage Posts')

@section('content')
<div class="container py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Manage Your Posts</h1>
        <button class="btn btn-success rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#createPostModal">
            <i class="bi bi-plus-circle me-1"></i> New Post
        </button>
    </div>

    <div class="modal fade" id="createPostModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Create New Post</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="createPostForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea name="body" rows="5" class="form-control rounded-3" required></textarea>
                        </div>
                        <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="publishToggle" name="status" checked>
                        <label class="form-check-label" for="publishToggle">Publish Immediately</label>
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success rounded-pill">
                            <i class="bi bi-check-circle me-1"></i> Publish
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<div class="modal fade" id="editPostModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Post</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPostForm">
                <div class="modal-body">
                    <input type="hidden" name="post_id" id="edit-post-id">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" id="edit-post-title" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="body" id="edit-post-body" rows="5" class="form-control rounded-3" required></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit-post-publish">
                        <label class="form-check-label" for="edit-post-publish">Publish this post</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill">Update Post</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <section class="mb-5">
        <h3 class="mb-3"><i class="bi bi-journal-text me-2"></i>Your Posts</h3>
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="posts-table"></tbody>
            </table>
        </div>
    </section>

    {{-- <section>
        <h3 class="mb-3"><i class="bi bi-trash3 me-2"></i>Trashed Posts</h3>
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Deleted At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="trashed-table"></tbody>
            </table>
        </div>
    </section> --}}
</div>

<style>
    h1, h3 {
        letter-spacing: -0.5px;
    }
    .table thead th {
        font-weight: 600;
    }
    .btn-sm {
        padding: 0.35rem 0.65rem;
        font-size: 0.85rem;
    }
    .modal-content {
        border-radius: 1rem;
    }
</style>

<script>
    const API_BASE = "{{ $API_BASE }}";
    const token = localStorage.getItem("token");

    if (!token) {
        window.location.href = "/login";
    }

    async function loadPosts({ mine = true } = {}) {
        let url = `${API_BASE}/posts`;
        if (mine) url += "?mine=true";
        let res = await fetch(url, { headers: { "Authorization": `Bearer ${token}` } });
        let posts = await res.json();

        let tbody = document.querySelector("#posts-table");
        tbody.innerHTML = "";
        (posts.data?.data ?? []).forEach(post => {
            tbody.innerHTML += `
                <tr>
                    <td class="fw-semibold">${post.title}</td>
                    <td>
                        <span class="badge ${post.status === 'published' ? 'bg-success' : 'bg-secondary'}">
                            ${post.status}
                        </span>
                    </td>
                    <td>${new Date(post.created_at).toLocaleDateString()}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary" onclick="editPost(${post.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="viewPost(${post.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deletePost(${post.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    async function loadTrashed() {
        let res = await fetch(`${API_BASE}/posts/trashed`, { headers: { "Authorization": `Bearer ${token}` } });
        let posts = await res.json();

        let tbody = document.querySelector("#trashed-table");
        tbody.innerHTML = "";

        console.log(posts);
        (posts.data ?? []).forEach(post => {
            tbody.innerHTML += `
                <tr>
                    <td class="fw-semibold">${post.title}</td>
                    <td>${new Date(post.deleted_at).toLocaleDateString()}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-success" onclick="restorePost(${post.id})">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="forceDelete(${post.id})">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    document.getElementById("createPostForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    // Gather form data
    let formData = new FormData(e.target);
    let body = Object.fromEntries(formData.entries());

    let publishToggle = document.getElementById("publishToggle");
    body.status = publishToggle.checked ? "published" : "draft";

    try {
        let res = await fetch(`${API_BASE}/posts`, {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${token}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify(body)
        });

        let data = await res.json();

        if (res.ok) {
            // Reset form and hide modal
            e.target.reset();
            bootstrap.Modal.getInstance(document.getElementById('createPostModal')).hide();
            
            loadPosts();
            loadTrashed();

            alert("Post created successfully!");
        } else {
            alert(data.message || "Failed to create post.");
        }
    } catch (err) {
        console.error("Error creating post:", err);
        alert("An error occurred while creating the post.");
    }
});
  
        async function editPost(id) {
       const post = [...document.querySelectorAll("#posts-table tr")]
        .map(row => ({
            id: row.querySelector("button[onclick^='editPost']").getAttribute("onclick").match(/\d+/)[0],
            title: row.cells[0].textContent,
            status: row.cells[1].textContent.trim(),
            body: row.cells[2]?.textContent || ""
        }))
        .find(p => p.id == id);

    if (!post) return;

    document.getElementById("edit-post-id").value = post.id;
    document.getElementById("edit-post-title").value = post.title;
    document.getElementById("edit-post-body").value = post.body;
    document.getElementById("edit-post-publish").checked = post.status === "published";

    new bootstrap.Modal(document.getElementById('editPostModal')).show();
}

// Handle update submission
document.getElementById("editPostForm").addEventListener("submit", async e => {
    e.preventDefault();
    const id = document.getElementById("edit-post-id").value;
    const title = document.getElementById("edit-post-title").value;
    const body = document.getElementById("edit-post-body").value;
    const publish = document.getElementById("edit-post-publish").checked;

    // Update post
    let res = await fetch(`${API_BASE}/posts/${id}`, {
        method: "PUT",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ title, body })
    });

    if (!res.ok) {
        const errorData = await res.json();
        alert(errorData.message || "Failed to update post");
        return;
    }

    const postStatus = publish ? "published" : "draft";
    const currentStatus = document.querySelector(`#posts-table tr td button[onclick='editPost(${id})']`)
        .closest("tr").querySelector("td:nth-child(2) .badge").textContent;

    if ((postStatus === "published" && currentStatus !== "published") ||
        (postStatus === "draft" && currentStatus === "published")) {
        await fetch(`${API_BASE}/posts/${id}/${publish ? 'publish' : 'unpublish'}`, {
            method: "PATCH",
            headers: { "Authorization": `Bearer ${token}` }
        });
    }

    bootstrap.Modal.getInstance(document.getElementById('editPostModal')).hide();
    loadPosts();
});

function viewPost(id) {
    window.location.href = `/posts/${id}`;
}

        async function deletePost(id) {
            if (!confirm("Are you sure?")) return;
            await fetch(`${API_BASE}/posts/${id}`, {
                method: "DELETE",
                headers: { "Authorization": `Bearer ${token}` }
            });
            loadPosts();
            loadTrashed();
        }

        async function restorePost(id) {
            await fetch(`${API_BASE}/posts/${id}/restore`, {
                method: "PATCH",
                headers: { "Authorization": `Bearer ${token}` }
            });
            loadPosts();
            loadTrashed();
        }

        async function forceDelete(id) {
            if (!confirm("This will permanently delete the post. Continue?")) return;
            await fetch(`${API_BASE}/posts/${id}/force-delete`, {
                method: "DELETE",
                headers: { "Authorization": `Bearer ${token}` }
            });
            loadTrashed();
        }

        async function togglePublish(id, status) {
            let endpoint = status === "published" ? "unpublish" : "publish";
            await fetch(`${API_BASE}/posts/${id}/${endpoint}`, {
                method: "PATCH",
                headers: { "Authorization": `Bearer ${token}` }
            });
            loadPosts();
        }

    loadPosts();
    loadTrashed();
    
</script>
@endsection
