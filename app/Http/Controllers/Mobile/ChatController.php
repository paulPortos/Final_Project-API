<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Nette\Schema\ValidationException;

class ChatController extends Controller
{
    public function showChat(Request $request) {
        // Get the authenticated user
        $user = $request->user();

        // Validate using Auth
        if (!$user) {
            return response()->json(['error' => 'Cannot find user.'], 400);
        }

        // Fetch chat messages for the user
        // Fetch the latest chat messages, grouped by email
        $chats = Chat::select('id', 'user_id', 'email', 'image_path', 'chat_message', 'created_at')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc') // Ensure the latest messages are prioritized
            ->get()
            ->unique('email'); // Group by unique email

        return response()->json(['chats' => $chats->values()], 200);
    }

    public function showChatLogs(Request $request, $email) {
        $authUser = $request->user();
        $emailExist = User::where('email', $email)->value('email');

        if (!$emailExist) {
            return response()->json(['error' => 'Email does not exist.'], 400);
        }

        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        // Fetch the chat logs between the authenticated user and the specified email
        $chats = Chat::where(function ($query) use ($authUser, $email) {
            $query->where('user_id', $authUser->id)
                ->where('email', $email);
        })
            ->orWhere(function ($query) use ($authUser, $email) {
                $query->where('email', $authUser->email)
                    ->where('user_id', User::where('email', $email)->value('id'));
            })
            ->orderBy('created_at', 'asc') // Optional: Order by message timestamp
            ->get();

        return response()->json(['chats' => $chats], 200);
    }

    public function sendMessage(Request $request) {
        try {
            $messageData = $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
                'image_path' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:2048'],
                'chat_message' => ['nullable'],
            ],[
                'user_id.exists' => 'The sender does not exist.',

                'image_path.mimes' => 'The image must be a file of type: jpg, png, jpeg.',
                'image_path.max' => 'The image may not be greater than 2MB.'
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e], 400);
        }
        $messageData['user_id'] = $request->user()->id;
        $message = Chat::create($messageData);

        return response()->json(['message' => $message], 201);
    }

    public function deleteMessage(Request $request, $id) {
        try {
            // Get the authenticated user
            $user = $request->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized.'], 401);
            }

            // Find the chat message
            $message = Chat::findOrFail($id);

            // Ensure the authenticated user is the owner of the message
            if ($message->user_id !== $user->id) {
                return response()->json(['error' => 'You do not have permission to delete this message.'], 403);
            }

            // Delete the message
            $message->delete();

            return response()->json(['message' => 'Chat message deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Message not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the message.'], 500);
        }
    }
}
