@extends('layouts.ecommerce')

@section('title')
    <title>Checkout - Dw Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="overlay"></div>
			<div class="container">
				<div class="banner_content text-center">
					<h2>Informasi Pengiriman</h2>
					<div class="page_link">
            <a href="{{ url('/') }}">Home</a>
						<a href="#">Checkout</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<!--================Checkout Area =================-->
	<section class="checkout_area section_gap">
		<div class="container">
			<div class="billing_details">
				<div class="row">
					<div class="col-lg-8">
            <h3>Informasi Pengiriman</h3>          
              @if (session('error'))
                  <div class="alert alert-danger">{{ session('error') }}</div>
              @endif
                        
              
            	<!-- REMOVE DULU VALUE ACTION-NYA JIKA INGIN MELIHATNYA DI BROWSER -->
            	<!-- KARENA ROUTE NAME front.store_checkout BELUM DIBUAT -->
              <form class="row contact_form" action="{{ route('cart.store') }}" method="post" novalidate="novalidate">
                            @csrf
                        <div class="col-md-12 form-group p_star">
                            <label for="">Nama Lengkap</label>
                            <input type="text" class="form-control" id="first" name="name" required>
                            
                            <!-- UNTUK MENAMPILKAN JIKA TERDAPAT ERROR VALIDASI -->
                            <p class="text-danger">{{ $errors->first('name') }}</p>
                        </div>
                        <div class="col-md-6 form-group p_star">
                            <label for="">No Telp</label>
                            <input type="text" class="form-control" id="number" name="phone" required>
                            <p class="text-danger">{{ $errors->first('phone') }}</p>
                        </div>
                        <div class="col-md-6 form-group p_star">
                            <label for="">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <p class="text-danger">{{ $errors->first('email') }}</p>
                        </div>
                        @php
                        //dd($cart);
                        @endphp
                        <div class="col-md-12 form-group p_star">
                            <label for="">Alamat Lengkap</label>
                            <input type="text" class="form-control" id="add1" name="address" required>
                            <p class="text-danger">{{ $errors->first('address') }}</p>
                        </div>
                        <div class="col-md-12 form-group p_star">
                            <label for="">Propinsi</label>
                            <select class="form-control" name="province_id" id="province_id" required>
                                <option value="">Pilih Propinsi</option>
                              	<!-- LOOPING DATA PROVINCE UNTUK DIPILIH OLEH CUSTOMER -->
                                @foreach ($provinces as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-danger">{{ $errors->first('province_id') }}</p>
                        </div>
                
                  <!-- ADAPUN DATA KOTA DAN KECAMATAN AKAN DI RENDER SETELAH PROVINSI DIPILIH -->
                        <div class="col-md-12 form-group p_star">
                            <label for="">Kabupaten / Kota</label>
                            <select class="form-control" name="city_id" id="city_id" required>
                                <option value="">Pilih Kabupaten/Kota</option>
                            </select>
                            <p class="text-danger">{{ $errors->first('city_id') }}</p>
                        </div>
                        <div class="col-md-12 form-group p_star">
                            <label for="">Kecamatan</label>
                            <select class="form-control" name="district_id" id="district_id" required>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                            <p class="text-danger">{{ $errors->first('district_id') }}</p>
                        </div>
                <!-- ADAPUN DATA KOTA DAN KECAMATAN AKAN DI RENDER SETELAH PROVINSI DIPILIH -->
                    
					</div>
					<div class="col-lg-4">
						<div class="order_box">
							<h2>Ringkasan Pesanan</h2>
							<ul class="list">
								<li>
									<a href="#">Product
										<span>Total</span>
									</a>
                </li>
                @foreach ($cart as $cart)
								<li>
									<a href="#">{{ \Str::limit($cart['name'], 10) }}
                    <span class="middle">x {{ $cart['qty'] }}</span>
                    <span class="last">Rp {{ number_format($cart['price']) }}</span>
									</a>
                </li>
                @endforeach
							</ul>
							<ul class="list list_2">
								<li>
									<a href="#">total
                    <span>Rp {{ number_format($total) }}</span>
									</a>
								</li>
								<li>
									<a href="#">Pengiriman
										<span>Rp 0</span>
									</a>
								</li>
								<li>
									<a href="#">Total
										<span>Rp {{ number_format($total) }}</span>
									</a>
								</li>
							</ul>
              <button class="main_btn">Bayar Pesanan</button>
              </form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Checkout Area =================-->
@endsection

@section('js')
    <script>
        $('#province_id').on('change', function(){
            $.ajax({
                url: "{{url('/api/city')}}",
                type: "GET" ,
                data: { province_id: $(this).val() },
                success: function(html){
                    $('#city_id').empty()
                    $('#city_id').append("<option value=''>Pilih Kabupaten/Kota</option>")
                    $.each(html.data, function(key, item){
                        $('#city_id').append("<option value='"+item.id+"'>"+item.name+"</option>")
                    })
                }
            });
        })
        $('#city_id').on('change',function(){
            $.ajax({
                url: "{{url('/api/district')}}",
                type: "GET",
                data: {city_id: $(this).val()},
                success: function(html){
                    $('#district_id').empty()
                    $('#district_id').append("<option value=''>Pilih Kecamatan</option>")
                    $.each(html.data, function(key, item){
                        $('#district_id').append("<option value='"+item.id+"'>"+item.name+"</option>")
                    })
                }
            });
        })
    </script>
@endsection