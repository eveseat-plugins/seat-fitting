let skills_informations;

$('#fitting-box').hide();
$('#skills-box').hide();
$('#eftexport').hide();
$('#showeft').val('');

$('#addFitting').on('click', function () {
    $('#fitEditModal').modal('show');
    $('#fitSelection').val('0');
    $('textarea#eftfitting').val('');
});

$('#fitlist').on('click', '#editfit', function () {
    $('#fitEditModal').modal('show');
    let id = $(this).data('id');
    $('#fitSelection').val(id);

    $.ajax({
        headers: function () {
        },
        url: "/fitting/geteftfittingbyid/" + id,
        type: "GET",
        datatype: 'string',
        timeout: 10000
    }).done(function (result) {
        $('textarea#eftfitting').val(result);
    }).fail(function (xmlHttpRequest, textStatus, errorThrown) {
    });
});

$('#fitlist').on('click', '#viewfit', function () {
    $('#highSlots, #midSlots, #lowSlots, #rigs, #cargo, #drones, #subSlots')
        .find('tbody')
        .empty();
    $('#fittingId').text($(this).data('id'));

    $.ajax({
        headers: function () {
        },
        url: "/fitting/getfittingbyid/" + $(this).data('id'),
        type: "GET",
        dataType: 'json',
        timeout: 10000
    }).done(function (result) {
        $('#highSlots, #midSlots, #lowSlots, #rigs, #cargo, #drones, #subSlots')
            .find('tbody')
            .empty();
        $('#showeft').val('');
        $('#fitting-box').show();
        fillFittingWindow(result);
    });

    $.ajax({
        headers: function () {
        },
        url: "/fitting/getskillsbyfitid/" + $(this).data('id'),
        type: "GET",
        dataType: 'json',
        timeout: 10000
    }).done(function (result) {
        if (result) {
            skills_informations = result;

            $('#skills-box').show();
            $('#skillbody').empty();

            if ($('#characterSpinner option').size() === 0) {
                for (var toons in result.characters) {
                    $('#characterSpinner').append('<option value="' + result.characters[toons].id + '">' + result.characters[toons].name + '</option>');
                }
            }
            fillSkills(result);
        }
    });
});

$('#characterSpinner').change(function () {
    if (skills_informations) {
        $('#skills-box').show();
        $('#skillbody').empty();

        fillSkills(skills_informations);
    }
});