@include('core/base::forms.fields.custom-select', compact('name', 'showLabel', 'showField', 'options'))

@push('footer')
    <script>
        $('#' + '{{ Arr::get($options['attr'], 'id') }}').select2({
            minimumInputLength: 2,
            ajax: {
                url: '{{ Arr::get($options['attr'], 'data-url') }}',
                quietMillis: 500,
                data: function (params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function (data) {
                    let results = data.data.map((item) => {
                        return {
                            id: item['id'],
                            text: item['name'],
                        };
                    });
                    return {
                        results: results
                    };
                }
            }
        });
    </script>
@endpush
