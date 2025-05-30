<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate; 

use App\Services\Messaging\MessageServiceFactory;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'platform' => 'required|in:Telegram,Whatsapp,Discord,Slack',
            'recipients' => 'required|string',
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $path = $request->file('attachment')?->store('attachments', 'public');

        $message = Message::create([
            'user_id' => auth()->id(),
            'platform' => $request->platform,
            'recipients' => json_encode(explode(',', $request->recipients)),
            'message' => $request->message,
            'attachment' => $path,
        ]);

        app(MessageServiceFactory::class)
            ->make($request->platform)
            ->sendMessage($message);

        Log::info('Message sent', [
            'user' => auth()->id(),
            'platform' => $request->platform
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Message sent successfully.',
                'data' => $message
            ], 201);
        }

        return redirect()->route('messages.index')->with('success', 'Message sent!');
    }

    public function create()
    {
        return view('messages.create');
    }


    public function index()
    {
        $messages = Gate::allows('access-user-metrics')
            ? Message::latest()->paginate(10) 
            : Message::where('user_id', Auth::id())->latest()->paginate(10);

        $users = Gate::allows('access-user-metrics') ? User::all() : collect([Auth::user()]);

        return view('messages.index', compact('messages', 'users'));
    }

    public function getUserMessages(Request $request, $userId)
    {
        Gate::authorize('access-user-metrics');

        $messages = Message::where('user_id', $userId)
                           ->latest()
                           ->get();

        return response()->json($messages);
    }
}
