<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>INVOICE</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta charset="UTF-8">
	<style media="all">
		@page {
			margin: 0;
			padding: 0;
		}

		body {
			font-size: 0.875rem;
			font-weight: normal;
			padding: 0;
			margin: 0;
		}

		.gry-color *,
		.gry-color {
			color: #000;
		}

		table {
			width: 100%;
		}

		table th {
			font-weight: normal;
		}

		table.padding th {
			padding: .25rem .7rem;
		}

		table.padding td {
			padding: .25rem .7rem;
		}

		table.sm-padding td {
			padding: .1rem .7rem;
		}

		.border-bottom td,
		.border-bottom th {
			border-bottom: 1px solid #eceff4;
		}
		.text-left{
			text-align:left;
		}

		.text-center {
			text-align: center;
		}

		.text-right {
			text-align: right;
		}
	</style>
</head>

<body>
	<div class="header" style="border: 1px solid #333;  text-align: center;">
		<h6 style="margin: 5px 0;">INVOICE</h6>
		<h4>BIZNIE</h4>
		<p>1ST FLOOR CEAT TYPE LTD.DR BHATTI ROAD BHATINDA PANJAB (03) 151001</p>
		<p>Tel: 9876543210 <span>Email: admin@gmail.com</span></p>
	</div>
	<div>
		<div style="padding: 1rem;">
			<table>
				<tr>
					<td style="font-size: 1rem;" class="strong">Invoice No.: {{rand(1111, 9999)}}</td>
					<td>Vehicle No.: {{$driver_detail->vehicle_number}}</td>
				</tr>
				<tr>
					<td class="gry-color small">Dated: {{$driver_detail->created_at}}</td>
					<td>Station</td>
				</tr>
				<tr>
					<td class="gry-color small">Place of Supply</td>
					<td>E-Way Bill No.: {{rand(1111, 9999)}}</td>
				</tr>
				<tr>
					<td class="gry-color small">Reverse Charge</td>
					<td>Purchase Order</td>
				</tr>
				<tr>
					<td class="gry-color small">GR / RR No.</td>
					<td>Challan No.</td>
				</tr>
				<tr>
					<td class="gry-color small">Transport</td>
					<td>LC No.</td>
				</tr>
			</table>
		</div>
		<hr>
		<div style="padding: 1rem;">
			<table>
				<tr>
					<th class="text-left">Billed to</th>
					<th class="text-left">Shipped to</th>
				</tr>
				<tr>
					<td>
                        <b>Pincode: </b> {{ $order_detail->billing_address['pin_code'] }} <br>
                        <b>Address: </b> {{ $order_detail->billing_address['address_line_one']??'' }} <br>
                        <b>City: </b> {{ $order_detail->billing_address['city'] }} <br>
                        <b>State: </b> {{ $order_detail->billing_address['state'] }} <br>
                    </td>
					<td>
                        <b>Pincode: </b> {{ $order_detail->delivery_address['pin_code'] }} <br>
                        <b>Address: </b> {{ $order_detail->delivery_address['address_line_one']??'' }} <br>
                        <b>City: </b> {{ $order_detail->delivery_address['city'] }} <br>
                        <b>State: </b> {{ $order_detail->delivery_address['state'] }} <br>
                    </td>
				</tr>
			</table>
		</div>
		<hr>
		<div class="table-responsive">
            <table class="table" style="border-collapse: collapse; width: 100%;">
                <tr style="background:#eae7e7;">
                    <th style="border: 1px solid #333; text-align: left;  padding: 15px;">SN.</th>
                    @foreach ($order_detail->value[0]['value'] as $variation_heading)
                        <th style="border: 1px solid #333; text-align: left;  padding: 15px;">{{ $variation_heading['name'] }}</th>
                    @endforeach
                    <th style="border: 1px solid #333; text-align: left;  padding: 15px;">Quantity</th>
                    <th style="border: 1px solid #333; text-align: left;  padding: 15px;">Gauge Diff.</th>
                    <th style="border: 1px solid #333; text-align: left;  padding: 15px;">Final Price</th>
                </tr>
                @foreach ($order_detail->value as $variation)
                    <tr>
                        <td style="border: 1px solid #333; text-align: left;  padding: 15px;">{{$loop->iteration}}</td>
                        @foreach ($variation['value'] as $value)
                            <td style="border: 1px solid #333; text-align: left;  padding: 15px;">{{ $value['value'] }}</td>
                        @endforeach
                        <td style="border: 1px solid #333; text-align: left;  padding: 15px;">{{ $variation['quantity'] }}</td>
                        <td style="border: 1px solid #333; text-align: left;  padding: 15px;">{{ $variation['price'] }}</td>
                        <td style="border: 1px solid #333; text-align: left;  padding: 15px;">{{ $variation['price'] + $order_detail->base_price }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="{{ count($order_detail->value[0]['value'])+1 }}" style="border: 1px solid #333; text-align: left; padding: 15px; border-left: hidden; border-bottom: hidden;">
                        <td style="border: 1px solid #333; text-align: left; padding: 15px;">
                            <label for="base_price" class="form-label">Base Price</label>
                        </td>
                        <td colspan="2" style="border: 1px solid #333; text-align: left; padding: 15px;">
                            {{ $order_detail->base_price }}
                        </td>
                    </td>
                </tr>

                <tr>
                    <td colspan="{{ count($order_detail->value[0]['value'])+1 }}" style="border: 1px solid #333; text-align: left; padding: 15px; border-left: hidden; border-bottom: hidden;">
                        <td style="border: 1px solid #333; text-align: left; padding: 15px;">
                            <label for="transport_price" class="form-label">Transport Price</label>
                        </td>
                        <td colspan="2" style="border: 1px solid #333; text-align: left; padding: 15px;">
                            {{ $order_detail->transport_price }}
                        </td>
                    </td>
                </tr>
                <tr>
                    <td colspan="{{ count($order_detail->value[0]['value'])+1 }}" style="border: 1px solid #333; text-align: left; padding: 15px; border-left: hidden; border-bottom: hidden;">
                        <td style="border: 1px solid #333; text-align: left; padding: 15px;">
                            <label for="commission" class="form-label">Commission</label>
                        </td>
                        <td colspan="2" style="border: 1px solid #333; text-align: left; padding: 15px;">
                            {{ $order_detail->commission }}
                        </td>
                    </td>
                </tr>
            </table>
        </div>

		<div style="padding: 1rem;">
			<table>
				<tr>
					<td>Terms and Conditions:</td>
					<td>E-invoice QR Code</td>
					<td>Receiver's Signature</td>
				</tr>
				<tr>
					<td>1. Goods once sold will not be taken back.
						<br>
						2. Interest @ 18% p.a. will be charged <br> if the payment is not made with in the stipulated time.
						<br>3. Subject to 'BATHINDA' jurisdiction only.
					</td>
					<td>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?data=HelloWorld&amp;size=100x100" alt="" style="width:50%"/>
                    </td>
					<td></td>
				</tr>
			</table>
		</div>

	</div>
</body>

</html>
