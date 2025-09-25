<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BuyerSellerConversation;
use App\Models\BuyerSellerMessage;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function start(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $data = $request->validate([
            'seller_id' => ['required', 'integer', 'exists:users,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id']
        ]);

        $sellerId = $data['seller_id'];
        $productId = $data['product_id'] ?? null;

        // Check if seller exists and is a provider
        $seller = User::find($sellerId);
        if (!$seller || $seller->role !== 'provider') {
            return response()->json(['message' => 'Invalid seller'], 400);
        }

        // Don't allow self-chat
        if ($user->id === $sellerId) {
            return response()->json(['message' => 'Cannot chat with yourself'], 400);
        }

        // Allow any authenticated user to chat with sellers (not just buyers)
        // Admin, buyers, and even other providers can chat with sellers

        // Find existing conversation or create new one
        $conversation = BuyerSellerConversation::where('buyer_id', $user->id)
            ->where('seller_id', $sellerId)
            ->where('product_id', $productId)
            ->first();

        if (!$conversation) {
            $conversation = BuyerSellerConversation::create([
                'buyer_id' => $user->id,
                'seller_id' => $sellerId,
                'product_id' => $productId,
                'last_message_at' => now()
            ]);

            // Add initial message with product link if provided
            if ($productId) {
                $product = Product::find($productId);
                $initialMessage = "Chat started about product: {$product->name} - /products/{$productId}";
                BuyerSellerMessage::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $user->id,
                    'body' => $initialMessage
                ]);
            }
        }

        return response()->json(['id' => $conversation->id, 'message' => 'Chat started']);
    }

    public function conversations(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $conversations = BuyerSellerConversation::where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->with(['buyer:id,name', 'seller:id,name', 'product:id,name', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->orderByDesc('updated_at')
            ->get();

        return response()->json($conversations);
    }

    public function messages(Request $request, $conversationId)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $conversation = BuyerSellerConversation::where('id', $conversationId)
            ->where(function($q) use ($user) {
                $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
            })
            ->first();

        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->orderBy('created_at')
            ->get();

        \Log::info('Messages for conversation ' . $conversationId . ':', $messages->toArray());

        return response()->json($messages);
    }

    public function sendMessage(Request $request, $conversationId)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $conversation = BuyerSellerConversation::where('id', $conversationId)
            ->where(function($q) use ($user) {
                $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
            })
            ->first();

        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        $data = $request->validate([
            'content' => ['required', 'string', 'max:5000']
        ]);

        $message = BuyerSellerMessage::create([
            'conversation_id' => $conversationId,
            'sender_id' => $user->id,
            'body' => $data['content']
        ]);

        $conversation->update(['last_message_at' => now()]);

        return response()->json($message->load('sender:id,name'), 201);
    }
}
