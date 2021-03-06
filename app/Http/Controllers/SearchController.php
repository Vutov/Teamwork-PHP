<?php namespace Forum\Http\Controllers;

use Forum\Comment;
use Forum\Http\Requests;
use Forum\Topic;
use Forum\User;
use Request;

class SearchController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function search(Requests\SearchRequest $request)
    {
        $input = Request::all();
        $query = $input['search'];
        $criteria = $input['criteria'];
        $query = preg_split('/\s+/', $query, -1, PREG_SPLIT_NO_EMPTY);
        $data = [];
        foreach ($query as $str) {
            if ($criteria === 'Username') {
                $user = User::where('name', 'LIKE', '%' . $str . '%')->get()->toArray();
                $data[$str] = $user;
            } else if ($criteria === 'Topic title') {
                $topic = Topic::where('title', 'LIKE', '%' . $str . '%')->get()->toArray();
                $data[$str] = $topic;
            } else if ($criteria === 'Topic question') {
                $topic = Topic::where('body', 'LIKE', '%' . $str . '%')->get()->toArray();
                $data[$str] = $topic;
            } else if ($criteria === 'Topic tags') {
                $criteria = 'Topic tag';
                $tag = str_replace('#', '', $str);
                $topic = Topic::where('tags', 'LIKE', '%' . $tag . '%')->get()->toArray();
                $data[$str] = $topic;
            } else if ($criteria === 'Topic category') {
                $criteria = 'Topic categorie';
                $topic = Topic::where('category', 'LIKE', '%' . $str . '%')->get()->toArray();
                $data[$str] = $topic;
            } else if ($criteria === 'Comment title') {
                $comment = Comment::where('title', 'LIKE', '%' . $str . '%')->get()->toArray();
                $data[$str] = $comment;
            } else if ($criteria === 'Comment answer') {
                $comment = Comment::where('body', 'LIKE', '%' . $str . '%')->get()->toArray();
                $data[$str] = $comment;
            } else {
                abort(404);
            }
        }

        return view('search', ['data' => $data, 'criteria' => $criteria]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($criteria, $id)
    {

        if ($criteria === 'cat') {
            $result = Topic::where('category', '=', $id)->get()->toArray();
        } else if ($criteria === 'tag') {
            $result = Topic::where('tags', 'LIKE', '%' . $id . '%')->get()->toArray();
        } else {
            abort(404);
        }
        $final = [];
        foreach ($result as $topic) {
            $tags = preg_split('/[,\s]+/', $topic['tags'], -1, PREG_SPLIT_NO_EMPTY);
            $topic['tags'] = $tags;
            $final[] = $topic;
        }

        return view('search', ['data' => $final, 'criteria' => $criteria, 'query' => $id]);
    }
}
