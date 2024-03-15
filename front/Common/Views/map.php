<div class="container">
  <div class="row">
    <div class="col-md-2">
      <ul class="list-group district-info">
        <li class="list-group-item">
          <span class="text-dark-green text-bold" id="total_districts">0</span>
          <br />Districts
        </li>
        <li class="list-group-item">
          <span class="text-dark-green text-bold" id="total_blocks">0</span>
          <br />Blocks
        </li>
        <li class="list-group-item">
          <span class="text-dark-green text-bold" id="total_gps"><?= $gps['total_gps'] ?></span>
          <br />GPs
        </li>
        <li class="list-group-item">
          <span class="text-dark-green text-bold" id="total_villages"><?= $gps['total_villages'] ?></span>
          <br />Villages
        </li>
        <li class="list-group-item">
          <span class="text-dark-green text-bold" id="total_farmers"><?= $farmers['total_farmers'] ?></span>
          <br />Farmers
        </li>
      </ul>
    </div>
    <div class="col-md-8">
      <div id="map-area">
        <?= $svg_map ?>
      </div>
    </div>
  </div>
</div>

<script type="text/babel">
  let mapinfo;

  $(function () {
    $.ajax({
      url: '<?= $map_url ?>',
      dataType: 'JSON',
      async: true,
      success: function (json) {
        if (json.success) {
          mapinfo = json;
          showDistInfo(mapinfo);
          $('#hdQcdetails .dist-block').each(function () {
            const mapid = parseInt($(this).data('dist-id'));
            if (mapinfo.dists[mapid].is_omm) {
              $(this).addClass(mapinfo.dists[mapid].data-ommit-class);
              const html = `<strong><u>${mapinfo.dists[mapid].name}</u></strong><br>${mapinfo.dists[mapid].total_blocks} blocks<br>${mapinfo.dists[mapid].total_chc} CHCs<br>${mapinfo.dists[mapid].total_csc} CSCs<br>${mapinfo.dists[mapid].total_farmers} Farmers<br>`;
              $(this).attr('title', html.trim());
            }
          });
          $('#total_districts').text(mapinfo.total_districts);
          $('#total_blocks').text(mapinfo.total_blocks);
        }
      },
      error: function () {
        console.log('something went wrong while fetching info');
      }
    });

    $('.dist-block').mouseover(function () {
      const mapid = parseInt($(this).data('dist-id'));
      if (mapinfo.dists[mapid].is_omm) {
        $('#info-label').text(mapinfo.dists[mapid].name);
        showDistInfo(mapinfo.dists[mapid]);
      }
    });

    $('.dist-block').mouseout(function () {
      $('#info-label').text('All OMM Districts');
      showDistInfo(mapinfo);
    });

  });

  function showDistInfo(mapinfo) {
    $('.total-blocks').text(mapinfo.total_blocks || '');
    $('.area-coverage').text(mapinfo.area_coverage || '');
    $('.total-farmers').text(mapinfo.total_farmers || '');
    $('.total-chc').text(mapinfo.total_chc || '');
    $('.total-csc').text(mapinfo.total_csc || '');
  }
</script>
