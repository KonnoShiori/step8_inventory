$(function () {
    /**
     * dataTablesのオプション設定
     */
    const TABLE_OPTIONS = {
        language: {
            "decimal": ".",
            "thousands": ",",
            "sProcessing": "処理中...",
            "sLengthMenu": "_MENU_ 件表示",
            "sZeroRecords": "データはありません。",
            "sInfo": " _TOTAL_ 件中 _START_ から _END_ まで表示",
            "sInfoEmpty": " 0 件中 0 から 0 まで表示",
            "sInfoFiltered": "（全 _MAX_ 件より抽出）",
            "sInfoPostFix": "",
            "sSearch": "検索:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "先頭",
                "sPrevious": "前",
                "sNext": "次",
                "sLast": "最終"
            }
        },
        paging: true,
        displayLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        lengthChange: true,
        searching: false,
        ordering: true,
        info: true,
        order: [[0, 'asc']],
        columnDefs: [{ targets: [1, 6], sortable: false }]
    }

    /**
     * dataTablesの適用
     */
    let table = $('#indexTable').DataTable(TABLE_OPTIONS);

    /**
     * 検索ボタン押下→検索結果の表示
     */
    $('#btnSearchIndex').on('click', makeIndex);

    /**
     * 検索結果の表示
     */
    function makeIndex() {
        let search = $('#txtSearch').val();
        let company_filter = $('#drpCompany').val();
        let lower_price = $('#numLowerPrice').val();
        let high_price = $('#numHighPrice').val();
        let lower_stock = $('#numLowerStock').val();
        let high_stock = $('#numHighStock').val();

        $.ajax({
            url: '/products.index.result',
            type: 'GET',
            data: {
                'search': search,
                'company_filter': company_filter,
                'lower_price': lower_price,
                'high_price': high_price,
                'lower_stock': lower_stock,
                'high_stock': high_stock,
            },
            dataType: 'json'

        }).done(function (response) {
            //テーブルのデータ(td)を削除
            $('#indexTable tbody').empty();
            // dataTablesのリセット
            table.clear().draw();
            table.destroy();

            for (const index in response) {
                let tr = $('<tr></tr>');

                // id
                let td1 = $('<td></td>', {
                    "class": "index-table__contents--right"
                });
                td1.text(response[index].id);

                // 商品画像
                let td2 = $('<td></td>', {
                    "class": "index-table__contents--img"
                });
                let img_src = 'storage/product_images/' + response[index].img_path;
                let td_img = $('<img></img>', {
                    "class": "index-table__img",
                    "src": img_src,
                    "alt": "商品画像"
                });
                td2.append(td_img);

                // 商品名
                let td3 = $('<td></td>', {
                    "class": "index-table__contents--left"
                });
                td3.text(response[index].product_name);

                // 価格
                let td4 = $('<td></td>', {
                    "class": "index-table__contents--right"
                });
                td4.text("\xA5" + response[index].price);

                // 在庫
                let td5 = $('<td>/td>', {
                    "class": "index-table__contents--right"
                });
                td5.text(response[index].stock);

                // メーカー名
                let td6 = $('<td></td>', {
                    "class": "index-table__contents--left"
                });
                td6.text(response[index].company.company_name);

                // 詳細+削除ボタン
                let td7 = $('<td></td>', {
                    "class": "index-table__contents--btn"
                });
                // 詳細ボタン
                let td_btn1 = $('<button></button>', {
                    "class": "default__btn",
                    "type": "button",
                    "onclick": 'location.href="/product.show/' + response[index].id + '"',
                    "text": "詳細"
                });
                // 削除ボタン
                let td_btn2 = $('<button></button>', {
                    "class": "default__btn delete__btn-ajax index-table__delete-btn",
                    "type": "button",
                    "text": "削除"
                });
                td7.append(td_btn1);
                td7.append(td_btn2);


                // デーブル行作成
                tr.append(td1);
                tr.append(td2);
                tr.append(td3);
                tr.append(td4);
                tr.append(td5);
                tr.append(td6);
                tr.append(td7);
                $('tbody').append(tr);
            }

            // dataTablesの適用
            table = $('#indexTable').DataTable(TABLE_OPTIONS);

            // 削除実行記録をリセット
            deleteControlReset();

        }).fail(function () {
            console.log('エラー');
        });
    }

    /**
     * 削除ボタンの実行
     */
    $(document).on('click', '.delete__btn-ajax', function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if (confirm("削除しますか？")) {
            $(this).parents('tr').remove();

            let delete_id = $(this).closest('tr').children('td').first().text();

            $.ajax({
                type: 'post',
                url: '/products.destroy/' + delete_id,
                dataType: 'json',
                data: {
                    'id': delete_id
                }
            })
                .done(function () {
                    $('.alert-success').empty();
                    let message = "ID:" + delete_id + "を削除しました";
                    $('.alert-success').append('<p>' + message + '</p>');

                    // 削除実行を記録（ソート等の制御用）
                    deleteControlDone();

                }).fail(function () {
                    console.log('エラー');
                });
        }
    });

    /**

    * 削除実行後のソート、ページングの制御用
     */
    // デフォルト
    var delete_done = 'no';

    // 削除後
    function deleteControlDone() {
        delete_done = 'yes';
    };

    // 削除前にリセット
    function deleteControlReset() {
        delete_done = 'no';
    };

    /**
     * 削除後の場合
     * ソート、ページング、表示数のボタン押下時は再検索する
     */
    function deleteCheck() {
        if (delete_done == 'yes') {
            makeIndex();
            // 削除実行記録をリセット
            deleteControlReset();
        }
    }
        //  ソートボタン
        $(document).on('click', 'th', deleteCheck);
        //  ページングボタン
        $(document).on('click', '.paginate_button', deleteCheck);
        // 表示数ボタン
        $(document).on('change', 'select', deleteCheck);

});
