<?php

/*
    Copyright 2013 Jose Rojas
    All rights reserved.
*/


class BookingsModel
{
    static $BOOKINGS = array(
        "1:1" => array(
            array(
                "id" => "booking1",
                "hotel_id" => "hotel1",
                "recommended_bid_price" => 225,
                "book_now_price" => 265,
                "book_now_id" => "booking1"
            ),
            array(
                "id" => "booking2",
                "hotel_id" => "hotel2",
                "recommended_bid_price" => 125,
                "book_now_price" => 175,
                "book_now_id" => "booking2"
            )
        ),
        "1:2" => array(
            array(
                "id" => "booking1",
                "hotel_id" => "hotel1",
                "recommended_bid_price" => 255,
                "book_now_price" => 285,
                "book_now_id" => "booking1"
            ),
            array(
                "id" => "booking2",
                "hotel_id" => "hotel2",
                "recommended_bid_price" => 135,
                "book_now_price" => 175,
                "book_now_id" => "booking2"
            )
        )
    );
}

?>
