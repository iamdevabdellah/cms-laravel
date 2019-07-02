<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Posts\CreatePostsRequest;
use App\Http\Requests\Posts\UpdatePostRequest;
use Illuminate\Support\Facades\Storage;
use App\Post;
use App\Category;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('posts.index')->with('posts', Post::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create')->with('categories',Category::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePostsRequest $request)
    {
        // upload image to storage
        $image = $request->image->store('posts');
        // create post
        Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'content'=> $request->content,
            'published_at' => $request->published_at,
            'category_id' => $request->category,
            'image'=> $image
        ]);
        // flash message
        session()->flash('success', 'Post created successfully.');
        // redirect user
        return redirect(route('posts.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('posts.create')->with('post', $post)->with('categories', Category::all());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        // this is for security purpose 
        $data = $request->only([
            'title', 'description', 'published_at', 'content'
        ]);
        //check if new image updated
        if($request->hasFile('image')) {
            //upload it
            $image = $request->image->store('posts');
            //delete old image
            //Storage::delete($post->image);
            $post->deleteImage();

            $data['image'] = $image;
        }
        //upload attributes
        $post->update($data);

        //flash message
        session()->flash('success', 'Post updated successfully');

        //redirect user
        return redirect(route('posts.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::withTrashed()->where('id',$id)->firstOrFail();

        // soft delete post or permanent delete + image delete from storage
        if($post->trashed()) {
            $post->deleteImage();
            $post->forceDelete();
        }
        else {
            $post->delete();
        }
        // flash message
        session()->flash('success', 'Post trashed successfully.');
        // redirect user
        return redirect(route('posts.index'));
        
    }

    public function trashed() {

        $trashed = Post::onlyTrashed()->get();
        //dd($trashed);
       return view('posts.index')->with('posts', $trashed);
    }

    public function restore($id) {

        $post = Post::onlyTrashed()->where('id',$id)->firstOrFail();

        $post->restore();

        session()->flash('success', 'Post restored successfully.');

        return redirect()->back();
    }
}
