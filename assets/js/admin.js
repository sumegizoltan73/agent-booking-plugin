
document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('agent-booking-admin-calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'timeGridWeek',
          locale: 'hu',
          headerToolbar: {
              center: 'dayGridMonth,timeGridWeek,dayGridDay' // buttons for switching between views
          },
          eventTimeFormat: {
              hour: '2-digit',
              minute: '2-digit',
              hour12: false
          },
          events: async function(fetchInfo, successCallback) {
            const agentId = document.getElementById('agent-id').value;

              const response = await fetch(
                  agentBooking.restUrl + 'calendar-events?agent_id=' + agentId
              );

              const data = await response.json();

              successCallback(data);
          },
          eventClick: function(info) {

                Swal.fire({

                    title: 'Slot részletek',

                    html: `
                        <p>
                            ${info.event.start.toLocaleString()}
                        </p>
                        <p>
                            Ügynök:
                            ${info.event.extendedProps.name}
                        </p>
                        <p class="${info.event.extendedProps.status}" style="color: ${getSlotColor(info.event.extendedProps.status)};">
                            Status:
                            ${info.event.extendedProps.status}
                        </p>
                        <p>
                            <button onclick="updateSlot(${info.event.extendedProps.slot_id}, 'BLOCKED')">BLOCK</button>
                            <span style="margin-left: 20px;">&nbsp;</span>
                            <button onclick="updateSlot(${info.event.extendedProps.slot_id}, 'FREE')">FREE</button>
                        </p>
                    `
                });
            }
        });
        calendar.render();
        window.agentBookingCalendar = calendar;
      });

async function generateSlots() {
    const url = agentBooking.restUrl + 'generate-slots';
    const agentId = document.getElementById('agent-id').value;
    const response = await fetch(
        url,
        {
            method: 'POST',

            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': agentBooking.nonce
            },

            body: JSON.stringify({
                agent_id: agentId
            })
        }
    );

    const data = await response.json();
    window.agentBookingCalendar.refetchEvents();
    console.log(data);
}

async function updateSlot(id, status) {

    const url =
        agentBooking.restUrl +
        'update-slot-status';

    const response = await fetch(
        url,
        {
            method: 'POST',

            headers: {
                'Content-Type': 'application/json',

                'X-WP-Nonce':
                    agentBooking.nonce
            },

            body: JSON.stringify({
                id: id,
                status: status
            })
        }
    );

    window.agentBookingCalendar.refetchEvents();
    document.querySelector("button.swal2-confirm").click();
    const data = await response.json();

    console.log(data);
}

function getSlotColor(status) {

    switch(status) {

        case 'FREE':
            return '#4caf50';

        case 'BOOKED':
            return '#f44336';

        case 'BLOCKED':
            return '#9e9e9e';
    }
}

function refreshCalendar() {
    window.agentBookingCalendar.refetchEvents();
}