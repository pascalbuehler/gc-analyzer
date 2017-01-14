$(document).ready(function(){
    // Enable tooltips
    $('[data-toggle="tooltip"]').tooltip();   
    // Home submit form
    $('#home-form-search').click(function() {
        var code = $('input[name="code"]', $(this)).val();
        if(code.length>0) {
            window.location.href = '/'+code;
        }
    });
});
