<?php

use App\Models\Post;
use Illuminate\Http\Response;

if (!function_exists('authorizePost')) {

    function authorizePost(Post $post)
    {
        if ($post->user_id !== auth()->id()) {

            abort(Response::HTTP_FORBIDDEN, 'Unauthorized to access this post.');
        }
        return true;
    }

}
