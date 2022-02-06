@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-2"> <!-- 13_コンポーネントでサイドバーを実装 -->
            @component('components.sidebar', ['categories' => $categories, 'major_category_names' => $major_category_names]) <!-- 13_コンポーネントにデータを送る記述 -->
            @endcomponent
        </div>
        <div class="col-9">
            <div class="container">
                @if ($category !== null) <!-- 13_カテゴリーがnullで無ければ、すなわちカテゴリーが選択されていたら -->
                    <a href="/">トップ</a> > <a href="#">{{ $category->major_category_name }}</a> > {{ $category->name }}
                    <h1>{{ $category->name }}の商品一覧{{$total_count}}件</h1>

                    <!-- todo: 14_ソートの選択肢の記述 -->
                    <form method="GET" action="{{ route('products.index')}}" class="form-inline">
                        <input type="hidden" name="category" value="{{ $category->id }}">
                        並び替え
                        <select name="sort" onChange="this.form.submit();" class="form-inline ml-2"> <!-- todo: 14_セレクトボックス name='sort'というキーでvalueが送られる -->
                            @foreach ($sort as $key => $value) <!-- 14_ $sort はコントローラーから来たソートに使用する選択肢の配列 -->
                                @if ($sorted == $value)  <!-- 14_ $sorted は選択されているソートの種類 -->
                                    <option value=" {{ $value }}" selected>{{ $key }}</option>
                                @else
                                    <option value=" {{ $value }}">{{ $key }}</option>
                                @endif
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>
            <div class="container mt-4">
                <div class="row w-100">
                    @foreach($products as $product)
                        <div class="col-3">
                            <a href="{{route('products.show', $product)}}">
                                @if ($product->image !== "")
                                    <img src="{{ asset('storage/products/'.$product->image) }}" class="img-thumbnail">
                                @else
                                    <img src="{{ asset('img/dummy.png')}}" class="img-thumbnail">
                                @endif
                            </a>
                            <div class="row">
                                <div class="col-12">
                                    <p class="samazon-product-label mt-2">
                                        {{$product->name}}<br>
                                        <label>￥{{$product->price}}</label>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!--todo: 13_カテゴリで絞り込んだ条件を保持してページングする-->
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
