define(['core_user/repository'], function(repository) {
    return {
        init: function() {
            // Initialize the repository for the drawer preference.
            repository.init('drawer-open-nav', 'alpha');
        }
    };
});
