<?php

namespace App\Http\Controllers;

use App\Events\Message as EventsMessage;
use App\Events\MessagePosted;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Pusher\Pusher;

class MessageController extends Controller
{
    private $pusher;

    public function __construct()
    {
        $config = Config::get('broadcasting.connections.pusher');

        $options = [
            'cluster' => $config['options']['cluster'],
            'encrypted' => $config['options']['encrypted']
        ];

        $this->pusher = new Pusher(
            $config['key'],
            $config['secret'],
            $config['app_id'],
            $options
        );
    }

    public function sendMessage(Request $request) {
        // if (!Auth::check()) {
        //     return new Response('Forbidden', 403);
        // }
        $data = $request->json()->all();
        $data['user'] = Auth::user()->id;
        $this->pusher->trigger('presence-chat', 'send-message', $data);
        // $message = new Message();
        // $message->room = $request->input('room', 'private-chat');
        // $message->sender = Auth::user()->id;
        // $message->content = $request->input('content', $request->content);
        // $message->save();
        // return $message->load('sender')->toOthers();
        // broadcast(new MessagePosted("fsdfdsfd"));
        // broadcast(new MessagePosted($message->load('sender')))->toOthers();

        // event(new MessagePosted('aaaa'));

    }

    public function authorizeUser(Request $request) {
        if (!Auth::check()) {
            return new Response('Forbidden', 403);
        }

        $presenceData = ['name' => Auth::user()->username];
        echo $this->pusher->presence_auth(
            $request->input('channel_name'), 
            $request->input('socket_id'),
            Auth::user()->id,
            $presenceData
        );
    }

    public function test(Request $request)
    {
    //     $event = new SendMessageEvent($data);
    // event($event);
        // event(new MessagePosted('aaaa'));
        // event(new EventsMessage($request->username, $request->message));
        return 1;
    }
    
    public function index(Request $request)
    {   
        $messages = Message::with(['sender'])->where('room', 2)->orderBy('created_at', 'asc')->get();
        return $messages;
        // $lesson = Lesson::where('slug', $slug)->with('comments')->first();

        // foreach ($lesson->comments as $comment) {
        //     $user = UserInfo::where('user_id', $comment->user_id)->first();
        //     $comment->avatar = $user->avatar;
        //     $comment->name = $user->name;
        //     $comment->username = $comment->with('user')->first()->username;
        // }
        // return $lesson;
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $user = Auth::user()->id;
        // $message = new Message();
        // $message->fill($request->all());
        // $message->user_id = $user;
        // $message->save();
        // $message = new Message();
        // $message->fill($request->all());
        // $message->sender = Auth::user()->id;
        // broadcast(new MessagePosted($message->load('sender')))->toOthers();
        // return statusResponse2(200, 200, 'Thêm mới thành công!', '');

        $message = new Message();
        $message->room = $request->input('room', 2);
        $message->sender = Auth::user()->id;
        $message->content = $request->input('content', $request->content);
        $message->save();
        // return $message->load('sender')->toOthers();
        // broadcast(new MessagePosted("fsdfdsfd"));
        broadcast(new MessagePosted($message->load('sender')))->toOthers();
        return response()->json(['message' => $message->load('sender')]);
    }

    /**
     * Display the specified resource.
     */
   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user_id = Auth::user()->id;
        $message = Message::findOrFail($request->id);
        // $this->authorize('update', $message);

            $message->fill($request->all());
            $message->user_id = $user_id;
            $message->save();
        
        return statusResponse2(200, 200, 'Cập nhật thành công!', '');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = Message::findOrFail($id); 
        // $this->authorize('delete', $message);
        Message::destroy($id);

        return statusResponse2(200, 200, 'Xóa thành công!', '');
    }
}