<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Models\User;
use App\Models\Setting;
use App\Http\Controllers\Admin\SubjectController as AdminSubjectController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\StatsController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\ParentController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Vendor\ProductController as VendorProductController;
use App\Http\Controllers\Vendor\PostController as VendorPostController;
use App\Models\Product;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\SubCategoryController as AdminSubCategoryController;
use App\Models\Category;
use App\Models\SubCategory;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Self profile endpoints
Route::middleware('auth:sanctum')->group(function(){
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
});

// Parent (guardian) routes
Route::middleware('auth:sanctum')->prefix('parent')->group(function(){
    Route::get('/children', [ParentController::class, 'children']);
    Route::post('/children', [ParentController::class, 'addChild']);
    Route::delete('/children/{id}', [ParentController::class, 'removeChild']);
});

// Admin routes
Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('admin')->group(function () {
    Route::get('/stats', function (Request $request) {
        return response()->json([
            'ok' => true,
            'user' => $request->user()->only(['id','name','email','role']),
            'timestamp' => now()->toISOString(),
        ]);
    });

    Route::get('/users', function () {
        return User::query()
            ->select(['id','name','email','role','created_at'])
            ->orderByDesc('id')
            ->limit(50)
            ->get();
    });

    // Minimal logs endpoint (static for now)
    Route::get('/logs', function () {
        return response()->json([
            ['id' => 1, 'level' => 'info', 'message' => 'App started', 'time' => now()->subMinutes(10)->toISOString()],
            ['id' => 2, 'level' => 'warning', 'message' => 'High memory usage', 'time' => now()->subMinutes(5)->toISOString()],
            ['id' => 3, 'level' => 'error', 'message' => 'Failed job retry scheduled', 'time' => now()->toISOString()],
        ]);
    });

    // Settings endpoints (persisted in DB)
    Route::get('/settings', function () {
        $setting = Setting::query()->first();
        if (!$setting) {
            $setting = Setting::create([
                'site_name' => config('app.name', 'LevelUP'),
                'maintenance' => false,
            ]);
        }
        return response()->json($setting);
    });

    Route::put('/settings', function (Request $request) {
        $data = $request->validate([
            'site_name' => ['required','string','max:255'],
            'maintenance' => ['sometimes','boolean'],
        ]);
        $setting = Setting::query()->first();
        if (!$setting) {
            $setting = new Setting();
        }
        $setting->site_name = $data['site_name'];
        if (array_key_exists('maintenance', $data)) {
            $setting->maintenance = (bool) $data['maintenance'];
        }
        $setting->save();
        return response()->json($setting->refresh());
    });

    // Admin: Subjects
    Route::get('/subjects', [AdminSubjectController::class, 'index']);
    Route::post('/subjects', [AdminSubjectController::class, 'store']);

    // Admin: Books
    Route::get('/books', [AdminBookController::class, 'index']);
    Route::post('/books', [AdminBookController::class, 'store']);

    // Admin: Users management
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{id}', [AdminUserController::class, 'show']);
    Route::post('/users/{id}', [AdminUserController::class, 'update']);
    Route::post('/users/{id}/password', [AdminUserController::class, 'resetPassword']);

    // Admin: Stats (real data)
    Route::get('/dashboard/kpis', [StatsController::class, 'kpis']);
    Route::get('/dashboard/charts/users-growth', [StatsController::class, 'usersGrowth']);
    Route::get('/dashboard/charts/visits-trend', [StatsController::class, 'visitsTrend']);
    Route::get('/dashboard/charts/daily-logins', [StatsController::class, 'dailyLogins']);
    Route::get('/dashboard/charts/sources', [StatsController::class, 'sources']);

    // Admin: Login history per user
    Route::get('/users/{id}/login-logs', function ($id) {
        $limit = (int) request('limit', 50);
        $limit = $limit > 0 && $limit <= 200 ? $limit : 50;
        $logs = DB::table('login_logs')
            ->where('user_id', $id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
        return response()->json($logs);
    });

    // Admin: Catalog (Categories & Subcategories)
    Route::get('/catalog/categories', [AdminCategoryController::class, 'index']);
    Route::post('/catalog/categories', [AdminCategoryController::class, 'store']);
    Route::delete('/catalog/categories/{id}', [AdminCategoryController::class, 'destroy']);

    Route::get('/catalog/subcategories', [AdminSubCategoryController::class, 'index']);
    Route::post('/catalog/subcategories', [AdminSubCategoryController::class, 'store']);
    Route::delete('/catalog/subcategories/{id}', [AdminSubCategoryController::class, 'destroy']);
});

// Vendor (provider) routes
Route::middleware('auth:sanctum')->prefix('vendor')->group(function(){
    // Products
    Route::get('/products', [VendorProductController::class, 'index']);
    Route::post('/products', [VendorProductController::class, 'store']);
    Route::post('/products/{id}', [VendorProductController::class, 'update']);
    Route::delete('/products/{id}', [VendorProductController::class, 'destroy']);

    // Posts
    Route::get('/posts', [VendorPostController::class, 'index']);
    Route::post('/posts', [VendorPostController::class, 'store']);
    Route::delete('/posts/{id}', [VendorPostController::class, 'destroy']);
});

// AI Study Advisor (public)
Route::post('/ai/study-plan', [AIController::class, 'studyPlan']);
// AI Chat (public)
Route::post('/ai/chat', [AIController::class, 'chat']);

// AI Conversations (auth required)
Route::middleware('auth:sanctum')->group(function(){
    Route::get('/ai/conversations', [AIController::class, 'conversations']);
    Route::post('/ai/conversations', [AIController::class, 'createConversation']);
    Route::get('/ai/conversations/{id}', [AIController::class, 'showConversation']);

    // User messaging
    Route::get('/messages/conversations', [MessageController::class, 'conversations']);
    Route::post('/messages/start', [MessageController::class, 'start']);
    Route::post('/messages/start-admin', [MessageController::class, 'startAdmin']);
    Route::get('/messages/conversations/{id}', [MessageController::class, 'show']);
    Route::post('/messages/conversations/{id}/send', [MessageController::class, 'send']);
    Route::post('/messages/conversations/{id}/read', [MessageController::class, 'markRead']);
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);

    // Spaced repetition (reviews)
    Route::get('/reviews/next', [ReviewController::class, 'next']);
    Route::post('/reviews/schedule', [ReviewController::class, 'schedule']);
});

// Public subjects endpoints
Route::get('/subjects', [SubjectController::class, 'index']);
Route::get('/subjects/{slug}', [SubjectController::class, 'show']);

// Public Products endpoints
Route::get('/products', function(){
    $paginator = Product::query()
        ->select(['id','name','price','price_discount','image_path','description','created_at'])
        ->orderByDesc('id')
        ->paginate(20);
    // Map image_path to full URL
    $paginator->getCollection()->transform(function($p){
        if ($p->image_path) {
            $p->image_path = Storage::disk('public')->url($p->image_path);
        }
        return $p;
    });
    return $paginator;
});
// Public Posts endpoints
Route::get('/posts', function(){
    $paginator = Post::query()
        ->select(['id','content','image_path','created_at'])
        ->orderByDesc('id')
        ->paginate(20);
    $paginator->getCollection()->transform(function($p){
        if ($p->image_path) {
            $p->image_path = Storage::disk('public')->url($p->image_path);
        }
        return $p;
    });
    return $paginator;
});
