<?php

namespace App\Controller;

use App\Entity\Trips;
use App\Controller\TripMeasureController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TripController extends AbstractController
{   
    private $_tripMeasure = null;

    public function __construct(TripMeasureController $tripMeasure){
        $this->_tripMeasure = $tripMeasure;
    }

    /**
     * @Route("/", name="trip")
     */
    public function index(  )
    {
    	
    	$trips = $this->getTrips();

        return $this->render('trip/index.html.twig', [
            'controller_name' => 'TripController',
            'trips' => $trips
        ]);
    }

    public function getTrips(){

    	$repository = $this->getDoctrine()->getRepository(Trips::class);
    	$db_trips = $repository->findAll();

    	$trips = array();

    	foreach ($db_trips as $key => $trip) {
    		
    		$avg_interval_speeds = array();	
    		$max_avg_speed = 0;

    		$trip_intervals = $this->_tripMeasure->getTripIntervals( $trip->getId() );
			$trip_distance = $this->_tripMeasure->getTripDistance( $trip->getId() );

    		if( !empty($trip_intervals) ){

	    		foreach ($trip_intervals as $key => $trip_interval) {
	    			$avg_interval_speeds[] = 3600 * $trip_interval / $trip->getMeasureInterval();
	    		}

	    		$max_avg_speed = round( max($avg_interval_speeds) );

	    	}

	    	$trips[] = array(
    				"id" 			  => $trip->getId(),
    				"name" 			  => $trip->getName(),  
    				"measureInterval" => $trip->getMeasureInterval(), 
    				"distance"		  => $trip_distance['distance'],
    				"maxAvgSpeed" 	  => $max_avg_speed,  				
    			);

    	}

    	return $trips;
    }
}
