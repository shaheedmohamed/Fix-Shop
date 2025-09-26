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

// Admin: list products with simple pagination
Route::get('/admin/products', function(){
    try{
        $limit = (int) request('limit', 30);
        $limit = $limit > 0 && $limit <= 1000 ? $limit : 30;
        $page = max(1, (int) request('page', 1));

        $q = Product::with(['user:id,name,store_name'])
            ->select(['id','user_id','name','price','price_discount','image_path','created_at']);

        $items = $q->orderByDesc('id')
            ->skip(($page - 1) * $limit)
            ->take($limit + 1)
            ->get();

        $hasMore = $items->count() > $limit;
        if ($hasMore) $items = $items->slice(0, $limit)->values();

        return response()->json([
            'data' => $items,
            'page' => $page,
            'limit' => $limit,
            'has_more' => $hasMore,
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
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

    // Admin: Products listing
    Route::get('/products', function(){
        $products = Product::with('user:id,name,store_name')->select(['id','name','price','image_path','user_id','created_at'])->orderByDesc('id')->paginate(20);
        
        // Fix image URLs
        $products->getCollection()->transform(function($product){
            if ($product->image_path) {
                $url = Storage::disk('public')->url($product->image_path);
                $product->image_path = str_replace('http://localhost', request()->getSchemeAndHttpHost(), $url);
            }
            return $product;
        });
        
        return $products;
    });

    // Admin: Single product for editing
    Route::get('/products/{id}', function($id){
        $product = Product::with('user:id,name,store_name')->findOrFail($id);
        if ($product->image_path) {
            $product->image_path = Storage::disk('public')->url($product->image_path);
        }
        return $product;
    });

    // Admin: Update product
    Route::post('/products/{id}', function(Request $request, $id){
        $product = Product::findOrFail($id);
        
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string','max:5000'],
            'price' => ['required','numeric','min:0'],
            'price_discount' => ['nullable','numeric','min:0'],
            'category_id' => ['nullable','integer','exists:categories,id'],
            'subcategory_id' => ['nullable','integer','exists:sub_categories,id'],
            'brand' => ['nullable','string','max:100'],
            'color' => ['nullable','string','max:50'],
            'unit' => ['nullable','string','max:20'],
            'size' => ['nullable','string','max:50'],
            'sku' => ['nullable','string','max:255'],
            'stock_qty' => ['nullable','integer','min:0'],
            'min_order_qty' => ['nullable','integer','min:1'],
            'status' => ['nullable','string','in:active,inactive,pending,rejected'],
            'admin_notes' => ['nullable','string','max:1000'],
            'seo_title' => ['nullable','string','max:255'],
            'seo_description' => ['nullable','string'],
            'seo_keywords' => ['nullable','string'],
            'image' => ['nullable','image','max:4096']
        ]);

        if ($request->hasFile('image')) {
            if ($product->image_path) Storage::disk('public')->delete($product->image_path);
            $product->image_path = $request->file('image')->store('img', 'public');
        }

        foreach ($data as $key => $value) {
            if ($key !== 'image') {
                $product->{$key} = $value;
            }
        }
        
        $product->save();
        return response()->json($product);
    });

    // Admin: Delete product
    Route::delete('/products/{id}', function($id){
        $product = Product::findOrFail($id);
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    });

    // Admin: Providers listing
    Route::get('/providers', function(){
        return User::where('role', 'provider')->select(['id','name','store_name','email','phone','created_at'])->orderByDesc('id')->paginate(20);
    });

    // Admin: Chats listing
    Route::get('/chats', function(){
        return App\Models\BuyerSellerConversation::with(['buyer:id,name', 'seller:id,name', 'product:id,name'])
            ->select(['id','buyer_id','seller_id','product_id','created_at','updated_at'])
            ->orderByDesc('updated_at')
            ->paginate(20);
    });
});

// Vendor (provider) routes
Route::middleware('auth:sanctum')->prefix('vendor')->group(function(){
    // Products
    Route::get('/products', [VendorProductController::class, 'index']);
    Route::get('/products/{id}', [VendorProductController::class, 'show']);
    Route::post('/products', [VendorProductController::class, 'store']);
    Route::post('/products/{id}', [VendorProductController::class, 'update']);
    Route::delete('/products/{id}', [VendorProductController::class, 'destroy']);

    // Posts
    Route::get('/posts', [VendorPostController::class, 'index']);
    Route::post('/posts', [VendorPostController::class, 'store']);
    Route::delete('/posts/{id}', [VendorPostController::class, 'destroy']);
    
    // Communications (vendor chat conversations)
    Route::get('/communications', function(){
        $user = request()->user();
        if ($user->role !== 'provider') return response()->json(['message'=>'Forbidden'], 403);
        
        return App\Models\BuyerSellerConversation::where('seller_id', $user->id)
            ->with(['buyer:id,name,email', 'product:id,name', 'messages' => function($q) {
                $q->latest()->limit(1)->with('sender:id,name');
            }])
            ->orderByDesc('updated_at')
            ->get();
    });
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

// Public Catalog endpoints
Route::get('/catalog/categories', function(){
    return Category::with('subcategories:id,category_id,name,slug')->select(['id','name','slug'])->orderBy('name')->get();
});
Route::get('/catalog/subcategories', function(){
    return SubCategory::select(['id','category_id','name','slug'])->orderBy('name')->get();
});

// Chat endpoints
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/chat/start', [App\Http\Controllers\ChatController::class, 'start']);
    Route::get('/chat/conversations', [App\Http\Controllers\ChatController::class, 'conversations']);
    Route::get('/chat/conversations/{id}/messages', [App\Http\Controllers\ChatController::class, 'messages']);
    Route::post('/chat/conversations/{id}/messages', [App\Http\Controllers\ChatController::class, 'sendMessage']);
});

// Service Requests endpoints
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/service-requests', function(Request $request) {
        $user = $request->user();
        
        $data = $request->validate([
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'budget' => 'required|string',
            'location' => 'nullable|string|max:255',
            'deadline' => 'nullable|date|after:today',
            'attachments.*' => 'nullable|image|max:2048'
        ]);
        
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('img', 'public');
                $attachments[] = $path;
            }
        }
        
        $serviceRequest = App\Models\ServiceRequest::create([
            'user_id' => $user->id,
            'category' => $data['category'],
            'title' => $data['title'],
            'description' => $data['description'],
            'budget' => $data['budget'],
            'location' => $data['location'] ?? null,
            'deadline' => $data['deadline'] ?? null,
            'attachments' => $attachments
        ]);
        
        return response()->json($serviceRequest, 201);
    });
    
    Route::get('/service-requests', function(Request $request) {
        $user = $request->user();
        
        $requests = App\Models\ServiceRequest::where('user_id', $user->id)
            ->with(['offers' => function($q) {
                $q->with('provider:id,name,store_name');
            }])
            ->orderByDesc('created_at')
            ->get();
            
        return response()->json($requests);
    });
});

// Posts endpoints
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/posts', function(Request $request) {
        $user = $request->user();
        
        $data = $request->validate([
            'content' => 'required|string|max:2000',
            'image' => 'nullable|image|max:2048'
        ]);
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('img', 'public');
        }
        
        $post = App\Models\Post::create([
            'user_id' => $user->id,
            'content' => $data['content'],
            'image_path' => $imagePath
        ]);
        
        $post->load('user:id,name');
        return response()->json($post, 201);
    });
    
    Route::get('/posts', function(Request $request) {
        $posts = App\Models\Post::with(['user:id,name', 'comments.user:id,name'])
            ->withCount('comments')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
            
        return response()->json($posts);
    });
    
    // Comments endpoints
    Route::post('/posts/{post}/comments', function(Request $request, $postId) {
        $user = $request->user();
        
        $data = $request->validate([
            'content' => 'required|string|max:1000'
        ]);
        
        $comment = App\Models\Comment::create([
            'post_id' => $postId,
            'user_id' => $user->id,
            'content' => $data['content']
        ]);
        
        $comment->load('user:id,name');
        return response()->json($comment, 201);
    });
    
    Route::get('/posts/{post}/comments', function($postId) {
        $comments = App\Models\Comment::where('post_id', $postId)
            ->with('user:id,name')
            ->orderBy('created_at')
            ->get();
            
        return response()->json($comments);
    });
});

// Public Products endpoints
Route::get('/products', function(){
    try {
        \Log::info('Products API called');
        $q = Product::query()
            ->select(['id','name','price','price_discount','image_path','description','created_at','category_id','subcategory_id']);
        if ($cat = request('category')) {
            $q->where('category_id', $cat);
        }
        if ($sub = request('subcategory')) {
            $q->where('subcategory_id', $sub);
        }
        if (!is_null(request('min'))) {
            $q->where('price', '>=', (float) request('min'));
        }
        if (!is_null(request('max'))) {
            $q->where('price', '<=', (float) request('max'));
        }
        if ($ex = request('exclude')) {
            $q->where('id', '!=', $ex);
        }
        if ($term = trim((string) request('q', ''))) {
            $q->where(function($w) use ($term){
                $like = '%'.$term.'%';
                $w->where('name','like',$like)
                  ->orWhere('description','like',$like)
                  ->orWhere('brand','like',$like)
                  ->orWhere('sku','like',$like)
                  ->orWhere('seo_title','like',$like)
                  ->orWhere('seo_description','like',$like)
                  ->orWhere('seo_keywords','like',$like);
            });
        }
        $limit = (int) request('limit', 20);
        $limit = $limit > 0 && $limit <= 100 ? $limit : 20;
        $page = max(1, (int) request('page', 1));
        $products = $q->orderByDesc('id')
            ->skip(($page - 1) * $limit)
            ->take($limit + 1) // fetch one extra to detect more
            ->get();
        
        \Log::info('Products found: ' . $products->count());
        
        // Map image_path to full URL
        $products->transform(function($p){
            if ($p->image_path) {
                $val = $p->image_path;
                // keep absolute urls as-is
                if (str_starts_with($val, 'http')) {
                    // do nothing
                } else {
                    // Fix URL to use correct domain for storage paths
                    $url = Storage::disk('public')->url($val);
                    $p->image_path = str_replace('http://localhost', request()->getSchemeAndHttpHost(), $url);
                }
            }
            return $p;
        });
        
        $hasMore = $products->count() > $limit;
        if ($hasMore) $products = $products->slice(0, $limit)->values();

        return response()->json([
            'data' => $products,
            'page' => $page,
            'limit' => $limit,
            'has_more' => $hasMore,
        ]);
    } catch (\Exception $e) {
        \Log::error('Products API error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

Route::get('/products/{id}', function($id){
    $product = Product::with(['user:id,name,store_name'])
        ->select(['id','user_id','name','price','price_discount','image_path','description','brand','sku','stock_qty','category_id','subcategory_id','created_at'])
        ->findOrFail($id);

    if ($product->image_path) {
        $url = Storage::disk('public')->url($product->image_path);
        $product->image_path = str_replace('http://localhost', request()->getSchemeAndHttpHost(), $url);
    }

    return $product;
});
// Public Posts endpoints
Route::get('/posts', function(){
    $paginator = Post::query()
        ->select(['id','content','image_path','created_at','user_id'])
        ->with('user:id,name,store_name')
        ->orderByDesc('id')
        ->paginate(20);
    $paginator->getCollection()->transform(function($p){
        if ($p->image_path) {
            $url = Storage::disk('public')->url($p->image_path);
            $p->image_path = str_replace('http://localhost', request()->getSchemeAndHttpHost(), $url);
        }
        return $p;
    });
    return $paginator;
});
