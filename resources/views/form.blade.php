<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
          <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Coalition</title>
        <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/datatables.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/dataTables.bootstrap.min.css') }}">

        </style>
    </head>
    <body>
        <div class = "page-header text-center">
           
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 col-sm-4">
                    <form class="form" action="#" method="POST">
                        <legend>Here you can submit a product</legend>
                        <div class="input-group">
                            <label for="product-name">Product Name</label>
                            <input type="text" class="form-control" placeholder="Product Name"  id="product-name" name="name">
                        </div>

                        <div class="input-group">
                            <label for="quantity">Quantity In Stock</label>
                            <input id="quantity" name="quantity" type="number" class="form-control" placeholder="Quantity" >
                        </div>

                        <div class="input-group">
                            <label for="price">Price Per Itme</label>
                            <input type="number" name="price" class="form-control" id="price" aria-label="Amount (to the nearest dollar)">
                        </div>
                        <button id="submit-from-btn" type="button" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="col-xs-12 col-sm-8">
                    <legend>Here you can <a href="#" class="btn btn-success" id="load-products">load</a> submitted products with <strong class="text-success">pagination</strong></legend>
                    <table class="table table-hover" id="products">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product name</th>
                                <th>Quantity in stock</th>
                                <th>Price per item</th>
                                <th>Datetime submitted </th>
                                <th>Total value </th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/datatables.min.js') }}"></script>

    <script type="text/javascript">
        $.ajaxSetup({
            'headers': {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $(document).ready( function () {
            var products_table = 
            $('#products').DataTable({
                "pageLength"    : 10,
                "deferRender"   : true,
                "processing"    : true,
                "serverSide"    : true,
                "deferLoading"  : 0,
                ajax : {
                    "url": "{{ route('products.all') }}"
                },
                columns: [
                    {
                        data: function(data, type, row, meta) {
                            if (typeof products_table != "undefined")
                                return meta.row + products_table.page.info().length * products_table.page.info().page + 1;
                            else 
                                return meta.row + 1;
                        },
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'quantity',
                        name: 'quantity',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'price',
                        name: 'price',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: false,
                        orderable: false
                    },
                    {
                        render: function (data, type, row, meta) {
                            return row['quantity'] * row['price']
                        }
                    }
                ],
                initComplete: function(settings, json) {
                    $('#products_filter').addClass('hidden').find(" input").unbind();
                }
            });
            $("#submit-from-btn").click(function (e) {
                e.preventDefault();
                var target_form = $(this).closest("form");
                console.log(target_form)
                $.ajax({
                    type    : 'POST',
                    url     : "{{ route('products.store') }}",
                    data    :  $(target_form).serialize(),
                    success : function (res) {
                        if(res.status) {
                            $("#price").val("");
                            $("#quantity").val("");
                            $("#product-name").val("");
                            alert(res.message);
                        }

                    } ,
                    error   : function (res) {
                        var msg = "";
                        $.each(res.responseJSON.errors, function (i, err) {
                            msg += err[0] + "\n\r"
                        })
                        if(res.status == 422)
                            alert(res.responseJSON.message + "\n\r" + msg);   
                    }
                })
            })
            $("#load-products").click(function(e) {
                e.preventDefault();
                products_table.search("").draw();
            })
        });
    </script>
</html>
