
var max_modal_width = (document.viewport.getDimensions().width-100);
if(max_modal_width > 1000) max_modal_width = 1000;

var max_modal_height = (document.viewport.getDimensions().height-100);
Event.observe(window, 'load', function() {
    of1 = $('content').cumulativeOffset();
    of2 = $('buttons').cumulativeOffset();
    diff = of2[1]-of1[1];

    if ($('subnav')) {
        $('subnav').setStyle({'min-height': diff + 'px'});

        width = $('container').getWidth() - $('subnav').getWidth() - 10;

        $('content-inner').setStyle({height: diff + 'px', width: width + 'px'});
    }
    $('modal').setStyle({height:document.viewport.getDimensions().height+'px'});
    $('modal-inner').setStyle({
        height:max_modal_height+'px',
        width:max_modal_width+'px'
    });
    $('overlay').setStyle({height:document.viewport.getDimensions().height+'px'});

    new Draggable('modal-inner', { revert: false, handle: 'modal-title' });


    $('modal-micro').setStyle({height:document.viewport.getDimensions().height+'px'});
    $('modal-inner-micro').setStyle({
        height:max_modal_height+'px',
        width:max_modal_width+'px'
    });
    $('overlay-micro').setStyle({height:document.viewport.getDimensions().height+'px'});

    new Draggable('modal-inner-micro', { revert: false, handle: 'modal-title-micro' });
});

