<?php

namespace MVP\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use MVP\Post;
use Illuminate\Support\Facades\Storage;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class PostsController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {      
        $posts = Post::orderBy('created_at', 'desc')->paginate(12);
        return view('articles.index')->with('posts', $posts); 
    }
    
    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        return view('articles.create');
    }
    
    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'featured_image' => 'image|nullable|max:1999'
            ]);
            
            
            // Handle File Upload
            
            if($request->hasFile('featured_image')) {
                
                // Get filename with the extension
                $fileNameWithExt = $request->file('featured_image')->getClientOriginalName();
                
                // Get just file name
                $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                
                // Get just ext
                $extension = $request->file('featured_image')->guessClientExtension();
                
                //  File name to store
                $fileNameToStore = time().'.'.$extension;
                
                // Upload image
                $path = $request->file('featured_image')->storeAs('public/featured_images/', $fileNameToStore);
                
            } else {
                $fileNameToStore = 'noimage.png';
            }
            
            // Create Post
            
            $post = new Post;
            $post->title = $request->input('title');
            $post->body = $request->input('body');
            $post->user_id =  auth()->user()->id;
            $post->featured_image = $fileNameToStore;
            
            $slug = SlugService::createSlug(Post::class, 'slug', $post->title);
            $post->slug = $slug;
            
            $post->save();
            
            return redirect('/articles')->with('success', 'Article Published');
        }
        
        /**
        * Display the specified resource.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
        public function show($slug)
        {

            $post = Post::where('slug', $slug)->firstOrFail();
            return view('articles.show')->with('post', $post);
        }
        
        /**
        * Show the form for editing the specified resource.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
        public function edit($id)
        {
            $post = Post::find($id);
        
            if( (int)auth()->user()->id !== (int) $post->user_id) {
                return redirect('/articles')->with('error', 'Unauthorized Page');
        }

          return view('articles.edit')->with('post', $post);
        }
        
        /**
        * Update the specified resource in storage.
        *
        * @param  \Illuminate\Http\Request  $request
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
        public function update(Request $request, $slug)
        {
            $this->validate($request, [
                'title' => 'required',
                'body' => 'required'
                ]);
                
                // Handle File Upload
                
                if($request->hasFile('featured_image')) {
                    
                    // Get filename with the extension
                    $fileNameWithExt = $request->file('featured_image')->getClientOriginalName();
                    
                    // Get just file name
                    $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                    
                    // Get just ext
                    $extension = $request->file('featured_image')->getClientOriginalExtension();
                    
                    //File name to store
                    $fileNameToStore = $filename.'_'.time().'.'.$extension;
                    
                    // Upload image
                    $path = $request->file('featured_image')->storeAs('public/featured_images/', $fileNameToStore);
                    
                }
                
                
                // Create Post
                $post = Post::find($slug);
                $post->title = $request->input('title');
                $post->body = $request->input('body');
                
                $slug = SlugService::createSlug(Post::class, 'slug', $post->title);
                $post->slug = $slug;
                
                if($request->hasFile('featured_image')) {
                    $post->featured_image = $fileNameToStore;
                }
                
                $post->save();
                
                return redirect('/articles')->with('success', 'Article Updated');
            }
            
            /**
            * Remove the specified resource from storage.
            *
            * @param  int  $id
            * @return \Illuminate\Http\Response
            */
            public function destroy($id)
            {
                $post = Post::find($id);
                
                
                if( (int)auth()->user()->id !== (int) $post->user_id) {
                   return redirect('/articles')->with('error', 'Unauthorized Page');
            }

                if($post->featured_image != 'noimage.png') {
                    // Delete Image
                    Storage::delete('public/featured_images/'.$post->featured_image);
                }
                
                $post->delete();
                return redirect('/articles')->with('success', 'Article Removed');
            }
        }
