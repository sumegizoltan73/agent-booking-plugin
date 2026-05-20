document
    .getElementById('generate-slots')
    .addEventListener('click', async () => {

        await fetch(
            '/wp-json/agent-booking/v1/generate-slots',
            {
                method: 'POST'
            }
        );

        alert('Kész');
    });

document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('agent-booking-admin-calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth'
        });
        calendar.render();
      });