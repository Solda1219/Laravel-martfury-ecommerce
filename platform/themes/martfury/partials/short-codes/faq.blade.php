<div class="ps-faqs pt-40 pb-40">
    <div class="container">
        <div class="ps-section__header">
            <h1>{!! clean($title) !!}</h1>
        </div>
        <div class="ps-section__content">
            <div class="table-responsive">
                <table class="table ps-table--faqs">
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td class="heading" rowspan="{{ $category->faqs->count() }}">
                                    <h4>{{ $category->name }}</h4>
                                </td>
                                @foreach($category->faqs as $faq)
                                    @if ($loop->first)
                                        <td class="question">{{ $faq->question }}</td>
                                        <td>{!! clean($faq->answer) !!}</td>
                                    @endif
                                @endforeach
                            </tr>
                            @foreach($category->faqs as $faq)
                                @if (!$loop->first)
                                    <tr>
                                        <td class="question">{{ $faq->question }}</td>
                                        <td>{!! clean($faq->answer) !!}</td>
                                    </tr>
                                @endif
                            @endforeach

                            @if (!$loop->last)
                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
