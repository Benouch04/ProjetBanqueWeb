/*!
    * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2023 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
    // 
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList
            .contains('sb-sidenav-toggled'));
        });
    }

});
/**
 * Script de l'affichage numTel
 */
window.addEventListener('load', function() {
    var input = document.querySelector(".intl-tel-input");
    if (input) {
      intlTelInput(input, {
        // options personnalisées
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        preferredCountries: ['fr', 'us', 'gb'],
        separateDialCode: true,
        customContainer: "form-control",
      });
    }
  });


  
  document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        timeZone: 'Europe/Paris',
        headerToolbar: {
            start: 'prev,next today',
            center: 'title',
            end: 'dayGridMonth,timeGridWeek'
        },
        events: eventData, 
        editable: true,
        eventResizableFromStart: true,
        eventClick: function (info) {
            window.location.href = `/calendar/${info.event.id}`;
        }
    });
    
    calendar.render();
});
  