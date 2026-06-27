@if(count($combinations) > 0)
    <table class="table table-bordered">
        <thead>
        <tr>
            <td class="text-center">
                <label for="" class="control-label">Variant</label>
            </td>
            <td class="text-center">
                <label for="" class="control-label">Variant Price</label>
            </td>
            <td class="text-center">
                <label for="" class="control-label">Variant Stock</label>
            </td>
        </tr>
        </thead>
        <tbody>

        @foreach ($combinations as $key => $combination)
            @php
                $variantType = (string) ($combination['type'] ?? '');
                $variantFieldKey = str_replace('.', '_', $variantType);
            @endphp
            <tr>
                <td>
                    <label for="" class="control-label">{{ $variantType }}</label>
                </td>
                <td>
                    <input type="number" name="price_{{ $variantFieldKey }}"
                           value="{{$combination['price']}}" min="0"
                           step="0.01"
                           class="form-control" >
                    <span class="error-text" data-error="price_{{ $variantFieldKey }}"></span>
                </td>
                <td>
                    <input type="number" name="stock_{{ $variantFieldKey }}" value="{{ $combination['stock']??0 }}"
                           min="0" max="1000000"
                           class="form-control" onkeyup="update_qty()" >
                    <span class="error-text" data-error="stock_{{ $variantFieldKey }}"></span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
