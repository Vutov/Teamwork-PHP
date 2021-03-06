<?php namespace Forum\Http\Controllers;

use Forum\Comment;
use Forum\Http\Requests;
use Forum\Topic;
use Forum\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Requests\ViewProfileRequest $request)
    {
        $comments = Auth::user()->comments->toArray();
        $topics = Auth::user()->topics->toArray();

        return view('profile', ['topics' => $topics, 'comments' => $comments, 'name' => 'You', 'text' => 'Your']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $name = $id;
        $id = User::where('name', '=', $id)->firstOrFail()['id'];
        $topics = Topic::where('user_id', '=', $id)->get()->toArray();
        $comments = Comment::where('user_id', '=', $id)->get()->toArray();

        return view('profile', ['topics' => $topics, 'comments' => $comments, 'name' => $name, 'text' => $name . '\'s']);
    }

}
