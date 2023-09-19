<?php

namespace App\Http\Controllers;

use App\Events\MessagePosted;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class MessageController extends Controller
{
    public function index(Request $request)
    {   
        $messages = Message::with(['sender'])->where('room', 1)->orderBy('created_at', 'asc')->get();
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
        $message->room = $request->input('room', 1);
        $message->sender = Auth::user()->id;
        $message->content = $request->input('content', $request->content);

        $message->save();
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
