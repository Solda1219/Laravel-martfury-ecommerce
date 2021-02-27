@if (count($ads))
    <div class="ps-home-ads mt-40 mb-40">
        <div class="ps-container">
            <div class="row">
                @for($i = 0; $i < count($ads); $i++)
                    <div class="col-lg-{{ 12 / count($ads) }}">
                        <div class="ps-collection">
                            {!! $ads[$i] !!}
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
@endif
