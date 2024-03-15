//grand total
gt_mon_phy = 0;
gt_cum_phy = 0;
$('.mon_phy').each(function () {
    mon_phy = parseInt($(this).find('input').val()) || 0;
    gt_mon_phy += mon_phy;

    upto_phy = parseInt($(this).closest('tr').find('.upto_phy').text()) || 0;
    gt_cum_phy += (upto_phy+mon_phy)
});
$('#gt_mon_phy').text(gt_mon_phy);
$('#gt_cum_phy').text(gt_cum_phy);

//sub total
sub_mon_phy = 0;
sub_cum_phy = 0;
$('tr[data-parent="'+parent_id+'"]').each(function () {
    mon_phy = parseInt($(this).find('.mon_
