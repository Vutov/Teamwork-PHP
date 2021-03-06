<?php namespace Forum\Http\Controllers;

use Carbon\Carbon;
use Forum\Comment;
use Forum\Http\Requests;
use Forum\Http\Requests\NewTopicRequest;
use Forum\Http\Requests\SubmitTopicRequest;
use Forum\Topic;
use Forum\User;
use Illuminate\Support\Facades\Auth;
use Request;

class TopicsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $topics = Topic::latest()->get();
        $data = [];
        foreach ($topics as $topic) {
            $user =User::find($topic['user_id']);
            $author = $user['name'];
            $admin = $user['admin'];
            $time = $topic->created_at->diffForHumans();
            $topic = $topic->toArray();
            $topic['author'] = $author;
            $topic['admin'] = $admin;
            $topic['time'] = $time;
            $tags = preg_split('/[,\s]+/', $topic['tags'], -1, PREG_SPLIT_NO_EMPTY);
            $topic['tags'] = $tags;
            $data[] = $topic;
        }

        return view('all-topics', ['topics' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(NewTopicRequest $request)
    {
        return view('new-topic', [
            'event' => 'Create',
            'title' => 'New Question',
            'controller' => 'create'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(SubmitTopicRequest $request)
    {
        $input = Request::all();
        $tags = preg_replace('/[#$\\/|?!\.@()\]\[\'\":^%\-=*\s]+/',"",  $input['tags']);
        $tags = preg_split('/[,]+/', $tags, -1, PREG_SPLIT_NO_EMPTY);
        $tags = array_unique($tags);
        $topic = new Topic([
            'title' => $input['title'],
            'body' => $input['body'],
            'visits' => 0,
            'tags' => join($tags, ', '),
            'category' => $input['category'],
        ]);
        Auth::user()->topics()->save($topic);
        session()->flash('flash_message', 'New topic created');
        return redirect('/forum');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $topic = Topic::findOrFail($id);
        $comments = Comment::where('topic_id', '=', $id)->latest()->get();
        $data = [];
        foreach($comments as $comment) {
            $time = $comment->created_at->diffForHumans();
            $comment = $comment->toArray();
            $comment['time'] = $time;
            $admin = false;
            if ($comment['user_id']) {
                $admin = User::where('id' , '=', $comment['user_id'])->get()->toArray()[0]['admin'];
            }
            $comment['admin'] = $admin;
            $data[] = $comment;

        }
        //Update view count
        $topic->visits++;
        $topic->push();
        //Sum up the data
        $topic = $topic->toArray();
        $time = Carbon::parse($topic['created_at'])->format('jS F Y \a\t H:m:s');
        $user =User::find($topic['user_id']);
        $author = $user['name'];
        $admin = $user['admin'];
        $tags = preg_split('/[,\s]+/', $topic['tags'], -1, PREG_SPLIT_NO_EMPTY);
        $topic['tags'] = $tags;
        $topic['time'] = $time;
        $topic['author'] = $author;
        $topic['admin'] = $admin;
        $topic['comments'] = $data;

        return view('topic', $topic);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id, Requests\EditRequest $request)
    {
        $topic = Topic::findOrFail($id);
        return view('new-topic', [
            'event' => 'Update',
            'title' => 'Update question',
            'controller' => 'edit',
            'topic' => $topic,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id, SubmitTopicRequest $request)
    {
        $input = Request::all();
        $topic = Topic::findOrFail($id);
        $topic->update($input);

        session()->flash('flash_message', 'You have successfully updated your question!');
        return redirect("/forum/$id");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id, Requests\EditRequest $request)
    {
        Topic::where('id', '=', $id)->delete();
        session()->flash('flash_message', 'Question deleted!');

        return redirect('/');
    }

}
