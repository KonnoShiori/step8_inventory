@extends('layouts.app')

@section('title', '商品一覧')

@section('content')
    <div class="contents">
        <h2 class="contents__title">商品一覧</h2>

        <form class="search-form">
            @csrf
            <input id="txtSearch" class="default__search" type="search" name="search" placeholder="検索キーワード">

            <select id="drpCompany" class="default__drp" name="company_filter" id="">
                <option value="">メーカー名</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}">
                        {{ $company->company_name }}</option>
                @endforeach

                <input id="btnSearchIndex" class="default__btn" type="button" value="検索">

                <button class="default__btn" type="button"
                    onclick=location.href="{{ route('productsIndex') }}">クリア</button>

                <div class="search-form__input--number">
                    <p>価格：
                        <input id="numLowerPrice" type="number" name="lower_price" placeholder="下限">
                        <input id="numHighPrice" type="number" name="high_price" placeholder="上限">
                    </p>
                    <p>在庫数：
                        <input id="numLowerStock" type="number" name="lower_stock" placeholder="下限">
                        <input id="numHighStock" type="number" name="high_stock" placeholder="上限">
                    </p>
                </div>
        </form>

        <div class="alert-success">
        </div>

        <div>
            <table id="indexTable" class="index-table">
                <thead class="index-table__litle">
                    <tr>
                        <th class="index-table__title--id">ID</th>
                        <th class="index-table__title--img">商品画像</th>
                        <th class="index-table__title--product-name">商品名</th>
                        <th class="index-table__title--price">価格</th>
                        <th class="index-table__title--stock">在庫数</th>
                        <th class="index-table__title--company-name">メーカー名</th>
                        <th class="index-table__title--btn">
                            <button class="default__btn nav-create__btn" type="button"
                                onclick=location.href="{{ route('productCreate') }}">新規登録</button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td class="index-table__contents--right">{{ $product->id }}</td>
                            <td class="index-table__contents--img">
                                <img class="index-table__img"
                                    src="{{ asset('storage/product_images/' . $product->img_path) }}" alt="商品画像">
                            </td>
                            <td class="index-table__contents--left">{{ $product->product_name }}</td>
                            <td class="index-table__contents--right">&yen;{{ $product->price }}</td>
                            <td class="index-table__contents--right">{{ $product->stock }}</td>
                            <td class="index-table__contents--left">{{ $product->company->company_name }}</td>
                            <td class="index-table__contents--btn">
                                <button class="default__btn" type="button"
                                    onclick=location.href="{{ route('productShow', $product->id) }}">詳細</button>

                                <button class="default__btn delete__btn-ajax index-table__delete-btn"
                                    type="button">削除</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
