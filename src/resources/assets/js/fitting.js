function fillFittingWindow(result) {
    if (result) {
        $('#fitting-window').show();
        $('#middle-header').text(result.shipname + ', ' + result.fitname);
        $('#showeft').val(result.eft);
        $('#eftexport').show();

        const exportLinks = $('#exportLinks')
        exportLinks.show().empty();
        for(const link of result.exportLinks){
            exportLinks.append(`<a href="${link.url}" class="list-group-item list-group-item-action">${link.name}</a>`)
        }

        const eveTechUrl = 'https://images.evetech.net/types';

        for (const slotType in result) {
            const slot = result[slotType];
            const iconUrl = `${eveTechUrl}/${slot.id}/icon?size=32`;
            const row = `<tr><td><img src="${iconUrl}" height='24' />${slot.name}</td></tr>`;

            let slotId = null;

            if (slotType.startsWith('HiSlot')) {
                slotId = 'highSlots';
            } else if (slotType.startsWith('MedSlot')) {
                slotId = 'midSlots';
            } else if (slotType.startsWith('LoSlot')) {
                slotId = 'lowSlots';
            } else if (slotType.startsWith('RigSlot')) {
                slotId = 'rigs';
            } else if (slotType.startsWith('SubSlot')) {
                slotId = 'subSlots';
            } else if (slotType.startsWith('dronebay')) {
                slotId = 'drones';
            }

            if (slotId && slotId !== 'drones') {
                $('#' + slotId).find('tbody').append(row);
            } else if (slotId === 'drones') {
                for (const droneId in result['dronebay']) {
                    const drone = result['dronebay'][droneId];
                    const droneRow = `<tr><td><img src="${eveTechUrl}/${droneId}/icon?size=32" height="24" />${drone.name}</td><td>${drone.qty}</td></tr>`;
                    $('#' + slotId).find('tbody').append(droneRow);
                }
            }
        }
    }
}

function fillSkills(result) {
    const characterId = $('#characterSpinner').find(":selected").val();

    if (!characterId) {
        return;
    }

    if (!result.characters) {
        return;
    }

    const character = result.characters[characterId];

    if (!character) {
        return;
    }

    for (const skillId in result.skills) {
        const skill = result.skills[skillId];

        if (!!character.skill[skill.typeId]) {
            let charskilllvl = character.skill[skill.typeId].level;
            let rank = character.skill[skill.typeId].rank;

            let graphbox = drawLevelBox(skill.level, charskilllvl, skill.typeName, rank);
            $('#skillbody').append(graphbox);
        }
    }
}

function formatTime(points) {
    if (!points) {
        return;
    }

    let totalHours = points / 1800;
    let days = Math.floor(totalHours / 24);
    let remainingHours = Math.floor(totalHours - (days * 24));
    let remainingMinutes = Math.floor((totalHours - (days * 24) - remainingHours) * 60);

    return `${days}d ${remainingHours}h ${remainingMinutes}m`;
}

function drawLevelBox(neededLevel, currentLevel, skillName, rank) {
    let trainingtime = '';
    let row = '';
    let pointdiff = 0;

    if (currentLevel === 0) {
        row = '<tr class="bg-red">';
        trainingtime = formatTime(rank * 250 * Math.pow(5.66, (neededLevel - 1)));
    } else if ((neededLevel - currentLevel) > 0) {
        row = '<tr class="bg-orange">';
        pointdiff = (rank * 250 * Math.pow(5.66, (neededLevel - 1))) - (rank * 250 * Math.pow(5.66, (currentLevel - 1)));
        trainingtime = formatTime(pointdiff);
    } else {
        row = '<tr>';
    }

    let graph = '<td>' + skillName + ' <small>(x' + rank + ')</small></td>';
    graph += '<td style="width: 11em"><div style="background-color: transparent; width: 5.5em; text-align: center; height: 1.35em; letter-spacing: 2.25px;" class="pull-right">';

    if (currentLevel >= neededLevel) {
        for (let i = 0; i < neededLevel; i++) {
            graph = graph + '<span class="fa fa-square " style="vertical-align: text-top; color: #5ac597;"></span>';
        }
        for (let i = neededLevel; i < currentLevel; i++) {
            graph = graph + '<span class="fa fa-square text-green" style="vertical-align: text-top"></span>';
        }
        for (let i = 0; i < (5 - currentLevel); i++) {
            graph = graph + '<span class="fa fa-circle text-green" style="vertical-align: text-top"></span>';
        }
    } else {
        for (let i = 0; i < currentLevel; i++) {
            graph = graph + '<span class="fa fa-square " style="vertical-align: text-top; color: #5ac597;"></span>';
        }
        for (let i = 0; i < (neededLevel - currentLevel); i++) {
            graph = graph + '<span class="fa fa-circle text-danger" style="vertical-align: text-top"></span>';
        }
        for (let i = 0; i < (5 - neededLevel); i++) {
            graph = graph + '<span class="fa fa-circle text-green" style="vertical-align: text-top"></span>';
        }
    }

    graph += '</div><span class="pull-right"><small>' + trainingtime + '</small> </span></td></tr>';
    graph += '</td>';

    return row + graph + '</tr>';
}