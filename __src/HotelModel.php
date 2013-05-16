<?php

/*
    Copyright 2013 Jose Rojas
    All rights reserved.
*/

class HotelModel
{
    static $HOTELS = array(
        array (
            "id" => "hotel1",
            "name" => "Hotel Awesome",
            "rating" => 5,
            "trip_advisor_rating" => 4.5,
            "region" => "Shopping District",
            "address" => "123 Imaginary Rd., San Francisco, CA, 94041",
            "zipcode" => 94041,
            "description" => "This is one awesome hotel.",
            "overview" => array(
                "location" => "Near the shopping district",
                "features" => "Lorem Ipsum",
                "guestrooms" => "Lorem Ipsum",
            ),
            "thumbnails" => array(),
            "general_amenities" => 
                array ("Pool", "Sauna", "Dining Hall"),
            "room_amenities" => 
                array ("Minibar", "Digital TV", "Basic Cable", "HBO"),            
        ),

        array (
            "id" => "hotel2",
            "name" => "Hotel Cheapy",
            "rating" => 3,
            "trip_advisor_rating" => 2.5,
            "region" => "Fisherman's Wharf",
            "address" => "627 Somewhere Rd., San Francisco, CA, 94047",
            "zipcode" => 94047,
            "description" => "This is one cheap hotel.",
            "overview" => array(
                "location" => "Near the tourist district",
                "features" => "Lorem Ipsum",
                "guestrooms" => "Lorem Ipsum",
            ),
            "thumbnails" => array(),
            "general_amenities" => 
                array ("Pool"),
            "room_amenities" => 
                array ("Minibar", "Basic Cable"),            
        )
    );

    public static function getModelSimple($hotel)
    {
        //slice out "extra" data. Presumably these extra items are in a separate table within the db
        unset($hotel['overview']);
        unset($hotel['general_amenities']);
        unset($hotel['room_amenities']);

        return $hotel;
    }
}

?>
