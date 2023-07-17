<div class="main-container">
    <div class="block">
        <form action="" method="post">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    <?= $heading_title; ?>
                </h3>

            </div>
            <div class="block-content block-content-full">
                <div id="page_list_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="row">

                        <div class="col-3">
                            <label class="form-label">Weak</label>
                            <input type="text">

                        </div>
                        <div class="col-3">
                            <label class="form-label">To</label>
                            <input type="text" name="" id="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="page_list" class="table table-bordered table-striped "
                            aria-describedby="page_list_info">
                            <thead>
                                <tr>
                                    <th>Block</th>
                                    <th>Ragi</th>
                                    <th>Little Millet</th>
                                    <th>Foxtail Millet</th>
                                    <th>Sorghum</th>
                                    <th>Pearl Millet</th>
                                    <th>Barnyard Millet</th>
                                    <th>Kodo Millet </th>
                                    <th>Porso Millet</th>
                                    <th>TOTAL</th>
                                </tr>

                            </thead>
                            <tbody>
                                <tr>
                                    <td rowspan="3">block1</td>
                                    <td>SMI </td>
                                    <td>SMI </td>
                                    <td>SMI </td>
                                    <td>SMI </td>
                                    <td>SMI </td>
                                    <td>SMI </td>
                                    <td>SMI </td>
                                    <td>SMI </td>
                                    <td rowspan="3">200</td>
                                </tr>
                                <tr>

                                    <td>LT</td>
                                    <td>LT</td>
                                    <td>LT</td>
                                    <td>LT</td>
                                    <td>LT</td>
                                    <td>LT</td>
                                    <td>LT</td>
                                    <td>LT</td>
                                </tr>
                                <tr>
                                    <td>LS</td>
                                    <td>LS</td>
                                    <td>LS</td>
                                    <td>LS</td>
                                    <td>LS</td>
                                    <td>LS</td>
                                    <td>LS</td>
                                    <td>LS</td>
                                </tr>
                            </tbody>

                        </table>
                        <div style="float: right;">
                            <button class="btn btn-primary">Approve</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>