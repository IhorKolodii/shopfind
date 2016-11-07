$(document).ready( function() {
    $('#selectType').change(function() {
        var value = $(this).val();
        if (value === '1') {
            var fields = '<option value="ALL">ALL</option><option value="S">S</option><option value="M">M</option><option value="L">L</option>';
            $('#selectSize').html(fields);
        } else if (value === '2') {
            var fields = '<option value="ALL">ALL</option>';
            for (var i = 35; i < 46; i++) {
                fields += '<option value="'+i+'">'+i+'</option>';
            }
            $('#selectSize').html(fields);
        }
    });
    $("#selectType").val('1');
});