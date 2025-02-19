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
// $(document).ready(function () {
 
//     $('#search').on('keyup', function () {
//         var searchtext = $(this).val().toLowerCase();
        
//         $('#externship_list_table tr').filter(function () {
//             $(this).toggle($(this).text().toLowerCase().indexOf(searchtext) > -1);
//         });
//     });
// }); 
document.addEventListener('DOMContentLoaded', function() {
    // Get the search input element
    var searchInput = document.getElementById('search');
    
    if (searchInput) {
        // Add event listener for the 'keyup' event on the search input
        searchInput.addEventListener('keyup', function() {
            var searchText = this.value.toLowerCase(); // Get the search input and convert to lowercase

            // Get all rows in the externship_list_table
            var rows = document.querySelectorAll('#externship_list_table tr');
            rows.forEach(function(row) {
                // Check if the row contains the search text
                var rowText = row.textContent.toLowerCase();
                if (rowText.indexOf(searchText) > -1) {
                    row.style.display = ''; // Show the row
                } else {
                    row.style.display = 'none'; // Hide the row
                }
            });
        });
    } else {
        console.error('Search input element not found.');
    }
});


