@extends('index')
@section('main')
    <div class="main-content md:px-15 px-2 py-5">
        <div class="back-btn">
            <a href="{{ url()->previous() }}" class="text-[13px] text-blue-500 font-[500]"><- Back to Products</a>
        </div>
        <div class="product-img-and-details grid md:grid-cols-2  grid-cols-1 gap-10 py-5">
            <div class="product-images  min-h-[400px]  overflow-hidden">
                {{-- main image --}}
                <div class="main-img overflow-hidden h-[500px] w-[100%] border rounded-2xl relative">
                    <img src="{{ asset($product->main_image) }}" alt="" class="h-[100%] object-cover w-[100%]">
                    <div class="discount absolute top-0 right-0 p-2 ">
                        <p class="px-2 py-1 font-[500] text-white bg-red-500 rounded-full text-[13px] ">
                            -{{ $product->old_price && $product->discount_price ? round((($product->old_price - $product->price) / $product->old_price) * 100) : '' }}%
                            OFF</p>
                    </div>
                </div>
                {{-- gallery images --}}
                <div class="gallery-images w-full h-[100%] flex gap-2 mt-5">
                    @if ($product->gallery_images)
                        @foreach ($product->gallery_images as $image)
                            <div class="img h-[150px] w-[150px] overflow-hidden rounded-2xl border border-gray-300 transition-transform duration-200 cursor-pointer">
                                <img src="{{ asset($image) }}" alt="" class="h-[100%] w-[100%] object-cover ">
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-400">No gallery images available.</p>
                    @endif
                </div>
            </div>
            <div class="product-details border min-h-[400px] rounded-2xl p-5">
                {{-- category-stock --}}
                <div class="category-stock flex items-center gap-3 text-[11px] font-[500]">
                    <div class="category">
                        <p class="px-4 py-1 bg-gradient-to-r  from-blue-500  to-purple-500 text-white rounded-full">
                            {{ $product->category->name }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="px-4 py-1 {{ $product->stock_quantity > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}   rounded-full">
                            {{ $product->stock_quantity > 0 ? 'In Stock' : 'Out Of Stock' }}</p>
                    </div>
                </div>
                {{-- name --}}
                <div class="product-name my-2">
                    <h2 class="text-[24px] font-[700]">Wireless Bluetooth Headphones</h2>
                </div>
                {{-- ratings and reviews --}}
                <div class="ratings-reviews mb-3">
                    <div class="ratings flex items-center gap-1">
                        <div class="stars flex gap-1 items-center">

                            @for ($i = 1; $i <= 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    fill="{{ $product->average_rating >= $i ? 'currentColor' : 'none' }}"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="w-5 h-5 text-yellow-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.218 3.602a.563.563 0 00-.162.556l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.523 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.385a.562.562 0 00-.162-.556L3.25 9.988a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                </svg>
                            @endfor
                            <p class=" font-bold mx-2 text-[13px]">{{ $product->average_rating }}</p> | <p
                                class="text-[13px] text-blue-500">
                                {{ $product->total_reviews }} reviews</p>
                        </div>
                    </div>

                </div>
                <hr>
                {{-- description --}}
                <div class="description my-3">
                    <p class="text-[13px]">{{ $product->description }}</p>
                </div>
                {{-- price --}}
                <div class="price">
                    <div class="current-price flex items-center gap-2">
                        <h2 class="text-4xl font-bold">${{ round($product->price) }}</h2>
                        <del
                            class="text-[14px] text-gray-400 mt-2">{{ $product->old_price ? '$' . $product->old_price : '' }}</del>
                        <p
                            class="text-[11px] font-[700] text-green-500 font-[500] mt-2 bg-red-100 text-red-700 px-4 py-[1px] rounded-full">
                            {{ $product->discount_price && $product->old_price ? 'Save $' . ($product->old_price - $product->price) : '' }}
                        </p>
                    </div>
                </div>
                {{-- offer tagline --}}
                <div class="offer-tagline my-2 pb-3">
                    <p class="text-[11px] text-green-500 font-[600]">Limited time offer!
                        {{ $product->old_price && $product->discount_price ? round((($product->old_price - $product->price) / $product->old_price) * 100) : '' }}%
                        off.</p>
                </div>
                <hr>
                {{-- key features --}}
                <div class="key-features my-5">
                    <h5 class="text-[15px] font-bold">Key Features:</h5>
                    <ul class="my-2 text-[12px] flex flex-col gap-1 text-gray-600">
                        @foreach ($product->key_features as $feature)
                            <li class="flex items-center gap-2">
                                <div class="svg bg-green-100 rounded-full p-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-check w-3 h-3 text-green-600">
                                        <path d="M20 6 9 17l-5-5"></path>
                                    </svg></div>{{ $feature }}
                            </li>
                        @endforeach

                    </ul>
                </div>
                {{-- quantity --}}
                <div class="quantity">
                    <h5 class="text-[13px] font-bold mb-2">Quantity:</h5>
                    <button class="btn text-2xl font-[500] decreaseQty"
                        data-id="{{ auth()?->guard('web')?->user()?->carts->where('product_id', $product->id)->first()->id }}">-</button>
                    <input type="text" name="" id=""
                        value="{{ auth()->guard('web')?->user()?->carts->where('product_id', $product->id)->first()->quantity ?? 1 }}"
                        class="w-[50px] text-center font-bold text-xl cartQty" disabled>
                    <button class="btn text-2xl increaseQty"
                        data-id="{{ auth()?->guard('web')?->user()?->carts->where('product_id', $product->id)->first()->id }}">+</button>
                </div>
                {{-- cart btn  and whishlist and share btn --}}
                <div class="cart-wishlist-store my-6 grid grid-cols-8 gap-2">
                    {{-- cart btn --}}
                    <div class="cart-btn col-span-6 ">
                        <button
                            class="flex items-center justify-center gap-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white px-4 py-4 rounded-xl font-[500] w-full text-[13px] hover:shadow-2xl hover:w-[101%] hover:h-[101%] transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-shopping-cart w-4 h-4"
                                data-fg-b3jl109="1.25:1.22454:/src/app/pages/ProductDetail.tsx:252:17:9825:36:e:ShoppingCart::::::BPYc"
                                data-fgid-b3jl109=":rua:">
                                <circle cx="8" cy="21" r="1"></circle>
                                <circle cx="19" cy="21" r="1"></circle>
                                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12">
                                </path>
                            </svg>
                            Add to Cart</button>
                    </div>
                    {{-- wishlist btn --}}
                    <div
                        class="wishlist flex items-center gap-1 border border-gray-300 rounded-xl justify-center p-2 hover:text-red-600 hover:bg-red-100 hover:border-red-600 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-heart w-4 h-4 group-hover:text-red-500 transition-colors"
                            data-fg-b3jl112="1.25:1.22454:/src/app/pages/ProductDetail.tsx:256:17:10103:72:e:Heart::::::Bi8G"
                            data-fgid-b3jl112=":ruc:">
                            <path
                                d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z">
                            </path>
                        </svg>
                    </div>
                    {{-- share btn --}}
                    <div
                        class="share-btn flex items-center gap-1 border border-gray-300 rounded-xl justify-center p-2 hover:text-blue-600 hover:bg-blue-100 hover:border-blue-600 transition-colors duration-20">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-share2 lucide-share-2 w-4 h-4 group-hover:text-blue-500 transition-colors"
                            data-fg-b3jl114="1.25:1.22454:/src/app/pages/ProductDetail.tsx:259:17:10391:74:e:Share2::::::BCAr"
                            data-fgid-b3jl114=":rue:">
                            <circle cx="18" cy="5" r="3"></circle>
                            <circle cx="6" cy="12" r="3"></circle>
                            <circle cx="18" cy="19" r="3"></circle>
                            <line x1="8.59" x2="15.42" y1="13.51" y2="17.49"></line>
                            <line x1="15.41" x2="8.59" y1="6.51" y2="10.49"></line>
                        </svg>
                    </div>
                </div>
                {{-- Shipping charge and warenty --}}
                <div class="shipping-and-warenty grid grid-cols-2 gap-5">
                    <div
                        class="shipping-charge border border-gray-300 rounded-xl p-3 hover:border-blue-500 hover:bg-blue-100 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-truck w-6 h-6 text-blue-600 mb-2"
                            data-fg-b3jl118="1.25:1.22454:/src/app/pages/ProductDetail.tsx:266:17:10737:48:e:Truck::::::Dl9l"
                            data-fgid-b3jl118=":ruh:">
                            <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path>
                            <path d="M15 18H9"></path>
                            <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14">
                            </path>
                            <circle cx="17" cy="18" r="2"></circle>
                            <circle cx="7" cy="18" r="2"></circle>
                        </svg>
                        <h2 class="text-[12px] font-bold">Free Shipping</h2>
                        <p class="text-[10px] text-gray-600 mt-1">On Orders over $50</p>
                    </div>
                    <div
                        class="warrenty border border-gray-300 rounded-xl p-3 hover:border-green-500 hover:bg-green-100 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-shield w-6 h-6 text-green-600 mb-2"
                            data-fg-b3jl124="1.25:1.22454:/src/app/pages/ProductDetail.tsx:271:17:11094:50:e:Shield::::::B8zf"
                            data-fgid-b3jl124=":rul:">
                            <path
                                d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                            </path>
                        </svg>
                        <h2 class="text-[12px] font-bold">{{ $product->warenty }} year Warranty</h2>
                        <p class="text-[10px] text-gray-600 mt-1">Full Coverage</p>
                    </div>
                </div>
            </div>
        </div>
        {{-- about product --}}
        <div class="about-product min-h-[10rem] w-full border rounded-2xl my-10">
            <div class="header min-h-[4rem] border-b flex flex-wrap items-center md:px-5 px-2 md:gap-5 gap-2 text-[13px] font-bold text-gray-500">
                <button class="border-b-2 border-blue-600 text-blue-600 py-4 description">Description</button>
                <button class="py-4 border-blue-600 hover:text-black specification">Specification</button>
                <button class="py-4 border-blue-600 hover:text-black reviews">Reviews(123)</button>
            </div>
            <div class="about-product-body p-5 ">
             
                @include('partials.productDetails.description')
            </div>
        </div>
        {{-- related products --}}
        <div class="related-products my-10">
            <h2 class="text-[18px] font-bold mb-5">Related Products</h2>
            <div class="related-product-cards md:grid grid-cols-5 flex flex-wrap gap-5">
             
            </div>
        </div>
    </div>
        @endsection
        @section('scripts')
        <script>
            $(document).ready(function(){
                $.ajax({
                    url:'/products/related/{{$product->slug}}',
                    method:'GET',
                    success:function(res){
                        $('.related-product-cards').html(res);
                    }
                })
            })
            $(document).on('click','.about-product .header' ,function(e){
                e.preventDefault();
                let data;
                if(e.target.classList.contains('description')){
                    data = 'description';
                }else if(e.target.classList.contains('specification')){
                    data = 'specification';
                }else if(e.target.classList.contains('reviews')){
                    data = 'reviews';
                }
              $('.about-product .header button').removeClass('border-b-2 text-blue-600 ').addClass('hover:text-black');
              $(e.target).addClass('border-b-2 text-blue-600').removeClass('hover:text-black');
              $.ajax({
                url:"/products/{{$product->slug}}/" + data,
                method:"GET",
                success:function(res){
                    $('.about-product-body').html(res);
                }
            })
        })

        // open review modal
function openReviewModal(){
    const isLoggedIn = @json(auth()->check());

    if(isLoggedIn){
        my_modal_3.showModal();
    } else {
        alert('Please log in to submit a review');
    }
}
        // review form submit
        $(document).on('submit','.reviewForm',function(e){
            e.preventDefault();
            let formData = {
                rating:$('input[name="rating"]:checked').val(),
                title:$('input[name="title"]').val(),
                comment:$('textarea[name="comment"]').val(),
            };
            $.ajax({
                url:'{{route('products.submitReview', $product->slug)}}',
                method:'POST',
                data:formData,
                success:function(res){
                    if(res.success){
                        alert(res.message);
                        $('.reviewForm')[0].reset();
                        my_modal_3.close();
                    }
                }
            })
        })


        // change main image on click of gallery image
        $(document).on('click','.gallery-images .img',function(e){
            console.log($(this).find('img').attr('src'));
            let imgSrc = $(this).find('img').attr('src');
            $('.main-img img').attr('src',imgSrc);
            $(this).addClass('border-blue-500 scale-105').removeClass('border-gray-300').siblings().removeClass('border-blue-500 scale-105').addClass('border-gray-300');

        })
        </script>
        @endsection
