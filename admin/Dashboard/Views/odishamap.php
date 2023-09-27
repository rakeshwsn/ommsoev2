<style>
    .tentative_map_content {
        position: relative;
    }

    #tentative_map .legends {
        position: absolute;
        bottom: 0px;
        right: 0;
        top: auto;
        left: auto;
        margin: 0;
        transform: translateY(0%);

    }

    #tentative_map .legends p {
        transform: rotate(0deg);
        position: relative;
        left: 0;
        width: auto;
        text-align: center;
    }

    #tentative_map .legends ul {
        flex-flow: row;
    }

    .legends ul {
        margin-bottom: 0;
        display: flex;
        flex-flow: column;
    }

    #tentative_map .legends ul li {
        flex-flow: column;
        margin: 0 5px;
    }

    .legends ul li {
        display: flex;
        margin-bottom: 0px;
        align-items: center;
        color: #888888;
        flex-flow: row;
        font-size: 0.7rem;
        font-weight: 600;
        margin-right: 15px;
    }

    #tentative_map .legends ul li span {
        height: 8px;
        width: 50px;
    }

    .legends ul li span {
        height: 70px;
        width: 16px;
        display: block;
        margin-right: 6px;
        border-radius: 0px;
    }

    .bg-scale0 {
        fill: #EFEFEF !important;
        background: #EFEFEF !important;
    }

    .bg-scale1 {
        fill: #FEE86C !important;
        background: #FEE86C !important;
    }

    .bg-scale2 {
        fill: #FABC5B !important;
        background: #FABC5B !important;
    }

    .bg-scale3 {
        fill: #F7AD5D !important;
        background: #F7AD5D !important;
    }

    .bg-scale4 {
        fill: #F1A777 !important;
        background: #F1A777 !important;
    }

    .maptable {

        /* display: block; */
        height: 500px;
        overflow: auto;
    }

    table thead {
        background-color: #FABC5B;
        position: sticky;
        top: 0;
    }
   
</style>
<div class="block" id="tentative_map">
    <div class="block-header block-header-default">
        <h3 class="block-title">Scale of Odisha Millets Mission</h3>
    </div>
    <div class="block-content block-content-full tentative_map_content">
        <div class="map-section">
            <div class="row">
                <div class="col-6">
                    <div id="map-container">

                    </div>
                    <div class="legends">
                        <p class=" mb-1 mt-0">Based on No. of Blocks </p>
                        <ul class="">
                            <li><span class="bg-scale0"></span> 0 </li>
                            <li><span class="bg-scale1"></span> 1 - 3 </li>
                            <li><span class="bg-scale2"></span> 3 - 4 </li>
                            <li><span class="bg-scale3"></span> 4 - 5 </li>
                            <li><span class="bg-scale4"></span> 5+ </li>
                        </ul>
                    </div>
                </div>
                <div class="maptable col-6">
                    <!-- <div class="table-responsive" > -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">District</th>
                                <th scope="col">Total Block</th>
                                <th scope="col">Total GP</th>
                                <th scope="col">Total Villages</th>
                                <th scope="col">Total Farmer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($maps as $map) { ?>
                                <tr data-dist="<?= $map->district_id ?>">
                                    <td><?= $map->districts ?></td>
                                    <td><?= $map->total_blocks ?></td>
                                    <td><?= $map->total_gps ?></td>
                                    <td><?= $map->total_villages ?></td>
                                    <td><?= $map->total_farmer ?></td>
                                </tr>
                            <?php  } ?>
                        </tbody>
                    </table>
                    <!-- </div> -->
                </div>

            </div>
        </div>

    </div>
</div>


<?php js_start() ?>

<script src="<?php echo theme_url('assets/js/snap.svg-min.js') ?>"></script>
<script>
    window.onload = function() {
        Snap.load("uploads/files/ommodishamap.svg", function(loadedFragment) {
            // 'loadedFragment' contains the loaded SVG elements
            var map = loadedFragment.select("svg");

            // Get the jQuery element where you want to append the SVG
            var $element = $("#map-container");

            // Append the SVG to the element
            $element.append(map.node);

            // Now you can manipulate the SVG elements as needed
            // For example, let's change the color of a path element to red
            mapinfo = <?php echo json_encode($maps); ?>;
            // console.log(mapinfo);
            const district = map.selectAll(".dist");

            district.forEach(function(obj) {
                var mapid = obj.attr('id');
                if (mapid) {

                    const dist = mapinfo.filter(item => item.district_id === mapid);


                    if (dist[0].blocks >= 1 && dist[0].blocks <= 3) {
                        obj.addClass('bg-scale1')
                    }
                    if (dist[0].blocks >= 4 && dist[0].blocks <= 5) {
                        obj.addClass('bg-scale2')
                    }
                    if (dist[0].blocks >= 6 && dist[0].blocks <= 8) {
                        obj.addClass('bg-scale3')
                    }
                    if (dist[0].blocks >= 9) {
                        obj.addClass('bg-scale4')
                    } else if (dist[0].blocks == 0) {
                        obj.addClass('bg-scale2')
                    }


                }
                obj.click(clickCallback);

            }, "text");

        });

        var clickCallback = function(event) {
            console.log(event);
            var id = event.target.attributes.id.nodeValue;
            console.log(id);
            $(".maptable tr").removeClass("bg-primary");
            $(".maptable tr[data-dist=" + id + "]").addClass("bg-primary text-light");

            const highlightElement = $(".maptable tr[data-dist=" + id + "]");
            if (highlightElement.length) {
                const container = $(".maptable");
                if (container.length) {
                    container.animate({
                        scrollTop: highlightElement.offset().top - container.offset().top + container.scrollTop() -50
                    }, "slow");
                }
            }

        };

    };
</script>
// replace img with svg when cached promise resolves

<?php js_end() ?>