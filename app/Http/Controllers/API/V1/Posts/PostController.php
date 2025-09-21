<?php

namespace App\Http\Controllers\API\V1\Posts;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Traits\ApiResponse;
use Heccubernny\ActivityTracker\Facades\ActivityTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = auth('api')->user();
        $query = Post::query()->with('user');

        $hasPosts = $user ? $user->posts()->exists() : false;

        // Apply "mine" filter if requested and user has posts
        if ($request->boolean('mine') && $hasPosts) {
            $query->where('user_id', $user->id);
        } else {
            // Default visibility rules
            if ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } else {
                $query->where('status', 'published');
            }
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('body', 'LIKE', "%{$search}%");
            });
        }

        // Date filter
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        // Paginate results
        $posts = $query->latest()->paginate($request->get('per_page', 10));

        return $this->successResponse([
            'data' => PostResource::collection($posts)->response()->getData(true),
            'has_posts' => $hasPosts,
        ], 'Posts fetched successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePostRequest $request)
    {
        //
        $post = auth()->user()->posts()->create($request->validated());
        return $this->successResponse(new PostResource($post), 'Post created successfully', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Post $post)
    {
        if (!auth()->check() && $post->status !== 'published') {
            return $this->errorResponse('Post not available', 403);
        }

        if (auth()->check() && $post->status === 'draft' && $post->user_id !== auth()->id()) {
            return $this->errorResponse('You are not allowed to view this draft', 403);
        }

        $user = auth()->user();

        $post->logActivity('viewed Single post', ['description' => 'Post was viewed by user']);


        // $post->update(['title' => 'Updated']);

        // $post->save();

        // // manually log activity


        activity()->log([
            // 'user_id' => auth()->guard('api')->id(),
    // 'user_type' => get_class(auth()->guard('api')->user()),
            'name' => 'view post',
            'description' => 'User viewed a post',
            'properties' => ['type' => 'monthly'],
            'subject' => $post,
        ]);


        $post->logActivity('published', ['description' => 'Post was published']);


        return $this->successResponse(new PostResource($post->load('user')), 'Post retrieved');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePostRequest $request
     * @param  Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePostRequest $request, Post $post)
    {

        authorizePost($post);
        $post->update($request->validated());
        return $this->successResponse(new PostResource($post), 'Post updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post)
    {

        authorizePost($post);

        $post->delete();
        return $this->successResponse(null, 'Post deleted successfully');
    }

    public function publish(Post $post)
    {
        $this->authorizePost($post);

        if ($post->status === 'published') {
            return $this->errorResponse('Post is already published', 400);
        }

        $post->update(['status' => 'published']);

        return $this->successResponse(new PostResource($post), 'Post published successfully');
    }

    public function unpublish(Post $post)
    {
        $this->authorizePost($post);

        if ($post->status === 'draft') {
            return $this->errorResponse('Post is already a draft', 400);
        }

        $post->update(['status' => 'draft']);

        return $this->successResponse(new PostResource($post), 'Post unpublished successfully');
    }


    public function trashed()
    {
        $posts = Post::onlyTrashed()
        ->where('user_id', auth()->id())
        ->latest()
        ->paginate(10);

        return $this->successResponse(
            PostResource::collection($posts)->response()->getData(true),
            'Trashed posts fetched successfully'
        );
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()->where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $post->restore();

        return $this->successResponse(new PostResource($post), 'Post restored successfully');
    }

    //Permanently delete
    public function forceDelete($id)
    {
        $post = Post::onlyTrashed()->where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $post->forceDelete();

        return $this->successResponse(null, 'Post permanently deleted');
    }

}
