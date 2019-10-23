<?php

namespace App\Controller;

use App\Entity\TripMeasures;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TripMeasureController extends AbstractController
{
    /**
     * @Route("/trip/measure", name="trip_measure")
     */
    public function index()
    {	
    	$distances = $this->getTripDistance();

        return $this->render('trip_measure/index.html.twig', [
            'controller_name' => 'TripMeasureController',
            'distances' => $distances
        ]);
    }

    public function getTripDistance( int $tripId ){

    	$em = $this->getDoctrine()->getRepository(TripMeasures::class);

    	$qb = $em->createQueryBuilder('tm');
    	$qb->select('tm.tripId, MAX(tm.distance) distance')
		   ->groupBy('tm.tripId')
		   ->having('tm.tripId = :tripId')
		   ->setParameter('tripId', $tripId);

		$result = $qb->getQuery()->getSingleResult();

		return $result;
    }

    public function getTripsDistances(){

    	$em = $this->getDoctrine()->getRepository(TripMeasures::class);

    	$qb = $em->createQueryBuilder('tm');
    	$qb->select('tm.tripId, MAX(tm.distance) distance')
		   ->groupBy('tm.tripId');

		$result = $qb->getQuery()->getResult();

		return $result;
    }

    public function getTripIntervals( int $tripId ){

    	if( !empty($tripId) ){
	    	$em = $this->getDoctrine()->getRepository(TripMeasures::class);

	    	$qb = $em->createQueryBuilder('tm');
	    	$qb->select('tm')
			   ->where('tm.tripId = :tripId')
			   ->setParameter('tripId', $tripId);

			$trip_measures = $qb->getQuery()->getResult();
			$trip_measures_count = count($trip_measures);

			$trip_intervals = array();

			if( $trip_measures_count > 1 ){

				for ($i=0; $i < $trip_measures_count - 1; $i++) { 
					$trip_intervals[] = $trip_measures[$i+1]->getDistance() - $trip_measures[$i]->getDistance();
				}

			}

			return $trip_intervals;
		}

		return false;
    }
}
