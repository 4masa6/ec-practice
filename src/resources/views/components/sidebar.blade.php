<div class="container">
    @foreach ($major_category_names as $major_category_name) <!--todo: 13_メインカテゴリーをループする-->
        <h2>{{ $major_category_name }}</h2>
        @foreach ($categories as $category) <!--todo: 13_カテゴリーをループする-->
            @if ($category->major_category_name === $major_category_name) <!--todo: 13_カテゴリーデータの中から取得したメインカテゴリーが、メインカテゴリーと一致したら表示する-->
                <!--todo: 13_クリックするとカテゴリーidがindexアクションに渡されるようにする-->
                <label class="samazon-sidebar-category-label"><a href="{{ route('products.index', ['category' => $category->id]) }}">{{ $category->name }}</a></label>
            @endif
        @endforeach
    @endforeach
</div>
