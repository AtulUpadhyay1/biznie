<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    @if($formMode)
        @include('admin.all_seller.form')
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                   <div class="card-header">
                        <div class="row">
                            <div class="col-6 card-title">
                                <h4>All Sellers</h4>
                            </div>
                            <div class="col-6 text-end">
                                <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                    <div class="input-group flatpickr wd-200 me-2 mb-2 mb-md-0" id="dashboardDate">
                                        <span class="input-group-text input-group-addon bg-transparent border-danger"
                                            data-toggle><i data-feather="calendar" class="text-danger"></i></span>
                                        <input type="text" class="form-control bg-transparent border-danger"
                                            placeholder="Select date" data-input>
                                    </div>
                                    <x-add-btn text="Add New Seller" function="create()" />
                                </div>
                            </div>
                        </div>
                   </div>
                   <div class="card-body">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>#Id</th>
                                        <th style="width: 25%">Name</th>
                                        <th>Phone</th>
                                        <th>Email Address</th>
                                        <th>Registration Date</th>
                                        <th>Updation Date</th>
                                        <th style="width: 20%">Last Active</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <b>Business Name:</b>
                                            <span>Sudhanshu Textile Indusrty</span>
                                            <br>
                                            <b>User Name:</b>
                                            <span>Sudhanshu Kumar Jhandewala</span>
                                        </td>
                                        <td>6390041900</td>
                                        <td>Shudhanshu@gmail.com</td>
                                        <td>03/10/2023</td>
                                        <td>03/10/2023</td>
                                        <td>03/10/2023, 5.45 Pm</td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <b>Business Name:</b>
                                            <span>Sudhanshu Textile Indusrty</span>
                                            <br>
                                            <b>User Name:</b>
                                            <span>Sudhanshu Kumar Jhandewala</span>
                                        </td>
                                        <td>6390041900</td>
                                        <td>Shudhanshu@gmail.com</td>
                                        <td>03/10/2023</td>
                                        <td>03/10/2023</td>
                                        <td>03/10/2023, 5.45 Pm</td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <b>Business Name:</b>
                                            <span>Sudhanshu Textile Indusrty</span>
                                            <br>
                                            <b>User Name:</b>
                                            <span>Sudhanshu Kumar Jhandewala</span>
                                        </td>
                                        <td>6390041900</td>
                                        <td>Shudhanshu@gmail.com</td>
                                        <td>03/10/2023</td>
                                        <td>03/10/2023</td>
                                        <td>03/10/2023, 5.45 Pm</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                   </div>
                </div>
            </div>
        </div>
    @endif
</div>
