
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

async function generateUniqueSlots(agent, 
    range,
    from,
    to,
    duration) {
    const url = agentBooking.restUrl + 'generate-unique-slots';
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
                agent_id: agent, 
                range,
                from,
                to,
                duration
            })
        }
    );

    const data = await response.json();
    window.agentBookingCalendar.refetchEvents();
    console.log(data);
}

async function generateUniqueSlotsPopUp() {
    const agent_select_html = document.getElementById('agent-id').innerHTML;

    const { value: formValues } = await Swal.fire({

        title: 'Slot generálás',

        html: `
            <select id="agent-id-for-slot-generate">
                ${agent_select_html}
            </select>
            <input
                id="slot-date-range"
                class="swal2-input"
            />

            <input
                id="time-from"
                type="time"
                class="swal2-input"
            />

            <input
                id="time-to"
                type="time"
                class="swal2-input"
            />

            <select id="slot-duration">
                <option value="15">15 perc</option>
                <option value="30">30 perc</option>
                <option value="60">60 perc</option>
            </select>
        `,

        showCancelButton: true,
        allowEscapeKey: true,
        preConfirm: () => {
            const agent = document.getElementById("agent-id-for-slot-generate").value;
            const range = document.getElementById("slot-date-range").value;
            const from = document.getElementById("time-from").value;
            const to = document.getElementById("time-to").value;
            const duration = document.getElementById("slot-duration").value;

            const isValid = (range && from && to && from.length === 5 && to.length === 5);
            return [
                agent, 
                range,
                from,
                to,
                duration,
                isValid
            ]
        },
        didOpen: () => {

            jQuery('#slot-date-range')
                .daterangepicker({
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                });
        }
    });

    const [agent, range, from, to, duration, isValid] = formValues;
    if (isValid) { 
        // generate
        generateUniqueSlots(agent, 
            range,
            from,
            to,
            duration);
    }
    else {
        Swal.fire({
            title: 'Hiba!',
            text: 'Minden mezőt töltsön ki!',
            icon: 'error'
        });
    }
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