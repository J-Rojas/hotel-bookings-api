<?php

/*
    Copyright 2013 Jose Rojas
    All rights reserved.
*/

include_once ('HotelModel.php');

class HotelsProcessor extends BaseProcessor
{
    public function findHotel($id)
    {
        return $this->findModelItem($id, "id", HotelModel::$HOTELS);
    }

    public function findHotelsByZipcode($id)
    {
        return $this->findModelItems($id, "zipcode", HotelModel::$HOTELS);
    }

    public function get_location($defaultParam = NULL)
    {
        $hotel_zips = $this->getd("location", $defaultParam);        
       
        $list = explode(',', $hotel_zips);
            
        $hotels = array();
        if (count($list) > 1)
        {
            $hotel = array();
            foreach ($list as $id)
            {
                $hotels = array_merge($hotels, $this->findHotelsByZipcode($id));
            }
        }
        else
            $hotels = $this->findHotelsByZipcode($hotel_zips);

        $this->checkFound($hotels,"hotels not found");

        return $hotels;
    }

    public function get_details($defaultParam = NULL)
    {
        $hotel_id = $this->getd("hotel_id", $defaultParam);

        $list = explode(',', $hotel_id);
            
        if (count($list) > 1)
        {
            $hotel = array();
            foreach ($list as $id)
            {
                $hotel[] = $this->findHotel($id);
            }
        }
        else
            $hotel = $this->findHotel($hotel_id);

        $this->checkFound($hotel,"hotel not found");

        return $hotel;      
    }
}

?>
