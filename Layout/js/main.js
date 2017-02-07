$(document).ready(function(){
    // Enable tooltips
    $('[data-toggle="tooltip"]').tooltip();   
    // Home submit form
    $('#home-form-search').click(function() {
        var code = $('input[name="code"]', $(this)).val();
        if(code.length>0) {
            window.location.href = './'+code;
        }
    });
    // Run async plugins
    $('[id^="plugin-container"][data-pluginrunmode="async"]').each(function() {
        var pluginName = $(this).data('pluginname');
        var runid = $(this).data('runid');
        runPluginAsync(pluginName, runid);
    });
});

function runPluginAsync(pluginName, runid) {
    
    pluginLoadingOn(pluginName);
    
    var pluginUrl = location.pathname+'/'+pluginName;
    var request = $.ajax({
        url: pluginUrl,
        method: 'GET',
        data: {
            runid: runid
        },
        dataType: 'json'
    });

    request.always(function() {
        pluginLoadingOff(pluginName);
    });

    request.done(function(result) {
        // Failed
        if(typeof result.status==='undefined' || !result.status) {
            $('#plugin-button-'+pluginName).removeClass('btn-primary').addClass('btn-danger');
            $('#plugin-container-'+pluginName).remove();
            return;
        }

        // Time (if enabled)
        if(typeof result.time!=='undefined' && result.time!==false) {
            $('#plugin-button-'+pluginName).append(' ('+result.time.toFixed(2)+')');
        }

        // Success
        if(typeof result.success!=='undefined' && result.success) {
            var icon = '<i class="glyphicon glyphicon-exclamation-sign"></i> ';
            $('#plugin-button-'+pluginName).prepend(icon);
            $('#plugin-title-'+pluginName).prepend(icon).find('i').addClass('text-danger');
        }

        // Output
        if(typeof result.output!=='undefined' && result.output.length>0) {
            $('#plugin-button-'+pluginName).removeClass('btn-danger').addClass('btn-primary').attr('disabled', false);
            $('#plugin-output-'+pluginName).html(result.output);
        }
        else {
            $('#plugin-button-'+pluginName).remove();
            $('#plugin-container-'+pluginName).remove();
        }
        
    });

    request.fail(function() {
        $('#plugin-button-'+pluginName).removeClass('btn-primary').addClass('btn-danger');
        $('#plugin-container-'+pluginName).remove();
    });
}

function pluginLoadingOn(pluginName) {
    var icon = '<i class="glyphicon glyphicon-cog gly-spin loading"></i> ';
    $('#plugin-button-'+pluginName).prepend(icon);
    $('#plugin-output-'+pluginName).addClass('loading').prepend(icon);
}

function pluginLoadingOff(pluginName) {
    $('#plugin-button-'+pluginName+' i.loading').remove();
    $('#plugin-output-'+pluginName).removeClass('loading').html('');
} 
