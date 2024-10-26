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

$(document).ready(function () {
    $("#mysearch").on('keyup',function(){
        var search = $(this).val().toLowerCase();
        $('#datatable tr').filter(function(){
        $(this).toggle($(this).text().toLowerCase().indexOf(search)>-1);

        });
    });
   $('#id_starthour, #id_startminute, #id_endhour, #id_endminute').on('change', function() {
        
        // Get the values of the inputs
        var startHour = parseInt($('#id_starthour').val()) || 0;
        var startMinute = parseInt($('#id_startminute').val()) || 0;
        var endHour = parseInt($('#id_endhour').val()) || 0;
        var endMinute = parseInt($('#id_endminute').val()) || 0;
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
    
        if(endTimeInMinutes > startTimeInMinutes){
            $('#id_duration').val(durationHours + ' : ' + durationMinutes);
            $('#custom-div-id').text('');
        }else{
            
            $('#id_duration').val('');
            $('#custom-div-id').text('End Time must be greater than Start Time');
        }
      
   });
   $('.del-btn').on('click', function () {
    var $row = $(this).closest('tr'); // Get the closest table row
    var id = $(this).val(); // Get the ID value

    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this user record!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: '/mod/dataentry/deleterecord.php',
                type: 'POST',
                data: {
                    'delete_btn_set': 1,
                    'dataid': id,
                },
                  success: function(response){

                    swal("Oops! Your user record  has been deleted!", {
                        icon: "success",
                        }).then((result)=>{
                        $row.hide();
                            location.reload();
                        });
                    }
            });
        } else {
            swal("Your user record is safe!");
        }
    });
});

    
});
