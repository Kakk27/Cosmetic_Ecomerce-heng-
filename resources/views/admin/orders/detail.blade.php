@extends('admin.layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Order: #{{$order->id}}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{route('orders.index')}}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            @include('admin.message')
            <div class="row">
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header pt-3">
                            <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                <h1 class="h5 mb-3">Shipping Address</h1>
                                <address>
                                    <strong>{{$order->first_name.' '.$order->last_name}}</strong><br>
                                    {{$order->address}}<br>
                                    {{$order->city}}, {{$order->zip}},{{$order->countryName}}<br>
                                    Phone: {{$order->mobile}}<br>
                                    Email: {{$order->email}}
                                </address>
                                <strong>Shipped Date</strong>
                                @if(!empty($order->shipped_date))
                                    {{ \Carbon\Carbon::parse($order->shipped_date)->format('d-M-Y') }}
                                @else
                                    n/a
                                @endif
                                </div>



                                <div class="col-sm-4 invoice-col">
                                    {{-- <b>Invoice #007612</b><br>
                                    <br> --}}
                                    <b>Order ID:</b> {{$order->id}}<br>
                                    <b>Total:</b>${{number_format($order->grand_total,2)}}<br>
                                    <b>Status:</b>
                                        @if ($order->status == 'pending')
                                        <span class="badge bg-danger">Pending</span>
                                        @elseif($order->status == 'shipped')
                                            <span class="badge bg-info">Shipped</span>
                                        @elseif ($order->status == 'delivered')
                                            <span class="badge bg-success">Delivered</span>
                                        @else
                                            <span class="badge bg-danger">cancelLed</span>
                                        @endif
                                    <br>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-3">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th width="100">Price</th>
                                        <th width="100">Qty</th>
                                        <th width="100">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderItems as $item)
                                        <tr>
                                            <td>{{$item->name}}</td>
                                            <td>${{number_format($item->price,2)}}</td>
                                            <td>{{$item->qty}}</td>
                                            <td>${{number_format($item->total,2)}}</td>
                                        </tr>
                                    @endforeach
                                        <th colspan="3" class="text-right">Subtotal:</th>
                                        <td>${{number_format($order->subtotal,2)}}</td>
                                    </tr>

                                    <tr>
                                        <th colspan="3" class="text-right">Dicount:{{(!empty($order->coupon_code))? '('.$order->coupon_code.')':''}}</th>
                                        <td>${{number_format($order->discount,2)}}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">Shipping:</th>
                                        <td>${{number_format($order->shipping,2)}}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">Grand Total:</th>
                                        <td>${{number_format($order->grand_total,2)}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <form action="" method="POST" id="changeOrderStatusForm" name="changeOrderStatusForm">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Order Status</h2>
                                <div class="mb-3">
                                    <select name="status" id="status" class="form-control">
                                        <option value="pending"{{($order->status == 'pending')? 'selected' : ''}}>Pending</option>
                                        <option value="shipped"{{($order->status == 'shipped')? 'selected' : ''}}>Shipped</option>
                                        <option value="delivered"{{($order->status == 'delivered')? 'selected' : ''}}>Delivered</option>
                                        <option value="cancelled"{{($order->status == 'cancelled')? 'selected' : ''}}>Cancelled</option>
                                        {{-- <option value="{{($order->status == '')? 'selected' : ''}}">Cancelled</option> --}}
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="shipped_date">Shipped Date</label>
                                    <input placeholder="Shipped Date" value="{{$order->shipped_date}}" class="form-control" type="text" name="shipped_date" id="shipped_date">
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Send Inovice Email</h2>
                            <div class="mb-3">
                                <select name="status" id="status" class="form-control">
                                    <option value="">Customer</option>
                                    <option value="">Admin</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary">Send</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </section>
@endsection

@section('customJs')
    <script>
        $(document).ready(function(){
            $('#shipped_date').datetimepicker({
                // options here
                format:'Y-m-d H:i:s',
            });
        });

        $("#changeOrderStatusForm").submit(function(event){
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled',true);
            $.ajax({
                url: '{{route("orders.changeOrderStatusForm",$order->id)}}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success:function(response){
                    $("button[type=submit]").prop('disabled',false);
                     window.location.href="{{route('orders.detail',$order->id)}}"
                    // if(response["status"]== true){
                    //     window.location.href="{{route('coupons.index')}}"
                    //     $("#code").removeClass('is-invalid')
                    //         .siblings('p')
                    //         .removeClass('invalid-feedback').html("");
                    //     $("#discount_amount").removeClass('is-invalid')
                    //         .siblings('p')
                    //         .removeClass('invalid-feedback').html("");
                    //     $("#starts_at").removeClass('is-invalid')
                    //         .siblings('p')
                    //         .removeClass('invalid-feedback').html("");
                    //     $("#expires_at").removeClass('is-invalid')
                    //         .siblings('p')
                    //         .removeClass('invalid-feedback').html("");
                    // }else{
                    //     var errors = response['errors']
                    //     if(errors['code']){
                    //         $("#code").addClass('is-invalid')
                    //         .siblings('p')
                    //         .addClass('invalid-feedback').html(errors['code']);
                    //     }else{
                    //         $("#code").removeClass('is-invalid')
                    //         .siblings('p')
                    //         .removeClass('invalid-feedback').html("");
                    //     }

                    //     if(errors['discount_amount']){
                    //         $("#discount_amount").addClass('is-invalid')
                    //         .siblings('p')
                    //         .addClass('invalid-feedback').html(errors['discount_amount']);
                    //     }else{
                    //         $("#discount_amount").removeClass('is-invalid')
                    //         .siblings('p')
                    //         .removeClass('invalid-feedback').html("");
                    //     }

                    //     if(errors['starts_at']){
                    //         $("#starts_at").addClass('is-invalid')
                    //         .siblings('p')
                    //         .addClass('invalid-feedback').html(errors['starts_at']);
                    //     }else{
                    //         $("#starts_at").removeClass('is-invalid')
                    //         .siblings('p')
                    //         .removeClass('invalid-feedback').html("");
                    //     }

                    //     if(errors['expires_at']){
                    //         $("#expires_at").addClass('is-invalid')
                    //         .siblings('p')
                    //         .addClass('invalid-feedback').html(errors['expires_at']);
                    //     }else{
                    //         $("#expires_at").removeClass('is-invalid')
                    //         .siblings('p')
                    //         .removeClass('invalid-feedback').html("");
                    //     }
                    // }

                }
            })
        });
    </script>
@endsection
