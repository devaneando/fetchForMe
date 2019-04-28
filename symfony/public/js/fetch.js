$( document ).ready(function() {
    function fillLocation(location)
    {
        if ($("#fetch_hotel_latitude").val() == "") {
            $("#fetch_hotel_latitude").val(location.coords.latitude);
        }

        if ($("#fetch_hotel_longitude").val() == "") {
            $("#fetch_hotel_longitude").val(location.coords.longitude);
        }

        if ($("#fetch_hotel_kilometers").val() == "") {
            $("#fetch_hotel_kilometers").val(100);
        }
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(fillLocation);
    }
});
