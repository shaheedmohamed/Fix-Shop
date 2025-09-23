<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReviewController extends Controller
{
    // Return due reviews for today (simple MVP)
    public function next(Request $request)
    {
        $user = $request->user();
        $now = now();
        $items = Review::where('user_id', $user->id)
            ->where(function($q) use ($now){
                $q->whereNull('due_at')->orWhere('due_at', '<=', $now);
            })
            ->orderBy('due_at', 'asc')
            ->limit(50)
            ->get();
        return response()->json($items);
    }

    // Create/update a review scheduling record based on user feedback
    public function schedule(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'subject' => ['nullable','string','max:100'],
            'item_type' => ['nullable','string','max:50'],
            'item_id' => ['required','string','max:100'],
            // quality: again|hard|good|easy or numeric 0-3
            'quality' => ['required'],
        ]);

        $quality = $this->normalizeQuality($data['quality']); // 0..3

        $review = Review::firstOrNew([
            'user_id' => $user->id,
            'item_id' => $data['item_id'],
        ]);
        if (!$review->exists) {
            $review->subject = $data['subject'] ?? null;
            $review->item_type = $data['item_type'] ?? null;
            $review->repetitions = 0;
            $review->ease = 2.5; // default
            $review->interval = 0;
        }

        // Simplified SM-2 style adjustments
        if ($quality === 0) { // again
            $review->interval = 0;
            $review->repetitions = 0;
            $review->due_at = now()->addHours(8);
            $review->ease = max(1.3, $review->ease - 0.2);
        } else {
            $review->repetitions += 1;
            if ($review->repetitions == 1) {
                $review->interval = 1;
            } elseif ($review->repetitions == 2) {
                $review->interval = 3;
            } else {
                $review->interval = (int) round($review->interval * $review->ease);
            }
            // ease up/down
            if ($quality === 1) { // hard
                $review->ease = max(1.3, $review->ease - 0.15);
            } elseif ($quality === 2) { // good
                $review->ease = $review->ease + 0.0;
            } else { // easy
                $review->ease = min(3.0, $review->ease + 0.1);
            }
            $review->due_at = now()->addDays(max(1, $review->interval));
        }

        $review->user_id = $user->id;
        $review->save();

        return response()->json($review->refresh());
    }

    private function normalizeQuality($q): int
    {
        if (is_numeric($q)) {
            $n = (int) $q;
            return max(0, min(3, $n));
        }
        $map = [
            'again' => 0,
            'hard' => 1,
            'good' => 2,
            'easy' => 3,
        ];
        $k = strtolower((string) $q);
        return $map[$k] ?? 2;
    }
}
