<div class="form-group">
    <label for="widget-name">{{ trans('core/base::forms.name') }}</label>
    <input type="text" class="form-control" name="name" value="{{ $config['name'] }}">
</div>

@php
    $categories = get_product_categories(['status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED], ['children'], [], true);
@endphp

<div class="form-group product-categories-select">
    <div class="multi-choices-widget list-item-checkbox">
        <ul>
            @foreach ($categories as $category)
                <li>
                    <label>
                        <input type="checkbox"
                               name="categories[]"
                               value="{{ $category->id }}"
                               @if (in_array($category->id, $config['categories'])) checked="checked" @endif>
                        {{ $category->name }}
                    </label>
                    @if ($category->children->count())
                        <ul style="padding-left: 20px">
                            @foreach ($category->children as $child)
                                <li>
                                    <label>
                                        <input type="checkbox"
                                               name="categories[]"
                                               value="{{ $child->id }}"
                                               @if (in_array($child->id, $config['categories'])) checked="checked" @endif>
                                        {{ $child->name }}
                                    </label>
                                    @if ($child->children->count())
                                        <ul style="padding-left: 20px">
                                            @foreach ($child->children as $item)
                                                <li>
                                                    <label>
                                                        <input type="checkbox"
                                                               name="categories[]"
                                                               value="{{ $item->id }}"
                                                               @if (in_array($item->id, $config['categories'])) checked="checked" @endif>
                                                        {{ $item->name }}
                                                    </label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>

<style>
    .product-categories-select .list-item-checkbox {
        background: #f1f1f1; margin-bottom: 20px; padding-left: 15px !important;
    }

    .product-categories-select .list-item-checkbox .hrv-checkbox, input[type=checkbox] {
        margin-left : 2px;
    }
</style>
