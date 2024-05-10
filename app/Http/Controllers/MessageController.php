<?php

namespace App\Http\Controllers;

use App\Events\Message as EventsMessage;
use App\Events\MessagePosted;
use App\Models\Course;
use App\Models\Message;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Pusher\Pusher;
use Illuminate\Support\Facades\DB;

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
        $userId = Auth::user()->id;
        // return $data;
        $message = new Message();
        $message->room = $request->room;
        $message->sender = Auth::user()->id;
        $message->content = $request->content;
        $message->save();

        $sendData = Message::where('id', $message->id)->with(['sender' => function ($query) use ($userId){
            $query->select('id', 'username');
            // $query->addSelect(DB::raw("IF(id = $userId, true, false) as is_checked"));
        }])->first();
        // return $sendData;
        $this->pusher->trigger($request->channel, $request->event, $sendData);

        // broadcast(new MessagePosted($message->load('sender')))->toOthers();
        // return response()->json(['message' => $message->load('sender')]);
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

    

    public function groupChat(Request $request)
    {
        // return 1;
        $arrCourse = DB::table('course_user')->where('user_id', Auth::user()->id)->where('confirm', true)->pluck('course_id');
        $getCourse = Course::whereIn('id', $arrCourse)->get();

        foreach ($getCourse as $item) {
            if ($item->avatar) {
                $item->avatar = '/storage/images/course/' . $item->avatar;
            }
        }
        // $getCourse->each(function ($course) {

        //     $course_user = $course->users();
        //     $idTeacher = $course_user->first()->id;
        //     $course->nameTeacher = UserInfo::where('user_id', $idTeacher)->first()->name;
        //     $course->numberOfMember = $course_user->count();
        //     $course->numberOfLesson = $course->lessons()->count();
        //     if ($course->avatar) {
        //         $course->avatar = '/storage/images/course/' . $course->avatar;
        //     }
        // });
        return $getCourse;
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
        $userId = Auth::user()->id;
        $messages = Message::where('room', $request->room)->with(['sender' => function ($query) use ($userId){
            $query->select('id', 'username');
            // $query->addSelect(DB::raw("IF(id = $userId, true, false) as is_checked"));
        }])->paginate(50);

        // $messages = Message::with(['sender'])->where('room', $request->room)->orderBy('created_at', 'asc')->get();
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
        broadcast(new MessagePosted($message));
    // return ['message' => $message->load('user')];
        // return $message->load('sender')->toOthers();
        // broadcast(new MessagePosted("fsdfdsfd"));
        // return $message->load('sender')->toOthers();
        // broadcast(new MessagePosted($message->load('sender')))->toOthers();
        // return response()->json(['message' => $message->load('sender')]);
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
