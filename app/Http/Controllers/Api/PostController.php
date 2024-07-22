<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        // get all posts
        $posts =  Post::latest()->paginate(5);

        // return collection of posts as a resource
        /* New digunakan untuk memberi tau bahwa buat object baru. 
        tanpa new PHP tidak akan mengenali apa yang kita maksudkan */
        return new PostResource(true, 'List Data Posts', $posts);
    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'content'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //create post
        $post = Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
        ]);

        //return response
        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

    public function show($id)
    {
        //find post by ID
        $post = Post::find($id);

        // Jika post tidak ditemukan, kirim pesan kesalahan
        if (!$post) {
            return new PostResource(false, 'Post dengan ID ' . $id . ' tidak ditemukan!', null);
        }

        //return single post as a resource
        return new PostResource(true, 'Detail Data Post!', $post);
    }

    public function update(Request $request, $id)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required'
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //find post by ID
        $post = Post::find($id);

        // Jika post tidak ditemukan, kirim pesan kesalahan
        if (!$post) {
            return new PostResource(false, 'Post dengan ID ' . $id . ' tidak ditemukan!', null);
        }

        //check if image is not empty
        if ($request->hasFile('image')) {

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            /* basename() mengembalikan bagian terakhir dari sebuah path ke file atau direktori yang diberikan
            Kemudian, path lengkap ini digunakan sebagai argumen untuk menghapus file */
            Storage::delete('public/posts/' . basename($post->image));

            //update post with new image
            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        } else {

            //update post without image
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        }

        //return response
        return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
    }

    public function destroy($id)
    {
        //find post by ID
        $post = Post::find($id);

        // Jika post tidak ditemukan, kirim pesan kesalahan
        if (!$post) {
            return new PostResource(false, 'Post dengan ID ' . $id . ' tidak ditemukan!', null);
        }

        //delete image
        Storage::delete('public/posts/' . basename($post->image));

        //delete post database
        $post->delete();

        //return response
        return new PostResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}
