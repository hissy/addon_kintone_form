$(function(){
    $('.subfield-container').cloneya()
        .on('form_input.cloneya', function(event, input, toClone, newClone) {
            var name = $(input).prop('name');
            var name_chunk = name.split('][value][');
            var index = name_chunk[1];
            name_chunk[1] = parseInt(index) + 1;
            $(input).prop('name', name_chunk.join('][value]['));

            $(toClone).find('button').hide();
        });
});