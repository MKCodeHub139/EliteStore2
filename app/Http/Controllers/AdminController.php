<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    //
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'email|required|string',
            'password' => 'string|required'
        ]);
        if ($validated['email'] === env('ADMIN_EMAIL') && $validated['password'] === env('ADMIN_PASSWORD')) {
            // session me admin login ka flag set karo
            session(['is_admin_logged_in' => true]);
            return redirect()->route('admin.dashboard')->with('success', 'Admin login successful.');
        } else {
            return back()->with('error', 'Invalid admin credentials.');
        }
    }

    // dashboard
    public function dashboard()
    {
        $orders = Order::get();
        $categories = Category::get();
        $products = Product::get();
        $users = User::get();
        return view('admin.pages.dashboard', compact('orders', 'categories', 'products', 'users'));
    }
    // products data for datatable
    public function productsData(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::with('category')->select('products.*')->latest();
            return DataTables::of($data)
                ->addColumn('name', function (Product $product) {
                    return '<div class="flex items-center gap-3">
                    <img src="' . asset($product?->main_image) . '" alt="' . $product->name . '" class="w-16 h-16 object-cover rounded-full">
                    <span>' . $product->name . '</span>
                    </div>';
                    // <p>ID '.$product->id.'</p>
                })
                ->addColumn('category', function (Product $product) {
                    return $product->category ? $product->category->name : 'Uncategorized';
                })
                ->addColumn('actions', function (Product $product) {
                    // $editUrl = route('admin.products.edit', $product->id);
                    // $deleteUrl = route('admin.products.delete', $product->id);
                    return '<button class="btn btn-sm btn-primary edit-product-btn product-modal-btn" data-id="' . $product->id . '" onclick="my_modal_4.showModal()">Edit</button> 
                    <button class="btn btn-sm btn-danger delete-product-btn" data-id="' . $product->id . '">Delete</button>';
                })
                ->rawColumns(['actions', 'category', 'name'])
                ->make(true);
        }
    }
    // create product form
    public function createProduct()
    {
        $product = new Product();
        $categories = Category::get();
        $brands = Brand::get();
        return view('admin.partials.addProductForm', compact('categories', 'brands', 'product'));
    }
    // store product 
    public function storeProduct(Request $request)
    {

        $validated = $request->validate([
            'name' => 'string|required',
            'description' => 'string|nullable',
            'category_id' => 'exists:categories,id|required',
            'brand_id' => 'exists:brands,id|nullable',
            'key_features' => 'nullable',
            'short_description' => 'string|nullable',
            'old_price' => 'numeric|required',
            'discount_price' => 'numeric|nullable',
            'cost_price' => 'numeric|required',
            'stock_quantity' => 'integer|nullable',
            'main_image' => 'nullable|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'warenty' => 'nullable|String',
            'has_variants' => 'boolean',
            'variant_type' => 'string|nullable',
            'status' => 'string|in:active,inactive',
            'is_featured' => 'boolean|nullable',
            'is_trending' => 'boolean|nullable',
            'is_hot' => 'boolean|nullable',
            'is_new' => 'boolean|nullable',
            // add more validation rules as needed
        ]);
        // set price
        $price = $validated['old_price'];
        if ($validated['discount_price']) {
            $price = $validated['old_price'] - $validated['discount_price'];
        }
        // slug logic
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        // sku logic
        $sku = 'PROD' . '-' . strtoupper(Str::random(6));

        $keyFeatures = explode(",", $request->key_features);
        if ($request->hasFile('main_image')) {
            $mainImage = $request->file('main_image');
            $mainImageName = time() . '_' . uniqid() . '.' . $mainImage->getClientOriginalExtension();
            $mainImage->move(public_path('uploads/products'), $mainImageName);
            $validated['main_image'] = 'uploads/products/' . $mainImageName;
        }
        if ($request->hasFile('gallery_images')) {
            $galleryImageNames = [];
            foreach ($request->file('gallery_images') as $galleryImage) {
                $name = time() . '_' . uniqid() . '.' . $galleryImage->getClientOriginalExtension();
                $galleryImage->move(public_path('uploads/products'), $name);
                $galleryImageNames[] = 'uploads/products/' . $name;
            }
        }
        $product = Product::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? '',
            'price' => $price,
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'] ?? null,
            'key_features' => $keyFeatures,
            'short_description' => $validated['short_description'] ?? '',
            'old_price' => $validated['old_price'] ?? null,
            'discount_price' => $validated['discount_price'] ?? null,
            'cost_price' => $validated['cost_price'] ?? null,
            'stock_quantity' => $validated['stock_quantity'] ?? 0,
            'sku' => $sku,
            'main_image' => $validated['main_image'] ?? null,
            'gallery_images' => $galleryImageNames ?? null,
            'warenty' => $validated['warenty'] ?? 0,
            'has_variants' => $validated['has_variants'] ?? false,
            'variant_type' => $validated['variant_type'] ?? null,
            'status' => $validated['status'] ?? 'inactive',
            'is_featured' => $validated['is_featured'] ?? false,
            'is_trending' => $validated['is_trending'] ?? false,
            'is_hot' => $validated['is_hot'] ?? false,
            'is_new' => $validated['is_new'] ?? false,
        ]);

        if ($product) {
            return response()->json(['success' => true, 'message' => 'Product created successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to create product.']);
        }
    }
    // edit product form
    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::get();
        $brands = Brand::get();
        return view('admin.partials.addProductForm', compact('product', 'categories', 'brands'));
    }
    public function updateProduct(Request $request, $id)
    {
        if (!is_writable(public_path('uploads/products'))) {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'name' => 'string|required',
            'description' => 'string|nullable',
            'category_id' => 'exists:categories,id|required',
            'brand_id' => 'exists:brands,id|nullable',
            'key_features' => 'nullable',
            'short_description' => 'string|nullable',
            'old_price' => 'numeric|required',
            'discount_price' => 'numeric|nullable',
            'cost_price' => 'numeric|nullable',
            'stock_quantity' => 'integer|nullable',
            'main_image' => 'nullable|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'warenty' => 'nullable|String',
            'has_variants' => 'boolean',
            'variant_type' => 'string|nullable',
            'status' => 'string|in:Active,Inactive',
            'is_featured' => 'boolean|nullable',
            'is_trending' => 'boolean|nullable',
            'is_hot' => 'boolean|nullable',
            'is_new' => 'boolean|nullable',
        ]);
        // set price
        $price = $validated['old_price'];
        if ($validated['discount_price']) {
            $price = $validated['old_price'] - $validated['discount_price'];
        }
        // slug logic
        $slug = $product->slug;
        if ($validated['name'] != $product->name) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $count = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
        }
        $keyFeatures = explode(",", $request->key_features);
        if ($request->hasFile('main_image')) {
            if ($product->main_image) {
                $oldImagePath = public_path($product->main_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $mainImage = $request->file('main_image');
            $mainImageName = time() . '_' . uniqid() . '.' . $mainImage->getClientOriginalExtension();
            $mainImage->move(public_path('uploads/products'), $mainImageName);
            $validated['main_image'] = 'uploads/products/' . $mainImageName;
        }
        $galleryImageNames = [];
        if ($request->hasFile('gallery_images')) {
            if ($product->gallery_images) {
                foreach ($product->gallery_images as $oldGalleryImage) {
                    $oldImagePath = public_path($oldGalleryImage);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            }
            foreach ($request->file('gallery_images') as $galleryImage) {
                $name = time() . '_' . uniqid() . '.' . $galleryImage->getClientOriginalExtension();
                $galleryImage->move(public_path('uploads/products'), $name);
                $galleryImageNames[] = 'uploads/products/' . $name;
            }
            $validated['gallery_images'] = $galleryImageNames;
        }
        $validated['key_features'] = $keyFeatures;
        // save price
        $product->price = $price;
        $product->slug = $slug;
        if ($product->update($validated)) {
            return response()->json(['success' => true, 'message' => 'Product updated successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to updated product.']);
        }
    }
    }
    // categories data for datatable
    public function categoriesData(Request $request)
    {
        $categories = Category::get();
        return view('admin.partials.categoryCard', compact('categories'));
    }
    // create category form
    public function createCategory()
    {
        return view('admin.partials.categoryForm');
    }
    // store category
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'string|required',
            'slug' => 'string|required|unique:categories,slug',
            'description' => 'string|nullable',
            'image_background_color' => 'string|nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/categories'), $imageName);
            $validated['image'] = 'uploads/categories/' . $imageName;
        }
        $category = Category::create($validated);
        if ($category) {
            return response()->json(['success' => true, 'message' => 'Category created successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to create category.']);
        }
    }


    // users data for datatable

    public function usersData(Request $request)
    {
        $users = User::select('id', 'name', 'phone', 'email', 'total_orders', 'created_at')->get();
        return DataTables::of($users)
            ->addColumn('actions', function (User $user) {
                return '<button class="btn btn-sm btn-primary edit-user-btn" data-id="' . $user->id . '">Edit</button> 
                    <button class="btn btn-sm btn-danger delete-user-btn" data-id="' . $user->id . '">Delete</button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    // orders data
    public function getOrders(Request $request)
    {
        if (request()->ajax()) {

            $orders = Order::select('orders.*');
            if (!empty($request->status) && $request->status != 'all') {
                $orders->where('status', $request->status);
            }
            return DataTables::of($orders)
                ->addColumn('name', function ($row) {
                    return $row->user->name;
                })
                ->addColumn('status', function ($row) {
                    $statusClass = '';
                    switch ($row->status) {
                        case 'Pending':
                            $statusClass = 'bg-red-600 text-white';
                            break;
                        case 'Processing':
                            $statusClass = 'bg-yellow-500 text-white';
                            break;
                        case 'Shipped':
                            $statusClass = 'bg-blue-500 text-white';
                            break;
                        case 'Delivered':
                            $statusClass = 'bg-green-500 text-white';
                            break;
                        default:
                            $statusClass = 'bg-gray-500 text-white';
                    }
                    return '<span class="px-2 py-1 rounded-full ' . $statusClass . ' text-sm">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('Actions', function ($row) {
                    return '<div>
                    <button class="btn btn-sm btn-primary edit-order-btn" data-id="' . $row->id . '"  onclick="my_modal_4.showModal()">Edit</button>
                    <button class="btn btn-sm btn-danger delete-order-btn" data-id="' . $row->id . '">Delete</button>
                </div>';
                })
                ->rawColumns(['name', 'Actions', 'status'])
                ->make(true);
        }
    }
    public function editOrder($id)
    {
        $order = Order::findOrFail($id);
        $orderItems = $order->orderItems()->with('products')->get();
        return view('admin.partials.editOrderForm', compact('order', 'orderItems'));
    }
    // update order quantity
    public function updateOrderQuantity(Request $request, $id)
    {
        $orderItem = OrderItem::findOrFail($id);
        $validated = $request->validate([
            'quantity' => 'integer|required|min:1',
        ]);
        $orderItem->quantity = $validated['quantity'];
        $orderItem->price = $orderItem->products->price * $validated['quantity'];
        if ($orderItem->save()) {
            // recalculate order total
            $order = $orderItem->order;
            $subtotal = $order->orderItems()->sum('price');
            $tax = $subtotal * 0.1; // assuming 10% tax
            $shipping = 50; // flat shipping rate
            $total = $subtotal + $tax + $shipping;
            $order->subtotal = $subtotal;
            $order->tax = $tax;
            $order->shipping = $shipping;
            $order->total = $total;
            $order->save();
            return response()->json(['success' => true, 'message' => 'Order quantity updated successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to update order quantity.']);
        }
    }
    // update order details
    public function updateOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate([
            'status' => 'string|in:pending,processing,shipped,delivered|required',
            'shipping_adderess' => 'string|required',
            'tax' => 'numeric|required',
            'shipping' => 'numeric|required',
            'total' => 'numeric|required',
            'payment_method' => 'string|required',
            'payment_status' => 'string|required',
            'quantity' => 'array|required|min:1',
            // add more validation rules as needed
        ]);
        $order->status = $validated['status'];
        $order->shipping_adderess = $validated['shipping_adderess'];
        $order->payment_method = $validated['payment_method'];
        $order->payment_status = $validated['payment_status'];
        foreach ($order->orderItems as $index => $orderItem) {
            $orderItem->quantity = (int) $validated['quantity'][$index];
            $orderItem->price = $orderItem->products->price * $validated['quantity'][$index];
            $orderItem->save();
        }
        $order->subtotal = $order->orderItems->sum(function ($orderItem) {
            $product = $orderItem->products->first();
            return $product->price * $orderItem->quantity;
        });

        $order->tax = $validated['tax'];
        $order->shipping = $validated['shipping'];
        $order->total = $order->subtotal + $order->tax + $order->shipping;

        if ($order->save()) {
            return response()->json(['success' => true, 'message' => 'Order updated successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to update order.']);
        }
    }
}
