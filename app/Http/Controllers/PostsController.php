<?php

namespace App\Http\Controllers;

use App\Http\Helpers;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;

class PostsController extends Controller
{
    public function index()
    {
        return PostResource::collection(Post::all())->response()->setStatusCode(200);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|min:15|max:40',
            'body' => 'required|min:40|max:5000',
            'cover' => 'image|mimes:jpg,jpeg,png'
        ]);

        if($request->hasFile('cover')){
            $file = $request->file('cover');
            $folderName = 'covers';
            $fileNameToStore = Helpers::uploadImage($file, $folderName);
        } else
             $fileNameToStore = 'no-image.jpg';

        $post = Post::create([
            'title' => $fields['title'],
            'body' => $fields['body'],
            'cover' => $fileNameToStore,
            'user_id' => auth()->user()->id
        ]);

        return (new PostResource($post))->response()->setStatusCode(200);

    }

    public function show($id)
    {
        $post = Helpers::doesItExist(Post::class, $id);
        if ($post) {
            return (new PostResource(Post::findOrfail($id)))->response()->setStatusCode(200);
        }

        return response()->json([
            'message' => 'Page not Found!'
        ], 404);
    }

    public function update(Request $request, $id) {
        $post = Helpers::doesItExist(Post::class, $id);
        if ($post) {
            if ($request->user()->can('update', $post)) {
                $fields = $request->validate([
                    'title' => 'required|min:15|max:40',
                    'body' => 'required|min:40|max:5000',
                    'cover' => 'image|mimes:jpg,jpeg,png'
                ]);

                if($request->hasFile('cover')){
                    $oldCoverImage = $post->cover;
                    $file = $request->file('cover');
                    $folderName = 'covers';
                    $fileNameToStore = Helpers::uploadImage($file, $folderName);

                    if ($post->cover != 'no-image.jpg')
                        Storage::delete('public/covers/'. $oldCoverImage);

                    $fields['cover'] = $fileNameToStore;
                }
                $post->update($fields);

                return (new PostResource($post))->response()->setStatusCode(200);
            } else
                return response()->json([
                    'message' => 'You are not authorized to do this action!'
                ], 403);

        }
            return response()->json([
                'message' => 'Page not Found!'
            ], 404);
    }

    public function destroy(Request $request, $id) {
        $post = Helpers::doesItExist(Post::class, $id);
        if (isset($post)) {
            if ($request->user()->can('delete', $post)) {
                $coverImage = $post->cover;
                if ($coverImage != 'no-image.png')
                    Storage::delete('public/covers/'.$coverImage);

                Post::destroy($id);
                return response()->json([
                    'message' =>'Post deleted successfully.'
                ], 200);
            } else
                return response()->json([
                    'message' => 'You are not authorized to do this action!'
                ], 403);

        }

        return response()->json([
            'message' => 'Page not Found!'
        ], 404);
    }
}
