<?php

/*
    Copyright 2013 Jose Rojas
    All rights reserved.
*/

include_once('HotelModel.php');
include_once('BookingsModel.php');

class BookingsProcessor extends BaseProcessor
{

    public function getBookingList()
    {
        $rooms = $this->args['rooms'];
        $guests = $this->args['guests'];
        $key = $rooms . ":" . $guests;
        //don't consider the checkin/checkout date as this is just mock data

        $bookings = BookingsModel::$BOOKINGS[$key];
  
        return $bookings;
    }       

    public function findBookingsByZipcode($zipcode)
    {
        $bookings = $this->getBookingList();    
        if ($bookings)
        {
            //find the hotel
            return $this->findModelItemsAssoc($zipcode, "zipcode", $bookings, 
                array("hotel_id" => 
                    array("model" => HotelModel::$HOTELS, "key" => "id", "field" => "hotel_details" )
                )
            );            
        }       

        return null;
    }

    public function findBookingByHotel($id)
    {
        $bookings = $this->getBookingList();    
        if ($bookings != null)
        {
            //find the hotel
            return $this->findModelItem($id, "hotel_id", $bookings);            
        }       

        return null;
    }

    public function get_location($default)
    {
        $zipcodes = $this->getd("location", $default);        

        $list = explode(",", $zipcodes);

        $bookings = array();
        if (count($list) > 1)
        {
            foreach ($list as $id)
            {
                $items = $this->findBookingsByZipcode($id);
                $bookings = array_merge($bookings, $items);
            }
        }
        else
            $bookings = $this->findBookingsByZipcode($zipcodes);

        for ($i = 0; $i < count($bookings); $i++)
        {
            $item = $bookings[$i];
            $item['hotel_details'] = HotelModel::getModelSimple($item['hotel_details']);
            $bookings[$i] = $item; //PHP array copy by value is annoying :(
        }

        $this->checkFound($bookings,"bookings not found");

        return $bookings;
    }

    public function get_hotel($default)
    {
        $hotel_id = $this->getd("hotel_id", $default);        

        $bookings = $this->findBookingByHotel($hotel_id);

        foreach ($bookings as $item)
            $item['hotel_details'] = HotelModel::getModelSimple($item['hotel_details']);

        $this->checkFound($bookings,"bookings not found");

        return $bookings;
    }

    public function post_bid($default)
    {
        $hotel_id = $this->getd("hotel_id", $default);        

        return array( "result" => "notify", "hotel_id" => $hotel_id, "bid_id" => "mybid1" );
    }

    public function post_book($default)
    {
        $hotel_id = $this->getd("hotel_id", $default);

        return array( "result" => "unavailable", "hotel_id" => $hotel_id );
    }
}

?>
