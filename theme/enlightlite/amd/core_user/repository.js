require(['core_user/repository', 'core/ajax'], function(userRepository, ajax) {

    // Function to update user preference for the navigation drawer.
    function updateDrawerPreference(state) {
        var preference = {
            name: 'drawer-open-nav',
            value: state
        };

        // Use the repository to save the user preference.
        userRepository.update_user_preferences([preference]).done(function() {
            console.log('User preference updated successfully.');
        }).fail(function() {
            console.error('Failed to update user preference.');
        });
    }

    // Example of toggling the drawer and updating the user preference.
    var drawer = document.querySelector('#nav-drawer');
    if (drawer) {
        drawer.addEventListener('click', function() {
            var isOpen = drawer.classList.contains('open');
            updateDrawerPreference(isOpen ? 'open' : 'closed');
        });
    }

});
