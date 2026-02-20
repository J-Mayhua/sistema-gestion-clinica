// public/assets/js/patient_calendar.js

$(document).ready(function() {
    // Simulación de datos del calendario
    var doctorAvailability = {
        '2024-07-25': 'occupied',
        '2024-07-26': 'available',
        '2024-07-27': 'available'
    };

    // Renderizar el calendario
    function renderCalendar() {
        var calendarDiv = $('#calendar');
        var today = moment();
        var end = today.clone().add(1, 'month');
        var date = today.clone().startOf('month').startOf('week');

        var calendarHtml = '<table><thead><tr>';
        for (var i = 0; i < 7; i++) {
            calendarHtml += '<th>' + moment().weekday(i).format('ddd') + '</th>';
        }
        calendarHtml += '</tr></thead><tbody><tr>';

        while (date.isBefore(end)) {
            for (var i = 0; i < 7; i++) {
                if (date.isSame(today, 'month')) {
                    var dateStr = date.format('YYYY-MM-DD');
                    var availability = doctorAvailability[dateStr] || 'available';
                    var cellClass = availability === 'available' ? 'available' : 'occupied';
                    calendarHtml += '<td class="' + cellClass + '" data-date="' + dateStr + '">' + date.date() + '</td>';
                } else {
                    calendarHtml += '<td></td>';
                }
                date.add(1, 'day');
            }
            calendarHtml += '</tr>';
        }
        calendarHtml += '</tbody></table>';

        calendarDiv.html(calendarHtml);
    }

    // Inicializar calendario
    renderCalendar();

    // Manejar la solicitud de cita
    $('form').on('submit', function(event) {
        event.preventDefault();
        var selectedDate = $('#date').val();
        var selectedTime = $('#time').val();

        if (selectedDate && selectedTime) {
            $.post('patient_dashboard.php', {
                request_appointment: true,
                date: selectedDate,
                time: selectedTime
            }, function(response) {
                alert(response.message);
            }, 'json');
        } else {
            alert('Por favor, complete todos los campos.');
        }
    });

    // Manejar la selección de fecha
    $('#calendar').on('click', 'td.available', function() {
        var selectedDate = $(this).data('date');
        $('#date').val(selectedDate);
    });
});
