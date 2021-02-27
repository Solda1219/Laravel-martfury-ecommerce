{!! Theme::partial('header') !!}

<div class="ps-page--blog">
    <div class="container">
        <div class="ps-page__header">
            <h1>{{ SeoHelper::getTitle() }}</h1>
            <div class="ps-breadcrumb--2">
                {!! Theme::partial('breadcrumbs') !!}
            </div>
        </div>
        <div class="ps-blog--sidebar">
            <div class="ps-blog__left">
                {!! Theme::content() !!}
            </div>
            <div class="ps-blog__right">
                {!! dynamic_sidebar('primary_sidebar') !!}
            </div>
        </div>
    </div>
</div>

{!! Theme::partial('footer') !!}
