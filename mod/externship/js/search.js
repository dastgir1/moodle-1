// document.addEventListener('DOMContentLoaded', function() {
//     // Get the search input element
//     var searchInput = document.getElementById('search');
    
//     if (searchInput) {
//         // Add event listener for the 'keyup' event on the search input
//         searchInput.addEventListener('keyup', function() {
//             var searchText = this.value.toLowerCase(); // Get the search input and convert to lowercase

//             // Get all rows in the externship_list_table
//             var rows = document.querySelectorAll('#entrytable tr');
//             rows.forEach(function(row) {
//                 // Check if the row contains the search text
//                 var rowText = row.textContent.toLowerCase();
//                 if (rowText.indexOf(searchText) > -1) {
//                     row.style.display = ''; // Show the row
//                 } else {
//                     row.style.display = 'none'; // Hide the row
//                 }
//             });
//         });
//     } else {
//         console.error('Search input element not found.');
//     }
// });
$(document).ready(function() {
    // Get the search input element
    var $searchInput = $('#search');
    
    if ($searchInput.length) {
        // Add event listener for the 'keyup' event on the search input
        $searchInput.on('keyup', function() {
            var searchText = $(this).val().toLowerCase(); // Get the search input and convert to lowercase

            // Get all rows in the externship_list_table
            $('#entrytable tr').each(function() {
                var rowText = $(this).text().toLowerCase();
                if (rowText.indexOf(searchText) > -1) {
                    $(this).show(); // Show the row
                } else {
                    $(this).hide(); // Hide the row
                }
            });
        });
    } else {
        console.error('Search input element not found.');
    }
});
