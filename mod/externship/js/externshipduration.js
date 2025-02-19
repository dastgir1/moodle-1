// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
document.querySelectorAll('#id_starthour, #id_startminute, #id_endhour, #id_endminute').forEach(function(element) {
    element.addEventListener('change', function() {
        // Get the values of the inputs
        var startHour = parseInt(document.getElementById('id_starthour').value, 10);
        var startMinute = parseInt(document.getElementById('id_startminute').value, 10);
        var endHour = parseInt(document.getElementById('id_endhour').value, 10);
        var endMinute = parseInt(document.getElementById('id_endminute').value, 10);

        // Check if inputs are valid numbers
        if (isNaN(startHour) || isNaN(startMinute) || isNaN(endHour) || isNaN(endMinute)) {
            document.getElementById('id_duration').value = '';
            document.getElementById('custom-div-id').textContent = 'Invalid input. Please enter valid times.';
            return;
        }

        // Convert the start and end times to minutes
        var startTimeInMinutes = (startHour * 60) + startMinute;
        var endTimeInMinutes = (endHour * 60) + endMinute;

        // Calculate the duration in minutes
        var durationInMinutes = endTimeInMinutes - startTimeInMinutes;

        // Handle case when the end time is earlier than the start time (e.g., overnight time)
        if (durationInMinutes < 0) {
            durationInMinutes += 24 * 60; // Add 24 hours in minutes
        }

        // Convert the duration to hours and minutes
        var durationHours = Math.floor(durationInMinutes / 60);
        var durationMinutes = durationInMinutes % 60;
        
        if (endTimeInMinutes > startTimeInMinutes) {
            document.getElementById('id_duration').value = durationHours + ' hours ' + durationMinutes + ' minutes';
            document.getElementById('custom-div-id').textContent = '';
        } else {
            document.getElementById('id_duration').value = '';
            document.getElementById('custom-div-id').textContent = 'End Time must be greater than Start Time';
        }
    });
});