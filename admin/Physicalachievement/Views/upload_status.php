
<div class="col-12">
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Upload Status</h3>
        </div>
        <div class="block-content block-content-full">
            <form>
                <div class="row">
                    <div class="col-md-2">
                        <select class="form-control" id="year" name="year">
                                        <option value="">select</option>
                                        <option value="2" selected>2023-24</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary"><i class="si si-magnifier"></i> Submit</button>
                    </div>

                </div>
            </form>


        </div>
    </div>
    <div class="block">
    <div class="float-right">

            </div>
            <div class="block-content block-content-full" style="overflow-y: scroll;">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                    <tr>
                        <?php foreach ($headers as $header) : ?>
                            <td><?= $header ?></td>

                        <?php endforeach; ?>

                    </tr>
                </thead>
                <?php
                if (!empty($monthdata)) {
                    $serino = 1;
                ?>
                    <tbody>
                        <?php

                        foreach ($monthdata as $key => $monthdatas) : ?>
                            <tr>
                                <td><?php echo $serino++; ?></td>
                                <td><?php echo $monthdatas['district']; ?></td>
                                <td><label class="text-center <?php echo $monthdatas['April'] == 0 ? 'badge badge-danger' : 'badge badge-success'; ?>"><?php echo $monthdatas['April'] == 0 ? 'Not Uploaded' : 'Uploaded'; ?></label></td>
                                <td><label class="text-center <?php echo $monthdatas['May'] == 0 ? 'badge badge-danger' : 'badge badge-success'; ?>"><?php echo $monthdatas['May'] == 0 ? 'Not Uploaded' : 'Uploaded'; ?></label></td>
                                <td><label class="text-center <?php echo $monthdatas['June'] == 0 ? 'badge badge-danger' : 'badge badge-success'; ?>"><?php echo $monthdatas['June'] == 0 ? 'Not Uploaded' : 'Uploaded'; ?></label></td>
                                <td><label class="text-center <?php echo $monthdatas['July'] == 0 ? 'badge badge-danger' : 'badge badge-success'; ?>"><?php echo $monthdatas['July'] == 0 ? 'Not Uploaded' : 'Uploaded'; ?></label></td>
                                <td><label class="text-center <?php echo $monthdatas['August'] == 0 ? 'badge badge-danger' : 'badge badge-success'; ?>"><?php echo $monthdatas['August'] == 0 ? 'Not Uploaded' : 'Uploaded'; ?></label></td>
                                <td><label class="text-center <?php echo $monthdatas['September'] == 0 ? 'badge badge-danger' : 'badge badge-success'; ?>"><?php echo $monthdatas['September'] == 0 ? 'Not Uploaded' : 'Uploaded'; ?></label></td>
                                <td><label class="text-center <?php echo $monthdatas['October'] == 0 ? 'badge badge-danger' : 'badge badge-success'; ?>"><?php echo $monthdatas['October'] == 0 ? 'Not Uploaded' : 'Uploaded'; ?></label></td>
                                <td><label class="text-center <?php echo $monthdatas['November'] == 0 ? 'badge badge-danger' : 'badge badge-success'; ?>"><?php echo $monthdatas['November'] == 0 ? 'Not Uploaded' : 'Uploaded'; ?></label></td>
                                <td><label class="text-center <?php echo $monthdatas['December'] == 0 ? 'badge badge-danger' : 'badge badge-success'; ?>"><?php echo $monthdatas['December'] == 0 ? 'Not Uploaded' : 'Uploaded'; ?></label></td>
                                <td><label class="text-center <?php echo $monthdatas['January'] == 0 ? 'badge badge-danger' : 'badge badge-success'; ?>"><?php echo $monthdatas['January'] == 0 ? 'Not Uploaded' : 'Uploaded'; ?></label></td>
                                <td><label class="text-center <?php echo $monthdatas['February'] == 0 ? 'badge badge-danger' : 'badge badge-success'; ?>"><?php echo $monthdatas['February'] == 0 ? 'Not Uploaded' : 'Uploaded'; ?></label></td>
                                <td><label class="text-center <?php echo $monthdatas['March'] == 0 ? 'badge badge-danger' : 'badge badge-success'; ?>"><?php echo $monthdatas['March'] == 0 ? 'Not Uploaded' : 'Uploaded'; ?></label></td>

                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                <?php
                } else {
                    echo "<h1>No data available.</h1>";
                }
                ?>


            </table>
        </div>
    </div>
</div>