<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\BusinessSetting;
use App\Models\Message;
use App\Models\ProductQuery;
use Auth;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (BusinessSetting::where('type', 'conversation_system')->first()->value == 1) {
            $user_id = Auth::user()->id;
            $conversations = Conversation::where('sender_id', $user_id)->orWhere('receiver_id', $user_id)->orderBy('updated_at', 'desc')->paginate(5);
            return view('seller.conversations.index', compact('conversations'));
        } else {
            flash(translate('Conversation is disabled at this moment'))->warning();
            return back();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $conversation = Conversation::findOrFail(decrypt($id));
        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->sender_viewed = 1;
        } elseif ($conversation->receiver_id == Auth::user()->id) {
            $conversation->receiver_viewed = 1;
        }
         Message::where('conversation_id', $conversation->id)->where('user_id','!=', Auth::user()->id)->update([
           'is_read' => 1,
        ]);
        $conversation->save();
        return view('seller.conversations.show', compact('conversation'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        $conversation = Conversation::findOrFail(decrypt($request->id));
        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->sender_viewed = 1;
            $conversation->save();
        } else {
            $conversation->receiver_viewed = 1;
            $conversation->save();
        }
        Message::where('conversation_id', $conversation->id)->where('user_id','!=', Auth::user()->id)->update([
            'is_read' => 1,
        ]);
        return view('frontend.partials.messages', compact('conversation'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function message_store(Request $request)
    {
        $authUser = Auth::user();

        $message = new Message;
        $message->conversation_id = $request->conversation_id;
        $message->user_id = $authUser->id;
        $message->message = $request->message;
        $message->save();

        $conversation = $message->conversation;
        $conversation->sender_viewed = "0";
        $conversation->receiver_viewed = "1";
        $conversation->save();

        return back();
    }
    public function countConversations()
    {
        $conversations = get_seller_message_count();
        return response()->json([
            'conversations' => $conversations
        ]);
    }

}
