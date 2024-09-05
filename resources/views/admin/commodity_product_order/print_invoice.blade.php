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
            page-break-before: always;
        }

        @media print {
            .page-break {
                page-break-before: always;
            }
        }

        body {
            font-weight: normal;
            padding: 0;
            margin: 0;
        }

        table {
            width: 100%;
            font-size: 14px;
        }

        .table {
            border-collapse: collapse;
            border: 1px solid;
        }

        table tr {
            border: 1px solid;
        }

        .table th,
        .table td {
            padding: 5px 8px;
            border-right: 1px solid;
        }

        .table .a {
            width: 350px;
            table-layout: fixed;
        }

        .text-start {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .px-40 {
            padding: 10px 40px;
        }

        .invoice {
            padding: 0;
        }

        .invoice .tax {
            font-size: 20px;
            font-weight: 600;
        }

        .b-0 {
            border: 0;
        }

        .b-1 {
            border: 1px solid #000;
        }

        .b-top {
            border-top: 1px solid #000;
        }

        .t-bold {
            font-weight: 600;
        }

        .h-30 {
            height: 30px;
        }
    </style>
</head>

<body class="px-40">
    <div class="invoice">
        <table>
            <tr>
                <td class="text-center tax">Tax Invoice</td>
                <td class="text-end">E-invoice</td>
            </tr>
            <tr>
                <td>
                    <span>IRN : <b>3498dfnkfgkfbgkj348943898438rnkbdk</b></span><br>
                    <span>ACK : <b>457945793479744397</b></span><br>
                    <span>ACK Date : <b>{{ date('Y-m-d') }}</b></span><br>
                </td>
                <td class="text-end">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?data=HelloWorld&amp;size=100x100"
                        alt="" style="width:50%" />
                </td>
            </tr>
        </table>
    </div>
    @php
        $invoice_no = rand(1111, 9999);
        $e_bill_no = rand(111111111, 999999999);
    @endphp
    <div class="invoice">
        <table class="table">
            <tr class="b-1">
                <td colspan="3" rowspan="3" class="a"><b>BIZNIE</b><br>
                    1ST FLOOR CEAT TYPE LTD<br>
                    DR BHATTI ROAD BHATINDA PANJAB (03) 151001<br>
                    GSTIN/UIN: 09AAECM1417A1Z1<br>
                    State Name : PANJAB<br>
                    CIN: U27104UP200PTC028874</td>
                <td><span>Invoice No.<br>
                        MMAP/24-25/{{ $invoice_no }} </span><br>
                    <span> e-Way Bill No.<br>
                        {{ $e_bill_no }}</span>
                </td>
                <td>Dated<br>
                    {{ date('Y-m-d') }}</td>
            </tr>
            <tr class="b-1">
                <td>Delivery Note</td>
                <td>Mode/Terms of Payment</td>
            </tr>
            <tr class="b-1">
                <td>Dispatch Doc No</td>
                <td>Delivery Note Date</td>
            </tr>
            <tr class="b-1">
                <td colspan="3" rowspan="3" class="a">Consignee (Ship to)<br>
                    <b>M/S {{ $order_detail->getSeller->name }}</b><br>
                    {{ $order_detail->billing_address['address_line_one'] }}, MOB<br>
                    NO {{ $order_detail->getSeller->phone }}<br>
                    {{ $order_detail->billing_address['state'] }} - {{ $order_detail->billing_address['pin_code'] }}, India<br>
                    GSTIN/UIN : 09AGOPN3178Q1ZC<br>
                    State Name : {{ $order_detail->billing_address['state'] }}
                </td>
                <td>Dispatched through<br>
                    <b>BY ROAD</b>
                </td>
                <td>Destination<br>
                    <b>{{ $order_detail->billing_address['address_line_one'] }}</b>
                </td>
            </tr>
            <tr class="b-1">
                <td>Bill of Lading/LR-RR No</td>
                <td>Motor Vehicle No.<br>
                    <b>{{ $driver_detail->vehicle_number }}</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom:0;">Term oF Delivery
                    <br>
                    <span class="t-bold">FREIGHT PAID BY CONSIGNEE</span>
                </td>
            </tr>
            <tr>
                <td colspan="3" rowspan="3" class="a b-top">Buyer (Bill to)<br>
                    <b>M/S {{ $order_detail->getCustomer->name }}</b><br>
                    {{ $order_detail->delivery_address['address_line_one'] }}, MOB<br>
                    NO {{ $order_detail->getCustomer->phone }}<br>
                    {{ $order_detail->delivery_address['state'] }} - {{ $order_detail->billing_address['pin_code'] }}, India<br>
                    GSTIN/UIN : 09AGOPN3178Q1ZC<br>
                    State Name : {{ $order_detail->delivery_address['state'] }}, Code : 09
                </td>
                <td rowspan="3" colspan="2" style="border-top:1px solid #fff;"></td>
            </tr>
        </table>
    </div>
    <div class="invoice">
        <table class="table">
            <tr class="b-1">
                <th>S.No.</th>
                <th>Description Of <br>Good Services</th>
                <th>HSN/SAC</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Per</th>
                <th>Amount</th>
            </tr>
            @php
                $total_price = 0;
            @endphp
            @foreach ($order_detail->value as $variation)
                <tr class="b-0 h-30">
                    <td>{{ $loop->iteration }}</td>
                    <td class="t-bold">
                        @foreach ($variation['value'] as $value)
                            {{ $value['value'] }} {{ $value['unit']['short_name'] }},
                        @endforeach
                    </td>
                    <td>{{rand(1111, 2222)}}</td>
                    <td class="t-bold text-center">{{ $variation['quantity'] }} MT</td>
                    <td class="text-center">{{ formatIndianNumber($variation['price']) }}</td>
                    <td>MT</td>
                    @php
                        $total_price += $variation['price'] + $order_detail->base_price;
                    @endphp
                    <td class="t-bold text-end">{{ formatIndianNumber($variation['price'] + $order_detail->base_price) }}</td>
                </tr>
            @endforeach
            <tr class="b-0 h-30">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="b-top text-end">{{ formatIndianNumber($total_price) }}</td>
            </tr>
            <tr class="b-0 h-30">
                <td></td>
                <td class="t-bold text-end">Insurance on Sale</td>
                <td>997114</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="t-bold text-end">97.80</td>
            </tr>
            <tr class="b-0 h-30">
                <td></td>
                <td class="t-bold text-end">CGST OUTPUT TAX </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="t-bold text-end">40,888.78</td>
            </tr>
            <tr class="b-0 h-30">
                <td></td>
                <td class="t-bold text-end">SGST OUTPUT TAX</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="t-bold text-end">40,888.78</td>
            </tr>
            <tr class="b-0 h-30">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="b-0 h-30">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="b-0 h-30">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="b-0 h-30">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="b-0 h-30">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr style="border:1px solid #000;">
                <td colspan="7" class="text-end">continued to page number 2</td>
            </tr>
        </table>
    </div>
    <!--Next Page -->
    <div class="page-break"></div>
    <h2 class="text-center">Tax Invoice</h2>
    <div class="invoice">
        <table class="table">
            <tr class="b-1">
                <td colspan="3" rowspan="3" class="a"><b>BIZNIE</b><br>
                    1ST FLOOR CEAT TYPE LTD<br>
                    DR BHATTI ROAD BHATINDA PANJAB (03) 151001<br>
                    GSTIN/UIN: 09AAECM1417A1Z1<br>
                    State Name : PANJAB<br>
                    CIN: U27104UP200PTC028874</td>
                <td><span>Invoice No.<br>
                        MMAP/24-25/{{ $invoice_no }} </span><br>
                    <span> e-Way Bill No.<br>
                        {{ $e_bill_no }}</span>
                </td>
                <td>Dated<br>
                    {{ date('Y-m-d') }}</td>
            </tr>
            <tr class="b-1">
                <td>Delivery Note</td>
                <td>Mode/Terms of Payment</td>
            </tr>
            <tr class="b-1">
                <td>Dispatch Doc No</td>
                <td>Delivery Note Date</td>
            </tr>
            <tr class="b-1">
                <td colspan="3" rowspan="3" class="a">Consignee (Ship to)<br>
                    <b>M/S {{ $order_detail->getSeller->name }}</b><br>
                    {{ $order_detail->billing_address['address_line_one'] }}, MOB<br>
                    NO {{ $order_detail->getSeller->phone }}<br>
                    {{ $order_detail->billing_address['state'] }} - {{ $order_detail->billing_address['pin_code'] }}, India<br>
                    GSTIN/UIN : 09AGOPN3178Q1ZC<br>
                    State Name : {{ $order_detail->billing_address['state'] }}
                </td>
                <td>Dispatched through<br>
                    <b>BY ROAD</b>
                </td>
                <td>Destination<br>
                    <b>{{ $order_detail->billing_address['address_line_one'] }}</b>
                </td>
            </tr>
            <tr class="b-1">
                <td>Bill of Lading/LR-RR No</td>
                <td>Motor Vehicle No.<br>
                    <b>{{ $driver_detail->vehicle_number }}</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom:0;">Term oF Delivery
                    <br>
                    <span class="t-bold">FREIGHT PAID BY CONSIGNEE</span>
                </td>
            </tr>
            <tr>
                <td colspan="3" rowspan="3" class="a b-top">Buyer (Bill to)<br>
                    <b>M/S {{ $order_detail->getCustomer->name }}</b><br>
                    {{ $order_detail->delivery_address['address_line_one'] }}, MOB<br>
                    NO {{ $order_detail->getCustomer->phone }}<br>
                    {{ $order_detail->delivery_address['state'] }} - {{ $order_detail->billing_address['pin_code'] }}, India<br>
                    GSTIN/UIN : 09AGOPN3178Q1ZC<br>
                    State Name : {{ $order_detail->delivery_address['state'] }}, Code : 09
                </td>
                <td rowspan="3" colspan="2" style="border-top:1px solid #fff;"></td>
            </tr>
        </table>
    </div>
    <div class="invoice">
        <table class="table border-table">
            <tr class="b-1">
                <th>S.No.</th>
                <th>Description Of <br>Good Services</th>
                <th>HSN/SAC</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Per</th>
                <th>Amount</th>
            </tr>
            <tr class="b-0 h-30">
                <td></td>
                <td class="t-bold">TCS ON SALE (0.1%) </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-end t-bold">536.00</td>
            </tr>
            <tr class="b-0 h-30">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-end b-top">5,36,633.35</td>
            </tr>
            <tr class="b-0 h-30">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="b-0 h-30">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="b-0 h-30">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr style="border:1px solid #000;">
                <td></td>
                <td class="text-end">Total</td>
                <td></td>
                <td class="text-center t-bold">9.780 MT</td>
                <td></td>
                <td></td>
                <td class="t-bold text-end">Rs. 5,36,633.35</td>
            </tr>
            <tr class="b-0">
                <td colspan="7">Amount Chargeable (in words) </td>
            </tr>
            <tr class="b-0">
                <td colspan="7" class="t-bold">INR Five Lakh Thirty Six Thousand Six Hundred Thirty Three and
                    Thirty Five paise Only</td>
            </tr>
            <tr class="b-top">
                <td rowspan="2">HSN/SAC</td>
                <td rowspan="2" class="text-center">Taxable<br>Value

                </td>
                <td colspan="2" class="text-center">CGST</td>
                <!-- <td></td> -->
                <td colspan="2" class="text-center">SGST/UTGST</td>
                <!-- <td></td> -->
                <td rowspan="2" class="text-center">Total
                    <br>
                    Tax Amount
                </td>
            </tr>
            <tr>
                <!-- <td></td> -->
                <!-- <td>Value</td> -->
                <td class="text-center">Rate</td>
                <td class="text-center">Amount</td>
                <td class="text-center">Rate</td>
                <td class="text-center">Amount</td>
                <!-- <td>Tax Amount</td> -->
            </tr>
            <tr>
                <td rowspan="2">72142090<br>
                    997114
                </td>
                <td class="text-end">4,54,221.99</td>
                <td class="text-end">9%</td>
                <td class="text-end">40,879.98</td>
                <td class="text-end">9%</td>
                <td class="text-end">40,879.98</td>
                <td class="text-end">81,759.96</td>
            </tr>
            <tr>
                <!-- <td>997114</td> -->
                <td class="text-end">97.80</td>
                <td class="text-end">9%</td>
                <td class="text-end">8.80</td>
                <td class="text-end">9%</td>
                <td class="text-end">8.80</td>
                <td class="text-end">17.56</td>
            </tr>
            <tr>
                <td class="t-bold text-end">Total</td>
                <td class="t-bold text-end">4,54,319.79</td>
                <td></td>
                <td class="t-bold text-end">40,888.78</td>
                <td></td>
                <td class="t-bold text-end">40,888.78</td>
                <td class="t-bold text-end">81,777.56</td>
            </tr>
            <tr class="b-0">
                <td colspan="7">Tax Amount (in words) : <b>INR Eighty One Thousand Seven Hundred Seventy Seven and
                        Fifty Six paise Only</b></td>
                <!-- <td></td> -->
            </tr>
            <tr class="b-0">
                <td colspan="7">Company’s PAN : <b>AAECM 1417 A</b></td>
                <!-- <td></td> -->
            </tr>
            <tr class="b-0">
                <td rowspan="2" colspan="4"><span style="text-decoration:underline;">Declaration </span>:<br>
                    <span>We declare that this invoice shows the actual price of the <br>
                        goods described and that all particulars are true and correct.</span>
                </td>
                <td rowspan="2" class="text-end b-top" colspan="6"><b>for  BIZNIE</b><br><br>
                    Authorised Signatory
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
