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
    }
@endphp

<!-- Page Content Wrapper -->
<div class="page-content-wraper">
    <!-- Bread Crumb -->
    {!! Theme::breadcrumb()->render() !!}
    <!-- Bread Crumb -->

    <!-- Page Content -->
    <section id="product-detail-page" class="content-page single-product-content">
        <!-- Product -->
        <div id="product-detail" class="container">
            <div class="row">
                <!-- Product Image -->
                <div class="col-lg-6 col-md-6 col-sm-12 mb-30">
                    <div class="product-page-image">
                        <!-- Slick Image Slider -->
                        <div class="product-image-slider product-image-gallery" id="product-image-gallery"
                             data-pswp-uid="3">
                            @foreach ($productImages as $img)
                                <div class="item">
                                    <img src="{{ RvMedia::getImageUrl($img, 'product_detail') }}"
                                         data-zoom-image="{{ RvMedia::getImageUrl($img, 'product_detail') }}"
                                         alt="{{ $originalProduct->name }}"/>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Slick Thumb Slider -->
                    <div class="product-image-slider-thumbnails">
                        @foreach ($productImages as $thumb)
                            <div class="item">
                                <img src="{{ RvMedia::getImageUrl($thumb,'product') }}"
                                     alt="{{ $originalProduct->name }}"/>
                            </div>
                        @endforeach
                    </div>
                    <!-- End Slick Thumb Slider -->
                </div>
                <!-- End Product Image -->

                <!-- Product Content -->
                <div class="col-lg-6 col-md-6 col-sm-12 mb-30">
                    <div class="product-page-content">
                        <h2 class="product-title">{{ $originalProduct->name }}</h2>
                        @if (EcommerceHelper::isReviewEnabled())
                            <div class="product-rating">
                                <div class="star-rating" itemprop="reviewRating" itemscope=""
                                     itemtype="http://schema.org/Rating"
                                     title="Rated {{ get_average_star_of_product($product->id) }} out of 5">
                                    <span style="width: {{ get_average_star_of_product($product->id) * 20 }}%"></span>
                                </div>
                                    <div class="product-rating-count"><a href="#list-reviews">( <span
                                                    class="count">{{ get_count_reviewed_of_product($originalProduct->id) }}</span>
                                            {{ __('Reviews') }} )</a>
                                    </div>
                            </div>
                        @endif
                        <div class="product-price">
                            @if ($originalProduct->front_sale_price !== $originalProduct->price)
                                <del>{{ format_price($originalProduct->front_sale_price) }}</del>
                                <span>
                                    <span class="product-price-text">{{ format_price($originalProduct->front_sale_price) }}</span>
                                </span>
                            @else
                                <span>
                                    <span class="product-price-text">{{ format_price($originalProduct->price) }}</span>
                                </span>
                            @endif
                        </div>
                        <p class="product-description" id="detail-description">
                            {!! $product->description !!}
                        </p>

                        <div class="text-warning"></div>
                        <div class="row product-filters">
                            @if ($product->variations()->count() > 0)
                                {!! render_product_swatches($product, [
                                    'selected' => $selectedAttrs,
                                ]) !!}
                            @endif
                        </div>
                        <form class="single-variation-wrap">
                            @csrf
                            {!! apply_filters(ECOMMERCE_PRODUCT_DETAIL_EXTRA_HTML, null) !!}
                            <input type="hidden" name="product_is_out_of_stock"
                                   value="{{ $originalProduct->isOutOfStock() }}" id="hidden-product-is_out_of_stock"/>
                            <input type="hidden" name="id" id="hidden-product-id" value="{{ $originalProduct->id }}"/>
                            <div class="product-quantity">
                                <span data-value="+" class="quantity-btn quantityPlus"></span>
                                <input class="quantity input-lg" step="1" min="1" max="20" name="qty" value="1"
                                       title="Quantity" type="number"/>
                                <span data-value="-" class="quantity-btn quantityMinus"></span>
                            </div>
                            <button id="btn-add-cart" class="btn btn-lg btn-black"><i class="fa fa-shopping-bag"
                                                                                      aria-hidden="true"></i>{{ __('Add to cart') }}
                            </button>
                        </form>
                        <div class="product-meta">
                            @if ($originalProduct->sku)
                                <span>{{ __('SKU') }} : <span id="product-sku" class="sku"
                                                  itemprop="sku">{{ $originalProduct->sku }}</span></span>
                            @endif
                            <span><span
                                        id="is-out-of-stock">{{ ! $originalProduct->isOutOfStock() ? __('In stock') : __('Out of stock') }}</span></span>

                            @if (!$product->categories->isEmpty())
                                <span>{{ __('Categories') }} :
                                    @foreach ($product->categories as $category)
                                        <a href="{{ $category->url }}"> {{ $category->name }}
                                            @if (! $loop->last) , @endif
                                        </a>
                                    @endforeach
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div> <!-- /row -->
        </div> <!-- product-detail -->
        <!-- End Product -->

        <!-- Product Content Tab -->
        <div class="product-tabs-wrapper container">
            <ul class="product-content-tabs nav nav-tabs" role="tablist">

                <li class="nav-item">
                    <a class="active" href="#tab_description" role="tab" data-toggle="tab">{{ __('Description') }}</a>
                </li>
                <li class="nav-item">
                    <a class="" href="#tab_additional_information" role="tab"
                       data-toggle="tab">{{ theme_option('product-bonus-title')}}</a>
                </li>
                @if (EcommerceHelper::isReviewEnabled())
                    <li class="nav-item">
                        <a class="" href="#tab_reviews" role="tab" data-toggle="tab">{{ __('Reviews') }}
                            (<span> {{ get_count_reviewed_of_product($product->id) }}</span>)</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="" href="#tab_more_products" role="tab" data-toggle="tab">{{ __('Related products') }}</a>
                </li>

            </ul>
            <div class="product-content-Tabs_wraper tab-content container">
                <div id="tab_description" role="tabpanel" class="tab-pane fade in active">
                    <!-- Accordion Title -->
                    <h6 class="product-collapse-title" data-toggle="collapse" data-target="#tab_description-coll">
                        {{ __('Description') }}</h6>
                    <!-- End Accordion Title -->
                    <!-- Accordion Content -->
                    <div id="tab_description-coll" class="shop_description product-collapse collapse container">
                        <div class="row">
                            <div class=" col-md-12">
                                {!! clean($product->content) !!}
                            </div>
                        </div>
                    </div>
                    <!-- End Accordion Content -->
                </div>

                <div id="tab_additional_information" role="tabpanel" class="tab-pane fade">
                    <!-- Accordion Title -->
                    <h6 class="product-collapse-title" data-toggle="collapse"
                        data-target="#tab_additional_information-coll">{{ theme_option('product-bonus-title')}}</h6>
                    <!-- End Accordion Title -->
                    <!-- Accordion Content -->
                    <div id="tab_additional_information-coll" class="container product-collapse collapse">

                        {!! theme_option('product-bonus') !!}

                    </div>
                    <!-- End Accordion Content -->
                </div>

                @if (EcommerceHelper::isReviewEnabled())
                    <div id="tab_reviews" role="tabpanel" class="tab-pane fade">
                    <!-- Accordion Title -->
                    <h6 class="product-collapse-title" data-toggle="collapse" data-target="#tab_reviews-coll">{{ __('Reviews') }}
                        ({{ get_count_reviewed_of_product($product->id) }})</h6>
                    <!-- End Accordion Title -->
                    <!-- Accordion Content -->
                    <div id="tab_reviews-coll" class=" product-collapse collapse container">
                        <div class="row">
                            <div class="review-form-wrapper col-md-12">
                                {!! render_review_form($product->id) !!}
                            </div>
                        </div>
                    </div>
                    <!-- End Accordion Content -->
                </div>
                @endif

                <div id="tab_more_products" role="tabpanel" class="tab-pane fade">
                    <!-- Accordion Content -->

                    <div class="row">
                        <!-- Product Carousel -->
                        @php
                            $up_sale_products = get_up_sale_products($product);
                        @endphp

                        @if (! empty($up_sale_products))

                            <div class="container product-carousel">
                                <div id="new-tranding" class="product-item-4 owl-carousel owl-theme nf-carousel-themé">
                                    <!-- item.1 -->

                                    @foreach ($up_sale_products as $up_id)
                                        @php
                                            $up_sale = get_product_by_id($up_id);
                                        @endphp
                                        @include('plugins/ecommerce::themes.includes.default-product', ['product' => $up_sale])
                                    @endforeach
                                </div>
                            </div>

                    @endif
                    <!-- End Product Carousel -->
                    </div>

                    <!-- End Accordion Content -->
                </div>

            </div>
        </div>
        <!-- End Product Content Tab -->

        <!-- Product Carousel -->
        @php
            $relatedProducts = get_related_products($product);
        @endphp

        @if (!empty($relatedProducts))

            <div class="container product-carousel">
                <h2 class="page-title">{{ __('Related products') }}</h2>
                <div id="new-tranding" class="product-item-4 owl-carousel owl-theme nf-carousel-theme1">
                    @foreach ($relatedProducts as $related)
                        @include('plugins/ecommerce::themes.includes.default-product', ['product' => $related])
                    @endforeach

                </div>
            </div>

         @endif
    <!-- End Product Carousel -->
    </section>
</div>
<!-- End Page Content Wrapper -->
