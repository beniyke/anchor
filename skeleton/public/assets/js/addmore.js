//define counter
var sectionsCount = 1;
$('.remove-section').hide();
//add new section
$('body').on('click', '.addsection', function() {
    //define template
    var template = $($(this).data('clone')).clone();

    //increment
    sectionsCount++;

    //loop through each input
    var section = template.clone().find(':input').each(function(){

        //set id to store the updated section number
        var newId = this.id + sectionsCount;

        //update for label
        $(this).prev().attr('for', newId);

        //update id
        this.id = newId;
        $(this).val('');

    }).end()

    //inject new section
    .appendTo($(this).data('appendto'));

    // Show remove button if sectionsCount is greater than 1
    if (sectionsCount > 1) {
        $('.remove-section').show();
    }

    return false;
});

//remove section
$('body').on('click', '.remove-section', function() {
    //fade out section
    $(this).parent().fadeOut(300, function(){
        //remove parent element (main section)
        $(this).parent().parent().remove();

        // Decrement sectionsCount after removing a section
        sectionsCount--;

        // Hide remove button if sectionsCount is less than 2
        if (sectionsCount < 2) {
            $('.remove-section').hide();
        }
        return false;
    });
    return false;
});
