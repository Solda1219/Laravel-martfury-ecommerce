@php
    $originalProduct = $product;
    $selectedAttrs = [];
    $productImages = $product->images;
    if ($product->is_variation) {
        $product = get_parent_product($product->id);
        $selectedAttrs = app(\Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface::class)
            ->getAttributeIdsOfChildrenProduct($originalProduct->id);
        if (count($productImages) == 0) {
            $productImages = $product->images;
        }
    } else {
        $selectedAttrs = $product->defaultVariation->productAttributes->pluck('id')->all();
    }

    $countRating = 0;
    if (EcommerceHelper::isReviewEnabled()) {
        $reviews = $originalProduct->reviews->where('status', \Botble\Base\Enums\BaseStatusEnum::PUBLISHED);
        $countRating = $reviews->count();
    }

    Theme::set('stickyHeader', 'false');
    Theme::set('topHeader', Theme::partial('header-product-page', compact('product', 'countRating')));
    Theme::set('bottomFooter', Theme::partial('footer-product-page', compact('product')));
    Theme::set('pageId', 'product-page');
    Theme::set('headerMobile', Theme::partial('header-mobile-product'));
@endphp

<div class="ps-page--product">
    <div class="ps-container">
            <div class="ps-page__container">
                <div class="ps-page__left">
                    <div class="ps-product--detail ps-product--fullwidth">
                        <div class="ps-product__header">
                            <div class="ps-product__thumbnail" data-vertical="true">
                                <figure>
                                    <div class="ps-wrapper">
                                        <div class="ps-product__gallery" data-arrow="true">
                                            @foreach ($productImages as $img)
                                                <div class="item">
                                                    <a href="{{ RvMedia::getImageUrl($img) }}">
                                                        <img src="{{ RvMedia::getImageUrl($img) }}" alt="{{ $originalProduct->name }}"/>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </figure>
                                <div class="ps-product__variants" data-item="4" data-md="4" data-sm="4" data-arrow="false">
                                    @foreach ($productImages as $img)
                                        <div class="item">
                                            <img src="{{ RvMedia::getImageUrl($img, 'thumb') }}" alt="{{ $originalProduct->name }}"/>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="ps-product__info">
                                <h1>{{ $product->name }}</h1>
                                <div class="ps-product__meta">
                                    @if ($product->brand_id)
                                        <p>{{ __('Brand') }}: <a href="{{ $product->brand->url }}">{{ $product->brand->name }}</a></p>
                                    @endif
                                    @if (EcommerceHelper::isReviewEnabled())
                                        @if ($countRating > 0)
                                            <div class="rating_wrap">
                                                <a href="#tab-reviews">
                                                    <div class="rating">
                                                        <div class="product_rate" style="width: {{ get_average_star_of_product($originalProduct->id) * 20 }}%"></div>
                                                    </div>
                                                    <span class="rating_num">({{ $countRating }} {{ __('reviews') }})</span>
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <h4 class="ps-product__price @if ($product->front_sale_price !== $product->price) sale @endif"><span>{{ format_price($product->front_sale_price) }}</span> @if ($product->front_sale_price !== $product->price) <del>{{ format_price($product->price) }} </del> @endif</h4>
                                <div class="ps-product__desc">
                                    <div class="ps-list--dot">
                                        {!! clean($product->description) !!}
                                    </div>
                                </div>
                                @php $flashSale = $product->flashSales()->first(); @endphp

                                @if ($flashSale)
                                    <div class="ps-product__countdown">
                                        <figure>
                                            <figcaption> {{ __("Don't Miss Out! This promotion will expires in") }}</figcaption>
                                            <ul class="ps-countdown" data-time="{{ $flashSale->end_date }}">
                                                <li><span class="days"></span>
                                                    <p>{{ __('Days') }}</p>
                                                </li>
                                                <li><span class="hours"></span>
                                                    <p>{{ __('Hours') }}</p>
                                                </li>
                                                <li><span class="minutes"></span>
                                                    <p>{{ __('Minutes') }}</p>
                                                </li>
                                                <li><span class="seconds"></span>
                                                    <p>{{ __('Seconds') }}</p>
                                                </li>
                                            </ul>
                                        </figure>
                                        <figure>
                                            <figcaption>{{ __('Sold Items') }}</figcaption>
                                            <div class="ps-product__progress-bar ps-progress" data-value="{{ $flashSale->pivot->quantity > 0 ? ($flashSale->pivot->sold / $flashSale->pivot->quantity) * 100 : 0 }}">
                                                <div class="ps-progress__value"><span style="width: {{ $flashSale->pivot->quantity > 0 ? $flashSale->pivot->sold / $flashSale->pivot->quantity : 0 }}%;"></span></div>
                                                <p><b>{{ $flashSale->pivot->sold }}/{{ $flashSale->pivot->quantity }}</b> {{ __('Sold') }}</p>
                                            </div>
                                        </figure>
                                    </div>
                                @endif

                                @if ($product->variations()->count() > 0)
                                    <div class="pr_switch_wrap">
                                        {!! render_product_swatches($product, [
                                            'selected' => $selectedAttrs,
                                            'view'     => Theme::getThemeNamespace() . '::views.ecommerce.attributes.swatches-renderer'
                                        ]) !!}
                                    </div>
                                    <div class="number-items-available" style="display: none; margin-bottom: 10px;"></div>
                                @endif
                                <form class="add-to-cart-form" method="POST" action="{{ route('public.cart.add-to-cart') }}">
                                    @csrf
                                    {!! apply_filters(ECOMMERCE_PRODUCT_DETAIL_EXTRA_HTML, null) !!}
                                    <div class="ps-product__shopping">
                                        <figure>
                                            <figcaption>{{ __('Quantity') }}</figcaption>
                                            <div class="form-group--number product__qty">
                                                <button class="up" type="button"><i class="icon-plus"></i></button>
                                                <button class="down" type="button"><i class="icon-minus"></i></button>
                                                <input class="form-control qty-input" type="text" name="qty" value="1" placeholder="1" readonly>
                                            </div>
                                        </figure>
                                        <input type="hidden" name="id" class="hidden-product-id" value="{{ ($originalProduct->is_variation || !$originalProduct->defaultVariation->product_id) ? $originalProduct->id : $originalProduct->defaultVariation->product_id }}"/>

                                        @if (EcommerceHelper::isCartEnabled())
                                            <button class="ps-btn ps-btn--black" type="submit">{{ __('Add to cart') }}</button>
                                            @if (EcommerceHelper::isQuickBuyButtonEnabled())
                                                <button class="ps-btn" type="submit" name="checkout">{{ __('Buy Now') }}</button>
                                            @endif
                                        @endif
                                        <div class="ps-product__actions">
                                            <a class="js-add-to-wishlist-button" href="{{ route('public.wishlist.add', $product->id) }}"><i class="icon-heart"></i></a>
                                            <a class="js-add-to-compare-button" href="{{ route('public.compare.add', $product->id) }}" title="{{ __('Compare') }}"><i class="icon-chart-bars"></i></a>
                                        </div>
                                    </div>
                                </form>
                                <div class="ps-product__specification">
                                    @if ($product->sku)
                                        <p><strong>{{ __('SKU') }}:</strong> {{ $product->sku }}</p>
                                    @endif

                                    @if ($product->categories->count())
                                        <p class="categories"><strong> {{ __('Categories') }}:</strong>
                                            @foreach($product->categories as $category)
                                                <a href="{{ $category->url }}">{{ $category->name }}</a>@if (!$loop->last),@endif
                                            @endforeach
                                        </p>
                                    @endif

                                    @if ($product->tags->count())
                                        <p class="tags"><strong> {{ __('Tags') }}:</strong>
                                            @foreach($product->tags as $tag)
                                                <a href="{{ $tag->url }}">{{ $tag->name }}</a>@if (!$loop->last),@endif
                                            @endforeach
                                        </p>
                                    @endif
                                </div>
                                <div class="ps-product__sharing">
                                    <a class="facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($product->url) }}" target="_blank"><i class="fa fa-facebook"></i></a>
                                    <a class="linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode($product->url) }}&summary={{ rawurldecode(strip_tags($product->description)) }}" target="_blank"><i class="fa fa-twitter"></i></a>
                                    <a class="twitter" href="https://twitter.com/intent/tweet?url={{ urlencode($product->url) }}&text={{ strip_tags($product->description) }}" target="_blank"><i class="fa fa-linkedin"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="ps-product__content ps-tab-root">
                            <ul class="ps-tab-list">
                                <li class="active"><a href="#tab-description">{{ __('Description') }}</a></li>
                                @if (EcommerceHelper::isReviewEnabled())
                                    <li><a href="#tab-reviews">{{ __('Reviews') }} ({{ $countRating }})</a></li>
                                @endif
                            </ul>
                            <div class="ps-tabs">
                                <div class="ps-tab active" id="tab-description">
                                    <div class="ps-document">
                                        <div>
                                            {!! clean($product->content, 'youtube') !!}
                                        </div>
                                        @if (theme_option('facebook_comment_enabled_in_product', 'yes') == 'yes')
                                            <br />
                                            {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, Theme::partial('comments')) !!}
                                        @endif
                                    </div>
                                </div>
                                @if (EcommerceHelper::isReviewEnabled())
                                    <div class="ps-tab" id="tab-reviews">
                                    <div class="row">
                                        <div class="col-lg-5">
                                            <div class="ps-block--average-rating">
                                                <div class="ps-block__header">
                                                    <h3>{{ number_format($reviews->avg('star'), 2) }}</h3>
                                                    @if ($countRating > 0)
                                                        <div class="rating_wrap">
                                                            <div class="rating">
                                                                <div class="product_rate" style="width: {{ $reviews->avg('star') * 20 }}%"></div>
                                                            </div>
                                                            <span class="rating_num">({{ $countRating }} {{ __('reviews') }})</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ps-block__star"><span>{{ __('5 Star') }}</span>
                                                    @php
                                                        $stars = $reviews->where('star', 5)->count();
                                                        if ($stars > 0) {
                                                            $stars = $stars / $countRating * 100;
                                                        }
                                                    @endphp
                                                    <div class="ps-progress" data-value="{{ $stars }}"><span></span></div><span>{{ ((int) ($stars * 100)) / 100 }}%</span>
                                                </div>
                                                <div class="ps-block__star"><span>{{ __('4 Star') }}</span>
                                                    @php
                                                        $stars = $reviews->where('star', 4)->count();
                                                        if ($stars > 0) {
                                                            $stars = $stars / $countRating * 100;
                                                        }
                                                    @endphp
                                                    <div class="ps-progress" data-value="{{ $stars }}"><span></span></div><span>{{ ((int) ($stars * 100)) / 100 }}%</span>
                                                </div>
                                                <div class="ps-block__star"><span>{{ __('3 Star') }}</span>
                                                    @php
                                                        $stars = $reviews->where('star', 3)->count();
                                                        if ($stars > 0) {
                                                            $stars = $stars / $countRating * 100;
                                                        }
                                                    @endphp
                                                    <div class="ps-progress" data-value="{{ $stars }}"><span></span></div><span>{{ ((int) ($stars * 100)) / 100 }}%</span>
                                                </div>
                                                <div class="ps-block__star"><span>{{ __('2 Star') }}</span>
                                                    @php
                                                        $stars = $reviews->where('star', 2)->count();
                                                        if ($stars > 0) {
                                                            $stars = $stars / $countRating * 100;
                                                        }
                                                    @endphp
                                                    <div class="ps-progress" data-value="{{ $stars }}"><span></span></div><span>{{ ((int) ($stars * 100)) / 100 }}%</span>
                                                </div>
                                                <div class="ps-block__star"><span>{{ __('1 Star') }}</span>
                                                    @php
                                                        $stars = $reviews->where('star', 1)->count();
                                                        if ($stars > 0) {
                                                            $stars = $stars / $countRating * 100;
                                                        }
                                                    @endphp
                                                    <div class="ps-progress" data-value="{{ $stars }}"><span></span></div><span>{{ ((int) ($stars * 100)) / 100 }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            {!! Form::open(['route' => 'public.reviews.create', 'method' => 'post', 'class' => 'ps-form--review form-review-product']) !!}
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <h4>{{ __('Submit Your Review') }}</h4>
                                                @if (!auth('customer')->check())
                                                    <p class="text-danger">{{ __('Please') }} <a href="{{ route('customer.login') }}">{{ __('login') }}</a> {{ __('to write review!') }}</p>
                                                @endif
                                                <div class="form-group form-group__rating">
                                                    <label for="review-star">{{ __('Your rating of this product') }}</label>
                                                    <select name="star" class="ps-rating" data-read-only="false" id="review-star">
                                                        <option value="0">0</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <textarea class="form-control" name="comment" id="txt-comment" rows="6" placeholder="{{ __('Write your review') }}" @if (!auth('customer')->check()) disabled @endif></textarea>
                                                </div>

                                                <div class="success-message text-success" style="display: none;">
                                                    <span></span>
                                                </div>
                                                <div class="error-message text-danger" style="display: none;">
                                                    <span></span>
                                                </div>

                                                <div class="form-group submit">
                                                    <button class="ps-btn @if (!auth('customer')->check()) btn-disabled @endif" type="submit" @if (!auth('customer')->check()) disabled @endif>{{ __('Submit Review') }}</button>
                                                </div>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                    @if (EcommerceHelper::isReviewEnabled())
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="block--product-reviews">
                                                    <div class="block__header">
                                                        <h2>{{ $countRating }} {{ __('reviews for ":product"', ['product' => $product->name]) }}</h2>
                                                    </div>
                                                    <div class="block__content" id="app">
                                                        <product-reviews-component url="{{ route('public.ajax.product-reviews', $product->id) }}"></product-reviews-component>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ps-page__right">
                    <aside class="widget widget_product widget_features">
                        @for ($i = 1; $i <= 5; $i++)
                            @if (theme_option('product_feature_' . $i . '_title'))
                                <p><i class="{{ theme_option('product_feature_' . $i . '_icon') }}"></i> {{ theme_option('product_feature_' . $i . '_title') }}</p>
                            @endif
                        @endfor
                    </aside>
                    <aside class="widget">
                        {!! AdsManager::display('product-sidebar') !!}
                    </aside>
                </div>
            </div>

            @php
                $crossSellProducts = array_slice(get_cross_sale_products($originalProduct), 0, 7);
            @endphp
            @if (count($crossSellProducts) > 0)
                <div class="ps-section--default ps-customer-bought">
                    <div class="ps-section__header">
                        <h3>{{ __('Customers who bought this item also bought') }}</h3>
                    </div>
                    <div class="ps-section__content">
                        <div class="row">
                            @foreach($crossSellProducts as $crossId)
                                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6">
                                    <div class="ps-product">
                                        {!! Theme::partial('product-item', ['product' => get_product_by_id($crossId)]) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="ps-section--default" id="products">
                <div class="ps-section__header">
                    <h3>{{ __('Related products') }}</h3>
                </div>
                <related-products-component url="{{ route('public.ajax.related-products', $product->id) }}?limit=6"></related-products-component>
            </div>
    </div>
</div>
