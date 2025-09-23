<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\AIConversation;
use App\Models\AIMessage;

/**
 * AIController
 *
 * Usage & Setup (OpenRouter):
 * 1) Add these to your .env (do NOT hardcode the key in code)
 *    OPENROUTER_API_KEY=sk-or-...
 *    OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
 *    OPENROUTER_MODEL=gpt-4o-mini
 *    # optional (recommended by OpenRouter for attribution)
 *    OPENROUTER_SITE_URL=https://your-site.example
 *    OPENROUTER_APP_NAME=Your App Name
 *
 * 2) Route: POST /api/ai/study-plan
 *
 * 3) Frontend JSON: stage, grade, subjects[], time, style, goal, language (optional)
 */
class AIController extends Controller
{
    public function studyPlan(Request $request)
    {
        $data = $request->validate([
            'stage' => ['required','string'],
            'grade' => ['required','string'],
            'subjects' => ['required','array','min:1'],
            'subjects.*' => ['string'],
            'time' => ['required','string'],
            'style' => ['required','string'],
            'goal' => ['required','string'],
            'language' => ['nullable','in:ar,en'],
        ]);

        $language = $data['language'] ?? 'ar';
        $model = env('OPENROUTER_MODEL', 'gpt-4o-mini');
        $baseUrl = rtrim(env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'), '/');
        $apiKey = env('OPENROUTER_API_KEY');
        if (!$apiKey) {
            return response()->json(['message' => 'Missing OPENROUTER_API_KEY in .env'], 500);
        }

        $system = $language === 'ar'
            ? 'أنت مستشار دراسي خبير للطلاب في مصر (ابتدائي/إعدادي/ثانوي). اصنع خطة واقعية منظمة بمصادر مناسبة.'
            : 'You are an expert Egyptian K-12 study advisor. Create realistic, structured plans with suitable resources.';

        $prompt = [
            'stage' => $data['stage'],
            'grade' => $data['grade'],
            'subjects' => $data['subjects'],
            'time' => $data['time'],
            'style' => $data['style'],
            'goal' => $data['goal'],
            'requirements' => [
                'plan_detail' => 'Weekly plan with daily breakdown for 2-4 weeks. Tailored to Egyptian curriculum.',
                'resources' => 'List Arabic-first resources (YouTube channels, sites, books, platforms).',
                'tips' => 'Actionable tips for consistency, revision, and exam prep.',
                'format' => 'Return in markdown with clear headings and bullet points. Additionally, include a machine-readable JSON schedule in a fenced code block marked as json at the end. The JSON should follow this shape: { "title": string, "weeks": [ { "week": number, "days": [ { "day": string, "items": [ { "subject": string, "task": string, "duration_minutes": number, "resource": string } ] } ], "tips": [string] } ] }. Ensure the JSON is valid and parsable.'
            ]
        ];

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => json_encode($prompt, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
            ],
            'temperature' => 0.7,
        ];

        $headers = [];
        if ($site = env('OPENROUTER_SITE_URL')) {
            $headers['Referer'] = $site;
            $headers['HTTP-Referer'] = $site; // some infra expects this variant
        }
        if ($app = env('OPENROUTER_APP_NAME')) {
            $headers['X-Title'] = $app;
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->withHeaders($headers)
            ->post($baseUrl.'/chat/completions', $payload);

        if ($response->failed()) {
            $status = $response->status();
            $body = $response->json() ?: ['raw' => $response->body()];
            // Try to extract a readable message
            $apiMsg = $body['error']['message'] ?? $body['message'] ?? null;
            $hint = null;
            if ($status === 401) { $hint = 'Unauthorized: check OPENAI_API_KEY.'; }
            if ($status === 403) { $hint = 'Forbidden: check project/org access and model availability.'; }
            if ($status === 404) { $hint = 'Model not found: verify OPENAI_MODEL exists for your account.'; }
            if ($status === 429) { $hint = 'Rate limited or quota exceeded.'; }

            Log::error('OpenRouter chat/completions failed', [
                'status' => $status,
                'error' => $body,
            ]);

            return response()->json([
                'message' => 'OpenRouter request failed',
                'status' => $status,
                'details' => $apiMsg,
                'hint' => $hint,
            ], 500);
        }

        $json = $response->json();
        $content = $json['choices'][0]['message']['content'] ?? '';

        return response()->json([
            'plan' => $content,
            'model' => $model,
        ]);
    }

    /**
     * Simple chat endpoint using OpenRouter chat/completions
     * Request JSON: { messages: [{role:'user'|'assistant'|'system', content:string}], model?: string, temperature?: number }
     */
    public function chat(Request $request)
    {
        $data = $request->validate([
            'messages' => ['required','array','min:1'],
            'messages.*.role' => ['required','in:user,assistant,system'],
            'messages.*.content' => ['required','string'],
            'model' => ['nullable','string'],
            'temperature' => ['nullable','numeric'],
            'conversation_id' => ['nullable','integer','exists:ai_conversations,id'],
        ]);

        $model = $data['model'] ?? env('OPENROUTER_MODEL', 'gpt-4o-mini');
        $baseUrl = rtrim(env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'), '/');
        $apiKey = env('OPENROUTER_API_KEY');
        if (!$apiKey) {
            return response()->json(['message' => 'Missing OPENROUTER_API_KEY in .env'], 500);
        }

        $headers = [];
        if ($site = env('OPENROUTER_SITE_URL')) {
            $headers['Referer'] = $site;
            $headers['HTTP-Referer'] = $site;
        }
        if ($app = env('OPENROUTER_APP_NAME')) {
            $headers['X-Title'] = $app;
        }

        $payload = [
            'model' => $model,
            'messages' => $data['messages'],
            'temperature' => $data['temperature'] ?? 0.7,
        ];

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->withHeaders($headers)
            ->post($baseUrl.'/chat/completions', $payload);

        if ($response->failed()) {
            $status = $response->status();
            $body = $response->json() ?: ['raw' => $response->body()];
            Log::error('OpenRouter chat endpoint failed', ['status' => $status, 'error' => $body]);
            return response()->json([
                'message' => 'OpenRouter request failed',
                'status' => $status,
                'details' => $body['error']['message'] ?? $body['message'] ?? null,
            ], 500);
        }

        $json = $response->json();
        $content = $json['choices'][0]['message']['content'] ?? '';

        // Persist if user is authenticated
        $convId = $data['conversation_id'] ?? null;
        $user = Auth::user();
        if ($user) {
            // Create conversation if not provided
            if (!$convId) {
                $title = substr(strip_tags($data['messages'][0]['content'] ?? 'New chat'), 0, 80);
                $conv = AIConversation::create(['user_id' => $user->id, 'title' => $title ?: 'New chat']);
                $convId = $conv->id;
            } else {
                $conv = AIConversation::where('id', $convId)->where('user_id', $user->id)->first();
                if (!$conv) { return response()->json(['message' => 'Conversation not found'], 404); }
            }

            // Save last user message (only the new one) and assistant reply
            $last = end($data['messages']);
            if ($last && in_array($last['role'], ['user','assistant','system'])) {
                AIMessage::create(['ai_conversation_id' => $convId, 'role' => $last['role'], 'content' => $last['content']]);
            }
            AIMessage::create(['ai_conversation_id' => $convId, 'role' => 'assistant', 'content' => $content]);
        }

        return response()->json(['reply' => $content, 'model' => $model, 'conversation_id' => $convId]);
    }

    // List user's conversations (auth required)
    public function conversations(Request $request)
    {
        $user = $request->user();
        $list = AIConversation::where('user_id', $user->id)
            ->orderByDesc('updated_at')
            ->select(['id','title','updated_at','created_at'])
            ->get();
        return response()->json($list);
    }

    // Create a conversation with an optional title (auth required)
    public function createConversation(Request $request)
    {
        $user = $request->user();
        $data = $request->validate(['title' => ['nullable','string','max:255']]);
        $conv = AIConversation::create([
            'user_id' => $user->id,
            'title' => $data['title'] ?? 'New chat',
        ]);
        return response()->json($conv, 201);
    }

    // Show a conversation with its messages (auth required)
    public function showConversation(Request $request, $id)
    {
        $user = $request->user();
        $conv = AIConversation::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $messages = $conv->messages()->orderBy('created_at')->get(['id','role','content','created_at']);
        return response()->json(['conversation' => $conv->only(['id','title','created_at','updated_at']), 'messages' => $messages]);
    }
}
