<!DOCTYPE html>
<html lang="en">

<head>
      <title>Bizni Invoice</title>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <style>
            @media print {
                  body{
                        margin: 0;
                  }
                  .invoice-row {
                        display: flex;

                  }

                  .invoice-row .invoice {
                        width: 50%;
                  }

                  .invoice-container {
                        padding: 2rem;
                  }

                  .invoice-container .header {
                        text-align: center;
                  }

                  .invoice-row .invoice h6 {
                        font-size: 15px;
                        margin: 5px 0;
                  }

                  .invoice-container .header h6 {
                        margin: 5px 0;
                  }

                  .irn-row {
                        display: flex;

                  }

                  .irn-row .invoice {
                        width: 30%;
                  }

                  table,
                  td,
                  th {
                        border: 1px solid #333;
                        text-align: left;
                  }

                  table {
                        border-collapse: collapse;
                        width: 100%;
                  }

                  th,
                  td {
                        padding: 15px;
                  }

                  .sign-row {
                        display: flex;

                  }

                  .sign-row .invoice {
                        width: 33%;
                  }
            }

            .invoice-row {
                  display: flex;

            }

            .invoice-row .invoice {
                  width: 50%;
            }

            .invoice-container {
                  padding: 2rem;
            }

            .invoice-container .header {
                  text-align: center;
            }

            .invoice-row .invoice h6 {
                  font-size: 15px;
                  margin: 5px 0;
            }

            .invoice-container .header h6 {
                  margin: 5px 0;
            }

            .irn-row {
                  display: flex;

            }

            .irn-row .invoice {
                  width: 30%;
            }

            table,
            td,
            th {
                  border: 1px solid #333;
                  text-align: left;
            }

            table {
                  border-collapse: collapse;
                  width: 100%;
            }

            th,
            td {
                  padding: 15px;
            }

            .sign-row {
                  display: flex;

            }

            .sign-row .invoice {
                  width: 33%;
            }
            .p-1{
                  padding:1rem;
            }

            @media(max-width:576px) {
                  .table-responsive {
                        overflow-x: scroll;
                  }

                  .invoice-container {
                        padding: .5rem;
                  }

                  .invoice-row {
                        flex-direction: column;
                        row-gap: 20px;
                  }

                  .invoice-row .invoice {
                        width: 100%;
                  }
            }
      </style>
</head>
<body>
      <div class="invoice-container">
            <div class="header" style="border: 1px solid #333;">
                  <h6>INVOICE</h6>
                  <h4>BIZNIE</h4>
                  <p>1ST FLOOR CEAT TYPE LTD.DR BHATTI ROAD BHATINDA PANJAB (03) 151001</p>
                  <p>Tel: 9876543210 <span>Email: admin@gmail.com</span></p>
            </div>

            <div class="invoice-row p-1" style="border: 1px solid #333;">
                  <div class="invoice">
                        <h6>Invoice No. 3856</h6>
                        <h6>Dated</h6>
                        <h6>Place of Supply</h6>
                        <h6>Reverse Charge</h6>
                        <h6>GR / RR No.</h6>
                        <h6>Transport</h6>
                  </div>
                  <div class="invoice">
                        <h6>Vehicle No.</h6>
                        <h6>Station</h6>
                        <h6>E-Way Bill No.</h6>
                        <h6>Purchase Order</h6>
                        <h6>Challan No.</h6>
                        <h6>LC No.</h6>
                  </div>
            </div>

            <div class="invoice-row p-1" style="border: 1px solid #333;">
                  <div class="invoice">
                        <h5>Billed to</h5>
                        <h6>DAMODAR CONSTRUCTION COMPANY :</h6>
                        <h6>PARTY PAN :</h6>
                        <h6>PARTY MOBILE NO :</h6>
                        <h6>Reverse Charge :</h6>
                        <h6>GSTIN / UIN :</h6>
                  </div>
                  <div class="invoice">
                        <h5>Shipped to</h5>
                        <h6>DAMODAR CONSTRUCTION COMPANY </h6>
                        <h6>PARTY PAN :</h6>
                        <h6>PARTY MOBILE NO :</h6>
                        <h6>Reverse Charge :</h6>
                        <h6>GSTIN / UIN :</h6>
                  </div>
            </div>

            <div class="irn-row p-1" style="border: 1px solid #333;">
                  <div class="invoice">
                        <p>IRN : </p>
                  </div>
                  <div class="invoice">
                        <p>ASK NO. :</p>
                  </div>
                  <div class="invoice">
                        <p>ACK DATE :</p>
                  </div>
            </div>
            <div class="table-responsive">
                  <table class="table">
                        <tr>
                              <th>SN.</th>
                              <th>Description of Goods</th>
                              <th>HSN/SAC Code</th>
                              <th>Qty</th>
                              <th>Unit</th>
                              <th>Price</th>
                              <th>Amt.Before</th>
                              <th>Ammount</th>
                        </tr>
                        <tr>
                              <td>1.</td>
                              <td>Griffin</td>
                              <td>$100</td>
                              <td></td>
                              <td>MT</td>
                              <td></td>
                              <td></td>
                              <td></td>
                        </tr>
                        <tr>
                              <td>2.</td>
                              <td>Griffin</td>
                              <td>$100</td>
                              <td></td>
                              <td>MT</td>
                              <td></td>
                              <td></td>
                              <td></td>
                        </tr>
                        <tr>
                              <td>3.</td>
                              <td>Griffin</td>
                              <td>$100</td>
                              <td></td>
                              <td>MT</td>
                              <td></td>
                              <td></td>
                              <td></td>
                        </tr>
                        <tr>
                              <td>4.</td>
                              <td>Griffin</td>
                              <td>$100</td>
                              <td></td>
                              <td>MT</td>
                              <td></td>
                              <td></td>
                              <td></td>
                        </tr>
                        <tr>
                              <td colspan="5"></td>
                              <td>Add:GST<br>Add:SGST</td>
                              <td>@ &nbsp;&nbsp;&nbsp;9%<br>@ &nbsp;&nbsp;&nbsp;9%</td>
                              <td>20,0000<br>1,02323</td>
                        </tr>
                        <tr>
                              <td colspan="7" style="text-align: center;">Grand Total </td>
                              <td>20,0000<br>1,02323</td>
                        </tr>
                  </table>
            </div>
            <div class="tax-detail p-1">
                  <h4>
                        <span>Tax Rate</span>
                        <span>Taxable Amt.</span>
                        <span>CGST Amt.</span>
                        <span>SGST Amt.</span>
                        <span>Total Tax</span>
                  </h4>
            </div>

            <div class="bank-detail p-1" style="border: 1px solid #333;">
                  <h4>Bank Detail :
                        <span>HDFC BANK LTD BATHINDA A/C NO. 500400300201, IFSC CODE : HDFC0001346 </span>
                  </h4>
            </div>

            <div class="sign-row p-1" style="border: 1px solid #333;">
                  <div class="invoice">
                        <h6>Terms and Conditions:</h6>
                        <p>1. Goods once sold will not be taken back.</p>
                        <p>2. Interest @ 18% p.a. will be charged <br> if the payment is not made with in the stipulated time.</p>
                        <p>3. Subject to 'BATHINDA' jurisdiction only.</p>
                  </div>
                  <div class="invoice">
                        <h6>E-invoice QR Code</h6>
                  </div>
                  <div class="invoice">
                        <h6>Receiver's Signature</h6>
                  </div>
            </div>
      </div>
</body>

</html>
